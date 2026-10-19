<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * The admin account comes from the ADMIN_* environment values, never from a known default password.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ProjectSeeder::class,
            ContactSettingSeeder::class,
        ]);
    }
}
