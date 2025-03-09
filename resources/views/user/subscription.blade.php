<!-- filepath: /c:/xampp/htdocs/motret/resources/views/user/subscription.blade.php -->
@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 50px;
        text-align: center;
        height: 100%;
        margin-bottom: 20px; /* Atur jarak sesuai kebutuhan */
    }
    .card h4 {
        margin-bottom: 50px;
        font-size: 24px;
    }
    .card p {
        margin-bottom: 25px;
        font-size: 16px;
        text-align: left; /* Membuat teks dalam paragraf rata kiri */
    }
    .card h3 {
        margin-top: 40px;
        margin-bottom: 30px;
        font-size: 26px;
    }
    .btn-green {
        background-color: #4CAF50;
        color: white;
        border-radius: 20px;
        padding: 12px 25px;
        font-size: 18px;
    }
    .btn-green:hover {
        background-color: #45a049;
    }
    .equal-height {
        display: flex;
        align-items: stretch;
    }
    .bi-check-circle {
        color: #FFCD29;
    }
    .text-left {
        text-align: left; /* Kelas tambahan jika ingin digunakan */
    }
</style>
</head>
<body>
    
    <div class="container mt-5 pt-10">
        @if($duration && $endDate)
            <p>Anda memiliki paket langganan {{ $duration }}, yang akan berakhir pada {{ $endDate }}.</p>
        @endif
        <div class="row justify-content-center equal-height">
            @php
                $price1Month = $prices->where('duration', '1_month')->first();
                $price3Months = $prices->where('duration', '3_months')->first();
                $price6Months = $prices->where('duration', '6_months')->first();
                $price1Year = $prices->where('duration', '1_year')->first();
            @endphp
            <div class="col-md-4 d-flex mb-4">
                <div class="card flex-fill">
                    <h4 class="fw-bold">Paket 1 Bulan</h4>
                    <p><i class="bi bi-check-circle"></i> Dapatkan lencana khusus</p>
                    <p><i class="bi bi-check-circle"></i> Download foto tanpa batas</p>
                    <p><i class="bi bi-x-circle" style="color: #ff2929;"></i> Jadi kreator kami</p>
                    <h3 class="fw-bold">Rp. {{ number_format($price1Month->price, 0, ',', '.') }} <small>/bulan</small></h3>
                    @if($existingDuration >= 1)
                        <button class="btn btn-green" disabled>Beli Paket</button>
                    @else
                        <button class="btn btn-green" onclick="buySubscription({{ $price1Month->id }})">Beli Paket</button>
                    @endif
                </div>
            </div>

            <div class="col-md-4 d-flex mb-4">
                <div class="card flex-fill">
                    <h4 class="fw-bold">Paket 3 Bulan</h4>
                    <p><i class="bi bi-check-circle"></i> Dapatkan lencana khusus</p>
                    <p><i class="bi bi-check-circle"></i> Download foto tanpa batas</p>
                    <p><i class="bi bi-x-circle" style="color: #ff2929;"></i> Jadi kreator kami</p>
                    <h3 class="fw-bold">Rp. {{ number_format($price3Months->price, 0, ',', '.') }} <small>/3 bulan</small></h3>
                    @if($existingDuration >= 3)
                        <button class="btn btn-green" disabled>Beli Paket</button>
                    @else
                        <button class="btn btn-green" onclick="buySubscription({{ $price3Months->id }})">Beli Paket</button>
                    @endif
                </div>
            </div>

            <div class="col-md-4 d-flex mb-4">
                <div class="card flex-fill">
                    <h4 class="fw-bold">Paket 6 Bulan</h4>
                    <p><i class="bi bi-check-circle"></i> Dapatkan lencana khusus</p>
                    <p><i class="bi bi-check-circle"></i> Download foto tanpa batas</p>
                    <p><i class="bi bi-x-circle" style="color: #ff2929;"></i> Jadi kreator kami</p>
                    <h3 class="fw-bold">Rp. {{ number_format($price6Months->price, 0, ',', '.') }} <small>/6 bulan</small></h3>
                    @if($existingDuration >= 6)
                        <button class="btn btn-green" disabled>Beli Paket</button>
                    @else
                        <button class="btn btn-green" onclick="buySubscription({{ $price6Months->id }})">Beli Paket</button>
                    @endif
                </div>
            </div>

            <div class="col-md-4 d-flex mb-4">
                <div class="card flex-fill">
                    <h4 class="fw-bold">Paket 1 Tahun</h4>
                    <p><i class="bi bi-check-circle"></i> Dapatkan lencana khusus</p>
                    <p><i class="bi bi-check-circle"></i> Download foto tanpa batas</p>
                    <p><i class="bi bi-x-circle" style="color: #ff2929;"></i> Jadi kreator kami</p>
                    <h3 class="fw-bold">Rp. {{ number_format($price1Year->price, 0, ',', '.') }} <small>/tahun</small></h3>
                    @if($existingDuration >= 12)
                        <button class="btn btn-green" disabled>Beli Paket</button>
                    @else
                        <button class="btn btn-green" onclick="buySubscription({{ $price1Year->id }})">Beli Paket</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        function buySubscription(subscriptionPriceId) {
            fetch('{{ route('transaction.create') }}', {
                  method: 'POST',
                  headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
                  body: JSON.stringify({ subscription_price_id: subscriptionPriceId })
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
            fetch('{{ route('transaction.checkStatus') }}', {
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
</body>
@endsection