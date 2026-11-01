<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPERADMIN_EMAIL');
        $password = env('SUPERADMIN_PASSWORD');

        if (! $email || ! $password) {
            throw new RuntimeException(
                'SUPERADMIN_EMAIL dan SUPERADMIN_PASSWORD harus diisi pada .env sebelum seeding. '.
                'Jangan menggunakan kredensial hard-coded.'
            );
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SUPERADMIN_NAME', 'Superadmin'),
                'password' => $password, // di-hash otomatis oleh cast 'hashed'
                'role' => UserRole::Superadmin,
                'is_active' => true,
            ],
        );
    }
}
