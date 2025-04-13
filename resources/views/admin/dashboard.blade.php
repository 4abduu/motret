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
    <h2>Welcome Admin</h2>
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users') }}" class="text-success">Manage User</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.photos') }}" class="text-success">Manage Foto</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.manageComments') }}" class="text-success">Manage Verification</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions') }}" class="text-success">Manage Comment</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.manageReports') }}" class="text-success">Manage Subscription</a></li>
    </ol>
</div>

<!-- Card Stats -->
<div class="row">
  <div class="col-md-12 grid-margin">
      <div class="row">
          <!-- Card 1: Data User -->
          <div class="col-md-2 mb-4 stretch-card">
              <div class="card card-hover" style="background-color: #32bd40; color: white;">
                  <div class="card-body">
                      <p class="mb-2"><i class="fas fa-users"></i> Users</p>
                      <p class="fs-24 mb-2">{{ $userCount }}</p>
                      <small class="text-white">Last 7 days: {{ $userPercentage > 0 ? '+' : '' }}{{ $userPercentage }}</small>
                  </div>
              </div>
          </div>
          <!-- Card 2: Data Foto -->
          <div class="col-md-2 mb-4 stretch-card">
              <div class="card card-hover" style="background-color: #2aa835; color: white;">
                  <div class="card-body">
                      <p class="mb-2"><i class="fas fa-image"></i> Photos</p>
                      <p class="fs-24 mb-2">{{ $photoCount }}</p>
                      <small class="text-white">Last 7 days: {{ $photoPercentage > 0 ? '+' : '' }}{{ $photoPercentage }}</small>
                  </div>
              </div>
          </div>
          <!-- Card 3: Data Komentar -->
          <div class="col-md-2 mb-4 stretch-card">
              <div class="card card-hover" style="background-color: #23922d; color: white;">
                  <div class="card-body">
                      <p class="mb-2"><i class="fas fa-comment"></i> Comments</p>
                      <p class="fs-24 mb-2">{{ $commentCount }}</p>
                      <small class="text-white">Last 7 days: {{ $commentPercentage > 0 ? '+' : '' }}{{ $commentPercentage }}</small>
                  </div>
              </div>
          </div>
          <!-- Card 4: Data Laporan -->
          <div class="col-md-2 mb-4 stretch-card">
              <div class="card card-hover" style="background-color: #1c7a24; color: white;">
                  <div class="card-body">
                      <p class="mb-2"><i class="fas fa-flag"></i> Reports</p>
                      <p class="fs-24 mb-2">{{ $reportCount }}</p>
                      <small class="text-white">Last 7 days: {{ $reportPercentage > 0 ? '+' : '' }}{{ $reportPercentage }}</small>
                  </div>
              </div>
          </div>
          <!-- Card 5: Data Transaksi -->
          <div class="col-md-2 mb-4 stretch-card">
              <div class="card card-hover" style="background-color: #15631b; color: white;">
                  <div class="card-body">
                      <p class="mb-2"><i class="fas fa-dollar-sign"></i> Transactions</p>
                      <p class="fs-24 mb-2">{{ $transactionCount }}</p>
                      <small class="text-white">Last 7 days: {{ $transactionPercentage > 0 ? '+' : '' }}{{ $transactionPercentage }}</small>
                  </div>
              </div>
          </div>
          <!-- Card 6: Data Verifikasi -->
          <div class="col-md-2 mb-4 stretch-card">
              <div class="card card-hover" style="background-color: #0f4d14; color: white;">
                  <div class="card-body">
                      <p class="mb-2"><i class="fas fa-check-circle"></i> Verifications</p>
                      <p class="fs-24 mb-2">{{ $verificationCount }}</p>
                      <small class="text-white">Last 7 days: {{ $verificationPercentage > 0 ? '+' : '' }}{{ $verificationPercentage }}</small>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>

<!-- Chart -->
<div class="row">
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">User Growth</h5>
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Photo Uploads</h5>
                <canvas id="photoUploadChart"></canvas>
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
                  @foreach($recentUsers as $user)
                  <li class="list-group-item"><i class="fas fa-user-plus"></i> New user registered: {{ $user->name }}</li>
                  @endforeach
                  @foreach($recentPhotos as $photo)
                  <li class="list-group-item"><i class="fas fa-image"></i> New photo uploaded by {{ $photo->user->name }}</li>
                  @endforeach
                  @foreach($recentComments as $comment)
                  <li class="list-group-item"><i class="fas fa-comment"></i> New comment by {{ $comment->user->name }}</li>
                  @endforeach
                  @foreach($recentReports as $report)
                  <li class="list-group-item"><i class="fas fa-flag"></i> New report submitted by {{ $report->user->name }}</li>
                  @endforeach
                  @foreach($recentVerifications as $verification)
                  <li class="list-group-item"><i class="fas fa-check-circle"></i> New verification request by {{ $verification->user->name }}</li>
                  @endforeach
                  @foreach($recentTransactions as $transaction)
                  <li class="list-group-item"><i class="fas fa-dollar-sign"></i> New transaction by {{ $transaction->user->name }}: {{ $transaction->gross_amount }}</li>
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
                        <a href="{{ route('admin.users') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.photos') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-image"></i> Manage Photos
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.manageComments') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-comments"></i> Manage Comments
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.subscriptions') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-dollar-sign"></i> Manage Subscriptions
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.manageReports') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-flag"></i> Manage Reports
                        </a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="{{ route('admin.verificationRequests') }}" class="btn btn-success btn-block" style="color: white;">
                            <i class="fas fa-check-circle"></i> Manage Verifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    
<!-- Script untuk Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: @json($userGrowthData['labels']),
            datasets: [{
                label: 'User Growth',
                data: @json($userGrowthData['data']),
                backgroundColor: 'rgba(50, 189, 64, 0.2)',
                borderColor: '#32bd40',
                borderWidth: 2,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Photo Upload Chart
    const photoUploadCtx = document.getElementById('photoUploadChart').getContext('2d');
    new Chart(photoUploadCtx, {
        type: 'line',
        data: {
            labels: @json($photoUploadData['labels']),
            datasets: [{
                label: 'Photo Uploads',
                data: @json($photoUploadData['data']),
                backgroundColor: 'rgba(42, 168, 53, 0.2)',
                borderColor: '#2aa835',
                borderWidth: 2,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@if(session('login_success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: '{{ session('login_success') }}',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            toast: true,
            background: '#32bd40',
            color: '#fff',
            iconColor: '#fff',
            didOpen: (toast) => {
                toast.addEventListener('click', () => {
                    Swal.close();
                })
            }
        });
    });
</script>
@endif
@endpush