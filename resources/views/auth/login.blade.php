@extends('layouts.login')

@section('content')
<div class="container d-flex">
    <div class="row w-100">
        <div class="col-md-4 d-flex align-items-center justify-content-center">
            <div class="brand-logo" style=" margin-left: -150px;">
                <img src="{{ asset('images/Motret logo.png') }}" alt="logo"  style="width: 400px; height: auto;">
            </div>
        </div>
        <div class="col-md-8">
            <form method="POST" action="{{ route('login.post') }}" class="forms-sample w-100">
                @csrf
                <div class="form-group">
                    <label for="exampleInputUsername1" class="custom-label">Username or Email</label>
                    <input type="text" name="email" class="form-control" id="exampleInputUsername1" placeholder="Username or Email" required>
                </div>
                <div class="form-group position-relative">
                    <label for="exampleInputPassword1" class="custom-label">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required>
                    <span toggle="#exampleInputPassword1" class="fa fa-eye-slash toggle-password" 
                        style="color: #32bd40; position: absolute; right: 4%; margin-top: -6%; transform: translateY(-50%); font-size: 22px; cursor: pointer;"></span>
                </div>
                <button type="submit" class="btn btn-success me-2">Login</button>
            </form>

            <div class="text-center mt-4 font-weight-light">
                Don't have an account?
                <a type="button" data-bs-toggle="modal" data-bs-target="#registerModal" class="text-success">Create</a>
                or login with
                <a href="{{ route('home') }}" class="text-success">Guest</a>
                access?
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('auth.google') }}" class="btn btn-outline-dark btn-icon-text">
                    <img src="images/google.png" alt="Google Logo" style="width: 20px; height: 20px; margin-right: 8px;">
                    Login with Google
                </a>
            </div>
            <div class="text-center mt-4 font-weight-light">
                Forgot password? Reset your password 
                <a href="{{ route('password.request') }}" class="text-success">here</a>
            </div>
        </div>
    </div>
</div>

<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Create Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Profile Photo</label>
                        <input type="file" name="profile_photo" class="form-control" id="profile_photo">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                        <span toggle="#password" class="fa fa-eye-slash toggle-password" 
                        style="color: #32bd40; position: absolute; right: 4%; margin-top: -6%; transform: translateY(-50%); font-size: 22px; cursor: pointer;"></span>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                        <span toggle="#password_confirmation" class="fa fa-eye-slash toggle-password" 
                        style="color: #32bd40; position: absolute; right: 4%; margin-top: -6%; transform: translateY(-50%); font-size: 22px; cursor: pointer;"></span>
                    </div>
                    <button type="submit" class="btn btn-success">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Menampilkan modal jika URL memiliki parameter 'register=true'
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('register') && urlParams.get('register') === 'true') {
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
            registerModal.show();
        }

        // Reset form saat modal register ditutup
        const registerModalElement = document.getElementById('registerModal');
        registerModalElement.addEventListener('hidden.bs.modal', function () {
            const form = registerModalElement.querySelector('form');
            if (form) {
                form.reset(); // Mengosongkan semua input dalam form
            }
        });

        // Toggle password visibility
        const togglePassword = document.querySelectorAll('.toggle-password');
        togglePassword.forEach(toggle => {
            toggle.addEventListener('click', function () {
                const target = document.querySelector(this.getAttribute('toggle'));
                if (target.type === "password") {
                    target.type = "text";
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                } else {
                    target.type = "password";
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                }
            });
        });

        if (document.querySelector('.alert-danger')) {
            const newUrl = window.location.href.split('?')[0];
            window.history.replaceState({}, document.title, newUrl);
        }
    });
</script>
@endpush