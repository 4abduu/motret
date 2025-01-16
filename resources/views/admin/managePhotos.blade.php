@extends('layouts.app')

@section('title', 'Manage Photos')

@section('content')
    <div class="container">
        <h1 class="my-4">Manage Photos</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th> <!-- Tambahkan kolom status -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($photos as $photo)
                    <tr>
                        <td><img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" width="100"></td>
                        <td>{{ $photo->title }}</td>
                        <td>{{ $photo->description }}</td>
                        <td>
                            @if($photo->banned)
                                <span class="badge bg-danger">Banned</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editPhotoModal{{ $photo->id }}">Edit</button>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailPhotoModal{{ $photo->id }}">Detail</button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $photo->id }}">Delete</button>
                        </td>
                    </tr>

                    <!-- Modal Edit Photo -->
                    <div class="modal fade" id="editPhotoModal{{ $photo->id }}" tabindex="-1" aria-labelledby="editPhotoModalLabel{{ $photo->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editPhotoModalLabel{{ $photo->id }}">Edit Photo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" class="img-fluid">
                                    </div>
                                    <form method="POST" action="{{ route('admin.photos.edit', $photo->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" class="form-control" id="title" value="{{ $photo->title }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea name="description" class="form-control" id="description" required>{{ $photo->description }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail Photo -->
                    <div class="modal fade" id="detailPhotoModal{{ $photo->id }}" tabindex="-1" aria-labelledby="detailPhotoModalLabel{{ $photo->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailPhotoModalLabel{{ $photo->id }}">Detail Photo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" class="img-fluid">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Uploaded by:</strong></label>
                                        <p>{{ $photo->user->username }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Title:</strong></label>
                                        <p>{{ $photo->title }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Description:</strong></label>
                                        <p>{{ $photo->description }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Hashtags:</strong></label>
                                        <p>{{ implode(', ', json_decode($photo->hashtags)) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Confirm Delete Photo -->
                    <div class="modal fade" id="confirmDeleteModal{{ $photo->id }}" tabindex="-1" aria-labelledby="confirmDeleteModalLabel{{ $photo->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmDeleteModalLabel{{ $photo->id }}">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this photo?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.photos.delete', $photo->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection