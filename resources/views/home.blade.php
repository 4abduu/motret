@extends('layouts.app')

@section('title', 'Homepage')

@push('link')

    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <link rel="stylesheet" href="{{asset ('user/assets/css/app.css')}}">
    <link rel="stylesheet" href="{{asset ('user/assets/css/theme.css')}}">
@endpush

@section('content')

<div class="container mb-4">
    <div class="most-searched-container">
        <h4 class="most-searched-title">Kata kunci yang sering dicari: </h4>
        <div class="most-searched-keywords">
            @foreach($mostSearchedKeywords as $search)
                <a href="{{ route('search', ['query' => $search->keyword]) }}" class="keyword-item">
                    {{ $search->keyword }}
                </a>
            @endforeach
        </div>
    </div>
</div>

        <div class="container-fluid">
            <h6>Foto yang paling banyak dilihat</h6>
            <div class="row">
                <div class="card-columns">
                    @foreach($mostViewedPhotos as $photo)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" alt="{{ $photo->title }}">
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


        <div class="container-fluid">
            <h6>Foto yang paling banyak disukai</h6>
            <div class="row">
                <div class="card-columns">
                    @foreach($mostLikedPhotos as $photo)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" alt="{{ $photo->title }}">
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

        <div class="container-fluid">
            <h6>Foto yang paling banyak diunduh</h6>
            <div class="row">
                <div class="card-columns">
                    @foreach($mostDownloadedPhotos as $photo)
                        <div class="card card-pin">
                            <a href="{{ route('photos.show', $photo->id) }}">
                                @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                                    <img src="{{ asset('storage/' . $photo->path) }}" class="card-img" alt="{{ $photo->title }}">
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