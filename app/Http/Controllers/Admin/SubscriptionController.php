<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPriceSystem;
use Illuminate\Http\Request;
use App\Models\SubscriptionCombo;
use App\Models\SubscriptionSystem;
use App\Models\SubscriptionUser;
use App\Models\SubscriptionPriceUser;
use App\Models\Transaction;
use Carbon\Carbon;


class SubscriptionController extends Controller
{
    public function index()
    {
        // Ambil jumlah data untuk card stats
        $transactionCount = Transaction::count();
        $userPriceCount = SubscriptionPriceUser::count();
        $systemPriceCount = SubscriptionPriceSystem::count();
        $userSubscriptionCount = SubscriptionUser::count();
        $systemSubscriptionCount = SubscriptionSystem::count();
        $comboSubscriptionCount = SubscriptionCombo::count();

        // Hitung persentase
        $transactionPercentage = $this->calculatePercentageChange(Transaction::class);
        $userPricePercentage = $this->calculatePercentageChange(SubscriptionPriceUser::class);
        $systemPricePercentage = $this->calculatePercentageChange(SubscriptionPriceSystem::class);
        $userSubscriptionPercentage = $this->calculatePercentageChange(SubscriptionUser::class);
        $systemSubscriptionPercentage = $this->calculatePercentageChange(SubscriptionSystem::class);
        $comboSubscriptionPercentage = $this->calculatePercentageChange(SubscriptionCombo::class);

        // Ambil data terbaru untuk recent activities
        $recentPriceSystemChanges = SubscriptionPriceSystem::
            latest()
            ->take(1)
            ->get();

        $recentPriceUserChanges = SubscriptionPriceUser::with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentSystemSubscriptions = SubscriptionSystem::with(['user', 'transaction'])
            ->latest()
            ->take(5)
            ->get();

        $recentUserSubscriptions = SubscriptionUser::with(['user', 'targetUser', 'transaction'])
            ->latest()
            ->take(5)
            ->get();

        $recentComboSubscriptions = SubscriptionCombo::with(['user', 'targetUser', 'transaction'])
            ->latest()
            ->take(5)
            ->get();

        // Gabungkan semua recent activities
        $recentActivities = collect()
            ->merge($recentPriceSystemChanges->map(function ($item) {
                return [
                    'type' => 'price_system_change',
                    'user' => $item->user,
                    'created_at' => $item->created_at,
                    'message' => "Admin baru saja mengubah harga langganan sistem {$item->duration}."
                ];
            }))
            ->merge($recentPriceUserChanges->map(function ($item) {
                return [
                    'type' => 'price_user_change',
                    'user' => $item->user,
                    'created_at' => $item->created_at,
                    'message' => "{$item->user->name} mengubah harga langganan user."
                ];
            }))
            ->merge($recentSystemSubscriptions->map(function ($item) {
                return [
                    'type' => 'system_subscription',
                    'user' => $item->user,
                    'created_at' => $item->created_at,
                    'message' => "{$item->user->name} melakukan langganan sistem."
                ];
            }))
            ->merge($recentUserSubscriptions->map(function ($item) {
                return [
                    'type' => 'user_subscription',
                    'user' => $item->user,
                    'target_user' => $item->targetUser,
                    'created_at' => $item->created_at,
                    'message' => "{$item->user->name} melakukan langganan ke {$item->targetUser->name}."
                ];
            }))
            ->merge($recentComboSubscriptions->map(function ($item) {
                return [
                    'type' => 'combo_subscription',
                    'user' => $item->user,
                    'target_user' => $item->targetUser,
                    'created_at' => $item->created_at,
                    'message' => "{$item->user->name} melakukan langganan kombo ke {$item->targetUser->name}."
                ];
            }))
            ->sortByDesc('created_at') // Urutkan berdasarkan created_at terbaru
            ->take(10); // Ambil 10 aktivitas terbaru

        // Kirim data ke view
        return view('admin.subscriptions.subscriptions', compact(
            'transactionCount',
            'userPriceCount',
            'systemPriceCount',
            'userSubscriptionCount',
            'systemSubscriptionCount',
            'comboSubscriptionCount',
            'recentActivities',
            'transactionPercentage',
            'userPricePercentage',
            'systemPricePercentage',
            'userSubscriptionPercentage',
            'systemSubscriptionPercentage',
            'comboSubscriptionPercentage'
        ));
    }

    private function calculatePercentageChange($model)
    {
        // Hitung jumlah data 7 hari terakhir
        $last7DaysCount = $model::where('created_at', '>=', Carbon::now()->subDays(7))->count();
    
        // Hitung jumlah data 7 hari sebelumnya (8-14 hari yang lalu)
        $previous7DaysCount = $model::whereBetween('created_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])->count();
    
        // Jika periode sebelumnya 0 dan sekarang ada data, maka 100% kenaikan
        if ($previous7DaysCount == 0) {
            return $last7DaysCount > 0 ? "+100%" : "0%";
        }
    
        // Hitung persentase perubahan
        $percentageChange = (($last7DaysCount - $previous7DaysCount) / $previous7DaysCount) * 100;
    
        // Tambahkan tanda "+" jika ada kenaikan
        $formattedPercentage = round($percentageChange, 2);
        if ($formattedPercentage > 0) {
            return "+" . $formattedPercentage . "%";
        } elseif ($formattedPercentage < 0) {
            return $formattedPercentage . "%"; // Tanda minus otomatis sudah ada
        } else {
            return "0%";
        }
    }

    public function priceSystem(){
        $prices = SubscriptionPriceSystem::all();
        return view('admin.subscriptions.subsSystem', compact('prices'));
    }

    public function updatePriceSystem(Request $request, $id)
    {
        $request->validate([
            'duration' => 'required|in:1_month,3_months,6_months,1_year',
            'price' => 'required|numeric|min:0',
        ]);

        $price = SubscriptionPriceSystem::findOrFail($id);
        $price->update($request->all());

        return redirect()->back()->with('success', 'Harga langganan sistem berhasil diubah.');
    }

    public function transactions()
    {
        $transactions = Transaction::orderBy('created_at', 'desc')->get();
        return view('admin.subscriptions.transactions', compact('transactions'));
    }

    public function systemPrices()
    {
        $prices = SubscriptionPriceSystem::orderBy('created_at', 'desc')->get();
        return view('admin.subscriptions.priceSubsSystem', compact('prices'));
    }

    public function userPrices()
    {
        $prices = SubscriptionPriceUser::orderBy('created_at', 'desc')->get();
        return view('admin.subscriptions.priceSubsUser', compact('prices'));
    }

    public function userSubscriptions()
    {
        $subscriptions = SubscriptionUser::orderBy('created_at', 'desc')->get();
        return view('admin.subscriptions.subsUser', compact('subscriptions'));
    }

    public function systemSubscriptions()
    {
        $subscriptions = SubscriptionSystem::orderBy('created_at', 'desc')->get();
        return view('admin.subscriptions.subsSystem', compact('subscriptions'));
    }

    public function comboSubscriptions()
    {
        $subscriptions = SubscriptionCombo::orderBy('created_at', 'desc')->get();
        return view('admin.subscriptions.subsCombo', compact('subscriptions'));
    }
}