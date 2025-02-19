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
        <div class="row justify-content-center equal-height">
            @foreach($prices as $price)
                <div class="col-md-4 d-flex mb-4">
                    <div class="card flex-fill">
                        <h4 class="fw-bold">Unlimited Subscription</h4>
                        <p><i class="bi bi-check-circle"></i> Dapatkan lencana khusus</p>
                        <p><i class="bi bi-check-circle"></i> Download foto tanpa batas</p>
                        <p><i class="bi bi-x-circle" style="color: #ff2929;"></i> Jadi kreator kami</p>
                        <h3 class="fw-bold">Rp. {{ number_format($price->price, 0, ',', '.') }} <small>/{{ str_replace('_', ' ', $price->duration) }}</small></h3>
                        <button class="btn btn-green" onclick="buySubscription({{ $price->id }})">Beli Paket</button>
                    </div>
                </div>
            @endforeach
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