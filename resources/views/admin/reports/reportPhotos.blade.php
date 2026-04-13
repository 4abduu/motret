@extends('layouts.app')

@section('title', 'Manage Reports')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-report-photos.css') }}">
@endpush

@section('content')
<div class="row">
    <h3>Manage Reports Photo</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Reports Photo</li>
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
                                <th>Status</th>
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
                                        <!-- Button "preview" -->
                                        <button onclick="window.location.href='{{ route('admin.users.previewPhotos', $report->photo->id) }}'" class="btn btn-info btn-icon">
                                            <i class="ti-eye" style="color: white;"></i>
                                        </button>
                                        <!-- Button "ban" -->
                                        <button type="button" class="btn btn-warning btn-icon ban-photo-btn" data-id="{{ $report->photo->id }}" data-reason="{{ $report->reason }}">
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
window.adminReportPhotosConfig = {
    csrfToken: "{{ csrf_token() }}",
    banPhotoRouteTemplate: "{{ route('admin.photos.ban', ':id') }}",
    deleteReportRouteTemplate: "{{ route('admin.reports.delete', ':id') }}"
};
</script>
<script src="{{ asset('js/pages/admin-report-photos.js') }}"></script>
@endpush