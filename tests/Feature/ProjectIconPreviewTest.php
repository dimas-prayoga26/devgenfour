<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProjectIconPreviewTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_previews_the_icon_using_the_english_translation_like_saving_does(): void
    {
        config(['services.deepl.key' => 'kunci-uji:fx']);
        Http::fake(['api-free.deepl.com/v2/translate' => Http::response(['translations' => [
            ['detected_source_language' => 'ID', 'text' => 'Flower Shop'],
        ]])]);

        $response = $this->actingAs(User::factory()->create())->getJson('/admin/project-icon-preview?category=Toko%20Bunga');

        $response->assertOk()->assertJsonPath('icon', 'flower');
        $this->assertStringStartsWith('<', $response->json('svg'));
        Http::assertSent(fn (Request $request): bool => $request['text'] === ['Toko Bunga']);
    }

    public function test_repeated_categories_reuse_the_cached_translation(): void
    {
        config(['services.deepl.key' => 'kunci-uji:fx']);
        Http::fake(['api-free.deepl.com/v2/translate' => Http::response(['translations' => [
            ['detected_source_language' => 'ID', 'text' => 'Flower Shop'],
        ]])]);
        $admin = User::factory()->create();

        $this->actingAs($admin)->getJson('/admin/project-icon-preview?category=Toko%20Bunga');
        $response = $this->actingAs($admin)->getJson('/admin/project-icon-preview?category=toko%20bunga');

        $response->assertOk()->assertJsonPath('icon', 'flower');
        Http::assertSentCount(1);
    }

    public function test_curated_indonesian_keywords_skip_the_translation(): void
    {
        config(['services.deepl.key' => 'kunci-uji:fx']);
        Http::fake(['api-free.deepl.com/*' => Http::response(['translations' => []])]);

        $response = $this->actingAs(User::factory()->create())->getJson('/admin/project-icon-preview?category=Bengkel%20Motor');

        $response->assertOk()->assertJsonPath('icon', 'wrench');
        Http::assertNothingSent();
    }

    public function test_previews_from_the_indonesian_category_without_an_api_key(): void
    {
        config(['services.deepl.key' => null]);
        Http::fake(['api-free.deepl.com/*' => Http::response(['translations' => []])]);

        $response = $this->actingAs(User::factory()->create())->getJson('/admin/project-icon-preview?category=Jasa%20Laundry');

        $response->assertOk()->assertJsonPath('icon', 'washing-machine');
        Http::assertNothingSent();
    }

    public function test_failed_translation_still_previews_from_the_indonesian_category(): void
    {
        config(['services.deepl.key' => 'kunci-uji:fx']);
        Http::fake(['api-free.deepl.com/v2/translate' => Http::response(['message' => 'Quota exceeded'], 456)]);

        $response = $this->actingAs(User::factory()->create())->getJson('/admin/project-icon-preview?category=Jasa%20Laundry');

        $response->assertOk()->assertJsonPath('icon', 'washing-machine');
    }

    public function test_empty_category_has_no_icon(): void
    {
        $response = $this->actingAs(User::factory()->create())->getJson('/admin/project-icon-preview?category=%20');

        $response->assertOk()->assertExactJson(['icon' => null, 'svg' => null]);
    }

    public function test_overly_long_category_is_rejected(): void
    {
        $response = $this->actingAs(User::factory()->create())->getJson('/admin/project-icon-preview?category='.str_repeat('a', 61));

        $response->assertUnprocessable()->assertJsonValidationErrors('category');
    }

    public function test_guest_cannot_preview_icons(): void
    {
        $response = $this->getJson('/admin/project-icon-preview?category=Toko');

        $response->assertUnauthorized();
    }
}
