<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Photo;
use App\Models\Reply;
use App\Models\Comment;
use Carbon\Carbon;

class DeleteBannedContent extends Command
{
    protected $signature = 'banned:delete';
    protected $description = 'Menghapus foto dan komentar/balasan yang telah dibanned lebih dari 7 hari';

    /**
     * Menjalankan proses utama untuk menghapus konten yang telah dibanned lebih dari 7 hari.
     * 
     * Langkah-langkah:
     * 1. Mengambil foto yang telah dibanned lebih dari 7 hari.
     * 2. Menghapus file asli dan versi low-res dari storage.
     * 3. Menghapus data foto dari database.
     * 4. Menghapus komentar dan balasan yang telah dibanned lebih dari 7 hari.
     * 5. Menampilkan jumlah konten yang berhasil dihapus.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now('UTC');
    
        // Hapus foto yang sudah dibanned lebih dari 7 hari
        $photosToDelete = Photo::where('banned', 1)
            ->whereNotNull('ban_expires_at')
            ->where('ban_expires_at', '<=', $now)
            ->get();
    
        $deletedPhotosCount = 0;
    
        foreach ($photosToDelete as $photo) {
            try {
                // Hapus file asli dari storage
                $filePath = storage_path('app/public/' . $photo->path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
    
                // Hapus file low-res dari storage
                $lowResPath = storage_path('app/public/low_res_photos/' . basename($photo->path));
                if (file_exists($lowResPath)) {
                    unlink($lowResPath);
                }
    
                // Hapus data foto dari database
                $photo->delete();
                $deletedPhotosCount++;
            } catch (\Exception $e) {
                $this->error("Gagal menghapus foto ID {$photo->id}: " . $e->getMessage());
            }
        }
    
        // Hapus komentar yang sudah dibanned lebih dari 7 hari
        $deletedComments = Comment::where('banned', 1)
            ->whereNotNull('ban_expires_at')
            ->where('ban_expires_at', '<=', $now)
            ->delete();

        $deletedReplies = Reply::where('banned', 1)
            ->whereNotNull('ban_expires_at')
            ->where('ban_expires_at', '<=', $now)
            ->delete();
    
        $this->info("Deleted $deletedPhotosCount banned photos, $deletedComments banned comments and $deletedReplies banned replies.");
    }
}
