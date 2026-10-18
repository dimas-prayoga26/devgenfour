<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PortfolioDisplayTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_homepage_lists_published_projects_in_their_display_order(): void
    {
        Project::factory()->create(['name' => 'Proyek Kedua', 'sort_order' => 2]);
        Project::factory()->create(['name' => 'Proyek Pertama', 'sort_order' => 1]);
        Project::factory()->hidden()->create(['name' => 'Proyek Draf', 'sort_order' => 0]);

        $response = $this->get('/?lang=id');

        $response->assertOk()
            ->assertSeeTextInOrder(['Proyek Pertama', 'Proyek Kedua'])
            ->assertDontSeeText('Proyek Draf');
    }

    public function test_english_page_uses_the_english_copy_when_provided(): void
    {
        Project::factory()->withEnglishCopy()->create(['category' => 'Toko Online']);

        $response = $this->get('/?lang=en');

        $response->assertOk()
            ->assertSeeText('Online Store')
            ->assertDontSeeText('Toko Online');
    }

    public function test_english_page_falls_back_to_the_indonesian_copy_when_english_is_empty(): void
    {
        Project::factory()->create(['description' => 'Deskripsi hanya dalam Bahasa Indonesia.']);

        $response = $this->get('/?lang=en');

        $response->assertOk()->assertSeeText('Deskripsi hanya dalam Bahasa Indonesia.');
    }

    public function test_homepage_renders_the_stored_project_icon(): void
    {
        Project::factory()->create(['icon' => 'minus', 'icon_svg' => '<path d="M5 12h14" />']);

        $response = $this->get('/?lang=id');

        $response->assertOk()->assertSee('<path d="M5 12h14" /></svg>', false);
    }

    public function test_project_names_are_escaped_on_the_homepage(): void
    {
        Project::factory()->create(['name' => '<script>alert(1)</script>']);

        $response = $this->get('/?lang=id');

        $response->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_portfolio_section_is_omitted_when_nothing_is_published(): void
    {
        Project::factory()->hidden()->create();

        $response = $this->get('/?lang=id');

        $response->assertOk()->assertDontSee('id="portfolio"', false);
    }
}
