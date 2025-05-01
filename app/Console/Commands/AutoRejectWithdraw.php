<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Withdrawal;
use App\Models\BalanceHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Notif;

class AutoRejectWithdraw extends Command
{
    protected $signature = 'withdraw:auto-reject';
    protected $description = 'Tolak otomatis penarikan yang pending lebih dari 2x24 jam';

    /**
     * Menjalankan proses utama untuk menolak otomatis penarikan yang pending lebih dari 48 jam.
     * 
     * Langkah-langkah:
     * 1. Memulai transaksi database.
     * 2. Menghitung batas waktu (48 jam yang lalu).
     * 3. Mengambil data penarikan yang kadaluarsa.
     * 4. Jika tidak ada data, proses selesai.
     * 5. Memproses setiap penarikan kadaluarsa:
     *    a. Mengubah status penarikan menjadi "rejected".
     *    b. Memperbarui riwayat saldo terkait.
     *    c. Membuat notifikasi untuk pengguna.
     * 6. Melakukan commit transaksi jika semua berhasil.
     * 7. Melakukan rollback jika terjadi kesalahan.
     *
     * @return int Status eksekusi command (SUCCESS atau FAILURE).
     */
    public function handle()
    {
        // 1. Start database transaction
        DB::beginTransaction();

        try {
            // 2. Calculate time limit (48 hours ago)
            $timeLimit = Carbon::now()->subHours(48);
            $this->info("Memeriksa penarikan sebelum: " . $timeLimit->format('Y-m-d H:i:s'));

            // 3. Get expired withdrawal data
            $expiredWithdrawals = Withdrawal::where('status', 'pending')
                ->where('created_at', '<=', $timeLimit)
                ->get();

            // 4. If no data, notify and finish
            if ($expiredWithdrawals->isEmpty()) {
                $this->info("Tidak ada penarikan yang perlu diproses.");
                return Command::SUCCESS;
            }

            $this->info("Menemukan " . $expiredWithdrawals->count() . " penarikan kadaluarsa.");

            // 5. Process each withdrawal
            foreach ($expiredWithdrawals as $withdrawal) {
                $this->line("Memproses penarikan ID: " . $withdrawal->id);

                // a. Update withdrawal status to 'rejected'
                $withdrawal->update([
                    'status' => 'rejected',
                    'note' => 'Ditolak otomatis setelah 48 jam',
                ]);

                // b. Update balance history
                BalanceHistory::where('source_id', $withdrawal->id)
                    ->where('source_type', 'withdrawal')
                    ->update([
                        'status' => 'rejected',
                        'note' => 'Ditolak otomatis setelah 48 jam',
                    ]);

                // c. Create notification for user
                Notif::create([
                    'user_id' => $withdrawal->user_id,
                    'type' => 'system',
                    'target_id' => $withdrawal->user_id,
                    'message' => 'Penarikan ditolak otomatis karena melebihi 48 jam',
                ]);
            }

            // 6. Commit transaction if all successful
            DB::commit();
            $this->info("Berhasil memproses " . $expiredWithdrawals->count() . " penarikan.");
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            // 7. Rollback if error occurs
            DB::rollBack();
            $this->error("Gagal memproses: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}