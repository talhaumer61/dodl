<?php
// ENROLL COURSES
if (isset($_POST['enrolled_courses'])) {
    $enrolled = false;
    $total_amount = 0;
    $currency_code = (__COUNTRY__ == 'pk' ? 'PKR' : 'USD');
    
    for($i=1; $i <= sizeof($_POST['id']); $i++){
        $value  = $_POST['id'][$i];
        $type   = $_POST['type'][$i];
        $learn_type   = $_POST['learn_type'][$i];
        $name   = $_POST['name'][$i];
        $amount = $_POST['amount'][$i];
        // PROGRAM
        if($type == 1){
            $condition	=	array( 
                                     'select' 		=>  'secs_id'
                                    ,'where' 		=>	array( 
                                                                 'id_ad_prg'    =>	cleanvars($value)
                                                                ,'id_type'		=>  cleanvars($type)
                                                                ,'id_std'		=>  cleanvars($_SESSION['userlogininfo']['STDID'])
                                                                ,'is_deleted'	=>	'0'	
                                                            )
                                    ,'search_by'    =>  ' AND secs_status IN (1,2)'
                                    ,'return_type' 	=>	'count'
                                ); 
            if(!($dblms->getRows(ENROLLED_COURSES, $condition))) {
                // GET COURES
                $condition = array(
                                         'select'       =>  'GROUP_CONCAT(id_curs) courses'
                                        ,'where'        =>  array(
                                                                    'id_ad_prg'    =>   cleanvars($value)
                                                                )
                                        ,'return_type'  =>  'single'
                                    );
                $PROGRAMS_STUDY_SCHEME = $dblms->getRows(PROGRAMS_STUDY_SCHEME, $condition);

                $values = array(
                                     'secs_status'			=> ''.($learn_type == 3 ? 1 : 2).''
                                    ,'id_std'			    => cleanvars($_SESSION['userlogininfo']['STDID'])
                                    ,'id_org'			    => cleanvars(($_SESSION['userlogininfo']['LOGINORGANIZATIONID']))
                                    ,'id_curs'			    => cleanvars($PROGRAMS_STUDY_SCHEME['courses'])
                                    ,'id_ad_prg'            => cleanvars($value)
                                    ,'id_type'              => cleanvars($type)
                                    ,'id_added'             => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                    ,'date_added'           => date('Y-m-d G:i:s')
                                ); 
                $sqllms = $dblms->insert(ENROLLED_COURSES, $values);
                if($learn_type != 3){
                    $enroll_id[] = $dblms->lastestid();
                    $id_curs[]=$value;
                    $id_type[]=$type;
                    $curs_amount[]=$amount;
                    $lrn_type[]=$learn_type;
                }
                if ($sqllms) {
                    get_SendMail([
                        'sender'        => SMTP_EMAIL,
                        'senderName'    => SITE_NAME,
                        'receiver'      => cleanvars($_POST['adm_fullname']),
                        'receiverName'  => cleanvars($_POST['adm_email']),
                        'subject'       => "You enrolled in a course from ".TITLE_HEADER."",
                        'body'          => '
                            <p>
                                Congratulations on enrolling in the Degree('.$name.')! Here are the details and next steps to begin your learning journey.
                                <br>
                                <b>Orientation for New Learner</b>
                                <br>
                                Join course orientation to learn more about LMS, and get tips for success. 
                                <br>
                                <b>Click here for orientation</b>
                                <br>
                                <a href="https://youtu.be/5ZEUEok9Mig" target="_blank">Orientation video!</a>
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
                    $enrolled = true;
                    $dblms->querylms('DELETE FROM '.WISHLIST.' WHERE id_ad_prg = "'.cleanvars($value).'" AND id_type = "'.cleanvars($type).'" AND id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'" ');
                }                
            }
        }
        // MASTER TRACK
        elseif($type == 2){
            $condition	=	array( 
                                     'select' 		=>  'secs_id'
                                    ,'where' 		=>	array( 
                                                                 'id_mas'       =>	cleanvars($value)
                                                                ,'id_std'		=>  cleanvars($_SESSION['userlogininfo']['STDID'])
                                                                ,'id_type'      =>  cleanvars($type)
                                                                ,'is_deleted'	=>	'0'	
                                                            )
                                    ,'search_by'    =>  ' AND secs_status IN (1,2)'
                                    ,'return_type' 	=>	'count'
                                ); 
            if(!($dblms->getRows(ENROLLED_COURSES, $condition))) {
                // GET COURES
                $condition = array(
                                         'select'       =>  'GROUP_CONCAT(id_curs) courses'
                                        ,'where'        =>  array(
                                                                    'id_mas'    =>   cleanvars($value)
                                                                )
                                        ,'return_type'  =>  'single'
                                    );
                $MASTER_TRACK_DETAIL = $dblms->getRows(MASTER_TRACK_DETAIL, $condition);

                $values = array(
                                     'secs_status'			=> ''.($learn_type == 3 ? 1 : 2).''
                                    ,'id_std'			    => cleanvars($_SESSION['userlogininfo']['STDID'])
                                    ,'id_org'			    => cleanvars(($_SESSION['userlogininfo']['LOGINORGANIZATIONID']))
                                    ,'id_type'              => cleanvars($type)
                                    ,'id_curs'			    => cleanvars($MASTER_TRACK_DETAIL['courses'])
                                    ,'id_mas'               => cleanvars($value)
                                    ,'id_added'             => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                    ,'date_added'           => date('Y-m-d G:i:s')
                                ); 
                $sqllms = $dblms->insert(ENROLLED_COURSES, $values);
                if($learn_type != 3){
                    $enroll_id[] = $dblms->lastestid();
                    $id_curs[]=$value;
                    $id_type[]=$type;
                    $curs_amount[]=$amount;
                    $lrn_type[]=$learn_type;
                }
                if ($sqllms) {
                    get_SendMail([
                        'sender'        => SMTP_EMAIL,
                        'senderName'    => SITE_NAME,
                        'receiver'      => cleanvars($_POST['adm_fullname']),
                        'receiverName'  => cleanvars($_POST['adm_email']),
                        'subject'       => "You enrolled in a course from ".TITLE_HEADER."",
                        'body'          => '
                            <p>
                                Congratulations on enrolling in the Master Track('.$name.')! Here are the details and next steps to begin your learning journey.
                                <br>
                                <b>Orientation for New Learner</b>
                                <br>
                                Join course orientation to learn more about LMS, and get tips for success. 
                                <br>
                                <b>Click here for orientation</b>
                                <br>
                                <a href="https://youtu.be/5ZEUEok9Mig" target="_blank">Orientation video!</a>
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
                    $enrolled = true;
                    $dblms->querylms('DELETE FROM '.WISHLIST.' WHERE id_mas = "'.cleanvars($value).'" AND id_type = "'.cleanvars($type).'" AND id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'" ');
                }                
            }
        }
        // COURSE OR e-Training
        elseif($type == 3 || $type == 4){
            $condition	=	array( 
                                     'select' 		=>  'secs_id'
                                    ,'where' 		=>	array( 
                                                                 'id_curs'      =>	cleanvars($value)
                                                                ,'id_std'		=>  cleanvars($_SESSION['userlogininfo']['STDID'])
                                                                ,'id_type'      =>  cleanvars($type)
                                                                ,'is_deleted'	=>	'0'	
                                                            )
                                    ,'search_by'    =>  ' AND secs_status IN (1,2)'
                                    ,'return_type' 	=>	'count'
                                ); 
            if(!($dblms->getRows(ENROLLED_COURSES, $condition))) {
                $values = array(
                                     'secs_status'			=> ''.($learn_type == 3 ? 1 : 2).''
                                    ,'id_std'			    => cleanvars($_SESSION['userlogininfo']['STDID'])
                                    ,'id_org'			    => cleanvars(($_SESSION['userlogininfo']['LOGINORGANIZATIONID']))
                                    ,'id_curs'              => cleanvars($value)
                                    ,'id_type'              => cleanvars($type)
                                    ,'id_added'             => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                    ,'date_added'           => date('Y-m-d G:i:s')
                                ); 
                $sqllms = $dblms->insert(ENROLLED_COURSES, $values);
                if($learn_type != 3){
                    $enroll_id[] = $dblms->lastestid();
                    $id_curs[]=$value;
                    $id_type[]=$type;
                    $curs_amount[]=$amount;
                    $lrn_type[]=$learn_type;
                }
                if ($sqllms) {    
                    get_SendMail([
                        'sender'        => SMTP_EMAIL,
                        'senderName'    => SITE_NAME,
                        'receiver'      => cleanvars($_POST['adm_fullname']),
                        'receiverName'  => cleanvars($_POST['adm_email']),
                        'subject'       => "You enrolled in a course from ".TITLE_HEADER."",
                        'body'          => '
                            <p>
                                Congratulations on enrolling in the Course('.$name.')! Here are the details and next steps to begin your learning journey.
                                <br>
                                <b>Orientation for New Learner</b>
                                <br>
                                Join course orientation to learn more about LMS, and get tips for success. 
                                <br>
                                <b>Click here for orientation</b>
                                <br>
                                <a href="https://youtu.be/5ZEUEok9Mig" target="_blank">Orientation video!</a>
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
                    $enrolled = true;
                    $dblms->querylms('DELETE FROM '.WISHLIST.' WHERE id_curs = "'.cleanvars($value).'" AND id_type = "'.cleanvars($type).'" AND id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'" ');
                }                
            }
        }
        if($learn_type != 3){
            $total_amount += $amount;
        }
    }
    if($enrolled){
        do {
            $trans_no   = date('ym').mt_rand(10000,99999);
            $sqlChallan = "SELECT challan_no FROM ".CHALLANS." WHERE challan_no = '$trans_no'";
            $sqlCheck   = $dblms->querylms($sqlChallan);
        } while (mysqli_num_rows($sqlCheck) > 0);

        $issue_date = date('Y-m-d');
        $due_date   = date('Y-m-d', strtotime($issue_date . ' +15 days'));

        if(!empty($_POST['discount']) && !empty($_POST['after_discount_amount'])){
            $challan_amount = $_POST['after_discount_amount'];
        } else {
            $challan_amount = $_POST['total_amount'];
        }
        
        $values = array(
                             'status'			        => '2'
                            ,'id_std'                   => cleanvars($_SESSION['userlogininfo']['STDID'])
                            ,'challan_no'               => cleanvars($trans_no)
                            ,'id_enroll'			    => cleanvars(implode(",",$enroll_id))
                            ,'without_discount_amount'  => cleanvars($_POST['total_amount'])
                            ,'discount'                 => cleanvars($_POST['discount'])
                            ,'total_amount'             => cleanvars($challan_amount)
                            ,'currency_code'            => cleanvars($currency_code)
                            ,'issue_date'               => cleanvars($issue_date)
                            ,'due_date'                 => cleanvars($due_date)
                            ,'id_added'                 => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                            ,'date_added'               => date('Y-m-d G:i:s')
                        );
        $sqllms = $dblms->insert(CHALLANS, $values);        
        if($sqllms){
            $challanID = $dblms->lastestid();
            $i=0;
            foreach ($enroll_id as $value) {
                    $values = array(
                                         'status'			        => '1'
                                        ,'id_curs'                  => cleanvars($id_curs[$i])
                                        ,'id_type'                  => cleanvars($id_type[$i])
                                        ,'learn_type'               => cleanvars($lrn_type[$i])
                                        ,'amount'                   => cleanvars($curs_amount[$i])
                                        ,'id_challan'               => cleanvars($challanID)
                                        ,'id_enroll'                => cleanvars($value)
                                        ,'id_added'                 => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                        ,'date_added'               => date('Y-m-d G:i:s')
                                    );
                    $sqllms = $dblms->insert(CHALLAN_DETAIL, $values);    
                

                $i++;
            }

            // UPDATE COUPON AS APPLIED
            if(!empty($_POST['is_applied']) && $_POST['is_applied'] == 1){
                $values = array(
                                 'is_applied'               => 1
                                ,'id_modify'                => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                ,'date_modify'              => date('Y-m-d G:i:s')
                            );
                $sqllms = $dblms->update(COUPONS, $values, "WHERE cpn_code  = '".cleanvars($_POST['cpn_code'])."' AND is_deleted = '0' ");

                $couponRemarks = 'Coupon with code: '.cleanvars($_POST['cpn_code']).' Applied to a student with ID: '.cleanvars($_SESSION['userlogininfo']['LOGINIDA']).' and challan id is : '.$challanID.' ';
                sendRemark("$couponRemarks", '2', $challanID); 
            }
            $challanRemarks = 'Challan Created of enrollment IDs : '.cleanvars(implode(",",$enroll_id)).'';
            sendRemark("$challanRemarks", '1', $challanID);

            if($total_amount <= 0){
                $values = array(
                                     'status'			=> 1
                                    ,'paid_amount'		=> cleanvars($total_amount)
                                    ,'paid_date'		=> date('Y-m-d')
                                    ,'id_modify'		=> cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                    ,'date_modify'		=> date('Y-m-d G:i:s')
                                );
                $sqllms = $dblms->Update(CHALLANS, $values , "WHERE challan_id  = '".cleanvars($challanID)."' ");
                if($sqllms){
                    // enroll courses
                    $values = array(
                                         'secs_status'			=> '1'
                                        ,'id_modify'			=> cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                        ,'date_modify'			=> date('Y-m-d G:i:s')
                                    ); 
                    $sqllms = $dblms->Update(ENROLLED_COURSES, $values , "WHERE secs_id IN (".cleanvars(implode(",",$enroll_id)).") ");
        
                    // send in transactions
                    $values = array(
                                         'trans_status'			=> 1
                                        ,'trans_no'				=> cleanvars($trans_no)
                                        ,'trans_amount'         => cleanvars($total_amount)
                                        ,'currency_code'		=> cleanvars($currency_code)
                                        ,'id_enroll'			=> cleanvars(implode(",",$enroll_id))
                                        ,'id_std'               => cleanvars($_SESSION['userlogininfo']['STDID'])
                                        ,'date'           		=> date('Y-m-d')
                                        ,'id_added'             => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                        ,'date_added'           => date('Y-m-d G:i:s')
                                    ); 
                    $sqllms = $dblms->insert(TRANSACTION, $values);
        
                    // REMAKRS
                    sendRemark("Challan Paid", '2', cleanvars($challanID));
                    sessionMsg("Success", "Successfully Enrolled.", "success");
                    header("Location: ".SITE_URL."student/courses", true, 301);
                    exit();
                }
            } else {
                sessionMsg("Success!","Enrollment Request Sent","success");
                header("location: ".SITE_URL."student/challans");
            }
        }
    } else {
        sessionMsg("Error!","Error during Enrolled","danger");
        header("location: ".SITE_URL."courses");
    }
}

// LEARN FREE CHALLAN
if (isset($_POST['enrolled_learn_free'])) {
    $enrolled = false;
    $total_amount = $_POST['total_amount'];
    $currency_code = (__COUNTRY__ == 'pk' ? 'PKR' : 'USD');
    
    $value  = $_POST['id'];
    $type   = $_POST['type'];
    $learn_type   = $_POST['learn_type'];
    $name   = $_POST['name'];
    $amount = $_POST['amount'];
    $enroll_id = $_POST['enroll_id'];
    $conditions = array ( 
        'select'       =>	'challan_id'
        ,'where' 		=>	array( 
                                    'id_std'          => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                    ,'is_deleted'      => 0 
                                    ,'id_enroll'       => cleanvars($enroll_id) 
                                )
        ,'return_type'	=>	'count'
    );
    $challan = $dblms->getRows(CHALLANS, $conditions); 
        
    if(!$challan){
        do {
            $trans_no   = date('ym').mt_rand(10000,99999);
            $sqlChallan = "SELECT challan_no FROM ".CHALLANS." WHERE challan_no = '$trans_no'";
            $sqlCheck   = $dblms->querylms($sqlChallan);
        } while (mysqli_num_rows($sqlCheck) > 0);

        $issue_date = date('Y-m-d');
        $due_date   = date('Y-m-d', strtotime($issue_date . ' +15 days'));

        if(!empty($_POST['discount']) && !empty($_POST['after_discount_amount'])){
            $challan_amount = $_POST['after_discount_amount'];
        } else {
            $challan_amount = $_POST['total_amount'];
        }
        
        $values = array(
                             'status'			        => '2'
                            ,'id_std'                   => cleanvars($_SESSION['userlogininfo']['STDID'])
                            ,'challan_no'               => cleanvars($trans_no)
                            ,'id_enroll'			    => cleanvars($enroll_id)
                            ,'without_discount_amount'  => cleanvars($_POST['total_amount'])
                            // ,'discount'                 => cleanvars($_POST['discount'])
                            ,'discount'                 => ($_POST['total_amount'] - $challan_amount)
                            ,'total_amount'             => cleanvars($challan_amount)
                            ,'currency_code'            => cleanvars($currency_code)
                            ,'issue_date'               => cleanvars($issue_date)
                            ,'due_date'                 => cleanvars($due_date)
                            ,'id_added'                 => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                            ,'date_added'               => date('Y-m-d G:i:s')
                        );
        $sqllms = $dblms->insert(CHALLANS, $values);        
        if($sqllms){
            $challanID = $dblms->lastestid();
            $values = array(
                'status'			        => '1'
               ,'id_curs'                  => cleanvars($value)
               ,'id_type'                  => cleanvars($type)
               ,'learn_type'               => cleanvars($learn_type)
               ,'amount'                   => cleanvars($amount)
               ,'id_challan'               => cleanvars($challanID)
               ,'id_enroll'                => cleanvars($enroll_id)
               ,'id_added'                 => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
               ,'date_added'               => date('Y-m-d G:i:s')
            );
            $sqllms = $dblms->insert(CHALLAN_DETAIL, $values);

            // UPDATE COUPON AS APPLIED
            if(!empty($_POST['is_applied']) && $_POST['is_applied'] == 1){
                $values = array(
                                 'is_applied'               => 1
                                ,'id_modify'                => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                ,'date_modify'              => date('Y-m-d G:i:s')
                            );
                $sqllms = $dblms->update(COUPONS, $values, "WHERE cpn_code  = '".cleanvars($_POST['cpn_code'])."' AND is_deleted = '0' ");

                $couponRemarks = 'Coupon with code: '.cleanvars($_POST['cpn_code']).' Applied to a student with ID: '.cleanvars($_SESSION['userlogininfo']['LOGINIDA']).' and challan id is : '.$challanID.' ';
                sendRemark("$couponRemarks", '2', $challanID); 
            }
            $challanRemarks = 'Challan Created of enrollment IDs : '.cleanvars($enroll_id).'';
            sendRemark("$challanRemarks", '1', $challanID);

            if($total_amount <= 0){
                $values = array(
                                     'status'			=> 1
                                    ,'paid_amount'		=> cleanvars($total_amount)
                                    ,'paid_date'		=> date('Y-m-d')
                                    ,'id_modify'		=> cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                    ,'date_modify'		=> date('Y-m-d G:i:s')
                                );
                $sqllms = $dblms->Update(CHALLANS, $values , "WHERE challan_id  = '".cleanvars($challanID)."' ");
                if($sqllms){
                    // enroll courses
                    $values = array(
                                         'secs_status'			=> '1'
                                        ,'id_modify'			=> cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                        ,'date_modify'			=> date('Y-m-d G:i:s')
                                    ); 
                    $sqllms = $dblms->Update(ENROLLED_COURSES, $values , "WHERE secs_id IN (".cleanvars(implode(",",$enroll_id)).") ");
        
                    // send in transactions
                    $values = array(
                                         'trans_status'			=> 1
                                        ,'trans_no'				=> cleanvars($trans_no)
                                        ,'trans_amount'         => cleanvars($total_amount)
                                        ,'currency_code'		=> cleanvars($currency_code)
                                        ,'id_enroll'			=> cleanvars($enroll_id)
                                        ,'id_std'               => cleanvars($_SESSION['userlogininfo']['STDID'])
                                        ,'date'           		=> date('Y-m-d')
                                        ,'id_added'             => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
                                        ,'date_added'           => date('Y-m-d G:i:s')
                                    ); 
                    $sqllms = $dblms->insert(TRANSACTION, $values);
        
                    // REMAKRS
                    sendRemark("Challan Paid", '2', cleanvars($challanID));
                    sessionMsg("Success", "Successfully Enrolled.", "success");
                    header("Location: ".SITE_URL."student/courses", true, 301);
                    exit();
                }
            } else {
                sessionMsg("Success!","Challan Created of enrollment IDs : ".cleanvars($enroll_id)."","success");
                header("location: ".SITE_URL."student/challans");
            }
        }
    } else {
        sessionMsg("Error!","Challan Already Exists","danger");
        header("location: ".SITE_URL."courses");
    }
}

// SHOW INTEREST IN COURSES
if (isset($_POST['interest_submit'])) {
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
		header("Location:".SITE_URL.$_POST['url']."", true, 301);
		exit();
	}else{
        $values = array(
                             'status'           => 1
                            ,'name'             => cleanvars($_POST['name'])
                            ,'email'            => cleanvars($_POST['email'])
                            ,'type'             => cleanvars($_POST['type'])
                            ,'id_interest'      => cleanvars($_POST['id'])
                            ,'city'             => cleanvars($_POST['city'])
                            ,'remarks'          => cleanvars($_POST['remarks'])
                            ,'ip_posted'        => cleanvars(LMS_IP)
                            ,'date_posted'      => date('Y-m-d G:i:s')
                        ); 
        $sqllms = $dblms->insert(STUDENT_INTERESTED_COURSES, $values);
             
        if($sqllms){
            $latestID = $dblms->lastestid();
            sendRemark(moduleName(CONTROLER)." posted", '1', $latestID);
            sessionMsg("Success","Interest Request Sent","success");
            header("location: ".SITE_URL.$_POST['url']."");
        } else {        
            sessionMsg("Error","Something went wrong","danger");
            header("location: ".SITE_URL.$_POST['url']."");
        }
    }
}
?>