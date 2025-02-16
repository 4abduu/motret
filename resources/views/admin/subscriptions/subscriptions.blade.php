@extends('layouts.app')  {{-- Menggunakan layout umum untuk header, footer, dan lainnya --}}

@section('content')
<div class="row">
              <div class="col-md-12 grid-margin">
                <div class="row">
                  <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h2 class="font-weight-bold">Manage Subscriptions</h2>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item active">Subscriptions</li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.transactions') }}" class="text-success">Transactions</a></li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.systemPrices') }}" class="text-success">Subs System Price</a></li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.userPrices') }}" class="text-success">Subs User Price</a></li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.userSubscriptions') }}"class="text-success">Subs System</a></li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.systemSubscriptions') }}"class="text-success">Subs Users</a></li>
                      <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.comboSubscriptions') }}" class="text-success">Subs Combo</a></li>
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
                                    <p class="mb-4">Transaction</p>
                                    <p class="fs-30 mb-2">{{ $subsTransactionCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.subscriptions.transactions') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-yellow">
                                <div class="card-body">
                                    <p class="mb-4">System Price</p>
                                    <p class="fs-30 mb-2">{{ $subsSystemPriceCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.subscriptions.systemPrices') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-green">
                                <div class="card-body">
                                    <p class="mb-4">User Price</p>
                                    <p class="fs-30 mb-2">{{ $subsUserPriceCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.subscriptions.userPrices') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-green">
                                <div class="card-body">
                                    <p class="mb-4">Subs User</p>
                                    <p class="fs-30 mb-2">{{ $subsUserCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.subscriptions.userSubscriptions') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-green">
                                <div class="card-body">
                                    <p class="mb-4">Subs System</p>
                                    <p class="fs-30 mb-2">{{ $subsSystemCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.subscriptions.systemSubscriptions') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-green">
                                <div class="card-body">
                                    <p class="mb-4">Subs Combo</p>
                                    <p class="fs-30 mb-2">{{ $subsComboCount }}</p>
                                    <div class="text-end">
                                        <a href="{{ route('admin.subscriptions.comboSubscriptions') }}" class="text-white">View >></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection