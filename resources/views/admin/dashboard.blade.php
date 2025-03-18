@extends('layouts.app')  {{-- Menggunakan layout umum untuk header, footer, dan lainnya --}}

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h2 class="font-weight-bold">Welcome Admin</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}" class="text-success">Manage User</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.photos') }}" class="text-success">Manage Foto</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.manageComments') }}" class="text-success">Manage Comment</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions') }}" class="text-success">Manage Berlangganan</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.manageReports') }}" class="text-success">Manage Report</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 grid-margin transparent">
        <div class="row">
            <div class="col-md-3 mb-4 stretch-card transparent">
                <div class="card card-tale">
                    <div class="card-body">
                        <p class="mb-4">Data User</p>
                        <p class="fs-30 mb-4">{{ $userCount }}</p>
                        <div class="text-end">
                            <a href="{{ route('admin.users') }}" class="text-white">View >></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4 stretch-card transparent">
                <div class="card card-dark-blue">
                    <div class="card-body">
                        <p class="mb-4">Data Foto</p>
                        <p class="fs-30 mb-4">{{ $photoCount }}</p>
                        <div class="text-end">
                            <a href="{{ route('admin.photos') }}" class="text-white">View >></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4 stretch-card transparent">
              <div class="card card-light-green">
                  <div class="card-body">
                      <p class="mb-4">Data Verifikasi</p>
                      <p class="fs-30 mb-4">{{ $verificationCount }}</p>
                      <div class="text-end">
                          <a href="{{ route('admin.verificationRequests') }}" class="text-white">View >></a>
                      </div>
                  </div>
              </div>
          </div>
            <div class="col-md-3 mb-4 stretch-card transparent">
                <div class="card card-light-blue">
                    <div class="card-body">
                        <p class="mb-4">Data Komentar</p>
                        <p class="fs-30 mb-4">{{ $commentCount }}</p>
                        <div class="text-end">
                            <a href="{{ route('admin.manageComments') }}" class="text-white">View >></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-4 stretch-card transparent">
                <div class="card card-light-yellow">
                    <div class="card-body">
                        <p class="mb-4">Data Laporan</p>
                        <p class="fs-30 mb-2">{{ $reportCount }}</p>
                        <div class="text-end">
                            <a href="{{ route('admin.manageReports') }}" class="text-white">View >></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4 stretch-card transparent">
              <div class="card card-light-danger">
                  <div class="card-body">
                      <p class="mb-4">Data Berlangganan</p>
                      <p class="fs-30 mb-2">{{ $transactionCount }}</p>
                      <div class="text-end">
                          <a href="{{ route('admin.subscriptions') }}" class="text-white">View >></a>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </div>
</div>
@endsection