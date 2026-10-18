<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create the first admin account from the ADMIN_* environment values.
     *
     * Without ADMIN_PASSWORD the password is "password", which is only suitable for local
     * development; set ADMIN_PASSWORD before seeding a public server.
     */
    public function run(): void
    {
        $email = config('company.admin.email');

        if (User::query()->where('email', $email)->exists()) {
            $this->command?->info("Akun admin {$email} sudah ada, dilewati.");

            return;
        }

        $configuredPassword = config('company.admin.password');
        $password = filled($configuredPassword) ? $configuredPassword : 'password';

        User::query()->create([
            'name' => config('company.admin.name'),
            'email' => $email,
            'password' => $password,
        ]);

        $this->command?->info("Akun admin dibuat: {$email}");

        if (blank($configuredPassword)) {
            $this->command?->warn('Password: password (isi ADMIN_PASSWORD sebelum seeding di server publik)');
        }
    }
}
