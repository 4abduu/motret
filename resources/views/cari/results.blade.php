@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
    <div class="container">
        <h1 class="my-4">Search Results for "{{ $query }}"</h1>
        @if($users->isEmpty() && $photos->isEmpty())
            <p>No results found.</p>
        @else
            @if(!$users->isEmpty())
                <h2>Users</h2>
                <div class="list-group mb-4">
                    @foreach($users as $user)
                        <a href="{{ route('user.showProfile', $user->username) }}" class="list-group-item list-group-item-action">
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="mb-1">{{ '@' . $user->username }}</p>
                        </a>
                    @endforeach
                </div>
            @endif

            @if(!$users->isEmpty() && !$photos->isEmpty())
                <hr class="my-4">
            @endif

            @if(!$photos->isEmpty())
                <h2>Photos</h2>
                <div class="row">
                    @foreach($photos as $photo)
                        <div class="col-md-4">
                            <div class="card mb-4 shadow-sm">
                                <a href="{{ route('photos.show', $photo->id) }}">
                                    <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="{{ $photo->title }}">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $photo->title }}</h5>
                                    <p class="card-text">{{ $photo->description }}</p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            Hashtags: {{ implode(', ', json_decode($photo->hashtags)) }}
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
@endsection