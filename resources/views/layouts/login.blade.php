<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
    
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
    }
    .brand-logo img {
        width: 100px;
        margin-right: 10px;
    }
    </style>
</head>
<body>
    <div class="container-scroller d-flex flex-column">
        <div class="container-fluid page-body-wrapper full-page-wrapper d-flex align-items-center justify-content-center flex-grow-1 p-0">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-6 mx-auto">
                            @if ($errors->any())
                            <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <span id="error-countdown" class="float-end"></span>
                                        <li>{{ $error }}</li>
                                    @endforeach
                                    <span id="warning-countdown" class="float-end"></span>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif
                        @yield('content')
                        
                    </div>
                </div>
            </div>
            <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025 Motret. All rights reserved.</span>
        </div>
    </footer>
        </div>
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
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
    
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>