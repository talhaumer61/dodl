<?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <title>'.moduleName($zone).' | '.TITLE_HEADER.'</title>    
    <!-- App favicon -->
    <link rel="shortcut icon" href="'.SITE_URL_PORTAL.'assets/images/favicon.ico">

    <!-- Primary Meta Tags -->
    <meta name="title" content="Directorate of Online and Distance Learning (DODL)" />
    <meta name="description" content="Directorate of Online and Distance Learning (DODL) is an advanced e-learning platform offering online and distance education programs with interactive lectures, assignments, and assessments for learners worldwide." />
    <meta name="keywords" content="DODL, online learning, distance learning, e-learning, virtual education, LMS, online courses, university portal, study online, remote education, student portal" />
    <meta name="author" content="Directorate of Online and Distance Learning (DODL)" />
    <meta name="robots" content="index, follow" />
    <meta name="language" content="English" />
    <meta name="revisit-after" content="7 days" />

    <!-- THEME CSS -->
    <link href="'.SITE_URL.'assets/img/favicon.ico" rel="icon" type="image/png">
    <link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/icons.css">
    <link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/uikit.css">
    <link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/style.css">
    <link href="'.SITE_URL.'assets/learn/css/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/fontawesome/css/fontawesome.min.css"/>
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/bootstrap.min.css" />

    <!-- LEARN PANEL CUSTOM-PLAYER -->
    <link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/owl.carousel.min.css">
	<link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/select2.min.css">
	<link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/plyr.css">
    <script src="'.SITE_URL.'assets/learn/js/YouTubeToHtml5.js"></script>
    
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script src="https://player.vimeo.com/api/player.js"></script>
    
</head>
<body class="bg-white" draggable="true">
    <div id="wrapper" class="course-watch">';
        require_once 'include/sessionMsg.php';
?>