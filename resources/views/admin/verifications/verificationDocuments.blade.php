@extends('layouts.app')

@section('title', 'Dokumen Verifikasi')

@push('link')
<style>
/* Modal Zoom Styles */
.photo-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    justify-content: center;
    align-items: center;
    overflow: hidden;
    z-index: 9999;
    touch-action: none;
}

.modal-content {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    cursor: grab;
    transform-origin: 0 0;
    user-select: none;
    transition: transform 0.15s ease-out;
}


.modal-content.grabbing {
    cursor: grabbing;
}

.close-modal {
    position: absolute;
    top: 20px;
    right: 30px;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1001;
    transition: 0.3s;
}

.close-modal:hover {
    color: #bbb;
}

#zoom-controls {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 15px;
    z-index: 1001;
}

#zoom-controls button {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.2s;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    border-radius: 50%;
}

#zoom-controls button:hover {
    transform: scale(1.1);
    background: rgba(255, 255, 255, 0.3);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .close-modal {
        top: 15px;
        right: 20px;
        font-size: 30px;
    }
    
    #zoom-controls {
        bottom: 20px;
    }
    
    #zoom-controls button {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
}

/* Prevent scrolling when modal is open */
body.modal-open {
    overflow: hidden;
}
</style>
@endpush

@section('content')

<div class="row">
    <h3>Dokumen Verifikasi</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.verificationRequests') }}" class="text-success">Permintaan Verifikasi</a></li>
        <li class="breadcrumb-item active">Dokumen Verifikasi</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Dokumen Verifikasi untuk {{ $verificationRequest->full_name }}</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Jenis Dokumen</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($verificationRequest->documents as $document)
                                <tr>
                                    <td>{{ ucfirst($document->file_type) }}</td>
                                    <td>
                                        <button onclick="openDocumentModal('{{ asset('storage/' . $document->file_path) }}')" 
                                                class="btn btn-info btn-sm" style="color: white;">
                                            Lihat Dokumen
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.verificationRequests') }}" class="btn btn-success text-white mt-3">Kembali</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Zoom Dokumen -->
<div id="photo-modal" class="photo-modal">
    <span class="close-modal">&times;</span>
    <img id="modal-img" class="modal-content">
    <div id="zoom-controls">
        <button id="zoom-in" class="btn btn-light rounded-circle"><i class="bi bi-zoom-in"></i></button>
        <button id="zoom-out" class="btn btn-light rounded-circle"><i class="bi bi-zoom-out"></i></button>
        <button id="reset-zoom" class="btn btn-light rounded-circle"><i class="bi bi-arrow-counterclockwise"></i></button>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
<script>
// Fungsi untuk membuka modal dokumen
function openDocumentModal(src) {
    const modal = document.getElementById("photo-modal");
    const modalImg = document.getElementById("modal-img");
    
    modal.style.display = 'flex';
    modalImg.src = src;
    
    // Reset transform
    currentScale = 1;
    posX = 0;
    posY = 0;
    updateTransform();
    
    // Tambahkan event listeners
    modalImg.addEventListener('wheel', handleWheel, { passive: false });
    modalImg.addEventListener('mousedown', handleMouseDown);
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);
    
    // Inisialisasi gesture touch
    initHammer();
    
    // Cegah scrolling body
    document.body.classList.add('modal-open');
}

// ==================== FITUR ZOOM GAMBAR ====================

// Inisialisasi modal zoom gambar
const modal = document.getElementById("photo-modal");
const modalImg = document.getElementById("modal-img");
const closeModal = document.querySelector(".close-modal");
const zoomInBtn = document.getElementById("zoom-in");
const zoomOutBtn = document.getElementById("zoom-out");
const resetZoomBtn = document.getElementById("reset-zoom");

// State zoom
let currentScale = 1;
let posX = 0;
let posY = 0;
const MIN_SCALE = 0.5;
const MAX_SCALE = 4;
let isDragging = false;
let startX, startY;
let hammer;

// Fungsi untuk update transform gambar
function updateTransform() {
    modalImg.style.transform = `translate(${posX}px, ${posY}px) scale(${currentScale})`;
}

// Fungsi untuk membatasi skala zoom
function clampScale(scale) {
    return Math.max(MIN_SCALE, Math.min(MAX_SCALE, scale));
}

// Inisialisasi Hammer.js untuk gesture touch
function initHammer() {
    if (hammer) hammer.destroy();
    
    hammer = new Hammer(modalImg, {
        recognizers: [
            [Hammer.Pan, { direction: Hammer.DIRECTION_ALL }],
            [Hammer.Pinch],
            [Hammer.Tap, { event: 'doubletap', taps: 2 }]
        ]
    });

    let initialScale, initialPosX, initialPosY;

    // Gesture pan (geser)
    hammer.on('panstart', function() {
        if (currentScale > 1) {
            initialPosX = posX;
            initialPosY = posY;
            modalImg.style.cursor = 'grabbing';
        }
    });

    hammer.on('pan', function(e) {
        if (currentScale > 1) {
            posX = initialPosX + e.deltaX;
            posY = initialPosY + e.deltaY;
            updateTransform();
        }
    });

    hammer.on('panend', function() {
        modalImg.style.cursor = currentScale > 1 ? 'grab' : 'default';
    });

    // Gesture pinch zoom
    hammer.on('pinchstart', function() {
        initialScale = currentScale;
    });

    hammer.on('pinch', function(e) {
        const newScale = clampScale(initialScale * e.scale);
        if (newScale !== currentScale) {
            currentScale = newScale;
            
            const rect = modalImg.getBoundingClientRect();
            const centerX = (e.center.x - rect.left - posX) / currentScale;
            const centerY = (e.center.y - rect.top - posY) / currentScale;
            
            posX = e.center.x - rect.left - centerX * currentScale;
            posY = e.center.y - rect.top - centerY * currentScale;
            
            updateTransform();
        }
    });

    // Double tap untuk zoom in/out
    hammer.on('doubletap', function(e) {
        const rect = modalImg.getBoundingClientRect();
        const tapX = e.center.x - rect.left;
        const tapY = e.center.y - rect.top;
        
        if (currentScale > 1) {
            // Reset zoom
            currentScale = 1;
            posX = 0;
            posY = 0;
        } else {
            // Zoom in 2x pada posisi tap
            currentScale = 2;
            posX = -(tapX * (currentScale - 1));
            posY = -(tapY * (currentScale - 1));
        }
        updateTransform();
    });
}

// Event listener untuk mouse wheel zoom
function handleWheel(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const delta = e.deltaY < 0 ? 1.1 : 0.9;
    const newScale = clampScale(currentScale * delta);
    
    if (newScale !== currentScale) {
        const rect = modalImg.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        posX = x - (x - posX) * (newScale / currentScale);
        posY = y - (y - posY) * (newScale / currentScale);
        currentScale = newScale;
        
        updateTransform();
    }
}

// Event listener untuk mouse down (drag)
function handleMouseDown(e) {
    if (e.button === 0 && currentScale > 1) {
        isDragging = true;
        startX = e.clientX - posX;
        startY = e.clientY - posY;
        modalImg.style.cursor = 'grabbing';
        e.preventDefault();
    }
}

// Event listener untuk mouse move (drag)
function handleMouseMove(e) {
    if (isDragging) {
        posX = e.clientX - startX;
        posY = e.clientY - startY;
        updateTransform();
    }
}

// Event listener untuk mouse up (drag)
function handleMouseUp(e) {
    if (e.button === 0) {
        isDragging = false;
        modalImg.style.cursor = currentScale > 1 ? 'grab' : 'default';
    }
}

// Blokir klik kanan pada gambar
modalImg.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

// Tutup modal zoom
closeModal.addEventListener('click', function() {
    modal.style.display = 'none';
    // Hapus event listeners
    modalImg.removeEventListener('wheel', handleWheel);
    modalImg.removeEventListener('mousedown', handleMouseDown);
    document.removeEventListener('mousemove', handleMouseMove);
    document.removeEventListener('mouseup', handleMouseUp);
    
    // Aktifkan kembali scrolling body
    document.body.classList.remove('modal-open');
});

// Klik di luar gambar untuk tutup modal
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
});

// Tombol zoom in
zoomInBtn.addEventListener('click', function() {
    const newScale = clampScale(currentScale * 1.2);
    if (newScale !== currentScale) {
        const rect = modalImg.getBoundingClientRect();
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        posX = -(centerX * (newScale - currentScale)) + posX * (newScale / currentScale);
        posY = -(centerY * (newScale - currentScale)) + posY * (newScale / currentScale);
        currentScale = newScale;
        
        updateTransform();
    }
});

// Tombol zoom out
zoomOutBtn.addEventListener('click', function() {
    const newScale = clampScale(currentScale * 0.8);
    if (newScale !== currentScale) {
        const rect = modalImg.getBoundingClientRect();
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        posX = (centerX * (currentScale - newScale)) + posX * (newScale / currentScale);
        posY = (centerY * (currentScale - newScale)) + posY * (newScale / currentScale);
        currentScale = newScale;
        
        updateTransform();
    }
});

// Tombol reset zoom
resetZoomBtn.addEventListener('click', function() {
    currentScale = 1;
    posX = 0;
    posY = 0;
    updateTransform();
});
</script>
@endpush