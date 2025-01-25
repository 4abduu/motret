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
    @php
    $userRole = auth()->check() ? auth()->user()->role : 'guest';
    @endphp
    @if($userRole !== 'admin')
    <style>
        body {
            padding-top: 70px;
        }
        </style>
    @endif
    @stack('styles')
</head>
<body>
    <div class="container-scroller">


    <!-- Navbar -->
    @if($userRole === 'admin')
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <a class="navbar-brand brand-logo me-5" href="{{ url('/') }}"><img src="{{ asset('images/Motret logo.png') }}" class="me-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}"><img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                            <img src="{{ asset('images/foto profil.jpg') }}" alt="profile" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="ti-power-off text-primary"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    @elseif($userRole === 'user' && $userRole === 'pro')
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <a class="navbar-brand brand-logo me-5" href="{{ url('/') }}"><img src="{{ asset('images/Motret logo.png') }}" class="me-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}"><img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('photos.create') }}">
                            <i class="ti-upload text-primary"></i> Upload
                        </a>
                    </li>
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                            <img src="{{ asset('images/foto profil.jpg') }}" alt="profile" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end navbar-dropdown" aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ route('user.profile') }}">
                                <i class="ti-user text-primary"></i> Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="ti-power-off text-primary"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    @else
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <a class="navbar-brand brand-logo me-5" href="{{ url('/') }}"><img src="{{ asset('images/Motret logo.png') }}" class="me-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}"><img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}?register=true">Register</a>
                    </li>
                </ul>
            </div>
        </nav>
    @endif
        @if(auth()->check() && auth()->user()->role === 'admin')
    <div class="container-fluid page-body-wrapper">
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="icon-grid menu-icon"></i>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users') }}">
                                <i class="mdi mdi-account-multiple menu-icon"></i>
                                <span class="menu-title">Manage User</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.photos') }}">
                                <i class="icon-image menu-icon"></i>
                                <span class="menu-title">Manage Foto</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.comments') }}">
                                <i class="mdi mdi-comment-text-outline menu-icon"></i>
                                <span class="menu-title">Manage Comment</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.subscriptions') }}">
                                <i class="mdi mdi-crown menu-icon"></i>
                                <span class="menu-title">Manage Berlangganan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.reports') }}">
                                <i class="icon-ban menu-icon"></i>
                                <span class="menu-title">Manage Report</span>
                            </a>
                        </li>
                    </ul>
                </nav>
        <!-- Page Content Wrapper -->
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
       @endif
 
                @yield('content')
            </div>
        </div>
    </div>
            <!-- content-wrapper ends -->
            <!-- partial:../../partials/_footer.html -->
            <footer class="footer">
                <div class="d-sm-flex justify-content-center justify-content-sm-between">
                    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025 Motret. All rights reserved.</span>
                </div>
            </footer>
            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        new DataTable('#example');
    </script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <!-- End custom js for this page-->

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
    @stack('scripts')
</body>
</html>