<?php
include "../dbsetting/lms_vars_config.php";
include "../dbsetting/classdbconection.php";
include "../functions/functions.php";
include "../functions/login_func.php";
$dblms = new dblms();
    
if($_POST['method'] == '__GET_WISHLIST') { 
    // WISHLIST
    $condition = array (
                             'select'       =>	'w.wl_id, w.id_type, w.id_curs, w.id_mas, w.id_ad_prg, c.curs_name, c.curs_photo, c.curs_href, mt.mas_name, mt.mas_photo, mt.mas_href, ap.program, p.prg_name, p.prg_photo, p.prg_href'
                            ,'join'         =>	'LEFT JOIN '.COURSES.' c ON c.curs_id = id_curs
                                                 LEFT JOIN '.MASTER_TRACK.' mt ON mt.mas_id = w.id_mas
                                                 LEFT JOIN '.PROGRAMS.' p ON p.prg_id = w.id_ad_prg
                                                 LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = w.id_ad_prg' 
                            ,'where'        =>	array( 
                                                        'w.id_std'    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                    ) 
                            ,'order_by'     =>  'w.wl_id DESC'
                            ,'return_type'	=>	'all'
                        ); 
    $WISHLIST = $dblms->getRows(WISHLIST.' w', $condition);

    if($WISHLIST){
        foreach ($WISHLIST as $row) {
            $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
            if($row['id_type'] == 1){
                $href       = 'degree-detail/'.$row['prg_href'];
                $idWish     = $row['id_ad_prg'];
                $name       = $row['prg_name'];
                $file_url   = SITE_URL_PORTAL.'uploads/images/programs/'.$row['prg_photo'];
            }elseif($row['id_type'] == 2){
                $href       = 'master-track-detail/'.$row['mas_href'];
                $idWish     = $row['id_mas'];
                $name       = $row['mas_name'];
                $file_url   = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$row['mas_photo'];
            }elseif($row['id_type'] == 3 || $row['id_type'] == 4){
                $href       = ($row['id_type'] == 3 ? 'courses' : 'e-trainings').'/'.$row['curs_href'];
                $idWish     = $row['id_curs'];
                $name       = $row['curs_name'];
                $file_url   = SITE_URL_PORTAL.'uploads/images/courses/'.$row['curs_photo'];
            }
            if (check_file_exists($file_url)) {
                $photo = $file_url;
            }
            echo'
            <li>
                <div class="media">
                    <div class="d-flex media-wide">
                        <div class="avatar">
                            <img alt src="'.$photo.'" alt="'.$name.'"/>
                        </div>
                        <div class="media-body">
                            <h6>'.$name.'</h6>
                            <div class="course-info d-flex align-items-center border-bottom-0 pb-0">
                                <div class="rating-img d-flex align-items-center">
                                    <img src="'.SITE_URL.'assets/img/icon/icon-23.svg" alt="">
                                    <p>'.get_enroll_type($row['id_type']).'</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="remove-btn">
                        <a href="javascript:;" onclick="remove_from_wishlist('.$row['wl_id'].')" class="btn">Remove</a>
                    </div>
                </div>
            </li>';
        }
        echo'
        <form action="'.SITE_URL.'invoice" method="POST" class="text-center">
            <input type="hidden" name="url" value="'.$_POST['url'].'">
            <button type="submit" name="enroll" class="btn btn-sm btn-info text-white mt-2">Checkout</button>
        </form>';
    } else {
        echo'<li class="text-center text-danger">No Record Found...</li>';
    }
}
?>