@extends('layouts.app')

@section('title', 'Permintaan Penarikan Saldo')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-balance-withdrawals.css') }}">
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold">Permintaan Penarikan Saldo</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penarikan Saldo</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Permintaan Penarikan Saldo</h4>
                <div class="table-responsive">
                    <table id="example" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $index => $withdrawal)
                            <tr>
                                <td data-label="No">{{ $index + 1 }}</td>
                                <td data-label="User">
                                    <div class="d-flex align-items-center">
                                        @if($withdrawal->user->profile_photo_path)
                                            <img src="{{ asset('storage/'.$withdrawal->user->profile_photo_path) }}" 
                                                 class="rounded-circle me-2" 
                                                 width="30" 
                                                 height="30" 
                                                 alt="{{ $withdrawal->user->name }}">
                                        @else
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 30px; height: 30px;">
                                                {{ strtoupper(substr($withdrawal->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span>{{ $withdrawal->user->name }}</span>
                                    </div>
                                </td>
                                <td data-label="Jumlah">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                                <td data-label="Metode">{{ ucfirst($withdrawal->method) }}</td>
                                <td data-label="Tujuan">
                                    {{ $withdrawal->destination }} 
                                    <small class="d-block text-muted">{{ $withdrawal->destination_name }}</small>
                                </td>
                                <td data-label="Status">
                                    <span class="badge 
                                        @if($withdrawal->status == 'pending') badge-pending
                                        @elseif($withdrawal->status == 'success') badge-success
                                        @else badge-rejected
                                        @endif">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                                <td data-label="Tanggal">{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                                <td>
                                        <button class="btn btn-info btn-icon action-btn" data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $withdrawal->id }}" title="Detail">
                                            <i class="ti-info" style="color: white;"></i>
                                        </button>

                                        @if($withdrawal->status == 'pending')
                                            <button class="btn btn-success btn-icon action-btn approve-btn" 
                                                    data-id="{{ $withdrawal->id }}" 
                                                    title="Setujui">
                                                <i class="ti-check" style="color: white;"></i>
                                            </button>
                                            
                                            <button class="btn btn-danger btn-icon action-btn reject-btn" 
                                                    data-id="{{ $withdrawal->id }}" 
                                                    title="Tolak">
                                                <i class="ti-close" style="color: white;"></i>
                                            </button>
                                        @endif

                                        <button class="btn btn-danger btn-icon action-btn delete-btn" 
                                                data-id="{{ $withdrawal->id }}" 
                                                title="Hapus">
                                            <i class="ti-trash" style="color: white;"></i>
                                        </button>
                                </td>
                            </tr>

                            <!-- Modal Detail -->
                            <div class="modal fade" id="detailModal{{ $withdrawal->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Penarikan</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="info-card">
                                                <h6 class="mb-3 fw-bold text-success">
                                                    <i class="fas fa-info-circle me-2"></i>Informasi Penarikan
                                                </h6>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <div class="info-label">User</div>
                                                        <div class="info-value">
                                                            <div class="d-flex align-items-center">
                                                                @if($withdrawal->user->profile_photo_path)
                                                                    <img src="{{ asset('storage/'.$withdrawal->user->profile_photo_path) }}" 
                                                                         class="rounded-circle me-2" 
                                                                         width="30" 
                                                                         height="30" 
                                                                         alt="{{ $withdrawal->user->name }}">
                                                                @else
                                                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" 
                                                                         style="width: 30px; height: 30px;">
                                                                        {{ strtoupper(substr($withdrawal->user->name, 0, 1)) }}
                                                                    </div>
                                                                @endif
                                                                <span>{{ $withdrawal->user->name }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-label">Jumlah</div>
                                                        <div class="info-value">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-label">Status</div>
                                                        <div class="info-value">
                                                            <span class="badge 
                                                                @if($withdrawal->status == 'pending') badge-pending
                                                                @elseif($withdrawal->status == 'success') badge-success
                                                                @else badge-rejected
                                                                @endif">
                                                                {{ ucfirst($withdrawal->status) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <div class="info-label">Metode</div>
                                                        <div class="info-value">{{ ucfirst($withdrawal->method) }}</div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-label">Nomor Tujuan</div>
                                                        <div class="info-value">{{ $withdrawal->destination }}</div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="info-label">Nama Penerima</div>
                                                        <div class="info-value">{{ $withdrawal->destination_name }}</div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="info-label">Catatan</div>
                                                        <div class="info-value">{{ $withdrawal->note ?? '-' }}</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="info-label">Tanggal</div>
                                                        <div class="info-value">{{ $withdrawal->created_at->format('d M Y H:i') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($withdrawal->status == 'pending')
                                            <div class="d-flex justify-content-center gap-3">
                                                <button class="btn btn-success approve-btn" 
                                                        data-id="{{ $withdrawal->id }}">
                                                    <i class="ti-check me-2"></i>Setujui
                                                </button>
                                                <button class="btn btn-danger reject-btn" 
                                                        data-id="{{ $withdrawal->id }}">
                                                    <i class="ti-close me-2"></i>Tolak
                                                </button>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.adminBalanceWithdrawalsConfig = {
    csrfToken: "{{ csrf_token() }}",
    approveRouteTemplate: "/admin/penarikan-saldo/:id/acc",
    rejectRouteTemplate: "/admin/penarikan-saldo/:id/reject",
    deleteRouteTemplate: "/admin/penarikan-saldo/:id/delete"
};
</script>
<script src="{{ asset('js/pages/admin-balance-withdrawals.js') }}"></script>
@endpush