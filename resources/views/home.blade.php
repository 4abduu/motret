@extends('layouts.app')

@section('title', 'Homepage')

{{-- @push('link')
<script type="text/javascript">
    (function() {
        var css = document.createElement('link');
        css.href = 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
        css.rel = 'stylesheet';
        css.type = 'text/css';
        document.getElementsByTagName('head')[0].appendChild(css);
    })();
</script>
<link rel="stylesheet" href="{{ asset('user/assets/css/app.css') }}">
<link rel="stylesheet" href="{{ asset('user/assets/css/theme.css') }}">
@endpush --}}

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/home.css') }}">
@endpush


@section('content')
@if($photos->count() > 0)
<div class="container mb-4">
    <div class="most-searched-container">
        <h4 class="most-searched-title mb-2">Kata kunci yang sering dicari: </h4>
        <div class="most-searched-keywords">
            @foreach($mostSearchedKeywords as $search)
                <a href="{{ route('search', ['query' => $search->keyword]) }}" class="keyword-item">
                    {{ $search->keyword }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<div class="container-fluid mb-4">
    <div class="d-flex justify-content-between align-items-baseline mb-3"> <!-- Ubah align-items-center ke align-items-baseline -->
        <h4 class="mb-0">Foto yang paling banyak dilihat</h4> <!-- Hapus mb-3 dan ganti dengan mb-0 -->
        <a href="{{ route('photos.more', ['type' => 'most_viewed']) }}" class="section-link d-flex align-items-center">
            Lihat lebih banyak <i class="bi bi-arrow-right ms-2"></i> <!-- Tambahkan ms-2 untuk spacing -->
        </a>
    </div>
    <div class="horizontal-scroll-container">
        <div class="horizontal-scroll-wrapper">
            @foreach($mostViewedPhotos->take(5) as $photo)
            <div class="scroll-card">
                <a href="{{ route('photos.show', $photo->id) }}">
                    @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                        <img src="{{ asset('storage/' . $photo->path) }}" class="scroll-img" loading="lazy" alt="{{ $photo->title }}">
                    @else
                        <canvas class="scroll-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                    @endif
                    <div class="overlay"></div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="container-fluid mb-5"> <!-- Ubah mb-4 ke mb-5 untuk jarak antar section -->
    <div class="d-flex justify-content-between align-items-baseline mb-3"> <!-- align-items-baseline -->
        <h4 class="mb-0">Foto yang paling banyak disukai</h4> <!-- mb-0 -->
        <a href="{{ route('photos.more', ['type' => 'most_liked']) }}" class="section-link d-flex align-items-center">
            Lihat lebih banyak <i class="bi bi-arrow-right ms-2"></i> <!-- ms-2 -->
        </a>
    </div>
    <div class="horizontal-scroll-container">
        <div class="horizontal-scroll-wrapper">
            @foreach($mostLikedPhotos->take(5) as $photo)
            <div class="scroll-card">
                <a href="{{ route('photos.show', $photo->id) }}">
                    @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                        <img src="{{ asset('storage/' . $photo->path) }}" class="scroll-img" loading="lazy" alt="{{ $photo->title }}">
                    @else
                        <canvas class="scroll-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                    @endif
                    <div class="overlay"></div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="container-fluid mb-5"> <!-- Konsisten mb-5 -->
    <div class="d-flex justify-content-between align-items-baseline mb-3"> <!-- align-items-baseline -->
        <h4 class="mb-0">Foto yang paling banyak diunduh</h4> <!-- mb-0 -->
        <a href="{{ route('photos.more', ['type' => 'most_downloaded']) }}" class="section-link d-flex align-items-center">
            Lihat lebih banyak <i class="bi bi-arrow-right ms-2"></i> <!-- ms-2 -->
        </a>
    </div>
    <div class="horizontal-scroll-container">
        <div class="horizontal-scroll-wrapper">
            @foreach($mostDownloadedPhotos ->take(5) as $photo)
            <div class="scroll-card">
                <a href="{{ route('photos.show', $photo->id) }}">
                    @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                        <img src="{{ asset('storage/' . $photo->path) }}" class="scroll-img" loading="lazy" alt="{{ $photo->title }}">
                    @else
                        <canvas class="scroll-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                    @endif
                    <div class="overlay">
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <h4 class="mb-3">Semua foto</h4>
        <div class="card-columns">
            @foreach($photos as $photo)
            <div class="card card-pin">
                <a href="{{ route('photos.show', $photo->id) }}">
                    @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" loading="lazy" alt="{{ $photo->title }}">
                    @else
                        <canvas class="card-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                    @endif
                    <div class="overlay">
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

@else
<div class="container-fluid d-flex align-items-center justify-content-center home-empty-photos">
    <div class="text-center px-4 home-empty-content">
        <!-- Modern illustration-style icon -->
        <div class="mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
        </div>
        
        <!-- Title with subtle gradient text -->
        <h3 class="mb-3 fw-semibold home-empty-title">
            Belum Ada Foto Tersedia
        </h3>
        
        <!-- Description text -->
        @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
        <p class="text-muted mb-4 home-empty-description">
            Silakan kembali nanti atau unggah foto pertama Anda untuk memulai koleksi!
        </p>
        @else
        <p class="text-muted mb-4 home-empty-description">
            Silakan kembali nanti untuk melihat foto-foto menarik yang akan datang!
        </p>
        @endif
        
        <!-- CTA Button with animation -->
            @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                <a href="{{ route('photos.create') }}" class="btn btn-success px-4 py-3 rounded-pill shadow-sm home-upload-btn">
                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> Unggah Foto Pertama Sekarang!
                </a>
            @endif
    </div>
</div>
@endif

@endsection


@push('scripts')
<script src="{{ asset('user/assets/js/app.js') }}"></script>
<script src="{{ asset('user/assets/js/theme.js') }}"></script>
<script src="{{ asset('js/pages/home.js') }}"></script>

@if(session('login_success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah alert sudah ditampilkan sebelumnya
        if (!localStorage.getItem('loginAlertShown')) {
            Swal.fire({
                icon: 'success',
                title: '{{ session('login_success') }}',
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                background: '#32bd40',
                color: '#fff',
                iconColor: '#fff',
                didOpen: (toast) => {
                    toast.addEventListener('click', () => {
                        Swal.close();
                    })
                }
            });
            // Set flag di localStorage
            localStorage.setItem('loginAlertShown', 'true');
            
            // Hapus flag saat user navigasi ke halaman lain
            window.addEventListener('beforeunload', function() {
                localStorage.removeItem('loginAlertShown');
            });
        }
    });
</script>
@endif

@if(session('logout_success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah alert sudah ditampilkan sebelumnya
        if (!localStorage.getItem('logoutAlertShown')) {
            Swal.fire({
                icon: 'success',
                title: '{{ session('logout_success') }}',
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                background: '#32bd40',
                color: '#fff',
                iconColor: '#fff',
                didOpen: (toast) => {
                    toast.addEventListener('click', () => {
                        Swal.close();
                    })
                }
            });
            // Set flag di localStorage
            localStorage.setItem('logoutAlertShown', 'true');
            
            // Hapus flag saat user navigasi ke halaman lain
            window.addEventListener('beforeunload', function() {
                localStorage.removeItem('logoutAlertShown');
            });
        }
    });
</script>
@endif
@if(session('subscription_message'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Subscription Activated!',
            text: '{{ session('subscription_message') }}',
            confirmButtonColor: '#32bd40',
            timer: 5000,
            timerProgressBar: true,
        });
    });
</script>
@endif
@endpush