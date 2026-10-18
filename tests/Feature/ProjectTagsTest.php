<?php

namespace Tests\Feature;

use App\Enums\ProjectTone;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectTagsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_existing_tags_are_reused_and_only_new_tags_are_translated(): void
    {
        Storage::fake('public');
        $this->fakeDeepL(['Kelola Pesanan' => 'Order Management']);
        Tag::factory()->create(['name' => 'Katalog Produk', 'name_en' => 'Product Catalog']);

        $this->actingAs(User::factory()->create())->post('/admin/projects', $this->payload(['katalog produk', 'Kelola Pesanan']));

        $project = Project::query()->sole();
        $this->assertSame(['Katalog Produk', 'Kelola Pesanan'], $project->tags->pluck('name')->all());
        $this->assertSame(['Product Catalog', 'Order Management'], $project->tags->pluck('name_en')->all());
        $this->assertDatabaseCount('tags', 2);
        Http::assertSent(fn (Request $request): bool => $request['text'] === ['Kelola Pesanan']);
        Http::assertNotSent(fn (Request $request): bool => in_array('katalog produk', $request['text'], true) || in_array('Katalog Produk', $request['text'], true));
    }

    public function test_tags_keep_the_order_the_admin_entered(): void
    {
        Storage::fake('public');
        $this->fakeDeepL([]);

        $this->actingAs(User::factory()->create())->post('/admin/projects', $this->payload(['Reservasi', 'Antarmuka', 'Laporan']));

        $this->assertSame(['Reservasi', 'Antarmuka', 'Laporan'], Project::query()->sole()->tags->pluck('name')->all());
    }

    public function test_removing_a_tag_from_a_project_keeps_the_tag_for_reuse(): void
    {
        Storage::fake('public');
        $this->fakeDeepL([]);
        $project = Project::factory()->withTags(['Katalog Produk' => 'Product Catalog', 'Pembayaran' => 'Payment'])->create();
        $removedTag = Tag::query()->where('name', 'Pembayaran')->sole();

        $this->actingAs(User::factory()->create())->put("/admin/projects/{$project->id}", $this->payload(['Katalog Produk']));

        $this->assertSame(['Katalog Produk'], $project->refresh()->tags->pluck('name')->all());
        $this->assertModelExists($removedTag);
        $this->assertSame(['Katalog Produk', 'Pembayaran'], Tag::suggestions());
    }

    public function test_tags_remain_after_their_project_is_deleted(): void
    {
        Storage::fake('public');
        $project = Project::factory()->withTags(['Reservasi' => 'Bookings'])->create();

        $this->actingAs(User::factory()->create())->delete("/admin/projects/{$project->id}");

        $this->assertDatabaseCount('project_tag', 0);
        $this->assertSame(['Reservasi'], Tag::suggestions());
    }

    public function test_english_page_shows_english_tag_names_with_an_indonesian_fallback(): void
    {
        Project::factory()->withTags(['Pembayaran' => 'Payment', 'Laporan' => null])->create();

        $response = $this->get('/?lang=en');

        $response->assertOk()
            ->assertSeeText('Payment')
            ->assertSeeText('Laporan')
            ->assertDontSeeText('Pembayaran');
    }

    /**
     * @param  list<string>  $tags
     * @return array<string, mixed>
     */
    private function payload(array $tags): array
    {
        return [
            'name' => 'Nusa Commerce',
            'category' => 'Toko Online',
            'description' => 'Contoh toko online.',
            'tags' => $tags,
            'tone' => ProjectTone::Mint->value,
            'is_published' => '1',
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ];
    }
}
