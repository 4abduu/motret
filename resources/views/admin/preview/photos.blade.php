@extends('layouts.app')

@section('title', 'Preview User Photos')

@section('content')

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    .container {
        width: 80%;
        max-width: 1000px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.5); /* Overlay transparan */
        z-index: 1;
    }
    .btn, .dropdown, #like-section, .download-button {
        position: relative;
        z-index: 2; /* Pastikan tombol berada di atas overlay */
    }
    .comment-container {
        max-height: 300px; /* Sesuaikan tinggi maksimal */
        overflow-y: auto; /* Tambahkan scrollbar jika konten terlalu panjang */
        padding-right: 10px; /* Hindari konten tertutup scrollbar */
    }

    /* (Opsional) Custom scrollbar agar lebih bagus */
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
        gap: 10px; /* Kurangi jarak antar elemen */
    }

    .most-searched-title {
        font-size: 16px; /* Kurangi ukuran font judul */
        margin: 0;
        white-space: nowrap;
    }

    .most-searched-keywords {
        display: flex;
        gap: 5px; /* Kurangi jarak antar keyword */
        flex-wrap: wrap;
    }

    .keyword-item {
        display: inline-block;
        padding: 3px 7px; /* Kurangi padding dalam kotak */
        background-color: #f1f1f1;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px; /* Kurangi ukuran font keyword */
        color: #333;
    }

    .keyword-item:hover {
        background-color: #ddd;
    }

    /* Ubah warna link menjadi hitam */
    a {
        color: #000;
    }

    a:hover {
        color: #555;
    }

    /* Ubah warna teks "Balas" dan ikon titik tiga menjadi hitam */
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const token = '{{ csrf_token() }}';
    const photoId = {{ $photo->id }};

    // Blokir klik kanan
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

    // Blokir inspect element
    document.addEventListener('keydown', function (e) {
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
            e.preventDefault();
        }
    });

    // Render gambar ke canvas dan tambahkan watermark
    const canvas = document.getElementById('photoCanvas');
    const imgSrc = canvas.getAttribute('data-src');
    const img = new Image();
    img.src = imgSrc;
    img.crossOrigin = "anonymous"; // Untuk mencegah CORS error jika dihosting
    img.onload = function () {
        // Set canvas size ke ukuran gambar
        canvas.width = img.width;
        canvas.height = img.height;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        // Tambahkan watermark berulang diagonal
        const watermarkText = "MOTRET"; // Kata yang diulang
        const fontSize = 25; // Ukuran font watermark
        ctx.font = `${fontSize}px Arial`;
        ctx.fillStyle = "rgba(255, 255, 255, 0.3)"; // Warna semi-transparan
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";

        // Atur jarak antar teks watermark
        const stepX = 150; // Jarak horizontal antar watermark
        const stepY = 100; // Jarak vertikal antar watermark
        const angle = -30 * (Math.PI / 180); // Rotasi 30 derajat

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