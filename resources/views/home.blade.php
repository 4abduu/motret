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
        column-count: 4;
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
    overflow-x: scroll;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 10px;
    -ms-overflow-style: none;
    scrollbar-width: thin;
}

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
    gap: 15px;
    padding: 5px 0;
    scroll-snap-type: x mandatory;
    scroll-padding: 10px;
}

/* Scroll Card Styling */
.scroll-card {
    flex: 0 0 auto;
    width: 300px;
    height: 230px;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    scroll-snap-align: start;
}

/* Mobile Adjustments */
@media (max-width: 576px) {
    .scroll-card {
        width: 200px;
        height: 160px;
    }
    .horizontal-scroll-container::-webkit-scrollbar {
        height: 6px;
    }
    .horizontal-scroll-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
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
</style>

@section('content')


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
    <h6 class="mb-3">Foto yang paling banyak dilihat</h6>
    <div class="horizontal-scroll-container">
        <div class="horizontal-scroll-wrapper">
            @foreach($mostViewedPhotos as $photo)
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
<div class="container-fluid mb-4">
    <h6 class="mb-3">Foto yang paling banyak disukai</h6>
    <div class="horizontal-scroll-container">
        <div class="horizontal-scroll-wrapper">
            @foreach($mostLikedPhotos as $photo)
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

<div class="container-fluid mb-4">
    <h6 class="mb-3">Foto yang paling banyak diunduh</h6>
    <div class="horizontal-scroll-container">
        <div class="horizontal-scroll-wrapper">
            @foreach($mostDownloadedPhotos as $photo)
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
        Swal.fire({
            icon: 'success',
            title: '{{ session('login_success') }}',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
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
    });
</script>
@endif


@if(session('logout_success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '{{ session('logout_success') }}',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
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
    });
</script>
@endif
@endpush