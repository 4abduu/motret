@extends('layouts.app')

@section('title', 'Homepage')

@push('link')
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <link rel="stylesheet" href="{{asset ('user/assets/css/app.css')}}">
    <link rel="stylesheet" href="{{asset ('user/assets/css/theme.css')}}">
@endpush

@section('content')
<main role="main">
    <section class="mt-4 mb-5">
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