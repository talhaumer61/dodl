<?php
$condition = array ( 
                         'select' 		=>	'a.adm_fullname as full_name, a.adm_username as user_name, a.adm_photo as profile_image, a.adm_phone as phone_no, a.adm_email as email, s.std_dob as date_of_birth, s.id_country as country, s.std_address_1 as permanent_address'
                        ,'join' 		=>	'INNER JOIN '.STUDENTS.' s ON s.std_loginid = a.adm_id' 
                        ,'where' 		=>	array( 
                                                     'adm_status'       => '1'
                                                    ,'adm_username'     => cleanvars($_SESSION['userlogininfo']['LOGINUSER']) 
                                                ) 
                        ,'return_type'	=>	'single'
                    ); 
$rowDetail = $dblms->getRows(ADMINS.' a', $condition);

$array_missing = array();
foreach ($rowDetail as $key => $value) {
    if($value == '' && !is_int($key)){
        array_push($array_missing, moduleName($key));
    }
}
$missing = implode(", ", $array_missing);
if($missing != '' && $_SESSION['SHOWNOTIFICATION'] == 1){
    unset($_SESSION['SHOWNOTIFICATION']);    
    echo'
    <script src="assets/js/app.js"></script>
    
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger">
                    <h6 class="modal-title text-white" id="exampleModalLabel"><i class="fas fa-user-edit me-1"></i>Complete your profile</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <div class="modal-body">
                    <b>Important details missing:</b>
                    <p style="font-size: 14px; padding: 1rem;" class="m-0">
                        Complete your profile by providing 
                        <i class="text-danger me-1 ms-1">"'.$missing.'"</i> 
                        Go to Account Setting
                        <i><a href="'.SITE_URL.'profile-detail" class="text-danger"> Click Here</a></i>
                    </p>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"><i class="far fa-times-circle me-1"></i>Skip for Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}
echo'
<script type="text/javascript">
    $(window).on("load",function(){
        $("#notificationModal").modal("show");
    });
</script>';
?>