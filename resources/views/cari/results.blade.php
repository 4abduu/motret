@extends('layouts.app')

@section('title', 'Search Results')

@push('link')
    <style>
            html, body{
                margin: 0; /* Reset margin */
            }
        /* Search Results Page */
        .search-results-container {
            padding: 20px; /* Berikan padding */
        }
    
        .search-results-container h3 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
    
        .search-results-container h2 {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
    
        .list-group-item {
            border: none;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
    
        .list-group-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    
        .list-group-item h5 {
            color: #32bd40; /* Warna hijau untuk nama user */
        }
    
        .list-group-item p {
            color: #666; /* Warna abu-abu untuk username */
        }
    
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            color: #333;
        }

        .card-text {
            color: #666;
        }

        @media (max-width: 768px) {
            .search-results-container {
                margin-top: 60px;
                padding: 10px;
            }

            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mt-3">
        <h4 class="my-4">Search Results for "{{ $keyword }}"</h4>
        @if($users->isEmpty() && $photos->isEmpty())
            <p class="text-muted">No results found.</p>
        @else
            @if(!$users->isEmpty())
                <h3 class="mb-4">Users</h3>
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
                <h3 class="mb-4">Photos</h3>
                <div class="row">
                    @foreach($photos as $photo)
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
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