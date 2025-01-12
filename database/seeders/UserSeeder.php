<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'username' => 'admin',
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'), // Ganti sesuai kebutuhan
                'role' => 'admin',
                'subscription_ends_at' => null,
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
                'download_reset_at' => null,
            ],
            [
                'id' => 2,
                'username' => 'user',
                'name' => 'Regular User',
                'email' => 'user@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'subscription_ends_at' => null,
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
                'download_reset_at' => null,
            ],
            [
                'id' => 3,
                'username' => 'proo',
                'name' => 'Pro User',
                'email' => 'pro@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'pro',
                'subscription_ends_at' => '2025-02-11 19:58:05',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
                'download_reset_at' => null,
            ],
            [
                'id' => 5,
                'username' => 'tesakun',
                'name' => 'Tes Akun',
                'email' => 'tesakun@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'subscription_ends_at' => null,
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
                'download_reset_at' => '2025-01-13 03:53:14',
            ],
            [
                'id' => 7,
                'username' => '4abduu_',
                'name' => 'Abdurrahman Ichwan',
                'email' => 'abdurrahmanichwan77@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'subscription_ends_at' => null,
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
                'download_reset_at' => '2025-01-14 03:59:43',
            ],
        ]);
    }
}
