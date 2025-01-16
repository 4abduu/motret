@extends('layouts.app')

@section('title', 'Manage Reports')

@section('content')
    <div class="container">
        <h1 class="my-4">Laporan</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Diunggah oleh</th>
                    <th>Pelapor</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td><img src="{{ asset('storage/' . $report->photo->path) }}" alt="{{ $report->photo->title }}" width="100"></td>
                        <td>{{ $report->photo->user->username }}</td>
                        <td>{{ $report->user->username }}</td>
                        <td>{{ $report->reason }}</td>
                        <td>
                            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#banPhotoModal" data-photo-id="{{ $report->photo->id }}"><i class="fas fa-ban"></i></button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteReportModal" data-report-id="{{ $report->id }}"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Konfirmasi Ban -->
    <div class="modal fade" id="banPhotoModal" tabindex="-1" aria-labelledby="banPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="banPhotoModalLabel">Konfirmasi Ban Postingan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin membanned postingan ini?
                </div>
                <div class="modal-footer">
                    <form id="banPhotoForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Ban</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteReportModal" tabindex="-1" aria-labelledby="deleteReportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteReportModalLabel">Konfirmasi Hapus Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus laporan ini?
                </div>
                <div class="modal-footer">
                    <form id="deleteReportForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var deleteReportModal = document.getElementById('deleteReportModal');
            deleteReportModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var reportId = button.getAttribute('data-report-id');
                var form = document.getElementById('deleteReportForm');
                form.action = '/reports/' + reportId;
            });

            var banPhotoModal = document.getElementById('banPhotoModal');
            banPhotoModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var photoId = button.getAttribute('data-photo-id');
                var form = document.getElementById('banPhotoForm');
                form.action = '/photos/' + photoId + '/ban';
            });
        });
    </script>
@endpush