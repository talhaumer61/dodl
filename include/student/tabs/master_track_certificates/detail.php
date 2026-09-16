<?php
$conditions = array ( 
                         'select'       =>	'c.curs_name, c.curs_photo, c.curs_id, c.curs_href, ec.secs_id, ec.id_mas, ec.id_ad_prg
                                            ,COUNT(DISTINCT cl.lesson_id) AS lesson_count
                                            ,COUNT(DISTINCT ca.id) AS assignment_count
                                            ,COUNT(DISTINCT cq.quiz_id) AS quiz_count
                                            ,COUNT(DISTINCT lt.track_id) AS track_count'
                        ,'join'         =>	'INNER JOIN '.MASTER_TRACK.' m ON m.mas_id = ec.id_mas AND m.mas_href = "'.cleanvars($view).'"
                                             INNER JOIN '.COURSES.' c ON FIND_IN_SET(c.curs_id, ec.id_curs)
                                             LEFT JOIN '.COURSES_LESSONS.' AS cl ON cl.id_curs = c.curs_id AND cl.lesson_status = 1 AND cl.is_deleted = 0
                                             LEFT JOIN '.COURSES_ASSIGNMENTS.' AS ca ON ca.id_curs = c.curs_id AND ca.status = 1 AND ca.is_deleted = 0
                                             LEFT JOIN '.QUIZ.' AS cq ON cq.id_curs = c.curs_id AND cq.quiz_status = 1 AND cq.is_deleted = 0 AND cq.is_publish = 1
                                             LEFT JOIN '.LECTURE_TRACKING.' AS lt ON lt.id_curs = c.curs_id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.is_completed = 2 AND lt.is_deleted = 0 AND lt.id_mas = ec.id_mas AND lt.id_ad_prg = ec.id_ad_prg' 
                        ,'where' 		=>	array(      
                                                     'ec.is_deleted'    =>  '0'
                                                    ,'ec.secs_status'   =>  '1' 
                                                    ,'ec.id_ad_prg'     =>  '0'
                                                    ,'ec.id_std' 	    =>  cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                )
                        ,'group_by'	    =>	'c.curs_id'
                        ,'return_type'	=>	'all'
                    ); 
$ENROLLED_COURSES = $dblms->getRows(ENROLLED_COURSES.' ec', $conditions);
echo'
<div class="settings-widget">
    <div class="settings-inner-blk p-0">
        <div class="comman-space pb-0">
            <div class="notify-sec">
                <h5 class="mb-3"><span class="text-dark">Courses in:</span> '.moduleName($view).'</h5>
            </div>
            <div class="row">';
                if($ENROLLED_COURSES){
                    foreach ($ENROLLED_COURSES as $course) {
                        // CHECK FILE EXIST
                        $photo      = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                        $file_url   = SITE_URL_PORTAL.'uploads/images/courses/'.$course['curs_photo'];
                        if (check_file_exists($file_url)) {
                            $photo = $file_url;
                        }

                        $arrays = array();
                        // OPEN LESSON
                        $con    = array ( 
                                             'select'   =>	'cl.lesson_id as id, cl.id_week, lt.is_completed'
                                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON ( lt.id_curs = '.$course['curs_id'].' AND lt.id_lecture = cl.lesson_id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$course['id_mas'].' AND lt.id_ad_prg = '.$course['id_ad_prg'].')' 
                                            ,'where' 	=>	array( 
                                                                     'cl.lesson_status' => 1
                                                                    ,'cl.is_deleted'    => 0
                                                                    ,'cl.id_curs' 	    => cleanvars($course['curs_id']) 
                                                            ) 
                                            ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                                            ,'order_by'     =>	'cl.id_week ASC LIMIT 1'
                                            ,'return_type'  =>	'single'
                                        ); 
                        $COURSES_LESSONS = $dblms->getRows(COURSES_LESSONS.' cl', $con);
                        if($COURSES_LESSONS){
                            array_push($COURSES_LESSONS, 'lesson');
                            array_push($arrays, $COURSES_LESSONS);
                        }

                        // OPEN ASSIGNMENT
                        $con    = array ( 
                                             'select'   =>	'ca.id as id, ca.id_week, lt.is_completed'
                                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.cleanvars($course['curs_id']).' AND lt.id_assignment = ca.id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$course['id_mas'].' AND lt.id_ad_prg = '.$course['id_ad_prg'].')' 
                                            ,'where' 	=>	array( 
                                                                     'ca.status'        => 1
                                                                    ,'ca.is_deleted'    => 0
                                                                    ,'ca.id_curs' 	    => cleanvars($course['curs_id']) 
                                                                ) 
                                            ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                                            ,'order_by'     =>	'ca.id_week ASC LIMIT 1'
                                            ,'return_type'  =>	'single'
                                        ); 
                        $COURSES_ASSIGNMENTS = $dblms->getRows(COURSES_ASSIGNMENTS.' ca', $con);
                        if($COURSES_ASSIGNMENTS){
                            array_push($COURSES_ASSIGNMENTS, 'assignments');
                            array_push($arrays, $COURSES_ASSIGNMENTS);
                        }

                        // OPEN QUIZ
                        $con    = array ( 
                                             'select'   =>	'q.quiz_id as id, q.id_week, lt.is_completed'
                                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' lt ON (lt.id_quiz = q.quiz_id AND lt.id_curs = '.$course['curs_id'].' AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$course['id_mas'].' AND lt.id_ad_prg = '.$course['id_ad_prg'].')'
                                            ,'where' 	=>	array( 
                                                                     'q.quiz_status'    => 1
                                                                    ,'q.is_deleted'     => 0
                                                                    ,'q.is_publish'     => 1
                                                                    ,'q.id_curs'        => cleanvars($course['curs_id']) 
                                                            ) 
                                            ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                                            ,'order_by'     =>	'q.id_week ASC, q.quiz_id ASC'
                                            ,'return_type'  =>	'single'
                                        ); 
                        $QUIZ = $dblms->getRows(QUIZ.' q', $con, $sql);
                        if($QUIZ){
                            array_push($QUIZ, 'quiz');
                            array_push($arrays, $QUIZ);
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
                            $redirection = end($arrays[$minIdWeekIndex]).'/'.$arrays[$minIdWeekIndex]['id'];
                        }else{ 
                            $redirection = 'lesson/0';
                        }

                        // PERCENTAGE
                        $Total  = $course['lesson_count']+$course['assignment_count']+$course['quiz_count'];
                        $Obtain = $course['track_count'];
                        $pecent = (($Obtain/$Total)*100);
                        $percent = ($pecent >= '100' ? '100' : $pecent);

                        echo'
                        <div class="col-xl-3 col-lg-4 col-md-6 d-flex">
                            <div class="course-box shadow course-design d-flex">
                                <div class="product">
                                    <div class="product-img">
                                        <a href="'.SITE_URL.'learn/'.$course['curs_href'].'/'.$redirection.'/'.$course['secs_id'].'">
                                            <img class="img-fluid" alt src="'.$photo.'" />
                                        </a>
                                    </div>
                                    <div class="product-content">
                                        <h3 class="title">
                                            <a href="'.SITE_URL.'learn/'.$course['curs_href'].'/'.$redirection.'/'.$course['secs_id'].'">'.$course['curs_name'].'</a>
                                        </h3>
                                        <div class="rating-student">
                                            <div class="rating">
                                                <i class="fas fa-star filled"></i>
                                                <i class="fas fa-star filled"></i>
                                                <i class="fas fa-star filled"></i>
                                                <i class="fas fa-star filled"></i>
                                                <i class="fas fa-star"></i>
                                                <span class="d-inline-block average-rating"><span>4.0</span></span>
                                            </div>
                                            <div class="edit-rate">
                                                <a href="javascript:;">Rating</a>
                                            </div>
                                        </div>
                                        <div class="progress-stip">
                                            <div class="progress-bar bg-success progress-bar-striped active-stip" style="width: '.intval($pecent).'% !important"></div>
                                        </div>
                                        <div class="student-percent">
                                            <p>'.intval($percent).'% Completed</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                }else {
                    echo '<h4 class="pb-3" align="center">No Course Found</h4>';
                }
                echo'
            </div>
        </div>
    </div>
</div>';
?>