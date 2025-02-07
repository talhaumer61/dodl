<?php
// page-1
if (isset($_POST['submit_interest_1'])) {
    $condition	=	array ( 
								 'select'		=>	'id'
								,'where'		=>	array( 
															'email'       => cleanvars($_POST['email'])
														)
								,'return_type'	=>	'count'
							);
	if($dblms->getRows(TEACHER_INTEREST, $condition, $sql)) {
		sessionMsg('Error','Record Already Exists.','danger');
		header("Location: ".SITE_URL."", true, 301);
		exit();
	} else {
        $values = array(
                             'status'                   => 1
                            ,'fullname'                 => cleanvars($_POST['fullname'])
                            ,'department'               => cleanvars($_POST['department'])
                            ,'designation'              => cleanvars($_POST['designation'])
                            ,'organization'             => cleanvars($_POST['organization'])
                            ,'teaching_experience'      => cleanvars($_POST['teaching_experience'])
                            ,'professional_experience'  => cleanvars($_POST['professional_experience'])
                            ,'email'                    => cleanvars($_POST['email'])
                            ,'phone_no'                 => cleanvars($_POST['phone_no'])
                            ,'linkedin_link'            => cleanvars($_POST['linkedin_link'])
                            ,'youtube_link'             => cleanvars($_POST['youtube_link'])
                            ,'request_ip'               => cleanvars(LMS_IP)
                            ,'request_datetime'         => date('Y-m-d G:i:s')
                        ); 
        $sqllms = $dblms->insert(TEACHER_INTEREST, $values);
        if($sqllms) { 
			$latestID =	$dblms->lastestid();
            $_SESSION['teacher_interest_id'] = $latestID;

            // DETAIL SAVE
            foreach ($_POST['question'] as $keyQst => $valQst) {                
                $values = array(
                                     'id_setup'         => cleanvars($latestID)
                                    ,'id_question'      => cleanvars($valQst)
                                );
                if($_POST['type'][$keyQst] == 3){
                    $values['option_selected'] = cleanvars($_POST['option'][$keyQst]);
                    if($_POST['option'][$keyQst] == 5){
                        $values['description_answer'] = cleanvars($_POST['answer'][$keyQst]);
                    }
                } else {
                    $values['description_answer'] = cleanvars($_POST['answer'][$keyQst]);
                }
                $sqllms = $dblms->insert(TEACHER_INTEREST_DETAIL, $values);
            }
			sendRemark('Request Submitted', '1', $latestID);
			sessionMsg('Successfully', 'Record Successfully Added.', 'success');
			header("Location: ".SITE_URL.CONTROLER."/page-2", true, 301);
			exit();
		}
    }
}

// page-2
if (isset($_POST['submit_interest_2'])) {
    $latestID = $_SESSION['teacher_interest_id'];

    // DETAIL SAVE
    foreach ($_POST['question'] as $keyQst => $valQst) {                
        $values = array(
                             'id_setup'         => cleanvars($latestID)
                            ,'id_question'      => cleanvars($valQst)
                        );
        if($_POST['type'][$keyQst] == 3){
            $values['option_selected'] = cleanvars($_POST['option'][$keyQst]);
            if($_POST['option'][$keyQst] == 5){
                $values['description_answer'] = cleanvars($_POST['answer'][$keyQst]);
            }
        } else {
            $values['description_answer'] = cleanvars($_POST['answer'][$keyQst]);
        }
        $sqllms = $dblms->insert(TEACHER_INTEREST_DETAIL, $values);
    }
    sendRemark('Request Submitted', '1', $latestID);
    sessionMsg('Successfully', 'Request Submitted.', 'success');
    header("Location: ".SITE_URL."", true, 301);
    exit();
}
?>