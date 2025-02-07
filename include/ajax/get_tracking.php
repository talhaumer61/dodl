<?php
include '../dbsetting/lms_vars_config.php';
include '../dbsetting/classdbconection.php';
include '../functions/functions.php';
session_start();
$dblms = new dblms();
if ($_POST['track_mood'] === 'playing') {
    $con    = array(
                         'select'       => 'track_id,is_completed'
                        ,'where'        => array(
                                                     'id_video'     => $_POST['id_video']
                                                    ,'id_lecture'   => $_POST['id_lecture']
                                                    ,'id_curs'      => $_POST['id_curs']
                                                    ,'id_mas'       => $_POST['id_mas']
                                                    ,'id_ad_prg'    => $_POST['id_ad_prg']
                                                    ,'id_std'       => $_SESSION['userlogininfo']['STDID']
                                                    ,'is_deleted'   => 0
                                            )
                        ,'return_type'  => 'single'
    );
    $chk    = $dblms->getRows(LECTURE_TRACKING,$con);
    if ($chk) {
        if ($chk['is_completed'] == 1) {
            $_SESSION['TRACK_ID'] = $chk['track_id'];
            echo'track_already_added';
        } else {
            echo'track_already_added_dont_track';
        }
    } else {
        $values = array(
                         'track_status'     =>	1
                        ,'is_completed'		=>	1
                        ,'id_week'		    =>	$_POST['id_week']
                        ,'track_mood'		=>	$_POST['track_mood']
                        ,'id_std'		    =>	$_SESSION['userlogininfo']['STDID']
                        ,'id_week'	        =>	$_POST['id_week']
                        ,'id_lecture'	    =>	$_POST['id_lecture']
                        ,'id_curs'	        =>	$_POST['id_curs']
                        ,'id_mas'           =>  $_POST['id_mas']
                        ,'id_ad_prg'        =>  $_POST['id_ad_prg']
                        ,'id_video'		    =>	$_POST['id_video']
                        ,'video_duration'	=>	$_POST['video_duration']
                        ,'id_added'		    =>	$_SESSION['userlogininfo']['LOGINIDA']
                        ,'date_added'	    =>	date('Y-m-d G:i:s')
        ); 
        $sqllms	    =	$dblms->insert(LECTURE_TRACKING, $values);
        if ($sqllms) {
            $latestID = $dblms->lastestid();
            $_SESSION['TRACK_ID'] = $latestID;
            echo'track_added';
        }
    }
}
if ($_POST['track_mood'] === 'completed') {
    $values = array(
                     'is_completed' =>	2
                    ,'track_mood'	=>	$_POST['track_mood']
                    ,'id_modify'	=>	$_SESSION['userlogininfo']['LOGINIDA']
                    ,'date_modify'	=>	date('Y-m-d G:i:s')
    ); 
    $sqllms     =   $dblms->Update(LECTURE_TRACKING, $values , 'WHERE track_id  = '.$_SESSION['TRACK_ID'].'');
    if ($sqllms) {
        $latestID = $_SESSION['TRACK_ID'];

        $arrays = array();
        // OPEN LESSON
        $con    = array ( 
                             'select'   =>	'cl.lesson_id as id, cl.lesson_topic as title, cl.id_week, lt.is_completed'
                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON ( lt.id_curs = '.$_POST['id_curs'].' AND lt.id_lecture = cl.lesson_id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$_POST['id_mas'].' AND lt.id_ad_prg = '.$_POST['id_ad_prg'].')' 
                            ,'where' 	=>	array( 
                                                     'cl.lesson_status' => 1
                                                    ,'cl.is_deleted'    => 0
                                                    ,'cl.id_curs' 	    => cleanvars($_POST['id_curs']) 
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
                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.cleanvars($_POST['id_curs']).' AND lt.id_assignment = ca.id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$_POST['id_mas'].' AND lt.id_ad_prg = '.$_POST['id_ad_prg'].')' 
                            ,'where' 	=>	array( 
                                                     'ca.status'        => 1
                                                    ,'ca.is_deleted'    => 0
                                                    ,'ca.id_curs' 	    => cleanvars($_POST['id_curs']) 
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
                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' lt ON (lt.id_quiz = q.quiz_id AND lt.id_curs = '.$_POST['id_curs'].' AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$_POST['id_mas'].' AND lt.id_ad_prg = '.$_POST['id_ad_prg'].')'
                            ,'where' 	=>	array( 
                                                     'q.quiz_status'    => 1
                                                    ,'q.is_deleted'     => 0
                                                    ,'q.is_publish'     => 1
                                                    ,'q.id_curs'        => cleanvars($_POST['id_curs']) 
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
            $redirection = 'lesson/0';
        }
        echo '<a href="'.SITE_URL.'learn/'.$_POST['curs_href'].'/'.$redirection.'" class="btn btn-sm btn-success">NEXT '.moduleName($type).': '.html_entity_decode($name).'</a>';
    }
}
if ($_POST['track_mood'] === 'reading_metrail') {
    $con    = array(
                         'select'       => 'track_id,is_completed'
                        ,'where'        => array(
                                                     'id_lecture'   => $_POST['id_lecture']
                                                    ,'is_completed' => 2
                                                    ,'id_curs'      => $_POST['id_curs']
                                                    ,'id_mas'       => $_POST['id_mas']
                                                    ,'id_ad_prg'    => $_POST['id_ad_prg']
                                                    ,'id_std'       => $_SESSION['userlogininfo']['STDID']
                                                    ,'is_deleted'   => 0
                                            )
                        ,'return_type'  => 'count'
    );
    $chk    = $dblms->getRows(LECTURE_TRACKING,$con);
    if (!$chk) {
        $values = array(
                         'track_status'     =>	1
                        ,'is_completed'		=>	2
                        ,'id_week'		    =>	$_POST['id_week']
                        ,'track_mood'		=>	$_POST['track_mood']
                        ,'id_std'		    =>	$_SESSION['userlogininfo']['STDID']
                        ,'id_week'	        =>	$_POST['id_week']
                        ,'id_lecture'	    =>	$_POST['id_lecture']
                        ,'id_curs'	        =>	$_POST['id_curs']
                        ,'id_mas'           =>  $_POST['id_mas']
                        ,'id_ad_prg'        =>  $_POST['id_ad_prg']
                        ,'id_added'		    =>	$_SESSION['userlogininfo']['LOGINIDA']
                        ,'date_added'	    =>	date('Y-m-d G:i:s')
        ); 
        $sqllms	    =	$dblms->insert(LECTURE_TRACKING, $values);
        if ($sqllms) {
            $arrays = array();
            // OPEN LESSON
            $con    = array ( 
                                'select'   =>	'cl.lesson_id as id, cl.lesson_topic as title, cl.id_week, lt.is_completed'
                                ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON ( lt.id_curs = '.$_POST['id_curs'].' AND lt.id_lecture = cl.lesson_id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$_POST['id_mas'].' AND lt.id_ad_prg = '.$_POST['id_ad_prg'].')' 
                                ,'where' 	=>	array( 
                                                        'cl.lesson_status' => 1
                                                        ,'cl.is_deleted'    => 0
                                                        ,'cl.id_curs' 	    => cleanvars($_POST['id_curs']) 
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
                                ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.cleanvars($_POST['id_curs']).' AND lt.id_assignment = ca.id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$_POST['id_mas'].' AND lt.id_ad_prg = '.$_POST['id_ad_prg'].')' 
                                ,'where' 	=>	array( 
                                                        'ca.status'        => 1
                                                        ,'ca.is_deleted'    => 0
                                                        ,'ca.id_curs' 	    => cleanvars($_POST['id_curs']) 
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
                                ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' lt ON (lt.id_quiz = q.quiz_id AND lt.id_curs = '.$_POST['id_curs'].' AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$_POST['id_mas'].' AND lt.id_ad_prg = '.$_POST['id_ad_prg'].')'
                                ,'where' 	=>	array( 
                                                        'q.quiz_status'    => 1
                                                        ,'q.is_deleted'     => 0
                                                        ,'q.is_publish'     => 1
                                                        ,'q.id_curs'        => cleanvars($_POST['id_curs']) 
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
                $redirection = 'lesson/0';
            }
            echo '<a href="'.SITE_URL.'learn/'.$_POST['curs_href'].'/'.$redirection.'" class="btn btn-sm btn-success">Next '.moduleName($type).': '.$name.'</a>';
        }   
    } else {
        echo'reading_metrail_completed';
    }
}
if ($_POST['track_mood'] === 'my_note_pad_saved') {
    $values = array(
                        'my_note_pad' => cleanvars($_POST['myNote'])
                    ); 
    $sqllms = $dblms->Update(LECTURE_TRACKING, $values , 'WHERE id_week = '.cleanvars($_POST['id_week']).' AND id_lecture = '.cleanvars($_POST['id_lecture']).' AND id_curs = '.cleanvars($_POST['id_curs']).' AND id_mas = '.cleanvars($_POST['id_mas']).' AND id_ad_prg = '.cleanvars($_POST['id_ad_prg']).' AND id_std = '.cleanvars($_POST['id_std']).'');
    if ($sqllms) {
        echo 'myNotePadSaved';
    }
}
if ($_POST['track_mood'] === 'std_review_saved') {
    $values = array(
                        'std_review' => cleanvars($_POST['std_review'])
                    ); 
    $sqllms = $dblms->Update(LECTURE_TRACKING, $values , 'WHERE id_week = '.cleanvars($_POST['id_week']).' AND id_lecture = '.cleanvars($_POST['id_lecture']).' AND id_curs = '.cleanvars($_POST['id_curs']).' AND id_mas = '.cleanvars($_POST['id_mas']).' AND id_ad_prg = '.cleanvars($_POST['id_ad_prg']).' AND id_std = '.cleanvars($_POST['id_std']).'');
    if ($sqllms) {
        echo 'ReviewSaved';
    }
}
if ($_POST['_method'] === 'student_teacher_qna') {
    if($_POST['std_message'] != ''){        
        $values = array(
                             'status'           =>  1
                            ,'type'             =>  1
                            ,'read_status'      =>  2
                            ,'id_user'          =>  cleanvars($_POST['id_std'])
                            ,'id_curs'	        =>  cleanvars($_POST['id_curs'])
                            ,'id_lecture'	    =>  cleanvars($_POST['id_lecture'])
                            ,'message'          =>  cleanvars($_POST['std_message'])
                            ,'ip_user'          =>  cleanvars(LMS_IP)
                            ,'datetime_sent'    =>  date('Y-m-d G:i:s')
                            ,'id_added'         =>  cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                            ,'date_added'       =>  date('Y-m-d G:i:s')
                        ); 
        $sqllms = $dblms->insert(QUESTION_ANSWERS, $values);
        if($sqllms){
		    $latestID = $dblms->lastestid();
            $values = array (
                                 'id_user'		=>	cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                ,'id_record'	=>	cleanvars($latestID)
                                ,'filename'		=>	cleanvars(CONTROLER)
                                ,'action'		=>	1
                                ,'dated'		=>	date('Y-m-d G:i:s')
                                ,'ip'			=>	cleanvars(LMS_IP)
                                ,'remarks'		=>	"Message Sent"
                                ,'id_campus'	=>	cleanvars($_SESSION['userlogininfo']['LOGINCAMPUS'])
                            );
            $sqlRemarks = $dblms->insert(LOGS, $values);            
            echo'
            <div class="message">
                <div class="alert-success sender">
                    '.$_POST['std_message'].'
                </div>
                <div class="message-time-right">'.date('d M, Y h:i A').' - '.get_msg_status(1).'</div>
            </div>';
        }
    }
}
?>