@extends('layouts.app')

@section('title', 'Upload Photo')

@push('styles')
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

  </style>
@endpush
@section('content')
    <div class="container">
        <h3 class="my-4">Unggah Foto</h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('photos.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title d-flex">Pilih file foto</h4>
                    <input type="file" name="photo" class="dropify" id="photo" required onchange="previewImage(event)">
                  </div>
                </div>
              </div>
              <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Unggah Foto</h4>
                    <form class="forms-sample">
                      <div class="form-group">
                        <label for="title">Judul</label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="Judul" required>
                      </div>
                      <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea type="text" name="description" class="form-control" id="description" rows="3" placeholder="Deskripsi"></textarea>
                      </div>
                      <div class="form-group">
                        <label for="hashtags">Hastag (tanpa#)</label>
                        <input type="text" name="hashtags" class="form-control" id="hashtags" placeholder="Hastag">
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
          $('.dropify').dropify({
              messages: {
                  'default': 'Seret dan lepas file di sini atau klik',
                  'replace': 'Seret dan lepas file di sini atau klik untuk mengganti',
                  'remove':  'Hapus',
                  'error':   'Oops, terjadi kesalahan.'
              },
              error: {
                  'fileSize': 'Ukuran file terlalu besar (maksimal 2MB).',
                  'imageFormat': 'Format file tidak didukung (hanya jpeg, png, jpg).'
              }
          });
      });
        document.querySelector('form').addEventListener('submit', function(e) {
            var submitButton = document.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerText = 'Uploading...';
        });
    </script>   
@endpush