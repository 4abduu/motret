@if($userRole === 'admin')
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
            <a class="navbar-brand brand-logo me-5" href="{{ url('/') }}">
                <img src="{{ asset('images/Motret logo.png') }}" class="me-2" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">
                <img src="{{ asset('images/Motret logo kecil.png') }}" alt="logo" />
            </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item nav-profile dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                        @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/photo_profile/' . Auth::user()->profile_photo) }}" alt="profile" />
                        @else
                            <img src="{{ asset('images/foto profil.jpg') }}" alt="profile" />
                        @endif                    
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}">
                            <i class="ti-power-off text-success"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
@elseif($userRole === 'user' || $userRole === 'pro')
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
            <a class="navbar-brand brand-logo me-5" href="{{ url('/') }}">
                <img src="{{ asset('images/Motret logo.png') }}" class="me-2" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">
                <img src="{{ asset('assets/images/Motret logo.png') }}" alt="logo" />
            </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <ul class="navbar-nav mr-lg-2">
                <li class="nav-item nav-search d-none d-lg-block">
                    <form id="search-form" action="{{ route('search') }}" method="GET" class="input-group">
                        <div class="input-group-prepend hover-cursor" id="navbar-search-icon" onclick="submitSearchForm()">
                            <span class="input-group-text" id="search">
                                <i class="icon-search"></i>
                            </span>
                        </div>
                        <input type="text" name="query" class="form-control" id="navbar-search-input" placeholder="Cari foto sekarang" aria-label="search" aria-describedby="search">
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('photos.create') }}">
                        <i class="ti-upload text-success"></i> Unggah
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('notifications.index') }}">
                        <i class="icon-bell mx-0 fs-5 text-dark"></i> 
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('subscription') }}">
                        <img src="{{ asset('images/crown.png') }}" alt="Crown" style="width: 24px; height: 24px;">
                    </a>
                </li>
                <li class="nav-item nav-profile dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                        @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/photo_profile/' . Auth::user()->profile_photo) }}" alt="profile" />
                        @else
                            <img src="{{ asset('images/foto profil.jpg') }}" alt="profile" />
                        @endif                    
                    </a>
                    <div class="dropdown-menu dropdown-menu-end navbar-dropdown" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="{{ route('user.profile') }}">
                            <i class="ti-user text-success"></i> Profil
                        </a>                        
                        <a class="dropdown-item" href="{{ route('user.settings') }}">
                            <i class="ti-settings text-success"></i> Pengaturan
                        </a>
                        <a class="dropdown-item" href="{{ route('logout') }}">
                            <i class="ti-power-off text-success"></i> Keluar
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
@else
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
            <a class="navbar-brand brand-logo me-5" href="{{ url('/') }}">
                <img src="{{ asset('images/Motret logo.png') }}" class="me-2" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">
                <img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" />
            </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <ul class="navbar-nav mr-lg-2">
                <li class="nav-item nav-search d-none d-lg-block">
                    <form id="search-form" action="{{ route('search') }}" method="GET" class="input-group">
                        <div class="input-group-prepend hover-cursor" id="navbar-search-icon" onclick="submitSearchForm()">
                            <span class="input-group-text" id="search">
                                <i class="icon-search"></i>
                            </span>
                        </div>
                        <input type="text" name="query" class="form-control" id="navbar-search-input" placeholder="Cari foto sekarang" aria-label="search" aria-describedby="search">
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <a class="btn btn-success ml-auto text-white" style="border-radius: 30px; padding: 9px 25px; margin-right: -15px;" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-secondary text-white" style="border-radius: 30px; padding: 9px 20px;" href="{{ route('register') }}">Register</a>
                </li>
            </ul>
        </div>
    </nav>
@endif

@push('scripts')
<script>
    function submitSearchForm() {
        const searchInput = document.getElementById('navbar-search-input');
        if (searchInput.value.trim() !== "") {
            document.getElementById('search-form').submit();
        } else {
            alert('Please enter a search term.');
        }
    }
</script>
@endpush