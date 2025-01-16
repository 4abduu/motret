<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Motret')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Motret</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users') }}">Manage Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.photos') }}">Manage Photos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.reports') }}">Laporan</a>
                        </li>
                    @elseif(Auth::check() && Auth::user()->role === 'user')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.profile') }}">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('photos.create') }}">Upload Photo</a>
                        </li>
                    @endif
                </ul>
            </div>
            @if(Auth::check())
                <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
            @endif
        </div>
    </nav>

    <div class="container mt-4">
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
        @yield('content')
    </div>
    <script>
         document.addEventListener('DOMContentLoaded', function () {
        var successAlert = document.querySelector('.alert-success');
        var errorAlert = document.querySelector('.alert-danger');

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
    });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    @stack('scripts')
</body>
</html>