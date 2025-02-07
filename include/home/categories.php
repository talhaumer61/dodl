<?php 
$condition = array ( 
                     'select'       =>  'cc.cat_id, cc.cat_name, cc.cat_href, cc.cat_icon, COUNT(a.admoff_degree) as TotalCrs'
                    ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.id_cat = cc.cat_id AND a.admoff_type=3'
                    ,'where' 	      =>  array ( 
                                                 'cc.is_deleted'    =>  0
                                                ,'cc.cat_status'    =>  1
                                                ,'cc.id_type'       =>  1
                                              )
                    ,'group_by'     =>  'a.id_cat'
                    ,'order_by'     =>  'RAND()'
                    ,'limit'        =>  8
                    ,'return_type'  =>  'all'
                  ); 
$COURSES_CATEGORIES = $dblms->getRows(COURSES_CATEGORIES.' cc', $condition);
echo'
<section class="home-two topcategory-sec pt-0">
  <div class="container">    
    <div class="header-two-title text-center" data-aos="fade-up">
      <!--<p class="tagline">Favourite Course</p>-->
      <h2>Top Category</h2>
      <div class="header-two-text">
        <p class="mb-0">Explore our highest-rated courses, designed to help you excel in your professional journey.</p>
      </div>
    </div>
    <div class="owl-carousel instructors-course owl-theme aos" data-aos="fade-up">';
      foreach ($COURSES_CATEGORIES as $key => $category) {          
        echo'
        <div class="instructors-widget border">
          <div class="instructors-img">
            <a href="'.SITE_URL.'courses/categories/'.$category['cat_id'].'">
              <img class="img-fluid" src="'.SITE_URL_PORTAL.'uploads/images/courses/categories/icons/'.$category['cat_icon'].'"/>
            </a>
          </div>
          <div class="instructors-content text-center">
            <h5><a href="'.SITE_URL.'courses/categories/'.$category['cat_id'].'">'.$category['cat_name'].'</a></h5>
            <p>'.$category['TotalCrs'].' Courses</p>
          </div>
        </div>';
      }
      echo'
    </div>
    <div class="row mt-5">
      <div class="col-lg-12">
        <div class="more-details text-center" data-aos="fade-down">
          <a href="'.SITE_URL.'courses" class="discover-btn">View all Categories <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>';
?>