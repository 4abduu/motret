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
        //$prices = SubscriptionPriceSystem::all();
        //return view('admin.subscriptions.subsSystem', compact('prices'));
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