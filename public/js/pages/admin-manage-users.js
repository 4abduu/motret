document.addEventListener('DOMContentLoaded', function () {
    var config = window.manageUsersConfig || {};
    var csrfToken = config.csrfToken || '';
    var createUrl = config.createUrl || '/admin/users/create';

    // Inisialisasi DataTables
    $('#example').DataTable();

    function showAlert(icon, title, text, callback) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonText: 'OK'
        }).then(function () {
            if (callback) {
                callback();
            }
        });
    }

    var addUserForm = document.querySelector('#createUserModal form');
    if (addUserForm) {
        addUserForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            var formData = new FormData(this);

            try {
                var response = await fetch(createUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                var data = await response.json();

                if (response.ok) {
                    showAlert('success', 'Berhasil!', data.message || 'User berhasil ditambahkan.', function () {
                        window.location.reload();
                    });
                } else {
                    showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat menambahkan user.');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
            }
        });
    }

    document.addEventListener('submit', async function (e) {
        if (e.target && e.target.matches('.edit-user-form')) {
            e.preventDefault();

            var formData = new FormData(e.target);
            var userId = e.target.getAttribute('data-id');
            formData.append('_method', 'PUT');

            try {
                var response = await fetch('/admin/users/' + userId, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                var data = await response.json();

                if (response.ok) {
                    showAlert('success', 'Berhasil!', data.message || 'User berhasil diupdate.', function () {
                        window.location.reload();
                    });
                } else {
                    showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat mengupdate user.');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
            }
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.closest('.delete-user-btn')) {
            var button = e.target.closest('.delete-user-btn');
            var userId = button.getAttribute('data-id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda tidak akan bisa mengembalikan user ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(async function (result) {
                if (result.isConfirmed) {
                    try {
                        var response = await fetch('/admin/users/' + userId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        var data = await response.json();

                        if (response.ok) {
                            showAlert('success', 'Berhasil!', data.message || 'User berhasil dihapus.', function () {
                                window.location.reload();
                            });
                        } else {
                            showAlert('error', 'Gagal!', data.message || 'Terjadi kesalahan saat menghapus user.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showAlert('error', 'Oops...', 'Terjadi kesalahan saat memproses permintaan.');
                    }
                }
            });
        }
    });
});
