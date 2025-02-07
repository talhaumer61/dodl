<?php
echo'
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0"/>
    <title>'.SITE_NAME.'</title>
    <link rel="shortcut icon" type="image/x-icon" href="'.SITE_URL.'assets/img/favicon.ico"/>
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
    <script src="'.SITE_URL.'assets/js/jquery-3.6.0.min.js"></script>
    <!-- SWEETALERT JS/CSS -->
    <link rel="stylesheet" href="'.SITE_URL.'assets/plugins/sweetalert/sweetalert_custom.css">
    <script src="'.SITE_URL.'assets/plugins/sweetalert/sweetalert.min.js"></script>
    <!-- WHATSAPP -->
    <link rel="stylesheet" href="'.SITE_URL.'assets/css/whatsapp-chat-support.css">
    <!-- INPUT MASK -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
  </head>
  <body draggable="false">
    <div class="main-wrapper">';
      include_once 'sessionMsg.php';
      $sqlstring	= "";
			$adjacents	= 3;
			if(!($Limit)) 	{ $Limit = 20; } 
			if($page)		{ $start = ($page - 1) * $Limit; } else {	$start = 0;	}
?>