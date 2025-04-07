@extends('layouts.app')

@section('title', 'Preview User Profile')

@push('link')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* Gunakan CSS yang sama seperti di profile user */
    :root {
        --primary-color: #32bd40;
        --primary-hover: #2aa336;
        --secondary-color: #f5f5f5;
        --text-color: #333;
        --text-muted: #6c757d;
        --border-color: #eee;
        --shadow-sm: 0 2px 10px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 15px rgba(0,0,0,0.1);
        --shadow-lg: 0 4px 15px rgba(0,0,0,0.2);
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        overflow: hidden;
        border-radius: 8px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }

    .photo-card img {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .album-card-container {
        position: relative;
        width: 100%;
        padding-top: 100%;
        overflow: hidden;
        border-radius: 8px 8px 0 0;
        background-color: var(--secondary-color);
    }

    .album-cover-grid {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 2px;
    }

    .album-cover-main {
        grid-column: 1 / 3;
        grid-row: 1 / 2;
        overflow: hidden;
    }

    .album-cover-secondary {
        overflow: hidden;
    }

    .album-cover-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s ease;
    }

    .album-cover-link:hover .album-cover-img {
        transform: scale(1.03);
    }

    .album-cover-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ddd;
        font-size: 24px;
    }

    .album-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 4px;
    }

    .album-desc {
        font-size: 12px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 8px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-start mb-3">
    <button class="btn btn-link text-decoration-none" onclick="history.back()">
        <i class="bi bi-arrow-left" style="font-size: 2rem; color: #32bd40;"></i>
    </button>
</div>

<div class="d-flex justify-content-center">
    <div class="col-md-4 grid-margin grid-margin-md-0 stretch-card">
        <div class="card shadow-lg">
            <div class="card-body text-center">
                <div>
                    <img src="{{ $user->profile_photo_url }}" class="img-lg rounded-circle mb-2" alt="profile image" />
                    <h4>{{ $user->name }} 
                        @if($user->verified)
                        <i class="bi bi-patch-check-fill" style="color: gold;"></i>
                        @endif
                        @if ($user->role === 'pro')
                        <i class="bi bi-star-fill" style="color: gold;"></i>
                        @endif
                    </h4>
                    <p class="text-muted mb-0">{{ $user->username }}</p>
                </div>
                <div class="border-top pt-3">
                    <div class="row">
                        <div class="col-6">
                            <h6>{{ $user->followers()->count() }}</h6>
                            <p class="text-muted">Followers</p>
                        </div>
                        <div class="col-6">
                            <h6>{{ $user->following()->count() }}</h6>
                            <p class="text-muted">Following</p>
                        </div>
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <ul class="nav nav-tabs nav-justified mt-3" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="photos-tab" data-bs-toggle="tab" href="#photos" role="tab" aria-controls="photos" aria-selected="true">Foto</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="albums-tab" data-bs-toggle="tab" href="#albums" role="tab" aria-controls="albums" aria-selected="false">Album</a>
        </li>
        @if($user->verified && $hasSubscriptionPrice)
            <li class="nav-item">
                <a class="nav-link" id="subscription-tab" data-bs-toggle="tab" href="#subscription" role="tab" aria-controls="subscription" aria-selected="false">Langganan</a>
            </li>
        @endif
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Tab Foto -->
        <div class="tab-pane fade show active" id="photos" role="tabpanel" aria-labelledby="photos-tab">
            <h3 class="mt-5 mb-4">Foto yang Diunggah</h3>
            <div class="row">
                @foreach($photos as $photo)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100 photo-card">
                            <a href="{{ asset('storage/' . $photo->path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="{{ $photo->title }}">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $photo->title }}</h5>
                                <p class="card-text">{{ $photo->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tab Album -->
        <div class="tab-pane fade" id="albums" role="tabpanel" aria-labelledby="albums-tab">
            <h3 class="mt-5 mb-3">Album</h3>
            <div class="row">
                @foreach($albums as $album)
                <div class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="card shadow-sm h-100 position-relative">
                        <a href="{{ route('admin.albums.preview', $album->id) }}">
                            <div class="album-card-container">
                                @if($album->photos->count() > 0)
                                    <div class="album-cover-grid">
                                        @foreach($album->photos->take(3) as $photo)
                                            <div class="{{ $loop->first ? 'album-cover-main' : 'album-cover-secondary' }}">
                                                <img src="{{ asset('storage/' . $photo->path) }}" class="album-cover-img" alt="{{ $album->name }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="album-cover-placeholder">
                                        <i class="bi bi-images text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="card-body">
                            <h5 class="album-title">{{ $album->name }}</h5>
                            <p class="album-desc">{{ $album->description }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($albums->isEmpty())
                <div class="text-center mt-5">
                    <i class="bi bi-folder-x" style="font-size: 3rem; color: #ddd;"></i>
                    <h5 class="mt-3 text-muted">Belum ada album</h5>
                </div>
            @endif
        </div>

        <!-- Tab Langganan -->
        @if($user->verified && $hasSubscriptionPrice)
            <div class="tab-pane fade" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
                <h3 class="mt-5 mb-4">Langganan</h3>
                <div class="row">
                    @foreach($premiumPhotos as $photo)
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <a href="{{ asset('storage/' . $photo->path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="{{ $photo->title }}">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $photo->title }}</h5>
                                    <p class="card-text">{{ $photo->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        {{-- @if($albums->isEmpty())
                <div class="text-center mt-5">
                    <i class="bi bi-folder-x" style="font-size: 3rem; color: #ddd;"></i>
                    <h5 class="mt-3 text-muted">Belum ada album</h5>
                </div>
            @endif --}}
    </div>
</div>
@endsection