@extends('layouts.app')

@push('link')
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
                <h2 class="font-weight-bold">Manage Comment</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Comment</li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.comments') }}" class="text-success">Manage Comment</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.replies') }}" class="text-success">Manage Replies</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Card Stats -->
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <!-- Card 1: Manage Comment -->
            <div class="col-md-2 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #32bd40; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-comment"></i> Comments</p>
                        <p class="fs-24 mb-2">{{ $commentCount }}</p>
                        <small class="text-white">Last 7 days: {{ $commentPercentage > 0 ? '+' : '' }}{{ $commentPercentage }}</small>
                    </div>
                </div>
            </div>
            <!-- Card 2: Manage Replies -->
            <div class="col-md-2 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #2aa835; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-reply"></i> Replies</p>
                        <p class="fs-24 mb-2">{{ $replyCount }}</p>
                        <small class="text-white">Last 7 days: {{ $replyPercentage > 0 ? '+' : '' }}{{ $replyPercentage }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tampilkan jumlah komentar dan balasan secara terpisah -->
<div class="row">
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Comments</h5>
                <ul class="list-group">
                    @foreach($recentComments as $comment)
                    <li class="list-group-item">
                        <i class="fas fa-comment"></i> {{ $comment->user->name }}: {{ Str::limit($comment->comment, 50) }}
                        <span class="text-muted float-end">{{ $comment->created_at->diffForHumans() }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Replies</h5>
                <ul class="list-group">
                    @foreach($recentReplies as $reply)
                    <li class="list-group-item">
                        <i class="fas fa-reply"></i> {{ $reply->user->name }}: {{ Str::limit($reply->reply, 50) }}
                        <span class="text-muted float-end">{{ $reply->created_at->diffForHumans() }}</span>
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
                        <a href="{{ route('admin.comments') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-comments"></i> Manage Comments
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.replies') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-reply"></i> Manage Replies
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection