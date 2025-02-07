<?php
// TRANSACTIONS and TOTAL COUNT
$conditions = array ( 
                         'select' 		=>	'trans_id, trans_no, date, trans_amount, currency_code'
                        ,'where' 		=>	array( 
                                                     'is_deleted'		=> '0'
                                                    ,'trans_status'      => '1' 
                                                    ,'id_std' 	        => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                )
                        ,'order_by'     =>  'trans_id DESC'
                        ,'return_type'	=>	'all'
                    ); 
$transactions = $dblms->getRows(TRANSACTION, $conditions);
$conditions['return_type']  = 'count';
$totaltransctions = $dblms->getRows(TRANSACTION, $conditions);

// TOTAL ENROLL
$conditions = array ( 
                         'select' 		=>	'secs_id'
                        ,'where' 		=>	array( 
                                                     'is_deleted'		=> '0'
                                                    ,'secs_status'       => '1' 
                                                    ,'id_std' 	        => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                ) 
                        ,'return_type'	=>	'count'
                    ); 
$totalenroll = $dblms->getRows(ENROLLED_COURSES, $conditions);

// TOTAL WISHLIST
$condition = array (
                         'select' 		=>	'wl_id'
                        ,'where' 		=>	array( 
                                                    'id_std'     	  => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                ) 
                        ,'order_by'     =>  "wl_id DESC"
                        ,'return_type'	=>	'count'
                    ); 
$totalwishlist = $dblms->getRows(WISHLIST,$condition);

// ENROLLMENTS
$conditions = array ( 
                         'select'       =>	'c.curs_name, c.curs_photo, c.curs_id, c.curs_href, mt.mas_name, mt.mas_href, mt.mas_photo, p.prg_photo, p.prg_href, ap.program, ec.secs_id, ec.id_type, ec.secs_status, ec.id_curs, ec.id_mas, ec.id_ad_prg
                                            ,COUNT(DISTINCT cl.lesson_id) AS lesson_count
                                            ,COUNT(DISTINCT ca.id) AS assignment_count
                                            ,COUNT(DISTINCT cq.quiz_id) AS quiz_count
                                            ,COUNT(DISTINCT lt.track_id) AS track_count'
                        ,'join'         =>	'INNER JOIN '.CHALLANS.' ch ON FIND_IN_SET(ec.secs_id,ch.id_enroll) AND ch.is_deleted = 0
                                             LEFT JOIN '.COURSES.' c ON c.curs_id = ec.id_curs AND c.curs_status = 1 AND c.is_deleted = 0
                                             LEFT JOIN '.MASTER_TRACK.' mt ON mt.mas_id = ec.id_mas AND mt.mas_status = 1 AND mt.is_deleted = 0
                                             LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ec.id_ad_prg
                                             LEFT JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg                                             
                                             LEFT JOIN '.COURSES_LESSONS.' AS cl ON FIND_IN_SET(cl.id_curs, ec.id_curs) AND cl.lesson_status = 1 AND cl.is_deleted = 0
                                             LEFT JOIN '.COURSES_ASSIGNMENTS.' AS ca ON FIND_IN_SET(ca.id_curs, ec.id_curs) AND ca.status = 1 AND ca.is_deleted = 0
                                             LEFT JOIN '.QUIZ.' AS cq ON FIND_IN_SET(cq.id_curs, ec.id_curs) AND cq.quiz_status = 1 AND cq.is_deleted = 0 AND cq.is_publish = 1
                                             LEFT JOIN '.LECTURE_TRACKING.' AS lt ON FIND_IN_SET(lt.id_curs, ec.id_curs) AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.is_completed = 2 AND lt.is_deleted = 0 AND lt.id_mas = ec.id_mas AND lt.id_ad_prg = ec.id_ad_prg'
                        ,'where' 		=>	array( 
                                                     'ec.is_deleted'    => '0'
                                                    ,'ec.id_std' 	    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                )
                        ,'group_by'     =>	'ec.secs_id'
                        ,'order_by'     =>	'ec.secs_id DESC'
                        ,'return_type'	=>	'all'
                    ); 
$ENROLLED_COURSES = $dblms->getRows(ENROLLED_COURSES.' ec', $conditions, $sql);
echo'
<div class="settings-top-widget student-deposit-blk">
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="card stat-info ttl-tickets">
                <div class="card-body">
                    <div class="view-all-grp d-flex">
                        <div class="student-ticket-view">
                            <h3>'.($totalenroll?$totalenroll: 0).'</h3>
                            <p>Purchased Courses</p>
                        </div>
                        <div class="img-deposit-ticket">
                            <img src="'.SITE_URL.'assets/img/students/book.svg" alt />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card stat-info open-tickets">
                <div class="card-body">
                    <div class="view-all-grp d-flex">
                        <div class="student-ticket-view">
                            <h3>'.($totaltransctions?$totaltransctions:0).'</h3>
                            <p>Total Transactions</p>
                        </div>
                        <div class="img-deposit-ticket">
                            <img src="'.SITE_URL.'assets/img/students/receipt-text.svg" alt />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card stat-info ttl-tickets">
                <div class="card-body">
                    <div class="view-all-grp d-flex">
                        <div class="student-ticket-view">
                            <h3>'.($totalwishlist?$totalwishlist:0).'</h3>
                            <p>Total Wishlists</p>
                        </div>
                        <div class="img-deposit-ticket">
                            <img src="'.SITE_URL.'assets/img/students/wish.svg" alt />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="settings-widget">
    <div class="settings-inner-blk p-0">
        <div class="comman-space pb-0">
            <div class="filter-grp ticket-grp d-flex align-items-center justify-content-between" >
                <h3>My Learning</h3>
            </div>
            <div class="settings-tickets-blk table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">Sr.</th>
                            <th>Name</th>
                            <th class="text-center">Progress</th>
                            <th class="text-center">Certificate</th>
                        </tr>
                    </thead>
                    <tbody>';
                        if($ENROLLED_COURSES){
                            $srno = 0;
                            foreach ($ENROLLED_COURSES as $row) {
                                // Learn Type
                                $conditions = array ( 
                                                    'select' 		=>	'id_type'
                                                    ,'where' 		=>	array( 
                                                                                'is_deleted'		=> '0'
                                                                                ,'admoff_degree'      => $row['id_curs']
                                                                            )
                                                    ,'return_type'	=>	'single'
                                ); 
                                $learn_type = $dblms->getRows(ADMISSION_OFFERING, $conditions);

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
                                    // PERCENTAGE
                                    $Total      = $row['lesson_count']+$row['assignment_count']+$row['quiz_count'];
                                    $Obtain     = $row['track_count'];
                                    $percent    = (($Obtain/$Total)*100);
                                    $percent    = ($percent >= '100' ? '100' : $percent);

                                    if($type == '3' || $type == '4'){
                                        $arrays = array();
                                        // OPEN LESSON
                                        $con    = array ( 
                                                            'select'   =>	'cl.lesson_id as id, cl.id_week, lt.is_completed'
                                                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON ( lt.id_curs = '.$row['curs_id'].' AND lt.id_lecture = cl.lesson_id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$row['id_mas'].' AND lt.id_ad_prg = '.$row['id_ad_prg'].')' 
                                                            ,'where' 	=>	array( 
                                                                                    'cl.lesson_status' => 1
                                                                                    ,'cl.is_deleted'    => 0
                                                                                    ,'cl.id_curs' 	    => cleanvars($row['curs_id']) 
                                                                                ) 
                                                            ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                                                            ,'order_by'     =>	'cl.id_week ASC LIMIT 1'
                                                            ,'return_type'  =>	'single'
                                                        ); 
                                        $COURSES_LESSONS = $dblms->getRows(COURSES_LESSONS.' cl', $con);
                                        if($COURSES_LESSONS){
                                            array_push($COURSES_LESSONS, 'lesson');
                                            array_push($arrays, $COURSES_LESSONS);
                                        }                                        

                                        // OPEN ASSIGNMENT
                                        $con    = array ( 
                                                            'select'   =>	'ca.id as id, ca.id_week, lt.is_completed'
                                                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' AS lt ON (lt.id_curs = '.cleanvars($row['curs_id']).' AND lt.id_assignment = ca.id AND lt.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).' AND lt.id_mas = '.$row['id_mas'].' AND lt.id_ad_prg = '.$row['id_ad_prg'].')' 
                                                            ,'where' 	=>	array( 
                                                                                    'ca.status'        => 1
                                                                                    ,'ca.is_deleted'    => 0
                                                                                    ,'ca.id_curs' 	    => cleanvars($row['curs_id']) 
                                                                                ) 
                                                            ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                                                            ,'order_by'     =>	'ca.id_week ASC LIMIT 1'
                                                            ,'return_type'  =>	'single'
                                                        ); 
                                        $COURSES_ASSIGNMENTS = $dblms->getRows(COURSES_ASSIGNMENTS.' ca', $con);
                                        if($COURSES_ASSIGNMENTS){
                                            array_push($COURSES_ASSIGNMENTS, 'assignments');
                                            array_push($arrays, $COURSES_ASSIGNMENTS);
                                        }

                                        // OPEN QUIZ
                                        $con    = array ( 
                                                            'select'   =>	'q.quiz_id as id, q.id_week, lt.is_completed'
                                                            ,'join'     =>	'LEFT JOIN '.LECTURE_TRACKING.' lt ON (lt.id_quiz = q.quiz_id AND lt.id_curs = '.$row['curs_id'].' AND lt.id_std = '.$_SESSION['userlogininfo']['STDID'].' AND lt.id_mas = '.$row['id_mas'].' AND lt.id_ad_prg = '.$row['id_ad_prg'].')'
                                                            ,'where' 	=>	array( 
                                                                                    'q.quiz_status'    => 1
                                                                                    ,'q.is_deleted'     => 0
                                                                                    ,'q.is_publish'     => 1
                                                                                    ,'q.id_curs'        => cleanvars($row['curs_id']) 
                                                                            ) 
                                                            ,'search_by'    =>	' AND ( lt.is_completed IS NULL OR lt.is_completed = "" OR lt.is_completed = 1 )'
                                                            ,'order_by'     =>	'q.id_week ASC, q.quiz_id ASC'
                                                            ,'return_type'  =>	'single'
                                                        ); 
                                        $QUIZ = $dblms->getRows(QUIZ.' q', $con, $sql);
                                        if($QUIZ){
                                            array_push($QUIZ, 'quiz');
                                            array_push($arrays, $QUIZ);
                                        }

                                        // Initialize variables to keep track of the minimum id_week and its index
                                        $minIdWeek = PHP_INT_MAX;
                                        $minIdWeekIndex = -1;

                                        // Loop through each array
                                        foreach ($arrays as $index => $array) {
                                            // Check if the current array has the lowest id_week
                                            if (isset($array['id_week']) && $array['id_week'] < $minIdWeek) {
                                                // Update the minimum id_week and its index
                                                $minIdWeek = $array['id_week'];
                                                $minIdWeekIndex = $index;
                                            }
                                        }

                                        // Output the array with the lowest id_week AND modify redirection
                                        if ($minIdWeekIndex != -1) {
                                            $redirection = end($arrays[$minIdWeekIndex]).'/'.$arrays[$minIdWeekIndex]['id'];
                                        }else{ 
                                            $redirection = 'lesson/0';
                                        }

                                        $href = SITE_URL.'learn/'.$row['curs_href'].'/'.$redirection.'/'.$row['secs_id'];
                                    } else if($type == '2'){
                                        $href = SITE_URL.'student/master-track-certificates/'.$row['mas_href'];
                                    } else if($type == '1'){
                                        $href = SITE_URL.'student/degrees/'.$row['prg_href'];
                                    }
                                    echo'
                                    <tr>
                                        <td class="text-center">'.$srno.'</td>
                                        <td>
                                            <div class="sell-table-group d-flex align-items-center">
                                                <div class="sell-group-img">
                                                    <a href="'.$href.'">
                                                        <img class="img-fluid p-1 border" alt="" src="'.$photo.'">
                                                    </a>
                                                </div>
                                                <div class="sell-tabel-info">
                                                    <p>                                                    
                                                        <a href="'.$href.'">'.$name.'</a>
                                                    </p>
                                                    <div class="course-info d-flex align-items-center border-bottom-0 pb-0">
                                                        <div class="rating-img d-flex align-items-center">
                                                            <img src="'.SITE_URL.'assets/img/icon/icon-23.svg" alt="">
                                                            <p>'.get_enroll_type($type).'</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress-stip">
                                                <div class="progress-bar bg-success progress-bar-striped active-stip" style="width: '.intval($percent).'% !important"></div>
                                            </div>
                                            <div class="student-percent">
                                                <p class="m-0">'.intval($percent).'% Completed</p>
                                            </div>
                                        </td>
                                        <td class="text-center">';
                                            if($percent == '100' && $learn_type['id_type'] !=3 ){
                                                echo'<a href="'.SITE_URL.'certificate-print/'.$row['secs_id'].'"><span class="badge badge-info"><i class="fa fa-graduation-cap me-1"></i>Get Certificate</span></a>';
                                            }
                                            echo'
                                        </td>
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