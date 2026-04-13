document.addEventListener('DOMContentLoaded', function () {
    var albumShowConfig = window.albumShowConfig || {};
    var csrfToken = albumShowConfig.csrfToken || document.querySelector('meta[name="csrf-token"]').content;

    function setupDescription() {
        var descElement = document.getElementById('album-description');
        if (!descElement) {
            return;
        }

        var fullText = descElement.getAttribute('data-full-text') || '';
        var isMobile = window.innerWidth <= 768;
        var charLimit = isMobile ? 100 : 150;

        if (fullText.length > charLimit) {
            var visibleText = fullText.substring(0, charLimit);
            var hiddenText = fullText.substring(charLimit);

            descElement.innerHTML =
                '<span class="visible-text">' + visibleText + '</span>' +
                '<span class="hidden-text" style="display:none">' + hiddenText + '</span>' +
                '<span class="read-more">...Lainnya</span>';
        } else {
            descElement.textContent = fullText;
        }
    }

    function toggleDescription(e) {
        var readMoreBtn = e.target.closest('.read-more');
        if (!readMoreBtn) {
            return;
        }

        e.stopPropagation();
        var descElement = readMoreBtn.closest('.album-description');
        if (!descElement) {
            return;
        }

        var fullText = descElement.getAttribute('data-full-text') || '';
        var isExpanded = readMoreBtn.textContent.includes('Sembunyikan');

        if (isExpanded) {
            var isMobile = window.innerWidth <= 768;
            var charLimit = isMobile ? 100 : 150;
            var visibleText = fullText.substring(0, charLimit);

            descElement.innerHTML =
                '<span class="visible-text">' + visibleText + '</span>' +
                '<span class="hidden-text" style="display:none">' + fullText.substring(charLimit) + '</span>' +
                '<span class="read-more">...Lainnya</span>';
        } else {
            descElement.innerHTML = fullText + '<span class="read-more">Sembunyikan</span>';
        }
    }

    function updateDescriptionDisplay(element, text) {
        var isMobile = window.innerWidth <= 768;
        var charLimit = isMobile ? 100 : 150;
        var shouldTruncate = text.length > charLimit;

        element.innerHTML = shouldTruncate
            ? text.substring(0, charLimit) + '<span class="read-more">...Lainnya</span>'
            : text;
    }

    async function saveChanges(albumId, fieldType, newValue, editContainer, originalElement) {
        var loadingDiv = document.createElement('div');
        loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        editContainer.appendChild(loadingDiv);

        try {
            var endpoint = '/albums/' + albumId + '/update' + (fieldType === 'title' ? 'Title' : 'Description');
            var response = await fetch(endpoint, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json'
                },
                body: JSON.stringify(fieldType === 'title' ? { title: newValue } : { description: newValue })
            });

            var result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal menyimpan perubahan');
            }

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: fieldType === 'title' ? 'Judul album berhasil diperbarui' : 'Deskripsi album berhasil diperbarui',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: 'var(--primary-light)',
                color: 'var(--dark-color)',
                iconColor: 'var(--primary-color)'
            });

            return result;
        } catch (error) {
            console.error('Error:', error);
            showError('Gagal menyimpan: ' + error.message);
            throw error;
        } finally {
            loadingDiv.remove();
        }
    }

    function enableEdit(element, type) {
        var id = element.dataset.id;
        var currentValue =
            element.dataset.fullText || element.textContent.replace(/\.\.\.Lainnya|Sembunyikan/g, '').trim();

        var editDiv = document.createElement('div');
        editDiv.className = 'edit-container';

        var inputField = type === 'description' ? document.createElement('textarea') : document.createElement('input');
        inputField.className = 'edit-input ' + (type === 'description' ? 'edit-description' : 'edit-title');
        inputField.value = currentValue;

        var buttonsDiv = document.createElement('div');
        buttonsDiv.className = 'button-container';
        buttonsDiv.innerHTML = '<button class="btn-cancel">Batal</button><button class="btn-save">Simpan</button>';

        editDiv.append(inputField, buttonsDiv);
        element.replaceWith(editDiv);
        inputField.focus();

        buttonsDiv.querySelector('.btn-cancel').addEventListener('click', function () {
            editDiv.replaceWith(element);
        });

        buttonsDiv.querySelector('.btn-save').addEventListener('click', async function () {
            var newValue = inputField.value.trim();
            if (newValue !== currentValue) {
                await saveChanges(id, type, newValue, editDiv, element);
                element.dataset.fullText = newValue;
                updateDescriptionDisplay(element, newValue);
            }
            editDiv.replaceWith(element);
        });

        inputField.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                buttonsDiv.querySelector('.btn-cancel').click();
            }
            if (type === 'title' && e.key === 'Enter') {
                buttonsDiv.querySelector('.btn-save').click();
            }
        });
    }

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

    async function deletePhoto(photoId, albumId) {
        Swal.fire({
            title: 'Menghapus...',
            html: 'Sedang menghapus foto dari album',
            allowOutsideClick: false,
            didOpen: function () {
                Swal.showLoading();
            },
            background: 'var(--primary-light)',
            color: 'var(--dark-color)'
        });

        try {
            var response = await fetch('/albums/' + albumId + '/removePhoto/' + photoId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error('Gagal menghapus foto');
            }

            var result = await response.json();

            if (result.success) {
                showSuccess('Foto telah dihapus');
                var button = document.querySelector('.photo-menu-btn[data-photo-id="' + photoId + '"]');
                var photoCard = button ? button.closest('.photo-card') : null;

                if (photoCard) {
                    photoCard.style.transform = 'scale(0.9)';
                    photoCard.style.opacity = '0';
                    setTimeout(function () {
                        photoCard.remove();
                    }, 300);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Gagal menghapus foto');
        }
    }

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
        }).then(function (result) {
            if (result.isConfirmed) {
                deletePhoto(photoId, albumId);
            }
        });
    }

    setupDescription();
    window.addEventListener('resize', setupDescription);
    document.addEventListener('click', toggleDescription);

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-icon')) {
            e.stopPropagation();
            var targetElement = document.getElementById(e.target.dataset.target);
            if (targetElement) {
                enableEdit(targetElement, e.target.dataset.type);
            }
        }
    });

    var visibilityToggle = document.getElementById('visibility-toggle');
    if (visibilityToggle) {
        visibilityToggle.addEventListener('click', async function () {
            var albumId = this.dataset.id;
            var icon = this.querySelector('i');
            var textElement = this.querySelector('.visibility-text');
            var isCurrentlyPublic = icon.classList.contains('fa-eye');

            this.style.pointerEvents = 'none';
            textElement.textContent = 'Memproses...';
            icon.classList.remove(isCurrentlyPublic ? 'fa-eye' : 'fa-eye-slash');
            icon.classList.add('fa-spinner', 'fa-spin');

            try {
                var response = await fetch('/albums/' + albumId + '/updateVisibility', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: isCurrentlyPublic ? 0 : 1 })
                });

                if (!response.ok) {
                    throw new Error('Gagal mengubah visibilitas');
                }

                var result = await response.json();
                if (result.success) {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add(result.status == 1 ? 'fa-eye' : 'fa-eye-slash');
                    textElement.textContent = result.status == 1 ? 'Publik' : 'Privat';
                    showSuccess('Album sekarang ' + (result.status == 1 ? 'Publik' : 'Privat'));
                }
            } catch (error) {
                console.error('Error:', error);
                icon.classList.remove('fa-spinner', 'fa-spin');
                icon.classList.add(isCurrentlyPublic ? 'fa-eye' : 'fa-eye-slash');
                textElement.textContent = isCurrentlyPublic ? 'Publik' : 'Privat';
                showError('Gagal mengubah visibilitas');
            } finally {
                this.style.pointerEvents = 'auto';
            }
        });
    }

    document.querySelectorAll('.photo-menu-btn').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            confirmDeletePhoto(this.dataset.photoId, this.dataset.albumId);
        });
    });
});
