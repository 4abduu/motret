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
    
    .most-searched-container {
        display: flex;
        flex-direction: column; /* Mengatur tata letak vertikal */
        gap: 10px; /* Jarak antara judul dan daftar kata kunci */
        margin-bottom: 2rem;
    }


.most-searched-title {
        font-size: 1.25rem;
        margin: 0; /* Menghapus margin default */
        font-weight: bold; /* Tebalkan judul */
        text-align: center; /* Judul di sebelah kiri */
    }

.most-searched-keywords {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
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
  .most-searched-container {
    flex-direction: column;
    gap: 10px;
  }

  .most-searched-title {
    font-size: 1rem;
  }

  .keyword-item {
    font-size: 12px;
    padding: 4px 8px;
  }

  .card-title {
    font-size: 1rem;
  }
}
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

<div class="container-fluid">
    <div class="row">
        <div class="card-columns">
            @foreach($photos as $photo)
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

@endsection


@push('scripts')
<script src="{{ asset('user/assets/js/app.js') }}"></script>
<script src="{{ asset('user/assets/js/theme.js') }}"></script>

<script>
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
                };
            });
        @endif

        // // Blokir klik kanan
        // document.addEventListener('contextmenu', function (e) {
        //     e.preventDefault();
        // });

        // // Blokir inspect element
        // document.addEventListener('keydown', function (e) {
        //     if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
        //         e.preventDefault();
        //     }
        // });
    });
</script>
@endpush