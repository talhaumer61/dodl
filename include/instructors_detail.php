<?php
if(!empty(ZONE)){
  // EMPLOYEE DETAIL
  $condition = array ( 
                          'select'       =>  'e.emply_gender, e.emply_id,e.emply_loginid,e.emply_photo,e.emply_name,d.designation_name, e.emply_permanent_address, e.	emply_phone, e.	emply_email'
                          ,'join'         =>  'LEFT JOIN '.DESIGNATIONS.' d ON d.designation_id = e.id_designation'
                          ,'where' 	      =>  array ( 
                                                      'e.is_deleted'     => 0
                                                      ,'e.emply_status'   => 1
                                                      ,'e.emply_id'       => cleanvars(ZONE)
                                              )
                          ,'return_type'  =>  'single'
                      ); 
  $EMPLOYEES = $dblms->getRows(EMPLOYEES.' e', $condition);
  if($EMPLOYEES){
    // ALLOCATED COURSES
    $condition = array ( 
                             'select'       =>  'c.curs_id, c.curs_name, c.curs_wise, c.duration, c.curs_icon, c.curs_rating, c.curs_photo, cc.cat_name, c.curs_href,c.best_seller, c.curs_type_status
                             ,COUNT(secs_id) as TotalStd'
                            ,'join'         =>  'INNER JOIN '.ALLOCATE_TEACHERS.' ct ON c.curs_id = ct.id_curs AND FIND_IN_SET('.$EMPLOYEES['emply_id'].', ct.id_teacher)
                                                 INNER JOIN '.COURSES_CATEGORIES.' cc ON c.id_cat=cc.cat_id
                                                 LEFT JOIN '.ENROLLED_COURSES.' ec ON FIND_IN_SET(c.curs_id, ec.id_curs)'
                            ,'where' 	      =>  array ( 
                                                         'c.is_deleted'     => 0
                                                        ,'c.curs_status'    => 1
                                                      )
                            ,'group_by'     =>  'c.curs_id'
                            ,'return_type'  =>  'all'
                        ); 
    $COURSES = $dblms->getRows(COURSES.' c', $condition, $sql);

    // EMPLOYEE EDUCATION
    $condition = array ( 
                          'select'         =>  'program, subjects, institute, year'
                          ,'where' 	        =>  array ( 
                                                        'is_deleted'       => 0
                                                      ,'status'            => 1
                                                      ,'id_employee'       => $EMPLOYEES['emply_id']
                                                    )
                          ,'return_type'    =>  'all'
                      ); 
    $emp_education = $dblms->getRows(EMPLOYEE_EDUCATIONS, $condition);

    // EMPLOYEE EXPERIENCE
    $condition = array ( 
                        'select'         =>  'jobfield, organization, designation, date_start, date_end'
                        ,'where' 	        =>  array ( 
                                                      'is_deleted'   => 0
                                                      ,'status'       => 1
                                                      ,'id_employee'  => $EMPLOYEES['emply_id']
                                                  )
                        ,'return_type'    =>  'all'
                      ); 
    $emp_experience = $dblms->getRows(EMPLOYEE_EXPERIENCE, $condition);

    // EMPLOYEE, LANGUAGES
    $condition = array ( 
                          'select'        =>  'language_name'
                        ,'where' 	        =>  array ( 
                                                      'is_deleted'       => 0
                                                      ,'status'            => 1
                                                      ,'id_employee'       => $EMPLOYEES['emply_id']
                                                    )
                        ,'return_type'    =>  'all'
    ); 
    $emp_language = $dblms->getRows(EMPLOYEE_LANGUAGE_SKILLS, $condition);

    // EMPLOYEE TRAININGS
    $condition = array ( 
                          'select'       =>  'course'
                          ,'where'        =>  array ( 
                                                      'is_deleted'     => 0
                                                      ,'status'         => 1
                                                      ,'id_employee'    => $EMPLOYEES['emply_id']
                                                    )
                          ,'return_type'  =>  'all'
                        ); 
    $emp_skills = $dblms->getRows(EMPLOYEE_TRAININGS, $condition);

    $total_students = 0;
    $found = 0;
    foreach ($COURSES as $key => $value) {
      if($value['curs_type_status'] != 1){
        $total_students += $value['TotalStd'];

        $found++;
        if($found == 1){
          $total_students += 100;
        }
      }
    }

    // CHECK FILE EXIST
    if($EMPLOYEES['emply_gender'] == 'Female'){
      $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
    }else{            
      $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
    }
    if(!empty($EMPLOYEES['emply_photo'])){
      $file_url   = SITE_URL_PORTAL.'uploads/images/employees/'.$EMPLOYEES['emply_photo'];
      if (check_file_exists($file_url)) {
        $photo = $file_url;
      }
    }
    echo'
    <div class="page-banner instructor-bg-blk">
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-12">
            <div class="profile-info-blk">
              <a class="profile-info-img">
                <img src="'.$photo.'" alt class="img-fluid" />
              </a>
              <h3 class="text-light">'.$EMPLOYEES['emply_name'].'</h3>
              <p>'.$EMPLOYEES['designation_name'].'</p>';
              /*
              echo'
              <ul class="list-unstyled inline-inline profile-info-social">
                <li class="list-inline-item">
                  <a href="javascript:;">
                    <i class="fa-brands fa-facebook"></i>
                  </a>
                </li>
                <li class="list-inline-item">
                  <a href="javascript:;">
                    <i class="fa-brands fa-twitter"></i>
                  </a>
                </li>
                <li class="list-inline-item">
                  <a href="javascript:;">
                    <i class="fa-brands fa-instagram"></i>
                  </a>
                </li>
                <li class="list-inline-item">
                  <a href="javascript:;">
                    <i class="fa-brands fa-linkedin"></i>
                  </a>
                </li>
              </ul>';
              */
              echo'
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="page-content pb-0">
      <div class="container">
        <div class="row">
          <div class="col-xl-12 col-md-12">
            <div class="settings-top-widget student-deposit-blk">
              <div class="row">
                <div class="col-lg-3 col-md-6">
                  <div class="card stat-info ttl-tickets">
                    <div class="card-body">
                      <div class="view-all-grp d-flex">
                        <div class="student-ticket-view">
                          <h3>'.sizeof($COURSES).'</h3>
                          <p>Courses</p>
                        </div>
                        <div class="img-deposit-ticket">
                          <img src="'.SITE_URL.'assets/img/students/book.svg" alt />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="card stat-info open-tickets">
                    <div class="card-body">
                      <div class="view-all-grp d-flex">
                        <div class="student-ticket-view">
                          <h3>1</h3>
                          <p>Experience</p>
                        </div>
                        <div class="img-deposit-ticket">
                          <img src="'.SITE_URL.'assets/img/students/receipt-text.svg" alt />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="card stat-info close-tickets">
                    <div class="card-body">
                      <div class="view-all-grp d-flex">
                        <div class="student-ticket-view">
                          <h3>'.$total_students.'</h3>
                          <p>Students</p>
                        </div>
                        <div class="img-deposit-ticket">
                          <img src="'.SITE_URL.'assets/img/students/profile-user.svg" alt />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="card stat-info open-tickets">
                    <div class="card-body">
                      <div class="view-all-grp d-flex">
                        <div class="student-ticket-view">
                          <h3>4.8</h3>
                          <p>Rating</p>
                        </div>
                        <div class="img-deposit-ticket">
                          <img src="'.SITE_URL.'assets/img/students/empty-wallet-tick.svg" alt />
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
                  <div class="row">
                    <h5 class="subs-title text-left">Courses</h5>';
                    if($COURSES){
                      foreach ($COURSES as $key => $value) {
                        $curs_rating = (($value['curs_rating'] == '0' || $value['curs_rating'] == '') ? '4' : $value['curs_rating']);

                        $curs_photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                        if(!empty($value['curs_photo'])){
                          $file_url   = SITE_URL_PORTAL.'uploads/images/courses/'.$value['curs_photo'];
                          if (check_file_exists($file_url)) {
                            $curs_photo = $file_url;
                          }
                        }
                        echo'
                        <div class="col-xl-6 col-lg-12 col-md-12" data-aos="fade-down">
                          <div class="trending-courses-group">
                            <div class="trending-courses-two">
                              <div class="product-img course-column-img">
                                <a '.($value['curs_type_status'] == '1' ? '' : 'href="'.SITE_URL.'courses/'.$value['curs_href'].'"').'>
                                  <img class="img-fluid" alt src="'.$curs_photo.'"/>
                                </a>
                                <div class="trending-price-1">
                                  '.get_curs_status($value['curs_type_status']).'
                                </div>
                              </div>
                              <div class="course-content-column">
                                <div class="name-text featured-info-two">
                                  <h3 class="title instructor-text">
                                    <a '.($value['curs_type_status'] == '1' ? '' : 'href="'.SITE_URL.'courses/'.$value['curs_href'].'"').'>'.$value['curs_name'].'</a>
                                  </h3>
                                  <div class="d-flex align-items-center">
                                    <div class="rating-price-two d-flex align-items-center">
                                      <p><i class="fa-regular fa-clock me-2"></i> '.$value['duration'].' '.get_CourseWise($value['curs_wise']).($value['duration'] > 1 ? 's' : '').'</p>
                                    </div>
                                  </div>
                                </div>';
                                if($value['curs_type_status'] != '1'){
                                  echo'
                                  <div class="d-flex align-items-center">
                                    <div class="rating-price-two d-flex align-items-center">
                                      <div class="rating">';
                                        for ($rate=1; $rate <= 5; $rate++) {
                                          echo'<i class="fas fa-star '.($rate <= $curs_rating ? 'filled' : '').'"></i>';
                                        }
                                        echo'
                                      </div>
                                    </div>
                                    <div class="course-view d-inline-flex align-items-center">
                                      <div class="course-price-two">
                                        <h3>'.($value['TotalStd'] + 100).' Students</h3>
                                      </div>
                                    </div>
                                  </div>';
                                }
                                echo'
                              </div>
                            </div>
                          </div>
                        </div>';
                      }
                    }else{
                      echo'<h6 class="text-danger">No Courses Found...!</h6>';
                    }
                    echo'
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="page-content course-sec p-0">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">';
            /*
            echo'
            <div class="card overview-sec">
                <div class="card-body">
                    <h5 class="subs-title">About Me</h5>
                    <p class="mb-0">'.nl2br($row['std_about']).'</p>
                </div>
            </div>';
            */
            echo'
            <div class="card education-sec">
              <div class="card-body">
                <h5 class="subs-title">Education</h5>';
                if($emp_education){
                  foreach($emp_education as $key => $value){
                    echo'
                    <div class="edu-wrap mb-3">
                      <div class="edu-name"><span><i class="fa fa-graduation-cap"></i></span></div>
                      <div class="edu-detail">
                        <h6>'.$value['subjects'].' - '.$value['program'].'</h6>
                        <p class="edu-duration">'.html_entity_decode($value['institute']).(($value['year'])?" (".$value['year'].")":"").'</p>
                      </div>
                    </div>';
                  }  
                }else{
                  echo'<h6 class="text-danger">No Education History Found...!</h6>';
                }
                echo'
              </div>
            </div>
            <div class="card education-sec">
              <div class="card-body">
                <h5 class="subs-title">Experience</h5>';
                if($emp_experience){
                  foreach($emp_experience as $key => $value){
                    echo'
                    <div class="edu-wrap px-2">
                      <div class="edu-name"><span><i class="fas fa-bookmark"></i></span></div>
                      <div class="edu-detail">
                        <h6>'.$value['designation'].'</h6>
                        <p class="edu-duration">'.$value['organization'].(($value['date_start'])?" (".date('M/Y',strtotime($value['date_start']))."-".date('M/Y',strtotime($value['date_end'])).")":"").'</p>
                      </div>
                    </div>';
                  }
                }else{
                  echo'<h6 class="text-danger">No Experience Record Found...!</h6>';
                }
                echo'
              </div>
            </div>';
            /*
            echo'
            <div class="card review-sec">
              <div class="card-body">
                <h5 class="subs-title">Reviews</h5>
                <div class="review-item">
                  <div class="instructor-wrap border-0 m-0">
                    <div class="about-instructor">
                      <div class="abt-instructor-img">
                        <img src="'.SITE_URL.'assets/img/user/user1.jpg" alt="img" class="img-fluid" />
                      </div>
                      <div class="instructor-detail">
                        <h5>Nicole Brown</h5>
                        <p>UX/UI Designer</p>
                      </div>
                    </div>
                    <div class="rating">
                      <i class="fas fa-star filled"></i>
                      <i class="fas fa-star filled"></i>
                      <i class="fas fa-star filled"></i>
                      <i class="fas fa-star filled"></i>
                      <i class="fas fa-star"></i>
                    </div>
                  </div>
                  <p class="rev-info"> “ This is the second Photoshop course I have completed with Cristian. Worth every penny and recommend it highly. To get the most out of this course, its best to to take the Beginner to Advanced course first. The sound and video quality is of a good standard. Thank you Cristian. “</p>
                  <a href="javascript:void(0);" class="btn btn-reply"><i class="feather-corner-up-left"></i> Reply</a>
                </div>
              </div>
            </div>';
            */
            echo'                
          </div>
          <div class="col-lg-4">
            <div class="card overview-sec">
              <div class="card-body overview-sec-body">
                <h5 class="subs-title">Professional Skills</h5>
                <div class="sidebar-tag-labels">
                  <ul class="list-unstyled">';
                    if($emp_skills){
                      foreach ($emp_skills as $key => $value) {
                        echo '<li><a class>'.$value['course'].'</a></li>';
                      }
                    }else{
                      echo'<h6 class="text-danger">No Skills Found...!</h6>';
                    }
                    echo'
                  </ul>
                </div>
              </div>
            </div>
            <div class="card overview-sec">
              <div class="card-body overview-sec-body">
                <h5 class="subs-title">Language Skills</h5>
                <div class="sidebar-tag-labels">
                  <ul class="list-unstyled">';
                    if($emp_language){
                      foreach ($emp_language as $key => $value) {
                        echo '<li><a class>'.$value['language_name'].'</a></li>';
                      }
                    }else{
                      echo'<h6 class="text-danger">No Languages Found...!</h6>';
                    }                
                    echo'
                  </ul>
                </div>
              </div>
            </div>
            <div class="card overview-sec">
              <div class="card-body">
                <h5 class="subs-title">Contact Details</h5>
                <div class="contact-info-list">
                  <div class="edu-wrap">
                    <div class="edu-name">
                      <span><img src="'.SITE_URL.'assets/img/instructor/email-icon.png" alt="Address"/></span>
                    </div>
                    <div class="edu-detail">
                      <h6>Email</h6>
                      <p>
                        <a href="javascript:;">
                          <span>'.$EMPLOYEES['emply_email'].'</span >
                        </a>
                      </p>
                    </div>
                  </div>';
                  /*
                  echo'
                  <div class="edu-wrap">
                    <div class="edu-name">
                      <span><img src="'.SITE_URL.'assets/img/instructor/phone-icon.png" alt="Address"/></span>
                    </div>
                    <div class="edu-detail">
                      <h6>Phone</h6>
                      <p><a href="javascript:;">'.$EMPLOYEES['emply_phone'].'</a></p>
                    </div>
                  </div>';
                  */
                  echo'
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>';
  } else {    
    header("location: ".SITE_URL.CONTROLER."");
  }
}
?>