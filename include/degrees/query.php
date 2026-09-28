<?php
if (isset($_POST['interest_submit'])) {

	validate_recaptcha(); // ? validate before processing anything

    $condition	=	array ( 
							'select' 	=> "id",
							'where' 	=> array( 
													 'email'        =>	cleanvars($_POST['email'])
													,'id_interest'  =>	cleanvars($_POST['id'])	
													,'type'         =>	cleanvars($_POST['type'])		
												),
							'return_type' 	=> 'count' 
						  ); 
	if($dblms->getRows(STUDENT_INTERESTED_COURSES, $condition)) {
		sessionMsg("Error", "Record Already Exist.", "danger");
		header("Location:".SITE_URL."", true, 301);
		exit();
	}else{
        $values = array(
                             'status'           => 1
                            ,'name'             => cleanvars($_POST['name'])
                            ,'email'            => cleanvars($_POST['email'])
                            ,'type'             => 1
                            ,'id_interest'      => cleanvars($_POST['id_interest'])
                            ,'city'             => cleanvars($_POST['city'])
                            ,'whatsapp'         => cleanvars($_POST['whatsapp'])
                            ,'remarks'          => cleanvars($_POST['remarks'])
                            ,'ip_posted'        => cleanvars(LMS_IP)
                            ,'date_posted'      => date('Y-m-d G:i:s')
                        ); 
        $sqllms = $dblms->insert(STUDENT_INTERESTED_COURSES, $values);
             
        if($sqllms){
            $latestID = $dblms->lastestid();
            sendRemark(moduleName(CONTROLER)." posted", '1', $latestID);
            sessionMsg("Success","Interest Request Sent","success");
            header("location: ".SITE_URL."");
        } else {        
            sessionMsg("Error","Something went wrong","danger");
            header("location: ".SITE_URL."/degrees");
        }
    }
}
?>