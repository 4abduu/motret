$(document).ready(function () {
    $('.ban-photo-btn').click(function () {
        const id = $(this).data('id');
        const reason = $(this).data('reason');

        Swal.fire({
            title: 'Konfirmasi Ban Postingan',
            html: '<p>Anda yakin ingin membanned postingan ini?</p><p><strong>Alasan:</strong> ' + reason + '</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Ban!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: window.adminReportPhotosConfig.banPhotoRouteTemplate.replace(':id', id),
                type: 'PUT',
                data: {
                    _token: window.adminReportPhotosConfig.csrfToken,
                    _method: 'PUT'
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Postingan berhasil dibanned.',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Terjadi kesalahan saat memproses permintaan.',
                        icon: 'error'
                    });
                }
            });
        });
    });

    $('.delete-report-btn').click(function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus laporan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: window.adminReportPhotosConfig.deleteReportRouteTemplate.replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: window.adminReportPhotosConfig.csrfToken,
                    _method: 'DELETE'
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Laporan berhasil dihapus.',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Terjadi kesalahan saat menghapus laporan.',
                        icon: 'error'
                    });
                }
            });
        });
    });
});
