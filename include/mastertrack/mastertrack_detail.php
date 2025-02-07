<?php
$certificate2 = $mastertracks["business-essentials-mastertrack-certificate"];

// MASTER TRACK CERTIFICATE DETAIL
$condition = array ( 
                     'select'   =>	'mt.mas_id, mt.mas_duration, mt.mas_detail, mt.mas_name, mt.mas_shortdetail, mtc.mstcat_name, COUNT(DISTINCT ec.secs_id) as TotalStd, COUNT(DISTINCT mtd.id_curs) TotalCourses, GROUP_CONCAT(DISTINCT mtd.id_curs) TotalCoursesIds, ec2.secs_id as ifEnrolled, w.wl_id as ifWishlist'
                    ,'join'     =>  'INNER JOIN '.MASTER_TRACK_CATEGORIES.' mtc ON mtc.mstcat_id = mt.id_mstcat
                                     LEFT JOIN '.ENROLLED_COURSES.' ec ON FIND_IN_SET(mt.mas_id, ec.id_curs)
                                     LEFT JOIN '.MASTER_TRACK_DETAIL.' mtd ON mtd.id_mas = mt.mas_id
                                     LEFT JOIN '.ENROLLED_COURSES.' ec2 ON ec2.id_mas = mt.mas_id AND ec2.id_ad_prg = 0 AND ec2.id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'"
                                     LEFT JOIN '.WISHLIST.' w ON w.id_mas = mt.mas_id AND w.id_ad_prg IS NULL AND w.id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'" '
                    ,'where' 		=>	array( 
                                             'mt.mas_href'      => $zone
                                            ,'mt.is_deleted'    => '0'  
                                          )
                    ,'return_type'	=>	'single'
                  ); 
$certificate = $dblms->getRows(MASTER_TRACK.' mt', $condition);

// COURSES DETAIL
$condition = array ( 
                       'select'       =>	'curs_name, what_you_learn, curs_about, curs_skills'
                      ,'where'        =>	array( 
                                               'curs_status'    =>  1
                                              ,'is_deleted'     =>  0
                                            )
                      ,'search_by'    =>  ' AND curs_id IN ('.$certificate['TotalCoursesIds'].')'
                      ,'return_type'	=>	'all'
); 
$COURSES = $dblms->getRows(COURSES, $condition);

// INSTRUCTOR ID
$condition = array ( 
                       'select'       =>	'GROUP_CONCAT(id_teacher) teacher_ids'
                      ,'search_by'    =>  ' WHERE id_curs IN ('.$certificate['TotalCoursesIds'].')'
                      ,'return_type'	=>	'single'
                    ); 
$ALLOCATE_TEACHERS = $dblms->getRows(ALLOCATE_TEACHERS, $condition);

// FAQ'S
$condition = array ( 
                       'select'       =>	'question, answer'
                      ,'where'        =>	array( 
                                               'status'       =>  1
                                              ,'is_deleted'   =>  0
                                            )
                      ,'search_by'    =>  ' AND id_curs IN ('.$certificate['TotalCoursesIds'].')'
                      ,'order_by'     =>  ' RAND()'
                      ,'return_type'	=>	'all'
                      ); 
$COURSES_FAQS = $dblms->getRows(COURSES_FAQS, $condition);

// TEACHER DETAIL
if($ALLOCATE_TEACHERS){
  $condition = array ( 
                       'select'       =>	'e.emply_name, e.emply_gender, e.emply_specialsubject, e.emply_email, e.emply_photo, a.adm_photo'
                      ,'join'         =>  'INNER JOIN '.ADMINS.' a ON a.adm_id = e.emply_loginid'
                      ,'where' 		    =>	array( 
                                               'e.is_deleted'     => 0
                                              ,'e.emply_status'   =>  1
                                            )
                      ,'search_by'    =>  ' AND e.emply_id IN ('.$ALLOCATE_TEACHERS['teacher_ids'].')'
                      ,'return_type'	=>	'all'
                    ); 
  $EMPLOYEES = $dblms->getRows(EMPLOYEES.' e', $condition);
}

// MENU
$detailMenu = array(
   'overview'
  // ,'program'
  ,'courses'
  ,'instructors'
  ,'earn-credit'
  ,'faq'
  ,'reviews'
);
echo'
<div class="inner-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="instructor-wrap border-bottom-0 m-0">
          <div class="about-instructor align-items-center">';
            /*  
            echo'
            <div class="abt-instructor-img">
              <a href="#">
                <img src="'.$certificate2['university_logo'].'" alt="img" class="img-fluid"/>
              </a>
            </div>
            <div class="instructor-detail me-3">
              <h5><a href="#">'.$certificate2['university'].'</a></h5>
            </div>';
            */
            echo'
            <div class="rating mb-0">
              <i class="fas fa-star filled"></i>
              <i class="fas fa-star filled"></i>
              <i class="fas fa-star filled"></i>
              <i class="fas fa-star filled"></i>
              <i class="fas fa-star"></i>
              <span class="d-inline-block average-rating"><span>4.5</span> (15)</span>
            </div>
          </div>
          <span class="web-badge mb-3">'.$certificate['mstcat_name'].'</span>
        </div>
        <h2 style="display:inline;">'.$certificate['mas_name'].'</h2>
        <p class="w-75">'.$certificate['mas_shortdetail'].'</p>
        <div class="course-info d-flex align-items-center border-bottom-0 m-0 p-0">
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/icon-01.svg" alt />
            <p>'.$certificate['TotalCourses'].' Courses</p>
          </div>
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/timer-icon.svg" alt />
            <p>'.$certificate['mas_duration'].' Months</p>
          </div>
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/people.svg" alt />
            <p>'.$certificate['TotalStd'].' Students</p>
          </div>
        </div>
      </div>
      <div class="col-lg-12">';
        if(!empty($certificate['ifEnrolled'])){
          echo'<button style="float:right" class="btn btn-enroll"><i class="fa fa-check"></i> Enrolled</button>';
        }else{
          echo'
          <form action="'.SITE_URL.'invoice" method="POST">
            <input type="hidden" name="url" value="'.CONTROLER.'/'.ZONE.'">
            <input type="hidden" name="id" value="'.$certificate['mas_id'].'">
            <input type="hidden" name="type" value="2">
            <button name="enroll" style="float:right" type="submit" class="btn btn-enroll">Apply Now</button>
          </form>';
        }
        echo'
        <div data-id="'.$certificate['mas_id'].'">';
          if (isset($certificate['ifWishlist']) && !empty($certificate['ifWishlist'])){
            echo '<a class="btn btn-wish mx-1" style="float: right;" href="javascript:;" onclick="remove_from_wishlist('.$certificate['ifWishlist'].','.$certificate['mas_id'].',2,2)"><i class="fa-solid fa-heart"></i> Remove Wishlist</a>';
          } else {
            echo '<a class="btn btn-wish mx-1" style="float: right;" href="javascript:;" onclick="add_to_wishlist('.$certificate['mas_id'].',2,2)"><i class="fa-regular fa-heart"></i> Add to Wishlist</a>';
          }
          echo'
        </div>
      </div>
    </div>
  </div>
</div>
<section class="page-content course-sec"> 
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="featured-courses-five-tab">
            <div class="tab-content">
              <div class="nav tablist-five" style="text-align: left !important;" role="tablist">';
                foreach ($detailMenu as $key => $menu) {
                  echo'<a class="nav-tab '.($key == 0 ? 'active' : '').'" data-bs-toggle="tab" href="#'.$menu.'" role="tab" >'.($menu == 'faq' ? 'FAQ\'s' : moduleName($menu)).'</a>';
                }
                echo'
              </div>
              <div class="tab-content">
                <div class="tab-pane fade show active" id="overview">
                  <div class="card overview-sec">
                    <div class="card-body">';
                      if(!empty($certificate['mas_detail'])){
                        echo html_entity_decode(html_entity_decode($certificate['mas_detail']));
                      }
                      echo'
                    </div>
                  </div>
                </div>';
                /* 
                echo'
                <div class="tab-pane fade show " id="program">
                  <div class="card overview-sec">
                    <div class="card-body">
                    '.$certificate2['description']['program'].'
                    </div>
                  </div>
                </div>';
                */
                echo'
                <div class="tab-pane fade show" id="courses">
                  <div class="card overview-sec">
                    <div class="card-body">
                      <h5 class="subs-title text-center">'.$certificate['TotalCourses'].' courses in this '.$certificate['mas_duration'].' month program</h5>';
                      if($COURSES){
                        $srno = 0;
                        foreach ($COURSES as $course) {
                          $srno++;
                          echo'
                          <h6>Course '.$srno.' of '.$certificate['TotalCourses'].'</h6>
                          <h5 class="subs-title">'.$course['curs_name'].'</h5>';

                          if(!empty($course['what_you_learn'])){
                            $what_you_learn = json_decode(html_entity_decode($course['what_you_learn']), true);
                            $srLearn = 0;
                            if($what_you_learn){
                              foreach ($what_you_learn as $value) {
                                if(!empty($value)){
                                  $srLearn++;
                                  if($srLearn == 1){
                                    echo'
                                    <h6>WHAT YOU WILL LEARN</h6>
                                    <div class="row"><div class="col"><ul>';
                                  }
                                  echo '<li>'.$value.'</li>';
                                  $srLearn = 1;
                                }
                              }
                              if($srLearn == 1){
                                echo '</ul></div></div>';
                              }
                            }
                          }
                          if($course['curs_skills']){
                            echo'
                            <h6>SKILLS YOU WILL GAIN</h6>
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
                          if(!empty($course['curs_about'])){
                            echo'
                            <h6>Overview</h6>
                            <p>'.html_entity_decode(html_entity_decode($course['curs_about'])).'</p>';
                          }
                          echo'<hr>';
                        }
                      }
                      echo'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="instructors">
                  <div class="card overview-sec">
                    <div class="card-body">
                      <h5 class="subs-title text-center">List of Teachers in the relavent certification</h5>
                      <div class="row">';                        
                        if($EMPLOYEES){
                          foreach ($EMPLOYEES as $rowTeacher) {
                            // CHECK FILE EXIST
                            if($rowTeacher['emply_gender'] == '2'){
                              $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
                            }else{            
                              $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
                            }
                            if(!empty($rowTeacher['adm_photo'])){
                              $file_url = SITE_URL_PORTAL.'uploads/images/admin/'.$rowTeacher['adm_photo'];
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
                                      <h5>'.$rowTeacher['emply_name'].'</h5>
                                      <p>'.$rowTeacher['emply_specialsubject'].'</p>
                                      <p>'.$rowTeacher['emply_email'].'</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>';
                          }
                        }else{
                          echo '<h5>No Teachers Allocated...!</h5>';
                        }
                        echo'
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="earn-credit">
                  <div class="card overview-sec">
                    <div class="card-body">
                      '.$certificate2['description']['earn-credit'].'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="faq">
                  <div class="card overview-sec">
                    <div class="card-body">
                      <h5 class="subs-title text-center">Frequently asked questions</h5>';
                      if($COURSES_FAQS){
                        foreach ($COURSES_FAQS as $key => $faq) {
                        echo'
                        <div class="course-card">
                          <h6 class="cou-title">
                            <a class="collapsed" data-bs-toggle="collapse" href="#faq'.$key.'" aria-expanded="'.($key == 0 ? 'true' : 'false').'">'.$faq['question'].'</a>
                          </h6>
                          <div id="faq'.$key.'" class="card-collapse collapse px-3 '.($key == 0 ? 'show' : '').'">
                            '.html_entity_decode(html_entity_decode($faq['answer'])).'
                          </div>
                        </div>';
                        }
                      }else{
                        echo '<h5>No Faq\'s Found!</h5>';
                      }
                      echo'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="reviews">
                  <div class="card overview-sec">
                    <div class="card-body">
                      '.$certificate2['description']['reviews'].'
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
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
        </div>
      </div>
    </div>
  </div>
</section>';
?>