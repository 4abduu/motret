<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta tags dan referensi CSS -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Tambahkan CSRF token di sini -->
    <title>{{ config('app.name', 'Motret') }}</title>
    <link rel="stylesheet" href="{{ asset('../../../assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('../../../assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('../../../assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('../../../assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('../../../assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('../../../assets/css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('../../../assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('../../../images/Motret logo kecil.png') }}" />
    <link rel="stylesheet" href="{{ asset('../../../assets/vendors/dropify/dropify.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('../../../assets/vendors/jquery-file-upload/uploadfile.css') }}" />
    <link rel="stylesheet" href="{{ asset('../../../assets/vendors/jquery-tags-input/jquery.tagsinput.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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
            padding: 2rem 0;
            text-align: center;
        }

        .footer-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            max-width: 1350px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-logo {
            flex: 1;
            min-width: 200px;
            margin: 1rem 0;
            text-align: left;
        }

        .footer-logo img {
            width: 100px; /* Sesuaikan ukuran logo */
            margin-bottom: 10px; /* Jarak antara logo dan teks */
        }

        .footer-logo p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }

        .footer-section {
            flex: 1;
            min-width: 200px;
            margin: 1rem 0;
            text-align: left
        }

        .footer-section h4 {
            margin-bottom: 1rem;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .footer-section a {
            display: block;
            color: #333;
            text-decoration: none;
            margin: 0.5rem 0;
        }

        .footer-section a:hover {
            color: #32bd40;
        }

        .footer-bottom {
            margin-top: 2rem;
            border-top: 1px solid #ddd;
            padding-top: 1rem;
            font-size: 0.9rem;
            color: #666;
        }

        .footer-bottom a {
            color: #666;
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .footer-bottom a:hover {
            color: #32bd40;
        }
        .social-icons a {
    color: #333; /* Warna ikon default */
    text-decoration: none; /* Menghapus underline */
    transition: color 0.3s ease; /* Efek transisi warna */
}

.social-icons a:hover {
    color: #32bd40; /* Warna ikon saat dihover */
}
.social-icons i {
        font-size: 20px; /* Sesuaikan ukuran ikon */
    }
    </style>
</head>
<body>
    @php
    $userRole = auth()->check() ? auth()->user()->role : 'guest';
    @endphp
    <div class="container-scroller">
        @include('partials.navbar')
        
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
                    <div class="footer-container">
                        <!-- Logo dan Teks -->
                        <div class="footer-logo">
                            <img src="{{ asset('images/Motret logo.png') }}" class="me-2" alt="logo" />
                            <!-- Ikon Email, WhatsApp, dan Facebook -->
                            <div class="social-icons mt-2">
                                <a href="#" class="me-3">
                                    <i class="bi bi-envelope"></i> <!-- Ikon Email -->
                                </a>
                                <a href="#" class="me-3">
                                    <i class="bi bi-whatsapp"></i> <!-- Ikon WhatsApp -->
                                </a>
                                <a href="#" class="me-3">
                                    <i class="bi bi-facebook"></i> <!-- Ikon Facebook -->
                                </a>
                                <a href="#">
                                    <i class="bi bi-instagram"></i> <!-- Ikon Facebook -->
                                </a>
                            </div>
                        </div>
                        <!-- Bagian Lainnya -->
                        <div class="footer-section">
                            <h4>Products</h4>
                            <a href="#">Product</a>
                            <a href="#">Policy</a>
                            <a href="#">Log In</a>
                            <a href="#">Request Asset</a>
                            <a href="#">Paramahipu</a>
                        </div>
                        <div class="footer-section">
                            <h4>About us</h4>
                            <a href="#">About India</a>
                            <a href="#">Contact us</a>
                            <a href="#">Fastwrist</a>
                            <a href="#">Consent</a>
                        </div>
                        <div class="footer-section">
                            <h4>Resources</h4>
                            <a href="#">Help center</a>
                            <a href="#">Book & demo</a>
                            <a href="#">Server status</a>
                            <a href="#">Blog</a>
                        </div>
                        <div class="footer-section">
                            <h4>Contact Us</h4>
                            <a href="#" class="me-3 text-decoration-none d-flex align-items-center mb-3">
                                <i class="bi bi-whatsapp" style="font-size: 22px; color: #32bd40;"></i> <!-- Ikon WhatsApp -->
                                <span class="ms-2">+62 123 4567 890</span> <!-- Nomor Telepon -->
                            </a>
                            <a href="#" class="me-3 text-decoration-none d-flex align-items-center">
                                <i class="bi bi-envelope" style="font-size: 22px; color: #32bd40;"></i> <!-- Ikon WhatsApp -->
                                <span class="ms-2">motret.kreatif4@gmail.com</span> <!-- Nomor Telepon -->
                            </a>
                        </div>
                    </div>
                    <div class="footer-bottom">
                        <span>Copyright © 2025 Motret. All rights reserved.</span>
                        <a href="#">Terms of Service</a>
                        <a href="#">Privacy Policy</a>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('../../../assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('../../../assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('../../../assets/js/template.js') }}"></script>
    <script src="{{ asset('../../../assets/js/settings.js') }}"></script>
    <script src="{{ asset('../../../assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('../../../assets/js/todolist.js') }}"></script>
    <script src="{{ asset('../../../assets/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('../../../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('.../../../assets/js/data-table.js') }}"></script>
    <script src="{{ asset('../../../assets/js/dropify.js') }}"></script>
    <script src="{{ asset('../../../assets/js/dropzone.js') }}"></script>
    <script src="{{ asset('../../../assets/js/jquery-file-upload.js') }}"></script>
    <script src="{{ asset('../../../assets/vendors/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('../../../assets/vendors/jquery-file-upload/jquery.uploadfile.min.js') }}"></script>
    <script src="{{ asset('../../../assets/vendors/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
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
        // Function buat handle countdown dan hapus alert
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

        // Cek dan mulai countdown untuk setiap alert
        let successAlert = document.querySelector('.alert-success');
        let errorAlert = document.querySelector('.alert-danger');
        let warningAlert = document.querySelector('.alert-warning');

        startCountdown(successAlert, document.getElementById('success-countdown'));
        startCountdown(errorAlert, document.getElementById('error-countdown'));
        startCountdown(warningAlert, document.getElementById('warning-countdown'));

        // Hapus notifikasi dari sessionStorage setelah ditampilkan
        sessionStorage.removeItem('success');
        sessionStorage.removeItem('error');
        sessionStorage.removeItem('warning');

        // Hapus notifikasi jika user menekan tombol back (fix browser cache issue)
        if (performance.navigation.type === 2) { // 2 = Back button ditekan
            if (successAlert) successAlert.remove();
            if (errorAlert) errorAlert.remove();
            if (warningAlert) warningAlert.remove();
        }
    });
</script>

    
    @stack('scripts')
</body>
</html>