<?php
include "include/dbsetting/classdbconection.php";
include "include/dbsetting/lms_vars_config.php";
include "include/functions/functions.php";
include "include/functions/login_func.php";
include "uploads/data/data.php";
$dblms = new dblms();
$varx = '';
$user = NULL;
if (isset($_SESSION['userlogininfo']['LOGINTYPE'])){
  $user  = get_logintypes(cleanvars($_SESSION['userlogininfo']['LOGINTYPE']));
}
if($control == 'logout') {
  panelLMSSTDLogout();
}

if(CONTROLER != "teacher-engagement-interest"){
  unset($_SESSION['teacher_interest_id']);
}

// MENU
$profileMenu = array(
                       'profile-detail'
                      ,'profile-security'
                      ,'education'
                      ,'social-links'
                      ,'notification'
                      ,'profile-privacy'
                      ,'profile-delete'
                      ,'notifications'
                      ,'referrals'
                    );

// SITE REDIRECTIONS
if (CONTROLER == "about" || CONTROLER == "about-us"){
  $varx = 'about';
  include ('include/about.php');
} else if (CONTROLER == "courses" || CONTROLER == "short-courses" || CONTROLER == "certificate-courses"){
  $_SESSION['id_type'] = 1;
  $varx = 'courses';
  include ('include/courses.php');
} else if(CONTROLER == "trainings"){
  $_SESSION['id_type'] = 2;
  $varx = 'trainings';
  include ('include/courses.php');
} else if (CONTROLER == "online-degrees" || CONTROLER == "degrees" || CONTROLER == "degree-detail"){
  $varx = 'degrees';
  include ('include/degrees.php');
} else if (CONTROLER == "master-track-certificates" || CONTROLER  == 'master-track-detail'){
  $varx = 'mastertrack';
  include ('include/mastertrack.php');
} else if (CONTROLER == "signin"){
  if(isset($_SESSION['userlogininfo']['LOGINIDA'])) {
    sessionMsg('Message', 'Already Logged in.', 'success');
    header("Location: ".SITE_URL."dashboard");	
  } else { 
    include ('include/signin.php');
  }
} else if (CONTROLER == "forgot-password"){
  if(isset($_SESSION['userlogininfo']['LOGINIDA'])) {
    sessionMsg('Message', 'Already Logged in.', 'success');
    header("Location: ".SITE_URL."dashboard");	
  } else { 
    include ('include/forgot_password.php');
  }
} else if (CONTROLER == "signup"){
  if(isset($_SESSION['userlogininfo']['LOGINIDA'])) {
    sessionMsg('Message', 'Already Logged in.', 'success');
    header("Location: ".SITE_URL."dashboard");	
  } else { 
    include ('include/signup.php');
  }
} else if (CONTROLER == "signup-as-instructor"){
  if(isset($_SESSION['userlogininfo']['LOGINIDA']) ) {
    if($_SESSION['userlogininfo']['LOGINISTEACHER'] == 1){
      include ('include/signup.php');
    } else {
      sessionMsg('Message', 'Already an Instructor.', 'info');
      header("Location: ".SITE_URL."dashboard");	
    }
  } else {
    header("Location: ".SITE_URL."signin");
  }
} else if (in_array(CONTROLER, $profileMenu) && isset($_SESSION['userlogininfo']['LOGINTYPE']) == 3){
  $varx = CONTROLER;
  include ('include/'.$user.'/edit_profile.php');
} else if ((CONTROLER == "profile" || CONTROLER == "my-profile") && isset($_SESSION['userlogininfo']['LOGINTYPE']) == 3){
  include ('include/'.$user.'/profile.php');
} else if ((CONTROLER == "dashboard" || CONTROLER == "my-dashboard" || CONTROLER == "student") && isset($_SESSION['userlogininfo']['LOGINTYPE']) == 3){
  include ('include/'.$user.'/dashboard.php');
} else if (CONTROLER == "learn" && isset($_SESSION['userlogininfo']['LOGINTYPE']) == 3){
  include ('include/learnpanel/index.php');
} else if (CONTROLER == "courselearn" && isset($_SESSION['userlogininfo']['LOGINTYPE']) == 3){
  include("include/courses.php");
} else if (CONTROLER == "instructors"){
  include("include/instructors.php");
} else if (CONTROLER == "terms"){
  include("include/terms.php");
} else if (CONTROLER == "blogs"){
  include("include/blogs.php");
} else if (CONTROLER == "privacy"){
  include("include/privacy.php");
} else if (CONTROLER == "invoice"){
  include("include/enroll/invoice.php");
} else if (CONTROLER == "learn-free-invoice"){
  include("include/enroll/learn_free_invoice.php");
} else if (CONTROLER == "show-interest"){
  include("include/enroll/show_interest.php");
} else if(CONTROLER == "home"){
  $varx = CONTROLER;
  include ('include/index.php');  
} else if(CONTROLER == "pma"){
  include ('pma.php'); 
} else if(CONTROLER == "challan-print"){
  include ('challan_print.php');
} else if(CONTROLER == "certificate-print"){
  include ('certificate_print.php');
} else if(CONTROLER == "feedback"){
  include("include/feedback/feedback_form.php");
} else if(CONTROLER == "payfast"){
  include("include/payfast/index.php");
} else if(CONTROLER == "payfast-cancel"){
  include("include/payfast/cancel.php");
} else if(CONTROLER == "payfast-success"){
  include("include/payfast/success.php");
} else if(CONTROLER == "verify-certificate"){
  include("include/print_forms/verify_certificate.php");
} else if(CONTROLER == "teacher-engagement-interest"){
  include("include/requests/teacher_engagement_interest.php");
} else if(CONTROLER == "signup-skill-ambassador"){
  include("include/signup_skill_ambassador.php");
} else {
  header("Location: ".SITE_URL."home");
}
?>