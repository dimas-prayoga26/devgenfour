<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/settings');

        $response->assertRedirectToRoute('admin.login');
    }

    public function test_settings_page_shows_the_saved_values(): void
    {
        Setting::putMany(['email' => 'halo@devgenfour.com', 'instagram' => 'https://instagram.com/devgenfour']);

        $response = $this->actingAs(User::factory()->create())->get('/admin/settings');

        $response->assertOk()
            ->assertSee('value="halo@devgenfour.com"', false)
            ->assertSee('value="https://instagram.com/devgenfour"', false);
    }

    public function test_saving_stores_filled_values_and_removes_emptied_ones(): void
    {
        Setting::putMany(['linkedin' => 'https://linkedin.com/company/lama']);

        $response = $this->actingAs(User::factory()->create())->put('/admin/settings', [
            'email' => 'halo@devgenfour.com',
            'whatsapp' => '0812-3456-7890',
            'address' => 'Indramayu, Jawa Barat',
            'instagram' => 'https://instagram.com/devgenfour',
            'linkedin' => '',
        ]);

        $response->assertRedirectToRoute('admin.settings.edit')
            ->assertSessionHas('status', 'Pengaturan kontak berhasil disimpan.');
        $this->assertSame([
            'address' => 'Indramayu, Jawa Barat',
            'email' => 'halo@devgenfour.com',
            'instagram' => 'https://instagram.com/devgenfour',
            'whatsapp' => '0812-3456-7890',
        ], Setting::query()->orderBy('key')->pluck('value', 'key')->all());
    }

    public function test_invalid_values_are_rejected_with_indonesian_messages(): void
    {
        $response = $this->actingAs(User::factory()->create())->put('/admin/settings', [
            'email' => 'bukan-email',
            'whatsapp' => 'nomor saya',
            'instagram' => 'javascript:alert(1)',
            'facebook' => 'facebook.com/devgenfour',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Format email tidak valid.',
            'whatsapp' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda +, tanda -, atau tanda kurung.',
            'instagram' => 'Link Instagram harus berupa link lengkap yang diawali https://.',
            'facebook' => 'Link Facebook harus berupa link lengkap yang diawali https://.',
        ]);
        $this->assertDatabaseCount('settings', 0);
    }

    public function test_pasted_google_maps_iframe_code_keeps_only_its_link(): void
    {
        $response = $this->actingAs(User::factory()->create())->put('/admin/settings', [
            'maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12&amp;hl=id" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
        ]);

        $response->assertRedirectToRoute('admin.settings.edit');
        $this->assertSame('https://www.google.com/maps/embed?pb=!1m18!1m12&hl=id', Setting::query()->where('key', 'maps_embed')->value('value'));
    }

    public function test_maps_embed_from_another_site_is_rejected(): void
    {
        $response = $this->actingAs(User::factory()->create())->put('/admin/settings', [
            'maps_embed' => '<iframe src="https://situs-lain.example/peta"></iframe>',
        ]);

        $response->assertSessionHasErrors(['maps_embed' => 'Kode sematan harus berasal dari Google Maps (Bagikan → Sematkan peta).']);
        $this->assertDatabaseCount('settings', 0);
    }

    public function test_guest_cannot_change_settings(): void
    {
        $response = $this->put('/admin/settings', ['email' => 'penyusup@example.com']);

        $response->assertRedirectToRoute('admin.login');
        $this->assertDatabaseCount('settings', 0);
    }
}
