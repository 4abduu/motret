@extends('layouts.app')

@section('title', 'Manage User Reports')

@push('link')
    <style>
        .dt-length {
            margin-left: 20px;
            padding-bottom: 10px;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <h3>Manage User Reports</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage User Reports</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manage User Reports</h4>
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Reported User</th>
                                <th>Reporter</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Banned Type</th>
                                <th>Banned Until</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportUsers as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ route('admin.users.previewProfile', $report->reportedUser->id) }}" style="color: black;"><b>{{ $report->reportedUser->username }}</b></a></td>
                                    <td><a href="{{ route('admin.users.previewProfile', $report->user->id) }}" style="color: black;"><b>{{ $report->user->username }}</b></a></td>
                                    <td>{{ $report->reason }}</td>
                                    <td>
                                        @if($report->reportedUser->banned)
                                            <div class="badge badge-danger">Banned</div>
                                        @else
                                            <div class="badge badge-warning">Pending</div>
                                        @endif
                                    </td>
                                    <td>{{ $report->reportedUser->banned_type }}</td>
                                    <td>{{ $report->reportedUser->banned_until }}</td>
                                    <td>
                                        <!-- Button "ban" -->
                                        <button type="button" class="btn btn-warning btn-icon ban-user-btn" 
                                            data-id="{{ $report->reportedUser->id }}" 
                                            data-reason="{{ $report->reason }}">
                                            <i class="icon-ban" style="color: white;"></i>
                                        </button>
                                        <!-- Button "delete report" -->
                                        <button type="button" class="btn btn-danger btn-icon delete-report-btn" data-id="{{ $report->id }}">
                                            <i class="ti-trash" style="color: white;"></i>
                                        </button>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle ban user button
        $('.ban-user-btn').click(function() {
            const userId = $(this).data('id');
            const reason = $(this).data('reason');
            
            Swal.fire({
                title: 'Konfirmasi Ban Pengguna',
                html: `<p>Anda yakin ingin membanned pengguna ini?</p>
                       <p><strong>Alasan:</strong> ${reason}</p>
                       <div class="form-group mt-3">
                           <label for="swal-ban-type">Tipe Ban</label>
                           <select id="swal-ban-type" class="form-control">
                               <option value="temporary">Sementara</option>
                               <option value="permanent">Permanen</option>
                           </select>
                       </div>
                       <div class="form-group mt-3" id="swal-ban-until-group">
                           <label for="swal-ban-until">Tanggal Berakhir Ban</label>
                           <input type="date" id="swal-ban-until" class="form-control">
                       </div>
                       <div class="form-group mt-3">
                           <label for="swal-ban-reason">Alasan Ban</label>
                           <textarea id="swal-ban-reason" class="form-control">${reason}</textarea>
                       </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Ban!',
                cancelButtonText: 'Batal',
                focusConfirm: false,
                preConfirm: () => {
                    const bannedType = $('#swal-ban-type').val();
                    const bannedUntil = bannedType === 'temporary' ? $('#swal-ban-until').val() : null;
                    const bannedReason = $('#swal-ban-reason').val();
                    
                    if (!bannedReason) {
                        Swal.showValidationMessage('Alasan ban harus diisi');
                        return false;
                    }
                    
                    if (bannedType === 'temporary' && !bannedUntil) {
                        Swal.showValidationMessage('Tanggal berakhir ban harus diisi untuk ban sementara');
                        return false;
                    }
                    
                    return {
                        banned_type: bannedType,
                        banned_until: bannedUntil,
                        banned_reason: bannedReason
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { banned_type, banned_until, banned_reason } = result.value;
                    
                    $.ajax({
                        url: "{{ route('admin.users.ban', ':id') }}".replace(':id', userId),
                        type: 'PUT',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'PUT',
                            banned_type: banned_type,
                            banned_until: banned_until,
                            banned_reason: banned_reason
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message || 'Pengguna berhasil dibanned.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message || 'Terjadi kesalahan saat memproses permintaan.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
            
            // Toggle date input based on ban type selection
            $('#swal-ban-type').change(function() {
                if ($(this).val() === 'permanent') {
                    $('#swal-ban-until-group').hide();
                } else {
                    $('#swal-ban-until-group').show();
                }
            }).trigger('change');
        });

        // Handle delete report button
        $('.delete-report-btn').click(function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus laporan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.reports.delete', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message || 'Laporan berhasil dihapus.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message || 'Terjadi kesalahan saat menghapus laporan.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush