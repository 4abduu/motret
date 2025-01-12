<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Ambil data dari tabel 'users'
        $users = DB::table('users')->get();

        // Isi data ke dalam tabel 'users' menggunakan model User atau langsung menggunakan DB
        foreach ($users as $user) {
            User::create([
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role,
                'download_reset_at' => $user->download_reset_at,
            ]);
        }
    }
}
