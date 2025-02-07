<?php
$condition = array ( 
                         'select' 		=>	'a.adm_fullname, a.adm_username, a.adm_phone, a.adm_email, s.std_dob, s.id_country, s.std_address_1, s.std_address_2, s.city_name, s.std_postal_address, s.std_about, s.std_gender'
                        ,'join' 		=>	'INNER JOIN '.STUDENTS.' s ON s.std_loginid = a.adm_id' 
                        ,'where' 		=>	array( 
                                                     'adm_status'       => '1'
                                                    ,'adm_username'     => cleanvars($_SESSION['userlogininfo']['LOGINUSER']) 
                                                ) 
                        ,'return_type'	=>	'single'
                    ); 
$row = $dblms->getRows(ADMINS.' a', $condition);
echo '
<form method="post" enctype="multipart/form-data">
    <div class="course-group mb-0 d-flex">
        <div class="course-group-img d-flex align-items-center">
            <label for="profile-pic">
                <a>
                    <figure>
                        <img src="'.$_SESSION['userlogininfo']['LOGINPHOTO'].'" id="show-profile-pic" alt class="img-fluid"/>
                        <figcaption><i class="feather-camera"></i></figcaption>
                    </figure>
                </a>
            </label>
            <input type="file" style="display:none;z-index:999;" accept="image/png,image/jpeg,image/jpg" name="profile_pic" id="profile-pic">
            <div class="course-name">
                <h4><a href="'.SITE_URL.'profile">Your avatar</a></h4>
                <p>PNG or JPG no bigger than 800px wide and tall.</p>
            </div>
        </div>
        <div class="profile-share d-flex align-items-center justify-content-center" >
            <button type="submit" name="update_profile_pic" class="btn btn-success profile-btn">Update</button>
            <a href="javascript:;" class="btn btn-danger profile-btn profile-btn-delete">Delete</a>
        </div>
    </div>
</form>
<div class="checkout-form personal-address add-course-info">
    <div class="personal-info-head">
        <h4>Personal Details</h4>
        <p>Edit your personal information and address.</p>
    </div>
    <form method="post" autocomplete="off">
        <div class="row">
            <div class="col form-group">
                <label class="form-control-label">Username</label>
                <input type="text" class="form-control" value="'.$row['adm_username'].'" readonly/>
            </div>
            <div class="col form-group">
                <label class="form-control-label">Email</label>
                <input type="text" class="form-control" name="email" value="'.$row['adm_email'].'" readonly/>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="col form-group">
                    <label class="form-control-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="full_name" value="'.$row['adm_fullname'].'" placeholder="Enter your full Name" required/>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="form-control-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control phone" name="phone_no" value="'.$row['adm_phone'].'" placeholder="Enter your Phone" required/>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select class="form-select select country-select" name="std_gender" required>
                        <option value="">Select Gender</option>';
                        foreach (get_gendertypes() as $key => $value) {
                            echo'<option value="'.$key.'" '.($key == $row['std_gender'] ? 'selected':'').'>'.$value.'</option>';
                        }
                        echo'
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-control-label">Birthday <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="dob" value="'.$row['std_dob'].'"  placeholder="Birth of Date" required/>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-label">Country <span class="text-danger">*</span></label>
                    <select class="form-select select country-select" name="country" required>
                        <option value="">Select country</option>';
                        foreach ($country as $key => $value) {
                            echo'<option value="'.$key.'" '.(!empty($row['id_country']) && $key==$row['id_country'] ? 'selected':'').'>'.$value.'</option>';
                        }
                        echo'
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-control-label">City <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="city" value="'.$row['city_name'].'" placeholder="Enter your City" required/>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-control-label">ZipCode <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="zip_code" value="'.$row['std_postal_address'].'" placeholder="Enter your Zipcode" required/>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-control-label">Permanent Address <span class="text-danger">*</span></label>
                    <textarea name="address_1" class="form-control">'.$row['std_address_1'].'</textarea>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-control-label">Postal Address (Optional)</label>
                    <textarea name="address_2" class="form-control">'.$row['std_address_2'].'</textarea>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="form-control-label">About You</label>
                    <textarea name="about" class="form-control">'.$row['std_about'].'</textarea>
                </div>
            </div>
            <div class="update-profile text-end">
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </div>
        </div>
    </form>
</div>';
?>