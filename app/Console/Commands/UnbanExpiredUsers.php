<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class UnbanExpiredUsers extends Command
{
    protected $signature = 'users:unban';
    protected $description = 'Menghapus status banned dari user yang masa banned-nya sudah habis';

    public function handle()
    {
        $now = Carbon::now('UTC');
        $users = User::where('banned', true)
                    ->where('banned_type', 'temporary')
                    ->whereNotNull('banned_until')
                    ->where('banned_until', '<=', $now)
                    ->get();

        foreach ($users as $user) {
            $user->banned = false;
            $user->banned_until = null;
            $user->banned_reason = null;
            $user->banned_type = null;
            $user->save();
        }

        $this->info("Unbanned " . $users->count() . " user(s).");
    }
}
