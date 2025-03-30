@extends('layouts.app')

@push('link')
    <style>
        :root {
            --primary-color: #3a86ff;
            --success-color: #2a9d36;
            --danger-color: #e63946;
            --dark-color: #1d3557;
            --light-color: #f1faee;
        }

        /* Album Header */
        .album-header {
            padding: 1.5rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .album-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .album-title:hover {
            opacity: 0.9;
        }

        .album-description {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .album-description:hover {
            opacity: 1;
        }

        .edit-icon {
            font-size: 0.8em;
            opacity: 0.7;
            transition: all 0.2s ease;
        }

        .album-title:hover .edit-icon,
        .album-description:hover .edit-icon {
            opacity: 1;
        }

        /* Visibility Toggle */
        .visibility-toggle {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .visibility-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .visibility-text {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Photo Grid */
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .photo-card {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            aspect-ratio: 1;
        }

        .photo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .photo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            padding: 1rem;
            color: white;
        }

        .photo-title {
            font-weight: 600;
            margin-bottom: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .photo-actions {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
        }

        .photo-menu-btn {
            background: rgba(0, 0, 0, 0.5);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .photo-menu-btn:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 2rem 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .photo-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }
            
            .album-title {
                font-size: 1.5rem;
            }
            
            .visibility-toggle {
                position: static;
                margin-top: 1rem;
                justify-content: flex-end;
            }
        }

        @media (max-width: 576px) {
            .photo-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Album Header -->
    <div class="album-header">
        <h2 id="album-title" class="album-title editable" data-id="{{ $album->id }}">
            {{ $album->name }}
            @if(Auth::check() && Auth::id() === $album->user_id)
                <i class="fas fa-pencil-alt edit-icon"></i>
            @endif
        </h2>
        
        <p id="album-description" class="album-description editable" data-id="{{ $album->id }}">
            {{ $album->description }}
            @if(Auth::check() && Auth::id() === $album->user_id)
                <i class="fas fa-pencil-alt edit-icon"></i>
            @endif
        </p>
        
        @if(Auth::check() && Auth::id() === $album->user_id && Auth::user()->role === 'pro')
            <div class="visibility-toggle" id="visibility-toggle" data-id="{{ $album->id }}">
                <i class="fas {{ $album->status ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                <span class="visibility-text">{{ $album->status ? 'Publik' : 'Privat' }}</span>
            </div>
        @endif
    </div>

    <!-- Photo Grid -->
    @if($album->photos->isEmpty())
        <div class="empty-state">
            <i class="fas fa-images fa-3x mb-3" style="color: #ddd;"></i>
            <h4>Album ini masih kosong</h4>
            <p class="text-muted">Tambahkan foto untuk mengisi album Anda</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus"></i> Tambahkan Foto
            </a>
        </div>
    @else
        <div class="photo-grid">
            @foreach($album->photos as $photo)
                <div class="photo-card">
                    <a href="{{ route('photos.show', $photo->id) }}">
                        <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}">
                        <div class="photo-overlay">
                            <h3 class="photo-title">{{ $photo->title }}</h3>
                        </div>
                    </a>
                    
                    @if(Auth::check() && Auth::id() === $album->user_id)
                        <div class="photo-actions">
                            <button class="photo-menu-btn" data-photo-id="{{ $photo->id }}">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Delete Photo Modal (Dynamic content will be inserted by JavaScript) -->
<div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus foto ini dari album?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Make elements editable (title and description)
    function makeEditable(element, type) {
        const id = element.getAttribute('data-id');
        const originalText = element.textContent.trim();
        
        // Create input element
        const input = document.createElement(type === 'description' ? 'textarea' : 'input');
        input.value = originalText;
        input.className = 'form-control';
        
        // For description textarea
        if (type === 'description') {
            input.rows = 3;
        }
        
        // Handle save on blur or Enter key
        const saveChanges = () => {
            const newValue = input.value.trim();
            if (newValue !== originalText) {
                updateAlbumField(id, type, newValue);
            } else {
                element.innerHTML = `${originalText} <i class="fas fa-pencil-alt edit-icon"></i>`;
            }
        };
        
        input.addEventListener('blur', saveChanges);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && type !== 'description') {
                saveChanges();
            }
        });
        
        // Replace element content with input
        element.innerHTML = '';
        element.appendChild(input);
        input.focus();
    }
    
    // Update album field via AJAX
    function updateAlbumField(albumId, field, value) {
        const endpoint = `/albums/${albumId}/update${field.charAt(0).toUpperCase() + field.slice(1)}`;
        
        fetch(endpoint, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ [field]: value })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                const element = document.getElementById(`album-${field}`);
                element.innerHTML = `${data[field]} <i class="fas fa-pencil-alt edit-icon"></i>`;
                
                // Show success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `Album ${field} telah diperbarui`,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memperbarui album'
            });
        });
    }
    
    // Toggle album visibility (for Pro users)
    const visibilityToggle = document.getElementById('visibility-toggle');
    if (visibilityToggle) {
        visibilityToggle.addEventListener('click', function() {
            const albumId = this.getAttribute('data-id');
            const icon = this.querySelector('i');
            const text = this.querySelector('.visibility-text');
            const isPublic = icon.classList.contains('fa-eye');
            
            fetch(`/albums/${albumId}/toggleVisibility`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ status: isPublic ? 0 : 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    if (data.status) {
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                        text.textContent = 'Publik';
                    } else {
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                        text.textContent = 'Privat';
                    }
                    
                    // Show notification
                    Swal.fire({
                        icon: 'success',
                        title: 'Visibilitas Diubah',
                        text: `Album sekarang ${data.status ? 'Publik' : 'Privat'}`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengubah visibilitas'
                });
            });
        });
    }
    
    // Photo delete functionality
    const deleteModal = new bootstrap.Modal(document.getElementById('deletePhotoModal'));
    let currentPhotoId = null;
    let currentAlbumId = {{ $album->id }};
    
    document.querySelectorAll('.photo-menu-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentPhotoId = this.getAttribute('data-photo-id');
            deleteModal.show();
        });
    });
    
    document.getElementById('confirm-delete').addEventListener('click', function() {
        fetch(`/albums/${currentAlbumId}/removePhoto/${currentPhotoId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                deleteModal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Foto Dihapus',
                    text: 'Foto telah dihapus dari album',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Refresh after delete
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat menghapus foto'
            });
        });
    });
    
    // Add click handlers for editable elements
    document.querySelectorAll('.editable').forEach(element => {
        if (element.querySelector('.edit-icon')) {
            element.addEventListener('click', function() {
                const type = this.id.includes('title') ? 'title' : 'description';
                makeEditable(element, type);
            });
        }
    });
});
</script>
@endpush