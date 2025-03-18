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

    @stack('link')
    <!-- Tambahan CSS -->
    <style>
        html, body {
            height: 100%;
            margin: 0;
            flex-direction: column;
            overflow-x: hidden;
            overflow: hidden;
        }

        .container-scroller {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .page-body-wrapper {
            flex: 1;
            display: flex;
            flex-direction: row; /* Menyusun sidebar dan konten utama secara horizontal */
            overflow: hidden; /* Memastikan elemen ini tidak ter-scroll */
        }

        .main-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto; /* Hanya konten utama yang bisa di-scroll secara vertikal */
            height: 100vh; /* Pastikan tinggi penuh untuk memungkinkan scroll */
        }

        .content-wrapper {
            flex: 1; /* Memastikan bagian ini fleksibel */
        }

        .navbar{
            z-index: 999;
        }
    </style>
    @stack('styles')
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
                    <!-- Footer -->
                    <footer class="footer bg-light">
                        <span class="text-muted">Copyright © 2025 Motret. All rights reserved.</span>
                    </footer>
                </div>

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