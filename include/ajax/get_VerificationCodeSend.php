<?php
include '../dbsetting/lms_vars_config.php';
include '../dbsetting/classdbconection.php';
include '../functions/functions.php';
$dblms = new dblms();
if ($_POST['_method'] == 'send-verification-code') {
    $VERIFICATION_CODES = $dblms->querylms("DELETE FROM ".VERIFICATION_CODES." WHERE adm_email = '".cleanvars($_POST['adm_email'])."'");
    if ($VERIFICATION_CODES) {
        $OTP_CODE = get_VerificationCode();
        $values = array(
                             'adm_email'    =>	cleanvars($_POST['adm_email'])
                            ,'cod_code'     =>	$OTP_CODE
                            ,'is_used'      =>	0
                            ,'cod_time'     =>	date('G:i:s')
        ); 
        $VERIFICATION_CODES		=	$dblms->insert(VERIFICATION_CODES, $values);
        if ($VERIFICATION_CODES) {
            $customBody = '
                <table style="font-family: \'Open Sans\', Arial, sans-serif; border-radius: 50px; width: 900px; max-width: 900px;" align="center">
                    <thead>
                        <tr>
                            <td align="center" style="background-color: #ebeef5;">
                                <table style="font-family: \'Open Sans\', Arial, sans-serif; background-color: #ebeef5; width: 900px; max-width: 900px;" align="center">
                                    <thead>
                                        <tr>
                                            <td align="center" style="background-color: #ebeef5; padding: 20px;">
                                                <a href="'.SITE_URL.'" target="_blank">
                                                    <img src="'.SITE_URL.'assets/img/logo/logo.png" width="168" height="auto" style="display: block; width: 168px; height: auto;">
                                                </a>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td align="center" style="background-color: #ebeef5; padding: 20px;">
                                                <table>
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding: 10px;">Dear '.(!empty($_POST['adm_fullname'])?moduleName($_POST['adm_fullname']):'Student').',</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px;">
                                                                Thank you for registering with '.SITE_NAME.'. Please use the following 6-digit code to verify your email address:
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px;">
                                                                <div style="text-align: center; margin-top: 30px; margin-bottom: 20px;">
                                                                    <table align="center">
                                                                        <tr>';
                                                                            for ($i = 0; $i < 4; $i++) {
                                                                                $customBody .= '
                                                                                <td style="background-color: green; color: white; width: 50px; height: 50px; border-radius: 5px; text-align: center; font-size: 24px; font-weight: bold; user-select: none;">
                                                                                    '.substr($OTP_CODE, $i, 1).'
                                                                                </td>';
                                                                                if ($i < 5) {
                                                                                    $customBody .= '
                                                                                    <td style="width: 10px;"></td>';
                                                                                }
                                                                            }
                                                                            $customBody .= '
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px;">This code will expire in 3 minutes. If you did not request this, please ignore this email.</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px;">Sincerely,<br>'.TITLE_HEADER.'</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding: 10px;">
                                                                <br>
                                                                <br>© '.date('Y').' '.TITLE_HEADER.'
                                                                <br>'.SITE_ADDRESS.'
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </thead>
                </table>';
            get_SendMail([
                'sender'        => SMTP_EMAIL,
                'senderName'    => SITE_NAME,
                'receiver'      => cleanvars($_POST['adm_email']),
                'receiverName'  => (!empty($_POST['adm_fullname'])?moduleName($_POST['adm_fullname']):'Student'),
                'subject'       => "Account verification mail (OTP: ".$OTP_CODE.")",
                'body'          => $customBody,
                'tokken'        => SMTP_TOKEN,
            ], 'send-mail');
            echo'code-sent';
        } else {
            echo'code-not-sent';
        }
    }
}

if ($_POST['_method'] == 'check-verification-code') {
    $condition = array(
                         'select'       =>  'cod_code, cod_time, is_used'
                        ,'where'        =>  array(
                                                     'adm_email'    =>	cleanvars($_POST['adm_email'])
                                                    ,'is_used'      =>	0
                                                )
                        ,'order_by'     =>  ' cod_id '
                        ,'return_type'  =>  'single'
    );
    $VERIFICATION_CODES = $dblms->getRows(VERIFICATION_CODES, $condition);
    if ($VERIFICATION_CODES['cod_code']) {
        $giveTime       = date('G:i:s', strtotime($VERIFICATION_CODES['cod_time'] . ' +180 seconds'));
        $takkenTime     = date('G:i:s');
        if ($giveTime >= $takkenTime) {
            if ($VERIFICATION_CODES['cod_code'] == cleanvars($_POST['user_code'])) {
                $VERIFICATION_CODES = $dblms->querylms("DELETE FROM ".VERIFICATION_CODES." WHERE adm_email = ".cleanvars($_POST['adm_email']));
                echo'code-matched';
            } else {
                echo'code-not-matched';
            }
        } else {
            echo'code-expired';
        }
    }
}

if ($_POST['_method'] == 'forgot-account-password') {
    // ADMIN
    $condition = array ( 
                         'select'		=>	'a.adm_id, a.adm_fullname'
                        ,'where'		=>	array( 
                                                     'a.adm_email'  =>	cleanvars($_POST['adm_email'])
                                                    ,'a.is_deleted'	=>	0
                                                )
                        ,'return_type' 	=>	'single' 
    ); 
    $ADMINS = $dblms->getRows(ADMINS.' AS a', $condition);
    if ($ADMINS) {
        $VERIFICATION_CODES = $dblms->querylms("DELETE FROM ".VERIFICATION_CODES." WHERE adm_email = '".cleanvars($_POST['adm_email'])."'");
        if ($VERIFICATION_CODES) {
            $OTP_CODE = get_VerificationCode();
            $values = array(
                                 'adm_email'    =>	cleanvars($_POST['adm_email'])
                                ,'id_adm'       =>	$ADMINS['adm_id']
                                ,'cod_code'     =>	$OTP_CODE
                                ,'is_used'      =>	0
                                ,'cod_time'     =>	date('G:i:s')
            ); 
            $VERIFICATION_CODES = $dblms->insert(VERIFICATION_CODES, $values);
            if ($VERIFICATION_CODES) {
			    $latestID =	$dblms->lastestid();
                $customBody = '
                <table style="font-family: \'Open Sans\', Arial, sans-serif; border-radius: 50px; width: 900px; max-width: 900px;" align="center">
                    <thead>
                        <tr>
                            <td width="150"></td>
                            <td align="center" style="background-color: #ebeef5;">
                                <table style="font-family: \'Open Sans\', Arial, sans-serif; background-color: #ebeef5; width: 900px; max-width: 900px;" align="center">
                                    <thead>
                                        <tr>
                                            <td align="center" style="background-color: #ebeef5; padding: 20px;">
                                                <a href="'.SITE_URL.'" target="_blank">
                                                    <img src="'.SITE_URL.'assets/images/brand/logo.png" width="168" height="auto" style="display: block; width: 168px; height: auto;">
                                                </a>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td align="center" style="background-color: #ebeef5; padding: 20px;">
                                                <table>
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding: 10px;">Dear '.moduleName($ADMINS['adm_fullname']).',</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px;">
                                                                You have requested to reset your account password. Please click on the following link to change your password:
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px; text-align: center;">
                                                                <a href="'.SITE_URL.'forgot-password/'.get_dataHashingOnlyExp($ADMINS['adm_fullname'].','.$_POST['adm_email'].','.$ADMINS['adm_id'].','.$latestID.','.$OTP_CODE,true).'" style="display: inline-block; padding: 15px 30px; background-color: #4CAF50; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px;">Reset Your Password</a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px;">If you did not request this, please ignore this email.</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px;">Sincerely,<br>'.TITLE_HEADER.'</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding: 10px;">
                                                                <br>
                                                                <br>© '.date('Y').' '.TITLE_HEADER.'
                                                                <br>'.SITE_ADDRESS.'
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="150"></td>
                        </tr>
                    </thead>
                </table>
                ';
                get_SendMail([
                    'sender'        => SMTP_EMAIL,
                    'senderName'    => SITE_NAME,
                    'receiver'      => cleanvars($_POST['adm_email']),
                    'receiverName'  => (!empty($_POST['adm_fullname'])?moduleName($_POST['adm_fullname']):'Student'),
                    'subject'       => 'Forgot account password mail (Link in the mail)',
                    'body'          => $customBody,
                    'tokken'        => SMTP_TOKEN,
                ], 'send-mail');

                echo'code-sent';
            } else {
                echo'code-not-sent';
            }
        }
    } else {
        echo'cant-find-any-email';
    }
}
?>