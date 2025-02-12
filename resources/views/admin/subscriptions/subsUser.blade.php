@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Langganan User</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Verified User</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->user->name }}</td>
                    <td>{{ $subscription->verifiedUser->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection