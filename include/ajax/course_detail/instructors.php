<?php
// ALLOCATED TEACHERS
$condition = array ( 
                         'select'       =>  'a.adm_photo, e.emply_name, e.emply_href, e.emply_gender, e.emply_id, e.emply_photo, d.dept_name, des.designation_name, COUNT(alt2.id_curs) as t_curs'
                        ,'join'         =>  'INNER JOIN '.ALLOCATE_TEACHERS.' alt2 on FIND_IN_SET(e.emply_id,alt2.id_teacher)
                                             LEFT JOIN '.ADMINS.' a ON a.adm_id = e.emply_loginid
                                             LEFT JOIN '.DEPARTMENTS.' d on e.id_dept = d.dept_id
                                             LEFT JOIN '.DESIGNATIONS.' des on e.id_designation = des.designation_id'
                        ,'where' 		=>  array( 
                                                     'alt2.id_curs'     =>  $_GET['curs_id']
                                                    ,'e.emply_status'   =>  1
                                                    ,'e.is_deleted'     =>  0
                                                )
                        ,'group_by'     =>  'e.emply_id'
                        ,'return_type'	=>  'all'
                    );
$EMPLOYEES = $dblms->getRows(EMPLOYEES.' e', $condition);
echo'
<div class="tab-pane fade show" id="instructors">
    <div class="card overview-sec">
        <div class="card-body">
            <div class="row">';
                if($EMPLOYEES){
                    foreach ($EMPLOYEES as $value) :
                        // CHECK FILE EXIST
                        if($value['emply_gender'] == '2'){
                            $photo = SITE_URL_PORTAL.'uploads/images/default_female.jpg';
                        } else {            
                            $photo = SITE_URL_PORTAL.'uploads/images/default_male.jpg';
                        }
                        if(!empty($value['adm_photo'])){
                            $photo = SITE_URL_PORTAL.'uploads/images/admin/'.$value['adm_photo'];
                        } else if (!empty($value['emply_photo'])){
                            $photo = SITE_URL_PORTAL.'uploads/images/employees/'.$value['emply_photo'];
                        }
                        $emply_name = $value['emply_name'];
                        if(strlen($emply_name) > 20){
                            $emply_name = substr($emply_name,0,20).'...';
                        }
                        echo'
                        <div class="col-6">
                            <div class="card p-2 my-1 shadow">
                                <div class="col-md-12 instructor-wrap border-bottom-0 m-0">
                                    <div class="about-instructor align-items-center mb-0">
                                        <div class="me-3">
                                            <img src="'.$photo.'" width="100" height="100" alt="img" class="circle p-1"/>
                                        </div>
                                        <div class="instructor-detail me-3">
                                            <h5><a href="'.SITE_URL.'instructors/'.$value['emply_href'].'">'.$emply_name.'</a></h5>
                                            <p>'.$value['designation_name'].'</p>
                                            <p>'.$value['dept_name'].'</p>
                                            <p>'.$value['t_curs'].' Courses</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    endforeach;
                } else {
                    echo '<div class="alert alert-danger mb-0 text-center"><strong>No data found...!</strong></div>';
                }
                echo '
            </div>
        </div>
    </div>
</div>';