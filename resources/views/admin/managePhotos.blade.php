@extends('layouts.app')

@section('title', 'Manage Photos')

@section('content')
    <div class="container">
        <h1 class="my-4">Manage Photos</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <span id="success-countdown" class="float-end"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <span id="error-countdown" class="float-end"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <table class="table table-striped">
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
                        <td><img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" width="100"></td>
                        <td>{{ $photo->title }}</td>
                        <td>{{ $photo->description }}</td>
                        <td>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editPhotoModal{{ $photo->id }}">Edit</button>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#detailPhotoModal{{ $photo->id }}">Detail</button>
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
                @endforeach
            </tbody>
        </table>
    </div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var successAlert = document.querySelector('.alert-success');
        var errorAlert = document.querySelector('.alert-danger');

        if (successAlert) {
            var successCountdown = document.getElementById('success-countdown');
            var successTimeLeft = 5;
            successCountdown.innerText = successTimeLeft;

            var successInterval = setInterval(function () {
                successTimeLeft--;
                successCountdown.innerText = successTimeLeft;

                if (successTimeLeft <= 0) {
                    clearInterval(successInterval);
                    successAlert.remove();
                }
            }, 1000);
        }

        if (errorAlert) {
            var errorCountdown = document.getElementById('error-countdown');
            var errorTimeLeft = 5;
            errorCountdown.innerText = errorTimeLeft;

            var errorInterval = setInterval(function () {
                errorTimeLeft--;
                errorCountdown.innerText = errorTimeLeft;

                if (errorTimeLeft <= 0) {
                    clearInterval(errorInterval);
                    errorAlert.remove();
                }
            }, 1000);
        }
    });
</script>
@endpush