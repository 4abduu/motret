@extends('layouts.app')
@push('link')
<link rel="stylesheet" href="{{ asset('css/pages/admin-datatable-page.css') }}">
@endpush

@section('content')

<div class="row">
    <h3>Langganan User</h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success">Dashboard</a></li>
        <li class="breadcrumb-item active">Langganan User</li>
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
                                <th>No</th>
                                <th>User</th>
                                <th>Verified User</th>
                                <th>Harga</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $index => $subscription)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $subscription->user->name }}</td>
                                <td>{{ $subscription->targetUser->name }}</td>
                                <td>{{ $subscription->price }}</td>
                                <td>{{ $subscription->start_date }}</td>
                                <td>{{ $subscription->end_date }}</td>
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