<?php
require_once 'include/head.php';
require_once 'include/top_nav.php';
require_once 'include/breadcrumb.php';

// STUDENT PROFILE DETAIL
$condition = array ( 
                        'select' 		=>	'e.emply_id, s.id_intrests, s.std_about, s.std_sociallinks, s.std_address_1, s.std_address_2, s.std_postal_address, a.adm_email, a.adm_phone'
                        ,'join'         =>  'LEFT JOIN '.STUDENTS.' s ON a.adm_id = s.std_loginid AND s.is_deleted = 0 AND s.std_status
                                             LEFT JOIN '.EMPLOYEES.' e ON a.adm_id = e.emply_loginid AND e.is_deleted = 0 AND e.emply_status'  
                        ,'where' 		=>	array( 
                                                    'adm_id'    =>  cleanvars($_SESSION['userlogininfo']['LOGINIDA']) 
                                                ) 
                        ,'return_type'	=>	'single'
                    );
$row = $dblms->getRows(ADMINS.' a', $condition);

if(!empty($row['emply_id'])){
    // Employee Education
    $condition = array ( 
                             'select' 		=>	'program, institute, year'
                            ,'where' 		=>	array( 
                                                         'id_employee'      => cleanvars($row['emply_id']) 
                                                        ,'is_deleted'       => 0 
                                                    ) 
                            ,'return_type'	=>	'all'
                        );     
    $emply_edu = $dblms->getRows(EMPLOYEE_EDUCATIONS, $condition);

    // Employee Experience
    $condition = array ( 
                             'select' 		=>	'jobfield, organization, designation, date_start, date_end'
                            ,'where' 		=>	array( 
                                                         'id_employee'      => cleanvars($row['emply_id']) 
                                                        ,'is_deleted'       => 0 
                                                        ,'status'           => 1 
                                                    ) 
                            ,'return_type'	=>	'all'
                        );     
    $emply_exp = $dblms->getRows(EMPLOYEE_EXPERIENCE, $condition);
}

// Student Education
$condition = array ( 
                         'select' 		=>	'program, institute, year'
                        ,'where' 		=>	array( 
                                                     'id_std'                => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                    ,'is_deleted' 	         => 0 
                                                    ,'status' 	             => 1 
                                                ) 
                        ,'return_type'	=>	'all'
                    );     
$std_edu = $dblms->getRows(STUDENT_EDUCATIONS, $condition);

// Enrolled Courses
$condition = array ( 
                         'select' 		=>	'GROUP_CONCAT(id_curs) as curs_ids'
                        ,'where' 		=>	array( 
                                                     'id_std'           => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                    ,'secs_status'      => 1 
                                                    ,'is_deleted'       => 0 
                                                ) 
                        ,'return_type'	=>	'single'
                    );
$enrolled_courses = $dblms->getRows(ENROLLED_COURSES, $condition);

// SKILLS
if($enrolled_courses){
    $condition  =   array ( 
                             'select' 		=>	'GROUP_CONCAT(curs_skills) as skills'
                            ,'where' 		=>	array(
                                                         'is_deleted'   => 0 
                                                        ,'curs_status'  => 1 
                                                    )
                            ,'search_by'    =>  ' AND FIND_IN_SET(curs_id, "'.$enrolled_courses['curs_ids'].'")'
                            ,'return_type'	=>	'single'
                        );     
    $SKILLS = $dblms->getRows(COURSES, $condition);
}

// INTERESTS
if(!empty($row['id_intrests'])){    
    $condition  =   array ( 
                             'select' 		=>	'skill_name'
                            ,'where' 		=>	array(
                                                         'is_deleted'   => 0 
                                                        ,'skill_status'  => 1 
                                                    )
                            ,'search_by'    =>  ' AND FIND_IN_SET(skill_id, "'.$row['id_intrests'].'")'
                            ,'order_by'     =>	' RAND()'
                            ,'return_type'	=>	'all'
                    );     
    $interests = $dblms->getRows(SKILLS, $condition);
}

// SOCCIAL LINKS
$sociallinks = unserialize($row['std_sociallinks']);

$condition = array ( 
                     'select'       =>	'rev_id, rev_name, rev_photo, rev_detail'
                    ,'where'        =>	array( 
                                               'rev_status'   =>  '1' 
                                              ,'is_deleted'   =>  '0'
                                            )
                    ,'order_by'     =>	'RAND()'
                    ,'limit'        =>	'4'
                    ,'return_type'	=>	'all'
                  );
$REVIEWS = $dblms->getRows(REVIEWS, $condition);
echo'
<div class="page-banner student-bg-blk">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="profile-info-blk">
                    <a class="profile-info-img">
                        <img src="'.$_SESSION['userlogininfo']['LOGINPHOTO'].'" alt="Profile Avatar" class="img-fluid" />
                    </a>
                    <h4>
                        <a class="text-white">'.$_SESSION['userlogininfo']['LOGINNAME'].'</a><span>'.get_levels($_SESSION['userlogininfo']['STDLEVEL']).'</span>
                    </h4>
                    <p>Learner</p>';
                    if($sociallinks):
                        echo '
                        <ul class="list-unstyled inline-inline profile-info-social">';
                            foreach ($sociallinks as $key => $value) {
                                echo '<li class="list-inline-item"><a href="'.$value.'" target="_blank"><i class="'.get_social_links($key)['icon'].'"></i></a></li>';
                            }
                            echo'
                        </ul>';
                    endif;
                    echo '
                </div>
            </div>
        </div>
    </div>
</div>
<section class="page-content course-sec">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card overview-sec">
                    <div class="card-body">
                        <h5 class="subs-title">About Me</h5>
                        <p class=" mb-0">'.(!empty($row['std_about']) ? $row['std_about'] : '<span class="text-danger">Not Set Yet.</span>').'</p>
                    </div>
                </div>
                <div class="card education-sec">
                    <div class="card-body">
                        <h5 class="subs-title">Education</h5>';
                        $edu_sr = 0;
                        foreach ($std_edu as $key => $value) {
                            $edu_sr++;
                            echo'
                            <div class="edu-wrap">
                                <div class="edu-name">
                                    <span><i class="fa fa-graduation-cap"></i></span>
                                </div>
                                <div class="edu-detail">
                                    <h6>'.$value['program'].'</h6>
                                    <p class="edu-duration">'.$value['institute'].' - ('.$value['year'].')</p>
                                </div>
                            </div>';
                        }
                        if(!empty($row['emply_id']) && !empty($emply_edu)){
                            $edu_sr++;
                            foreach ($emply_edu as $key => $value) {
                                echo'
                                <div class="edu-wrap">
                                    <div class="edu-name">
                                        <span><i class="fa fa-graduation-cap"></i></span>
                                    </div>
                                    <div class="edu-detail">
                                        <h6>'.$value['program'].'</h6>
                                        <p class="edu-duration">'.$value['institute'].' - ('.$value['year'].')</p>
                                    </div>
                                </div>';
                            }
                        }
                        if($edu_sr == 0){
                            echo '<p class="text-danger mb-0">Not Set Yet.</p>';
                        }
                        echo'
                    </div>
                </div>';
                if(!empty($row['emply_id']) && !empty($emply_exp)){
                    echo'
                    <div class="card education-sec">
                        <div class="card-body">
                            <h5 class="subs-title">Experience</h5>';
                            foreach ($emply_exp as $key => $value) {
                                echo'
                                <div class="edu-wrap">
                                    <div class="edu-name">
                                        <span><i class="fas fa-bookmark"></i></span>
                                    </div>
                                    <div class="edu-detail">
                                        <h6>'.$value['jobfield'].' - '.$value['designation'].'</h6>
                                        <p class="edu-duration">'.$value['organization'].' - ('.date("F, Y",strtotime($value['date_start'])).' - '.date("F, Y",strtotime($value['date_end'])).')</p>
                                    </div>
                                </div>';
                            }
                            echo'
                        </div>
                    </div>';
                }
                echo'
                <div class="card review-sec">
                    <div class="card-body">
                        <h5 class="subs-title">Reviews</h5>';
                        foreach ($REVIEWS as $key => $rev) {
                            $words = explode(' ', $rev['rev_name']);
                            $initials = '';
                            foreach ($words as $word) {
                                $initials .= $word[0];
                            }
                            $rev_title = substr($initials, 0, 2);
                
                            // CHECK FILE EXIST
                            $rev_photo = '';
                            $file_url = SITE_URL_PORTAL.'uploads/images/reviews/'.$rev['rev_photo'];
                            if (check_file_exists($file_url)) {
                                $rev_photo = $file_url;
                            }
                            echo'
                            <div class="review-item">
                                <div class="instructor-wrap border-0 m-0">
                                    <div class="about-instructor">
                                        <div class="abt-instructor-img">';
                                            if(!empty($rev_photo)){
                                                echo'<img src="'.$rev_photo.'" alt class="img-fluid"/>';
                                            } else {
                                                echo'<h6 class="circle bg-warning text-white" style="margin: auto; width:50px; height:50px;">'.$rev_title.'</h6>';
                                            }
                                            echo'
                                        </div>
                                        <div class="instructor-detail">
                                            <h5>'.$rev['rev_name'].'</h5>
                                        </div>
                                    </div>
                                </div>
                                <p class="rev-info"> “ '.html_entity_decode($rev['rev_detail']).' “</p>
                            </div>';
                        }
                        echo'
                    </div>
                </div>';
                /*
                echo'
                <div class="card comment-sec">
                    <div class="card-body">
                        <h5 class="subs-title">Add a review</h5>
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Full Name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Email" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Subject" />
                            </div>
                            <div class="form-group">
                                <textarea rows="4" class="form-control" placeholder="Your Comments" ></textarea>
                            </div>
                            <div class="submit-section">
                                <button class="btn submit-btn" type="submit"> Submit </button>
                            </div>
                        </form>
                    </div>
                </div>';
                */
                echo'
            </div>
            <div class="col-lg-4">
                <div class="card overview-sec">
                    <div class="card-body overview-sec-body">
                        <h5 class="subs-title">Professional Skills</h5>
                        <div class="sidebar-tag-labels">
                            <ul class="list-unstyled">';
                                if(!empty($SKILLS)){
                                    $array = explode(",", $SKILLS['skills']);
                                    $array = array_unique($array);
                                    foreach ($array as $skill) {
                                        echo'<li><a href="javascript:;" class>'.$skill.'</a></li>';                                        
                                    }
                                }
                                else{
                                    echo '<p class="text-danger mb-0">Not Set Yet.</p>';
                                }
                                echo'
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card overview-sec">
                    <div class="card-body overview-sec-body">
                        <h5 class="subs-title">Social Accounts</h5>
                        <div class="sidebar-tag-labels">
                            <ul class="list-unstyled">';
                                if($sociallinks){
                                    foreach ($sociallinks as $key => $value) {
                                        echo '<li><a href="'.$value.'" class target="_blank"><i class="'.get_social_links($key)['icon'].'"></i> '.get_social_links($key)['name'].'</a></li>';
                                    }
                                }else{
                                    echo '<p class="text-danger mb-0">Not Set Yet.</p>';
                                }
                                echo'
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card overview-sec">
                    <div class="card-body">
                        <h5 class="subs-title">Contact Details</h5>
                        <div class="contact-info-list">
                            <div class="edu-wrap">
                                <div class="edu-name">
                                    <span><img src="assets/img/instructor/email-icon.png" alt="Address"/></span>
                                </div>
                                <div class="edu-detail">
                                    <h6 class="mb-0">Email</h6>
                                    '.(!empty($row['adm_email']) ? '<p>'.$row['adm_email'].'</p>' : '<p class="text-danger mb-0">Not Set Yet.</p>').'
                                </div>
                            </div>
                            <div class="edu-wrap">
                                <div class="edu-name">
                                    <span><img src="assets/img/instructor/phone-icon.png" alt="Phone"/></span>
                                </div>
                                <div class="edu-detail">
                                    <h6 class="mb-0">Phone</h6>
                                    '.(!empty($row['adm_phone']) ? '<p>'.$row['adm_phone'].'</p>' : '<p class="text-danger mb-0">Not Set Yet.</p>').'
                                </div>
                            </div>
                            <div class="edu-wrap">
                                <div class="edu-name">
                                    <span><img src="assets/img/instructor/address-icon.png" alt="Address"/></span>
                                </div>
                                <div class="edu-detail">
                                    <h6 class="mb-0">Address</h6>
                                    '.(!empty($row['std_address_1']) ? '<p>'.$row['std_address_1'].'</p>' : '<p class="text-danger mb-0">Not Set Yet.</p>').'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>';
require_once 'include/footer.php';
?>