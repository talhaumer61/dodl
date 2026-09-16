<?php
echo'
<div class="checkout-form personal-address add-course-info">
    <div class="showing-list">
        <div class="row">
            <div class="col-lg-8">
                <div class="personal-info-head">
                    <h4>Notifications</h4>
                    <p>All notifications and announcements are here.</p>
                </div>   
            </div>
            <div class="col-lg-4">
                <div class="show-filter add-course-info">
                    <form action="#">
                        <div class="row gx-2 align-items-center">
                            <div class="search-group">
                                <i class="feather-search"></i>
                                <input type="text" oninput="call_ajax()" data-search class="form-control" placeholder="Search..."/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="change"></div>    
</div>';
?>
<script>
    $(document).ready(function(){
        call_ajax();
    })
    function call_ajax(page=1,limit = 10){
        page_url = "include/student/edit_profile/notifications/table.php";
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
</script>