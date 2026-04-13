@extends('layouts.app')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/album-show.css') }}">
@endpush

@section('content')
<div class="container py-4">
    <div class="album-header">
        <!-- Title Section -->
        <div class="title-section">
            <div class="title-content">
                <h2 class="album-title editable-text" id="album-title" data-id="{{ $album->id }}">
                    {{ $album->name }}
                </h2>
            </div>
            @if(Auth::check() && Auth::id() === $album->user_id)
                <div class="edit-icon-container">
                    <i class="fas fa-pencil-alt edit-icon" data-target="album-title" data-type="title"></i>
                </div>
            @endif
        </div>
    
        <!-- Description Section -->
        <div class="description-section">
            <div class="description-content">
                <p class="album-description editable-text" 
                    id="album-description" 
                    data-id="{{ $album->id }}" 
                    data-full-text="{{ $album->description }}">
                    {{ \Illuminate\Support\Str::limit($album->description, 150, '...') }}
                </p>
            </div>
            @if(Auth::check() && Auth::id() === $album->user_id)
                <div class="edit-icon-container">
                    <i class="fas fa-pencil-alt edit-icon" data-target="album-description" data-type="description"></i>
                </div>
            @endif
        </div>

        <!-- Visibility Toggle -->
        @if(Auth::check() && Auth::id() === $album->user_id && Auth::user()->role === 'pro')
            <div class="visibility-toggle" id="visibility-toggle" data-id="{{ $album->id }}">
                <i class="fas {{ $album->status ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                <span class="visibility-text">{{ $album->status ? 'Publik' : 'Privat' }}</span>
            </div>
        @endif
    </div>

    @if($album->photos->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-images"></i>
            </div>
            <h4>Album ini masih kosong</h4>
            @if(Auth::check() && Auth::id() === $album->user_id)
                <p>Mulai dengan menambahkan foto pertama Anda untuk mengisi album ini</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambahkan Foto
                </a>
            @else
                <p>Album ini belum memiliki foto</p>
            @endif
        </div>
    @else
        <div class="photo-grid">
            @foreach($album->photos as $photo)
                <div class="photo-card">
                    <a href="{{ route('photos.show', $photo->id) }}">
                        <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" loading="lazy">
                        <div class="photo-overlay">
                            <h3 class="photo-title">{{ $photo->title }}</h3>
                            <div class="photo-date">{{ $photo->created_at->format('d M Y') }}</div>
                        </div>
                    </a>
                    
                    @if(Auth::check() && Auth::id() === $album->user_id)
                        <div class="photo-actions">
                            <button class="photo-menu-btn" data-photo-id="{{ $photo->id }}" data-album-id="{{ $album->id }}">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.albumShowConfig = {
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};
</script>
<script src="{{ asset('js/pages/album-show.js') }}"></script>
@endpush