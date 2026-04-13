document.addEventListener('DOMContentLoaded', function () {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    $(document).on('click', '.approve-btn', function () {
        const withdrawalId = $(this).data('id');

        Swal.fire({
            title: 'Setujui Penarikan?',
            text: 'Anda yakin ingin menyetujui permintaan penarikan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(async (result) => {
            if (!result.isConfirmed) {
                return;
            }

            try {
                const response = await fetch(window.adminBalanceWithdrawalsConfig.approveRouteTemplate.replace(':id', withdrawalId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.adminBalanceWithdrawalsConfig.csrfToken,
                        'Content-Type': 'application/json',
                        Accept: 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    Toast.fire({ icon: 'success', title: data.message || 'Penarikan berhasil disetujui.' });
                    setTimeout(() => location.reload(), 1000);
                } else {
                    Toast.fire({ icon: 'error', title: data.message || 'Terjadi kesalahan saat menyetujui penarikan.' });
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat memproses permintaan.' });
            }
        });
    });

    $(document).on('click', '.reject-btn', function () {
        const withdrawalId = $(this).data('id');

        Swal.fire({
            title: 'Tolak Penarikan?',
            text: 'Anda yakin ingin menolak permintaan penarikan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            input: 'textarea',
            inputLabel: 'Alasan Penolakan (Opsional)',
            inputPlaceholder: 'Masukkan alasan penolakan...',
            inputAttributes: {
                'aria-label': 'Masukkan alasan penolakan'
            }
        }).then(async (result) => {
            if (!result.isConfirmed) {
                return;
            }

            try {
                const response = await fetch(window.adminBalanceWithdrawalsConfig.rejectRouteTemplate.replace(':id', withdrawalId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.adminBalanceWithdrawalsConfig.csrfToken,
                        'Content-Type': 'application/json',
                        Accept: 'application/json'
                    },
                    body: JSON.stringify({ note: result.value })
                });

                const data = await response.json();

                if (response.ok) {
                    Toast.fire({ icon: 'success', title: data.message || 'Penarikan berhasil ditolak.' });
                    setTimeout(() => location.reload(), 1000);
                } else {
                    Toast.fire({ icon: 'error', title: data.message || 'Terjadi kesalahan saat menolak penarikan.' });
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat memproses permintaan.' });
            }
        });
    });

    $(document).on('click', '.delete-btn', function () {
        const withdrawalId = $(this).data('id');

        Swal.fire({
            title: 'Hapus Data Penarikan?',
            text: 'Anda tidak akan dapat mengembalikan data ini!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(async (result) => {
            if (!result.isConfirmed) {
                return;
            }

            try {
                const response = await fetch(window.adminBalanceWithdrawalsConfig.deleteRouteTemplate.replace(':id', withdrawalId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.adminBalanceWithdrawalsConfig.csrfToken,
                        'Content-Type': 'application/json',
                        Accept: 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    Toast.fire({ icon: 'success', title: data.message || 'Data penarikan berhasil dihapus.' });
                    setTimeout(() => location.reload(), 1000);
                } else {
                    Toast.fire({ icon: 'error', title: data.message || 'Terjadi kesalahan saat menghapus data penarikan.' });
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan saat memproses permintaan.' });
            }
        });
    });
});
