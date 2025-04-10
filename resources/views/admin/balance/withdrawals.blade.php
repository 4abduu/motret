@extends('layouts.app')

@section('title', 'Permintaan Penarikan Saldo')

@push('link')
<style>
    /* Card Styles */
    .card-header-custom {
        background-color: #32bd40 !important;
        color: white !important;
        padding: 1rem 1.5rem;
    }
    
    /* Button Styles */
    .action-btn {
        margin: 0.25rem;
        min-width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    /* Badge Styles */
    .badge-pending {
        background-color: #ffc107;
        color: #212529;
        padding: 0.35em 0.65em;
    }
    
    .badge-success {
        background-color: #28a745;
        color: white;
        padding: 0.35em 0.65em;
    }
    
    .badge-rejected {
        background-color: #dc3545;
        color: white;
        padding: 0.35em 0.65em;
    }
    
    /* Table Styles */
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }
    
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 1rem;
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .table tbody tr:hover {
        background-color: rgba(50, 189, 64, 0.05);
    }
    
    /* Modal Styles */
    .modal-header {
        background-color: #32bd40;
        color: white;
        padding: 1.25rem 1.5rem;
    }
    
    .modal-title {
        font-weight: 600;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .info-card {
        border-left: 4px solid #32bd40;
        border-radius: 0 8px 8px 0;
        background-color: #f8f9fa;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        font-size: 1.05rem;
        color: #212529;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 0;
        }
        
        .table thead {
            display: none;
        }
        
        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        
        .table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            margin-right: 1rem;
            color: #495057;
        }
        
        .table tbody td:last-child {
            border-bottom: none;
        }
        
        .action-buttons {
            justify-content: flex-end;
        }
    }
</style>
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
    <div class="col-12">
        <div class="card">
            <div class="card-header card-header-custom">
                <h4 class="mb-0">Daftar Permintaan Penarikan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="withdrawalsTable" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="text-end">Aksi</th>
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
                                    <div class="d-flex justify-content-end action-buttons">
                                        <button class="btn btn-info btn-icon action-btn" data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $withdrawal->id }}" title="Detail">
                                            <i class="ti-info-alt"></i>
                                        </button>

                                        @if($withdrawal->status == 'pending')
                                            <button class="btn btn-success btn-icon action-btn approve-btn" 
                                                    data-id="{{ $withdrawal->id }}" 
                                                    title="Setujui">
                                                <i class="ti-check"></i>
                                            </button>
                                            
                                            <button class="btn btn-danger btn-icon action-btn reject-btn" 
                                                    data-id="{{ $withdrawal->id }}" 
                                                    title="Tolak">
                                                <i class="ti-close"></i>
                                            </button>
                                        @endif

                                        <button class="btn btn-danger btn-icon action-btn delete-btn" 
                                                data-id="{{ $withdrawal->id }}" 
                                                title="Hapus">
                                            <i class="ti-trash"></i>
                                        </button>
                                    </div>
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
document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTable
    $('#withdrawalsTable').DataTable({
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // SweetAlert2 configuration
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Handle approve withdrawal
    $(document).on('click', '.approve-btn', function() {
        const withdrawalId = $(this).data('id');
        
        Swal.fire({
            title: 'Setujui Penarikan?',
            text: "Anda yakin ingin menyetujui permintaan penarikan ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin/penarikan-saldo/${withdrawalId}/acc`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Penarikan berhasil disetujui.'
                        });
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Terjadi kesalahan saat menyetujui penarikan.'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat memproses permintaan.'
                    });
                }
            }
        });
    });

    // Handle reject withdrawal
    $(document).on('click', '.reject-btn', function() {
        const withdrawalId = $(this).data('id');
        
        Swal.fire({
            title: 'Tolak Penarikan?',
            text: "Anda yakin ingin menolak permintaan penarikan ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            input: 'textarea',
            inputLabel: 'Alasan Penolakan (Opsional)',
            inputPlaceholder: 'Masukkan alasan penolakan...',
            inputAttributes: {
                'aria-label': 'Masukkan alasan penolakan'
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin/penarikan-saldo/${withdrawalId}/reject`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ note: result.value })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Penarikan berhasil ditolak.'
                        });
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Terjadi kesalahan saat menolak penarikan.'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat memproses permintaan.'
                    });
                }
            }
        });
    });

    // Handle delete withdrawal
    $(document).on('click', '.delete-btn', function() {
        const withdrawalId = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Data Penarikan?',
            text: "Anda tidak akan dapat mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin/penarikan-saldo/${withdrawalId}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message || 'Data penarikan berhasil dihapus.'
                        });
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Terjadi kesalahan saat menghapus data penarikan.'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat memproses permintaan.'
                    });
                }
            }
        });
    });
});
</script>
@endpush