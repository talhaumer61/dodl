<?php 
$condition = array ( 
                    'select' 		    =>	'mt.mas_duration, mt.mas_href, mt.mas_id,mt.mas_status,mt.mas_icon,mt.mas_photo,mt.mas_name, COUNT(DISTINCT mtd.id_curs) TotalCourses'
                    ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = mt.mas_id AND a.admoff_type=2
                                        INNER JOIN '.MASTER_TRACK_DETAIL.' mtd ON mtd.id_mas = mt.mas_id'
                  ,'where' 		      =>	array( 
                                              'mt.mas_status' 	    => '1' 
                                              ,'mt.is_deleted' 	    => '0' 
                                          ) 
                  ,'order_by'       => "mt.mas_id"
                  ,'group_by'       => "mt.mas_id DESC"
                  ,'return_type'	  =>	'all'
                  ); 
$MASTERTRACK = $dblms->getRows(MASTER_TRACK.' mt', $condition);

$condition = array ( 
                         'select'       =>  "e.emply_id,e.emply_photo,e.emply_name,e.emply_gender,d.designation_name"
                        ,'join'         =>  'LEFT JOIN '.DESIGNATIONS.' d ON d.designation_id = e.id_designation'
                        ,'where' 	      =>  array ( 
                                                     'e.is_deleted'     => 0
                                                    ,'e.emply_status'   => 1
                                            )
                        ,'order_by'     =>  ' RAND()'
                        ,'limit'        =>  20
                        ,'return_type'  =>  'all'
                    ); 
$EMPLOYEES = $dblms->getRows(EMPLOYEES.' e', $condition);
echo'
<section class="section trend-course">
  <div class="container">
    <div class="section-header aos" data-aos="fade-up">
      <div class="section-sub-head">
        <span>What’s New</span>
        <h2>MasterTrack</h2>
      </div>
      <div class="all-btn all-category d-flex align-items-center">
        <a href="'.SITE_URL.'master-track-certificates" class="btn btn-primary">All Certificates</a>
      </div>
    </div>
    <div class="section-text aos" data-aos="fade-up">
      <p class="mb-0">MasterTrack are specialized, short-term programs offering in-depth knowledge in a specific field, enhancing career skills and expertise.</p>
    </div>
    <div class="owl-carousel trending-course owl-theme aos" data-aos="fade-up">';
      foreach ($MASTERTRACK as $key => $certificate){
        // CHECK FILE EXIST
        $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
        $file_url = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$certificate['mas_photo'];
        if (check_file_exists($file_url)) {
          $photo = $file_url;
        }

        // WISHLIST
        if (isset($_SESSION['userlogininfo'])) {
          $condition = array ( 
                                 'select' 		  =>	'wl_id, id_mas'
                                ,'where' 		    =>	array( 
                                                             'id_std'     => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                            ,'id_mas'     => $certificate['mas_id']
                                                          ) 
                                ,'search_by'    => ' AND id_ad_prg IS NULL'
                                ,'return_type'	=>	'single'
                              ); 
          $wishlist = $dblms->getRows(WISHLIST, $condition);
        }
        echo'
        <div class="course-box trend-box">
          <div class="product trend-product">
            <div class="product-img">
              <a href="'.SITE_URL.'master-track-detail/'.$certificate['mas_href'].'">
                <img class="img-fluid" alt src="'.$photo.'"/>
              </a>
            </div>
            <div class="product-content">
              <h3 class="title">
                <a href="'.SITE_URL.'master-track-detail/'.$certificate['mas_href'].'">'.$certificate['mas_name'].'</a>
              </h3>
              <div class="course-info d-flex align-items-center">
                <div class="rating-img d-flex align-items-center">
                  <img src="assets/img/icon/icon-01.svg" alt class="img-fluid"/>
                  <p>'.$certificate['TotalCourses'].' Courses</p>
                </div>
                <div class="course-view d-flex align-items-center">
                  <img src="assets/img/icon/icon-02.svg" alt class="img-fluid"/>
                  <p>'.$certificate['mas_duration'].' Months</p>
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
              <div class="all-btn d-flex align-items-center">
                <a href="'.SITE_URL.'master-track-detail/'.$certificate['mas_href'].'" class="btn btn-primary">Read More</a>
                <div class="course-share d-flex align-items-center justify-content-center" data-id="'.$certificate['mas_id'].'">';
                  if (isset($wishlist) && !empty($wishlist)){
                    echo '<a href="javascript:;" onclick="remove_from_wishlist('.$wishlist['wl_id'].','.$certificate['mas_id'].',2)"><i class="fa-solid fa-heart"></i></a>';
                  } else {
                    echo '<a href="javascript:;" onclick="add_to_wishlist('.$certificate['mas_id'].',2)"><i class="fa-regular fa-heart"></i></a>';
                  }
                  echo'
                </div>
              </div>
            </div>
          </div>
        </div>';
      }
      echo'
    </div>

    <div class="feature-instructors">
      <div class="section-header aos" data-aos="fade-up">
        <div class="section-sub-head feature-head text-center">
          <h2>Featured Instructor</h2>
          <div class="section-text aos" data-aos="fade-up">
            <p class="mb-0">Get inspired by our featured instructors, distinguished professionals who offer unparalleled knowledge and practical insights in their areas of expertise.</p>
          </div>
        </div>
      </div>
      <div class="owl-carousel instructors-course owl-theme aos" data-aos="fade-up">';
        foreach ($EMPLOYEES as $key => $value) {
          // CHECK FILE EXIST
          if($value['emply_gender'] == '2'){
            $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
          }else{            
            $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
          }
          if(!empty($value['emply_photo'])){
            $file_url   = SITE_URL_PORTAL.'uploads/images/employees/'.$value['emply_photo'];
            if (check_file_exists($file_url)) {
              $photo = $file_url;
            }
          }
          echo'
          <div class="instructors-widget">
            <div class="instructors-img">
              <a href="'.SITE_URL.'instructors/'.$value['emply_id'].'">
                <img class="img-fluid" alt src="'.$photo.'"/>
              </a>
            </div>
            <div class="instructors-content text-center">
              <h5><a href="'.SITE_URL.'instructors/'.$value['emply_id'].'">'.$value['emply_name'].'</a></h5>
              <p>'.$value['designation_name'].'</p>
            </div>
          </div>';
        }
        echo'
      </div>
      <div class="row mt-5">
        <div class="col-lg-12">
          <div class="more-details text-center" data-aos="fade-down">
            <a href="'.SITE_URL.'instructors" class="discover-btn">View all <i class="fas fa-arrow-right ms-2"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>';
?>