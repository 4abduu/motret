@extends('layouts.app')

@section('title', 'Manage Photos')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-manage-photos.css') }}">
@endpush

@section('content')
<div class="row">
    <h3>Manage Photos</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Photos</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manage Photos</h4>
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Photo</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($photos as $index => $photo)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" class="img-square" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0;">
                                    </td>
                                    <td>{{ $photo->title }}</td>
                                    <td>{{ $photo->description }}</td>
                                    <td>
                                        @if($photo->banned)
                                            <div class="badge badge-danger">Banned</div>
                                        @else
                                            <div class="badge badge-success">Active</div>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="#editPhotoModal{{ $photo->id }}"><i class="ti-pencil-alt" style="color: white;"></i></button>
                                        <button class="btn btn-info btn-icon" data-bs-toggle="modal" data-bs-target="#detailPhotoModal{{ $photo->id }}"><i class="ti-info" style="color: white;"></i></button>
                                        <button class="btn btn-danger btn-icon delete-photo-btn" data-id="{{ $photo->id }}"><i class="ti-trash" style="color: white;"></i></button>
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
                                                <div class="mb-3 text-center">
                                                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" class="img-fluid">
                                                </div>
                                                <form method="POST" action="{{ route('admin.photos.edit', $photo->id) }}" class="edit-photo-form" data-id="{{ $photo->id }}">
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
                                                    <button type="submit" class="btn btn-success text-white">Save changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Detail Photo -->
                                <div class="modal fade" id="detailPhotoModal{{ $photo->id }}" tabindex="-1" aria-labelledby="detailPhotoModalLabel{{ $photo->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header text-white" style="background-color: #32bd40;">
                                                <h5 class="modal-title" id="detailPhotoModalLabel{{ $photo->id }}">Detail Photo</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                                                </div>
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body">
                                                        <h6 class="card-title" style="color: #32bd40;">Informasi Photo</h6>
                                                        <p><strong>Uploaded by:</strong> {{ $photo->user->username }}</p>
                                                        <p><strong>Title:</strong> {{ $photo->title }}</p>
                                                        <p><strong>Description:</strong> {{ $photo->description }}</p>
                                                        <p><strong>Hashtags:</strong> {{ implode(', ', json_decode($photo->hashtags)) }}</p>
                                                        <p><strong>Status:</strong> 
                                                            @if($photo->banned)
                                                                <span class="badge badge-danger">Banned</span>
                                                            @else
                                                                <span class="badge badge-success">Active</span>
                                                            @endif
                                                        </p>
                                                        <div class="text-center mt-3">
                                                            <a href="{{ route('admin.users.previewPhotos', $photo->id) }}" class="btn btn-outline-success btn-sm custom-preview-btn">
                                                                <i class="ti-eye me-2"></i> Preview Postingan
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

@push('scripts')
<script>
window.adminManagePhotosConfig = {
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};
</script>
<script src="{{ asset('js/pages/admin-manage-photos.js') }}"></script>
@endpush

@endsection