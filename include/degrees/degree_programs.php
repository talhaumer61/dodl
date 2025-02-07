<?php
$condition = array ( 
                     'select' 		  =>	'p.prg_href, pc.cat_name, ap.id, ap.program, p.prg_duration, p.prg_photo'
                    ,'join'         =>  'INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = ap.id AND a.admoff_type = 1
                                         INNER JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg
                                         INNER JOIN '.PROGRAMS_CATEGORIES.' pc ON pc.cat_id = p.id_cat'
                    ,'where' 		    =>	array( 
                                                 'ap.status'      =>  1 
                                                ,'ap.is_deleted'  =>  0 
                                                ,'pc.cat_href'    =>  cleanvars($zone)
                                            )
                    ,'group_by'     =>  'ap.id'
                    ,'order_by'     =>  'ap.id DESC'
                    ,'return_type'	=>	'all'
                  ); 
$ADMISSION_PROGRAMS = $dblms->getRows(ADMISSION_PROGRAMS.' ap', $condition);
echo'
<div class="page-banner">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-12">
        <h1 class="mb-0">'.moduleName($zone).'</h1>
      </div>
    </div>
  </div>
</div>
<div class="page-content">
  <div class="container">
    <div class="settings-widget">
      <div class="settings-inner-blk">
        <div class="comman-space p-0">
          <div class="settings-tickets-blk course-instruct-blk table-responsive">
            <table class="table table-nowrap mb-0">
              <thead>
                <tr>
                  <th colspan="3">
                    <h4>Find the right online bachelor\'s degree for you</h4>
                    <h6>Business Bachelor\'s Degrees</h6>
                  </th>
                </tr>
              </thead>
              <tbody>';
                foreach ($ADMISSION_PROGRAMS as $prg) {
                  $condition = array ( 
                                         'select'       =>	'id_curs, id_curstype'
                                        ,'where'        =>	array( 
                                                                'id_ad_prg'   =>  $prg['id'] 
                                                              )
                                        ,'return_type'	=>	'all'
                                      ); 
                  $scheme = $dblms->getRows(PROGRAMS_STUDY_SCHEME, $condition);

                  $totalcrs=0;
                  foreach ($scheme as $key => $value) {
                    $course = explode(',',$value['id_curs']);
                    $totalcrs += sizeof($course);
                  }

                  if (isset($_SESSION['userlogininfo'])) {
                    $condition = array ( 
                                           'select' 		  =>	'wl_id, id_ad_prg'
                                          ,'where' 		    =>	array( 
                                                                      'id_ad_prg'   =>  $prg['id'] 
                                                                  )
                                          ,'search_by'    => ' AND id_mas IS NULL'
                                          ,'return_type'	=>	'single'
                                        ); 
                    $wishlist = $dblms->getRows(WISHLIST, $condition);
                  }
                  echo'
                  <tr>
                    <td>
                      <div class="sell-table-group d-flex align-items-center">
                        <div class="sell-group-img">
                          <a href="'.SITE_URL.'degree-detail/'.$prg['prg_href'].'">
                            <img class="img-fluid" alt="" src="'.SITE_URL_PORTAL.'uploads/images/programs/'.($prg['prg_photo'] ? $prg['prg_photo'] : "default.jpg").'"/>
                          </a>
                        </div>
                        <div class="sell-tabel-info">
                          <p><a href="'.SITE_URL.'degree-detail/'.$prg['prg_href'].'">'.$prg['program'].'</a></p>
                          <small>Offered by '.SITE_NAME.'</small>
                          <div class="course-info d-flex align-items-center border-bottom-0 pb-0">
                            <div class="rating-img d-flex align-items-center">
                              <img src="'.SITE_URL.'assets/img/icon/icon-01.svg" alt />
                              <p>'.$totalcrs.' Courses</p>
                            </div>
                            <div class="course-view d-flex align-items-center" >
                              <img src="'.SITE_URL.'assets/img/icon/timer-start.svg" alt />
                              <p>'.$prg['prg_duration'].' Years</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td width="70" class="text-center">
                      <div class="all-btn all-category d-flex align-items-center">
                        <form action="'.SITE_URL.'invoice" method="POST">
                          <input type="hidden" name="url" value="degrees">
                          <input type="hidden" name="id" value="'.$prg['id'].'">
                          <input type="hidden" name="type" value="1">
                          <button name="enroll" type="submit" class="btn btn-primary">BUY NOW</button>
                        </form>
                      </div>
                    </td>
                    <td width="70" class="text-center">
                      <div class="course-share d-flex align-items-center justify-content-center" data-id="'.$prg['id'].'">';
                        if (isset($wishlist) && !empty($wishlist)){
                          echo '<a href="javascript:;" onclick="remove_from_wishlist('.$wishlist['wl_id'].','.$prg['id'].',1)"><i class="fa-solid fa-heart"></i></a>';
                        } else {
                          echo '<a href="javascript:;" onclick="add_to_wishlist('.$prg['id'].',1)"><i class="fa-regular fa-heart"></i></a>';
                        }
                        echo'
                      </div>
                    </td>
                  </tr>';
                }
                echo'
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>';
?>