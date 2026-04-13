document.addEventListener('click', function (event) {
    const button = event.target && event.target.closest('.delete-comment-btn');
    if (!button) {
        return;
    }

    const commentId = button.getAttribute('data-id');
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

        fetch(window.adminCommentsConfig.deleteCommentRouteTemplate.replace(':id', commentId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.adminCommentsConfig.csrfToken
            }
        }).then((response) => {
            if (response.ok) {
                Swal.fire('Deleted!', 'Your comment has been deleted.', 'success').then(() => location.reload());
            }
        });
    });
});
