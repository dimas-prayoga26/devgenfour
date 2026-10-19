<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ContactSettingSeeder extends Seeder
{
    /**
     * Fill the contact details and social media links shown on the homepage.
     *
     * Only keys that are not saved yet are filled, so running the seeder again never overwrites
     * what the admin has changed on the settings page.
     */
    public function run(): void
    {
        $created = collect($this->defaults())
            ->filter(fn (string $value, string $key): bool => Setting::query()->firstOrCreate(['key' => $key], ['value' => $value])->wasRecentlyCreated)
            ->keys();

        if ($created->isEmpty()) {
            $this->command?->info('Pengaturan kontak sudah terisi, dilewati.');

            return;
        }

        $this->command?->info('Pengaturan kontak diisi: '.$created->implode(', '));
        $this->command?->warn('Ganti dengan data asli di halaman Admin → Pengaturan.');
    }

    /**
     * @return array<string, string>
     */
    private function defaults(): array
    {
        return [
            'email' => 'halo@devgenfour.com',
            'whatsapp' => '0812-3456-7890',
            'address' => 'Indramayu, Jawa Barat',
            'instagram' => 'https://instagram.com/devgenfour',
            'linkedin' => 'https://linkedin.com/company/devgenfour',
            'youtube' => 'https://youtube.com/@devgenfour',
        ];
    }
}
