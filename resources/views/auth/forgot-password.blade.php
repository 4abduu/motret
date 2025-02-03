@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Forgot Password</h2>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email_or_username">Email or Username</label>
            <input type="text" name="email_or_username" class="form-control" id="email_or_username" required>
            @if ($errors->has('email_or_username'))
                <span class="text-danger">{{ $errors->first('email_or_username') }}</span>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
</div>
@endsection