<?php
//Student Education
$condition = array ( 
                        'select' 		=>	'id, program, institute, year,grade'
                        ,'where' 		=>	array( 
                                                     'id_std'           => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                    ,'is_deleted'       => 0 
                                                ) 
                        ,'return_type'	=>	'all'
); 
$STUDENT_EDUCATIONS = $dblms->getRows(STUDENT_EDUCATIONS, $condition);

/*
//Course Skills 
$condition = array ( 
                        'select' 		=>	'skill_id, skill_name'
                        ,'where' 		=>	array( 
                                                     'skill_status'     => 1 
                                                    ,'is_deleted'       => 0 
                                                ) 
                        ,'return_type'	=>	'all'
); 
$skill = $dblms->getRows(COURSES_SKILLS, $condition);

//Student Intrests 
$condition = array ( 
                         'select' 		=>	'id_intrests'
                        ,'where' 		=>	array( 
                                                     'std_id'           => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                    ,'std_status'       => 1 
                                                    ,'is_deleted'       => 0 
                                                ) 
                        ,'return_type'	=>	'single'
); 
$intrests = $dblms->getRows(STUDENTS, $condition);
$my_intrests=explode(",",$intrests['id_intrests']);
*/
echo '
<div class="checkout-form personal-address add-course-info">
    <form method="post" autocomplete="off" enctype="multipart/form-data">';
        /*
        echo'
        <div class="col-lg-12">
            <div class="form-group">
                <label class="form-label">Interests</label>
                <select class="form-select select" name="id_intrests[]" multiple>';
                    foreach ($skill as $key => $value) {
                        echo'<option value="'.$value['skill_id'].'" '.(in_array($value['skill_id'],$my_intrests) ? 'selected' : '').'>'.$value['skill_name'].'</option>';
                    }
                    echo'
                </select>
            </div>
        </div>';
        */
        echo'
        <div class="col-lg-12">
            <label class="form-control-label">Education</label>
            <div class="card-body border">';
                if($STUDENT_EDUCATIONS){
                    foreach ($STUDENT_EDUCATIONS as $key => $value) {
                        echo '
                        <div class="row" id="rowResource">
                            <input value="'.$value['id'].'" type="hidden" name="id[]" id="fileInput">
                            <div class="col-lg-3 form-group">
                                <label class="form-label">Program </label>
                                <input type="text" value="'.$value['program'].'" class="form-control" name="program[]" />
                            </div>
                            <div class="col-lg-3 form-group">
                                <label class="form-label">Institute </label>
                                <input class="form-control" value="'.$value['institute'].'" type="text" name="institute[]" id="fileInput">
                            </div>
                            <div class="col-lg-3 form-group">
                                <label class="form-label">Grade</label>
                                <input class="form-control" value="'.$value['grade'].'" type="text" name="grade[]" id="fileInput">
                            </div>
                            <div class="col-lg-2 form-group">
                                <label class="form-label">Year</label>
                                <input type="text" value="'.$value['year'].'" class="form-control" name="year[]"/>
                            </div>';
                            if($key== 0){
                                echo'
                                <div class="col-md-1 d-flex align-items-center" style="margin-top: 12px;">
                                    <i class="fa fa-plus-circle" onclick="addinput(this)" style="font-size: 30px;"></i>
                                </div>';
                            }
                            else{
                                echo '<div class="col-md-1 d-flex align-items-center" style="margin-top: 12px;">
                                <i class="fa-regular fa-circle-xmark" onclick="removeinput(this.id)" id="addResource'.$key.'" style="font-size: 30px;"></i>
                            </div>';
                            }
                            echo'
                        </div>';
                    }
                }
                else {
                    echo'
                    <div class="row" id="rowResource">
                        <div class="col-lg-3 form-group">
                            <label class="form-label">Program </label>
                            <input type="text" class="form-control" name="program[]" />
                        </div>
                        <div class="col-lg-3 form-group">
                            <label class="form-label">Institute </label>
                            <input class="form-control" type="text" name="institute[]" id="fileInput">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label class="form-label">Grade</label>
                            <input class="form-control" type="text" name="grade[]" id="fileInput">
                        </div>
                        <div class="col-lg-2 form-group">
                            <label class="form-label">Year</label>
                            <input type="text" class="form-control" name="year[]"/>
                        </div>
                        <div class="col-md-1 d-flex align-items-center" style="margin-top: 12px;">
                            <i class="fa fa-plus-circle" onclick="addinput(this)" style="font-size: 30px;"></i>
                        </div>
                    </div>';
                }
                echo'  
            </div>
        </div>
        <div class="update-profile text-end">
            <button type="submit" name="update_edu_intrests" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<script>
var i = 0;
function addinput(p) {
    i++;
    var rowResource = $(p).closest("#rowResource");

    $.ajax({
        type    : "POST",
        url     : "include/ajax/education.php",
        data    : { "isrno" : i },
        success: function (response) {
            rowResource.after(response);
        }
    });
}
function removeinput(id) {
    var rowResource = $("#"+id);
    rowResource = rowResource.parent().parent();
    rowResource.html("");
}

</script>';
?>