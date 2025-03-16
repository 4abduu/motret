@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="card-title mb-3">Reset Password</h3>
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <form class="forms-sample" method="POST" action="{{ route('password.update') }}">
                @csrf
                <div class="form-group">
                    <label for="token">Verification Code</label>
                    <input type="text" name="token" class="form-control" id="token" placeholder="Verification Code" required>
                    @if ($errors->has('token'))
                        <span class="text-danger">{{ $errors->first('token') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="New Password" required>
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation"  placeholder="Confirm New Password" required>
                </div>
                <button type="submit" class="btn btn-success text-white me-2">Reset Password</button>
            </form>
          </div>
        </div>
      </div>
</div>
@endsection