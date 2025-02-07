<?php 
$condition = array ( 
                         'select'       =>  "e.emply_id,e.emply_photo,e.emply_name,d.designation_name"
                        ,'join'         =>  'INNER JOIN '.DESIGNATIONS.' d ON d.designation_id = e.id_designation'
                        ,'where' 	      =>  array ( 
                                                     'e.is_deleted'     => 0
                                                    ,'e.emply_status'   => 1
                                            )
                        ,'order_by'   	=> 'RAND() ASC LIMIT 6' 
                        ,'return_type'  =>  'all'
                    ); 
$EMPLOYEES = $dblms->getRows(EMPLOYEES.' e', $condition);
$values	=	array ( 
                   'select'		      => "a.curs_name,a.curs_href,a.curs_photo"
                  ,'join'           => 'INNER JOIN '.ADMISSION_OFFERING.' AS ao ON ao.admoff_degree	= a.curs_id'
                  ,'where'		      => array( 
                                         'a.is_deleted'		  => 0
                                        ,'ao.admoff_type'		=> 1
                                    )
                  ,'order_by'   	=> 'RAND() DESC LIMIT 6' 
                  ,'return_type'	  => 'all' 
); 
$COURSES = $dblms->getRows(COURSES.' AS a', $values);
echo'
<section class="section trend-course">
  <div class="container">';
    if ($COURSES) {
      echo'
      <div class="section-header aos" data-aos="fade-up">
        <div class="section-sub-head">
          <span>What’s New</span>
          <h2>Master Track Certificates</h2>
        </div>
        <div class="all-btn all-category d-flex align-items-center">
          <a href="'.SITE_URL.'master-track-certificates" class="btn btn-primary">All Certificates</a>
        </div>
      </div>
      <div class="section-text aos" data-aos="fade-up">
        <p class="mb-0"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Eget aenean accumsan bibendum gravida maecenas augue elementum et neque. Suspendisse imperdiet.</p>
      </div>
      <div class="owl-carousel trending-course owl-theme aos" data-aos="fade-up">';
        foreach ($COURSES as $key => $courses){
          echo'
          <div class="course-box trend-box">
            <div class="product trend-product">
              <div class="product-img">
                <a href="'.SITE_URL.'master-track-detail/'.$courses['curs_href'].'">
                  <img class="img-fluid" alt src="'.SITE_URL_PORTAL.'uploads/images/admissions/master_track/photo/'.$courses['curs_photo'].'"/>
                </a>
              </div>
              <div class="product-content">
                <div class="course-group d-flex">
                  <div class="course-group-img d-flex">
                    <a href="'.SITE_URL.'master-track-detail/'.$courses['curs_href'].'">
                      <img src="'.SITE_URL.'uploads/images/university/logo/university4.png" alt class="img-fluid"/>
                    </a>
                    <div class="course-name">
                      <h4>
                        <a href="#">IE Business School</a>
                      </h4>
                      <p>University</p>
                    </div>
                  </div>
                  <div class="course-share d-flex align-items-center justify-content-center">
                    <a href="'.SITE_URL.'master-track-detail/'.$courses['curs_href'].'"><i class="fa-regular fa-heart"></i></a>
                  </div>
                </div>
                <h3 class="title">
                  <a href="'.SITE_URL.'master-track-detail/'.$courses['curs_href'].'">'.$courses['curs_name'].'</a>
                </h3>
                <div class="course-info d-flex align-items-center">
                  <div class="rating-img d-flex align-items-center">
                    <img src="assets/img/icon/icon-01.svg" alt class="img-fluid"/>
                    <p>4 courses</p>
                  </div>
                  <div class="course-view d-flex align-items-center">
                    <img src="assets/img/icon/icon-02.svg" alt class="img-fluid"/>
                    <p>4 courses</p>
                  </div>
                </div>
                <div class="rating">
                  <i class="fas fa-star filled"></i>
                  <i class="fas fa-star filled"></i>
                  <i class="fas fa-star filled"></i>
                  <i class="fas fa-star filled"></i>
                  <i class="fas fa-star"></i>
                  <span class="d-inline-block average-rating"><span>4.0</span> (15)</span>
                </div>
                <div class="all-btn all-category d-flex align-items-center">
                  <a href="'.SITE_URL.'master-track-detail/'.$courses['curs_href'].'" class="btn btn-primary">BUY NOW</a>
                </div>
              </div>
            </div>
          </div>';
        }
        echo'
      </div>';
    }
    if ($EMPLOYEES) {
      echo'
      <div class="feature-instructors">
        <div class="section-header aos" data-aos="fade-up">
          <div class="section-sub-head feature-head text-center">
            <h2>Featured Instructor</h2>
            <div class="section-text aos" data-aos="fade-up">
              <p class="mb-0"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Eget aenean accumsan bibendum gravida maecenas augue elementum et neque. Suspendisse imperdiet.</p>
            </div>
          </div>
        </div>
        <div class="owl-carousel instructors-course owl-theme aos" data-aos="fade-up">';
          foreach ($EMPLOYEES as $key => $value) {
            // CHECK FILE EXIST
            $photo      = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
            if(!empty($value['emply_photo'])){
              $file_url   = SITE_URL_PORTAL.'uploads/images/employees/'.$value['emply_photo'];
              if (check_file_exists($file_url)) {
                $photo = $file_url;
              }
            }
            echo'
            <div class="instructors-widget">
              <div class="instructors-img">
                <a href="#">
                  <img class="img-fluid" alt src="'.$photo.'"/>
                </a>
              </div>
              <div class="instructors-content text-center">
                <h5><a href="#">'.$value['emply_name'].'</a></h5>
                <p>'.$value['designation_name'].'</p>
                <div class="student-count d-flex justify-content-center">
                  <i class="fa-solid fa-user-group"></i>
                  <span>100+</span>
                </div>
              </div>
            </div>';
          }
          echo'
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="more-details text-center" data-aos="fade-down">
              <a href="'.SITE_URL.'instructors" class="discover-btn">View all <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
          </div>
        </div>
      </div>';
    }
    echo'
  </div>
</section>';
?>