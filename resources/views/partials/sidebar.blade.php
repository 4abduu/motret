@if(auth()->check() && auth()->user()->role === 'admin')
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
                <a class="nav-link" href="{{ route('admin.verificationRequests') }}">
                    <i class="ti-id-badge menu-icon"></i>
                    <span class="menu-title">Manage Verification</span>
                </a>
            </li>
            
            {{-- Dropdown Saldo Pengguna --}}
            @php 
                $isBalanceActive = request()->is('admin/penarikan-saldo*') || 
                                 request()->is('admin/daftar-saldo*') ||
                                 request()->is('admin/riwayat-saldo*');
            @endphp
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#balance" aria-expanded="{{ $isBalanceActive ? 'true' : 'false' }}" aria-controls="balance">
                    <i class="mdi mdi-cash-multiple menu-icon"></i>
                    <span class="menu-title" onclick="window.location='{{ route('admin.saldo') }}'">Balance</span>
                    <i class="menu-arrow" onclick="toggleDropdown(event, '#balance')"></i>
                </a>
                <div class="collapse {{ $isBalanceActive ? 'show' : '' }}" id="balance">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/penarikan-saldo*') ? 'active' : '' }}" 
                               href="{{ route('admin.saldo.penarikan') }}">
                                 Withdrawals
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/daftar-saldo*') ? 'active' : '' }}" 
                               href="{{ route('admin.saldo.daftar') }}">
                                 User Balance
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Dropdown Comment --}}
            @php 
                $isCommentActive = request()->routeIs('admin.comments') || request()->routeIs('admin.replies'); 
            @endphp
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#comment" aria-expanded="{{ $isCommentActive ? 'true' : 'false' }}" aria-controls="comment">
                    <i class="mdi mdi-comment-text-outline menu-icon"></i>
                    <span class="menu-title" onclick="window.location='{{ route('admin.manageComments') }}'">Comment</span>
                    <i class="menu-arrow" onclick="toggleDropdown(event, '#comment')"></i>
                </a>
                <div class="collapse {{ $isCommentActive ? 'show' : '' }}" id="comment">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.comments') }}"> Manage Comment </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.replies') }}"> Manage Replies </a></li>
                    </ul>
                </div>
            </li>

            {{-- Dropdown Report --}}
            @php 
                $isReportActive = request()->routeIs('admin.reports.*'); 
            @endphp
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#report" aria-expanded="{{ $isReportActive ? 'true' : 'false' }}" aria-controls="report">
                    <i class="icon-ban menu-icon"></i>
                    <span class="menu-title" onclick="window.location='{{ route('admin.manageReports') }}'">Report</span>
                    <i class="menu-arrow" id="dropdown-icon-report" onclick="toggleDropdown(event, '#report')"></i>
                </a>
                <div class="collapse {{ $isReportActive ? 'show' : '' }}" id="report">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.users') }}"> Report User </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.comments') }}"> Report Comment </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.photos') }}"> Report Photo </a></li>
                    </ul>
                </div>
            </li>

            {{-- Dropdown Langganan --}}
            @php 
                $isSubscriptionActive = request()->routeIs('admin.subscriptions.*'); 
            @endphp
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#subscription" aria-expanded="{{ $isSubscriptionActive ? 'true' : 'false' }}" aria-controls="subscription">
                    <i class="ti-wallet menu-icon"></i>
                    <span class="menu-title" onclick="window.location='{{ route('admin.subscriptions') }}'">Langganan</span>
                    <i class="menu-arrow" onclick="toggleDropdown(event, '#subscription')"></i>
                </a>
                <div class="collapse {{ $isSubscriptionActive ? 'show' : '' }}" id="subscription">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.subscriptions.transactions') }}"> Transaksi </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.subscriptions.systemPrices') }}"> Harga Sistem </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.subscriptions.userPrices') }}"> Harga User </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.subscriptions.userSubscriptions') }}"> Langganan User </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.subscriptions.systemSubscriptions') }}"> Langganan Sistem </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.subscriptions.comboSubscriptions') }}"> Langganan Kombo </a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
      document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function (toggle) {
        toggle.addEventListener("click", function () {
          let target = document.querySelector(this.getAttribute("href"));
          
          if (target.classList.contains("show")) {
            target.classList.remove("show");  // Menghapus class 'show'
            target.style.display = "none";    // Menyembunyikan dropdown
            this.setAttribute("aria-expanded", "false");
          } else {
            target.classList.add("show");     // Menambahkan class 'show'
            target.style.display = "block";   // Menampilkan dropdown
            this.setAttribute("aria-expanded", "true");
          }
        });
      });
    });
</script>