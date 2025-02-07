<?php
// SIGN UP
if(isset($_POST['signup_as_student']) || isset($_POST['sigup_as_instructor'])) {
	if (empty($_SESSION['userlogininfo']['LOGINIDA'])) { 
		$condition	=	array ( 
									'select'		=> "adm_username"
									,'where'		=> array( 
																'adm_username'		=> cleanvars($_POST['adm_username'])
																,'is_deleted'		=> '0'
															)
									,'return_type'	=> 'count' 
								); 
		if($dblms->getRows(ADMINS, $condition)) {
			sessionMsg('Error','User Name Already Exist.','danger');
			header("Location: ".SITE_URL."signup", true, 301);
			exit();
		} else {
			$array 				= get_PasswordVerify($_POST['adm_userpass']);
			$std_password 		= $array['hashPassword'];
			$salt 				= $array['salt'];	

			$values = array(
								 'adm_status'		=> 1
								,'adm_type'			=> 3
								,'adm_logintype'	=> 3
								,'is_teacher'		=> 1
								,'adm_fullname'		=> cleanvars($_POST['adm_fullname'])
								,'adm_username'		=> cleanvars($_POST['adm_username'])
								,'adm_email'		=> cleanvars($_POST['adm_email'])
								,'adm_phone'		=> cleanvars($_POST['adm_phone'])
								,'adm_userpass'		=> cleanvars($std_password)
								,'adm_salt'			=> cleanvars($salt)
								,'date_added'		=> date('Y-m-d G:i:s')
							);
			$sqllms = $dblms->insert(ADMINS, $values);

			if($sqllms) {
				$latestID   =	$dblms->lastestid();
				$stdValues = array(
									 'std_loginid'			=> $latestID
									,'std_status'			=> 1
									,'std_level'			=> 1
									,'std_name'				=> cleanvars($_POST['adm_fullname'])
									,'std_gender'			=> cleanvars($_POST['adm_gender'])
									,'date_added'			=> date('Y-m-d G:i:s')
									,'id_added'				=> $latestID
								);
				if (!empty($_POST['org_id'])) {
					$stdValues['id_org']					= cleanvars($_POST['org_id']);
				}
				$sqllms = $dblms->insert(STUDENTS, $stdValues);

				get_SendMail([
					'sender'        => SMTP_EMAIL,
					'senderName'    => SITE_NAME,
					'receiver'      => cleanvars($_POST['adm_email']),
					'receiverName'  => cleanvars($_POST['adm_fullname']),
					'subject'       => "Welcome to ".TITLE_HEADER.", Your Journey Begins Here!",
					'body'          => '
						<p>
							We are excited to have you join our learning community! This email contains all the essential information to get you started on DODL.
							<br>
							<a href="https://youtu.be/5ZEUEok9Mig" target="_blank">Information video!</a>
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

				sendRemark('Student account created', '1', $latestID);
				sessionMsg('Success', 'Student account created.', 'success');
				header("Location: ".SITE_URL."signin", true, 301);
				exit();
			}
		}
	} else {
		$latestID =	cleanvars($_SESSION['userlogininfo']['LOGINIDA']);
		$empValues = array(
								 'emply_status'			=> 1
								,'emply_request'		=> 2
								,'emply_gender'			=> cleanvars($_POST['adm_gender'])
								,'emply_joining_date'	=> date('Y-m-d')
								,'emply_name'			=> cleanvars($_POST['adm_fullname'])
								,'emply_email'			=> cleanvars($_POST['adm_email'])
								,'date_added'			=> date('Y-m-d G:i:s')
								,'id_added'				=> $latestID
							);
		$sqllms = $dblms->insert(EMPLOYEES, $empValues);

		// REMARKS
		if($sqllms){
			$emplyID = $dblms->lastestid();
			$_SESSION['userlogininfo']['EMPLYID']		= cleanvars($emplyID);
			$_SESSION['userlogininfo']['EMPLYREQUEST']	= '2';
			
			get_SendMail([
				'sender'        => SMTP_EMAIL,
				'senderName'    => SITE_NAME,
				'receiver'      => cleanvars($_POST['adm_email']),
				'receiverName'  => cleanvars($_POST['adm_fullname']),
				'subject'       => "Welcome to ".TITLE_HEADER.", Your Journey Begins Here!",
				'body'          => '
					<p>
						We are excited to have you join our learning community! You account is under verification for instructor login please wait and this email contains all the essential information to get you started on DODL.
						<br>
						<a href="https://youtu.be/5ZEUEok9Mig" target="_blank">Information video!</a>
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

			sendRemark('Requested to become an instructor', '1', $latestID);
			sessionMsg('Successfully', 'Request Sent.', 'success');
			header("Location: ".SITE_URL."dashborad", true, 301);
			exit();
		}
	}
}

// FORGET PASSWORD
if(isset($_POST['submit_forgot_password'])) {
	$array 				= get_PasswordVerify(cleanvars($_POST['adm_userpass']));
	$std_password 		= $array['hashPassword'];
	$salt 				= $array['salt'];	

	$values = array(
						 'adm_userpass'		=> cleanvars($std_password)
						,'adm_salt'			=> cleanvars($salt)
						,'id_modify'		=> cleanvars($_POST['adm_id'])
						,'date_modify'		=> date('Y-m-d G:i:s')
					);
	$sqllms = $dblms->Update(ADMINS, $values, " adm_id = ".cleanvars($_POST['adm_id'])." ");

	// REMARKS
	if($sqllms){
		$lastestid = $dblms->lastestid();

		sendRemark('Account Password Changed', '2', $lastestid);
		sessionMsg('Successfully', 'Account Password Changed.', 'success');
		header("Location: ".SITE_URL."signin", true, 301);
		exit();
	}
}

// SIGNUP SKILL AMBASSADOR
if(isset($_POST['signup_skill_ambassador'])) {
	$year = date('y');
	$sqlQuery = $dblms->querylms("SELECT 
									IFNULL(
										CONCAT(
											'SA-', $year, '-', 
											LPAD(
												CAST(SUBSTRING_INDEX(MAX(org_reg), '-', -1) AS UNSIGNED) + 1, 
												5, '0'
											)
										), 
										CONCAT('SA-', $year, '-00001')
									) AS new_org_reg
									FROM ".SKILL_AMBASSADOR."
									WHERE org_reg LIKE CONCAT('SA-', $year, '-%')
								");
	$valQuery = mysqli_fetch_array($sqlQuery);

	$condition 	=	[
						 'select'		=>	'org_id'
						,'where'		=>	[
												 'org_name'		=>	cleanvars($_POST['adm_fullname'])
												,'is_deleted'	=>	'0'
											]
						,'return_type' 	=>	'count'
	]; 
	if($dblms->getRows(SKILL_AMBASSADOR, $condition)) {
		sessionMsg("Error", "Record Already Exist.", "danger");
		header("Location:".moduleName().".php", true, 301);
		exit();
	} else {
		$passArray 		= get_PasswordVerify($_POST['adm_userpass']);
		$hashPassword 	= $passArray['hashPassword'];
		$salt 			= $passArray['salt'];
		
		$values	= 	[
						 'adm_status'			=> 2 // pending
						,'adm_type'				=> 4 // type - organization
						,'adm_logintype'		=> 8 // panel - organization
						,'adm_fullname'			=> cleanvars($_POST['adm_fullname'])
						,'adm_username'			=> cleanvars($_POST['adm_username'])
						,'adm_phone'			=> cleanvars($_POST['adm_phone'])
						,'adm_userpass'			=> $hashPassword
						,'adm_salt'				=> $salt
						,'adm_email'			=> cleanvars($_POST['adm_email'])
						,'id_added'				=> cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
						,'date_added'			=> date('Y-m-d G:i:s')
		];
		$sqllms = $dblms->insert(ADMINS, $values);
		
		$latestID = $dblms->lastestid();


		$values	= 	[
						'org_status'			=> 2, // pending
						'org_type'				=> 1, // type - organization
						'allow_add_members'		=> 2, // no
						'parent_org'			=> 0,
						'id_loginid'			=> $latestID,
						'org_name'				=> cleanvars($_POST['adm_fullname']),
						'org_reg'				=> cleanvars($valQuery['new_org_reg']),
						'org_referral_link'		=> cleanvars('_'.$valQuery['new_org_reg']),
						'org_percentage'		=> cleanvars($_POST['org_percentage']),
						'org_profit_percentage'	=> cleanvars($_POST['org_profit_percentage']),
						'org_email'				=> cleanvars($_POST['adm_email']),
						'org_phone'				=> cleanvars($_POST['adm_phone']),
						'cv_url'				=> cleanvars($_POST['cv_url']),
						'id_added'				=> cleanvars($_SESSION['userlogininfo']['LOGINIDA']),
						'date_added'			=> date('Y-m-d G:i:s'),
		];
		$sqllms = $dblms->insert(SKILL_AMBASSADOR, $values);
		
		if($sqllms) { 
			$latestID = $dblms->lastestid();

			// upload file
			if (!empty($_FILES['cv_file']['name'])) {
				$path_parts = pathinfo($_FILES["cv_file"]["name"]);
				$extension  = strtolower($path_parts['extension']);
				if (in_array($extension, array('jpeg', 'jpg', 'png', 'pdf'))) {
					// directory, original file and file name
					$file_dir		= '../../uploads/files/organization_cv/';
					$file_name		= to_seo_url(cleanvars($_POST['adm_fullname']).'-'.$valQuery['new_org_reg']).'-'.$latestID.".".($extension);
					$original_file	= $file_dir.$file_name;
					
					// Server-side file transfer via cURL
					$file_input_name = 'cv_file'; // This can be dynamically set based on your form input name
					$file = $_FILES[$file_input_name]['tmp_name'];
		
					// api url
					$apiUrl = SITE_URL_PORTAL.'api/web/file_upload.php';
		
					// post data array
					$postData   =   [
										 'file_input_name'	=> $file_input_name
										,'file_name'		=> $file_name
										,'file_dir'			=> $file_dir
										,'uploaded_file'	=> new CURLFile($file, $_FILES[$file_input_name]['type'], $_FILES[$file_input_name]['name'])
									];
		
					// Call the function to send the file
					$response = sendFileToServer($apiUrl, $postData);
		
					// Handle response from the destination subdomain ---- 1 = UPLOADED, 0 = ERROR
					if ($response === '1') {
						// Update database and perform other actions as needed
						$dataFile		= array( 'cv_file' => $file_name );
						$sqlUpdateFile	= $dblms->Update(SKILL_AMBASSADOR, $dataFile, "WHERE org_id = '".$latestID."'");
					}
				}
			}

			// send mail
			get_SendMail([
				'sender'        => SMTP_EMAIL,
				'senderName'    => SITE_NAME,
				'receiver'      => cleanvars($_POST['adm_email']),
				'receiverName'  => cleanvars($_POST['adm_fullname']),
				'subject'       => "Welcome to ".TITLE_HEADER.", Your Journey Begins Here!",
				'body'          => '
					<p>
						We are excited to have you join our community! This email contains all the essential information to get you started with '.SITE_NAME.'.
						<br>
						your unique registration number is <b class="text-primary">'.$valQuery['new_org_reg'].'</b>
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

			// REMARKS
			sendRemark(moduleName(CONTROLER), '1', $latestID);
			sessionMsg("Success", "Request Successfully Sent.", "success");
			header("Location: ".SITE_URL."home", true, 301);
			exit();
		}
	}
}
?>