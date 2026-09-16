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

    <!-- Stylesheets -->
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/fontawesome/css/fontawesome.min.css"/>
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/owl.theme.default.min.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/slick/slick.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/slick/slick-theme.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/select2/css/select2.min.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/aos/aos.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/style.css" />
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/feather/feather.css" />

    <!-- Scripts -->
    <script src="'.SITE_URL.'assets/js/jquery-3.6.0.min.js"></script>

    <!-- SWEETALERT JS/CSS -->
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/sweetalert/sweetalert_custom.css">
    <script src="'.SITE_URL.'assets/plugins/sweetalert/sweetalert.min.js"></script>

    <!-- WHATSAPP -->
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/whatsapp-chat-support.css">
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/float-buttons.css">

    <!-- INPUT MASK -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

    <!-- GOOGLE RECAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- SWIPER -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  </head>
  <body draggable="true">
    <div class="main-wrapper">';
    include_once 'sessionMsg.php';
    $sqlstring = "";
    $adjacents = 3;
    if(!($Limit)) { $Limit = 20; }  
    if($page) { $start = ($page - 1) * $Limit; } else { $start = 0; }
?>