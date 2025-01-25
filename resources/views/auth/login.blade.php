@extends('layouts.login')

@push('styles')
<style>
html, body {
    height: 100%;  /* Mengatur tinggi halaman agar mengisi seluruh layar */
    margin: 0;     /* Menghilangkan margin default */
    padding: 0;    /* Menghilangkan padding default */
}

.container-scroller {
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* Memastikan container utama mengisi seluruh tinggi layar */
}

.page-body-wrapper {
    display: flex;
    flex-direction: column;
    flex-grow: 1;  /* Membuat halaman konten utama mengisi sisa ruang */
}

.full-page-wrapper {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

.auth-form-light {
    width: 100%;
    max-width: 600px;
    margin: auto;
    padding: 30px;
    border-radius: 10px;
}

.form-control {
    border-radius: 20px;
}

.btn-success {
    width: 100%;
    border-radius: 20px;
}

.custom-label {
    color: #32bd40; /* Ubah warna sesuai keinginan */
    font-family: 'Arial', sans-serif; /* Ubah font sesuai keinginan */
}

.footer {
    width: 100%;
 /* Menambahkan garis border atas untuk memperjelas footer */
}
.brand-logo img {
    width: 100px;
    margin-right: 10px;
}
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="brand-logo">
        <img src="{{ asset('images/Motret logo.png') }}" alt="logo">
    </div>
    <form method="POST" action="{{ route('login.post') }}" class="forms-sample w-100">
        @csrf
        <div class="form-group">
            <label for="exampleInputUsername1" class="custom-label">Username or Email</label>
            <input type="text" name="email" class="form-control" id="exampleInputUsername1" placeholder="Username or Email" required>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1" class="custom-label">Password</label>
            <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required>
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
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
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
    });
</script>
@endpush