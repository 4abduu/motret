@extends('layouts.login')

@section('content')
<div class="container d-flex">
    <div class="row w-100">
        <div class="col-md-8">
            @if (session('status'))
                <div class="alert alert-danger" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login.post') }}" class="forms-sample w-100">
                @csrf
                <div class="form-group">
                    <label for="exampleInputUsername1" class="custom-label">Username or Email</label>
                    <input type="text" name="email" class="form-control" id="exampleInputUsername1" placeholder="Username or Email" required>
                </div>
                <div class="form-group position-relative">
                    <label for="exampleInputPassword1" class="custom-label">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required>
                    <span toggle="#exampleInputPassword1" class="fa fa-eye-slash toggle-password"></span>
                </div>
                <button type="submit" class="btn btn-success me-2">Login</button>
            </form>

            <div class="text-center mt-4 font-weight-light">
                Tidak memiliki akun?
                <a href="{{ route('register') }}" class="text-success">Buat</a>
                atau masuk dengan akses 
                <a href="{{ route('home') }}" class="text-success">Tamu</a>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('auth.google') }}" class="btn btn-outline-dark btn-icon-text">
                    <img src="images/google.png" alt="Google Logo" style="width: 20px; height: 20px; margin-right: 8px;">
                    Masuk dengan Google
                </a>
            </div>
            <div class="text-center mt-4 font-weight-light">
                Lupa kata sandi? Reset kata sandi Anda 
                <a href="{{ route('password.request') }}" class="text-success">di sini</a>
            </div>
        </div>
    </div>
</div>
@endsection