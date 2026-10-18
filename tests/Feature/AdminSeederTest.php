<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_creates_the_admin_with_the_configured_credentials(): void
    {
        config(['company.admin' => ['name' => 'Admin Devgenfour', 'email' => 'admin@devgenfour.test', 'password' => 'rahasia-123']]);

        $this->artisan('db:seed', ['--class' => AdminSeeder::class])
            ->expectsOutputToContain('Akun admin dibuat: admin@devgenfour.test')
            ->doesntExpectOutputToContain('Password:')
            ->assertSuccessful();

        $admin = User::query()->where('email', 'admin@devgenfour.test')->sole();
        $this->assertSame('Admin Devgenfour', $admin->name);
        $this->assertTrue(Hash::check('rahasia-123', $admin->password));
    }

    public function test_falls_back_to_password_when_none_is_configured(): void
    {
        config(['company.admin' => ['name' => 'Admin Devgenfour', 'email' => 'admin@devgenfour.test', 'password' => '']]);

        $this->artisan('db:seed', ['--class' => AdminSeeder::class])
            ->expectsOutputToContain('Password: password')
            ->assertSuccessful();

        $admin = User::query()->where('email', 'admin@devgenfour.test')->sole();
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_leaves_an_existing_admin_untouched(): void
    {
        User::factory()->create(['email' => 'admin@devgenfour.test', 'password' => 'password-lama']);
        config(['company.admin' => ['name' => 'Admin Devgenfour', 'email' => 'admin@devgenfour.test', 'password' => 'password-baru']]);

        $this->artisan('db:seed', ['--class' => AdminSeeder::class])
            ->expectsOutputToContain('Akun admin admin@devgenfour.test sudah ada, dilewati.')
            ->assertSuccessful();

        $admin = User::query()->where('email', 'admin@devgenfour.test')->sole();
        $this->assertTrue(Hash::check('password-lama', $admin->password));
    }
}
