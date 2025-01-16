@extends('layouts.app')

@section('title', 'Homepage')

@section('content')
    <div class="container">
        <div class="text-center">
            @if(Auth::check())
                <h1>Halo, Selamat Datang di Motret, {{ Auth::user()->username }}</h1>
                <a href="{{ route('photos.create') }}" class="btn btn-primary mt-3">Upload Foto</a>
            @else
                <h1>Halo, Selamat Datang di Motret</h1>
            @endif
            <div class="mt-4">
                @if(Auth::check())
                    <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('login') }}" onclick="openRegisterModal(event)" class="btn btn-secondary">Register</a>
                @endif
            </div>
        </div>

        <div class="row mt-5">
        @foreach($photos as $photo)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        @if($photo->banned)
                            <div class="card-body">
                                <p class="card-text">Postingan ini telah dibanned.</p>
                            </div>
                        @else
                            <a href="{{ route('photos.show', $photo->id) }}">
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="{{ $photo->title }}">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $photo->title }}</h5>
                                <p class="card-text">{{ $photo->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openRegisterModal(event) {
        event.preventDefault();
        window.location.href = "{{ route('login') }}?register=true";
    }
</script>
@endpush