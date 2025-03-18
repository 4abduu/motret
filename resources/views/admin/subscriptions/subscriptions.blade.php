@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h2 class="font-weight-bold">Manage Transactions</h2>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.transactions') }}" class="text-success">Transactions</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.systemPrices') }}" class="text-success">System Prices</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.userPrices') }}" class="text-success">User Prices</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.userSubscriptions') }}" class="text-success">User Subs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.systemSubscriptions') }}" class="text-success">System Subs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.comboSubscriptions') }}" class="text-success">Combo Subs</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Card Stats -->
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <!-- Card 1: Transactions -->
            <div class="col-md-2 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #32bd40; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-dollar-sign"></i> Transactions</p>
                        <p class="fs-24 mb-2">{{ $transactionCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Card 2: User Prices -->
            <div class="col-md-2 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #2aa835; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-tag"></i> User Prices</p>
                        <p class="fs-24 mb-2">{{ $userPriceCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Card 3: System Prices -->
            <div class="col-md-2 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #23922d; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-tags"></i> System Prices</p>
                        <p class="fs-24 mb-2">{{ $systemPriceCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Card 4: User Subs -->
            <div class="col-md-2 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #1c7a24; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-users"></i> User Subs</p>
                        <p class="fs-24 mb-2">{{ $userSubscriptionCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Card 5: System Subs -->
            <div class="col-md-2 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #15631b; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-server"></i> System Subs</p>
                        <p class="fs-24 mb-2">{{ $systemSubscriptionCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Card 6: Combo Subs -->
            <div class="col-md-2 mb-4 stretch-card">
                <div class="card card-hover" style="background-color: #0f4d14; color: white;">
                    <div class="card-body">
                        <p class="mb-2"><i class="fas fa-box"></i> Combo Subs</p>
                        <p class="fs-24 mb-2">{{ $comboSubscriptionCount }}</p>
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
                <h5 class="card-title">Recent Activities</h5>
                <ul class="list-group">
                    @foreach($recentActivities as $activity)
                    <li class="list-group-item">
                        @if($activity['type'] == 'price_system_change')
                            <i class="fas fa-tags"></i> {{ $activity['message'] }}
                        @elseif($activity['type'] == 'price_user_change')
                            <i class="fas fa-tag"></i> {{ $activity['message'] }}
                        @elseif($activity['type'] == 'system_subscription')
                            <i class="fas fa-server"></i> {{ $activity['message'] }}
                        @elseif($activity['type'] == 'user_subscription')
                            <i class="fas fa-users"></i> {{ $activity['message'] }}
                        @elseif($activity['type'] == 'combo_subscription')
                            <i class="fas fa-box"></i> {{ $activity['message'] }}
                        @endif
                        <span class="text-muted float-end">{{ $activity['created_at']->diffForHumans() }}</span>
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
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.subscriptions.transactions') }}" class="btn btn-success btn-block">
                            <i class="fas fa-list"></i> View All Transactions
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.subscriptions.systemPrices') }}" class="btn btn-success btn-block">
                            <i class="fas fa-tags"></i> System Prices
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.subscriptions.userPrices') }}" class="btn btn-success btn-block">
                            <i class="fas fa-tag"></i> User Prices
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.subscriptions.userSubscriptions') }}" class="btn btn-success btn-block">
                            <i class="fas fa-users"></i> User Subs
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.subscriptions.systemSubscriptions') }}" class="btn btn-success btn-block">
                            <i class="fas fa-server"></i> System Subs
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.subscriptions.comboSubscriptions') }}" class="btn btn-success btn-block">
                            <i class="fas fa-box"></i> Combo Subs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection