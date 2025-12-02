<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->assignRole('admin'); // role assign

        // Normal users
        User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->assignRole('employee');

        User::create([
            'name' => 'User 2',
            'email' => 'user2@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->assignRole('employee');

        User::create([
            'name' => 'User 3',
            'email' => 'user3@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->assignRole('employee');

        User::create([
            'name' => 'User 4',
            'email' => 'user4@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->assignRole('employee');
    }
}
