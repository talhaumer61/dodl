<?php
$condition = array ( 
                         'select' 		=>	'std_sociallinks'
                        ,'where' 		=>	array( 
                                                    'std_id' 	    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                                                ) 
                        ,'return_type'	=>	'single'
                    ); 
$row = $dblms->getRows(STUDENTS, $condition);
$social = unserialize($row['std_sociallinks']);
echo'
<div class="checkout-form personal-address">
    <div class="personal-info-head">
        <h4>Change Password</h4>
        <p>We will email you a confirmation when changing your password, so please expect that email after submitting.</p>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <form action="" onsubmit="return validation()" method="post" id="save-password">
                <div class="form-group">
                    <label class="form-control-label">Current password</label>
                    <input type="password" name="std_password" class="form-control" placeholder="Current Password">
                </div>
                <div class="form-group">
                    <label class="form-control-label">New Password</label>
                    <div class="pass-group" id="passwordInput">
                        <input type="password" class="form-control pass-input" name="password" placeholder="Enter new password" autocomplete="off">
                        <span class="toggle-password feather-eye"></span>
                        <span class="pass-checked"><i class="feather-check"></i></span>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <span id="poor"></span>
                        <span id="weak"></span>
                        <span id="strong"></span>
                        <span id="heavy"></span>
                    </div>
                    <div id="passwordInfo"></div>
                </div>
                <div class="form-group">
                    <label class="form-control-label">Confirm Password</label>
                    <input type="password" name="c_password" placeholder="Confirm Password" class="form-control" >
                </div>
                <div class="update-profile save-password">
                    <button type="submit" name="change_password" class="btn btn-primary">Save Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function validation() {
    password = $("input[name=\'password\']").val();
    c_password = $("input[name=\'c_password\']").val();
    std_password = $("input[name=\'std_password\']").val();
    if(password && c_password && std_password){
        if(password == c_password){
            var rstatus = false;
            $.ajax({
                url: "'.SITE_URL.'include/ajax/check_userpass.php", 
                type: "POST",
                data : {std_password},
                dataType : "json",
                success: function(response) {
                    if(response.status == "success"){
                        $.ajax({
                            url : "'.SITE_URL.CONTROLER.'",
                            type : "post",
                            data : {password,"change_password" : true},
                            success : function(){
                                swal( {
                                    title: "Successfully",
                                    text: "Successfully Change password",
                                    type: "success"
                                }, function () {
                                    location.reload();
                                } );
                            }
                        })
                    }
                    else{
                        swal({
                            title: "Password",
                            text: "Current Password Not Match!",
                            type: "warning"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status, error);
                }
            });
            return rstatus;
        }else{
            swal( {
                title: "Password",
                text: "Password and Conform password did not match!",
                type: "warning"
            });
            return false;
        }
    } else {
        swal( {
            title: "Fields",
            text: "All fields are required!",
            type: "warning"
        });
        return false;
    }
}
</script>';
?>