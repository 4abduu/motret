@extends('layouts.app')

@section('title', 'Upload Photo')

@push('link')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
  <style>
    .container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
        margin-top: -5vh;
    }

    .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .required-field::after {
        content: " *";
        color: red;
    }

    .photo-requirements {
        background-color: #f8f9fa;
        border-left: 4px solid #28a745;
        padding: 10px 15px;
        margin: 15px 0;
        font-size: 14px;
        border-radius: 0 4px 4px 0;
    }

    .photo-requirements ul {
        padding-left: 20px;
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .container {
            margin-top: 0;
            padding: 15px;
        }
    }
  </style>
@endpush

@section('content')
    <div class="container">
        <h3 class="my-4">Unggah Foto</h3>
        <form method="POST" action="{{ route('photos.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title d-flex required-field">Pilih file foto</h4>
                            <div class="photo-requirements">
                                <p><strong>Ketentuan foto:</strong></p>
                                <ul>
                                    <li>Format: JPEG, PNG, JPG</li>
                                    <!--<li>Ukuran maksimal: 2MB</li>-->
                                </ul>
                            </div>
                            <input type="file" name="photo" class="dropify" id="photo" required onchange="previewImage(event)">
                        </div>
                    </div>
                </div>
                <div class="col-md-8 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Detail Foto</h4>
                            <form class="forms-sample">
                                <div class="form-group">
                                    <label for="title" class="required-field">Judul</label>
                                    <input type="text" name="title" class="form-control" id="title" placeholder="Judul" required>
                                </div>
                                <div class="form-group">
                                    <label for="description" class="required-field">Deskripsi</label>
                                    <textarea type="text" name="description" class="form-control" id="description" rows="3" placeholder="Deskripsi" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="hashtags" class="required-field">Hastag (tanpa #)</label>
                                    <input type="text" name="hashtags" class="form-control" id="hashtags" placeholder="Contoh: landscape sunset nature" required>
                                </div>
                                @if (Auth::user()->verified)
                                <div class="form-group">
                                    <label for="premium" class="form-label">Premium</label>
                                    <select class="form-select" id="premium" name="premium" required>
                                        <option value="0">Biasa</option>
                                        <option value="1">Premium</option>
                                    </select>
                                </div>
                                @endif
                                @if (Auth::user()->role === 'pro')
                                <div class="form-group">
                                    <label for="status" class="form-label">Visibilitas</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="1">Publik</option>
                                        <option value="0">Privat</option>
                                    </select>
                                </div>
                                @endif
                                <button type="submit" class="btn btn-success text-white me-2">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>

<script>
$(document).ready(function() {
    var drEvent = $('.dropify').dropify();

    // Event listener untuk input file
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const validExtensions = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validExtensions.includes(file.type)) {
                alert('Hanya file gambar (JPEG, PNG, JPG) yang diperbolehkan');
                e.target.value = ''; // Clear input file
                drEvent.data('dropify').clearElement(); // Reset Dropify
            }
        }
    });

    // Event Dropify ketika file di-drop
    drEvent.on('dropify.error.imageFormat', function(event, element) {
        alert('Hanya file gambar (JPEG, PNG, JPG) yang diperbolehkan');
        element.clearElement(); // Reset Dropify
    });

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('photo');
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const validExtensions = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validExtensions.includes(file.type)) {
                e.preventDefault();
                alert('Harap pilih file gambar yang valid (JPEG, PNG, JPG)');
                return false;
            }
        }

        var submitButton = document.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerText = 'Uploading...';
    });
});

</script>
@endpush