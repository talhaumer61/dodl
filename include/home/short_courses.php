<?php
$condition = array ( 
                    'select' 		    =>	'c.curs_code, c.curs_type_status, cc.cat_name, c.curs_href, c.curs_id, c.curs_name, c.curs_icon, c.curs_photo, c.curs_href, c.curs_rating, c.duration, c.curs_hours, c.curs_wise, a.id_type, COUNT(DISTINCT ec.secs_id) as TotalStd, COUNT(DISTINCT l.lesson_id) TotalLesson'
                    ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = c.curs_id AND a.admoff_type = 4
                                         LEFT JOIN '.ENROLLED_COURSES.' ec ON FIND_IN_SET(c.curs_id, ec.id_curs)
                                         LEFT JOIN '.COURSES_LESSONS.' l ON l.id_curs = c.curs_id AND l.is_deleted = 0 AND l.lesson_status = 1
                                         INNER JOIN '.COURSES_CATEGORIES.' cc ON cc.cat_id = c.id_cat AND cc.id_type = 2'
                    ,'where' 		    =>	array( 
                                               'c.curs_status' 	    => '1' 
                                              ,'c.is_deleted' 	    => '0'
                                            )
                    ,'group_by'     =>  'c.curs_id'
                    ,'order_by'     =>  ' RAND() '
                    ,'limit'        =>  '4'
                    ,'return_type'	=>	'all'
                  );
$COURSES = $dblms->getRows(COURSES.' c', $condition);
echo'
<section class="home-two trending-course-sec pt-5 pb-5">
  <div class="container">
    <div class="header-two-title text-center" data-aos="fade-up">
      <!--<p class="tagline">New Courses</p>-->
      <h2>Trainings</h2>
      <div class="header-two-text m-auto text-center d-block">
        <p class="mb-0">Trainings provide concise, focused learning on specific topics, offering quick skill enhancement and flexibility for busy individuals.</p>
      </div>
    </div>
    <div class="trending-course-main">
      <div class="row">';
        if($COURSES){
          foreach ($COURSES as $key => $course) {
            $curs_rating = (($course['curs_rating'] == '0' || $course['curs_rating'] == '') ? '4' : $course['curs_rating']);

            // CHECK FILE EXIST
            $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
            $file_url = SITE_URL_PORTAL.'uploads/images/courses/'.$course['curs_photo'];
            if (check_file_exists($file_url)) {
              $photo = $file_url;
            }

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
            
            echo'
            <div class="col-xl-6 col-lg-12 col-md-12" data-aos="fade-down">
              <div class="trending-courses-group">
                <div class="trending-courses-two">
                  <div class="product-img course-column-img">
                    <a '.($course['curs_type_status'] == 1 ? '' : 'href="'.SITE_URL.'trainings/'.$course['curs_href'].'"').'>
                      <img class="img-fluid" alt src="'.$photo.'"/>
                    </a>';
                    if (!empty($DISCOUNT['discount_id'])) {
                      echo'  
                      <div class="trending-price blink-image">
                        <h4 class="m-0">
                          <span>'.$DISCOUNT['discount'].'% OFF</span>
                        </h4>
                      </div>';
                    }
                    echo'
                  </div>
                  <div class="course-content-column">
                    <div class="name-text featured-info-two">
                      <h3 class="title instructor-text">
                        <a '.($course['curs_type_status'] == 1 ? '' : 'href="'.SITE_URL.'trainings/'.$course['curs_href'].'"').'>'.$course['curs_name'].'</a>
                      </h3>
                      <div class="d-flex align-items-center">
                        <div class="rating-price-two d-flex align-items-center">
                          <p><i class="fa-regular fa-clock me-2"></i> '.$course['curs_hours'].' Hour'.($course['curs_hours'] > 1 ? 's' : '').'</p>
                        </div>
                        <div class="course-view d-inline-flex align-items-center">
                          <div class="course-price-two">
                            <h3>'.get_LeanerType($course['id_type']).'</h3>
                          </div>
                        </div>
                      </div>
                    </div>';
                    /*
                    if($course['curs_type_status'] != '1'){
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
                            <h3>'.($course['TotalStd'] + 100).' Students</h3>
                          </div>
                        </div>
                      </div>';
                    }
                    */
                    echo'
                  </div>
                </div>
              </div>
            </div>';
          }
        }
        echo'
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12" data-aos="fade-up">
        <div class="more-details text-center">
          <a href="'.SITE_URL.'trainings" class="discover-btn">View all Trainings <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>';
?>