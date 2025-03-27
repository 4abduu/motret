@extends('layouts.app')

@push('link')
    <style>
        .custom-preview-btn {
            transition: all 0.3s ease;
            color: #32bd40;
            border-color: #32bd40;
        }

        .custom-preview-btn:hover {
            background-color: #32bd40 !important;
            color: white !important;
            border-color: #32bd40 !important;
        }

        .custom-preview-btn:hover i {
            color: white !important;
        }

        .dt-length {
            margin-left: 20px;
            padding-bottom: 10px;
        }
    </style>
@endpush

@section('content')

<div class="row">
    <h3>Manage Comments</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Comments</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Photo</th>
                                <th>Uploaded By</th>
                                <th>Commented By</th>
                                <th>Comment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $index => $comment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $comment->photo->path) }}" alt="{{ $comment->photo->title }}" class="img-square" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0;" />
                                    </td>
                                    <td>{{ $comment->photo->user->username }}</td>
                                    <td>{{ $comment->user->username }}</td>
                                    <td>{{ $comment->comment }}</td>
                                    <td>
                                        <button class="btn btn-info btn-icon" data-bs-toggle="modal" data-bs-target="#detailCommentModal{{ $comment->id }}"><i class="ti-info-alt" style="color: white;"></i></button>
                                        <button class="btn btn-danger btn-icon delete-comment-btn" data-id="{{ $comment->id }}"><i class="ti-trash" style="color: white;"></i></button>
                                    </td>
                                </tr>

                                <!-- Modal Detail Comment -->
                                <div class="modal fade" id="detailCommentModal{{ $comment->id }}" tabindex="-1" aria-labelledby="detailCommentModalLabel{{ $comment->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header text-white" style="background-color: #32bd40;">
                                                <h5 class="modal-title" id="detailCommentModalLabel{{ $comment->id }}">Detail Comment</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <img src="{{ asset('storage/' . $comment->photo->path) }}" alt="{{ $comment->photo->title }}" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                                                </div>
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body">
                                                        <h6 class="card-title" style="color: #32bd40;">Informasi Comment</h6>
                                                        <p><strong>Uploaded By:</strong> {{ $comment->photo->user->username }}</p>
                                                        <p><strong>Commented By:</strong> {{ $comment->user->username }}</p>
                                                        <p><strong>Comment:</strong> {{ $comment->comment }}</p>
                                                        <p><strong>Commented At:</strong> {{ $comment->created_at->format('d M Y, H:i') }}</p>
                                                        <!-- Tombol Preview -->
                                                        <div class="text-center mt-3">
                                                            <a href="{{ route('admin.previewCommentReplies', ['id' => $comment->id, 'type' => 'comment']) }}" class="btn btn-outline-success btn-sm custom-preview-btn">
                                                                <i class="ti-eye me-2"></i> Preview Comment
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // SweetAlert2 untuk delete comment
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-comment-btn')) {
            const commentId = event.target.getAttribute('data-id');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda tidak bisa mengembalikan data yang sudah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#32bd40',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/comments/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => {
                        if (response.ok) {
                            Swal.fire(
                                'Deleted!',
                                'Your comment has been deleted.',
                                'success'
                            ).then(() => location.reload());
                        }
                    });
                }
            });
        }
    });
</script>
@endpush

