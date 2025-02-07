<?php 
$condition = array ( 
                    'select'        =>  "blog_href, blog_name, blog_tags, blog_date, blog_photo"
                    ,'where' 	    =>  array( 
                                               'blog_status'    => 1
                                              ,'is_deleted'    => 0
                                            )
                    ,'order_by'     =>  ' blog_id ASC'
                    ,'return_type'  =>  'all' 
                   ); 
$BLOGS = $dblms->getRows(BLOGS, $condition);
echo'
<section class="section latest-blog">
  <div class="container">
    <div class="section-header aos" data-aos="fade-up">
      <div class="section-sub-head feature-head text-center mb-0">
        <h2>Latest Blogs</h2>
        <div class="section-text aos" data-aos="fade-up">
          <p class="mb-0">
            Uncover thought-provoking content and up-to-date information in our most recent blogs.
          </p>
        </div>
      </div>
    </div>
    <div class="owl-carousel blogs-slide owl-theme aos" data-aos="fade-up">';
      foreach ($BLOGS as $key => $blogValue) {
        echo'
        <div class="instructors-widget blog-widget">
          <div class="instructors-img">
            <a href="'.SITE_URL.'blogs/'.$blogValue['blog_href'].'">
              <img class="img-fluid" alt src="'.SITE_URL_PORTAL.'uploads/images/blogs/'.$blogValue['blog_photo'].'"/>
            </a>
          </div>
          <div class="instructors-content text-center">
            <h6 class="line-clamp-2">
              <a href="'.SITE_URL.'blogs/'.$blogValue['blog_href'].'">'.$blogValue['blog_name'].'</a>
            </h6>
            <div class="student-count d-flex justify-content-center">
              <i class="fa-solid fa-calendar-days"></i>
              <span>'.date('Y-m-d', strtotime($blogValue['blog_date'])).'</span>
            </div>
          </div>
        </div>';
      }
      echo'
    </div>';
    /*
    echo'
    <div class="enroll-group aos" data-aos="fade-up">
      <div class="row">
        <div class="col-lg-4 col-md-6">
          <div class="total-course d-flex align-items-center">
            <div class="blur-border">
              <div class="enroll-img">
                <img src="'.SITE_URL.'assets/img/icon-07.png" alt class="img-fluid"/>
              </div>
            </div>
            <div class="course-count">
              <h3><span class="counterUp">'.($COUNT_STD+1000).'</span></h3>
              <p>LEARNERS ENROLLED</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="total-course d-flex align-items-center">
            <div class="blur-border">
              <div class="enroll-img">
                <img src="'.SITE_URL.'assets/img/icon-08.png" alt class="img-fluid"/>
              </div>
            </div>
            <div class="course-count">
              <h3><span class="counterUp">'.$COUNT_AD_OFF.'</span></h3>
              <p>TOTAL COURSES</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="total-course d-flex align-items-center">
            <div class="blur-border">
              <div class="enroll-img">
                <img src="'.SITE_URL.'assets/img/icon-09.png" alt class="img-fluid"/>
              </div>
            </div>
            <div class="course-count">
              <h3><span class="counterUp">'.$COUNT_COUNTRIES.'</span></h3>
              <p>COUNTRIES</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="lab-course">
      <div class="section-header aos" data-aos="fade-up">
        <div class="section-sub-head feature-head text-center">
          <h2>Unlimited access to multiple courses</h2>
        </div>
      </div>
      <div class="icon-group aos" data-aos="fade-up">
        <div class="offset-lg-1 col-lg-12">
          <div class="row">';
            foreach ($home['course_access'] as $key => $course_access) {
              echo'
              <div class="col-lg-1 col-3">
                <div class="total-course d-flex align-items-center">
                  <div class="blur-border">
                    <div class="enroll-img">
                      <img src="'.$course_access.'" alt class="img-fluid"/>
                    </div>
                  </div>
                </div>
              </div>';
            }
            echo'
          </div>
        </div>
      </div>
    </div>';
    */
    echo'
  </div>
</section>';
?>