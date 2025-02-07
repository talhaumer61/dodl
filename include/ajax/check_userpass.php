<?php
include "../dbsetting/lms_vars_config.php";
include "../dbsetting/classdbconection.php";
$dblms = new dblms();
include "../functions/functions.php";
session_start();

if(isset($_POST['std_password'])) {
    $condition = array(
                             'select'       =>  'adm_salt, adm_userpass'
                            ,'where'        =>  array(
                                                         'is_deleted'       => 0
                                                        ,'adm_username'     => cleanvars($_SESSION['userlogininfo']['LOGINUSER'])
                                                    )
                            ,'return_type'  =>  'single'
                        );
    $row = $dblms->getRows(ADMINS , $condition);
    if($row){
        if(get_PasswordVerify($_POST['std_password'], $row['adm_userpass'], $row['adm_salt'])){
            $response_array['status'] = 'success';
        }else{
            $response_array['status'] = 'error';
        }
    }else{
        $response_array['status'] = 'error';
    }
    echo json_encode($response_array);
}
?>