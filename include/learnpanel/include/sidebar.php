<?php
// PERCENTEAGE      
$Total      = $COURSES['lesson_count']+$COURSES['assignment_count']+$COURSES['quiz_count'];
$Obtain     = $COURSES['track_count'];
$percent    = (($Obtain/$Total)*100);
$percent    = ($percent >= '100' ? '100' : $percent);

$arrays = array();
// OPEN LESSON
$con    = array ( 
                     'select'   =>	'cl.lesson_id as id, cl.lesson_topic as title, cl.id_week, lt.is_completed'
                    ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON ( lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_lecture = cl.lesson_id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')' 
                    ,'where' 	=>	array( 
                                                'cl.lesson_status' => 1
                                            ,'cl.is_deleted'    => 0
                                            ,'cl.id_curs' 	    => cleanvars($COURSES['curs_id']) 
                                        ) 
                    ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                    ,'order_by'     =>	'cl.id_week ASC LIMIT 1'
                    ,'return_type'  =>	'single'
                ); 
$NEXT_COURSES_LESSONS = $dblms->getRows(COURSES_LESSONS.' cl', $con);
if($NEXT_COURSES_LESSONS){
    array_push($NEXT_COURSES_LESSONS, 'lesson');
    array_push($arrays, $NEXT_COURSES_LESSONS);
}

// OPEN ASSIGNMENT
$con    = array ( 
                     'select'   =>	'ca.id as id, ca.id_week, ca.caption as title, lt.is_completed'
                    ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.cleanvars($COURSES['curs_id']).' AND lt.id_assignment = ca.id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')' 
                    ,'where' 	=>	array( 
                                                'ca.status'        => 1
                                            ,'ca.is_deleted'    => 0
                                            ,'ca.id_curs' 	    => cleanvars($COURSES['curs_id']) 
                                        ) 
                    ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                    ,'order_by'     =>	'ca.id_week ASC LIMIT 1'
                    ,'return_type'  =>	'single'
                ); 
$NEXT_COURSES_ASSIGNMENTS = $dblms->getRows(COURSES_ASSIGNMENTS.' ca', $con);
if($NEXT_COURSES_ASSIGNMENTS){
    array_push($NEXT_COURSES_ASSIGNMENTS, 'assignments');
    array_push($arrays, $NEXT_COURSES_ASSIGNMENTS);
}

// OPEN QUIZ
$con    = array ( 
                     'select'   =>	'q.quiz_id as id, q.id_week, q.quiz_title as title, lt.is_completed'
                    ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' lt ON (lt.id_quiz = q.quiz_id AND lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')'
                    ,'where' 	=>	array( 
                                                'q.quiz_status'    => 1
                                            ,'q.is_deleted'     => 0
                                            ,'q.is_publish'     => 1
                                            ,'q.id_curs'        => cleanvars($COURSES['curs_id']) 
                                    ) 
                    ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                    ,'order_by'     =>	'q.id_week ASC, q.quiz_id ASC'
                    ,'return_type'  =>	'single'
                ); 
$NEXT_QUIZ = $dblms->getRows(QUIZ.' q', $con, $sql);
if($NEXT_QUIZ){
    array_push($NEXT_QUIZ, 'quiz');
    array_push($arrays, $NEXT_QUIZ);
}

// Initialize variables to keep track of the minimum id_week and its index
$minIdWeek = PHP_INT_MAX;
$minIdWeekIndex = -1;

// Loop through each array
foreach ($arrays as $index => $array) {
    // Check if the current array has the lowest id_week
    if (isset($array['id_week']) && $array['id_week'] < $minIdWeek) {
        // Update the minimum id_week and its index
        $minIdWeek = $array['id_week'];
        $minIdWeekIndex = $index;
    }
}

// Output the array with the lowest id_week AND modify redirection
if ($minIdWeekIndex != -1) {
    $type = end($arrays[$minIdWeekIndex]);
    $name = $arrays[$minIdWeekIndex]['title'];
    $next_id = $arrays[$minIdWeekIndex]['id'];
    $redirection = $type.'/'.$next_id;
} else { 
    $next_id = '';
    $redirection = 'lesson/0';
}

// SIDE BAR OF THE LEARN PANEL
echo'
<div '.(isset($_SESSION['QUIZ_STARTTED'])?'hidden':'class="sidebar"').'>
    <span class="btn-close-mobi right-3 left-auto" uk-toggle="target: #wrapper ; cls: is-active"></span>
    <div class="flex justify-between lg:-ml-1 mt-1 mr-2">
        <a href="'.SITE_URL.'student/'.(!empty($COURSES['id_mas'])?'master-track-certificates':(!empty($COURSES['id_ad_prg'])?'degrees/bs-information-technology':'courses')).'" class="flex items-center text-blue-500">
            <i class="fa fa-chevron-left"></i>
            <span class="md:inline hidden"> Back</span>
        </a>
    </div>
    <h5 class="font-bold mt-1"> '.$COURSES['curs_name'].' </h5>
    <h6><span class="badge bg-success">'.$COURSES['curs_code'].'</span></h6>
    <div class="lg:inline hidden">
        <div class="relative overflow-hidden rounded-md bg-gray-200 h-1 mt-3">
            <div class="w-2/4 h-full bg-green-500" style="width: '.intval($percent).'% !important"></div>
        </div>
        <div class="text-sm border-b py-2">
            <div> '.intval($percent).'% Complete</div>
            <!--<div> Last activity on '.date('F d, Y').'</div>-->
        </div>
    </div>
    <div class="sidebar_inner is-watch-2" data-simplebar>
        <div id="curriculum">';
            if($percent == '100' && ($COURSES['id_type'] == 3 || $COURSES['id_type'] == 4)){
                echo'
                <ul>
                    <li class="uk-active">
                        <a href="'.SITE_URL.'certificate-print/'.$_SESSION['FLAG'].'" target="_blank" class="p-3">
                            <i style="font-size:20px;" class="fa fa-graduation-cap"></i>
                            <h6 class="mx-1 mb-0">Get Certificate</h6>
                        </a>
                    </li>
                </ul>';
            }
            echo'
            <div uk-accordion>';                        
                // COURSE RESOURCES
                $conResources = array(
                                        'select'       =>  '*'
                                    ,'where'        =>  array(
                                                                    'is_deleted'           =>	0
                                                                ,'status'               =>	1
                                                                ,'id_curs'				=>	cleanvars($COURSES['curs_id'])
                                                                ,'id_lesson'            =>	'0'
                                                            )
                                    ,'order_by'     =>  'id ASC'
                                    ,'return_type'  =>  'all'
                );
                $valResources = $dblms->getRows(COURSES_DOWNLOADS, $conResources);
                if($valResources){
                    echo'
                    <div class="pt-2 pb-2 border-bottom">
                        <a class="uk-accordion-title text-md mx-2 font-semibold" href="#">
                            <div class="mb-1 text-sm font-medium"></div> 
                            Course Resources
                        </a>
                        <div class="uk-accordion-content mt-3">
                            <ul class="course-curriculum-list">';
                                foreach ($valResources as $keySrc => $valSrc) {
                                    if(!empty($valSrc['file']) || !empty($valSrc['url']) || !empty($valSrc['embedcode'])){
                                        echo'
                                        <div class="card">
                                            <div class="card-header alert-dark">
                                                <h6>'.$valSrc['file_name'].'</h6>
                                            </div>
                                            <div class="card-body">
                                                '.get_CourseResources($valSrc['id_type']).'';
                                                if($valSrc['file']){
                                                    echo'
                                                    <a class="ms-1" href="'.SITE_URL_PORTAL.'uploads/files/course_resources/'.$valSrc['file'].'" target="_blank"><span class="badge bg-dark"><i class="fa fa-eye"></i> View</span></a>
                                                    <a class="ms-1" href="'.SITE_URL_PORTAL.'uploads/files/course_resources/'.$valSrc['file'].'" download="'.$valSrc['file'].'"><span class="badge bg-success"><i class="fa fa-download"></i> Download</span></a>';
                                                }
                                                if($valSrc['url']){
                                                    echo'<a class="ms-1" href="'.$valSrc['url'].'" target="_blank"><span class="badge bg-dark"><i class="fa fa-link"></i> Visit Link</span></a>';
                                                }
                                                if($valSrc['embedcode']){
                                                    echo'<a class="ms-1" onclick="show_modal(\''.SITE_URL.'include/modals/learn/course_resources/view_video.php?view_id='.cleanvars($valSrc['id']).'\');"><span class="badge bg-danger"><i class="fab fa-youtube"></i> Play Video</span></a>';
                                                }
                                                echo'
                                            </div>
                                        </div>';
                                    }
                                }
                                echo'
                            </ul>
                        </div>
                    </div>';
                }
                
                foreach(get_LessonWeeks() as $Weekkey => $WeekVal):
                    $tab_open_flag  = 0;
                    $tab_close_flag = 0;
                    // WEEK TITLE
                    $condition = array ( 
                                        'select' 		    =>	'caption'
                                        ,'where' 		    =>	array( 
                                                                        'id_week'      => $Weekkey
                                                                    ,'id_curs'      => cleanvars($COURSES['curs_id'])
                                                                    ,'is_deleted'   => '0'  
                                                                    ,'status'       => '1'  
                                                                )
                                        ,'return_type'	=>	'single'
                                        );
                    $weekTitle = $dblms->getRows(COURSES_WEEK_TITLE, $condition);
                    
                    //COURSES LESSONS
                    $con = array(
                                        'select'       =>  'l.lesson_id, l.id_week, l.lesson_topic, l.lesson_content, lt.is_completed'
                                    ,'join'         =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_lecture = l.lesson_id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')'
                                    ,'where'        =>  array(
                                                                    'l.id_curs'        => cleanvars($COURSES['curs_id'])
                                                                ,'l.id_week'        => cleanvars($Weekkey)
                                                                ,'l.lesson_status'  => 1
                                                                ,'l.is_deleted'     => 0    
                                                            )
                                    ,'order_by'     =>  'l.lesson_id ASC'
                                    ,'return_type'  =>  'all'
                                );
                    $COURSES_LESSONS = $dblms->getRows(COURSES_LESSONS.' AS l',$con, $sql);
                    // COURSES LESSONS LOCKING
                    if($COURSES_LESSONS) {
                        foreach ($COURSES_LESSONS AS $Lskey => $LsVal) {
                            if ($LsVal['lesson_id'] == $slug) {
                                if (!$tab_open_flag) {
                                    $tab_open_flag++;
                                }
                            }
                            if ($LsVal['is_completed'] == 2) {
                                if (!$tab_close_flag) {
                                    $tab_close_flag++;
                                }
                            }
                        }
                    }

                    //COURSES ASSIGNMENTS
                    $con = array(
                                        'select'       => 'a.id, a.caption, a.id_week, lt.is_completed'
                                    ,'join'         => 'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_assignment = a.id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')'
                                    ,'where'        => array(
                                                                    'a.id_curs'    => cleanvars($COURSES['curs_id'])
                                                                ,'a.id_week'    => cleanvars($Weekkey)
                                                                ,'a.status'     => 1
                                                                ,'a.is_deleted' => 0
                                                            )
                                    ,'order_by'     => 'a.id ASC'
                                    ,'return_type'  => 'all'
                                );
                    $COURSES_ASSIGNMENTS = $dblms->getRows(COURSES_ASSIGNMENTS.' AS a',$con);
                    // COURSES ASSIGNMENTS LOCKING
                    if($COURSES_ASSIGNMENTS) {
                        foreach ($COURSES_ASSIGNMENTS AS $Lskey => $LsVal) {
                            if ($LsVal['id'] == $slug) {
                                if (!$tab_open_flag) {
                                    $tab_open_flag++;
                                }
                            }
                            if ($LsVal['is_completed'] == 2) {
                                if (!$tab_close_flag) {
                                    $tab_close_flag++;
                                }
                            }
                        }
                    }
                    //COURSES QUIZ
                    $con    = array(
                                        'select'       => 'q.quiz_id, q.quiz_title, q.id_week, lt.is_completed'
                                    ,'join'         => 'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_quiz = q.quiz_id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')'
                                    ,'where'        => array(
                                                                    'q.quiz_status'  => 1
                                                                ,'q.is_publish'   => 1
                                                                ,'q.is_deleted'   => 0
                                                                ,'q.id_curs'      => cleanvars($COURSES['curs_id'])
                                                                ,'q.id_week'      => cleanvars($Weekkey)
                                                            )
                                    ,'return_type'  => 'all'
                                );
                    $QUIZ = $dblms->getRows(QUIZ.' q',$con);
                    // COURSES QUIZ LOCKING
                    if($QUIZ) {
                        foreach ($QUIZ AS $Lskey => $LsVal) {
                            if ($LsVal['quiz_id'] == $slug) {
                                if (!$tab_open_flag) {
                                    $tab_open_flag++;
                                }
                            }
                            if ($LsVal['is_completed'] == 2) {
                                if (!$tab_close_flag) {
                                    $tab_close_flag++;
                                }
                            }
                        }
                    }

                    // LOAD SIDEBAR
                    if ($COURSES_LESSONS || $COURSES_ASSIGNMENTS || $QUIZ) {
                        echo'
                        <div class="'.(($tab_open_flag)?'uk-open':'').' pt-2 pb-2 border-bottom">
                            <a class="uk-accordion-title text-md mx-2 font-semibold" href="#">
                                <div class="mb-1 text-sm font-medium"></div> 
                                '.moduleName(get_CourseWise($COURSES['curs_wise'])).' '.$COURSES_LESSONS['id_week'].$Weekkey.'
                                '.(!empty($weekTitle['caption']) ? '- '.$weekTitle['caption'] : '').'
                            </a>
                            <div class="uk-accordion-content mt-3">
                                <ul class="course-curriculum-list">';
                                    if($COURSES_LESSONS) {
                                        $sidebar_view = 'lesson';
                                        echo'
                                        <div class="card">
                                            <div class="card-header alert-primary">
                                                <h6 class="mb-0">Topics</h6>
                                            </div>
                                            <div class="card-body">';
                                                foreach ($COURSES_LESSONS AS $Lskey => $LsVal) {
                                                    $href = '';
                                                    $cursor = ' style="cursor: not-allowed"';
                                                    $is_completed = ((!empty($LsVal['is_completed']) && $LsVal['is_completed'] == 2) ? 'fa-solid fa-check text-success' : '');
                                                    $lesson_content = ((!empty($LsVal['lesson_content']) && $LsVal['lesson_content'] != 2) ? 'fas fa-play-circle' : 'fa fa-book-reader');
                                                    if(($LsVal['is_completed'] == 2 || $next_id == $LsVal['lesson_id']) && $sidebar_view == 'lesson'){
                                                        $href = 'href="'.SITE_URL.'learn/'.ZONE.'/lesson/'.$LsVal['lesson_id'].'"';
                                                        $cursor = ' style="cursor: pointer"';
                                                    }
                                                    echo'
                                                    <li class="'.(($LsVal['lesson_id'] == $slug) ? 'uk-active' : '').'">
                                                        <a '.$href.$cursor.'>
                                                            <i style="font-size:20px;" class="'.$lesson_content.'"></i>
                                                            <h6 class="mx-1 mb-0">'.($Lskey+1).'-'.html_entity_decode($LsVal['lesson_topic']).'</h6> 
                                                            <span>
                                                                <i class="'.$is_completed.'"></i>
                                                            </span>
                                                        </a>
                                                    </li>';
                                                }
                                                echo'
                                            </div>
                                        </div>';
                                    }
                                    if ($COURSES_ASSIGNMENTS) {
                                        $sidebar_view = 'assignments';
                                        echo'
                                        <div class="card">
                                            <div class="card-header alert-primary">
                                                <h6 class="mb-0">Assignments</h6>
                                            </div>
                                            <div class="card-body">';
                                                foreach ($COURSES_ASSIGNMENTS AS $Lskey => $LsVal) {
                                                    $href = '';
                                                    $cursor = ' style="cursor: not-allowed"';
                                                    $is_completed = ((!empty($LsVal['is_completed']) && $LsVal['is_completed'] == 2) ? 'fa-solid fa-check text-success' : '');
                                                    if(($LsVal['is_completed'] == 2 || $next_id == $LsVal['id']) && $sidebar_view == 'assignments'){
                                                        $href = 'href="'.SITE_URL.'learn/'.ZONE.'/assignments/'.$LsVal['id'].'"';
                                                        $cursor = ' style="cursor: pointer"';
                                                    }
                                                    echo'
                                                    <li class="'.(($LsVal['id'] === $slug) ? 'uk-active' : '').'">
                                                        <a '.$href.$cursor.'>
                                                            <i style="font-size:20px;" class="fas fa-clipboard"></i>
                                                            <h6 class="mx-1 mb-0">'.html_entity_decode($LsVal['caption']).'</h6> 
                                                            <span> 
                                                                <i class="'.$is_completed.'"></i>
                                                            </span>
                                                        </a>
                                                    </li>';
                                                }   
                                                echo'
                                            </div>
                                        </div>';
                                    }
                                    if ($QUIZ) {
                                        $sidebar_view = 'quiz';
                                        echo'
                                        <div class="card">
                                            <div class="card-header alert-primary">
                                                <h6 class="mb-0">Quizes</h6>
                                            </div>
                                            <div class="card-body">';
                                                foreach ($QUIZ AS $Lskey => $LsVal) {
                                                    $href = '';
                                                    $cursor = ' style="cursor: not-allowed"';
                                                    $is_completed = ((!empty($LsVal['is_completed']) && $LsVal['is_completed'] == 2) ? 'fa-solid fa-check text-success' : '');
                                                    if(($LsVal['is_completed'] == 2 || $next_id == $LsVal['quiz_id']) && $sidebar_view == 'quiz'){
                                                        $href = 'href="'.SITE_URL.'learn/'.ZONE.'/quiz/'.$LsVal['quiz_id'].'"';
                                                        $cursor = ' style="cursor: pointer"';
                                                    }
                                                    echo'
                                                    <li class="'.(($LsVal['quiz_id'] === $slug) ? 'uk-active' : '').'">
                                                        <a '.$href.$cursor.'>
                                                            <i style="font-size:20px;" class="fas fa-paperclip"></i>
                                                            <h6 class="mx-1 mb-0">'.html_entity_decode($LsVal['quiz_title']).'</h6> 
                                                            <span> 
                                                                <i class="'.$is_completed.'"></i>
                                                            </span>
                                                        </a>
                                                    </li>';
                                                }   
                                                echo'
                                            </div>
                                        </div>';
                                    }
                                    echo'
                                </ul>
                            </div>
                        </div>';
                    }
                endforeach;
                echo'
            </div>
        </div>
    </div>
    <div class="side_overly" uk-toggle="target: #wrapper ; cls: is-collapse is-active"></div>
</div>';
?>