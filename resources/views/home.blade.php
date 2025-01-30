@extends('layouts.app')

@section('title', 'Homepage')

@push('link')
<style>
    .scrolling-wrapper {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 10px;
}
.card-pin {
    flex: 0 0 auto;
    width: 250px; /* Atur ukuran kartu agar konsisten */
}

</style>
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <link rel="stylesheet" href="{{asset ('user/assets/css/app.css')}}">
    <link rel="stylesheet" href="{{asset ('user/assets/css/theme.css')}}">
@endpush

@section('content')
<main role="main">
    <section class="mt-4 mb-5">
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
                            <img class="card-img" src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}">
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

        <div class="container-fluid mb-4">
            <h2>Most Liked Photos</h2>
            <div class="scrolling-wrapper">
                @foreach($mostLikedPhotos as $photo)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            <img class="card-img" src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}">
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

        <div class="container-fluid mb-4">
            <h2>Most Downloaded Photos</h2>
            <div class="scrolling-wrapper">
                @foreach($mostDownloadedPhotos as $photo)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            <img class="card-img" src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}">
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

        <div class="container-fluid">
            <div class="row">
                <div class="card-columns">
                    @foreach($photos as $photo)
                        @if($photo->banned && $photo->user_id !== Auth::id())
                            @continue
                        @endif
                        <div class="card card-pin">
                            @if($photo->banned && $photo->user_id === Auth::id())
                                <div class="overlay">
                                    <h2 class="card-title title">Postingan Dilarang</h2>
                                    <p class="text-warning">Alasan: 
                                        @foreach($photo->reports as $report)
                                            {{ $report->reason }}
                                        @endforeach
                                    </p>
                                </div>
                            @else
                            <a href="{{ route('photos.show', $photo->id) }}">
                                <img class="card-img" src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}">
                                <div class="overlay">
                                    <h2 class="card-title title">{{ $photo->title }}</h2>
                                    <div class="more">
                                            <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> More
                                    </div>
                                </div>                                        
                            </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</main>

@endsection

@push('scripts')
<script src="{{asset ('user/assets/js/app.js')}}"></script>
<script src="{{asset ('user/assets/js/theme.js')}}"></script>

<script>
    function openRegisterModal(event) {
        event.preventDefault();
        window.location.href = "{{ route('login') }}?register=true";
    }
</script>
@endpush
