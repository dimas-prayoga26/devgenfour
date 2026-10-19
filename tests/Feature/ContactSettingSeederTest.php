<?php

namespace Tests\Feature;

use App\Models\Setting;
use Database\Seeders\ContactSettingSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ContactSettingSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_fills_the_contact_details_shown_on_the_homepage(): void
    {
        $this->artisan('db:seed', ['--class' => ContactSettingSeeder::class])
            ->expectsOutputToContain('Pengaturan kontak diisi: email, whatsapp, address, instagram, linkedin, youtube')
            ->assertSuccessful();

        $this->get('/?lang=id')
            ->assertOk()
            ->assertSee('href="mailto:halo@devgenfour.com', false)
            ->assertSee('href="https://wa.me/6281234567890', false)
            ->assertSee('<iframe src="https://www.google.com/maps?q=Indramayu%2C%20Jawa%20Barat&amp;output=embed"', false)
            ->assertSee('href="https://instagram.com/devgenfour"', false)
            ->assertDontSeeText('Informasi kontak bisnis akan segera tersedia.');
    }

    public function test_keeps_the_values_the_admin_already_saved(): void
    {
        Setting::putMany(['email' => 'tim@devgenfour.com', 'whatsapp' => '0899-0000-1111']);

        $this->artisan('db:seed', ['--class' => ContactSettingSeeder::class])
            ->expectsOutputToContain('Pengaturan kontak diisi: address, instagram, linkedin, youtube')
            ->assertSuccessful();

        $this->assertSame('tim@devgenfour.com', Setting::query()->where('key', 'email')->value('value'));
        $this->assertSame('0899-0000-1111', Setting::query()->where('key', 'whatsapp')->value('value'));
    }

    public function test_skips_when_every_contact_detail_is_already_saved(): void
    {
        $this->seed(ContactSettingSeeder::class);

        $this->artisan('db:seed', ['--class' => ContactSettingSeeder::class])
            ->expectsOutputToContain('Pengaturan kontak sudah terisi, dilewati.')
            ->assertSuccessful();

        $this->assertDatabaseCount('settings', 6);
    }
}
