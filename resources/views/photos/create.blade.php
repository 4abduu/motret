@extends('layouts.app')

@section('title', 'Upload Photo')

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
                    <input type="file" name="photo" class="dropify" id="photo" required>
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
                      <button type="submit" class="btn btn-success me-2">Upload</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
        </form>
    </div>
@endsection