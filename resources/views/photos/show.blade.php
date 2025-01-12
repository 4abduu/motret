@extends('layouts.app')

@section('title', $photo->title)

@section('content')
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <span id="success-countdown" class="float-end"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <span id="error-countdown" class="float-end"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
        <h1 class="my-4">{{ $photo->title }}</h1>
        <div class="row">
            <div class="col-md-8">
                <img src="{{ asset('storage/' . $photo->path) }}" class="img-fluid" alt="{{ $photo->title }}">
            </div>
            <div class="col-md-4">
                <h3>Description</h3>
                <p>{{ $photo->description }}</p>
                <h3>Hashtags</h3>
                <p>{{ implode(', ', json_decode($photo->hashtags)) }}</p>
                <h3>Uploaded by</h3>
                <p>{{ $photo->user->username }}</p>
                <h3>Download</h3>
                
                    <form action="{{ route('photos.download', $photo->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </form>
            </div>
        </div>
    </div>
@endsection