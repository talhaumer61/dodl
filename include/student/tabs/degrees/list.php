<?php
$conditions = array ( 
                         'select'       =>	'ec.secs_id, p.prg_id, p.prg_href, p.prg_name, p.prg_photo, p.prg_duration, ap.id, ec.id_curs
                                            ,COUNT(DISTINCT cl.lesson_id) AS lesson_count
                                            ,COUNT(DISTINCT ca.id) AS assignment_count
                                            ,COUNT(DISTINCT cq.quiz_id) AS quiz_count
                                            ,COUNT(DISTINCT lt.track_id) AS track_count'
                        ,'join'         =>	'INNER JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ec.id_ad_prg
                                             INNER JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg
                                             LEFT JOIN '.COURSES_LESSONS.' cl ON FIND_IN_SET(cl.id_curs, ec.id_curs) AND cl.lesson_status = 1 AND cl.is_deleted = 0
                                             LEFT JOIN '.COURSES_ASSIGNMENTS.' ca ON FIND_IN_SET(ca.id_curs, ec.id_curs) AND ca.status = 1 AND ca.is_deleted = 0
                                             LEFT JOIN '.QUIZ.' cq ON FIND_IN_SET(cq.id_curs, ec.id_curs) AND cq.quiz_status = 1 AND cq.is_deleted = 0 AND cq.is_publish = 1
                                             LEFT JOIN '.LECTURE_TRACKING.' lt ON FIND_IN_SET(lt.id_curs, ec.id_curs) AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.is_completed = 2 AND lt.is_deleted = 0 AND lt.id_mas = ec.id_mas AND lt.id_ad_prg = ec.id_ad_prg'
                        ,'where' 		=>	array( 
                                                     'ec.is_deleted'    => '0'
                                                    ,'ec.secs_status'   => '1' 
                                                    ,'ec.id_mas'        => '0'
                                                    ,'ec.id_std' 	    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                )
                        ,'not_equal'    =>  array(
                                                    'ec.id_ad_prg'      =>  '0'
                                                )
                        ,'group_by'     =>  ' ec.secs_id, p.prg_id'
                        ,'return_type'	=>	'all'
                    );
$ENROLLED_COURSES = $dblms->getRows(ENROLLED_COURSES.' ec', $conditions);
echo'
<div class="settings-widget">
    <div class="settings-inner-blk p-0">
        <div class="comman-space pb-0">
            <div class="row">';
                if($ENROLLED_COURSES){
                    foreach ($ENROLLED_COURSES as $row) {
                        // CHECK FILE EXIST
                        $photo      = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                        $file_url   = SITE_URL_PORTAL.'uploads/images/programs/'.$row['prg_photo'];
                        if (check_file_exists($file_url)) {
                            $photo = $file_url;
                        }

                        // PERCENTAGE
                        $Total      = $row['lesson_count']+$row['assignment_count']+$row['quiz_count'];
                        $Obtain     = $row['track_count'];
                        $percent    = (($Obtain/$Total)*100);
                        $percent    = ($percent >= '100' ? '100' : $percent);

                        echo'
                        <div class="col-xl-3 col-lg-4 col-md-6 d-flex">
                            <div class="course-box shadow course-design d-flex">
                                <div class="product">
                                    <div class="product-img">
                                        <a href="'.SITE_URL.$control.'/'.$zone.'/'.$row['prg_href'].'">
                                            <img class="img-fluid" alt src="'.$photo.'"/>
                                        </a>
                                    </div>
                                    <div class="product-content">
                                        <h3 class="title">
                                            <a href="'.SITE_URL.$control.'/'.$zone.'/'.$row['prg_href'].'">'.$row['prg_name'].'</a>
                                        </h3>
                                        <div class="course-info d-flex align-items-center border-bottom-0 pb-0">
                                            <div class="rating-img d-flex align-items-center">
                                                <img src="'.SITE_URL.'assets/img/icon/icon-01.svg" alt />
                                                <p>'.sizeof(explode(',', $row['id_curs'])).' Courses</p>
                                            </div>
                                            <div class="course-view d-flex align-items-center" >
                                                <img src="'.SITE_URL.'assets/img/icon/timer-start.svg" alt />
                                                <p>'.$row['prg_duration'].' Years</p>
                                            </div>
                                        </div>
                                        <div class="rating-student">
                                            <div class="rating">
                                                <i class="fas fa-star filled"></i>
                                                <i class="fas fa-star filled"></i>
                                                <i class="fas fa-star filled"></i>
                                                <i class="fas fa-star filled"></i>
                                                <i class="fas fa-star"></i>
                                                <span class="d-inline-block average-rating"><span>4.0</span></span>
                                            </div>
                                            <div class="edit-rate">
                                                <a href="javascript:;">Rating</a>
                                            </div>
                                        </div>
                                        <div class="progress-stip">
                                            <div class="progress-bar bg-success progress-bar-striped active-stip" style="width: '.intval($percent).'% !important"></div>
                                        </div>
                                        <div class="student-percent">
                                            <p>'.intval($percent).'% Completed</p>
                                        </div>';
                                        if($percent == '100'){
                                            echo'
                                            <div class="student-percent text-center">
                                                <a href="'.SITE_URL.'certificate-print/'.$row['secs_id'].'" class="badge bg-info"><i class="fa fa-graduation-cap me-1"></i>Get Certificate</a>
                                            </div>';
                                        }
                                        echo'
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                }else {
                    echo '<h4 class="pb-3" align="center">No Degree Found</h4>';
                }
                echo'
            </div>
        </div>
    </div>
</div>';
?>