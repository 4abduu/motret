<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Skydash Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../../assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:../../partials/_navbar.html -->
      <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <a class="navbar-brand brand-logo me-5" href="/index"><img src="images/Motret logo.png" class="me-2" alt="logo" /></a>
          <a class="navbar-brand brand-logo-mini" href="index.html"><img src="assets/images/logo-mini.svg" alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
            <ul class="navbar-nav mr-lg-2">
              <li class="nav-item nav-search d-none d-lg-block">
                <div class="input-group">
                  <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                    <span class="input-group-text" id="search">
                      <i class="icon-search"></i>
                    </span>
                  </div>
                  <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
                </div>
              </li>
            </ul>
            <ul class="navbar-nav navbar-nav-right">
              <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                  <img src="/images/foto profil.jpg" alt="profile" />
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                  <a class="dropdown-item">
                    <i class="ti-power-off text-primary"></i> Logout </a>
                </div>
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
              <span class="icon-menu"></span>
            </button>
          </div>
</nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
  <li class="nav-item">
      <a class="nav-link" href="../../index">
        <i class="icon-grid menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../../crudUser">
        <i class="mdi mdi-account-multiple menu-icon"></i>
        <span class="menu-title">Manage User</span>
      </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="../../crudFoto">
          <i class="icon-image menu-icon"></i>
          <span class="menu-title">Manage Foto</span>
        </a>
      </li>
    <li class="nav-item">
        <a class="nav-link" href="../../crudComment">
          <i class="mdi mdi-comment-text-outline menu-icon"></i>
          <span class="menu-title">Manage Comment</span>
        </a>
      </li>
    <li class="nav-item">
        <a class="nav-link" href="../../crudBerlangganan">
          <i class="mdi mdi-crown menu-icon"></i>
          <span class="menu-title">Manage Berlangganan</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../../crudReport">
          <i class="icon-ban menu-icon"></i>
          <span class="menu-title">Manage Report</span>
        </a>
      </li>
  </ul>
</nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <h2>Data Report</h2>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="/index">Dashboard</a></li>
                  <li class="breadcrumb-item active">Manage Laporan Report</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="example" class="table table-striped" style="width:100%">
                          <thead>
                            <tr>
                              <th> no </th>
                              <th> foto </th>
                              <th> Pengunggah </th>
                              <th> Pelapor </th>
                              <th> Alasan </th>
                              <th> Aksi </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                                <td> 1 </td>
                                <td>
                                    <img src="images/foto profil.jpg" alt="image" class="img-square" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0;" />
                                  </td>
                                  <td> herman08 </td>
                                  <td> budii23 </td>
                                  <td> Konten tidak pantas </td>
                                  <td>
                                    <!-- Button "email" yang memicu modal -->
                                    <button type="button" class="btn btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="#exampleModalToggle">
                                      <i class="icon-ban"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#exampleModalToggle2">
                                      <i class="mdi mdi-window-close"></i>
                                    </button>
                                  </td>
                            </tr>
                          </tbody>
                        </table>
                        <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalToggleLabel">Hapus Foto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                Foto akan di hapus permanen.
                              </div>
                              <div class="modal-footer">
                                <button class="btn btn-danger" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">Hapus</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
            
            <!-- content-wrapper ends -->
            <!-- partial:../../partials/_footer.html -->
            <footer class="footer">
              <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2023.</a> All rights reserved.</span>
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
      <script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
      <!-- endinject -->
      <!-- Plugin js for this page -->
      <!-- End plugin js for this page -->
      <!-- inject:js -->
      <script src="../../assets/js/off-canvas.js"></script>
      <script src="../../assets/js/template.js"></script>
      <script src="../../assets/js/settings.js"></script>
      <script src="../../assets/js/todolist.js"></script>
      <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
      <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"></script>
      <script>
        new DataTable('#example');
      </script>
      <!-- endinject -->
      <!-- Custom js for this page-->
      <!-- End custom js for this page-->
    </body>
  </html>