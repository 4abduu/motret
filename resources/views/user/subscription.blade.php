@extends('layouts.app')

@section('content')
<style>
    .subscription-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }
    .subscription-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    .subscription-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
    }
    .subscription-header p {
        font-size: 1.1rem;
        color: #4a5568;
    }
    .subscription-status {
        background-color: #f0fdf4;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-left: 5px solid #10b981;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .subscription-plans {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    .plan-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .plan-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }
    .plan-header h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }
    .plan-price {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 1rem 0;
    }
    .plan-price small {
        font-size: 1rem;
        opacity: 0.9;
    }
    .plan-body {
        padding: 2rem;
    }
    .plan-features {
        list-style: none;
        padding: 0;
        margin: 0 0 2rem 0;
    }
    .plan-features li {
        padding: 0.75rem 0;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
    }
    .plan-features li:last-child {
        border-bottom: none;
    }
    .plan-features i {
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }
    .bi-check-circle {
        color: #10b981;
    }
    .bi-x-circle {
        color: #ef4444;
    }
    .plan-button {
        display: block;
        width: 100%;
        padding: 1rem;
        border: none;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 4px 6px rgba(5, 150, 105, 0.2);
    }
    .btn-disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }
    .badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .badge-success {
        background-color: #dcfce7;
        color: #166534;
    }
    .popular-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background-color: #fef3c7;
        color: #92400e;
        padding: 0.25rem 1rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .price-per-month {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .duration-badge {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 1rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: inline-block;
    }
</style>

<div class="subscription-container">
    <div class="subscription-header">
        <h1>Premium Subscription Plans</h1>
        <p>Unlock exclusive features and content with our premium subscriptions</p>
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

    <div class="subscription-plans">
        @foreach($prices as $price)
        @php
            $durationMonths = match($price->duration) {
                '1_month' => 1,
                '3_months' => 3,
                '6_months' => 6,
                '1_year' => 12,
                default => 0
            };
            
            $pricePerMonth = number_format($price->price / $durationMonths, 0, ',', '.');
            
            $durationText = match($durationMonths) {
                1 => '1 Month',
                3 => '3 Months',
                6 => '6 Months',
                12 => '1 Year',
                default => $durationMonths . ' Months'
            };
            
            $canSubscribe = $durationMonths > $existingDuration;
            $isPopular = $durationMonths == 3 || $durationMonths == 6;
        @endphp
        
        <div class="plan-card">
            @if($isPopular)
                <div class="popular-badge">Most Popular</div>
            @endif
            <div class="plan-header">
                <h3>{{ $durationText }} Plan</h3>
                <div class="plan-price">
                    Rp {{ number_format($price->price, 0, ',', '.') }}
                </div>
                <div class="duration-badge">Rp {{ $pricePerMonth }}/month</div>
            </div>
            <div class="plan-body">
                <ul class="plan-features">
                    <li><i class="bi bi-check-circle"></i> Exclusive profile badge</li>
                    <li><i class="bi bi-check-circle"></i> Unlimited photo downloads</li>
                    <li><i class="bi bi-check-circle"></i> Priority customer support</li>
                    <li><i class="bi bi-check-circle"></i> Access to premium content</li>
                    <li><i class="bi bi-x-circle"></i> Creator mode features</li>
                </ul>
                
                @if($canSubscribe)
                    <button class="plan-button btn-primary" 
                            onclick="buySubscription({{ $price->id }})">
                        Subscribe Now
                    </button>
                @else
                    <button class="plan-button btn-disabled" disabled>
                        @if($existingDuration > 0)
                            Choose Longer Duration
                        @else
                            Select Plan
                        @endif
                    </button>
                    @if($existingDuration > 0)
                        <p class="text-center mt-3 text-sm text-gray-500">
                            Please select a plan with longer duration
                        </p>
                    @endif
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Midtrans SDK -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function buySubscription(subscriptionPriceId) {
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

        fetch('{{ route('transaction.create') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                subscription_price_id: subscriptionPriceId,
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
                confirmButtonColor: '#10b981',
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

        fetch('{{ route('transaction.checkStatus') }}', {
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
                    text: 'Your premium subscription is now active.',
                    confirmButtonColor: '#10b981',
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
                confirmButtonColor: '#10b981',
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
            confirmButtonColor: '#10b981',
        });
    }

    function showSuccessAlert(title, text) {
        Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            confirmButtonColor: '#10b981',
            timer: 3000,
            timerProgressBar: true,
        });
    }

    function showInfoAlert(title, text) {
        Swal.fire({
            icon: 'info',
            title: title,
            text: text,
            confirmButtonColor: '#10b981',
            timer: 5000,
            timerProgressBar: true,
        });
    }

    function showWarningAlert(title, text) {
        Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonColor: '#10b981',
        });
    }
</script>
@endsection