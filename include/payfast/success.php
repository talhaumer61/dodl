<?php
//Require Vars, DB Connection and Function Files
require_once ("include/dbsetting/lms_vars_config.php");
require_once('include/dbsetting/classdbconection.php');
$dblms = new dblms();
require_once('include/functions/login_func.php');
require_once('include/functions/functions.php');

//User Authentication
checkCpanelLMSSTDLogin();

/*
// current url
$url = curPageURL();

$http_response = explode("?", $url);
$responseString = $http_response[1];

// Parse the URL (optional, if you only need the query string)
$parsedUrl = parse_url($url);
$queryString = $parsedUrl['query'] ?? '';  // Use the query string if available

// Split the query string on "&"
$keyValuePairs = explode('&', $queryString);

// Initialize an array to store key-value pairs
$data_resp = [];
foreach ($keyValuePairs as $pair) {
  // Split each pair on "="
  $parts = explode('=', $pair);

  // Extract key and value (handle cases with missing values)
  $key = isset($parts[0]) ? urldecode($parts[0]) : null;  // Decode for URL-encoded values
  $value = isset($parts[1]) ? urldecode($parts[1]) : null;   // Decode for URL-encoded values

  // Add the key-value pair to the data array
  $data_resp[$key] = $value;
}
*/

if (!empty($_GET)) {
	if ($_GET['err_code'] == '000' || $_GET['err_code'] == '00') {
		
		// GET CHALLAN INFO
		$conditions = array ( 
								 'select' 		=>	'*'
								,'where' 		=>	array( 
															 'is_deleted'    =>  '0'
															,'challan_id'	=>  cleanvars(ZONE)
															,'challan_no'	=>	cleanvars($_GET['basket_id']) 
														)
								,'return_type'	=>	'single'
							); 
		$row = $dblms->getRows(CHALLANS, $conditions, $sql);

		// UPDATE CHALLAN
		$values = array(
							 'status'			=> 1
							,'paid_amount'		=> cleanvars($_GET['transaction_amount'])
							,'paid_date'		=> date('Y-m-d', strtotime(cleanvars($_GET['order_date'])))
							,'id_modify'		=> cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
							,'date_modify'		=> date('Y-m-d G:i:s')
						);
		$sqllms = $dblms->Update(CHALLANS, $values , "WHERE challan_id  = '".cleanvars(ZONE)."' AND challan_no = '".cleanvars($_GET['basket_id'])."'");

		// if challan paid
		if($sqllms){
			// enroll courses
			$values = array(
								 'secs_status'			=> '1'
								,'id_modify'			=> cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
								,'date_modify'			=> date('Y-m-d G:i:s')
							); 
			$sqllms = $dblms->Update(ENROLLED_COURSES, $values , "WHERE secs_id IN (".cleanvars($row['id_enroll']).") ");

			// send in transactions
			$values = array(
								 'trans_status'			=> 1
								,'trans_no'				=> cleanvars($_GET['basket_id'])
								,'trans_amount'         => cleanvars($_GET['transaction_amount'])
								,'currency_code'		=> cleanvars($_GET['transaction_currency'])
								,'id_enroll'			=> cleanvars($row['id_enroll'])
								,'id_std'               => cleanvars(LMS_VIEW)
								,'date'           		=> date('Y-m-d', strtotime(cleanvars($_GET['order_date'])))
								,'id_added'             => cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
								,'date_added'           => date('Y-m-d G:i:s')
							); 
			$sqllms = $dblms->insert(TRANSACTION, $values);

			// send in api transactions
			$values = array(
								  'status'				=> '1'
								, 'id_api'				=> '8'
								, 'customer_code'		=> 'MNHAJ'
								, 'branch_code'			=> 'PayFast'
								, 'challan_no'			=> $_GET['basket_id']
								, 'trans_id'			=> $_GET['transaction_id']
								, 'trans_amount'		=> $_GET['transaction_amount']
								, 'trans_currency'		=> $_GET['transaction_currency']
								, 'trans_date'			=> date('Y-m-d', strtotime(cleanvars($_GET['order_date'])))
								, 'date_added'			=> date("Y-m-d G:i:s")
								, 'ip'					=> cleanvars(LMS_IP)
								, 'response_detail'		=> cleanvars($responseString)
							);
			$sqllms = $dblms->Insert(APITRANSACTIONS , $values);

			// REMAKRS
			sendRemark("Challan Paid", '2', cleanvars(ZONE));
			sessionMsg("Success", "".$_GET['err_msg'].".", "success");
			header("Location: ".SITE_URL."student/courses", true, 301);
			exit();
		} else {
			sessionMsg("Error", "Something went wrong.", "danger");
			header("Location: ".SITE_URL."student/challans", true, 301);
			exit();
		}		
	} else {
		sessionMsg("Error", "Something went wrong.", "danger");
		header("Location: ".SITE_URL."student/challans", true, 301);
		exit();
	}
} else {
	sessionMsg("Error", "Something went wrong.", "danger");
	header("Location: ".SITE_URL."student/challans", true, 301);
	exit();
}
?>