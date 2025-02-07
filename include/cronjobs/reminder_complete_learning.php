<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();

// ALREADY GENERATED CERTIFICATE
$conditions = array ( 
                         'select'       =>	'GROUP_CONCAT(gc.id_enroll) as generatedCertId'
                        ,'where' 		=>	array( 
                                                     'gc.is_deleted'    => '0'
                                                )
                        ,'return_type'	=>	'single'
                    );
$generateCert = $dblms->getRows(GENERATED_CERTIFICATES.' gc', $conditions);

// ENROLLMENTS
$sqlEnrollments	= $dblms->querylms("SELECT ec.secs_id, ec.date_added as datetime_enroll, ec.id_std, ec.id_curs, ec.id_mas, ec.id_ad_prg, c.curs_name, mt.mas_name ,p.prg_name, a.adm_fullname, a.adm_email, 
                                        COUNT(DISTINCT cl.lesson_id) AS lesson_count, 
                                        COUNT(DISTINCT ca.id) AS assignment_count, 
                                        COUNT(DISTINCT cq.quiz_id) AS quiz_count, 
                                        COUNT(DISTINCT lt.track_id) AS track_count, 
                                        MAX(lt.date_added) AS last_track_date
                                        FROM ".ENROLLED_COURSES." ec
                                        INNER JOIN ".CHALLANS." ch ON FIND_IN_SET(ec.secs_id, ch.id_enroll) AND ch.is_deleted = 0
                                        INNER JOIN ".STUDENTS." s ON s.std_id = ec.id_std AND s.is_deleted = 0 AND s.std_status = 1
                                        INNER JOIN ".ADMINS." a ON a.adm_id = s.std_loginid AND a.is_deleted = 0 AND a.adm_status = 1
                                        LEFT JOIN ".COURSES." c ON c.curs_id = ec.id_curs AND c.curs_status = 1 AND c.is_deleted = 0 AND c.curs_type_status IN (2,3,4)
                                        LEFT JOIN ".MASTER_TRACK." mt ON mt.mas_id = ec.id_mas AND mt.mas_status = 1 AND mt.is_deleted = 0
                                        LEFT JOIN ".ADMISSION_PROGRAMS." ap ON ap.id = ec.id_ad_prg
                                        LEFT JOIN ".PROGRAMS." p ON p.prg_id = ap.id_prg
                                        LEFT JOIN ".COURSES_LESSONS." cl ON FIND_IN_SET(cl.id_curs, ec.id_curs) AND cl.lesson_status = 1 AND cl.is_deleted = 0
                                        LEFT JOIN ".COURSES_ASSIGNMENTS." ca ON FIND_IN_SET(ca.id_curs, ec.id_curs) AND ca.status = 1 AND ca.is_deleted = 0
                                        LEFT JOIN ".QUIZ." cq ON FIND_IN_SET(cq.id_curs, ec.id_curs) AND cq.quiz_status = 1 AND cq.is_deleted = 0 AND cq.is_publish = 1
                                        LEFT JOIN ".LECTURE_TRACKING." lt ON FIND_IN_SET(lt.id_curs, ec.id_curs) AND lt.id_std = ec.id_std AND lt.is_completed = 2 AND lt.is_deleted = 0 AND lt.id_mas = ec.id_mas AND lt.id_ad_prg = ec.id_ad_prg
                                        WHERE ec.is_deleted = '0' 
                                        AND ec.secs_status  = '1'
                                        AND ec.secs_id NOT IN (".$generateCert['generatedCertId'].") 
                                        AND (COALESCE(c.curs_name, '') != '' 
                                                OR COALESCE(mt.mas_name, '') != ''
                                                OR COALESCE(ap.program, '') != ''
                                            )
                                        GROUP BY ec.secs_id
                                        HAVING COUNT(DISTINCT lt.track_id) <= (COUNT(DISTINCT cl.lesson_id) + COUNT(DISTINCT ca.id) + COUNT(DISTINCT cq.quiz_id))
                                    ");
if(mysqli_num_rows($sqlEnrollments) > 0){
    while ($valCourse = mysqli_fetch_array($sqlEnrollments)) {        
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
                                                            ,'rm.type'          => 2
                                                        )
                                ,'order_by'     =>	'rm.datetime_mailed DESC'
                                ,'return_type'	=>	'single'
                            );
        $REMINDER_MAILS_LOGS = $dblms->getRows(REMINDER_MAILS_LOGS.' rm', $conditions);        
        
        if(!$REMINDER_MAILS_LOGS){
            // INSERT IN MAILS LOGS
            $values = array(
                                 'type'             => 2
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
                    'subject'       => "Friendly Reminder: Complete Your ".$type_name." (".html_entity_decode(html_entity_decode($cert_name)).") on ".TITLE_HEADER."",
                    'body'          => '
                        <div style="background-color: #f9f9f9; padding: 20px; font-family: Arial, sans-serif; color: #333;">
                            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                                <h2 style="color: #0056b3; font-weight: bold; margin-top: 20px;">Dear <strong>'.ucwords(strtolower($valCourse['adm_fullname'])).'</strong>,</h2>
                                <p style="font-size: 16px; line-height: 1.8;">
                                    We hope you are making progress on your course! We Wanted to gently remind you to complete your '.$type_name.' ('.html_entity_decode(html_entity_decode($cert_name)).') on '.TITLE_HEADER.'
                                </p>
                                <p style="font-size: 16px; line-height: 1.8;">
                                    Your learning journey is important to us. Completing your course will equip you with valuable skills and knowledge.
                                </p>
                                <p style="font-size: 16px; line-height: 1.8;">
                                    If You need any assistance, our support team is ready to help.
                                </p>
                                <p style="font-size: 16px; line-height: 1.8;">
                                    <b>Warm regards,</b>
                                    <br>
                                    Support Team
                                    <br>
                                    '.SMTP_EMAIL.'
                                    <br>
                                    '.SITE_NAME . ' <strong>(' . TITLE_HEADER . ')</strong>'.'
                                    <br>
                                    <b>Minhaj University Lahore</b>
                                </p>
                            </div>
                        </div>
                    ',
                    'tokken'        => SMTP_TOKEN,
                ], 'send-mail');
            }
        } else {
            $last_mail_date = (isset($REMINDER_MAILS_LOGS['datetime_mailed']) ? $REMINDER_MAILS_LOGS['datetime_mailed'] : $valCourse['datetime_enroll']);
            $last_mail_date = date('Y-m-d', strtotime($last_mail_date. ' + 15 days'));
            
            if(date('Y-m-d') > $last_mail_date){
                // INSERT IN MAILS LOGS
                $values = array(
                                     'type'             => 2
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
                        'subject'       => "Friendly Reminder: Complete Your ".$type_name." (".html_entity_decode(html_entity_decode($cert_name)).") on ".TITLE_HEADER."",
                        'body'          => '
                            <div style="background-color: #f9f9f9; padding: 20px; font-family: Arial, sans-serif; color: #333;">
                                <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                                    <h2 style="color: #0056b3; font-weight: bold; margin-top: 20px;">Dear <strong>'.ucwords(strtolower($valCourse['adm_fullname'])).'</strong>,</h2>
                                    <p style="font-size: 16px; line-height: 1.8;">
                                        We hope you are making progress on your course! We Wanted to gently remind you to complete your '.$type_name.' ('.html_entity_decode(html_entity_decode($cert_name)).') on '.TITLE_HEADER.'
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.8;">
                                        Your learning journey is important to us. Completing your course will equip you with valuable skills and knowledge.
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.8;">
                                        If You need any assistance, our support team is ready to help.
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.8;">
                                        <b>Warm regards,</b>
                                        <br>
                                        Support Team
                                        <br>
                                        '.SMTP_EMAIL.'
                                        <br>
                                        '.SITE_NAME . ' <strong>(' . TITLE_HEADER . ')</strong>'.'
                                        <br>
                                        <b>Minhaj University Lahore</b>
                                    </p>
                                </div>
                            </div>
                        ',
                        'tokken'        => SMTP_TOKEN,
                    ], 'send-mail');
                }
            }
        }
    }
}
?>