@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Langganan Sistem</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Harga</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->user->name }}</td>
                    <td>{{ $subscription->price }}</td>
                    <td>{{ $subscription->start_date }}</td>
                    <td>{{ $subscription->end_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection