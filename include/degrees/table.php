<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();
session_start();

$search_field = array("p.prg_name");
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
    $search_by .= " AND ( FIND_IN_SET(" .implode(" , $search_item ) OR FIND_IN_SET ( ",$value)." , $search_item ) )";
  }
}

$condition = array ( 
                     'select' 		  =>	'ap.id, ap.program, ap.shortdetail, p.prg_photo, p.prg_href, pc.cat_name, a.admoff_enddate, a.admoff_startdate'
                    ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = ap.id AND a.admoff_type = 1
                                         INNER JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg
                                         INNER JOIN '.PROGRAMS_CATEGORIES.' pc ON pc.cat_id = p.id_cat'
                    ,'where' 		    =>	array( 
                                                 'ap.status'      =>  1 
                                                ,'ap.is_deleted'  =>  0 
                                            ) 
                    ,'search_by'    =>  ''.$search_by.''
                    ,'order_by'     =>  'ap.id DESC'
                    ,'return_type'	=>	'all'
                  ); 
$table = ADMISSION_PROGRAMS." ap";

include '../pagination.php';
echo'
<div class="row">';
  foreach ($values as $key => $degree) {
    // CHECK FILE EXIST
    $file_url = SITE_URL_PORTAL.'uploads/images/programs/'.$degree['prg_photo'];
    if (check_file_exists($file_url)) {
        $prg_photo = $file_url;
    } else {
        $prg_photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
    }

    // WISHLIST
    if (isset($_SESSION['userlogininfo'])) {
      $condition = array ( 
                             'select' 		  =>	'wl_id, id_ad_prg'
                            ,'where' 		    =>	array( 
                                                        'id_ad_prg'   =>  $degree['id'] 
                                                    )
                            ,'search_by'    => ' AND id_mas IS NULL'
                            ,'return_type'	=>	'single'
                          ); 
      $wishlist = $dblms->getRows(WISHLIST, $condition);
    }
    echo'
    <div class="col-md-4 col-sm-6">
      <div class="blog grid-blog shadow">
        <div class="blog-image">
          <a href="'.SITE_URL.'degree-detail/'.$degree['prg_href'].'"><img class="img-fluid" src="'.$prg_photo.'" alt="Program Image"/></a>
        </div>
        <div class="blog-grid-box">
          <div class="blog-info clearfix">
            <div class="post-left">
              <ul>
                <li>
                  <img class="img-fluid" src="'.SITE_URL.'assets/img/icon/icon-23.svg" alt/>'.$degree['cat_name'].'
                </li>
              </ul>
            </div>
          </div>
          <h3 class="blog-title">
            <a href="'.SITE_URL.'degree-detail/'.$degree['prg_href'].'">'.$degree['program'].'</a>
          </h3>
          <div class="blog-info clearfix">
            <div class="post-left">
              <ul>
                <li>
                  <img class="img-fluid" src="'.SITE_URL.'assets/img/icon/icon-22.svg" alt/>Application due from '.date('d M, Y', strtotime($degree['admoff_startdate'])).' to '.date('d M, Y', strtotime($degree['admoff_enddate'])).'
                </li>
              </ul>
            </div>
          </div>
          <div class="blog-content blog-read">
            <p class="line-clamp-2">'.$degree['shortdetail'].'</p>
            <div class="d-flex">
              <a href="'.SITE_URL.'degree-detail/'.$degree['prg_href'].'" class="read-more btn btn-primary">Read More</a>
              <div class="course-share d-flex align-items-center justify-content-center" data-id="'.$degree['id'].'">';
                if (isset($wishlist) && !empty($wishlist)){
                  echo '<a href="javascript:;" onclick="remove_from_wishlist('.$wishlist['wl_id'].','.$degree['id'].',1)"><i class="fa-solid fa-heart"></i></a>';
                } else {
                  echo '<a href="javascript:;" onclick="add_to_wishlist('.$degree['id'].',1)"><i class="fa-regular fa-heart"></i></a>';
                }
                echo'
              </div>
            </div>
          </div>
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