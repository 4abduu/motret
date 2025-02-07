subscriptionIndex.blade.php

@extends('layouts.app')

@section('content')
</head>
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
            <div class="col-md-4 d-flex">
                <div class="card flex-fill">
                    <h4 class="fw-bold">Unlimited Subscription</h4>
                    <p><i class="bi bi-check-circle"></i> Unggah foto tanpa batas</p>
                    <p><i class="bi bi-check-circle"></i> Download foto tanpa batas</p>
                    <p><i class="bi bi-x-circle" style="color: #ff2929;"></i> Jadi kreator kami</p>
                    <h3 class="fw-bold">Rp. 25.000 <small>/bln</small></h3>
                    <button class="btn btn-green">Beli Paket</button>
                </div>
            </div>
            
            <div class="col-md-4 d-flex">
                <div class="card flex-fill">
                    <h4 class="fw-bold">Verified User</h4>
                    <p><i class="bi bi-check-circle"></i> Jadi kreator kami</p>
                    <p><i class="bi bi-check-circle"></i> Buatlah karya yang menarik</p>
                    <p><i class="bi bi-check-circle"></i> Gabung tanpa biaya</p>
                    <h3 class="fw-bold">Rp. 50.000 <small>/bln</small></h3>
                    <button class="btn btn-green">Beli Paket</button>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection