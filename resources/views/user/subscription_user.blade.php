@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <div class="text-center">
        <img src="{{ $user->profile_photo_url }}" class="img-lg rounded-circle mb-2" alt="profile image" />
        <h4>{{ $user->name }}</h4>
        <p class="text-muted mb-0">{{ $user->username }}</p>
    </div>
    <h3 class="mt-5 mb-3 text-center">Pilih Paket Langganan</h3>
    @if($duration && $endDateFormatted)
        <p>Anda memiliki paket langganan {{ $duration }}, yang akan berakhir pada {{ $endDateFormatted }}.</p>
    @endif

    @php
        $count = 0;
        if($subscriptionPrices->price_1_month) $count++;
        if($subscriptionPrices->price_3_months) $count++;
        if($subscriptionPrices->price_6_months) $count++;
        if($subscriptionPrices->price_1_year) $count++;
        $colSize = $count == 1 ? 'col-md-12' : ($count == 2 ? 'col-md-6' : 'col-md-4');
    @endphp

    <div class="d-flex justify-content-center">
        <div class="row">
            @if($subscriptionPrices)
                @if($subscriptionPrices->price_1_month)
                    <div class="{{ $colSize }}">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">1 Bulan</h5>
                                <p class="card-text">Rp. {{ number_format($subscriptionPrices->price_1_month, 0, ',', '.') }}</p>
                                @if($existingDuration >= 1)
                                    <button class="btn btn-success text-white" disabled>Langganan</button>
                                @else
                                    <button class="btn btn-success text-white" onclick="buySubscription('{{ $subscriptionPrices->price_1_month }}', '1_month')">Langganan</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                @if($subscriptionPrices->price_3_months)
                    <div class="{{ $colSize }} mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">3 Bulan</h5>
                                <p class="card-text">Rp. {{ number_format($subscriptionPrices->price_3_months, 0, ',', '.') }}</p>
                                @if($existingDuration >= 3)
                                    <button class="btn btn-success text-white" disabled>Langganan</button>
                                @else
                                    <button class="btn btn-success text-white" onclick="buySubscription('{{ $subscriptionPrices->price_3_months }}', '3_months')">Langganan</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                @if($subscriptionPrices->price_6_months)
                    <div class="{{ $colSize }} mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">6 Bulan</h5>
                                <p class="card-text">Rp. {{ number_format($subscriptionPrices->price_6_months, 0, ',', '.') }}</p>
                                @if($existingDuration >= 6)
                                    <button class="btn btn-success text-white" disabled>Langganan</button>
                                @else
                                    <button class="btn btn-success text-white" onclick="buySubscription('{{ $subscriptionPrices->price_6_months }}', '6_months')">Langganan</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                @if($subscriptionPrices->price_1_year)
                    <div class="{{ $colSize }} mb-2">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">1 Tahun</h5>
                                <p class="card-text">Rp. {{ number_format($subscriptionPrices->price_1_year, 0, ',', '.') }}</p>
                                @if($existingDuration >= 12)
                                    <button class="btn btn-success text-white" disabled>Langganan</button>
                                @else
                                    <button class="btn btn-success text-white" onclick="buySubscription('{{ $subscriptionPrices->price_1_year }}', '1_year')">Langganan</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <p class="text-center">Pengguna ini belum menetapkan harga langganan.</p>
            @endif
        </div>
    </div>

    <h3 class="mt-5 mb-3 text-center">Pilih Paket Langganan Kombo</h3>

    @php
        $countCombo = 0;
        if($subscriptionPrices->price_1_month) $countCombo++;
        if($subscriptionPrices->price_3_months) $countCombo++;
        if($subscriptionPrices->price_6_months) $countCombo++;
        if($subscriptionPrices->price_1_year) $countCombo++;
        $colSizeCombo = $countCombo == 1 ? 'col-md-12' : ($countCombo == 2 ? 'col-md-6' : 'col-md-4');
    @endphp

    <div class="d-flex justify-content-center">
        <div class="row">
            @if($subscriptionPrices->price_1_month)
                @php
                    $comboPrice1Month = $systemPrices->where('duration', '1_month')->first()->price + $subscriptionPrices->price_1_month;
                @endphp
                <div class="{{ $colSizeCombo }} mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">1 Bulan</h5>
                            <p class="card-text">Rp. {{ number_format($comboPrice1Month, 0, ',', '.') }}</p>
                            @if($existingDuration >= 1)
                                <button class="btn btn-success text-white" id="combo_1_month" disabled>Langganan Kombo</button>
                            @else
                                <button class="btn btn-success text-white" id="combo_1_month" onclick="buyComboSubscription('{{ $comboPrice1Month }}', '1_month')">Langganan Kombo</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($subscriptionPrices->price_3_months)
                @php
                    $comboPrice3Months = $systemPrices->where('duration', '3_months')->first()->price + $subscriptionPrices->price_3_months;
                @endphp
                <div class="{{ $colSizeCombo }} mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">3 Bulan</h5>
                            <p class="card-text">Rp. {{ number_format($comboPrice3Months, 0, ',', '.') }}</p>
                            @if($existingDuration >= 3)
                                <button class="btn btn-success text-white" id="combo_3_months" disabled>Langganan Kombo</button>
                            @else
                                <button class="btn btn-success text-white" id="combo_3_months" onclick="buyComboSubscription('{{ $comboPrice3Months }}', '3_months')">Langganan Kombo</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($subscriptionPrices->price_6_months)
                @php
                    $comboPrice6Months = $systemPrices->where('duration', '6_months')->first()->price + $subscriptionPrices->price_6_months;
                @endphp
                <div class="{{ $colSizeCombo }} mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">6 Bulan</h5>
                            <p class="card-text">Rp. {{ number_format($comboPrice6Months, 0, ',', '.') }}</p>
                            @if($existingDuration >= 6)
                                <button class="btn btn-success text-white" id="combo_6_months" disabled>Langganan Kombo</button>
                            @else
                                <button class="btn btn-success text-white" id="combo_6_months" onclick="buyComboSubscription('{{ $comboPrice6Months }}', '6_months')">Langganan Kombo</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($subscriptionPrices->price_1_year)
                @php
                    $comboPrice1Year = $systemPrices->where('duration', '1_year')->first()->price + $subscriptionPrices->price_1_year;
                @endphp
                <div class="{{ $colSizeCombo }} mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">1 Tahun</h5>
                            <p class="card-text">Rp. {{ number_format($comboPrice1Year, 0, ',', '.') }}</p>
                            @if($existingDuration >= 12)
                                <button class="btn btn-success text-white" id="combo_1_year" disabled>Langganan Kombo</button>
                            @else
                                <button class="btn btn-success text-white" id="combo_1_year" onclick="buyComboSubscription('{{ $comboPrice1Year }}', '1_year')">Langganan Kombo</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
    function buySubscription(price, package) {
        fetch('{{ route('subscription.subscribe', ['username' => $user->username]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ subscription_price_id: price, package: package })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        alert('Pembayaran berhasil!');
                        checkTransactionStatus(result.order_id);
                    },
                    onPending: function(result) {
                        alert('Menunggu pembayaran...');
                        checkTransactionStatus(result.order_id);
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal!');
                    },
                    onClose: function() {
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                    }
                });
            } else {
                alert('Terjadi kesalahan, coba lagi!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan dalam permintaan pembayaran.');
        });
    }

    function buyComboSubscription(price, duration) {
        fetch('{{ route('subscription.subscribeCombo', ['username' => $user->username]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ combo_price: price, duration: duration })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        alert('Pembayaran berhasil!');
                        checkTransactionStatusCombo(result.order_id);
                    },
                    onPending: function(result) {
                        alert('Menunggu pembayaran...');
                        checkTransactionStatusCombo(result.order_id);
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal!');
                    },
                    onClose: function() {
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                    }
                });
            } else {
                alert('Terjadi kesalahan, coba lagi!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan dalam permintaan pembayaran.');
        });
    }

    function checkTransactionStatus(orderId) {
        fetch('{{ route('transaction.checkStatusUser') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.status === 'success') {
                alert('Status transaksi berhasil diperbarui.');
                window.location.href = data.redirect_url; // Arahkan ke halaman profil akun yang dilanggan
            } else {
                alert('Gagal memperbarui status transaksi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan dalam memeriksa status transaksi.');
        });
    }

    function checkTransactionStatusCombo(orderId) {
        fetch('{{ route('transaction.checkStatusCombo') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.status === 'success') {
                alert('Status transaksi berhasil diperbarui.');
                window.location.href = data.redirect_url; // Arahkan ke halaman profil akun yang dilanggan
            } else {
                alert('Gagal memperbarui status transaksi.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan dalam memeriksa status transaksi.');
        });
    }
</script>
@endsection