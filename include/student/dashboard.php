<?php
require_once 'include/head.php';
require_once 'include/top_nav.php';

if(!$zone){
    $zone = 'dashboard';
}
$fileName = str_replace('-','_',$zone);

$dashboardMenu = array(
    array(
         'title'    =>  'Dashboard'
        ,'url'      =>  'dashboard'
    )
    ,array(
         'title'    =>  'Certificate Courses'
        ,'url'      =>  'courses'
    )
    ,array(
         'title'    =>  'Trainings'
        ,'url'      =>  'trainings'
    )
    ,array(
         'title'    =>  'MasterTrack'
        ,'url'      =>  'master_track_certificates'
    )
    ,array(
         'title'    =>  'Online Degree'
        ,'url'      =>  'degrees'
    )
    ,array(
         'title'    =>  'Wishlist'
        ,'url'      =>  'wishlist'
    )
    // ,array(
    //      'title'    =>  'Purchases'
    //     ,'url'      =>  'purchases'
    // )
    // ,array(
    //      'title'    =>  'Deposit'
    //     ,'url'      =>  'deposit'
    // )
    ,array(
         'title'    =>  'Enrollment'
        ,'url'      =>  'requests'
    )
    ,array(
         'title'    =>  'Challan'
        ,'url'      =>  'challans'
    )
);

// CHALLAN COUNT
$conditions = array ( 
                         'select' 		=>	'challan_id'
                        ,'where' 		=>	array( 
                                                     'is_deleted'   =>  '0'
                                                    ,'status'       =>  '2'
                                                    ,'id_std'       =>  cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                )
                        ,'return_type'	=>	'count'
                        ); 
$COUNT_CHALLAN = $dblms->getRows(CHALLANS, $conditions);
echo'<br><br><br><br>
<div class="course-student-header">
    <div class="container">
        <div class="student-group">
            <div class="course-group">
                <div class="course-group-img d-flex">
                    <a href="'.SITE_URL.'profile"><img src="'.$_SESSION['userlogininfo']['LOGINPHOTO'].'" alt class="img-fluid"/></a>
                    <div class="d-flex align-items-center">
                        <div class="course-name">
                            <h4>
                                <a href="'.SITE_URL.'profile">'.$_SESSION['userlogininfo']['LOGINNAME'].'</a><span>'.get_levels($_SESSION['userlogininfo']['STDLEVEL']).'</span>
                            </h4>
                            <p>'.ucwords('learner').'</p>
                        </div>
                    </div>
                </div>
                <div class="course-share">
                    <a href="'.SITE_URL.'profile-detail" class="btn btn-primary">Account Settings</a>
                </div>
            </div>
        </div>
        <div class="my-student-list">
    <nav class="navbar navbar-expand-lg" style="background: transparent !important;">
        <button class="navbar-toggler w-100" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="border-color: white;">
            <span class="navbar-toggler-icon" style="filter: invert(1);"> <i class="fa-solid fa-bars"></i> </span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">';

                foreach ($dashboardMenu as $key => $value) {
                    echo '<a href="'.SITE_URL.'student/'.to_seo_url($value['url']).'" 
                            class="nav-link '.(to_seo_url($value['url']) == $zone ? 'active' : '').'" 
                            style="color: white !important; '.($value['url'] == 'challans'? "padding-top:0.38rem !important;":"").' ">
                            '.$value['title'].'';
                            if ($value['url'] == 'challans' && $COUNT_CHALLAN > 0) {
                                blinkMsg($COUNT_CHALLAN.' Pending', 'warning');
                            }
                            echo'
                          </a>';
                    
                    
                }

echo '      </div>
        </div>
    </nav>
</div>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-md-12">
                <div class="tab-content">
                    <div class="tab-content">';
                        include ('tabs/'.$fileName.'.php');
                        echo'
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
require_once 'include/footer.php';
?>