@extends('layouts.app')

@section('content')

@push('link')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script>
    var isGuest = @json(Auth::check() ? false : true);
</script>
<style>
    /* CSS Variables */
    :root {
        --primary-color: #32bd40;
        --primary-hover: #2aa336;
        --secondary-color: #f5f5f5;
        --text-color: #333;
        --text-muted: #6c757d;
        --border-color: #eee;
        --shadow-sm: 0 2px 10px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 15px rgba(0,0,0,0.1);
        --shadow-lg: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    /* Base Styles */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        overflow: hidden;
        border-radius: 15px;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-success {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }
    
    /* Photo Card */
    .photo-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .photo-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .photo-card img {
        transition: opacity 0.3s ease;
        height: 200px;
        object-fit: cover;
        width: 100%;
    }
    
    .photo-card:hover img {
        opacity: 0.9;
    }
    
    .photo-banned-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0,0,0,0.7);
        color: white;
        font-size: 14px;
    }
    
    /* Album Card */
    .album-card-container {
        position: relative;
        width: 100%;
        padding-top: 100%;
        overflow: hidden;
        border-radius: 8px 8px 0 0;
        background-color: var(--secondary-color);
    }
    
    .album-cover-grid {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 2px;
    }
    
    .album-cover-main {
        grid-column: 1 / 3;
        grid-row: 1 / 2;
        overflow: hidden;
    }
    
    .album-cover-secondary {
        overflow: hidden;
    }
    
    .album-cover-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s ease;
    }
    
    .album-cover-link:hover .album-cover-img {
        transform: scale(1.03);
    }
    
    .album-cover-placeholder {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ddd;
        font-size: 24px;
    }
    
    /* Album Details */
    .album-details {
        border-top: 1px solid var(--border-color);
        padding: 10px;
    }
    
    .album-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-color);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 4px;
    }
    
    .album-desc {
        font-size: 12px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 8px;
    }
    
    /* Dropdown Styles - Fixed */
    .dropdown-toggle::after {
        display: none !important;
    }
    
    .dropdown-menu {
        min-width: 160px;
        font-size: 14px;
        box-shadow: var(--shadow-sm);
        border: none;
        z-index: 1050 !important; /* Increased z-index */
        transform-origin: top left !important;
    }
    
    /* Mobile Actions */
    .mobile-actions, .album-mobile-actions {
        position: absolute;
        bottom: 8px;
        right: 8px;
        z-index: 10;
        background-color: rgba(0,0,0,0.5);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: auto;
    }
    
    .mobile-actions .dropdown-toggle,
    .album-mobile-actions .dropdown-toggle {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin: 0;
        border: none;
        background: transparent;
    }
    
    .mobile-actions .bi-three-dots-vertical,
    .album-mobile-actions .bi-three-dots-vertical {
        color: white;
        font-size: 16px;
    }
    
    /* Desktop Actions */
    .dropdown-desktop .bi-three-dots-vertical {
        color: var(--text-color);
        font-size: 16px;
    }
    
    /* Dropdown Items */
    .dropdown-item {
        padding: 8px 12px;
        transition: background-color 0.2s;
    }
    
    .dropdown-item i {
        width: 18px;
        text-align: center;
        margin-right: 8px;
    }

    .dropdown-menu.show {
        display: block;
    }

    .card, .photo-card, .album-card-container {
        overflow: visible !important;
    }
    /* Premium Badge */
    .premium-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background-color: gold;
        color: #333;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
        z-index: 1;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 767.98px) {
        .dropdown-desktop {
            display: none !important;
        }
    
        .col-6 {
            padding: 0 4px;
        }
        
        .mb-3 {
            margin-bottom: 8px !important;
        }
        
        .photo-card .card-body {
            display: none;
            margin-bottom: 16px;
        }
        
        .photo-card img {
            height: auto;
            width: 100%;
        }
        
        .album-details {
            display: none;
        }
    
        .row-foto {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }
    }
    
    @media (min-width: 768px) {
        .mobile-actions,
        .album-mobile-actions {
            display: none !important;
        }
        
        .album-card-container {
            border-radius: 8px;
        }
        
        .album-details {
            display: block;
        }
        
        .photo-card .card-body {
            display: block;
        }
    }
    
    /* SweetAlert2 Mobile Styles */
    .swal-mobile-container {
        width: auto !important;
        max-width: 300px;
    }
    
    .swal-mobile-popup {
        font-size: 14px !important;
        margin: 0.5rem !important;
    }
    
    .swal-mobile-title {
        font-size: 16px !important;
        margin-bottom: 0.5rem !important;
    }
    
    @media (max-width: 480px) {
        .swal2-popup {
            width: 80% !important;
            font-size: 13px !important;
            padding: 0.75rem !important;
        }
        
        .swal2-title {
            font-size: 14px !important;
        }
        
        .swal2-content {
            font-size: 13px !important;
        }
    }
</style>
@endpush

<div class="d-flex justify-content-center">
    <div class="col-md-4 grid-margin grid-margin-md-0 stretch-card">
        <div class="card shadow-lg">
            <div class="card-body text-center">
                <div>
                    <img src="{{ $user->profile_photo_url }}" class="img-lg rounded-circle mb-2" alt="profile image" />
                    <h4>{{ $user->name }} 
                        @if($user->verified)
                        <i class="ti-crown" style="color: gold;" title="Verified User"></i>
                        @endif
                        @if ($user->role === 'pro')
                        <i class="ti-star" style="color: gold;" title="Professional User"></i>
                        @endif
                    </h4>
                    <p class="text-muted mb-0">{{ $user->username }}</p>
                </div>
                @if(Auth::id() === $user->id)
                    <p class="mt-2 card-text">Email: {{ $user->email }}</p>
                    <p>{{ $user->bio }}</p>
                    <p><a href="{{ $user->website }}" target="_blank">{{ $user->website }}</a></p>
                    <button class="btn btn-success btn-sm mt-3 mb-4 text-white" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profil</button>
                @else
                    <div id="follow-section">
                        @if(Auth::check())
                            <button id="follow-button" 
                                    class="btn {{ Auth::user()->isFollowing($user) ? 'btn-danger unfollow-button' : 'btn-primary follow-button' }} btn-sm mt-3 mb-4" 
                                    data-user-id="{{ $user->id }}"
                                    data-initial-state="{{ Auth::user()->isFollowing($user) ? 'following' : 'not-following' }}">
                                {{ Auth::user()->isFollowing($user) ? 'Unfollow' : 'Follow' }}
                            </button>
                            <button type="button" class="btn btn-link p-0 me-3" data-bs-toggle="modal" data-bs-target="#reportUserModal">
                                <i class="bi bi-flag text-danger"></i>
                            </button>
                        @else
                            <button class="btn btn-primary btn-sm mt-3 mb-4" onclick="window.location.href='{{ route('login') }}'">
                                Follow
                            </button>
                        @endif
                    </div>
                @endif
                <div class="border-top pt-3">
                    <div class="row">
                        <div class="col-6">
                            <h6 id="followers-count">{{ $user->followers()->count() }}</h6>
                            <p class="btn btn-link text-success" data-bs-toggle="modal" data-bs-target="#followersModal">Followers</p>
                        </div>
                        <div class="col-6">
                            <h6 id="following-count">{{ $user->following()->count() }}</h6>
                            <p class="btn btn-link text-success" data-bs-toggle="modal" data-bs-target="#followingModal">Following</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <ul class="nav nav-tabs nav-justified mt-3" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="photos-tab" data-bs-toggle="tab" href="#photos" role="tab" aria-controls="photos" aria-selected="true">Foto</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="albums-tab" data-bs-toggle="tab" href="#albums" role="tab" aria-controls="albums" aria-selected="false">Album</a>
        </li>
        @if(Auth::check() && $user->verified && ($hasSubscriptionPrice || Auth::id() === $user->id))
            <li class="nav-item">
                <a class="nav-link" id="subscription-tab" data-bs-toggle="tab" href="#subscription" role="tab" aria-controls="subscription" aria-selected="false">Langganan</a>
            </li>
        @endif
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Tab Foto -->
        <div class="tab-pane fade show active" id="photos" role="tabpanel" aria-labelledby="photos-tab">
            <h3 class="mt-5 mb-4">Foto yang Diunggah</h3>
            @if($photos->count() > 0)
            <div class="row">
                @foreach($photos as $photo)
                    @if($photo->banned && $photo->user_id !== Auth::id())
                        @continue
                    @endif
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100 photo-card position-relative">
                            @if($photo->banned && $photo->user_id === Auth::id())
                                <div class="card-body">
                                    <h5 class="card-title text-danger">Postingan ini telah dibanned.</h5>
                                    @foreach($photo->reports as $report)
                                        <p class="card-text"><strong>Alasan:</strong> {{ $report->reason }}</p>
                                    @endforeach
                                </div>
                            @else
                                <a href="{{ route('photos.show', $photo->id) }}" class="text-decoration-none">
                                    <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="{{ $photo->title }}" style="height: 200px; object-fit: cover;">
                                </a>
                                
                                <!-- Mobile Dropdown -->
                                <div class="mobile-actions d-md-none">
                                    <div class="dropdown dropup">
                                        <button class="dropdown-toggle" type="button" id="mobilePhotoDropdown-{{ $photo->id }}" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper="static">
                                            <i class="bi bi-three-dots-vertical text-white"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobilePhotoDropdown-{{ $photo->id }}">
                                            @if(Auth::check() && Auth::id() === $photo->user_id)
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('photos.edit', $photo->id) }}">
                                                        <i class="bi bi-pencil me-2"></i> Edit Foto
                                                    </a>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center delete-photo-btn" data-id="{{ $photo->id }}" data-title="{{ $photo->title }}">
                                                        <i class="bi bi-trash me-2"></i> Hapus Foto
                                                    </button>
                                                </li>
                                            @elseif(Auth::check())
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center" onclick="copyToClipboard('{{ route('photos.show', $photo->id) }}')">
                                                        <i class="bi bi-share me-2"></i> Bagikan
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#reportPhotoModal-{{ $photo->id }}">
                                                        <i class="bi bi-flag me-2"></i> Laporkan
                                                    </button>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="download-button" id="downloadForm">
                                                        @csrf
                                                        <button type="button" class="dropdown-item d-flex align-items-center w-100" id="downloadButton">
                                                            <i class="bi bi-download me-2"></i> Download
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center" onclick="copyToClipboard('{{ route('photos.show', $photo->id) }}')">
                                                        <i class="bi bi-share me-2"></i> Bagikan
                                                    </button>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="download-button" id="downloadForm">
                                                        @csrf
                                                        <button type="button" class="dropdown-item d-flex align-items-center w-100" id="downloadButton">
                                                            <i class="bi bi-download me-2"></i> Download
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="card-title mb-2">{{ $photo->title }}</h5>
                                        
                                        <!-- Desktop Dropdown -->
                                        <div class="dropdown dropup d-none d-md-block">
                                            <button class="btn btn-link p-0 dropdown-toggle" type="button" id="desktopPhotoDropdown-{{ $photo->id }}" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper="static">
                                                <i class="bi bi-three-dots-vertical text-dark"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="desktopPhotoDropdown-{{ $photo->id }}">
                                                @if(Auth::check() && Auth::id() === $photo->user_id)
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('photos.edit', $photo->id) }}">
                                                            <i class="bi bi-pencil me-2"></i> Edit Foto
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item d-flex align-items-center delete-photo-btn" data-id="{{ $photo->id }}" data-title="{{ $photo->title }}">
                                                            <i class="bi bi-trash me-2"></i> Hapus Foto
                                                        </button>
                                                    </li>
                                                @elseif(Auth::check())
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center" onclick="copyToClipboard('{{ route('photos.show', $photo->id) }}')">
                                                            <i class="bi bi-share me-2"></i> Bagikan
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#reportPhotoModal-{{ $photo->id }}">
                                                            <i class="bi bi-flag me-2"></i> Laporkan
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="download-button" id="downloadForm-{{ $photo->id }}">
                                                            @csrf
                                                            <button type="button" class="dropdown-item d-flex align-items-center w-100 download-btn" data-id="{{ $photo->id }}">
                                                                <i class="bi bi-download me-2"></i> Download
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center" onclick="copyToClipboard('{{ route('photos.show', $photo->id) }}')">
                                                            <i class="bi bi-share me-2"></i> Bagikan
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="download-button" id="downloadForm-{{ $photo->id }}">
                                                            @csrf
                                                            <button type="button" class="dropdown-item d-flex align-items-center w-100 download-btn" data-id="{{ $photo->id }}">
                                                                <i class="bi bi-download me-2"></i> Download
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="card-text">{{ $photo->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
        
                    <!-- Modal Report Photo -->
                    <div class="modal fade" id="reportPhotoModal-{{ $photo->id }}" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel-{{ $photo->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reportModalLabel-{{ $photo->id }}">Laporkan Foto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="reportForm-photo-{{ $photo->id }}" method="POST" action="{{ route('photo.report', $photo->id) }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="reason">Alasan Melaporkan</label>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="reason" id="reason1-{{ $photo->id }}" value="Konten Dewasa / Tidak Pantas">
                                                    Konten Dewasa / Tidak Pantas
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="reason" id="reason2-{{ $photo->id }}" value="Mengandung Kekerasan">
                                                    Mengandung Kekerasan
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="reason" id="reason3-{{ $photo->id }}" value="Pelanggaran hak cipta">
                                                    Pelanggaran hak cipta
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="reason" id="reason4-{{ $photo->id }}" value="Informasi Pribadi Tanpa Izin">
                                                    Informasi Pribadi Tanpa Izin
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="reason" id="reason5-{{ $photo->id }}" value="Spam / Promosi Berlebihan">
                                                    Spam / Promosi Berlebihan
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="reason" id="reason6-{{ $photo->id }}" value="Lainnya">
                                                    Lainnya
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3" id="description-group-photo-{{ $photo->id }}" style="display: none;">
                                            <label for="description-photo-{{ $photo->id }}">Alasan</label>
                                            <textarea class="form-control" id="description-photo-{{ $photo->id }}" name="description" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger mt-3">Laporkan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <!-- Delete Photo Confirmation Modal -->
                    <div class="modal fade" id="deletePhotoModal-{{ $photo->id }}" tabindex="-1" aria-labelledby="deletePhotoModalLabel-{{ $photo->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deletePhotoModalLabel-{{ $photo->id }}">Konfirmasi Hapus Foto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Apakah Anda yakin ingin menghapus foto "{{ $photo->title }}"?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form method="POST" action="{{ route('photos.destroy', $photo->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-image" style="font-size: 3rem; color: #6c757d;"></i>
                <p class="mt-3 text-muted">@if(Auth::id() === $user->id) Anda belum mengunggah foto @else Pengguna ini belum mengunggah foto @endif</p>
                @if(Auth::id() === $user->id)
                    <a href="{{ route('photos.create') }}" class="btn btn-success mt-2">
                        <i class="bi bi-plus"></i> Unggah Foto Pertama Anda
                    </a>
                @endif
            </div>
        @endif
        </div>

        <!-- Tab Album -->
        <div class="tab-pane fade" id="albums" role="tabpanel" aria-labelledby="albums-tab">
            <h3 class="mt-5 mb-3">Album</h3>
            @if(Auth::id() === $user->id)
                <button type="button" class="btn btn-success btn-sm text-white mb-3" data-bs-toggle="modal" data-bs-target="#createAlbumModal" id="mainCreateAlbumButton" style="{{ $albums->count() === 0 ? 'display: none;' : '' }}">
                    <i class="bi bi-plus"></i> Buat Album
                </button>
            @endif
            
            @if($albums->count() > 0)
            <div class="row" id="albumsContainer">
                @foreach($albums as $album)
                    <div class="col-6 col-md-4 col-lg-3 mb-3" id="album-{{ $album->id }}">
                        <div class="card shadow-sm h-100 position-relative">
                            <div class="album-card-container">
                                <a href="{{ route('albums.show', $album->id) }}" class="album-cover-link">
                                    @if($album->photos->count() > 0)
                                        <div class="album-cover-grid">
                                            <div class="album-cover-main">
                                                <img src="{{ asset('storage/' . $album->photos[0]->path) }}" 
                                                    class="album-cover-img" 
                                                    alt="{{ $album->photos[0]->title }}"
                                                    loading="lazy">
                                            </div>
                                            @if($album->photos->count() > 1)
                                            <div class="album-cover-secondary">
                                                <img src="{{ asset('storage/' . $album->photos[1]->path) }}" 
                                                    class="album-cover-img" 
                                                    alt="{{ $album->photos[1]->title }}"
                                                    loading="lazy">
                                            </div>
                                            @endif
                                            @if($album->photos->count() > 2)
                                            <div class="album-cover-secondary">
                                                <img src="{{ asset('storage/' . $album->photos[2]->path) }}" 
                                                    class="album-cover-img" 
                                                    alt="{{ $album->photos[2]->title }}"
                                                    loading="lazy">
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="album-cover-placeholder">
                                            <i class="bi bi-images text-muted"></i>
                                        </div>
                                    @endif
                                </a>
                            </div>
                            
                            <!-- Mobile Dropdown -->
                            <div class="album-mobile-actions d-md-none">
                                <div class="dropdown dropup">
                                    <button class="dropdown-toggle" type="button" id="mobileAlbumDropdown-{{ $album->id }}" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper="static">
                                        <i class="bi bi-three-dots-vertical text-white"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileAlbumDropdown-{{ $album->id }}">
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center" onclick="copyToClipboard('{{ route('albums.show', $album->id) }}')">
                                                <i class="bi bi-share me-2"></i> Bagikan
                                            </button>
                                        </li>
                                        @if(Auth::id() === $album->user_id)
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#editAlbumModal-{{ $album->id }}">
                                                    <i class="bi bi-pencil me-2"></i> Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center delete-album-btn" 
                                                        data-id="{{ $album->id }}" 
                                                        data-name="{{ $album->name }}">
                                                    <i class="bi bi-trash me-2"></i> Hapus Album
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Desktop Dropdown -->
                            <div class="album-details d-none d-md-block p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="album-title mb-1">{{ $album->name }}</h6>
                                        @if($album->description)
                                            <p class="album-desc text-muted small mb-1">{{ Str::limit($album->description, 40) }}</p>
                                        @endif
                                    </div>
                                    <div class="dropdown dropup">
                                        <button class="btn btn-link p-0 dropdown-toggle" type="button" id="desktopAlbumDropdown-{{ $album->id }}" data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper="static">
                                            <i class="bi bi-three-dots-vertical text-dark"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="desktopAlbumDropdown-{{ $album->id }}">
                                            <li>
                                                <button class="dropdown-item d-flex align-items-center" onclick="copyToClipboard('{{ route('albums.show', $album->id) }}')">
                                                    <i class="bi bi-share me-2"></i> Bagikan
                                                </button>
                                            </li>
                                            @if(Auth::id() === $album->user_id)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#editAlbumModal-{{ $album->id }}">
                                                        <i class="bi bi-pencil me-2"></i> Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center delete-album-btn" 
                                                        data-id="{{ $album->id }}" 
                                                        data-name="{{ $album->name }}">
                                                        <i class="bi bi-trash me-2"></i> Hapus Album
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Edit Album -->
                    <div class="modal fade" id="editAlbumModal-{{ $album->id }}" tabindex="-1" aria-labelledby="editAlbumModalLabel-{{ $album->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editAlbumModalLabel-{{ $album->id }}">Edit Album</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editAlbumForm-{{ $album->id }}" 
                                        class="edit-album-form" 
                                        data-id="{{ $album->id }}"
                                        method="POST" 
                                        action="{{ route('albums.update', $album->id) }}">
                                        @method('PUT')
                                        <div class="form-group mb-3">
                                            <label for="albumName" class="form-label">Nama Album</label>
                                            <input type="text" class="form-control" name="name" value="{{ $album->name }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="albumDescription" class="form-label">Deskripsi</label>
                                            <textarea class="form-control" name="description" rows="3">{{ $album->description }}</textarea>
                                        </div>
                                        @if (Auth::check() && Auth::user()->role === 'pro')
                                            <div class="form-group mb-3">
                                                <label for="status" class="form-label">Visibilitas</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="1" {{ $album->status == 1 ? 'selected' : '' }}>Publik</option>
                                                    <option value="0" {{ $album->status == 0 ? 'selected' : '' }}>Privat</option>
                                                </select>
                                            </div>
                                        @endif
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-folder-x" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3 text-muted">@if(Auth::id() === $user->id) Anda belum memiliki album @else Pengguna ini belum memiliki album @endif</p>
                    @if(Auth::id() === $user->id)
                        <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#createAlbumModal">
                            <i class="bi bi-plus"></i> Buat Album Pertama Anda
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Tab Langganan -->
        <div class="tab-pane fade" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
            @if(Auth::check() && $user->verified)
                @if(Auth::id() === $user->id)
                    <!-- Creator View -->
                    <div class="creator-subscription-view">
                        <h3 class="mt-5 mb-4">Langganan Anda</h3>
                        
                        @if(!$hasSubscriptionPrice)
                            <!-- Empty State - No Subscription Setup -->
                            <div class="text-center py-5 bg-light rounded-3">
                                <i class="bi bi-credit-card-2-front" style="font-size: 3rem; color: #6c757d;"></i>
                                <h4 class="mt-3">Belum Ada Langganan</h4>
                                <p class="text-muted">Atur paket langganan untuk memulai monetisasi konten Anda</p>
                                <button class="btn btn-warning mt-3" onclick="window.location.href='{{ route('subscription.manage') }}'">
                                    <i class="bi bi-gear me-2"></i> Atur Langganan
                                </button>
                            </div>
                        @else
                            <!-- Creator Dashboard -->
                            <div class="creator-dashboard">
                                <!-- Action Buttons -->
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <button class="btn btn-warning" onclick="window.location.href='{{ route('subscription.manage') }}'">
                                            <i class="bi bi-pencil me-2"></i> Kelola Langganan
                                        </button>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subscribersModal">
                                            <i class="bi bi-people me-2"></i> Lihat Pelanggan
                                        </button>
                                        @if($premiumPhotos->count() > 1)
                                        <button class="btn btn-success" onclick="window.location.href='{{ route('photos.create') }}'">
                                            <i class="bi bi-plus-circle me-2"></i> Tambah Konten Premium
                                        </button>
                                        @endif
                                    </div>
                                        <button class="btn btn-danger" onclick="window.location.href='{{ route('withdrawal.balance') }}'">
                                            <i class="bi bi-cash-stack me-2"></i> Tarik Saldo
                                        </button>
                                </div>
        
                                <!-- Premium Content Section -->
                                <div class="premium-content-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5>Konten Premium Anda</h5>
                                        <span class="badge bg-primary">{{ $premiumPhotos->count() }} Foto</span>
                                    </div>
        
                                    @if($premiumPhotos->count() > 0)
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                            @foreach($premiumPhotos as $photo)
                                                <div class="col">
                                                    <div class="card h-100 shadow-sm">
                                                        <a href="{{ route('photos.show', $photo->id) }}" class="position-relative">
                                                            <img src="{{ asset('storage/' . $photo->path) }}" 
                                                                 class="card-img-top img-fluid" 
                                                                 alt="Premium Photo" 
                                                                 style="height: 200px; object-fit: cover;">
                                                            <span class="position-absolute top-0 end-0 m-2 badge bg-success">
                                                                <i class="bi bi-star-fill me-1"></i> Premium
                                                            </span>
                                                        </a>
                                                        <div class="card-body">
                                                            <h6 class="card-title">{{ $photo->title }}</h6>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">
                                                                    {{ $photo->created_at->format('d M Y') }}
                                                                </small>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-link p-0" type="button" 
                                                                            id="photoDropdown-{{ $photo->id }}" 
                                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="bi bi-three-dots-vertical"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="photoDropdown-{{ $photo->id }}">
                                                                        <li>
                                                                            <a class="dropdown-item" href="{{ route('photos.edit', $photo->id) }}">
                                                                                <i class="bi bi-pencil me-2"></i> Edit
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <button class="dropdown-item text-danger delete-photo-btn" 
                                                                                    data-id="{{ $photo->id }}">
                                                                                <i class="bi bi-trash me-2"></i> Hapus
                                                                            </button>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5 bg-light rounded-3">
                                            <i class="bi bi-images" style="font-size: 3rem; color: #6c757d;"></i>
                                            <h5 class="mt-3">Belum Ada Konten Premium</h5>
                                            <p class="text-muted">Tambahkan foto eksklusif untuk pelanggan Anda</p>
                                            <button class="btn btn-success mt-2" onclick="window.location.href='{{ route('photos.create') }}'">
                                                <i class="bi bi-plus-circle me-2"></i> Buat Konten Premium
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Subscriber View -->
                    <div class="subscriber-view">
                        <h3 class="mt-5 mb-4">Langganan</h3>
                        @if(Auth::user()->subscriptions($user->id))
                            <!-- Already Subscribed View -->
                            <div class="subscription-active">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <button class="btn btn-outline-success btn-sm" 
                                                onclick="window.location.href='{{ route('subscription.options', ['username' => $user->username]) }}'">
                                            <i class="bi bi-arrow-repeat me-1"></i> Perpanjang
                                        </button>
                                    </div>
        
                                <!-- Premium Content -->
                                <div class="premium-content">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5>Konten Eksklusif</h5>
                                        <span class="badge bg-primary">{{ $premiumPhotos->count() }} Foto</span>
                                    </div>
        
                                    @if($premiumPhotos->count() > 0)
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                            @foreach($premiumPhotos as $photo)
                                                <div class="col">
                                                    <div class="card h-100 shadow-sm">
                                                        <a href="{{ route('photos.show', $photo->id) }}" class="position-relative">
                                                            <img src="{{ asset('storage/' . $photo->path) }}" 
                                                                 class="card-img-top img-fluid" 
                                                                 alt="Premium Photo" 
                                                                 style="height: 200px; object-fit: cover;">
                                                            <span class="position-absolute top-0 end-0 m-2 badge bg-success">
                                                                <i class="bi bi-star-fill me-1"></i> Eksklusif
                                                            </span>
                                                        </a>
                                                        <div class="card-body">
                                                            <h6 class="card-title">{{ $photo->title }}</h6>
                                                            <p class="card-text small text-muted">{{ Str::limit($photo->description, 60) }}</p>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">
                                                                    {{ $photo->created_at->format('d M Y') }}
                                                                </small>
                                                                <span class="badge bg-secondary">
                                                                    {{ $photo->downloads }} <i class="bi bi-download ms-1"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5 bg-light rounded-3">
                                            <i class="bi bi-images" style="font-size: 3rem; color: #6c757d;"></i>
                                            <h5 class="mt-3">Belum Ada Konten</h5>
                                            <p class="text-muted">Pengguna ini belum mengunggah konten eksklusif</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- Not Subscribed View -->
                            <div class="subscription-options">
                                <div class="row">
                                    <div class="col-lg-8 mx-auto">
                                        <div class="card shadow-sm mb-4">
                                            <div class="card-body text-center">
                                                <i class="bi bi-lock-fill" style="font-size: 3rem; color: #6c757d;"></i>
                                                <h4 class="mt-3">Konten Eksklusif</h4>
                                                <p class="text-muted">Berlangganan untuk mengakses semua foto premium dari {{ $user->name }}</p>
                                                
                                                <div class="pricing-options mt-4">
                                                    <div class="row g-3">
                                                        @foreach($subscriptionPlans as $plan)
                                                            <div class="col-md-4">
                                                                <div class="card h-100 {{ $plan['recommended'] ? 'border-primary' : '' }}">
                                                                    <div class="card-body text-center">
                                                                        @if($plan['recommended'])
                                                                            <span class="badge bg-primary mb-2">Rekomendasi</span>
                                                                        @endif
                                                                        <h5 class="card-title">{{ $plan['name'] }}</h5>
                                                                        <h3 class="my-3">Rp{{ number_format($plan['price'], 0, ',', '.') }}</h3>
                                                                        <p class="small text-muted">per {{ $plan['duration'] }}</p>
                                                                        <ul class="list-unstyled small text-start my-3">
                                                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Akses semua foto eksklusif</li>
                                                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Download konten premium</li>
                                                                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Dukungan prioritas</li>
                                                                        </ul>
                                                                        <a href="{{ route('subscription.subscribe', ['username' => $user->username, 'plan' => $plan['id']]) }}" 
                                                                           class="btn {{ $plan['recommended'] ? 'btn-primary' : 'btn-outline-primary' }} w-100">
                                                                            Langganan Sekarang
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-4 small text-muted">
                                                    <p><i class="bi bi-info-circle me-2"></i> Pembayaran aman melalui sistem kami</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <!-- Not Verified or Not Available -->
                <div class="text-center py-5">
                    <i class="bi bi-person-x" style="font-size: 3rem; color: #6c757d;"></i>
                    <h5 class="mt-3">
                        @if(Auth::id() === $user->id)
                            Fitur Langganan Belum Aktif
                        @else
                            Langganan Tidak Tersedia
                        @endif
                    </h5>
                    <p class="text-muted">
                        @if(Auth::id() === $user->id)
                            Aktifkan fitur langganan untuk monetisasi konten Anda
                        @else
                            Pengguna ini belum mengaktifkan fitur langganan
                        @endif
                    </p>
                    @if(Auth::id() === $user->id)
                        <button class="btn btn-warning mt-2" onclick="window.location.href='{{ route('subscription.manage') }}'">
                            <i class="bi bi-gear me-2"></i> Aktifkan Sekarang
                        </button>
                    @endif
                </div>
            @endif
        </div>
</div>
</div>

<!-- Modal Daftar Langganan -->
<div class="modal fade" id="subscribersModal" tabindex="-1" role="dialog" aria-labelledby="subscribersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscribersModalLabel">Daftar Langganan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    @foreach($subscribers as $subscriber)
                        @if(isset($subscriber->user->username))
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('user.showProfile', ['username' => $subscriber->user->username]) }}" class="text-decoration-none">
                                    <b>{{ $subscriber->user->username }}</b>
                                </a>
                                @if(Auth::check() && Auth::id() !== $subscriber->user_id)
                                    <button 
                                        class="btn btn-sm {{ Auth::user()->isFollowing($subscriber->user) ? 'btn-danger unfollow-button' : 'btn-success follow-button' }}" 
                                        data-user-id="{{ $subscriber->user->id }}">
                                        {{ Auth::user()->isFollowing($subscriber->user) ? 'Unfollow' : 'Follow' }}
                                    </button>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
@if(Auth::id() === $user->id)
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editProfileForm" 
                action="{{ route('user.updateProfile') }}" 
                enctype="multipart/form-data" 
                method="POST">
            @csrf
            @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" id="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" id="bio" rows="3">{{ $user->bio }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="website" class="form-label">Website</label>
                        <input type="text" name="website" class="form-control" id="website" value="{{ $user->website }}">
                    </div>
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Foto Profil</label>
                        <input type="file" name="profile_photo" class="form-control" id="profile_photo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success text-white">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Buat Album -->
@if(Auth::id() === $user->id)
    <div class="modal fade" id="createAlbumModal" tabindex="-1" role="dialog" aria-labelledby="createAlbumModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="createAlbumForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAlbumModalLabel">Buat Album Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="albumName" class="form-label">Nama Album</label>
                            <input type="text" class="form-control" id="albumName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="albumDescription" class="form-label">Deskripsi Album</label>
                            <textarea class="form-control" id="albumDescription" name="description" rows="3"></textarea>
                        </div>
                        @if (Auth::check() && Auth::user()->role === 'pro')
                            <div class="mb-3">
                                <label for="albumStatus" class="form-label">Visibilitas</label>
                                <select class="form-select" id="albumStatus" name="status" required>
                                    <option value="1">Publik</option>
                                    <option value="0">Privat</option>
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Buat Album</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Modal Followers -->
<div class="modal fade" id="followersModal" tabindex="-1" role="dialog" aria-labelledby="followersModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followersModalLabel">Followers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="followers-list">
                    @foreach($user->followers as $follower)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.showProfile', $follower->username) }}" style="color: black;"><b>{{ $follower->username }}</b></a>
                            @if(Auth::check() && Auth::id() !== $follower->id)
                                <button 
                                    class="btn btn-sm {{ Auth::user()->isFollowing($follower) ? 'btn-danger unfollow-button' : 'btn-success follow-button' }}" 
                                    data-user-id="{{ $follower->id }}">
                                    {{ Auth::user()->isFollowing($follower) ? 'Unfollow' : 'Follow' }}
                                </button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Following -->
<div class="modal fade" id="followingModal" tabindex="-1" role="dialog" aria-labelledby="followingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followingModalLabel">Following</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="following-list">
                    @foreach($user->following as $following)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('user.showProfile', $following->username) }}" style="color: black;"><b>{{ $following->username }}</b></a>
                            @if(Auth::check() && Auth::id() !== $following->id)
                                <button 
                                    class="btn btn-sm {{ Auth::user()->isFollowing($following) ? 'btn-danger unfollow-button' : 'btn-success follow-button' }}" 
                                    data-user-id="{{ $following->id }}">
                                    {{ Auth::user()->isFollowing($following) ? 'Unfollow' : 'Follow' }}
                                </button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Report User -->
<div class="modal fade" id="reportUserModal" tabindex="-1" role="dialog" aria-labelledby="reportUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportUserModalLabel">Laporkan Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm-user-{{ $user->id }}" method="POST" action="{{ route('user.report', $user->id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="reason">Alasan Melaporkan</label>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="reason" id="reason1-user" value="Akun Palsu / Identitas Palsu">
                                Akun Palsu / Identitas Palsu
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="reason" id="reason2-user" value="Mengirim Pesan Tidak Pantas">
                                Mengirim Pesan Tidak Pantas
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="reason" id="reason3-user" value="Pelecehan / Bullying">
                                Pelecehan / Bullying
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="reason" id="reason4-user" value="Melanggar Aturan Komunitas">
                                Melanggar Aturan Komunitas
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="reason" id="reason5-user" value="Spam / Bot">
                                Spam / Bot
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="reason" id="reason6-user" value="Lainnya">
                                Lainnya
                            </label>
                        </div>
                    </div>
                    <div class="form-group mt-3" id="description-group-user" style="display: none;">
                        <label for="description-user">Alasan</label>
                        <textarea class="form-control" id="description-user" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger mt-3" id="submitReportUser">Laporkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
    
@endsection
    
@push('scripts')
<script>
    // Clipboard copy function
    function copyToClipboard(text, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        navigator.clipboard.writeText(text).then(() => {
            showSwalAlert('success', 'Berhasil!', 'Link berhasil disalin');
        }).catch(err => {
            console.error('Failed to copy:', err);
            showSwalAlert('error', 'Gagal', 'Gagal menyalin link');
        });
    }

    // Show SweetAlert notification
    function showSwalAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            background: icon === 'success' ? '#32bd40' : '',
            iconColor: '#fff',
            color: '#fff',
            timerProgressBar: true,
            width: '300px',
            padding: '0.5rem'
        });
    }

    // Initialize when DOM is loaded
    document.addEventListener("DOMContentLoaded", function() {
        // CSRF token and elements
        const token = '{{ csrf_token() }}';
        const currentUserId = "{{ Auth::id() }}";
        const profileUserId = "{{ $user->id }}";
        
        // Initialize Bootstrap dropdowns
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        const dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });

        let lastScrollPosition = window.scrollY || document.documentElement.scrollTop;

        // Restore scroll position after action
        function restoreScrollPosition() {
            window.scrollTo({
                top: lastScrollPosition,
                behavior: 'instant'
            });
        }

        // Update scroll position on window scroll
        window.addEventListener('scroll', () => {
            lastScrollPosition = window.scrollY || document.documentElement.scrollTop;
        });

        // Handle download
        function handleDownload(event) {
            event.preventDefault();
            const photoId = event.currentTarget.getAttribute('data-id');
            const downloadForm = document.getElementById(`downloadForm-${photoId}`);
            
            @if(!Auth::check())
                Swal.fire({
                    title: 'Login Required',
                    text: 'Downloads as a guest will be low quality. Log in for high-quality downloads.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Log In',
                    cancelButtonText: 'Continue as Guest',
                    cancelButtonColor: '#d33',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: 'Low Quality Download',
                            text: 'Since you are a guest, this download will be in low resolution.',
                            icon: 'warning',
                            confirmButtonText: 'Proceed',
                            cancelButtonText: 'Cancel',
                            showCancelButton: true,
                            reverseButtons: true
                        }).then((res) => {
                            if (res.isConfirmed) {
                                downloadForm.submit();
                            }
                        });
                    }
                });
            @else
                downloadForm.submit();
            @endif
        }

        // Attach event listeners to all download buttons
        document.querySelectorAll('.download-btn').forEach(btn => {
            btn.addEventListener('click', handleDownload);
        });

        // Report user modal logic
        const userReasonRadios = document.querySelectorAll('#reportUserModal input[name="reason"]');
        const userDescriptionGroup = document.getElementById('description-group-user');
        const userDescriptionInput = document.getElementById('description-user');

        if (userReasonRadios && userDescriptionGroup && userDescriptionInput) {
            userReasonRadios.forEach(radio => {
                radio.addEventListener("change", function() {
                    if (this.value === "Lainnya") {
                        userDescriptionGroup.style.display = "block";
                        userDescriptionInput.required = true;
                    } else {
                        userDescriptionGroup.style.display = "none";
                        userDescriptionInput.required = false;
                    }
                });
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        // Update follow button appearance
        function updateButtonAppearance(button, isFollowing) {
            if (!button) return;
            
            button.textContent = isFollowing ? 'Unfollow' : 'Follow';
            button.className = isFollowing 
                ? 'btn btn-danger btn-sm unfollow-button' 
                : 'btn btn-success btn-sm follow-button';
            button.style.marginTop = '16px';
            button.style.marginBottom = '24px';
        }

        // Handle follow/unfollow action
        async function handleFollowAction(button, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            const targetUserId = button.getAttribute('data-user-id');
            const isUnfollow = button.classList.contains('unfollow-button');
            const url = isUnfollow ? `/users/${targetUserId}/unfollow` : `/users/${targetUserId}/follow`;
            
            const originalState = {
                text: button.textContent,
                class: button.className,
                disabled: button.disabled
            };
            
            try {
                updateButtonAppearance(button, !isUnfollow);
                button.disabled = true;

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! Status: ${response.status}`);
                }

                document.querySelectorAll(`button[data-user-id="${targetUserId}"]`).forEach(btn => {
                    updateButtonAppearance(btn, data.action === 'follow');
                    btn.disabled = false;
                });

                if (targetUserId === profileUserId) {
                    document.querySelectorAll('#followers-count').forEach(el => {
                        el.textContent = data.followers_count;
                    });
                }

                if (currentUserId === profileUserId) {
                    document.querySelectorAll('#following-count').forEach(el => {
                        el.textContent = data.following_count;
                    });
                }

                await refreshModalContentSmoothly('#followersModal');
                await refreshModalContentSmoothly('#followingModal');
                
                return data;
            } catch (error) {
                console.error('Error:', error);
                button.textContent = originalState.text;
                button.className = originalState.class;
                button.disabled = originalState.disabled;
                return null;
            }
        }

        // Refresh modal content
        async function refreshModalContentSmoothly(modalId) {
            const modal = document.querySelector(modalId);
            if (!modal || !modal.classList.contains('show')) return;
            
            try {
                const modalBody = modal.querySelector('.modal-body');
                const scrollPosition = modalBody.scrollTop;
                
                const response = await fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                
                if (!response.ok) return;
                
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector(modalId + ' .modal-body')?.innerHTML;
                
                if (newContent) {
                    modalBody.innerHTML = newContent;
                    modalBody.scrollTop = scrollPosition;
                    initModalButtons(modal);
                }
            } catch (error) {
                console.error('Failed to refresh modal:', error);
            }
        }

        // Initialize modal buttons
        function initModalButtons(modal) {
            if (!modal) return;
            
            modal.querySelectorAll('.follow-button, .unfollow-button').forEach(btn => {
                const userId = btn.getAttribute('data-user-id');
                const isFollowing = btn.classList.contains('unfollow-button');
                
                const newBtn = btn.cloneNode(true);
                btn.replaceWith(newBtn);
                
                newBtn.addEventListener('click', (e) => handleFollowAction(newBtn, e));
                updateButtonAppearance(newBtn, isFollowing);
            });
        }

        // Initialize main follow button
        const followButton = document.getElementById('follow-button');
        if (followButton) {
            const initialState = followButton.getAttribute('data-initial-state') === 'following';
            updateButtonAppearance(followButton, initialState);
            followButton.addEventListener('click', (e) => handleFollowAction(followButton, e));
        }

        // Event delegation for follow buttons in modals
        document.addEventListener('click', function(e) {
            const button = e.target.closest('.follow-button, .unfollow-button');
            if (button) {
                handleFollowAction(button, e);
            }
        });

        // Initialize modals when shown
        ['#followersModal', '#followingModal'].forEach(modalId => {
            const modal = document.querySelector(modalId);
            if (modal) {
                modal.addEventListener('show.bs.modal', function() {
                    fetch(window.location.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.ok ? response.text() : Promise.reject())
                    .then(html => {
                        const parser
 = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.querySelector(modalId + ' .modal-body')?.innerHTML;
                        if (newContent) {
                            this.querySelector('.modal-body').innerHTML = newContent;
                            initModalButtons(this);
                        }
                    })
                    .catch(() => console.log('Failed to load modal data'));
                });
            }
        });

        // Initialize album edit buttons
            document.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.edit-album-btn');
                if (editBtn) {
                    e.preventDefault();
                    const albumId = editBtn.dataset.id;
                    openEditAlbumModal(albumId);
                }
            });

        // Create album function
        const createAlbumForm = document.getElementById('createAlbumForm');
        if (createAlbumForm) {
            // Reset form when modal is closed
            const createAlbumModal = document.getElementById('createAlbumModal');
            if (createAlbumModal) {
                createAlbumModal.addEventListener('hidden.bs.modal', function() {
                    createAlbumForm.reset();
                });
            }

            createAlbumForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                
                const formData = new FormData(createAlbumForm);
                
                try {
                    const response = await fetch('{{ route('albums.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) throw new Error(data.message || `HTTP error! Status: ${response.status}`);

                    if (data.success) {
                        // Close modal and clean backdrop
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createAlbumModal'));
                        modal.hide();
                        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());

                        // Clean backdrop manually
                        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = 'visible';

                        // Add new album to DOM
                        addNewAlbumToDOM(data.album);

                        // Reset form
                        this.reset();

                        // Show success message
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Album berhasil dibuat!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } catch (error) {
                    console.error('Error creating album:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: error.message || 'Gagal membuat album. Silakan coba lagi.'
                    });
                }
            });
        }

        // Function to add new album to DOM
        function addNewAlbumToDOM(album) {
            // Find or create album container
            let albumsContainer = document.getElementById('albumsContainer');
            
            const albumsTab = document.getElementById('albums');
            
            // Remove "You don't have any albums" message if exists
            const emptyState = albumsTab.querySelector('.text-center.py-5');
            if (emptyState) {
                emptyState.remove();
            }

            // If container doesn't exist (when there are no albums)
            if (!albumsContainer) {
                // Create a new container
                albumsContainer = document.createElement('div');
                albumsContainer.id = 'albumsContainer';
                albumsContainer.className = 'row';
                
                // Add container to the albums tab
                albumsTab.appendChild(albumsContainer);
            }
            
            // Add new album to container
            albumsContainer.insertAdjacentHTML('beforeend', generateAlbumCardHtml(album));
            initAlbumCard(album.id); // Initialize event listeners

            // If there were no albums before, show the main button
            const mainButton = document.getElementById('mainCreateAlbumButton');
            if (mainButton && mainButton.style.display === 'none') {
                mainButton.style.display = 'block';
            }

            // In addNewAlbumToDOM()
            const mainCreateButton = document.getElementById('mainCreateAlbumButton');
            if (mainCreateButton && mainCreateButton.style.display === 'none') {
                mainCreateButton.style.display = 'block';
            }
        }

        // Function to generate HTML for album card with cover grid
        function generateAlbumCardHtml(album) {
            // Generate cover grid HTML based on available photos
            let coverHtml = '';
            if (album.photos && album.photos.length > 0) {
                coverHtml = `
                    <div class="album-cover-grid">
                        <div class="album-cover-main">
                            <img src="/storage/${album.photos[0].path}" 
                                 class="album-cover-img" 
                                 alt="${album.photos[0].title || 'Album cover'}"
                                 loading="lazy">
                        </div>
                        ${album.photos.length > 1 ? `
                        <div class="album-cover-secondary">
                            <img src="/storage/${album.photos[1].path}" 
                                 class="album-cover-img" 
                                 alt="${album.photos[1].title || 'Album cover'}"
                                 loading="lazy">
                        </div>
                        ` : ''}
                        ${album.photos.length > 2 ? `
                        <div class="album-cover-secondary">
                            <img src="/storage/${album.photos[2].path}" 
                                 class="album-cover-img" 
                                 alt="${album.photos[2].title || 'Album cover'}"
                                 loading="lazy">
                        </div>
                        ` : ''}
                    </div>
                `;
            } else {
                coverHtml = `
                    <div class="album-cover-placeholder">
                        <i class="bi bi-images text-muted"></i>
                    </div>
                `;
            }

            return `
                <div class="col-6 col-md-4 col-lg-3 mb-3" id="album-${album.id}">
                    <div class="card shadow-sm h-100 position-relative">
                        <div class="album-card-container">
                            <a href="/albums/${album.id}" class="album-cover-link">
                                ${coverHtml}
                            </a>
                        </div>
                        
                        <!-- Mobile Dropdown -->
                        <div class="album-mobile-actions d-md-none">
                            <div class="dropdown dropup">
                                <button class="dropdown-toggle" type="button" id="mobileAlbumDropdown-${album.id}" 
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-popper="static">
                                    <i class="bi bi-three-dots-vertical text-white"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileAlbumDropdown-${album.id}">
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center" onclick="copyToClipboard('/albums/${album.id}')">
                                            <i class="bi bi-share me-2"></i> Bagikan
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center edit-album-btn" data-id="${album.id}">
                                            <i class="bi bi-pencil me-2"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center delete-album-btn" 
                                                data-id="${album.id}" 
                                                data-name="${album.name}">
                                            <i class="bi bi-trash me-2"></i> Hapus
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Desktop Dropdown -->
                        <div class="album-details d-none d-md-block p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="album-title mb-1">${album.name}</h6>
                                    ${album.description ? `<p class="album-desc text-muted small mb-1">${album.description}</p>` : ''}
                                </div>
                                <div class="dropdown dropup">
                                    <button class="btn btn-link p-0 dropdown-toggle" type="button" 
                                            id="desktopAlbumDropdown-${album.id}" 
                                            data-bs-toggle="dropdown" aria-expanded="false" 
                                            data-bs-popper="static">
                                        <i class="bi bi-three-dots-vertical text-dark"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="desktopAlbumDropdown-${album.id}">
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center" onclick="copyToClipboard('/albums/${album.id}')">
                                                <i class="bi bi-share me-2"></i> Bagikan
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center edit-album-btn" data-id="${album.id}">
                                                <i class="bi bi-pencil me-2"></i> Edit
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item d-flex align-items-center delete-album-btn" 
                                                    data-id="${album.id}" 
                                                    data-name="${album.name}">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Initialize event listeners for album
        function initAlbumEventListeners(albumId) {
            document.querySelectorAll(`.edit-album-btn[data-id="${albumId}"]`).forEach(btn => {
                btn.addEventListener('click', function() {
                    const albumId = this.getAttribute('data-id');
                    openEditAlbumModal(albumId);
                }, { once: true }); // Ensure the event listener is only added once
            });
            
            // Delete Album
            document.querySelectorAll(`.delete-album-btn[data-id="${albumId}"]`).forEach(btn => {
                btn.addEventListener('click', function() {
                    const albumId = this.getAttribute('data-id');
                    const albumName = this.getAttribute('data-name');
                    confirmDeleteAlbum(albumId, albumName);
                });
            });
        }

        // Function to initialize album card
        function initAlbumCard(albumId) {
            const dropdownElementList = [].slice.call(document.querySelectorAll(`#album-${albumId} .dropdown-toggle`));
            dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });

            // Event listener for delete
            document.querySelectorAll(`#album-${albumId} .delete-album-btn`).forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const albumId = this.getAttribute('data-id');
                    const albumName = this.getAttribute('data-name');
                    confirmDeleteAlbum(albumId, albumName);
                });
            });
        }

        // Event listener for all delete album buttons
        document.querySelectorAll('.delete-album-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const albumId = this.getAttribute('data-id');
                const albumName = this.getAttribute('data-name');
                confirmDeleteAlbum(albumId, albumName);
            });
        });

        // Function to open edit album modal
        async function openEditAlbumModal(albumId) {
            try {
                const response = await fetch(`/albums/${albumId}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest' // Penting untuk identifikasi AJAX
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch album data');
                }

                const data = await response.json();

                // Remove previous modal if exists
                const existingModal = document.getElementById('editAlbumModalDynamic');
                if (existingModal) {
                    existingModal.remove();
                }

                // Generate modal HTML dynamically
                const modalHtml = `
                    <div class="modal fade" id="editAlbumModalDynamic" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Album</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editAlbumForm-${albumId}" method="POST" action="/albums/${albumId}">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group mb-3">
                                            <label class="form-label">Album Name</label>
                                            <input type="text" class="form-control" name="name" value="${data.album.name}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="3">${data.album.description || ''}</textarea>
                                        </div>
                                        ${data.current_user_role === 'pro' ? `
                                        <div class="form-group mb-3">
                                            <label class="form-label">Visibility</label>
                                            <select class="form-select" name="status" required>
                                                <option value="1" ${data.album.status == 1 ? 'selected' : ''}>Public</option>
                                                <option value="0" ${data.album.status == 0 ? 'selected' : ''}>Private</option>
                                            </select>
                                        </div>
                                        ` : ''}
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Append modal to DOM
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                const modalElement = document.getElementById('editAlbumModalDynamic');
                const modal = new bootstrap.Modal(modalElement);

                // Show modal
                modal.show();

                // Handle form submission
                const form = modalElement.querySelector(`#editAlbumForm-${albumId}`);
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleEditAlbumSubmit(form, albumId, modal);
                });

                // Clean up when modal is closed
                modalElement.addEventListener('hidden.bs.modal', function() {
                    this.remove();
                });

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Failed to load album data'
                });
            }
        }

        // Initialize album cards and event listeners
        document.querySelectorAll('[id^="album-"]').forEach(albumCard => {
            const albumId = albumCard.id.replace('album-', '');
            initAlbumCard(albumId);
        });


        // Function to handle edit album submission
        async function handleEditAlbumSubmit(form, albumId, modal) {
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! Status: ${response.status}`);
                }

                if (data.success) {
                    // Update the album card in the DOM
                    updateAlbumCard(albumId, data.album);

                    // Close the modal before showing the alert
                    modal.hide();

                    // Show success alert without scrolling
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Album berhasil diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error updating album:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Gagal memperbarui album'
                });
            } finally {
                submitButton.disabled = false;
            }
        }

        // Function to update album card after edit
        function updateAlbumCard(albumId, albumData) {
            const albumCard = document.querySelector(`#album-${albumId}`);
            if (!albumCard) return;
            
            // Update title
            const titleElement = albumCard.querySelector('.album-title');
            if (titleElement) titleElement.textContent = albumData.name;
            
            // Update description
            const descElement = albumCard.querySelector('.album-desc');
            if (descElement) {
                if (albumData.description) {
                    descElement.textContent = albumData.description;
                    descElement.style.display = 'block';
                } else {
                    descElement.style.display = 'none';
                }
            }
        }

        // Confirm delete album
        async function confirmDeleteAlbum(albumId, albumName) {
            // Save scroll position before showing alert
            const scrollPosition = window.scrollY || document.documentElement.scrollTop;

            const result = await Swal.fire({
                title: 'Konfirmasi Hapus Album',
                html: `Anda yakin ingin menghapus album <strong>${albumName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#32bd40',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonTitle: 'Batal',
                heightAuto: false,
                didOpen: () => {
                    window.scrollTo({
                        top: scrollPosition,
                        behavior: 'instant'
                    });
                }
            });

            if (result.isConfirmed) {
                await deleteAlbum(albumId);
            }

            // Restore scroll position after alert is closed
            window.scrollTo({
                top: scrollPosition,
                behavior: 'instant'
            });
        }

        // Function to delete album
        async function deleteAlbum(albumId) {
            const scrollPosition = window.scrollY || document.documentElement.scrollTop;

            try {
                const response = await fetch(`/albums/${albumId}`, {
                    method: 'DELETE',
                    headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! Status: ${response.status}`);
                }
                

                if (data.success) {
                    const albumCard = document.querySelector(`#album-${albumId}`);
                    if (albumCard) albumCard.remove();
                    
                    // Check if there are still albums
                    const remainingAlbums = document.querySelectorAll('#albumsContainer .col-6');
                    if (remainingAlbums.length === 0) {
                        showEmptyAlbumState();
                    }
                    
                    await Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Album telah dihapus.',
                        timer: 1500,
                        showConfirmButton:
 false
                    });

                    window.scrollTo({
                        top: scrollPosition,
                        behavior: 'instant'
                    });
                }
            } catch (error) {
                console.error('Error deleting album:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Gagal menghapus album'
                });
            }
        }

        // Show empty album state
        function showEmptyAlbumState() {
            const albumsContainer = document.querySelector('#albumsContainer');
            if (albumsContainer) {
            
                albumsContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-folder-x" style="font-size: 3rem; color: #6c757d;"></i>
                        <p class="mt-3 text-muted">Anda belum memiliki album</p>
                        <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#createAlbumModal">
                            <i class="bi bi-plus"></i> Buat Album Pertama Anda
                        </button>
                    </div>
                `;
            }
            else {
                Swal.fire('Error', 'Album container cannot be found', 'error');
            }

            const mainButton = document.getElementById('mainCreateAlbumButton');
            if (mainButton) {
                mainButton.style.display = 'none';
            }
        }

        // Change event listener for edit profile form
        const editProfileForm = document.getElementById('editProfileForm');
        if (editProfileForm) {
            editProfileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST', 
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData 
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Close the modal before showing the success alert
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                        if (modal) {
                            modal.hide();
                        }

                        // Show success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        // Close the modal before showing the error alert
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                        if (modal) {
                            modal.hide();
                        }

                        // Show error alert
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Close the modal before showing the error alert
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                    if (modal) {
                        modal.hide();
                    }

                    // Show error alert
                    Swal.fire('Error', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                });
            });
        }

        // Add event listener for all delete photo buttons
        document.querySelectorAll('.delete-photo-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const photoId = this.getAttribute('data-id');
                const photoTitle = this.getAttribute('data-title');
                
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: `Anda yakin ingin menghapus foto <strong>${photoTitle}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#32bd40',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonTitle: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Process photo deletion
                        fetch(`/photos/${photoId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Berhasil!', 'Foto berhasil dihapus.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', data.message, 'error');
                            }
                        });
                    }
                });
            });
        });

        // Event listener for all report forms
        document.querySelectorAll('form[id^="reportForm"]').forEach(form => {
            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const actionUrl = this.action;
                const modalElement = this.closest('.modal');
                const modal = bootstrap.Modal.getInstance(modalElement);

                try {
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || `HTTP error! Status: ${response.status}`);
                    }

                    if (data.success) {
                        // Reset form
                        this.reset();

                        // Close modal and clean backdrop
                        if (modal) {
                            modal.hide();
                            document.body.classList.remove('modal-open');
                            const backdrops = document.querySelectorAll('.modal-backdrop');
                            backdrops.forEach(backdrop => backdrop.remove());
                            document.body.style.overflow = 'auto';
                            document.body.style.paddingRight = '0';
                        }

                        // Show SweetAlert and reload after clicking OK
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonText: 'OK',
                        }).then(() => {
                            location.reload(); // Reload page
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan.',
                            confirmButtonText: 'OK',
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message || 'Terjadi kesalahan saat memproses permintaan.',
                        confirmButtonText: 'OK',
                    });
                }
            });

            // Reset form when modal is closed
            const modalElement = form.closest('.modal');
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function() {
                    form.reset();
                });
            }
        });

        // Event listener for reason "Lainnya"
        document.querySelectorAll('input[name="reason"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const form = this.closest('form');
                const descriptionGroup = form.querySelector('.form-group[id^="description-group"]');
                const descriptionInput = form.querySelector('textarea[name="description"]');

                if (this.value === 'Lainnya') {
                    descriptionGroup.style.display = 'block';
                    descriptionInput.required = true;
                } else {
                    descriptionGroup.style.display = 'none';
                    descriptionInput.required = false;
                }
            });
        });

        // Clean backdrop and restore scroll when modal is closed
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.classList.remove('modal-open');
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '0';
                
                // Remove backdrop
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
            });
        });

        // Di dalam DOMContentLoaded
        document.querySelectorAll('.edit-album-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const albumId = this.getAttribute('data-id'); // Tambahkan data-id pada form
                const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                handleEditAlbumSubmit(this, albumId, modal);
            });
        });

        // Function to clean backdrop
        function cleanModalBackdrops() {
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        }

        // Call this function every time a modal is closed
        modal.hide();
        cleanModalBackdrops();

        // Initialize album cards and event listeners
        document.querySelectorAll('[id^="album-"]').forEach(albumCard => {
            const albumId = albumCard.id.replace('album-', '');
            initAlbumCard(albumId);
        });
    });
</script>
@endpush