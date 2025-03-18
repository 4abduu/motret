@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="col-md-12 col-xl-13 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h3 class="card-title">Settings</h3>
            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="username-tab" data-bs-toggle="tab" href="#username" role="tab" aria-controls="username" aria-selected="true">Ubah Username</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Ubah Password</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="email-tab" data-bs-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Ubah Email</a>
                </li>
                @if(!Auth::user()->verified)
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="verification-tab" data-bs-toggle="tab" href="#verification" role="tab" aria-controls="verification" aria-selected="false">Request Verified</a>
                </li>
                @endif
            </ul>
            <div class="tab-content" id="settingsTabsContent">
                <div class="tab-pane fade show active" id="username" role="tabpanel" aria-labelledby="username-tab">
                    <form method="POST" action="{{ route('user.updateUsername') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group mt-3">
                            <label for="username">Username Baru</label>
                            <input type="text" name="username" class="form-control" id="username-input" required oninput="checkUsername()">                            <div id="username-availability" class="mt-2"></div>
                        </div>
                        <button type="submit" class="btn btn-success text-white mt-3">Ubah Username</button>
                    </form>
                </div>
                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                    <form method="POST" action="{{ route('user.updatePassword') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group mt-3">
                            <label for="current_password">Password Lama</label>
                            <input type="password" name="current_password" class="form-control" id="current_password" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="new_password">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" id="new_password" required>
                            @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-success text-white mt-3">Ubah Password</button>
                        <a href="{{ route('password.request') }}" class="btn btn-danger text-white mt-3">Reset Password</a>
                    </form>
                </div>
                <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                    <form method="POST" action="{{ route('user.verifyEmailCode') }}">
                        @csrf
                        <div class="form-group mt-3">
                            <label for="current_email">Email Lama</label>
                            <div class="input-group">
                                <input type="email" name="current_email" class="form-control" id="current_email" required>
                                <button type="button" class="btn btn-success text-white" onclick="sendVerificationCode()">Send Code</button>
                            </div>
                            <div id="email-verification-status" class="mt-2"></div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="verification_code">Kode Verifikasi</label>
                            <input type="text" name="verification_code" class="form-control" id="verification_code" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="new_email">Email Baru</label>
                            <input type="email" name="new_email" class="form-control" id="new_email" required oninput="checkEmail()">
                            <div id="email-availability" class="mt-2"></div>
                        </div>
                        <button type="submit" class="btn btn-success text-white mt-3">Ubah Email</button>
                    </form>
                </div>
                @if(!Auth::user()->verified)
                <div class="tab-pane fade" id="verification" role="tabpanel" aria-labelledby="verification-tab">
                    <form action="{{ route('user.submitVerification') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Nama Lengkap -->
                        <div class="form-group mt-3">
                            <label for="full_name">Nama Lengkap: <span class="text-danger">*</span></label>
                            <input type="text" id="full_name" name="full_name" class="form-control" required>
                        </div>
                        
                        <!-- Username -->
                        <div class="form-group mt-3">
                            <label for="username">Username di Motret: <span class="text-danger">*</span></label>
                            <input type="text" id="verification-username" name="username" class="form-control" required oninput="checkVerificationUsername()">
                            <div id="username-verification-status" class="mt-2"></div>
                        </div>
                        
                        <!-- Upload KTP -->
                        <div class="form-group mt-3">
                            <label for="ktp">Upload KTP: <span class="text-danger">*</span></label>
                            <input type="file" id="ktp" name="ktp" class="form-control" accept="image/*,.pdf" required>
                        </div>
                        
                        <!-- Upload Selfie dengan KTP -->
                        <div class="form-group mt-3">
                            <label for="selfie">Upload Selfie dengan KTP: <span class="text-danger">*</span></label>
                            <input type="file" id="selfie" name="selfie" class="form-control" accept="image/*" required>
                        </div>
                        
                        <!-- Upload Portofolio (Opsional) -->
                        <div class="form-group mt-3">
                            <label for="portfolio">Upload Portofolio (Opsional):</label>
                            <input type="file" id="portfolio" name="portfolio" class="form-control" accept="image/*,.pdf">
                        </div>
                        
                        <!-- Upload Sertifikat (Opsional) -->
                        <div class="form-group mt-3">
                            <label for="certificate">Upload Sertifikat (Opsional):</label>
                            <input type="file" id="certificate" name="certificate" class="form-control" accept="image/*,.pdf">
                        </div>
                        
                        <!-- Alasan Verifikasi -->
                        <div class="form-group mt-3">
                            <label for="reason">Alasan ingin menjadi verified user: <span class="text-danger">*</span></label>
                            <textarea id="reason" name="reason" class="form-control" rows="4" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success text-white mt-3">Kirim Pengajuan</button>
                    </form>
                </div>    
                @endif
            </div>
          </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let usernameTimeout, emailTimeout;
    
    document.getElementById('username-input').addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        usernameTimeout = setTimeout(checkUsername, 500);
    });

    document.getElementById('new_email').addEventListener('input', function() {
        clearTimeout(emailTimeout);
        emailTimeout = setTimeout(checkEmail, 500);
    });

    function checkUsername() {
    clearTimeout(usernameTimeout);
    document.getElementById('username-availability').innerHTML = '';
    usernameTimeout = setTimeout(() => {
        const username = document.getElementById('username-input').value; // Gunakan ID baru
        console.log('Username input:', username); // Debugging: Lihat nilai username

        if (!username) {
            console.log('Username is empty'); // Debugging: Cek jika username kosong
            return;
        }

        fetch('{{ route('user.checkUsername') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ username })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Backend response:', data); // Debugging: Lihat respons dari backend
            const availability = document.getElementById('username-availability');
            if (data.exists) {
                availability.innerHTML = '<span class="text-danger">Username sudah digunakan.</span>';
            } else {
                availability.innerHTML = '<span class="text-success">Username tersedia.</span>';
            }
        })
        .catch(error => {
            console.error('Error:', error); // Debugging: Tangani error
        });
    }, 500);
}

    function checkEmail() {
        clearTimeout(emailTimeout);
        document.getElementById('email-availability').innerHTML = '';
        emailTimeout = setTimeout(() => {
            const email = document.getElementById('new_email').value;
            fetch('{{ route('user.checkEmail') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            })
            .then(response => response.json())
            .then(data => {
                const availability = document.getElementById('email-availability');
                availability.innerHTML = data.exists ? '<span class="text-danger">Email sudah digunakan.</span>' : '<span class="text-success">Email tersedia.</span>';
            });
        }, 500);
    }

    function sendVerificationCode() {
        const email = document.getElementById('current_email').value;
        if (!email || !validateEmail(email)) {
            document.getElementById('email-verification-status').innerHTML = '<span class="text-danger">Masukkan email yang valid.</span>';
            return;
        }
        fetch('{{ route('user.sendEmailVerification') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ old_email: email })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('email-verification-status').innerHTML = data.success ? '<span class="text-success">Kode verifikasi telah dikirim.</span>' : '<span class="text-danger">' + data.message + '</span>';
        });
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function checkVerificationUsername() {
        clearTimeout(usernameTimeout);
        document.getElementById('username-verification-status').innerHTML = '';
        usernameTimeout = setTimeout(() => {
            const username = document.getElementById('verification-username').value;
            fetch('{{ route('user.checkVerificationUsername') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ username })
            })
            .then(response => response.json())
            .then(data => {
                const verificationStatus = document.getElementById('username-verification-status');
                verificationStatus.innerHTML = data.isValid ? '<span class="text-success">Username sesuai.</span>' : '<span class="text-danger">Username tidak sesuai.</span>';
            });
        }, 500);
    }
</script>
@endpush
@endsection