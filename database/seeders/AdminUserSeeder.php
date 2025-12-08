<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gsg.unila.ac.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'A',
                'email_verified_at' => now(),
            ]
        );

        // Create demo user
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('user123'),
                'role' => 'U',
                'email_verified_at' => now(),
            ]
        );
    }
}


