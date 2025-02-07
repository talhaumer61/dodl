<?php
require_once 'include/head.php';
require_once 'include/top_nav.php';
require_once 'include/breadcrumb.php';
include_once 'edit_profile/query.php';

// MENU
$profileMenu = array(
	'profile-detail'        => array( 'title' => 'Profile Detail'           , 'url' => 'profile-detail'         , 'icon'    => 'feather-settings')
   ,'profile-security'      => array( 'title' => 'Profile Security'         , 'url' => 'profile-security'       , 'icon'    => 'feather-user')
   ,'education'             => array( 'title' => 'Education'                , 'url' => 'education'              , 'icon'    => 'feather-file')
   ,'social-links'          => array( 'title' => 'Social Links'             , 'url' => 'social-links'           , 'icon'    => 'feather-refresh-cw')
   ,'notifications'         => array( 'title' => 'Notifications'            , 'url' => 'notifications'          , 'icon'    => 'feather-bell')
   ,'profile-privacy'       => array( 'title' => 'Profile Privacy'          , 'url' => 'profile-privacy'        , 'icon'    => 'feather-lock')
   ,'profile-delete'        => array( 'title' => 'Profile Delete'           , 'url' => 'profile-delete'         , 'icon'    => 'feather-trash-2')
//    ,'referrals'             => array( 'title' => 'Referrals'                , 'url' => 'referrals'              , 'icon'    => 'feather-user-plus')
);

echo'
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-md-4">
                <div class="settings-widget dash-profile mb-3">
                    <div class="settings-menu p-0">
                        <div class="profile-bg">
                            <h5>'.get_levels($_SESSION['userlogininfo']['STDLEVEL']).'</h5>
                            <img src="'.SITE_URL.'assets/img/profile-bg.jpg" alt />
                            <div class="profile-img">
                                <a href="'.SITE_URL.'profile"><img src="'.$_SESSION['userlogininfo']['LOGINPHOTO'].'" alt/></a>
                            </div>
                        </div>
                        <div class="profile-group">
                            <div class="profile-name text-center">
                                <h4><a href="'.SITE_URL.'profile">'.$_SESSION['userlogininfo']['LOGINNAME'].'</a></h4>
                                <p>Learner</p>
                            </div>
                            <div class="go-dashboard text-center">
                                <a href="'.SITE_URL.'dashboard" class="btn btn-primary">Go to Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="settings-widget account-settings">
                    <div class="settings-menu">
                        <h3>Account Settings</h3>
                        <ul>';
                            foreach ($profileMenu as $key => $value) {
                                echo'
                                <li class="nav-item '.($varx == $key ? 'active':'').'">
                                    <a href="'.SITE_URL.$value['url'].'" class="nav-link"><i class="'.$value['icon'].'"></i> '.$value['title'].'</a>
                                </li>';
                            }
                            echo'
                            <li class="nav-item">
                                <a href="'.SITE_URL.'logout" class="nav-link"><i class="feather-power"></i> Sign Out</a>
                            </li>
                        </ul>';
                        /*
                        echo'
                        <h3>SUBSCRIPTION</h3>
                        <ul>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="feather-calendar"></i> My Subscriptions</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="feather-credit-card"></i> Billing Info</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="feather-credit-card"></i> Payment</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link"><i class="feather-clipboard"></i> Invoice</a>
                            </li>
                        </ul>';
                        */
                        echo'
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-md-8">
                <div class="settings-widget profile-details">
                    <div class="bg-white p-0" style="border-radius: 10px;">
                        <div class="profile-heading">
                            <h3>Profile Details</h3>
                            <p>You have full control to manage your own account setting.</p>
                        </div>';
                        include('edit_profile/'.str_replace('-', '_', $varx).'.php');
                        echo '
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
require_once 'include/footer.php';
echo '
<script>
    let image = $("#show-profile-pic");
    let deletebtn = $(".profile-btn-delete");
    image.css({
        "cursor" : "pointer"
    })
    $("#profile-pic").change(function(){
        readURL(this);
        $(".profile-btn").css({
            "display" : "block"
        })
    });
    deletebtn.click(function(){
        $(image).attr("src","uploads/images/students/'.$_SESSION['userlogininfo']['LOGINPHOTO'].'");
        $(".profile-btn").css({
            "display" : "none"
        })
    })
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(image).attr("src",e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
';
?>