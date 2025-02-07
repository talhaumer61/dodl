<?php 
$condition = array ( 
                     'select'       =>	'rev_id, rev_name, rev_photo, rev_detail'
                    ,'where' 		    =>	array( 
                                               'rev_status'   =>  '1' 
                                              ,'is_deleted'   =>  '0'
                                            )
                    ,'return_type'	=>	'all'
                  );
$REVIEWS = $dblms->getRows(REVIEWS, $condition);
echo'
<section class="section user-love">
  <div class="container">
    <div class="section-header white-header aos" data-aos="fade-up">
      <div class="section-sub-head feature-head text-center">
        <span>Check out these real reviews</span>
        <h2>Users-love-us Don\'t take it from us.</h2>
      </div>
    </div>
  </div>
</section>
<section class="testimonial-four">
  <div class="review">
    <div class="container">
      <div class="testi-quotes">
        <img src="assets/img/qute.png" alt />
      </div>
      <div class="mentor-testimonial lazy slider aos" data-aos="fade-up" data-sizes="50vw ">';
        foreach ($REVIEWS as $key => $row) {
          $words = explode(' ', $row['rev_name']);
          $initials = '';
          foreach ($words as $word) {
            $initials .= $word[0];
          }
          $rev_title = substr($initials, 0, 2);

          // CHECK FILE EXIST
          $rev_photo = '';
          $file_url = SITE_URL_PORTAL.'uploads/images/reviews/'.$row['rev_photo'];
          if (check_file_exists($file_url)) {
            $rev_photo = $file_url;
          }
          echo'
          <div class="d-flex justify-content-center">
            <div class="testimonial-all d-flex justify-content-center">
              <div class="testimonial-two-head text-center align-items-center d-flex">
                <div class="testimonial-four-saying">
                  <div class="testi-right">
                    <img src="assets/img/qute-01.png" alt />
                  </div>
                  <p>'.html_entity_decode($row['rev_detail']).'</p>
                  <div class="four-testimonial-founder">
                    <div class="fount-about-img">';
                      if(!empty($rev_photo)){
                        echo'<img src="'.$rev_photo.'" alt class="img-fluid"/>';
                      } else {
                        echo'<h2 class="circle bg-warning text-white" style="margin: auto; width:80px; height:80px;">'.$rev_title.'</h2>';
                      }
                      echo'                      
                    </div>
                    <h3>'.$row['rev_name'].'</h3>
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