<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Create the admin role and give it to every existing account, so the admins who could log in before
     * the panel required the role keep their access.
     */
    public function up(): void
    {
        $role = Role::findOrCreate(UserRole::Admin->value, 'web');

        User::query()->each(fn (User $user): User => $user->assignRole($role));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::query()->where('name', UserRole::Admin->value)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
