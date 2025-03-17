@extends('layouts.app')

@push('styles')
    <style>
        /* Styling untuk header album */
.album-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 25px;
    background: linear-gradient(135deg, #32bd40, #2a9d36);
    color: white;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Styling untuk nama album */
.album-title {
    font-size: 2.2rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
    position: relative;
}

.album-title:hover {
    opacity: 0.8;
}

.album-title .edit-icon {
    margin-left: 10px;
    font-size: 1rem;
    cursor: pointer;
}

/* Styling untuk deskripsi album */
.album-description {
    font-size: 1.2rem;
    font-style: italic;
    margin-top: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    max-width: 80%;
    margin-left: auto;
    margin-right: auto;
}

.album-description:hover {
    opacity: 0.8;
}

.album-description .edit-icon {
    margin-left: 5px;
    font-size: 1rem;
    cursor: pointer;
}
/* Tampilan grid untuk foto */
.photo-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px; /* Jarak antar foto */
    margin-bottom: 30px; /* Beri jarak dengan footer */
}

/* Kartu foto */
.photo-grid .card {
    flex: 1 1 calc(25% - 15px); /* 4 foto per baris */
    max-width: calc(25% - 15px);
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.photo-grid .card img {
    width: 100%;
    height: 200px; /* Sesuaikan tinggi foto */
    object-fit: cover;
}

/* Overlay untuk judul foto */
.overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.5);
    color: white;
    padding: 10px;
    text-align: center;
}

/* Dropdown titik tiga horizontal */
.dropdown-container {
    position: absolute;
    top: 10px;
    right: 10px;
}

.dropdown-toggle {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
}

/* Footer */
.footer {
    margin-top: auto; /* Footer selalu di bawah */
    padding: 20px 0;
    background: #f8f9fa;
    text-align: center;
    border-top: 1px solid #ddd;
}

/* Responsif */
@media (max-width: 768px) {
    .photo-grid .card {
        flex: 1 1 calc(50% - 15px); /* 2 foto per baris di layar kecil */
        max-width: calc(50% - 15px);
    }
}
    </style>
@endpush

@section('content')
<div class="container mt-4">
    <!-- Judul dan Deskripsi Album -->
    <div class="album-header">
        <h2 id="album-title" class="album-title editable" data-id="{{ $album->id }}">
            {{ $album->name }}
            @if(Auth::check() && Auth::id() === $album->user_id)
                <i class="fas fa-pencil-alt edit-icon"></i>
            @endif
        </h2>
        <p id="album-description" class="album-description editable" data-id="{{ $album->id }}">
            {{ $album->description }}
            @if(Auth::check() && Auth::id() === $album->user_id)
                <i class="fas fa-pencil-alt edit-icon"></i>
            @endif
        </p>
    </div>

    <!-- Daftar Foto -->
    <div class="row">
        @if($album->photos->isEmpty())
            <div class="col-md-12 text-center">
                <p>Album ini belum memiliki foto.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Tambahkan foto ke album Anda</a>
            </div>
        @else
            <div class="photo-grid">
                @foreach($album->photos as $photo)
                    <div class="card card-pin">
                        <a href="{{ route('photos.show', $photo->id) }}">
                            <img class="card-img" src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}">
                            <div class="overlay">
                                <h2 class="card-title title">{{ $photo->title }}</h2>
                            </div>
                        </a>
                        <!-- Dropdown Hapus Foto (Hanya untuk Pemilik Album) -->
                        @if(Auth::check() && Auth::id() === $album->user_id)
                            <div class="dropdown-container">
                                <button class="dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    &#8942; <!-- Unicode untuk titik tiga horizontal -->
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deletePhotoModal-{{ $photo->id }}">
                                            <i class="fas fa-trash"></i> Hapus Foto
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Hapus Foto -->
                    @if(Auth::check() && Auth::id() === $album->user_id)
                        <div class="modal fade" id="deletePhotoModal-{{ $photo->id }}" tabindex="-1" role="dialog" aria-labelledby="deletePhotoModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deletePhotoModalLabel">Konfirmasi Hapus Foto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus foto ini dari album?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('albums.removePhoto', ['albumId' => $album->id, 'photoId' => $photo->id]) }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk membuat elemen editable
    function makeEditable(element, type) {
        const id = element.getAttribute('data-id');
        const originalText = element.textContent.trim();
        const input = document.createElement('input');
        input.type = 'text';
        input.value = originalText;
        input.classList.add('form-control');
        input.addEventListener('blur', function () {
            saveChanges(element, input.value, type, id);
        });
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                saveChanges(element, input.value, type, id);
            }
        });
        element.innerHTML = '';
        element.appendChild(input);
        input.focus();
    }

    // Fungsi untuk menyimpan perubahan
    function saveChanges(element, newValue, type, id) {
        const url = type === 'title' ? `/albums/${id}/updateTitle` : `/albums/${id}/updateDescription`;
        const token = '{{ csrf_token() }}';

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ [type]: newValue })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.innerHTML = `${data[type]} <i class="fas fa-pencil-alt edit-icon"></i>`;
            } else {
                element.innerHTML = `${originalText} <i class="fas fa-pencil-alt edit-icon"></i>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            element.innerHTML = `${originalText} <i class="fas fa-pencil-alt edit-icon"></i>`;
        });
    }

    // Tambahkan event listener untuk elemen editable
    document.querySelectorAll('.editable').forEach(element => {
        if (element.querySelector('.edit-icon')) {
            element.addEventListener('click', function () {
                const type = element.id === 'album-title' ? 'title' : 'description';
                makeEditable(element, type);
            });
        }
    });
});
</script>
@endpush