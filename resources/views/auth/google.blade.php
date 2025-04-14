@extends('layouts.login')

@section('content')
<div class="container d-flex">
    <div class="row w-100">
        <div class="col-md-8">
            <form method="POST" action="{{ route('register.post') }}" class="forms-sample w-100" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="hidden" name="name" class="form-control" id="name" 
                           placeholder="Masukkan nama Anda" value="{{ $googleUser->getName(); }}" required>
                    <input type="hidden" name="email" class="form-control" id="email" 
                           placeholder="Masukkan email Anda" value="{{ $googleUser->getEmail(); }}" required>
                    <label for="username" class="custom-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" 
                           placeholder="Pilih username" value="{{ old('username') }}" required>
                    <small class="text-muted" style="font-size: 12px; display: block; margin-top: 5px;">
                        Username harus 4-20 karakter, hanya boleh mengandung huruf kecil, angka, titik, dan underscore.
                    </small>
                    <div id="username-feedback" class="mt-2" style="font-size: 14px;"></div>
                </div>
                <div class="form-group position-relative">
                    <label for="password" class="custom-label">Kata Sandi</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Buat kata sandi" required>
                    <span toggle="#password" class="fa fa-eye-slash toggle-password" style="margin-top: -14px"></span>
                    <small class="text-muted" style="font-size: 12px; display: block; margin-top: 5px;">
                        Password minimal 8 karakter, harus mengandung huruf dan angka.
                    </small>
                </div>
                <div class="form-group position-relative">
                    <label for="password_confirmation" class="custom-label">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Konfirmasi kata sandi Anda" required>
                    <span toggle="#password_confirmation" class="fa fa-eye-slash toggle-password"></span>
                </div>
                <button type="submit" class="btn btn-success me-2">Daftar</button>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time username validation
    const usernameInput = document.getElementById('username');
    const usernameFeedback = document.getElementById('username-feedback');
    let usernameTimeout = null;
    
    usernameInput.addEventListener('input', function() {
        const username = this.value.trim();
        const isValidChar = /^[a-z0-9._]+$/.test(username);
        
        // Clear previous timeout if exists
        if (usernameTimeout) {
            clearTimeout(usernameTimeout);
        }
        
        // Clear feedback while typing
        usernameFeedback.textContent = '';
        usernameFeedback.className = '';
        this.style.borderColor = '';
        
        // Check length and character validity
        if (username.length > 0) {
            if (username.length < 4 || username.length > 20) {
                this.style.borderColor = '#ff4444';
                usernameFeedback.textContent = 'Username harus 4-20 karakter';
                usernameFeedback.style.color = '#ff4444';
            } else if (!isValidChar) {
                this.style.borderColor = '#ff4444';
                usernameFeedback.textContent = 'Hanya boleh mengandung huruf kecil, angka, titik (.), dan underscore (_)';
                usernameFeedback.style.color = '#ff4444';
            } else {
                // Valid format, check availability after a delay
                usernameTimeout = setTimeout(() => {
                    checkUsernameAvailability(username);
                }, 500); // 0.5 second delay after typing stops
            }
        }
    });
    
    function checkUsernameAvailability(username) {
        // Show loading state
        usernameFeedback.textContent = 'Memeriksa ketersediaan username...';
        usernameFeedback.style.color = '#007bff';
        
        fetch('{{ route("check.usernameRegister") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({username: username})
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                usernameInput.style.borderColor = '#ff4444';
                usernameFeedback.textContent = 'Username sudah digunakan';
                usernameFeedback.style.color = '#ff4444';
            } else {
                usernameInput.style.borderColor = '#32bd40';
                usernameFeedback.textContent = 'Username tersedia';
                usernameFeedback.style.color = '#32bd40';
            }
        })
        .catch(error => {
            console.error('Error checking username:', error);
            usernameFeedback.textContent = 'Gagal memeriksa username. Silakan coba lagi.';
            usernameFeedback.style.color = '#ff4444';
        });
    }

    // Real-time password validation
    const passwordInput = document.getElementById('password');
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const hasLetter = /[a-zA-Z]/.test(password);
        const hasNumber = /\d/.test(password);
        
        if (password.length > 0 && (password.length < 8 || !hasLetter || !hasNumber)) {
            this.style.borderColor = '#ff4444';
        } else {
            this.style.borderColor = '#32bd40';
        }
    });
});
</script>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const errorFields = {!! json_encode($errors->keys()) !!};
        
        Swal.fire({
            icon: 'error',
            title: 'Registrasi Gagal',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#32bd40',
        }).then((result) => {
            if (result.isConfirmed) {
                // Reset only password if it has an error
                const fieldsToReset = errorFields.includes('password') ? ['password'] : [];
                
                fieldsToReset.forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.value = '';
                        input.style.borderColor = '';
                    }
                });
                
                // Focus on the first error field
                if (errorFields.length > 0) {
                    const firstInput = document.querySelector(`[name="${errorFields[0]}"]`);
                    if (firstInput) firstInput.focus();
                }
            }
        });
    });
</script>
@endif
@endpush