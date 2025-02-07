<?php
require_once 'include/head.php';
require_once 'include/top_nav.php';
require_once 'include/breadcrumb.php';
if (!isset($_POST['enroll'])) {
  header("Location: ".SITE_URL."dashboard");
}else if (!isset($_SESSION['userlogininfo'])) {
  $_SESSION['search_path'] = $_POST['url'];
  sessionMsg("Warning!","Your are not Login","warning");
  header("location: ".SITE_URL."signin");
}
include_once 'query.php';

$condition = array (
                        'select' 		    =>	'w.wl_id ,w.id_curs, w.id_mas, w.id_ad_prg
                                            ,c.curs_name, c.curs_photo
                                            ,m.mas_name, m.mas_photo
                                            ,p.prg_name, p.prg_photo'
                        ,'join' 		    =>	'LEFT JOIN '.COURSES.' c ON c.curs_id = w.id_curs
                                             LEFT JOIN '.MASTER_TRACK.' m ON m.mas_id = w.id_mas
                                             LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = w.id_ad_prg
                                             LEFT JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg' 
                        ,'where' 		    =>	array( 
                                                  'id_std'  => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                ) 
                        ,'order_by'     =>  "w.wl_id DESC"
                        ,'return_type'	=>	'all'
                    ); 
$WISHLIST = $dblms->getRows(WISHLIST.' w',$condition, $sql);
$found = false;
echo'
<div class="container">
  <div class="row">
    <div class="col-xl-12 col-lg-12 mb-2 col-md-12">	
      <div class="row">
        <div class="col-md-12">
          <form action="" method="post">
            <div class="settings-widget">
              <div class="settings-inner-blk p-0">
                <div class="sell-course-head comman-space">
                  <h3>Invoice</h3>
                  <p>List of Enrollment</p>
                </div>
                <div class="comman-space pb-0">
                  <div class="settings-tickets-blk course-instruct-blk table-responsive">
                    <table class="table table-nowrap mb-2">                      
                      <thead>
                        </tr>
                        <tr>
                          <th>COURSE</th>
                          <th width="150"></th>
                          <th width="150" class="text-center">Price ('.(__COUNTRY__ == 'pakistan'?'PKR':'USD').')</th>
                          <th width="50" class="text-center">Action</th>
                        </tr>
                      </thead>
                      <tbody>';
                        $i = 0;
                        $total = 0;
                        if($WISHLIST){
                          foreach ($WISHLIST as $row) {
                            $i++;                            
                            // NAME, PHOTO
                            $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                            if($row['prg_name']){
                              $type = '1';
                              $idWish = $row['id_ad_prg'];
                              $name = $row['prg_name'];
                              $file_url = SITE_URL_PORTAL.'uploads/images/programs/'.$row['prg_photo'];
                            }elseif($row['mas_name']){
                              $type = '2';
                              $idWish = $row['id_mas'];
                              $name = $row['mas_name'];
                              $file_url = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$row['mas_photo'];
                            }elseif($row['curs_name']){
                              $type = '3';
                              $idWish = $row['id_curs'];
                              $name = $row['curs_name'];
                              $file_url = SITE_URL_PORTAL.'uploads/images/courses/'.$row['curs_photo'];                              
                            }
                            if($_POST['type'] == $type && $idWish == $_POST['id']){
                              $found = true;
                            }
                            if (check_file_exists($file_url)) {
                              $photo = $file_url;
                            }
                            // ADMOFF_AMOUNT
                            $condition = array (
                                                   'select'       =>	'ao.admoff_amount, ao.admoff_amount_in_usd, dd.discount_type, dd.discount, d.discount_from, d.discount_to' 
                                                  ,'join'         =>	'LEFT JOIN '.DISCOUNT_DETAIL.' AS dd ON ao.admoff_degree = dd.id_curs
                                                                        LEFT JOIN '.DISCOUNT.' AS d ON d.discount_id = dd.id_setup AND d.discount_status = 1 AND d.is_deleted = 0' 
                                                  ,'where' 		    =>	array( 
                                                                             'ao.admoff_status'  => 1
                                                                            ,'ao.admoff_type'    => cleanvars($type)
                                                                            ,'ao.admoff_degree'  => cleanvars($idWish)
                                                                          ) 
                                                  ,'return_type'	=>	'single'
                                                ); 
                            $ADMISSION_OFFERING = $dblms->getRows(ADMISSION_OFFERING.' AS ao',$condition, $sql);
                            $currentDate  = date('Y-m-d');
                            $startDate    = $ADMISSION_OFFERING['discount_from'];
                            $endDate      = $ADMISSION_OFFERING['discount_to'];
                            if ($currentDate >= $startDate && $currentDate <= $endDate) {
                              if ($ADMISSION_OFFERING['discount_type'] == 1) {
                                if (__COUNTRY__ == 'pakistan') {
                                  $admoff_amount_after_discount = ($ADMISSION_OFFERING['admoff_amount'] - $ADMISSION_OFFERING['discount']);
                                } else {
                                  $admoff_amount_after_discount = ($ADMISSION_OFFERING['admoff_amount_in_usd'] - $ADMISSION_OFFERING['discount']);
                                }
                              } else if ($ADMISSION_OFFERING['discount_type'] == 2) {
                                if (__COUNTRY__ == 'pakistan') {
                                  $admoff_amount_after_discount = ($ADMISSION_OFFERING['admoff_amount'] - ($ADMISSION_OFFERING['admoff_amount'] * ($ADMISSION_OFFERING['discount'] / 100) ));
                                } else {
                                  $admoff_amount_after_discount = ($ADMISSION_OFFERING['admoff_amount_in_usd'] - ($ADMISSION_OFFERING['admoff_amount_in_usd'] * ($ADMISSION_OFFERING['discount'] / 100) ));
                                }
                              } else {
                                $admoff_amount_after_discount = $ADMISSION_OFFERING['admoff_amount'];
                              }
                            } else {
                              $admoff_amount_after_discount = $ADMISSION_OFFERING['admoff_amount'];
                            }
                            $total += $admoff_amount_after_discount;
                            echo '
                            <tr>
                              <td>
                                <div class="sell-table-group d-flex align-items-center">
                                  <div class="sell-group-img">
                                    <a href="'.SITE_URL.'courses/'.$course['curs_id'].'">
                                      <img alt="'.$photo.'" src="'.$photo.'" class="img-fluid">
                                    </a>
                                  </div>
                                  <div class="sell-tabel-info">
                                    <p>'.$name.'</p>
                                    <div class="d-flex align-items-center border-bottom-0 pb-0">
                                      <div class="rating-img d-flex align-items-center pe-3">
                                        <img src="assets/img/icon/icon-01.svg" alt="">
                                        <p>6</p>
                                      </div>
                                      <div class="course-view d-flex align-items-center">
                                        <img src="assets/img/icon/timer-start.svg" alt="">
                                        <p>6</p>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <input type="hidden" class="removable" name="id['.$i.']" value="'.$idWish.'">
                                <input type="hidden" class="removable" name="type['.$i.']" value="'.$type.'">
                                <input type="hidden" class="removable" name="amount['.$i.']" value="'.$admoff_amount_after_discount.'">
                              </td>
                              <td class="text-center">'.get_enroll_type($type).'</td>
                              <td class="text-center">';
                                if ($ADMISSION_OFFERING['discount_type'] == 1) {
                                  echo'
                                  '.(__COUNTRY__ == 'pakistan'?'PKR':'USD').' '.number_format($admoff_amount_after_discount).'.00';  
                                } else if ($ADMISSION_OFFERING['discount_type'] == 2) {
                                  echo'
                                  '.(__COUNTRY__ == 'pakistan'?'PKR':'USD').' '.number_format($admoff_amount_after_discount).'.00';
                                } else {
                                  echo'
                                  '.(__COUNTRY__ == 'pakistan'?'PKR':'USD').' '.number_format($admoff_amount_after_discount).'.00';  
                                }
                                echo'
                              </td>
                              <td class="text-center"><a href="javascript:;" onclick="remove_item(this)"><i class="fa fa-trash"></i></a></td>
                            </tr>';
                          }
                        }
                        if(!$found){
                          $i++;
                          $condition = array ( 
                                               'select'       =>	'ao.admoff_degree'
                                              ,'join' 		    =>	'' 
                                              ,'where' 		    =>	array( 
                                                                           'ao.admoff_status'   =>  1
                                                                          ,'ao.is_deleted'      =>  0
                                                                          ,'ao.admoff_degree'   =>  cleanvars($_POST['id'])
                                                                          ,'ao.admoff_type'     =>  cleanvars($_POST['type'])
                                                                        ) 
                                              ,'return_type'	=>	'single'
                                            );
                          if($_POST['type'] == 1){
                            $condition['select'] = 'ao.admoff_degree, ap.program, p.prg_photo, ao.admoff_amount, ao.admoff_amount_in_usd, dd.discount_type, dd.discount, d.discount_from, d.discount_to';
                            $condition['join'] = 'LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ao.admoff_degree
                                                  LEFT JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg
                                                  LEFT JOIN '.DISCOUNT_DETAIL.' AS dd ON ao.admoff_degree = dd.id_curs
                                                  LEFT JOIN '.DISCOUNT.' AS d ON d.discount_id = dd.id_setup AND d.discount_status = 1 AND d.is_deleted = 0';
                          }elseif($_POST['type'] == 2){
                            $condition['select'] = 'ao.admoff_degree, m.mas_name, m.mas_photo, ao.admoff_amount, ao.admoff_amount_in_usd, dd.discount_type, dd.discount, d.discount_from, d.discount_to';
                            $condition['join'] = 'LEFT JOIN '.MASTER_TRACK.' m ON m.mas_id = ao.admoff_degree
                                                  LEFT JOIN '.DISCOUNT_DETAIL.' AS dd ON ao.admoff_degree = dd.id_curs
                                                  LEFT JOIN '.DISCOUNT.' AS d ON d.discount_id = dd.id_setup AND d.discount_status = 1 AND d.is_deleted = 0';
                          }elseif($_POST['type'] == 3){
                            $condition['select'] = 'ao.admoff_degree, c.curs_name, c.curs_photo, ao.admoff_amount, ao.admoff_amount_in_usd, dd.discount_type, dd.discount, d.discount_from, d.discount_to';
                            $condition['join'] = 'LEFT JOIN '.COURSES.' c ON c.curs_id = ao.admoff_degree
                                                  LEFT JOIN '.DISCOUNT_DETAIL.' AS dd ON ao.admoff_degree = dd.id_curs
                                                  LEFT JOIN '.DISCOUNT.' AS d ON d.discount_id = dd.id_setup AND d.discount_status = 1 AND d.is_deleted = 0';
                          }
                          $ADMISSION_OFFERING = $dblms->getRows(ADMISSION_OFFERING.' ao',$condition, $sql);                          
                          $currentDate  = date('Y-m-d');
                          $startDate    = $ADMISSION_OFFERING['discount_from'];
                          $endDate      = $ADMISSION_OFFERING['discount_to'];
                          if ($currentDate >= $startDate && $currentDate <= $endDate) {
                            if ($ADMISSION_OFFERING['discount_type'] == 1) {
                              if (__COUNTRY__ == 'pakistan') {
                                $admoff_amount_after_discount = ($ADMISSION_OFFERING['admoff_amount'] - $ADMISSION_OFFERING['discount']);
                              } else {
                                $admoff_amount_after_discount = ($ADMISSION_OFFERING['admoff_amount_in_usd'] - $ADMISSION_OFFERING['discount']);
                              }
                            } else if ($ADMISSION_OFFERING['discount_type'] == 2) {
                              if (__COUNTRY__ == 'pakistan') {
                                $admoff_amount_after_discount = ($ADMISSION_OFFERING['admoff_amount'] - ($ADMISSION_OFFERING['admoff_amount'] * ($ADMISSION_OFFERING['discount'] / 100) ));
                              } else {
                                $admoff_amount_after_discount = ($ADMISSION_OFFERING['admoff_amount_in_usd'] - ($ADMISSION_OFFERING['admoff_amount_in_usd'] * ($ADMISSION_OFFERING['discount'] / 100) ));
                              }
                            } else {
                              $admoff_amount_after_discount = $ADMISSION_OFFERING['admoff_amount'];
                            }
                          } else {
                            $admoff_amount_after_discount = $ADMISSION_OFFERING['admoff_amount'];
                          }  


                          $total += $admoff_amount_after_discount;

                          // NAME, PHOTO
                          $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                          if($ADMISSION_OFFERING['prg_name']){
                            $type = '1';
                            $name = $ADMISSION_OFFERING['prg_name'];
                            $file_url = SITE_URL_PORTAL.'uploads/images/programs/'.$ADMISSION_OFFERING['prg_photo'];
                          }elseif($ADMISSION_OFFERING['mas_name']){
                            $type = '2';
                            $name = $ADMISSION_OFFERING['mas_name'];
                            $file_url = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$ADMISSION_OFFERING['mas_photo'];
                          }elseif($ADMISSION_OFFERING['curs_name']){
                            $type = '3';
                            $name = $ADMISSION_OFFERING['curs_name'];
                            $file_url = SITE_URL_PORTAL.'uploads/images/courses/'.$ADMISSION_OFFERING['curs_photo'];
                          }
                          if (check_file_exists($file_url)) {
                            $photo = $file_url;
                          }
                          echo '
                          <tr>
                            <td>
                              <div class="sell-table-group d-flex align-items-center">
                                <div class="sell-group-img">
                                  <a href="#">
                                    <img alt="'.$photo.'" src="'.$photo.'" class="img-fluid">
                                  </a>
                                </div>
                                <div class="sell-tabel-info">
                                  <p>'.$name.'</p>
                                  <div class="d-flex align-items-center border-bottom-0 pb-0">
                                    <div class="rating-img d-flex align-items-center pe-3">
                                      <img src="assets/img/icon/icon-01.svg" alt="">
                                      <p>6</p>
                                    </div>
                                    <div class="course-view d-flex align-items-center">
                                      <img src="assets/img/icon/timer-start.svg" alt="">
                                      <p>6</p>
                                    </div>
                                  </div>
                                </div>
                              </div>                              
                              <input type="hidden" class="removable" name="id['.$i.']" value="'.$ADMISSION_OFFERING['admoff_degree'].'">
                              <input type="hidden" class="removable" name="type['.$i.']" value="'.$_POST['type'].'">
                              <input type="hidden" class="removable" name="amount['.$i.']" value="'.$admoff_amount_after_discount.'">
                            </td>
                            <td class="text-center">'.get_enroll_type($_POST['type']).'</td>
                            <td class="text-center">'.(__COUNTRY__ == 'pakistan'?'PKR':'USD').' '.number_format($admoff_amount_after_discount).'.00</td>
                            <td class="text-center"><a href="javascript:;" onclick="remove_item(this)"><i class="fa fa-trash"></i></a></td>
                          </tr>';
                        }
                        echo '
                        <tr>
                          <th>
                            <div class="form-switch text-primary">
                              <input class="form-check-input me-2" id="have_cpn_code" name="have_cpn_code" type="checkbox">
                              <label class="form-check-label mb-0" for="have_cpn_code">Have Coupon Code?</label>
                            </div>
                          </th>
                          <th class="text-end">
                            Grand Total:
                            <input type="hidden" id="total_amount" name="total_amount" value="'.$total.'">
                          </th>
                          <th class="text-center">'.(__COUNTRY__ == 'pakistan'?'PKR':'USD').' '.number_format($total).'.00</th>
                          <th class="text-center"></th>
                        </tr>                        
                        <tr id="coupon_code_tr"></tr>
                      </tbody>
                      <tbody id="discount_div"></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="container-fluid">
              <div class="update-profile row">
                <div class="col">
                  <a href="'.SITE_URL.$_POST['url'].'" class="btn btn-wish w-100 mr-1"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                </div>                      
                <div class="col">
                  <button name="enrolled_courses" type="submit" class="btn btn-enroll w-100">Enroll Now</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<hr>
<script>
function remove_item(item){
  $(item).closest("tr").remove();
  var hiddenInputs = $(\'input[type="hidden"].removable\');

  if (hiddenInputs.length === 0) {
    window.location.href = "'.SITE_URL.$_POST['url'].'";
  }
}

document.addEventListener("DOMContentLoaded", function() {
  $("#coupon_code_tr").hide();
  $("#coupon_code_tr").html("");
  $("#discount_div").html("");

  document.getElementById("have_cpn_code").addEventListener("change", function() {
    if (this.checked) {  
      $("#coupon_code_tr").show();    
      $("#coupon_code_tr").html(`
                                  <th></th>
                                  <th class="text-end">Coupon Code:</th>
                                  <th><input type="text" class="form-control" name="cpn_code" id="cpn_code" placeholder="Coupon Code" oninput="get_coupon_discount(this.value)" required></th>
                                  <th id="coupon_check"></th>
                                `);
    } else {
      $("#coupon_code_tr").hide();
      $("#coupon_code_tr").html("");
      $("#discount_div").html("");
    }
  });
});

function get_coupon_discount(val) {
  var cpn_code = $("#cpn_code").val();
  var total_amount = $("#total_amount").val();

  $.ajax({
    url: "include/ajax/get_coupon_discount.php",
    type: "POST",
    data: { 
      cpn_code: cpn_code,
      total_amount: total_amount
    },
    dataType: "json", 
    success: function(response) {
      $("#discount_div").html(response.html_content);
      $("#coupon_check").html(response.coupon_check);
    },
    error: function(xhr, status, error) {
      console.error("AJAX request failed:", status, error);
    }
  });
}

</script>';
require_once 'include/footer.php';
?>