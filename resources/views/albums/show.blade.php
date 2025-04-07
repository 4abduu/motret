@extends('layouts.app')

@push('link')
<style>
/* ===== ROOT VARIABLES ===== */
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

/* ===== BASE STYLES ===== */
.container {
    padding: 2rem 15px;
}

/* ===== ALBUM HEADER ===== */
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

/* ===== TITLE & DESCRIPTION SECTION ===== */
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
    cursor: pointer;
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

/* ===== EDIT ICON STYLES ===== */
.edit-icon-container {
    position: absolute;
    left: 40%;
    top: 50%;
    transform: translateY(-50%);
}

.edit-icon {
    font-size: 1.2em;
    opacity: 0.7;
    transition: all var(--transition-speed);
    cursor: pointer;
    background: rgba(255,255,255,0.2);
    padding: 8px;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.edit-icon:hover {
    opacity: 1;
    transform: scale(1.1);
    background: rgba(255,255,255,0.3);
}

/* ===== EDIT MODE STYLES ===== */
.edit-container {
    width: 100%;
    margin-bottom: 0.5rem;
}

.edit-input {
    width: 100%;
    padding: 0.75rem;
    border-radius: 8px;
    border: 2px solid rgba(255,255,255,0.4);
    background: rgba(255,255,255,0.15);
    color: white;
    font-family: inherit;
    box-sizing: border-box;
}

.edit-title {
    font-size: 2.2rem;
    font-weight: 700;
}

.edit-description {
    font-size: 1.15rem;
    line-height: 1.6;
    min-height: 120px;
    resize: none;
}

/* ===== ACTION BUTTONS ===== */
.button-container {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.btn-save, .btn-cancel {
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-speed);
    padding: 0.5rem 1.25rem;
    font-family: inherit;
}

.btn-save {
    background-color: white;
    color: var(--primary-dark);
}

.btn-save:hover {
    background-color: #f0f0f0;
    transform: translateY(-2px);
}

.btn-cancel {
    background-color: rgba(255,255,255,0.1);
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
}

.btn-cancel:hover {
    background-color: rgba(255,255,255,0.2);
    transform: translateY(-2px);
}

/* ===== VISIBILITY TOGGLE ===== */
.visibility-toggle {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255, 255, 255, 0.15);
    padding: 0.6rem 1.2rem;
    border-radius: 50px;
    cursor: pointer;
    transition: all var(--transition-speed);
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255,255,255,0.1);
    z-index: 1;
}

.visibility-toggle:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.visibility-text {
    font-size: 0.95rem;
    font-weight: 500;
}

/* ===== PHOTO GRID ===== */
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

/* Animation delays for photo cards */
.photo-card:nth-child(1) { animation-delay: 0.1s; }
.photo-card:nth-child(2) { animation-delay: 0.2s; }
.photo-card:nth-child(3) { animation-delay: 0.3s; }
.photo-card:nth-child(4) { animation-delay: 0.4s; }
.photo-card:nth-child(5) { animation-delay: 0.5s; }
.photo-card:nth-child(6) { animation-delay: 0.6s; }
.photo-card:nth-child(7) { animation-delay: 0.7s; }
.photo-card:nth-child(8) { animation-delay: 0.8s; }

.photo-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.18);
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

.photo-actions {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    z-index: 3;
    opacity: 0;
    transition: opacity var(--transition-speed);
}

.photo-card:hover .photo-actions {
    opacity: 1;
}

.photo-menu-btn {
    background: rgba(0, 0, 0, 0.6);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-speed);
    backdrop-filter: blur(5px);
}

.photo-menu-btn:hover {
    background: rgba(0, 0, 0, 0.8);
    transform: scale(1.1);
}

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--primary-light);
    border-radius: 16px;
    margin: 3rem 0;
    color: var(--dark-color);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px dashed rgba(50,189,64,0.3);
}

.empty-state-icon {
    font-size: 3.5rem;
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    opacity: 0.8;
}

.empty-state h4 {
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.empty-state p {
    max-width: 500px;
    margin: 0 auto 1.5rem;
    color: var(--dark-color);
    opacity: 0.8;
}

/* ===== BUTTONS ===== */
.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    padding: 0.65rem 1.5rem;
    border-radius: 50px;
    font-weight: 500;
    transition: all var(--transition-speed);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(50,189,64,0.3);
}

/* ===== ANIMATIONS ===== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ===== RESPONSIVE STYLES ===== */
@media (max-width: 992px) {
    .photo-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
    
    .title-content, .description-content {
        max-width: 50%;
    }
}

@media (max-width: 768px) {
    .album-header {
        padding: 1.5rem;
    }
    
    .photo-grid {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1.25rem;
    }
    
    .album-title {
        font-size: 1.6rem;
        line-height: 1.3;
    }
    
    .album-description {
        font-size: 1rem;
    }
    
    .visibility-toggle {
        position: static;
        margin-top: 1rem;
        justify-content: flex-end;
        display: inline-flex;
    }

    .empty-state {
        padding: 3rem 1.5rem;
    }
    
    .title-content, .description-content {
        max-width: 70%;
    }
    
    .edit-icon {
        width: 32px;
        height: 32px;
        padding: 7px;
    }
}

@media (max-width: 576px) {
    .photo-grid {
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .album-title {
        font-size: 1.4rem;
        white-space: normal;
        line-height: 1.3;
    }

    .empty-state-icon {
        font-size: 2.5rem;
    }
    
    .title-content, .description-content {
        max-width: 100%;
    }
    
    .edit-icon {
        width: 30px;
        height: 30px;
        padding: 6px;
        font-size: 1em;
    }
    
    .title-section, .description-section {
        flex-direction: row;
        align-items: center;
    }
    
    .album-description {
        font-size: 0.95rem;
    }

    .edit-icon-container {
        position: relative;
        left: auto;
        top: auto;
        transform: none;
        margin-left: 1rem;
    }
}

.read-more {
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
    cursor: pointer;
    display: inline-block;
    margin-left: 4px;
    transition: all var(--transition-speed);
    background: rgba(0,0,0,0.2);
    padding: 2px 8px;
    border-radius: 12px;
    white-space: nowrap;
}

.read-more:hover {
    color: white;
    background: rgba(0,0,0,0.3);
}
</style>
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
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // --- DESCRIPTION HANDLING ---
    function setupDescription() {
        const descElement = document.getElementById('album-description');
        if (!descElement) return;
        
        const fullText = descElement.getAttribute('data-full-text');
        const isMobile = window.innerWidth <= 768;
        const charLimit = isMobile ? 100 : 150;
        
        if (fullText.length > charLimit) {
            const visibleText = fullText.substring(0, charLimit);
            const hiddenText = fullText.substring(charLimit);
            
            descElement.innerHTML = `
                <span class="visible-text">${visibleText}</span>
                <span class="hidden-text" style="display:none">${hiddenText}</span>
                <span class="read-more">...Lainnya</span>
            `;
        } else {
            descElement.textContent = fullText;
        }
    }
    
    function handleResize() {
        setupDescription();
    }
    
    function toggleDescription(e) {
        const readMoreBtn = e.target.closest('.read-more');
        if (!readMoreBtn) return;
        
        e.stopPropagation();
        const descElement = readMoreBtn.closest('.album-description');
        const fullText = descElement.getAttribute('data-full-text');
        const isExpanded = readMoreBtn.textContent.includes('Sembunyikan');
        
        if (isExpanded) {
            const isMobile = window.innerWidth <= 768;
            const charLimit = isMobile ? 100 : 150;
            const visibleText = fullText.substring(0, charLimit);
            
            descElement.innerHTML = `
                <span class="visible-text">${visibleText}</span>
                <span class="hidden-text" style="display:none">${fullText.substring(charLimit)}</span>
                <span class="read-more">...Lainnya</span>
            `;
        } else {
            descElement.innerHTML = `
                ${fullText}
                <span class="read-more">Sembunyikan</span>
            `;
        }
    }
    
    setupDescription();
    window.addEventListener('resize', handleResize);
    document.addEventListener('click', toggleDescription);
    
    // --- EDIT FUNCTIONALITY ---
    function enableEdit(element, type) {
        const id = element.dataset.id;
        const currentValue = element.dataset.fullText || 
                           element.textContent.replace(/\.\.\.Lainnya|Sembunyikan/g, '').trim();
        
        const editDiv = document.createElement('div');
        editDiv.className = 'edit-container';
        
        const inputField = type === 'description' ? 
            document.createElement('textarea') : 
            document.createElement('input');
            
        inputField.className = `edit-input ${type === 'description' ? 'edit-description' : 'edit-title'}`;
        inputField.value = currentValue;
        
        const buttonsDiv = document.createElement('div');
        buttonsDiv.className = 'button-container';
        buttonsDiv.innerHTML = `
            <button class="btn-cancel">Batal</button>
            <button class="btn-save">Simpan</button>
        `;
        
        editDiv.append(inputField, buttonsDiv);
        element.replaceWith(editDiv);
        inputField.focus();
        
        buttonsDiv.querySelector('.btn-cancel').addEventListener('click', function() {
            editDiv.replaceWith(element);
        });
        
        buttonsDiv.querySelector('.btn-save').addEventListener('click', async function() {
            const newValue = inputField.value.trim();
            if (newValue !== currentValue) {
                await saveChanges(id, type, newValue, editDiv, element);
                element.dataset.fullText = newValue;
                updateDescriptionDisplay(element, newValue);
            }
            editDiv.replaceWith(element);
        });
        
        inputField.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                buttonsDiv.querySelector('.btn-cancel').click();
            }
            if (type === 'title' && e.key === 'Enter') {
                buttonsDiv.querySelector('.btn-save').click();
            }
        });
    }
    
    async function saveChanges(albumId, fieldType, newValue, editContainer, originalElement) {
        const loadingDiv = document.createElement('div');
        loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        editContainer.appendChild(loadingDiv);
        
        try {
            const endpoint = `/albums/${albumId}/update${fieldType === 'title' ? 'Title' : 'Description'}`;
            const response = await fetch(endpoint, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    [fieldType === 'title' ? 'name' : 'description']: newValue
                })
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || 'Gagal menyimpan perubahan');
            }
            
            return result;
            
        } catch (error) {
            console.error('Error:', error);
            showError('Gagal menyimpan: ' + error.message);
            throw error;
        } finally {
            loadingDiv.remove();
        }
    }
    
    function updateDescriptionDisplay(element, text) {
        const isMobile = window.innerWidth <= 768;
        const charLimit = isMobile ? 100 : 150;
        const shouldTruncate = text.length > charLimit;
        
        element.innerHTML = shouldTruncate 
            ? `${text.substring(0, charLimit)}<span class="read-more">...Lainnya</span>`
            : text;
    }
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-icon')) {
            e.stopPropagation();
            const targetElement = document.getElementById(e.target.dataset.target);
            if (targetElement) {
                enableEdit(targetElement, e.target.dataset.type);
            }
        }
    });
    
    // --- VISIBILITY TOGGLE ---
    const visibilityToggle = document.getElementById('visibility-toggle');
    if (visibilityToggle) {
        visibilityToggle.addEventListener('click', async function() {
            const albumId = this.dataset.id;
            const icon = this.querySelector('i');
            const textElement = this.querySelector('.visibility-text');
            const isCurrentlyPublic = icon.classList.contains('fa-eye');
            
            this.style.pointerEvents = 'none';
            textElement.textContent = 'Memproses...';
            
            // Remove current icon and add spinner
            icon.classList.remove(isCurrentlyPublic ? 'fa-eye' : 'fa-eye-slash');
            icon.classList.add('fa-spinner', 'fa-spin');
            
            try {
                const response = await fetch(`/albums/${albumId}/updateVisibility`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        status: isCurrentlyPublic ? 0 : 1 
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Gagal mengubah visibilitas');
                }
                
                const result = await response.json();
                
                if (result.success) {
                    // Remove spinner and add new icon
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add(result.status == 1 ? 'fa-eye' : 'fa-eye-slash');
                    textElement.textContent = result.status == 1 ? 'Publik' : 'Privat';
                    
                    showSuccess(`Album sekarang ${result.status == 1 ? 'Publik' : 'Privat'}`);
                }
                
            } catch (error) {
                console.error('Error:', error);
                
                // Revert to original state
                icon.classList.remove('fa-spinner', 'fa-spin');
                icon.classList.add(isCurrentlyPublic ? 'fa-eye' : 'fa-eye-slash');
                textElement.textContent = isCurrentlyPublic ? 'Publik' : 'Privat';
                
                showError('Gagal mengubah visibilitas');
            } finally {
                this.style.pointerEvents = 'auto';
            }
        });
    }
    
    // --- PHOTO MANAGEMENT ---
    document.querySelectorAll('.photo-menu-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            confirmDeletePhoto(
                this.dataset.photoId, 
                this.dataset.albumId
            );
        });
    });
    
    function confirmDeletePhoto(photoId, albumId) {
        Swal.fire({
            title: 'Hapus Foto?',
            html: '<p>Foto akan dihapus dari album ini</p>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary-color)',
            cancelButtonColor: 'var(--danger-color)',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            background: 'var(--primary-light)',
            color: 'var(--dark-color)'
        }).then((result) => {
            if (result.isConfirmed) {
                deletePhoto(photoId, albumId);
            }
        });
    }
    
    async function deletePhoto(photoId, albumId) {
        Swal.fire({
            title: 'Menghapus...',
            html: 'Sedang menghapus foto dari album',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
            background: 'var(--primary-light)',
            color: 'var(--dark-color)'
        });
        
        try {
            const response = await fetch(`/albums/${albumId}/removePhoto/${photoId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            if (!response.ok) {
                throw new Error('Gagal menghapus foto');
            }
            
            const result = await response.json();
            
            if (result.success) {
                showSuccess('Foto telah dihapus', () => {
                    const photoCard = document.querySelector(
                        `.photo-menu-btn[data-photo-id="${photoId}"]`
                    )?.closest('.photo-card');
                    
                    if (photoCard) {
                        photoCard.style.transform = 'scale(0.9)';
                        photoCard.style.opacity = '0';
                        setTimeout(() => photoCard.remove(), 300);
                    }
                });
            }
            
        } catch (error) {
            console.error('Error:', error);
            showError('Gagal menghapus foto');
        }
    }
    
    // --- HELPER FUNCTIONS ---
    function showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            background: '#32bd40',
            iconColor: '#fff',
            color: '#fff',
            timerProgressBar: true
        });
    }

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            background: '#d9534f',
            iconColor: '#fff',
            color: '#fff',
            timerProgressBar: true
        });
    }


});
</script>
@endpush