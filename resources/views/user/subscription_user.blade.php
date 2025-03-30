@extends('layouts.app')

@section('content')
<style>
    .subscription-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }
    .creator-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    .creator-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #10b981;
        margin-bottom: 1rem;
    }
    .creator-name {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }
    .creator-username {
        font-size: 1.1rem;
        color: #4a5568;
    }
    .section-title {
        text-align: center;
        font-size: 1.75rem;
        font-weight: 600;
        color: #2d3748;
        margin: 3rem 0 2rem;
        position: relative;
    }
    .section-title:after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: #10b981;
        margin: 0.5rem auto 0;
        border-radius: 2px;
    }
    .subscription-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-bottom: 4rem;
    }
    .subscription-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    .subscription-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 1.5rem;
        text-align: center;
    }
    .card-header h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }
    .card-price {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }
    .card-body {
        padding: 1.5rem;
    }
    .card-features {
        list-style: none;
        padding: 0;
        margin: 0 0 1.5rem 0;
    }
    .card-features li {
        padding: 0.5rem 0;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
    }
    .card-features li:last-child {
        border-bottom: none;
    }
    .card-features i {
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }
    .bi-check-circle {
        color: #10b981;
    }
    .card-button {
        display: block;
        width: 100%;
        padding: 0.75rem;
        border: none;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
    }
    .btn-disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }
    .combo-card .card-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    .combo-card .btn-primary {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    .combo-card .btn-primary:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        box-shadow: 0 4px 6px rgba(124, 58, 237, 0.2);
    }
    .price-per-month {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .subscription-status {
        background-color: #f0fdf4;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-left: 5px solid #10b981;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="subscription-container">
    <div class="creator-header">
        <img src="{{ $user->profile_photo_url }}" class="creator-avatar" alt="{{ $user->name }}">
        <h1 class="creator-name">{{ $user->name }}</h1>
        <p class="creator-username">{{ $user->username }}</p>
    </div>

    @if($existingDuration > 0)
    <div class="subscription-status">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Your Current Subscription</h4>
                <p class="mb-0">You have an active subscription for <strong>{{ $duration }}</strong> 
                   that will end on <strong>{{ $endDateFormatted }}</strong>.</p>
            </div>
            <span class="badge badge-success">Active</span>
        </div>
    </div>
    @endif

    <h2 class="section-title">Creator Subscriptions</h2>
    
    <div class="subscription-cards">
        @if($subscriptionPrices->price_1_month)
        <div class="subscription-card">
            <div class="card-header">
                <h3>1 Month</h3>
                <div class="card-price">Rp {{ number_format($subscriptionPrices->price_1_month, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ number_format($subscriptionPrices->price_1_month / 1, 0, ',', '.') }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> Access to exclusive content</li>
                    <li><i class="bi bi-check-circle"></i> Direct messages</li>
                    <li><i class="bi bi-check-circle"></i> Behind-the-scenes</li>
                </ul>
                @if($existingDuration >= 1)
                    <button class="card-button btn-disabled" disabled>Subscribed</button>
                @else
                    <button class="card-button btn-primary" onclick="buySubscription('{{ $subscriptionPrices->price_1_month }}', '1_month')">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        @if($subscriptionPrices->price_3_months)
        <div class="subscription-card">
            <div class="card-header">
                <h3>3 Months</h3>
                <div class="card-price">Rp {{ number_format($subscriptionPrices->price_3_months, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ number_format($subscriptionPrices->price_3_months / 3, 0, ',', '.') }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All 1-month benefits</li>
                    <li><i class="bi bi-check-circle"></i> Exclusive live streams</li>
                    <li><i class="bi bi-check-circle"></i> Early access to content</li>
                </ul>
                @if($existingDuration >= 3)
                    <button class="card-button btn-disabled" disabled>Subscribed</button>
                @else
                    <button class="card-button btn-primary" onclick="buySubscription('{{ $subscriptionPrices->price_3_months }}', '3_months')">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        @if($subscriptionPrices->price_6_months)
        <div class="subscription-card">
            <div class="card-header">
                <h3>6 Months</h3>
                <div class="card-price">Rp {{ number_format($subscriptionPrices->price_6_months, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ number_format($subscriptionPrices->price_6_months / 6, 0, ',', '.') }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All 3-month benefits</li>
                    <li><i class="bi bi-check-circle"></i> Personalized content</li>
                    <li><i class="bi bi-check-circle"></i> Monthly Q&A sessions</li>
                </ul>
                @if($existingDuration >= 6)
                    <button class="card-button btn-disabled" disabled>Subscribed</button>
                @else
                    <button class="card-button btn-primary" onclick="buySubscription('{{ $subscriptionPrices->price_6_months }}', '6_months')">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        @if($subscriptionPrices->price_1_year)
        <div class="subscription-card">
            <div class="card-header">
                <h3>1 Year</h3>
                <div class="card-price">Rp {{ number_format($subscriptionPrices->price_1_year, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ number_format($subscriptionPrices->price_1_year / 12, 0, ',', '.') }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All 6-month benefits</li>
                    <li><i class="bi bi-check-circle"></i> Annual surprise gift</li>
                    <li><i class="bi bi-check-circle"></i> Priority requests</li>
                </ul>
                @if($existingDuration >= 12)
                    <button class="card-button btn-disabled" disabled>Subscribed</button>
                @else
                    <button class="card-button btn-primary" onclick="buySubscription('{{ $subscriptionPrices->price_1_year }}', '1_year')">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>

    <h2 class="section-title">Combo Subscriptions</h2>
    
    <div class="subscription-cards">
        @if($subscriptionPrices->price_1_month)
        @php
            $comboPrice1Month = $systemPrices->where('duration', '1_month')->first()->price + $subscriptionPrices->price_1_month;
        @endphp
        <div class="subscription-card combo-card">
            <div class="card-header">
                <h3>1 Month Combo</h3>
                <div class="card-price">Rp {{ number_format($comboPrice1Month, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ number_format($comboPrice1Month / 1, 0, ',', '.') }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All creator benefits</li>
                    <li><i class="bi bi-check-circle"></i> Plus system features</li>
                    <li><i class="bi bi-check-circle"></i> Best value package</li>
                </ul>
                @if($existingDuration >= 1)
                    <button class="card-button btn-disabled" disabled>Subscribed</button>
                @else
                    <button class="card-button btn-primary" onclick="buyComboSubscription('{{ $comboPrice1Month }}', '1_month')">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        @if($subscriptionPrices->price_3_months)
        @php
            $comboPrice3Months = $systemPrices->where('duration', '3_months')->first()->price + $subscriptionPrices->price_3_months;
        @endphp
        <div class="subscription-card combo-card">
            <div class="card-header">
                <h3>3 Months Combo</h3>
                <div class="card-price">Rp {{ number_format($comboPrice3Months, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ number_format($comboPrice3Months / 3, 0, ',', '.') }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All creator benefits</li>
                    <li><i class="bi bi-check-circle"></i> Plus system features</li>
                    <li><i class="bi bi-check-circle"></i> Best value package</li>
                </ul>
                @if($existingDuration >= 3)
                    <button class="card-button btn-disabled" disabled>Subscribed</button>
                @else
                    <button class="card-button btn-primary" onclick="buyComboSubscription('{{ $comboPrice3Months }}', '3_months')">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        @if($subscriptionPrices->price_6_months)
        @php
            $comboPrice6Months = $systemPrices->where('duration', '6_months')->first()->price + $subscriptionPrices->price_6_months;
        @endphp
        <div class="subscription-card combo-card">
            <div class="card-header">
                <h3>6 Months Combo</h3>
                <div class="card-price">Rp {{ number_format($comboPrice6Months, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ number_format($comboPrice6Months / 6, 0, ',', '.') }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All creator benefits</li>
                    <li><i class="bi bi-check-circle"></i> Plus system features</li>
                    <li><i class="bi bi-check-circle"></i> Best value package</li>
                </ul>
                @if($existingDuration >= 6)
                    <button class="card-button btn-disabled" disabled>Subscribed</button>
                @else
                    <button class="card-button btn-primary" onclick="buyComboSubscription('{{ $comboPrice6Months }}', '6_months')">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        @if($subscriptionPrices->price_1_year)
        @php
            $comboPrice1Year = $systemPrices->where('duration', '1_year')->first()->price + $subscriptionPrices->price_1_year;
        @endphp
        <div class="subscription-card combo-card">
            <div class="card-header">
                <h3>1 Year Combo</h3>
                <div class="card-price">Rp {{ number_format($comboPrice1Year, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ number_format($comboPrice1Year / 12, 0, ',', '.') }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All creator benefits</li>
                    <li><i class="bi bi-check-circle"></i> Plus system features</li>
                    <li><i class="bi bi-check-circle"></i> Best value package</li>
                </ul>
                @if($existingDuration >= 12)
                    <button class="card-button btn-disabled" disabled>Subscribed</button>
                @else
                    <button class="card-button btn-primary" onclick="buyComboSubscription('{{ $comboPrice1Year }}', '1_year')">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Midtrans SDK -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function buySubscription(price, package) {
        const button = event.target;
        const originalText = button.innerHTML;
        
        // Show loading state
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processing...
        `;
        button.disabled = true;
        
        Swal.fire({
            title: 'Processing Payment',
            html: 'Preparing your subscription...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('subscription.subscribe', ['username' => $user->username]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                package: package,
                _token: '{{ csrf_token() }}'
            })
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Payment processing failed');
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            button.innerHTML = originalText;
            button.disabled = false;
            
            if (data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        showSuccessAlert('Payment Successful!', 'Your subscription has been activated.');
                        checkTransactionStatus(result.order_id);
                    },
                    onPending: function(result) {
                        showInfoAlert('Payment Pending', 'Please complete your payment to activate subscription.');
                        checkTransactionStatus(result.order_id);
                    },
                    onError: function(result) {
                        showPaymentError(result);
                    },
                    onClose: function() {
                        showWarningAlert('Payment Cancelled', 'You closed the payment popup without completing the transaction.');
                    }
                });
            } else {
                throw new Error('Failed to get payment token');
            }
        })
        .catch(error => {
            button.innerHTML = originalText;
            button.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Payment Failed',
                text: error.message || 'An error occurred during payment processing',
                confirmButtonColor: '#3b82f6',
            });
        });
    }

    function buyComboSubscription(price, duration) {
        const button = event.target;
        const originalText = button.innerHTML;
        
        // Show loading state
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processing...
        `;
        button.disabled = true;
        
        Swal.fire({
            title: 'Processing Payment',
            html: 'Preparing your subscription...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('subscription.subscribeCombo', ['username' => $user->username]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                combo_price: price,
                duration: duration,
                _token: '{{ csrf_token() }}'
            })
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Payment processing failed');
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            button.innerHTML = originalText;
            button.disabled = false;
            
            if (data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        showSuccessAlert('Payment Successful!', 'Your combo subscription has been activated.');
                        checkComboTransactionStatus(result.order_id);
                    },
                    onPending: function(result) {
                        showInfoAlert('Payment Pending', 'Please complete your payment to activate subscription.');
                        checkComboTransactionStatus(result.order_id);
                    },
                    onError: function(result) {
                        showPaymentError(result);
                    },
                    onClose: function() {
                        showWarningAlert('Payment Cancelled', 'You closed the payment popup without completing the transaction.');
                    }
                });
            } else {
                throw new Error('Failed to get payment token');
            }
        })
        .catch(error => {
            button.innerHTML = originalText;
            button.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Payment Failed',
                text: error.message || 'An error occurred during payment processing',
                confirmButtonColor: '#8b5cf6',
            });
        });
    }

    function checkTransactionStatus(orderId) {
        Swal.fire({
            title: 'Verifying Payment',
            html: 'Please wait while we verify your payment...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('transaction.checkStatusUser') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                order_id: orderId,
                _token: '{{ csrf_token() }}'
            })
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Verification failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Subscription Activated!',
                    text: 'Your creator subscription is now active.',
                    confirmButtonColor: '#3b82f6',
                    timer: 3000,
                    timerProgressBar: true,
                    willClose: () => {
                        window.location.reload();
                    }
                });
            } else {
                throw new Error(data.message || 'Verification failed');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: error.message || 'Failed to verify payment status',
                confirmButtonColor: '#3b82f6',
            });
        });
    }

    function checkComboTransactionStatus(orderId) {
        Swal.fire({
            title: 'Verifying Payment',
            html: 'Please wait while we verify your payment...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('transaction.checkStatusCombo') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                order_id: orderId,
                _token: '{{ csrf_token() }}'
            })
        })
        .then(async response => {
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Verification failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Subscription Activated!',
                    text: 'Your combo subscription is now active.',
                    confirmButtonColor: '#8b5cf6',
                    timer: 3000,
                    timerProgressBar: true,
                    willClose: () => {
                        window.location.reload();
                    }
                });
            } else {
                throw new Error(data.message || 'Verification failed');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: error.message || 'Failed to verify payment status',
                confirmButtonColor: '#8b5cf6',
            });
        });
    }

    function showPaymentError(result) {
        let errorMessage = 'Payment failed. Please try again.';
        
        if (result.status_code === '202') {
            errorMessage = 'Transaction was denied by your bank.';
        } else if (result.status_code === '400') {
            errorMessage = 'Invalid payment data.';
        } else if (result.status_message) {
            errorMessage = result.status_message;
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Payment Failed',
            html: `
                <div class="text-left">
                    <p>${errorMessage}</p>
                    ${result.status_code ? `<p class="mb-1"><strong>Error Code:</strong> ${result.status_code}</p>` : ''}
                    ${result.transaction_id ? `<p class="mb-0"><strong>Transaction ID:</strong> ${result.transaction_id}</p>` : ''}
                </div>
            `,
            confirmButtonColor: '#3b82f6',
        });
    }

    function showSuccessAlert(title, text) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#3b82f6',
            timer: 3000,
            timerProgressBar: true,
        });
    }

    function showInfoAlert(title, text) {
        Swal.fire({
            icon: 'info',
            title: title,
            text: text,
            confirmButtonColor: '#3b82f6',
            timer: 5000,
            timerProgressBar: true,
        });
    }

    function showWarningAlert(title, text) {
        Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonColor: '#3b82f6',
        });
    }
</script>
@endsection