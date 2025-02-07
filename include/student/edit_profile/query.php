<?php
// CHANGE PROFILE PHOTO
if (isset($_POST['update_profile_pic'])) {
    $ID = $_SESSION['userlogininfo']['LOGINIDA'];
    if (!empty($_FILES['profile_pic']['name'])) {
        $path_parts = pathinfo($_FILES["profile_pic"]["name"]);
        $extension  = strtolower($path_parts['extension']);
        if (in_array($extension, array('jpeg', 'jpg', 'png'))) {
            // directory, original file and file name
            $file_dir       = '../../uploads/images/admin/';
            $file_name		= to_seo_url(cleanvars($_SESSION['userlogininfo']['LOGINNAME'])).'-'.$ID.".".($extension);
            $original_file  = $file_dir.$file_name;
            
            // Server-side file transfer via cURL
            $file_input_name = 'profile_pic'; // This can be dynamically set based on your form input name
            $file = $_FILES[$file_input_name]['tmp_name'];

            // api url
            $apiUrl = SITE_URL_PORTAL.'api/web/file_upload.php';

            // post data array
            $postData   =   [
                                 'file_input_name'  => $file_input_name
                                ,'file_name'        => $file_name
                                ,'file_dir'         => $file_dir
                                ,'uploaded_file'    => new CURLFile($file, $_FILES[$file_input_name]['type'], $_FILES[$file_input_name]['name'])
                            ];

            // Call the function to send the file
            $response = sendFileToServer($apiUrl, $postData);

            // Handle response from the destination subdomain ---- 1 = UPLOADED, 0 = ERROR
            if ($response === '1') {
                // Update database and perform other actions as needed
                $dataImage      = array( 'adm_photo' => $file_name );
                $sqlUpdateFile  = $dblms->Update(ADMINS, $dataImage, "WHERE adm_id = '".$ID."'");

                if($sqlUpdateFile){                    
                    $_SESSION['userlogininfo']['LOGINPHOTO'] = SITE_URL_PORTAL.'uploads/images/admin/'.$file_name;
                    
                    // REMARKS
                    sendRemark('Profile Photo Updated', '2', $ID);
                    sessionMsg("Updated!","Profile Photo Successfully Updated","info");
                    header("location: ".SITE_URL.CONTROLER, true, 301);
                    exit;
                } else {                    
                    sessionMsg("Error!","Error occured while updating sql.","danger");
                    header("location: ".SITE_URL.CONTROLER, true, 301);
                    exit;
                }
            } else {
                sessionMsg("Error!","Error occured while updating response.","danger");
                header("location: ".SITE_URL.CONTROLER, true, 301);
                exit;
            }
        }
    }
}

// EDIT PROFILE DETAIL
if (isset($_POST['update_profile'])) {
    $ID = $_SESSION['userlogininfo']['LOGINIDA'];
    
	$values = array(
                         'adm_fullname'			=> cleanvars($_POST['full_name'])
                        ,'adm_email'			=> cleanvars($_POST['email'])
                        ,'adm_phone'			=> cleanvars($_POST['phone_no'])
                        ,'id_modify'            => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                        ,'date_modify'          => date('Y-m-d G:i:s')
                    );     
    $sqllms = $dblms->update(ADMINS, $values, "WHERE adm_id = '$ID'");

	$values = array(
                         'std_dob'   			=> cleanvars($_POST['dob'])
                        ,'std_name'  			=> cleanvars($_POST['full_name'])
                        ,'id_country'  			=> cleanvars($_POST['country'])
                        ,'std_address_1'		=> cleanvars($_POST['address_1'])
                        ,'std_address_2'		=> cleanvars($_POST['address_2'])
                        ,'city_name'    		=> cleanvars($_POST['city'])
                        ,'std_postal_address'	=> cleanvars($_POST['zip_code'])
                        ,'std_about'         	=> cleanvars($_POST['about'])
                        ,'std_gender'         	=> cleanvars($_POST['std_gender'])
                        ,'id_modify'            => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                        ,'date_modify'          => date('Y-m-d G:i:s')
                    );
    $sqllms = $dblms->update(STUDENTS, $values, "WHERE std_id = '".$_SESSION['userlogininfo']['STDID']."'");

    if($_SESSION['userlogininfo']['LOGINISTEACHER'] != 1){
        $values = array(
                                     'emply_name'				=>	cleanvars($_POST['full_name'])
                                    ,'emply_gender'				=>	cleanvars($_POST['emply_gender'])
                                    ,'emply_dob'				=>	cleanvars(date("Y-m-d", strtotime($_POST['dob'])))
                                    ,'emply_email'				=>	cleanvars($_POST['email'])
                                    ,'emply_permanent_address'	=>	cleanvars($_POST['address_1'])
                                    ,'emply_postal_address'		=>	cleanvars($_POST['address_2'])
                                    ,'emply_gender'             => cleanvars($_POST['std_gender'])
                                    ,'id_modify'				=>	cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                    ,'date_modify'				=>	date('Y-m-d H:i:s')
                                );
        $sqllms = $dblms->Update(EMPLOYEES, $values , "WHERE emply_loginid = '".cleanvars($_SESSION['userlogininfo']['LOGINIDA'])."'");
    }
    
    if ($sqllms) {
        $_SESSION['userlogininfo']['LOGINNAME']     =	cleanvars($_POST['full_name']);
        $_SESSION['userlogininfo']['LOGINEMAIL']    =	cleanvars($_POST['email']);
        
        // REMARKS
        sendRemark('Profile Information Updated', '2', $ID);
        sessionMsg("Updated!","Successfully Update Profile","success");
        header("location: ".SITE_URL.CONTROLER);
    }
}

// DELETE PROFILE
if (!empty($zone) && $zone ="conform") {
    $values = array(
                         'id_deleted'	=>	cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                        ,'is_deleted'	=>	'1'
                        ,'ip_deleted'	=>	cleanvars($ip)
                        ,'date_deleted'	=>	date('Y-m-d G:i:s')
                    );
    $sqlDel = $dblms->Update(STUDENTS,  $values , "WHERE std_loginid  = '".cleanvars($_SESSION['userlogininfo']['LOGINIDA'])."'");
    $sqlDel = $dblms->Update(ADMINS,    $values , "WHERE adm_id       = '".cleanvars($_SESSION['userlogininfo']['LOGINIDA'])."'");
    if ($sqlDel) {        
        sendRemark('Profile Deleted', '3', $ID);
        panelLMSSTDLogout();
    }
}

// SOCIAL LINKS
if (isset($_POST['social_profile'])) {
    $social = array();
    foreach ($_POST['social'] as $key => $value) {
        if ($value){
            $social[$key] = $value;
        }
    }
    $values = array(
                         'std_sociallinks'      => serialize($social)
                        ,'id_modify'            => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])            
                        ,'date_modify'          => date('Y-m-d G:i:s')
                    );
    $sql = $dblms->Update(STUDENTS, $values , "WHERE std_id  = '".cleanvars($_SESSION['userlogininfo']['STDID'])."'");
    if ($sql) {
        sendRemark('Social Links Updated', '2', $_SESSION['userlogininfo']['STDID']);
        sessionMsg("Update","Your Social Profile has been Updated",'info');
        header("location: ".SITE_URL.CONTROLER);
    }
}

// SOCIAL PRIVACY
if (isset($_POST['social_privacy'])) {
    $privacy_profile = ($_POST['std_privacy_profile']) ? true : false;
    $privacy_courses = ($_POST['std_privacy_courses']) ? true : false;
    $values = array(
                 'std_privacy_profile'      => cleanvars($privacy_profile)
                ,'std_privacy_courses'      => cleanvars($privacy_courses)
                ,'id_modify'                => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])            
                ,'date_modify'              => date('Y-m-d G:i:s')
    );
    $sql = $dblms->Update(STUDENTS, $values , "WHERE std_id  = '".cleanvars($_SESSION['userlogininfo']['STDID'])."'");
    if ($sql) {
        sendRemark('Social Privacy Updated', '2', $_SESSION['userlogininfo']['STDID']);
        sessionMsg("Update","Your Social Profile has been Updated",'info');
        header("location: ".SITE_URL.CONTROLER);
    }
}

// CHANGE PASSWORD
if (isset($_POST['change_password'])) {
    $ID = $_SESSION['userlogininfo']['LOGINIDA'];

    $condition = array ( 
                             'select' 		=>	'adm_id, adm_logintype, adm_username, adm_salt'
                            ,'where' 		=>	array( 
                                                        'adm_id'    => cleanvars($ID) 
                                                    ) 
                            ,'return_type'	=>	'single'
                        );
    $row = $dblms->getRows(ADMINS, $condition); 
    $salt = $row['adm_salt'];
    $password = hash('sha256', $_POST['password'] . $salt);
    for ($round = 0; $round < 65536; $round++) {
        $password = hash('sha256', $password . $salt);
    }
    $values = array(
                     'adm_userpass'			=> cleanvars($password)
                    ,'id_modify'            => cleanvars($ID)
                    ,'date_modify'          => date('Y-m-d G:i:s')
    ); 
    $sqllms = $dblms->update(ADMINS, $values, "WHERE adm_id = '".cleanvars($ID)."'");

    if ($sqllms) {
        $dataLog = array(
                             'login_type'		=> cleanvars($row['adm_logintype'])
                            ,'id_login_id'		=> cleanvars($ID)
                            ,'user_name'		=> cleanvars($row['adm_username'])
                            ,'user_pass'		=> cleanvars($_POST['password'])
                            ,'dated'			=> date("Y-m-d G:i:s")
        );
        $sqllmslog  = $dblms->Insert(LOGIN_HISTORY , $dataLog);

        sendRemark('Account Password Updated', '2', $ID);
    }
}

// EDUCATION-INTERESTS
if (isset($_POST['update_edu_intrests'])) {
    $values = array(
                        'is_deleted'   => 1
                    ); 
    $sqlDel = $dblms->update(STUDENT_EDUCATIONS, $values, "WHERE id_std = '".$_SESSION['userlogininfo']['STDID']."'");
    if($sqlDel){
        foreach ($_POST['program'] as $key => $value){
            if(!empty($_POST['program'][$key])){
                $values = array(
                                 'status'			=> 1
                                ,'id_std'			=> cleanvars($_SESSION['userlogininfo']['STDID'])
                                ,'program'			=> cleanvars($_POST['program'][$key])
                                ,'institute'		=> cleanvars($_POST['institute'][$key])
                                ,'grade'			=> cleanvars($_POST['grade'][$key])
                                ,'year'			    => cleanvars($_POST['year'][$key])
                            );
                $sqllms = $dblms->Insert(STUDENT_EDUCATIONS, $values);
            }
        }
    }
    if(!empty($_POST['id_intrests'])){
	    $values = array(
                            'id_intrests'   => implode(",",$_POST['id_intrests'])
                        ); 
        $sqllms = $dblms->update(STUDENTS, $values, "WHERE std_id = '".$_SESSION['userlogininfo']['STDID']."'");
    }
    if ($sqllms) {
        sendRemark(moduleName(CONTROLER).' Updated', '2', $_SESSION['userlogininfo']['STDID']);
        sessionMsg("Updated!","Record Successfully Update","info");
        header("location: ".SITE_URL.CONTROLER);
    }
}
?>