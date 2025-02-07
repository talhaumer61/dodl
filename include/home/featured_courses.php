<?php
$condition = array ( 
                       'select'       =>  'cc.cat_id, cc.cat_name, cc.cat_icon, cc.cat_image'
                      ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.id_cat = cc.cat_id AND a.admoff_type=3'
                      ,'where' 	      =>  array ( 
                                                   'cc.is_deleted'      =>  0
                                                  ,'cc.cat_status'      =>  1
                                                  ,'cc.id_type'         =>  1
                                                )
                      ,'order_by'     =>  ' RAND()'
                      ,'group_by'     =>  'a.id_cat'
                      ,'limit'        =>  5
                      ,'return_type'  =>  'all'
                    ); 
$COURSES_CATEGORIES = $dblms->getRows(COURSES_CATEGORIES.' cc', $condition);

$condition = array ( 
                       'select'       =>	'c.curs_href, c.curs_type_status, c.curs_id, c.curs_wise, c.duration, c.curs_name, c.curs_rating, c.curs_hours, c.curs_icon, c.curs_photo, c.curs_code, c.curs_href, a.admoff_amount, a.admoff_type, a.admoff_amount_in_usd, att.id_teacher, e.emply_name 
                                          ,COUNT(DISTINCT ec.secs_id) as TotalStd, COUNT(DISTINCT l.lesson_id) TotalLesson'
                      ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = c.curs_id AND a.admoff_type=3
                                            LEFT JOIN '.ENROLLED_COURSES.' ec ON FIND_IN_SET(c.curs_id, ec.id_curs)
                                            LEFT JOIN '.COURSES_LESSONS.' l ON l.id_curs = c.curs_id AND l.is_deleted = 0 AND l.lesson_status = 1
                                            LEFT JOIN '.ALLOCATE_TEACHERS.' att ON att.id_curs = c.curs_id
                                            LEFT JOIN '.EMPLOYEES.' e ON e.emply_id = att.id_teacher'
                      ,'where' 		    =>	array( 
                                                 'c.curs_status' 	    => '1' 
                                                ,'c.is_deleted' 	    => '0'
                                              )
                      ,'group_by'     =>  'c.curs_id'
                      ,'limit'        =>  9
                      ,'order_by'     =>  ' FIELD(c.curs_type_status, "2", "4", "3", "5", "1"), RAND()'
                      ,'return_type'	=>	'all'
                    );
$COURSESALL = $dblms->getRows(COURSES.' c', $condition, $sql);
echo'
<section class="featured-section-five">
  <div class="container">
    <div class="header-five-title text-center" data-aos="fade-down">
      <h2>Featured Courses</h2>
      <p>Pick Your Favourite Course</p>
    </div>
    <div class="row">
      <div class="featured-courses-five-tab">
        <div class="tab-content">
          <div class="nav tablist-five" role="tablist">
            <a class="nav-tab active" data-bs-toggle="tab" href="#categoryall" role="tab">All</a>';
            if($COURSES_CATEGORIES){
              foreach ($COURSES_CATEGORIES as $key => $category){
                echo'<a class="nav-tab " data-bs-toggle="tab" href="#category'.$key.'" role="tab">'.$category['cat_name'].'</a>';
              }
            }
            echo'
          </div>
          <div class="tab-content">';           
            echo'
            <div class="tab-pane fade show active" id="categoryall">
              <div class="ux-design-five">
                <div class="row">';
                  if($COURSESALL){
                    foreach ($COURSESALL as $id => $course) {
                      $curs_rating = (($course['curs_rating'] == '0' || $course['curs_rating'] == '') ? '4' : $course['curs_rating']);

                      // CHECK FILE EXIST
                      $file_url = SITE_URL_PORTAL.'uploads/images/courses/'.$course['curs_photo'];
                      if (check_file_exists($file_url)) {
                        $curs_photo = $file_url;
                      } else {
                        $curs_photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                      }

                      // WISHLIST
                      if (isset($_SESSION['userlogininfo'])) {
                        $condition = array ( 
                                              'select' 		=>	'wl_id, id_curs'
                                              ,'where' 		=>	array( 
                                                                          'id_std'   => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                                          ,'id_curs'  => $course['curs_id']
                                                                      ) 
                                              , 'search_by'   => ' AND id_ad_prg IS NULL AND id_mas IS NULL'
                                              ,'return_type'	=>	'single'
                                            ); 
                        $wishlist = $dblms->getRows(WISHLIST, $condition);
                      }
                      echo'
                      <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                        <div class="course-box-five">
                          <div class="product-five">
                            <div class="product-img-five">
                              <a href="">
                                <img class="img-fluid" alt src="'.$curs_photo.'" width="100%"/>
                              </a>
                              <div class="heart-five" data-id="'.$course['curs_id'].'">';
                                if($course['curs_type_status'] == '1'){

                                } else {
                                  if (isset($wishlist) && !empty($wishlist)){
                                    echo '<a href="javascript:;" onclick="remove_from_wishlist('.$wishlist['wl_id'].','.$course['curs_id'].',3)"><i class="fa-solid fa-heart"></i></a>';
                                  } else {
                                    echo '<a href="javascript:;" onclick="add_to_wishlist('.$course['curs_id'].',3)"><i class="fa-regular fa-heart"></i></a>';
                                  }
                                }
                                echo'
                              </div>';
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
                                if($course['curs_type_status'] != '1' && !empty($DISCOUNT['discount_id'])) {
                                  echo'  
                                  <div class="trending-price-right blink-image">
                                    <h4 class="m-0">
                                      <span>'.$DISCOUNT['discount'].'% OFF</span>
                                    </h4>
                                  </div>';
                                }
                              echo'
                            </div>
                            <div class="product-content-five">
                              <h3 class="product-five-title mb-1">
                                <a href="'.SITE_URL.'courses/'.$course['curs_href'].'">'.$course['curs_name'].'</a>
                              </h3>
                              '.(!empty($course['emply_name']) ? '<pre class="mb-2" style="color: #1A155D;"><i class="fas fa-user-tie me-1"></i>'.$course['emply_name'].'</pre>' : '').'
                              
                              <div class="info-five-middle mb-0 border-0 ">                                  
                                <div class="rating-img">
                                  <span class="me-2"><i class="fa-solid fa-book-open"></i></span>
                                  <p>'.$course['duration'].' '.get_CourseWise($course['curs_wise']).($course['duration'] > 1 ? 's' : '').'</p>
                                </div>
                                <div class="rating-img">
                                  <p>';
                                    $coursePrice = (__COUNTRY__ == 'pk')?$course['admoff_amount']:$course['admoff_amount_in_usd'];
                                    if($course['curs_type_status'] != '1'){
                                      if (!empty($DISCOUNT['discount_id'])) {
                                        echo'
                                        <small><s>'.(__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice).'</s></small>
                                        <span class="text-info">/';
                                          if ($DISCOUNT['discount_type'] == 1) {
                                            echo ($coursePrice - $DISCOUNT['discount']);
                                          } else if ($DISCOUNT['discount_type'] == 2) {
                                            echo ($coursePrice - ($coursePrice * ($DISCOUNT['discount'] / 100) ));
                                          }
                                          echo'
                                        </span>';
                                      } else {
                                        echo (__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice);
                                      }
                                    }
                                    echo'
                                  </p>
                                </div>
                              </div>';
                              /*
                              if($course['curs_type_status'] != '1'){
                                echo'
                                <div class="info-five-middle pb-0 mb-0 border-0">
                                  <div class="rating-img">
                                    <div class="rating">';
                                      for ($rating=1; $rating <= 5; $rating++) {
                                        echo'<i class="fas fa-star '.($rating <= $curs_rating ? 'filled' : '').'"></i>';
                                      }
                                      echo'
                                    </div>
                                  </div>
                                  <div class="rating-img">
                                    <span class="me-2"><i class="fa-solid fa-user"></i></span>
                                    <p>'.($course['TotalStd'] + 100).' Students</p>
                                  </div>
                                </div>';
                              }
                              */
                              if($course['curs_type_status']){
                                echo get_curs_status($course['curs_type_status']);
                              }
                              echo'
                            </div>
                          </div>
                          <div class="joing-course-ovelay">';
                            if($course['curs_type_status'] == '1'){
                              echo'
                              <form action="'.SITE_URL.'show-interest" method="POST">
                                <input type="hidden" name="url" value="'.CONTROLER.'">
                                <input type="hidden" name="id" value="'.$course['curs_id'].'">
                                <input type="hidden" name="type" value="'.$course['admoff_type'].'">
                                <button name="show_interest" type="submit" class="joing-course-btn">Show Interest</button>
                              </form>';
                            } else {
                              echo'<a href="'.SITE_URL.'courses/'.$course['curs_href'].'" class="joing-course-btn" >Join Course</a>';
                            }
                            echo'
                          </div>
                        </div>
                      </div>';
                    }
                  }
                  echo'
                </div>
              </div>
            </div>';
            if($COURSES_CATEGORIES){
              foreach ($COURSES_CATEGORIES as $key => $category){
                $condition = array ( 
                                       'select'       =>	'c.curs_href, c.curs_type_status, c.curs_id, c.curs_wise, c.duration, c.curs_name, c.curs_rating, c.curs_hours, c.curs_icon, c.curs_photo, c.curs_code, c.curs_href, a.admoff_amount, a.admoff_type, a.admoff_amount_in_usd, att.id_teacher, e.emply_name 
                                                          ,COUNT(DISTINCT ec.secs_id) as TotalStd, COUNT(DISTINCT l.lesson_id) TotalLesson'
                                      ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = c.curs_id AND a.admoff_type=3
                                                           LEFT JOIN '.ENROLLED_COURSES.' ec ON FIND_IN_SET(c.curs_id, ec.id_curs)
                                                           LEFT JOIN '.COURSES_LESSONS.' l ON l.id_curs = c.curs_id AND l.is_deleted = 0 AND l.lesson_status = 1
                                                           LEFT JOIN '.ALLOCATE_TEACHERS.' att ON att.id_curs = c.curs_id
                                                           LEFT JOIN '.EMPLOYEES.' e ON e.emply_id = att.id_teacher'
                                      ,'where' 		    =>	array( 
                                                                 'c.curs_status' 	    => '1' 
                                                                ,'c.is_deleted' 	    => '0'
                                                              )
                                      ,'search_by'    =>  ' AND FIND_IN_SET('.$category['cat_id'].', c.id_cat)'
                                      ,'group_by'     =>  'c.curs_id'
                                      ,'limit'        =>  9
                                      ,'order_by'     =>  ' FIELD(c.curs_type_status, "2", "4", "3", "5", "1"), RAND()'
                                      ,'return_type'	=>	'all'
                                    );
                $COURSES = $dblms->getRows(COURSES.' c', $condition); 
                echo'
                <div class="tab-pane fade show" id="category'.$key.'">
                  <div class="ux-design-five">
                    <div class="row">';
                      if($COURSES){
                        foreach ($COURSES as $id => $course) {
                          $curs_rating = (($course['curs_rating'] == '0' || $course['curs_rating'] == '') ? '4' : $course['curs_rating']);

                          // CHECK FILE EXIST
                          $file_url = SITE_URL_PORTAL.'uploads/images/courses/'.$course['curs_photo'];
                          if (check_file_exists($file_url)) {
                            $curs_photo = $file_url;
                          } else {
                            $curs_photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                          }

                          // WISHLIST
                          if (isset($_SESSION['userlogininfo'])) {
                            $condition = array ( 
                                                   'select' 		=>	'wl_id, id_curs'
                                                  ,'where' 		=>	array( 
                                                                               'id_std'   => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                                              ,'id_curs'  => $course['curs_id']
                                                                          ) 
                                                  , 'search_by'   => ' AND id_ad_prg IS NULL AND id_mas IS NULL'
                                                  ,'return_type'	=>	'single'
                                                ); 
                            $wishlist = $dblms->getRows(WISHLIST, $condition);
                          }
                          echo'
                          <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                            <div class="course-box-five">
                              <div class="product-five">
                                <div class="product-img-five">
                                  <a href="">
                                    <img class="img-fluid" alt src="'.$curs_photo.'" width="100%"/>
                                  </a>
                                  <div class="heart-five" data-id="'.$course['curs_id'].'">';
                                    if($course['curs_type_status'] == '1'){

                                    } else {
                                      if (isset($wishlist) && !empty($wishlist)){
                                        echo '<a href="javascript:;" onclick="remove_from_wishlist('.$wishlist['wl_id'].','.$course['curs_id'].',3)"><i class="fa-solid fa-heart"></i></a>';
                                      } else {
                                        echo '<a href="javascript:;" onclick="add_to_wishlist('.$course['curs_id'].',3)"><i class="fa-regular fa-heart"></i></a>';
                                      }
                                    }
                                    echo'
                                  </div>';
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
                                    if ($course['curs_type_status'] != '1' && !empty($DISCOUNT['discount_id'])) {
                                      echo'  
                                      <div class="trending-price-right blink-image">
                                        <h4 class="m-0">
                                          <span>'.$DISCOUNT['discount'].'% OFF</span>
                                        </h4>
                                      </div>';
                                    }
                                  echo'
                                </div>
                                <div class="product-content-five">
                                  <h3 class="product-five-title mb-1">
                                    <a href="'.SITE_URL.'courses/'.$course['curs_href'].'">'.$course['curs_name'].'</a>
                                  </h3>
                                  '.(!empty($course['emply_name']) ? '<pre class="mb-2" style="color: #1A155D;"><i class="fas fa-user-tie me-1"></i>'.$course['emply_name'].'</pre>' : '').'
                                  
                                  <div class="info-five-middle mb-0 border-0 ">                                  
                                    <div class="rating-img">
                                      <span class="me-2"><i class="fa-solid fa-book-open"></i></span>
                                      <p>'.$course['duration'].' '.get_CourseWise($course['curs_wise']).($course['duration'] > 1 ? 's' : '').'</p>
                                    </div>
                                    <div class="rating-img">
                                      <p>';
                                        $coursePrice = (__COUNTRY__ == 'pk')?$course['admoff_amount']:$course['admoff_amount_in_usd'];
                                        if($course['curs_type_status'] != '1'){
                                          if (!empty($DISCOUNT['discount_id'])) {
                                            echo'
                                            <small><s>'.(__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice).'</s></small>
                                            <span class="text-info">/';
                                              if ($DISCOUNT['discount_type'] == 1) {
                                                echo ($coursePrice - $DISCOUNT['discount']);
                                              } else if ($DISCOUNT['discount_type'] == 2) {
                                                echo ($coursePrice - ($coursePrice * ($DISCOUNT['discount'] / 100) ));
                                              }
                                              echo'
                                            </span>';
                                          } else {
                                            echo (__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice);
                                          }
                                        }
                                        echo'
                                      </p>
                                    </div>
                                  </div>';
                                  /*
                                  if($course['curs_type_status'] != '1'){
                                    echo'
                                    <div class="info-five-middle pb-0 mb-0 border-0">
                                      <div class="rating-img">
                                        <div class="rating">';
                                          for ($rating=1; $rating <= 5; $rating++) {
                                            echo'<i class="fas fa-star '.($rating <= $curs_rating ? 'filled' : '').'"></i>';
                                          }
                                          echo'
                                        </div>
                                      </div>
                                      <div class="rating-img">
                                        <span class="me-2"><i class="fa-solid fa-user"></i></span>
                                        <p>'.($course['TotalStd'] + 100).' Students</p>
                                      </div>
                                    </div>';
                                  }
                                  */
                                  if($course['curs_type_status']){
                                    echo get_curs_status($course['curs_type_status']);
                                  }
                                  echo'
                                </div>
                              </div>
                              <div class="joing-course-ovelay">';
                                if($course['curs_type_status'] == '1'){
                                  echo'
                                  <form action="'.SITE_URL.'show-interest" method="POST">
                                    <input type="hidden" name="url" value="'.CONTROLER.'">
                                    <input type="hidden" name="id" value="'.$course['curs_id'].'">
                                    <input type="hidden" name="type" value="'.$course['admoff_type'].'">
                                    <button name="show_interest" type="submit" class="joing-course-btn">Show Interest</button>
                                  </form>';
                                } else {
                                  echo'<a href="'.SITE_URL.'courses/'.$course['curs_href'].'" class="joing-course-btn" >Join Course</a>';
                                }
                                echo'
                              </div>
                            </div>
                          </div>';
                        }
                      }
                      echo'
                    </div>
                  </div>
                </div>';
              }
            }
            echo'
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12" data-aos="fade-up">
        <div class="more-details text-center">
          <a href="'.SITE_URL.'courses" class="discover-btn">View all Courses <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>';
?>