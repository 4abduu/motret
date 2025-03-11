@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="text-center">
        <img src="{{ $user->profile_photo_url }}" class="img-lg rounded-circle mb-2" alt="profile image" />
        <h4>{{ $user->name }}</h4>
        <p class="text-muted mb-0">{{ $user->username }}</p>
    </div>
    <h2 class="mt-5 text-center">Pilih Paket Langganan</h2>
    <div class="d-flex justify-content-center">
        <div class="row">
            @if($subscriptionPrices)
                @if($subscriptionPrices->price_1_month)
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">1 Bulan</h5>
                                <p class="card-text">Rp. {{ number_format($subscriptionPrices->price_1_month, 0, ',', '.') }}</p>
                                <button class="btn btn-primary" onclick="buySubscription('{{ $subscriptionPrices->price_1_month }}', '1_month')">Langganan</button>
                            </div>
                        </div>
                    </div>
                @endif
                @if($subscriptionPrices->price_3_months)
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">3 Bulan</h5>
                                <p class="card-text">Rp. {{ number_format($subscriptionPrices->price_3_months, 0, ',', '.') }}</p>
                                <button class="btn btn-primary" onclick="buySubscription('{{ $subscriptionPrices->price_3_months }}', '3_months')">Langganan</button>
                            </div>
                        </div>
                    </div>
                @endif
                @if($subscriptionPrices->price_6_months)
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">6 Bulan</h5>
                                <p class="card-text">Rp. {{ number_format($subscriptionPrices->price_6_months, 0, ',', '.') }}</p>
                                <button class="btn btn-primary" onclick="buySubscription('{{ $subscriptionPrices->price_6_months }}', '6_months')">Langganan</button>
                            </div>
                        </div>
                    </div>
                @endif
                @if($subscriptionPrices->price_1_year)
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">1 Tahun</h5>
                                <p class="card-text">Rp. {{ number_format($subscriptionPrices->price_1_year, 0, ',', '.') }}</p>
                                <button class="btn btn-primary" onclick="buySubscription('{{ $subscriptionPrices->price_1_year }}', '1_year')">Langganan</button>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <p class="text-center">Pengguna ini belum menetapkan harga langganan.</p>
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
                location.reload();
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