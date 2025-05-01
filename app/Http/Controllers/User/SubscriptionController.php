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
    Notif,
    BalanceHistory,
};
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Jenis langganan yang tersedia.
     * @var array
     */
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

    /**
     * Mengatur konfigurasi Midtrans untuk transaksi pembayaran.
     */
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Menampilkan halaman langganan sistem untuk pengguna.
     * Mengambil data langganan aktif (sistem dan combo) dan menghitung durasi langganan yang tersisa.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get active subscriptions
        $systemSubscription = SubscriptionSystem::where('user_id', $user->id)
            ->where('end_date', '>', now())
            ->first();

        $comboSubscription = SubscriptionCombo::where('user_id', $user->id)
            ->where('end_date', '>', now())
            ->first();

        // Determine source and duration
        $source = $comboSubscription ? 'combo' : ($systemSubscription ? 'system' : null);
        $endDate = null;
        $durationText = '0 Bulan';
        
        // Calculate combined end date
        if ($systemSubscription || $comboSubscription) {
            $systemEndDate = $systemSubscription ? Carbon::parse($systemSubscription->end_date) : null;
            $comboEndDate = $comboSubscription ? Carbon::parse($comboSubscription->end_date) : null;
            
            $endDate = $systemEndDate && $comboEndDate 
                ? $systemEndDate->greaterThan($comboEndDate) ? $systemEndDate : $comboEndDate
                : ($systemEndDate ?? $comboEndDate);
                
            $durationText = $this->getDurationText($endDate);
        }

        return view('user.subscription', [
            'prices' => SubscriptionPriceSystem::all(),
            'hasActiveSubscription' => $systemSubscription || $comboSubscription,
            'hasComboSubscription' => $comboSubscription !== null,
            'source' => $source,
            'duration' => $durationText,
            'endDateFormatted' => $endDate ? $endDate->format('d F Y') : null,
            'existingDuration' => $endDate ? $this->calculateAccurateRemainingMonths($endDate) : 0,
            'systemEndDate' => $systemSubscription ? $systemSubscription->end_date : null,
            'systemEndDateFormatted' => $systemSubscription ? Carbon::parse($systemSubscription->end_date)->format('d F Y') : null,
            'comboEndDate' => $comboSubscription ? $comboSubscription->end_date : null,
            'comboEndDateFormatted' => $comboSubscription ? Carbon::parse($comboSubscription->end_date)->format('d F Y') : null,
        ]);
    }

    /**
     * Menampilkan opsi langganan untuk pengguna tertentu.
     * Mengambil data langganan aktif (user, combo, dan sistem) untuk pengguna target.
     *
     * @param string $username Username pengguna target.
     * @return \Illuminate\View\View
     */
    public function showSubscriptionOptions($username)
    {
        $targetUser = User::where('username', $username)->firstOrFail();
        $user = Auth::user();
        
        // Get active user subscription
        $userSubscription = SubscriptionUser::where('user_id', $user->id)
            ->where('target_user_id', $targetUser->id)
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'desc')
            ->first();

        // Get active combo subscription
        $comboSubscription = SubscriptionCombo::where('user_id', $user->id)
            ->where('target_user_id', $targetUser->id)
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'desc')
            ->first();

        // Get active system subscription
        $systemSubscription = SubscriptionSystem::where('user_id', $user->id)
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'desc')
            ->first();

        // Calculate existing duration for each subscription type
        $userExistingDuration = $userSubscription 
            ? $this->calculateAccurateRemainingMonths($userSubscription->end_date) 
            : 0;

        $comboExistingDuration = $comboSubscription 
            ? $this->calculateAccurateRemainingMonths($comboSubscription->end_date) 
            : 0;

        $systemExistingDuration = $systemSubscription 
            ? $this->calculateAccurateRemainingMonths($systemSubscription->end_date) 
            : 0;

        $userDuration = $userSubscription 
            ? $this->getDurationText($userSubscription->end_date) 
            : 0;
        
        $systemDuration = $systemSubscription
            ? $this->getDurationText($systemSubscription->end_date) 
            : 0;

        $comboDuration = $comboSubscription
            ? $this->getDurationText($comboSubscription->end_date) 
            : 0;

        // Determine the maximum duration between system and user subscriptions
        $maxDuration = max($systemExistingDuration, $userExistingDuration, $comboExistingDuration);

        // Prepare data for view
        $data = [
            'user' => $targetUser,
            'subscriptionPrices' => SubscriptionPriceUser::where('user_id', $targetUser->id)->first(),
            'systemPrices' => [
                '1_month' => SubscriptionPriceSystem::where('duration', '1_month')->value('price'),
                '3_months' => SubscriptionPriceSystem::where('duration', '3_months')->value('price'),
                '6_months' => SubscriptionPriceSystem::where('duration', '6_months')->value('price'),
                '1_year' => SubscriptionPriceSystem::where('duration', '1_year')->value('price'),
            ],
            'hasActiveSubscription' => $userSubscription || $comboSubscription || $systemSubscription,
            'hasComboSubscription' => $comboSubscription !== null,
            'hasUserSubscription' => $userSubscription !== null,
            'hasSystemSubscription' => $systemSubscription !== null,
            'userExistingDuration' => $userExistingDuration,
            'comboExistingDuration' => $comboExistingDuration,
            'systemExistingDuration' => $systemExistingDuration,
            'maxDuration' => $maxDuration, // New variable for maximum duration
            'userDuration' => $userDuration,
            'systemDuration' => $systemDuration,
            'comboDuration' => $comboDuration,
            'userSubscription' => $userSubscription,
            'comboSubscription' => $comboSubscription,
            'systemSubscription' => $systemSubscription,
        ];

        // Add formatted dates and durations
        if ($userSubscription) {
            $data['userEndDateFormatted'] = Carbon::parse($userSubscription->end_date)->format('d F Y');
            $data['userDurationText'] = $this->getDurationText($userSubscription->end_date);
        }

        if ($comboSubscription) {
            $data['comboEndDateFormatted'] = Carbon::parse($comboSubscription->end_date)->format('d F Y');
            $data['comboDurationText'] = $this->getDurationText($comboSubscription->end_date);
        }

        if ($systemSubscription) {
            $data['systemEndDateFormatted'] = Carbon::parse($systemSubscription->end_date)->format('d F Y');
            $data['systemDurationText'] = $this->getDurationText($systemSubscription->end_date);
        }

        return view('user.subscription_user', $data);
    }

    /**
    * Menampilkan halaman manajemen langganan untuk pengguna.
    * Mengambil data harga langganan pengguna saat ini.
    *
    * @return \Illuminate\View\View
    */
    public function manage()
    {
        $subscriptionPrices = SubscriptionPriceUser::where('user_id', Auth::id())->first();
        return view('user.manage_subscription', compact('subscriptionPrices'));
    }

    /**
     * Membuat transaksi baru untuk langganan sistem.
     * Memvalidasi durasi langganan dan memproses pembayaran melalui Midtrans.
     *
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi subscription_price_id.
     * @return \Illuminate\Http\JsonResponse
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
     * Membuat transaksi langganan untuk pengguna tertentu.
     * Memvalidasi durasi langganan dan memproses pembayaran melalui Midtrans.
     *
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi paket langganan.
     * @param string $username Username pengguna target.
     * @return \Illuminate\Http\JsonResponse
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
     * Membuat transaksi langganan combo (gabungan sistem dan user).
     * Memproses pembayaran melalui Midtrans.
     *
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi harga combo, durasi, dan harga sistem/user.
     * @param string $username Username pengguna target.
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribeCombo(Request $request, $username)
    {
        $request->validate([
            'combo_price' => 'required|numeric',
            'duration' => 'required|in:1_month,3_months,6_months,1_year',
            'system_price' => 'required|numeric',
            'user_price' => 'required|numeric'
        ]);
    
        try {
            $targetUser = User::where('username', $username)->firstOrFail();
    
            $metadata = [
                'duration' => $request->duration,
                'target_user_id' => $targetUser->id,
                'system_price' => $request->system_price,
                'user_price' => $request->user_price
            ];
    
            return $this->processPayment(Auth::user(), $request->combo_price, 'combo', $metadata);
        } catch (\Exception $e) {
            Log::error('Combo subscription failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Payment Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memeriksa status transaksi langganan sistem melalui Midtrans.
     * Memperbarui status transaksi di database.
     *
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi order_id.
     * @return \Illuminate\Http\JsonResponse
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

            return response()->json(['status' => 'success', 'redirect_url' => route('home')]);

        } catch (\Exception $e) {
            Log::error('Transaction status check failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Memeriksa status transaksi langganan pengguna melalui Midtrans.
     * Memperbarui status transaksi di database.
     *
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi order_id.
     * @return \Illuminate\Http\JsonResponse
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

            $transaction = Transaction::with('targetUser')->where('order_id', $status->order_id)->firstOrFail();

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('user.showProfile', ['username' => $transaction->targetUser->username])
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
     * Memeriksa status transaksi langganan combo melalui Midtrans.
     * Memperbarui status transaksi di database.
     *
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi order_id.
     * @return \Illuminate\Http\JsonResponse
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

            $transaction = Transaction::with('targetUser')->where('order_id', $status->order_id)->firstOrFail();

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('user.showProfile', ['username' => $transaction->targetUser->username])
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
     * Menyimpan harga langganan pengguna.
     * Memvalidasi dan memperbarui harga langganan di database.
     *
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi harga langganan.
     * @return \Illuminate\Http\RedirectResponse
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
     * Mengambil data langganan sistem aktif untuk pengguna tertentu.
     *
     * @param int $userId ID pengguna.
     * @return array Data langganan sistem.
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
     * Mengambil data langganan pengguna aktif untuk pengguna tertentu.
     *
     * @param int $userId ID pengguna.
     * @param int $targetUserId ID pengguna target.
     * @return array Data langganan pengguna.
     */
    protected function getUserSubscriptionData($userId, $targetUserId)
    {
        // Cari semua langganan aktif (baik user maupun combo)
        $userSubscription = SubscriptionUser::with('transaction')
            ->where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();

        $comboSubscription = SubscriptionCombo::with('transaction')
            ->where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();

        // Ambil end_date yang paling akhir
        $endDate = null;
        if ($userSubscription && $comboSubscription) {
            $endDate = Carbon::parse($userSubscription->end_date) > Carbon::parse($comboSubscription->end_date) 
                ? $userSubscription->end_date 
                : $comboSubscription->end_date;
        } elseif ($userSubscription) {
            $endDate = $userSubscription->end_date;
        } elseif ($comboSubscription) {
            $endDate = $comboSubscription->end_date;
        }

        $data = [
            'existingDuration' => 0,
            'endDateFormatted' => null,
            'duration' => '0 Bulan',
            'hasActiveSubscription' => false,
            'subscription' => $userSubscription ?? $comboSubscription
        ];
        
        if ($endDate) {
            $existingDuration = $this->calculateRemainingMonths(Carbon::parse($endDate));
            
            $data = [
                'existingDuration' => $existingDuration,
                'endDateFormatted' => Carbon::parse($endDate)->format('d F Y'),
                'duration' => $this->getDurationText($existingDuration),
                'hasActiveSubscription' => true,
                'subscription' => $userSubscription ?? $comboSubscription
            ];
        }
        
        return $data;
    }

    /**
     * Menghitung sisa bulan dari tanggal akhir langganan.
     *
     * @param string $endDate Tanggal akhir langganan.
     * @return int Jumlah bulan yang tersisa.
     */
    protected function calculateRemainingMonths($endDate)
    {
        $now = Carbon::now();
        $end = Carbon::parse($endDate);
        
        if ($now >= $end) {
            return 0;
        }
        
        // Calculate complete months
        $completeMonths = $now->diffInMonths($end);
        
        // Calculate remaining days after complete months
        $remainingDays = $now->copy()->addMonths($completeMonths)->diffInDays($end);
        
        // If more than 0 days remaining, count as partial month
        return $remainingDays > 0 ? $completeMonths + 1 : $completeMonths;
    }

    /**
     * Memvalidasi durasi langganan sistem.
     * Memastikan durasi baru lebih panjang dari sisa durasi aktif.
     *
     * @param int $userId ID pengguna.
     * @param string $duration Durasi langganan.
     * @return array Hasil validasi.
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
        
        $remainingMonths = $this->calculateAccurateRemainingMonths($subscription->end_date);
        $remainingText = $this->getDurationText($remainingMonths);
        
        // New duration must be longer than remaining duration
        return [
            'valid' => $durationMonths > $remainingMonths,
            'remaining_months' => $remainingMonths,
            'remaining_text' => $remainingText
        ];
    }

    /**
     * Memvalidasi durasi langganan pengguna.
     * Memastikan durasi baru lebih panjang dari sisa durasi aktif.
     *
     * @param int $userId ID pengguna.
     * @param string $duration Durasi langganan.
     * @param int $targetUserId ID pengguna target.
     * @return array Hasil validasi.
     */
    protected function validateUserSubscriptionDuration($userId, $duration, $targetUserId)
    {
        $durationMonths = $this->getDurationInMonths($duration);
        
        // Check both user subscriptions and combo subscriptions
        $userSubscription = SubscriptionUser::where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();
            
        $comboSubscription = SubscriptionCombo::where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->first();
        
        // Get the latest end date between user and combo subscriptions
        $latestEndDate = null;
        if ($userSubscription && $comboSubscription) {
            $latestEndDate = Carbon::parse($userSubscription->end_date) > Carbon::parse($comboSubscription->end_date) 
                ? $userSubscription->end_date 
                : $comboSubscription->end_date;
        } elseif ($userSubscription) {
            $latestEndDate = $userSubscription->end_date;
        } elseif ($comboSubscription) {
            $latestEndDate = $comboSubscription->end_date;
        }
        
        // No active subscription - any duration is valid
        if (!$latestEndDate) {
            return [
                'valid' => true,
                'remaining_months' => 0,
                'remaining_text' => '0 bulan'
            ];
        }
        
        $remainingMonths = $this->calculateAccurateRemainingMonths($latestEndDate);
        $remainingText = $this->getDurationText($latestEndDate);
        
        // New duration must be longer than remaining duration
        return [
            'valid' => $durationMonths > $remainingMonths,
            'remaining_months' => $remainingMonths,
            'remaining_text' => $remainingText
        ];
    }

    /**
     * Memvalidasi durasi langganan combo.
     * Memastikan durasi baru lebih panjang dari sisa durasi aktif.
     *
     * @param int $userId ID pengguna.
     * @param string $duration Durasi langganan.
     * @param int $targetUserId ID pengguna target.
     * @return array Hasil validasi.
     */
    protected function validateComboSubscriptionDuration($userId, $duration, $targetUserId)
    {
        $durationMonths = $this->getDurationInMonths($duration);
        
        // Get system subscription
        $systemSubscription = SubscriptionSystem::where('user_id', $userId)
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'desc')
            ->first();
        
        // Get user subscription
        $userSubscription = SubscriptionUser::where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'desc')
            ->first();
        
        // Get combo subscription
        $comboSubscription = SubscriptionCombo::where('user_id', $userId)
            ->where('target_user_id', $targetUserId)
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'desc')
            ->first();
        
        // Calculate remaining months for each
        $systemRemaining = $systemSubscription ? $this->calculateAccurateRemainingMonths($systemSubscription->end_date) : 0;
        $userRemaining = $userSubscription ? $this->calculateAccurateRemainingMonths($userSubscription->end_date) : 0;
        $comboRemaining = $comboSubscription ? $this->calculateAccurateRemainingMonths($comboSubscription->end_date) : 0;
        
        // Get the maximum remaining duration
        $maxRemaining = max($systemRemaining, $userRemaining, $comboRemaining);
        
        return [
            'valid' => $durationMonths > $maxRemaining,
            'remaining_months' => $maxRemaining,
            'remaining_text' => $this->getDurationText($maxRemaining)
        ];
    }

    /**
     * Memproses pembayaran melalui Midtrans.
     * Membuat transaksi baru dan mengembalikan token Snap Midtrans.
     *
     * @param \App\Models\User $user Pengguna yang melakukan pembayaran.
     * @param int $amount Jumlah pembayaran.
     * @param string $type Jenis langganan (system, user, combo).
     * @param array $metadata Metadata tambahan untuk transaksi.
     * @return \Illuminate\Http\JsonResponse
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
            'metadata' => json_encode($metadata), // Ensure metadata is JSON encoded
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
            return response()->json(['error' => 'Payment gateway error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui status transaksi di database berdasarkan status dari Midtrans.
     *
     * @param \App\Models\Transaction $transaction Transaksi yang akan diperbarui.
     * @param object $status Status transaksi dari Midtrans.
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

    /**
     * Memproses pembayaran yang berhasil.
     * Memperbarui atau membuat langganan baru berdasarkan transaksi.
     *
     * @param \App\Models\Transaction $transaction Transaksi yang berhasil.
     * @param object $status Status transaksi dari Midtrans.
     */
    protected function processSuccessfulPayment($transaction, $status)
    {
        $type = $transaction->type;
        $config = $this->subscriptionTypes[$type];
        
        // Get duration based on type
        $duration = $this->getDurationFromPayment($transaction, $status, $type);

        // Handle combo subscription separately
        if ($type === 'combo') {
            return $this->handleComboSubscription($transaction, $duration, $status);
        }

        // Existing logic for system/user subscriptions
        $existingSubscriptions = $config['model']::where('user_id', $transaction->user_id)
            ->when(in_array($type, ['user', 'combo']), function ($query) use ($transaction) {
                $query->where('target_user_id', $transaction->target_user_id);
            })
            ->where('end_date', '>', Carbon::now())
            ->orderBy('end_date', 'desc')
            ->get();

        $latestActiveSubscription = $existingSubscriptions->first();

        $endDate = $latestActiveSubscription 
            ? Carbon::parse($latestActiveSubscription->end_date)->addMonths($duration)
            : Carbon::now()->addMonths($duration);

        $subscriptionData = [
            'user_id' => $transaction->user_id,
            'price' => $transaction->gross_amount,
            'start_date' => $latestActiveSubscription ? $latestActiveSubscription->start_date : Carbon::now(),
            'end_date' => $endDate,
            'transaction_id' => $transaction->id,
        ];
        
        if (in_array($type, ['user', 'combo'])) {
            $subscriptionData['target_user_id'] = $transaction->target_user_id;
        }

        $subscription = $config['model']::updateOrCreate(
            ['id' => $latestActiveSubscription ? $latestActiveSubscription->id : null],
            $subscriptionData
        );

        // Process balance for user subscriptions
        if (in_array($type, ['user', 'combo'])) {
            $this->addCreatorBalance($transaction, $type);
        }
        
        // Update user role if system/combo
        if (in_array($type, ['system', 'combo'])) {
            $transaction->user->update([
                'role' => 'pro',
                'subscription_ends_at' => $endDate
            ]);
        }

        // Kondisional untuk pesan
        $isNewSubscription = !$latestActiveSubscription; // Jika tidak ada langganan aktif sebelumnya
        $message = '';

        if ($type === 'system') {
            $message = $isNewSubscription
                ? "Selamat! Anda telah mengaktifkan langganan sistem sampai " . $endDate->format('d F Y')
                : "Selamat! Anda telah memperpanjang langganan sistem sampai " . $endDate->format('d F Y');
        } elseif ($type === 'user') {
            $message = $isNewSubscription
                ? "Selamat! Anda telah mengaktifkan langganan ke pengguna sampai " . $endDate->format('d F Y')
                : "Selamat! Anda telah memperpanjang langganan ke pengguna sampai " . $endDate->format('d F Y');
        } elseif ($type === 'combo') {
            $message = $isNewSubscription
                ? "Selamat! Anda telah mengaktifkan langganan kombo sampai " . $endDate->format('d F Y')
                : "Selamat! Anda telah memperpanjang langganan kombo sampai " . $endDate->format('d F Y');
        }

        // Simpan pesan ke session
        session()->flash('subscription_message', $message);

        $this->createNotification($transaction, $endDate);

        Log::info("Subscription {$type} created/updated", [
            'transaction_id' => $transaction->id,
            'end_date' => $endDate->format('Y-m-d')
        ]);
    }

    /**
     * Memproses langganan combo secara terpisah.
     * Membuat atau memperbarui langganan sistem dan pengguna.
     *
     * @param \App\Models\Transaction $transaction Transaksi combo.
     * @param int $duration Durasi langganan.
     * @param object $status Status transaksi dari Midtrans.
     * @return \App\Models\SubscriptionCombo
     */
    protected function handleComboSubscription($transaction, $duration, $status)
    {
        $metadata = json_decode($transaction->metadata, true);

        if (!isset($metadata['system_price']) || !isset($metadata['user_price'])) {
            throw new \Exception('Invalid metadata for combo subscription');
        }

        $systemPrice = $metadata['system_price'];
        $userPrice = $metadata['user_price'];

        // Process system subscription
        $systemSubscription = SubscriptionSystem::where('user_id', $transaction->user_id)
            ->where('end_date', '>=', now())
            ->orderBy('end_date', 'desc')
            ->first();

        $systemEndDate = $systemSubscription 
            ? Carbon::parse($systemSubscription->end_date)->addMonths($duration)
            : now()->addMonths($duration);

        SubscriptionSystem::updateOrCreate(
            ['user_id' => $transaction->user_id],
            [
                'price' => $systemPrice,
                'start_date' => $systemSubscription ? $systemSubscription->start_date : now(),
                'end_date' => $systemEndDate,
                'transaction_id' => $transaction->id
            ]
        );

        // Process user subscription
        $userSubscription = SubscriptionUser::where('user_id', $transaction->user_id)
            ->where('target_user_id', $transaction->target_user_id)
            ->where('end_date', '>=', now())
            ->orderBy('end_date', 'desc')
            ->first();

        $userEndDate = $userSubscription 
            ? Carbon::parse($userSubscription->end_date)->addMonths($duration)
            : now()->addMonths($duration);

        SubscriptionUser::updateOrCreate(
            [
                'user_id' => $transaction->user_id,
                'target_user_id' => $transaction->target_user_id
            ],
            [
                'price' => $userPrice,
                'start_date' => $userSubscription ? $userSubscription->start_date : now(),
                'end_date' => $userEndDate,
                'transaction_id' => $transaction->id
            ]
        );

        // Create combo record
        $comboSubscription = SubscriptionCombo::create([
            'user_id' => $transaction->user_id,
            'target_user_id' => $transaction->target_user_id,
            'system_price' => $systemPrice,
            'user_price' => $userPrice,
            'total_price' => $transaction->gross_amount,
            'start_date' => now(),
            'end_date' => $userEndDate,
            'transaction_id' => $transaction->id
        ]);

        // Add balance to creator
        $targetUser = User::find($transaction->target_user_id);
        if ($targetUser && $userPrice > 0) {
            $targetUser->balance += $userPrice;
            $targetUser->save();

            BalanceHistory::create([
                'user_id' => $targetUser->id,
                'type' => 'income',
                'amount' => $userPrice,
                'source_id' => $transaction->id,
                'source_type' => 'subscription',
                'status' => 'success',
                'note' => 'Pendapatan dari langganan kombo'
            ]);
        }

        // Update user role
        $transaction->user->update([
            'role' => 'pro',
            'subscription_ends_at' => $systemEndDate
        ]);

        // Create notification
        $this->createNotification($transaction, $userEndDate);

        Log::info("Combo subscription processed", [
            'user_id' => $transaction->user_id,
            'system_end' => $systemEndDate->format('Y-m-d'),
            'user_end' => $userEndDate->format('Y-m-d'),
            'combo_id' => $comboSubscription->id
        ]);

        return $comboSubscription;
    }

    /**
     * Menambahkan saldo ke kreator berdasarkan transaksi langganan.
     *
     * @param \App\Models\Transaction $transaction Transaksi langganan.
     * @param string $type Jenis langganan (user atau combo).
     */
    protected function addCreatorBalance($transaction, $type)
    {
        $targetUser = User::find($transaction->target_user_id);
        
        if ($targetUser) {
            $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];
            $amountToAdd = ($type === 'combo') 
                ? ($metadata['user_price'] ?? 0) 
                : $transaction->gross_amount;
            


            if ($amountToAdd > 0) {
                // Hitung potongan 20%
                $deduction = $amountToAdd * 0.2;
                $netAmount = $amountToAdd - $deduction;
                
                // Tambahkan saldo setelah dipotong
                $targetUser->balance += $netAmount;
                $targetUser->save();
    
                BalanceHistory::create([
                    'user_id' => $targetUser->id,
                    'type' => 'income', // Pastikan ini sesuai dengan nilai yang diizinkan
                    'amount' => $netAmount,
                    'source_id' => $transaction->id,
                    'source_type' => 'subscription',
                    'status' => 'success',
                    'note' => 'Pendapatan dari langganan ' . $type,
                ]);
            }
        }
    }

    /**
     * Membuat atau mendapatkan langganan berdasarkan transaksi.
     *
     * @param \App\Models\Transaction $transaction Transaksi langganan.
     * @param array $config Konfigurasi model langganan.
     * @param int $duration Durasi langganan.
     * @return \App\Models\Transaction
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
     * Menghitung tanggal akhir baru untuk langganan.
     *
     * @param \App\Models\Transaction $subscription Langganan yang aktif.
     * @param int $duration Durasi langganan baru.
     * @return \Carbon\Carbon Tanggal akhir baru.
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
     * Membuat notifikasi untuk langganan.
     *
     * @param \App\Models\Transaction $transaction Transaksi langganan.
     * @param string $endDate Tanggal akhir langganan.
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
     * Mendapatkan pesan notifikasi berdasarkan jenis langganan.
     *
     * @param \App\Models\Transaction $transaction Transaksi langganan.
     * @param string $endDateFormatted Tanggal akhir langganan yang diformat.
     * @return string Pesan notifikasi.
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
     * Mendapatkan durasi langganan berdasarkan pembayaran.
     *
     * @param \App\Models\Transaction $transaction Transaksi langganan.
     * @param object $status Status transaksi dari Midtrans.
     * @param string $type Jenis langganan (system, user, combo).
     * @return int Durasi langganan dalam bulan.
     */
    protected function getDurationFromPayment($transaction, $status, $type)
    {
        try {
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
                    $metadata = json_decode($transaction->metadata, true);
                    if (isset($metadata['duration'])) {
                        return $this->getDurationInMonths($metadata['duration']);
                    }
                    throw new \Exception('Duration not found in combo metadata');
            }
    
            throw new \Exception('Could not determine subscription duration');
        } catch (\Exception $e) {
            Log::error("Failed to get duration for {$type} subscription: " . $e->getMessage());
            return $type === 'combo' ? 12 : 1; // Default longer duration for combo
        }
    }

    /**
     * Mengonversi string durasi menjadi jumlah bulan.
     *
     * @param string $duration String durasi (1_month, 3_months, dll.).
     * @return int Jumlah bulan.
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
     * Mengonversi jumlah bulan menjadi teks durasi.
     *
     * @param string $endDate Tanggal akhir langganan.
     * @return string Teks durasi.
     */
    protected function getDurationText($endDate)
    {
        $now = now();
        $end = Carbon::parse($endDate);
        
        if ($now >= $end) {
            return '0 Hari';
        }
        
        $diffInMonths = $now->diffInMonths($end);
        $remainingDays = $now->copy()->addMonths($diffInMonths)->diffInDays($end);
        
        // If there are remaining days after complete months, count as partial month
        if ($remainingDays > 0) {
            $diffInMonths++;
        }
        
        if ($diffInMonths >= 12) {
            $years = floor($diffInMonths / 12);
            return $years . ' Tahun' . ($years > 1 ? '' : '');
        } elseif ($diffInMonths >= 1) {
            return $diffInMonths . ' Bulan' . ($diffInMonths > 1 ? '' : '');
        } else {
            $diffInDays = $now->diffInDays($end);
            return $diffInDays . ' Hari' . ($diffInDays > 1 ? '' : '');
        }
    }

    /**
     * Menghitung sisa bulan secara akurat dari tanggal akhir langganan.
     *
     * @param string $endDate Tanggal akhir langganan.
     * @return int Jumlah bulan yang tersisa.
     */
    protected function calculateAccurateRemainingMonths($endDate)
    {
        $now = Carbon::now();
        $end = Carbon::parse($endDate);
        
        if ($now >= $end) {
            return 0;
        }
        
        $diffInMonths = $now->diffInMonths($end);
        $dateAfterMonths = $now->copy()->addMonths($diffInMonths);
        
        if ($dateAfterMonths < $end) {
            $diffInMonths++;
        }
        
        Log::info("Remaining months calculated: {$diffInMonths} for end date {$endDate}");
        
        return $diffInMonths;
    }
}