@extends('layouts.app')

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
                                            <button class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#deleteCommentModal{{ $comment->id }}"><i class="ti-trash" style="color: white;"></i></button>
                                        </td>
                                    </tr>

                                    <!-- Modal Detail Comment -->
                                    <div class="modal fade" id="detailCommentModal{{ $comment->id }}" tabindex="-1" aria-labelledby="detailCommentModalLabel{{ $comment->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="detailCommentModalLabel{{ $comment->id }}">Detail Comment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center mb-3">
                                                        <img src="{{ asset('storage/' . $comment->photo->path) }}" alt="{{ $comment->photo->title }}" class="img-fluid" style="max-width: 50%; height: auto;">
                                                    </div>
                                                    <p><strong>Uploaded By:</strong> {{ $comment->photo->user->username }}</p>
                                                    <p><strong>Commented By:</strong> {{ $comment->user->username }}</p>
                                                    <p><strong>Comment:</strong> {{ $comment->comment }}</p>
                                                    <p><strong>Commented At:</strong> {{ $comment->created_at->format('d M Y, H:i') }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Delete Comment -->
                                    <div class="modal fade" id="deleteCommentModal{{ $comment->id }}" tabindex="-1" aria-labelledby="deleteCommentModalLabel{{ $comment->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteCommentModalLabel{{ $comment->id }}">Delete Comment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this comment?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="POST" action="{{ route('admin.comments.delete', $comment->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    </form>
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