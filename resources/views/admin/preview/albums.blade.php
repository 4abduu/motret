@extends('layouts.app')

@push('link')
<style>
/* Gunakan CSS yang sama seperti di show.blade.php */
:root {
    --primary-color: #32bd40;
    --primary-dark: #2a9d36;
    --primary-light: #e8f5e9;
    --success-color: #2a9d36;
    --danger-color: #e63946;
    --dark-color: #1a3e1f;
    --light-color: #f1f8e9;
    --transition-speed: 0.3s;
}

.container {
    padding: 2rem 15px;
}

.album-header {
    position: relative;
    padding: 2rem;
    margin-bottom: 2.5rem;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
    overflow: hidden;
}

.album-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    transform: rotate(30deg);
    pointer-events: none;
}

.title-section, .description-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 1rem;
}

.title-content, .description-content {
    flex: 1;
    min-width: 0;
    word-break: break-word;
    position: relative;
    max-width: 40%;
}

.album-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    transition: all var(--transition-speed);
    cursor: default;
    position: relative;
    line-height: 1.2;
}

.album-description {
    font-size: 1.15rem;
    opacity: 0.9;
    margin: 0;
    line-height: 1.6;
    cursor: pointer;
    position: relative;
}

.photo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.75rem;
    margin-bottom: 3rem;
}

.photo-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    transition: all var(--transition-speed);
    aspect-ratio: 1;
    background: #f8f9fa;
    animation: fadeIn 0.5s ease forwards;
    opacity: 0;
}

.photo-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.photo-card:hover img {
    transform: scale(1.05);
}

.photo-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    padding: 1.25rem;
    color: white;
    z-index: 2;
    transform: translateY(100%);
    opacity: 0;
    transition: all var(--transition-speed);
}

.photo-card:hover .photo-overlay {
    transform: translateY(0);
    opacity: 1;
}

.photo-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 1.1rem;
}

.photo-date {
    font-size: 0.85rem;
    opacity: 0.8;
}
</style>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle "Lainnya" toggle for description
    document.addEventListener('click', function(e) {
        const readMoreBtn = e.target.closest('.read-more');
        if (!readMoreBtn) return;

        const descElement = readMoreBtn.closest('.album-description');
        const fullText = descElement.getAttribute('data-full-text');
        const isExpanded = readMoreBtn.textContent.includes('Sembunyikan');

        if (isExpanded) {
            descElement.innerHTML = `
                ${fullText.substring(0, 150)}<span class="read-more">...Lainnya</span>
            `;
        } else {
            descElement.innerHTML = `
                ${fullText}<span class="read-more">Sembunyikan</span>
            `;
        }
    });
});
</script>
@endpush