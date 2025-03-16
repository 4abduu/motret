@extends('layouts.login')

@section('content')
<div class="container d-flex justify-content-start align-items-center">
    <div class="row w-100 ">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Forgot Password</h2>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}" class="forms-sample w-100">
                @csrf
                <div class="form-group">
                    <label for="email_or_username" class="custom-label">Email or Username</label>
                    <input type="text" name="email_or_username" class="form-control" id="email_or_username" required>
                    @if ($errors->has('email_or_username'))
                        <span class="text-danger">{{ $errors->first('email_or_username') }}</span>
                    @endif
                </div>
                <button type="submit" class="btn btn-success w-100 mt-4">Send Reset Link</button>
            </form>
        </div>
    </div>
</div>
@endsection