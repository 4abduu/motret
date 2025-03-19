@extends('layouts.app')

@push('styles')
  <style>
    .card-hover:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.btn-success {
    background-color: #32bd40;
    border-color: #32bd40;
}

.btn-success:hover {
    background-color: #2aa835;
    border-color: #2aa835;
}
  </style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h2 class="font-weight-bold">Manage Reports</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.users') }}" class="text-success">Report User</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.comments') }}" class="text-success">Report Comment</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.photos') }}" class="text-success">Report Photo</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Card Stats -->
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <!-- Card 1: Report User -->
            <div class="col-md-3 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #32bd40; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-user-times"></i> Report User</p>
                        <p class="fs-24 mb-2">{{ $reportUserCount }}</p>
                        <small class="text-white">Last 7 days: {{ $reportUserPercentage > 0 ? '+' : '' }}{{ $reportUserPercentage }}</small>
                    </div>
                </div>
            </div>
            <!-- Card 2: Report Comment -->
            <div class="col-md-3 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #2aa835; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-comment-slash"></i> Report Comment</p>
                        <p class="fs-24 mb-2">{{ $reportCommentCount }}</p>
                        <small class="text-white">Last 7 days: {{ $reportCommentPercentage > 0 ? '+' : '' }}{{ $reportCommentPercentage }}</small>
                    </div>
                </div>
            </div>
            <!-- Card 3: Report Reply -->
            <div class="col-md-3 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #23922d; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-reply"></i> Report Reply</p>
                        <p class="fs-24 mb-2">{{ $reportReplyCount }}</p>
                        <small class="text-white">Last 7 days: {{ $reportReplyPercentage > 0 ? '+' : '' }}{{ $reportReplyPercentage }}</small>
                    </div>
                </div>
            </div>
            <!-- Card 4: Report Photo -->
            <div class="col-md-3 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #1c7a24; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-image"></i> Report Photo</p>
                        <p class="fs-24 mb-2">{{ $reportPhotoCount }}</p>
                        <small class="text-white">Last 7 days: {{ $reportPhotoPercentage > 0 ? '+' : '' }}{{ $reportPhotoPercentage }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Reports</h5>
                <ul class="list-group">
                    @foreach($recentReports as $report)
                    <li class="list-group-item">
                        @if($report->photo_id)
                            <i class="fas fa-image"></i> {{ $report->user->name }} baru saja melakukan report foto dari {{ $report->photo->user->name }}.
                        @elseif($report->reported_user_id)
                            <i class="fas fa-user-times"></i> {{ $report->user->name }} telah melakukan report {{ $report->reportedUser->name }}.
                        @elseif($report->comment_id)
                            <i class="fas fa-comment-slash"></i> {{ $report->user->name }} telah melakukan report komentar dari {{ $report->comment->user->name }}.
                        @elseif($report->reply_id)
                            <i class="fas fa-reply"></i> {{ $report->user->name }} telah melakukan report balasan dari {{ $report->reply->user->name }}.
                        @endif
                        <span class="text-muted float-end">{{ $report->created_at->diffForHumans() }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Quick Actions</h5>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.reports.users') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-users"></i> Manage User Reports
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.reports.comments') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-comments"></i> Manage Comment Reports
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.reports.photos') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-image"></i> Manage Photo Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection