@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="card-title mb-4">Edit Foto</h3>
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <form class="forms-sample" method="POST" action="{{ route('photos.update', $photo->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="title" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ $photo->title }}" required>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required>{{ $photo->description }}</textarea>
                </div>
                <div class="form-group">
                    <label for="hashtags" class="form-label">Hashtags</label>
                    <input type="text" class="form-control" id="hashtags" name="hashtags" value="{{ implode(', ', json_decode($photo->hashtags)) }}" required>
                </div>
                @if (Auth::user()->role === 'pro')
                <div class="form-group">
                    <label for="status" class="form-label">Visibilitas</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="1" {{ $photo->status === '1' ? 'selected' : '' }}>Publik</option>
                        <option value="0" {{ $photo->status === '0' ? 'selected' : '' }}>Privat</option>
                    </select>
                </div>
                @endif
                <button type="submit" class="btn btn-success text-white me-2">Reset Password</button>
            </form>
          </div>
        </div>
    </div>
</div>
@endsection