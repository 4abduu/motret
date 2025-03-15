@extends('layouts.login')

@section('content')
<div class="container d-flex">
    <div class="row w-100">
        <div class="col-md-4 d-flex align-items-center justify-content-center">
            <div class="brand-logo">
                <img src="{{ asset('images/Motret logo.png') }}" alt="logo" style="width: 400px; height: auto;">
            </div>
        </div>
        <div class="col-md-8">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('register.post') }}" class="forms-sample w-100" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name" class="custom-label">Nama</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama Anda" required>
                </div>
                <div class="form-group">
                    <label for="username" class="custom-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="Pilih username" required>
                </div>
                <div class="form-group">
                    <label for="email" class="custom-label">Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Masukkan email Anda" required>
                </div>
                <div class="form-group position-relative">
                    <label for="password" class="custom-label">Kata Sandi</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Buat kata sandi" required>
                    <span toggle="#password" class="fa fa-eye-slash toggle-password"></span>
                </div>
                <div class="form-group position-relative">
                    <label for="password_confirmation" class="custom-label">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Konfirmasi kata sandi Anda" required>
                    <span toggle="#password_confirmation" class="fa fa-eye-slash toggle-password"></span>
                </div>
                <div class="form-group">
                    <label for="profile_photo" class="custom-label">Foto Profil</label>
                    <input type="file" name="profile_photo" class="form-control" id="profile_photo" accept="image/*">
                </div>
                <button type="submit" class="btn btn-success me-2">Daftar</button>
            </form>

            <div class="text-center mt-4 font-weight-light">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-success">Login</a>
                atau masuk sebagai
                <a href="{{ route('home') }}" class="text-success">Tamu</a>
            </div>
        </div>
    </div>
</div>
@endsection