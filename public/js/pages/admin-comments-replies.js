document.addEventListener('click', function (event) {
    const button = event.target && event.target.closest('.delete-reply-btn');
    if (!button) {
        return;
    }

    const replyId = button.getAttribute('data-id');
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: 'Anda tidak bisa mengembalikan data yang sudah dihapus!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#32bd40',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        fetch(window.adminRepliesConfig.deleteReplyRouteTemplate.replace(':id', replyId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.adminRepliesConfig.csrfToken
            }
        }).then((response) => {
            if (response.ok) {
                Swal.fire('Deleted!', 'Your reply has been deleted.', 'success').then(() => location.reload());
            }
        });
    });
});
