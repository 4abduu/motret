@extends('layouts.app')  {{-- Menggunakan layout umum untuk header, footer, dan lainnya --}}

@section('content')
<div class="row">
              <div class="col-md-12 grid-margin">
                <div class="row">
                  <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h2 class="font-weight-bold">Welcome Admin</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Manage User</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.photos') }}">Manage Foto</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.comments') }}">Manage Comment</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions') }}">Manage Berlangganan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports') }}">Manage Report</a></li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-8 grid-margin transparent">
                <div class="row">
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4">Data User</p>
                                <p class="fs-30 mb-4">{{ $userCount }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4">Data Foto</p>
                                <p class="fs-30 mb-4">{{ $photoCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                    <div class="card card-light-blue">
                      <div class="card-body">
                        <p class="mb-4">Data Komentar</p>
                        <p class="fs-30 mb-4">{{ $commentCount}}</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 stretch-card transparent">
                    <div class="card card-light-danger">
                      <div class="card-body">
                        <p class="mb-4">Data Berlangganan</p>
                        <p class="fs-30 mb-2">#</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
@endsection