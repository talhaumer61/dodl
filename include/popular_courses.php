<?php
  echo '
  <!--Section-->
		<section class="sptb bg-white">
			<div class="container">
				<div class="section-title">
					<h2>Popular Courses</h2>
					<!-- <p class="fs-18 lead">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p> -->
				</div>
				<div id="defaultCarousel" class="owl-carousel Card-owlcarousel owl-carousel-icons">';
        $sqllms1	= $dblms->querylms("SELECT c.course_id, c.thumbnail, c.course_title, c.course_duration, c.course_detail, cat.category_name, c.course_fee 
                                      FROM ".COURSES." c
                                      INNER JOIN ".COURSE_CATEGORY." cat ON cat.category_id   = c.id_course_category
                                      WHERE c.id_deleted = '0'
                                      AND c.is_popular = '1'
                                      ORDER BY c.course_id  DESC
                                    ");
        $countCourse = mysqli_num_rows($sqllms1);
        if($countCourse > 0) {
          $resut_sqllms = $sqllms1;
        }else{
          $sqllms2	= $dblms->querylms("SELECT c.course_id, c.thumbnail, c.course_title, c.course_duration, c.course_detail, cat.category_name, c.course_fee 
                                        FROM ".COURSES."
                                        INNER JOIN ".COURSE_CATEGORY." cat ON cat.category_id   = c.id_course_category
                                        WHERE c.id_deleted = '0'
                                        ORDER BY c.course_id  DESC
                                      ");
          $resut_sqllms = $sqllms2;
        }
        while($rowsvalues = mysqli_fetch_array($resut_sqllms)) :
          echo '
					<div class="item">
						<div class="card mb-0 ">
							<div class="card-body p-4">
                <div class="item-card7-img">
                  <div class="item-card7-imgs">
                    <a href="'.SITE_URL.'course-detail/'.$rowsvalues['course_id'].'"></a>
                    <img src="'.SITE_URL.'uploads/images/course_thumbnail/small/'.$rowsvalues['thumbnail'].'" alt="img" class="cover-image br-7 border">
                  </div>';
                  // echo'
                  // <div class="item-card7-overlaytext">
                  //   <h4 class="mb-0">$'.$rowsvalues['course_fee'].'</h4>
                  // </div>';
                  echo'
                </div>
                <a href="'.SITE_URL.'course-detail/'.$rowsvalues['course_id'].'">
                <small class="text-muted">-- '.$rowsvalues['category_name'].'</small>
                <h4 class="font-weight-semibold mb-2 mt-3">'.$rowsvalues['course_title'].'</h4></a>
                <div class="pt-2 mb-3">
                  <a class="me-4"><span class="font-weight-bold">Duration :</span> <span class="text-muted">'.get_monthduration($rowsvalues['course_duration']).'</span></a>
                </div>
								<div class="mb-4 text-justify text-wrap">'.html_entity_decode(html_entity_decode($rowsvalues['course_detail'])).'</div>
							</div>
						</div>
					</div>';
        endwhile;
        echo '
				</div>
			</div>
		</section>
		<!--/Section-->';
?>