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


@section('content')

<style>  
    /* ============================== */
    /* Most Searched Section Styling */
    /* ============================== */
    .most-searched-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 2rem;
    }

    .most-searched-title {
        font-size: 1.25rem;
        font-weight: bold;
        text-align: center;
        margin: 0;
    }

    .most-searched-keywords {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .keyword-item {
        background-color: #f0f0f0;
        padding: 6px 12px;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-block;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .keyword-item:hover {
        background-color: #32bd40;
        color: #fff;
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* ====================== */
    /* Responsive Adjustments */
    /* ====================== */
    @media (max-width: 768px) {
        .most-searched-title {
            font-size: 1rem;
        }

        .keyword-item {
            font-size: 12px;
            padding: 4px 8px;
        }
    }

    @media (max-width: 480px) {
        .most-searched-title {
            font-size: 0.9rem;
        }
        .keyword-item {
            font-size: 11px;
            padding: 3px 6px;
        }
    }

    /* ==================== */
    /* Masonry Card Layout */
    /* ==================== */
    .card-columns {
        column-count: 2;
        column-gap: 1rem;
    }

    @media (min-width: 768px) {
        .card-columns {
            column-count: 3;
        }
    }

    @media (min-width: 1024px) {
        .card-columns {
            column-count: 5;
        }
    }

    .card {
        display: inline-block;
        width: 100%;
        margin-bottom: 1rem;
    }

    /* Overlay Effect */
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .card-pin:hover .overlay {
        opacity: 1;
    }

    /* =========================== */
    /* Horizontal Scroll Section */
    /* =========================== */
    .horizontal-scroll-container {
        width: 100%;
        overflow-x: auto; /* Changed from scroll to hide scrollbar */
        padding-bottom: 10px;
        position: relative; /* Added for positioning the "Lihat Lebih Banyak" */
    }

    /* Hide scrollbar completely */
    .horizontal-scroll-container::-webkit-scrollbar {
        height: 8px;
        background: transparent;
    }

    .horizontal-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 10px;
    }

    .horizontal-scroll-container:hover::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.6);
    }

    .horizontal-scroll-wrapper {
        display: flex;
        gap: 20px;
        padding: 5px 0;
        scroll-snap-type: x mandatory;
        scroll-padding: 10px;
    }

    /* Scroll Card Styling */
    .scroll-card {
            flex: 0 0 auto;
            width: 300px;
            height: 180px; /* Tinggi dipendekkan dari 230px menjadi 180px */
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

    /* Mobile Adjustments */
    @media (max-width: 576px) {
            .scroll-card {
                width: 150px; /* Lebar foto lebih kecil */
                height: 100px; /* Tinggi foto lebih kecil */
            }
            .horizontal-scroll-wrapper {
                gap: 10px; /* Kurangi jarak antar foto */
            }
        }

    .scroll-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .scroll-card:hover .overlay {
        opacity: 1;
    }

    /*lihat lebih banyak*/
    .section-link {
            font-size: 1rem;
            color: #1b1c1beb;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
            line-height: 1.2;
        }

        .section-link:hover {
            color: #1b1c1bac;
            text-decoration: underline;
        }
</style>

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
        <a href="{{ route('photos.more') }}" class="section-link d-flex align-items-center"> <!-- Tambahkan d-flex align-items-center -->
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
        <a href="{{ route('photos.more') }}" class="section-link d-flex align-items-center">
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
        <a href="{{ route('photos.more') }}" class="section-link d-flex align-items-center">
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

@endsection


@push('scripts')
<script src="{{ asset('user/assets/js/app.js') }}"></script>
<script src="{{ asset('user/assets/js/theme.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const lazyCanvases = document.querySelectorAll("canvas.card-img, canvas.scroll-img");
    
    const observer = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const canvas = entry.target;
                    const imgSrc = canvas.getAttribute("data-src");
                    if (imgSrc) {
                        const img = new Image();
                        img.src = imgSrc;
                        img.onload = function () {
                            const ctx = canvas.getContext("2d");
                            let width = canvas.clientWidth;
                            let height = canvas.clientHeight;
                            const aspectRatio = img.width / img.height;

                            if (width / height > aspectRatio) {
                                width = height * aspectRatio;
                            } else {
                                height = width / aspectRatio;
                            }

                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(img, 0, 0, width, height);
                        };
                    }
                    observer.unobserve(canvas);
                }
            });
        },
        { rootMargin: "100px" }
    );

    document.querySelectorAll("canvas.card-img, canvas.scroll-img").forEach((canvas) => {
        observer.observe(canvas);
    });

    // Fallback untuk browser yang tidak support IntersectionObserver
    if (!("IntersectionObserver" in window)) {
        document.querySelectorAll("canvas.card-img, canvas.scroll-img").forEach((canvas) => {
            const imgSrc = canvas.getAttribute("data-src");
            if (imgSrc) {
                const img = new Image();
                img.src = imgSrc;
                img.onload = function () {
                    const ctx = canvas.getContext("2d");
                    let width = canvas.clientWidth;
                    let height = canvas.clientHeight;
                    const aspectRatio = img.width / img.height;

                    if (width / height > aspectRatio) {
                        width = height * aspectRatio;
                    } else {
                        height = width / aspectRatio;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                };
            }
        });
    }
    
    // Blokir klik kanan
    document.addEventListener("contextmenu", function (e) {
        e.preventDefault();
    });
    
    // Blokir inspect element
    document.addEventListener("keydown", function (e) {
        if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I")) {
            e.preventDefault();
        }
    });
});

</script>

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
@endpush