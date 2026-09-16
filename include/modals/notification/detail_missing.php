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
if($rowDetail){
    foreach ($rowDetail as $key => $value) {
        if($value == '' && !is_int($key)){
            array_push($array_missing, moduleName($key));
        }
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

// NOTIFICATION
$today = date('Y-m-d');
$condition = array(
                     'select'       =>  'not_id, not_status, not_title, not_description, start_date, end_date, display_location, display_audience'
                    ,'where'        =>  array(
                                                 'is_deleted'   =>  0
                                                ,'not_status'   =>  1
                                            )
                    ,'search_by'    =>  ' AND start_date <= "'.$today.'" AND end_date >= "'.$today.'" AND FIND_IN_SET(2, display_location) AND FIND_IN_SET(1, display_audience)'
                    ,'order_by'     =>  'not_id DESC'
                    ,'return_type'  =>  'single'
                );
$rowNot = $dblms->getRows(NOTIFICATIONS, $condition, $sql);
if($rowNot && $_SESSION['NOTIFICATION'] == 1){
    unset($_SESSION['NOTIFICATION']);
    echo'
    <script src="assets/js/app.js"></script>
    <div class="modal fade" id="shownotificationModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger">
                    <h6 class="modal-title text-white" id="exampleModalLabel"><i class="fas fa-bell me-1"></i>Notification</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <div class="modal-body">
                    '.html_entity_decode(html_entity_decode($rowNot['not_description'])).'
                </div>
            </div>
        </div>
    </div>';
}

echo'
<script type="text/javascript">
    $(window).on("load", function() {
        // Show the first modal
        $("#notificationModal").modal("show");

        // Wait for a small delay to check if the modal is actually shown
        setTimeout(function() {
            if ($("#notificationModal").hasClass("show")) {
                // If the first modal is open, show the second when it\'s closed
                $("#notificationModal").on("hidden.bs.modal", function() {
                    $("#shownotificationModal").modal("show");
                });
            } else {
                // If the first modal is not open, show the second modal immediately
                $("#shownotificationModal").modal("show");
            }
        }, 500); // Adjust delay if needed
    });
</script>';
?>