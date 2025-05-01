<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class UnbanExpiredUsers extends Command
{
    protected $signature = 'users:unban';
    protected $description = 'Menghapus status banned dari user yang masa banned-nya sudah habis';

    /**
     * Menjalankan proses utama untuk menghapus status banned dari pengguna yang masa banned-nya telah berakhir.
     * 
     * Langkah-langkah:
     * 1. Mengambil data pengguna yang memiliki status banned sementara dan masa banned telah habis.
     * 2. Menghapus status banned, alasan banned, dan tanggal berakhir banned dari pengguna.
     * 3. Menyimpan perubahan ke database.
     * 4. Menampilkan jumlah pengguna yang berhasil di-unban.
     *
     * @return void
     */
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
