@extends('layouts.app') 

@section('content')
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
    <script>
        var isGuest = @json(Auth::check() ? false : true);
    </script>
</head>
<style>
    /* Modal Zoom Styles */
    .photo-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        justify-content: center;
        align-items: center;
        overflow: hidden;
        z-index: 9999;
        touch-action: none;
    }

    .modal-content {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        cursor: grab;
        transform-origin: 0 0;
        user-select: none;
        transition: transform 0.15s ease-out;
    }

    .modal-content.grabbing {
        cursor: grabbing;
    }

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
        pointer-events: none; /* Tidak menghalangi interaksi dengan elemen di bawahnya */
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

    canvas {
    display: block;
    width: 100%;
    height: auto;
    z-index: 1;
}

.close-modal {
    position: absolute;
    top: 20px;
    right: 30px;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1001;
    transition: 0.3s;
}

.close-modal:hover {
    color: #bbb;
}
#zoom-controls {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 15px;
    z-index: 1001;
}

#zoom-controls button {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.2s;
}


#zoom-controls button:hover {
    transform: scale(1.1);
}
    
/* Responsive adjustments */
@media (max-width: 768px) {
    .close-modal {
        top: 15px;
        right: 20px;
        font-size: 30px;
    }
    
    #zoom-controls {
        bottom: 20px;
    }
    
    #zoom-controls button {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
}

/* Prevent scrolling when modal is open */
body.modal-open {
    overflow: hidden;
}
    
    /* Other UI Elements */
    .photo-container {
        position: relative;
        display: inline-block;
    }
    
    .zoom-btn {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
        z-index: 998 !important;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        #zoom-controls {
            bottom: 10px;
            padding: 6px 12px;
        }
        
        #zoom-controls button {
            font-size: 20px;
            padding: 3px 10px;
        }
        
        #close-modal {
            left: 15px;
            font-size: 25px;
        }
    }
    
    /* Prevent scrolling when modal is open */
    body.modal-open {
        overflow: hidden;
    }

#zoom-controls button {
    background: none;
    color: white;
    border: none;
    font-size: 24px; /* Lebih besar untuk mobile */
    cursor: pointer;
    padding: 5px 15px;
    border-radius: 5px;
    transition: background 0.3s;
    touch-action: manipulation; /* Mencegah delay tap di mobile */
}

#zoom-controls button:active {
    background: rgba(255, 255, 255, 0.3);
}

#zoom-controls button:hover {
    background: rgba(255, 255, 255, 0.2);
}

body.modal-open {
    overflow: hidden;
}

    .photo-container {
        position: relative;
        display: inline-block;
    }

    .zoom-btn {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
        z-index: 998 !important;
    }

    .zoom-btn:hover {
        background: rgba(0, 0, 0, 0.9);
    }

.overlay-img {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
}

.card-pin:hover .overlay-img {
  opacity: 1;
}

/* Style untuk mobile */
@media (max-width: 768px) {
    #zoom-controls {
        bottom: 10px;
        padding: 6px 12px;
    }
    
    #zoom-controls button {
        font-size: 20px;
        padding: 3px 10px;
    }
    
    #close-modal {
        left: 15px;
        font-size: 25px;
    }
}


</style>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-md-6 position-relative">
            <div class="photo-container shadow-sm rounded">
            <canvas id="photoCanvas" class="img-fluid rounded" data-src="{{ asset('storage/' . $photo->path) }}"></canvas>
                @if(Auth::check())
                    <button id="open-modal" class="zoom-btn btn btn-light rounded-circle p-2">
                        <i class="fas fa-search-plus"></i>
                    </button>
                @endif

            </div>
            <div class="overlay"></div>
            <div class="d-flex align-items-center mt-3">
                <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="me-3 download-button" id="downloadForm">
                    @csrf
                    <button type="button" class="btn btn-link p-0" id="downloadButton">
                        <i class="bi bi-download text-dark fw-bold fs-5"></i>
                    </button>
                </form>
                <div id="like-section" class="me-3">
                    <button id="like-button" class="btn btn-link p-0" data-liked="{{ $photo->isLikedBy(Auth::user()) ? 'true' : 'false' }}" {{ Auth::check() ? '' : 'disabled' }}>
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

                <div class="dropdown">
                    <button class="btn btn-link p-0" id="bookmark-button" data-bs-toggle="dropdown" aria-expanded="false" {{ Auth::check() ? '' : 'disabled' }}>
                        <i class="bi bi-bookmark text-dark fw-bold fs-5"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="bookmark-button">
                        @if($albums)
                            @foreach($albums as $album)
                                <li>
                                    <a class="dropdown-item add-to-album" href="#" data-album-id="{{ $album->id }}">
                                        {{ $album->name }}
                                        @if($album->photos->contains($photo->id))
                                            <i class="fas fa-check text-success"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider"></li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createAlbumModal">
                                <i class="bi bi-plus"></i> Buat Album Baru
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <button type="button" class="btn btn-link p-0 me-3" onclick="copyToClipboard()">
                    <i class="bi bi-share text-dark fw-bold fs-5"></i>
                </button>
                @if (!Auth::check() || Auth::id() !== $photo->user_id)
                <button type="button" class="btn btn-link p-0 me-3" data-bs-toggle="modal" data-bs-target="#reportModal-{{ $photo->id }}" {{ Auth::check() ? '' : 'disabled' }}>
                    <i class="bi bi-flag text-dark fw-bold fs-5"></i>
                </button>
                @endif
            </div>            
            <div class="mt-4 text-start comment-container">
                <h3 class="mb-4 text-start">{{ $photo->title }}</h3>
                <h5 class="text-start">{{ $photo->description }}</h5>
                <div class="most-searched-container mb-2">
                    <h4 class="most-searched-title">Hashtags:</h4>
                    <div class="most-searched-keywords">
                        @foreach(json_decode($photo->hashtags) as $hashtag)
                            <a href="{{ route('search', ['query' => $hashtag]) }}" class="keyword-item badge bg-secondary text-decoration-none me-1">
                                {{ $hashtag }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <p class="text-start d-flex align-items-center mb-3">
                    @if($photo->user->profile_photo)
                        <img src="{{ asset('storage/photo_profile/' . $photo->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40">
                    @else
                        <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40"/>
                    @endif
                
                    <a href="{{ route('user.showProfile', $photo->user->username) }}" class="fw-bold me-2">{{ $photo->user->username }}</a>
                    @if($photo->user->verified)
                        <i class="ti-medall-alt me-2" style="color: gold;"></i>
                    @endif 
                    @if($photo->user->role === 'pro')
                        <i class="ti-star me-2" style="color: gold;"></i>
                    @endif
                
                    <!-- Tombol Follow -->
                    @if(Auth::check())
                        @if(Auth::id() !== $photo->user->id)
                            <button class="btn btn-sm {{ Auth::user()->isFollowing($photo->user) ? 'btn-dark' : 'btn-success' }} ms-3 follow-button" 
                                    data-user-id="{{ $photo->user->id }}"
                                    data-following="{{ Auth::user()->isFollowing($photo->user) ? 'true' : 'false' }}">
                                {{ Auth::user()->isFollowing($photo->user) ? 'Unfollow' : 'Follow' }}
                            </button>
                        @endif
                    @else
                        <button class="btn btn-sm btn-success ms-3" onclick="window.location.href='{{ route('login') }}'">
                            Follow
                        </button>
                    @endif
                </p>
                
                <h6 class="text-start">Komentar</h6>
                                
                @foreach($photo->comments as $comment)
                    @php
                        $isOwner = Auth::check() && Auth::id() === $comment->user_id;
                        $isBanned = $comment->banned && $comment->ban_expires_at;
                        $showBannedMessage = $isBanned && now()->lt($comment->ban_expires_at);
                        $report = $comment->reports->first();
                    @endphp
                
                    @if(!$isBanned || ($showBannedMessage && $isOwner))
                        <div class="card mb-2">
                            <div class="card-body p-2">
                                @if($isBanned && $isOwner)
                                    <div class="alert alert-warning p-2 mb-2">
                                        Komentar anda telah ditangguhkan
                                    </div>
                                @else
                                    <div class="d-flex align-items-center mb-1">
                                        @if($comment->user->profile_photo)
                                            <img src="{{ asset('storage/photo_profile/' . $comment->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30">
                                        @else
                                            <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30"/>
                                        @endif
                                        <strong>
                                            <a href="{{ route('user.showProfile', $comment->user->username) }}" class="text-dark fw-bold text-decoration-none">
                                                {{ $comment->user->username }}
                                            </a>
                                        </strong>
                                        @if($comment->user->verified)
                                            <i class="ti-medall-alt" style="color: gold;"></i>
                                        @endif 
                                        @if($comment->user->role === 'pro')
                                            <i class="ti-star" style="color: gold;"></i>
                                        @endif
                                        @if($comment->user_id === $photo->user_id)
                                            <span class="text">• Pembuat</span>
                                        @endif
                                    </div>
                                    <p class="mb-1 ms-4">{{ $comment->comment }}</p>
                                    <div class="d-flex align-items-center ms-4 mt-1">
                                        <small class="text-muted me-2" style="font-size: 13px;">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </small>
                                        @if(Auth::check())
                                        <button class="btn btn-link p-0 reply-button" data-comment-id="{{ $comment->id }}">
                                            <i class="bi bi-reply"></i>
                                        </button>
                                            <div class="dropdown ms-2">
                                                <button class="btn btn-link p-0" type="button" id="dropdownMenuButton-{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $comment->id }}">
                                                    @if($isOwner)
                                                        <li>
                                                            <button class="dropdown-item delete-comment" data-comment-id="{{ $comment->id }}">
                                                                Hapus Komentar
                                                            </button>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportCommentModal-{{ $comment->id }}">
                                                                Lapor Komentar
                                                            </button>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                    @if(Auth::check() && !$isBanned)
                                        <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                            <form method="POST" action="{{ route('comments.reply', $comment->id) }}">
                                                @csrf
                                                <div class="input-group mb-3 ms-4">
                                                    <input type="text" class="form-control" name="reply" placeholder="Tambahkan balasan..." required>
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="bi bi-send-fill text-dark fw-bold fs-5 rotate-90"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                
                        <!-- Replies - Hanya tampilkan jika komentar tidak dibanned -->
                        @if(!$isBanned)
                            @foreach($comment->replies as $reply)
                                @php
                                    $isReplyOwner = Auth::check() && Auth::id() === $reply->user_id;
                                    $isReplyBanned = $reply->banned && $reply->ban_expires_at;
                                    $showReplyBannedMessage = $isReplyBanned && now()->lt($reply->ban_expires_at);
                                @endphp
                
                                @if(!$isReplyBanned || ($showReplyBannedMessage && $isReplyOwner))
                                    <div class="ms-4 mt-1">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                @if($isReplyBanned && $isReplyOwner)
                                                    <div class="alert alert-warning p-2 mb-2">
                                                        Balasan anda telah ditangguhkan
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center mb-1">
                                                        @if($reply->user->profile_photo)
                                                            <img src="{{ asset('storage/photo_profile/' . $reply->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25">
                                                        @else
                                                            <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25"/>
                                                        @endif
                                                        <strong>
                                                            <a href="{{ route('user.showProfile', $reply->user->username) }}" class="text-dark fw-bold text-decoration-none">
                                                                {{ $reply->user->username }}
                                                            </a>
                                                        </strong>
                                                        @if($reply->user->verified)
                                                            <i class="ti-medall-alt" style="color: gold;"></i>
                                                        @endif 
                                                        @if($reply->user->role === 'pro')
                                                            <i class="ti-star" style="color: gold;"></i>
                                                        @endif
                                                        @if($reply->user_id === $photo->user_id)
                                                            <span class="text">• Pembuat</span>
                                                        @endif
                                                    </div>
                                                    <p class="mb-1 ms-4">{{ $reply->reply }}</p>
                                                    <div class="d-flex align-items-center ms-4 mt-1">
                                                        <small class="text-muted" style="font-size: 12px; margin-top: -2px;">
                                                            {{ $reply->created_at->diffForHumans() }}
                                                        </small>
                                                    
                                                        <!-- Dropdown harus membungkus button -->
                                                        <div class="dropdown ms-2" style="margin-top: -15px;">
                                                            @if (Auth::check())
                                                                <button class="btn btn-link" type="button" id="dropdownMenuButton-{{ $reply->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="bi bi-three-dots"></i>
                                                                </button>
                                                                
                                                            @endif
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $reply->id }}">
                                                                @if($reply->user_id === Auth::id())
                                                                    <li>
                                                                        <button type="button" class="dropdown-item delete-reply" data-reply-id="{{ $reply->id }}">
                                                                            Hapus Balasan
                                                                        </button>
                                                                    </li>
                                                                @elseif (Auth::check())
                                                                    <li>
                                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportReplyModal-{{ $reply->id }}">
                                                                            Lapor Balasan
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endif
                @endforeach
            </div>  
            
            <!-- Form tambah komentar -->
            <div class="card-footer mt-auto p-2">
                @if(Auth::check())
                    <form id="commentForm" method="POST" action="{{ route('photos.comments.store', $photo->id) }}" class="text-start">
                        @csrf
                        <div class="mb-3 d-flex">
                            <textarea class="form-control rounded-5" name="comment" id="commentText" rows="1" placeholder="Tambahkan komentar..." required></textarea>
                            <button type="submit" class="btn btn-link p-0 ms-2">
                                <i class="bi bi-send text-dark fw-bold fs-5"></i>
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-start">Silakan <a href="{{ route('login') }}">login</a> untuk menambahkan komentar.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <h3 class="card-title mb-3">Jelajahi untuk lainnya</h3>
    <div class="row">
        <div class="card-columns">
            @if($randomPhotos->isEmpty())
                <div class="col-12 text-center">
                    <p class="text-muted">Tidak ada foto yang tersedia saat ini.</p>
                </div>
            @else
                @foreach($randomPhotos as $randomPhoto)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $randomPhoto->id) }}">
                            @if(Auth::check() && (Auth::user()->role === 'user' || Auth::user()->role === 'pro'))
                                <img src="{{ asset('storage/' . $randomPhoto->path) }}" class="card-img" alt="{{ $randomPhoto->title }}">
                            @else
                                <canvas class="card-img" data-src="{{ asset('storage/' . $randomPhoto->path) }}" alt="{{ $randomPhoto->title }}"></canvas>
                            @endif                            
                        <div class="overlay-img"></div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Modal Buat Album -->
<div class="modal fade" id="createAlbumModal" tabindex="-1" role="dialog" aria-labelledby="createAlbumModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="createAlbumForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createAlbumModalLabel">Buat Album Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Album</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Album</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    @if (Auth::check() && Auth::user()->role === 'pro')
                        <div class="mb-3">
                            <label for="status" class="form-label">Visibilitas</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1">Publik</option>
                                <option value="0">Privat</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="status" value="1">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Buat Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Report Photo-->
<div class="modal fade" id="reportModal-{{ $photo->id }}" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel-{{ $photo->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel-{{ $photo->id }}">Laporkan Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm" method="POST" action="{{ route('photo.report', $photo->id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="reason">Alasan Melaporkan</label>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input"name="reason" id="reason1"
                                value="Konten tidak pantas">
                                Konten tidak pantas
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="reason" id="reason2" 
                                value="Spam">
                                Spam
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input"name="reason" id="reason3" 
                                value="Pelanggaran hak cipta">
                                Pelanggaran hak cipta
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="reason" id="reason4"
                                value="Lainnya">
                                Lainnya
                            </label>
                        </div>
                    </div>
                    <div class="form-group" id="photo-description-group" style="display: none;">
                        <label for="photo-description">Alasan</label>
                        <textarea class="form-control" id="photo-description" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Laporkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($photo->comments as $comment)
<!-- Modal Report Komentar-->
<div class="modal fade" id="reportCommentModal-{{ $comment->id }}" tabindex="-1" role="dialog" aria-labelledby="reportCommentModalLabel-{{ $comment->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportCommentModalLabel-{{ $comment->id }}">Laporkan Komentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm" method="POST" action="{{ route('comment.report', $comment->id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="reason">Alasan Melaporkan</label>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input"name="reason" id="reason1"
                                value="Konten tidak pantas">
                                Konten tidak pantas
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="reason" id="reason2" 
                                value="Spam">
                                Spam
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input"name="reason" id="reason3" 
                                value="Pelanggaran hak cipta">
                                Pelanggaran hak cipta
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="reason" id="reason4"
                                value="Lainnya">
                                Lainnya
                            </label>
                        </div>
                    </div>
                    <div class="form-group" id="comment-description-group" style="display: none;">
                        <label for="comment-description">Alasan</label>
                        <textarea class="form-control" id="comment-description" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Laporkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($comment->replies as $reply)

<!-- Modal Report Replies-->
<div class="modal fade" id="reportReplyModal-{{ $reply->id }}" tabindex="-1" role="dialog" aria-labelledby="reportReplyModalLabel-{{ $reply->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportReplyModalLabel-{{ $reply->id }}">Laporkan Balasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm" method="POST" action="{{ route('reply.report', $reply->id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="reason">Alasan Melaporkan</label>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input"name="reason" id="reason1"
                                value="Konten tidak pantas">
                                Konten tidak pantas
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="reason" id="reason2" 
                                value="Spam">
                                Spam
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input"name="reason" id="reason3" 
                                value="Pelanggaran hak cipta">
                                Pelanggaran hak cipta
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="reason" id="reason4"
                                value="Lainnya">
                                Lainnya
                            </label>
                        </div>
                    </div>
                    <div class="form-group" id="reply-description-group" style="display: none;">
                        <label for="reply-description">Alasan</label>
                        <textarea class="form-control" id="reply-description" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Laporkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endforeach

<!-- Modal Zoom Foto -->
<div id="photo-modal" class="photo-modal">
    <span class="close-modal">&times;</span>
    <img id="modal-img" class="modal-content">
    <div id="zoom-controls">
        <button id="zoom-in" class="btn btn-light rounded-circle"><i class="bi bi-zoom-in"></i></button>
        <button id="zoom-out" class="btn btn-light rounded-circle"><i class="bi bi-zoom-out"></i></button>
        <button id="reset-zoom" class="btn btn-light rounded-circle"><i class="bi bi-arrow-counterclockwise"></i></button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/panzoom@9.4.0/dist/panzoom.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/panzoom/9.4.1/panzoom.min.js"></script>
<script>
// Fungsi untuk menyalin URL ke clipboard
function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Link berhasil disalin',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            background: '#32bd40',
            iconColor: '#fff',
            color: '#fff',
            timerProgressBar: true
        });
    }).catch(err => {
        console.error('Gagal menyalin:', err);
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Gagal menyalin link',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    });
}

document.addEventListener("DOMContentLoaded", function () {


    // Variabel global
    const token = '{{ csrf_token() }}';
    const photoId = {{ $photo->id }};
    const currentUserId = {{ Auth::id() ?? 'null' }};
    const photoUserId = {{ $photo->user_id }};

    // ==================== FUNGSI UTAMA ====================

    // Fungsi untuk menampilkan SweetAlert
    function showAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            background: icon === 'success' ? '#32bd40' : '#d9534f',
            iconColor: '#fff',
            color: '#fff',
            timerProgressBar: true
        });
    }
    
        function handleDownload(event) {
            event.preventDefault(); // Biar form gak langsung submit

            @if(!Auth::check())
                Swal.fire({
                    title: 'Login Required',
                    text: 'Downloads as a guest will be low quality. Log in for high-quality downloads.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Log In',
                    cancelButtonText: 'Continue as Guest',
                    cancelButtonColor: '#d33',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: 'Low Quality Download',
                            text: 'Since you are a guest, this download will be in low resolution.',
                            icon: 'warning',
                            confirmButtonText: 'Proceed',
                            cancelButtonText: 'Cancel',  // Tambahin tombol Cancel
                            showCancelButton: true,      // Aktifin tombol Cancel
                            reverseButtons: true
                        }).then((res) => {
                            if (res.isConfirmed) {
                                document.getElementById('downloadForm').submit();
                            }
                            // Kalau user klik di luar modal atau cancel, gak ngapa-ngapain
                        });
                    }
                });
            @else
                document.getElementById('downloadForm').submit();
            @endif
        }


                // Tambahkan event listener hanya ke tombol download
                document.getElementById("downloadButton").addEventListener("click", handleDownload);

// ==================== FITUR ZOOM GAMBAR ====================

    // Inisialisasi modal zoom gambar
    const modal = document.getElementById("photo-modal");
    const modalImg = document.getElementById("modal-img");
    const closeModal = document.querySelector(".close-modal");
    const zoomInBtn = document.getElementById("zoom-in");
    const zoomOutBtn = document.getElementById("zoom-out");
    const resetZoomBtn = document.getElementById("reset-zoom");

    // State zoom
    let currentScale = 1;
    let posX = 0;
    let posY = 0;
    const MIN_SCALE = 0.5;
    const MAX_SCALE = 4;
    let isDragging = false;
    let startX, startY;
    let hammer;

    // Fungsi untuk update transform gambar
    function updateTransform() {
        modalImg.style.transform = `translate(${posX}px, ${posY}px) scale(${currentScale})`;
    }

    // Fungsi untuk membatasi skala zoom
    function clampScale(scale) {
        return Math.max(MIN_SCALE, Math.min(MAX_SCALE, scale));
    }

    // Inisialisasi Hammer.js untuk gesture touch
    function initHammer() {
        if (hammer) hammer.destroy();
        
        hammer = new Hammer(modalImg, {
            recognizers: [
                [Hammer.Pan, { direction: Hammer.DIRECTION_ALL }],
                [Hammer.Pinch],
                [Hammer.Tap, { event: 'doubletap', taps: 2 }]
            ]
        });

        let initialScale, initialPosX, initialPosY;

        // Gesture pan (geser)
        hammer.on('panstart', function() {
            if (currentScale > 1) {
                initialPosX = posX;
                initialPosY = posY;
                modalImg.style.cursor = 'grabbing';
            }
        });

        hammer.on('pan', function(e) {
            if (currentScale > 1) {
                posX = initialPosX + e.deltaX;
                posY = initialPosY + e.deltaY;
                updateTransform();
            }
        });

        hammer.on('panend', function() {
            modalImg.style.cursor = currentScale > 1 ? 'grab' : 'default';
        });

        // Gesture pinch zoom
        hammer.on('pinchstart', function() {
            initialScale = currentScale;
        });

        hammer.on('pinch', function(e) {
            const newScale = clampScale(initialScale * e.scale);
            if (newScale !== currentScale) {
                currentScale = newScale;
                
                const rect = modalImg.getBoundingClientRect();
                const centerX = (e.center.x - rect.left - posX) / currentScale;
                const centerY = (e.center.y - rect.top - posY) / currentScale;
                
                posX = e.center.x - rect.left - centerX * currentScale;
                posY = e.center.y - rect.top - centerY * currentScale;
                
                updateTransform();
            }
        });

        // Double tap untuk zoom in/out
        hammer.on('doubletap', function(e) {
            const rect = modalImg.getBoundingClientRect();
            const tapX = e.center.x - rect.left;
            const tapY = e.center.y - rect.top;
            
            if (currentScale > 1) {
                // Reset zoom
                currentScale = 1;
                posX = 0;
                posY = 0;
            } else {
                // Zoom in 2x pada posisi tap
                currentScale = 2;
                posX = -(tapX * (currentScale - 1));
                posY = -(tapY * (currentScale - 1));
            }
            updateTransform();
        });
    }

    // Event listener untuk mouse wheel zoom
    function handleWheel(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const delta = e.deltaY < 0 ? 1.1 : 0.9;
        const newScale = clampScale(currentScale * delta);
        
        if (newScale !== currentScale) {
            const rect = modalImg.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            posX = x - (x - posX) * (newScale / currentScale);
            posY = y - (y - posY) * (newScale / currentScale);
            currentScale = newScale;
            
            updateTransform();
        }
    }

    // Event listener untuk mouse down (drag)
    function handleMouseDown(e) {
        if (e.button === 0 && currentScale > 1) {
            isDragging = true;
            startX = e.clientX - posX;
            startY = e.clientY - posY;
            modalImg.style.cursor = 'grabbing';
            e.preventDefault();
        }
    }

    // Event listener untuk mouse move (drag)
    function handleMouseMove(e) {
        if (isDragging) {
            posX = e.clientX - startX;
            posY = e.clientY - startY;
            updateTransform();
        }
    }

    // Event listener untuk mouse up (drag)
    function handleMouseUp(e) {
        if (e.button === 0) {
            isDragging = false;
            modalImg.style.cursor = currentScale > 1 ? 'grab' : 'default';
        }
    }

    // Blokir klik kanan pada gambar
    modalImg.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Buka modal zoom
    @if (Auth::check())
    document.getElementById('open-modal').addEventListener('click', () => {
        modal.style.display = 'flex';
        const photoCanvas = document.getElementById('photoCanvas');
        modalImg.src = photoCanvas.dataset.src;
        
        // Reset transform
        currentScale = 1;
        posX = 0;
        posY = 0;
        updateTransform();
        
        // Tambahkan event listeners
        modalImg.addEventListener('wheel', handleWheel, { passive: false });
        modalImg.addEventListener('mousedown', handleMouseDown);
        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', handleMouseUp);
        
        // Inisialisasi gesture touch
        initHammer();
        
        // Cegah scrolling body
        document.body.classList.add('modal-open');
    });
    @endif

    // Tutup modal zoom
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
        // Hapus event listeners
        modalImg.removeEventListener('wheel', handleWheel);
        modalImg.removeEventListener('mousedown', handleMouseDown);
        document.removeEventListener('mousemove', handleMouseMove);
        document.removeEventListener('mouseup', handleMouseUp);
        
        // Aktifkan kembali scrolling body
        document.body.classList.remove('modal-open');
    });

    // Klik di luar gambar untuk tutup modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    });

    // Tombol zoom in
    zoomInBtn.addEventListener('click', function() {
        const newScale = clampScale(currentScale * 1.2);
        if (newScale !== currentScale) {
            const rect = modalImg.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            posX = -(centerX * (newScale - currentScale)) + posX * (newScale / currentScale);
            posY = -(centerY * (newScale - currentScale)) + posY * (newScale / currentScale);
            currentScale = newScale;
            
            updateTransform();
        }
    });

    // Tombol zoom out
    zoomOutBtn.addEventListener('click', function() {
        const newScale = clampScale(currentScale * 0.8);
        if (newScale !== currentScale) {
            const rect = modalImg.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            posX = (centerX * (currentScale - newScale) + posX * (newScale / currentScale));
            posY = (centerY * (currentScale - newScale) + posY * (newScale / currentScale));
            currentScale = newScale;
            
            updateTransform();
        }
    });

    // Tombol reset zoom
    resetZoomBtn.addEventListener('click', function() {
        currentScale = 1;
        posX = 0;
        posY = 0;
        updateTransform();
    });

    // ==================== SISTEM KOMENTAR ====================

    // Fungsi untuk membuat elemen komentar baru
    function createCommentElement(comment, user, isCurrentUser) {
    const isPhotoOwner = user.id === {{ $photo->user_id }};
    const profilePhoto = user.profile_photo 
        ? `<img src="/storage/photo_profile/${user.profile_photo}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30">`
        : `<img src="/images/foto profil.jpg" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30"/>`;
    
    const verifiedIcon = user.verified ? '<i class="ti-medall-alt" style="color: gold;"></i>' : '';
    const proIcon = user.role === 'pro' ? '<i class="ti-star" style="color: gold;"></i>' : '';
    const photoOwnerBadge = isPhotoOwner ? '<span class="text">• Pembuat</span>' : '';
    
    return `
        <div class="card mb-2" data-comment-id="${comment.id}">
            <div class="card-body p-2">
                <div class="d-flex align-items-center mb-1">
                    ${profilePhoto}
                    <strong>
                        <a href="/${user.username}" class="text-dark fw-bold text-decoration-none">
                            ${user.username}
                        </a>
                    </strong>
                    ${verifiedIcon}
                    ${proIcon}
                    ${photoOwnerBadge}
                </div>
                <p class="mb-1 ms-4">${comment.comment}</p>
                <div class="d-flex align-items-center ms-4 mt-1">
                    <small class="text-muted me-2" style="font-size: 13px;">
                        Baru saja
                    </small>
                    <button class="btn btn-link p-0 reply-button" data-comment-id="${comment.id}">
                        <i class="bi bi-reply"></i>
                    </button>
                    ${isCurrentUser ? `
                    <div class="dropdown ms-2">
                        <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button class="dropdown-item delete-comment" data-comment-id="${comment.id}">
                                    Hapus Komentar
                                </button>
                            </li>
                        </ul>
                    </div>
                    ` : ''}
                </div>
                <!-- Form reply untuk komentar baru -->
                <div class="reply-form" id="reply-form-${comment.id}" style="display: none;">
                    <form method="POST" action="/comments/${comment.id}/reply">
                        @csrf
                        <div class="input-group mb-3 ms-4">
                            <input type="text" class="form-control" name="reply" placeholder="Tambahkan balasan..." required>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-send-fill text-dark fw-bold fs-5 rotate-90"></i>
                                </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
}

    // Fungsi untuk membuat elemen balasan baru
    function createReplyElement(reply, user, isCurrentUser, photoUserId) {
    const isPhotoOwner = user.id === photoUserId;
    const profilePhoto = user.profile_photo
        ? `<img src="/storage/photo_profile/${user.profile_photo}" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25">`
        : `<img src="/images/foto profil.jpg" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25"/>`;

    const verifiedIcon = user.verified ? '<i class="ti-medall-alt" style="color: gold;"></i>' : '';
    const proIcon = user.role === 'pro' ? '<i class="ti-star" style="color: gold;"></i>' : '';
    const photoOwnerBadge = isPhotoOwner ? '<span class="text">• Pembuat</span>' : '';

    return `
    <div class="ms-4 mt-1" data-reply-id="${reply.id}">
        <div class="card">
            <div class="card-body p-2">
                <div class="d-flex align-items-center mb-1">
                    ${profilePhoto}
                    <strong>
                        <a href="/${user.username}" class="text-dark fw-bold text-decoration-none">
                            ${user.username}
                        </a>
                    </strong>
                    ${verifiedIcon} ${proIcon}
                    ${isPhotoOwner ? '<span class="text">• Pembuat</span>' : ''}
                </div>
                <p class="mb-1 ms-4">${reply.reply}</p>
                <div class="d-flex align-items-center ms-4 mt-1">
                    <small class="text-muted" style="font-size: 12px; margin-top: -2px;">
                        Baru saja
                    </small>
                    ${isCurrentUser ? `
                    <div class="dropdown ms-2" style="margin-top: -15px;">
                        <button class="btn btn-link" type="button" id="dropdownMenuButton-${reply.id}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-${reply.id}">
                            <li>
                                <button type="button" class="dropdown-item delete-reply" data-reply-id="${reply.id}">
                                    Hapus Balasan
                                </button>
                            </li>
                        </ul>
                    </div>` : `
                    <div class="dropdown ms-2" style="margin-top: -15px;">
                        <button class="btn btn-link" type="button" id="dropdownMenuButton-${reply.id}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-${reply.id}">
                            <li>
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportReplyModal-${reply.id}">
                                    Lapor Balasan
                                </button>
                            </li>
                        </ul>
                    </div>`}
                </div>
            </div>
        </div>
    </div>
`;
}

    // ==================== EVENT LISTENERS ====================

    // Form komentar utama
const commentForm = document.getElementById('commentForm');
if (commentForm) {
    commentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(commentForm);
        const commentText = formData.get('comment');
        
        try {
            const response = await fetch(commentForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            
            if (data.success) {
                // Reset form
                commentForm.reset();
                
                // Buat dan tambahkan komentar baru
                const commentContainer = document.querySelector('.comment-container');
                const newComment = createCommentElement(data.comment, data.comment.user, true);
                commentContainer.insertAdjacentHTML('beforeend', newComment);
                
                // SweetAlert sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Komentar berhasil ditambahkan',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    background: '#32bd40',
                    iconColor: '#fff',
                    color: '#fff',
                    timerProgressBar: true
                });
            }
        } catch (error) {
            console.error('Error submitting comment:', error);
            // SweetAlert error
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menambahkan komentar. Silakan coba lagi.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });
}

// Fungsi untuk handle reply button (delegasi event)
document.addEventListener('click', function(e) {
    // Handle tombol reply
    if (e.target.closest('.reply-button')) {
        e.preventDefault();
        const button = e.target.closest('.reply-button');
        const commentId = button.getAttribute('data-comment-id');
        const replyForm = document.getElementById(`reply-form-${commentId}`);
        
        // Tutup semua form reply lain
        document.querySelectorAll('.reply-form').forEach(form => {
            if (form.id !== `reply-form-${commentId}`) {
                form.style.display = 'none';
            }
        });
        
        // Toggle form yang dipilih
        if (replyForm.style.display === 'none' || !replyForm.style.display) {
            replyForm.style.display = 'block';
            replyForm.querySelector('input').focus(); // Auto focus ke input
        } else {
            replyForm.style.display = 'none';
        }
    }

    // Handle submit reply
    if (e.target.closest('.reply-form form')) {
        e.preventDefault();
        const form = e.target.closest('form');
        submitReplyForm(form);
    }
});

async function submitReplyForm(form) {
    try {
        const replyText = form.querySelector('[name="reply"]').value.trim();
        if (!replyText) return;

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: new FormData(form)
        });

        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const data = await response.json();
        
        if (data.success) {
            form.reset();
            form.closest('.reply-form').style.display = 'none';
            
            // Tambahkan reply baru ke DOM
            const replyContainer = form.closest('.card-body');
            const newReply = createReplyElement(
                data.reply, 
                data.reply.user, 
                data.reply.user.id === {{ Auth::id() ?? 'null' }},
                data.photoUserId
            );
            
            replyContainer.insertAdjacentHTML('beforeend', newReply);
            
            // Notifikasi sukses
            showSuccessAlert('Balasan berhasil ditambahkan');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorAlert('Gagal menambahkan balasan');
    } finally {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = false;
    }
}

// Fungsi bantuan untuk alert
function showSuccessAlert(message) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        background: '#32bd40',
        iconColor: '#fff',
        color: '#fff',
        timerProgressBar: true
    });
}

function showErrorAlert(message) {
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
}

    // Hapus komentar
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.delete-comment')) {
            e.preventDefault();
            const button = e.target.closest('.delete-comment');
            const commentId = button.getAttribute('data-comment-id');
            
            // Dialog konfirmasi
            Swal.fire({
                title: 'Hapus Komentar?',
                text: "Anda yakin ingin menghapus komentar ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/comments/${commentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) throw new Error('Network response was not ok');
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Hapus elemen komentar dari DOM
                            const commentElement = button.closest('.card');
                            if (commentElement) {
                                commentElement.remove();
                                showAlert('success', 'Berhasil!', 'Komentar berhasil dihapus.');
                            }
                        }
                    } catch (error) {
                        console.error('Error deleting comment:', error);
                        showAlert('error', 'Gagal!', 'Gagal menghapus komentar. Silakan coba lagi.');
                    }
                }
            });
        }
    });

    // Hapus balasan
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.delete-reply')) {
            e.preventDefault();
            const button = e.target.closest('.delete-reply');
            const replyId = button.getAttribute('data-reply-id');
            
            // Dialog konfirmasi
            Swal.fire({
                title: 'Hapus Balasan?',
                text: "Anda yakin ingin menghapus balasan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/replies/${replyId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) throw new Error('Network response was not ok');
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Hapus elemen balasan dari DOM
                            const replyElement = button.closest('.card');
                            if (replyElement) {
                                replyElement.remove();
                                showAlert('success', 'Berhasil!', 'Balasan berhasil dihapus.');
                            }
                        }
                    } catch (error) {
                        console.error('Error deleting reply:', error);
                        showAlert('error', 'Gagal!', 'Gagal menghapus balasan. Silakan coba lagi.');
                    }
                }
            });
        }
    });

    // ==================== FITUR LAINNYA ====================

    // Fungsi untuk menampilkan/menyembunyikan input deskripsi alasan lainnya
    function setupReportModals() {
        // Event delegation for all report modals
        document.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'reason') {
                const modal = e.target.closest('.modal');
                if (!modal) return;
                
                const isOther = e.target.value === "Lainnya";
                let descriptionGroup, textarea;
                
                if (modal.id.startsWith('reportModal-')) {
                    descriptionGroup = document.getElementById('photo-description-group');
                    textarea = document.getElementById('photo-description');
                } else if (modal.id.startsWith('reportCommentModal-')) {
                    descriptionGroup = document.getElementById('comment-description-group');
                    textarea = document.getElementById('comment-description');
                } else if (modal.id.startsWith('reportReplyModal-')) {
                    descriptionGroup = document.getElementById('reply-description-group');
                    textarea = document.getElementById('reply-description');
                }
                
                if (descriptionGroup) {
                    descriptionGroup.style.display = isOther ? 'block' : 'none';
                    if (textarea) {
                        textarea.required = isOther;
                        if (isOther) {
                            textarea.focus(); // Auto-focus when "Lainnya" is selected
                        }
                    }
                }
            }
        });

        // Form submission validation
        document.querySelectorAll('[id^="reportModal-"], [id^="reportCommentModal-"], [id^="reportReplyModal-"]').forEach(modal => {
            const form = modal.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const selectedReason = form.querySelector('input[name="reason"]:checked');
                    const isOther = selectedReason && selectedReason.value === "Lainnya";
                    let textarea;
                    
                    if (modal.id.startsWith('reportModal-')) {
                        textarea = document.getElementById('photo-description');
                    } else if (modal.id.startsWith('reportCommentModal-')) {
                        textarea = document.getElementById('comment-description');
                    } else if (modal.id.startsWith('reportReplyModal-')) {
                        textarea = document.getElementById('reply-description');
                    }
                    
                    if (isOther && (!textarea || textarea.value.trim() === '')) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Harap isi alasan pelaporan',
                            confirmButtonColor: '#3085d6',
                        });
                        textarea.focus();
                    }
                });
            }
        });

        // Reset all report modals when closed
        document.querySelectorAll('[id^="reportModal-"], [id^="reportCommentModal-"], [id^="reportReplyModal-"]').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                // Reset radio buttons
                const radioButtons = modal.querySelectorAll('input[type="radio"]');
                radioButtons.forEach(radio => {
                    radio.checked = false;
                });

                // Hide and clear description field
                let descriptionGroup, textarea;
                
                if (modal.id.startsWith('reportModal-')) {
                    descriptionGroup = document.getElementById('photo-description-group');
                    textarea = document.getElementById('photo-description');
                } else if (modal.id.startsWith('reportCommentModal-')) {
                    descriptionGroup = document.getElementById('comment-description-group');
                    textarea = document.getElementById('comment-description');
                } else if (modal.id.startsWith('reportReplyModal-')) {
                    descriptionGroup = document.getElementById('reply-description-group');
                    textarea = document.getElementById('reply-description');
                }

                if (descriptionGroup) {
                    descriptionGroup.style.display = 'none';
                }
                if (textarea) {
                    textarea.required = false;
                    textarea.value = '';
                }
            });
        });
    }

    // Fungsi untuk handle like/unlike foto
    function handleLikeButton() {
        const likeButton = document.getElementById('like-button');
        const likesCount = document.getElementById('likes-count');

        if (likeButton) {
            likeButton.addEventListener('click', function(event) {
                event.preventDefault();
                const liked = likeButton.getAttribute('data-liked') === 'true';
                const url = liked ? '{{ route('photos.unlike', $photo->id) }}' : '{{ route('photos.like', $photo->id) }}';

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    likeButton.innerHTML = `<i class="bi ${data.liked ? 'bi-heart-fill' : 'bi-heart'}" style="color: ${data.liked ? 'red' : 'black'};"></i>`;
                    likeButton.setAttribute('data-liked', data.liked);
                    
                    if (data.likes_count > 0) {
                        likesCount.textContent = data.likes_count + (data.likes_count === 1 ? ' like' : ' likes');
                    } else {
                        likesCount.textContent = '';
                    }
                })
                .catch(console.error);
            });
        }
    }

    // Fungsi untuk handle tombol tambah ke album
    function handleAddToAlbum() {
        document.addEventListener('click', function(event) {
            if (event.target.closest('.add-to-album')) {
                event.preventDefault();
                const button = event.target.closest('.add-to-album');
                const albumId = button.getAttribute('data-album-id');
                const url = button.querySelector('i') ? `/albums/${albumId}/photos/${photoId}/remove` : `/albums/${albumId}/photos/${photoId}/add`;

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.querySelector('i') ? button.querySelector('i').remove() : button.innerHTML += ' <i class="bi bi-check text-success"></i>';
                    }
                })
                .catch(console.error);
            }
        });
    }

    // Fungsi untuk membuat album baru
    function handleCreateAlbum() {
        const createAlbumForm = document.getElementById('createAlbumForm');

        if (createAlbumForm) {
            createAlbumForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(createAlbumForm);
                const submitButton = createAlbumForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;

                fetch('{{ route('albums.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Album berhasil dibuat!',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Tutup modal dan reset form
                                const modal = bootstrap.Modal.getInstance(document.getElementById('createAlbumModal'));
                                modal.hide();
                                createAlbumForm.reset();

                                // Tambahkan album baru ke dropdown
                                const dropdownMenu = document.querySelector('.dropdown-menu');
                                if (dropdownMenu) {
                                    const newAlbumItem = document.createElement('li');
                                    newAlbumItem.innerHTML = `
                                        <a class="dropdown-item add-to-album" href="#" data-album-id="${data.album.id}">
                                            ${data.album.name}
                                        </a>
                                    `;

                                    const lastItemBeforeDivider = dropdownMenu.querySelector('li:last-child');
                                    dropdownMenu.insertBefore(newAlbumItem, lastItemBeforeDivider);
                                }
                            }
                        });
                    }
                })
                .finally(() => {
                    submitButton.disabled = false;
                });
            });
        }
    }

    // Blokir klik kanan dan inspect element
    function blockRightClickAndInspect() {
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
            }
        });
    }

    // Render gambar ke canvas dengan watermark
    function renderImageWithWatermark() {
        const canvas = document.getElementById('photoCanvas');
        if (!canvas) return;

        const imgSrc = canvas.getAttribute('data-src');
        const img = new Image();
        img.src = imgSrc;
        img.crossOrigin = "anonymous";

        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            // Tambahkan watermark
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
    }

            
        // Handle follow buttons in the photo list
        document.querySelectorAll('.follow-button').forEach(button => {
            button.addEventListener('click', function() {
                handleFollowUnfollow(this);
            });
        });

        // Function to update follow button appearance
        function updateFollowButton(button, following) {
            if (following) {
                button.textContent = 'Unfollow';
                button.classList.remove('btn-success');
                button.classList.add('btn-dark');
            } else {
                button.textContent = 'Follow';
                button.classList.remove('btn-dark');
                button.classList.add('btn-success');
            }
            button.setAttribute('data-following', following);
        }

        // Function to handle follow/unfollow action
        function handleFollowUnfollow(button) {
            const userId = button.getAttribute('data-user-id');
            const isFollowing = button.getAttribute('data-following') === 'true';
            const url = isFollowing ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateFollowButton(button, !isFollowing);
                    
                    // You can add additional updates here if needed
                    // For example, update follower counts if they're displayed
                } else {
                    throw new Error(data.message || 'Failed to process request.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // You can add SweetAlert or other error handling here
            });
        }

    @if(!Auth::check() || (Auth::check() && (Auth::user()->role !== 'user' && Auth::user()->role !== 'pro')))
        function renderCanvasImgGuest() {  
            document.querySelectorAll('canvas.card-img').forEach(function (canvas) {
                var imgSrc = canvas.getAttribute('data-src');
                var img = new Image();
                img.src = imgSrc;
                img.onload = function () {
                    var ctx = canvas.getContext('2d');
                    var width = canvas.width;
                    var height = canvas.height;
                    var aspectRatio = img.width / img.height;

                    if (width / height > aspectRatio) {
                        width = height * aspectRatio;
                    } else {
                        height = width / aspectRatio;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                };
            });
        }

    // ==================== INISIALISASI ====================

    // Panggil semua fungsi yang diperlukan
    renderCanvasImgGuest();    
    @endif
    setupReportModals();
    handleLikeButton();
    handleAddToAlbum();
    handleCreateAlbum();
    blockRightClickAndInspect();
    renderImageWithWatermark();
    handleFollowUnfollow();
});
</script>
@endpush