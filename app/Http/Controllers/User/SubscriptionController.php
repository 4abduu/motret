<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{
    Transaction,
    SubscriptionPriceSystem,
    SubscriptionPriceUser,
    SubscriptionSystem,
    SubscriptionUser,
    SubscriptionCombo,
    User,
    Notif
};
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    protected $subscriptionTypes = [
        'system' => [
            'model' => SubscriptionSystem::class,
            'price_model' => SubscriptionPriceSystem::class,
            'relation' => null
        ],
        'user' => [
            'model' => SubscriptionUser::class,
            'price_model' => SubscriptionPriceUser::class,
            'relation' => 'target_user_id'
        ],
        'combo' => [
            'model' => SubscriptionCombo::class,
            'price_model' => SubscriptionPriceSystem::class,
            'relation' => 'target_user_id'
        ]
    ];

    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Display subscription page
     */
    public function index()
    {
        Log::info('Displaying subscription page for user: ' . Auth::id());
        try {
            $data = $this->getSystemSubscriptionData(Auth::id());
            $data['prices'] = SubscriptionPriceSystem::all();
            
            return view('user.subscription', $data);
        } catch (\Exception $e) {
            Log::error('Error displaying subscription page: ' . $e->getMessage());
            return back()->with('error', 'Failed to load subscription page');
        }
    }

    /**
     * Show subscription options for a user
     */
    public function showSubscriptionOptions($username)
    {
        Log::info('Displaying subscription options for user: ' . $username . ' by: ' . Auth::id());
        try {
            $targetUser = User::where('username', $username)->firstOrFail();
            $data = $this->getUserSubscriptionData(Auth::id(), $targetUser->id);
            
            $data['user'] = $targetUser;
            $data['subscriptionPrices'] = SubscriptionPriceUser::where('user_id', $targetUser->id)->firstOrFail();
            $data['systemPrices'] = SubscriptionPriceSystem::all();
            
            return view('user.subscription_user', $data);
        } catch (\Exception $e) {
            Log::error('Error showing subscription options: ' . $e->getMessage());
            return back()->with('error', 'Failed to load subscription options');
        }
    }

    /**
     * Create a new system transaction
     */
    public function createTransaction(Request $request)
    {
        Log::info('Creating new system transaction', ['request' => $request->all()]);
        
        $request->validate([
            'subscription_price_id' => 'required|exists:harga_langganan_sistem,id'
        ]);

        try {
            $price = SubscriptionPriceSystem::findOrFail($request->subscription_price_id);
            $user = Auth::user();

            // Validate subscription duration
            $validation = $this->validateSystemSubscriptionDuration($user->id, $price->duration);
            if (!$validation['valid']) {
                Log::warning('Invalid subscription duration', [
                    'user_id' => $user->id,
                    'duration' => $price->duration,
                    'remaining' => $validation['remaining_months']
                ]);
                return response()->json([
                    'error' => 'Paket durasi terlalu pendek. Sisa langganan Anda '.$validation['remaining_text'].'. Silakan pilih paket dengan durasi lebih panjang.'
                ], 400);
            }

            return $this->processPayment($user, $price->price, 'system', [
                'subscription_price_id' => $price->id
            ]);
        } catch (\Exception $e) {
            Log::error('System transaction creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Transaction failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Subscribe to a user
     */
    public function subscribeOn(Request $request, $username)
    {
        Log::info('Subscribing to user', ['username' => $username, 'request' => $request->all()]);
        
        $validated = $request->validate([
            'package' => 'required|in:1_month,3_months,6_months,1_year'
        ]);

        try {
            $targetUser = User::where('username', $username)->firstOrFail();
            $subscriptionPrices = SubscriptionPriceUser::where('user_id', $targetUser->id)->firstOrFail();
            $price = $subscriptionPrices->{'price_' . $validated['package']};

            // Validate subscription duration
            $validation = $this->validateUserSubscriptionDuration(Auth::id(), $validated['package'], $targetUser->id);
            if (!$validation['valid']) {
                Log::warning('Invalid subscription duration for user subscription', [
                    'user_id' => Auth::id(),
                    'target_user_id' => $targetUser->id,
                    'package' => $validated['package'],
                    'remaining' => $validation['remaining_months']
                ]);
                return response()->json([
                    'error' => 'Paket durasi terlalu pendek. Sisa langganan Anda '.$validation['remaining_text'].'. Silakan pilih paket dengan durasi lebih panjang.'
                ], 400);
            }

            return $this->processPayment(Auth::user(), $price, 'user', [
                'package' => $validated['package'],
                'target_user_id' => $targetUser->id
            ]);
        } catch (\Exception $e) {
            Log::error('User subscription failed: ' . $e->getMessage());
            return response()->json(['error' => 'Subscription failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Subscribe to combo package
     */
    public function subscribeCombo(Request $request, $username)
    {
        Log::info('Subscribing to combo package', ['username' => $username, 'request' => $request->all()]);
        
        $validated = $request->validate([
            'combo_price' => 'required|numeric',
            'duration' => 'required|in:1_month,3_months,6_months,1_year'
        ]);

        try {
            $targetUser = User::where('username', $username)->firstOrFail();

            // Validate subscription duration
            $validation = $this->validateComboSubscriptionDuration(Auth::id(), $validated['duration'], $targetUser->id);
            if (!$validation['valid']) {
                Log::warning('Invalid subscription duration for combo', [
                    'user_id' => Auth::id(),
                    'target_user_id' => $targetUser->id,
                    'duration' => $validated['duration'],
                    'remaining' => $validation['remaining_months']
                ]);
                return response()->json([
                    'error' => 'Paket durasi terlalu pendek. Sisa langganan Anda '.$validation['remaining_text'].'. Silakan pilih paket dengan durasi lebih panjang.'
                ], 400);
            }

            return $this->processPayment(Auth::user(), $validated['combo_price'], 'combo', [
                'duration' => $validated['duration'],
                'target_user_id' => $targetUser->id
            ]);
        } catch (\Exception $e) {
            Log::error('Combo subscription failed: ' . $e->getMessage());
            return response()->json(['error' => 'Subscription failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Check transaction status
     */
    public function checkTransactionStatus(Request $request)
    {
        Log::info('Checking transaction status', ['order_id' => $request->order_id]);
        
        $request->validate(['order_id' => 'required']);

        try {
            $status = \Midtrans\Transaction::status($request->order_id);
            Log::debug('Midtrans status response', ['status' => $status]);
            
            $status = json_decode(json_encode($status));

            DB::transaction(function () use ($status) {
                $transaction = Transaction::where('order_id', $status->order_id)->firstOrFail();
                Log::debug('Found transaction', ['transaction_id' => $transaction->id]);
                
                $this->updateTransaction($transaction, $status);

                if ($status->transaction_status == 'settlement') {
                    Log::info('Processing successful payment', ['transaction_id' => $transaction->id]);
                    $this->processSuccessfulPayment($transaction, $status);
                }
            });

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Transaction status check failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Check transaction status for user subscriptions
     */
    public function checkTransactionStatusUser(Request $request)
    {
        $request->validate(['order_id' => 'required']);

        try {
            $status = \Midtrans\Transaction::status($request->order_id);
            $status = json_decode(json_encode($status));

            DB::transaction(function () use ($status) {
                $transaction = Transaction::where('order_id', $status->order_id)->firstOrFail();
                $this->updateTransaction($transaction, $status);

                if ($status->transaction_status == 'settlement') {
                    $this->processSuccessfulPayment($transaction, $status);
                }
            });

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('user.profile')
            ]);

        } catch (\Exception $e) {
            Log::error('User transaction status check failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify user subscription status'
            ], 500);
        }
    }

    /**
     * Check transaction status for combo subscriptions
     */
    public function checkTransactionStatusCombo(Request $request)
    {
        $request->validate(['order_id' => 'required']);

        try {
            $status = \Midtrans\Transaction::status($request->order_id);
            $status = json_decode(json_encode($status));

            DB::transaction(function () use ($status) {
                $transaction = Transaction::where('order_id', $status->order_id)->firstOrFail();
                $this->updateTransaction($transaction, $status);

                if ($status->transaction_status == 'settlement') {
                    $this->processSuccessfulPayment($transaction, $status);
                }
            });

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('user.profile')
            ]);

        } catch (\Exception $e) {
            Log::error('Combo transaction status check failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify combo subscription status'
            ], 500);
        }
    }

    /**
     * Save user subscription prices
     */
    public function saveSubsUser(Request $request)
    {
        Log::info('Saving user subscription prices', ['user_id' => Auth::id(), 'request' => $request->all()]);
        
        $validated = $request->validate([
            'price_1_month' => 'required|string',
            'price_3_months' => 'nullable|string',
            'price_6_months' => 'nullable|string',
            'price_1_year' => 'nullable|string',
        ]);

        try {
            $prices = array_map(function($price) {
                return $price ? (int) str_replace('.', '', $price) : null;
            }, $validated);

            SubscriptionPriceUser::updateOrCreate(
                ['user_id' => Auth::id()],
                $prices
            );

            Log::info('Subscription prices saved successfully', ['user_id' => Auth::id()]);
            return redirect()->route('user.profile')->with('success', 'Harga langganan berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Failed to save subscription prices: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan harga langganan: '.$e->getMessage());
        }
    }

    /**
     * Get system subscription data
     */
    protected function getSystemSubscriptionData($userId)
    {
        $subscription = SubscriptionSystem::with('transaction')
            ->where('user_id', $userId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();

        $data = [
            'existingDuration' => 0,
            'endDateFormatted' => null,
            'duration' => '0 Bulan',
            'hasActiveSubscription' => false,
            'subscription' => $subscription
        ];
        
        if ($subscription) {
            $endDate = Carbon::parse($subscription->end_date);
            $existingDuration = $this->calculateRemainingMonths($endDate);
            
            $data = [
                'existingDuration' => $existingDuration,
                'endDateFormatted' => $endDate->format('d F Y'),
                'duration' => $this->getDurationText($existingDuration),
                'hasActiveSubscription' => true,
                'subscription' => $subscription
            ];
        }
        
        return $data;
    }


    /**
     * Get user subscription data
     */
    protected function getUserSubscriptionData($userId, $targetUserId)
    {
        $subscription = SubscriptionUser::with('transaction')
            ->where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();

        $data = [
            'existingDuration' => 0,
            'endDateFormatted' => null,
            'duration' => '0 Bulan',
            'hasActiveSubscription' => false,
            'subscription' => $subscription
        ];
        
        if ($subscription) {
            $endDate = Carbon::parse($subscription->end_date);
            $existingDuration = $this->calculateRemainingMonths($endDate);
            
            $data = [
                'existingDuration' => $existingDuration,
                'endDateFormatted' => $endDate->format('d F Y'),
                'duration' => $this->getDurationText($existingDuration),
                'hasActiveSubscription' => true,
                'subscription' => $subscription
            ];
        }
        
        return $data;
    }

    /**
     * Calculate remaining months from end date
     */
    protected function calculateRemainingMonths($endDate)
    {
        $now = Carbon::now();
        if ($now > $endDate) {
            return 0;
        }
        
        $diffInMonths = $now->diffInMonths($endDate);
        $remainingDays = $now->copy()->addMonths($diffInMonths)->diffInDays($endDate);
        
        // If there are remaining days beyond complete months, count as partial month
        return $remainingDays > 0 ? $diffInMonths + 1 : $diffInMonths;
    }

    /**
     * Validate system subscription duration
     */
    protected function validateSystemSubscriptionDuration($userId, $duration)
    {
        $durationMonths = $this->getDurationInMonths($duration);
        $subscription = SubscriptionSystem::where('user_id', $userId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();
        
        // No active subscription - any duration is valid
        if (!$subscription) {
            return [
                'valid' => true,
                'remaining_months' => 0,
                'remaining_text' => '0 bulan'
            ];
        }
        
        $remainingMonths = $this->calculateRemainingMonths($subscription->end_date);
        $remainingText = $this->getDurationText($remainingMonths);
        
        // New duration must be longer than remaining duration
        return [
            'valid' => $durationMonths > $remainingMonths,
            'remaining_months' => $remainingMonths,
            'remaining_text' => $remainingText
        ];
    }

    /**
     * Validate user subscription duration
     */
    protected function validateUserSubscriptionDuration($userId, $duration, $targetUserId)
    {
        $durationMonths = $this->getDurationInMonths($duration);
        $subscription = SubscriptionUser::where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();
        
        // No active subscription - any duration is valid
        if (!$subscription) {
            return [
                'valid' => true,
                'remaining_months' => 0,
                'remaining_text' => '0 bulan'
            ];
        }
        
        $remainingMonths = $this->calculateRemainingMonths($subscription->end_date);
        $remainingText = $this->getDurationText($remainingMonths);
        
        // New duration must be longer than remaining duration
        return [
            'valid' => $durationMonths > $remainingMonths,
            'remaining_months' => $remainingMonths,
            'remaining_text' => $remainingText
        ];
    }

    /**
     * Validate combo subscription duration
     */
    protected function validateComboSubscriptionDuration($userId, $duration, $targetUserId)
    {
        $durationMonths = $this->getDurationInMonths($duration);
        $subscription = SubscriptionCombo::where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();
        
        // No active subscription - any duration is valid
        if (!$subscription) {
            return [
                'valid' => true,
                'remaining_months' => 0,
                'remaining_text' => '0 bulan'
            ];
        }
        
        $remainingMonths = $this->calculateRemainingMonths($subscription->end_date);
        $remainingText = $this->getDurationText($remainingMonths);
        
        // New duration must be longer than remaining duration
        return [
            'valid' => $durationMonths > $remainingMonths,
            'remaining_months' => $remainingMonths,
            'remaining_text' => $remainingText
        ];
    }

    /**
     * Process payment with Midtrans
     */
    protected function processPayment($user, $amount, $type, $metadata = [])
    {
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'target_user_id' => $metadata['target_user_id'] ?? null,
            'order_id' => uniqid(),
            'transaction_status' => 'pending',
            'gross_amount' => $amount,
            'type' => $type,
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'metadata' => $metadata
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment gateway error: '.$e->getMessage()], 500);
        }
    }

    /**
     * Update transaction record
     */
    protected function updateTransaction($transaction, $status)
    {
        $transaction->update([
            'transaction_status' => $status->transaction_status,
            'payment_type' => $status->payment_type,
            'transaction_id' => $status->transaction_id,
            'fraud_status' => $status->fraud_status,
        ]);
    }
    public function manage()
    {
        $subscriptionPrices = SubscriptionPriceUser::where('user_id', Auth::id())->first();
        return view('user.manage_subscription', compact('subscriptionPrices'));
    }
    /**
     * Process successful payment
     */
    protected function processSuccessfulPayment($transaction, $status)
    {
        $type = $transaction->type;
        $config = $this->subscriptionTypes[$type];
        
        // Get duration based on type
        $duration = $this->getDurationFromPayment($transaction, $status, $type);
        
        // Calculate start and end dates
        $now = Carbon::now();
        $endDate = $now->copy()->addMonths($duration);
        
        // Create or update subscription record
        $subscriptionData = [
            'user_id' => $transaction->user_id,
            'price' => $transaction->gross_amount,
            'start_date' => $now,
            'end_date' => $endDate,
            'transaction_id' => $transaction->id,
        ];
        
        // Add target_user_id for user and combo subscriptions
        if (in_array($type, ['user', 'combo'])) {
            $subscriptionData['target_user_id'] = $transaction->target_user_id;
        }
        
        // For combo subscriptions, add additional fields if needed
        if ($type === 'combo') {
            $subscriptionData['system_price'] = $transaction->gross_amount; // Adjust as needed
            $subscriptionData['user_price'] = 0; // Adjust based on your logic
            $subscriptionData['total_price'] = $transaction->gross_amount;
        }
        
        // Update or create subscription record
        $subscription = $config['model']::updateOrCreate(
            [
                'user_id' => $transaction->user_id,
                ...(in_array($type, ['user', 'combo']) ? ['target_user_id' => $transaction->target_user_id] : [])
            ],
            $subscriptionData
        );
        
        // Update user role if system or combo
        if (in_array($type, ['system', 'combo'])) {
            $transaction->user->update([
                'role' => 'pro',
                'subscription_ends_at' => $endDate
            ]);
        }
        
        // Create notification
        $this->createNotification($transaction, $endDate);
        
        Log::info("Subscription {$type} created/updated", [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'end_date' => $endDate
        ]);
    }

    /**
     * Get or create subscription record
     */
    protected function getOrCreateSubscription($transaction, $config, $duration)
    {
        $query = $config['model']::where('user_id', $transaction->user_id);
        
        if ($config['relation']) {
            $query->where($config['relation'], $transaction->target_user_id);
        }
        
        $subscription = $query->where('end_date', '>', Carbon::now())->first();
        
        if (!$subscription) {
            $subscription = $config['model']::create([
                'user_id' => $transaction->user_id,
                $config['relation'] => $config['relation'] ? $transaction->target_user_id : null,
                'price' => $transaction->gross_amount,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths($duration),
                'transaction_id' => $transaction->id,
            ]);
        }
        
        return $subscription;
    }

    /**
     * Calculate new end date
     */
    protected function calculateNewEndDate($subscription, $duration)
    {
        // If subscription is still active, add to existing end date
        if ($subscription->end_date > Carbon::now()) {
            return Carbon::parse($subscription->end_date)->addMonths($duration);
        }
        
        // Otherwise start from now
        return Carbon::now()->addMonths($duration);
    }

    /**
     * Create notification for subscription
     */
    protected function createNotification($transaction, $endDate)
    {
        $endDateFormatted = Carbon::parse($endDate)->format('d F Y');
        $message = $this->getNotificationMessage($transaction, $endDateFormatted);
        
        Notif::create([
            'notify_for' => $transaction->user_id,
            'notify_from' => $transaction->user_id,
            'target_id' => $transaction->id,
            'type' => 'system',
            'message' => $message,
        ]);
    }

    /**
     * Get notification message based on subscription type
     */
    protected function getNotificationMessage($transaction, $endDateFormatted)
    {
        switch ($transaction->type) {
            case 'system':
                return "Selamat! Langganan sistem Anda diperpanjang sampai $endDateFormatted";
            case 'user':
                $targetUser = User::find($transaction->target_user_id);
                return "Selamat! Langganan ke {$targetUser->username} diperpanjang sampai $endDateFormatted";
            case 'combo':
                $targetUser = User::find($transaction->target_user_id);
                return "Selamat! Langganan kombo ke {$targetUser->username} dan sistem diperpanjang sampai $endDateFormatted";
            default:
                return "Langganan Anda diperpanjang sampai $endDateFormatted";
        }
    }

    /**
     * Get duration from payment based on type
     */
    protected function getDurationFromPayment($transaction, $status, $type)
    {
        switch ($type) {
            case 'system':
                $price = SubscriptionPriceSystem::where('price', $transaction->gross_amount)
                    ->firstOrFail();
                return $this->getDurationInMonths($price->duration);
                
            case 'user':
                $price = SubscriptionPriceUser::where('user_id', $transaction->target_user_id)
                    ->firstOrFail();
                
                foreach (['1_month', '3_months', '6_months', '1_year'] as $duration) {
                    if ($price->{'price_' . $duration} == $transaction->gross_amount) {
                        return $this->getDurationInMonths($duration);
                    }
                }
                break;
                
            case 'combo':
                // For combo, we get duration from metadata
                return $this->getDurationInMonths($transaction->metadata['duration']);
        }
        
        throw new \Exception('Could not determine subscription duration');
    }

    /**
     * Convert duration string to months
     */
    protected function getDurationInMonths($duration)
    {
        switch ($duration) {
            case '1_month': return 1;
            case '3_months': return 3;
            case '6_months': return 6;
            case '1_year': return 12;
            default: return (int) $duration;
        }
    }

    /**
     * Convert months to duration text
     */
    protected function getDurationText($months)
    {
        switch ($months) {
            case 1: return '1 Bulan';
            case 3: return '3 Bulan';
            case 6: return '6 Bulan';
            case 12: return '1 Tahun';
            default: return $months . ' Bulan';
        }
    }
}