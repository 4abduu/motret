@extends('layouts.app')  {{-- Menggunakan layout umum untuk header, footer, dan lainnya --}}

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin">
      <div class="row">
        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
          <h2 class="font-weight-bold">Manage Comment</h2>
          <ol class="breadcrumb">
              <li class="breadcrumb-item active">Comment</li>
              <li class="breadcrumb-item"><a href="{{ route('admin.users') }}" class="text-success">Manage Comment</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.photos') }}" class="text-success">Manage Replies</a></li>
          </ol>
        </div>
      </div>
    </div>
  </div>
            <div class="row">
                <div class="col-md-12 grid-margin transparent">
                    <div class="row">
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-light-blue">
                                <div class="card-body">
                                    <p class="mb-4">Manage Comment</p>
                                    <p class="fs-30 mb-2">{{ $commentCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.comments') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-yellow">
                                <div class="card-body">
                                    <p class="mb-4">Manage Replies</p>
                                    <p class="fs-30 mb-2">{{ $replyCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.replies') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection