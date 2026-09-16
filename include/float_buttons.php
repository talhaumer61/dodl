<?php
// WhatsApp Chat button
$waclass = ((date("H") >='08' AND date("H")<='18') ? 'online' : 'offline');
echo'
<div class="whatsapp-popup">
    <div class="whatsapp-button">
    <i class="fab fa-whatsapp i-open"></i>
    <i class="far fa-times-circle fa-fw i-close"></i>
    </div>
    <div class="popup-content">
    <div class="popup-content-header">
        <i class="fab fa-whatsapp"></i>
        <h5>Start a Conversation<span>Start a Conversation</span></h5>
    </div>
    <div class="whatsapp-content">
        <ul>
        <li class="'.$waclass.'">
            <a class="whatsapp-agent" href="javascript:void(0)" data-number="'.SITE_PHONE.'" data-message="Assalam o Alaikum, Sufyan Arshad! I have visited '.SITE_NAME.' and want to know know something more.">
            <div class="whatsapp-img">
                <img src="'.SITE_URL_PORTAL.'uploads/images/default_male.jpg" class="whatsapp-avatar" width="60" height="60">
            </div>
            <div>
                <span class="whatsapp-text">
                <span class="whatsapp-label">For Support - <span class="status">'.ucfirst($waclass).'</span></span> Sufyan Arshad</span>
            </div>
            </a>
        </li>
        </ul>
    </div>
    <div class="content-footer">
        <p>Use this feature to chat with our agent.</p>
    </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $(".whatsapp-button").on( "click", function() {
        $(\'.whatsapp-popup\').toggleClass(\'open\');
    });

    $(".whatsapp-agent").on( "click", function() {
        // go_to_whatsapp($(this).attr(\'data-number\'));
        go_to_whatsapp($(this).attr(\'data-number\'), $(this).attr(\'data-message\'));
    });

    function go_to_whatsapp(number, text = ""){
        var WhatsAppUrl = \'https://web.whatsapp.com/send\';
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        WhatsAppUrl = \'https://api.whatsapp.com/send\'; 
        }
        var url = WhatsAppUrl+\'?phone=\'+number;
        if (text !== "") {
        url += \'&text=\'+text;
        }
        var win = window.open(url, \'_blank\');
        win.focus();
    }
});
</script>';

// Floating Dashboard Button for Students
if (isset($user) && $user == 'student') { 
    echo'
    <div class="floating-dashboard" id="dashboardMenu">
        <button class="dashboard-btn" onclick="toggleDashboardMenu()">
            <i class="fas fa-home icon-home"></i>
            <i class="fas fa-times icon-close"></i>
        </button>

        <div class="dashboard-popup">
            <div class="popup-menu">
                <a href="'.SITE_URL.'student/dashboard">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="'.SITE_URL.'student/courses">
                    <i class="fas fa-graduation-cap"></i> Certificate Courses
                </a>
                <a href="'.SITE_URL.'student/trainings">
                    <i class="fas fa-chalkboard-teacher"></i> Trainings
                </a>
                <a href="'.SITE_URL.'student/wishlist">
                    <i class="fas fa-heart"></i> Wishlist
                </a>
                <a href="'.SITE_URL.'student/requests">
                    <i class="fas fa-book"></i> Enrollments
                </a>
                <a href="'.SITE_URL.'student/challans">
                    <i class="fas fa-file-invoice"></i> Challans
                </a>
                <a href="'.SITE_URL.'profile-detail">
                    <i class="fas fa-user-cog"></i> Profile
                </a>
            </div>
        </div>
    </div>
    <script>
    function toggleDashboardMenu() {
        document.getElementById("dashboardMenu").classList.toggle("open");
    }
    </script>';
} else { 
    echo'
    <a href="'.SITE_URL.'signin" class="floating-button floating-signin-button" title="Sign In">
        <i class="fas fa-sign-in-alt"></i>
    </a>';
}