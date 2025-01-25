<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Skydash Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../public/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../public/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../public/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../public/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../public/assets/vendors/mdi/css/materialdesignicons.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../public/assets/images/favicon.png" />
    <style>
      
        .form-control {
            border-radius: 20px;
        }
        .btn-success {
            width: 100%;
            border-radius: 20px;
        }
        .auth-form-light {
            display: flex;
            align-items: center;
        }
        .brand-logo img {
            width: 100px;
            margin-right: 60px;
        }
        .custom-label {
            color: #32bd40; /* Change this to your desired color */
            font-family: 'Arial', sans-serif; /* Change this to your desired font family */
        }
    </style>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-6 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5 d-flex"  style="border-radius: 30px 0 0 30px">
                        <div class="brand-logo">
                            <img src="../public/images/Motret logo.png" alt="logo" style="width: 250px;">
                        </div>
                        <form class="forms-sample w-50">
                            <div class="form-group">
                                <label for="exampleInputUsername1" class="custom-label">Username</label>
                                <input type="text" class="form-control" id="exampleInputUsername1" placeholder="Username">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1" class="custom-label">Password</label>
                                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                            </div>
                            <button type="submit" class="btn btn-success me-2">Login</button>
                            <div class="text-center mt-4 font-weight-light"> Don't have an account? <a type="button" data-bs-toggle="modal" data-bs-target="#registerModal" class="text-success">Create</a></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
    <!-- container-scroller -->
    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="registerModalLabel">Create Account</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <form>
                      <div class="mb-3">
                          <label for="registerUsername" class="form-label">Username</label>
                          <input type="text" class="form-control" id="registerUsername" required>
                      </div>
                      <div class="mb-3">
                          <label for="registerFullName" class="form-label">Full Name</label>
                          <input type="text" class="form-control" id="registerFullName" required>
                      </div>
                      <div class="mb-3">
                          <label for="registerEmail" class="form-label">Email</label>
                          <input type="email" class="form-control" id="registerEmail" required>
                      </div>
                      <div class="mb-3">
                          <label for="registerPassword" class="form-label">Password</label>
                          <input type="password" class="form-control" id="registerPassword" required>
                      </div>
                      <div class="mb-3">
                          <label for="registerConfirmPassword" class="form-label">Confirm Password</label>
                          <input type="password" class="form-control" id="registerConfirmPassword" required>
                      </div>
                      <button type="submit" class="btn btn-success">Register</button>
                  </form>
              </div>
          </div>
      </div>
  </div>

    <!-- plugins:js -->
    <script src="../public/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="../public/assets/js/off-canvas.js"></script>
    <script src="../public/assets/js/template.js"></script>
    <script src="../public/assets/js/settings.js"></script>
    <script src="../public/assets/js/todolist.js"></script>
    <!-- endinject -->
</body>
</html>