<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Photo; // Ensure that the Foto class exists in this namespace or update the namespace accordingly
use App\Models\Comment;
use Carbon\Carbon;

class DeleteBannedContent extends Command
{
    protected $signature = 'banned:delete';
    protected $description = 'Menghapus foto dan komentar yang telah dibanned lebih dari 7 hari';

    public function handle()
    {
        $now = Carbon::now('UTC'); // Menggunakan waktu UTC 00

        // Hapus foto yang sudah dibanned lebih dari 7 hari
        $deletedPhotos = Photo::where('banned', 1)
            ->whereNotNull('ban_expires_at')
            ->where('ban_expires_at', '<=', $now)
            ->delete();

        // Hapus komentar yang sudah dibanned lebih dari 7 hari
        $deletedComments = Comment::where('banned', 1)
            ->whereNotNull('ban_expires_at')
            ->where('ban_expires_at', '<=', $now)
            ->delete();

        $this->info("Deleted $deletedPhotos banned photos and $deletedComments banned comments.");
    }
}
