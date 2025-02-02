managereports blade:
@extends('layouts.app')

@section('title', 'Manage Reports')

@section('content')
<div class="row">
    <h3>Manage Reports</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a  href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Reports</li>
      </ol>
</div>


<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manage Reports</h4>
                <div class="table-responsive">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Pengunggah</th>
                            <th>Pelapor</th>
                            <th>Alasan</th>
                            <th>Status</th> <!-- Tambahkan kolom status -->
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportPhotos as $index => $report)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . $report->photo->path) }}" alt="{{ $report->photo->title }}" class="img-square" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0;" />
                                </td>
                                <td>{{ $report->photo->user->username }}</td>
                                <td>{{ $report->user->username }}</td>
                                <td>{{ $report->reason }}</td>
                                <td>
                                    @if($report->photo->banned)
                                    <div class="badge badge-danger">Banned</div>
                                    @else
                                    <div class="badge badge-warning">Pending</div>
                                    @endif
                                </td>
                                <td>
                                    <!-- Button "ban" -->
                                    <button type="button" class="btn btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="#banPhotoModal{{ $report->id }}">
                                        <i class="icon-ban" style="color: white;"></i>
                                    </button>
                                    <!-- Button "delete report" -->
                                    <button type="button" class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#deleteReportForm{{ $report->id }}">
                                    <i class="ti-trash" style="color: white;"></i>
                                    </button>
                                </td>
                            </tr>
 

                                <!-- Modal Konfirmasi Ban -->
                                <div class="modal fade" id="banPhotoModal{{ $report->id }}" tabindex="-1" aria-labelledby="banPhotoModalLabel{{ $report->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="banPhotoModalLabel{{ $report->id }}">Konfirmasi Ban Postingan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <img src="{{ asset('storage/' . $report->photo->path) }}" alt="{{ $report->photo->title }}" class="img-fluid" style="max-width: 100%; height: auto;">
                                                </div>
                                                <p><strong>Uploaded By:</strong> {{ $report->photo->user->username }}</p>
                                                <p><strong>Reported By:</strong> {{ $report->user->username }}</p>
                                                <p><strong>Reason:</strong> {{ $report->reason }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST" action="{{ route('admin.photos.ban', $report->photo->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-danger">Ban</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteReportModal{{ $report->id }}" tabindex="-1" aria-labelledby="deleteReportModalLabel" aria-hidden="true">
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
    @endforeach
                    </tbody>
                </table>
            </div>
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
                var banReason = document.getElementById('ban_reason');
                banReason.value = '';
            });
        });
    </script>
@endpush