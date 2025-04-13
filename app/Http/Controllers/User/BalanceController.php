<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\BalanceHistory;
use App\Models\Notif;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BalanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $balance = $user->balance;

        return view('user.withdrawal', compact('balance'));
    }

    public function storeWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'method' => 'required|string',
            'destination' => 'required|string',
            'destination_name' => 'required|string',
            'amount' => 'required|numeric',
            'note' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check if the user has enough balance
        if ($request->amount > $user->balance) {
            return back()->withErrors([
                'amount' => 'Saldo tidak mencukupi untuk penarikan ini.'
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'status' => 'pending',
                'method' => $request->method,
                'destination' => $request->destination,
                'destination_name' => $request->destination_name,
                'note' => $request->note,
            ]);

            // Create balance history
            BalanceHistory::create([
                'user_id' => $user->id,
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'source_id' => $withdrawal->id,
                'source_type' => 'withdrawal',
                'status' => 'pending',
                'method' => $request->method,
                'destination' => $request->destination,
                'destination_name' => $request->destination_name,
                'note' => $request->note,
            ]);

            // Create notification
            Notif::create([
                'notify_for' => $user->id,
                'notify_from' => null,
                'target_id' => $user->id,
                'type' => 'system',
                'message' => 'Pengajuan penarikan dana telah diterima, mohon tunggu 2x24 jam untuk melihat hasilnya.',
            ]);

            DB::commit();

            return back()->with('success', 'Pengajuan penarikan berhasil dikirim. Mohon tunggu 2x24 jam kerja untuk proses penarikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal Error: '.$e->getMessage());
            
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat mengajukan penarikan. Silakan coba lagi.'
            ])->withInput();
        }
    }

    public function historyBalance(Request $request)
    {
        $user = Auth::user();
        
        $month = $request->input('month');
        $year = $request->input('year');
    
        // Query utama untuk data riwayat
        $mainQuery = BalanceHistory::where('user_id', $user->id);
        
        // Clone query untuk filter bulan/tahun
        $filteredQuery = clone $mainQuery;
    
        // Terapkan filter bulan/tahun pada query utama
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
    
        Log::info('Balance History Data: ', $riwayat->toArray());
    
        return view('user.balance_history', [
            'riwayat'         => $riwayat,
            'totalIncome'     => $totalIncome,
            'totalWithdrawal' => $totalWithdrawal,
            'currentBalance'  => $user->balance,
            'month'           => $month,
            'year'            => $year,
        ]);
    }
}
