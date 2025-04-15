<style>
    @media (max-width: 767.98px) {
        .mobile a {
            display: none !important;
            position: absolute; /* Supaya elemen tidak mengambil space */
            visibility: hidden;
        }
    }
    @media (max-width: 767.98px) {
        .nav-item.notifications,
        .nav-item.subscription {
            display: none !important;
            position: absolute; /* Supaya elemen tidak mengambil space */
            visibility: hidden;
    }
    }
    @media (max-width: 767.98px) {
        .dropdown-menu {
                width: 180px !important; /* Atur lebar dropdown lebih kecil */
                left: auto !important;  /* Biar tidak terlalu mepet ke kiri */
                right: 10px !important; /* Sesuaikan posisi ke kanan */
        }
    }


    @media (min-width: 768px) {
        .dropdown-menu a[href*="/notifications"],
        .dropdown-menu a[href*="/subscription"] {
            display: none !important;
            position: absolute; /* Supaya elemen tidak mengambil space */
            visibility: hidden;
        }
    }

  /* Tambahkan/modifikasi style berikut */
.navbar {
    padding: 0 15px; /* Sesuaikan padding navbar */
}

.navbar-menu-wrapper.justify-content-center {
    flex-grow: 1;
    padding: 0 10px; /* Sesuaikan padding */
}

#search-form * {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}

#navbar-search-input {
    flex-grow: 1;
    min-width: 150px;
    margin-right: 10px;
    background-color: #F0F0F0; /* Warna abu-abu muda */
    border-color: #e0e0e0; /* Warna border abu-abu */
    border-radius: 20px; /* Membuat sudut melengkung */
    padding: 8px 15px; /* Padding yang lebih nyaman */
}

.nav-item.nav-search {
    flex-grow: 1;
    margin-right: 15px; /* Jarak antara search dan icon unggah */
}

.navbar-nav.navbar-nav-right {
    flex-shrink: 0; /* Mencegah penyusutan */
    white-space: nowrap;
}
/* Style untuk search box dengan icon */
.search-input-with-icon {
    width: 96%;
    padding: 10px 15px 10px 35px; /* Padding kiri dikurangi */
    border-radius: 20px;
    background-color: #F0F0F0;
    transition: all 0.3s ease;
    height: 40px;
}

.search-input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #777777; /* Warna abu-abu untuk icon */
    z-index: 4;
}

.search-input-with-icon:focus {
    outline: none;
    border-color: #777777;
    background-color: #F0F0F0;
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.05);
}

/* Efek hover pada icon */
.search-input-icon:hover {
    color: #495057; /* Warna lebih gelap saat hover */
}

/* Untuk tampilan mobile */
@media (max-width: 767.98px) {
    .search-input-with-icon {
        padding: 8px 12px 8px 32px;
        height: 36px;
    }
    
    .search-input-icon {
        left: 10px;
        font-size: 16px;
    }
}
</style>
    
    
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
                                <i class="ti-power-off text-success"></i> Keluar
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    @elseif($userRole === 'user' || $userRole === 'pro')
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-nowrap align-items-center">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                <a class="navbar-brand brand-logo me-5" href="{{ url('/') }}">
                    <img src="{{ asset('images/Motret logo.png') }}" class="me-2" alt="logo" />
                </a>
                <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">
                    <img src="{{ asset('images/Motret logo kecil.png') }}" alt="logo" />
                </a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center flex-grow-1 w-100">
                <ul class="navbar-nav w-100">
                    <li class="nav-item nav-search w-100">
                        <form id="search-form" action="{{ route('search') }}" method="GET" class="position-relative">
                            <i class="icon-search search-input-icon"></i>
                            <input type="text" name="query" class="form-control search-input-with-icon" 
                                   placeholder="Cari foto sekarang" aria-label="search" >
                        </form>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-nav-right ml-auto">
                    <li class="nav-item mobile d-none d-md-inline-block">
                        <a class="nav-link text-dark fw-semibold" href="{{ route('photos.create') }}">
                            <i class="ti-upload text-success me-1"></i> Unggah
                        </a>
                    </li>
                    <li class="nav-item notifications mobile ">
                        <a class="nav-link" href="{{ route('notifications.index') }}">
                            <i class="icon-bell mx-0 fs-5 text-dark"></i> 
                        </a>
                    </li>
                    <li class="nav-item subscription mobile">
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
                                <a class="dropdown-item desktop-hide" href="{{ route('notifications.index') }}">
                                    <i class="icon-bell text-success"></i> Notifikasi
                                </a>
                                <a class="dropdown-item desktop-hide" href="{{ route('subscription') }}">
                                    <i class="ti-crown text-success"></i> Langganan
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
                <img src="{{ asset('images/Motret logo kecil.png') }}" alt="logo" />
            </a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <ul class="navbar-nav w-100">
                <li class="nav-item nav-search w-100">
                    <form id="search-form" action="{{ route('search') }}" method="GET" class="position-relative">
                        <i class="icon-search search-input-icon"></i>
                        <input type="text" name="query" class="form-control search-input-with-icon" 
                               placeholder="Cari foto sekarang" aria-label="search">
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item">
                    <a class="btn btn-success ml-auto text-white" style="border-radius: 30px; padding: 9px 25px; margin-right: -15px;" href="{{ route('login') }}">Masuk</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-secondary text-white" style="border-radius: 30px; padding: 9px 20px;" href="{{ route('register') }}">Daftar</a>
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
    
            }
        }
    </script>
    @endpush