<?php
// MCDL LOGIN_FUNC
session_start();

// CHECK ALREADY SIGN IN
function checkCpanelLMSSTDLogin() {

	if(isset($_POST['submit_signin'])):
		return cpanelLMSSignInAsStudent();
	endif;
	
	if(!isset($_SESSION['userlogininfo'])):
		header("Location: ".SITE_URL."");
		exit;
	endif;

}

// SIGN IN FUNCTION
function cpanelLMSSignInAsStudent() {
	require_once ("include/dbsetting/lms_vars_config.php");
	require_once ("include/dbsetting/classdbconection.php");
	require_once ("include/functions/functions.php");
	$dblms = new dblms();

	if (isset($_COOKIE['SWITCHTOSTUDENT']) && !empty($_COOKIE['SWITCHTOSTUDENT'])):

		$decrypt_username 	= get_dataHashing($_COOKIE['SWITCHTOSTUDENT'],false);
		
		$d_cond = array ( 
							 'select' 		=>	'user_pass'
							,'where' 		=>	array( 
														'user_name' => $decrypt_username 
													) 	
							,'not_equal'	=>	array(
														'user_pass' => ''
													)
							,'order_by'	    =>	'id DESC'
							,'return_type'	=>	'single'
		); 			
		$e_row = $dblms->getRows(LOGIN_HISTORY, $d_cond);
	endif;

	$adm_username   = ((isset($_POST['login_id']) && !empty($_POST['login_id']))? cleanvars($_POST['login_id']): cleanvars($decrypt_username));
	$adm_userpass  	= ((isset($_POST['user_pass']) && !empty($_POST['user_pass']))? cleanvars($_POST['user_pass']): cleanvars($e_row['user_pass']));

	$errorMessage 	= (empty($adm_username))?'You must enter your User Name'		:'';
	$errorMessage 	= (empty($adm_userpass))?'You must enter the User Password'		:'';

	if (!empty($adm_username) && !empty($adm_userpass)) {

		$loginconditions = array ( 
									 'select' 		=>	'a.*, s.std_id, s.std_level, s.std_gender, s.id_org, e.emply_request'
									,'join' 		=>	'LEFT JOIN '.STUDENTS.' s ON s.std_loginid = a.adm_id AND s.std_status = 1 AND s.is_deleted = 0
														 LEFT JOIN '.EMPLOYEES.' e ON e.id_added = a.adm_id AND e.emply_status = 1 AND e.is_deleted = 0'
									,'where' 		=>	array( 
																 'a.adm_status'		=> '1'
																,'a.is_deleted' 	=> '0'
															)
									,'search_by'	=>	' AND a.is_teacher IN (1,3) AND (a.adm_username = "'.$adm_username.'" OR a.adm_email = "'.$adm_username.'" )'
									,'return_type'	=>	'single'
		); 		
		$row = $dblms->getRows(ADMINS.' a', $loginconditions);
	
		if ($row) {

			// PASSWORD HASHING
			$salt 		= $row['adm_salt'];
			$password 	= hash('sha256', $adm_userpass . $salt);			
			for ($round = 0; $round < 65536; $round++) {
				$password = hash('sha256', $password . $salt);
			}

			if($password == $row['adm_userpass']) { 				
				
				$dataLog = array(
									 'login_type'		=> cleanvars($row['adm_logintype'])
									,'id_login_id'		=> cleanvars($row['adm_id'])
									,'user_name'		=> cleanvars($adm_username)
									,'user_pass'		=> cleanvars($adm_userpass)
									,'id_campus'		=> cleanvars($row['id_campus'])
									,'dated'			=> date("Y-m-d G:i:s")
				);

				$sqllmslog  = $dblms->Insert(LOGIN_HISTORY , $dataLog);

				// CHECK STUDENT IMAGE EXIST
				if($row['std_gender'] == '2'){
					$adm_photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
				}else{            
					$adm_photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
				}
				if(!empty($row['adm_photo'])){
					$file_url = SITE_URL_PORTAL.'uploads/images/admin/'.$row['adm_photo'];
					if (check_file_exists($file_url)) {
						$adm_photo = $file_url;
					}
				}
				
				// REFERRAL_CONTROL
				if (!empty($_SESSION['webSiteStorage']['COURSE_REFERRAL_VIEW'])) {
					$ref_array        = explode(',', get_dataHashingOnlyExp($_SESSION['webSiteStorage']['COURSE_REFERRAL_VIEW'], false));
					if ($row['adm_email'] == $ref_array[3]) {
						$curs_href        = cleanvars($ref_array[0]);
						$ref_percentage   = cleanvars($ref_array[1]);
						$ref_id           = cleanvars($ref_array[2]);
						$adm_email        = cleanvars($ref_array[3]);
						$condition = array(
											 'select'       =>  'r.ref_id'
											,'join'         =>  'INNER JOIN '.REFERRAL_TEACHER_SHARING.' AS rt ON rt.id_ref = r.ref_id AND FIND_IN_SET("'.$adm_email.'", rt.std_emails) AND rt.ref_shr_status = "1" AND rt.is_deleted = "0"
																	LEFT JOIN '.ADMINS.' AS a ON FIND_IN_SET(a.adm_email, rt.std_emails) AND a.adm_status = "1" AND a.is_deleted = "0"'
											,'where'        =>  array(
																		 'r.is_deleted' =>	0
																		,'r.ref_status' =>	1
																		,'r.ref_id'     =>	$ref_id
																	)
											,'group_by'     =>  ' a.adm_id '
											,'search_by'    =>  ' AND r.ref_date_time_from < "'.date('Y-m-d G:i:s').'" AND r.ref_date_time_to > "'.date('Y-m-d G:i:s').'"'
											,'return_type'  =>  'single'
						);
						$REFERRAL_CONTROL = $dblms->getRows(REFERRAL_CONTROL.' AS r', $condition);
						if ($REFERRAL_CONTROL) {
							$ref_url = SITE_URL.$_SESSION['webSiteStorage']['COURSE_REFERRAL_CONTROLER']."/".$_SESSION['webSiteStorage']['COURSE_REFERRAL_ZONE']."/".$_SESSION['webSiteStorage']['COURSE_REFERRAL_VIEW'];
						}
					}
				}

				$userlogininfo = array();
					$userlogininfo['LOGINIDA'] 			=	$row['adm_id'];
					$userlogininfo['LOGINTYPE'] 		=	$row['adm_type'];
					$userlogininfo['LOGINAFOR'] 		=	$row['adm_logintype'];
					$userlogininfo['LOGINUSER'] 		=	$row['adm_username'];
					$userlogininfo['LOGINNAME'] 		=	$row['adm_fullname'];
					$userlogininfo['LOGINEMAIL'] 		=	$row['adm_email'];
					$userlogininfo['LOGINPHONE'] 		=	$row['adm_phone'];
					$userlogininfo['LOGINPHOTO'] 		=	$adm_photo;
					$userlogininfo['LOGINCAMPUS'] 		=	$row['id_campus'];
					$userlogininfo['STDID'] 			=	$row['std_id'];
					$userlogininfo['STDLEVEL'] 			=	$row['std_level'];
					$userlogininfo['STDGENDER']			=	$row['std_gender'];
					$userlogininfo['LOGINISTEACHER']	=	$row['is_teacher']; // 1=student, 2=teacher, 3=both
					$userlogininfo['EMPLYREQUEST']		=	$row['emply_request']; // 1=approve, 2=pending, 3=rejected
					if (!empty($row['id_org'])) {
						$userlogininfo['LOGINORGANIZATIONID'] 	= 	$row['id_org'];
					}
				$_SESSION['userlogininfo'] 				=	$userlogininfo;
				$_SESSION['SHOWNOTIFICATION'] 			= 1;

				// UNSET COOKIE MAIN
				$e_url = 'mul.edu.pk';
				setcookie('SWITCHTOSTUDENT','',time()-86400,'/',$e_url);

				sessionMsg('Successfully', 'You are Log on Successfully.', 'success');
				if (isset($_SESSION['search_path'])) {
					if ($REFERRAL_CONTROL) {
						header("Location: ".$ref_url);
					} else {
						header("Location: ".$_SESSION['search_path']);
					}
				} else {
					if ($REFERRAL_CONTROL) {
						header("Location: ".$ref_url);
					} else {
						header("Location: ".SITE_URL."dashboard");
					}
				}
				exit();
			} else {
				$errorMessage = '<p class="text-danger">Invalid User Password.</p>';
			}
			
		} else {
			$errorMessage = '<p class="text-danger">Invalid User Name or Password.</p>';
		}		
	}
	return $errorMessage;
}

// LOTOUT FUNCTION
function panelLMSSTDLogout() {
	if (isset($_SESSION['userlogininfo']['LOGINIDA'])) {
		unset($_SESSION['userlogininfo']);
		session_destroy();
	}
	session_start();
	sessionMsg("Logout","Your are Logout","danger");
	header("Location: ".SITE_URL);
	exit;
}
?>