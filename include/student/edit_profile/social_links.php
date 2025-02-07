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
echo '
<div class="checkout-form personal-address add-course-info">
    <div class="personal-info-head">
        <h4>Social Profile</h4>
        <p>Edit your social media links.</p>
    </div>
    <form method="post">
        <div class="row">';
            foreach (get_social_links() as $key => $value) {
                echo'
                <div class="col-lg-12">
                    <div class="form-group">
                        <label class="form-control-label">'.$value['name'].'</label>
                        <input type="text" name="social['.$key.']" value="'.(isset($social[$key]) ? $social[$key] : '').'" class="form-control" placeholder="'.$value['name'].' Profile URL">
                    </div>
                </div>';
            }
            echo '<div class="update-profile save-social text-end">
                <button type="submit" name="social_profile" class="btn btn-primary">Save Social Profile</button>
            </div>
        </div>
    </form>
</div>';
?>