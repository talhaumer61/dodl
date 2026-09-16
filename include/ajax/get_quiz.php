<?php
include '../dbsetting/lms_vars_config.php';
include '../dbsetting/classdbconection.php';
include '../functions/functions.php';
session_start();
$dblms = new dblms();

// START QUIZ: Initialize index and session
if ($_POST['quiz_method'] == 'start') {
    $id_quiz = cleanvars($_POST['id_quiz']);
    $id_std  = $_SESSION['userlogininfo']['STDID'];

    $condition = array(
        'select' => 'quiz_qns_id, quiz_qns_level, quiz_qns_type, quiz_qns_question, quiz_qns_option, quiz_qns_marks',
        'where'  => array('id_quiz' => $id_quiz),
        'return_type' => 'all'
    );
    $QUIZ_QUESTIONS = $dblms->getRows(QUIZ_QUESTIONS, $condition);
    
    $_SESSION['QUIZ_STARTTED']   = $QUIZ_QUESTIONS;
    $_SESSION['CURRENT_Q_IDX']   = 0; 
    $_SESSION['QUIZ_ATTEMPT_ID'] = null; 

    // --- TRACKING LOGIC: START ---
    // Check if tracking entry exists
    $checkTrack = $dblms->getRows(LECTURE_TRACKING, array(
        'select' => 'track_id, is_completed, quiz_attempts',
        'where' => array(
            'id_quiz'       => $id_quiz,
            'id_std'        => $id_std,
            'is_deleted'    => 0
        ),
        'return_type' => 'single'
    ));

    if ($checkTrack) {
        // Update to 1 (In Progress / Attempt started)
        // $dblms->querylms("UPDATE ".LECTURE_TRACKING." SET quiz_attempts = quiz_attempts + 1 WHERE track_id = '".$checkTrack['track_id']."'");
    } else {
        // Insert new entry with is_completed = 1
        $insTrack = array(
            'id_quiz'      => $id_quiz,
            'id_std'       => $id_std,
            'id_curs'      => cleanvars($_POST['id_curs']), // Ensure these are passed in AJAX data
            'id_week'      => cleanvars($_POST['id_week']),
            'is_completed' => 1,
            'track_status'   => 1,
            'quiz_attempts' => 0,
            'date_added'   => date('Y-m-d G:i:s')
        );
        $dblms->insert(LECTURE_TRACKING, $insTrack);
    }
    // --- TRACKING LOGIC: END ---
    echo "started";
}

// SAVE SINGLE QUESTION & MOVE TO NEXT
if ($_POST['quiz_method'] == 'save_question') {
    $idx = $_SESSION['CURRENT_Q_IDX'];
    $q_data = $_SESSION['QUIZ_STARTTED'][$idx];
    $is_last = ($idx == count($_SESSION['QUIZ_STARTTED']) - 1);

    if (empty($_SESSION['QUIZ_ATTEMPT_ID'])) {
        $attempt_values = array(
            'id_quiz'             => cleanvars($_POST['id_quiz']),
            'id_std'              => cleanvars($_SESSION['userlogininfo']['STDID']),
            'id_added'            => cleanvars($_SESSION['userlogininfo']['LOGINIDA']),
            'qzstd_submited_date' => date('Y-m-d G:i:s'),
            'date_added'          => date('Y-m-d G:i:s')
        );
        $dblms->insert(QUIZ_STUDENTS, $attempt_values);
        $_SESSION['QUIZ_ATTEMPT_ID'] = $dblms->lastestid();
    }

    $qzstd_id = $_SESSION['QUIZ_ATTEMPT_ID'];
    $qns_marks = 0;
    $is_true = 0;
    $option_key = '';
    $answer = '';

    if ($q_data['quiz_qns_type'] == 3) { // MCQ
        if (isset($_POST['answer'])) {
            $selected = $_POST['answer'];
            $options = json_decode(html_entity_decode($q_data['quiz_qns_option']), true);
            $option_key = ($selected == 0 ? 'a' : ($selected == 1 ? 'b' : ($selected == 2 ? 'c' : 'd')));
            if ($options[$selected]['option_true'] == 1) {
                $is_true = 1;
                $qns_marks = $q_data['quiz_qns_marks'];
            }
        }
    } else { 
        $answer = isset($_POST['answer']) ? cleanvars($_POST['answer']) : '';
    }

    $detail_values = array(
        'id_quiz_qns' => $q_data['quiz_qns_id'],
        'id_qzstd'    => $qzstd_id,
        'option_key'  => $option_key,
        'is_true'     => $is_true,
        'qns_answer'  => $answer,
        'qns_marks'   => $qns_marks
    );
    $dblms->insert(QUIZ_STUDENT_DETAILS, $detail_values);

    if ($is_last) {
        $sum_sql = "SELECT SUM(qns_marks) as total FROM ".QUIZ_STUDENT_DETAILS." WHERE id_qzstd = $qzstd_id";
        $res = $dblms->querylms($sum_sql);
        $row = $res->fetch_assoc();
        $total_marks = $row['total'];
        
        $quiz_info = $dblms->getRows(QUIZ, array('where'=>array('quiz_id'=>$_POST['id_quiz']), 'return_type'=>'single'));
        $pass_fail = ($total_marks >= $quiz_info['quiz_passingmarks']) ? 1 : 0;

        $dblms->update(QUIZ_STUDENTS, array(
            'qzstd_obtain_marks' => $total_marks,
            'qzstd_pass_fail'    => $pass_fail
        ), "WHERE qzstd_id = $qzstd_id");

        // --- UPDATE TRACKING ON FINISH ---
        // Change to 2 if pass_fail is 1 (passed), else keep at 1 (or set to 0/failed as per your preference)
        $status_to_update = ($pass_fail == 1) ? 2 : 1; 

        $dblms->querylms("UPDATE ".LECTURE_TRACKING." 
                         SET is_completed = ".$status_to_update.", 
                             quiz_attempts = quiz_attempts + 1 
                         WHERE id_quiz = ".cleanvars($_POST['id_quiz'])." 
                         AND is_deleted = 0
                         AND id_std = ".$_SESSION['userlogininfo']['STDID']);

        unset($_SESSION['QUIZ_STARTTED'], $_SESSION['CURRENT_Q_IDX'], $_SESSION['QUIZ_ATTEMPT_ID']);
        echo "finished";
    } else {
        $_SESSION['CURRENT_Q_IDX']++;
        echo "next";
    }
}

if ($_POST['quiz_method'] == 'cancel') {
    $id_quiz = cleanvars($_POST['id_quiz']);
    $id_std  = $_SESSION['userlogininfo']['STDID'];
    $qzstd_id = $_SESSION['QUIZ_ATTEMPT_ID'];

    $dblms->querylms("UPDATE ".LECTURE_TRACKING." 
                     SET quiz_attempts = quiz_attempts + 1 
                     WHERE id_quiz = '".$id_quiz."' 
                     AND id_std = '".$id_std."'
                     AND is_deleted = 0");

    if (!empty($qzstd_id)) {
        $dblms->querylms("DELETE FROM ".QUIZ_STUDENT_DETAILS." WHERE id_qzstd = '".$qzstd_id."'");
        
        $dblms->querylms("DELETE FROM ".QUIZ_STUDENTS." WHERE qzstd_id = '".$qzstd_id."'");
    }

    unset($_SESSION['QUIZ_STARTTED'], $_SESSION['CURRENT_Q_IDX'], $_SESSION['QUIZ_ATTEMPT_ID']);
    echo "cancelled";
}
?>