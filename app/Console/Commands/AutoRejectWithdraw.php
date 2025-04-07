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