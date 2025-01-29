@extends('layouts.app')

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
        @foreach($album->photos as $photo)
            <div class="col-md-4 mb-4">
                <div class="card">
                        <a href="{{ route('photos.show', $photo->id) }}">
                    <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="{{ $photo->title }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $photo->title }}</h5>
                        <p class="card-text">{{ $photo->description }}</p>
                    </div>
                        </a>
                </div>
            </div>
        @endforeach
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