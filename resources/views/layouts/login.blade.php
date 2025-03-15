<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/Motret logo kecil.png') }}" />
    <style>
        /* Reset CSS */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Hilangkan scrollbar jika tidak diperlukan */
        }
    
        /* Container Utama */
        .container-scroller {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
    
        /* Wrapper untuk Konten */
        .page-body-wrapper {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            margin: 0;
            padding: 0;
        }
    
        /* Full Page Wrapper */
        .full-page-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0; /* Hapus padding jika tidak diperlukan */
            margin: 0; /* Hapus margin jika tidak diperlukan */
        }
    
        /* Form Auth */
        .auth-form-light {
            width: 100%;
            max-width: 600px;
            margin: 0 auto; /* Hanya margin atas dan bawah */
            padding: 30px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    
        /* Input Field */
        .form-control {
            border-radius: 20px;
            padding: 12px 15px; /* Padding atas-bawah dan kiri-kanan */
            font-size: 14px;
            width: 100%;
            height: 54px; /* Atur ketinggian input */
            box-sizing: border-box; /* Pastikan padding tidak menambah ukuran elemen */
        }
    
        /* Tombol Success */
        .btn-success {
            width: 100%;
            border-radius: 20px;
            padding: 12px 15px;
            font-size: 16px;
            color: #fff;
            background-color: #32bd40;
            border: 2px solid transparent; /* Tambahkan border transparan */
            box-sizing: border-box; /* Pastikan border termasuk dalam ukuran elemen */
            transition: all 0.3s ease; /* Animasi untuk semua perubahan */
        }
    
        .btn-success:hover {
            background-color: transparent !important;
            border: 2px solid #32bd40 !important; /* Border hijau saat hover */
            color: #32bd40 !important;
        }
    
        /* Label Kustom */
        .custom-label {
            color: #32bd40;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin-bottom: 8px;
        }
    
        /* Footer */
        .footer {
            width: 100%;
            text-align: center;
            padding: 20px 0; /* Kurangi padding footer */
            background-color: #f8f9fa; /* Tambahkan background jika diperlukan */
            margin-top: auto; /* Pastikan footer tetap di bawah */
        }
    
        /* Logo Brand */
        .brand-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            margin-right: 0; /* Hapus margin kanan yang tidak diperlukan */
        }
    
        .brand-logo img {
            width: 100%;
            max-width: 400px;
            height: auto;
            margin-right: 20vh; /* Atur margin kanan */ 
        }
    
        /* Toggle Password */
        .toggle-password {
            color: #32bd40;
            cursor: pointer;
            font-size: 20px;
            position: absolute;
            right: 15px; /* Atur jarak dari kanan */
            top: 68%;
            transform: translateY(-50%);
        }
    
        /* Form Group */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
    
        /* Text Center */
        .text-center {
            text-align: center;
        }
    
        /* Text Success */
        .text-success {
            color: #32bd40;
        }
    
        /* Responsif untuk Layar Kecil */
        @media (max-width: 768px) {
            .brand-logo img {
                max-width: 200px; /* Perkecil logo di layar kecil */
            }
            .full-page-wrapper {
                padding: 0; /* Hapus padding di layar kecil */
            }
            .brand-logo {
                margin-top: 10px; /* Kurangi margin atas di layar kecil */
            }
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
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
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var errorAlert = document.getElementById('error-alert');
            if (errorAlert) {
                var errorCountdown = document.getElementById('error-countdown');
                var errorTimeLeft = 5;
                if (errorCountdown) {
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
            }

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
        });
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