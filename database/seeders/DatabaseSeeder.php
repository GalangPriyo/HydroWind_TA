<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Admins
        User::create([
            'name' => 'Admin 1',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin 2',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Users
        User::create([
            'name' => 'User 1',
            'email' => 'user1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'User 2',
            'email' => 'user2@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'User 3',
            'email' => 'user3@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'User 4',
            'email' => 'user4@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'User 5',
            'email' => 'user5@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}
