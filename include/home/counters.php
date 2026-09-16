<?php
// COUNT COURSES
$condition = array ( 
                       'select' 		    =>	'admoff_id'
                      ,'where' 		      =>	array( 
                                                   'admoff_status' 	    => '1' 
                                                  ,'admoff_type' 	      => '3' 
                                                  ,'is_deleted' 	      => '0' 
                                                )
                      ,'return_type'	  =>	'count'
                    ); 
$COUNT_AD_OFF = $dblms->getRows(ADMISSION_OFFERING, $condition);

// COUNT COURSES CATEGORIES
$condition = array ( 
                       'select' 		    =>	'cat_id'
                      ,'where' 		      =>	array( 
                                                   'cat_status'   => '1'
                                                  ,'is_deleted'   => '0' 
                                                )
                      ,'return_type'	  =>	'count'
                    ); 
$COUNT_CURS_CAT = $dblms->getRows(COURSES_CATEGORIES, $condition);

// COUNT CERTIFICATE & PROGRAM
$condition = array ( 
                       'select' 		    =>	'admoff_id'
                      ,'where' 		      =>	array( 
                                                   'admoff_status' 	    => '1'
                                                  ,'is_deleted' 	      => '0' 
                                                )
                      ,'not_equal'      =>  array(
                                                  'admoff_type' 	      => '3' 
                                                )
                      ,'return_type'	  =>	'count'
                    ); 
$COUNT_AD_PRG = $dblms->getRows(ADMISSION_OFFERING, $condition);

// COUNT TUTORS
$condition = array ( 
                       'select' 		    =>	'emply_id'
                      ,'where' 		      =>	array( 
                                                   'emply_status' 	    => '1'
                                                  ,'is_deleted' 	      => '0' 
                                                )
                      ,'return_type'	  =>	'count'
                    ); 
$COUNT_EMPLY = $dblms->getRows(EMPLOYEES, $condition);

// COUNT STUDENTS
$condition = array ( 
                       'select' 		    =>	'std_id'
                      ,'where' 		      =>	array( 
                                                   'std_status'   => '1'
                                                  ,'is_deleted'   => '0' 
                                                )
                      ,'return_type'	  =>	'count'
                    ); 
$COUNT_STD = $dblms->getRows(STUDENTS, $condition);

// COUNT COUNTRIES
$condition = array ( 
                       'select' 		    =>	'country_id'
                      ,'where' 		      =>	array( 
                                                   'country_status'   => '1'
                                                  ,'is_deleted'       => '0' 
                                                )
                      ,'return_type'	  =>	'count'
                    ); 
$COUNT_COUNTRIES = $dblms->getRows(COUNTRIES, $condition);

echo'
<section class="section student-course">
  <div class="container">
    <div class="course-widget">
      <div class="row">
        <div class="col-lg-3 col-md-6">
          <div class="course-full-width">
            <div class="blur-border course-radius align-items-center aos" data-aos="fade-up">
              <div class="online-course d-flex align-items-center">
                <div class="course-img">
                  <img src="'.SITE_URL.'assets/img/pencil-icon.png" alt />
                </div>
                <div class="course-inner-content">
                  <h4>'.$COUNT_AD_OFF.'</h4>
                  <p>Certificate Courses</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="course-full-width">
            <div class="blur-border course-radius align-items-center aos" data-aos="fade-up">
              <div class="online-course d-flex align-items-center">
                <div class="course-img">
                  <img src="'.SITE_URL.'assets/img/courses-icon.png" alt />
                </div>
                <div class="course-inner-content">
                  <h4>'.$COUNT_EMPLY.'</h4>
                  <p>Expert Tutors</p>
                </div>
              </div>
            </div>
          </div>
        </div>        
        <div class="col-lg-3 col-md-6">
          <div class="course-full-width">
            <div class="blur-border course-radius align-items-center aos" data-aos="fade-up">
              <div class="online-course d-flex align-items-center">
                <div class="course-img">
                  <img src="'.SITE_URL.'assets/img/certificate-icon.png" alt />
                </div>
                <div class="course-inner-content">
                  <h4>'.$COUNT_CURS_CAT.'</h4>
                  <p>Course Categories</p>
                </div>
              </div>
            </div>
          </div>
        </div>        
        <div class="col-lg-3 col-md-6">
          <div class="course-full-width">
            <div class="blur-border course-radius align-items-center aos" data-aos="fade-up">
              <div class="online-course d-flex align-items-center">
                <div class="course-img">
                  <img src="'.SITE_URL.'assets/img/graduate-icon.png" alt />
                </div>
                <div class="course-inner-content">
                  <h4>'.($COUNT_STD+1000).'</h4>
                  <p>Learners</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>';
?>