<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\SubscriptionPriceSystem;
use App\Models\SubscriptionPriceUser;
use App\Models\SubscriptionSystem;
use App\Models\SubscriptionUser;
use App\Models\User;
use App\Models\Notif;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use function env;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Set your Merchant Server Key
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        // Set sanitization on (default)
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        // Set 3DS transaction for credit card to true
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);
    }

    public function index()
    {
        $prices = SubscriptionPriceSystem::all();
        $user = Auth::user();
        $existingSubscription = SubscriptionSystem::where('user_id', $user->id)->where('end_date', '>=', Carbon::now())->first();
        $existingDuration = $existingSubscription ? Carbon::parse($existingSubscription->end_date)->diffInMonths(Carbon::parse($existingSubscription->updated_at)) : 0;
        $endDate = $existingSubscription ? Carbon::parse($existingSubscription->end_date)->format('d F Y') : null;
        $duration = $existingSubscription ? $this->getDurationText($existingDuration) : null;

        return view('user.subscription', compact('prices', 'existingDuration', 'endDate', 'duration'));
    }

    private function getDurationText($months)
    {
        switch ($months) {
            case 1:
                return '1 bulan';
            case 3:
                return '3 bulan';
            case 6:
                return '6 bulan';
            case 12:
                return '1 tahun';
            default:
                return $months . ' bulan';
        }
    }

    public function manage()
    {
        $subscriptionPrices = SubscriptionPriceUser::where('user_id', Auth::id())->first();
        return view('user.manage_subscription', compact('subscriptionPrices'));
    }

    public function createTransaction(Request $request)
    {
        $user = Auth::user();
        $subscriptionPrice = SubscriptionPriceSystem::findOrFail($request->subscription_price_id);

        // Periksa durasi langganan yang ada
        $existingSubscription = SubscriptionSystem::where('user_id', $user->id)->where('end_date', '>', Carbon::now())->first();
        if ($existingSubscription) {
            $existingDuration = $this->getDurationInMonths($existingSubscription->duration);
            $newDuration = $this->getDurationInMonths($subscriptionPrice->duration);

            // Batasi pembelian paket dengan durasi lebih singkat
            if ($newDuration <= $existingDuration) {
                return response()->json(['error' => 'Anda tidak dapat membeli paket dengan durasi yang lebih singkat atau sama.'], 400);
            }
        }

        // Buat transaksi baru
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'order_id' => uniqid(), // Generate unique order ID
            'transaction_status' => 'pending',
            'payment_type' => '',
            'gross_amount' => $subscriptionPrice->price,
            'transaction_id' => '',
            'fraud_status' => '',
            'type' => 'system',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => $subscriptionPrice->price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json(['error' => 'Could not connect to Midtrans. Please try again later.'], 500);
        }
    }

    public function checkTransactionStatus(Request $request)
    {
        $orderId = $request->input('order_id');
        Log::info('Checking transaction status for order ID: ' . $orderId, []);

        try {
            $status = \Midtrans\Transaction::status($orderId);
            $status = json_decode(json_encode($status)); // Pastikan $status adalah objek
            Log::info('Midtrans Transaction Status:', (array) $status);

            DB::transaction(function () use ($status) {
                $transaction = Transaction::where('order_id', $status->order_id)->firstOrFail();
                Log::info('Transaction found:', $transaction->toArray());

                $transaction->update([
                    'transaction_status' => $status->transaction_status,
                    'payment_type' => $status->payment_type,
                    'transaction_id' => $status->transaction_id,
                    'fraud_status' => $status->fraud_status,
                ]);

                Log::info('Updated transaction:', $transaction->toArray());

                $grossAmount = $transaction->gross_amount;
                Log::info('Gross Amount:', ['gross_amount' => $grossAmount]);

                if ($status->transaction_status == 'settlement') {
                    $price = SubscriptionPriceSystem::where('price', $grossAmount)->firstOrFail();
                    $startDate = Carbon::now();
                    $endDate = $startDate->copy()->addMonths($this->getDurationInMonths($price->duration));

                    $user = $transaction->user;
                    $user->update(['role' => 'pro']);
                    $user->update(['subscription_ends_at' => $endDate]);

                    $existingSubscription = SubscriptionSystem::where('user_id', $user->id)->where('end_date', '>', Carbon::now())->first();
                    if ($existingSubscription) {
                        $existingSubscription->update([
                            'price' => $price->price,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'transaction_id' => $transaction->id,
                        ]);
                    } else {
                        SubscriptionSystem::create([
                            'user_id' => $user->id,
                            'price' => $price->price,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'transaction_id' => $transaction->id,
                        ]);
                    }

                    // Buat notifikasi
                    Notif::create([
                        'notify_for' => $user->id,
                        'notify_from' => $user->id,
                        'target_id' => $transaction->id,
                        'type' => 'system',
                        'message' => 'Selamat! Anda telah berlangganan ke sistem sampai dengan ' . $endDate->format('d F Y'),
                    ]);

                    Log::info('Notification created for user:', ['user_id' => $user->id]);
                }
            });

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Could not connect to Midtrans. Please try again later.'], 500);
        }
    }

    private function getDurationInMonths($duration)
    {
        switch ($duration) {
            case '1_month':
                return 1;
            case '3_months':
                return 3;
            case '6_months':
                return 6;
            case '1_year':
                return 12;
            default:
                return 0;
        }
    }

    public function saveSubsUser(Request $request)
    {
        $request->validate([
            'price_1_month' => 'required|string',
            'price_3_months' => 'nullable|string',
            'price_6_months' => 'nullable|string',
            'price_1_year' => 'nullable|string',
        ]);

        $price_1_month = str_replace('.', '', $request->price_1_month);
        $price_3_months = str_replace('.', '', $request->price_3_months) ?: null;
        $price_6_months = str_replace('.', '', $request->price_6_months) ?: null;
        $price_1_year = str_replace('.', '', $request->price_1_year) ?: null;

        SubscriptionPriceUser::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'price_1_month' => $price_1_month,
                'price_3_months' => $price_3_months,
                'price_6_months' => $price_6_months,
                'price_1_year' => $price_1_year,
            ]
        );

        return redirect()->route('user.profile')->with('success', 'Harga langganan berhasil disimpan.');
    }

    public function history()
    {
        $transactions = Transaction::where('user_id', Auth::id())->get();
        return view('user.subscription.history', compact('transactions'));
    }

    public function showSubscriptionOptions($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $subscriptionPrices = SubscriptionPriceUser::where('user_id', $user->id)->first();

        return view('user.subscription_user', compact('user', 'subscriptionPrices'));
    }

    public function subscribeOn(Request $request, $username)
    {
        $user = Auth::user();
        $targetUser = User::where('username', $username)->firstOrFail();
        $subscriptionPrices = SubscriptionPriceUser::where('user_id', $targetUser->id)->firstOrFail();
    
        $validated = $request->validate([
            'package' => 'required|in:1_month,3_months,6_months,1_year',
        ]);
    
        $price = $subscriptionPrices->{'price_' . $validated['package']};
    
        // Buat transaksi baru
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'target_user_id' => $targetUser->id,
            'order_id' => uniqid(), // Generate unique order ID
            'transaction_status' => 'pending',
            'payment_type' => '',
            'gross_amount' => $price,
            'transaction_id' => '',
            'fraud_status' => '',
            'type' => 'user',
        ]);
    
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => $price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];
    
        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Error SubscribeOn: ' . $e->getMessage());
            return response()->json(['error' => 'Could not connect to Midtrans. Please try again later.'], 500);
        }
    }

    public function checkTransactionStatusUser(Request $request)
    {
        $orderId = $request->input('order_id');
        Log::info('Checking transaction status for order ID: ' . $orderId, []);
    
        try {
            $status = \Midtrans\Transaction::status($orderId);
            $status = json_decode(json_encode($status)); // Pastikan $status adalah objek
            Log::info('Midtrans Transaction Status:', (array) $status);
    
            DB::transaction(function () use ($status, &$redirectUrl) {
                $transaction = Transaction::where('order_id', $status->order_id)->firstOrFail();
                Log::info('Transaction found:', $transaction->toArray());
    
                $transaction->update([
                    'transaction_status' => $status->transaction_status,
                    'payment_type' => $status->payment_type,
                    'transaction_id' => $status->transaction_id,
                    'fraud_status' => $status->fraud_status,
                ]);
    
                Log::info('Updated transaction:', $transaction->toArray());
    
                $grossAmount = $transaction->gross_amount;
                Log::info('Gross Amount:', ['gross_amount' => $grossAmount]);
    
                if ($status->transaction_status == 'settlement') {
                    $price = SubscriptionPriceUser::where('price_1_month', $grossAmount)
                        ->orWhere('price_3_months', $grossAmount)
                        ->orWhere('price_6_months', $grossAmount)
                        ->orWhere('price_1_year', $grossAmount)
                        ->first();
    
                    if (!$price) {
                        Log::error('Price not found for the given gross amount.');
                        throw new \Exception('Price not found for the given gross amount.');
                    }
    
                    Log::info('Price found:', $price->toArray());
    
                    $duration = $this->getDurationFromPrice($price, $grossAmount);
                    $startDate = Carbon::now();
                    $endDate = $startDate->copy()->addMonths($duration);
    
                    $user = $transaction->user;
                    Log::info('User found:', $user->toArray());
    
                    $targetUser = User::find($transaction->target_user_id);
                    if (!$targetUser) {
                        Log::error('Target user not found.');
                        throw new \Exception('Target user not found.');
                    }
                    Log::info('Target user found:', $targetUser->toArray());
    
                    $existingSubscription = SubscriptionUser::where('user_id', $user->id)->where('target_user_id', $targetUser->id)->where('end_date', '>', Carbon::now())->first();
                    if ($existingSubscription) {
                        $existingSubscription->update([
                            'price' => $grossAmount,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'transaction_id' => $transaction->id,
                        ]);
                        Log::info('Existing subscription updated:', $existingSubscription->toArray());
                    } else {
                        $newSubscription = SubscriptionUser::create([
                            'user_id' => $user->id,
                            'target_user_id' => $targetUser->id,
                            'price' => $grossAmount,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'transaction_id' => $transaction->id,
                        ]);
                        Log::info('New subscription created:', $newSubscription->toArray());
                    }
    
                    // Buat notifikasi
                    $notif = Notif::create([
                        'notify_for' => $user->id,
                        'notify_from' => $user->id,
                        'target_id' => $transaction->id,
                        'type' => 'system',
                        'message' => 'Selamat! Anda telah berlangganan ke '. $targetUser->username .' sampai dengan ' . $endDate->format('d F Y'),
                    ]);
    
                    Log::info('Notification created:', $notif->toArray());
    
                    // Set redirect URL to target user's profile
                    $redirectUrl = route('user.showProfile', $targetUser->username);
                }
            });
    
            return response()->json(['status' => 'success', 'redirect_url' => $redirectUrl]);
        } catch (\Exception $e) {
            Log::error('Midtrans Error CheckTransactions: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Could not connect to Midtrans. Please try again later.'], 500);
        }
    }
    
    private function getDurationFromPrice($price, $grossAmount)
    {
        if ($price->price_1_month == $grossAmount) {
            return 1;
        } elseif ($price->price_3_months == $grossAmount) {
            return 3;
        } elseif ($price->price_6_months == $grossAmount) {
            return 6;
        } elseif ($price->price_1_year == $grossAmount) {
            return 12;
        } else {
            throw new \Exception('Invalid price for the given gross amount.');
        }
    }

}