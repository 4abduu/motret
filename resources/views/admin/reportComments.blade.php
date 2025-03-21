@extends('layouts.app')

@section('title', 'Manage Comment and Reply Reports')

@section('content')
<div class="row">
    <h3>Manage Comment and Reply Reports</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Comment and Reply Reports</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manage Comment and Reply Reports</h4>
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Type</th>
                                <th>Content</th>
                                <th>Author</th>
                                <th>Reporter</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportComments as $index => $report)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>Comment</td>
                                    <td>{{ $report->comment->comment }}</td>
                                    <td>{{ $report->comment->user->username }}</td>
                                    <td>{{ $report->user->username }}</td>
                                    <td>{{ $report->reason }}</td>
                                    <td>
                                        @if($report->comment->banned)
                                            <div class="badge badge-danger">Banned</div>
                                        @else
                                            <div class="badge badge-warning">Pending</div>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Button "preview" -->
                                        <button onclick="window.location.href='{{ route('admin.users.previewComments', $report->comment_id) }}'" class="btn btn-info btn-icon">
                                            <i class="ti-eye" style="color: white;"></i>
                                        </button>
                                        <!-- Button "ban" -->
                                        <button type="button" class="btn btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="#banCommentModal{{ $report->id }}">
                                            <i class="icon-ban" style="color: white;"></i>
                                        </button>
                                        <!-- Button "delete report" -->
                                        <button type="button" class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#deleteReportModal{{ $report->id }}">
                                            <i class="ti-trash" style="color: white;"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Konfirmasi Ban -->
                                <div class="modal fade" id="banCommentModal{{ $report->id }}" tabindex="-1" aria-labelledby="banCommentModalLabel{{ $report->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="banCommentModalLabel{{ $report->id }}">Konfirmasi Ban Komentar</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Commented By:</strong> {{ $report->comment->user->username }}</p>
                                                <p><strong>Reported By:</strong> {{ $report->user->username }}</p>
                                                <p><strong>Reason:</strong> {{ $report->reason }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST" action="{{ route('admin.comments.ban', $report->comment->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-danger text-white">Ban</button>
                                                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Konfirmasi Hapus -->
                                <div class="modal fade" id="deleteReportModal{{ $report->id }}" tabindex="-1" aria-labelledby="deleteReportModalLabel{{ $report->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteReportModalLabel{{ $report->id }}">Konfirmasi Hapus Laporan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus laporan ini?
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST" action="{{ route('admin.reports.delete', $report->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger text-white">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @foreach($reportReplies as $index => $report)
                                <tr>
                                    <td>{{ $reportComments->count() + $index + 1 }}</td>
                                    <td>Reply</td>
                                    <td>{{ $report->reply->reply }}</td>
                                    <td>{{ $report->reply->user->username }}</td>
                                    <td>{{ $report->user->username }}</td>
                                    <td>{{ $report->reason }}</td>
                                    <td>
                                        @if($report->reply->banned)
                                            <div class="badge badge-danger">Banned</div>
                                        @else
                                            <div class="badge badge-warning">Pending</div>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Button "preview" -->
                                        <a href="{{ route('admin.users.previewReplies', $report->reply_id) }}" class="btn btn-info btn-icon">
                                            <i class="ti-eye" style="color: white;"></i>
                                        </a>
                                        <!-- Button "ban" -->
                                        <button type="button" class="btn btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="#banReplyModal{{ $report->id }}">
                                            <i class="icon-ban" style="color: white;"></i>
                                        </button>
                                        <!-- Button "delete report" -->
                                        <button type="button" class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#deleteReportModal{{ $report->id }}">
                                            <i class="ti-trash" style="color: white;"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Konfirmasi Ban -->
                                <div class="modal fade" id="banReplyModal{{ $report->id }}" tabindex="-1" aria-labelledby="banReplyModalLabel{{ $report->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="banReplyModalLabel{{ $report->id }}">Konfirmasi Ban Balasan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Replied By:</strong> {{ $report->reply->user->username }}</p>
                                                <p><strong>Reported By:</strong> {{ $report->user->username }}</p>
                                                <p><strong>Reason:</strong> {{ $report->reason }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST" action="{{ route('admin.replies.ban', $report->reply->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-danger text-white">Ban</button>
                                                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Konfirmasi Hapus -->
                                <div class="modal fade" id="deleteReportModal{{ $report->id }}" tabindex="-1" aria-labelledby="deleteReportModalLabel{{ $report->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteReportModalLabel{{ $report->id }}">Konfirmasi Hapus Laporan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus laporan ini?
                                            </div>
                                            <div class="modal-footer">
                                                <form method="POST" action="{{ route('admin.reports.delete', $report->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger text-white">Hapus</button>
                                                </form>
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
        var deleteReportModal = document.getElementById('deleteReportModal');
        deleteReportModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var reportId = button.getAttribute('data-report-id');
            var form = document.getElementById('deleteReportForm');
            form.action = '/reports/' + reportId;
        });

        var banPhotoModal = document.getElementById('banPhotoModal');
        banPhotoModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var photoId = button.getAttribute('data-photo-id');
            var form = document.getElementById('banPhotoForm');
            form.action = '/comments/' + photoId + '/ban';
            var banReason = document.getElementById('ban_reason');
            banReason.value = '';
        });
    });
</script>
@endpush