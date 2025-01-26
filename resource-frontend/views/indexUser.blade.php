<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Landing Page - User</title>
    <script type="text/javascript"> (function() { var css = document.createElement('link'); css.href = 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; css.rel = 'stylesheet'; css.type = 'text/css'; document.getElementsByTagName('head')[0].appendChild(css); })(); </script>
    <link rel="stylesheet" href="user/assets/css/app.css">
    <link rel="stylesheet" href="user/assets/css/theme.css">

</head>

<body>

<!-- filepath: /c:/xampp new/htdocs/gallery-foto/resources/views/indexUser.blade.php -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <a class="navbar-brand font-weight-bolder mr-3" href="/indexUser">
        <img src="images/Motret logo.png" style="height: 40px;">
    </a>
    
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
        
    
    <section class="mt-4 mb-5">
    <div class="container-fluid">
    	<div class="row">
    		<div class="card-columns">
    			<div class="card card-pin">
    				<img class="card-img" src="https://images.unsplash.com/photo-1512355144108-e94a235b10af?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=c622d56d975113a08c71c912618b5f83&auto=format&fit=crop&w=500&q=60" alt="Card image">
    				<div class="overlay">
    					<h2 class="card-title title">Cool Title</h2>
    					<div class="more">
    						<a href="post.html">
    						<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> More </a>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    </section>
        
    </main>

    <script src="user/assets/js/app.js"></script>
    <script src="user/assets/js/theme.js"></script>
</body>
</html>
