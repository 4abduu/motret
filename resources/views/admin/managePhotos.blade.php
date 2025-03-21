@extends('layouts.app')

@section('title', 'Manage Photos')

@section('content')
<div class="row">
    <h3>Manage Photos</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a  href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
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
                    <th>Status</th> <!-- Tambahkan kolom status -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                    <tr>
                @foreach($photos as $index => $photo)
                        <td>{{ $index + 1 }}</td>
                        <td><img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}" class="img-square" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0;"></td>
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
                            <button class="btn btn-info btn-icon" data-bs-toggle="modal" data-bs-target="#detailPhotoModal{{ $photo->id }}"><i class="ti-info-alt" style="color: white;"></i></button>
                            <button class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $photo->id }}"><i class="ti-trash" style="color: white;"></i></button>
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
                                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.photos.delete', $photo->id) }}" class="delete-photo-form" data-id="{{ $photo->id }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger text-white">Delete</button>
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

<script>
    document.querySelectorAll(".edit-photo-form").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Mencegah pengiriman form default
            
            let photoId = this.getAttribute("data-id");
            let modal = bootstrap.Modal.getInstance(document.getElementById(`editPhotoModal${photoId}`));
            
            // Tutup modal terlebih dahulu
            modal.hide();

            // Tunggu sedikit sebelum memunculkan SweetAlert
            setTimeout(() => {
                Swal.fire({
                    title: "Good job!",
                    text: "Your changes have been saved!",
                    icon: "success"
                }).then(() => {
                    // Kirim form setelah SweetAlert ditutup
                    form.submit();
                });
            }, 500); // Delay 500ms agar modal tertutup dengan mulus
        });
    });

    document.querySelectorAll(".delete-photo-form").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Mencegah pengiriman form langsung
            
            let photoId = this.getAttribute("data-id");
            let modal = bootstrap.Modal.getInstance(document.getElementById(`confirmDeleteModal${photoId}`));

            // Tutup modal terlebih dahulu
            modal.hide();

            // Tunggu sedikit sebelum memunculkan SweetAlert
            setTimeout(() => {
                Swal.fire({
                    title: "Deleted!",
                    text: "Your photo has been deleted.",
                    icon: "success"
                }).then(() => {
                    // Kirim form setelah SweetAlert ditutup
                    form.submit();
                });
            }, 500);
        });
    });
</script>

@endsection