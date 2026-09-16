<?php
// LESSONS
$condition = array ( 
                     'select'       =>	'cl.id_week, wt.caption'
                    ,'join'         =>  'LEFT JOIN '.COURSES_WEEK_TITLE.' wt ON wt.id_week = cl.id_week AND wt.id_curs = cl.id_curs AND wt.is_deleted = 0 AND wt.status = 1'
                    ,'where'        =>	array( 
                                                 'cl.id_curs'      => $_GET['curs_id']
                                                ,'cl.is_deleted'   => '0'  
                                            )
                    ,'group_by'     =>	'cl.id_week'
                    ,'return_type'	=>	'all'
                  );
$COURSES_LESSONS_WEEKS = $dblms->getRows(COURSES_LESSONS.' cl', $condition);
echo'
<div class="tab-pane fade show" id="course_content">
    <div class="card overview-sec">
        <div class="card-body">';
            $total_lec = 0;
            foreach ($COURSES_LESSONS_WEEKS as $key => $value) :                
                // COURSE LESSONS
                $condition = array ( 
                                         'select'       =>	'lesson_topic, lesson_detail, lesson_content'
                                        ,'where'        =>	array( 
                                                                     'id_week' 	     => $value['id_week']
                                                                    ,'id_curs' 	     => $_GET['curs_id']
                                                                    ,'is_deleted' 	 => '0'  
                                                                )
                                        ,'return_type'	=>	'count'
                                    );
                $lessonCount = $dblms->getRows(COURSES_LESSONS, $condition);
                $condition['return_type'] = 'all';
                $COURSES_LESSONS = $dblms->getRows(COURSES_LESSONS, $condition);

                //COURSE ASSIGNMENTS
                $condition = array ( 
                                        'select'        =>	'caption'
                                        ,'where'        =>	array( 
                                                                     'id_week' 	     => $value['id_week']
                                                                    ,'id_curs' 	     => $_GET['curs_id']
                                                                    ,'is_deleted' 	 => '0'
                                                                    ,'status'        => '1'  
                                                                )                                                        
                                        ,'order_by'     => 'id ASC'
                                        ,'return_type'	=>	'count'
                                    );
                $assignCount = $dblms->getRows(COURSES_ASSIGNMENTS, $condition);
                $condition['return_type'] = 'all';
                $COURSES_ASSIGNMENTS = $dblms->getRows(COURSES_ASSIGNMENTS, $condition);
                // COURSE QUIZ
                $condition = array ( 
                                         'select'       =>	'quiz_title'
                                        ,'where'        =>	array( 
                                                                     'id_week'        => $value['id_week']
                                                                    ,'id_curs'        => $_GET['curs_id']
                                                                    ,'is_deleted'     => '0'
                                                                    ,'quiz_status'    => '1'
                                                                    ,'is_publish'     => '1'
                                                                )                                                            
                                        ,'order_by'     =>  'quiz_id ASC'
                                        ,'return_type'	=>	'count'
                                    );
                $quizCount = $dblms->getRows(QUIZ, $condition);
                $condition['return_type'] = 'all';
                $QUIZ = $dblms->getRows(QUIZ, $condition,$sql);
                echo'
                <div class="row">
                    <div class="col">
                        <h5 class="subs-title">'.get_CourseWise($_GET['curs_wise']).' '.$value['id_week'].' '.(!empty($value['caption']) ? '- '.$value['caption'] : '').'</h5>
                    </div>
                </div>
                <div class="course-card">
                    <h6 class="cou-title">
                    <a class="collapsed" data-bs-toggle="collapse" href="#collapse'.$key.'" aria-expanded="false">
                        '.($lessonCount != 0 ? $lessonCount.' Topics' : '').'
                        '.($assignCount != 0 ? ', '.$assignCount.' Assignment' : '').'
                        '.($quizCount != 0 ? ', '.$quizCount.' Quiz' : '').'
                    </a>
                    </h6>
                    <div id="collapse'.$key.'" class="card-collapse collapse mb-4 '.(($value['id_week']==1) ? 'show' : '').'" >
                        <div class="container">
                            <ul>';
                                $srno=0;
                                if($COURSES_LESSONS){
                                    foreach ($COURSES_LESSONS as $inkey => $lesson) {
                                        $srno++;
                                        echo'
                                        <li>
                                            <p>
                                                <i class="'.($lesson['lesson_content'] != 2 ? 'fas fa-play-circle' : 'fa fa-book-reader').'"></i>
                                                Topic '.$srno.': '.(html_entity_decode($lesson['lesson_topic'])).'
                                            </p>
                                            <div><p class="text-danger">'.($lesson['lesson_content'] == 3 ? 'Video and Reading' : get_topic_content($lesson['lesson_content'])).'</p></div>
                                        </li>';
                                        $total_lec++;
                                    }
                                }
                                if($COURSES_ASSIGNMENTS){
                                    foreach ($COURSES_ASSIGNMENTS as $key => $value) {
                                        echo'
                                        <li>
                                            <p><i class="fas fa-clipboard"></i>  Assignment-'.($key+1).'</p>
                                            <div><p class="text-danger">Assignment</p></div>
                                        </li>';
                                    }  
                                }
                                if($QUIZ){
                                    foreach ($QUIZ as $key => $value) {
                                        echo'
                                        <li>
                                            <p><i class="fas fa-link"></i>  Quiz-'.($key+1).'</p>
                                            <div><p class="text-danger">Quiz</p></div>
                                        </li>';
                                    }  
                                }
                                echo'
                            </ul>
                        </div>
                    </div>
                </div>';
            endforeach;
            echo'
        </div>
    </div>
</div>';