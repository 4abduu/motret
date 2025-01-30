@extends('layouts.app')
@push('link')
    <style>
        /* CSS to position the dropdown outside the card */
        .card-columns {
            position: relative;
        }

        .dropdown-container {
            position: absolute;
            top: 100%; /* Positions the dropdown below the card */
            right: 60%; /* Moves the dropdown to the left of the card */
            margin-right: 10px; /* Optional: Add space between the card and dropdown */
        }

    </style>
    <link rel="stylesheet" href="{{asset ('user/assets/css/app.css')}}">
    <link rel="stylesheet" href="{{asset ('user/assets/css/theme.css')}}">
@endpush
@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 id="album-title" class="editable" data-id="{{ $album->id }}">
                {{ $album->name }}
                <i class="fas fa-pencil-alt edit-icon"></i>
            </h1>
            <p id="album-description" class="editable" data-id="{{ $album->id }}">
                {{ $album->description }}
                <i class="fas fa-pencil-alt edit-icon"></i>
            </p>
        </div>
    </div>
    <div class="row">
        @if($album->photos->isEmpty())
            <div class="col-md-12 text-center">
                <p>Album ini belum memiliki foto.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Tambahkan foto ke album Anda</a>
            </div>
        @else
            @foreach($album->photos as $photo)
            <div class="card-columns">
                <div class="card card-pin">
                    <a href="{{ route('photos.show', $photo->id) }}">
                        <img class="card-img" src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}">
                        <div class="overlay">
                            <h2 class="card-title title">{{ $photo->title }}</h2>
                            <div class="more">
                                <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> More
                            </div>
                        </div>                                        
                    </a>
                </div>
                <!-- Dropdown outside the card -->
                <div class="dropdown dropdown-container">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deletePhotoModal-{{ $photo->id }}">
                                <i class="fas fa-trash"></i> Hapus Foto
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


                                <!-- Modal Konfirmasi Hapus Foto -->
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
            @endforeach
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
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

    document.querySelectorAll('.editable').forEach(element => {
        element.addEventListener('click', function () {
            const type = element.id === 'album-title' ? 'title' : 'description';
            makeEditable(element, type);
        });
    });
});
</script>
@endsection