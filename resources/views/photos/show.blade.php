@extends('layouts.app') 

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

    #photo-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        overflow: hidden;
        z-index: 1000;
    }

    
#modal-img {
    max-width: 90%;
    max-height: 90%;
    cursor: grab;
    transition: transform 0.2s ease-out;
}

#close-modal {
    position: absolute;
    top: 10px;
    left: 30px;
    font-size: 30px;
    color: white;
    cursor: pointer;
}

#zoom-controls {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.5);
    padding: 5px 10px;
    border-radius: 5px;
    z-index: 1001; /* Pastikan tombol di atas gambar */
    display: flex;
    gap: 10px; /* Jarak antar tombol */
}


#zoom-controls button {
    background: none;
    color: white;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background 0.3s;
}

#zoom-controls button:hover {
    background: rgba(255, 255, 255, 0.2);
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

</style>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 position-relative">
            <div class="photo-container">
                <canvas id="photoCanvas" class="img-fluid" data-src="{{ asset('storage/' . $photo->path) }}"></canvas>
                
                <button id="open-modal" class="zoom-btn">
                    <i class="fas fa-search-plus"></i>
                </button>
            </div>
            <div class="overlay"></div>
            <div class="d-flex align-items-center mt-3">
                <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="me-3 download-button">
                    @csrf
                    <button type="submit" class="btn btn-link p-0">
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
        <div class="col-md-6 ">
            <div class="d-flex align-items-center mb-3">
                <button type="button" class="btn btn-link p-0 me-3" onclick="copyToClipboard()">
                    <i class="bi bi-share text-dark fw-bold fs-5"></i>
                </button>
                <button type="button" class="btn btn-link p-0 me-3" data-bs-toggle="modal" data-bs-target="#reportModal-{{ $photo->id }}" {{ Auth::check() ? '' : 'disabled' }}>
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
                                <a href="{{ route('search', ['query' => $hashtag]) }}" class="keyword-item">
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

                    <a href="{{ route('user.showProfile', $photo->user->username) }}" class="fw-bold">{{ $photo->user->username }}</a>
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
                                <a href="{{ route('user.showProfile', $comment->user->username) }}" class="text-dark fw-bold text-decoration-none">
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
                                
                                @if(Auth::check())
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-link reply-button" data-comment-id="{{ $comment->id }}">Balas</button>
                                        <div class="dropdown ms-2">
                                            <button class="btn btn-link" type="button" id="dropdownMenuButton-{{ $comment->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $comment->id }}">
                                                @if($isOwner)
                                                    <li>
                                                        <form method="POST" action="{{ route('comments.destroy', $comment->id) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">Hapus Komentar</button>
                                                        </form>
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
                                    </div>
                                    <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display: none;">
                                        <form method="POST" action="{{ route('comments.reply', $comment->id) }}">
                                            @csrf
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" name="reply" placeholder="Tambahkan balasan..." required>
                                                <button class="btn btn-outline-secondary" type="submit">
                                                    <i class="bi bi-send-fill text-dark fw-bold fs-5 rotate-90"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
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
                                        <a href="{{ route('user.showProfile', $reply->user->username) }}" class="text-dark fw-bold text-decoration-none">
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
                                    <button class="btn btn-link" type="button" id="dropdownMenuButton-{{ $reply->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    @if(Auth::check())
                                        <div class="dropdown">
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $reply->id }}">
                                                @if($reply->user_id === Auth::id())
                                                    <li>
                                                        <button type="button" class="dropdown-item delete-reply" data-reply-id="{{ $reply->id }}">
                                                            Hapus Balasan
                                                        </button>
                                                    </li>
                                                @else
                                                    <li>
                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportCommentModal-{{ $reply->id }}">
                                                            Lapor Balasan
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                        </div>
                        <div class="modal fade" id="reportCommentModal-{{ $comment->id }}" tabindex="-1" role="dialog" aria-labelledby="reportCommentModalLabel-{{ $comment->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="reportCommentModalLabel-{{ $comment->id }}">Laporkan Komentar</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="reportCommentForm-{{ $comment->id }}" method="POST" action="{{ route('comment.report', $comment->id) }}">
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
                                            <div class="form-group" id="description-group" style="display: none;">
                                                <label for="description">Alasan</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger">Laporkan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>  
            @if(Auth::check())
            <div class="card-footer mt-auto">
                <form method="POST" action="{{ route('photos.comments.store', $photo->id) }}" class="text-start">
                    @csrf
                    <div class="mb-3 d-flex">
                        <textarea class="form-control" name="comment" rows="1" placeholder="Tambahkan komentar..." required></textarea>
                        <button type="submit" class="btn btn-link p-0 ms-2">
                            <i class="bi bi-send text-dark fw-bold fs-5"></i>
                        </button>
                    </div>
                </form>
                @else
                    <p class="text-start">Silakan <a href="{{ route('login') }}">login</a> untuk menambahkan komentar.</p>
                @endif
                {{-- PENTING PENTING PENTING !!!!!!!!!! --}}
                @if(Auth::check())
            </div> 
            @endif                 
        </div>
    </div>
</div>

<div class="my-4">
    <h3 class="card-title mb-4">Jelajahi untuk lainnya</h3>
    <div class="row">
        @if($randomPhotos->isEmpty())
            <div class="col-12 text-center">
                <p class="text-muted">Tidak ada foto yang tersedia saat ini.</p>
            </div>
        @else
            @foreach($randomPhotos as $randomPhoto)
                <div class="col-md-3 mb-4">
                    <a href="{{ route('photos.show', $randomPhoto->id) }}">
                        <img src="{{ asset('storage/' . $randomPhoto->path) }}" class="img-fluid rounded" alt="{{ $randomPhoto->title }}">
                    </a>
                </div>
            @endforeach
        @endif
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
                    <div class="form-group" id="description-group" style="display: none;">
                        <label for="description">Alasan</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Laporkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Zoom Foto -->
<div id="photo-modal">
    <span id="close-modal">&times;</span>
    <img id="modal-img" class="img-fluid" alt="{{ $photo->title }}">
    <div id="zoom-controls">
        <button id="zoom-in">+</button>
        <button id="zoom-out">-</button>
        <button id="reset-zoom">Reset</button> <!-- Tombol reset zoom -->
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/panzoom@9.4.0/dist/panzoom.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/panzoom/9.4.1/panzoom.min.js"></script>
<script>
    function copyToClipboard() {
        // Dapatkan URL saat ini
        const url = window.location.href;

        // Copy URL ke clipboard
        navigator.clipboard.writeText(url).then(() => {
            // Tampilkan notifikasi
            showNotification('Link copied to clipboard');
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }

    function showNotification(message) {
        // Buat elemen notifikasi
        const notification = document.createElement('div');
        notification.innerText = message;
        notification.style.position = 'fixed';
        notification.style.top = '50%'; // Posisi vertikal di tengah
        notification.style.left = '50%'; // Posisi horizontal di tengah
        notification.style.transform = 'translate(-50%, -50%)'; // Geser ke tengah
        notification.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
        notification.style.color = '#fff';
        notification.style.padding = '10px 20px';
        notification.style.borderRadius = '5px';
        notification.style.zIndex = '1000';
        notification.style.fontSize = '14px';
        notification.style.textAlign = 'center'; // Teks di tengah

        // Tambahkan notifikasi ke body
        document.body.appendChild(notification);

        // Hapus notifikasi setelah 3 detik
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 3000);
    }

document.addEventListener("DOMContentLoaded", function () {
    const token = '{{ csrf_token() }}';
    const photoId = {{ $photo->id }};

    const modal = document.getElementById("photo-modal");
    const modalImg = document.getElementById("modal-img");
    const closeModal = document.getElementById("close-modal");
    const zoomInBtn = document.getElementById("zoom-in");
    const zoomOutBtn = document.getElementById("zoom-out");
    const resetZoomBtn = document.getElementById("reset-zoom");

    let panzoomInstance = null;

    // Buka modal saat tombol zoom diklik
    document.getElementById('open-modal').addEventListener('click', () => {
        modal.style.display = 'flex';

        // Ambil gambar dari canvas dan tampilkan di modal
        const photoCanvas = document.getElementById('photoCanvas');
        modalImg.src = photoCanvas.dataset.src;

        // Inisialisasi Panzoom
        panzoomInstance = panzoom(modalImg, {
            maxScale: 4,
            minScale: 1,
            contain: 'outside',
            startScale: 1,
        });

        // Handle zoom in dengan tombol (+)
        zoomInBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation(); // Hindari double-click zooming
            panzoomInstance.smoothZoom(modalImg.clientWidth / 2, modalImg.clientHeight / 2, 1.2);
        });

        // Handle zoom out dengan tombol (-)
        zoomOutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation(); // Hindari double-click zooming
            panzoomInstance.smoothZoom(modalImg.clientWidth / 2, modalImg.clientHeight / 2, 0.8);
        });

        // Handle reset zoom
        resetZoomBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation(); // Hindari double-click zooming
            panzoomInstance.zoomAbs(0, 0, 1); // Reset zoom ke skala 1
            panzoomInstance.moveTo(0, 0); // Reset posisi ke tengah
        });

        // Handle close modal
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
            panzoomInstance.reset();
        });

        // Handle click outside modal to close
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                panzoomInstance.reset();
            }
        });

        // Handle touchpad/mouse wheel untuk zoom
        modalImg.addEventListener('wheel', (e) => {
            e.preventDefault();
            if (e.ctrlKey) {
                panzoomInstance.zoomWithWheel(e);
            } else {
                panzoomInstance.panWithWheel(e);
            }
        });
    });

    // Fungsi untuk menampilkan/menyembunyikan input deskripsi alasan lainnya
    function toggleOtherReasonInput() {
        document.querySelectorAll('input[name="reason"]').forEach(radio => {
            radio.addEventListener("change", function () {
                const isOther = this.value === "Lainnya";
                document.getElementById("description-group").style.display = isOther ? "block" : "none";
                $('#otherReasonGroup').toggle(isOther);
                $('#other_reason').attr('required', isOther);
            });
        });
    }

    // Reset form saat modal ditutup
    $('#reportModal').on('hidden.bs.modal', function () {
        $('#reportForm')[0].reset();
        $('#otherReasonGroup').hide();
        $('#other_reason').attr('required', false);
    });

    // Fungsi untuk handle like/unlike foto
    function handleLikeButton() {
        const likeButton = document.getElementById('like-button');
        const likesCount = document.getElementById('likes-count');

        if (likeButton) {
            likeButton.addEventListener('click', function (event) {
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
                        likesCount.textContent = ''; // Jika tidak ada like, teks dihilangkan
                    }
                })
                .catch(console.error);
            });
        }
    }

    // Fungsi untuk handle tombol tambah ke album
    function handleAddToAlbum() {
        document.addEventListener('click', function (event) {
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
            createAlbumForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(createAlbumForm);
                const submitButton = createAlbumForm.querySelector('button[type="submit"]');
                submitButton.disabled = true; // Disable submit button to prevent multiple submissions

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
                                // Tutup modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('createAlbumModal'));
                                modal.hide();

                                // Hapus overlay modal
                                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

                                // Hapus class 'modal-open' dari body
                                document.body.classList.remove('modal-open');

                                // Reset form
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

                                    // Temukan elemen <li> terakhir sebelum divider
                                    const lastItemBeforeDivider = dropdownMenu.querySelector('li:last-child');
                                    dropdownMenu.insertBefore(newAlbumItem, lastItemBeforeDivider);
                                }
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Gagal membuat album. Silakan coba lagi.',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(async (error) => {
                    console.error('Fetch error:', error);
                    const responseText = await error.response.text();
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: 'Terjadi kesalahan: ' + responseText,
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    submitButton.disabled = false; // Re-enable submit button
                });
            });
        }
    }

    // Fungsi untuk handle tombol balas komentar
    function handleReplyButton() {
        document.addEventListener('click', function (event) {
            if (event.target.closest('.reply-button')) {
                const button = event.target.closest('.reply-button');
                const commentId = button.getAttribute('data-comment-id');
                const replyForm = document.getElementById(`reply-form-${commentId}`);
                replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
            }
        });

        document.body.addEventListener("submit", async function (event) {
            const form = event.target.closest(".reply-form form");
            if (!form) return;

            event.preventDefault();
            event.stopPropagation(); // Mencegah multiple event listener

            const formData = new FormData(form);
            const commentId = form.closest('.reply-form').id.split('-')[2]; // Ambil ID komentar
            const url = form.getAttribute('action'); // Ambil URL action dari form

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

                const data = await response.json();
                if (data.success) {
                    const replyId = data.reply.id;
                    const userId = data.reply.user.id;
                    const currentUserId = data.currentUserId;
                    const photoUserId = data.photoUserId;

                    const isCurrentUser = userId === currentUserId;
                    const isPhotoOwner = userId === photoUserId;

                    const profilePhoto = data.reply.user.profile_photo ?
                        `<img src="/storage/photo_profile/${data.reply.user.profile_photo}" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25">` :
                        `<img src="/images/foto profil.jpg" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25"/>`;

                    const verifiedIcon = data.reply.user.verified ? '<i class="ti-medall-alt" style="color: gold;"></i>' : '';
                    const proIcon = data.reply.user.role === 'pro' ? '<i class="ti-star" style="color: gold;"></i>' : '';
                    const photoOwnerBadge = isPhotoOwner ? '<span class="text">• Pembuat</span>' : '';

                    const replyHtml = `
                        <div class="ms-4 mt-2" id="reply-${replyId}">
                            ${profilePhoto}
                            <strong>
                                <a href="/${data.reply.user.username}" class="text-dark fw-bold text-decoration-none">
                                    ${data.reply.user.username}
                                </a>
                            </strong>
                            ${verifiedIcon}
                            ${proIcon}
                            ${photoOwnerBadge}
                            <p>${data.reply.reply}</p>
                            <small class="text-muted">${data.reply.created_at}</small>
                            <button class="btn btn-link" type="button" id="dropdownMenuButton-${replyId}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <div class="dropdown">
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-${replyId}">
                                    ${isCurrentUser ? `
                                        <li>
                                            <button type="button" class="dropdown-item delete-reply" data-reply-id="${replyId}">
                                                Hapus Balasan
                                            </button>
                                        </li>
                                    ` : `
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportCommentModal-${replyId}">
                                                Lapor Balasan
                                            </button>
                                        </li>
                                    `}
                                </ul>
                            </div>
                        </div>
                    `;

                    document.querySelector(`#reply-form-${commentId}`).insertAdjacentHTML('beforebegin', replyHtml);
                    form.reset();
                    form.closest('.reply-form').style.display = 'none';
                }
            } catch (error) {
                console.error("Error submitting reply:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengirim balasan. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Fungsi untuk handle tombol hapus balasan
    function handleDeleteReply() {
        document.body.addEventListener("click", async function (event) {
            if (event.target.closest(".delete-reply")) {
                event.preventDefault();
                const button = event.target.closest(".delete-reply");
                const replyId = button.getAttribute("data-reply-id");
                const url = `/replies/${replyId}`;

                try {
                    const response = await fetch(url, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": token,
                            "Content-Type": "application/json"
                        }
                    });

                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

                    const data = await response.json();
                    if (data.success) {
                        document.getElementById(`reply-${replyId}`).remove();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || "Gagal menghapus reply.",
                            confirmButtonText: 'OK'
                        });
                    }
                } catch (error) {
                    console.error("Error deleting reply:", error);
                }
            }
        });
    }

    // Blokir klik kanan dan inspect element
    function blockRightClickAndInspect() {
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
            }
        });
    }

    // Render gambar ke canvas dan tambahkan watermark
    function renderImageWithWatermark() {
        const canvas = document.getElementById('photoCanvas');
        if (!canvas) return;

        const imgSrc = canvas.getAttribute('data-src');
        const img = new Image();
        img.src = imgSrc;
        img.crossOrigin = "anonymous";

        img.onload = function () {
            canvas.width = img.width;
            canvas.height = img.height;
            canvas.style.position = "relative";
            canvas.style.zIndex = "1";
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
    }

    // Panggil semua fungsi
    toggleOtherReasonInput();
    handleLikeButton();
    handleAddToAlbum();
    handleCreateAlbum();
    handleReplyButton();
    handleDeleteReply();
    blockRightClickAndInspect();
    renderImageWithWatermark();
});
</script>
@endpush