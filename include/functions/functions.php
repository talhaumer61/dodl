<?php 
// Time Zone
date_default_timezone_set("Asia/Karachi");

//Status 
$admstatus = array (
						array('id'=>1, 'name'=>'Active'), 
						array('id'=>2, 'name'=>'Inactive')
				   );

function get_admstatus($id) {
	$listadmstatus= array (
							'1' => '<span class="label label-primary">Active</span>', 
							'2' => '<span class="label label-warning">Inactive</span>');
	return $listadmstatus[$id];
}

//Notification Status
$status = array (
	array('id'=>1, 'name'=>'Yes'), 
	array('id'=>2, 'name'=>'No')
);

function get_notification($id) {
	$listnote= array (
			'1' => '<span class="label label-success">Yes</span>', 
			'2' => '<span class="label label-warning">No</span>'
		);
	return $listnote[$id];
}

//Student Status 
$stdstatus = array (
	array('id'=>1, 'name'=>'Active')	,
	array('id'=>2, 'name'=>'Left')		,
	array('id'=>3, 'name'=>'Expel')		,
	array('id'=>4, 'name'=>'Freeze')	,
	array('id'=>5, 'name'=>'Passed')
);

function get_stdstatus($id) {
	$liststdstatus= array (
			'1' => '<span class="label label-primary">Active</span>', 
			'2' => '<span class="label label-warning">Left</span>'	, 
			'3' => '<span class="label label-danger">Expel</span>'	, 
			'4' => '<span class="label label-info">Freeze</span>', 
			'5' => '<span class="label label-success">Passed</span>'
		);
	return $liststdstatus[$id];
}

//Status 
$status = array (
					array('id'=>1, 'name'=>'Active'), 
					array('id'=>2, 'name'=>'Inactive')
				);

// Leave Status 
$statusLeave = array(
						array('id'=>1, 'name'=>'Approved'), 
						array('id'=>2, 'name'=>'Pending'), 
						array('id'=>3, 'name'=>'Rejected')
				    );
					
function get_leave($id) {
	$liststatus= array (
							'1' => '<span class="badge badge-success">Approved</span>', 
							'2' => '<span class="badge badge-warning">Pending</span>', 
							'3' => '<span class="badge badge-danger">Rejected</span>');
	return $liststatus[$id];
}

// Payments Status
$payments = array (
						array('id'=>1, 'name'=>'Paid')		, 
						array('id'=>2, 'name'=>'Pending')	, 
						array('id'=>3, 'name'=>'Unpaid')	, 
						array('id'=>4, 'name'=>'Partial Paid')
				   );

function get_payments($id) {
	$listpayments = array (		
							'1' => '<span class="badge badge-success">Paid</span>', 
							'2' => '<span class="badge badge-warning">Pending</span>', 
							'3' => '<span class="badge badge-danger">Unpaid</span>', 
							'4' => '<span class="badge badge-info">Partial Paid</span>'
						  );
	return $listpayments[$id];
}

function get_payments1($id) {
	$listpayments = array (
							'1' => 'Paid'		, 
							'2' => 'Pending'	,
							'3' => 'Unpaid'
						  );
	return $listpayments[$id];
}

// Royalty Types 
$rolyaltyType = array (
	array('id'=>1, 'name'=>'Regular')			, 
	array('id'=>2, 'name'=>'Irregular')
);

function get_royaltyType($id) {
	$listRoyaltyType = array (
			'1' => 'Regular'		, 
			'2' => 'Irregular'
		);
	return $listRoyaltyType[$id];
}

// Royalty For 
$rolyaltyFor = array (
	array('id'=>1, 'name'=>'All Student')		, 
	array('id'=>2, 'name'=>'According to Class')
);

function get_royaltyFor($id) {
	$listRoyaltyFor = array (
			'1' => 'All Student'		, 
			'2' => 'According to Class'
		);
	return $listRoyaltyFor[$id];
}

// Royalty For
$rolyaltyAmount = array (
	array('id'=>1, 'name'=>'Fixed')		, 
	array('id'=>2, 'name'=>'Percentage')
);

function get_royaltyAmount($id) {
	$listRoyaltyAmount = array (
			'1' => 'Fixed'		, 
			'2' => 'Percentage'
		);
	return $listRoyaltyAmount[$id];
}

// Complaint Status
$status = array (
	array('id'=>1, 'name'=>'Resolved'),
	array('id'=>2, 'name'=>'Pending'), 
	array('id'=>3, 'name'=>'Rejected')
);

function get_complaint($id) {
	$listcomplaint= array (
			'1' => '<span class="label label-success">Resolved</span>', 
			'2' => '<span class="label label-warning">Pending</span>', 
			'3' => '<span class="label label-danger">Rejected</span>');
	return $listcomplaint[$id];
}

function get_complaint1($id) {
	$listcomplaint= array (
			'1' => 'Resolved', 
			'2' => 'Pending', 
			'3' => 'Rejected');
	return $listcomplaint[$id];
}

// Delivery Status 
$status = array (
					array('id'=>1, 'name'=>'Pending'), 
					array('id'=>2, 'name'=>'Onhold'), 
					array('id'=>3, 'name'=>'Accepted'), 
					array('id'=>4, 'name'=>'Dispatched'), 
					array('id'=>5, 'name'=>'Delivered'), 
					array('id'=>6, 'name'=>'Rejected')
				);

function get_delivery($id) {
	$listdelivery= array (
							'1' => '<span class="label label-dark">Pending</span>'	, 
							'2' => '<span class="label label-warning">Onhold</span>'	, 
							'3' => '<span class="label label-primary">Accepted</span>'	, 
							'4' => '<span class="label label-info">Dispatched</span>'	, 
							'5' => '<span class="label label-success">Delivered</span>'	, 
							'6' => '<span class="label label-danger">Rejected</span>');
	return $listdelivery[$id];
}

// Guardian 
$guardian = array (
	array('id'=>1, 'name'=>'Father'),
	array('id'=>2, 'name'=>'Mother'),
	array('id'=>3, 'name'=>'Brother'),
	array('id'=>4, 'name'=>'Sister'),
	array('id'=>5, 'name'=>'Uncle'),
	array('id'=>6, 'name'=>'Other')
   );

// Admins Rights
$admtypes = array (
					array('id'=>1, 'name'=>'Super Admin')	,
					array('id'=>2, 'name'=>'Teacher')	,
					array('id'=>3, 'name'=>'Student')	
				   );

function get_admtypes($id) {
	$listadmrights = array (
								 '1' => 'Super Admin'
								,'2' => 'Teacher'
								,'3' => 'Student'	
	);
	return $listadmrights[$id];
}

// Status Yes No 
$statusyesno = array (
						array('id'=>1, 'name'=>'Yes'), array('id'=>2, 'name'=>'No')
				   );

function get_statusyesno($id) {
	
	$liststatusyesno = array (
								'1'	=> 'Yes',	'2'	=> 'No'
							 );
	return $liststatusyesno[$id];
}

// Hostel Type 
$hostelype = array (
						array('id'=>1, 'name'=>'Boys'), array('id'=>2, 'name'=>'Girls')
				   );

function get_hostelype($id) {
	$listhostelype = array (
								'1'	=> 'Boys',	'2'	=> 'Girls'
							 );
	return $listhostelype[$id];
}

// Rupees in Word
function convert_number_to_words($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

// Subject Type
$subjecttype = array (
						array('id'=>1, 'name'=>'Optional'), array('id'=>2, 'name'=>'Mandatory')
				   );

function get_subjecttype($id) {
	
	$listsubjecttype= array (
							'1' => '<span class="label label-primary">Optional</span>', 
							'2' => '<span class="label label-warning">Mandatory</span>');
	return $listsubjecttype[$id];
}

// Employee Type 
$emply_type = array (
						array('id'=>1, 'name'=>'Teaching'), array('id'=>2, 'name'=>'Non Teaching')
				   );

function get_emplytype($id) {
	$listemply= array (
							'1' => 'Teaching', 
							'2' => 'Non Teacheing');
	return $listemply[$id];
}

// Inquiry Type
$inquirysrc = array (
						array('id'=>1, 'name'=>'Online')
				   );

function get_inquirysrc($id) {
	$lissrc= array (
							'1' => 'Online');
	return $lissrc[$id];
}

// Transport USer Type
$type = array (
						array('id'=>1, 'name'=>'Student'), array('id'=>2, 'name'=>'Employee')
				   );

function get_usertype($id) {
	$listuser= array (
							'1' => 'Student', 
							'2' => 'Employee');
	return $listuser[$id];
}

// Attendce Keywords
$attendtype = array (
					array('id'=>1, 'name'=>'Present'),
					array('id'=>2, 'name'=>'Absent'),
					array('id'=>3, 'name'=>'Holiday'),
					array('id'=>4, 'name'=>'Late')
				   );

function get_attendtype($id) {
	$attendcetype = array (
							'1'	=> '<span class="label label-success">P</span>', 
							'2'	=> '<span class="label label-danger">A</span>', 
							'3'	=> '<span class="label label-primary">H</span>', 
							'4'	=> '<span class="label label-warning">L</span>'
							);
	return $attendcetype[$id];
}

function get_attendtype1($id) {
	$listpayments = array (
							'1' => 'Present'	, 
							'2' => 'Absent'		,
							'3' => 'Holiday'	,
							'4' => 'Late'
						  );
	return $listpayments[$id];
}

// Digital Resources 
function get_digitalresource($id) {
	$listdigitalresource = array (
							'1' => 'youtube'	, 
							'2' => 'website'	,
							'3' => 'ebook'		
						  );
	return $listdigitalresource[$id];
}

// Exam Terms
$termrtypes = array (
					array('id'=>1, 'name'=>'First Term')	,
					array('id'=>2, 'name'=>'Second Term')	,
					array('id'=>3, 'name'=>'Third Term')
				   );

function get_term($id = "") {
	$listterm = array (
						'1' => 'First Term'		, 
						'2' => 'Second Term'	, 
						'3' => 'Third Term'			
						);
	return (!empty($id) ? $listterm[$id] : $listterm);
}

// Exam Assessments 
function get_assessment($id) {
	$listassessment = array (
						'1' => 'Assessment Manual'	, 
						'2' => 'Assessment Policy'	, 
						'3' => 'Assessment Scheme'		
						);
	return $listassessment[$id];
}

// Months Keywords 
$monthtypes = array (
					array('id'=>1, 'name'=>'January'),
					array('id'=>2, 'name'=>'February'),
					array('id'=>3, 'name'=>'March'),
					array('id'=>4, 'name'=>'April'),
					array('id'=>5, 'name'=>'May'),
					array('id'=>6, 'name'=>'June'),
					array('id'=>7, 'name'=>'July'),
					array('id'=>8, 'name'=>'August'),
					array('id'=>9, 'name'=>'September'),
					array('id'=>10, 'name'=>'October'),
					array('id'=>11, 'name'=>'November'),
					array('id'=>12, 'name'=>'December')
				   );

$summermonth = array (
					array('id'=>3, 'name'=>'March'),
					array('id'=>4, 'name'=>'April'),
					array('id'=>5, 'name'=>'May')
					);

function get_monthtypes($id) {
	$month = array (
							'1'		=> 'January',
							'2'		=> 'February',
							'3'		=> 'March',
							'4'		=> 'April',
							'5'		=> 'May',
							'6'		=> 'June',
							'7'		=> 'July',
							'8'		=> 'August',
							'9'		=> 'September',
							'10'	=> 'October',
							'11'	=> 'November',
							'12'	=> 'December'
							);
	return $month[$id];
}

//Days Keywords 
$daytypes = array (
					array('id'=>1, 'name'=>'Monday')	,
					array('id'=>2, 'name'=>'Tuesday')	,
					array('id'=>3, 'name'=>'Wednesday')	,
					array('id'=>4, 'name'=>'Thursday')	,
					array('id'=>5, 'name'=>'Friday')	,
					array('id'=>6, 'name'=>'Saturday')	,
					array('id'=>7, 'name'=>'Sunday')
				   );

function get_daytypes($id) {
	$day = array (
							'1'		=> 'Monday'		,
							'2'		=> 'Tuesday'	,
							'3'		=> 'Wednesday'	,
							'4'		=> 'Thursday'	,
							'5'		=> 'Friday'		,
							'6'		=> 'Saturday'	,
							'7'		=> 'Sunday'
							);
	return $day[$id];
} 

// Qualifications 
$qualtypes = array (
					array('id'=>1, 'name'=>'Bachelors')	,
					array('id'=>2, 'name'=>'Master')	,
					array('id'=>3, 'name'=>'Docrate')	,
					array('id'=>4, 'name'=>'Others')	
				   );

function get_qualtypes($id) {
	$qual = array (
							'1'		=> 'Bachelors'	,
							'2'		=> 'Master'		,
							'3'		=> 'Docrate'	,
							'4'		=> 'Others'
							);
	return $qual[$id];
} 

// Building 
$buildings = array (
					array('id'=>1, 'name'=>'Owned')				,
					array('id'=>2, 'name'=>'Rented')			,
					array('id'=>3, 'name'=>'To be arranged')	
					);
function get_buildings($id) {
	$build = array (
							'1'		=> 'Owned'				,
							'2'		=> 'Rented'				,
							'3'		=> 'To be arranged'		
							);
	return $build[$id];
} 

// Building Type 
$buildingtypes = array (
					array('id'=>1, 'name'=>'Resdential') ,
					array('id'=>2, 'name'=>'Commercial')		
				   );

function get_buildingtypes($id) {
	$building = array (
							'1'		=> 'Resdential'	,
							'2'		=> 'Commercial'		
							);
	return $building[$id];
} 

// Mediums 
$mediumtypes = array (
					array('id'=>1, 'name'=>'Resdential') ,
					array('id'=>2, 'name'=>'Commercial')		
				   );

function get_mediumtypes($id) {
	$medium = array (
							'1'		=> 'English'	,
							'2'		=> 'Urdu'		
							);
	return $medium[$id];
} 

// Investment Type 
$investypes = array (
					array('id'=>1, 'name'=>'Personal') 	  ,
					array('id'=>2, 'name'=>'Partnership') ,
					array('id'=>3, 'name'=>'Bank loan') 		
				   );

function get_investypes($id) {
	$investment = array (
							'1'		=> 'Personal'		,
							'2'		=> 'Partnership'	,
							'3'		=> 'Bank loan'		
							);
	return $investment[$id];
} 

// Calls 
$calltypes = array (
					array('id'=>1, 'name'=>'Incoming') ,
					array('id'=>2, 'name'=>'Out Going')		
				   );

function get_calltypes($id) {
	$calls = array (
							'1'		=> 'Incoming'	,
							'2'		=> 'Out Going'		
							);
	return $calls[$id];
} 

// Campus Type 
$buildingtype = array (
	array('id'=>1,  'name'=>'MES Owned in MQI Building')		,
	array('id'=>2,  'name'=>'MES Owned in Rented Building')		,
	array('id'=>3,  'name'=>'MES Owned in Free Building')		,
	array('id'=>3,  'name'=>'MES Franchised in MQI Building')	,
	array('id'=>3,  'name'=>'MES Franchised')					,
	array('id'=>3,  'name'=>'Affiliated with MES')		
);

function get_building($id) {
	$buildingtype = array (
							'1'		=> 'MES Owned in MQI Building'		,
							'2'		=> 'MES Owned in Rented Building'	,
							'3'		=> 'MES Owned in Free Building'		,
							'3'		=> 'MES Franchised in MQI Building' ,
							'3'		=> 'MES Franchised'					,
							'3'		=> 'Affiliated with MES'
							);
	return $buildingtype[$id];
}

// Campus For -
$campusfor = array (
	array('id'=>1, 'name'=>'Boys'), array('id'=>2, 'name'=>'Girls') , array('id'=>3, 'name'=>'Both')
);

function get_campusfor($id) {
	$listcampusfor = array (
				'1'	=> 'Boys',	'2'	=> 'Girls',	'3'	=> 'Both'
			);
	return $listcampusfor[$id];
}

// Roles 
$rolefor = array (
	array('id'=>1,  'name'=>'Head Office')	,
	array('id'=>2,  'name'=>'Campus')		,
	array('id'=>3,  'name'=>'Both')		
);

function get_rolefor($id) {
	$role = array (
							'1'		=> 'Head Office'	,
							'2'		=> 'Campus'			,
							'3'		=> 'Both'		
							);
	return $role[$id];
}

// Roles 
$roletypes = array (
					array('id'=>1,  'name'=>'Admission')	,
					array('id'=>2,  'name'=>'Academic')		,
					array('id'=>3,  'name'=>'Attendance')	,
					array('id'=>4,  'name'=>'Exams')		,
					array('id'=>5,  'name'=>'HR')			,
					array('id'=>6,  'name'=>'Frenchies')	,
					array('id'=>7,  'name'=>'Complaints')	,
					array('id'=>8,  'name'=>'Accounts')		,
					array('id'=>9,  'name'=>'HR')			,
					array('id'=>10, 'name'=>'Frenchies')	,
					array('id'=>11, 'name'=>'Accounts')		,
					array('id'=>12, 'name'=>'Hostel')		,
					array('id'=>13, 'name'=>'Stationary')	,
					array('id'=>14, 'name'=>'Front Office')	,
					array('id'=>15, 'name'=>'Library')		,
					array('id'=>16, 'name'=>'Awards')		,
					array('id'=>17, 'name'=>'Events')		,
					array('id'=>18, 'name'=>'Admins')		,
					array('id'=>19, 'name'=>'Syllabus')
				   );

function get_roletypes($id) {
	$role = array (
							'1'		=> 'Admission'		,
							'2'		=> 'Academic'		,
							'3'		=> 'Attendance'		,
							'4'		=> 'Exams'			,
							'5'		=> 'HR'				,
							'6'		=> 'Frenchies'		,
							'7'		=> 'Complaints' 	,
							'8'		=> 'Accounts'		,
							'9'		=> 'HR'				,
							'10'	=> 'Frenchies'		,
							'11'	=> 'Accounts'		,
							'12'	=> 'Hostel'			,
							'13'	=> 'Stationary'		,
							'14'	=> 'Front Office'	,
							'15'	=> 'Library'		,
							'16'	=> 'Awards'			,
							'17'	=> 'Events'			,
							'18'	=> 'Admins'			,
							'19'	=> 'Syllabus'		
							);
	return $role[$id];
}

// Transcation Type
$transtype = array (
						array('id'=>1, 'name'=>'Credit'), array('id'=>2, 'name'=>'Debit')
				   );

function get_transtype($id) {
	
	$listtranstype = array (
								'1'	=> 'Credit',	
								'2'	=> 'Debit'
							 );
	return $listtranstype[$id];
}

// Transcation Method
$paymethod = array (
						array('id'=>1, 'name'=>'Cash')		, 
						array('id'=>2, 'name'=>'Check')       , 
						array('id'=>3, 'name'=>'Online')
				   );

function get_paymethod($id) {
	$listpaymethod= array (
							'1' => '<span class="label label-primary">Cash</span>', 
							'2' => '<span class="label label-warning">Check</span>', 
							'3' => '<span class="label label-warning">Online</span>');
	return $listpaymethod[$id];
}

$country = array('Bangladaish', 'China', 'India', 'Iran', 'Pakistan');
// Fee Duration 
$feeduration = array('Yearly', 'Half', 'Quatar', 'Monthly');
// Fee Type 
$feetype = array('Refundable', 'Nonrefundable');
// Gender 
$gender = array('Female', 'Male');
// Religion 
$religion = array('Islam', 'Christan', 'Hindu', 'Sikeh', 'Any other');
// Marital Status 
$marital = array('Married', 'Single');
//-Blood Groups
$bloodgroup = array('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-');

/*function cleanvars($str) {
		$str = trim($str);
		$str = mysql_escape_string($str);

	return($str);
}
*/
function cleanvars($str){ 
	return is_array($str) ? array_map('cleanvars', $str) : str_replace("\\", "\\\\", htmlspecialchars( stripslashes($str), ENT_QUOTES)); 
}

function to_seo_url($str){
	// if($str !== mb_convert_encoding( mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
	// $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
    $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
    $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
    $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
    $str = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $str);
    $str = trim($str, '-');
	$str = strtolower($str);
    return $str;
}

// Login Types 
$logintypes = array (
	array('id'=>1, 'name'=>'admin')	,
	array('id'=>2, 'name'=>'teacher')		,
	array('id'=>3, 'name'=>'student')		,
   );

function get_logintypes($id) {
	$listlogintypes = array (

			'1'	=> 'admin',
			'2'	=> 'teacher',
			'3'	=> 'student'				
			);
	return $listlogintypes[$id];
}

// Log File Action
function get_logfile($id) {

	$listlogfile = array (
							'1' => 'Add'		, 
							'2' => 'Update'		, 
							'3' => 'Delete'		,
							'4' => 'Login'	
						  );
	return $listlogfile[$id];

}

// Arrary Search
function arrayKeyValueSearch($array, $key, $value)
{
    $results = array();
    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }
        foreach ($array as $subArray) {
            $results = array_merge($results, arrayKeyValueSearch($subArray, $key, $value));
        }
    }
    return $results;
}

//Get Current Url
function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
 return $pageURL;
}

//Days Name
$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

	// New Addition
	function alert_msg($type, $title, $msg) {
		$_SESSION['msg']['title'] 	= $title;
		$_SESSION['msg']['text'] 	= $msg;
		$_SESSION['msg']['type'] 	= $type;
	}
	//Course Types
	$course_types = array (
							array('id' => 1, 'name' => 'Office and Secretarial', 'icon' => 'university', 'mobile_icon' => 'university'),
							array('id' => 2, 'name' => 'Microsoft Office', 'icon' => 'windows', 'mobile_icon' => 'windows'),
							array('id' => 3, 'name'=>'Sage, Accounting & Bookkeeping', 'icon' => 'files-o', 'mobile_icon' => 'file'),
							array('id' => 4, 'name'=>'Business & Management', 'icon' => 'pie-chart', 'mobile_icon' => 'chartPie'),
							array('id' => 5, 'name'=>'IT', 'icon' => 'laptop', 'mobile_icon' => 'laptop'),
							array('id' => 6, 'name'=>'Web Design & Photoshop', 'icon' => 'code', 'mobile_icon' => 'code'),
							array('id' => 7, 'name'=>'Career Development', 'icon' => 'graduation-cap', 'mobile_icon' => 'graduationCap'),
							array('id' => 8, 'name'=>'Marketing', 'icon' => 'bullhorn', 'mobile_icon' => 'bullhorn')
						  );

	function get_course_type($id) {
		$listadmstatus= array (
								'1' => 'Office and Secretarial', 
								'2' => 'Microsoft Office',
								'3' => 'Sage, Accounting & Bookkeeping',
								'4' => 'Business & Management',
								'5' => 'IT',
								'6' => 'Web Design & Photoshop',
								'7' => 'Career Development',
								'8' => 'Marketing',
							  );
		return $listadmstatus[$id];
	}

	// Get Status
	function get_status($id) {
		$liststatus= array (
								'1' => '<span class="label label-primary">Active</span>', 
								'0' => '<span class="label bg-secondary">Inactive</span>');
		return $liststatus[$id];
	}

	// Month Duration 
	$monthduration = array (
		array('id'=>1, 'name'=>'Month 1'),
		array('id'=>2, 'name'=>'Month 2'),
		array('id'=>3, 'name'=>'Month 3'),
		array('id'=>4, 'name'=>'Month 4'),
		array('id'=>5, 'name'=>'Month 5'),
		array('id'=>6, 'name'=>'Month 6'),
		array('id'=>7, 'name'=>'Month 7'),
		array('id'=>8, 'name'=>'Month 8'),
		array('id'=>9, 'name'=>'Month 9'),
		array('id'=>10, 'name'=>'Month 10'),
		array('id'=>11, 'name'=>'Month 11'),
		array('id'=>12, 'name'=>'Month 12'),
		);
		
	// Get Month duration
	function get_monthduration($id) {
		$monthduration= array (
						'1' => 'Month 1', 
						'2' => 'Month 2', 
						'3' => 'Month 3', 
						'4' => 'Month 4', 
						'5' => 'Month 5', 
						'6' => 'Month 6', 
						'7' => 'Month 7', 
						'8' => 'Month 8', 
						'9' => 'Month 9', 
						'10' => 'Month 10', 
						'11' => 'Month 11', 
						'12' => 'Month 12'
					  );
		return $monthduration[$id];
	}
	
	//Levels
	$levels = array (
		array('id'=>1, 'name'=>'Beginner'),
		array('id'=>2, 'name'=>'Intermediate'),
		array('id'=>3, 'name'=>'Advanced'),
		);

	// Get Levels
	function get_levels($id) {
		$levels= array (
						'1' => 'Beginner', 
						'2' => 'Intermediate', 
						'3' => 'Advanced', 
					  );
		return $levels[$id];
	}

	//Trainings
	$trainings = array (
		array('id'=>1, 'name'=>'Types of Training'),
		array('id'=>2, 'name'=>'Staff Training'),
		);

	// Get Levels
	function get_trainings($id) {
		$trainings = array (
							'1' => 'Types of Training', 
							'2' => 'Staff Training', 
						   );
		return $trainings[$id];
	}

	//Type(Free Or Paid)
	$filterCourseTypes = array (
		array('id'=>0, 'name'=>'Free'),
		array('id'=>1, 'name'=>'Paid'),
		);

	//Duration (Weekwise)
	$filterCourseDurations = array (
		array('id'=>2, 'name'=>'2 Months'),
		array('id'=>4, 'name'=>'4 Months'),
		array('id'=>6, 'name'=>'6 Months'),
		array('id'=>8, 'name'=>'8 Months'),
		);

	//Policies
	$policies = array (
		array('id'=>1, 'name'=>'Terms'),
		array('id'=>2, 'name'=>'Privacy Policy'),
		array('id'=>3, 'name'=>'Use Policy'),
		array('id'=>4, 'name'=>'Accessibility'),
		);

	// Get Policies
	function get_policies($id) {
		$policies = array (
							'1' => 'Terms', 
							'2' => 'Privacy Policy', 
							'3' => 'Use Policy', 
							'4' => 'Accessibility', 
						  );
		return $policies[$id];
	}

	//International Locations
	$internationalLocations = array (
		array('href'=>'http://pitman-training.com', 	'name'=>'United Kingdom'),
		array('href'=>'http://pitman-training.ie', 		'name'=>'Ireland'),
		array('href'=>'http://pitman-training.es', 		'name'=>'Spain'),
		array('href'=>'http://pitman-training.ro', 		'name'=>'Romania'),
		array('href'=>'http://pitman-training.ps', 		'name'=>'Palestine'),
		array('href'=>'http://pitman-training.kw', 		'name'=>'Kuwait'),
		array('href'=>'http://pitman-training.ng', 		'name'=>'Nigeria'),
		array('href'=>'http://pitman-training.ky', 		'name'=>'Cayman Islands'),
		array('href'=>'http://pitman-training.zw', 		'name'=>'Zimbabwe'),
		array('href'=>'http://www.pitamn-training.ru', 	'name'=>'Russia'),
		array('href'=>'https://www.pitman-training.pk', 'name'=>'Pakistan'),
		);

	//evoluion (years)
	$evoluionYears = array (
		array('id'=>1837, 'name'=>'1837'),
		array('id'=>1870, 'name'=>'1870'),
		array('id'=>1889, 'name'=>'1889'),
		array('id'=>1903, 'name'=>'1903'),
		array('id'=>1914, 'name'=>'1914'),
		array('id'=>1955, 'name'=>'1955'),
		array('id'=>1992, 'name'=>'1992'),
		array('id'=>1994, 'name'=>'1994'),
		array('id'=>2000, 'name'=>'2000'),
		array('id'=>2006, 'name'=>'2006'),
		array('id'=>2011, 'name'=>'2011'),
		array('id'=>2013, 'name'=>'2013'),
		array('id'=>2014, 'name'=>'2014'),
		array('id'=>2016, 'name'=>'2016'),
		array('id'=>2017, 'name'=>'2017'),
		);

	// Image Resizing
	function createResizedgImage($file, $img_dir, $img_fileName, $t_width, $t_height){
		$sourceProperties = getimagesize($file);
		$imageType = $sourceProperties[2];
	
		switch ($imageType) {
	
			case IMAGETYPE_PNG:
				$imageResourceId = imagecreatefrompng($file); 
				$targetLayer = imageResizeTransparent($imageResourceId,$sourceProperties[0],$sourceProperties[1], $t_width, $t_height);
				imagepng($targetLayer,$img_dir. $img_fileName);
				break;

			case IMAGETYPE_GIF:
				$imageResourceId = imagecreatefromgif($file); 
				$targetLayer = imageResize($imageResourceId,$sourceProperties[0],$sourceProperties[1], $t_width, $t_height);
				imagegif($targetLayer,$img_dir. $img_fileName);
				break;

			case IMAGETYPE_JPEG:
				$imageResourceId = imagecreatefromjpeg($file); 
				$targetLayer = imageResize($imageResourceId,$sourceProperties[0],$sourceProperties[1], $t_width, $t_height);
				imagejpeg($targetLayer,$img_dir. $img_fileName);
				break;
		}
	}
	function imageResize($imageResourceId,$width,$height, $targetWidth, $targetHeight) {
		$targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
		imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
		return $targetLayer;
	}
	function imageResizeTransparent($imageResourceId,$width,$height, $targetWidth, $targetHeight) {
		$targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
		imagealphablending($targetLayer, false);
		imagesavealpha($targetLayer,true);
		$transparent = imagecolorallocatealpha($targetLayer, 255, 255, 255, 127);
		imagefilledrectangle($targetLayer, 0, 0, $targetWidth, $targetHeight, $transparent);
		imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
		return $targetLayer;
	}

	// Times Ago
	function timeAgo($time_ago)
	{
		$time_ago = strtotime($time_ago);
		$cur_time   = time();
		$time_elapsed   = $cur_time - $time_ago;
		$seconds    = $time_elapsed ;
		$minutes    = round($time_elapsed / 60 );
		$hours      = round($time_elapsed / 3600);
		$days       = round($time_elapsed / 86400 );
		$weeks      = round($time_elapsed / 604800);
		$months     = round($time_elapsed / 2600640 );
		$years      = round($time_elapsed / 31207680 );
		// Seconds
		if($seconds <= 60){
			return "just now";
		}
		//Minutes
		else if($minutes <=60){
			if($minutes==1){
				return "one minute ago";
			}
			else{
				return "$minutes minutes ago";
			}
		}
		//Hours
		else if($hours <=24){
			if($hours==1){
				return "1 hour ago";
			}else{
				return "$hours hrs ago";
			}
		}
		//Days
		else if($days <= 7){
			if($days==1){
				return "yesterday";
			}else{
				return "$days days ago";
			}
		}
		//Weeks
		else if($weeks <= 4.3){
			if($weeks==1){
				return "1 week ago";
			}else{
				return "$weeks weeks ago";
			}
		}
		//Months
		else if($months <=12){
			if($months==1){
				return "1 month ago";
			}else{
				return "$months months ago";
			}
		}
		//Years
		else{
			if($years==1){
				return "1 year ago";
			}else{
				return "$years years ago";
			}
		}
	}

	//Image Uploader from Summernote
	function summernoteImageUploader($submitted_text, $file_path, $img_url){

      	if (strpos($submitted_text, '<img') !== false && strpos($submitted_text, ';base64') !== false) {

        
			$doc = new DOMDocument();
			$doc->loadHTML($submitted_text);

			$tags = $doc->getElementsByTagName('img');

			foreach ($tags as $tag) {
				// Get base64 encoded string
				$srcStr = $tag->getAttribute('src');
				$base64EncData = substr($srcStr, ($pos = strpos($srcStr, 'base64,')) !== false ? $pos + 7 : 0);
				$base64EncData = substr($base64EncData, 0, -1);

				// Get an image file
				$img = base64_decode($base64EncData);

				// Get file type
				$dataInfo = explode(";", $srcStr)[0];
				$fileExt = str_replace('data:image/', '', $dataInfo);

				// Create a new filename for the image
				$newImageName = str_replace(".", "", uniqid("summernote_", true));
				$filename = $newImageName . '.' . $fileExt;
				$file = $file_path.$filename;

				// Save the image to disk
				$success = file_put_contents($file, $img);
				$imgUrl = $img_url.$filename;

				// Update the forum thread text with an img tag for the new image
				$newImgTag = '<img src="'.$imgUrl.'" />';

				$tag->setAttribute('src', $imgUrl);
				$tag->setAttribute('data-original-filename', $tag->getAttribute('data-filename'));
				$tag->removeAttribute('data-filename');
				$submitted_text = $doc->saveHTML();
			}
      	}
	}
	// Get Preference Types
	function get_preferance_types($id = '') {
		$preferencetypes = array (
									  '1' => 'Online'
									, '2' => 'In Class'
								);
				
		if(!empty($id)){
			return $preferencetypes[$id];
		}else{
			return $preferencetypes;
		}
		}
		// Get Online Preference
		function get_online_preferance($id = '') {
			$preferenceOnline = array (
										'1' => 'Zoom'
										, '2' => 'Google meet'
										, '3' => 'Other'
									);
					
			if(!empty($id)){
				return $preferenceOnline[$id];
			}else{
				return $preferenceOnline;
			}
		}
	// Get Time Zones
	function get_time_zone($id = '') {
		$timeZone = array (
							 '1' => 'Morning (9AM To 2PM)'
							,'2' => 'Evening (3PM To 8PM)'
						  );
				
		if(!empty($id)){
			return $timeZone[$id];
		}else{
			return $timeZone;
		}
	}
	function get_PasswordVerify($password = '', $passwordMatching = '' , $saltMatching = '') {

		if (empty($passwordMatching) && empty($saltMatching)) {
			$array = array();
			$array['password'] = $password;
			$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
			$password = hash('sha256', $password . $salt);
			for ($round = 0; $round < 65536; $round++) {
				$password = hash('sha256', $password . $salt);
			}
			$array['hashPassword'] 		= $password;
			$array['salt'] 				= $salt;
			return $array;
		} else if (!empty($password) && !empty($passwordMatching) && !empty($saltMatching)) {
			$password = hash('sha256', $password . $saltMatching);
			for ($round = 0; $round < 65536; $round++) {
				$password = hash('sha256', $password . $saltMatching);
			}
			if ($password == $passwordMatching) {
				return true;
			} else {
				return false;	
			}
		} else {
			return false;
		}

	}
// SESSION MESSAGE
function sessionMsg($title = "", $msg = "", $color = "") {
	if (!empty($title) && !empty($msg)&& !empty($color)){
		$_SESSION['msg']['title'] 	= ''.$title.'';
		$_SESSION['msg']['text'] 	= ''.$msg.'';
		$_SESSION['msg']['type'] 	= ''.$color.'';
		if (!empty($_SESSION['msg']['title']) && !empty($_SESSION['msg']['text'])&& !empty($_SESSION['msg']['type'])){
			return true;
		}else{
			return false;
		}	
	}else{
		return false;
	}
}
// Get Social Links
function get_social_links($id = '') {
	$sociallinks = array (
								  '1' => array('name' => 'Facebook'	, 'icon' => 'fa-brands fa-facebook	')
								, '2' => array('name' => 'Twitter'	, 'icon' => 'fa-brands fa-twitter	')
								, '3' => array('name' => 'Instagram', 'icon' => 'fa-brands fa-instagram	')
								, '4' => array('name' => 'LinkedIn'	, 'icon' => 'fa-brands fa-linkedin	')
								, '5' => array('name' => 'Youtube'	, 'icon' => 'fa-brands fa-youtube	')
							);
	if(!empty($id)){
		return $sociallinks[$id];
	}else{
		return $sociallinks;
	}
}

// SEND REMARKS
function sendRemark($remarks = "", $action = "", $id_record = "") {
	if (!empty($remarks) && !empty($action) && !empty($id_record)) {
		require_once("include/dbsetting/lms_vars_config.php");
		require_once("include/dbsetting/classdbconection.php");
		$dblms = new dblms();

		$values = array (
							 'id_user'		=>	cleanvars($_SESSION['userlogininfo']['LOGINIDA'])
							,'id_record'	=>	cleanvars($id_record)
							,'filename'		=>	cleanvars(CONTROLER)
							,'action'		=>	cleanvars($action)
							,'dated'		=>	date('Y-m-d G:i:s')
							,'ip'			=>	cleanvars(LMS_IP)
							,'remarks'		=>	cleanvars($remarks)
							,'id_campus'	=>	cleanvars($_SESSION['userlogininfo']['LOGINCAMPUS'])
						);
		$sqlRemarks = $dblms->insert(LOGS, $values);
		if ($sqlRemarks) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

// ENCRYPTION
function get_dataHashing($str = '', $flag = true) {
    if (!empty($str)) {
    	$e_username 		= $str;
    	$e_key 				= "m^@c$&d#~l";
    	$e_chiper 			= "AES-128-CTR";
    	$e_iv 				= "4327890237234803";
    	$e_option			= 0;
    	return (($flag)?openssl_encrypt($e_username,$e_chiper,$e_key,$e_option,$e_iv):openssl_decrypt($e_username,$e_chiper,$e_key,$e_option,$e_iv));
    } else {
        return false;
    }
}
function get_LessonWeeks($id = '') {
	$week = array();
	for ($i=1;$i<=50;$i++):
		$week[$i] = $i;
	endfor;
	return ((!empty($id))? $week[$id]: $week);
}
function get_educationtypes($id = '') {
	$listeducationtypes = array (
		'1'	=> 'Below Matric',
		'2'	=> 'Matric',
		'3'	=> 'Intermediate',
		'4'	=> 'Graduation',
		'5'	=> 'Master',
		'6'	=> 'Phd'
	);
	return (!empty($id) ? $listeducationtypes[$id] : $listeducationtypes);
}
function moduleName($flag = true) {
	$fileName = strstr(basename($_SERVER['REQUEST_URI']), '.php', true);
	if (gettype($flag) == 'string') {		
		$flag = str_replace('_',' ',$flag);
		$flag = str_replace('-',' ',$flag);
		$flag = ucwords(strtolower($flag));
		return $flag;
	}
	if ($flag) {
		return strtolower($fileName);
	} else {
		$fileName = str_replace('_',' ',$fileName);
		$fileName = str_replace('-',' ',$fileName);
		return ucwords(strtolower($fileName));
	}
}
function get_YTVideoDuration($videoId = '') {
    if (!empty($videoId)) {
        $apiUrl	= 'https://www.googleapis.com/youtube/v3/videos?id='.$videoId.'&part=contentDetails&key='.YTDATAAPI.'';
		$ch		= curl_init($apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
        $data = json_decode($response, true);
        if (isset($data['items']) && count($data['items']) > 0) {
            $interval           = new DateInterval($data['items'][0]['contentDetails']['duration']);
            $hours              = $interval->h;
            $minutes            = $interval->i;
            $seconds            = $interval->s;
            $formattedTime      = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
            return $formattedTime;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function seconds_to_time($TimeInSeconds = '') {
    if (!empty($TimeInSeconds)) {
		$seconds = round($TimeInSeconds);
		$output = sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
		return $output;        
    } else {
        return false;
    }
}
function check_file_exists($file_url){
	if(!empty($file_url)){
		// Initialize cURL session
		$ch = curl_init($file_url);
	
		// Set cURL options
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
		// Execute cURL session
		curl_exec($ch);
	
		// Check if HTTP status is 200 (OK)
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
		if ($http_status == 200) {
			return true;
		} else {
			return false;
		}
	}else{		
		return false;
	}
}
// enrollment type
$enroll_type = array (
	 array('id'=>1, 'name'=>'Degree')
	,array('id'=>2, 'name'=>'Mater Track')
	,array('id'=>3, 'name'=>'Course')
	,array('id'=>4, 'name'=>'e-Training')
);
function get_enroll_type($id) {
	$listenroll_type= array (
			 '1' => '<span class="badge badge-primary">Degree</span>'
			,'2' => '<span class="badge badge-warning">Master Track</span>'
			,'3' => '<span class="badge badge-danger">Course</span>'
			,'4' => '<span class="badge badge-info">e-Training</span>'
		);
	return $listenroll_type[$id];
}
function get_topic_content($id = '') {
	$topic_content = array 	(
							 '1'	=> 'Video'
							,'2'	=> 'Reading Material'
							,'3'	=> 'Both'
						);
	return ((!empty($id))? $topic_content[$id]: $topic_content);
}
function get_curs_domain($id = '') {
	$curs_domain = array (
							 '1'	=> 'Arts & Humanities'
							,'2'	=> 'Expository Writing'
							,'3'	=> 'Natural Sciences'
							,'4'	=> 'Quantitative Reasoning'
							,'5'	=> 'Social Sciences'
							,'6'	=> 'Civilizational'
						);
	if(!empty($id)){
		return $curs_domain[$id];
	}else{
		return $curs_domain;
	}
}
function entityDecode($str,$n) {
	$d = $str;
	for ($i=0; $i < $n; $i++) { 
		$d = html_entity_decode($d);
	}
	return $d;
}
function get_is_publish($id = '') {
	$list_is_publish= array (
										  '1' => 'Yes'
										, '2' => 'No'
									);
	if(!empty($id)){
		$is_publish_label= array (
									 '1' => '<span class="badge badge-soft-primary">Yes</span>' 
									,'2' => '<span class="badge badge-soft-warning">No</span>'
								); 
		return $is_publish_label[$id];
	}else{
		return $list_is_publish;
	}
}
function get_CourseWise($id = '') {
	$list= array (
					 '1' => 'Week'
					,'2' => 'Module'
					,'3' => 'Section'
	);
	return (!empty($id) ? $list[$id] : $list);
}
// GENDERS
function get_gendertypes($id = '') {
	$gendertypes = array (
							 '1'	=> 'Male'
							,'2'	=> 'Female'
							,'3'	=> 'Others'
						);
	if(!empty($id)){
		return $gendertypes[$id];
	}else{
		return $gendertypes;
	}
}
// COURSE RESOURCES
function get_CourseResources($id = '') {
	$CourseResources= array (
								 '1' 	=> 'Lecture Slides'
								,'2' 	=> 'Lesson Video'
								,'3' 	=> 'Google Drive Link'
								,'4' 	=> 'Web Links'
								,'5' 	=> 'General Downloads'
							);
	if(!empty($id)){
		$CourseResources= array (
									 '1' => '<span class="badge bg-primary">Lecture Slides</span>'
									,'2' => '<span class="badge bg-warning">Lesson Video</span>'
									,'3' => '<span class="badge bg-info">Google Drive Link</span>'
									,'4' => '<span class="badge bg-success">Web Links</span>'
									,'5' => '<span class="badge bg-dark">General Downloads</span>'
								); 
		return $CourseResources[$id];
	}else{
		return $CourseResources;
	}
}
function get_curs_status($id = '') {
	$curs_status = array (
									 '2'	=> 'New'
									,'4'	=> 'Trending'
									,'3'	=> 'Popular'
									,'5'	=> 'Special Course'
									,'1'	=> 'Coming Soon'
								);
	if(!empty($id)){
		$curs_status = array (
										 '1'	=> '<span class="badge badge-danger">Coming Soon</span>'
										,'2'	=> '<span class="badge badge-primary">New</span>'
										,'3'	=> '<span class="badge badge-success">Popular</span>'
										,'4'	=> '<span class="badge badge-success">Trending</span>'
										,'5'	=> '<span class="badge badge-info">Special Course</span>'
									);
		return $curs_status[$id];
	}else{
		return  $curs_status;
	}
}
function blinkMsg($title = "", $color = "") {
	if (!empty($title) && !empty($color)){
		echo'
		<div class="image-container blink-image">
			<img src="'.SITE_URL.'assets/img/offers/offer-'.$color.'.png" width="100"/>
			<span class="centered-text">'.$title.'</span>
		</div>';
	}
}
function get_dataHashingOnlyExp($str = '', $flag = true) {
    if (!empty($str)) {
        $e_key     = "m^@c$&d#~l";
        $e_chiper  = "AES-128-CTR";
        $e_iv      = "4327890237234803";
        $e_option  = 0;

        if ($flag) {
            // Encrypt and then encode to base64
            $encrypted = openssl_encrypt($str, $e_chiper, $e_key, $e_option, $e_iv);
            return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($encrypted));
        } else {
            // Decode from base64 and then decrypt
            $decoded = base64_decode(str_replace(['-', '_'], ['+', '/'], $str));
            return openssl_decrypt($decoded, $e_chiper, $e_key, $e_option, $e_iv);
        }
    } else {
        return false;
    }
}
function get_VerificationCode() {
    // $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $characters = '0123456789';
    $couponCode = '';
    for ($i = 0; $i < 4; $i++) {
        $randomIndex = rand(0, strlen($characters) - 1);
        $couponCode .= $characters[$randomIndex];
    }
    return $couponCode;
}
// MESSAGE STATUS
function get_msg_status($id = '') {
	$msg_status = array (
							 '1'	=> 'Sent'
							,'2'	=> 'Edited'
						);
	if(!empty($id)){
		return $msg_status[$id];
	}else{
		return $msg_status;
	}
}
function get_LeanerType($id = '') {
	$list	= [
				 '1' => 'Paid'
				,'2' => 'Free'
				,'3' => 'Learn Free'
	];
	if (!empty($id)) {
		$list	= [
					 '1' => '<span class="badge badge-success">Paid</span>'
					,'2' => '<span class="badge badge-warning">Free</span>'
					,'3' => '<span class="badge badge-danger">Learn Free</span>'
		];
		return $list[$id];
	} else {
		return $list;
	}
}
function get_SendMail($postDataArray, $controller) {
    $ch = curl_init('https://mcdl.mul.edu.pk/smtp-mailer/'.$controller.'');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postDataArray));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return 'Error: ' . $error_msg;
    }
    curl_close($ch);
    return $response;
}
// SECTIONS
function get_teacher_interest_section($id = '') {
	$teacher_interest_section = array (
							 '1'	=> 'Personal Information'
							,'2'	=> 'Course Offering Type'
							,'3'	=> 'Course Detail'
							,'4'	=> 'Course Detail'
							,'5'	=> 'LMS Proficiency'
							,'6'	=> 'Course Development Needs'
							,'7'	=> 'Additional Comments'
						);
	if(!empty($id)){
		return $teacher_interest_section[$id];
	}else{
		return $teacher_interest_section;
	}
}

// send file to other server
function sendFileToServer($url, $postData) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
    curl_setopt($ch, CURLOPT_URL, $url); // Endpoint for handling file transfer
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for testing
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Execute the request and get the response
    $response = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    return $response;
}
?>