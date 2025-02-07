<?php
require_once 'include/head.php';
require_once 'include/top_nav.php';
require_once 'include/breadcrumb.php';

include_once 'query.php';

if (!isset($_POST['show_interest'])) {
  header("Location: ".SITE_URL."");
}else {
  echo'
  <div class="container">
    <div class="row">
      <div class="col-xl-12 col-lg-12 mb-2 col-md-12">	
        <div class="row">
          <div class="col-md-12">
            <form action="" method="post" autocomplete="off">
              <div class="settings-widget">
                <div class="settings-inner-blk p-0">
                  <div class="sell-course-head comman-space text-center">
                    <h3>'.moduleName(CONTROLER).'</h3>
                    <p>Stay tuned! Fill out our interest form to be the first to receive updates on this course. Don\'t miss the chance to explore this exciting offering!</p>
                  </div>
                  <div class="comman-space pb-0">
                    <input type="hidden" name="url" value="'.$_POST['url'].'">
                    <input type="hidden" name="id" value="'.$_POST['id'].'">
                    <input type="hidden" name="type" value="'.$_POST['type'].'">
                    <div class="row">
                        <div class="col form-group">
                          <label class="form-control-label">Name <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="name" value="'.$_SESSION['userlogininfo']['LOGINNAME'].'" required/>
                        </div>
                        <div class="col form-group">
                          <label class="form-control-label">Email <span class="text-danger">*</span></label>
                          <input type="email" class="form-control" name="email" value="'.$_SESSION['userlogininfo']['LOGINEMAIL'].'" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col form-group">
                          <label class="form-control-label">City <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="city" required/>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col form-group">
                        <label class="form-control-label">Reviews</label>
                        <textarea class="form-control" name="remarks"></textarea>
                      </div>
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
                    <button name="interest_submit" type="submit" class="btn btn-enroll w-100">Submit</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <hr>';
}
require_once 'include/footer.php';
?>