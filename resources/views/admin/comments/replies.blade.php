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
    <h3>Manage Replies</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Replies</li>
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
                                <th>Commented By</th>
                                <th>Comment</th>
                                <th>Replied By</th>
                                <th>Reply</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($replies as $index => $reply)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $reply->comment->user->username }}</td>
                                    <td>{{ $reply->comment->comment }}</td>
                                    <td>{{ $reply->user->username }}</td>
                                    <td>{{ $reply->reply }}</td>
                                    <td>
                                        <button class="btn btn-info btn-icon" data-bs-toggle="modal" data-bs-target="#detailReplyModal{{ $reply->id }}"><i class="ti-info-alt" style="color: white;"></i></button>
                                        <button class="btn btn-danger btn-icon delete-reply-btn" data-id="{{ $reply->id }}"><i class="ti-trash" style="color: white;"></i></button>
                                    </td>
                                </tr>

                                <!-- Modal Detail Reply -->
                                <div class="modal fade" id="detailReplyModal{{ $reply->id }}" tabindex="-1" aria-labelledby="detailReplyModalLabel{{ $reply->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header text-white" style="background-color: #32bd40;">
                                                <h5 class="modal-title" id="detailReplyModalLabel{{ $reply->id }}">Detail Reply</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <img src="{{ asset('storage/' . $reply->comment->photo->path) }}" alt="{{ $reply->comment->photo->title }}" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                                                </div>
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body">
                                                        <h6 class="card-title" style="color: #32bd40;">Informasi Reply</h6>
                                                        <p><strong>Commented By:</strong> {{ $reply->comment->user->username }}</p>
                                                        <p><strong>Comment:</strong> {{ $reply->comment->comment }}</p>
                                                        <p><strong>Replied By:</strong> {{ $reply->user->username }}</p>
                                                        <p><strong>Reply:</strong> {{ $reply->reply }}</p>
                                                        <p><strong>Replied At:</strong> {{ $reply->created_at->format('d M Y, H:i') }}</p>
                                                        <!-- Tombol Preview -->
                                                        <div class="text-center mt-3">
                                                            <a href="{{ route('admin.previewCommentReplies', ['id' => $reply->id, 'type' => 'reply']) }}" class="btn btn-outline-success btn-sm custom-preview-btn">
                                                                <i class="ti-eye me-2"></i> Preview Reply
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
    // SweetAlert2 untuk delete reply
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-reply-btn')) {
            const replyId = event.target.getAttribute('data-id');
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
                    fetch(`/admin/replies/${replyId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => {
                        if (response.ok) {
                            Swal.fire(
                                'Deleted!',
                                'Your reply has been deleted.',
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