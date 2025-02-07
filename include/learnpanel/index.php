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
                     'select'       =>  'c.curs_id, c.curs_wise, c.curs_name, c.curs_href, c.curs_code, ec.id_mas, ec.id_ad_prg, ec.id_std, ec.secs_id, ec.id_type
                                        ,COUNT(DISTINCT cl.lesson_id) AS lesson_count
                                        ,COUNT(DISTINCT ca.id) AS assignment_count
                                        ,COUNT(DISTINCT cq.quiz_id) AS quiz_count
                                        ,COUNT(DISTINCT lt.track_id) AS track_count'
                    ,'join'         =>  'INNER JOIN '.COURSES.' AS c ON FIND_IN_SET(c.curs_id,ec.id_curs) AND c.curs_href = "'.cleanvars($zone).'" AND c.curs_status = 1 AND c.is_deleted = 0
                                        LEFT JOIN '.COURSES_LESSONS.' AS cl ON cl.id_curs = c.curs_id AND cl.lesson_status = 1 AND cl.is_deleted = 0
                                        LEFT JOIN '.COURSES_ASSIGNMENTS.' AS ca ON ca.id_curs = c.curs_id AND ca.status = 1 AND ca.is_deleted = 0
                                        LEFT JOIN '.QUIZ.' AS cq ON cq.id_curs = c.curs_id AND cq.quiz_status = 1 AND cq.is_deleted = 0 AND cq.is_publish = 1
                                        LEFT JOIN '.LECTURE_TRACKING.' AS lt ON lt.id_curs = c.curs_id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.is_completed = 2 AND lt.is_deleted = 0 AND lt.id_mas = ec.id_mas AND lt.id_ad_prg = ec.id_ad_prg'
                    ,'where'        =>  array(
                                                 'ec.secs_status'   => 1
                                                ,'ec.is_deleted'    => 0    
                                                ,'ec.secs_id'       => $_SESSION['FLAG']
                                            )   
                    ,'return_type'  =>  'single'
                );
    $COURSES =  $dblms->getRows(ENROLLED_COURSES.' AS ec',$con, $sql);    
    if ($COURSES) {
        // ALREADY GENERATED CERTIFICATE
        $conditions = array ( 
                                 'select'       =>	'gc.cert_id, gc.id_enroll, gc.id_std, gc.std_name, gc.cert_type, gc.cert_name, gc.curs_hours, gc.date_generated'
                                ,'where' 		=>	array( 
                                                             'gc.is_deleted'    => '0'
                                                            ,'gc.id_std' 	    => cleanvars($COURSES['id_std']) 
                                                            ,'gc.id_enroll'     => cleanvars($COURSES['secs_id']) 
                                                        )
                                ,'return_type'	=>	'count'
                            );
        $generateCert = $dblms->getRows(GENERATED_CERTIFICATES.' gc', $conditions);
        if(!$generateCert){
            $Total      = $COURSES['lesson_count'] + $COURSES['assignment_count'] + $COURSES['quiz_count'];
            $Obtain     = $COURSES['track_count'];
            $percent    = (($Obtain / $Total) * 100);
            $percent    = ($percent >= '100' ? '100' : $percent);

            if($percent == '100'){
                // NAME, TYPE
                if ($COURSES['id_type'] == 3){
                    $cert_type  = '3';
                    $type_name  = 'Certificate Course';
                    $cert_name  = $COURSES['curs_name'];
                } else if ($COURSES['id_type'] == 4){
                    $cert_type  = '4';
                    $type_name  = 'e-Training';
                    $cert_name  = $COURSES['curs_name'];
                }

                // IF MAIL SENT ALREADY
                $conditions = array ( 
                                        'select'       =>	'rm.datetime_mailed'
                                        ,'where' 		=>	array( 
                                                                     'rm.id_std'        => cleanvars($COURSES['id_std'])
                                                                    ,'rm.id_enroll'     => cleanvars($COURSES['secs_id'])
                                                                    ,'rm.type'          => 1
                                                                )
                                        ,'order_by'     =>	'rm.datetime_mailed DESC'
                                        ,'return_type'	=>	'single'
                                    );
                $REMINDER_MAILS_LOGS = $dblms->getRows(REMINDER_MAILS_LOGS.' rm', $conditions);
                if(!$REMINDER_MAILS_LOGS){
                    // INSERT IN GENERATED CERTIFICATES
                    $values = array(
                                         'type'             => 1
                                        ,'id_enroll'        => cleanvars($COURSES['secs_id'])
                                        ,'id_std'			=> cleanvars($COURSES['id_std'])
                                        ,'std_name'			=> cleanvars($_SESSION['userlogininfo']['LOGINNAME'])
                                        ,'std_email'        => cleanvars($_SESSION['userlogininfo']['LOGINEMAIL'])
                                        ,'cert_type'        => cleanvars($cert_type)
                                        ,'cert_name'        => cleanvars($cert_name)
                                        ,'datetime_mailed'  => date('Y-m-d G:i:s')
                                    );     
                    $sqlInsert = $dblms->insert(REMINDER_MAILS_LOGS, $values);
                    if($sqlInsert){
                        get_SendMail([
                            'sender'        => SMTP_EMAIL,
                            'senderName'    => SITE_NAME,
                            'receiver'      => $_SESSION['userlogininfo']['LOGINEMAIL'],
                            'receiverName'  => $_SESSION['userlogininfo']['LOGINNAME'],
                            'subject'       => "Congratulations! You have Completed (".$cert_name." - ".$type_name.") on ".TITLE_HEADER."",
                            'body'          => '
                                                <div style="background-color: #f9f9f9; padding: 20px; font-family: Arial, sans-serif; color: #333;">
                                                    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                                                        <h2 style="color: #0056b3; font-weight: bold; margin-top: 20px;">Dear <strong>'.ucwords(strtolower($_SESSION['userlogininfo']['LOGINNAME'])).'</strong>,</h2>
                                                        <p style="font-size: 16px; line-height: 1.8;">
                                                            <span style="color: #28a745; font-weight: bold;">Congratulations</span> on successfully completing <strong>'.$cert_name.' - '.$type_name.'</strong>! We are immensely proud of your hard work and dedication.
                                                        </p>                                        
                                                        <p style="font-size: 16px; line-height: 1.8;">
                                                            Your completion certificate is now available for download.
                                                            <br>
                                                            To access it, kindly share your feedback on your LMS, and you can get your shareable certificate.
                                                        </p>                                        
                                                        <p style="font-size: 16px; line-height: 1.8;">
                                                            We encourage you to share your achievement with your network and continue your learning journey with us.
                                                            <br>
                                                            <strong>Explore more courses</strong> to enhance your skills and grow further.
                                                        </p>                                        
                                                        <p style="font-size: 16px; font-style: italic;">
                                                            Best wishes for your future endeavors!
                                                        </p>                                        
                                                        <p style="color: red; font-weight: bold;">
                                                            Note: Make sure you log-in to your account.
                                                        </p>
                                                        <div style="text-align: center; margin: 20px 0;">
                                                            <a href="'.SITE_URL.'certificate-print/'.$COURSES['secs_id'].'" style="display: inline-block; padding: 15px 25px; background-color: #17a2b8; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px;">
                                                                <i class="fa fa-graduation-cap" style="margin-right: 8px;"></i> Get Your Certificate
                                                            </a>
                                                        </div>                                        
                                                        <p style="font-size: 16px; line-height: 1.8;">
                                                            <b>Warm regards,</b>
                                                            <br>
                                                            Support Team
                                                            <br>
                                                            '.SMTP_EMAIL.'
                                                            <br>
                                                            '.SITE_NAME.' <strong>('.TITLE_HEADER.')</strong>
                                                            <br>
                                                            <b>Minhaj University Lahore</b>
                                                        </p>
                                                    </div>
                                                </div>',
                            'tokken'    => SMTP_TOKEN,
                        ], 'send-mail');
                    }
                } else {
                    $last_mail_date = date('Y-m-d', strtotime($REMINDER_MAILS_LOGS['datetime_mailed']. ' + 15 days'));
                    if(date('Y-m-d') > $last_mail_date){
                        get_SendMail([
                            'sender'        => SMTP_EMAIL,
                            'senderName'    => SITE_NAME,
                            'receiver'      => $_SESSION['userlogininfo']['LOGINEMAIL'],
                            'receiverName'  => $_SESSION['userlogininfo']['LOGINNAME'],
                            'subject'       => "Congratulations! You have Completed (".$cert_name." - ".$type_name.") on ".TITLE_HEADER."",
                            'body'          => '
                                                <div style="background-color: #f9f9f9; padding: 20px; font-family: Arial, sans-serif; color: #333;">
                                                    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                                                        <h2 style="color: #0056b3; font-weight: bold; margin-top: 20px;">Dear <strong>'.ucwords(strtolower($_SESSION['userlogininfo']['LOGINNAME'])).'</strong>,</h2>
                                                        <p style="font-size: 16px; line-height: 1.8;">
                                                            <span style="color: #28a745; font-weight: bold;">Congratulations</span> on successfully completing <strong>'.$cert_name.' - '.$type_name.'</strong>! We are immensely proud of your hard work and dedication.
                                                        </p>                                        
                                                        <p style="font-size: 16px; line-height: 1.8;">
                                                            Your completion certificate is now available for download.
                                                            <br>
                                                            To access it, kindly share your feedback on your LMS, and you can get your shareable certificate.
                                                        </p>                                        
                                                        <p style="font-size: 16px; line-height: 1.8;">
                                                            We encourage you to share your achievement with your network and continue your learning journey with us.
                                                            <br>
                                                            <strong>Explore more courses</strong> to enhance your skills and grow further.
                                                        </p>                                        
                                                        <p style="font-size: 16px; font-style: italic;">
                                                            Best wishes for your future endeavors!
                                                        </p>                                        
                                                        <p style="color: red; font-weight: bold;">
                                                            Note: Make sure you log-in to your account.
                                                        </p>
                                                        <div style="text-align: center; margin: 20px 0;">
                                                            <a href="'.SITE_URL.'certificate-print/'.$COURSES['secs_id'].'" style="display: inline-block; padding: 15px 25px; background-color: #17a2b8; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px;">
                                                                <i class="fa fa-graduation-cap" style="margin-right: 8px;"></i> Get Your Certificate
                                                            </a>
                                                        </div>                                        
                                                        <p style="font-size: 16px; line-height: 1.8;">
                                                            <b>Warm regards,</b>
                                                            <br>
                                                            Support Team
                                                            <br>
                                                            '.SMTP_EMAIL.'
                                                            <br>
                                                            '.SITE_NAME.' <strong>('.TITLE_HEADER.')</strong>
                                                            <br>
                                                            <b>Minhaj University Lahore</b>
                                                        </p>
                                                    </div>
                                                </div>',
                            'tokken'    => SMTP_TOKEN,
                        ], 'send-mail');
                    }
                }
            }
        }
        include 'include/learnpanel/include/sidebar.php';
        include 'include/learnpanel/include/content.php';
        include 'include/learnpanel/include/footer.php';
    } else {
        header("location: ".SITE_URL."student/courses");
    }
} else {
    header("location: ".SITE_URL."student/courses");
}
?>