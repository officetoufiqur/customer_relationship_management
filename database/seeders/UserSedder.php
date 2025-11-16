<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'User 2',
                'email' => 'user2@gmail.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'User 3',
                'email' => 'user3@gmail.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'User 4',
                'email' => 'user4@gmail.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'User 5',
                'email' => 'user5@gmail.com',
                'password' => bcrypt('password'),
            ],
        ]);
    }
}
