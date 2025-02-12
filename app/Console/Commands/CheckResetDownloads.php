<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Download;
use Carbon\Carbon;

class CheckResetDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:download_reset_at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Download Limit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('UTC');
        $users = User::where('download_reset_at', '<=', $now)->get();

        foreach ($users as $user) {
            $user->download_reset_at = Carbon::now()->addWeek();
            $user->save();            

            // Hapus download count untuk pengguna ini
            Download::where('user_id', $user->id)->delete();
        }

        $this->info("Reset download limit for " . $users->count() . " user(s).");
    }
}