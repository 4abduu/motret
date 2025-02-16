@extends('layouts.app')

@section('content')

<div class="row">
    <h3>Transaksi</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Transaksi</li>
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
            </div>
        </div>
    </div>
</div>


@endsection
@push('scripts')
<script>
    new DataTable('#example');
</script>
@endpush