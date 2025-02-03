@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Reset Password</h2>
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <div class="form-group">
            <label for="token">Verification Code</label>
            <input type="text" name="token" class="form-control" id="token" required>
            @if ($errors->has('token'))
                <span class="text-danger">{{ $errors->first('token') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" name="password" class="form-control" id="password" required>
            @if ($errors->has('password'))
                <span class="text-danger">{{ $errors->first('password') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>
@endsection