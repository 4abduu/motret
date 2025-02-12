@extends('layouts.app')

@section('title', 'Homepage')

@push('link')
<style>
    .scrolling-wrapper {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 10px;
        white-space: nowrap;
    }
    .card-pin {
        flex: 0 0 auto;
        width: 250px; /* Atur ukuran kartu agar konsisten */
        height: 250px; /* Atur tinggi kartu agar konsisten */
        position: relative;
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.5); /* Overlay transparan */
        z-index: 1;
    }
</style>
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <link rel="stylesheet" href="{{asset ('user/assets/css/app.css')}}">
    <link rel="stylesheet" href="{{asset ('user/assets/css/theme.css')}}">
@endpush

@section('content')
        <div class="container mb-4">
            <div class="row justify-content-center">
                <nav class="navbar navbar-expand-lg navbar-light bg-white pl-2 pr-2">
                    <button class="navbar-light navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExplore" aria-controls="navbarsDefault" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarsExplore">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="#">Semua Kategori</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Fashion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Seni</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Wisata</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Hewan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Tumbuhan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Makanan</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <div class="container mb-4">
            <h2>Most Searched Keywords</h2>
            <ul>
                @foreach($mostSearchedKeywords as $search)
                    <li><a href="{{ route('search', ['query' => $search->keyword]) }}">{{ $search->keyword }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="container-fluid mb-4">
            <h2>Most Viewed Photos</h2>
            <div class="scrolling-wrapper">
                @foreach($mostViewedPhotos as $photo)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" alt="{{ $photo->title }}">
                            @else
                                <canvas class="card-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                            @endif
                            <div class="overlay">
                                <div class="more">
                                    <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> More
                                </div>
                            </div>                                        
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="container-fluid mb-4">
            <h2>Most Liked Photos</h2>
            <div class="scrolling-wrapper">
                @foreach($mostLikedPhotos as $photo)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" alt="{{ $photo->title }}">
                            @else
                                <canvas class="card-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                            @endif
                            <div class="overlay">
                                <div class="more">
                                    <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> More
                                </div>
                            </div>                                        
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="container-fluid mb-4">
            <h2>Most Downloaded Photos</h2>
            <div class="scrolling-wrapper">
                @foreach($mostDownloadedPhotos as $photo)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" alt="{{ $photo->title }}">
                            @else
                                <canvas class="card-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                            @endif
                            <div class="overlay">
                                <div class="more">
                                    <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> More
                                </div>
                            </div>                                        
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="card-columns">
                    @foreach($photos as $photo)
                        @if($photo->banned && $photo->user_id !== Auth::id())
                            @continue
                        @endif
                        <div class="card card-pin">
                            <a href="{{ route('photos.show', $photo->id) }}">
                                @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                                    <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" alt="{{ $photo->title }}">
                                @else
                                    <canvas class="card-img" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
                                @endif
                                <div class="overlay">
                                    <h2 class="card-title title">{{ $photo->title }}</h2>
                                    <div class="more">
                                            <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> More
                                    </div>
                                </div>                                        
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

@endsection

@push('scripts')
<script src="{{asset ('user/assets/js/app.js')}}"></script>
<script src="{{asset ('user/assets/js/theme.js')}}"></script>

<script>
    function openRegisterModal(event) {
        event.preventDefault();
        window.location.href = "{{ route('login') }}?register=true";
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(!Auth::check() || (Auth::check() && (Auth::user()->role !== 'user' && Auth::user()->role !== 'pro')))
            document.querySelectorAll('canvas.card-img').forEach(function (canvas) {
                var imgSrc = canvas.getAttribute('data-src');
                var img = new Image();
                img.src = imgSrc;
                img.onload = function () {
                    var ctx = canvas.getContext('2d');
                    var width = canvas.width;
                    var height = canvas.height;
                    var aspectRatio = img.width / img.height;

                    if (width / height > aspectRatio) {
                        width = height * aspectRatio;
                    } else {
                        height = width / aspectRatio;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    // // Tambahkan watermark berulang secara diagonal
                    // ctx.font = '10px Arial'; // Ukuran font lebih kecil
                    // ctx.fillStyle = 'rgba(255, 255, 255, 0.3)'; // Warna lebih transparan
                    // var text = 'MOTRET ';
                    // var stepX = 50; // Jarak antar teks secara horizontal
                    // var stepY = 25;  // Jarak antar teks secara vertikal

                    // for (var y = -canvas.height; y < canvas.height * 2; y += stepY) {
                    //     for (var x = -canvas.width; x < canvas.width * 2; x += stepX) {
                    //         ctx.save();
                    //         ctx.translate(x, y);
                    //         ctx.rotate(-Math.PI / 6); // Rotasi teks miring
                    //         ctx.fillText(text, 0, 0);
                    //         ctx.restore();
                    //     }
                    // }
                };
            });
        @endif

        // Blokir klik kanan
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        // Blokir inspect element
        document.addEventListener('keydown', function (e) {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush