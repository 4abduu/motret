@extends('layouts.app')

@section('title', 'Permintaan Verifikasi')

@section('content')

<div class="row">
    <h3>Permintaan Verifikasi</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Permintaan Verifikasi</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Data Permintaan Verifikasi</h4>
                </div>
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Alasan</th>
                                <th>Dokumen</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($verificationRequests as $request)
                                <tr>
                                    <td>{{ $request->full_name }}</td>
                                    <td>{{ $request->username }}</td>
                                    <td>{{ $request->reason }}</td>
                                    <td>
                                        <a href="{{ route('admin.verificationDocuments', $request->id) }}" class="btn btn-info btn-sm" style="color: white;">Lihat Dokumen</a>
                                    </td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge badge-success">Disetujui</span>
                                        @else
                                            <span class="badge badge-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <form action="{{ route('admin.verificationRequests.approve', $request->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-success">Setujui</button>
                                            </form>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">Tolak</button>
                                        @endif
                                        <button class="btn btn-danger btn-icon delete-verification-btn" data-id="{{ $request->id }}"><i class="ti-trash" style="color: white;"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($verificationRequests as $request)
<!-- Modal Tolak -->
<div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel{{ $request->id }}">Tolak Permintaan Verifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.verificationRequests.reject', $request->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="message">Pesan Penolakan</label>
                        <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger text-white">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi DataTables
    new DataTable('#example');

    // Handle delete verification request
    document.addEventListener('click', function (e) {
        if (e.target && e.target.closest('.delete-verification-btn')) {
            const button = e.target.closest('.delete-verification-btn');
            const requestId = button.getAttribute('data-id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Permintaan verifikasi ini akan dihapus beserta dokumen terkait!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/admin/verification-requests/${requestId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                        });

                        if (response.ok) {
                            Swal.fire('Berhasil!', 'Permintaan verifikasi berhasil dihapus.', 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus permintaan verifikasi.', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire('Oops...', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                    }
                }
            });
        }
    });
});
</script>
@endpush