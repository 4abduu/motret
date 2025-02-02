<!-- filepath: /c:/xampp/htdocs/motret/resources/views/admin/reportUsers.blade.php -->
@extends('layouts.app')

@section('title', 'Manage User Reports')

@section('content')
<div class="row">
    <h3>Manage User Reports</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage User Reports</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manage User Reports</h4>
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Reported User</th>
                                <th>Reporter</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportUsers as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $report->reportedUser->username }}</td>
                                    <td>{{ $report->user->username }}</td>
                                    <td>{{ $report->reason }}</td>
                                    <td>
                                        @if($report->reportedUser->banned)
                                            <div class="badge badge-danger">Banned</div>
                                        @else
                                            <div class="badge badge-warning">Pending</div>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Button "ban" -->
                                        <button type="button" class="btn btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="#banUserModal{{ $report->id }}">
                                            <i class="icon-ban" style="color: white;"></i>
                                        </button>
                                        <!-- Button "delete report" -->
                                        <button type="button" class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#deleteReportModal{{ $report->id }}">
                                            <i class="ti-trash" style="color: white;"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Konfirmasi Ban -->
                                <div class="modal fade" id="banUserModal{{ $report->id }}" tabindex="-1" aria-labelledby="banUserModalLabel{{ $report->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="banUserModalLabel{{ $report->id }}">Konfirmasi Ban Pengguna</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Reported By:</strong> {{ $report->user->username }}</p>
                                                <p><strong>Reason:</strong> {{ $report->reason }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST" action="{{ route('admin.users.ban', $report->reportedUser->id) }}">
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
                                <div class="modal fade" id="deleteReportModal{{ $report->id }}" tabindex="-1" aria-labelledby="deleteReportModalLabel{{ $report->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteReportModalLabel{{ $report->id }}">Konfirmasi Hapus Laporan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus laporan ini?
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST" action="{{ route('admin.reports.delete', $report->id) }}">
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

        var banUserModal = document.getElementById('banUserModal');
        banUserModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-user-id');
            var form = document.getElementById('banUserForm');
            form.action = '/users/' + userId + '/ban';
        });
    });
</script>
@endpush