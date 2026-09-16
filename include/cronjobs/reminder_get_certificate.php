<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();

// ENROLLMENTS
$conditions = array ( 
                         'select'       =>	'c.curs_name, mt.mas_name ,p.prg_name
                                            ,ec.secs_id, ec.id_std, ec.id_curs, ec.id_mas, ec.id_ad_prg
                                            ,a.adm_fullname, a.adm_email
                                            ,COUNT(DISTINCT cl.lesson_id) AS lesson_count
                                            ,COUNT(DISTINCT ca.id) AS assignment_count
                                            ,COUNT(DISTINCT cq.quiz_id) AS quiz_count
                                            ,COUNT(DISTINCT lt.track_id) AS track_count'
                        ,'join'         =>	'INNER JOIN '.CHALLANS.' ch ON FIND_IN_SET(ec.secs_id, ch.id_enroll) AND ch.is_deleted = 0
                                             INNER JOIN '.STUDENTS.' s ON s.std_id = ec.id_std AND s.is_deleted = 0 AND s.std_status = 1
                                             INNER JOIN '.ADMINS.' a ON a.adm_id = s.std_loginid AND a.is_deleted = 0 AND a.adm_status = 1
                                             LEFT JOIN '.COURSES.' c ON c.curs_id = ec.id_curs AND c.curs_status = 1 AND c.is_deleted = 0
                                             LEFT JOIN '.MASTER_TRACK.' mt ON mt.mas_id = ec.id_mas AND mt.mas_status = 1 AND mt.is_deleted = 0
                                             LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ec.id_ad_prg
                                             LEFT JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg                                             
                                             LEFT JOIN '.COURSES_LESSONS.' AS cl ON FIND_IN_SET(cl.id_curs, ec.id_curs) AND cl.lesson_status = 1 AND cl.is_deleted = 0
                                             LEFT JOIN '.COURSES_ASSIGNMENTS.' AS ca ON FIND_IN_SET(ca.id_curs, ec.id_curs) AND ca.status = 1 AND ca.is_deleted = 0
                                             LEFT JOIN '.QUIZ.' AS cq ON FIND_IN_SET(cq.id_curs, ec.id_curs) AND cq.quiz_status = 1 AND cq.is_deleted = 0 AND cq.is_publish = 1
                                             LEFT JOIN '.LECTURE_TRACKING.' AS lt ON FIND_IN_SET(lt.id_curs, ec.id_curs) AND lt.id_std = ec.id_std AND lt.is_completed = 2 AND lt.is_deleted = 0 AND lt.id_mas = ec.id_mas AND lt.id_ad_prg = ec.id_ad_prg'
                        ,'where' 		=>	array( 
                                                     'ec.is_deleted'    =>  '0'
                                                    ,'ec.secs_status'   =>  '1'
                                                )
                        ,'search_by'    =>	' AND (COALESCE(c.curs_name, "") != "" OR COALESCE(mt.mas_name, "") != "" OR COALESCE(ap.program, "") != "")'
                        ,'group_by'     =>	'ec.secs_id'
                        ,'return_type'	=>	'all'
                    );
$ENROLLED_COURSES = $dblms->getRows(ENROLLED_COURSES.' ec', $conditions, $sql);

foreach ($ENROLLED_COURSES as $keyCourse => $valCourse) {
    // ALREADY GENERATED CERTIFICATE
    $conditions = array ( 
                             'select'       =>	'gc.cert_id, gc.id_enroll, gc.id_std, gc.std_name, gc.cert_type, gc.cert_name, gc.curs_hours, gc.date_generated'
                            ,'where' 		=>	array( 
                                                         'gc.is_deleted'    => '0'
                                                        ,'gc.id_std' 	    => cleanvars($valCourse['id_std']) 
                                                        ,'gc.id_enroll'     => cleanvars($valCourse['secs_id']) 
                                                    )
                            ,'return_type'	=>	'count'
                        );
    $generateCourse = $dblms->getRows(GENERATED_CERTIFICATES.' gc', $conditions);
    
    if(!$generateCourse){
        $Total      = $valCourse['lesson_count'] + $valCourse['assignment_count'] + $valCourse['quiz_count'];
        $Obtain     = $valCourse['track_count'];
        $percent    = (($Obtain / $Total) * 100);
        $percent    = ($percent >= '100' ? '100' : $percent);
    
        if($percent == '100'){
            // NAME, TYPE
            if ($valCourse['prg_name']){
                $cert_type  = '1';
                $type_name  = 'Degree';
                $cert_name  = $valCourse['prg_name'];
            } elseif ($valCourse['mas_name']){
                $cert_type  = '2';
                $type_name  = 'MasterTrack';
                $cert_name  = $valCourse['mas_name'];
            } elseif ($valCourse['curs_name']){
                $cert_type  = '3';
                $type_name  = 'Certificate Course';
                $cert_name  = $valCourse['curs_name'];
            }

            // IF MAIL SENT ALREADY
            $conditions = array ( 
                                     'select'       =>	'rm.datetime_mailed'
                                    ,'where' 		=>	array( 
                                                                 'rm.id_std'        => cleanvars($valCourse['id_std'])
                                                                ,'rm.id_enroll'     => cleanvars($valCourse['secs_id'])
                                                                ,'rm.type'          => 1
                                                            )
                                    ,'order_by'     =>	'rm.datetime_mailed DESC'
                                    ,'return_type'	=>	'single'
                                );
            $REMINDER_MAILS_LOGS = $dblms->getRows(REMINDER_MAILS_LOGS.' rm', $conditions);
            
            if(!$REMINDER_MAILS_LOGS){
                // INSERT IN MAILS LOGS
                $values = array(
                                     'type'             => 1
                                    ,'id_enroll'        => cleanvars($valCourse['secs_id'])
                                    ,'id_std'			=> cleanvars($valCourse['id_std'])
                                    ,'std_name'			=> cleanvars($valCourse['adm_fullname'])
                                    ,'std_email'        => cleanvars($valCourse['adm_email'])
                                    ,'cert_type'        => cleanvars($cert_type)
                                    ,'cert_name'        => cleanvars($cert_name)
                                    ,'datetime_mailed'  => date('Y-m-d G:i:s')
                                );     
                $sqlInsert = $dblms->insert(REMINDER_MAILS_LOGS, $values);
                if($sqlInsert){
                    get_SendMail([
                        'sender'        => SMTP_EMAIL,
                        'senderName'    => SITE_NAME,
                        'receiver'      => $valCourse['adm_email'],
                        'receiverName'  => $valCourse['adm_fullname'],
                        'subject'       => "Congratulations! You have Completed (".$cert_name." - ".$type_name.") on ".TITLE_HEADER."",
                        'body'          => '
                                            <div style="background-color: #f9f9f9; padding: 20px; font-family: Arial, sans-serif; color: #333;">
                                                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                                                    <h2 style="color: #0056b3; font-weight: bold; margin-top: 20px;">Dear <strong>'.ucwords(strtolower($valCourse['adm_fullname'])).'</strong>,</h2>
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
                                                        <a href="'.SITE_URL.'certificate-print/'.$valCourse['secs_id'].'" style="display: inline-block; padding: 15px 25px; background-color: #17a2b8; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px;">
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
                    // INSERT IN MAILS LOGS
                    $values = array(
                                        'type'             => 1
                                        ,'id_enroll'        => cleanvars($valCourse['secs_id'])
                                        ,'id_std'			=> cleanvars($valCourse['id_std'])
                                        ,'std_name'			=> cleanvars($valCourse['adm_fullname'])
                                        ,'std_email'        => cleanvars($valCourse['adm_email'])
                                        ,'cert_type'        => cleanvars($cert_type)
                                        ,'cert_name'        => cleanvars($cert_name)
                                        ,'datetime_mailed'  => date('Y-m-d G:i:s')
                                    );     
                    $sqlInsert = $dblms->insert(REMINDER_MAILS_LOGS, $values);
                    if($sqlInsert){
                        get_SendMail([
                            'sender'        => SMTP_EMAIL,
                            'senderName'    => SITE_NAME,
                            'receiver'      => $valCourse['adm_email'],
                            'receiverName'  => $valCourse['adm_fullname'],
                            'subject'       => "Congratulations! You have Completed (".$cert_name." - ".$type_name.") on ".TITLE_HEADER."",
                            'body'          => '
                                                <div style="background-color: #f9f9f9; padding: 20px; font-family: Arial, sans-serif; color: #333;">
                                                    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                                                        <h2 style="color: #0056b3; font-weight: bold; margin-top: 20px;">Dear <strong>'.ucwords(strtolower($valCourse['adm_fullname'])).'</strong>,</h2>
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
                                                            <a href="'.SITE_URL.'certificate-print/'.$valCourse['secs_id'].'" style="display: inline-block; padding: 15px 25px; background-color: #17a2b8; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px;">
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
    }
}
?>