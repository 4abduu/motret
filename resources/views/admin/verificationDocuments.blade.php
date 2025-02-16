@extends('layouts.app')

@section('title', 'Dokumen Verifikasi')

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
                                    <td><a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn btn-info btn-sm" style="color: white;">Lihat Dokumen</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.verificationRequests') }}" class="btn btn-success mt-3">Kembali</a>
            </div>
        </div>
    </div>
</div>

@endsection