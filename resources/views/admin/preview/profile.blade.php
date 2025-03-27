@extends('layouts.app')

@section('title', 'Preview User Profile')

@section('content')

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

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
                        <i class="ti-medall-alt" style="color: gold;"></i> <!-- Tambahkan ini -->
                        @endif
                        @if ($user->role === 'pro')
                        <i class="ti-star" style="color: gold;"></i> <!-- Tambahkan ini --> 
                        @endif
                    </h4>
                    <p class="text-muted mb-0">{{ $user->username }}</p>
                </div>
                <div class="border-top pt-3">
                    <div class="row">
                        <div class="col-6">
                            <h6 id="followers-count" >{{ $user->followers()->count() }}</h6>
                            <p class="btn btn-link text-success" data-bs-toggle="modal" data-bs-target="#followersModal">Followers</p>
                        </div>
                        <div class="col-6">
                            <h6 id="following-count" >{{ $user->following()->count() }}</h6>
                            <p class="btn btn-link text-success" data-bs-toggle="modal" data-bs-target="#followingModal">Following</p>
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
    </ul>
        
    <div class="tab-content" id="myTabContent">
        <!-- Tab Foto -->
        <div class="tab-pane fade show active" id="photos" role="tabpanel" aria-labelledby="photos-tab">
            <h2 class="mt-5">Foto yang Diunggah</h2>
            <div class="row">
                @foreach($photos as $photo)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <a href="{{ route('admin.users.previewPhotos', $photo->id) }}">
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
            <h2 class="mt-5">Album</h2>
            <div class="row">
                @foreach($albums as $album)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <a href="{{ route('admin.users.previewAlbums', $album->id) }}">
                                <div class="album-cover">
                                    @foreach($album->photos->take(3) as $photo)
                                        <img src="{{ asset('storage/' . $photo->path) }}" class="album-cover-photo" alt="{{ $photo->title }}">
                                    @endforeach
                                </div>
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $album->name }}</h5>
                                <p class="card-text">{{ $album->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


<!-- Modal Followers -->
<div class="modal fade" id="followersModal" tabindex="-1" role="dialog" aria-labelledby="followersModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followersModalLabel">Followers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="followers-list">
                    @foreach($user->followers as $follower)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.users.previewProfile', $follower->id) }}"><b>{{ $follower->username }}</b></a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Following -->
<div class="modal fade" id="followingModal" tabindex="-1" role="dialog" aria-labelledby="followingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followingModalLabel">Following</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="following-list">
                    @foreach($user->following as $following)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.users.previewProfile', $following->id) }}"><b>{{ $following->username }}</b></a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection