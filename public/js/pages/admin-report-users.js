$(document).ready(function () {
    $('.ban-user-btn').click(function () {
        const userId = $(this).data('id');
        const reason = $(this).data('reason');

        Swal.fire({
            title: 'Konfirmasi Ban Pengguna',
            html: '<p>Anda yakin ingin membanned pengguna ini?</p>' +
                '<p><strong>Alasan:</strong> ' + reason + '</p>' +
                '<div class="form-group mt-3">' +
                '<label for="swal-ban-type">Tipe Ban</label>' +
                '<select id="swal-ban-type" class="form-control">' +
                '<option value="temporary">Sementara</option>' +
                '<option value="permanent">Permanen</option>' +
                '</select>' +
                '</div>' +
                '<div class="form-group mt-3" id="swal-ban-until-group">' +
                '<label for="swal-ban-until">Tanggal Berakhir Ban</label>' +
                '<input type="date" id="swal-ban-until" class="form-control">' +
                '</div>' +
                '<div class="form-group mt-3">' +
                '<label for="swal-ban-reason">Alasan Ban</label>' +
                '<textarea id="swal-ban-reason" class="form-control">' + reason + '</textarea>' +
                '</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Ban!',
            cancelButtonText: 'Batal',
            focusConfirm: false,
            preConfirm: () => {
                const bannedType = $('#swal-ban-type').val();
                const bannedUntil = bannedType === 'temporary' ? $('#swal-ban-until').val() : null;
                const bannedReason = $('#swal-ban-reason').val();

                if (!bannedReason) {
                    Swal.showValidationMessage('Alasan ban harus diisi');
                    return false;
                }

                if (bannedType === 'temporary' && !bannedUntil) {
                    Swal.showValidationMessage('Tanggal berakhir ban harus diisi untuk ban sementara');
                    return false;
                }

                return {
                    banned_type: bannedType,
                    banned_until: bannedUntil,
                    banned_reason: bannedReason
                };
            }
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            const bannedType = result.value.banned_type;
            const bannedUntil = result.value.banned_until;
            const bannedReason = result.value.banned_reason;

            $.ajax({
                url: window.adminReportUsersConfig.banUserRouteTemplate.replace(':id', userId),
                type: 'PUT',
                data: {
                    _token: window.adminReportUsersConfig.csrfToken,
                    _method: 'PUT',
                    banned_type: bannedType,
                    banned_until: bannedUntil,
                    banned_reason: bannedReason
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Pengguna berhasil dibanned.',
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

        $('#swal-ban-type').change(function () {
            if ($(this).val() === 'permanent') {
                $('#swal-ban-until-group').hide();
            } else {
                $('#swal-ban-until-group').show();
            }
        }).trigger('change');
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
                url: window.adminReportUsersConfig.deleteReportRouteTemplate.replace(':id', id),
                type: 'DELETE',
                data: {
                    _token: window.adminReportUsersConfig.csrfToken,
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
