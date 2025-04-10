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
    console.log('DOM fully loaded and parsed');

    // CSRF token and elements
    const token = '{{ csrf_token() }}';
    const currentUserId = "{{ Auth::id() }}";
    const profileUserId = "{{ $user->id }}";
    
    // Initialize Bootstrap dropdowns
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    const dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Download button handler
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

    // Attach download event listener
    const downloadButton = document.getElementById("downloadButton");
    if (downloadButton) {
        downloadButton.addEventListener("click", handleDownload);
    }

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
            : 'btn btn-primary btn-sm follow-button';
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
                    const parser = new DOMParser();
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

    // Create album form handler
    const createAlbumForm = document.getElementById('createAlbumForm');
    if (createAlbumForm) {
        createAlbumForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('{{ route('albums.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', 'Album berhasil dibuat.', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Oops...', 'Terjadi kesalahan saat memproses permintaan.', 'error');
            });
        });
    }

    // Edit profile form handler
    const editProfileForm = document.getElementById('editProfileForm');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('{{ route('user.updateProfile') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Oops...', 'Terjadi kesalahan saat memproses permintaan.', 'error');
            });
        });
    }

    // Delete photo handler
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.delete-photo-btn')) {
            const button = e.target.closest('.delete-photo-btn');
            const photoId = button.getAttribute('data-id');
            const photoTitle = button.getAttribute('data-title');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Anda yakin ingin menghapus foto <strong>${photoTitle}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#32bd40',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/photos/${photoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', 'Foto berhasil dihapus.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Oops...', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                    });
                }
            });
        }
    });

    // Delete album handler
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.delete-album-btn')) {
            const button = e.target.closest('.delete-album-btn');
            const albumId = button.getAttribute('data-id');
            const albumName = button.getAttribute('data-name');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Anda yakin ingin menghapus album <strong>${albumName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#32bd40',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/albums/${albumId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', 'Album berhasil dihapus.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Oops...', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                    });
                }
            });
        }
    });
});
</script>
@endpush