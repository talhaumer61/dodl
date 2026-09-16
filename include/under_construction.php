<?php 
echo'
<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0"/>

    <title>'.(ZONE ? moduleName(ZONE).' | ' : '').''.(CONTROLER ? moduleName(CONTROLER).' | ' : '').'Directorate of Online and Distance Learning (DODL)</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="Directorate of Online and Distance Learning (DODL)" />
    <meta name="description" content="Directorate of Online and Distance Learning (DODL) is an advanced e-learning platform offering online and distance education programs with interactive lectures, assignments, and assessments for learners worldwide." />
    <meta name="keywords" content="DODL, online learning, distance learning, e-learning, virtual education, LMS, online courses, university portal, study online, remote education, student portal" />
    <meta name="author" content="Directorate of Online and Distance Learning (DODL)" />
    <meta name="robots" content="index, follow" />
    <meta name="language" content="English" />
    <meta name="revisit-after" content="7 days" />
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="'.SITE_URL.'" />
    <meta property="twitter:title" content="Directorate of Online and Distance Learning (DODL)" />
    <meta property="twitter:description" content="Explore the future of education with DODL — your hub for online and distance learning programs." />
    <meta property="twitter:image" content="'.SITE_URL.'assets/img/dodl-preview.jpg" />

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="'.SITE_URL.'assets/img/favicon.ico"/>

	<link rel="apple-touch-icon" href="'.SITE_URL.'assets/img/apple-icon.png">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="'.SITE_URL.'assets/css/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="'.SITE_URL.'assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="'.SITE_URL.'assets/plugins/fontawesome/css/all.min.css">

	<!-- Iconsax CSS -->
	<link rel="stylesheet" href="'.SITE_URL.'assets/css/iconsax.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="'.SITE_URL.'assets/css/style.css">
</head>

<body class="error-page">

	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<div class="error-box">
			<img src="'.SITE_URL.'assets/img/error/img-01.svg" alt="img" class="img-fluid bg-01">
			<img src="'.SITE_URL.'assets/img/error/img-02.svg" alt="img" class="img-fluid bg-02">
			<img src="'.SITE_URL.'assets/img/error/img-03.svg" alt="img" class="img-fluid bg-03">
			<img src="'.SITE_URL.'assets/img/error/img-04.svg" alt="img" class="img-fluid bg-04">
			<img src="'.SITE_URL.'assets/img/error/img-05.svg" alt="img" class="img-fluid bg-05">
			<img src="'.SITE_URL.'assets/img/error/img-06.svg" alt="img" class="img-fluid bg-06">
			<div class="error-logo">
				<a href="'.SITE_URL.'">
					<img src="'.SITE_URL.'assets/img/logo/logo.png" class="img-fluid" alt="Logo">
				</a>
			</div>
			<div class="error-box-img">
				<img src="'.SITE_URL.'assets/img/error/error-03.svg" alt="Img" class="img-fluid">
			</div>
			<h3 class="mb-3">The Learn Panel is <span class="text-primary ms-1">Under Maintenance</span></h3>
			<p class="h4 font-weight-normal">We are working on some upgrades. We will be back soon</p>
			<a href="'.SITE_URL.'" class="btn btn-primary"><i class="fa fa-arrow-left me-1"></i> Back to Home</a>
		</div>

	</div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
    <script src="'.SITE_URL.'assets/js/jquery-3.6.0.min.js"></script>

	<!-- Bootstrap Core JS -->
    <script src="'.SITE_URL.'assets/js/bootstrap.bundle.min.js"></script>

	<!-- Custom JS -->
    <script src="'.SITE_URL.'assets/js/script.js"></script>
</body>

<script>
document.addEventListener(\'keydown\', function(event) {
	// ctrl+shift+c
	if (event.ctrlKey && event.shiftKey && event.keyCode === 67) {
		event.preventDefault();
	}
	// ctrl+shift+i
	if (event.ctrlKey && event.shiftKey && event.keyCode === 73) {
		event.preventDefault();
	}
	// ctrl+u
	if (event.ctrlKey && event.keyCode === 85) {
		event.preventDefault();
	}
	// ctrl+s
	if (event.ctrlKey && event.keyCode === 83) {
		event.preventDefault();
	}
	// ctrl+p 
	if (event.ctrlKey && event.keyCode === 80) {
		event.preventDefault();
	}
	// ctrl+e
	if (event.ctrlKey && event.keyCode === 69) {
		event.preventDefault();
	}
});
// right click disable
document.addEventListener(\'contextmenu\', function(event) {
  event.preventDefault();
});
</script>

</html>';