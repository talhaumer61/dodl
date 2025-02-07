<?php
echo'
<!DOCTYPE html>
<html lang="en">
<head>
    <title>'.moduleName($zone).' | '.TITLE_HEADER.'</title>    
    <!-- App favicon -->
    <link rel="shortcut icon" href="'.SITE_URL_PORTAL.'assets/images/favicon.ico">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Courseplus is - Professional A unique and beautiful collection of UI elements">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- THEME CSS -->
    <link href="'.SITE_URL.'assets/img/favicon.ico" rel="icon" type="image/png">
    <link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/icons.css">
    <link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/uikit.css">
    <link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/style.css">
    <link href="'.SITE_URL.'assets/learn/css/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/toast.css">
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/fontawesome/css/fontawesome.min.css"/>
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/bootstrap.min.css" />

    <!-- LEARN PANEL CUSTOM-PLAYER -->
    <link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/owl.carousel.min.css">
	<link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/select2.min.css">
	<link rel="stylesheet" href="'.SITE_URL.'assets/learn/css/plyr.css">
    <script src="'.SITE_URL.'assets/learn/js/YouTubeToHtml5.js"></script>
    
    <!-- JQUERY -->
    <script src="'.SITE_URL_PORTAL.'assets/js/jquery.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://www.youtube.com/iframe_api"></script>
</head>
<body class="bg-white" draggable="true">
    <div id="wrapper" class="course-watch">';
        require_once 'include/sessionMsg.php';
?>