<?php
include "../../../dbsetting/classdbconection.php";
include "../../../dbsetting/lms_vars_config.php";
include "../../../functions/functions.php";
$dblms = new dblms();
session_start();

$today = date('Y-m-d');
$search_field = array("n.not_title");
$searches     = (isset($_POST['word'])) ? json_decode($_POST['word'], true) : "";
$checks       = (isset($_POST['check'])) ? json_decode($_POST['check']) : "";
$search_by    = ' AND n.start_date <= "'.$today.'" AND n.end_date >= "'.$today.'"';

if ($search_field) {
  foreach ($searches as $key => $search) {
    $search_by .= " AND ( " .implode(" LIKE '%$search%' OR ",$search_field)." LIKE '%$search%' )";
  }
}
if ($checks) {
  foreach ($checks as $search_item => $value) {
    $search_by .= " AND ( FIND_IN_SET(" .implode(" , $search_item ) OR FIND_IN_SET ( ",$value)." , $search_item ) )";
  }
}

// NOTIFICATIONS
$condition = array ( 
                       'select'       =>	'n.not_id, n.not_title, n.not_description, n.start_date, n.end_date, n.dated'
                      ,'where'        =>	array( 
                                                   'n.not_status'   => 1
                                                  ,'n.id_type'      => 1
                                                  ,'n.is_deleted'   => 0
                                                )
                      ,'search_by'    =>  ''.$search_by.''
                      ,'order_by'     =>	' n.dated DESC'
                      ,'return_type'	=>	'all'
                    );
$table = NOTIFICATIONS." n";

include '../../../pagination.php';

echo '
<div class="row">';
if($values){
  foreach ($values as $keyNot => $valNot) {  
    $NotTitleFirst = substr($valNot['not_title'], 0, 1);                      
    echo'
    <div class="bdr-bottom-line mb-3">
      <div class="instruct-review-blk ">
        <div class="review-item">
          <div class="instructor-wrap border-0 m-0">
            <div class="about-instructor">
              <div class="abt-instructor-img">
                <div class="circle bg-warning text-white me-1" style="height: 40px !important;width: 40px !important;">'.$NotTitleFirst.'</div>
              </div>
              <div class="instructor-detail">
                <p>'.$valNot['not_title'].'</p>
              </div>
            </div>
            <div class="rating">
              '.timeAgo($valNot['dated']).'
            </div>
          </div>
          <div class="rev-info">'.html_entity_decode(html_entity_decode($valNot['not_description'])).'</div>
        </div>
      </div>
    </div>';
  }
}
echo'
</div>
<div class="row">
  <div class="col-md-12">
    '.main_pg($count,$limit,$page,$next,$prev,$srno).'
  </div>
</div>';
?>