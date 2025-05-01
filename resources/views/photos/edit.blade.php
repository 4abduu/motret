@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="card-title mb-4">Edit Foto</h3>
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <form class="forms-sample" method="POST" action="{{ route('photos.update', $photo->id) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="title" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ $photo->title }}" required>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required>{{ $photo->description }}</textarea>
                </div>
                <div class="form-group">
                    <label for="hashtags" class="form-label">Hashtags</label>
                    <input type="text" class="form-control" id="hashtags" name="hashtags" value="{{ implode(', ', json_decode($photo->hashtags)) }}" required>
                </div>
                @if (Auth::user()->role === 'pro')
                <div class="form-group">
                    <label for="status" class="form-label">Visibilitas</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="1" {{ $photo->status === '1' ? 'selected' : '' }}>Publik</option>
                        <option value="0" {{ $photo->status === '0' ? 'selected' : '' }}>Privat</option>
                    </select>
                </div>
                @endif
                @if (Auth::user()->verified)
                <div class="form-group">
                    <label for="premium" class="form-label">Status</label>
                    <select class="form-select" id="premium" name="premium" required>
                        <option value="0" {{ $photo->premium === '0' ? 'selected' : '' }}>Biasa</option>
                        <option value="1" {{ $photo->premium === '1' ? 'selected' : '' }}>Premium</option>
                    </select>
                </div>
                @endif
                <button type="submit" class="btn btn-success text-white me-2">Ubah Foto</button>
            </form>
          </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const premiumSelect = document.getElementById('premium');
    const statusSelect = document.getElementById('status');

    function handleStatusLogic() {
        const premiumValue = premiumSelect ? premiumSelect.value : null;
        const statusValue = statusSelect ? statusSelect.value : null;

        // Jika premium dipilih, set visibilitas ke publik (1) dan nonaktifkan
        if (premiumValue === '1') {
            if (statusSelect) {
                statusSelect.value = '1';
                statusSelect.disabled = true;
            }
        } 
        // Jika visibilitas pribadi (0), set premium ke biasa (0) dan nonaktifkan
        else if (statusValue === '0') {
            if (premiumSelect) {
                premiumSelect.value = '0';
                premiumSelect.disabled = true;
            }
        } 
        // Jika bukan keduanya, aktifkan semua
        else {
            if (statusSelect) statusSelect.disabled = false;
            if (premiumSelect) premiumSelect.disabled = false;
        }
    }

    // Event listeners for changes
    if (premiumSelect) premiumSelect.addEventListener('change', handleStatusLogic);
    if (statusSelect) statusSelect.addEventListener('change', handleStatusLogic);

    // Initialize logic on page load
    handleStatusLogic();
});
</script>
@endpush