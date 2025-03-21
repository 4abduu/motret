@extends('layouts.app')

@section('content')

<div class="row">
    <h3>Harga Langganan User</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Harga Langganan User</li>
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
                                <th>User</th>
                                <th>Harga 1 Bulan</th>
                                <th>Harga 3 Bulan</th>
                                <th>Harga 6 Bulan</th>
                                <th>Harga 1 Tahun</th>
                            </tr>
                        </thead>
                        @foreach($prices as $price)
                        <tr>
                            <td>{{ $price->user->name }}</td>
                            <td>{{ $price->price_1_month }}</td>
                            <td>{{ $price->price_3_months }}</td>
                            <td>{{ $price->price_6_months }}</td>
                            <td>{{ $price->price_1_year }}</td>
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