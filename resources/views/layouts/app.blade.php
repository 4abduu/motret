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
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- endinject -->

    <script>
        new DataTable('#example');
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var successAlert = document.querySelector('.alert-success');
            var errorAlert = document.querySelector('.alert-danger');
            var warningAlert = document.querySelector('.alert-warning'); // Tambahkan ini

            if (successAlert) {
                var successCountdown = document.getElementById('success-countdown');
                var successTimeLeft = 5;
                successCountdown.innerText = successTimeLeft;

                var successInterval = setInterval(function () {
                    successTimeLeft--;
                    successCountdown.innerText = successTimeLeft;

                    if (successTimeLeft <= 0) {
                        clearInterval(successInterval);
                        successAlert.remove();
                    }
                }, 1000);
            }

            if (errorAlert) {
                var errorCountdown = document.getElementById('error-countdown');
                var errorTimeLeft = 5;
                errorCountdown.innerText = errorTimeLeft;

                var errorInterval = setInterval(function () {
                    errorTimeLeft--;
                    errorCountdown.innerText = errorTimeLeft;

                    if (errorTimeLeft <= 0) {
                        clearInterval(errorInterval);
                        errorAlert.remove();
                    }
                }, 1000);
            }

            if (warningAlert) { // Logika untuk notifikasi warning
                var warningCountdown = document.getElementById('warning-countdown');
                var warningTimeLeft = 5;
                warningCountdown.innerText = warningTimeLeft;

                var warningInterval = setInterval(function () {
                    warningTimeLeft--;
                    warningCountdown.innerText = warningTimeLeft;

                    if (warningTimeLeft <= 0) {
                        clearInterval(warningInterval);
                        warningAlert.remove();
                    }
                }, 1000);
            }
        });
    </script>
    <script>
        // Cek apakah elemen error alert ada
        var errorAlert = document.getElementById('error-alert');
        if (errorAlert) {
            var errorCountdown = document.getElementById('error-countdown');
            var errorTimeLeft = 5; // 5 detik
            if (errorCountdown) {
                errorCountdown.innerText = errorTimeLeft;
    
                var errorInterval = setInterval(function () {
                    errorTimeLeft--;
                    errorCountdown.innerText = errorTimeLeft;
    
                    if (errorTimeLeft <= 0) {
                        clearInterval(errorInterval);
                        errorAlert.remove(); // Hapus alert setelah hitung mundur selesai
                    }
                }, 1000);
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>