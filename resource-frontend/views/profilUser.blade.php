<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Post - Pintereso Bootstrap Template</title>
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <link rel="stylesheet" href="user/assets/css/app.css">
    <link rel="stylesheet" href="user/assets/css/theme.css">
    <link rel="stylesheet" href="../../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <!-- endinject -->
    <!-- inject:css -->
    <!-- endinject -->

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
		<a class="navbar-brand font-weight-bolder mr-3" href="/indexGuest">
			<img src="images/Motret logo.png" style="height: 40px;">
		</a>
		<button class="navbar-light navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsDefault" aria-controls="navbarsDefault" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarsDefault">
			<ul class="navbar-nav mx-auto align-items-center" style="width: 40%;">
				<form class="bd-search hidden-sm-down w-100">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text bg-graylight border-0" id="search-icon" style="border-radius: 30px 0 0 30px;">
								<i class="fa fa-search"></i>
							</span>
						</div>
						<input type="text" class="form-control bg-graylight border-0 font" id="search-input" placeholder="Search..." autocomplete="off" style="border-radius: 0 30px 30px 0;">
					</div>
					<div class="dropdown-menu bd-search-results" id="search-results">
					</div>
				</form>
			</ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/profilUser">
                        <img src="/images/foto profil.jpg" alt="Profile" class="rounded-circle" style="height: 40px; width: 40px;">
                    </a>
                </li>
            </ul>
		</div>
	</nav> 
    <main role="main">
    <section class="bg-gray200 pt-5 pb-5">
    <div class="container">
    	<div class="row justify-content-center">
    		<div class="col-md-7">
    			<article class="card" style="border-radius: 30px; padding: 10px 25px;">
                    <div class="card-body text-center" >
                        <img src="images/foto profil.jpg" class="rounded-circle img-fluid" alt="Profile Picture" style="width: 150px;">
                        <h3 class="mt-3">Username</h3>
                        <p class="text-muted">Full Name</p>
                        <p class="text-muted">Email: user@example.com</p>
                        <p class="text-muted">Transaction Ends: 2023-12-31</p>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
                    </div>
    			</article>
    		</div>
    	</div>
    </div>
    
    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="profilePicture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profilePicture">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="Username">
                        </div>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" value="Full Name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="user@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="transactionEnds" class="form-label">Transaction Ends</label>
                            <input type="date" class="form-control" id="transactionEnds" value="2023-12-31">
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </section>
        
    </main>

    <script src="user/assets/js/app.js"></script>
    <script src="user/assets/js/theme.js"></script>
      <!-- Bootstrap JS and dependencies -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <footer class="footer pt-5 pb-5 text-center">
        
    <div class="container">
        
        
            <!--
              All the links in the footer should remain intact.
              You may remove the links only if you donate:
              https://www.wowthemes.net/freebies-license/
            -->
          <p><span class="credits font-weight-bold">        
            <a target="_blank" class="text-dark" >motret </a>
          </span>
          </p>
    
    
        </div>
        
    </footer>    
</body>
    
</html>
