<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SyncBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('verified', 1)->get();

        foreach ($users as $user) {
            $user->syncBalance(); // panggil method dari model tadi
        }
    }
}
