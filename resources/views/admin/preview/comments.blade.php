@extends('layouts.app')

@section('title', 'Preview Comment/Reply')


@section('content')

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<link rel="stylesheet" href="{{ asset('css/pages/admin-preview-comments.css') }}">

<div class="d-flex justify-content-start mb-3">
    <button class="btn btn-link text-decoration-none" onclick="history.back()">
        <i class="bi bi-arrow-left" style="font-size: 2rem; color: #32bd40;"></i>
    </button>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 position-relative">
            <img src="{{ asset('storage/' . $highlighted->photo->path) }}" class="card-img" alt="{{ asset('storage/' . $highlighted->photo->title) }}">
            <div class="d-flex align-items-center mt-3">
                <form method="POST" action="{{ route('photos.download', $highlighted->photo->id) }}" class="me-3 download-button">
                    @csrf
                    <button type="submit" class="btn btn-link p-0" disabled>
                        <i class="bi bi-download text-dark fw-bold fs-5"></i>
                    </button>
                </form>
                <div id="like-section" class="me-3">
                    <button id="like-button" class="btn btn-link p-0" data-liked="{{ $highlighted->photo->isLikedBy(Auth::user()) ? 'true' : 'false' }}" disabled>
                        <i class="{{ $highlighted->photo->isLikedBy(Auth::user()) ? 'bi bi-heart-fill fs-5' : 'bi bi-heart fs-5' }}" 
                           style="color: {{ $highlighted->photo->isLikedBy(Auth::user()) ? 'red' : 'black' }};"></i>
                    </button>
                    @php
                        $likeCount = $highlighted->photo->likes()->count();
                    @endphp
                    @if ($likeCount > 0)
                        <span id="likes-count">{{ $likeCount }} {{ $likeCount === 1 ? 'like' : 'likes' }}</span>
                    @else
                        <span id="likes-count"></span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <button type="button" class="btn btn-link p-0 me-3" disabled>
                    <i class="bi bi-share text-dark fw-bold fs-5"></i> 
                </button>
                <button type="button" class="btn btn-link p-0 me-3" data-bs-toggle="modal" data-bs-target="#reportModal-{{ $highlighted->photo->id }}" disabled>
                    <i class="bi bi-flag text-dark fw-bold fs-5"></i>
                </button>
            </div>
            <div class="mt-4 text-start comment-container">
                <h3 class="mb-4 text-start">{{ $highlighted->photo->title }}</h3>
                <h5 class="text-start">{{ $highlighted->photo->description }}</h5>
                <div class="most-searched-container">
                    <h4 class="most-searched-title">Hashtags:</h4>
                    <div class="most-searched-keywords">
                        @foreach(json_decode($highlighted->photo->hashtags) as $hashtag)
                            <a href="{{ route('search', ['query' => $hashtag]) }}" class="keyword-item">
                                {{ $hashtag }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <p class="text-start d-flex align-items-center">
                    @if($highlighted->photo->user->profile_photo)
                        <img src="{{ asset('storage/photo_profile/' . $highlighted->photo->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40">
                    @else
                        <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40"/>
                    @endif
                    <a href="{{ route('admin.users.previewProfile', $highlighted->photo->user->id) }}" class="fw-bold">{{ $highlighted->photo->user->username }}</a>
                    @if($highlighted->photo->user->verified)
                        <i class="ti-medall-alt" style="color: gold;"></i>
                    @endif 
                    @if($highlighted->photo->user->role === 'pro')
                        <i class="ti-star" style="color: gold;"></i>
                    @endif
                </p>
                

            {{-- Bagian Komentar dan Replies (Tidak Diubah) --}}
            <h6 class="text-start">Komentar</h6>
            <!-- Tampilkan komentar yang di-highlight di paling atas -->
            @if($type === 'comment')
                <div class="mb-2 highlight">
                    @if($highlighted->user->profile_photo)
                        <img src="{{ asset('storage/photo_profile/' . $highlighted->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30">
                    @else
                        <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30"/>
                    @endif
                    <strong>
                        <a href="{{ route('admin.users.previewProfile', $highlighted->user->id) }}" class="text-dark fw-bold text-decoration-none">
                            {{ $highlighted->user->username }}
                        </a>
                    </strong>
                    @if($highlighted->user->verified)
                        <i class="ti-medall-alt" style="color: gold;"></i>
                    @endif 
                    @if($highlighted->user->role === 'pro')
                        <i class="ti-star" style="color: gold;"></i>
                    @endif
                    @if($highlighted->user_id === $highlighted->photo->user_id)
                        <span class="text">• Pembuat</span>
                    @endif
                    @if($highlighted->banned)
                        <p><em class="text-danger">[BANNED] {{ $highlighted->comment }}</em></p>
                        <small class="text-danger">Alasan: {{ $highlighted->reports->first()->reason ?? 'Tidak ada alasan' }}</small>
                    @else
                        <p>{{ $highlighted->comment }}</p>
                    @endif
                    <small class="text-muted">{{ $highlighted->created_at->diffForHumans() }}</small>
                </div>
            @endif
            
            <!-- Tampilkan semua komentar dan replies -->
            @foreach($highlighted->photo->comments->sortByDesc(function ($c) use ($highlighted) {
                return $c->id === $highlighted->id ? 1 : 0;
            }) as $c)
                @if($type !== 'comment' || $c->id !== $highlighted->id)
                    <div class="mb-2">
                        @if($c->user->profile_photo)
                            <img src="{{ asset('storage/photo_profile/' . $c->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30">
                        @else
                            <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30"/>
                        @endif
                        <strong>
                            <a href="{{ route('admin.users.previewProfile', $c->user->id) }}" class="text-dark fw-bold text-decoration-none">
                                {{ $c->user->username }}
                            </a>
                        </strong>
                        @if($c->user->verified)
                            <i class="ti-medall-alt" style="color: gold;"></i>
                        @endif 
                        @if($c->user->role === 'pro')
                            <i class="ti-star" style="color: gold;"></i>
                        @endif
                        @if($c->user_id === $highlighted->photo->user_id)
                            <span class="text">• Pembuat</span>
                        @endif
                        
                        @if($c->banned)
                            <p><em class="text-danger">[BANNED] {{ $c->comment }}</em></p>
                            <small class="text-danger">Alasan: {{ $c->reports->first()->reason ?? 'Tidak ada alasan' }}</small>
                        @else
                            <p>{{ $c->comment }}</p>
                        @endif
                        <small class="text-muted">{{ $c->created_at->diffForHumans() }}</small>
            
                        {{-- Tampilkan semua replies untuk komentar ini --}}
                        @foreach($c->replies->sortBy(function ($reply) use ($highlighted) {
                            return $reply->id === $highlighted->id ? -1 : $reply->created_at->timestamp;
                        }) as $reply)
                            <div class="ms-4 mt-2 {{ $type === 'reply' && $reply->id === $highlighted->id ? 'highlight' : '' }}" id="reply-{{ $reply->id }}">
                                @if($reply->user->profile_photo)
                                    <img src="{{ asset('storage/photo_profile/' . $reply->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25">
                                @else
                                    <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25"/>
                                @endif
                                <strong>
                                    <a href="{{ route('admin.users.previewProfile', $reply->user->id) }}" class="text-dark fw-bold text-decoration-none">
                                        {{ $reply->user->username }}
                                    </a>
                                </strong>
                                
                                @if($reply->banned)
                                    <p><em class="text-danger">[BANNED] {{ $reply->reply }}</em></p>
                                    <small class="text-danger">Alasan: {{ $reply->reports->first()->reason ?? 'Tidak ada alasan' }}</small>
                                @else
                                    <p>{{ $reply->reply }}</p>
                                @endif
                                
                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
</div>

@endsection