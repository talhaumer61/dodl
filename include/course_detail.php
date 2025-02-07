<?php
$course2 = $courses['python-for-everybody-specialization'];
// COURSE INFO
$condition = array ( 
                     'select'       =>	'ci.objectives, ci.outcomes,c.curs_rating, f.faculty_name, ci.outlines
                                        ,COUNT(DISTINCT ca.id) as TotalAssignments
                                        ,COUNT(DISTINCT cd.id) as TotalResources
                                        ,ao.id_type, ao.admoff_type, ao.admoff_amount, ao.admoff_amount_in_usd, ao.admoff_startdate, ao.admoff_enddate, c.curs_id,c.curs_name, c.curs_wise, c.curs_hours, c.duration, c.curs_video, c.how_it_work, c.curs_skills, c.curs_about, c.what_you_learn, c.curs_domain, c.curs_code, c.id_dept, c.id_level, c.id_lang, c.curs_type_status
                                        ,COUNT(DISTINCT l.lesson_id) TotalLesson
                                        ,GROUP_CONCAT(DISTINCT lg.lang_name) as languages
                                        ,COUNT(DISTINCT ec.secs_id) as TotalStd, ec2.secs_id as ifEnrolled, w.wl_id as ifWishlist'
                    ,'join'         =>  'LEFT JOIN '.COURSES_LESSONS.' l ON l.id_curs = c.curs_id AND l.is_deleted = 0 AND l.lesson_status = 1
                                         LEFT JOIN '.ENROLLED_COURSES.' ec ON FIND_IN_SET(c.curs_id, ec.id_curs)
                                         LEFT JOIN '.ENROLLED_COURSES.' ec2 ON ec2.id_curs = c.curs_id AND ec2.id_ad_prg = 0 AND ec2.id_mas = 0 AND ec2.id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'"
                                         LEFT JOIN '.WISHLIST.' w ON w.id_curs = c.curs_id AND w.id_ad_prg IS NULL AND w.id_mas IS NULL AND w.id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'"
                                         LEFT JOIN '.COURSES_DOWNLOADS.' cd on cd.id_curs = c.curs_id AND cd.is_deleted = 0 AND cd.status = 1
                                         LEFT JOIN '.COURSES_ASSIGNMENTS.' ca on ca.id_curs = c.curs_id AND ca.is_deleted = 0 AND ca.status = 1 
                                         LEFT JOIN '.ADMISSION_OFFERING.' ao on ao.admoff_degree = c.curs_id AND ao.is_deleted = 0
                                         LEFT JOIN '.COURSES_INFO.' ci ON ci.id_curs = c.curs_id
                                         LEFT JOIN '.LANGUAGES.' lg ON FIND_IN_SET(lg.lang_id,c.id_lang)
                                         LEFT JOIN '.FACULTIES.' f ON f.faculty_id = c.id_faculty'
                    ,'where' 		    =>	array( 
                                             'c.curs_href' 	   => $zone
                                            ,'c.curs_status' 	 => '1'  
                                            ,'c.is_deleted' 	 => '0'  
                                          )
                    ,'return_type'	=>	'single'
            ); 
$course = $dblms->getRows(COURSES.' c', $condition, $sql);

if($course['curs_id']){
// REFERRAL_CONTROL
if (!empty(LMS_VIEW)) {
  $ref_array        = explode(',', get_dataHashingOnlyExp(LMS_VIEW, false));
  if ($zone == $ref_array[0]) {
    $curs_href        = cleanvars($ref_array[0]);
    $ref_percentage   = cleanvars($ref_array[1]);
    $ref_id           = cleanvars($ref_array[2]);
    $adm_email        = cleanvars($ref_array[3]);
    $condition = array(
                         'select'       =>  'r.ref_id, r.ref_percentage, a.adm_email'
                        ,'join'         =>  'INNER JOIN '.COURSES.' AS c ON c.curs_id = "'.$course['curs_id'].'"
                                              INNER JOIN '.REFERRAL_TEACHER_SHARING.' AS rt ON rt.id_ref = r.ref_id AND FIND_IN_SET("'.$adm_email.'", rt.std_emails) AND rt.ref_shr_status = "1" AND rt.is_deleted = "0"
                                              LEFT JOIN '.ADMINS.' AS a ON FIND_IN_SET(a.adm_email, rt.std_emails) AND a.adm_status = "1" AND a.is_deleted = "0"'
                        ,'where'        =>  array(
                                                     'r.is_deleted' =>	0
                                                    ,'r.ref_status' =>	1
                                                    ,'r.ref_id'     =>	$ref_id
                                                )
                        ,'group_by'     =>  ' a.adm_id '
                        ,'search_by'    =>  ' AND r.ref_date_time_from < "'.date('Y-m-d G:i:s').'" AND r.ref_date_time_to > "'.date('Y-m-d G:i:s').'" AND FIND_IN_SET("'.cleanvars($course['curs_id']).'", r.id_curs)'
                        ,'return_type'  =>  'single'
    );
    $REFERRAL_CONTROL = $dblms->getRows(REFERRAL_CONTROL.' AS r', $condition, $sql);
  }
}

$curs_rating = (($course['curs_rating'] == '0' || $course['curs_rating'] == '') ? '4' : $course['curs_rating']);

$what_you_learn	=	json_decode(html_entity_decode($course['what_you_learn']), true);

// FAQ'S
$condition = array ( 
                     'select'       =>	'question,answer'
                    ,'where' 		    =>	array( 
                                               'id_curs' 	     => $course['curs_id']
                                              ,'is_deleted' 	 => '0'  
                                            )
                    ,'return_type'	=>	'all'
                  ); 
$get_faq = $dblms->getRows(COURSES_FAQS, $condition);

// LESSONS
$condition = array ( 
                     'select'       =>	'id_week'
                    ,'where' 		    =>	array( 
                                               'id_curs'      => $course['curs_id']
                                              ,'is_deleted'   => '0'  
                                            )
                    ,'search_by'	  =>	'group by id_week'
                    ,'return_type'	=>	'all'
                  );
$get_week = $dblms->getRows(COURSES_LESSONS, $condition);   

// DEPARTMENT
$condition = array ( 
                       'select'       =>	'dept_name'
                      ,'where' 		    =>	array( 
                                                   'dept_id ' 	   => $course['id_dept']
                                                  ,'is_deleted' 	 => '0'  
                                                )
                      ,'return_type'	=>	'single'
                    );
$get_dept = $dblms->getRows(DEPARTMENTS, $condition);

// ALLOCATED TEACHERS
$condition = array ( 
                       'select'       =>  'a.adm_photo, e.emply_name, e.emply_gender, e.emply_id, e.emply_photo, d.dept_name, des.designation_name, COUNT(alt2.id_curs) as t_curs'
                      ,'join'         =>  'INNER JOIN '.ALLOCATE_TEACHERS.' alt on FIND_IN_SET(e.emply_id,alt.id_teacher)
                                           INNER JOIN '.ALLOCATE_TEACHERS.' alt2 on FIND_IN_SET(e.emply_id,alt2.id_teacher)
                                           LEFT JOIN '.ADMINS.' a ON a.adm_id = e.emply_loginid
                                           LEFT JOIN '.DEPARTMENTS.' d on e.id_dept=d.dept_id
                                           LEFT JOIN '.DESIGNATIONS.' des on e.id_designation=des.designation_id'
                      ,'where' 		    =>  array( 
                                                   'alt.id_curs' 	   => $course['curs_id']
                                                  ,'e.emply_status'  => '1'  
                                                  ,'e.is_deleted' 	 => '0'  
                                                )
                      ,'group_by'     =>  'e.emply_id'
                      ,'return_type'	=>  'all'
                    );
$allocated_teachers = $dblms->getRows(EMPLOYEES.' e', $condition);

$courseMenu = array(
   'description'
  ,'course_content'
  ,'instructors'
  ,'how_it_works'
  ,'enrollment'
  ,'faq'
);
echo'
<div class="inner-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h2>'.$course['curs_name'].'</h2>
        <div class="instructor-wrap border-bottom-0 m-0">
          <span class="web-badge mb-3">'.$get_dept['dept_name'].'</span>
        </div>
        <pre class="text-info mb-2">'.$course['faculty_name'].'</pre>
        <div class="course-info d-flex align-items-center border-bottom-0 p-0">
          <div class="cou-info">';
            if($course['id_type'] != 2){
              $coursePrice = (__COUNTRY__ == 'pk')?$course['admoff_amount']:$course['admoff_amount_in_usd'];
              $condition = array ( 
                                     'select'       =>	'd.discount_id, dd.discount, dd.discount_type'
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
                <p>'.$DISCOUNT['discount'].'% OFF</p>
                <p>
                  <small><s>'.(__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice).'</s></small>
                  <span class="text-info">/';
                    if ($DISCOUNT['discount_type'] == 1) {
                      echo ($coursePrice - $DISCOUNT['discount']);
                    } else if ($DISCOUNT['discount_type'] == 2) {
                      echo ($coursePrice - ($coursePrice * ($DISCOUNT['discount'] / 100) ));
                    }  echo'
                  </span>
                </p>';
              } else {
                echo '<p>'.(__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice).'</p>';
              }
            } else {
              echo get_LeanerType($course['id_type']);
            }
            echo'
          </div>';
          /*
          echo'
          <div class="cou-info">
            <div class="rating">';
              for ($rate=1; $rate <= 5; $rate++) {
                echo'<i class="fas fa-star '.($rate <= $curs_rating ? 'filled' : '').'"></i>';
              }
              echo'
            </div>
          </div>';
          */
          echo'
        </div>
        <div class="course-info d-flex align-items-center border-bottom-0 m-0 p-0">
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/icon-01.svg" alt />
            <p>'.$course['duration'].' '.get_CourseWise($course['curs_wise']).($course['duration'] > 1 ? 's' : '').'</p>
          </div>';
          /*
          echo'
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/people.svg" alt />
            <p>'.($course['TotalStd']+100).' Students</p>
          </div>';
          if(!empty($course['admoff_startdate'])){
            echo'
            <div class="cou-info">
              <img src="'.SITE_URL.'assets/img/icon/timer-icon.svg" alt />
              <p>'.date('d M, Y', strtotime($course['admoff_startdate'])).' to '.date('d M, Y', strtotime($course['admoff_enddate'])).'</p>
            </div>';
          }
          */
          echo'
        </div>
      </div>
    </div>
  </div>
</div>
<section class="page-content course-sec">
  <div class="container">
    <div class="row">
      <div class="col-lg-'.($varx == 'student' ? '12' : '8').'">
        <div class="row">
          <div class="featured-courses-five-tab">
            <div class="tab-content">
              <div class="nav tablist-five" style="text-align: left !important;" role="tablist">';
                foreach ($courseMenu as $key => $value) {
                  echo'<a class="nav-tab '.($key == 0 ? 'active' : '').'" data-bs-toggle="tab" href="#'.$value.'" role="tab"> '.($value == 'faq' ? 'FAQs' : moduleName($value)).'</a>';
                }
                echo'
              </div>
              <div class="tab-content">
                <div class="tab-pane fade show active" id="description">
                  <div class="card overview-sec">
                    <div class="card-body">';
                      $sr = 0;
                      if($what_you_learn){
                        foreach ($what_you_learn as $value) {
                          if(!empty($value)){
                            $sr++;
                            if($sr == 1){
                              echo'
                              <h5 class="subs-title text-center">WHAT YOU WILL LEARN</h5>
                              <div class="row"><div class="col"><ul>';
                            }
                            echo '<li>'.$value.'</li>';
                            $sr = 1;
                          }
                        }                        
                        if($sr == 1){
                          echo '</ul></div></div><br>';
                        }
                      }
                      if($course['curs_skills']){
                        echo'
                        <h5 class="subs-title text-center">SKILLS YOU WILL GAIN</h5>
                        <div class="row">
                          <div class="col">
                            <ul>';
                              foreach (explode(',',$course['curs_skills']) as $key => $value) {
                                echo '<li>'.$value.'</li>';
                              }
                              echo '
                            </ul>
                          </div>
                        </div>';
                      }
                      if($course['curs_about']){
                        echo'
                        <h5 class="subs-title">Description</h5>
                        '.html_entity_decode(html_entity_decode($course['curs_about'])).'';
                      }
                      if($course['objectives']){
                        echo'
                        <h5 class="subs-title">Learning Objectives</h5>
                        '.html_entity_decode(html_entity_decode($course['objectives'])).'';
                      }
                      if($course['outcomes']){
                        echo'
                        <h5 class="subs-title">Learning Outcomes</h5>
                        '.html_entity_decode(html_entity_decode($course['outcomes'])).'';
                      }
                      if($course['outlines']){
                        echo'
                        <h5 class="subs-title">'.(CONTROLER == 'trainings' ? 'Sections Details' : 'Weekly Lectures Plan').'</h5>
                        '.html_entity_decode(html_entity_decode($course['outlines'])).'';
                      }
                      echo'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="how_it_works">
                  <div class="card overview-sec">
                    <div class="card-body">
                    <h5 class="subs-title text-center">How the Specialization Works</h5>
                    '.html_entity_decode(html_entity_decode($course['how_it_work'])).'</div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="instructors">
                  <div class="card overview-sec">
                    <div class="card-body">
                      <div class="row">';
                        if($allocated_teachers){
                          foreach ($allocated_teachers as $value) {
                            // CHECK FILE EXIST
                            if($value['emply_gender'] == '2'){
                              $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
                            }else{            
                              $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
                            }
                            if(!empty($value['adm_photo'])){
                              $file_url = SITE_URL_PORTAL.'uploads/images/admin/'.$value['adm_photo'];
                              if (check_file_exists($file_url)) {
                                $photo = $file_url;
                              }
                            } else {
                              $file_url = SITE_URL_PORTAL.'uploads/images/employees/'.$value['emply_photo'];
                              if (check_file_exists($file_url)) {
                                $photo = $file_url;
                              }
                            }
                            echo'
                            <div class="col-6">
                              <div class="card p-2 my-1 shadow">
                                <div class="col-md-12 instructor-wrap border-bottom-0 m-0">
                                  <div class="about-instructor align-items-center mb-0">
                                    <div class="me-3">
                                      <img src="'.$photo.'" width="100" height="100" alt="img" class="circle p-1"/>
                                    </div>
                                    <div class="instructor-detail me-3">
                                      <h5><a href="'.SITE_URL.'instructors/'.$value['emply_id'].'">'.$value['emply_name'].'</a></h5>
                                      <p>'.$value['designation_name'].'</p>
                                      <p>'.$value['dept_name'].'</p>
                                      <p>'.$value['t_curs'].' Courses</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>';
                          }
                        }else{
                          echo '<h5 class="text-center text-danger mb-0">No Instructors Found for this Course</h5>';
                        }
                        echo '
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="course_content">
                  <div class="card overview-sec">
                    <div class="card-body">';
                      $total_lec = 0;
                      foreach ($get_week as $key => $value) {
                        // WEEK TITLE
                        $condition = array ( 
                                              'select' 		    =>	'caption'
                                              ,'where' 		    =>	array( 
                                                                         'id_week'      => $value['id_week']
                                                                        ,'id_curs'      => $course['curs_id']
                                                                        ,'is_deleted'   => '0'  
                                                                        ,'status'       => '1'  
                                                                      )
                                              ,'return_type'	=>	'single'
                                            );
                        $weekTitle = $dblms->getRows(COURSES_WEEK_TITLE, $condition);
                        
                        // COURSE LESSONS
                        $condition = array ( 
                                              'select' 		    =>	'lesson_topic, lesson_detail, lesson_content'
                                              ,'where' 		    =>	array( 
                                                                         'id_week' 	     => $value['id_week']
                                                                        ,'id_curs' 	     => $course['curs_id']
                                                                        ,'is_deleted' 	 => '0'  
                                                                      )
                                              ,'return_type'	=>	'count'
                                            );
                        $lessonCount = $dblms->getRows(COURSES_LESSONS, $condition);
                        $condition['return_type'] = 'all';
                        $get_lesson = $dblms->getRows(COURSES_LESSONS, $condition);
                        //COURSE ASSIGNMENTS
                        $condition = array ( 
                                              'select' 		    =>	'caption'
                                              ,'where' 		    =>	array( 
                                                                      'id_week' 	     => $value['id_week']
                                                                      ,'id_curs' 	     => $course['curs_id']
                                                                      ,'is_deleted' 	 => '0'
                                                                      ,'status'        => '1'  
                                                                    )
                                                                
                                              ,'order_by'     => 'id ASC'
                                              ,'return_type'	=>	'count'
                                            );
                        $assignCount = $dblms->getRows(COURSES_ASSIGNMENTS, $condition);
                        $condition['return_type'] = 'all';
                        $get_assignments = $dblms->getRows(COURSES_ASSIGNMENTS, $condition);
                        // COURSE QUIZ
                        $condition = array ( 
                                               'select' 		    =>	'quiz_title'
                                              ,'where' 		    =>	array( 
                                                                       'id_week'        => $value['id_week']
                                                                      ,'id_curs'        => $course['curs_id']
                                                                      ,'is_deleted'     => '0'
                                                                      ,'quiz_status'    => '1'
                                                                      ,'is_publish'     => '1'
                                                                    )
                                                                
                                              ,'order_by'     => 'quiz_id ASC'
                                              ,'return_type'	=>	'count'
                                            );
                        $quizCount = $dblms->getRows(QUIZ, $condition);
                        $condition['return_type'] = 'all';
                        $get_quiz = $dblms->getRows(QUIZ, $condition,$sql);
                        echo'
                        <div class="row">
                          <div class="col">
                            <h5 class="subs-title">'.get_CourseWise($course['curs_wise']).' '.$value['id_week'].' '.(!empty($weekTitle['caption']) ? '- '.$weekTitle['caption'] : '').'</h5>
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
                                if($get_lesson){
                                  foreach ($get_lesson as $inkey => $lesson) {
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
                                if($get_assignments){
                                  foreach ($get_assignments as $key => $value) {
                                    echo'
                                    <li>
                                      <p><i class="fas fa-clipboard"></i>  Assignment-'.($key+1).'</p>
                                      <div><p class="text-danger">Assignment</p></div>
                                    </li>';
                                  }  
                                }
                                if($get_quiz){
                                  foreach ($get_quiz as $key => $value) {
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
                      }
                      echo'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="enrollment">
                  <div class="card overview-sec">
                    <div class="card-body">
                    '.$course2['description']['enrollment'].'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="faq">
                  <div class="card overview-sec">
                    <div class="card-body">';
                      if($get_faq){
                        echo'
                        <h5 class="subs-title text-center">Frequently asked questions</h5>';
                        foreach ($get_faq as $key => $faq) {
                          echo"
                          <div class='course-card'>
                            <h6 class='cou-title'>
                              <a class='collapsed' data-bs-toggle='collapse' href='#q-".$key."' aria-expanded='false'>".$faq['question']."</a>
                            </h6>
                            <div id='q-".$key."' class='card-collapse collapse px-3'>
                              ".html_entity_decode(html_entity_decode($faq['answer']))."
                            </div>
                          </div>";
                        }
                      }else{
                        echo '<h5 class="text-center text-danger mb-0">No FAQ\'s Found for this Course</h5>';
                      }
                      echo'
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>';
        /*
        echo'
        <div class="card comment-sec">
          <div class="card-body">
            <h5 class="subs-title">Post A comment</h5>
            <form>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Full Name"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email"/>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <input type="email" class="form-control" placeholder="Subject"/>
              </div>
              <div class="form-group">
                <textarea rows="4" class="form-control" placeholder="Your Comments"></textarea>
              </div>
              <div class="submit-section">
                <button class="btn submit-btn" type="submit">Submit</button>
              </div>
            </form>
          </div>
        </div>';
        */
        echo'
      </div>';
      if($varx != 'student'){
        echo '
        <div class="col-lg-4">
          <div class="sidebar-sec">
            <div class="video-sec vid-bg">
              <div class="card">
                <div class="card-body">
                  <a href="https://www.youtube.com/embed/'.$course['curs_video'].'" class="video-thumbnail" data-fancybox>
                    <div class="play-icon">
                      <i class="fa-solid fa-play"></i>
                    </div>
                    <img src="http://img.youtube.com/vi/'.$course['curs_video'].'/sddefault.jpg" alt />
                  </a>
                  <div class="video-details">';
                    /*
                    echo'
                    <div class="course-fee">
                      <h2>FREE</h2>
                      <p><span>PKR 40000</span> 50% off</p>
                    </div>';
                    */
                    echo'
                    <div class="row gx-2">
                      <div class="col" data-id="'.$course['curs_id'].'">';
                        if (isset($course['ifWishlist']) && !empty($course['ifWishlist'])){
                          echo '<a class="btn btn-wish w-100" href="javascript:;" onclick="remove_from_wishlist('.$course['ifWishlist'].','.$course['curs_id'].',3,3)"><i class="fa-solid fa-heart"></i> Remove Wishlist</a>';
                        } else {
                          echo '<a class="btn btn-wish w-100" href="javascript:;" onclick="add_to_wishlist('.$course['curs_id'].',3,3)"><i class="fa-regular fa-heart"></i> Add to Wishlist</a>';
                        }
                        echo'
                      </div>
                      <div class="col">';
                        // REFERRAL_CONTROL
                        $condition = array(
                                             'select'       =>  'r.ref_id, r.ref_percentage, c.curs_href'
                                            ,'join'         =>  'INNER JOIN '.COURSES.' AS c ON c.curs_id = '.cleanvars($course['curs_id']).''
                                            ,'where'        =>  array(
                                                                         'r.is_deleted' =>	0
                                                                        ,'r.ref_status' =>	1
                                                                    )
                                            ,'search_by'    =>  ' AND r.ref_date_time_from < "'.date('Y-m-d G:i:s').'" AND r.ref_date_time_to > "'.date('Y-m-d G:i:s').'" AND FIND_IN_SET(c.curs_id, r.id_curs) AND FIND_IN_SET('.cleanvars($_SESSION['userlogininfo']['LOGINIDA']).', r.id_user)'
                                            ,'return_type'  =>  'single'
                        );
                        $REFERRAL_CONTROL_STD = $dblms->getRows(REFERRAL_CONTROL.' AS r', $condition, $sql);
                        if ($REFERRAL_CONTROL_STD) {
                          echo'
                          <button id="curs_share_url_btn" class="btn btn-wish w-100"><i class="feather-share-2"></i> Share '.$REFERRAL_CONTROL_STD['ref_percentage'].'% OFF </button>
                          <input type="hidden" id="curs_share_url" value="'.SITE_URL.CONTROLER.'/'.ZONE.'/'.get_dataHashingOnlyExp($REFERRAL_CONTROL_STD['curs_href'].','.$REFERRAL_CONTROL_STD['ref_percentage'].','.$REFERRAL_CONTROL_STD['ref_id'].','.$_SESSION['userlogininfo']['LOGINEMAIL'].',student',true).'">';
                        } else {
                          echo'
                          <button id="curs_share_url_btn" class="btn btn-wish w-100"><i class="feather-share-2"></i> Share </button>
                          <input type="hidden" id="curs_share_url" value="'.SITE_URL.CONTROLER.'/'.ZONE.'">';
                        }
                        echo'
                      </div>
                    </div>';
                    if(!empty($course['ifEnrolled'])){
                      echo'<button class="btn btn-enroll w-100">Enrolled</button>';
                    } else if($course['curs_type_status'] == '5'){
                      echo'<a class="btn btn-enroll w-100" onclick="show_modal(\''.SITE_URL.'include/modals/courses/confirm_buy.php?url='.CONTROLER.'/'.ZONE.'&id='.cleanvars($course['curs_id']).'&type=3&curs_type_status='.$course['curs_type_status'].'\');">Buy Now</a>';    
                    } else if($course['curs_type_status'] == 1){
                      echo'
                      <form action="'.SITE_URL.'show-interest" method="POST">
                        <input type="hidden" name="url" value="'.CONTROLER.'/'.ZONE.'">
                        <input type="hidden" name="id" value="'.$course['curs_id'].'">
                        <input type="hidden" name="type" value="'.$course['admoff_type'].'">
                        <button name="show_interest" type="submit" class="btn btn-enroll w-100">Enroll Now</button>
                      </form>';
                    } else {
                      echo'
                      <form action="'.SITE_URL.'invoice" method="POST">
                        <input type="hidden" name="url" value="'.CONTROLER.'/'.ZONE.'">
                        <input type="hidden" name="id" value="'.$course['curs_id'].'">
                        <input type="hidden" name="type" value="'.$course['admoff_type'].'">';
                        if (!empty($REFERRAL_CONTROL['ref_id'])) {
                          if (isset($_SESSION['userlogininfo']['LOGINEMAIL']) && !empty($_SESSION['userlogininfo']['LOGINEMAIL'])) {
                            if (!empty($REFERRAL_CONTROL['ref_id'])) {
                              echo'
                              <input type="hidden" name="ref_hash" value="'.cleanvars(LMS_VIEW).'">';
                              $ref_flag = true;
                            } else {
                              $ref_flag = false;
                            }
                          } else {
                            echo 'no ref';
                            $webSiteStorage                                 = array();
                              $webSiteStorage['COURSE_REFERRAL_CONTROLER'] 	=	CONTROLER;
                              $webSiteStorage['COURSE_REFERRAL_ZONE'] 		  =	ZONE;
                              $webSiteStorage['COURSE_REFERRAL_VIEW'] 		  =	LMS_VIEW;
                            $_SESSION['webSiteStorage'] 			              =	$webSiteStorage;
                            if (!empty($REFERRAL_CONTROL['adm_email'])){
                              header("location: ".SITE_URL."signin/".cleanvars(LMS_VIEW)."");
                              echo 'signin';
                            } else {
                              header("location: ".SITE_URL."signup/".cleanvars(LMS_VIEW)."");
                              echo 'signup';
                            }
                          }
                        }
                        echo'
                        <button name="enroll" type="submit" class="btn btn-enroll w-100">Enroll Now '.(($ref_flag)?(!empty($ref_array[1])?'<span class="text-warning"> '.$ref_array[1].'% Off </span> On This Course':''):'').'</button>
                      </form>';
                    }
                    echo'                 
                  </div>
                </div>
              </div>
            </div>

            <div class="card include-sec">
              <div class="card-body">
                <div class="cat-title">
                  <h4>This Course Includes</h4>
                </div>
                <ul>';
                  /* echo'
                  <li>
                    <img src="assets/img/icon/import.svg" class="me-2" alt/>
                    11 hours on-demand video
                  </li>';*/
                  echo'
                  <li>
                    <i class="far fa-clock me-2"></i>
                    Duration: <b>'.$course['curs_hours'].' Hour'.($course['duration'] > 1 ? 's' : '').'</b>
                  </li>
                  <li>
                    <i class="fas fa-book-open me-2"></i>
                    '.get_CourseWise($course['curs_wise']).($course['duration'] > 1 ? 's' : '').': <b>'.$course['duration'].'</b>
                  </li>
                  <li>
                    <i class="fab fa-cc-visa me-2"></i>
                    Fee Status: <b>'.get_LeanerType($course['id_type']).'</b>
                  </li>
                  <li>
                    <i class="fas fa-laptop-house me-2"></i>
                    100% Online Course
                  </li>
                  <li>
                    <i class="fas fa-certificate me-2"></i>
                    Shareable Certificate
                  </li>
                  <li>
                    <i class="fas fa-chart-line me-2"></i>
                    Flexible Schedule
                  </li>
                  <li>
                    <i class="fas fa-chart-pie me-2"></i>
                    Level: <b>'.get_levels($course['id_level']).'</b>
                  </li>
                  <li>
                    <i class="fas fa-language me-2"></i>
                    Language: <b>'.$course['languages'].'</b>
                  </li>
                </ul>
              </div>
            </div>';
            /*
            echo'
            <div class="card feature-sec">
              <div class="card-body">
                <div class="cat-title">
                  <h4>Includes</h4>
                </div>
                <ul>
                  <li>
                    <img src="assets/img/icon/users.svg" class="me-2" alt />
                    Enrolled: <span>'.$course['TotalStd'].' students</span>
                  </li>';
                  echo '
                  <li>
                    <img src="assets/img/icon/timer.svg" class="me-2" alt />
                    Duration: <span>20 hours</span>
                  </li>';
                  echo '
                  <li>
                    <img src="assets/img/icon/chapter.svg" class="me-2" alt />
                    Lectures: <span>'.$total_lec.'</span>
                  </li>';
                  echo '
                  <li>
                    <img src="assets/img/icon/video.svg" class="me-2" alt />
                    Video:<span> 12 hours</span>
                  </li>';
                  echo'
                  <li>
                    <img src="assets/img/icon/chart.svg" class="me-2" alt />
                    Level: <span>'.get_levels($course['id_level']).'</span>
                  </li>
                </ul>
              </div>
            </div>';
            */
            echo'
          </div>
        </div>';
      }
      echo '
    </div>
  </div>
</section>

<script>
  const curs_share_url_btn = document.getElementById("curs_share_url_btn");
  const curs_share_url = document.getElementById("curs_share_url");
  curs_share_url_btn.addEventListener("click", () => {
    const textToCopy = curs_share_url.textContent || curs_share_url.value;
    const tempInput = document.createElement("textarea");
    tempInput.value = textToCopy;
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // Select the entire content
    try {
        const successful = document.execCommand("copy");
        const msg = successful ? "Copied." : "Not copied.";
        const toastClass = successful ? "bg-success" : "bg-warning";
        Toastify({
            text: msg,
            gravity: "top",
            position: "right",
            className: toastClass,
            duration: 2000,
            close: true,
            stopOnFocus: true,
            offset: { x: 50, y: 0 },
        }).showToast();
    } catch (err) {
        Toastify({
            text: "Not copied.",
            gravity: "top",
            position: "right",
            className: "bg-warning",
            duration: 2000,
            close: true,
            stopOnFocus: true,
            offset: { x: 50, y: 0 },
        }).showToast();
    }
    document.body.removeChild(tempInput); // Remove the temporary input element
  });
</script>';
} else {  
  header("Location: ".SITE_URL.CONTROLER."");	
}
?>