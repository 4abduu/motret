@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Edit Foto</h2>
    <form method="POST" action="{{ route('photos.update', $photo->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Judul</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $photo->title }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3" required>{{ $photo->description }}</textarea>
        </div>
        <div class="mb-3">
            <label for="hashtags" class="form-label">Hashtags</label>
            <input type="text" class="form-control" id="hashtags" name="hashtags" value="{{ implode(', ', json_decode($photo->hashtags)) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection