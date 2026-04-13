@extends('layouts.app')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/subscription-user.css') }}">
@endpush

@section('content')

<div class="subscription-container">
    <div class="creator-header">
        <img src="{{ $user->profile_photo_url }}" class="creator-avatar" alt="{{ $user->name }}">
        <h1 class="creator-name">{{ $user->name }}</h1>
        <p class="creator-username">{{ $user->username }}</p>
    </div>
    
@if($hasActiveSubscription)
<div class="subscription-status">

    {{-- Kalau ada combo --}}
    @if($hasComboSubscription)
    <h4>Langganan Kombo Aktif</h4>
        <p>
            Anda memiliki langganan <strong>user</strong> sampai {{ $comboEndDateFormatted }} ({{ $comboDuration }}) 
            dan <strong>sistem</strong> sampai {{ $systemEndDateFormatted }} ({{ $systemDuration }}).
        </p>
    @else
        {{-- Kalau ada user & system --}}
        @if($hasUserSubscription && $hasSystemSubscription)
        <h4>Langganan User dan Sistem Aktif</h4>
            <p>
                Anda memiliki langganan <strong>user</strong> sampai {{ $userEndDateFormatted }} ({{ $userDuration }}) 
                dan <strong>sistem</strong> sampai {{ $systemEndDateFormatted }} ({{ $systemDuration }}).
            </p>

        {{-- Kalau cuma user --}}
        @elseif($hasUserSubscription)
            <h4>Langganan User Aktif</h4>
            <p>
                Anda memiliki langganan <strong>user</strong> sampai {{ $userEndDateFormatted }} ({{ $userDuration }}).
            </p>

        {{-- Kalau cuma system --}}
        @elseif($hasSystemSubscription)
            <h4>Langganan Sistem Aktif</h4>
            <p>
                Anda memiliki langganan <strong>sistem</strong> sampai {{ $systemEndDateFormatted }} ({{ $systemDuration }}).
            </p>
        @endif
    @endif

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
                @if($userExistingDuration >= 1)
                    <button class="card-button btn-disabled" disabled>
                        @if($hasUserSubscription ?? false)
                            Already Subscribed
                        @else
                            Select Plan
                        @endif
                    </button>
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
                    <li><i class="bi bi-check-circle"></i> Access to exclusive content</li>
                    <li><i class="bi bi-check-circle"></i> Direct messages</li>
                    <li><i class="bi bi-check-circle"></i> Behind-the-scenes</li>
                </ul>
                @if($userExistingDuration >= 3)
                    <button class="card-button btn-disabled" disabled>
                        @if($hasUserSubscription ?? false)
                            Already Subscribed
                        @else
                            Select Plan
                        @endif
                    </button>
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
                    <li><i class="bi bi-check-circle"></i> Access to exclusive content</li>
                    <li><i class="bi bi-check-circle"></i> Direct messages</li>
                    <li><i class="bi bi-check-circle"></i> Behind-the-scenes</li>
                </ul>
                @if($userExistingDuration >= 6)
                    <button class="card-button btn-disabled" disabled>
                        @if($hasUserSubscription ?? false)
                            Already Subscribed
                        @else
                            Select Plan
                        @endif
                    </button>
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
                    <li><i class="bi bi-check-circle"></i> Access to exclusive content</li>
                    <li><i class="bi bi-check-circle"></i> Direct messages</li>
                    <li><i class="bi bi-check-circle"></i> Behind-the-scenes</li>
                </ul>
                @if($userExistingDuration >= 12)
                    <button class="card-button btn-disabled" disabled>
                        @if($hasUserSubscription ?? false)
                            Already Subscribed
                        @else
                            Select Plan
                        @endif
                    </button>
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
        {{-- COMBO 1 MONTH --}}
        @if($subscriptionPrices && $subscriptionPrices->price_1_month && isset($systemPrices['1_month']))
        @php
            $systemPrice = $systemPrices['1_month'] ?? 0;
            $userPrice = $subscriptionPrices->price_1_month ?? 0;
            $comboPrice = $systemPrice + $userPrice;
            $pricePerMonth = number_format(round($comboPrice / 1), 0, ',', '.');
            $durationMonths = 1;
            $shouldDisable = $durationMonths <= $maxDuration;
        @endphp
        
        <div class="subscription-card combo-card">
            <div class="card-header">
                <h3>1 Month Combo</h3>
                <div class="card-price">Rp {{ number_format($comboPrice, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ $pricePerMonth }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All creator benefits</li>
                    <li><i class="bi bi-check-circle"></i> Plus system features</li>
                    <li><i class="bi bi-check-circle"></i> Best value package</li>
                </ul>
                @if($shouldDisable)
                    <button class="card-button btn-disabled" disabled>
                        @if($hasComboSubscription)
                            Already Subscribed
                        @else
                            Choose Longer Duration
                        @endif
                    </button>
                    
                @else
                    <button class="card-button btn-primary" 
                            onclick="buyComboSubscription('{{ $comboPrice }}', '1_month')"
                            data-system-price="{{ $systemPrice }}"
                            data-user-price="{{ $userPrice }}">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        {{-- COMBO 3 MONTHS --}}
        @if($subscriptionPrices && $subscriptionPrices->price_3_months && isset($systemPrices['3_months']))
        @php
            $systemPrice = $systemPrices['3_months'] ?? 0;
            $userPrice = $subscriptionPrices->price_3_months ?? 0;
            $comboPrice = $systemPrice + $userPrice;
            $pricePerMonth = number_format(round($comboPrice / 3), 0, ',', '.');
            $durationMonths = 3;
            $shouldDisable = $durationMonths <= $maxDuration;
        @endphp
        
        <div class="subscription-card combo-card">
            <div class="card-header">
                <h3>3 Months Combo</h3>
                <div class="card-price">Rp {{ number_format($comboPrice, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ $pricePerMonth }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All creator benefits</li>
                    <li><i class="bi bi-check-circle"></i> Plus system features</li>
                    <li><i class="bi bi-check-circle"></i> Best value package</li>
                </ul>
                @if($shouldDisable)
                    <button class="card-button btn-disabled" disabled>
                        @if($hasComboSubscription)
                            Already Subscribed
                        @else
                            Choose Longer Duration
                        @endif
                    </button>
                    
                @else
                    <button class="card-button btn-primary" 
                            onclick="buyComboSubscription('{{ $comboPrice }}', '3_months')"
                            data-system-price="{{ $systemPrice }}"
                            data-user-price="{{ $userPrice }}">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        {{-- COMBO 6 MONTHS --}}
        @if($subscriptionPrices && $subscriptionPrices->price_6_months && isset($systemPrices['6_months']))
        @php
            $systemPrice = $systemPrices['6_months'] ?? 0;
            $userPrice = $subscriptionPrices->price_6_months ?? 0;
            $comboPrice = $systemPrice + $userPrice;
            $pricePerMonth = number_format(round($comboPrice / 6), 0, ',', '.');
            $durationMonths = 6;
            $shouldDisable = $durationMonths <= $maxDuration;
        @endphp
        
        <div class="subscription-card combo-card">
            <div class="card-header">
                <h3>6 Months Combo</h3>
                <div class="card-price">Rp {{ number_format($comboPrice, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ $pricePerMonth }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All creator benefits</li>
                    <li><i class="bi bi-check-circle"></i> Plus system features</li>
                    <li><i class="bi bi-check-circle"></i> Best value package</li>
                </ul>
                @if($shouldDisable)
                    <button class="card-button btn-disabled" disabled>
                        @if($hasComboSubscription)
                            Already Subscribed
                        @else
                            Choose Longer Duration
                        @endif
                    </button>
                    
                @else
                    <button class="card-button btn-primary" 
                            onclick="buyComboSubscription('{{ $comboPrice }}', '6_months')"
                            data-system-price="{{ $systemPrice }}"
                            data-user-price="{{ $userPrice }}">
                        Subscribe Now
                    </button>
                @endif
            </div>
        </div>
        @endif
        
        {{-- COMBO 1 YEAR --}}
        @if($subscriptionPrices && $subscriptionPrices->price_1_year && isset($systemPrices['1_year']))
        @php
            $systemPrice = $systemPrices['1_year'] ?? 0;
            $userPrice = $subscriptionPrices->price_1_year ?? 0;
            $comboPrice = $systemPrice + $userPrice;
            $pricePerMonth = number_format(round($comboPrice / 12), 0, ',', '.');
            $durationMonths = 12;
            $shouldDisable = $durationMonths <= $maxDuration;
        @endphp
        
        <div class="subscription-card combo-card">
            <div class="card-header">
                <h3>1 Year Combo</h3>
                <div class="card-price">Rp {{ number_format($comboPrice, 0, ',', '.') }}</div>
                <div class="price-per-month">Rp {{ $pricePerMonth }}/month</div>
            </div>
            <div class="card-body">
                <ul class="card-features">
                    <li><i class="bi bi-check-circle"></i> All creator benefits</li>
                    <li><i class="bi bi-check-circle"></i> Plus system features</li>
                    <li><i class="bi bi-check-circle"></i> Best value package</li>
                </ul>
                @if($shouldDisable)
                    <button class="card-button btn-disabled" disabled>
                        @if($hasComboSubscription)
                            Already Subscribed
                        @else
                            Choose Longer Duration
                        @endif
                    </button>
                    
                @else
                    <button class="card-button btn-primary" 
                            onclick="buyComboSubscription('{{ $comboPrice }}', '1_year')"
                            data-system-price="{{ $systemPrice }}"
                            data-user-price="{{ $userPrice }}">
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
@endsection

@push('scripts')
<script>
window.subscriptionUserConfig = {
    csrfToken: "{{ csrf_token() }}",
    subscribeUrl: "{{ route('subscription.subscribe', ['username' => $user->username]) }}",
    subscribeComboUrl: "{{ route('subscription.subscribeCombo', ['username' => $user->username]) }}",
    checkStatusUserUrl: "{{ route('transaction.checkStatusUser') }}",
    checkStatusComboUrl: "{{ route('transaction.checkStatusCombo') }}"
};
</script>
<script src="{{ asset('js/pages/subscription-user.js') }}"></script>
@endpush