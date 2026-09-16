<?php
$condition = array ( 
    'select' 		=>	'std_privacy_profile,std_privacy_courses'
   ,'where' 		=>	array( 
                               'std_id' 	    => cleanvars($_SESSION['userlogininfo']['STDID']) 
                           ) 
   ,'return_type'	=>	'single'
); 
$row = $dblms->getRows(STUDENTS, $condition);
echo '
<div class="checkout-form personal-address secure-alert">
    <div class="personal-info-head">
        <h4>Profile settings</h4>
        <p>These controls give you the ability to customize what areas of your profile others are able to see.</p>
    </div>
    <form method="post">
        <div class="form-check form-switch check-on">
            <input class="form-check-input" name="std_privacy_profile" type="checkbox" '.($row['std_privacy_profile'] ? 'checked' : '').'>
            <label class="form-check-label" >Others can see you profile?</label>
        </div>
        <div class="form-check form-switch check-on">
            <input class="form-check-input" name="std_privacy_courses" type="checkbox" '.($row['std_privacy_courses'] ? 'checked' : '').'>
            <label class="form-check-label" >Others can see you courses?</label>
        </div>
        <div class="update-profile save-social text-end">
            <button type="submit" name="social_privacy" class="btn btn-primary">Save Profile Privacy</button>
        </div>
    </form>
</div>
';

?>