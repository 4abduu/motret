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
@endif