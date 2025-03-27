@extends('layouts.app')

@section('title', 'Preview Comment/Reply')


@section('content')

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    /* CSS yang udah ada, gak diubah */
    .container {
        width: 80%;
        max-width: 1000px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
    }
    .highlight {
        background-color: rgba(173, 216, 230, 0.3); /* Biru muda transparan */
        border-left: 4px solid #1e90ff; /* Garis biru di sebelah kiri */
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease; /* Efek transisi */
    }
    .highlight:hover {
        background-color: rgba(173, 216, 230, 0.5); /* Lebih gelap saat dihover */
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.5);
        z-index: 1;
    }
    .btn, .dropdown, #like-section, .download-button {
        position: relative;
        z-index: 2;
    }
    .comment-container {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 10px;
    }
    .comment-container::-webkit-scrollbar {
        width: 8px;
    }
    .comment-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 5px;
    }
    .comment-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }
    .comment-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    .most-searched-container {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .most-searched-title {
        font-size: 16px;
        margin: 0;
        white-space: nowrap;
    }
    .most-searched-keywords {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    .keyword-item {
        display: inline-block;
        padding: 3px 7px;
        background-color: #f1f1f1;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        color: #333;
    }
    .keyword-item:hover {
        background-color: #ddd;
    }
    a {
        color: #000;
    }
    a:hover {
        color: #555;
    }
    .btn-link {
        color: #000;
    }
    .btn-link:hover {
        color: #555;
    }
</style>

<div class="d-flex justify-content-start mb-3">
    <button class="btn btn-link text-decoration-none" onclick="history.back()">
        <i class="bi bi-arrow-left" style="font-size: 2rem; color: #32bd40;"></i>
    </button>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 position-relative">
            <canvas id="photoCanvas" class="img-fluid" data-src="{{ asset('storage/' . $highlighted->photo->path) }}" alt="{{ $highlighted->photo->title }}"></canvas>
            <div class="overlay"></div>
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
                        <p>{{ $highlighted->comment }}</p>
                        <small class="text-muted">{{ $highlighted->created_at->diffForHumans() }}</small>
                    </div>
                @endif

                <!-- Tampilkan semua komentar dan replies -->
                @foreach($highlighted->photo->comments->sortByDesc(function ($c) use ($highlighted) {
                    return $c->id === $highlighted->id ? 1 : 0;
                }) as $c)
                    @php
                        $isOwner = Auth::check() && Auth::id() === $c->user_id;
                        $hideComment = !$isOwner && $c->banned;
                        $report = $c->reports->first();
                    @endphp

                    @if(!$hideComment && ($type !== 'comment' || $c->id !== $highlighted->id))
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
                                @if($isOwner)
                                    <p><em class="text-muted">Komentar anda telah dibanned: {{ $report->reason }}</em></p>
                                @endif
                            @else
                                <p>{{ $c->comment }}</p>
                                <small class="text-muted">{{ $c->created_at->diffForHumans() }}</small>
                            @endif

                            {{-- Tampilkan semua replies untuk komentar ini --}}
                            @foreach($c->replies->sortBy(function ($reply) use ($highlighted) {
                                return $reply->id === $highlighted->id ? -1 : $reply->created_at->timestamp;
                            }) as $reply)
                                @php
                                    $isReplyOwner = Auth::check() && Auth::id() === $reply->user_id;
                                    $hideReply = $c->banned || (!$isReplyOwner && $reply->banned);
                                @endphp

                                @if(!$hideReply)
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
                                        <p>{{ $reply->reply }}</p>
                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById('photoCanvas');
    const imgSrc = canvas.getAttribute('data-src');
    const img = new Image();
    img.src = imgSrc;
    img.crossOrigin = "anonymous";
    img.onload = function () {
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        const watermarkText = "MOTRET";
        const fontSize = 25;
        ctx.font = `${fontSize}px Arial`;
        ctx.fillStyle = "rgba(255, 255, 255, 0.3)";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        const stepX = 150;
        const stepY = 100;
        const angle = -30 * (Math.PI / 180);
        ctx.save();
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate(angle);
        for (let x = -canvas.width; x < canvas.width; x += stepX) {
            for (let y = -canvas.height; y < canvas.height; y += stepY) {
                ctx.fillText(watermarkText, x, y);
            }
        }
        ctx.restore();
    };
});
</script>
@endpush