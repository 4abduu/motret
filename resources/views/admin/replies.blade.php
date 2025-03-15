@extends('layouts.app')

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
                                    <button class="btn btn-info btn-icon" data-bs-toggle="modal" data-bs-target="#detailReplyModal{{ $reply->id }}"><i class="fa-solid fa-circle-info" style="color: white;"></i></button>
                                    <button class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#deleteReplyModal{{ $reply->id }}"><i class="ti-trash" style="color: white;"></i></button>
                                </td>
                            </tr>

                            <!-- Modal Detail Reply -->
                            <div class="modal fade" id="detailReplyModal{{ $reply->id }}" tabindex="-1" aria-labelledby="detailReplyModalLabel{{ $reply->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="detailReplyModalLabel{{ $reply->id }}">Detail Reply</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center mb-3">
                                                <img src="{{ asset('storage/' . $reply->comment->photo->path) }}" alt="{{ $reply->comment->photo->title }}" class="img-fluid" style="max-width: 100%; height: auto;">
                                            </div>
                                            <p><strong>Commented By:</strong> {{ $reply->comment->user->username }}</p>
                                            <p><strong>Comment:</strong> {{ $reply->comment->comment }}</p>
                                            <p><strong>Replied By:</strong> {{ $reply->user->username }}</p>
                                            <p><strong>Reply:</strong> {{ $reply->reply }}</p>
                                            <p><strong>Replied At:</strong> {{ $reply->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Delete Reply -->
                            <div class="modal fade" id="deleteReplyModal{{ $reply->id }}" tabindex="-1" aria-labelledby="deleteReplyModalLabel{{ $reply->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteReplyModalLabel{{ $reply->id }}">Delete Reply</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this reply?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST" action="{{ route('admin.replies.delete', $reply->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger text-white">Delete</button>
                                                <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Cancel</button>
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