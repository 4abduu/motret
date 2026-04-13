document.addEventListener('DOMContentLoaded', function () {
    new DataTable('#example');

    document.addEventListener('click', async function (e) {
        const button = e.target && e.target.closest('.delete-verification-btn');
        if (!button) {
            return;
        }

        const requestId = button.getAttribute('data-id');

        const result = await Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Permintaan verifikasi ini akan dihapus beserta dokumen terkait!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) {
            return;
        }

        try {
            const response = await fetch('/admin/verification-requests/' + requestId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': window.adminVerificationRequestsConfig.csrfToken
                }
            });

            if (response.ok) {
                await Swal.fire('Berhasil!', 'Permintaan verifikasi berhasil dihapus.', 'success');
                window.location.reload();
            } else {
                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus permintaan verifikasi.', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Oops...', 'Terjadi kesalahan saat memproses permintaan.', 'error');
        }
    });
});
