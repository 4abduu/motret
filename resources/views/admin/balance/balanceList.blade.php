@extends('layouts.app')

@section('title', 'Daftar Saldo Pengguna')

@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-balance-list.css') }}">
@endpush

@section('content')
<div class="row">
    <h3>Daftar Saldo Pengguna</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Saldo Pengguna</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Saldo Pengguna</h4>
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pengguna</th>
                                <th>Email</th>
                                <th>Saldo</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>Rp {{ number_format($user->balance, 0, ',', '.') }}</td>
                                <td>
                                    <button 
                                        type="button"
                                        class="btn btn-info btn-icon action-btn"
                                        title="Riwayat Saldo"
                                        onclick="window.location.href='{{ route('admin.saldo.detail', $user->id) }}'">
                                        <i class="ti-info" style="color: white;"></i>
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