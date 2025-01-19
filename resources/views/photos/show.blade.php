@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('storage/' . $photo->path) }}" class="img-fluid" alt="{{ $photo->title }}">
        </div>
        <div class="col-md-6">
            <h1>{{ $photo->title }}</h1>
            <p>{{ $photo->description }}</p>
            <p>Hashtags: {{ implode(', ', json_decode($photo->hashtags)) }}</p>
            <p>Diunggah oleh: <a href="{{ route('user.showProfile', $photo->user->username) }}">{{ $photo->user->username }}</a></p>
            <div id="like-section">
                <button id="like-button" class="btn btn-link" data-liked="{{ $photo->isLikedBy(Auth::user()) ? 'true' : 'false' }}">
                    <i class="{{ $photo->isLikedBy(Auth::user()) ? 'fas fa-heart' : 'fa-regular fa-heart' }}" 
                       style="color: {{ $photo->isLikedBy(Auth::user()) ? 'red' : 'black' }};"></i>
                </button>
                <span id="likes-count">{{ $photo->likes()->count() }}</span>
            </div>
            <form method="POST" action="{{ route('photos.download', $photo->id) }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-download"></i> Unduh
                </button>
            </form>
            <h3>Laporkan</h3>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#reportModal">
                <i class="fas fa-exclamation-triangle"></i> Laporkan
            </button>
            <h3>Komentar</h3>
            @if(Auth::check())
                <form method="POST" action="{{ route('photos.comments.store', $photo->id) }}">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control" name="comment" rows="3" placeholder="Tambahkan komentar..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            @else
                <p>Silakan <a href="{{ route('login') }}">login</a> untuk menambahkan komentar.</p>
            @endif
            <div class="mt-4">
                @foreach($photo->comments as $comment)
                    <div class="mb-2">
                        <strong>{{ $comment->user->username }}</strong>
                        @if($comment->user_id === $photo->user_id)
                            <span class="text-muted">• Pembuat</span>
                        @endif
                        <p>{{ $comment->comment }}</p>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
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
                        <h5 class="mt-2">{{ $randomPhoto->title }}</h5>
                        <p>Hashtags: {{ implode(', ', json_decode($randomPhoto->hashtags)) }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    

    <!-- Modal Report -->
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Laporkan Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm" method="POST" action="{{ route('photos.report', $photo->id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="reason">Alasan Melaporkan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reason" id="reason1" value="Konten tidak pantas" required>
                                <label class="form-check-label" for="reason1">
                                    Konten tidak pantas
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reason" id="reason2" value="Spam" required>
                                <label class="form-check-label" for="reason2">
                                    Spam
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reason" id="reason3" value="Pelanggaran hak cipta" required>
                                <label class="form-check-label" for="reason3">
                                    Pelanggaran hak cipta
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reason" id="reason4" value="Lainnya" required>
                                <label class="form-check-label" for="reason4">
                                    Lainnya
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi (Opsional)</label>
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
        $(document).ready(function() {
            $('input[name="reason"]').change(function() {
                if ($(this).val() === 'Lainnya') {
                    $('#otherReasonGroup').show();
                    $('#other_reason').attr('required', true);
                } else {
                    $('#otherReasonGroup').hide();
                    $('#other_reason').attr('required', false);
                }
            });

            $('#reportModal').on('hidden.bs.modal', function () {
                $('#reportForm')[0].reset();
                $('#otherReasonGroup').hide();
                $('#other_reason').attr('required', false);
            });
        });
    </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const likeButton = document.getElementById('like-button');
        const likesCount = document.getElementById('likes-count');
        const photoId = {{ $photo->id }};
        const token = '{{ csrf_token() }}';

        likeButton.addEventListener('click', function (event) {
            event.preventDefault();

            const liked = likeButton.getAttribute('data-liked') === 'true';
            const url = liked ? '{{ route('photos.unlike', $photo->id) }}' : '{{ route('photos.like', $photo->id) }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({}),
            })
            .then(response => response.json())
            .then(data => {
                if (data.liked) {
                    likeButton.innerHTML = '<i class="fas fa-heart" style="color: red;"></i>';
                    likeButton.setAttribute('data-liked', 'true');
                } else {
                    likeButton.innerHTML = '<i class="fa-regular fa-heart" style="color: black;"></i>';
                    likeButton.setAttribute('data-liked', 'false');
                }
                likesCount.textContent = data.likes_count;
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>

@endpush