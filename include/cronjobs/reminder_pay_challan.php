<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();

// PENDING CHALLANS
$condition = array(
                     'select'       =>  'ch.challan_no, ch.id_std, s.std_name, a.adm_fullname, a.adm_email'
                    ,'join'         =>  'INNER JOIN '.STUDENTS.' s ON s.std_id = ch.id_std AND s.std_status = 1 AND s.is_deleted = 0
                                         INNER JOIN '.ADMINS.' a ON a.adm_id = s.std_loginid AND a.adm_status = 1 AND a.is_deleted = 0'
                    ,'where'        =>  array(
                                                 'ch.status'        => 2
                                                ,'ch.is_deleted'    => 0
                                            )
                    ,'order_by'     =>  'ch.challan_id ASC'
                    ,'return_type'  =>  'all'
);
$CHALLANS = $dblms->getRows(CHALLANS.' ch', $condition, $sql);

foreach ($CHALLANS as $keyChallan => $valChallan) {
    // IF MAIL SENT ALREADY
    $conditions = array ( 
                             'select'       =>	'rm.datetime_mailed'
                            ,'where' 		=>	array( 
                                                         'rm.id_std'        => cleanvars($valChallan['id_std'])
                                                        ,'rm.challan_no'    => cleanvars($valChallan['challan_no'])
                                                        ,'rm.type'          => 3 // challan reminder
                                                    )
                            ,'order_by'     =>	'rm.datetime_mailed DESC'
                            ,'return_type'	=>	'single'
                        );
    $REMINDER_MAILS_LOGS = $dblms->getRows(REMINDER_MAILS_LOGS.' rm', $conditions);
    
    if(!$REMINDER_MAILS_LOGS){
        // INSERT IN MAILS LOGS
        $values = array(
                             'type'             => 3
                            ,'challan_no'       => cleanvars($valChallan['challan_no'])
                            ,'id_std'			=> cleanvars($valChallan['id_std'])
                            ,'std_name'			=> cleanvars($valChallan['adm_fullname'])
                            ,'std_email'        => cleanvars($valChallan['adm_email'])
                            ,'datetime_mailed'  => date('Y-m-d G:i:s')
                        );     
        $sqlInsert = $dblms->insert(REMINDER_MAILS_LOGS, $values);
        if($sqlInsert){
            /*
            get_SendMail([
                'sender'        => SMTP_EMAIL,
                'senderName'    => SITE_NAME,
                'receiver'      => $valChallan['adm_email'],
                'receiverName'  => $valChallan['adm_fullname'],
                'subject'       => 'Pending Fee Challan Reminder - '.$valChallan['challan_no'].'',
                'body'          => '
                                    <div style="background-color: #f9f9f9; padding: 20px; font-family: Arial, sans-serif; color: #333;">
                                        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                                            <h2 style="color: #0056b3; font-weight: bold; margin-top: 20px;">Dear <strong>'.ucwords(strtolower($valChallan['adm_fullname'])).'</strong>,</h2>
                                            <p style="font-size: 16px; line-height: 1.8;">
                                                This is a reminder from '.TITLE_HEADER.' to pay your pending fee with challan number '.$valChallan['challan_no'].'. Clear your dues to unlock your journey toward skill mastery, academic growth, and professional excellence. Timely payment will help you stay on track and move closer to achieving your goals.
                                                <br>
                                                For any queries, feel free to contact us at <b>'.SMTP_EMAIL.'</b>
                                            </p>                                        
                                            <p style="font-size: 16px; line-height: 1.8;">
                                                <b>Best regards,</b>
                                                <br>
                                                Support Team
                                                <br>
                                                '.SITE_NAME.' <strong>('.TITLE_HEADER.')</strong>
                                                <br>
                                                <b>Minhaj University Lahore</b>
                                            </p>
                                        </div>
                                    </div>',
                'tokken'    => SMTP_TOKEN,
            ], 'send-mail');
            */
        }
    } else {
        $last_mail_date = date('Y-m-d', strtotime($REMINDER_MAILS_LOGS['datetime_mailed']. ' + 15 days'));
        
        if(date('Y-m-d') > $last_mail_date){
            // INSERT IN MAILS LOGS
            $values = array(
                                 'type'             => 3
                                ,'challan_no'       => cleanvars($valChallan['challan_no'])
                                ,'id_std'			=> cleanvars($valChallan['id_std'])
                                ,'std_name'			=> cleanvars($valChallan['adm_fullname'])
                                ,'std_email'        => cleanvars($valChallan['adm_email'])
                                ,'datetime_mailed'  => date('Y-m-d G:i:s')
                            );     
            $sqlInsert = $dblms->insert(REMINDER_MAILS_LOGS, $values);
            if($sqlInsert){ 
                /*               
                get_SendMail([
                    'sender'        => SMTP_EMAIL,
                    'senderName'    => SITE_NAME,
                    'receiver'      => $valChallan['adm_email'],
                    'receiverName'  => $valChallan['adm_fullname'],
                    'subject'       => 'Pending Fee Challan Reminder - '.$valChallan['challan_no'].'',
                    'body'          => '
                                        <div style="background-color: #f9f9f9; padding: 20px; font-family: Arial, sans-serif; color: #333;">
                                            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                                                <h2 style="color: #0056b3; font-weight: bold; margin-top: 20px;">Dear <strong>'.ucwords(strtolower($valChallan['adm_fullname'])).'</strong>,</h2>
                                                <p style="font-size: 16px; line-height: 1.8;">
                                                    This is a reminder from '.TITLE_HEADER.' to pay your pending fee with challan number '.$valChallan['challan_no'].'. Clear your dues to unlock your journey toward skill mastery, academic growth, and professional excellence. Timely payment will help you stay on track and move closer to achieving your goals.
                                                    <br>
                                                    For any queries, feel free to contact us at <b>'.SMTP_EMAIL.'</b>
                                                </p>                                        
                                                <p style="font-size: 16px; line-height: 1.8;">
                                                    <b>Best regards,</b>
                                                    <br>
                                                    Support Team
                                                    <br>
                                                    '.SITE_NAME.' <strong>('.TITLE_HEADER.')</strong>
                                                    <br>
                                                    <b>Minhaj University Lahore</b>
                                                </p>
                                            </div>
                                        </div>',
                    'tokken'    => SMTP_TOKEN,
                ], 'send-mail');
                */
            }
        }
    }
}
?>