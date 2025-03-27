@extends('layouts.app')

@section('content')

@push('link')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        border-radius: 8px;
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
        display: none; /* Initially hidden */
    }
    
    .dropdown-menu.show {
        display: block !important; /* Bootstrap will add this class */
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
<div class="d-flex justify-content-center">
    <div class="col-md-4 grid-margin grid-margin-md-0 stretch-card">
        <div class="card shadow-lg">
            <div class="card-body text-center">
                <div>
                    <img src="{{ $user->profile_photo_url }}" class="img-lg rounded-circle mb-2" alt="profile image" />
                    <h4>{{ $user->name }} 
                        @if($user->verified)
                        <i class="ti-medall-alt" style="color: gold;"></i>
                        @endif
                        @if ($user->role === 'pro')
                        <i class="ti-star" style="color: gold;"></i>
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
                            <button id="follow-button" class="btn btn-success btn-sm mt-3 mb-4 text-white" data-user-id="{{ $user->id }}" data-following="{{ Auth::user()->isFollowing($user) ? 'true' : 'false' }}">
                                {{ Auth::user()->isFollowing($user) ? 'Unfollow' : 'Follow' }}
                            </button>
                            <button type="button" class="btn btn-link p-0 me-3" data-bs-toggle="modal" data-bs-target="#reportUserModal">
                                <i class="bi bi-flag text-danger"></i>
                            </button>
                        @else
                            <button id="follow-button" class="btn btn-success btn-sm mt-3 mb-4 text-white" onclick="window.location.href='{{ route('login') }}'">
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
                                <div class="position-absolute bottom-0 end-0 m-2 d-md-none">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-dark rounded-circle" type="button" id="mobilePhotoDropdown-{{ $photo->id }}" data-bs-toggle="dropdown" aria-expanded="false">
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
                                                    <button class="dropdown-item d-flex align-items-center delete-photo-btn" data-id="{{ $photo->id }}" data-title="{{ $photo->title }}">
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
                                                    <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="download-button">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item d-flex align-items-center w-100">
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
                                                    <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="download-button">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item d-flex align-items-center w-100">
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
                                        <div class="dropdown d-none d-md-block">
                                            <button class="btn btn-link p-0" type="button" id="desktopPhotoDropdown-{{ $photo->id }}" data-bs-toggle="dropdown" aria-expanded="false">
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
                                                        <button class="dropdown-item d-flex align-items-center delete-photo-btn" data-id="{{ $photo->id }}" data-title="{{ $photo->title }}">
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
                                                        <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="download-button">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item d-flex align-items-center w-100">
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
                                                        <form method="POST" action="{{ route('photos.download', $photo->id) }}" class="download-button">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item d-flex align-items-center w-100">
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
        
                    <!-- Report Photo Modal -->
                    <div class="modal fade" id="reportPhotoModal-{{ $photo->id }}" tabindex="-1" aria-labelledby="reportPhotoModalLabel-{{ $photo->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reportPhotoModalLabel-{{ $photo->id }}">Laporkan Foto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('photo.report', $photo->id) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Alasan Melaporkan</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason1-{{ $photo->id }}" value="Konten tidak pantas">
                                                <label class="form-check-label" for="reason1-{{ $photo->id }}">Konten tidak pantas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason2-{{ $photo->id }}" value="Spam">
                                                <label class="form-check-label" for="reason2-{{ $photo->id }}">Spam</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason3-{{ $photo->id }}" value="Pelanggaran hak cipta">
                                                <label class="form-check-label" for="reason3-{{ $photo->id }}">Pelanggaran hak cipta</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason4-{{ $photo->id }}" value="Lainnya">
                                                <label class="form-check-label" for="reason4-{{ $photo->id }}">Lainnya</label>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="description-group-{{ $photo->id }}" style="display: none;">
                                            <label for="description-{{ $photo->id }}" class="form-label">Keterangan</label>
                                            <textarea class="form-control" id="description-{{ $photo->id }}" name="description" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger">Laporkan</button>
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
        </div>
<!-- Tab Album -->
<!-- Tab Album -->
<div class="tab-pane fade" id="albums" role="tabpanel" aria-labelledby="albums-tab">
    <h3 class="mt-5 mb-3">Album</h3>
    @if(Auth::id() === $user->id)
        <button type="button" class="btn btn-success btn-sm mb-3 text-white" data-bs-toggle="modal" data-bs-target="#createAlbumModal">
            <i class="bi bi-plus"></i> Buat Album
        </button>
    @endif
    <div class="row">
        @foreach($albums as $album)
        <div class="col-6 col-md-4 col-lg-3 mb-3">
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
                <div class="position-absolute bottom-0 end-0 m-2 d-md-none">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-dark rounded-circle" type="button" id="mobileAlbumDropdown-{{ $album->id }}" data-bs-toggle="dropdown" aria-expanded="false">
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
                                    <button class="dropdown-item d-flex align-items-center text-danger delete-album-btn" data-id="{{ $album->id }}" data-name="{{ $album->name }}">
                                        <i class="bi bi-trash me-2"></i> Hapus
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
                        <div class="dropdown">
                            <button class="btn btn-link p-0" type="button" id="desktopAlbumDropdown-{{ $album->id }}" data-bs-toggle="dropdown" aria-expanded="false">
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
                                        <button class="dropdown-item d-flex align-items-center text-danger delete-album-btn" data-id="{{ $album->id }}" data-name="{{ $album->name }}">
                                            <i class="bi bi-trash me-2"></i> Hapus
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
                        <form method="POST" action="{{ route('albums.update', $album->id) }}">
                            @csrf
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

        <!-- Modal Hapus Album -->
        <div class="modal fade" id="deleteAlbumModal-{{ $album->id }}" tabindex="-1" aria-labelledby="deleteAlbumModalLabel-{{ $album->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAlbumModalLabel-{{ $album->id }}">Hapus Album</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus album <strong>{{ $album->name }}</strong>?</p>
                        <p class="text-danger">Semua foto dalam album ini juga akan dihapus dan tidak dapat dikembalikan!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('albums.destroy', $album->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Buat Album Baru -->
<div class="modal fade" id="createAlbumModal" tabindex="-1" aria-labelledby="createAlbumModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAlbumModalLabel">Buat Album Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('albums.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="albumName" class="form-label">Nama Album</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="albumDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    @if (Auth::check() && Auth::user()->role === 'pro')
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Visibilitas</label>
                            <select class="form-select" id="status" name="status" required>
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

            <!-- Tab Langganan -->
        @if(Auth::check() && $user->verified && ($hasSubscriptionPrice || Auth::id() === $user->id))
            <div class="tab-pane fade" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
                <h3 class="mt-5 mb-4">Langganan</h3>
                @if(Auth::id() === $user->id)
                    @if(!$hasSubscriptionPrice)
                        <button class="btn btn-warning mb-4" onclick="window.location.href='{{ route('subscription.manage') }}'">
                            <i class="bi bi-gear me-2"></i> Atur Langgananmu Sekarang
                        </button>
                    @else
                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <button class="btn btn-warning me-3" onclick="window.location.href='{{ route('subscription.manage') }}'">
                                    <i class="bi bi-pencil me-2"></i> Ubah Harga Langganan
                                </button>
                                <button class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#subscribersModal">
                                    <i class="bi bi-people me-2"></i> Lihat Daftar Langganan
                                </button>
                            </div>
                            <button class="btn btn-success text-white" onclick="window.location.href='{{ route('photos.create') }}'">
                                <i class="bi bi-plus me-2"></i> Tambah Foto Anda
                            </button>
                        </div>

                        <!-- Tampilkan Foto Premium -->
                        <h5 class="mb-3">Foto Premium Anda</h5>
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            @forelse($premiumPhotos as $photo)
                                <div class="col">
                                    <div class="card h-100 shadow-sm">
                                        <a href="{{ route('photos.show', $photo->id) }}">
                                            <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top img-fluid" alt="Premium Photo" style="height: 200px; object-fit: cover;">
                                        </a>
                                        <div class="card-body">
                                            <p class="card-text text-muted">Uploaded on {{ $photo->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col">
                                    <p class="text-muted">Anda belum mengunggah foto eksklusif.</p>
                                </div>
                            @endforelse
                        </div>
                    @endif
                @else
                    @if(Auth::user()->subscriptions()->where('target_user_id', $user->id)->exists())
                        <button class="btn btn-info" onclick="window.location.href='{{ route('subscription.options', ['username' => $user->username]) }}'">
                            <i class="bi bi-arrow-repeat me-2"></i> Perpanjang Langganan
                        </button>
                    @endif
                @endif

                @if(Auth::id() !== $user->id && Auth::user()->subscriptions()->where('target_user_id', $user->id)->exists())
                    <!-- Tampilkan Foto Eksklusif untuk Pelanggan -->
                    <h5 class="mt-4 mb-3">Foto Eksklusif</h5>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @forelse($premiumPhotos as $photo)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <a href="{{ route('photos.show', $photo->id) }}">
                                        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top img-fluid" alt="Premium Photo" style="height: 200px; object-fit: cover;">
                                    </a>
                                    <div class="card-body">
                                        <p class="card-text text-muted">Uploaded on {{ $photo->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col">
                                <p class="text-muted">Belum ada foto eksklusif.</p>
                            </div>
                        @endforelse
                    </div>
                @elseif(Auth::id() !== $user->id)
                    <!-- Pesan untuk Pengguna yang Belum Berlangganan -->
                    <div class="alert alert-info mt-4">
                        <h5 class="alert-heading">Anda belum berlangganan!</h5>
                        <p>Silakan berlangganan untuk membuka akses foto eksklusif.</p>
                        <hr>
                        <a href="{{ route('subscription.options', ['username' => $user->username]) }}" class="btn btn-primary">
                            <i class="bi bi-star me-2"></i> Langganan Sekarang
                        </a>
                    </div>
                @endif
            </div>
        @endif
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
                                        class="btn btn-sm {{ Auth::user()->isFollowing($subscriber->user) ? 'btn-danger unfollow-button' : 'btn-primary follow-button' }}" 
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
            <form method="POST" action="{{ route('user.updateProfile') }}" enctype="multipart/form-data">
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
                            <label for="name" class="form-label">Nama Album</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Album</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        @if (Auth::check() && Auth::user()->role === 'pro')
                            <div class="mb-3">
                                <label for="status" class="form-label">Visibilitas</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="1">Publik</option>
                                    <option value="0">Privat</option>
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="status" value="1">
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success text-white">Buat Album</button>
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
                            <a href="{{ route('user.showProfile', $follower->username) }}"><b>{{ $follower->username }}</b></a>
                            @if(Auth::check() && Auth::id() !== $follower->id)
                                <button 
                                    class="btn btn-sm {{ Auth::user()->isFollowing($follower) ? 'btn-danger unfollow-button' : 'btn-primary follow-button' }}" 
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
                            <a href="{{ route('user.showProfile', $following->username) }}"><b>{{ $following->username }}</b></a>
                            @if(Auth::check() && Auth::id() !== $following->id)
                                <button 
                                    class="btn btn-sm {{ Auth::user()->isFollowing($following) ? 'btn-danger unfollow-button' : 'btn-primary follow-button' }}" 
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
                <h5 class="modal-title" id="reportUserModalLabel">Laporkan Pengguna 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportUserForm" method="POST" action="{{ route('user.report', $user->id) }}">
                    @csrf
                    <div class="form-group">
                        <label for="reason">Alasan Melaporkan</label>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input"name="reason" id="reason1"
                                value="Konten tidak pantas">
                                Konten tidak pantas
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="reason" id="reason2" 
                                value="Spam">
                                Spam
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input"name="reason" id="reason3" 
                                value="Pelanggaran hak cipta">
                                Pelanggaran hak cipta
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" name="reason" id="reason4"
                                value="Lainnya">
                                Lainnya
                            </label>
                        </div>
                    </div>
                    <div class="form-group" id="description-group-user" style="display: none;">
                        <label for="description-user">Alasan</label>
                        <textarea class="form-control" id="description-user" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger text-white">Laporkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

    
    <!-- Modal Buat Album -->
    @if(Auth::id() === $user->id)
        <div class="modal fade" id="createAlbumModal" tabindex="-1" role="dialog" aria-labelledby="createAlbumModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="createAlbumForm"> <!-- Tambahkan ID untuk form -->
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="createAlbumModalLabel">Buat Album Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Album</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Album</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            @if (Auth::check() && Auth::user()->role === 'pro')
                                <div class="mb-3">
                                    <label for="status" class="form-label">Visibilitas</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="1">Publik</option>
                                        <option value="0">Privat</option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="status" value="1">
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
                                <a href="{{ route('user.showProfile', $follower->username) }}"><b>{{ $follower->username }}</b></a>
                                @if(Auth::check() && Auth::id() !== $follower->id)
                                    <button 
                                        class="btn btn-sm {{ Auth::user()->isFollowing($follower) ? 'btn-danger unfollow-button' : 'btn-primary follow-button' }}" 
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
                                <a href="{{ route('user.showProfile', $following->username) }}"><b>{{ $following->username }}</b></a>
                                @if(Auth::check() && Auth::id() !== $following->id)
                                    <button 
                                        class="btn btn-sm {{ Auth::user()->isFollowing($following) ? 'btn-danger unfollow-button' : 'btn-primary follow-button' }}" 
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
    @endsection
    
    @push('scripts')
    <script>
function copyToClipboard(text, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Link berhasil disalin',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            background: '#32bd40',
            iconColor: '#fff',
            color: '#fff',
            timerProgressBar: true,
            width: '300px', // Lebar maksimal
            padding: '0.5rem', // Padding lebih kecil
            customClass: {
                container: 'swal-mobile-container',
                popup: 'swal-mobile-popup',
                title: 'swal-mobile-title',
                content: 'swal-mobile-content'
            }
        });
    }).catch(err => {
        console.error('Gagal menyalin:', err);
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Gagal menyalin link',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            width: '300px',
            padding: '0.5rem'
        });
    });
}
        document.addEventListener("DOMContentLoaded", function() {
            const token = '{{ csrf_token() }}';
            const userReasonRadios = document.querySelectorAll('#reportUserModal input[name="reason"]');
            const userDescriptionGroup = document.getElementById('description-group-user');
            const userDescriptionInput = document.getElementById('description-user');


            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
    });

            userReasonRadios.forEach(radio => {
                radio.addEventListener("change", function () {
                    if (this.value === "Lainnya") {
                        userDescriptionGroup.style.display = "block";
                        userDescriptionInput.required = true;
                    } else {
                        userDescriptionGroup.style.display = "none";
                        userDescriptionInput.required = false;
                    }
                });
            });
                
                // Tutup dropdown saat klik di luar
                document.addEventListener('click', function() {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('show');
                    });
                });

            // Fungsi untuk follow/unfollow
            function updateFollowButton(button, following) {
                if (following) {
                    button.textContent = 'Unfollow';
                    button.classList.remove('btn-success', 'follow-button');
                    button.classList.add('btn-dark', 'unfollow-button');
                } else {
                    button.textContent = 'Follow';
                    button.classList.remove('btn-dark', 'unfollow-button');
                    button.classList.add('btn-success', 'follow-button');
                }
            }

            function handleFollowUnfollow(button) {
                const userId = button.getAttribute('data-user-id');
                const isUnfollow = button.classList.contains('unfollow-button');
                const url = isUnfollow ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateFollowButton(button, !isUnfollow);

                        // Update followers count
                        const followersCount = document.getElementById('followers-count');
                        if (followersCount) {
                            followersCount.textContent = data.followers_count;
                        }

                        // Update followers list in the modal
                        const followersList = document.getElementById('followers-list');
                        if (followersList) {
                            if (!isUnfollow) {
                                const newFollowerItem = document.createElement('li');
                                newFollowerItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                                newFollowerItem.innerHTML = `
                                    <a href="/users/${data.current_user.username}"><b>${data.current_user.username}</b></a>
                                    <button class="btn btn-sm btn-dark unfollow-button" data-user-id="${data.current_user.id}">Unfollow</button>
                                `;
                                followersList.appendChild(newFollowerItem);

                                // Add event listener to the new unfollow button
                                newFollowerItem.querySelector('.unfollow-button').addEventListener('click', function () {
                                    handleFollowUnfollow(this);
                                });
                            } else {
                                const followerItems = followersList.querySelectorAll('li');
                                followerItems.forEach(item => {
                                    if (item.querySelector('button').getAttribute('data-user-id') === data.current_user.id) {
                                        item.remove();
                                    }
                                });
                            }
                        }

                        // Update following list in the modal
                        const followingList = document.getElementById('following-list');
                        if (followingList) {
                            if (!isUnfollow) {
                                const newFollowingItem = document.createElement('li');
                                newFollowingItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                                newFollowingItem.innerHTML = `
                                    <a href="/users/${data.current_user.username}"><b>${data.current_user.username}</b></a>
                                    <button class="btn btn-sm btn-dark unfollow-button" data-user-id="${data.current_user.id}">Unfollow</button>
                                `;
                                followingList.appendChild(newFollowingItem);

                                // Add event listener to the new unfollow button
                                newFollowingItem.querySelector('.unfollow-button').addEventListener('click', function () {
                                    handleFollowUnfollow(this);
                                });
                            } else {
                                const followingItems = followingList.querySelectorAll('li');
                                followingItems.forEach(item => {
                                    if (item.querySelector('button').getAttribute('data-user-id') === data.current_user.id) {
                                        item.remove();
                                    }
                                });
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Gagal memproses permintaan.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Terjadi kesalahan saat memproses permintaan.',
                        confirmButtonText: 'OK'
                    });
                });
            }

            // Tombol Follow/Unfollow di profil pengguna
            const followButton = document.getElementById('follow-button');
            if (followButton) {
                followButton.addEventListener('click', function () {
                    handleFollowUnfollow(followButton);
                });
            }

            // Tombol Follow/Unfollow di modal followers/following
            document.querySelectorAll('.follow-button, .unfollow-button').forEach(button => {
                button.addEventListener('click', function () {
                    handleFollowUnfollow(button);
                });
            });

            // Event listener untuk form create album
            const createAlbumForm = document.getElementById('createAlbumForm');
            if (createAlbumForm) {
    createAlbumForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(createAlbumForm);
        const submitButton = createAlbumForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        try {
            const response = await fetch('{{ route('albums.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Album berhasil dibuat!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Pastikan semua modal ditutup dan cleanup dilakukan
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createAlbumModal'));
                    if (modal) {
                        modal.hide();
                    }

                    // Hapus semua backdrop modal
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });

                    // Hapus class modal-open dari body
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    // Reset form
                    createAlbumForm.reset();

                    // Tambahkan album baru ke tab panel
                    const newAlbumHtml = `
                                    <div class="col-md-4 mb-4">
                                        <div class="card">
                                            <a href="/albums/${data.album.id}">
                                                <div class="album-cover">
                                                    <!-- Jika ada foto, tambahkan di sini -->
                                                </div>
                                            </a>
                                            <div class="card-body">
                                                <h5 class="card-title">${data.album.name}</h5>
                                                <p class="card-text">${data.album.description}</p>
                                                ${data.album.user_id === data.current_user_id ? `
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton-${data.album.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-bookmarks-fill"></i>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-${data.album.id}">
                                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editAlbumModal-${data.album.id}">Edit</a></li>
                                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteAlbumModal-${data.album.id}">Hapus</a></li>
                                                        </ul>
                                                    </div>

                                                    <!-- Modal Edit Album -->
                                                    <div class="modal fade" id="editAlbumModal-${data.album.id}" tabindex="-1" aria-labelledby="editAlbumModalLabel-${data.album.id}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editAlbumModalLabel-${data.album.id}">Edit Album</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form id="editAlbumForm-${data.album.id}" method="POST" action="/albums/${data.album.id}">
                                                                        <input type="hidden" name="_method" value="PUT">
                                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                        <div class="form-group">
                                                                            <label for="albumName">Nama Album</label>
                                                                            <input type="text" class="form-control" name="name" value="${data.album.name}" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="albumDescription">Deskripsi</label>
                                                                            <textarea class="form-control" name="description" rows="3">${data.album.description}</textarea>
                                                                        </div>
                                                                        ${data.current_user_role === 'pro' ? `
                                                                            <div class="form-group">
                                                                                <label for="status" class="form-label">Visibilitas</label>
                                                                                <select class="form-select" id="status" name="status" required>
                                                                                    <option value="1" ${data.album.status === '1' ? 'selected' : ''}>Publik</option>
                                                                                    <option value="0" ${data.album.status === '0' ? 'selected' : ''}>Privat</option>
                                                                                </select>
                                                                            </div>
                                                                        ` : ''}
                                                                        <button type="submit" class="btn btn-success text-white">Simpan Perubahan</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Hapus Album -->
                                                    <div class="modal fade" id="deleteAlbumModal-${data.album.id}" tabindex="-1" aria-labelledby="deleteAlbumModalLabel-${data.album.id}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteAlbumModalLabel-${data.album.id}">Hapus Album</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Apakah Anda yakin ingin menghapus album ini?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                                                                    <form id="deleteAlbumForm-${data.album.id}" method="POST" action="/albums/${data.album.id}">
                                                                        <input type="hidden" name="_method" value="DELETE">
                                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                        <button type="submit" class="btn btn-danger text-white">Hapus</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                `;

                    const albumsRow = document.querySelector('#albums .row');
                    if (albumsRow) {
                        albumsRow.insertAdjacentHTML('beforeend', newAlbumHtml);
                        initializeDropdowns();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal membuat album. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            console.error('Error creating album:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: 'Terjadi kesalahan saat membuat album. Silakan coba lagi.',
                confirmButtonText: 'OK'
            });
        } finally {
            submitButton.disabled = false;
        }
    });
}

// Tambahkan console log untuk debug
document.querySelectorAll('.dropdown-toggle').forEach(el => {
    el.addEventListener('click', function() {
        console.log('Dropdown clicked');
        console.log('Associated menu:', this.nextElementSibling);
    });
});

// Tambahkan event listener untuk modal hidden
document.getElementById('createAlbumModal')?.addEventListener('hidden.bs.modal', function () {
    // Cleanup setelah modal ditutup
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.remove();
    });
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
});

// Fungsi untuk menginisialisasi dropdown
function initializeDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(dropdown => {
        new bootstrap.Dropdown(dropdown);
    });
}
// Ganti event listener untuk dropdown
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('dropdown-toggle') || 
        e.target.closest('.dropdown-toggle')) {
        e.preventDefault();
        e.stopPropagation();
        var dropdown = new bootstrap.Dropdown(e.target.closest('.dropdown-toggle'));
        dropdown.toggle();
    }
});
// Fungsi untuk menangani form edit album
function handleEditAlbumForm(albumId) {
    const editForm = document.getElementById(`editAlbumForm-${albumId}`);
    if (editForm) {
        editForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData(editForm);
            const submitButton = editForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            try {
                const response = await fetch(editForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Album berhasil diperbarui!',
                        confirmButtonText: 'OK'
                    });

                    // Perbarui informasi album di DOM
                    const albumTitle = document.querySelector(`#dropdownMenuButton-${albumId}`).closest('.card').querySelector('.card-title');
                    const albumDescription = document.querySelector(`#dropdownMenuButton-${albumId}`).closest('.card').querySelector('.card-text');

                    if (albumTitle) albumTitle.textContent = data.album.name;
                    if (albumDescription) albumDescription.textContent = data.album.description;

                    // Tutup modal edit
                    const editModal = bootstrap.Modal.getInstance(document.getElementById(`editAlbumModal-${albumId}`));
                    editModal.hide();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal memperbarui album. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                console.error('Error updating album:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Terjadi kesalahan saat memperbarui album. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            } finally {
                submitButton.disabled = false;
            }
        });
    }
}

// Fungsi untuk menangani form hapus album
function handleDeleteAlbumForm(albumId) {
    const deleteForm = document.getElementById(`deleteAlbumForm-${albumId}`);
    if (deleteForm) {
        deleteForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const submitButton = deleteForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            try {
                const response = await fetch(deleteForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(deleteForm)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Album berhasil dihapus!',
                        confirmButtonText: 'OK'
                    });

                    // Hapus elemen album dari DOM
                    const albumCard = document.querySelector(`#dropdownMenuButton-${albumId}`).closest('.col-md-4');
                    if (albumCard) albumCard.remove();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menghapus album. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                console.error('Error deleting album:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Terjadi kesalahan saat menghapus album. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            } finally {
                submitButton.disabled = false;
            }
        });
    }
}
// Fungsi untuk menampilkan alert
function showAlert(icon, title, text, callback = null) {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        confirmButtonColor: '#32bd40',
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

// Handle delete photo dengan event delegation
document.addEventListener('click', function (e) {
    // Delete Photo
    if (e.target && e.target.closest('.delete-photo-btn')) {
        const button = e.target.closest('.delete-photo-btn');
        const photoId = button.getAttribute('data-id');
        const photoTitle = button.getAttribute('data-title');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Anda akan menghapus foto <strong>${photoTitle}</strong>.<br>Anda tidak akan bisa mengembalikan foto ini!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#32bd40',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/photos/${photoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert('success', 'Berhasil!', data.message || 'Foto berhasil dihapus.', () => {
                            window.location.reload(); // Reload halaman setelah berhasil
                        });
                    } else {
                        showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat menghapus foto.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
                }
            }
        });
    }

    // Delete Album
    if (e.target && e.target.closest('.delete-album-btn')) {
        const button = e.target.closest('.delete-album-btn');
        const albumId = button.getAttribute('data-id');
        const albumName = button.getAttribute('data-name');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Anda akan menghapus album <strong>${albumName}</strong>.<br>Anda tidak akan bisa mengembalikan album ini!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#32bd40',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/albums/${albumId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showAlert('success', 'Berhasil!', data.message || 'Album berhasil dihapus.', () => {
                            window.location.reload(); // Reload halaman setelah berhasil
                        });
                    } else {
                        showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat menghapus album.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
                }
            }
        });
    }
});
// Panggil fungsi handleEditAlbumForm dan handleDeleteAlbumForm setelah menambahkan album baru
handleEditAlbumForm(data.album.id);
handleDeleteAlbumForm(data.album.id);
        });
    </script>
    @endpush