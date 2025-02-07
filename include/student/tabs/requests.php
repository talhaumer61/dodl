<?php
$conditions = array ( 
                         'select'       =>	'c.curs_name, c.curs_photo, c.curs_id, c.curs_href, mt.mas_name, mt.mas_photo, p.prg_photo, ap.program, ec.secs_id, ec.id_type, ec.secs_status, ec.id_mas, ec.id_ad_prg, ec.date_added'
                        ,'join'         =>	'LEFT JOIN '.COURSES.' c ON c.curs_id = ec.id_curs AND c.curs_status = 1 AND c.is_deleted = 0
                                             LEFT JOIN '.MASTER_TRACK.' mt ON mt.mas_id = ec.id_mas AND mt.mas_status = 1 AND mt.is_deleted = 0
                                             LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ec.id_ad_prg
                                             LEFT JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg'
                        ,'where' 		=>	array( 
                                                     'ec.is_deleted'    => '0'
                                                    ,'ec.id_std' 	    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                )
                        ,'order_by'     =>	'ec.secs_id DESC'
                        ,'return_type'	=>	'all'
                    ); 
$ENROLLED_COURSES = $dblms->getRows(ENROLLED_COURSES.' ec', $conditions, $sql);
echo'
<div class="settings-widget">
    <div class="settings-inner-blk p-0">
        <div class="comman-space pb-0">
            <div class="filter-grp ticket-grp d-flex align-items-center justify-content-between" >
                <h3>Enrollments</h3>
            </div>
            <div class="settings-tickets-blk table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">Sr.</th>
                            <th>Name</th>
                            <th width="100" class="text-center">Request Date</th>
                            <th width="100" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>';
                        if($ENROLLED_COURSES){
                            $srno = 0;
                            foreach ($ENROLLED_COURSES as $row) {
                                $srno++;
                                $type = '';
                                $name = '';
                                $file_url = '';
                                $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                                if($row['id_ad_prg']){
                                    $type       = $row['id_type'];
                                    $name       = $row['program'];
                                    $file_url   = SITE_URL_PORTAL.'uploads/images/programs/'.$row['prg_photo'];
                                }elseif($row['mas_name']){
                                    $type       = $row['id_type'];
                                    $name       = $row['mas_name'];
                                    $file_url   = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$row['mas_photo'];
                                }elseif($row['curs_name']){
                                    $type       = $row['id_type'];
                                    $name       = $row['curs_name'];
                                    $file_url   = SITE_URL_PORTAL.'uploads/images/courses/'.$row['curs_photo'];
                                }
                                if (check_file_exists($file_url)) {
                                    $photo = $file_url;
                                }
                                if($name != ''){
                                    echo'
                                    <tr>
                                        <td class="text-center">'.$srno.'</td>
                                        <td>
                                            <div class="sell-table-group d-flex align-items-center">
                                                <div class="sell-group-img">
                                                    <img class="img-fluid p-1 border" alt="" src="'.$photo.'">
                                                </div>
                                                <div class="sell-tabel-info">
                                                    <p>'.$name.'</p>
                                                    <div class="course-info d-flex align-items-center border-bottom-0 pb-0">
                                                        <div class="rating-img d-flex align-items-center">
                                                            <img src="'.SITE_URL.'assets/img/icon/icon-23.svg" alt="">
                                                            <p>'.get_enroll_type($type).'</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">'.date('d M, Y', strtotime($row['date_added'])).'</td>
                                        <td class="text-center">'.get_leave($row['secs_status']).'</td>
                                    </tr>';
                                }
                            } 
                        }
                        else{
                            echo '
                            <tr>
                                <td colspan="3" align="center">No Record Found</td>
                            </tr>';
                        }
                        echo '
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>';
?>