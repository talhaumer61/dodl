<?php
require_once 'include/head.php';
require_once 'include/top_nav.php';
require_once 'include/breadcrumb.php';

include_once 'query.php';

$enroll_id=get_dataHashingOnlyExp(ZONE,false);
$conditions = array ( 
  'select'       =>	'ao.admoff_degree, ao.admoff_type'
 ,'join'         =>	'INNER JOIN '.ADMISSION_OFFERING.' ao ON ec.id_curs = ao.admoff_degree'
 ,'where' 		=>	array( 
                              'ec.id_std'       => cleanvars($_SESSION['userlogininfo']['STDID']) 
                             ,'ec.secs_id'      => cleanvars($enroll_id) 
                         )
 ,'return_type'	=>	'single'
);
$challanFor = $dblms->getRows(ENROLLED_COURSES.' ec', $conditions); 

$condition  =   [ 
  'select'        =>  'o.org_link_to, o.org_percentage',
  'join'          =>  'INNER JOIN '.ADMINS.' AS a ON a.adm_id = o.id_loginid',
  'where' 	      =>  [
                          'o.org_status'  => 1,
                          'o.is_deleted'  => 0,
                          'o.org_id'      => $_SESSION['userlogininfo']['LOGINORGANIZATIONID'],
                      ],
  'return_type'  =>  'single',
];  
$ORGANIZATIONS = $dblms->getRows(SKILL_AMBASSADOR.' AS o', $condition);

echo'
<div class="container">
  <div class="row">
    <div class="col-xl-12 col-lg-12 mb-2 col-md-12">
      <div class="row">
        <div class="col-md-12">
          <form method="post" autocomplete="off">
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
                          <th width="150" class="text-center">Price</th>
                          <th width="50" class="text-center">Action</th>
                        </tr>
                      </thead>
                      <tbody>';
                        $i = 0;
                        $total = 0;
                        $priceFound = false;
                        if(!empty($enroll_id)){
                          $i++;
                          $idWish   = '';
                          $name     = '';
                          $file_url = '';
                          $condition = array ( 
                                               'select'       =>	'ao.admoff_degree, ao.id_type'
                                              ,'join' 		    =>	'' 
                                              ,'where' 		    =>	array( 
                                                                           'ao.admoff_status'   =>  1
                                                                          ,'ao.is_deleted'      =>  0
                                                                          ,'ao.admoff_degree'   =>  cleanvars($challanFor['admoff_degree'])
                                                                          ,'ao.admoff_type'     =>  cleanvars($challanFor['admoff_type'])
                                                                        ) 
                                              ,'return_type'	=>	'single'
                                            );
                          if($challanFor['admoff_type'] == 1){
                            $condition['select'] = 'ao.admoff_degree, ap.program, p.prg_photo, ao.admoff_amount, ao.admoff_amount_in_usd, ao.id_type';
                            $condition['join'] = 'LEFT JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id = ao.admoff_degree
                                                  LEFT JOIN '.PROGRAMS.' p ON p.prg_id = ap.id_prg';
                          }elseif($challanFor['admoff_type'] == 2){
                            $condition['select'] = 'ao.admoff_degree, m.mas_name, m.mas_photo, ao.admoff_amount, ao.admoff_amount_in_usd, ao.id_type';
                            $condition['join'] = 'LEFT JOIN '.MASTER_TRACK.' m ON m.mas_id = ao.admoff_degree';
                          }elseif($challanFor['admoff_type'] == 3 || $challanFor['admoff_type'] == 4){
                            $condition['select'] = 'ao.admoff_degree, c.curs_name, c.curs_photo, ao.admoff_amount, ao.admoff_amount_in_usd, ao.id_type';
                            $condition['join'] = 'LEFT JOIN '.COURSES.' c ON c.curs_id = ao.admoff_degree';
                          }
                          $ADMISSION_OFFERING = $dblms->getRows(ADMISSION_OFFERING.' ao',$condition, $sql);                         

                          // DISCOUNT
                          $condition = array ( 
                                                 'select'       =>	'd.discount_id, d.discount_from, d.discount_to, dd.discount, dd.discount_type'
                                                ,'join'         =>  'INNER JOIN '.DISCOUNT_DETAIL.' AS dd ON d.discount_id = dd.id_setup AND dd.id_curs = "'.$_POST['id'].'"'
                                                ,'where' 		    =>	array( 
                                                                           'd.discount_status' 	=> '1' 
                                                                          ,'d.is_deleted' 	    => '0'
                                                                        )
                                                ,'search_by'    =>  ' AND d.discount_from <= CURRENT_DATE AND d.discount_to >= CURRENT_DATE '
                                                ,'return_type'	=>	'single'
                                              );
                          $DISCOUNT = $dblms->getRows(DISCOUNT.' AS d ', $condition);
                                                  
                          $currentDate    = date('Y-m-d');
                          $startDate      = $DISCOUNT['discount_from'];
                          $endDate        = $DISCOUNT['discount_to'];
                          $admoff_amount  = (__COUNTRY__ == 'pk' ? $ADMISSION_OFFERING['admoff_amount'] : $ADMISSION_OFFERING['admoff_amount_in_usd']);
                          $currency_code  = (__COUNTRY__ == 'pk' ? 'PKR' : 'USD');
                          $discount_web   = 0;
                          $discount_org   = 0;

                          // WEBSITE DISCOUNT
                          if($DISCOUNT){
                            if ($currentDate >= $startDate && $currentDate <= $endDate) {
                              if ($DISCOUNT['discount_type'] == 1) {
                                $discount_web = $DISCOUNT['discount'];
                              } else if ($DISCOUNT['discount_type'] == 2) {
                                $discount_web = ($admoff_amount * ($DISCOUNT['discount'] / 100) );
                              }
                            }
                          }

                          // ORG DISCOUNT
                          if($ORGANIZATIONS){
                            if (date('Y-m-d',strtotime($ORGANIZATIONS['org_link_to'])) >= $currentDate) {
                              $discount_org = (($ORGANIZATIONS['org_percentage']/100)*$admoff_amount);
                            }
                          }

                          // APPLY GREATER DISCOUNT
                          if ($discount_org > $discount_web) {
                            $admoff_amount -= $discount_org;
                          } elseif ($discount_web > $discount_org) {
                            $admoff_amount -= $discount_web;
                          } else {                              
                            $admoff_amount -= $discount_web;
                          }
                          $total += $admoff_amount;

                          if (isset($_POST['ref_hash']) && !empty($_POST['ref_hash'])) {
                            $ref_array        = explode(',', get_dataHashingOnlyExp($_POST['ref_hash'], false));
                            $curs_href        = cleanvars($ref_array[0]);
                            $ref_percentage   = cleanvars($ref_array[1]);
                            $ref_id           = cleanvars($ref_array[2]);
                            $adm_email        = cleanvars($ref_array[3]);
                            $condition = array(
                                                 'select'       =>  'r.ref_percentage'
                                                ,'join'         =>  'INNER JOIN '.COURSES.' AS c ON c.curs_id = "'.$ADMISSION_OFFERING['admoff_degree'].'"
                                                                     INNER JOIN '.REFERRAL_TEACHER_SHARING.' AS rt ON rt.id_ref = r.ref_id AND FIND_IN_SET("'.$adm_email.'", rt.std_emails) AND rt.ref_shr_status = "1" AND rt.is_deleted = "0"
                                                                     LEFT JOIN '.ADMINS.' AS a ON FIND_IN_SET(a.adm_email, rt.std_emails) AND a.adm_status = "1" AND a.is_deleted = "0"'
                                                ,'where'        =>  array(
                                                                             'r.is_deleted' =>	0
                                                                            ,'r.ref_status' =>	1
                                                                            ,'r.ref_id'     =>	$ref_id
                                                                        )
                                                ,'group_by'     =>  ' a.adm_id '
                                                ,'search_by'    =>  ' AND r.ref_date_time_from < "'.date('Y-m-d G:i:s').'" AND r.ref_date_time_to > "'.date('Y-m-d G:i:s').'" AND FIND_IN_SET("'.cleanvars($ADMISSION_OFFERING['admoff_degree']).'", r.id_curs)'
                                                ,'return_type'  =>  'single'
                            );
                            $REFERRAL_CONTROL = $dblms->getRows(REFERRAL_CONTROL.' AS r', $condition, $sql);
                            if ($REFERRAL_CONTROL) {
                              $admoff_amount = ($admoff_amount - ($admoff_amount * ($REFERRAL_CONTROL['ref_percentage'] / 100) ));
                            }
                            $total += $admoff_amount;
                          }
                          
                          if($admoff_amount > 0){
                            $priceFound = true;
                          }

                          // NAME, PHOTO
                          $photo = SITE_URL_PORTAL.'uploads/images/default_curs.jpg';
                          if($challanFor['admoff_type'] == 1){
                            $name = $ADMISSION_OFFERING['prg_name'];
                            $file_url = SITE_URL_PORTAL.'uploads/images/programs/'.$ADMISSION_OFFERING['prg_photo'];
                          }elseif($challanFor['admoff_type'] == 2){
                            $name = $ADMISSION_OFFERING['mas_name'];
                            $file_url = SITE_URL_PORTAL.'uploads/images/admissions/master_track/'.$ADMISSION_OFFERING['mas_photo'];
                          }elseif($challanFor['admoff_type'] == 3 || $challanFor['admoff_type'] == 4){
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
                                      <img src="'.SITE_URL.'assets/img/icon/icon-01.svg" alt="">
                                      <p>6</p>
                                    </div>
                                    <div class="course-view d-flex align-items-center">
                                      <img src="'.SITE_URL.'assets/img/icon/timer-start.svg" alt="">
                                      <p>6</p>
                                    </div>
                                  </div>
                                  '.get_LeanerType($ADMISSION_OFFERING['id_type']).'
                                </div>
                              </div>                              
                              <input type="hidden" class="removable" name="id" value="'.$ADMISSION_OFFERING['admoff_degree'].'">
                              <input type="hidden" class="removable" name="enroll_id" value="'.$enroll_id.'">
                              <input type="hidden" class="removable" name="type" value="'.$challanFor['admoff_type'].'">
                              <input type="hidden" class="removable" name="learn_type" value="'.$ADMISSION_OFFERING['id_type'].'">
                              <input type="hidden" class="removable" name="name" value="'.$name.'">
                              <input type="hidden" class="removable enroll_amount" name="amount" value="'.$admoff_amount.'">
                            </td>
                            <td class="text-center">'.get_enroll_type($challanFor['admoff_type']).'</td>
                            <td class="text-center">'.$currency_code.' '.$admoff_amount.'</td>
                            <td class="text-center"><a href="javascript:;" onclick="remove_item(this)"><i class="fa fa-trash"></i></a></td>
                          </tr>';
                        }
                        echo '
                        <tr>
                          <th>';
                            if($priceFound == true){
                              echo'
                              <div class="form-switch text-primary">
                                <input class="form-check-input me-2" id="have_cpn_code" name="have_cpn_code" type="checkbox">
                                <label class="form-check-label mb-0" for="have_cpn_code">Have Coupon Code?</label>
                              </div>';
                            }
                            echo'
                          </th>
                          <th class="text-end">
                            Grand Total:
                            <input type="hidden" id="total_amount" name="total_amount" value="'.$total.'">
                          </th>
                          <th class="text-center" id="grand_total_display">'.$currency_code.' '.$total.'</th>
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
                  <a href="'.SITE_URL.$_POST['url'].'" class="btn btn-wish w-100 mr-1"><i class="fa fa-chevron-left" aria-="true"></i> Back</a>
                </div>                      
                <div class="col">
                  <button name="enrolled_learn_free" type="submit" class="btn btn-enroll w-100">Enroll Now</button>
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

  // Sum all fields with class "enroll_amount"
  var total = 0;
  $(".enroll_amount").each(function () {
    var value = parseFloat($(this).val()) || 0; // Parse the value or default to 0 if invalid
    total += value;
  });

  // Update the field with id "total_amount"
  $("#total_amount").val(total.toFixed(2)); // Ensures the value is formatted with 2 decimal places

  // Update the grand total display
  var currencyCode = "'.$currency_code.'"; // Replace "USD" with your dynamic currency code if necessary
  $("#grand_total_display").text(`${currencyCode} ${total.toFixed(2)}`);

  var Inputs = $(\'input[type="hidden"].removable\');
  if (Inputs.length === 0) {
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