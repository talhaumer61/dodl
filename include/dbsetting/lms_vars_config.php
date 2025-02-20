<?php
error_reporting(0);
ob_start();
ob_clean();
date_default_timezone_set("Asia/Karachi");
// session_start();

// GET USER LOCATION SETTING
$userip = '58.27.206.187';
// $userip = $_SERVER['REMOTE_ADDR'];
$url = "http://ip-api.com/php/{$userip}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($response !== false) {
  $userGeoInfo = unserialize($response);
  if ($userGeoInfo && $userGeoInfo['status'] === 'success') {
    define('__COUNTRY__', strtolower($userGeoInfo['countryCode']));
  } 
}

define('LMS_HOSTNAME'			, 'localhost');
define('LMS_NAME'				  , 'gptech_odl');
define('LMS_USERNAME'			, 'root');
define('LMS_USERPASS'			, '');

/*
define('LMS_HOSTNAME'			, 'localhost');
define('LMS_NAME'				  , 'neotericschools_mcdl2024');
define('LMS_USERNAME'			, 'neotericschools_mcdl24');
define('LMS_USERPASS'			, 'rMgOyiA]}dT2');
*/

// DB Tables
define('ADMINS'					                    , 'cms_admins');
define('ADMIN_ROLES'			                  , 'cms_admins_roles');
define('LOGS'					                      , 'cms_logfile');
define('LOGIN_HISTORY'			                , 'cms_login_history');
define('CURRENCIES'				                  , 'cms_currencies');
define('REGIONS'				                    , 'cms_regions');
define('COUNTRIES'				                  , 'cms_countries');
define('STATES'				                      , 'cms_states');
define('SUB_STATES'                         , 'cms_substates');
define('CITIES'                             , 'cms_cities');
define('PROGRAMS_CATEGORIES'                , 'cms_programs_categories');
define('COURSES_CATEGORIES'                 , 'cms_courses_categories');
define('FACULTIES'      		                , 'cms_faculties');
define('DEPARTMENTS'      		              , 'cms_departments');
define('COURSES'     			                  , 'cms_courses');
define('LANGUAGES'      		                , 'cms_languages');
define('COURSES_SKILLS'			                , 'cms_courses_skills');
define('ADMISSION_PROGRAMS'		              , 'cms_admission_programs');
define('ACADEMIC_SESSION'		                , 'cms_academic_session');
define('SETTINGS'		                        , 'cms_settings');
define('PROGRAMS'				                    , 'cms_programs');
define('DESIGNATIONS'			                  , 'cms_designtions');
define('SKILLS'					                    , 'cms_courses_skills');
define('EMPLOYEES'                          , 'cms_employees');
define('COURSES_INFO'                       , 'cms_courses_info');
define('COURSES_ASSIGNMENTS'                , 'cms_courses_assignments');
define('COURSES_ASSIGNMENTS_STUDENTS'       , 'cms_courses_assignmentstudents');
define('COURSES_GLOSSARY'                   , 'cms_courses_glossary');
define('COURSES_DOWNLOADS'                  , 'cms_courses_downloads');
define('COURSES_LESSONS'                    , 'cms_courses_lessons');
define('COURSES_DISCUSSION'                 , 'cms_courses_discussion');
define('COURSES_ANNOUNCEMENTS'              , 'cms_courses_announcements');
define('COURSES_FAQS'                       , 'cms_courses_faqs');
define('COURSES_BOOKS'                      , 'cms_courses_books');
define('QUESTION_BANK'                      , 'cms_question_bank');
define('QUESTION_BANK_DETAIL'               , 'cms_question_bank_detail');
define('EMPLOYEE_EXPERIENCE'                , 'cms_employee_experience');
define('EMPLOYEE_LANGUAGE_SKILLS'           , 'cms_employee_language_skills');
define('EMPLOYEE_TRAININGS'                 , 'cms_employee_trainings');
define('EMPLOYEE_MEMBERSHIPS'               , 'cms_employee_memberships');
define('EMPLOYEE_ACHIEVEMENTS'              , 'cms_employee_achievements');
define('EMPLOYEE_PUBLICATIONS'              , 'cms_employee_publications');
define('EMPLOYEE_EDUCATIONS'                , 'cms_employee_educations');
define('BANK_INFORMATION'                   , 'cms_employee_bank_informations');
define('STUDENTS'			                      , 'cms_students');
define('SOCIAL_PROFILE'                     , 'cms_social_profile');
define('TRANSACTION'                        , 'cms_transactions');
define('WISHLIST'                           , 'cms_wishlist');
define('ENROLLED_COURSES'                   , 'cms_enrolled_courses');
define('DEGREE'                             , 'cms_degree');
define('DEGREE_DETAIL'                      , 'cms_degree_detail');
define('MASTER_TRACK'                       , 'cms_master_track');
define('ADMISSION_OFFERING'                 , 'cms_admission_offering');
define('MASTER_TRACK_CATEGORIES'            , 'cms_master_track_categories');
define('MASTER_TRACK_DETAIL'                , 'cms_master_track_detail');
define('ALLOCATE_TEACHERS'                  , 'cms_courses_allocateteachers');
define('COURSES_DISCUSSIONSTUDENTS'         , 'cms_courses_discussionstudents');
define('PROGRAMS_STUDY_SCHEME'              , 'cms_programs_study_scheme');
define('LECTURE_TRACKING'		                , 'cms_lecture_tracking');
define('QUIZ'                               , 'cms_quiz');
define('QUIZ_QUESTIONS'                     , 'cms_quiz_questions');
define('QUIZ_STUDENTS'                      , 'cms_quiz_students');
define('QUIZ_STUDENT_DETAILS'               , 'cms_quiz_student_details');
define('CHALLANS'                           , 'cms_challans');
define('CHALLAN_DETAIL'                     , 'cms_challan_detail');
define('STUDENT_EDUCATIONS'                 , 'cms_student_educations');
define('GENERATED_CERTIFICATES'             , 'cms_generated_certificates');
define('COURSES_WEEK_TITLE'                 , 'cms_courses_week_title');
define('NOTIFICATIONS'                      , 'cms_notifications');
define('COUPONS'                            , 'cms_coupons');
define('FEEDBACK_QUESTIONS'                 , 'cms_feedback_questions');
define('STUDENT_FEEDBACK'                   , 'cms_student_feedback');
define('STUDENT_FEEDBACK_DETAIL'            , 'cms_student_feedback_detail');
define('REVIEWS'                            , 'cms_reviews');
define('BLOGS'                              , 'cms_blogs');
define('APITRANSACTIONS'                    , 'cms_paymentapis_transaction');
define('DISCOUNT'                           , 'cms_courses_discounts');
define('DISCOUNT_DETAIL'                    , 'cms_courses_discounts_details');
define('REFERRAL_CONTROL'                   , 'cms_referral_control');
define('REFERRAL_TEACHER_SHARING'           , 'cms_referral_teacher_sharing');
define('VERIFICATION_CODES'                 , 'cms_verification_codes');
define('QUESTION_ANSWERS'                   , 'cms_student_teacher_qna');
define('STUDENT_INTERESTED_COURSES'         , 'cms_students_interested_courses');
define('REMINDER_MAILS_LOGS'                , 'cms_reminder_mails_logs');
define('TEACHER_INTEREST_QUESTIONS'         , 'cms_teacher_interest_questions');
define('TEACHER_INTEREST'                   , 'cms_teacher_interest');
define('TEACHER_INTEREST_DETAIL'            , 'cms_teacher_interest_detail');
define('SKILL_AMBASSADOR'                   , 'cms_skill_ambassador');

// Variables
$ip	  		                = (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != '') ? $_SERVER['REMOTE_ADDR'] : '';
$control 	                = (isset($_REQUEST['control']) && $_REQUEST['control'] != '') ? $_REQUEST['control'] : '';
$zone 	 	                = (isset($_REQUEST['zone']) && $_REQUEST['zone'] != '') ? $_REQUEST['zone'] : '';
$view 		                = (isset($_REQUEST['view']) && $_REQUEST['view'] != '') ? $_REQUEST['view'] : '';
$slug 		                = (isset($_REQUEST['slug']) && $_REQUEST['slug'] != '') ? $_REQUEST['slug'] : '';
$flag 		                = (isset($_REQUEST['flag']) && $_REQUEST['flag'] != '') ? $_REQUEST['flag'] : '';
$page    	                = (isset($_REQUEST['page']) && $_REQUEST['page'] != '') ? $_REQUEST['page'] : '1';
$pg    		                = (isset($_REQUEST['pg']) && $_REQUEST['pg'] != '') ? $_REQUEST['pg'] : '1';
$wrds    	                = (isset($_REQUEST['wrds']) && $_REQUEST['wrds'] != '') ? $_REQUEST['wrds'] : '';
$do                       = '';
$redirection              = '';

define('LMS_IP'             , $ip);
define('ZONE'			          , $zone);
define('CONTROLER'			    , strtolower($control));
define('LMS_DO'			        , $do);
define('LMS_EPOCH'		      , date("U"));
define('LMS_VIEW'		        , $view);
define('LMS_FLAG'		        , $flag);
define("SITE_URL"			      , "https://localhost/GPT/dodl.mul.edu.pk/");
define("SITE_URL_PORTAL"	  , "https://localhost/GPT/mcdl.mul.edu.pk/");
define('TITLE_HEADER'		    , 'DODL');
define("SITE_NAME"			    , "Directorate of Open and Distance Learning");
define("SITE_PHONE"         , "+92 315 400 3459");
define("SITE_ADDRESS"		    , "Minhaj University Lahore Main Campus Hamdard Chowk,Township, Lahore, Pakistan.");
define("COPY_RIGHTS"		    , "Green Professional Technologies.");
define("COPY_RIGHTS_ORG"	  , "&copy; ".date("Y")." - All Rights Reserved.");
define("COPY_RIGHTS_URL"	  , "http://www.gptech.pk/");

// DATA API
define('YTDATAAPI'          , 'AIzaSyB8oaZ_V4iVfPjJEkaACSJUmo4kuC1gQ78');

// PAYFAST CREDENTIALS
define('PAYFAST_MERCHANTID'	, "16644");
define('PAYFAST_SECUREDKEY'	, "2r-AySWf7Gm7qwfc-5l10i");
define('PAYFAST_TOKENURL'	  , "https://ipg1.apps.net.pk/Ecommerce/api/Transaction/GetAccessToken");
define('PAYFAST_TRNSURL'	  , "https://ipg1.apps.net.pk/Ecommerce/api/Transaction/PostTransaction");

// SMTP MAIL VARS
define('SMTP_EMAIL'         , 'info.dodl@mul.edu.pk');
define('SMTP_TOKEN'         , 'b08a9b259dc86a5fa2ab8f409614b38dbef1768edbab1d2a7281c2c963d5b5ed');
?>