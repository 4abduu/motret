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
            width: '300px',
            padding: '0.5rem',
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
    console.log('DOM fully loaded and parsed.');

    const token = '{{ csrf_token() }}';
    const userReasonRadios = document.querySelectorAll('#reportUserModal input[name="reason"]');
    const userDescriptionGroup = document.getElementById('description-group-user');
    const userDescriptionInput = document.getElementById('description-user');
    const currentUserId = "{{ Auth::id() }}"; // ID user yang sedang login
    const profileUserId = "{{ $user->id }}"; // ID user yang sedang dilihat profilnya

    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    const dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    function handleDownload(event) {
            event.preventDefault(); // Biar form gak langsung submit

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
                            cancelButtonText: 'Cancel',  // Tambahin tombol Cancel
                            showCancelButton: true,      // Aktifin tombol Cancel
                            reverseButtons: true
                        }).then((res) => {
                            if (res.isConfirmed) {
                                document.getElementById('downloadForm').submit();
                            }
                            // Kalau user klik di luar modal atau cancel, gak ngapa-ngapain
                        });
                    }
                });
            @else
                document.getElementById('downloadForm').submit();
            @endif
        }


                // Tambahkan event listener hanya ke tombol download
                document.getElementById("downloadButton").addEventListener("click", handleDownload);

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
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
    // Fungsi update tampilan tombol (disederhanakan)
    function updateButtonAppearance(button, isFollowing) {
        if (!button) return;
        
        button.textContent = isFollowing ? 'Unfollow' : 'Follow';
        button.className = isFollowing 
            ? 'btn btn-danger btn-sm unfollow-button' 
            : 'btn btn-primary btn-sm follow-button';
            button.style.marginTop = '16px';
            button.style.marginBottom = '24px';
    }

    // Fungsi utama handle follow/unfollow yang lebih smooth
    async function handleFollowAction(button, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const targetUserId = button.getAttribute('data-user-id');
        const isUnfollow = button.classList.contains('unfollow-button');
        const url = isUnfollow ? `/users/${targetUserId}/unfollow` : `/users/${targetUserId}/follow`;
        
        // Simpan state asli untuk rollback jika error
        const originalState = {
            text: button.textContent,
            class: button.className,
            disabled: button.disabled
        };
        
        try {
            // Langsung update tampilan tanpa loading state
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

            // Update semua tombol untuk user ini
            document.querySelectorAll(`button[data-user-id="${targetUserId}"]`).forEach(btn => {
                updateButtonAppearance(btn, data.action === 'follow');
                btn.disabled = false;
            });

            // Update counter
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

            // Refresh modal tanpa scroll jump
            await refreshModalContentSmoothly('#followersModal');
            await refreshModalContentSmoothly('#followingModal');
            
            return data;
        } catch (error) {
            console.error('Error:', error);
            // Rollback ke state asli jika error
            button.textContent = originalState.text;
            button.className = originalState.class;
            button.disabled = originalState.disabled;
            
            // Tampilkan error hanya di console, tidak tampil ke user
            return null;
        }
    }

    // Refresh modal yang lebih smooth
    async function refreshModalContentSmoothly(modalId) {
        const modal = document.querySelector(modalId);
        if (!modal || !modal.classList.contains('show')) return;
        
        try {
            // Simpan scroll position sebelum refresh
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
                // Kembalikan scroll position
                modalBody.scrollTop = scrollPosition;
                initModalButtons(modal);
            }
        } catch (error) {
            console.error('Gagal refresh modal:', error);
        }
    }

    // Inisialisasi tombol modal
    function initModalButtons(modal) {
        if (!modal) return;
        
        modal.querySelectorAll('.follow-button, .unfollow-button').forEach(btn => {
            const userId = btn.getAttribute('data-user-id');
            const isFollowing = btn.classList.contains('unfollow-button');
            
            // Clone button untuk menghindari event listener duplikat
            const newBtn = btn.cloneNode(true);
            btn.replaceWith(newBtn);
            
            newBtn.addEventListener('click', (e) => handleFollowAction(newBtn, e));
            updateButtonAppearance(newBtn, isFollowing);
        });
    }

    // Inisialisasi tombol utama
    const followButton = document.getElementById('follow-button');
    if (followButton) {
        const initialState = followButton.getAttribute('data-initial-state') === 'following';
        updateButtonAppearance(followButton, initialState);
        followButton.addEventListener('click', (e) => handleFollowAction(followButton, e));
    }

    // Event delegation untuk tombol di dalam modal
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.follow-button, .unfollow-button');
        if (button) {
            handleFollowAction(button, e);
        }
    });

    // Inisialisasi modal saat dibuka
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
                .catch(() => console.log('Gagal memuat data modal'));
            });
        }
    });

    // Event listener for create album form
    // const createAlbumForm = document.getElementById('createAlbumForm');
    // if (createAlbumForm) {
    //     createAlbumForm.addEventListener('submit', async function (event) {
    //         event.preventDefault();

    //         const formData = new FormData(createAlbumForm);
    //         const submitButton = createAlbumForm.querySelector('button[type="submit"]');
    //         submitButton.disabled = true;

    //         try {
    //             const response = await fetch('{{ route('albums.store') }}', {
    //                 method: 'POST',
    //                 headers: {
    //                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //                     'Accept': 'application/json'
    //                 },
    //                 body: formData
    //             });

    //             if (!response.ok) {
    //                 throw new Error(`HTTP error! Status: ${response.status}`);
    //             }

    //             const data = await response.json();

    //             if (data.success) {
    //                 Swal.fire({
    //                     icon: 'success',
    //                     title: 'Berhasil!',
    //                     text: 'Album berhasil dibuat!',
    //                     confirmButtonText: 'OK'
    //                 }).then(() => {
    //                     window.location.reload();
    //                 });
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Gagal!',
    //                     text: 'Gagal membuat album. Silakan coba lagi.',
    //                     confirmButtonText: 'OK'
    //                 });
    //             }
    //         } catch (error) {
    //             console.error('Error creating album:', error);
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Terjadi Kesalahan!',
    //                 text: 'Terjadi kesalahan saat membuat album. Silakan coba lagi.',
    //                 confirmButtonText: 'OK'
    //             });
    //         } finally {
    //             submitButton.disabled = false;
    //         }
    //     });
    // }

    document.getElementById('createAlbumForm').addEventListener('submit', function (event) {
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

document.getElementById('editProfileForm').addEventListener('submit', function (event) {
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

// Hapus Foto
document.addEventListener('click', function (e) {
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

// Hapus Album (Tambahkan kode serupa untuk tombol hapus album)
document.addEventListener('click', function (e) {
    console.log('DOM fully loaded and parsed.');
    console.log('Event target:', e.target);
    console.log('Event target closest:', e.target.closest('.delete-album-btn'));
    console.log('Event target closest data-id:', e.target.closest('.delete-album-btn')?.getAttribute('data-id'));
    console.log('Event target closest data-name:', e.target.closest('.delete-album-btn')?.getAttribute('data-name'));
    console.log('Event target closest class:', e.target.closest('.delete-album-btn')?.classList);
    console.log('Event target closest class contains delete-album-btn:', e.target.closest('.delete-album-btn')?.classList.contains('delete-album-btn'));
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