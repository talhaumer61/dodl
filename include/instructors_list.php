<?php
echo'
<section class="course-content">
  <div class="container">
    <div class="row">
      <div class="col">
        <div class="showing-list">
          <div class="row">
            <div class="col-lg-9">
            </div>
            <div class="col-lg-3">
              <div class="show-filter add-course-info">
                <form action="#">
                  <div class="row gx-2 align-items-right">
                    <div class="col-md-12 col-item">
                      <div class="search-group">
                        <i class="feather-search"></i>
                        <input type="text" oninput="call_ajax()" data-search class="form-control" placeholder="Search...."/>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div id="change"></div>
      </div>
    </div>
  </div>
</section>
<script>
  $(document).ready(function(){
    call_ajax();
  })
  sessionStorage.removeItem("pagination"); 
  function call_ajax(page=1,limit = 8){
    page_url = "'.SITE_URL.'include/instructor/table.php";
    searchval = [];
    $("[data-search]").each(function() {
      searchval.push($(this).val());
    });
    searchcb = {};
    $("[data-search-cb]:checked").each(function() {
      let key = $(this).data("search-cb");
      let cbval = $(this).val();
      if (!searchcb[key]) {
        searchcb[key] = [];
      }
      searchcb[key].push(cbval);
    });
    console.log(searchcb);
    if(limit != 8) {
      let pagination = {"limit" : limit}
      sessionStorage.setItem("pagination",JSON.stringify(pagination));
    } 
    if(sessionStorage.getItem("pagination") ){
      pagination = JSON.parse(sessionStorage.getItem("pagination"));
      limit = (pagination["limit"] ? pagination["limit"] : limit);
    }
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