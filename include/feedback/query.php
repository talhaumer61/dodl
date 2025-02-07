<?php
if (isset($_POST['submit_feedback'])) {
    $condition	=	array ( 
								 'select'		=>	'feedback_id'
								,'where'		=>	array( 
															 'id_enroll'    => cleanvars($_POST['id_enroll'])
															,'id_std'       => cleanvars($_SESSION['userlogininfo']['STDID']) 
														)
								,'return_type'	=>	'count' 
							);
	if($dblms->getRows(STUDENT_FEEDBACK, $condition, $sql)) {
		sessionMsg('Error','Record Already Exists.','danger');
		header("Location: ".SITE_URL."student/courses", true, 301);
		exit();
	} else {
        $values = array(
                             'id_enroll'            => cleanvars($_POST['id_enroll'])
                            ,'cert_type'            => cleanvars($_POST['cert_type'])
                            ,'cert_name'            => cleanvars($_POST['cert_name'])
                            ,'date_generated'       => date('Y-m-d G:i:s')
                            ,'id_std'			    => cleanvars($_SESSION['userlogininfo']['STDID'])
                            ,'std_name'			    => cleanvars($_SESSION['userlogininfo']['LOGINNAME'])
                            ,'id_added'             => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                            ,'date_added'           => date('Y-m-d G:i:s')
                        ); 
        $sqllms = $dblms->insert(STUDENT_FEEDBACK, $values);
        if($sqllms) { 
			$latestID =	$dblms->lastestid();

            // DETAIL SAVE
            foreach ($_POST['question'] as $keyQst => $valQst) {
                $values = array(
                                     'id_feedback'      => cleanvars($latestID)
                                    ,'id_question'      => cleanvars($valQst)
                                    ,'answer'           => cleanvars($_POST['answer'][$keyQst])
                                );
                $sqllms = $dblms->insert(STUDENT_FEEDBACK_DETAIL, $values);
            }
			sendRemark('Feedback Submitted', '1', $latestID);
			sessionMsg('Successfully', 'Record Successfully Added.', 'success');
			header("Location: ".SITE_URL."certificate-print/".ZONE."", true, 301);
			exit();
		}
    }
}
?>