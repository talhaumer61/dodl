<?php

	include "../../admin/include/dbsetting/lms_vars_config.php";
	include "../../admin/include/dbsetting/classdbconection.php";
	$dblms = new dblms();
    include "../../admin/include/functions/functions.php";
    
if(isset($_POST['email'])) {

    $sqllmscheck  = $dblms->querylms("SELECT `adm_id`
                                      FROM ".ADMINS." 
                                      WHERE adm_username = '".cleanvars($_POST['email'])."'
                                      LIMIT 1"
                                    );
    if(mysqli_num_rows($sqllmscheck)) {
        $response_array['status'] = 'error';
       
    }else{
        $response_array['status'] = 'success';
    }
    echo json_encode($response_array);
}
?>