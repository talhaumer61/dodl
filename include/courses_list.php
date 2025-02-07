<?php
$condition = array ( 
                       'select'       =>	'd.dept_id,d.dept_name,COUNT(ao.admoff_degree) as t_curs'
                      ,'join'         =>  'INNER JOIN '.COURSES.' c on c.id_dept=d.dept_id
                                           INNER JOIN '.ADMISSION_OFFERING.' ao on ao.admoff_degree=c.curs_id
                                           INNER JOIN '.EMPLOYEES.' AS e ON e.id_dept = d.dept_id
                                           INNER JOIN '.ALLOCATE_TEACHERS.' AS ca ON FIND_IN_SET(e.emply_id, ca.id_teacher)
                                           INNER JOIN '.COURSES_CATEGORIES.' AS cc ON FIND_IN_SET(cc.cat_id, c.id_cat) AND cc.id_type = '.$_SESSION['id_type'].''
                      ,'where'        =>	array( 
                                                ' d.dept_status'    => '1'
                                                ,'d.is_deleted'     => '0'
                                                ,'c.curs_status'    => '1'
                                                ,'c.is_deleted'     => '0'
                                                ,'ao.admoff_type'   => '3'
                                              ) 
                      ,'group_by'     =>  'd.dept_id'
                      ,'return_type'	=>	'all'
                    ); 
$departments = $dblms->getRows(DEPARTMENTS.' d', $condition);
echo'
<div class="col-lg-9">
  <div class="showing-list">
    <div class="row">
      <div class="col-lg-6">
        <div class="d-flex align-items-center">
          <div class="view-icons">
            <a href="'.SITE_URL.CONTROLER.'" class="list-view active"><i class="feather-list"></i></a>
          </div>
          <div class="show-result">
            <h4>'.((CONTROLER == 'courses' || CONTROLER == 'certificate-courses') ? 'Certificate Courses' : 'Trainings').' List</h4>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="show-filter add-course-info">
          <form action="#">
            <div class="row gx-2 align-items-center">
              <div class="col col-item">
                <div class="search-group">
                  <i class="feather-search"></i>
                  <input type="text" oninput="call_ajax()" data-search class="form-control" placeholder="Search our courses"/>
                </div>
              </div>';
              if(CONTROLER != 'trainings'){
                echo'
                <div class="col col-item">
                  <div class="form-group select-form mb-0">
                    <select class="select select" data-search-cb="c.id_dept" onchange="call_ajax()" name="sellist1">
                      <option value="">Choose Faculty</option>';
                      foreach ($departments as $value) {
                        echo '<option value="'.$value['dept_id'].'" >'.$value['dept_name'].' ('.$value['t_curs'].')</option>';
                      }
                      echo '
                    </select>
                  </div>
                </div>';
              }
              echo'
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div id="change"></div>
</div>
<script>
    $(document).ready(function(){
        call_ajax();
    })
    function call_ajax(page=1,limit = 10){
        page_url = "'.SITE_URL.'include/courses/table.php";
        searchval = [];
        $("[data-search]").each(function() {
            searchval.push($(this).val());
        });
        searchcb = {};';
        if(!empty($view)){
          echo 'searchcb = {"c.id_cat":["'.$view.'"]}';
        }
        echo'
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
        let formData = new FormData();
        formData.append("word", JSON.stringify(searchval)); 
        formData.append("page", page);
        formData.append("limit", limit);
        formData.append("control", "'.CONTROLER.'");
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