<?php

	include "../../admin/include/dbsetting/lms_vars_config.php";
	include "../../admin/include/dbsetting/classdbconection.php";
	$dblms = new dblms();
    include "../../admin/include/functions/functions.php";
    
if(isset($_POST['username']) && isset($_POST['password'])) {
    session_start();

    $username   =   cleanvars($_POST['username']);
	$password   =   cleanvars($_POST['password']);
	$pass3      =   ($password);

    $sqllms	= $dblms->querylms("SELECT * FROM ".ADMINS."
                                WHERE adm_username = '".cleanvars($username)."' 
                                AND adm_status = '1' LIMIT 1");												
    $row = mysqli_fetch_array($sqllms); 
    $salt = $row['adm_salt'];
    $password = hash('sha256', $pass3 . $salt);
    for ($round = 0; $round < 65536; $round++) {
        $password = hash('sha256', $password . $salt);
    }
    if($password == $row['adm_userpass']) {

        $sqllms	= $dblms->querylms("SELECT `type`, `email`, `first_name`, `last_name`, `profile_image`
                                    FROM ".USERS." 
                                    WHERE user_id = '".$row['id_user']."' 
                                    AND status = '1' LIMIT 1");
        $user = mysqli_fetch_array($sqllms);
        $values = array (
                            "login_type"	=>	    cleanvars($user['type']),
                            "id_login_id"	=>	    cleanvars($row['adm_id']),
                            "user_name"		=>	    cleanvars($row['adm_username']),
                            "email"			=>	    cleanvars($user['email']),
                            "user_pass"		=>	    cleanvars($_POST['password']),
                            "dated" 		=>	    date('Y-m-d G:i:s')
                        );	
        $sqllms  = $dblms->insert(LOGIN_HISTORY, $values);
        
        $settingSqllms	= $dblms->querylms("SELECT s.id_batch, b.batch_name
                                            FROM ".SETTINGS." s
                                            INNER JOIN ".BATCH." b ON b.batch_id  = s.id_batch
                                            WHERE  s.id_deleted = '0'
                                            AND s.status = '1'
                                            LIMIT 1
                                            "
                                          );
        $settingValues = mysqli_fetch_array($settingSqllms);   

        $session_values = array(
                                    'adm_id'			=>		cleanvars($row['adm_id']),
                                    'user_id'			=>		cleanvars($row['id_user']),
                                    'name'				=>		cleanvars($user['first_name'])." ".cleanvars($user['last_name']),
                                    "type"				=>		cleanvars($user['type']),
                                    'profile_image'	    =>		cleanvars($user['profile_image']),
                                    'current_batch'     =>      cleanvars($settingValues['id_batch']),  
                                    'batch_name'        =>      cleanvars($settingValues['batch_name'])  

                                );
        unset($_SESSION['stdlogininfo']);
        $_SESSION['stdlogininfo'] = $session_values;
        
        $remarks = 'Login to Software';
        $values = array (
                            "id_user"		=>	cleanvars($_SESSION['stdlogininfo']['adm_id']),
                            "filename"	=>	strstr(basename($_SERVER['REQUEST_URI']), '.php', true),
                            "action"		=>	'4',
                            "dated"			=>	date('Y-m-d G:i:s'),
                            "ip"				=>	cleanvars($ip),
                            "remarks" 	=>	cleanvars($remarks)
                        );
        $sqllms  = $dblms->insert(LOGS, $values);
        $response_array['status'] = 'success';
    }else{
        $response_array['status'] = 'error';
    }

    echo json_encode($response_array);
}
?>