<?php
if(LMS_VIEW != 'quiz'){
    unset($_SESSION['QUIZ_STARTTED']);
}
if(!empty($zone)) {
    include 'include/learnpanel/include/header.php';
    $_SESSION['FLAG'] = (!empty(cleanvars(LMS_FLAG)))?cleanvars(LMS_FLAG):$_SESSION['FLAG'];
    
    if($slug == '0'){
        $con    = array ( 
                             'select'   =>	'cl.lesson_id, cl.id_week'
                            ,'join'     =>	'INNER JOIN '.COURSES.' c ON c.curs_id = cl.id_curs AND c.curs_href = "'.cleanvars($zone).'"'
                            ,'where' 	=>	array( 
                                                     'cl.lesson_status' => 1
                                                    ,'cl.is_deleted'    => 0
                                                )
                            ,'order_by'     =>	'cl.id_week ASC, cl.lesson_id ASC'
                            ,'return_type'  =>	'single'
                        ); 
        $NO_SLUG_LESSON = $dblms->getRows(COURSES_LESSONS.' cl', $con, $sql);
        $slug = $NO_SLUG_LESSON['lesson_id'];
    }
    $con = array(
                     'select'       => 'c.curs_id, c.curs_wise, c.curs_name, c.curs_href, c.curs_code, ec.id_mas, ec.id_ad_prg
                                        ,COUNT(DISTINCT cl.lesson_id) AS lesson_count
                                        ,COUNT(DISTINCT ca.id) AS assignment_count
                                        ,COUNT(DISTINCT cq.quiz_id) AS quiz_count
                                        ,COUNT(DISTINCT lt.track_id) AS track_count'
                    ,'join'         => 'INNER JOIN '.COURSES.' AS c ON FIND_IN_SET(c.curs_id,ec.id_curs) AND c.curs_href = "'.cleanvars($zone).'" AND c.curs_status = 1 AND c.is_deleted = 0
                                        LEFT JOIN '.COURSES_LESSONS.' AS cl ON cl.id_curs = c.curs_id AND cl.lesson_status = 1 AND cl.is_deleted = 0
                                        LEFT JOIN '.COURSES_ASSIGNMENTS.' AS ca ON ca.id_curs = c.curs_id AND ca.status = 1 AND ca.is_deleted = 0
                                        LEFT JOIN '.QUIZ.' AS cq ON cq.id_curs = c.curs_id AND cq.quiz_status = 1 AND cq.is_deleted = 0 AND cq.is_publish = 1
                                        LEFT JOIN '.LECTURE_TRACKING.' AS lt ON lt.id_curs = c.curs_id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.is_completed = 2 AND lt.is_deleted = 0 AND lt.id_mas = ec.id_mas AND lt.id_ad_prg = ec.id_ad_prg'
                    ,'where'        => array(
                                                 'ec.secs_status'   => 1
                                                ,'ec.is_deleted'    => 0    
                                                ,'ec.secs_id'       => $_SESSION['FLAG']
                                            )   
                    ,'return_type'  => 'single'
                );
    $COURSES =  $dblms->getRows(ENROLLED_COURSES.' AS ec',$con, $sql);
    if ($COURSES) {  
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
                    if($percent == '100' && $COURSES['id_mas'] == '0' && $COURSES['id_ad_prg'] == '0'){
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
                                <div class="'.(($tab_open_flag)?'uk-open':'').' pt-2">
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
                                                    <div class="card-header alert-dark">
                                                        <h6>Topics</h6>
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
                                                                    <h6 class="mx-1 mb-0">'.html_entity_decode($LsVal['lesson_topic']).'</h6> 
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
                                                    <div class="card-header alert-dark">
                                                        <h6>Assignments</h6>
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
                                                    <div class="card-header alert-dark">
                                                        <h6>Quizes</h6>
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
                                <div class="pt-2">
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
                        echo'
                    </div>
                </div>
            </div>
            <div class="side_overly" uk-toggle="target: #wrapper ; cls: is-collapse is-active"></div>
        </div>
        <div '.(isset($_SESSION['QUIZ_STARTTED'])?'':'class="main_content mb-5"').'>';
            // IF STUDENT WANT TO SEE COURSE LESSONS
            if ($view === 'lesson') {
                echo'
                <div class="relative z-10">';
                    //COURSES LESSONS                    
                    $con = array(
                                     'select'       => 'l.lesson_id, l.id_week, l.id_lecture, l.lesson_topic, l.lesson_content, l.lesson_video_code, l.lesson_detail, l.lesson_reading_detail, lt.is_completed, lt.my_note_pad, lt.std_review'
                                    ,'join'         => 'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_lecture = l.lesson_id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')'
                                    ,'where'        => array(
                                                                 'l.lesson_status'    => 1
                                                                ,'l.is_deleted'       => 0
                                                                ,'l.lesson_id'        => cleanvars($slug)
                                                            )
                                    ,'return_type'  => 'single'
                                );
                    $COURSES_LESSONS = $dblms->getRows(COURSES_LESSONS.' AS l',$con);

                    // FOR LOAD IF THE LESSON HAS THE VIDEO ID
                    if ($COURSES_LESSONS['lesson_content'] != 2) {
                        echo'
                        
                        <style>
                            #background-video {
                                width: 100%;
                                height: 100%;
                            }
                            .controls {
                                user-select: none;
                                position: absolute;
                                top: 77%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                gap: 10px;
                                transition: opacity 0.3s ease;
                                z-index: 1;
                            }
                            .control-bar {
                                user-select: none;
                                display: flex;
                                align-items: center;
                                background-color: rgba(0, 0, 0, 0.5);
                                border-radius: 50px;
                                padding: 10px;
                            }
                            .control-bar button, .volume-knob {
                                user-select: none;
                                background-color: transparent;
                                border: none;
                                cursor: pointer;
                                color: #d3d3d4;
                                font-size: 20px;
                                margin-left: 12px;
                                margin-right: 10px;
                            }
                            .video-knob {
                                user-select: none;
                                width: 300px;
                                height: 18px;
                                background-color: #444;
                                position: relative;
                                border-radius: 20px;
                                margin-left: 10px;
                                cursor: pointer;
                            }
                            .volume-knob {
                                user-select: none;
                                width: 118.5px;
                                height: 18px;
                                background-color: #444;
                                position: relative;
                                border-radius: 20px;
                                margin-left: 10px;
                                cursor: pointer;
                            }
                            .video-knob-handle, .volume-knob-handle {
                                user-select: none;
                                position: absolute;
                                top: 50%;
                                transform: translate(-50%, -50%);
                                height: 15px;
                                background-color: #d3d3d4;
                                border-radius: 50px;
                                cursor: grab;
                                color: #000;
                                text-align: center;
                                font-size: 10px;
                            }
                            .video-knob-handle {
                                width: 30px;
                                margin-left: 17px;
                            }
                            .video-knob-handle-p {
                                font-size: 9px;
                                line-height: 17px;
                            }
                            .volume-knob-handle {
                                left: 5;
                                width: 15px;
                                margin-left: 9px;
                            }
                            .knob-handle:active {
                                user-select: none;
                                cursor: grabbing;
                            }
                        </style>

                        <video id="player" class="player" controls playsinline data-yt2html5="https://www.youtube.com/watch?v='.$COURSES_LESSONS['lesson_video_code'].'"></video>

                        <script>
                            const controller = new YouTubeToHtml5({
                                autoload: false,
                                withAudio: true
                            });

                            controller.load();

                            document.addEventListener(\'DOMContentLoaded\', (event) => {
                                const video = document.querySelector(\'.player\');
                    
                                video.addEventListener(\'play\', () => {';
                                    if ($COURSES_LESSONS['is_completed'] != 2) {
                                        echo'
                                        $.ajax({
                                            url: "' . SITE_URL . 'include/ajax/get_tracking.php",
                                            method: "POST",
                                            data: {
                                                "id_video": id_video,
                                                "id_week": id_week,
                                                "id_lecture": id_lecture,
                                                "id_curs": id_curs,
                                                "curs_href": curs_href,
                                                "id_mas": id_mas,
                                                "id_ad_prg": id_ad_prg,
                                                "track_mood": "playing"
                                            },
                                            success: function(e) {
                                                if (e === "track_already_added") {
                                                    updateCountdown();
                                                } else if (e === "track_already_added_dont_track") {

                                                } else if (e === "track_added") {
                                                    updateCountdown();
                                                }
                                            }
                                        });';
                                    }
                                    echo'                                    
                                });
                    
                                video.addEventListener(\'pause\', () => {';
                                    if ($COURSES_LESSONS['is_completed'] != 2) {
                                        echo'
                                        pauseCountdown();';
                                    }
                                    echo'
                                });
                            });
                        </script>

                        <!--
                        <div class="embed-video">
                        -->';

                            
                            // LESSON TRACKING PLAYER AND JAVASCRIPT CODE
                            if ($COURSES_LESSONS['is_completed'] != 2) {
                                $originalTime = get_YTVideoDuration($COURSES_LESSONS['lesson_video_code']);
                                echo'
                                <script>
                                    function padZero(number) {
                                        return number < 10 ? `0${number}` : `${number}`;
                                    }
                                    function calculateHalfTime(originalTime) {
                                        const halfTime = originalTime * 0.5;
                                        return halfTime;
                                    }
                                    function timeToSeconds(timeString) {
                                        const [hours, minutes, seconds] = timeString. split(`:`); 
                                        const totalSeconds = (+hours) * 60 * 60 + (+minutes) * 60 + (+seconds);
                                        return totalSeconds;
                                    }
                                    function secondsToTime(seconds) {
                                        const hours = Math.floor((seconds / 3600));
                                        const minutes = Math.floor((seconds % 3600) / 60);
                                        const remainingSeconds = Math.floor((seconds % 60));
                                        const formattedTime = `${padZero(hours)}:${padZero(minutes)}:${padZero(remainingSeconds)}`;
                                        return formattedTime;
                                    }
                                    const countdownDuration = 1;
                                    var countdownPaused     = false;
                                    var secondsRemaining    = calculateHalfTime(timeToSeconds("'.$originalTime.'"));
                                    var id_video            = "'.$COURSES_LESSONS['lesson_video_code'].'";
                                    var id_week             = "'.$COURSES_LESSONS['id_week'].'";
                                    var id_lecture          = "'.$COURSES_LESSONS['lesson_id'].'";
                                    var id_curs             = "'.$COURSES['curs_id'].'";
                                    var id_mas              = "'.$COURSES['id_mas'].'";
                                    var id_ad_prg           = "'.$COURSES['id_ad_prg'].'";
                                    var curs_href           = "'.$COURSES['curs_href'].'";
                                    var countdownInterval;
                                    function pauseCountdown() {
                                        clearInterval(countdownInterval);
                                        countdownInterval = null;
                                        countdownPaused = true;
                                    }
                                    function resumeCountdown() {
                                        if (countdownPaused) {
                                            countdownInterval = setInterval(updateCountdown, 1000);
                                            countdownPaused = false;
                                        }
                                    }

                                    function updateCountdown() {
                                        var videoRemainingTime  = document.getElementById("video_remaining_time");
                                        if (secondsRemaining > countdownDuration) {
                                            secondsRemaining--;
                                            videoRemainingTime.innerHTML    = secondsToTime(secondsRemaining);
                                            countdownInterval               = setTimeout(updateCountdown,1000);
                                        } else {     
                                            $.ajax({
                                                 url        : "'.SITE_URL.'include/ajax/get_tracking.php"
                                                ,method     : "POST"
                                                ,data       : {
                                                                 "id_video"     : id_video
                                                                ,"id_week"      : id_week
                                                                ,"id_lecture"   : id_lecture
                                                                ,"id_curs"      : id_curs
                                                                ,"id_mas"       : id_mas
                                                                ,"id_ad_prg"    : id_ad_prg
                                                                ,"curs_href"    : curs_href
                                                                ,"track_mood"   : "completed"
                                                }
                                                ,success    : function(e) {
                                                    videoRemainingTime.innerHTML = e;
                                                }
                                            });
                                        }
                                    }
                                </script>';
                            }
                            echo'                 
                        <!--
                        </div>
                        -->';
                    } else {
                        // LESSON TRACKING FOR THE READING METRAIL
                        if ($COURSES_LESSONS['is_completed'] != 2) {
                            echo'
                            <script>
                                setTimeout(function() {
                                    var id_week             = "'.$COURSES_LESSONS['id_week'].'";
                                    var id_lecture          = "'.$COURSES_LESSONS['lesson_id'].'";
                                    var id_curs             = "'.$COURSES['curs_id'].'";
                                    var id_mas              = "'.$COURSES['id_mas'].'";
                                    var id_ad_prg           = "'.$COURSES['id_ad_prg'].'";
                                    var curs_href           = "'.$COURSES['curs_href'].'";
                                    var videoRemainingTime  = document.getElementById("video_remaining_time");
                                    $.ajax({
                                        url        : "'.SITE_URL.'include/ajax/get_tracking.php"
                                        ,method     : "POST"
                                        ,data       : {
                                                         "id_week"      : id_week
                                                        ,"id_lecture"   : id_lecture
                                                        ,"id_curs"      : id_curs
                                                        ,"id_mas"       : id_mas
                                                        ,"id_ad_prg"    : id_ad_prg
                                                        ,"curs_href"    : curs_href
                                                        ,"track_mood"   : "reading_metrail"
                                        }
                                        ,success    : function(e) {
                                            if (e == "reading_metrail_completed") {

                                            } else {
                                                videoRemainingTime.innerHTML = e;
                                            }
                                        }
                                    });
                                }, 10000);
                            </script>';
                        }
                    }
                    echo'
                    <div class="lg:px-6 border-b">
                        <div class="lg:py-3 mx-auto">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex flex-row align-items-center">
                                    <h4 class="text-xl font-semibold mb-0"> 
                                        About This Lesson 
                                    </h4>
                                </div>
                                <div class="text-center font-semibold mt-1">
                                    <h3 id="video_remaining_time">';
                                        // WHEN LESSON IS COMPLETED NEXT BTN
                                        if ($COURSES_LESSONS['is_completed'] == 2 && $next_id != '') {
                                            echo '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success">Next '.moduleName($type).': '.$name.'</a>';
                                        }
                                        echo'
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>';
                    if(!empty($COURSES_LESSONS['lesson_reading_detail'])){
                        echo'<div class="lg:px-6 mt-2">'.html_entity_decode(html_entity_decode($COURSES_LESSONS['lesson_reading_detail'])).'</div>';
                    }
                    echo'
                    <nav class="cd-secondary-nav border-b md:p-0 lg:px-6 bg-white " uk-sticky="cls-active:shadow-sm ; media: @s">
                        <ul uk-switcher="connect: #lesson-tabs; animation: uk-animation-fade">
                            <li><a class="lg:px-2">Topic Description </a></li>
                            <li><a class="lg:px-2">Topic Resources</a></li>
                            <li><a class="lg:px-2">Discussion Board</a></li>
                            <li><a class="lg:px-2">Note Book</a></li> 
                            <li><a class="lg:px-2">Announcements</a></li>
                            <li><a class="lg:px-2">FAQs</a></li>
                            <li><a class="lg:px-2">Review</a></li> 
                        </ul>
                    </nav>
                    <div class="lg:px-6">
                        <div class="lg:py-6 mx-auto uk-switcher" id="lesson-tabs">
                            <!-- Topic Description -->
                            <div>
                                <div class="row">
                                    <div class="col">';
                                        if(!empty($COURSES_LESSONS['lesson_detail'])){
                                            echo html_entity_decode(html_entity_decode($COURSES_LESSONS['lesson_detail']));
                                        }else{
                                            echo '<h6 class="text-danger">No Lesson Description...!</h6>';
                                        }
                                        echo'
                                    </div>
                                </div>
                            </div>

                            <!-- Topic Resources -->
                            <div>
                                <div class="row">';
                                    $con = array(
                                                    'select'       =>  'cd.id, cd.file_name, cd.url, cd.file'
                                                    ,'where'        =>  array(
                                                                                 'cd.id_curs'        => cleanvars($COURSES['curs_id'])
                                                                                ,'cd.id_lesson'      => cleanvars($COURSES_LESSONS['lesson_id'])
                                                                                ,'cd.status'         => 1
                                                                                ,'cd.is_deleted'     => 0    
                                                                            )
                                                    ,'order_by'     =>  'cd.id ASC'
                                                    ,'return_type'  =>  'all'
                                                );
                                    $COURSES_DOWNLOADS = $dblms->getRows(COURSES_DOWNLOADS.' AS cd',$con, $sql);
                                    if($COURSES_DOWNLOADS){
                                        foreach ($COURSES_DOWNLOADS AS $Cdkey => $CdVal) {
                                            if(!empty($CdVal['url'])){
                                                echo'
                                                <div class="col-md-4">
                                                    <div class="card p-3 mb-2">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex flex-row align-items-center">
                                                                <div class="ms-2 c-details">
                                                                    <h6 class="mb-0"><a href="'.$CdVal['url'].'" target="_blank">'.moduleName($CdVal['file_name']).'</a></h6>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <div class="text-center">
                                                                    <a href="'.$CdVal['url'].'" target="_blank"><i class="fa fa-link" style="font-size: 20px;"></i></a> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                            }
                                            if(!empty($CdVal['file'])){
                                                echo'
                                                <div class="col-md-4">
                                                    <div class="card p-3 mb-2">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex flex-row align-items-center">
                                                                <div class="ms-2 c-details">
                                                                    <h6 class="mb-0"><a href="'.SITE_URL_PORTAL.'uploads/files/lesson_plan/'.$CdVal['file'].'" target="_blank">'.moduleName($CdVal['file_name']).'</a></h6>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <div class="text-center">
                                                                    <a href="'.SITE_URL_PORTAL.'uploads/files/lesson_plan/'.$CdVal['file'].'" target="_blank"><i class="fa fa-download" style="font-size: 20px;"></i></a> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                            }
                                        }
                                    }else{
                                        echo '<h6 class="text-danger">No Course Resources...!</h6>';
                                    }
                                    echo'
                                </div>
                            </div>

                            <!-- Discussion Board  -->
                            <div>
                                <div class="row">';
                                    //COURSES DISCUSSION
                                    $con = array(
                                                     'select'       =>  'cd.discussion_id, cd.discussion_subject, cd.discussion_detail, cd.id_lecture, cds.dst_detail, a.adm_photo, a.adm_fullname, e.emply_gender'
                                                    ,'join'         =>  'INNER JOIN '.ADMINS.' a on a.adm_id = cd.id_added
                                                                         LEFT JOIN '.EMPLOYEES.' e on e.emply_id = cd.id_teacher
                                                                         LEFT JOIN '.COURSES_DISCUSSIONSTUDENTS.' cds on cds.id_discussion = cd.discussion_id'
                                                    ,'where'        =>  array(
                                                                                 'cd.id_curs'               =>  cleanvars($COURSES['curs_id'])
                                                                                ,'cd.discussion_status'     =>  1
                                                                                ,'cd.is_deleted'            =>  0
                                                                            )
                                                    ,'search_by'    =>  ' AND FIND_IN_SET('.$COURSES_LESSONS['id_lecture'].',cd.id_lecture)'
                                                    ,'return_type'  =>  'all'
                                                );
                                    $COURSES_DISCUSSION = $dblms->getRows(COURSES_DISCUSSION.' cd',$con, $sql);
                                    if($COURSES_DISCUSSION){
                                        include 'include/discussion_board/query.php';
                                        foreach ($COURSES_DISCUSSION AS $Cdkey => $CdVal) {
                                            if($CdVal['emply_gender'] == '2'){
                                                $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
                                            }else{            
                                                $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
                                            }
                                            if(!empty($CdVal['adm_photo'])){
                                                $file_url = SITE_URL_PORTAL.'uploads/images/admin/'.$CdVal['adm_photo'];
                                                if (check_file_exists($file_url)) {
                                                    $photo = $file_url;
                                                }
                                            }
                                            echo'
                                            <div class="col-md-12">
                                                <div class="card p-3 mb-3">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <div class="icon">
                                                                <img src="'.$photo.'" alt class="shadow w-12 h-12 icon">
                                                            </div>
                                                            <div class="ms-2 c-details">
                                                                <h6 class="mb-0">'.moduleName($CdVal['adm_fullname']).'</h6>
                                                                <b>Intructor</b>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <div class="text-center">';
                                                                if (empty($CdVal['dst_detail'])) {
                                                                    echo'<a class="btn btn-sm btn-dark text-white" onclick="show_modal(\''.SITE_URL.'include/modals/learn/discussion_board/add.php?id_discussion='.cleanvars($CdVal['discussion_id']).'\');">Submit Your Discussion</a>';
                                                                } else {
                                                                    echo'<span class="btn btn-sm btn-success text-white">Submited</span>';
                                                                }
                                                                echo'
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <h4 class="leading-8 text-xl">'.moduleName($CdVal['discussion_subject']).'</h4>
                                                        <p>'.html_entity_decode(html_entity_decode($CdVal['discussion_detail'])).'</p>
                                                    </div>
                                                </div>
                                            </div>';
                                        }                    
                                    }else{
                                        echo '<h6 class="text-danger">No Discussion Board...!</h6>';
                                    }
                                    echo '
                                </div>
                            </div>
                            
                            <!-- Note BooK -->
                            <div>
                                <div class="row">
                                    <div class="form-group">
                                        <label> Make Your Own Notes</label>
                                        <textarea class="form-control" rows="10" id="myNotePad">'.html_entity_decode(html_entity_decode($COURSES_LESSONS['my_note_pad'])).'</textarea>
                                    </div>
                                    <script>
                                    $(document).ready(function() {
                                        $("#myNotePad").on("input", function() {
                                            var myNote = $(this).val().trim();
                                            if (myNote !== "") {
                                                var id_week             = "'.$COURSES_LESSONS['id_week'].'";
                                                var id_lecture          = "'.$COURSES_LESSONS['lesson_id'].'";
                                                var id_curs             = "'.$COURSES['curs_id'].'";
                                                var curs_href           = "'.$COURSES['curs_href'].'";
                                                var id_mas              = "'.$COURSES['id_mas'].'";
                                                var id_ad_prg           = "'.$COURSES['id_ad_prg'].'";
                                                var id_std              = "'.$_SESSION['userlogininfo']['STDID'].'";
                                                $.ajax({
                                                     url        : "'.SITE_URL.'include/ajax/get_tracking.php"
                                                    ,method     : "POST"
                                                    ,data       : {
                                                                     "id_week"      : id_week
                                                                    ,"id_lecture"   : id_lecture
                                                                    ,"id_curs"      : id_curs
                                                                    ,"curs_href"    : curs_href
                                                                    ,"id_mas"       : id_mas
                                                                    ,"id_ad_prg"    : id_ad_prg
                                                                    ,"myNote"       : myNote
                                                                    ,"id_std"       : id_std
                                                                    ,"track_mood"   : "my_note_pad_saved"
                                                    }
                                                });
                                            }
                                        });
                                    });
                                    </script>
                                </div>
                            </div>

                            <!-- Announcements -->
                            <div>
                                <div class="row">';
                                    //COURSES ANNOUNCEMENTS
                                    $con    = array(
                                                         'select'       =>  'ca.announcement_topic, ca.announcement_detail, ca.id_lecture, ca.date_added, a.adm_photo, a.adm_fullname, e.emply_gender'
                                                        ,'join'         =>  'INNER JOIN '.ADMINS.' a on a.adm_id = ca.id_added
                                                                             LEFT JOIN '.EMPLOYEES.' e on e.emply_id = ca.id_teacher'
                                                        ,'where'        =>  array(
                                                                                     'ca.id_curs'                => cleanvars($COURSES['curs_id'])
                                                                                    ,'ca.announcement_status'    => 1
                                                                                    ,'ca.is_deleted'             => 0
                                                                                )
                                                        ,'return_type'  =>  'all'
                                                    );
                                    $COURSES_ANNOUNCEMENTS  = $dblms->getRows(COURSES_ANNOUNCEMENTS.' ca',$con);
                                    if($COURSES_ANNOUNCEMENTS){
                                        foreach ($COURSES_ANNOUNCEMENTS AS $Cakey => $CaVal) {
                                            // CHECK FILE EXIST
                                            if($CaVal['emply_gender'] == '2'){
                                                $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
                                            }else{            
                                                $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
                                            }
                                            if(!empty($CaVal['adm_photo'])){
                                                $file_url   = SITE_URL_PORTAL.'uploads/images/admin/'.$CaVal['adm_photo'];
                                                if (check_file_exists($file_url)) {
                                                    $photo = $file_url;
                                                }
                                            }
                                            echo'
                                            <div class="col-md-12">
                                                <div class="card p-3 mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <div class="icon">
                                                                <img src="'.$photo.'" alt class="rounded-full shadow w-12 h-12">
                                                            </div>
                                                            <div class="ms-2 c-details">
                                                                <h6 class="mb-0">'.moduleName($CaVal['adm_fullname']).'</h6>
                                                                <b>Intructor</b>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <div class="text-center">
                                                                Date: <b class="text-info"> '.date("d M,Y",strtotime($CaVal['date_added'])).' </b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <h4 class="leading-8 text-xl">'.moduleName($CaVal['announcement_topic']).'</h4>
                                                        <p>'.html_entity_decode(html_entity_decode($CaVal['announcement_detail'])).'</p>
                                                    </div>
                                                </div>
                                            </div>';
                                        }                    
                                    }else{
                                        echo '<h6 class="text-danger">No Announcements...!</h6>';
                                    }
                                    echo '
                                </div>
                            </div>

                            <!-- faq -->
                            <div>
                                <div class="row">
                                    <ul uk-accordion="multiple: true" class="divide-y space-y-3 space-y-6">';
                                        //COURSES FAQS
                                        $con                            = array();
                                        $con['select']                  = 'question,answer,id_lesson'; 
                                        $con['where']['id_curs']        = cleanvars($COURSES['curs_id']); 
                                        $con['where']['status']         = 1; 
                                        $con['where']['is_deleted']     = 0; 
                                        $con['return_type']             = 'all'; 
                                        $COURSES_FAQS                   = $dblms->getRows(COURSES_FAQS,$con);
                                        if($COURSES_FAQS){
                                            foreach ($COURSES_FAQS AS $Faqkey => $FaqVal) {
                                                echo'
                                                <li class="'.(($Faqkey == 0)?'uk-open':'').'">
                                                    <a class="uk-accordion-title font-semibold text-xl mt-4"> '.$FaqVal['question'].'</a>
                                                    <div class="uk-accordion-content mt-3">
                                                        '.html_entity_decode(html_entity_decode($FaqVal['answer'])).'
                                                    </div>
                                                </li>';
                                            }                    
                                        }else{
                                            echo '<h6 class="text-danger">No Frequent Asked Questions...!</h6>';
                                        }
                                        echo'
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Review -->
                            <div>
                                <div class="row">
                                    <div class="form-group">
                                        <label> Review about the Lecture Topic</label>
                                        <textarea class="form-control" rows="10" id="std_review">'.html_entity_decode(html_entity_decode($COURSES_LESSONS['std_review'])).'</textarea>
                                    </div>
                                    <script>
                                    $(document).ready(function() {
                                        $("#std_review").on("input", function() {
                                            var std_review = $(this).val().trim();
                                            if (std_review !== "") {
                                                var id_week             = "'.$COURSES_LESSONS['id_week'].'";
                                                var id_lecture          = "'.$COURSES_LESSONS['lesson_id'].'";
                                                var id_curs             = "'.$COURSES['curs_id'].'";
                                                var curs_href           = "'.$COURSES['curs_href'].'";
                                                var id_mas              = "'.$COURSES['id_mas'].'";
                                                var id_ad_prg           = "'.$COURSES['id_ad_prg'].'";
                                                var id_std              = "'.$_SESSION['userlogininfo']['STDID'].'";
                                                $.ajax({
                                                     url        : "'.SITE_URL.'include/ajax/get_tracking.php"
                                                    ,method     : "POST"
                                                    ,data       : {
                                                                     "id_week"      : id_week
                                                                    ,"id_lecture"   : id_lecture
                                                                    ,"id_curs"      : id_curs
                                                                    ,"curs_href"    : curs_href
                                                                    ,"id_mas"       : id_mas
                                                                    ,"id_ad_prg"    : id_ad_prg
                                                                    ,"std_review"   : std_review
                                                                    ,"id_std"       : id_std
                                                                    ,"track_mood"   : "std_review_saved"
                                                    }
                                                });
                                            }
                                        });
                                    });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            // IF STUDENT WANT TO SEE COURSE ASSIGNMENTS
            else if ($view === 'assignments') {
                echo'
                <div class="relative z-10">';
                    //COURSES ASSIGNMENTS
                    $con = array(
                                     'select'       => 'ca.id,ca.caption,ca.detail,ca.fileattach,ca.date_start,ca.date_end,e.emply_name,cas.student_file,ca.id_week,ca.id_curs,lt.is_completed'
                                    ,'join'         => 'LEFT JOIN '.EMPLOYEES.' e ON e.emply_id = ca.id_teacher 
                                                        LEFT JOIN '.COURSES_ASSIGNMENTS_STUDENTS.' cas ON ca.id = cas.id_assignment
                                                        LEFT JOIN '.LECTURE_TRACKING.' lt ON (lt.id_curs = '.$COURSES['curs_id'].' AND lt.id_assignment = ca.id AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$COURSES['id_mas'].' AND lt.id_ad_prg = '.$COURSES['id_ad_prg'].')'
                                    ,'where'        => array(
                                                                 'ca.id_curs'       => cleanvars($COURSES['curs_id'])
                                                                ,'ca.status'        => 1
                                                                ,'ca.is_deleted'    => 0
                                                                ,'ca.id'            => cleanvars($slug)
                                                            )
                                    ,'return_type'  => 'single'
                    );
                    $COURSES_ASSIGNMENTS = $dblms->getRows(COURSES_ASSIGNMENTS.' ca',$con);
                    // LOADING THE ASSIGNMENT FILE BUTTON
                    if ($COURSES_ASSIGNMENTS['lesson_content'] != 2) {
                        $extension      = pathinfo($COURSES_ASSIGNMENTS['fileattach'],PATHINFO_EXTENSION);
                        $officeFiles    = ['xlsx','xls','doc','docx','ppt','pptx'];
                        $pdfFiles       = ['pdf'];
                        $imgFiles       = ['png','jpg','jpeg'];

                        // WORD, PPT, EXCEL
                        if (in_array($extension,$officeFiles)) {
                            echo'
                            <!--
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://786electronic.com/include/hamza.pptx"></iframe>
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://786electronic.com/include/hamza.xlsx"></iframe>
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=https://786electronic.com/include/hamza.docx"></iframe>
                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src='.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'"></iframe>
                            -->
                            <div class="embed-video">
                                <iframe src="https://view.officeapps.live.com/op/embed.aspx?src='.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'"></iframe>
                            </div>';
                        } 
                        // PDF
                        else if (in_array($extension,$pdfFiles)) {    
                            echo'
                            <div class="embed-video">
                                <iframe src="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'"></iframe>
                            </div>';
                        } 
                        // IMAGE
                        else if (in_array($extension,$imgFiles)) {
                            echo'
                            <div class="embed-video">
                                <img src="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'" alt="'.$COURSES_ASSIGNMENTS['caption'].'" class="img-fluid">
                            </div>';
                        }
                    } 
                    echo'                    
                    <div class="lg:px-6">
                        <div class="lg:py-6 mx-auto">
                            <div class="row">                            
                                <div class="flex-row align-items-center mb-3">
                                    <h4 class="text-xl font-semibold mb-0"> '.html_entity_decode($COURSES_ASSIGNMENTS['caption']).'</h4>
                                    <div>'.html_entity_decode(html_entity_decode($COURSES_ASSIGNMENTS['detail'])).'</div>
                                </div>
                                <hr>
                                <div class="col-md-12 mb-3">
                                    <div class="text-center">';
                                        // ASSIGNMENT FILE DOWNLOAD
                                        if (!empty($COURSES_ASSIGNMENTS['fileattach'])) {
                                            echo'<a rel="nofollow" download="'.$COURSES_ASSIGNMENTS['fileattach'].'" target="_blank" title="Download Assignment File" href="'.SITE_URL_PORTAL.'uploads/files/assignments/'.$COURSES_ASSIGNMENTS['fileattach'].'" class="btn btn-sm btn-primary text-white me-1"><i class="fa fa-download me-1"></i>Assignment File</a>';
                                        }
                                        // STUDENT UPLOADED FILE 
                                        if (!empty($COURSES_ASSIGNMENTS['student_file'])) {
                                            echo'<a rel="nofollow" download="'.$COURSES_ASSIGNMENTS['student_file'].'" target="_blank" title="Download Submited File" href="'.SITE_URL.'uploads/files/student_assignments/'.$COURSES_ASSIGNMENTS['student_file'].'" class="btn btn-sm btn-dark text-white me-1"><i class="fa fa-download me-1"></i>Your File</a>';
                                            // echo'<span class="btn btn-sm btn-success text-white me-1">Assignment Submited</span>';
                                        }else{
                                            include 'include/assignments/query.php';
                                            echo'<a class="btn btn-sm btn-success text-white me-1" onclick="show_modal(\''.SITE_URL.'include/modals/learn/assignments/add.php?id_week='.cleanvars($COURSES_ASSIGNMENTS['id_week']).'&id_curs='.cleanvars($COURSES['curs_id']).'&id_mas='.cleanvars($COURSES['id_mas']).'&id_ad_prg='.cleanvars($COURSES['id_ad_prg']).'&id_assignment='.cleanvars($slug).'&id_lecture='.cleanvars($COURSES_ASSIGNMENTS['id']).'&caption='.$COURSES_ASSIGNMENTS['caption'].'\');"><i class="fa fa-upload me-1"></i>Submit Assignment</a>';
                                        }
                                        // WHEN LESSON, ASSIGNMENT OR QUIZ IS COMPLETED THEN NEXT BTN
                                        if ($COURSES_ASSIGNMENTS['is_completed'] == 2 && $next_id != '') {
                                            echo '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success">Next '.moduleName($type).': '.$name.'</a>';
                                        }
                                        echo'
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            // IF STUDENT WANT TO SEE COURSE QUIZ
            else if ($view === 'quiz') {
                echo'
                <div class="relative z-10">';
                    //COURSES QUIZ
                    $con    = array(
                                     'select'       =>  'q.quiz_id, q.quiz_title, q.quiz_instruction, q.quiz_time, q.quiz_totalmarks, q.quiz_passingmarks, q.quiz_no_qns, q.id_week, q.id_curs, qs.qzstd_id
                                                        ,COUNT(CASE WHEN qq.quiz_qns_type = 3 THEN qq.quiz_qns_id ELSE NULL END) AS countMultipleChoice
                                                        ,COUNT(CASE WHEN qq.quiz_qns_type = 1 THEN qq.quiz_qns_id ELSE NULL END) AS countShort
                                                        ,SUM(CASE WHEN qq.quiz_qns_type = 3 THEN qq.quiz_qns_marks ELSE NULL END) AS marksMultipleChoice
                                                        ,SUM(CASE WHEN qq.quiz_qns_type = 1 THEN qq.quiz_qns_marks ELSE NULL END) AS marksShort
                                                        ,qs.qzstd_obtain_marks, qs.qzstd_pass_fail, qs.qzstd_id'
                                    ,'join'         =>  'INNER JOIN '.QUIZ_QUESTIONS.' qq ON qq.id_quiz = q.quiz_id
                                                         LEFT JOIN '.QUIZ_STUDENTS.' qs ON ( qs.id_quiz = q.quiz_id AND qs.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).')'
                                    ,'where'        =>  array(
                                                                 'q.quiz_status'  => 1
                                                                ,'q.is_publish'   => 1
                                                                ,'q.is_deleted'   => 0
                                                                ,'q.quiz_id'      => cleanvars($slug)
                                                            )
                                    ,'return_type'  => 'single'
                                );
                    $QUIZ = $dblms->getRows(QUIZ.' q',$con);
                    if (isset($_SESSION['QUIZ_STARTTED'])) {
                        echo'
                        <form method="POST" id="quizFrom" class="quizFrom">
                            <script>
                                document.addEventListener("keydown", function (e) {
                                    if (e.ctrlKey && e.key === "r") {
                                        e.preventDefault();
                                    }
                                    if (e.ctrlKey && e.key === "e") {
                                        e.preventDefault();
                                    }
                                    if (e.ctrlKey && e.key === "F5") {
                                        e.preventDefault();
                                    }
                                });
                            </script>
                            <div class="lg:px-6">
                                <div class="lg:py-6 mx-auto">
                                    <style>
                                        .sticky-top {
                                            position: fixed;
                                            top: 0;
                                            left: 0;
                                            right: 0;
                                            z-index: 1000;
                                            background-color: white;
                                            padding: 10px;
                                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                        }
                                        p{
                                            margin-bottom: 5px;
                                        }
                                    </style>
                                    <div id="quizBar" class="sticky-top d-flex justify-content-between">
                                        <div class="d-flex flex-row align-items-center">
                                            <h4 class="text-xl font-semibold mb-0"> '.moduleName($QUIZ['quiz_title']).'</h4>
                                            <input type="hidden" value="'.cleanvars($QUIZ['quiz_id']).'" name="id_quiz">
                                            <input type="hidden" value="'.cleanvars($QUIZ['id_week']).'" name="id_week">
                                            <input type="hidden" value="'.cleanvars($QUIZ['id_curs']).'" name="id_curs">
                                            <input type="hidden" value="end" name="quiz_method">
                                        </div>
                                        <div>
                                            <h3>
                                                <span style="font-size: 20px;" class="fa fa-clock"></span> <span style="font-size: 20px;" id="quizTimmer">00:00:00</span>
                                                <script>
                                                    function SecToTime(seconds) {
                                                        const hours = Math.floor(seconds / 3600);
                                                        const minutes = Math.floor((seconds % 3600) / 60);
                                                        const remainingSeconds = seconds % 60;
                                                        var time = "";
                                                        if (hours == 0) {
                                                            time = `${minutes}:${remainingSeconds}`;
                                                        } else if (minutes == 0) {
                                                            time = `${remainingSeconds} seconds`;
                                                        } else {
                                                            time = `${hours}:${minutes}:${remainingSeconds}`;
                                                        }
                                                        return time;
                                                    }
                                                    function MinToSec(minutes) {
                                                        return minutes * 60;
                                                    }
                                                    const countdownDuration = 1;
                                                    var secondsRemaining    = MinToSec("'.$QUIZ['quiz_time'].'");
                                                    function quizTimmer() {
                                                        var quizTimmer  = document.getElementById("quizTimmer");
                                                        if (secondsRemaining > countdownDuration) {
                                                            secondsRemaining--;
                                                            quizTimmer.innerHTML    = SecToTime(secondsRemaining);
                                                        } else {     
                                                            quizTimmer.innerHTML    = "Time Is Up";
                                                            var quizFrom = $("#quizFrom").serialize();
                                                            $.ajax({
                                                                 url        : "'.SITE_URL.'include/ajax/get_quiz.php"
                                                                ,method     : "POST"
                                                                ,data       : quizFrom
                                                                ,success    : function() {
                                                                    location.reload();
                                                                }
                                                            });
                                                        }
                                                    }
                                                    setInterval(quizTimmer, 1000);
                                                </script>
                                                <button id="quizCancel" class="btn btn-danger btn-sm"><i class="fa fa-close me-1"></i>Cancel</button>
                                                <script>
                                                    $("#quizCancel").on("click",function(){
                                                        $.ajax({
                                                             url        : "'.SITE_URL.'include/ajax/get_quiz.php"
                                                            ,method     : "POST"
                                                            ,data       : {
                                                                "quiz_method" : "cancel"
                                                            }
                                                            ,success    : function() {
                                                                location.reload();
                                                            }
                                                        });
                                                    });
                                                </script>
                                            </h3>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="space-y-4 mt-5">';
                                        if ($_SESSION['QUIZ_STARTTED']) {
                                            $m_flag = 0; 
                                            $s_flag = 0;
                                            foreach ($_SESSION['QUIZ_STARTTED'] AS $key => $val) {
                                                $optionFlag = 0;
                                                if ($val['quiz_qns_type'] == 3) {
                                                    $m_flag++;
                                                    if ($m_flag == 1) {
                                                        echo'
                                                        <div class="card">
                                                            <div class="card-header alert-dark">
                                                                <b>Multiple Choice Questions</b>
                                                            </div>
                                                            <div class="card-body py-0">';
                                                    }
                                                    echo'
                                                    <div class="p-2 bg-light border rounded my-3">
                                                        <p><b>Q '.($key+1).'):</b></p>
                                                        '.entityDecode($val['quiz_qns_question'],5).'   
                                                        <div class="p-1">
                                                            <div class="row">';
                                                                foreach ($val['quiz_qns_option'] AS $okey => $oval) {
                                                                    $optionFlag++;
                                                                    echo'
                                                                    <div class="radio col bg-white m-1 border rounded">
                                                                        <input type="radio" id="o_'.$key.'_'.$optionFlag.'" name="'.to_seo_url(strip_tags(entityDecode($val['quiz_qns_question'],5))).'" value="'.($okey).'">
                                                                        <label for="o_'.$key.'_'.$optionFlag.'" style="font-size: 12px;">
                                                                            <span class="radio-label"></span> 
                                                                            '.($okey == 0?'a':($okey == 1?'b':($okey == 2?'c':'d'))).'): '.$oval['qns_option'].'
                                                                        </label>
                                                                    </div>
                                                                    '.(($okey%2) == 1?'</div><div class="row">':'').'';
                                                                }
                                                                echo'
                                                            </div>
                                                        </div>
                                                    </div>';
                                                } else if($val['quiz_qns_type'] == 1) {
                                                    $s_flag++;
                                                    if ($s_flag == 1) {
                                                        echo'
                                                        </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header alert-dark">
                                                                <b>Short Questions</b>
                                                            </div>
                                                            <div class="card-body py-0">';
                                                    }
                                                    echo'
                                                    <div class="p-2 my-3 border rounded bg-light">                                                    
                                                        <p><b>Q '.($key+1).'):</b></p>
                                                        '.entityDecode($val['quiz_qns_question'],5).'
                                                        <div class="p-1">
                                                            <textarea type="text" rows="6" id="textInput" class="form-control text-input" width="100%" name="'.to_seo_url(strip_tags(entityDecode($val['quiz_qns_question'],5))).'"></textarea>
                                                            <p class="text-end" style="font-size: 10px; margin-top: -20px; margin-right: 12px;">
                                                                Words (<span class="word-count">0</span>)
                                                            </p>
                                                        </div>
                                                    </div>';
                                                }
                                            }
                                            echo'
                                            </div>
                                            </div>
                                            <center><button id="endStart" class="btn btn-success w-25"><i class="fa fa-check me-1"></i>Submit Quiz</button></center>
                                            <script>
                                                $("#endStart").on("click",function(){
                                                    var quizFrom = $(".quizFrom").serialize()                                                    
                                                    $.ajax({
                                                         url        : "'.SITE_URL.'include/ajax/get_quiz.php"
                                                        ,method     : "POST"
                                                        ,data       : quizFrom
                                                        ,success    : function() {
                                                            location.reload();
                                                        }
                                                    });
                                                });
                                            </script>';
                                        }
                                        echo'
                                    </div>
                                </div>             
                            </div>
                        </form>';
                    } else {
                        unset($_SESSION['QUIZ_STARTTED']);
                        echo'
                        <div class="lg:px-6 border-b">
                            <div class="lg:py-3 mx-auto">
                                <div class="d-flex flex-row align-items-center">
                                    <h4 class="text-xl font-semibold mb-0"> 
                                        '.(isset($_SESSION['QUIZ_STARTTED'])).moduleName($QUIZ['quiz_title']).'';
                                        // WHEN LESSON IS COMPLETED NEXT BTN
                                        if ($QUIZ['is_completed'] == 2 && $next_id != '') {
                                            echo '<a href="'.SITE_URL.'learn/'.ZONE.'/'.$redirection.'" class="btn btn-sm btn-success">Next '.moduleName($type).': '.$name.'</a>';
                                        }
                                        echo'
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="lg:px-6">
                            <div class="lg:py-3 mx-auto">';    
                                if (!empty($QUIZ['quiz_instruction'])) {
                                    echo'
                                    <h5>Instruction</h5>
                                    <div>'.html_entity_decode(html_entity_decode(html_entity_decode($QUIZ['quiz_instruction']))).'</div>';
                                }
                                if (empty($QUIZ['qzstd_id'])) {
                                    echo'                                    
                                    <h5 class="text-danger">Notice!</h5>
                                    <p class="text-danger fw-bold">Once Your Strat Your Quiz Your Only Pass/Fail It Or Cancel It Your Can Not Skip It Or Your Can Not Do Another Thing.</p>
                                    <button id="quizStart" class="bg-gray-600 font-semibold hover:bg-gray-700 inline-flex items-center justify-center px-4 py-2 rounded-md text-center text-white w-full"> Start Quiz </button>
                                    <script>
                                        $("#quizStart").on("click",function(){
                                            $.ajax({
                                                url        : "'.SITE_URL.'include/ajax/get_quiz.php"
                                                ,method     : "POST"
                                                ,data       : {
                                                                 "id_quiz"      : "'.$QUIZ['quiz_id'].'"
                                                                ,"quiz_method"  : "start"
                                                }
                                                ,success    : function(response) {
                                                    window.location.href = "'.SITE_URL.'learn/'.$zone.'/quiz'.'/'.$QUIZ['quiz_id'].'";
                                                }
                                            });
                                        });
                                    </script>';
                                }
                                echo'
                                <div class="space-y-4">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" colspan="3">Quiz Marks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-light">
                                                <th>Question Type</th>
                                                <th class="text-center" width="150">No of Question</th>
                                                <th class="text-center" width="150">Marks</th>
                                            </tr>';
                                            if ($QUIZ['countMultipleChoice'] && $QUIZ['marksMultipleChoice']) {
                                                echo'
                                                <tr>
                                                    <td>Multiple Choice</td>
                                                    <td class="text-center">'.$QUIZ['countMultipleChoice'].'</td>
                                                    <td class="text-center">'.$QUIZ['marksMultipleChoice'].'</td>
                                                </tr>';
                                            }
                                            if ($QUIZ['countShort'] && $QUIZ['marksShort']) {
                                                echo'
                                                <tr>
                                                    <td>Short Question</td>
                                                    <td class="text-center">'.$QUIZ['countShort'].'</td>
                                                    <td class="text-center">'.$QUIZ['marksShort'].'</td>
                                                </tr>';
                                            }
                                            echo'
                                            <tr class="table-light">
                                                <th colspan="2">Total Marks</th>
                                                <th class="text-center">'.$QUIZ['quiz_totalmarks'].'</th>
                                            </tr>
                                            <tr class="table-light">
                                                <th colspan="2">Total Passing Marks</th>
                                                <th class="text-center">'.$QUIZ['quiz_passingmarks'].'</th>
                                            </tr>';
                                            if(isset($QUIZ['qzstd_pass_fail']) && $QUIZ['qzstd_pass_fail'] == 1){
                                                $bg_color = 'bg-success'; 
                                            } else if(isset($QUIZ['qzstd_pass_fail']) && $QUIZ['qzstd_pass_fail'] == 0){
                                                $bg_color = 'bg-danger'; 
                                            } else {
                                                $bg_color = 'bg-secondary'; 
                                            }
                                            echo'
                                            <tr class="text-white '.$bg_color.'">
                                                <th colspan="2">Obtain Marks</th>
                                                <th class="text-center">'.(!empty($QUIZ['qzstd_obtain_marks']) ? $QUIZ['qzstd_obtain_marks'] : 'Pending').'</th>
                                            </tr>';
                                            if (!empty($QUIZ['qzstd_id'])) {
                                                echo'
                                                <tr class="bg-success">
                                                    <th class="text-center text-white" colspan="3">Quiz Submited</th>
                                                </tr>';
                                            }
                                            echo'
                                        </tbody>
                                    </tbody>
                                </div>
                            </div>
                        </div>';
                    }
                    echo'
                </div>';
            }
            echo'
        </div>';
        include 'include/learnpanel/include/footer.php';
    } else {
        header("location: ".SITE_URL."student/courses");
    }
} else {
    header("location: ".SITE_URL."student/courses");
}
?>