@extends('layouts.app')

@section('content')


<div class="row">
    <h3>Harga Langganan Sistem</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Harga Langganan Sistem</li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Durasi</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prices as $price)
                                <tr>
                                    <td>
                                        @php
                                            $durations = [
                                                '1_month' => '1 Bulan',
                                                '3_months' => '3 Bulan',
                                                '6_months' => '6 Bulan',
                                                '1_year' => '1 Tahun'
                                            ];
                                        @endphp
                                        {{ $durations[$price->duration] ?? $price->duration }}
                                    </td>
                                    <td>{{ $price->price }}</td>
                                    <td><button class="btn btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $price->id }}"><i class="ti-pencil-alt" style="color: white;"></i></button></td>
                                </tr>
                                <!-- Modal Edit User -->
                                <div class="modal fade" id="editUserModal{{ $price->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $price->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUserModalLabel{{ $price->id }}">Edit Harga</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                            <form method="POST" action="{{ route('admin.subscriptions.updatePriceSystem', $price->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="duration" class="form-label">Durasi</label>
                                                    <select name="duration" class="form-control" id="duration" disabled>
                                                        <option value="1_month" {{ $price->duration == '1_month' ? 'selected' : '' }}>1 Bulan</option>
                                                        <option value="3_months" {{ $price->duration == '3_months' ? 'selected' : '' }}>3 Bulan</option>
                                                        <option value="6_months" {{ $price->duration == '6_months' ? 'selected' : '' }}>6 Bulan</option>
                                                        <option value="1_year" {{ $price->duration == '1_year' ? 'selected' : '' }}>1 Tahun</option>
                                                    </select>
                                                    <input type="hidden" name="duration" value="{{ $price->duration }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Harga</label>
                                                    <input type="number" name="price" class="form-control" id="price" value="{{ $price->price }}" required>
                                                </div>
                                                <button type="submit" class="btn btn-success text-white">Update</button>
                                                <a href="{{ route('admin.subscriptions.systemPrices') }}" class="btn btn-secondary text-white">Batal</a>
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

