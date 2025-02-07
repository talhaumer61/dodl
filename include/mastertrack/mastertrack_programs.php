<?php
include 'include/wishlist/query.php';
echo'
<div class="page-banner">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-12">
        <h1 class="mb-0">'.moduleName(CONTROLER).'</h1>
      </div>
    </div>
  </div>
</div>
<div class="page-content">
  <div class="container">  
    <div class="showing-list">
      <div class="row">
        <div class="col-lg-3">
        </div>
        <div class="col-lg-6">
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
    <h4 align="center" class="p-5 mb-0 text-danger bg-white border rounded">Coming Soon...!</h4>
    <div id="_change"></div>
  </div>
</div>

<script>
  $(document).ready(function(){
    call_ajax();
  })

  function call_ajax(page=1,limit = 10){
    page_url  = "include/mastertrack/table.php";
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