<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Creates the first admin account when no users exist.
     * Credentials come from ADMIN_EMAIL / ADMIN_PASSWORD; without a password a random one is generated
     * and printed once — nothing secret lives in the repository.
     */
    public function run(): void
    {
        if (User::exists()) {
            return;
        }

        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD') ?: Str::password(16);

        User::create([
            'name' => env('ADMIN_NAME', 'Administrator'),
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        if (! env('ADMIN_PASSWORD')) {
            $this->command?->warn("Admin account created: {$email} / {$password}  (change it after first login)");
        }
    }
}
