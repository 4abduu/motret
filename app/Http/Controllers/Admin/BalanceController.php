<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Withdrawal;
use App\Models\BalanceHistory;
use App\Models\Notif;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceController extends Controller
{

    /**
     * Menginisialisasi middleware untuk mengautentikasi admin.
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Menampilkan halaman utama manajemen saldo.
     * Mengambil data statistik, persentase perubahan, aktivitas terbaru, dan pengguna dengan saldo tertinggi.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get total verified users
        $totalUsers = User::where('verified', true)->count();
        
        // Get total balance across all users
        $totalBalance = User::where('verified', true)->sum('balance');
        
        // Get pending withdrawals count
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        
        // Get successful withdrawals this month
        $monthlyWithdrawals = Withdrawal::where('status', 'success')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        
        // Calculate percentage changes
        $balancePercentage = $this->calculatePercentageChange(User::where('verified', true), 'balance');
        $withdrawalPercentage = $this->calculatePercentageChange(Withdrawal::where('status', 'success'), 'amount');
        
        // Recent activities
        $recentWithdrawals = Withdrawal::with('user')
            ->latest()
            ->take(10)
            ->get();
        
        // Top users by balance
        $topUsers = User::where('verified', true)
            ->orderBy('balance', 'desc')
            ->take(5)
            ->get();

        return view('admin.manageBalance', compact(
            'totalUsers',
            'totalBalance',
            'pendingWithdrawals',
            'monthlyWithdrawals',
            'balancePercentage',
            'withdrawalPercentage',
            'recentWithdrawals',
            'topUsers'
        ));
    }

    /**
     * Menghitung persentase perubahan data dalam 7 hari terakhir dibandingkan dengan 7 hari sebelumnya.
     *
     * @param \Illuminate\Database\Eloquent\Builder $model Query model yang akan dihitung.
     * @param string|null $sumColumn Kolom yang akan dijumlahkan (opsional).
     * @return string Persentase perubahan dalam format string (misalnya "+50%" atau "-25%").
     */
    private function calculatePercentageChange($model, $sumColumn = null)
    {
        // For user balance
        if ($sumColumn) {
            $last7Days = $model->where('created_at', '>=', now()->subDays(7))->sum($sumColumn);
            $previous7Days = $model->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->sum($sumColumn);
        } 
        // For counts
        else {
            $last7Days = $model->where('created_at', '>=', now()->subDays(7))->count();
            $previous7Days = $model->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
        }

        if ($previous7Days == 0) {
            return $last7Days > 0 ? "+100%" : "0%";
        }

        $percentageChange = (($last7Days - $previous7Days) / $previous7Days) * 100;
        $formattedPercentage = round($percentageChange, 2);
        
        return ($formattedPercentage > 0 ? "+" : "") . $formattedPercentage . "%";
    }

    /**
     * Menampilkan daftar penarikan saldo.
     *
     * @return \Illuminate\View\View
     */
    public function withdrawals()
    {
        $withdrawals = Withdrawal::with('user')->get();
        return view('admin.balance.withdrawals', compact('withdrawals'));
    }

    /**
     * Menampilkan daftar pengguna dengan saldo mereka.
     * Data diurutkan berdasarkan saldo user tertinggi.
     *
     * @return \Illuminate\View\View
     */
    public function balanceUser()
    {
        $users = User::where('verified', true)
                    ->orderBy('balance', 'desc')
                    ->get(); 
        return view('admin.balance.balanceList', compact('users'));
    }

    /**
     * Menampilkan riwayat saldo pengguna tertentu.
     * Termasuk total pemasukan, total penarikan, dan saldo saat ini.
     *
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi filter bulan dan tahun.
     * @param int $userId ID pengguna.
     * @return \Illuminate\View\View
     */
    public function historyBalance(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
    
        $month = $request->input('month');
        $year = $request->input('year');
    
        // Query utama untuk data riwayat
        $mainQuery = BalanceHistory::where('user_id', $user->id);
        
        // Clone query untuk filter bulan/tahun
        $filteredQuery = clone $mainQuery;
    
        if ($year) {
            $mainQuery->whereYear('created_at', $year);
        }
        if ($month) {
            $mainQuery->whereMonth('created_at', $month);
        }
    
        // Clone query yang sudah difilter untuk hitung total
        $totalIncomeQuery = clone $mainQuery;
        $totalIncome = $totalIncomeQuery->where('type', 'income')->sum('amount');
    
        $totalWithdrawalQuery = clone $mainQuery;
        $totalWithdrawal = $totalWithdrawalQuery->where('type', 'withdrawal')
                                      ->where('status', 'success')
                                      ->sum('amount');
    
        // Ambil data riwayat dari query utama yang sudah difilter bulan/tahun
        $riwayat = $mainQuery->latest()->paginate(10);
    
        return view('admin.balance.balanceHistory', [
            'riwayat'         => $riwayat,
            'totalIncome'     => $totalIncome,
            'totalWithdrawal' => $totalWithdrawal,
            'currentBalance'  => $user->balance,
            'month'           => $month,
            'year'            => $year,
            'user'            => $user,
        ]);
    }

    /**
     * Menyetujui permintaan penarikan saldo.
     * Memperbarui status penarikan menjadi "success", mengurangi saldo pengguna, dan membuat notifikasi.
     *
     * @param int $id ID permintaan penarikan.
     * @return \Illuminate\Http\JsonResponse
     */
    public function accPenarikan($id)
    {

        DB::beginTransaction();

        try {
                $withdrawal = Withdrawal::findOrFail($id);
                
                if ($withdrawal->status !== 'pending') {
                    throw new \Exception('Permintaan sudah diproses sebelumnya');
                }

                $withdrawal->update(['status' => 'success']);
                
                
                $balanceHistory = BalanceHistory::where([
                    'source_id' => $withdrawal->id,
                    'source_type' => 'withdrawal',
                ])->firstOrFail();
    
                $balanceHistory->status = 'success';
                $balanceHistory->save();

                 $user = $withdrawal->user;
                 $user->balance -= $withdrawal->amount;
                 $user->save();

                Notif::create([
                    'notify_for' => $user->id,
                    'notify_from' => null,
                    'target_id' => $user->id,
                    'type' => 'system',
                    'message' => "Penarikan saldo sejumlah ". $withdrawal->amount ." ke ". $withdrawal->method ." dengan Nomor ". $withdrawal->destination ." a/n ". $withdrawal->destination_name ." berhasil. Silakan cek saldo Anda.",

                ]);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil menyetujui permintaan.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui permintaan. '. $e->getMessage()], 400);
        }
    }

    /**
     * Menolak permintaan penarikan saldo.
     * Memperbarui status penarikan menjadi "rejected" dan membuat notifikasi.
     *
     * @param int $id ID permintaan penarikan.
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectPenarikan($id)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $withdrawal = Withdrawal::findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                throw new \Exception('Permintaan sudah diproses sebelumnya');
            }

            $withdrawal->update(['status' => 'rejected']);

            $balanceHistory = BalanceHistory::where([
                'source_id' => $withdrawal->id,
                'source_type' => 'withdrawal',
            ])->firstOrFail();

            $balanceHistory->status = 'rejected';
            $balanceHistory->save();

            Notif::create([
                'notify_for' => $withdrawal->user_id,
                'notify_from' => null,
                'target_id' => $withdrawal->user_id,
                'type' => 'system',
                'message' => "Penarikan saldo sejumlah ". $withdrawal->amount ." ke ". $withdrawal->method ." dengan Nomor ". $withdrawal->destination ." a/n ". $withdrawal->destination_name ." ditolak. Silakan coba lagi atau hubungi admin untuk informasi lebih lanjut.",
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil menolak permintaan.'], 200);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menolak permintaan. '. $e->getMessage()], 400);

        }

    }

    /**
     * Menghapus permintaan penarikan saldo.
     * Menghapus data penarikan dan riwayat saldo terkait dari database.
     *
     * @param int $id ID permintaan penarikan.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePenarikan($id)
    {
        DB::beginTransaction();

        try {
            $withdrawal = Withdrawal::findOrFail($id);
            $withdrawal->delete();

            $balanceHistory = BalanceHistory::where([
                'source_id' => $withdrawal->id,
                'source_type' => 'withdrawal',
            ])->firstOrFail();

            $balanceHistory->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil menghapus penarikan.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus penarikan. '. $e->getMessage()], 400);
        }
    }
}
