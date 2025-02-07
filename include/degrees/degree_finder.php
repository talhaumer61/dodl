<?php
require_once 'include/wishlist/query.php';
$condition = array ( 
                       'select' 		  =>	'pc.cat_id, pc.cat_href, pc.cat_name, pc.cat_description'
                      ,'join'         =>  'INNER JOIN '.PROGRAMS.' p ON p.id_cat = pc.cat_id
                                           INNER JOIN '.ADMISSION_PROGRAMS.' ap ON ap.id_prg = p.prg_id
                                           INNER JOIN '.ADMISSION_OFFERING.' a ON a.admoff_degree = ap.id AND a.admoff_type = 1'
                      ,'where' 		    =>	array( 
                                                   'pc.cat_status'   =>  1 
                                                  ,'pc.is_deleted'   =>  0 
                                              ) 
                      ,'order_by'     =>  'pc.cat_id DESC'
                      ,'return_type'	=>	'all'
                    );
$PROGRAMS_CATEGORIES = $dblms->getRows(PROGRAMS_CATEGORIES.' pc', $condition);
// SEARCH
echo'
<div class="page-banner">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-12">
        <h1 class="mb-0">Take your career to the next level with an online degree</h1>
      </div>
    </div>
  </div>
</div>
<div class="page-content" style="margin-top:-8rem; background: none;">
  <div class="container">
    <div class="row">
      <div class="col-md-12 mx-auto">
        <div class="support-wrap add-course-info">
          <div class="checkout-form">
            <form action="#">
              <div class="row">
                <div class="col-md-6 col-item">
                  <input type="text" oninput="call_ajax()" data-search class="form-control" placeholder="Search Degree"/>
                </div>
                <div class="col-md-6 col-lg-6 col-item">
                  <div class="form-group select-form mb-0">
                    <select class="form-select select" data-search-cb="p.id_cat" onchange="call_ajax()" name="sellist1">
                      <option value="">Choose Faculty</option>';
                      foreach ($PROGRAMS_CATEGORIES as $cat) {
                        echo '<option value="'.$cat['cat_id'].'" >'.$cat['cat_name'].'</option>';
                      }
                      echo'
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <section class="mt-5">
      <h4 align="center" class="p-5 mb-0 text-danger bg-white border rounded">Coming Soon...!</h4>
      <div class="container" id="_change">';
        // DATA WILL APPEAR HERE
        echo'       
      </div>
    </section>
  </div>
</div>';

// JUMP TO DEGREE CATEGORY
echo'
<section class="course-content">
  <div class="container">
    <div class="notify-sec">
      <div class="row">
        <h5>Find the right online degree program for you</h5>';
        foreach ($PROGRAMS_CATEGORIES as $cat) {
          echo'
          <div class="col-md-4">
            <div class="notify-item shadow">
              <div class="notify-detail">
                <h6>
                  <a href="'.SITE_URL.'degrees/'.$cat['cat_href'].'">'.$cat['cat_name'].'</a>
                </h6>
                <div class="line-clamp-2">'.html_entity_decode($cat['cat_description']).'</div>
                <br>
                <a href="'.SITE_URL.'degrees/'.$cat['cat_href'].'"><i class="fa fa-arrow-right"></i> View all programs</a>
              </div>
            </div>
          </div>';
        }
        echo'
      </div>
    </div>
  </div>
</section>';

// BROWSE BY CATEGORY
/*echo'
<div class="page-content">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="title-sec">
          <h5>Explore more degrees by category</h5>
        </div>
        <div class="row">
          <div class="col-lg-4 col-md-6">
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-01.jpg" alt />
                </div>
                <h5>Logo Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-02.jpg" alt />
                </div>
                <h5>Business Cards & Stationery</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-03.jpg" alt />
                </div>
                <h5>Brochure Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-04.jpg" alt />
                </div>
                <h5>Social Media Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-05.jpg" alt />
                </div>
                <h5>Graphics for Streamers</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-06.jpg" alt />
                </div>
                <h5>Photoshop Editing</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-07.jpg" alt />
                </div>
                <h5>Brand Style Guides</h5>
              </div>
              <span class="cat-count">25</span>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-08.jpg" alt />
                </div>
                <h5>Illustration</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-09.jpg" alt />
                </div>
                <h5>Flyer Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-10.jpg" alt />
                </div>
                <h5>Icon Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-11.jpg" alt />
                </div>
                <h5>Packaging & Label Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-12.jpg" alt />
                </div>
                <h5>Presentation Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-13.jpg" alt />
                </div>
                <h5>Game Art</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-14.jpg" alt />
                </div>
                <h5>Pattern Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-15.jpg" alt />
                </div>
                <h5>Book Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-16.jpg" alt />
                </div>
                <h5>Invitation Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-17.jpg" alt />
                </div>
                <h5>UX Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
            <div class="category-box">
              <div class="category-title">
                <div class="category-img">
                  <img src="'.SITE_URL.'assets/img/category/category-06.jpg" alt />
                </div>
                <h5>Infographic Design</h5>
              </div>
              <div class="cat-count">
                <span>25</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>';
*/
echo'
<script>
  $(document).ready(function(){
    call_ajax();
  })

  function call_ajax(page=1,limit = 10){
    page_url  = "include/degrees/table.php";
    searchval = [];

    $("[data-search]").each(function() {
      searchval.push($(this).val());
    });

    searchcb = {};
    $("[data-search-cb]").each(function() {
      let key = $(this).data("search-cb");
      let value = $(this).val();
      if ($(this).is("select")) {
        if (!Array.isArray(value)) {
          value = value === null ? [] : [value];
        }
        if (value.filter(Boolean).length > 0 ) {
          if (!searchcb[key]) {
            searchcb[key] = [];
          }
          searchcb[key] = searchcb[key].concat(value.filter(Boolean));
        }
      } else if ($(this).is(":checked") && value !== undefined && value !== "") {
        if (!searchcb[key]) {
          searchcb[key] = [];
        }
        searchcb[key].push(value);
      }
    });

    // console.log(searchcb);
    let formData = new FormData();
    formData.append("word", JSON.stringify(searchval)); 
    formData.append("page", page);
    formData.append("limit", limit);
    formData.append("check", JSON.stringify(searchcb));
    $.ajax({
      url: page_url, 
      type : "POST",
      contentType: false,
      processData: false,
      data : formData,
      success: function(result){
        $("#change").html(result);
      }
    });
  }
</script>';
?>