<?php
$condition = array ( 
                       'select' 		  =>	'ap.id, ap.program, p.prg_photo, p.prg_href, p.prg_duration'
                      ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = ap.id AND a.admoff_type=1
                                           INNER JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg'
                      ,'where' 		    =>	array( 
                                                  'ap.status' 	        => '1' 
                                                  ,'ap.is_deleted' 	    => '0' 
                                              ) 
                      ,'order_by'     => 'ap.id DESC'
                      ,'limit'        =>  3
                      ,'return_type'	=>	'all'
                    ); 
$DEGREES = $dblms->getRows(ADMISSION_PROGRAMS.' ap', $condition);
echo'
<section class="section new-course">
  <div class="container">
    <div class="section-header aos" data-aos="fade-up">
      <div class="section-sub-head">
        <span>What’s New</span>
        <h2>Online Degrees</h2>
      </div>
      <div class="all-btn all-category d-flex align-items-center">
        <a href="'.SITE_URL.'degrees" class="btn btn-primary">All Degrees</a>
      </div>
    </div>
    <div class="section-text aos" data-aos="fade-up">
      <p class="mb-0">Join our online degree programs and take the next step in your educational journey, earning your degree from anywhere with our comprehensive and convenient online programs.</p>
    </div>
    <div class="course-feature">
      <div class="row">';
        foreach ($DEGREES as $key => $degree){
          // CHECK FILE EXIST
          $photo    = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
          $file_url = SITE_URL_PORTAL.'uploads/images/programs/'.$degree['prg_photo'];
          if (check_file_exists($file_url)) {
            $photo = $file_url;
          }

          // COURSES
          $condition = array ( 
                                 'select'       =>	'id_curs, id_curstype'
                                ,'where' 		    =>	array( 
                                                        'id_ad_prg' 	     => $degree['id'] 
                                                      )
                                ,'return_type'	=>	'all'
                              ); 
          $scheme = $dblms->getRows(PROGRAMS_STUDY_SCHEME, $condition);
          $totalcrs=0;
          foreach ($scheme as $key => $value) {
            $course=explode(',',$value['id_curs']);
            $totalcrs+=sizeof($course);
          }

          // WISHLIST
          if (isset($_SESSION['userlogininfo'])) {
            $condition = array ( 
                                   'select' 		  =>	'wl_id, id_ad_prg'
                                  ,'where' 		    =>	array( 
                                                               'id_std'     => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                              ,'id_ad_prg'  => $degree['id']
                                                            ) 
                                  , 'search_by'   => ' AND id_mas IS NULL'
                                  ,'return_type'	=>	'single'
                                ); 
            $wishlist = $dblms->getRows(WISHLIST, $condition);
          }
          echo'
          <div class="col-lg-4 col-md-6 d-flex">
            <div class="course-box d-flex aos" data-aos="fade-up">
              <div class="product">
                <div class="product-img">
                  <a href="'.SITE_URL.'degree-detail/'.$degree['prg_href'].'">
                    <img class="img-fluid" alt src="'.$photo.'"/>
                  </a>
                </div>
                <div class="product-content">';
                  /*
                  echo'
                  <div class="course-group d-flex">
                    <div class="course-group-img d-flex">
                      <a href="#">
                        <img src="'.$degree['university_logo'].'" alt class="img-fluid"/>
                      </a>
                      <div class="course-name">
                        <h4>
                          <a href="#">'.$degree['university'].'</a>
                        </h4>
                        <p>University</p>
                      </div>
                    </div>
                  </div>';
                  */
                  echo'
                  <h3 class="title instructor-text">
                    <a href="'.SITE_URL.'degree-detail/'.$degree['prg_href'].'">'.$degree['program'].'</a>
                  </h3>
                  <div class="course-info d-flex align-items-center">
                    <div class="rating-img d-flex align-items-center">
                      <img src="assets/img/icon/icon-01.svg" alt />
                      <p>'.$totalcrs.' Courses</p>
                    </div>
                    <div class="course-view d-flex align-items-center">
                      <img src="assets/img/icon/icon-02.svg" alt />
                      <p>'.$degree['prg_duration'].' Years</p>
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
                  <div class="all-btn d-flex align-items-center" >
                    <a href="'.SITE_URL.'degree-detail/'.$degree['prg_href'].'" class="btn btn-primary">Read More</a>
                    <div class="course-share d-flex align-items-center justify-content-center" data-id="'.$degree['id'].'">';
                      if (isset($wishlist) && !empty($wishlist)){
                        echo '<a href="javascript:;" onclick="remove_from_wishlist('.$wishlist['wl_id'].','.$degree['id'].',1)"><i class="fa-solid fa-heart"></i></a>';
                      } else {
                        echo '<a href="javascript:;" onclick="add_to_wishlist('.$degree['id'].',1)"><i class="fa-regular fa-heart"></i></a>';
                      }
                      echo'
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>';
        }
        echo'
      </div>
    </div>
  </div>
</section>';
?>