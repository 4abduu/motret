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
                    <i class="menu-arrow" onclick="toggleDropdown(event, '#report')"></i>
                </a>
                <div class="collapse {{ $isReportActive ? 'show' : '' }}" id="report">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.users') }}"> Report User </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.comments') }}"> Report Comment </a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.photos') }}"> Report Photo </a></li>
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