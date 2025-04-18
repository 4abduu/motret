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
        border-radius: 15px;
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
    .form-select:not(:disabled) {
        background-color: white;
        color: #212529;
        cursor: pointer;
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
                                    <li>Ukuran maksimal: 5MB</li>
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
                                    <label for="hashtags" class="required-field">Tagar (tanpa #)</label>
                                    <input type="text" name="hashtags" class="form-control" id="hashtags" placeholder="Contoh: landscape,sunset, nature" required>
                                </div>
                                @if (Auth::user()->role === 'pro')
                                <div class="form-group">
                                    <label for="status" class="form-label">Visibilitas</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="1">Publik</option>
                                        <option value="0">Pribadi</option>
                                    </select>
                                </div>
                                @endif
                                @if (Auth::user()->verified)
                                <div class="form-group">
                                    <label for="premium" class="form-label">Status</label>
                                    <select class="form-select" id="premium" name="premium" required>
                                        <option value="0">Biasa</option>
                                        <option value="1">Premium</option>
                                    </select>
                                </div>
                                @endif
                                <button type="submit" class="btn btn-success text-white me-2">Unggah</button>
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
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB in bytes

    // Event listener untuk input file
    document.getElementById('photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validasi ekstensi file
            const validExtensions = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validExtensions.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format tidak valid',
                    text: 'Hanya file gambar (JPEG, PNG, JPG) yang diperbolehkan',
                    confirmButtonText: 'OK',
                });
                e.target.value = '';
                drEvent.data('dropify').clearElement();
                return;
            }

            // Validasi ukuran file
            if (file.size > MAX_FILE_SIZE) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran file terlalu besar',
                    text: 'Ukuran file maksimal adalah 5MB',
                    confirmButtonText: 'OK',
                });
                e.target.value = '';
                drEvent.data('dropify').clearElement();
                return;
            }
        }
    });

    // Event Dropify ketika file di-drop
    drEvent.on('dropify.error.imageFormat', function(event, element) {
        Swal.fire({
            icon: 'error',
            title: 'Format tidak valid',
            text: 'Hanya file gambar (JPEG, PNG, JPG) yang diperbolehkan',
            confirmButtonText: 'OK',
        });
        element.clearElement();
    });

    // Validasi ukuran file saat di-drop
    drEvent.on('dropify.error.fileSize', function(event, element) {
        Swal.fire({
            icon: 'error',
            title: 'Ukuran file terlalu besar',
            text: 'Ukuran file maksimal adalah 5MB',
            confirmButtonText: 'OK',
        });
        element.clearElement();
    });

    // Handle premium and visibility logic
    function handleStatusLogic() {
        const premiumValue = $('#premium').val();
        const statusValue = $('#status').val();
        
        // Jika premium dipilih, set visibilitas ke publik (1) dan nonaktifkan
        if (premiumValue === '1') {
            $('#status').val('1').prop('disabled', true);
        } 
        // Jika visibilitas pribadi (0), set premium ke biasa (0) dan nonaktifkan
        else if (statusValue === '0') {
            $('#premium').val('0').prop('disabled', true);
        }
        // Jika bukan keduanya, aktifkan semua
        else {
            $('#status').prop('disabled', false);
            $('#premium').prop('disabled', false);
        }
    }

    // Form submission with AJAX and SweetAlert2
    $('form').on('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const fileInput = document.getElementById('photo');
        const submitButton = $(form).find('button[type="submit"]');
        
        // Validasi file sebelum upload
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            if (file.size > MAX_FILE_SIZE) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran file terlalu besar',
                    text: 'Ukuran file maksimal adalah 5MB',
                    confirmButtonText: 'OK',
                });
                return false;
            }
        }

        // Disable submit button and show loading state
        submitButton.prop('disabled', true);
        submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Foto berhasil diunggah.',
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('home') }}";
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat mengunggah foto.',
                        confirmButtonText: 'OK',
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat memproses permintaan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    errorMessage = xhr.statusText;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessage,
                    confirmButtonText: 'OK',
                });
            },
            complete: function() {
                submitButton.prop('disabled', false);
                submitButton.text('Upload');
            }
        });
    });

    // Event listeners for premium and status changes
    $('#premium, #status').on('change', handleStatusLogic);
    
    // Initialize the logic on page load
    handleStatusLogic();
});
</script>
@endpush