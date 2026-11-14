<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('app:create-admin')]
#[Description('Buat akun admin untuk mengelola portofolio')]
class CreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = text('Nama', required: true);

        $email = text('Email', required: true, validate: fn (string $value): ?string => match (true) {
            filter_var($value, FILTER_VALIDATE_EMAIL) === false => 'Format email tidak valid.',
            User::query()->where('email', $value)->exists() => 'Email ini sudah terdaftar.',
            default => null,
        });

        $password = password('Password (minimal 8 karakter)', required: true, validate: fn (string $value): ?string => mb_strlen($value) < 8
            ? 'Password minimal 8 karakter.'
            : null);

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ])->assignRole(Role::findOrCreate(UserRole::Admin->value));

        $this->components->info("Akun admin {$email} berhasil dibuat.");

        return self::SUCCESS;
    }
}
