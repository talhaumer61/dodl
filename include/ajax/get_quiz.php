<?php
include '../dbsetting/lms_vars_config.php';
include '../dbsetting/classdbconection.php';
include '../functions/functions.php';
session_start();
$dblms = new dblms();
// METHOD TO START THE QUIZ
if ($_POST['quiz_method'] == 'start') {
    $condition = array(
                         'select'       =>  'quiz_qns_id, quiz_qns_level, quiz_qns_type, quiz_qns_question, quiz_qns_option, quiz_qns_marks'
                        ,'where'        =>  array(  
                                                    'id_quiz'      => cleanvars($_POST['id_quiz'])
                                                )
                        ,'return_type'  =>  'all'
    );
    $QUIZ_QUESTIONS = $dblms->getRows(QUIZ_QUESTIONS, $condition);
    $qnsArray = array();
    $qns_type_flag = false;
    foreach ($QUIZ_QUESTIONS AS $key => $val) {
        $arrayCount = count($qnsArray);
        $qnsArray[$arrayCount]['quiz_qns_id']           = $val['quiz_qns_id'];
        $qnsArray[$arrayCount]['id_quiz']               = $val['id_quiz'];
        $qnsArray[$arrayCount]['quiz_qns_level']        = $val['quiz_qns_level'];
        $qnsArray[$arrayCount]['quiz_qns_type']         = $val['quiz_qns_type'];
        $qnsArray[$arrayCount]['quiz_qns_question']     = $val['quiz_qns_question'];
        if ($val['quiz_qns_type'] == 3) {
            $qnsArray[$arrayCount]['quiz_qns_option']       = json_decode(html_entity_decode($val['quiz_qns_option']),true);
            $qns_type_flag = true;
        }
        $qnsArray[$arrayCount]['quiz_qns_marks']        = $val['quiz_qns_marks'];
    }
    if ($qns_type_flag) {
        $qns_type_flag = 3;
    } else {
        $qns_type_flag = 1;
    }
    $_SESSION['QUIZ_STARTTED']  = $qnsArray;
    $_SESSION['QUIZ_TYPE']      = $qns_type_flag;
}

// METHOD TO CANCEL THE QUIZ
if ($_POST['quiz_method'] == 'cancel') {
    unset($_SESSION['QUIZ_STARTTED']);
}

// METHOD TO END OR COMPLETE THE QUIZ
if ($_POST['quiz_method'] == 'end') {
    $condition	=	array ( 
							'select' 		=>	"qzstd_id"
							,'where' 		=>	array( 
														 'is_deleted'	=>	'0'	
                                                        ,'id_quiz'		=>	cleanvars($_POST['id_quiz'])
														,'id_std'		=>	cleanvars($_SESSION['userlogininfo']['STDID'])
													)		
							,'return_type' 	=>	'count' 
	); 
	if(!$dblms->getRows(QUIZ_STUDENTS, $condition)) {	 
        $values = array(
                             'id_quiz'		        =>	cleanvars($_POST['id_quiz'])
                            ,'id_std'		        =>	cleanvars($_SESSION['userlogininfo']['STDID'])
                            ,'id_added'		        =>	cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                            ,'qzstd_submited_date'	=>	date('Y-m-d G:i:s')
                            ,'date_added'	        =>	date('Y-m-d G:i:s')
        );
        $sqllms	= $dblms->insert(QUIZ_STUDENTS, $values);
        if($sqllms) { 

            $latestID = $dblms->lastestid();

            foreach ($_SESSION['QUIZ_STARTTED'] AS $key => $val) {
                $qns_marks                      = 0;
                $values = array(
                                     'id_quiz_qns' => cleanvars($val['quiz_qns_id'])
                                    ,'id_qzstd' => cleanvars($latestID)
                );
                if ($val['quiz_qns_type'] == 3) {
                    $is_true                    = 0;
                    $option_key                 = '';
                    foreach ($val['quiz_qns_option'] AS $okey => $oval) {
                        $selectedOption         = $_POST[to_seo_url(strip_tags(entityDecode($val['quiz_qns_question'],5)))].'<br>';
                        $option_key             = ($selectedOption == 0?'a':($selectedOption == 1?'b':($selectedOption == 2?'c':'d')));
                        if ($okey == $selectedOption) {
                            if ($oval['option_true'] == 1) {
                                $is_true        = 1;
                                $qns_marks      = $val['quiz_qns_marks'];
                            }   
                        }
                    }
                    $values['option_key']	    = cleanvars($option_key);
                    $values['is_true']		    = cleanvars($is_true);
                } else {
                    $values['qns_answer']	    = cleanvars($_POST[to_seo_url(strip_tags(entityDecode($val['quiz_qns_question'],5)))]);
                }
                $values['qns_marks']	        = cleanvars($qns_marks);

                $sqllms	                        = $dblms->insert(QUIZ_STUDENT_DETAILS, $values);
            }

            if ($_SESSION['QUIZ_TYPE'] == 3) {
                $condition = array(
                                         'select'       =>  'q.quiz_passingmarks, SUM(qsd.qns_marks) AS qns_obtain_marks'
                                        ,'join'         =>  'INNER JOIN '.QUIZ_STUDENT_DETAILS.' AS qsd ON qsd.id_qzstd = qs.qzstd_id
                                                                INNER JOIN '.QUIZ.' AS q ON q.quiz_id = qs.id_quiz'
                                        ,'where'        =>  array(  
                                                                     'qs.is_deleted'    => 0
                                                                    ,'qs.id_quiz'       => cleanvars($_POST['id_quiz'])
                                                                    ,'qs.id_std'        => cleanvars($_SESSION['userlogininfo']['STDID'])
                                                            )
                                        ,'return_type'  =>  'single'
                );
                $QUIZ_STUDENTS = $dblms->getRows(QUIZ_STUDENTS.' AS qs', $condition);
                if ($QUIZ_STUDENTS) {
                    $values     = array( 
                                             'qzstd_obtain_marks'    => $QUIZ_STUDENTS['qns_obtain_marks'] 
                                            ,'qzstd_pass_fail'       => ($QUIZ_STUDENTS['quiz_passingmarks'] <= $QUIZ_STUDENTS['qns_obtain_marks']?'1':'0')
                    );
                    $sqllms = $dblms->Update(QUIZ_STUDENTS, $values, "WHERE id_quiz = '".cleanvars($_POST['id_quiz'])."' AND id_std = '".cleanvars($_SESSION['userlogininfo']['STDID'])."'");
                }
            }

            unset($_SESSION['QUIZ_STARTTED']);
            unset($_SESSION['QUIZ_TYPE']);

            $values = array(
                                 'track_status' => 1
                                ,'is_completed' => 2
                                ,'track_mood'   => 'quiz'
                                ,'id_quiz'	    => $_POST['id_quiz']
                                ,'id_week'	    => $_POST['id_week']
                                ,'id_curs'	    => $_POST['id_curs']
                                ,'id_std'		=> $_SESSION['userlogininfo']['STDID']
                                ,'id_added'		=> $_SESSION['userlogininfo']['LOGINIDA']
                                ,'date_added'	=> date('Y-m-d G:i:s')
            ); 
            $sqllms = $dblms->insert(LECTURE_TRACKING, $values);
        }
    }
}
?>