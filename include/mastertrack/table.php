<?php
include "../dbsetting/classdbconection.php";
include "../dbsetting/lms_vars_config.php";
include "../functions/functions.php";
$dblms = new dblms();
session_start();


$search_field = array("mt.mas_name");
$searches     = (isset($_POST['word'])) ? json_decode($_POST['word'], true) : "";
$search_by    = ""; 

if ($search_field) {
  foreach ($searches as $key => $search) {
    $search_by .= " AND ( " .implode(" LIKE '%$search%' OR ",$search_field)." LIKE '%$search%' )";
  }
}

$condition = array ( 
                     'select'       =>	'mt.mas_id, mt.mas_status, mt.mas_duration, mt.mas_icon, mt.mas_photo, mt.mas_name, mt.mas_href, COUNT(DISTINCT mtd.id_curs) TotalCourses'
                    ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = mt.mas_id AND a.admoff_type=2
                                         LEFT JOIN '.MASTER_TRACK_DETAIL.' mtd ON mtd.id_mas = mt.mas_id'
                    ,'where'        =>	array( 
                                                 'mt.mas_status'    =>  1
                                                ,'mt.is_deleted'    =>  0 
                                            ) 
                    ,'search_by'    =>  ''.$search_by.''
                    ,'group_by'     =>  'mt.mas_id'
                    ,'order_by'     =>  'mt.mas_id DESC'
                    ,'return_type'  =>	'all'
                  );
$table = MASTER_TRACK.' mt';

include '../pagination.php';
echo'
<div class="settings-widget mb-0">
    <div class="settings-inner-blk">
        <div class="comman-space p-0">
            <div  class="settings-tickets-blk course-instruct-blk table-responsive">
                <table class="table mb-0">';
                    if($values){
                        echo'
                        <thead>
                            <tr>
                                <th colspan="4">
                                    <h6 class="mb-0">Find the right online programs for you</h6>
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            foreach ($values as $key => $row){
                                // CHECK FILE EXIST
                                $file_url = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$row['mas_photo'];
                                if (check_file_exists($file_url)) {
                                    $mas_photo = $file_url;
                                } else {
                                    $mas_photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                                }

                                // WISHLIST
                                if (isset($_SESSION['userlogininfo'])) {
                                    $condition = array ( 
                                                        'select' 		=>	'wl_id, id_mas'
                                                        ,'where' 		=>	array( 
                                                                                    'id_mas' 	    => $row['mas_id'] 
                                                                                )
                                                        ,'search_by'    => ' AND id_ad_prg IS NULL'
                                                        ,'return_type'	=>	'single'
                                                        ); 
                                    $wishlist = $dblms->getRows(WISHLIST, $condition);
                                }  
                                echo'
                                <tr>
                                    <td>
                                        <div class="sell-table-group d-flex align-items-center">
                                            <div class="sell-group-img">
                                                <a href="'.SITE_URL.'master-track-detail/'.$row['mas_href'].'">
                                                    <img class="img-fluid" alt="" src="'.$mas_photo.'"/>
                                                </a>
                                            </div>
                                            <div class="sell-tabel-info">
                                                <p><a href="'.SITE_URL.'master-track-detail/'.$row['mas_href'].'">'.$row['mas_name'].'</a></p>
                                                <small>Offered by '.SITE_NAME.'</small>
                                                <div class="course-info d-flex align-items-center border-bottom-0 pb-0">
                                                    <div class="rating-img d-flex align-items-center">
                                                        <img src="'.SITE_URL.'assets/img/icon/icon-01.svg" alt />
                                                        <p>'.$row['TotalCourses'].' Courses</p>
                                                    </div>
                                                    <div class="course-view d-flex align-items-center" >
                                                        <img src="'.SITE_URL.'assets/img/icon/timer-start.svg" alt />
                                                        <p>'.$row['mas_duration'].' Months</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="70" class="text-center">
                                        <div class="all-btn all-category d-flex align-items-center">
                                            <form action="'.SITE_URL.'invoice" method="POST">
                                                <input type="hidden" name="url" value="master-track-certificates">
                                                <input type="hidden" name="id" value="'.$row['mas_id'].'">
                                                <input type="hidden" name="type" value="2">
                                                <button name="enroll" type="submit" class="btn btn-primary">BUY NOW</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td width="70" class="text-center">
                                        <div class="course-share d-flex align-items-center justify-content-center" data-id="'.$row['mas_id'].'">';
                                            if (isset($wishlist) && !empty($wishlist)){
                                                echo '<a href="javascript:;" onclick="remove_from_wishlist('.$wishlist['wl_id'].','.$row['mas_id'].',2)"><i class="fa-solid fa-heart"></i></a>';
                                            } else {
                                                echo '<a href="javascript:;" onclick="add_to_wishlist('.$row['mas_id'].', 2)"><i class="fa-regular fa-heart"></i></a>';
                                            }
                                            echo'
                                        </div>
                                    </td>
                                </tr>';
                            }
                            echo'
                        </tbody>';
                    }
                    echo'
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        '.main_pg($count,$limit,$page,$next,$prev,$srno).'
    </div>
</div>';
?>