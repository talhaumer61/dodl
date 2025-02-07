<?php
require_once 'head.php';
include 'signup/query.php';

if (!empty(ZONE)) {
    $condition  =   [ 
                        'select'        =>  'o.org_id, o.org_referral_link, o.org_link_to',
                        'join'          =>  'INNER JOIN '.ADMINS.' AS a ON a.adm_id = o.id_loginid',
                        'where' 	    =>  [
                                                'a.adm_logintype'       => 8,
                                                'a.adm_status'          => 1,
                                                'a.is_deleted'          => 0,
                                                'o.org_status'          => 1,
                                                'o.is_deleted'          => 0,
                                                'o.org_referral_link'   => cleanvars(ZONE),
                                            ],
                        'return_type'  =>  'single',
    ]; 
    $ORGANIZATIONS = $dblms->getRows(SKILL_AMBASSADOR.' AS o', $condition);
}
echo '
            <div class="row">
                <div class="col-md-6 login-bg">
                    <div class="owl-carousel login-slide owl-theme">
                        <div class="welcome-login">
                            <div class="login-banner">
                                <img src="'.SITE_URL.'assets/img/signup-img.png" class="img-fluid" alt="Logo">
                            </div>
                            <div class="mentor-course text-center">
                                <h2>Welcome to <br>'.SITE_NAME.'</h2>
                                <p>"Distance learning removes the barriers of location and time, opening the doors of education to a wider and more diverse audience."</p>
                            </div>
                        </div>
                        <div class="welcome-login">
                            <div class="login-banner">
                                <img src="'.SITE_URL.'assets/img/signup-img.png" class="img-fluid" alt="Logo">
                            </div>
                            <div class="mentor-course text-center">
                                <h2>Welcome to <br>'.SITE_NAME.'</h2>
                                <p>"Online learning is not a replacement for the traditional classroom, but rather a powerful tool that can supplement and enhance the learning experience."</p>
                            </div>
                        </div>
                        <div class="welcome-login">
                            <div class="login-banner">
                                <img src="'.SITE_URL.'assets/img/signup-img.png" class="img-fluid" alt="Logo">
                            </div>
                            <div class="mentor-course text-center">
                                <h2>Welcome to <br>'.SITE_NAME.'</h2>
                                <p>"The success of online learning depends on the active engagement of both learners and educators."</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 login-wrap-bg">
                    <div class="login-wrapper">
                        <div class="loginbox">
                            <div class="img-logo">
                                <img src="'.SITE_URL.'assets/img/logo/logo.png" class="img-fluid" alt="Logo">
                                <div class="back-home">
                                    <a href="'.SITE_URL.'">Back to Home</a>
                                </div>
                            </div>
                            <h1>Sign up to your account</h1>
                            <h5 class="text-danger">'.(isset($errorMsg) ?$errorMsg : '').'</h5>
                            <form method="POST" name="signup" autocomplete="off">';
                                if (date('Y-m-d', strtotime($ORGANIZATIONS['org_link_to'])) >= date('Y-m-d')) {
                                    echo'
                                    <div class="form-group">
                                        <label class="form-control-label">Referred From</label>
                                        <input type="text" class="form-control" value="'.$ORGANIZATIONS['org_referral_link'].'" name="is_referred" required="" readonly="">
                                        <input type="hidden" value="'.$ORGANIZATIONS['org_id'].'" name="org_id" required="" readonly="">
                                    </div>';
                                }
                                echo'
                                <div class="form-group">
                                    <label class="form-control-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="adm_fullname" name="adm_fullname" placeholder="Enter your Full Name" autocomplete="off" required '.((!empty($_SESSION['userlogininfo']['LOGINNAME']))? 'value="'.$_SESSION['userlogininfo']['LOGINNAME'].'" readonly=""': '').'>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">User Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="adm_username" placeholder="Enter your User Name" autocomplete="off" required '.((!empty($_SESSION['userlogininfo']['LOGINUSER']))? 'value="'.$_SESSION['userlogininfo']['LOGINUSER'].'" readonly=""': '').'>
                                    <small id="username_error"></small>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="adm_email" name="adm_email" placeholder="Enter your email address" autocomplete="off" required '.((!empty($_SESSION['userlogininfo']['LOGINEMAIL']))? 'value="'.$_SESSION['userlogininfo']['LOGINEMAIL'].'" readonly=""': '').'>
                                    <small id="email_error"></small><br>
                                    <a class="btn btn-primary btn-start FirstOTPSend" onclick="sendVerificationCode();">Send Code</a>
                                </div>
                                <div class="row">
                                    <div class="col">                                    
                                        <div class="form-group">
                                            <label class="form-control-label">Phone No <span class="text-danger">*</span></label>
                                            <input type="text" name="adm_phone" id="adm_phone" class="form-control phone" required>
                                        </div>
                                    </div>
                                </div>';
                                if (CONTROLER == 'signup') {
                                    echo'
                                    <div class="form-group OTPInput">
                                        <h3 class="text-center">Verify your account!</h3>
                                        <div class="verification-container">
                                            <input type="text" maxlength="1" class="verification-code firstInput" autofocus>
                                            <input type="text" maxlength="1" class="verification-code">
                                            <input type="text" maxlength="1" class="verification-code">
                                            <input type="text" maxlength="1" class="verification-code">
                                        </div>
                                        <h6 class="text-center incorrectOTP" style="display: none;"><span class="text-danger">Code does not match please send <span class="text-info" onclick="sendVerificationCode();" style="cursor: pointer;">again. <i class="fa fa-refresh"></i></span></span></h6>
                                        <h6 class="text-center OTPexpired" style="display: none;"><span class="text-danger">Your code is expired please send <span class="text-info" onclick="sendVerificationCode();" style="cursor: pointer;">again. <i class="fa fa-refresh"></i></span></span></h6>
                                    </div>';
                                }
                                if (!isset($_SESSION['userlogininfo']['LOGINIDA']) && empty($_SESSION['userlogininfo']['LOGINIDA'])) {
                                    echo'
                                    <div class="form-group">
                                        <label class="form-control-label">Password <span class="text-danger">*</span></label>
                                        <div class="pass-group" id="passwordInput">
                                            <input type="password" class="form-control pass-input" name="adm_userpass" placeholder="Enter your password" autocomplete="off" required>
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
                                    </div>';
                                }
                                echo'
                                <div class="edit-new-address wallet-method wallet-radio-blk d-flex align-items-center">';
                                    foreach (get_gendertypes() as $key => $value) {
                                        echo'
                                        <label class="radio-inline custom_radio me-4">
                                            <input type="radio" required name="adm_gender" value="'.$key.'" '.($_SESSION['userlogininfo']['STDGENDER'] == $key ? 'checked': '').'>
                                            <span class="checkmark"></span> <b>'.$value.'</b>
                                        </label>';
                                    }
                                    echo'
                                </div>
                                <div class="form-check remember-me">
                                    <label class="form-check-label mb-0">
                                        <input class="form-check-input" required type="checkbox" name="remember">
                                        <b>I agree to the <a href="'.SITE_URL.'terms">Terms of Service</a> and <a href="'.SITE_URL.'privacy">Privacy Policy.</a></b>
                                    </label>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-start signup_as_student" name="signup_as_student">Create your account</button>
                                </div>
                                <div class="google-bg text-center">
                                    <p class="mb-0">Already have an account? <a href="signin">Sign in </a></p>
                                </div>
                            </form>
                        </div>                    
                    </div>
                </div>
            </div>
        </div>
    <script src="'.SITE_URL.'assets/js/jquery-3.6.0.min.js"></script>
    <script src="'.SITE_URL.'assets/js/bootstrap.bundle.min.js"></script>
    <script src="'.SITE_URL.'assets/js/owl.carousel.min.js"></script>
    <script src="'.SITE_URL.'assets/js/validation.js"></script>
    <script src="'.SITE_URL.'assets/js/plugin.js"></script>
    <script src="'.SITE_URL.'assets/js/script.js"></script>
    <script>
        $("input[name=\'adm_username\']").on("input",function () {
            let inputValue = this.value;

            // Convert to lowercase and remove spaces
            inputValue = inputValue.toLowerCase().replace(/\s+/g, "");
            this.value = inputValue;
            if (inputValue.length >= 5) {
                $.ajax({
                    url: "'.SITE_URL.'include/ajax/check_username.php", 
                    type: "POST",
                    data : {username : this.value},
                    dataType : "json",
                    success: function(response) {
                        console.log(response)
                        if(response.status == "success")
                            $("#username_error").html("<span class=\'text-danger\'>User Name already exsist</span>");
                        else
                            $("#username_error").html("<span class=\'text-success\'>User Name available</span>");
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status, error);
                    }
                });
            }else{
                $("#username_error").html("<span class=\'text-danger\'>User Name should be Greater than 5 characters</span>");
            }
        });
        $("input[name=\'adm_email\']").on("input",function () {
            let inputValue = this.value;

            // Remove all spaces
            inputValue = inputValue.toLowerCase().replace(/\s+/g, "");
            this.value = inputValue;

            if(inputValue.length >= 5){
                const rep = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (rep.test(String(this.value).toLowerCase())) {
                    $.ajax({
                        url: "'.SITE_URL.'include/ajax/check_username.php", 
                        type: "POST",
                        data : {email : this.value},
                        dataType : "json",
                        success: function(response) {
                            console.log(response)
                            if(response.status == "success") {
                                $("#email_error").html("<span class=\'text-danger\'>Email already exsist</span>");
                                $(".FirstOTPSend").hide();
                            } else {
                                $("#email_error").html("<span class=\'text-success\'>Email available</span>");
                                $(".FirstOTPSend").show();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: " + status, error);
                        }
                    });
                } else {
                    $("#email_error").html("<span class=\'text-danger\'>Enter a valid email</span>");
                }
            }else{
                $("#email_error").html("<span class=\'text-danger\'>Email should be Greater than 5 characters</span>");
            }
        });
        $(".FirstOTPSend").hide();
        $(".signup_as_student").prop("disabled", true);
        $(".OTPInput").hide();
        const inputs = document.querySelectorAll(".verification-code");
        inputs.forEach((input, index) => {
            input.addEventListener("click", function() {
                input.removeAttribute("readonly");
            });
            input.addEventListener("input", function() {
                if (input.value.length > 0 && index < inputs.length - 1) {
                    input.setAttribute("readonly", "readonly"); // Make current field readonly
                    inputs[index + 1].removeAttribute("readonly");
                    inputs[index + 1].focus();
                } else if (index === inputs.length - 1) {
                    input.setAttribute("readonly", "readonly"); // Make the last field readonly
                    checkVerificationCode();
                }
            });
            input.addEventListener("keydown", function(e) {
                if (e.key === "Backspace" && index > 0 && input.value.length === 0) {
                    input.setAttribute("readonly", "readonly"); // Make current field readonly
                    inputs[index - 1].removeAttribute("readonly");
                    inputs[index - 1].focus();
                }
            });
        });
        function checkVerificationCode() {
            const code = Array.from(inputs).map(input => input.value).join("");
            if (code.length == 4) {
                $.ajax({
                     url    : "'.SITE_URL.'include/ajax/get_VerificationCodeSend.php"
                    ,type   : "POST"
                    ,data   : { 
                        "_method"       : "check-verification-code",
                        "adm_email"     : $("#adm_email").val(),
                        "adm_fullname"  : $("#adm_fullname").val(),
                        "user_code"     : code
                    }
                    ,success: function(response) {
                        if (response == "code-matched") {
                            Toastify({
                                newWindow: !0,
                                text: "Account Verification! Email verified",
                                gravity: "top",
                                position: "right",
                                className: "bg-success",
                                stopOnFocus: !0,
                                offset: "50",
                                duration: "2000",
                                close: true,
                                style: "style",
                            }).showToast();
                            $(".incorrectOTP").hide();
                            $(".OTPexpired").hide();
                            $(".FirstOTPSend").hide();
                            $(".OTPInput").hide();
                            $(".signup_as_student").prop("disabled", false);
                            $("#adm_email").prop("readonly", true);
                            $("#email_error").html("<span class=\'text-success\'>Email verified</span>");
                        } else if (response == "code-not-matched") {
                            Toastify({
                                newWindow: !0,
                                text: "Account Verification! Enter correct OTP code",
                                gravity: "top",
                                position: "right",
                                className: "bg-danger",
                                stopOnFocus: !0,
                                offset: "50",
                                duration: "2000",
                                close: true,
                                style: "style",
                            }).showToast();
                            $(".incorrectOTP").show();
                            $(".OTPexpired").hide();
                            $(".FirstOTPSend").hide();
                            $(".OTPInput").show();
                            $(".signup_as_student").prop("disabled", true);
                        } else if (response == "code-expired") {
                            Toastify({
                                newWindow: !0,
                                text: "Account Verification! OTP code is expired",
                                gravity: "top",
                                position: "right",
                                className: "bg-danger",
                                stopOnFocus: !0,
                                offset: "50",
                                duration: "2000",
                                close: true,
                                style: "style",
                            }).showToast();
                            $(".incorrectOTP").hide();
                            $(".OTPexpired").show();
                            $(".FirstOTPSend").hide();
                            $(".signup_as_student").prop("disabled", true);
                            $(".OTPInput").show();
                        }
                    }
                });
            }
        }
        function sendVerificationCode() {
            $.ajax({
                 url    : "'.SITE_URL.'include/ajax/get_VerificationCodeSend.php"
                ,type   : "POST"
                ,data   : { 
                    "_method"       : "send-verification-code",
                    "adm_email"     : $("#adm_email").val()
                }
                ,success: function(response) {
                    if (response == "code-sent") {
                        Toastify({
                            newWindow: !0,
                            text: "Account Verification! New verification code sent",
                            gravity: "top",
                            position: "right",
                            className: "bg-success",
                            stopOnFocus: !0,
                            offset: "50",
                            duration: "2000",
                            close: true,
                            style: "style",
                        }).showToast();
                        $("#email_error").html("");
                        $(".OTPInput").show();
                        $(".incorrectOTP").hide();
                        $(".OTPexpired").hide();
                        $(".FirstOTPSend").hide();
                        $(".verification-code").each(function() {
                            $(this).val("");
                        });
                        $(".firstInput").focus();
                        setTimeout(function() {
                            $(".OTPexpired").show();
                        }, 180000);
                    }
                }
            });
        }
    </script>
</body>
</html>';
?>