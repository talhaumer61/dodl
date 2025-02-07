<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();
session_start();

$search_field = array("c.curs_name");
$searches     = (isset($_POST['word'])) ? json_decode($_POST['word'], true) : "";
$checks       = (isset($_POST['check'])) ? json_decode($_POST['check']) : "";
$search_by    = "";

if ($search_field) {
  foreach ($searches as $key => $search) {
    $search_by .= " AND ( " .implode(" LIKE '%$search%' OR ",$search_field)." LIKE '%$search%' )";
  }
}
if ($checks) {
  foreach ($checks as $search_item => $value) {
    $search_by .= " AND ( FIND_IN_SET('" .implode("', $search_item ) OR FIND_IN_SET ('",$value)."' , $search_item ) )";
  }
}

$condition = array ( 
                     'select' 		  =>	'c.curs_id, c.curs_rating, c.curs_type_status, c.curs_name, c.curs_wise, c.duration, c.curs_icon, c.curs_photo, c.curs_leave, c.curs_total_marks, c.curs_credit_hours, c.curs_href, c.curs_hours, a.admoff_type, a.id_type, a.admoff_amount, a.admoff_amount_in_usd, COUNT(DISTINCT ec.secs_id) as TotalStd, COUNT(DISTINCT l.lesson_id) TotalLesson'
                    ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = c.curs_id AND a.admoff_type IN (3,4)
                                         LEFT JOIN '.ENROLLED_COURSES.' ec ON FIND_IN_SET(c.curs_id, ec.id_curs)
                                         LEFT JOIN '.COURSES_LESSONS.' l ON l.id_curs = c.curs_id AND l.is_deleted = 0 AND l.lesson_status = 1
                                         LEFT JOIN '.ALLOCATE_TEACHERS.' AS ca ON ca.id_curs=c.curs_id
                                         INNER JOIN '.COURSES_CATEGORIES.' cc ON FIND_IN_SET(cc.cat_id, c.id_cat) AND cc.id_type = '.$_SESSION['id_type'].''
                    ,'where' 		    =>	array( 
                                               'c.curs_status' 	    => '1' 
                                              ,'c.is_deleted' 	    => '0' 
                                            )
                    ,'search_by'    =>  ''.$search_by.''
                    ,'group_by'     =>  'c.curs_id'                    
                    ,'order_by'     =>  ' FIELD(c.curs_type_status, "2", "4", "3", "5", "1"), c.curs_id DESC'
                    ,'return_type'	=>	'all'
                  ); 
$table = COURSES." c";

include '../pagination.php';

echo'
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<div class="row">';
if($values){
  foreach ($values as $key => $course) {  
    $curs_rating = (($course['curs_rating'] == '0' || $course['curs_rating'] == '') ? '4' : $course['curs_rating']);
    // CHECK FILE EXIST
    $file_url = SITE_URL_PORTAL.'uploads/images/courses/'.$course['curs_photo'];
    if (check_file_exists($file_url)) {
      $curs_photo = $file_url;
    } else {
      $curs_photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
    }

    // WISHLIST
    if (isset($_SESSION['userlogininfo'])) {
      $condition = array ( 
                             'select' 		=>	'wl_id, id_curs'
                            ,'where' 		=>	array( 
                                                         'id_std'   => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                        ,'id_curs'  => $course['curs_id']
                                                    ) 
                            , 'search_by'   => ' AND id_ad_prg IS NULL AND id_mas IS NULL'
                            ,'return_type'	=>	'single'
                          ); 
      $wl = $dblms->getRows(WISHLIST, $condition);
    }
    echo'
      <script src="'.SITE_URL.'assets/js/jquery-3.6.0.min.js"></script>
      <div class="col-lg-12 col-md-12 d-flex">
      <div class="course-box course-design list-course d-flex">
        <div class="product">
          <div class="product-img">
            <a '.($course['curs_type_status'] == 1 ? '' : 'href="'.SITE_URL.CONTROLER.'/'.$course['curs_href'].'"').'>
              <img class="img-fluid" alt="" src="'.$curs_photo.'"/>
            </a>';
            if($course['curs_type_status'] == '1'){
              
            } else {              
              $condition = array ( 
                                     'select'       =>	'd.discount_id, dd.discount_type, dd.discount'
                                    ,'join'         =>  'INNER JOIN '.DISCOUNT_DETAIL.' AS dd ON d.discount_id = dd.id_setup AND dd.id_curs = "'.$course['curs_id'].'"'
                                    ,'where' 		    =>	array( 
                                                               'd.discount_status' 	=> '1' 
                                                              ,'d.is_deleted' 	    => '0'
                                                            )
                                    ,'search_by'    =>  ' AND d.discount_from <= CURRENT_DATE AND d.discount_to >= CURRENT_DATE '
                                    ,'return_type'	=>	'single'
                                  );
              $DISCOUNT = $dblms->getRows(DISCOUNT.' AS d ', $condition);
              if (!empty($DISCOUNT['discount_id'])) {
                echo'  
                <div class="trending-price blink-image">
                  <h4 class="m-0">
                    <span>'.$DISCOUNT['discount'].'% OFF</span>
                  </h4>
                </div>';
              }
            }
            echo'
          </div>
          <div class="product-content">
            <div class="head-course-title">
              <h3 class="title">
                <a '.($course['curs_type_status'] == 1 ? '' : 'href="'.SITE_URL.CONTROLER.'/'.$course['curs_href'].'"').'>'.$course['curs_name'].'</a>
              </h3>
              <div class="all-btn all-category d-flex align-items-center">';
                if($course['curs_type_status'] == '1'){
                  echo'
                  <form action="'.SITE_URL.'show-interest" method="POST">
                    <input type="hidden" name="url" value="'.CONTROLER.'">
                    <input type="hidden" name="id" value="'.$course['curs_id'].'">
                    <input type="hidden" name="type" value="'.$course['admoff_type'].'">
                    <button name="show_interest" type="submit" class="btn btn-primary">Show Interest</button>
                  </form>';
                } else if($course['curs_type_status'] == '5'){
                  echo'<button name="enroll" class="btn btn-primary" onclick="show_modal(\''.SITE_URL.'include/modals/courses/confirm_buy.php?url=courses&id='.cleanvars($course['curs_id']).'&type='.$course['admoff_type'].'&curs_type_status='.$course['curs_type_status'].'\');">'.(CONTROLER == 'trainings' ? 'JOIN NOW' : 'BUY NOW').'</button>';
                } else {
                  echo'
                  <form action="'.SITE_URL.'invoice" method="POST">
                    <input type="hidden" name="url" value="'.CONTROLER.'">
                    <input type="hidden" name="id" value="'.$course['curs_id'].'">
                    <input type="hidden" name="type" value="'.$course['admoff_type'].'">
                    <button name="enroll" type="submit" class="btn btn-primary">'.(CONTROLER == 'trainings' ? 'JOIN NOW' : 'BUY NOW').'</button>
                  </form>';
                }
                echo'
              </div>
            </div>
            <div class="course-info border-bottom-0 pb-0 d-flex align-items-center m-0">';
              if($course['admoff_type'] == 4){
                echo'
                <div class="rating-img d-flex align-items-center">
                  <img src="assets/img/icon/chapter.svg" alt />
                  <p>'.$course['curs_hours'].' Hour'.($course['curs_hours'] > 1 ? 's' : '').'</p>
                </div>';
              } else {
                echo'
                <div class="rating-img d-flex align-items-center">
                  <img src="assets/img/icon/chapter.svg" alt />
                  <p>'.$course['duration'].' '.get_CourseWise($course['curs_wise']).($course['duration'] > 1 ? 's' : '').'</p>
                </div>';
              }
              /*
              if($course['curs_type_status'] != '1'){
                echo'
                <div class="course-view d-flex align-items-center">
                  <img src="assets/img/icon/count-four.svg" alt />
                  <p>'.($course['TotalStd']+100).' Students</p>
                </div>';
              }
              */
              echo'
            </div>';
            /*
            if($course['curs_type_status'] != '1'){
              echo'
              <div class="rating m-1">';
                for ($rate=1; $rate <= 5; $rate++) {
                  echo'<i class="fas fa-star '.($rate <= $curs_rating ? 'filled' : '').'"></i>';
                }
                echo'
              </div>';
            }
            */
            echo'
            <p class="m-0">';
            if($course['id_type'] != 2){
              $coursePrice = (__COUNTRY__ == 'pk') ? $course['admoff_amount'] : $course['admoff_amount_in_usd'];
              if($course['curs_type_status'] != '1'){
                if (!empty($DISCOUNT['discount_id'])) {
                  echo'
                  <small><s>'.(__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice).'</s></small>
                  <span class="text-info">/';
                    if ($DISCOUNT['discount_type'] == 1) {
                      echo ($coursePrice - $DISCOUNT['discount']);
                    } else if ($DISCOUNT['discount_type'] == 2) {
                      echo ($coursePrice - ($coursePrice * ($DISCOUNT['discount'] / 100) ));
                    }
                    echo'
                  </span>';
                } else {
                  echo (__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice);
                }
              }
            }
            echo'
            </p>
            '.(CONTROLER == 'trainings' ? get_LeanerType($course['id_type']) : get_curs_status($course['curs_type_status'])).'
            <div class="course-group d-flex mb-0">';
              echo '<div class="course-share d-flex align-items-center justify-content-center" data-id="'.$course['curs_id'].'">';
              if($course['curs_type_status'] == '1'){

              } else {
                if (isset($wl) && !empty($wl)){
                  echo '<a href="javascript:;" onclick="remove_from_wishlist('.$wl['wl_id'].','.$course['curs_id'].','.$course['admoff_type'].')"><i class="fa-solid fa-heart"></i></a>';
                } else {
                  echo '<a href="javascript:;" onclick="add_to_wishlist('.$course['curs_id'].','.$course['admoff_type'].')"><i class="fa-regular fa-heart"></i></a>';
                }
              }
              echo '
              </div>
            </div>
          </div>
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