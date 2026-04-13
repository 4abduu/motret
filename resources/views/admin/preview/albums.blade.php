@extends('layouts.app')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-preview-albums.css') }}">
@endpush

@section('content')
<div class="container py-4">
    <div class="album-header">
        <!-- Title Section -->
        <div class="title-section">
            <div class="title-content">
                <h2 class="album-title">
                    {{ $album->name }}
                </h2>
            </div>
        </div>
    
        <!-- Description Section -->
        <div class="description-section">
            <div class="description-content">
                <p class="album-description" 
                    id="album-description" 
                    data-full-text="{{ $album->description }}">
                    {{ \Illuminate\Support\Str::limit($album->description, 150, '...') }}
                    @if(strlen($album->description) > 150)
                        <span class="read-more">...Lainnya</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    @if($album->photos->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-images"></i>
            </div>
            <h4>Album ini masih kosong</h4>
            <p>Belum ada foto yang ditambahkan ke album ini.</p>
        </div>
    @else
        <div class="photo-grid">
            @foreach($album->photos as $photo)
                <div class="photo-card">
                    <a href="{{ asset('storage/' . $photo->path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" loading="lazy">
                        <div class="photo-overlay">
                            <h3 class="photo-title">{{ $photo->title }}</h3>
                            <div class="photo-date">{{ $photo->created_at->format('d M Y') }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/admin-preview-albums.js') }}"></script>
@endpush