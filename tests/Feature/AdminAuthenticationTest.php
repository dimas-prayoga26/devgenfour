<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_visiting_the_admin_panel_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/projects');

        $response->assertRedirectToRoute('admin.login');
    }

    public function test_login_page_renders_for_guests(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk()->assertSeeText('Masuk ke admin');
    }

    public function test_valid_credentials_log_the_admin_in(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'rahasia-123']);

        $response = $this->post('/admin/login', ['email' => $admin->email, 'password' => 'rahasia-123']);

        $response->assertRedirectToRoute('admin.projects.index');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_wrong_password_is_rejected_with_a_message(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'rahasia-123']);

        $response = $this->from('/admin/login')->post('/admin/login', ['email' => $admin->email, 'password' => 'salah-sandi']);

        $response->assertRedirect('/admin/login')
            ->assertSessionHasErrors(['email' => 'Email atau password salah.']);
        $this->assertGuest();
    }

    public function test_account_without_the_admin_role_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'rahasia-123']);

        $response = $this->from('/admin/login')->post('/admin/login', ['email' => $user->email, 'password' => 'rahasia-123']);

        $response->assertRedirect('/admin/login')
            ->assertSessionHasErrors(['email' => 'Email atau password salah.']);
        $this->assertGuest();
    }

    public function test_account_without_the_admin_role_is_forbidden_from_the_admin_panel(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/admin/projects');

        $response->assertForbidden();
    }

    public function test_account_without_the_admin_role_can_still_log_out(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post('/admin/logout');

        $response->assertRedirectToRoute('admin.login');
        $this->assertGuest();
    }

    public function test_role_migration_keeps_existing_accounts_able_to_use_the_admin_panel(): void
    {
        $existingAdmin = User::factory()->create();
        $migration = require database_path('migrations/2026_11_14_232417_assign_admin_role_to_existing_users.php');

        $migration->up();

        $this->assertTrue($existingAdmin->refresh()->hasRole(UserRole::Admin));
        $this->actingAs($existingAdmin)->get('/admin/projects')->assertOk();
    }

    public function test_login_is_blocked_after_five_failed_attempts(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'rahasia-123']);

        foreach (range(1, 5) as $attempt) {
            $this->post('/admin/login', ['email' => $admin->email, 'password' => 'salah-sandi']);
        }
        $response = $this->post('/admin/login', ['email' => $admin->email, 'password' => 'rahasia-123']);

        $response->assertSessionHasErrors(['email' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.']);
        $this->assertGuest();
    }

    public function test_logged_in_admin_visiting_login_is_sent_to_the_project_list(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/login');

        $response->assertRedirectToRoute('admin.projects.index');
    }

    public function test_logout_ends_the_admin_session(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $response = $this->post('/admin/logout');

        $response->assertRedirectToRoute('admin.login');
        $this->assertGuest();
    }

    public function test_console_command_creates_an_admin_account(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Nama', 'Admin Devgenfour')
            ->expectsQuestion('Email', 'admin@devgenfour.test')
            ->expectsQuestion('Password (minimal 8 karakter)', 'rahasia-123')
            ->assertSuccessful();

        $admin = User::query()->where('email', 'admin@devgenfour.test')->sole();
        $this->assertSame('Admin Devgenfour', $admin->name);
        $this->assertTrue(Hash::check('rahasia-123', $admin->password));
        $this->assertTrue($admin->hasRole(UserRole::Admin));
    }
}
