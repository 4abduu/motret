@extends('layouts.app')

@section('title', 'Manage User Reports')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-report-users.css') }}">
@endpush

@section('content')
<div class="row">
    <h3>Manage User Reports</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
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
                                <th>Banned Type</th>
                                <th>Banned Until</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportUsers as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ route('admin.users.previewProfile', $report->reportedUser->id) }}" style="color: black;"><b>{{ $report->reportedUser->username }}</b></a></td>
                                    <td><a href="{{ route('admin.users.previewProfile', $report->user->id) }}" style="color: black;"><b>{{ $report->user->username }}</b></a></td>
                                    <td>{{ $report->reason }}</td>
                                    <td>
                                        @if($report->reportedUser->banned)
                                            <div class="badge badge-danger">Banned</div>
                                        @else
                                            <div class="badge badge-warning">Pending</div>
                                        @endif
                                    </td>
                                    <td>{{ $report->reportedUser->banned_type }}</td>
                                    <td>{{ $report->reportedUser->banned_until }}</td>
                                    <td>
                                        <!-- Button "ban" -->
                                        <button type="button" class="btn btn-warning btn-icon ban-user-btn" 
                                            data-id="{{ $report->reportedUser->id }}" 
                                            data-reason="{{ $report->reason }}">
                                            <i class="icon-ban" style="color: white;"></i>
                                        </button>
                                        <!-- Button "delete report" -->
                                        <button type="button" class="btn btn-danger btn-icon delete-report-btn" data-id="{{ $report->id }}">
                                            <i class="ti-trash" style="color: white;"></i>
                                        </button>
                                    </td>
                                </tr>
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
window.adminReportUsersConfig = {
    csrfToken: "{{ csrf_token() }}",
    banUserRouteTemplate: "{{ route('admin.users.ban', ':id') }}",
    deleteReportRouteTemplate: "{{ route('admin.reports.delete', ':id') }}"
};
</script>
<script src="{{ asset('js/pages/admin-report-users.js') }}"></script>
@endpush