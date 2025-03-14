@extends('layouts.app')

@section('content')

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<div class="d-flex justify-content-center">
    <div class="col-md-4 grid-margin grid-margin-md-0 stretch-card">
        <div class="card shadow-lg">
            <div class="card-body text-center">
                <div>
                    <img src="{{ $user->profile_photo_url }}" class="img-lg rounded-circle mb-2" alt="profile image" />
                    <h4>{{ $user->name }} 
                        @if($user->verified)
                        <i class="ti-medall-alt" style="color: gold;"></i> <!-- Tambahkan ini -->
                        @endif
                        @if ($user->role === 'pro')
                        <i class="ti-star" style="color: gold;"></i> <!-- Tambahkan ini --> 
                        @endif
                    </h4>
                    <p class="text-muted mb-0">{{ $user->username }}</p>
                </div>
                @if(Auth::id() === $user->id)
                            <p class="mt-2 card-text">Email: {{ $user->email }}</p>
                            <p>{{ $user->bio }}</p> <!-- Tambahkan ini -->
                            <p><a href="{{ $user->website }}" target="_blank">{{ $user->website }}</a></p> <!-- Tambahkan ini -->
                            <button class="btn btn-success btn-sm mt-3 mb-4 text-white" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profil</button>
                         @else
                            <div id="follow-section">
                                @if(Auth::check())
                                    <button id="follow-button"  class="btn btn-success btn-sm mt-3 mb-4 text-white" data-user-id="{{ $user->id }}" data-following="{{ Auth::user()->isFollowing($user) ? 'true' : 'false' }}">
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
                                    <h6 id="followers-count" >{{ $user->followers()->count() }}</h6>
                                    <p class="btn btn-link text-success" data-bs-toggle="modal" data-bs-target="#followersModal">Followers</p>
                                </div>
                                <div class="col-6">
                                    <h6 id="following-count" >{{ $user->following()->count() }}</h6>
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
            <h2 class="mt-5">Foto yang Diunggah</h2>
            <div class="row">
                @foreach($photos as $photo)
                    @if($photo->banned && $photo->user_id !== Auth::id())
                        @continue
                    @endif
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            @if($photo->banned && $photo->user_id === Auth::id())
                                <div class="card-body">
                                    <h5 class="card-title">Postingan ini telah dibanned.</h5>
                                    @foreach($photo->reports as $report)
                                        <p class="card-text"><strong>Alasan:</strong> {{ $report->reason }}</p>
                                    @endforeach
                                </div>
                            @else
                                <a href="{{ route('photos.show', $photo->id) }}">
                                    <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="{{ $photo->title }}">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $photo->title }}</h5>
                                    <p class="card-text">{{ $photo->description }}</p>
                                    <div class="dropdown">
                                        <button class="btn btn-link p-0" type="button" id="dropdownMenuButton-{{ $photo->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots text-dark"></i>                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $photo->id }}">
                                            @if(Auth::id() === $photo->user_id)
                                                <li><a class="dropdown-item" href="{{ route('photos.edit', $photo->id) }}">Edit Foto</a></li>
                                                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deletePhotoModal-{{ $photo->id }}">Hapus Foto</button></li>
                                            @else
                                                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportPhotoModal-{{ $photo->id }}">Laporkan</button></li>
                                                <li><a class="dropdown-item" href="#">Bagikan</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Modal Hapus Foto -->
                    <div class="modal fade" id="deletePhotoModal-{{ $photo->id }}" tabindex="-1" role="dialog" aria-labelledby="deletePhotoModalLabel-{{ $photo->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deletePhotoModalLabel-{{ $photo->id }}">Hapus Foto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus foto ini?
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

                    <!-- Modal Laporkan Foto -->
                    <div class="modal fade" id="reportPhotoModal-{{ $photo->id }}" tabindex="-1" role="dialog" aria-labelledby="reportPhotoModalLabel-{{ $photo->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reportPhotoModalLabel-{{ $photo->id }}">Laporkan Foto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="reportForm-{{ $photo->id }}" method="POST" action="{{ route('photo.report', $photo->id) }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="reason">Alasan Melaporkan</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason1" value="Konten tidak pantas" required>
                                                <label class="form-check-label" for="reason1">Konten tidak pantas</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason2" value="Spam" required>
                                                <label class="form-check-label" for="reason2">Spam</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason3" value="Pelanggaran hak cipta" required>
                                                <label class="form-check-label" for="reason3">Pelanggaran hak cipta</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="reason" id="reason4" value="Lainnya" required>
                                                <label class="form-check-label" for="reason4">Lainnya</label>
                                            </div>
                                        </div>
                                        <div class="form-group" id="description-group-{{ $photo->id }}" style="display: none;">
                                            <label for="description">Alasan</label>
                                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger">Laporkan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Tab Album -->
        <div class="tab-pane fade" id="albums" role="tabpanel" aria-labelledby="albums-tab">
            <h2 class="mt-5">Album</h2>
            @if(Auth::id() === $user->id)
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createAlbumModal">Buat Album Baru</button>
            @endif
            <div class="row">
                @foreach($albums as $album)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <a href="{{ route('albums.show', $album->id) }}">
                                <div class="album-cover">
                                    @foreach($album->photos->take(3) as $photo)
                                        <img src="{{ asset('storage/' . $photo->path) }}" class="album-cover-photo" alt="{{ $photo->title }}">
                                    @endforeach
                                </div>
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">{{ $album->name }}</h5>
                                <p class="card-text">{{ $album->description }}</p>
                                @if(Auth::id() === $album->user_id)
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton-{{ $album->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-bookmarks-fill "></i>                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $album->id }}">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editAlbumModal-{{ $album->id }}">Edit</a></li>
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteAlbumModal-{{ $album->id }}">Hapus</a></li>
                                        </ul>
                                    </div>
                                @endif
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
                                        <div class="form-group">
                                            <label for="albumName">Nama Album</label>
                                            <input type="text" class="form-control" name="name" value="{{ $album->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="albumDescription">Deskripsi</label>
                                            <textarea class="form-control" name="description" rows="3">{{ $album->description }}</textarea>
                                        </div>
                                        @if (Auth::check() && Auth::user()->role === 'pro')
                                            <div class="form-group">
                                                <label for="status" class="form-label">Visibilitas</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="1" {{ $album->status === '1' ? 'selected' : '' }}>Publik</option>
                                                    <option value="0" {{ $album->status === '0' ? 'selected' : '' }}>Privat</option>
                                                </select>
                                            </div>
                                        @endif
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
                                    Apakah Anda yakin ingin menghapus album ini?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <form method="POST" action="{{ route('albums.destroy', $album->id) }}">
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

<!-- Tab Langganan -->
@if(Auth::check() && $user->verified && ($hasSubscriptionPrice || Auth::id() === $user->id))
<div class="tab-pane fade" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
    <h2 class="mt-5 mb-4">Langganan</h2>
    
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
                <button class="btn btn-success" onclick="window.location.href='{{ route('photos.create') }}'">
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
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
                    <form action="{{ route('albums.store') }}" method="POST">
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
    
    <!-- Modal Report User -->
    <div class="modal fade" id="reportUserModal" tabindex="-1" role="dialog" aria-labelledby="reportUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportUserModalLabel">Laporkan Pengguna</h5>
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
                        <div class="form-group" id="description-group" style="display: none;">
                            <label for="description">Alasan</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">Laporkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
    
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const reasonRadios = document.querySelectorAll('input[name="reason"]');
        const descriptionGroup = document.getElementById("description-group");

        reasonRadios.forEach(radio => {
            radio.addEventListener("change", function() {
                if (this.value === "Lainnya") {
                    descriptionGroup.style.display = "block";
                } else {
                    descriptionGroup.style.display = "none";
                }
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateFollowButton(button, following) {
        if (following) {
            button.textContent = 'Unfollow';
            button.classList.remove('btn-success', 'follow-button');
            button.classList.add('btn-dark', 'unfollow-button');
        } else {
            button.textContent = 'Follow';
            button.classList.remove('btn-danger', 'unfollow-button');
            button.classList.add('btn-dark', 'follow-button');
        }
    }

    function handleFollowUnfollow(button) {
        const userId = button.getAttribute('data-user-id');
        const isUnfollow = button.classList.contains('unfollow-button');
        const url = isUnfollow ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;
        const token = '{{ csrf_token() }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => response.json())
        .then(data => {
            updateFollowButton(button, !isUnfollow);
            const followersCount = document.getElementById('followers-count');
            followersCount.textContent = data.followers_count;

            // Update followers list in the modal
            const followersList = document.getElementById('followers-list');
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

            // Update following list in the modal
            const followingList = document.getElementById('following-list');
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
        })
        .catch(error => console.error('Error:', error));
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
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateFollowButton(button, following) {
            if (following) {
                button.textContent = 'Unfollow';
                button.classList.remove('btn-primary', 'follow-button');
                button.classList.add('btn-danger', 'unfollow-button');
            } else {
                button.textContent = 'Follow';
                button.classList.remove('btn-danger', 'unfollow-button');
                button.classList.add('btn-primary', 'follow-button');
            }
        }

        function handleFollowUnfollow(button) {
            const userId = button.getAttribute('data-user-id');
            const isUnfollow = button.classList.contains('unfollow-button');
            const url = isUnfollow ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;
            const token = '{{ csrf_token() }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                updateFollowButton(button, !isUnfollow);
                const followersCount = document.getElementById('followers-count');
                followersCount.textContent = data.followers_count;

                // Update followers list in the modal
                const followersList = document.getElementById('followers-list');
                if (!isUnfollow) {
                    const newFollowerItem = document.createElement('li');
                    newFollowerItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                    newFollowerItem.innerHTML = `
                        <a href="/users/${data.current_user.username}"><b>${data.current_user.username}</b></a>
                        <button class="btn btn-sm btn-danger unfollow-button" data-user-id="${data.current_user.id}">Unfollow</button>
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

                // Update following list in the modal
                const followingList = document.getElementById('following-list');
                if (!isUnfollow) {
                    const newFollowingItem = document.createElement('li');
                    newFollowingItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                    newFollowingItem.innerHTML = `
                        <a href="/users/${data.current_user.username}"><b>${data.current_user.username}</b></a>
                        <button class="btn btn-sm btn-danger unfollow-button" data-user-id="${data.current_user.id}">Unfollow</button>
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
            })
            .catch(error => console.error('Error:', error));
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
    });
</script>
@endpush