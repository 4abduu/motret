@push('scripts')
<script src="https://unpkg.com/panzoom@9.4.0/dist/panzoom.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/panzoom/9.4.1/panzoom.min.js"></script>
<script>
// Fungsi untuk menyalin URL ke clipboard
function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
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
            timerProgressBar: true
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
            timer: 2000
        });
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Variabel global
    const token = '{{ csrf_token() }}';
    const photoId = {{ $photo->id }};
    const currentUserId = {{ Auth::id() ?? 'null' }};
    const photoUserId = {{ $photo->user_id }};

    // ==================== FITUR ZOOM GAMBAR ====================
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

    // Buka modal zoom - PERBAIKAN UTAMA DI SINI
    const openModalBtn = document.getElementById('open-modal');
    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
            
            @if(Auth::check() && (Auth::id() === $photo->user_id || Auth::user()->role === 'pro'))
                // Untuk pemilik dan pro user, tampilkan gambar asli dari img tag
                modalImg.src = document.getElementById('photoImg').src;
            @else
                // Untuk user biasa/guest, tampilkan gambar dari canvas
                const photoCanvas = document.getElementById('photoCanvas');
                modalImg.src = photoCanvas.dataset.src;
            @endif
            
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
        });
    }

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
            
            posX = (centerX * (currentScale - newScale) + posX * (newScale / currentScale));
            posY = (centerY * (currentScale - newScale) + posY * (newScale / currentScale));
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

    // ==================== FUNGSI UTAMA ====================

    // Fungsi untuk menampilkan SweetAlert
    function showAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            background: icon === 'success' ? '#32bd40' : '#d9534f',
            iconColor: '#fff',
            color: '#fff',
            timerProgressBar: true
        });
    }
    
    function handleDownload(event) {
        event.preventDefault();

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
                            document.getElementById('downloadForm').submit();
                        }
                    });
                }
            });
        @else
            document.getElementById('downloadForm').submit();
        @endif
    }

    // Tambahkan event listener ke tombol download
    document.getElementById("downloadButton").addEventListener("click", handleDownload);

    // ==================== SISTEM KOMENTAR ====================

    // Fungsi untuk membuat elemen komentar baru
    function createCommentElement(comment, user, isCurrentUser) {
        const isPhotoOwner = user.id === {{ $photo->user_id }};
        const profilePhoto = user.profile_photo 
            ? `<img src="/storage/photo_profile/${user.profile_photo}" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30">`
            : `<img src="/images/foto profil.jpg" alt="Profile Picture" class="rounded-circle me-2" width="30" height="30"/>`;
        
        const verifiedIcon = user.verified ? '<i class="ti-crown" style="color: gold;"></i>' : '';
        const proIcon = user.role === 'pro' ? '<i class="ti-star" style="color: gold;"></i>' : '';
        const photoOwnerBadge = isPhotoOwner ? '<span class="text">• Pembuat</span>' : '';
        
        return `
            <div class="card mb-2" data-comment-id="${comment.id}">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center mb-1">
                        ${profilePhoto}
                        <strong>
                            <a href="/${user.username}" class="text-dark fw-bold text-decoration-none">
                                ${user.username}
                            </a>
                        </strong>
                        ${verifiedIcon}
                        ${proIcon}
                        ${photoOwnerBadge}
                    </div>
                    <p class="mb-1 ms-4">${comment.comment}</p>
                    <div class="d-flex align-items-center ms-4 mt-1">
                        <small class="text-muted me-2" style="font-size: 13px;">
                            Baru saja
                        </small>
                        <button class="btn btn-link p-0 reply-button" data-comment-id="${comment.id}">
                            <i class="bi bi-reply"></i>
                        </button>
                        ${isCurrentUser ? `
                        <div class="dropdown ms-2">
                            <button class="btn btn-link p-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button class="dropdown-item delete-comment" data-comment-id="${comment.id}">
                                        Hapus Komentar
                                    </button>
                                </li>
                            </ul>
                        </div>
                        ` : ''}
                    </div>
                    <!-- Form reply untuk komentar baru -->
                    <div class="reply-form" id="reply-form-${comment.id}" style="display: none;">
                        <form method="POST" action="/comments/${comment.id}/reply">
                            @csrf
                            <div class="input-group mb-3 ms-4">
                                <input type="text" class="form-control" name="reply" placeholder="Tambahkan balasan..." required>
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-send-fill text-dark fw-bold fs-5 rotate-90"></i>
                                    </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    // Fungsi untuk membuat elemen balasan baru
    function createReplyElement(reply, user, isCurrentUser, photoUserId) {
        const isPhotoOwner = user.id === photoUserId;
        const profilePhoto = user.profile_photo
            ? `<img src="/storage/photo_profile/${user.profile_photo}" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25">`
            : `<img src="/images/foto profil.jpg" alt="Profile Picture" class="rounded-circle me-2" width="25" height="25"/>`;

        const verifiedIcon = user.verified ? '<i class="ti-crown" style="color: gold;"></i>' : '';
        const proIcon = user.role === 'pro' ? '<i class="ti-star" style="color: gold;"></i>' : '';
        const photoOwnerBadge = isPhotoOwner ? '<span class="text">• Pembuat</span>' : '';

        return `
        <div class="ms-4 mt-1" data-reply-id="${reply.id}">
            <div class="card">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center mb-1">
                        ${profilePhoto}
                        <strong>
                            <a href="/${user.username}" class="text-dark fw-bold text-decoration-none">
                                ${user.username}
                            </a>
                        </strong>
                        ${verifiedIcon} ${proIcon}
                        ${isPhotoOwner ? '<span class="text">• Pembuat</span>' : ''}
                    </div>
                    <p class="mb-1 ms-4">${reply.reply}</p>
                    <div class="d-flex align-items-center ms-4 mt-1">
                        <small class="text-muted" style="font-size: 12px; margin-top: -2px;">
                            Baru saja
                        </small>
                        ${isCurrentUser ? `
                        <div class="dropdown ms-2" style="margin-top: -15px;">
                            <button class="btn btn-link" type="button" id="dropdownMenuButton-${reply.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-${reply.id}">
                                <li>
                                    <button type="button" class="dropdown-item delete-reply" data-reply-id="${reply.id}">
                                        Hapus Balasan
                                    </button>
                                </li>
                            </ul>
                        </div>` : `
                        <div class="dropdown ms-2" style="margin-top: -15px;">
                            <button class="btn btn-link" type="button" id="dropdownMenuButton-${reply.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-${reply.id}">
                                <li>
                                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportReplyModal-${reply.id}">
                                        Lapor Balasan
                                    </button>
                                </li>
                            </ul>
                        </div>`}
                    </div>
                </div>
            </div>
        </div>
        `;
    }

    // ==================== EVENT LISTENERS ====================

    // Form komentar utama
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            if (e.target !== commentForm) return;

            e.preventDefault();

            const formData = new FormData(commentForm);

            try {
                const response = await fetch(commentForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Gagal menambahkan komentar');

                const data = await response.json();

                if (data.success) {
                    commentForm.reset();

                    const commentContainer = document.querySelector('.comment-container');
                    const newComment = createCommentElement(data.comment, data.comment.user, true);
                    commentContainer.insertAdjacentHTML('beforeend', newComment);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Komentar berhasil ditambahkan',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', error.message, 'error');
            }
        });
    }

    // Fungsi untuk handle reply button (delegasi event)
    document.addEventListener('click', function(e) {
        // Handle tombol reply
        if (e.target.closest('.reply-button')) {
            e.preventDefault();
            const button = e.target.closest('.reply-button');
            const commentId = button.getAttribute('data-comment-id');
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            
            // Tutup semua form reply lain
            document.querySelectorAll('.reply-form').forEach(form => {
                if (form.id !== `reply-form-${commentId}`) {
                    form.style.display = 'none';
                }
            });
            
            // Toggle form yang dipilih
            if (replyForm.style.display === 'none' || !replyForm.style.display) {
                replyForm.style.display = 'block';
                replyForm.querySelector('input').focus();
            } else {
                replyForm.style.display = 'none';
            }
        }

        // Handle submit reply
        if (e.target.closest('.reply-form form')) {
            e.preventDefault();
            const form = e.target.closest('form');
            submitReplyForm(form);
        }
    });

    async function submitReplyForm(form) {
        try {
            const replyText = form.querySelector('[name="reply"]').value.trim();
            if (!replyText) return;

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();
            
            if (data.success) {
                form.reset();
                form.closest('.reply-form').style.display = 'none';
                
                // Tambahkan reply baru ke DOM
                const replyContainer = form.closest('.card-body');
                const newReply = createReplyElement(
                    data.reply, 
                    data.reply.user, 
                    data.reply.user.id === {{ Auth::id() ?? 'null' }},
                    data.photoUserId
                );
                
                replyContainer.insertAdjacentHTML('beforeend', newReply);
                
                // Notifikasi sukses
                showSuccessAlert('Balasan berhasil ditambahkan');
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorAlert('Gagal menambahkan balasan');
        } finally {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    // Fungsi bantuan untuk alert
    function showSuccessAlert(message) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
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

    function showErrorAlert(message) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }

    // Hapus komentar
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.delete-comment')) {
            e.preventDefault();
            const button = e.target.closest('.delete-comment');
            const commentId = button.getAttribute('data-comment-id');
            
            // Dialog konfirmasi
            Swal.fire({
                title: 'Hapus Komentar?',
                text: "Anda yakin ingin menghapus komentar ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/comments/${commentId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) throw new Error('Network response was not ok');
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Hapus elemen komentar dari DOM
                            const commentElement = button.closest('.card');
                            if (commentElement) {
                                commentElement.remove();
                                showAlert('success', 'Berhasil!', 'Komentar berhasil dihapus.');
                            }
                        }
                    } catch (error) {
                        console.error('Error deleting comment:', error);
                        showAlert('error', 'Gagal!', 'Gagal menghapus komentar. Silakan coba lagi.');
                    }
                }
            });
        }
    });

    // Hapus balasan
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.delete-reply')) {
            e.preventDefault();
            const button = e.target.closest('.delete-reply');
            const replyId = button.getAttribute('data-reply-id');
            
            // Dialog konfirmasi
            Swal.fire({
                title: 'Hapus Balasan?',
                text: "Anda yakin ingin menghapus balasan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/replies/${replyId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!response.ok) throw new Error('Network response was not ok');
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Hapus elemen balasan dari DOM
                            const replyElement = button.closest('.card');
                            if (replyElement) {
                                replyElement.remove();
                                showAlert('success', 'Berhasil!', 'Balasan berhasil dihapus.');
                            }
                        }
                    } catch (error) {
                        console.error('Error deleting reply:', error);
                        showAlert('error', 'Gagal!', 'Gagal menghapus balasan. Silakan coba lagi.');
                    }
                }
            });
        }
    });

    // ==================== FITUR LAINNYA ====================

    // Fungsi untuk handle like/unlike foto
    function handleLikeButton() {
        const likeButton = document.getElementById('like-button');
        const likesCount = document.getElementById('likes-count');

        if (likeButton) {
            likeButton.addEventListener('click', function(event) {
                event.preventDefault();
                const liked = likeButton.getAttribute('data-liked') === 'true';
                const url = liked ? '{{ route('photos.unlike', $photo->id) }}' : '{{ route('photos.like', $photo->id) }}';

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    likeButton.innerHTML = `<i class="bi ${data.liked ? 'bi-heart-fill' : 'bi-heart'}" style="color: ${data.liked ? 'red' : 'black'};"></i>`;
                    likeButton.setAttribute('data-liked', data.liked);
                    
                    if (data.likes_count > 0) {
                        likesCount.textContent = data.likes_count + (data.likes_count === 1 ? ' like' : ' likes');
                    } else {
                        likesCount.textContent = '';
                    }
                })
                .catch(console.error);
            });
        }
    }

    // Fungsi untuk handle tombol tambah ke album
    function handleAddToAlbum() {
        document.addEventListener('click', function(event) {
            if (event.target.closest('.add-to-album')) {
                event.preventDefault();
                const button = event.target.closest('.add-to-album');
                const albumId = button.getAttribute('data-album-id');
                const url = button.querySelector('i') ? `/albums/${albumId}/photos/${photoId}/remove` : `/albums/${albumId}/photos/${photoId}/add`;

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.querySelector('i') ? button.querySelector('i').remove() : button.innerHTML += ' <i class="bi bi-check text-success"></i>';
                    }
                })
                .catch(console.error);
            }
        });
    }

    // Fungsi untuk membuat album baru
    function handleCreateAlbum() {
        const createAlbumForm = document.getElementById('createAlbumForm');

        if (createAlbumForm) {
            createAlbumForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(createAlbumForm);
                const submitButton = createAlbumForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;

                fetch('{{ route('albums.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Album berhasil dibuat!',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Tutup modal dan reset form
                                const modal = bootstrap.Modal.getInstance(document.getElementById('createAlbumModal'));
                                modal.hide();
                                createAlbumForm.reset();

                                // Tambahkan album baru ke dropdown
                                const dropdownMenu = document.querySelector('.dropdown-menu');
                                if (dropdownMenu) {
                                    const newAlbumItem = document.createElement('li');
                                    newAlbumItem.innerHTML = `
                                        <a class="dropdown-item add-to-album" href="#" data-album-id="${data.album.id}">
                                            ${data.album.name}
                                        </a>
                                    `;

                                    const lastItemBeforeDivider = dropdownMenu.querySelector('li:last-child');
                                    dropdownMenu.insertBefore(newAlbumItem, lastItemBeforeDivider);
                                }
                            }
                        });
                    }
                })
                .finally(() => {
                    submitButton.disabled = false;
                });
            });
        }
    }

    // Blokir klik kanan dan inspect element
    function blockRightClickAndInspect() {
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
            }
        });
    }

    // Render gambar ke canvas dengan watermark
    function renderImageWithWatermark() {
        const canvas = document.getElementById('photoCanvas');
        if (!canvas) return;

        // Hanya render watermark jika user bukan pemilik dan bukan pro
        @if(!Auth::check() || (Auth::check() && Auth::id() !== $photo->user_id && Auth::user()->role !== 'pro'))
            const imgSrc = canvas.getAttribute('data-src');
            const img = new Image();
            img.src = imgSrc;
            img.crossOrigin = "anonymous";

            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                // Tambahkan watermark
                const watermarkText = "MOTRET";
                const fontSize = 25;
                ctx.font = `${fontSize}px Arial`;
                ctx.fillStyle = "rgba(255, 255, 255, 0.3)";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";

                const stepX = 150;
                const stepY = 100;
                const angle = -30 * (Math.PI / 180);

                ctx.save();
                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate(angle);

                for (let x = -canvas.width; x < canvas.width; x += stepX) {
                    for (let y = -canvas.height; y < canvas.height; y += stepY) {
                        ctx.fillText(watermarkText, x, y);
                    }
                }

                ctx.restore();
            };
        @endif
    }

    // Handle delete photo button
    const deletePhotoButton = document.getElementById('delete-photo-button');
    if (deletePhotoButton) {
        deletePhotoButton.addEventListener('click', function () {
            Swal.fire({
                title: 'Hapus Foto?',
                text: "Apakah Anda yakin ingin menghapus foto ini? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan penghapusan ke server
                    fetch("{{ route('photos.destroy', $photo->id) }}", {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Redirect ke halaman home
                                window.location.href = "{{ route('home') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghapus foto.',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });
    }

    // Handle follow buttons
    document.querySelectorAll('.follow-button').forEach(button => {
        button.addEventListener('click', function() {
            handleFollowUnfollow(this);
        });
    });

    // Function to update follow button appearance
    function updateFollowButton(button, following) {
        if (following) {
            button.textContent = 'Unfollow';
            button.classList.remove('btn-success');
            button.classList.add('btn-dark');
        } else {
            button.textContent = 'Follow';
            button.classList.remove('btn-dark');
            button.classList.add('btn-success');
        }
        button.setAttribute('data-following', following);
    }

    // Function to handle follow/unfollow action
    function handleFollowUnfollow(button) {
        const userId = button.getAttribute('data-user-id');
        const isFollowing = button.getAttribute('data-following') === 'true';
        const url = isFollowing ? `/users/${userId}/unfollow` : `/users/${userId}/follow`;

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
                updateFollowButton(button, !isFollowing);
            } else {
                throw new Error(data.message || 'Failed to process request.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Render canvas image for guest users
    @if(!Auth::check() || (Auth::check() && Auth::user()->role !== 'pro' && Auth::id() !== $photo->user_id))
    function renderCanvasImgGuest() {  
        document.querySelectorAll('canvas.card-img').forEach(function (canvas) {
            var imgSrc = canvas.getAttribute('data-src');
            var img = new Image();
            img.src = imgSrc;
            img.onload = function () {
                var ctx = canvas.getContext('2d');
                var width = canvas.width;
                var height = canvas.height;
                var aspectRatio = img.width / img.height;

                if (width / height > aspectRatio) {
                    width = height * aspectRatio;
                } else {
                    height = width / aspectRatio;
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                // Tambahkan watermark untuk guest
                const watermarkText = "MOTRET";
                const fontSize = 15;
                ctx.font = `${fontSize}px Arial`;
                ctx.fillStyle = "rgba(255, 255, 255, 0.3)";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";

                const stepX = 100;
                const stepY = 80;
                const angle = -30 * (Math.PI / 180);

                ctx.save();
                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate(angle);

                for (let x = -canvas.width; x < canvas.width; x += stepX) {
                    for (let y = -canvas.height; y < canvas.height; y += stepY) {
                        ctx.fillText(watermarkText, x, y);
                    }
                }

                ctx.restore();
            };
        });
    }
    renderCanvasImgGuest();
    @endif

    // Handle report forms
    async function handleReportForms() {
        // Event listener untuk semua form report
        document.querySelectorAll('form[id^="reportForm"]').forEach(form => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const formData = new FormData(this);
                const actionUrl = this.action;
                const submitButton = this.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;

                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

                try {
                    const response = await fetch(actionUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const contentType = response.headers.get('Content-Type');

                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonText: 'OK',
                            });

                            this.reset();
                            const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                            if (modal) modal.hide();
                        }
                    } else {
                        const text = await response.text();
                        console.error('Expect JSON but got:', text);
                        throw new Error('Unexpected response format.');
                    }

                } catch (error) {
                    console.error('Error reporting comment:', error);
                    Swal.fire('Error!', error.message, 'error');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            });
        });

        // Event listener untuk alasan "Lainnya"
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

        // Reset form saat modal ditutup
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                if (form) {
                    form.reset();
                    const descriptionGroup = form.querySelector('[id^="description-group"]');
                    if (descriptionGroup) descriptionGroup.style.display = 'none';
                }
            });
        });
    }

        // ==================== FUNGSI TAMBAHAN ====================

    // Fungsi untuk menambahkan watermark
    function addWatermark() {
        const ctx = modalImg.getContext('2d');
        const watermarkText = "MOTRET";
        const fontSize = 25;
        ctx.font = `${fontSize}px Arial`;
        ctx.fillStyle = "rgba(255, 255, 255, 0.3)";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";

        const stepX = 150;
        const stepY = 100;
        const angle = -30 * (Math.PI / 180);

        ctx.save();
        ctx.translate(modalImg.width / 2, modalImg.height / 2);
        ctx.rotate(angle);

        for (let x = -modalImg.width; x < modalImg.width; x += stepX) {
            for (let y = -modalImg.height; y < modalImg.height; y += stepY) {
                ctx.fillText(watermarkText, x, y);
            }
        }

        ctx.restore();
    }

    // Fungsi untuk mencegah screenshot
    function preventScreenshot() {
        document.addEventListener('keydown', function (e) {
            if (e.key === 'PrntScrn' || e.key === 'PrtScn' || (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4'))) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Diperbolehkan!',
                    text: 'Screenshot tidak diperbolehkan.',
                    position: 'center',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });

        // Deteksi tombol Command + Shift + 3/4 (Mac)
        document.addEventListener('keydown', function (e) {
            if (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4')) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Diperbolehkan!',
                    text: 'Screenshot tidak diperbolehkan.',
                    position: 'center',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });

        // Deteksi tombol lain yang mungkin digunakan untuk screenshot
        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey && e.key === 'S') {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Diperbolehkan!',
                    text: 'Screenshot tidak diperbolehkan.',
                    position: 'center',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    }

    // Fungsi untuk menangani modal zoom dengan watermark
    const openModalBtn = document.getElementById('open-modal');
    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
            const photoCanvas = document.getElementById('photoCanvas');
            const img = new Image();
            img.src = photoCanvas.dataset.src;
            img.onload = () => {
                modalImg.width = img.width;
                modalImg.height = img.height;
                const ctx = modalImg.getContext('2d');
                ctx.drawImage(img, 0, 0);
                addWatermark();
            };

            currentScale = 1;
            posX = 0;
            posY = 0;
            updateTransform();
            document.body.classList.add('modal-open');

            // Aktifkan pencegahan screenshot
            preventScreenshot();
        });
    }

    // Panggil semua fungsi yang diperlukan
    handleLikeButton();
    handleAddToAlbum();
    handleCreateAlbum();
    blockRightClickAndInspect();
    renderImageWithWatermark();
    handleReportForms();
    preventScreenshot();
    addWatermark();
    handleFollowUnfollow();
    renderCanvasImgGuest();
    handleReportForms();
    handleDeletePhoto();
    handleFollowUnfollow();
    handleReportForms();
    handleCreateAlbum();
    handleAddToAlbum();
    handleLikeButton();
    handleReportForms();
});
</script>
@endpush