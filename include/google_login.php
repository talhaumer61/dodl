<?php
session_start();
require_once(__DIR__ . "/dbsetting/lms_vars_config.php");
require_once(__DIR__ . "/dbsetting/classdbconection.php");
require_once(__DIR__ . "/functions/functions.php");
$dblms = new dblms();

require_once(__DIR__ . '/../vendor/autoload.php');

if (isset($_GET['org_id']) && !empty($_GET['org_id']) && $_GET['org_id'] > 0) {
    $_SESSION['referral_org_id'] = $_GET['org_id'];
}

// === GOOGLE CLIENT CONFIG ===
$clientID     = '117788691303-nhcl1f27aksvdepb6vui3tsgd58dbuob.apps.googleusercontent.com';
$clientSecret = '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';
$redirectURI  = SITE_URL . 'include/google_login.php';

// Create Google Client
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectURI);
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $oauth = new Google_Service_Oauth2($client);
        $googleUser = $oauth->userinfo->get();

        $google_id    = cleanvars($googleUser->id);
        $google_name  = cleanvars($googleUser->name);
        $google_email = cleanvars($googleUser->email);
        $google_pic   = cleanvars($googleUser->picture);

        // === DEFAULT PHOTO FOR STUDENTS ===
        // $defaultPhotoPath = 'uploads/images/students/default.png';
        // $localPhoto = basename($defaultPhotoPath);

        // --- CHECK IF USER EXISTS ---
        $loginconditions = array ( 
									 'select' 		=>	'a.*, s.std_id, s.std_level, s.std_gender, s.id_org, e.emply_request'
									,'join' 		=>	'LEFT JOIN '.STUDENTS.' s ON s.std_loginid = a.adm_id AND s.std_status = 1 AND s.is_deleted = 0
														 LEFT JOIN '.EMPLOYEES.' e ON e.id_added = a.adm_id AND e.emply_status = 1 AND e.is_deleted = 0'
									,'where' 		=>	array( 
																'a.is_deleted' 	=> '0'
															)
									,'search_by'	=>	' AND a.is_teacher IN (1,3) AND (a.adm_username = "'.$google_email.'" OR a.adm_email = "'.$google_email.'" )'
									,'return_type'	=>	'single'
		); 		
		$row = $dblms->getRows(ADMINS.' a', $loginconditions);

        // --- IF USER NOT FOUND, CREATE NEW ONE + STUDENT PROFILE ---
        if (!$row) {
            $salt = bin2hex(random_bytes(8));
            $password = hash('sha256', $google_id . $salt);
            for ($i = 0; $i < 65536; $i++) {
                $password = hash('sha256', $password . $salt);
            }

            // Insert into ADMINS
            $insertData = array(
                'adm_fullname'   => $google_name,
                'adm_username'   => $google_email,
                'adm_email'      => $google_email,
                'adm_status'     => 1,
                'is_deleted'     => 0,
                'is_teacher'     => 1,
                'adm_type'       => 3,  // Student
                'adm_logintype'  => 3,  // Google login
                'adm_salt'       => $salt,
                'adm_userpass'   => $password,
                'date_added'     => date("Y-m-d H:i:s")
            );
            $dblms->insert(ADMINS, $insertData);
            $latestID = $dblms->lastestid();

            // Insert into STUDENTS
            $stdValues = array(
                'std_loginid' => $latestID,
                'std_status'  => 1,
                'std_level'   => 1,
                'std_name'    => $google_name,
                'date_added'  => date('Y-m-d H:i:s'),
                'id_added'    => $latestID
            );
            if (isset($_SESSION['referral_org_id']) && !empty($_SESSION['referral_org_id']) && $_SESSION['referral_org_id'] > 0) {
                $stdValues['id_org'] = $_SESSION['referral_org_id'];
            }
            $dblms->insert(STUDENTS, $stdValues);
            $stdID = $dblms->lastestid();            

            // Send welcome email (optional)
            get_SendMail([
                'sender'        => SMTP_EMAIL,
                'senderName'    => SITE_NAME,
                'receiver'      => cleanvars($google_email),
                'receiverName'  => cleanvars($google_name),
                'subject'       => "Welcome to ".TITLE_HEADER.", Your Journey Begins Here!",
                'body'          => '
                    <p>
                        We are excited to have you back to our learning community! This email contains all the essential information to get you started on DODL.
                        <br>
                        <a href="https://youtu.be/5ZEUEok9Mig" target="_blank">Information video!</a>
                        <br>
                        <br>
                        Email/Username: '.$google_email.'
                        <br>
                        Password: '.$google_id.'
                        <br>
                        <br>
                        <b>Warm regards,</b>
                        <br>
                        Support Team
                        <br>
                        <br>
                        '.SMTP_EMAIL.'
                        <br>
                        '.SITE_NAME.' <b>('.TITLE_HEADER.')</b>
                        <br>
                        <b>Minhaj University Lahore</b>
                    </p>
                ',
                'tokken'        => SMTP_TOKEN,
            ], 'send-mail');
            
            // --- SET SESSION ---
            $userlogininfo = array();
            $userlogininfo['LOGINIDA']      = $latestID;
            $userlogininfo['LOGINTYPE']     = 3;
            $userlogininfo['LOGINAFOR']     = 3;
            $userlogininfo['LOGINUSER']     = $google_email;
            $userlogininfo['LOGINNAME']     = $google_name;
            $userlogininfo['LOGINEMAIL']    = $google_email;
            $userlogininfo['LOGINPHONE']    = '';
            $userlogininfo['LOGINPHOTO']    = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
            $userlogininfo['LOGINCAMPUS']   = 0;
            $userlogininfo['STDID']         = $stdID;
            $userlogininfo['STDLEVEL']      = 1;
            $userlogininfo['STDGENDER']     = 0;
            $userlogininfo['LOGINISTEACHER'] = 1;
            $userlogininfo['EMPLYREQUEST']  = 0;
            $userlogininfo['LOGINORGANIZATIONID'] 	= 	$_SESSION['referral_org_id'] ?? 0;

            $_SESSION['userlogininfo'] = $userlogininfo;

            // --- LOG THE LOGIN ---
            $dataLog = array(
                'login_type' => 3,
                'id_login_id' => $latestID,
                'user_name' => $google_email,
                'user_pass' => $google_id,
                'email' => $google_email,
                'id_campus' => 0,
                'remarks' => 'GOOGLE_SIGNUP',
                'dated' => date("Y-m-d G:i:s")
            );
            $dblms->Insert(LOGIN_HISTORY, $dataLog);

            unset($_SESSION['referral_org_id']);

            // --- REDIRECT AFTER LOGIN ---
            sessionMsg('Successfully', 'You are Log on Successfully.', 'success');
            header("Location: " . SITE_URL . "home");
            exit();
        }
        else {
            if($row['adm_status'] != 1){
                $salt = bin2hex(random_bytes(8));
                $password = hash('sha256', $google_id . $salt);
                for ($i = 0; $i < 65536; $i++) {
                    $password = hash('sha256', $password . $salt);
                }

                get_SendMail([
                    'sender'        => SMTP_EMAIL,
                    'senderName'    => SITE_NAME,
                    'receiver'      => cleanvars($google_email),
                    'receiverName'  => cleanvars($google_name),
                    'subject'       => "Welcome back to ".TITLE_HEADER.", Your Journey Begins Here!",
                    'body'          => '
                        <p>
                            We are excited to have you back to our learning community! This email contains all the essential information to get you started on DODL.
                            <br>
                            <a href="https://youtu.be/5ZEUEok9Mig" target="_blank">Information video!</a>
                            <br>
                            <br>
                            Email/Username: '.$google_email.'
                            <br>
                            Password: '.$google_id.'
                            <br>
                            <br>
                            <b>Warm regards,</b>
                            <br>
                            Support Team
                            <br>
                            <br>
                            '.SMTP_EMAIL.'
                            <br>
                            '.SITE_NAME.' <b>('.TITLE_HEADER.')</b>
                            <br>
                            <b>Minhaj University Lahore</b>
                        </p>
                    ',
                    'tokken'        => SMTP_TOKEN,
                ], 'send-mail');

                // Fetch full user row again
                $dblms->Update(ADMINS, ['adm_status' => 1, 'adm_salt' => $salt, 'adm_userpass' => $password], " WHERE adm_id = " . $row['adm_id'] . " ");
                $dblms->Update(STUDENTS, ['std_status' => 1], " WHERE std_loginid = " . $row['adm_id'] . " ");
            }

            // CHECK STUDENT IMAGE EXIST
            if($row['std_gender'] == '2'){
                $adm_photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
            }else{            
                $adm_photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
            }
            if(!empty($row['adm_photo'])){
                $adm_photo = SITE_URL_PORTAL.'uploads/images/admin/'.$row['adm_photo'];
            }

            // --- SET SESSION ---
            $userlogininfo = array();
            $userlogininfo['LOGINIDA']      = $row['adm_id'];
            $userlogininfo['LOGINTYPE']     = $row['adm_type'];
            $userlogininfo['LOGINAFOR']     = $row['adm_logintype'];
            $userlogininfo['LOGINUSER']     = $row['adm_username'];
            $userlogininfo['LOGINNAME']     = $row['adm_fullname'];
            $userlogininfo['LOGINEMAIL']    = $row['adm_email'];
            $userlogininfo['LOGINPHONE']    = $row['adm_phone'];
            $userlogininfo['LOGINPHOTO']    = $adm_photo;
            $userlogininfo['LOGINCAMPUS']   = $row['id_campus'];
            $userlogininfo['STDID']         = $row['std_id'];
            $userlogininfo['STDLEVEL']      = $row['std_level'];
            $userlogininfo['STDGENDER']     = $row['std_gender'];
            $userlogininfo['LOGINISTEACHER'] = $row['is_teacher'];
            $userlogininfo['EMPLYREQUEST']  = $row['emply_request'];
            $userlogininfo['LOGINORGANIZATIONID'] = $row['id_org'];

            $_SESSION['userlogininfo'] = $userlogininfo;

            // --- LOG THE LOGIN ---
            $dataLog = array(
                'login_type' => cleanvars($row['adm_logintype']),
                'id_login_id' => cleanvars($row['adm_id']),
                'user_name' => cleanvars($row['adm_username']),
                'user_pass' => cleanvars($google_id),
                'email' => cleanvars($row['adm_email']),
                'id_campus' => cleanvars($row['id_campus']),
                'remarks' => 'GOOGLE_LOGIN',
                'dated' => date("Y-m-d G:i:s")
            );
            $dblms->Insert(LOGIN_HISTORY, $dataLog);

            // --- REDIRECT AFTER LOGIN ---
            sessionMsg('Successfully', 'You are Log on Successfully.', 'success');
            header("Location: " . SITE_URL . "home");
            exit();
        }

    } else {
        sessionMsg('Error', 'Error fetching Google token.', 'danger');
        header("Location: " . SITE_URL . "home");
        exit();
    }
} else {
    // === Redirect to Google ===
    $authUrl = $client->createAuthUrl();
    header("Location: " . $authUrl);
    exit();
}
