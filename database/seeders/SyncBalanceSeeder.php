<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
