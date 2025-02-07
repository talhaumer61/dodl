<?php
require_once 'include/head.php';
require_once 'include/top_nav.php';
require_once 'include/breadcrumb.php';

if(!empty(ZONE)){
  $zoneJson = get_dataHashingOnlyExp(ZONE, false);
  $zoneArray = json_decode($zoneJson, true);

  $conditions = array ( 
                         'select'       =>	'gc.cert_id, gc.id_enroll, gc.id_std, gc.std_name, gc.cert_type, gc.cert_name, gc.curs_hours, gc.date_generated'
                        ,'where'        =>	array( 
                                                     'gc.is_deleted'  => '0'
                                                    ,'gc.id_std'      => cleanvars($zoneArray['id_std']) 
                                                    ,'gc.id_enroll'   => cleanvars($zoneArray['id_enroll']) 
                                                    ,'gc.cert_id'     => cleanvars($zoneArray['cert_id']) 
                                                )
                        ,'return_type'	=>	'single'
                      );
  $CERTIFICATE = $dblms->getRows(GENERATED_CERTIFICATES.' gc', $conditions);
  if($CERTIFICATE){
    echo'
    <section class="course-content">
      <div class="container">
        <div class="row">
          <div class="col-xl-12 col-lg-12 mb-2 col-md-12">
            <form action="" method="post">
              <div class="settings-widget">
                <div class="settings-inner-blk p-0">
                  <div class="sell-course-head comman-space text-center">
                    <h3>Certificate Verification</h3>
                    <p>Thank you for completing the course!</p>
                  </div>
                  <div class="comman-space pb-0">
                    <div class="settings-tickets-blk course-instruct-blk table-responsive">
                      <p>
                        This is to certify that <b>'.$CERTIFICATE['std_name'].'</b> has success fully completed the online course of <b>'.$CERTIFICATE['cert_name'].'</b> of duration <b>'.$CERTIFICATE['curs_hours'].' hours</b> on dated <b>'.date('d M, Y' , strtotime($CERTIFICATE['date_generated'])).'</b> conducted by <b>'.SITE_NAME.'</b>
                      </p>                    
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center">
                <a href="'.SITE_URL.'" class="btn btn-wish w-50"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to Home</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>';
  } else {
    echo'
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    
    <section class="course-content">
      <div class="container">
        <div class="row">
          <div class="text-center">
            <lord-icon
                src="https://cdn.lordicon.com/usownftb.json"
                trigger="loop"
                colors="primary:#0a5c15,secondary:#e88c30"
                style="width:150px;height:150px">
            </lord-icon>
            <h5>No certificate found for requested verification link</h5>
            <a href="'.SITE_URL.'" class="btn btn-wish w-50 mt-3"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to Home</a>
          </div>
        </div>
      </div>
    </section>';
  }
} else {
  echo'<script>window.location.href = "'.SITE_URL.'";</script>';
}
require_once 'include/footer.php';
?>