<?php
// COURSE INFO
$condition = array ( 
                     'select'       =>	'f.faculty_name, d.dept_name
                                        ,ao.id_type, ao.admoff_type, ao.admoff_amount, ao.admoff_amount_in_usd
                                        ,c.curs_id, c.curs_name, c.curs_wise, c.curs_hours, c.duration, c.curs_video 
                                        ,c.id_level, c.curs_type_status, c.id_learning_method
                                        ,GROUP_CONCAT(DISTINCT lg.lang_name) as languages'
                    ,'join'         =>  'INNER JOIN '.DEPARTMENTS.' d ON d.dept_id = c.id_dept AND d.is_deleted = 0
                                         LEFT JOIN '.ADMISSION_OFFERING.' ao on ao.admoff_degree = c.curs_id AND ao.is_deleted = 0
                                         LEFT JOIN '.LANGUAGES.' lg ON FIND_IN_SET(lg.lang_id,c.id_lang)
                                         LEFT JOIN '.FACULTIES.' f ON f.faculty_id = c.id_faculty'
                    ,'where' 		    =>	array( 
                                             'c.curs_href' 	   => $zone
                                            ,'c.curs_status' 	 => '1'  
                                            ,'c.is_deleted' 	 => '0'  
                                          )
                    ,'return_type'	=>	'single'
            );
if(isset($_SESSION['userlogininfo']['STDID'])) {
  $condition['select'] .= ',ec2.secs_id as ifEnrolled, w.wl_id as ifWishlist';
  $condition['join'] .= ' LEFT JOIN '.ENROLLED_COURSES.' ec2 ON ec2.id_curs = c.curs_id AND ec2.id_ad_prg = 0 AND ec2.id_mas = 0 AND ec2.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).'
                         LEFT JOIN '.WISHLIST.' w ON w.id_curs = c.curs_id AND w.id_ad_prg IS NULL AND w.id_mas IS NULL AND w.id_std = '.cleanvars($_SESSION['userlogininfo']['STDID']).'';
}

$course = $dblms->getRows(COURSES.' c', $condition, $sql);

if($course['curs_id']){
// REFERRAL_CONTROL
if (!empty(LMS_VIEW)) {
  $ref_array        = explode(',', get_dataHashingOnlyExp(LMS_VIEW, false));
  if ($zone == $ref_array[0]) {
    $curs_href        = cleanvars($ref_array[0]);
    $ref_percentage   = cleanvars($ref_array[1]);
    $ref_id           = cleanvars($ref_array[2]);
    $adm_email        = cleanvars($ref_array[3]);
    $condition = array(
                         'select'       =>  'r.ref_id, r.ref_percentage, a.adm_email'
                        ,'join'         =>  'INNER JOIN '.COURSES.' AS c ON c.curs_id = "'.$course['curs_id'].'"
                                              INNER JOIN '.REFERRAL_TEACHER_SHARING.' AS rt ON rt.id_ref = r.ref_id AND FIND_IN_SET("'.$adm_email.'", rt.std_emails) AND rt.ref_shr_status = "1" AND rt.is_deleted = "0"
                                              LEFT JOIN '.ADMINS.' AS a ON FIND_IN_SET(a.adm_email, rt.std_emails) AND a.adm_status = "1" AND a.is_deleted = "0"'
                        ,'where'        =>  array(
                                                     'r.is_deleted' =>	0
                                                    ,'r.ref_status' =>	1
                                                    ,'r.ref_id'     =>	$ref_id
                                                )
                        ,'group_by'     =>  ' a.adm_id '
                        ,'search_by'    =>  ' AND r.ref_date_time_from < "'.date('Y-m-d G:i:s').'" 
                                              AND r.ref_date_time_to > "'.date('Y-m-d G:i:s').'" 
                                              AND FIND_IN_SET("'.cleanvars($course['curs_id']).'", r.id_curs)'
                        ,'return_type'  =>  'single'
    );
    $REFERRAL_CONTROL = $dblms->getRows(REFERRAL_CONTROL.' AS r', $condition, $sql);
  }
}

$courseMenu = array(
   'description'
  ,'course_content'
  ,'instructors'
  ,'how_it_works'
  ,'enrollment'
  ,'faq'
);
echo'
<div class="inner-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h2>'.$course['curs_name'].'</h2>
        <div class="instructor-wrap border-bottom-0 m-0">
          <span class="web-badge mb-5">'.$course['dept_name'].'</span>
        </div>';
        /*
        echo'
        <pre class="text-info mb-5">'.$course['faculty_name'].'</pre>';
        */
        echo'
        <q class="text-white mb-2">'.get_learning_method($course['id_learning_method']).'</q>
        <div class="course-info d-flex align-items-center border-bottom-0 p-0">
          <div class="cou-info">';
            if($course['id_type'] == 1){
              $coursePrice = (__COUNTRY__ == 'pk')?$course['admoff_amount']:$course['admoff_amount_in_usd'];
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
              if (!empty($DISCOUNT['discount_id'])) {
                echo'
                <p>'.$DISCOUNT['discount'].'% OFF</p>
                <p>
                  <small><s>'.(__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice).'</s></small>
                  <span class="text-info">/';
                    if ($DISCOUNT['discount_type'] == 1) {
                      echo ($coursePrice - $DISCOUNT['discount']);
                    } else if ($DISCOUNT['discount_type'] == 2) {
                      echo ($coursePrice - ($coursePrice * ($DISCOUNT['discount'] / 100) ));
                    }  echo'
                  </span>
                </p>';
              } else {
                echo '<p>'.(__COUNTRY__ == 'pk'?'PKR':'USD').' '.($coursePrice).'</p>';
              }
            } else {
              echo get_LeanerType($course['id_type']);
            }
            echo'
          </div>
        </div>
        <div class="course-info d-flex align-items-center border-bottom-0 m-0 p-0">
          <div class="cou-info">
            <img src="'.SITE_URL.'assets/img/icon/icon-01.svg" alt />
            <p>'.$course['duration'].' '.get_CourseWise($course['curs_wise']).($course['duration'] > 1 ? 's' : '').'</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<section class="page-content course-sec">
  <div class="container">
    <div class="row">
      <div class="col-lg-'.($varx == 'student' ? '12' : '8').'">
        <div class="row">
          <div class="featured-courses-five-tab">
            <div class="tab-content">
              <div class="nav tablist-five" style="text-align: left !important;" role="tablist">';
                foreach ($courseMenu as $key => $value):
                  echo'
                  <a class="nav-tab '.($key == 0 ? 'active' : '').'" 
                    data-bs-toggle="tab" 
                    href="#'.$value.'" 
                    role="tab"
                    data-type="'.$value.'">
                   '.($value == 'faq' ? 'FAQs' : moduleName($value)).'
                  </a>';
                endforeach;
                echo'
              </div>
              <div class="tab-content">';
                foreach ($courseMenu as $key => $value):
                  echo'
                  <div class="tab-pane fade '.($key == 0 ? 'show active' : '').'" id="'.$value.'">';
                    if($key == 0):
                      include('include/ajax/course_detail/description.php');
                    else:
                      echo'<div class="loader">Loading...</div>';
                    endif;
                    echo'
                  </div>';
                endforeach;
                echo'
              </div>
            </div>
          </div>
        </div>
      </div>';
      if($varx != 'student'){
        echo '
        <div class="col-lg-4">
          <div class="sidebar-sec">
            <div class="video-sec vid-bg">
              <div class="card">
                <div class="card-body">                  
                  <a href="#" 
                    class="video-thumbnail" 
                    data-bs-toggle="modal" 
                    data-bs-target="#videoModal"
                    data-video-id="'.$course['curs_video'].'">
                    <div class="play-icon">
                      <i class="fa-solid fa-play"></i>
                    </div>
                    <img src="https://vumbnail.com/'.$course['curs_video'].'.jpg" alt="Video">
                  </a>
                  <div class="video-details">
                    <div class="row gx-2">
                      <div class="col" data-id="'.$course['curs_id'].'">';
                        if (isset($course['ifWishlist']) && !empty($course['ifWishlist']) && isset($_SESSION['userlogininfo']['LOGINIDA'])){
                          echo '<a class="btn btn-wish w-100" href="javascript:;" onclick="remove_from_wishlist('.$course['ifWishlist'].','.$course['curs_id'].',3,3)"><i class="fa-solid fa-heart"></i> Remove Wishlist</a>';
                        } else {
                          echo '<a class="btn btn-wish w-100" href="javascript:;" onclick="add_to_wishlist('.$course['curs_id'].',3,3)"><i class="fa-regular fa-heart"></i> Add to Wishlist</a>';
                        }
                        echo'
                      </div>
                      <div class="col">';
                        // REFERRAL_CONTROL
                        if(isset($_SESSION['userlogininfo']['LOGINIDA'])) {
                            $condition = array(
                                'select' => 'r.ref_id, r.ref_percentage, c.curs_href'
                            , 'join' => 'INNER JOIN ' . COURSES . ' AS c ON c.curs_id = ' . cleanvars($course['curs_id']) . ''
                            , 'where' => array(
                                    'r.is_deleted' => 0
                                , 'r.ref_status' => 1
                                )
                            , 'search_by' => ' AND r.ref_date_time_from < "' . date('Y-m-d G:i:s') . '" 
                                                                  AND r.ref_date_time_to > "' . date('Y-m-d G:i:s') . '" 
                                                                  AND FIND_IN_SET(c.curs_id, r.id_curs) 
                                                                  AND FIND_IN_SET(' . cleanvars($_SESSION['userlogininfo']['LOGINIDA']) . ', r.id_user)'
                            , 'return_type' => 'single'
                            );
                            $REFERRAL_CONTROL_STD = $dblms->getRows(REFERRAL_CONTROL . ' AS r', $condition, $sql);

                            if ($REFERRAL_CONTROL_STD) {
                              echo '
                              <button id="curs_share_url_btn" class="btn btn-wish w-100"><i class="feather-share-2"></i> Share ' . $REFERRAL_CONTROL_STD['ref_percentage'] . '% OFF </button>
                              <input type="hidden" id="curs_share_url" value="' . SITE_URL . CONTROLER . '/' . ZONE . '/' . get_dataHashingOnlyExp($REFERRAL_CONTROL_STD['curs_href'] . ',' . $REFERRAL_CONTROL_STD['ref_percentage'] . ',' . $REFERRAL_CONTROL_STD['ref_id'] . ',' . $_SESSION['userlogininfo']['LOGINEMAIL'] . ',student', true) . '">';
                            } else {
                              echo '
                              <button id="curs_share_url_btn" class="btn btn-wish w-100"><i class="feather-share-2"></i> Share </button>
                              <input type="hidden" id="curs_share_url" value="' . SITE_URL . CONTROLER . '/' . ZONE . '">';
                            }
                        } else {
                            echo '
                          <button id="curs_share_url_btn" class="btn btn-wish w-100"><i class="feather-share-2"></i> Share </button>
                          <input type="hidden" id="curs_share_url" value="' . SITE_URL . CONTROLER . '/' . ZONE . '">';
                        }
                        echo'
                      </div>
                    </div>';
                    if(!empty($course['ifEnrolled']) && isset($_SESSION['userlogininfo']['LOGINIDA'])){
                      echo'<button class="btn btn-enroll w-100">Enrolled</button>';
                    } else if($course['curs_type_status'] == '5' ){
                      echo'<a class="btn btn-enroll w-100" onclick="show_modal(\''.SITE_URL.'include/modals/courses/confirm_buy.php?url='.CONTROLER.'/'.ZONE.'&id='.cleanvars($course['curs_id']).'&type=3&curs_type_status='.$course['curs_type_status'].'\');">Buy Now</a>';    
                    } else if($course['curs_type_status'] == 1){
                      echo'
                      <form action="'.SITE_URL.'show-interest" method="POST">
                        <input type="hidden" name="url" value="'.CONTROLER.'/'.ZONE.'">
                        <input type="hidden" name="id" value="'.$course['curs_id'].'">
                        <input type="hidden" name="type" value="'.$course['admoff_type'].'">
                        <button name="show_interest" type="submit" class="btn btn-enroll w-100">Enroll Now</button>
                      </form>';
                    } else {
                        $ref_flag = false;
                      echo'
                      <form action="'.SITE_URL.'invoice" method="POST">
                        <input type="hidden" name="url" value="'.CONTROLER.'/'.ZONE.'">
                        <input type="hidden" name="id" value="'.$course['curs_id'].'">
                        <input type="hidden" name="type" value="'.$course['admoff_type'].'">';
                        if (!empty($REFERRAL_CONTROL['ref_id'])) {
                          if (isset($_SESSION['userlogininfo']['LOGINEMAIL']) && !empty($_SESSION['userlogininfo']['LOGINEMAIL'])) {
                            if (!empty($REFERRAL_CONTROL['ref_id'])) {
                              echo'
                              <input type="hidden" name="ref_hash" value="'.cleanvars(LMS_VIEW).'">';
                              $ref_flag = true;
                            } else {
                              $ref_flag = false;
                            }
                          } else {
                            echo 'no ref';
                            $webSiteStorage                                 = array();
                              $webSiteStorage['COURSE_REFERRAL_CONTROLER'] 	=	CONTROLER;
                              $webSiteStorage['COURSE_REFERRAL_ZONE'] 		  =	ZONE;
                              $webSiteStorage['COURSE_REFERRAL_VIEW'] 		  =	LMS_VIEW;
                            $_SESSION['webSiteStorage'] 			              =	$webSiteStorage;
                            if (!empty($REFERRAL_CONTROL['adm_email'])){
                              header("location: ".SITE_URL."signin/".cleanvars(LMS_VIEW)."");
                              echo 'signin';
                            } else {
                              header("location: ".SITE_URL."signup/".cleanvars(LMS_VIEW)."");
                              echo 'signup';
                            }
                          }
                        }
                        echo'
                        <button name="enroll" type="submit" class="btn btn-enroll w-100">Enroll Now '.(($ref_flag)?(!empty($ref_array[1])?'<span class="text-warning"> '.$ref_array[1].'% Off </span> On This Course':''):'').'</button>
                      </form>';
                    }
                    echo'                 
                  </div>
                </div>
              </div>
            </div>

            <div class="card include-sec">
              <div class="card-body">
                <div class="cat-title">
                  <h4>This Course Includes</h4>
                </div>
                <ul>
                  <li>
                    <i class="far fa-clock me-2"></i>
                    Duration: <b>'.$course['curs_hours'].' Hour'.($course['duration'] > 1 ? 's' : '').'</b>
                  </li>
                  <li>
                    <i class="fas fa-book-open me-2"></i>
                    '.get_CourseWise($course['curs_wise']).($course['duration'] > 1 ? 's' : '').': <b>'.$course['duration'].'</b>
                  </li>
                  <li>
                    <i class="fab fa-cc-visa me-2"></i>
                    '.(CONTROLER == 'trainings' ? 'Training' : 'Certificate Course').' Type: <b>'.get_LeanerType($course['id_type']).'</b>
                  </li>
                  <li>
                    <i class="fas fa-laptop-house me-2"></i>
                    100% Online Course
                  </li>
                  <li>
                    <i class="fas fa-certificate me-2"></i>
                    Shareable Certificate
                  </li>
                  <li>
                    <i class="fas fa-chart-line me-2"></i>
                    Flexible Schedule
                  </li>
                  <li>
                    <i class="fas fa-chart-pie me-2"></i>
                    Level: <b>'.get_levels($course['id_level']).'</b>
                  </li>
                  <li>
                    <i class="fas fa-language me-2"></i>
                    Language: <b>'.$course['languages'].'</b>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>';
      }
      echo '
    </div>
  </div>
</section>

<div class="featured-section-five pt-4" style="background: #fafafa;">
  <div class="container">
      <div class="card border-0 shadow-sm">
          <div class="card-body d-lg-flex align-items-center p-4 p-lg-5">
              <div class="col-lg-5 col-md-12 col-sm-12 text-center mb-4 mb-lg-0">
                  <div class="bg-white p-3 rounded shadow-sm border d-inline-block">
                      <img src="'.SITE_URL.'assets/img/'.(CONTROLER == "courses" ? "course-certificate.jpeg" : "training-certificate.jpeg" ).'" alt="Shareable Certificate" class="img-fluid rounded" style="max-height: 550px; width: auto; object-fit: contain;"> 
                  </div>
              </div>
              <div class="col-lg-7 col-md-12 col-sm-12 ps-lg-5">
                <div class="mb-4">
                    <h2 class="text-primary fw-bold display-6">
                        <i class="fas fa-graduation-cap me-2"></i>Shareable Certificate
                    </h2>
                </div>
                <div class="community-item d-flex align-items-start mb-4 bg-white p-4 rounded shadow-sm border-start border-primary border-4">
                    <span class="community-icon-1 me-3 text-primary fs-3">
                        <i class="isax isax-book-saved5"></i>
                    </span>
                    <div>
                        <p class="mb-0 text-secondary lh-base">
                            Upon successfully completing the course, you will receive a digitally signed certificate from our institute, officially recognizing your achievement and newly acquired skills. This certificate can be shared easily across professional platforms such as LinkedIn, added to your resume or CV, and presented as proof of your expertise to potential employers or clients.
                        </p>
                    </div>
                </div>
                <div class="community-item d-flex align-items-center mt-3">
                    <div class="p-3 bg-white-50 border-start border-secondary rounded-end">
                        <p class="mb-0 text-muted fst-italic small">
                            <strong>Why it matters:</strong> In today’s fast-paced and competitive job market, showcasing verified credentials helps you stand out from the crowd. Gaining in-demand, industry-relevant skills not only strengthens your professional profile but also opens doors to new career opportunities and growth in your chosen field.
                        </p>
                    </div>
                </div>
              </div>
          </div>
      </div>
  </div>
</div>

<script>
  const curs_share_url_btn = document.getElementById("curs_share_url_btn");
  const curs_share_url = document.getElementById("curs_share_url");
  curs_share_url_btn.addEventListener("click", () => {
    const textToCopy = curs_share_url.textContent || curs_share_url.value;
    const tempInput = document.createElement("textarea");
    tempInput.value = textToCopy;
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // Select the entire content
    try {
        const successful = document.execCommand("copy");
        const msg = successful ? "Copied." : "Not copied.";
        const toastClass = successful ? "bg-success" : "bg-warning";
        Toastify({
            text: msg,
            gravity: "top",
            position: "right",
            className: toastClass,
            duration: 2000,
            close: true,
            stopOnFocus: true,
            offset: { x: 50, y: 0 },
        }).showToast();
    } catch (err) {
        Toastify({
            text: "Not copied.",
            gravity: "top",
            position: "right",
            className: "bg-warning",
            duration: 2000,
            close: true,
            stopOnFocus: true,
            offset: { x: 50, y: 0 },
        }).showToast();
    }
    document.body.removeChild(tempInput); // Remove the temporary input element
  });
  
  document.addEventListener("DOMContentLoaded", function() {
    let curs_id = "'.$course['curs_id'].'";
    let curs_wise = "'.$course['curs_wise'].'";

    function loadTabContent(type, target) {
      target.innerHTML = \'<div class="loader">Loading...</div>\'; // loader

      fetch("'.SITE_URL.'include/ajax/load_tab_content.php?type=" + type + "&curs_id=" + curs_id + "&curs_wise=" + curs_wise + "&_t=" + new Date().getTime())
        .then(response => response.text())
        .then(data => {
          target.innerHTML = data;
          target.setAttribute("data-loaded", "true");
          if (typeof applyClampText === "function") {
            applyClampText(target);
          }
        })
        .catch(err => {
          target.innerHTML = \'<p class="text-danger">Error loading content.</p>\';
        });
    }

    // Attach click listeners
    document.querySelectorAll(".nav-tab").forEach(function(tab) {
      tab.addEventListener("click", function(e) {
        let type = this.getAttribute("data-type");
        let target = document.querySelector(this.getAttribute("href"));

        // load only once
        if (target.getAttribute("data-loaded") !== "true") {
          loadTabContent(type, target);
        }
      });
    });

    // 🔹 Load first tab (description) on page load
    let firstTab = document.querySelector(".nav-tab.active");
    if (firstTab) {
      let type = firstTab.getAttribute("data-type");
      let target = document.querySelector(firstTab.getAttribute("href"));
      loadTabContent(type, target);
    }
  });

</script>';
} else {  
  header("Location: ".SITE_URL.CONTROLER."");	
}
?>
<script>
document.addEventListener("DOMContentLoaded", function () {
  var videoModal = document.getElementById('videoModal');
  videoModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var videoID = button.getAttribute('data-video-id');
    var iframe = document.getElementById("videoFrame");
    iframe.src = "https://player.vimeo.com/video/" + videoID + "?autoplay=1";
  });
  videoModal.addEventListener('hidden.bs.modal', function () {
    document.getElementById("videoFrame").src = "";
  });
});
</script>
<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-2">
        <div class="ratio ratio-16x9">
          <iframe id="videoFrame" src="" allow="autoplay; fullscreen" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>
</div>