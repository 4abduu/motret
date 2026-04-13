@extends('layouts.app')

@section('title', 'Manage Comment and Reply Reports')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-report-comments.css') }}">
@endpush

@section('content')
<div class="row">
    <h3>Manage Comment and Reply Reports</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Comment and Reply Reports</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manage Comment and Reply Reports</h4>
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Type</th>
                                <th>Content</th>
                                <th>Author</th>
                                <th>Reporter</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportComments as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>Comment</td>
                                    <td>{{ $report->comment->comment }}</td>
                                    <td>{{ $report->comment->user->username }}</td>
                                    <td>{{ $report->user->username }}</td>
                                    <td>{{ $report->reason }}</td>
                                    <td>
                                        @if($report->comment->banned)
                                            <div class="badge badge-danger">Banned</div>
                                        @else
                                            <div class="badge badge-warning">Pending</div>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Button "preview" -->
                                        <button onclick="window.location.href='{{ route('admin.previewCommentReplies', ['id' => $report->comment->id, 'type' => 'comment']) }}'" class="btn btn-info btn-icon">
                                            <i class="ti-eye" style="color: white;"></i>
                                        </button>
                                        <!-- Button "ban" -->
                                        <button type="button" class="btn btn-warning btn-icon ban-comment-btn" data-id="{{ $report->comment->id }}" data-type="comment" data-reason="{{ $report->reason }}">
                                            <i class="icon-ban" style="color: white;"></i>
                                        </button>
                                        <!-- Button "delete report" -->
                                        <button type="button" class="btn btn-danger btn-icon delete-report-btn" data-id="{{ $report->id }}">
                                            <i class="ti-trash" style="color: white;"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($reportReplies as $index => $report)
                                <tr>
                                    <td>{{ $reportComments->count() + $index + 1 }}</td>
                                    <td>Reply</td>
                                    <td>{{ $report->reply->reply }}</td>
                                    <td>{{ $report->reply->user->username }}</td>
                                    <td>{{ $report->user->username }}</td>
                                    <td>{{ $report->reason }}</td>
                                    <td>
                                        @if($report->reply->banned)
                                            <div class="badge badge-danger">Banned</div>
                                        @else
                                            <div class="badge badge-warning">Pending</div>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Button "preview" -->
                                        <button onclick="window.location.href='{{ route('admin.previewCommentReplies', ['id' => $report->reply->id, 'type' => 'reply']) }}'" class="btn btn-info btn-icon">
                                            <i class="ti-eye" style="color: white;"></i>
                                        </button>
                                        <!-- Button "ban" -->
                                        <button type="button" class="btn btn-warning btn-icon ban-comment-btn" data-id="{{ $report->reply->id }}" data-type="reply" data-reason="{{ $report->reason }}">
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
window.adminReportCommentsConfig = {
    csrfToken: "{{ csrf_token() }}",
    banCommentRouteTemplate: "{{ route('admin.comments.ban', ':id') }}",
    banReplyRouteTemplate: "{{ route('admin.replies.ban', ':id') }}",
    deleteReportRouteTemplate: "{{ route('admin.reports.delete', ':id') }}"
};
</script>
<script src="{{ asset('js/pages/admin-report-comments.js') }}"></script>
@endpush