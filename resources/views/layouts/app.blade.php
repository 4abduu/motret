<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta tags dan referensi CSS -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Tambahkan CSRF token di sini -->
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>{{ config('app.name', 'Motret') }}</title>
    <link rel="stylesheet" href="{{ asset('/assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('/images/Motret logo kecil.png') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendors/dropify/dropify.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendors/jquery-file-upload/uploadfile.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendors/jquery-tags-input/jquery.tagsinput.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"></script>
    <script>
        async function getFingerprint() {
            const fp = await FingerprintJS.load();
            const result = await fp.get();
            document.cookie = "guest_id=" + result.visitorId + "; path=/";
        }
        getFingerprint();
    </script>
    @stack('link')
    <!-- Tambahan CSS -->
    <style>
        html, body {
            height: 100%;
            margin: 0;
            flex-direction: column;
            overflow-x: hidden;
            overflow: auto;
        }

        .container-scroller {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .page-body-wrapper {
            overflow: visible;
            flex: 1;
            display: flex;
            flex-direction: row;
        }

        .main-panel {
            overflow: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            flex: 1;
            min-height: calc(100vh - 120px);
        }

        .navbar {
            z-index: 999;
        }

        .footer {
    background-color: #f8f9fa;
    padding: 1rem 0;
    text-align: center;
    margin-top: auto; /* Untuk memastikan footer tetap di bawah */
}

.footer a {
    color: #666;
    text-decoration: none;
    margin: 0 0.5rem;
}

.footer a:hover {
    color: #32bd40;
}

    /* Floating Action Button Mobile Only */
    .fab-upload {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background-color: #32bd40;
    color: white;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 99999; /* INI YANG DIBESARIN! */
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
    pointer-events: auto; /* PASTIKAN INI ADA */
}

.fab-upload:hover {
    background-color: #2aa836;
    transform: scale(1.1);
}

/* Tampilkan hanya di mobile */
@media (max-width: 768px) {
    .fab-upload {
        display: flex;
    }
}
    </style>
</head>
<body>
    @php
    $userRole = auth()->check() ? auth()->user()->role : 'guest';
    @endphp
    <div class="container-scroller">
        @include('partials.navbar')
        @if(Auth::check())
        <div id="user-logged-in" style="display:none;"></div>
        @endif
        <!-- Page Content -->
        <div class="container-fluid page-body-wrapper">
            @include('partials.sidebar')
            <div class="main-panel">
                <div class="content-wrapper">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <span id="success-countdown" class="float-end"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <span id="error-countdown" class="float-end"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session('warning') }}
                            <span id="warning-countdown" class="float-end"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <span id="error-countdown" class="float-end"></span>
                                    <li>{{ $error }}</li>
                                @endforeach
                                <span id="error-countdown" class="float-end"></span>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
                <footer class="footer">
                    <div class="footer-bottom">
                        <span>Copyright © 2025 Motret. All rights reserved.</span>
                        <a href="#">Terms of Service</a>
                        <a href="#">Privacy Policy</a>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    @auth
        @php
            $isAdmin = auth()->user()->role === 'admin';
            $isUploadPage = request()->is('foto/upload*');
        @endphp

        @if(!$isAdmin && !$isUploadPage)
            <button class="fab-upload" id="mobileUploadBtn" title="Upload Foto">
                <i class="bi bi-camera-fill"></i>
            </button>
        @endif
    @endauth


    <!-- Scripts -->
    <script src="{{ asset('/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('/assets/js/template.js') }}"></script>
    <script src="{{ asset('/assets/js/settings.js') }}"></script>
    <script src="{{ asset('/assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('/assets/js/todolist.js') }}"></script>
    <script src="{{ asset('/assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('/assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('/assets/js/data-table.js') }}"></script>
    <script src="{{ asset('/assets/js/dropify.js') }}"></script>
    <script src="{{ asset('/assets/js/dropzone.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery-file-upload.js') }}"></script>
    <script src="{{ asset('/assets/vendors/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('/assets/vendors/jquery-file-upload/jquery.uploadfile.min.js') }}"></script>
    <script src="{{ asset('/assets/vendors/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- endinject -->

    <script>
        new DataTable('#example');
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk memulai countdown alert
    function startCountdown(alertElement, countdownElement) {
        if (!alertElement || !countdownElement) return;

        let timeLeft = 5;
        countdownElement.innerText = timeLeft;

        let interval = setInterval(function () {
            timeLeft--;
            countdownElement.innerText = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(interval);
                alertElement.remove();
            }
        }, 1000);
    }

    // Handle alert success/error/warning
    const successAlert = document.querySelector('.alert-success');
    const errorAlert = document.querySelector('.alert-danger');
    const warningAlert = document.querySelector('.alert-warning');

    startCountdown(successAlert, document.getElementById('success-countdown'));
    startCountdown(errorAlert, document.getElementById('error-countdown'));
    startCountdown(warningAlert, document.getElementById('warning-countdown'));

    // Hapus notifikasi dari sessionStorage setelah ditampilkan
    sessionStorage.removeItem('success');
    sessionStorage.removeItem('error');
    sessionStorage.removeItem('warning');

    // Handle back button navigation
    if (performance.navigation.type === 2) {
        if (successAlert) successAlert.remove();
        if (errorAlert) errorAlert.remove();
        if (warningAlert) warningAlert.remove();
    }

    // ================== LOGIN REMINDER HANDLER ==================
    // Cara lebih reliable untuk cek status login
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    const hideReminder = sessionStorage.getItem("hideLoginReminder");
    const lastReminderTime = sessionStorage.getItem("lastReminderTime");
    const currentTime = new Date().getTime();
    
    // Debugging info
    console.log('Login Status:', isLoggedIn);
    console.log('Hide Reminder Flag:', hideReminder);
    console.log('Last Reminder Time:', lastReminderTime);

    // Atur waktu delay untuk reminder (10 menit = 600000 ms)
    const reminderDelay = 600000;
    
    // Cek apakah perlu menampilkan reminder
    if (!isLoggedIn && !hideReminder) {
        // Jika belum pernah menampilkan reminder atau sudah lewat waktu delay
        if (!lastReminderTime || (currentTime - lastReminderTime) > reminderDelay) {
            setTimeout(() => {
                Swal.fire({
                    title: "Anda Belum Login!",
                    text: "Login sekarang untuk menikmati fitur yang lebih lengkap.",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#32bd40",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Login Sekarang",
                    cancelButtonText: "Nanti Saja",
                    footer: '<label><input type="checkbox" id="dontShowAgain"> Jangan tampilkan lagi</label>',
                    allowOutsideClick: true,
                    allowEscapeKey: true
                }).then((result) => {
                    // Handle checkbox "Jangan tampilkan lagi"
                    const dontShowAgain = document.getElementById("dontShowAgain").checked;
                    
                    if (dontShowAgain) {
                        sessionStorage.setItem("hideLoginReminder", "true");
                    }
                    
                    // Simpan waktu terakhir reminder ditampilkan
                    sessionStorage.setItem("lastReminderTime", currentTime.toString());
                    
                    // Redirect jika memilih login
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
            }, reminderDelay);
        }
    }

    // Reset reminder status jika user login
    if (isLoggedIn) {
        sessionStorage.removeItem("hideLoginReminder");
        sessionStorage.removeItem("lastReminderTime");
    }

    const uploadBtn = document.getElementById('mobileUploadBtn');
    
    if (uploadBtn) {
        uploadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Button clicked!'); // Debugging
            window.location.href = "{{ route('photos.create') }}";
        });
    }
});

// Fungsi tambahan untuk handle session saat tab/window ditutup
window.addEventListener('beforeunload', function() {
    // Hanya reset jika user tidak memilih "Jangan tampilkan lagi"
    if (!sessionStorage.getItem("hideLoginReminder")) {
        sessionStorage.removeItem("lastReminderTime");
    }
});
</script>

    
    @stack('scripts')
</body>
</html>