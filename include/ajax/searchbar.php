<?php
include "../dbsetting/lms_vars_config.php";
include "../dbsetting/classdbconection.php";
$dblms = new dblms();
include "../functions/functions.php";

$search_word = trim($_POST['search_word']);

if($search_word != ''){
    $condition = array ( 
                             'select' 		    =>	'ao.admoff_type, ap.program, p.prg_name, p.prg_photo, p.prg_href, mt.mas_name, mt.mas_photo, mt.mas_href, c.curs_id, c.curs_name, c.curs_photo, c.curs_href, c.curs_type_status'
                            ,'join'             =>  'LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ao.admoff_degree AND ap.is_deleted = 0
                                                     LEFT JOIN '.MASTER_TRACK.' mt ON mt.mas_id = ao.admoff_degree AND mt.is_deleted = 0
                                                     LEFT JOIN '.COURSES.' c ON c.curs_id = ao.admoff_degree AND c.is_deleted = 0
                                                     LEFT JOIN '.PROGRAMS.' p ON p.prg_id  = ap.id_prg AND p.is_deleted = 0'
                            ,'where' 		    =>	[ 
                                                        'ao.is_deleted' 	    => '0'
                                                    ]
                            ,'search_by'        =>  'AND (ap.program LIKE "%'.$search_word.'%" 
                                                                    OR 
                                                        mt.mas_name LIKE "%'.$search_word.'%" 
                                                                    OR 
                                                        c.curs_name LIKE "%'.$search_word.'%")'
                            ,'order_by'     =>  'ao.admoff_id'
                            ,'return_type'	=>	'all'
                        );
    $ADMISSION_OFFERING = $dblms->getRows(ADMISSION_OFFERING.' ao', $condition);
    if ($ADMISSION_OFFERING && $search_word != "") {
        echo'
        <table>
            <tbody>';
                foreach($ADMISSION_OFFERING AS $key => $val):
                    $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                    if($val['admoff_type'] == 1){
                        $href       = 'degree-detail/'.$val['prg_href'];
                        $name       = $val['prg_name'];
                        $photo      = (!empty($val['prg_photo']) ? SITE_URL_PORTAL.'uploads/images/programs/'.$val['prg_photo'] : '');
                    }elseif($val['admoff_type'] == 2){
                        $href       = 'master-track-detail/'.$val['mas_href'];
                        $name       = $val['mas_name'];
                        $photo      = (!empty($val['mas_photo']) ? SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$val['mas_photo'] : '');
                    }elseif($val['admoff_type'] == 3 || $val['admoff_type'] == 4){
                        $href       = ($val['admoff_type'] == 3 ? 'courses/' : ($val['admoff_type'] == 4 ? 'trainings/' : '')).$val['curs_href'];
                        $name       = $val['curs_name'];
                        $curs_type  = $val['curs_type_status'];
                        $photo      = (!empty($val['curs_photo']) ? SITE_URL_PORTAL.'uploads/images/courses/'.$val['curs_photo'] : '');
                    }
                    echo'
                    <tr>
                        <td>
                            <a href="'.($curs_type == 1 ? 'javascript:void(0)' : SITE_URL.$href).'">
                                <div class="sell-table-group d-flex align-items-center mb-1">
                                    <div class="sell-group-img">
                                        <img class="p-1 border" width="100" height="70" style="object-fit: cover;" alt="'.$name.'" src="'.$photo.'">
                                    </div>
                                    <div class="sell-tabel-info">
                                        <p>'.$name.'</p>
                                        <div class="course-info d-flex align-items-center border-bottom-0 pb-0">
                                            <div class="rating-img d-flex align-items-center">
                                                '.get_enroll_type($val['admoff_type']).' '.($curs_type == 1 ? '<i class="text-info ms-1 attention-grabber" style="font-size: 12px;">Comig Soon - Details not available.</i>' : '').'
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </td>
                    </tr>';
                    continue;
                endforeach;                
                echo'
            </tbody>
        </table>';
    } else {
        echo'<div class="alert mb-0 text-center text-danger"><strong>No data found...!</strong></div>';
    }
}
?>

