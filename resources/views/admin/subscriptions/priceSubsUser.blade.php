@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Harga Langganan User</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Harga 1 Bulan</th>
                <th>Harga 3 Bulan</th>
                <th>Harga 6 Bulan</th>
                <th>Harga 1 Tahun</th>
            </tr>
        </thead>
        <tbody>
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
@endsection