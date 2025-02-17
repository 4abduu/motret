@extends('layouts.app')

@section('title', 'Upload Photo')

@section('content')
    <div class="container">
        <h1 class="my-4">Upload Foto</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('photos.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="photo" class="form-label">Photo</label>
                <input type="file" name="photo" class="form-control" id="photo" required>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Judul</label>
                <input type="text" name="title" class="form-control" id="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" id="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="hashtags" class="form-label">Hashtags (tanpa #)</label>
                <input type="text" name="hashtags" class="form-control" id="hashtags">
            </div>
            @if (Auth::user()->role == 'pro')
            <div class="mb-3">
                <label for="premium" class="form-label">Premium</label>
                <select class="form-control" id="premium" name="premium" required>
                    <option value="0">Biasa</option>
                    <option value="1">Premium</option>
                </select>
            </div>
            @endif
            <button type="submit" class="btn btn-success">Upload</button>
        </form>
    </div>
@endsection