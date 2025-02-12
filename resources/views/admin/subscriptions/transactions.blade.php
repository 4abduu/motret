@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Transaksi</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Order ID</th>
                <th>Status</th>
                <th>Payment Type</th>
                <th>Gross Amount</th>
                <th>Transaction ID</th>
                <th>Fraud Status</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>{{ $transaction->order_id }}</td>
                    <td>{{ $transaction->transaction_status }}</td>
                    <td>{{ $transaction->payment_type }}</td>
                    <td>{{ $transaction->gross_amount }}</td>
                    <td>{{ $transaction->transaction_id }}</td>
                    <td>{{ $transaction->fraud_status }}</td>
                    <td>{{ $transaction->type }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection