<?php
include "../dbsetting/lms_vars_config.php";
include "../dbsetting/classdbconection.php";
$dblms = new dblms();
include "../functions/functions.php";

$search_word = trim($_POST['search_word']);

if(!empty($search_word)){
    $condition = array ( 
                            'select' 		    =>	'ap.program, p.prg_name, p.prg_photo, p.prg_href, mt.mas_name, mt.mas_photo, mt.mas_href, c.curs_name, c.curs_photo, c.curs_href'
                            ,'join'             =>  'LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON (ap.id = ao.admoff_degree AND ap.is_deleted = 0)
                                                    LEFT JOIN '.MASTER_TRACK.' mt ON (mt.mas_id = ao.admoff_degree AND mt.is_deleted = 0)
                                                    LEFT JOIN '.COURSES.' c ON (c.curs_id = ao.admoff_degree AND c.is_deleted = 0)
                                                    LEFT JOIN '.PROGRAMS.' p ON (p.prg_id  = ap.id_prg AND p.is_deleted = 0)'
                            ,'where' 		    =>	array( 
                                                        'ao.is_deleted' 	    => '0'
                                                        )
                            ,'search_by'        =>  ' AND (ap.program LIKE "%'.$_POST['search_word'].'%" 
                                                                    OR 
                                                        mt.mas_name LIKE "%'.$_POST['search_word'].'%" 
                                                                    OR 
                                                        c.curs_name LIKE "%'.$_POST['search_word'].'%")'
                            ,'order_by'     =>  'ao.admoff_id'
                            ,'return_type'	=>	'all'
                        );
    $ADMISSION_OFFERING = $dblms->getRows(ADMISSION_OFFERING.' ao', $condition);
    if ($ADMISSION_OFFERING && $_POST['search_word'] != "") {
        echo'
        <table class="">
            <tbody>';
                foreach($ADMISSION_OFFERING AS $key => $val):
                    $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                    if($val['prg_name']){
                        $type       = '1';
                        $href       = 'degree-detail/'.$val['prg_href'];
                        $name       = $val['prg_name'];
                        $file_url   = SITE_URL_PORTAL.'uploads/images/programs/'.$val['prg_photo'];
                    }elseif($val['mas_name']){
                        $type       = '2';
                        $href       = 'master-track-detail/'.$val['mas_href'];
                        $name       = $val['mas_name'];
                        $file_url   = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$val['mas_photo'];
                    }elseif($val['curs_name']){
                        $type       = '3';
                        $href       = 'courses/'.$val['curs_href'];
                        $name       = $val['curs_name'];
                        $file_url   = SITE_URL_PORTAL.'uploads/images/courses/'.$val['curs_photo'];
                    }
                    if (check_file_exists($file_url)) {
                        $photo = $file_url;
                    }
                    echo'
                    <tr>
                        <td>
                            <div class="sell-table-group d-flex align-items-center mb-1">
                                <div class="sell-group-img">
                                    <a href="'.SITE_URL.$href.'">
                                        <img class="img-fluid p-1 border" alt="" src="'.$photo.'">
                                    </a>
                                </div>
                                <div class="sell-tabel-info">
                                    <p><a href="'.SITE_URL.$href.'">'.$name.'</a></p>
                                    <div class="course-info d-flex align-items-center border-bottom-0 pb-0">
                                        <div class="rating-img d-flex align-items-center">
                                            <img src="'.SITE_URL.'assets/img/icon/icon-23.svg" alt="">
                                            <p>'.get_enroll_type($type).'</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>';
                    continue;
                endforeach;
                
                echo'
            </tbody>
        </table>';
    } else {
        echo "";
    }
}
?>

