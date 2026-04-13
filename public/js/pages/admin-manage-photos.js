document.addEventListener('DOMContentLoaded', function () {
    $('#example').DataTable();

    function showAlert(icon, title, text, callback) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonText: 'OK'
        }).then(() => {
            if (callback) {
                callback();
            }
        });
    }

    document.addEventListener('submit', async function (e) {
        if (e.target && e.target.matches('.edit-photo-form')) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const photoId = e.target.getAttribute('data-id');
            formData.append('_method', 'PUT');

            try {
                const response = await fetch('/admin/photos/' + photoId, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': window.adminManagePhotosConfig.csrfToken
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('success', 'Berhasil!', data.message || 'Photo berhasil diupdate.', function () {
                        window.location.reload();
                    });
                } else {
                    showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat mengupdate photo.');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
            }
        }
    });

    document.addEventListener('click', function (e) {
        const button = e.target && e.target.closest('.delete-photo-btn');
        if (!button) {
            return;
        }

        const photoId = button.getAttribute('data-id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Anda tidak akan bisa mengembalikan foto ini!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (!result.isConfirmed) {
                return;
            }

            try {
                const response = await fetch('/admin/photos/' + photoId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.adminManagePhotosConfig.csrfToken
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('success', 'Berhasil!', data.message || 'Photo berhasil dihapus.', function () {
                        window.location.reload();
                    });
                } else {
                    showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat menghapus photo.');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
            }
        });
    });
});
