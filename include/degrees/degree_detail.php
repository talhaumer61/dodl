<?php
$degree2 = $degrees['bachelor-of-science-in-computer-science'];

// PROGRAM DETAIL
$condition = array ( 
                     'select'       =>	'pc.cat_name, ap.id, ap.program, ap.programme_length, p.prg_duration, ap.total_package, ap.examination_fee,ap.portal_fee, ap.library_fee, ap.student_card_fee, ap.library_mag_fee, p.prg_admission_fee, ap.total_payments, p.prg_payments_per_semester, ap.payment_per_smester, ap.eligibility_criteria, ap.alumni_benefits,p.prg_semesters, ap.career_outcomes, ap.class_profile, ap.cohorts_deadlines, ap.detail, ap.apply_enroll, ap.prg_for, ap.shortdetail, COUNT(DISTINCT ec.secs_id) as TotalStd, ec2.secs_id as ifEnrolled, w.wl_id as ifWishlist'
                    ,'join'         =>  'INNER JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg
                                         INNER JOIN '.PROGRAMS_CATEGORIES.' pc ON pc.cat_id = p.id_cat
                                         LEFT JOIN '.ENROLLED_COURSES.' ec ON FIND_IN_SET(ap.id , ec.id_ad_prg)
                                         LEFT JOIN '.ENROLLED_COURSES.' ec2 ON ec2.id_ad_prg = ap.id AND ec2.id_mas = 0 AND ec2.id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'"
                                         LEFT JOIN '.WISHLIST.' w ON w.id_ad_prg = ap.id AND w.id_mas IS NULL AND w.id_std = "'.cleanvars($_SESSION['userlogininfo']['STDID']).'" '
                    ,'where' 		    =>	array( 
                                             'p.prg_href'       =>  $zone
                                            ,'ap.is_deleted'    =>  0  
                                          )
                    ,'return_type'	=>	'single'
                  ); 
$degree = $dblms->getRows(ADMISSION_PROGRAMS.' ap', $condition);

if($degree){
  $condition = array ( 
                         'select'       =>	'id_curs, id_curstype'
                        ,'where'        =>	array( 
                                                'id_ad_prg'   =>  $degree['id'] 
                                              )
                        ,'return_type'	=>	'all'
                      ); 
  $scheme = $dblms->getRows(PROGRAMS_STUDY_SCHEME, $condition);
}
$totalcrs=0;
foreach ($scheme as $key => $value) {
  $course=explode(',',$value['id_curs']);
  $totalcrs+=sizeof($course);
}

// MENU
$detailMenu = array(
   'overview'
  ,'admissions'
  ,'academics'
  ,'financing'
  ,'careers'
  ,'reviews'
);
echo'
<div class="inner-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="instructor-wrap border-bottom-0 m-0">
          <div class="about-instructor align-items-center">';
            /*
            echo'
            <div class="abt-instructor-img">
              <a href="#">
                <img src="'.$degree2['university_logo'].'" alt="img" class="img-fluid"/>
              </a>
            </div>
            <div class="instructor-detail me-3">
              <h5><a href="#">'.$degree2['university'].'</a></h5>
            </div>';
            */
            echo'
            <div class="rating mb-0">
              <i class="fas fa-star filled"></i>
              <i class="fas fa-star filled"></i>
              <i class="fas fa-star filled"></i>
              <i class="fas fa-star filled"></i>
              <i class="fas fa-star"></i>
              <span class="d-inline-block average-rating"><span>4.5</span> (15)</span>
            </div>
          </div>
          <span class="web-badge mb-3">'.$degree['cat_name'].'</span>
        </div>
        <h2 style="display:inline;">'.$degree['program'].'</h2>
        <p class="w-75">'.$degree['shortdetail'].'</p>
        <div class="course-info d-flex align-items-center border-bottom-0 m-0">
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/icon-01.svg" alt />
            <p>'.$totalcrs.' Courses</p>
          </div>
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/timer-icon.svg" alt />
            <p>'.$degree['prg_duration'].' Years</p>
          </div>
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/people.svg" alt />
            <p>'.$degree['TotalStd'].' Student</p>
          </div>
        </div>
      </div>
      <div class="col-lg-12">';
        if(!empty($degree['ifEnrolled'])){
          echo'<button style="float:right" class="btn btn-enroll"><i class="fa fa-check"></i> Enrolled</button>';
        }else{
          echo'
          <form action="'.SITE_URL.'invoice" method="POST">
            <input type="hidden" name="url" value="'.CONTROLER.'/'.ZONE.'">
            <input type="hidden" name="id" value="'.$degree['id'].'">
            <input type="hidden" name="type" value="1">
            <button name="enroll" style="float:right" type="submit" class="btn btn-enroll">Apply Now</button>
          </form>';
        }
        echo'
        <div data-id="'.$degree['id'].'">';
          if (isset($degree['ifWishlist']) && !empty($degree['ifWishlist'])){
            echo '<a class="btn btn-wish mx-1" style="float: right;" href="javascript:;" onclick="remove_from_wishlist('.$degree['ifWishlist'].','.$degree['id'].',1,2)"><i class="fa-solid fa-heart"></i> Remove Wishlist</a>';
          } else {
            echo '<a class="btn btn-wish mx-1" style="float: right;" href="javascript:;" onclick="add_to_wishlist('.$degree['id'].',1,2)"><i class="fa-regular fa-heart"></i> Add to Wishlist</a>';
          }
          echo'
        </div>
      </div>
    </div>
  </div>
</div>

<section class="page-content course-sec"> 
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="featured-courses-five-tab">
            <div class="tab-content">
              <div class="nav tablist-five" style="text-align: left !important;" role="tablist">';
                foreach ($detailMenu as $key => $menu) {
                  echo'<a class="nav-tab '.($key == 0 ? 'active' : '').'" data-bs-toggle="tab" href="#'.$menu.'" role="tab" >'.moduleName($menu).'</a>';
                }
                echo'
              </div>
              <div class="tab-content">  
                <div class="tab-pane fade show active" id="overview">
                  <div class="card overview-sec">
                    <div class="card-body">';
                      if(!empty($degree['detail'])){
                        echo html_entity_decode(html_entity_decode($degree['detail']));
                      }
                      echo'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="admissions">
                  <div class="card overview-sec">
                    <div class="card-body">';
                      if(!empty($degree['prg_for'])){
                        echo'
                        <h5 class="subs-title">Who is this degree for?</h5>
                        '.html_entity_decode(html_entity_decode($degree['prg_for'])).'';
                      }
                      if(!empty($degree['apply_enroll'])){
                        echo'
                        <h5 class="subs-title">How to apply & enrol?</h5>
                        '.html_entity_decode(html_entity_decode($degree['apply_enroll'])).'';
                      }
                      if(!empty($degree['cohorts_deadlines'])){
                        echo'
                        <h5 class="subs-title">Cohorts and Deadlines</h5>
                        '.html_entity_decode(html_entity_decode($degree['cohorts_deadlines'])).'';
                      }
                      if(!empty($degree['eligibility_criteria'])){
                        echo'
                        <h5 class="subs-title">Eligibility Criteria</h5>
                        '.html_entity_decode(html_entity_decode($degree['eligibility_criteria'])).'';
                      }
                      if(!empty($degree['class_profile'])){
                        echo'
                        <h5 class="subs-title">Class Profile</h5>
                        '.html_entity_decode(html_entity_decode($degree['class_profile'])).'';
                      }
                      echo'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="academics">
                  <div class="card overview-sec">
                    <div class="card-body">';
                      if(!empty($degree['programme_length'])){
                        echo'
                        <h5 class="subs-title">Programme Length</h5>
                        '.html_entity_decode(html_entity_decode($degree['programme_length'])).' ';
                      }
                      foreach ($scheme as $key => $value) {
                        if(!empty($value['id_curs'])){
                          $course=explode(',',$value['id_curs']);
                          if($value['id_curstype']==1){
                            echo '<h5 class="subs-title">Core Courses</h5>';
                          }
                          else if($value['id_curstype']==2){
                            echo '<h5 class="subs-title">Foundation Courses</h5>';
                          }
                          else{
                            echo '<h5 class="subs-title">Elective Courses</h5>';
                          }
                          echo '
                          <table class="table table-bordered">
                            <thead>
                              <tr class="table-secondary">
                                <th width="15%" scope="col">Course Code</th>
                                <th scope="col">Course Name</th>
                              </tr>
                            </thead>
                            <tbody>';
                              foreach ($course as $inkey => $invalue) {
                                $condition = array ( 
                                                      'select' 		=>	'curs_code, curs_name'
                                                      ,'where' 		=>	array( 
                                                                              'curs_id' => $course[$inkey] 
                                                                            )
                                                      ,'return_type'	=>	'single'
                                                    ); 
                                $crs = $dblms->getRows(COURSES, $condition);
                                  echo '
                                  <tr>
                                    <td>'.$crs['curs_code'].'</td>
                                    <td>'.$crs['curs_name'].'</td>
                                  </tr>';
                              }
                              echo'
                            </tbody>
                          </table>';
                        }
                      }
                      echo'
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="financing">
                  <div class="card overview-sec">
                    <div class="card-body">
                    <h5 class="subs-title">Tuition Fee</h5>
                    <table class="table table-bordered">
                      <thead>
                        <tr class="table-secondary">
                          <th scope="col">Semesters</th>
                          <th scope="col">Fee/Semester</th>
                          <th scope="col">Payments/Semester</th>
                          <th scope="col">Total No Of Payments</th>
                          <th scope="col">Total Package</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>'.$degree['prg_semesters'].'</td>
                          <td>'.$degree['payment_per_smester'].'</td>
                          <td>'.$degree['prg_payments_per_semester'].'</td>
                          <td>'.$degree['total_payments'].'x '.$degree['payment_per_smester']/$degree['prg_payments_per_semester'] .'</td>
                          <td>'.$degree['total_package'].'</td>
                        </tr>
                      </tbody>
                    </table>
                    <hr>
                    <h5 class="subs-title">Additional Charges</h5>
                    <table class="table table-bordered">
                      <thead>
                        <tr class="table-secondary">
                          <th scope="col">Addmission Fee</th>
                          <th scope="col">Examination Fee</th>
                          <th scope="col">Portal Fee</th>
                          <th scope="col">Library Fee</th>
                          <th scope="col">Student Crad Fee</th>
                          <th scope="col">Library Magzine Fee</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>'.$degree['prg_admission_fee'].'</td>
                          <td>'.$degree['examination_fee'].'</td>
                          <td>'.$degree['portal_fee'].'</td>
                          <td>'.$degree['library_fee'].'</td>
                          <td>'.$degree['library_mag_fee'].'</td>
                        </tr>
                      </tbody>
                    </table>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="careers">
                  <div class="card overview-sec">
                    <div class="card-body">';
                      if(!empty($degree['career_outcomes'])){
                        echo'
                        <h5 class="subs-title">Career Outcomes</h5>
                        '.html_entity_decode(html_entity_decode($degree['career_outcomes'])).'';
                      }
                      if(!empty($degree['alumni_benefits'])){
                        echo'
                        <h5 class="subs-title">Aliumni Benefits</h5>
                        '.html_entity_decode(html_entity_decode($degree['alumni_benefits'])).'';
                      }
                      echo '
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade show" id="reviews">
                  <div class="card overview-sec">
                    <div class="card-body">
                      '.$degree2['description']['reviews'].'
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card comment-sec">
          <div class="card-body">
            <h5 class="subs-title">Post A comment</h5>
            <form>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Full Name" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email" />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <input type="email" class="form-control" placeholder="Subject" />
              </div>
              <div class="form-group">
                <textarea rows="4" class="form-control" placeholder="Your Comments" ></textarea>
              </div>
              <div class="submit-section">
                <button class="btn submit-btn" type="submit"> Submit  </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>';
?>