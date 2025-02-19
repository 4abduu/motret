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

</style>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 position-relative">
            <canvas id="photoCanvas" class="img-fluid" data-src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"></canvas>
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
                <button type="button" class="btn btn-link p-0 me-3">
                    <i class="bi bi-share text-dark fw-bold fs-5"></i> 
                </button>
                <button type="button" class="btn btn-link p-0 me-3" data-bs-toggle="modal" data-bs-target="#reportModal-{{ $photo->id }}">
                    <i class="bi bi-flag text-dark fw-bold fs-5"></i>
                </button>
            </div>
            <div class="mt-4 text-start comment-container">
                <h3 class="mb-4 text-start">{{ $photo->title }}</h3>
                <h5 class="text-start">{{ $photo->description }}</h5>
                <p class="text-start">Hashtags: {{ implode(', ', json_decode($photo->hashtags)) }}</p>
                
                <p class="text-start d-flex align-items-center">
                    @if($photo->user->profile_photo)
                        <img src="{{ asset('storage/photo_profile/' . $photo->user->profile_photo) }}" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40">
                    @else
                        <img src="{{ asset('images/foto profil.jpg') }}" alt="Profile Picture" class="rounded-circle me-2" width="40" height="40"/>
                    @endif

                    <a href="{{ route('user.showProfile', $photo->user->username) }}">{{ $photo->user->username }}</a>
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
                            <strong>{{ $comment->user->username }}</strong>
                            @if($photo->user->verified)
                                <i class="ti-medall-alt" style="color: gold;"></i>
                            @endif 
                            @if($photo->user->role === 'pro')
                                <i class="ti-star" style="color: gold;"></i> <!-- Tambahkan ini --> 
                            @endif
                            @if($comment->user_id === $photo->user_id)
                                <span class="text-muted">• Pembuat</span>
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
                                    <strong>{{ $reply->user->username }}</strong>
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
            </div>               
        </div>
    </div>
</div>

<div class="my-4">
    <h3>Jelajahi untuk foto lainnya</h3>
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
                        <label for="album-name" class="form-label">Nama Album</label>
                        <input type="text" class="form-control" id="album-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="album-description" class="form-label">Deskripsi Album</label>
                        <textarea class="form-control" id="album-description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Album</button>
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
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const token = '{{ csrf_token() }}';
    const photoId = {{ $photo->id }};

    // Mengatur tampilan input deskripsi alasan lainnya
    document.querySelectorAll('input[name="reason"]').forEach(radio => {
        radio.addEventListener("change", function () {
            const isOther = this.value === "Lainnya";
            document.getElementById("description-group").style.display = isOther ? "block" : "none";
            $('#otherReasonGroup').toggle(isOther);
            $('#other_reason').attr('required', isOther);
        });
    });

    // Reset form saat modal ditutup
    $('#reportModal').on('hidden.bs.modal', function () {
        $('#reportForm')[0].reset();
        $('#otherReasonGroup').hide();
        $('#other_reason').attr('required', false);
    });

    // Event listener untuk like button
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

    // Event delegation untuk tombol tambah ke album
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

    // Membuat album baru
    const createAlbumForm = document.getElementById('createAlbumForm');

    createAlbumForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(createAlbumForm);
        const submitButton = createAlbumForm.querySelector('button[type="submit"]');
        submitButton.disabled = true; // Disable submit button to prevent multiple submissions

        fetch('{{ route('albums.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json' // Paksa Laravel untuk mengembalikan JSON
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status); // Log response status
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Log response data
            if (data.success) {
                alert('Album berhasil dibuat!');
                location.reload(); // Reload the page to see the new album
            } else {
                alert('Gagal membuat album. Silakan coba lagi.');
            }
        })
        .catch(async (error) => {
        console.error('Fetch error:', error);
        
        // Coba baca response sebagai teks
        const responseText = await error.response.text();
        console.log('Response text:', responseText);
        
        alert('Terjadi kesalahan: ' + responseText);
    })
        .finally(() => {
            submitButton.disabled = false; // Re-enable submit button
        });
    });

    // Event delegation untuk tombol "Balas"
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
        const commentId = form.closest('.reply-form').id.split('-')[2];
        const url = form.getAttribute('action');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token },
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                const replyId = Date.now(); // Gunakan timestamp sementara sebagai ID unik
                const userId = data.reply.user.id; // Ambil ID pengguna untuk validasi
                const currentUserId = data.currentUserId; // ID pengguna yang sedang login

                const isCurrentUser = userId === currentUserId;
                
                const replyHtml = `
                    <div class="ms-4 mt-2" id="reply-${replyId}">
                        <strong>${data.reply.user.username}</strong>
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
        }
    });

    // Event listener untuk menghapus reply
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

                const data = await response.json();
                if (data.success) {
                    document.getElementById(`reply-${replyId}`).remove();
                } else {
                    alert(data.message || "Gagal menghapus komentar.");
                }
            } catch (error) {
                console.error("Error deleting reply:", error);
            }
        }
    });

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