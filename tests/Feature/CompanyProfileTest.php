<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_homepage_defaults_to_indonesian(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSeeText('Dari ide hebat,')
            ->assertSeeText('Bagaimana cara memulai sebuah proyek?')
            ->assertSessionHas('locale', 'id');
    }

    public function test_english_selection_translates_the_page_and_saves_the_preference(): void
    {
        $response = $this->get('/?lang=en');

        $response->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSeeText('From big ideas,')
            ->assertSeeText('How do we get a project started?')
            ->assertSeeText('All rights reserved.')
            ->assertSessionHas('locale', 'en');
    }

    public function test_saved_language_is_used_without_a_query_parameter(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/');

        $response->assertOk()->assertSee('<html lang="en">', false);
    }

    public function test_explicit_language_selection_overrides_the_saved_preference(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/?lang=id');

        $response->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSessionHas('locale', 'id');
    }

    public function test_unsupported_language_falls_back_to_indonesian(): void
    {
        $response = $this->get('/?lang=fr');

        $response->assertOk()
            ->assertSee('<html lang="id">', false)
            ->assertSessionHas('locale', 'id');
    }

    public function test_array_language_input_falls_back_without_an_error(): void
    {
        $response = $this->get('/?lang[]=en');

        $response->assertOk()->assertSee('<html lang="id">', false);
    }

    public function test_contact_links_are_not_invented_when_details_are_missing(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSeeText('Informasi kontak bisnis akan segera tersedia.')
            ->assertDontSee('href="mailto:', false)
            ->assertDontSee('href="https://wa.me/', false);
    }

    public function test_saved_contact_details_render_actionable_links(): void
    {
        Setting::putMany(['email' => 'team@example.com', 'whatsapp' => '0812-3456-7890']);

        $response = $this->get('/?lang=en');

        $response->assertOk()
            ->assertSee('href="mailto:team@example.com?subject=Project%20discussion%20with%20Devgenfour"', false)
            ->assertSee('href="https://wa.me/6281234567890?text=', false)
            ->assertSeeText('0812-3456-7890')
            ->assertDontSeeText('Business contact information will be available soon.');
    }

    public function test_footer_shows_a_map_of_the_saved_address(): void
    {
        Setting::putMany(['address' => 'Indramayu, Jawa Barat']);

        $response = $this->get('/?lang=id');

        $response->assertOk()
            ->assertSee('<iframe src="https://www.google.com/maps?q=Indramayu%2C%20Jawa%20Barat&amp;output=embed"', false)
            ->assertSee('href="https://www.google.com/maps/search/?api=1&amp;query=Indramayu%2C%20Jawa%20Barat"', false);
    }

    public function test_footer_has_no_map_without_an_address(): void
    {
        $response = $this->get('/?lang=id');

        $response->assertOk()->assertDontSee('<iframe', false);
    }

    public function test_address_and_social_links_open_safely_in_a_new_tab(): void
    {
        Setting::putMany(['address' => 'Indramayu, Jawa Barat', 'instagram' => 'https://instagram.com/devgenfour']);

        $response = $this->get('/?lang=id');

        $response->assertOk()
            ->assertSeeText('Indramayu, Jawa Barat')
            ->assertSee('href="https://instagram.com/devgenfour" target="_blank" rel="noopener noreferrer" aria-label="Instagram"', false)
            ->assertDontSee('aria-label="LinkedIn"', false);
    }
}
