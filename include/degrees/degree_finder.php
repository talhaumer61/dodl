<?php
require_once 'include/wishlist/query.php';
require_once 'include/degrees/query.php';
// SEARCH
echo '
<style>
  /* Match Select2 container styling to standard form-control inputs */
  .select2-container--default .select2-selection--single,
  .select-form select.form-select,
  .select-form select.form-control {
    height: 42px !important;
    border: 1px solid #dce0eb !important;
    border-radius: 5px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    color: #495057 !important;
    background-color: #fff !important;
    display: flex !important;
    align-items: center !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
    padding-left: 0 !important;
    color: #495057 !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px !important;
    right: 10px !important;
  }

  .select2-container {
    width: 100% !important;
  }
</style>
<div class="page-content" style="background: none;">
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
                    <p>Stay tuned! Fill out our interest form to be the first to receive updates on this degree. Don\'t miss the chance to explore this exciting offering!</p>
                  </div>
                  <div class="comman-space pb-0">
                    <input type="hidden" name="url" value="'.(isset($_POST['url']) ? $_POST['url'] : '').'">
                    <input type="hidden" name="id" value="'.(isset($_POST['id']) ? $_POST['id'] : '').'">
                    <input type="hidden" name="type" value="'.(isset($_POST['type']) ? $_POST['type'] : '').'">
                    
                    <div class="row">
                        <div class="col-md-6 col-lg-6 form-group">
                          <label class="form-control-label">Name <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="name" value="'.$_SESSION['userlogininfo']['LOGINNAME'].'" required/>
                        </div>
                        <div class="col-md-6 col-lg-6 form-group">
                          <label class="form-control-label">Email <span class="text-danger">*</span></label>
                          <input type="email" class="form-control" name="email" value="'.$_SESSION['userlogininfo']['LOGINEMAIL'].'" required/>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 col-lg-6 form-group">
                          <label class="form-control-label">Whatsapp Number <span class="text-danger">*</span></label>
                          <input type="number" class="form-control" name="whatsapp" required/>
                        </div>
                        <div class="col form-group">
                          <label class="form-control-label">City <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="city" required/>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col form-group">
                          <label class="form-control-label">Faculty / Interest <span class="text-danger">*</span></label>
                          <div class="select-form">
                            <select class="form-control select degree-select" name="id_interest">
                              <option value="">Choose Faculty</option>';
                              foreach (get_degree_interests() as $key => $value) {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                              }
                              echo '
                            </select>
                          </div>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col form-group">
                        <label class="form-control-label">Reviews</label>
                        <textarea class="form-control" name="remarks"></textarea>
                      </div>
                    </div>                    
                    
                    <div class="row">
                        <div class="col form-group">
                          <div class="g-recaptcha" data-sitekey="'.CAPTCHA_SITE_KEY.'"></div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="container-fluid">
                <div class="update-profile row">
                  <div class="col">
                    <a href="'.SITE_URL.(isset($_POST['url']) ? $_POST['url'] : '').'" class="btn btn-wish w-100 mr-1"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
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
</div>';

echo'
<script>
  $(document).ready(function(){
  if ($(".select").length > 0) {
        $(".select").select2({
            minimumResultsForSearch: -1,
            width: "100%"
        });
    }
  })
</script>';
?>