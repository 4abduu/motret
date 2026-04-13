@extends('layouts.app')

@section('title', 'Preview User Photos')

@section('content')

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<link rel="stylesheet" href="{{ asset('css/pages/admin-preview-photos.css') }}">

<div class="d-flex justify-content-start mb-3">
    <button class="btn btn-link text-decoration-none" onclick="history.back()">
        <i class="bi bi-arrow-left" style="font-size: 2rem; color: #32bd40;"></i>
    </button>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 position-relative">
            <canvas id="photoCanvas" class="img-fluid" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
            <div class="overlay"></div>
            <div class="d-flex align-items-center mt-3">
                <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="me-3 download-button">
                    @csrf
                    <button type="submit" class="btn btn-link p-0" disabled>
                        <i class="bi bi-download text-dark fw-bold fs-5"></i>
                    </button>
                </form>
                <div id="like-section" class="me-3">
                    <button id="like-button" class="btn btn-link p-0" data-liked="{{ $photo->isLikedBy(Auth::user()) ? 'true' : 'false' }}" disabled>
                        <i class="{{ $photo->isLikedBy(Auth::user()) ? 'bi bi-heart-fill fs-5' : 'bi bi-heart fs-5' }}" 
                           style="color: {{ $photo->isLikedBy(Auth::user()) ? 'red' : 'black' }};"></i>
                    </button>
                    @php
                        $likeCount = $photo->likes()->count();
                    @endphp
                    @if ($likeCount > 0)
                        <span id="likes-count">{{ $likeCount }} {{ $likeCount === 1 ? 'like' : 'likes' }}</span>
                    @else
                        <span id="likes-count"></span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="d-flex align-items-center mb-3">
                <button type="button" class="btn btn-link p-0 me-3" disabled>
                    <i class="bi bi-share text-dark fw-bold fs-5"></i> 
                </button>
                <button type="button" class="btn btn-link p-0 me-3" data-bs-toggle="modal" data-bs-target="#reportModal-{{ $photo->id }}" disabled>
                    <i class="bi bi-flag text-dark fw-bold fs-5"></i>
                </button>
            </div>
            <div class="mt-4 text-start comment-container">
                <h3 class="mb-4 text-start">{{ $photo->title }}</h3>
                <h5 class="text-start">{{ $photo->description }}</h5>
                    <div class="most-searched-container">
                        <h4 class="most-searched-title">Hashtags:</h4>
                        <div class="most-searched-keywords">
                            @foreach(json_decode($photo->hashtags) as $hashtag)
                                <a href="#" class="keyword-item">
                                    {{ $hashtag }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                <p class="text-start d-flex align-items-center">
                    @if($photo->user->profile_photo)
                        <img src="{{ asset('storage/photo_profile/' . $photo->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40">
                    @else
                        <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40"/>
                    @endif

                    <a href="{{ route('admin.users.previewProfile', $photo->user->id) }}" class="fw-bold">{{ $photo->user->username }}</a>
                    @if($photo->user->verified)
                        <i class="ti-medall-alt" style="color: gold;"></i>
                    @endif 
                    @if($photo->user->role === 'pro')
                        <i class="ti-star" style="color: gold;"></i> <!-- Tambahkan ini --> 
                    @endif
                </p>
                
                <h6 class="text-start">Komentar</h6>
                
                @foreach($photo->comments as $comment)
                    @php
                        $isOwner = Auth::check() && Auth::id() === $comment->user_id;
                        $hideComment = !$isOwner && $comment->banned;
                        $report = $comment->reports->first();
                    @endphp
                    
                    @if(!$hideComment)
                        <div class="mb-2">
                        @if($comment->user->profile_photo)
                            <img src="{{ asset('storage/photo_profile/' . $comment->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30">
                        @else
                            <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30"/>
                        @endif
                            <strong>
                                <a href="{{ route('admin.users.previewProfile', $comment->user->id) }}" class="text-dark fw-bold text-decoration-none">
                                    {{ $comment->user->username }}
                                </a>
                            </strong>
                            @if($comment->user->verified)
                                <i class="ti-medall-alt" style="color: gold;"></i>
                            @endif 
                            @if($comment->user->role === 'pro')
                                <i class="ti-star" style="color: gold;"></i> <!-- Tambahkan ini --> 
                            @endif
                            @if($comment->user_id === $photo->user_id)
                                <span class="text">• Pembuat</span>
                            @endif
                            @if($comment->banned)
                                @if($isOwner)
                                    <p><em class="text-muted">Komentar anda telah dibanned: {{ $report->reason }}</em></p>
                                @endif
                            @else
                                <p>{{ $comment->comment }}</p>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            
                            @endif
                            @foreach($comment->replies as $reply)
                            @php
                                $hideReply = $comment->banned || (!$isOwner && $reply->banned);
                            @endphp
                            
                            @if(!$hideReply)
                                <div class="ms-4 mt-2" id="reply-{{ $reply->id }}">
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
                                    @if($reply->user->verified)
                                        <i class="ti-medall-alt" style="color: gold;"></i>
                                    @endif 
                                    @if($reply->user->role === 'pro')
                                        <i class="ti-star" style="color: gold;"></i> <!-- Tambahkan ini --> 
                                    @endif
                                    @if($reply->user_id === $photo->user_id)
                                        <span class="text">• Pembuat</span>
                                    @endif
                                    <p>{{ $reply->reply }}</p>
                                    <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                    <button class="btn btn-link" type="button" id="dropdownMenuButton-{{ $reply->id }}" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                </div>
                            @endif
                        @endforeach
                        </div>
                    @endif
                @endforeach
            </div>  
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/admin-preview-photos.js') }}"></script>
@endpush