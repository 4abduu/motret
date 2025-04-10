@extends('layouts.app')

@section('title', 'Balance Management')

@push('link')
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .balance-card-1 { background-color: #32bd40; color: white; }
    .balance-card-2 { background-color: #2aa835; color: white; }
    .balance-card-3 { background-color: #23922d; color: white; }
    .balance-card-4 { background-color: #1c7a24; color: white; }
    
    .btn-success {
        background-color: #32bd40;
        border-color: #32bd40;
    }
    
    .btn-success:hover {
        background-color: #2aa835;
        border-color: #2aa835;
    }
    
    .badge-pending { background-color: #ffc107; color: #212529; }
    .badge-success { background-color: #28a745; color: white; }
    .badge-rejected { background-color: #dc3545; color: white; }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="row">
    <h2 class="font-weight-bold">Balance Management</h2>
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.saldo.daftar') }}" class="text-success">User Balances</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.saldo.penarikan') }}" class="text-success">Withdrawals</a></li>
    </ol>
</div>

<!-- Card Stats -->
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <!-- Card 1: Total Users -->
            <div class="col-md-3 mb-4 stretch-card">
                <div class="card card-hover balance-card-1">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-users"></i> Total Users</p>
                        <p class="fs-24 mb-2">{{ $totalUsers }}</p>
                        <small class="text-white">Verified users with balance</small>
                    </div>
                </div>
            </div>
            
            <!-- Card 2: Total Balance -->
            <div class="col-md-3 mb-4 stretch-card">
                <div class="card card-hover balance-card-2">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-wallet"></i> Total Balance</p>
                        <p class="fs-24 mb-2">Rp {{ number_format($totalBalance, 0, ',', '.') }}</p>
                        <small class="text-white">Last 7 days: {{ $balancePercentage }}</small>
                    </div>
                </div>
            </div>
            
            <!-- Card 3: Pending Withdrawals -->
            <div class="col-md-3 mb-4 stretch-card">
                <div class="card card-hover balance-card-3">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-clock"></i> Pending Withdrawals</p>
                        <p class="fs-24 mb-2">{{ $pendingWithdrawals }}</p>
                        <small class="text-white">Waiting for approval</small>
                    </div>
                </div>
            </div>
            
            <!-- Card 4: Monthly Withdrawals -->
            <div class="col-md-3 mb-4 stretch-card">
                <div class="card card-hover balance-card-4">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-money-bill-wave"></i> Monthly Withdrawals</p>
                        <p class="fs-24 mb-2">Rp {{ number_format($monthlyWithdrawals, 0, ',', '.') }}</p>
                        <small class="text-white">Last 7 days: {{ $withdrawalPercentage }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Users and Recent Activities -->
<div class="row">
    <!-- Top Users by Balance -->
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Top Users by Balance</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topUsers as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->profile_photo_url }}" alt="profile" class="user-avatar me-2">
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>Rp {{ number_format($user->balance, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('admin.saldo.detail', $user->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Withdrawal Activities -->
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Withdrawal Activities</h5>
                <ul class="list-group">
                    @foreach($recentWithdrawals as $withdrawal)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user me-2"></i>
                                <strong>{{ $withdrawal->user->name }}</strong> requested 
                                <strong>Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</strong>
                                via {{ ucfirst($withdrawal->method) }}
                            </div>
                            <div>
                                <span class="badge 
                                    @if($withdrawal->status == 'pending') badge-pending
                                    @elseif($withdrawal->status == 'success') badge-success
                                    @else badge-rejected
                                    @endif">
                                    {{ ucfirst($withdrawal->status) }}
                                </span>
                                <small class="text-muted ms-2">{{ $withdrawal->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
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
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('admin.saldo.daftar') }}" class="btn btn-success btn-block">
                            <i class="fas fa-users"></i> Manage User Balances
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('admin.saldo.penarikan') }}" class="btn btn-success btn-block">
                            <i class="fas fa-money-bill-wave"></i> Manage Withdrawals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection