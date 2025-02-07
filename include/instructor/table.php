<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();
session_start();
$search_field = array("e.emply_name","d.designation_name");
$searches     = (isset($_POST['word'])) ? json_decode($_POST['word'], true) : "";
$search_by    = ""; 

if ($search_field) {
  foreach ($searches as $key => $search) {
    $search_by .= " AND ( " .implode(" LIKE '%$search%' OR ",$search_field)." LIKE '%$search%' )";
  }
}
$condition = array ( 
                       'select'       =>  "e.emply_id, e.emply_photo, e.emply_name, d.designation_name, e.emply_gender"
                      ,'join'         =>  'LEFT JOIN '.DESIGNATIONS.' d ON d.designation_id = e.id_designation'
                      ,'where' 	      =>  array ( 
                                                   'e.is_deleted'     => 0
                                                  ,'e.emply_status'   => 1
                                                )
                      ,'search_by'    =>  ''.$search_by.''
                      ,'order_by'     =>  'e.emply_id DESC'
                      ,'return_type'  =>  'all'
                    ); 
// $EMPLOYEES = $dblms->getRows(EMPLOYEES.' e', $condition);
$table = EMPLOYEES.' e';
$search_field  = array("e.emply_name");
include '../pagination.php';
echo'
<div class="row">';
  foreach ($values as $key => $instructors) {
    // CHECK FILE EXIST
    if($instructors['emply_gender'] == '2'){
      $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
    }else{            
      $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
    }
    if(!empty($instructors['emply_photo'])){
      $file_url = SITE_URL_PORTAL.'uploads/images/employees/'.$instructors['emply_photo'];
      if (check_file_exists($file_url)) {
        $photo = $file_url;
      }
    }
    echo'      
    <div class="col-lg-3 col-md-6 d-flex">
      <div class="instructor-box flex-fill">
        <div class="instructor-img justify-content-center">
          <a href="'.SITE_URL.'instructors/'.$instructors['emply_id'].'">
            <img class="img-fluid" alt src="'.$photo.'" />
          </a>
        </div>
        <div class="instructor-content">
          <h5><a href="'.SITE_URL.'instructors/'.$instructors['emply_id'].'">'.$instructors['emply_name'].'</a></h5>
          <h6>'.$instructors['designation_name'].'</h6>
        </div>
      </div>
    </div>';
  }
  echo'
</div>
<div class="row">
  <div class="col-md-12">
  '.main_pg($count,$limit,$page,$next,$prev,$srno).'
  </div>
</div>';
?>