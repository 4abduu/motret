@extends('layouts.app')

@section('title', 'Manage Photos')

@section('content')
    <div class="container">
        <h1 class="my-4">Manage Photos</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($photos as $photo)
                    <tr>
                        <td><img src="{{ $photo->url }}" alt="{{ $photo->title }}" width="100"></td>
                        <td>{{ $photo->title }}</td>
                        <td>{{ $photo->description }}</td>
                        <td>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editPhotoModal{{ $photo->id }}">Edit</button>
                            <form action="{{ route('admin.photos.delete', $photo->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
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
                @endforeach
            </tbody>
        </table>
    </div>
@endsection