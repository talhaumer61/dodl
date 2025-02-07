<?php
include "../dbsetting/lms_vars_config.php";
include "../dbsetting/classdbconection.php";
$dblms = new dblms();
include "../functions/functions.php";
    
if(isset($_POST['username'])) {
    $condition = array(
                         'select'       =>  'adm_id'
                        ,'where'        =>  array(
                                                     'is_deleted'          => 0
                                                    ,'adm_username'        => $_POST['username']
                                                )
                        ,'return_type'  =>  'single'
                    );
    if($dblms->getRows(ADMINS , $condition)) {
        $response_array['status'] = 'success';       
    }else{
        $response_array['status'] = 'error';
    }
    echo json_encode($response_array);
}

if(isset($_POST['email'])) {
    $condition = array(
                         'select'       =>  'adm_id'
                        ,'where'        =>  array(
                                                     'is_deleted'       => 0
                                                    ,'adm_email'        => $_POST['email']
                                                )
                        ,'return_type'  =>  'single'
                    );
    if($dblms->getRows(ADMINS, $condition)) {
        $response_array['status'] = 'success';       
    }else{
        $response_array['status'] = 'error';
    }
    echo json_encode($response_array);
}
?>