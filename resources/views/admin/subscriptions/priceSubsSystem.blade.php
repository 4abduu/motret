@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Harga Langganan Sistem</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Durasi</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prices as $price)
                <tr>
                    <td>{{ $price->duration }}</td>
                    <td>{{ $price->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection