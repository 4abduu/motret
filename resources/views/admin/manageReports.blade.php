@extends('layouts.app')  {{-- Menggunakan layout umum untuk header, footer, dan lainnya --}}

@section('content')
<div class="row">
              <div class="col-md-12 grid-margin">
                <div class="row">
                  <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h2 class="font-weight-bold">Manage Report</h2>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item active">Dashboard</li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.manageReports') }}" class="text-success">Manage Report</a></li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.reports.users') }}" class="text-success">Report User</a></li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.reports.comments') }}" class="text-success">Report Comment</a></li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.reports.photos') }}" class="text-success">Report Photo</a></li>
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
                                    <p class="mb-4">Report User</p>
                                    <p class="fs-30 mb-2">{{ $reportUserCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.reports.users') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-yellow">
                                <div class="card-body">
                                    <p class="mb-4">Report Comment</p>
                                    <p class="fs-30 mb-2">{{ $reportCommentCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.reports.comments') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-green">
                                <div class="card-body">
                                    <p class="mb-4">Report Photo</p>
                                    <p class="fs-30 mb-2">{{ $reportPhotoCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.reports.photos') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection