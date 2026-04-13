@extends('layouts.app')

@section('title', 'Dokumen Verifikasi')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-verification-documents.css') }}">
@endpush

@section('content')

<div class="row">
    <h3>Dokumen Verifikasi</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.verificationRequests') }}" class="text-success">Permintaan Verifikasi</a></li>
        <li class="breadcrumb-item active">Dokumen Verifikasi</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Dokumen Verifikasi untuk {{ $verificationRequest->full_name }}</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Jenis Dokumen</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($verificationRequest->documents as $document)
                                <tr>
                                    <td>{{ ucfirst($document->file_type) }}</td>
                                    <td>
                                        <button onclick="openDocumentModal('{{ asset('storage/' . $document->file_path) }}')" 
                                                class="btn btn-info btn-sm" style="color: white;">
                                            Lihat Dokumen
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.verificationRequests') }}" class="btn btn-success text-white mt-3">Kembali</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Zoom Dokumen -->
<div id="photo-modal" class="photo-modal">
    <span class="close-modal">&times;</span>
    <img id="modal-img" class="modal-content">
    <div id="zoom-controls">
        <button id="zoom-in" class="btn btn-light rounded-circle"><i class="bi bi-zoom-in"></i></button>
        <button id="zoom-out" class="btn btn-light rounded-circle"><i class="bi bi-zoom-out"></i></button>
        <button id="reset-zoom" class="btn btn-light rounded-circle"><i class="bi bi-arrow-counterclockwise"></i></button>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
<script src="{{ asset('js/pages/admin-verification-documents.js') }}"></script>
@endpush