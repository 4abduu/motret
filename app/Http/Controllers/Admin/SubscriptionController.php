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
            'recentActivities'
        ));
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
        $transactions = Transaction::all();
        return view('admin.subscriptions.transactions', compact('transactions'));
    }

    public function systemPrices()
    {
        $prices = SubscriptionPriceSystem::all();
        return view('admin.subscriptions.priceSubsSystem', compact('prices'));
    }

    public function userPrices()
    {
        $prices = SubscriptionPriceUser::all();
        return view('admin.subscriptions.priceSubsUser', compact('prices'));
    }

    public function userSubscriptions()
    {
        $subscriptions = SubscriptionUser::all();
        return view('admin.subscriptions.subsUser', compact('subscriptions'));
    }

    public function systemSubscriptions()
    {
        $subscriptions = SubscriptionSystem::all();
        return view('admin.subscriptions.subsSystem', compact('subscriptions'));
    }

    public function comboSubscriptions()
    {
        $subscriptions = SubscriptionCombo::all();
        return view('admin.subscriptions.subsCombo', compact('subscriptions'));
    }
}