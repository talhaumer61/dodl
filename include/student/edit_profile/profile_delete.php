<?php
$condition = array ( 
    'select' 		=>	'std_privacy_profile,std_privacy_courses'
   ,'where' 		=>	array( 
                               'std_id' 	    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                           ) 
   ,'return_type'	=>	'single'
); 
$row = $dblms->getRows(STUDENTS, $condition);
include 'query.php';
echo '
<div class="checkout-form personal-address">
    <div class="personal-info-head">
        <h4>Warning</h4>
        <p>If you close your account, you will be unsubscribed from all your 0 courses, and will lose access forever.</p>
    </div>
    <div class="form-group">
        <label for="">Enter your password</label>
        <input type="password" name="std_pass" class="form-control" >    
    </div>
    <div class="un-subscribe p-0">
        <button id="delete-profile-btn" class="btn btn-danger">Delete My Account</button>
    </div>
</div>
<script>
$("#delete-profile-btn").on("click",function () {
    std_password = $("input[name=\'std_pass\']").val();
    $.ajax({
        url: "'.SITE_URL.'include/ajax/check_userpass.php", 
        type: "POST",
        data : {std_password},
        dataType : "json",
        success: function(response) {
            if(response.status == "success"){
                confirm_modal(\''.SITE_URL.CONTROLER.'/conform\');
            }
            else{
                $(this).attr("onclick","");
                swal( {
                    title: "Password",
                    text: "Password Not Match!",
                    type: "warning"
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: " + status, error);
        }
    });
});
</script>
';

?>