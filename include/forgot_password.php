<?php
        require_once 'head.php';
        include 'signup/query.php';
        echo '
        <div class="row">
            <div class="col-md-6 login-bg">
                <div class="owl-carousel login-slide owl-theme">
                    <div class="welcome-login">
                        <div class="login-banner">
                            <img src="'.SITE_URL.'assets/img/login-img.png" class="img-fluid" alt="Logo">
                        </div>
                        <div class="mentor-course text-center">
                            <h2>Welcome to <br>'.SITE_NAME.'</h2>
                            <p>"Distance learning removes the barriers of location and time, opening the doors of education to a wider and more diverse audience."</p>
                        </div>
                    </div>
                    <div class="welcome-login">
                        <div class="login-banner">
                            <img src="'.SITE_URL.'assets/img/login-img.png" class="img-fluid" alt="Logo">
                        </div>
                        <div class="mentor-course text-center">
                            <h2>Welcome to <br>'.SITE_NAME.'</h2>
                            <p>"Online learning is not a replacement for the traditional classroom, but rather a powerful tool that can supplement and enhance the learning experience."</p>
                        </div>
                    </div>
                    <div class="welcome-login">
                        <div class="login-banner">
                            <img src="'.SITE_URL.'assets/img/login-img.png" class="img-fluid" alt="Logo">
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
                        <div class="w-100">
                            <div class="img-logo">
                                <img src="'.SITE_URL.'assets/img/logo/logo.png" class="img-fluid" alt="Logo">
                                <div class="back-home">
                                    <a href="'.SITE_URL.'">Back to Home</a>
                                </div>
                            </div>';
                            $user_info = explode(',', get_dataHashingOnlyExp(ZONE, false));
                            $expireFlag = false;
                            if ($user_info) {
                                $condition	                =	array ( 
                                                        'select'		=>	'cod_time, cod_code'
                                                        ,'where'		=>	array( 
                                                                                     'cod_id'   =>	cleanvars($user_info[3])
                                                                                    ,'is_used'  =>	0
                                                                            )
                                                        ,'return_type' 	=>	'single' 
                                ); 
                                $VERIFICATION_CODES = $dblms->getRows(VERIFICATION_CODES, $condition);
                                if ($VERIFICATION_CODES) {
                                    $giveTime       = date('G:i:s', strtotime($VERIFICATION_CODES['cod_time'] . ' +180 seconds'));
                                    $takkenTime     = date('G:i:s');
                                    if ($giveTime >= $takkenTime) {
                                        if ($user_info[4] >= $VERIFICATION_CODES['cod_code']) {
                                            echo'
                                            <div class="change_pass_div">
                                                <form method="POST">
                                                    <h1>Setup your new password</h1>
                                                    <div class="row">
                                                        <input type="hidden" name="adm_id" value="'.$user_info[2].'">
                                                        <div class="form-group">
                                                            <label class="form-control-label">Email</label>
                                                            <input type="email" class="form-control" value="'.$user_info[1].'" readonly>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label class="form-control-label">New Password <span class="text-danger">*</span></label>
                                                                <div class="pass-group" id="passwordInput">
                                                                    <input type="password" class="form-control pass-input" name="adm_userpass" placeholder="Enter your new password" required>
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
                                                            <div class="d-grid">
                                                                <button type="submit" class="btn btn-primary btn-start" name="submit_forgot_password">Change Password</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            <div>
                                            <script>
                                                setTimeout(function() {
                                                    $(".change_pass_div").html(`
                                                    <h1>Forgot your account password</h1>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                <label class="form-control-label">Email <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control" name="adm_email" id="adm_email" placeholder="Enter your email address" autocomplete="off" required>
                                                                <small id="email_error"></small>
                                                            </div>
                                                            <div class="d-grid">
                                                                <button type="submit" class="btn btn-primary btn-start" id="submit_forgot_password" disabled name="submit_forgot_password" onclick="sendVerificationCode();">Send Code</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    `);
                                                    Toastify({
                                                        newWindow: !0,
                                                        text: "Forgot password! Link is expired please send that email again",
                                                        gravity: "top",
                                                        position: "right",
                                                        className: "bg-danger",
                                                        stopOnFocus: !0,
                                                        offset: "50",
                                                        duration: "2000",
                                                        close: true,
                                                        style: "style",
                                                    }).showToast();
                                                    var newUrl = "'.SITE_URL.'/'.CONTROLER.'";
                                                    window.history.pushState({ path: newUrl }, "", newUrl);
                                                }, 180000);
                                            </script>';   
                                        } else {
                                            $expireFlag = true;
                                        }
                                    } else {
                                        $dblms->querylms("DELETE FROM ".VERIFICATION_CODES." WHERE cod_id = ".cleanvars($user_info[3]));
                                        $expireFlag = true;
                                        echo'
                                        <script>
                                            window.onload = function() {
                                                Toastify({
                                                    newWindow: !0,
                                                    text: "Forgot password! Link is expired please send that email again",
                                                    gravity: "top",
                                                    position: "right",
                                                    className: "bg-danger",
                                                    stopOnFocus: !0,
                                                    offset: "50",
                                                    duration: "2000",
                                                    close: true,
                                                    style: "style",
                                                }).showToast();
                                            };
                                            var newUrl = "'.SITE_URL.'/'.CONTROLER.'";
                                            window.history.pushState({ path: newUrl }, "", newUrl);
                                        </script>';
                                    }
                                } else {
                                    $expireFlag = true;
                                    echo'
                                    <script>
                                        var newUrl = "'.SITE_URL.'/'.CONTROLER.'";
                                        window.history.pushState({ path: newUrl }, "", newUrl);
                                    </script>';
                                }
                            } else {
                                $expireFlag = true;
                                echo'
                                <script>
                                    var newUrl = "'.SITE_URL.'/'.CONTROLER.'";
                                    window.history.pushState({ path: newUrl }, "", newUrl);
                                </script>';
                            }
                            if ($expireFlag) {
                                echo'
                                <h1>Forgot your account password</h1>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label class="form-control-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="adm_email" id="adm_email" placeholder="Enter your email address" autocomplete="off" required>
                                            <small id="email_error"></small>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-start" id="submit_forgot_password" disabled name="submit_forgot_password" onclick="sendVerificationCode();">Send Code</button>
                                        </div>
                                    </div>
                                </div>';
                            }
                            echo'
                        </>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="'.SITE_URL.'assets/js/jquery-3.6.0.min.js"></script>
    <script src="'.SITE_URL.'assets/js/bootstrap.bundle.min.js"></script>
    <script src="'.SITE_URL.'assets/js/owl.carousel.min.js"></script>
    <script src="'.SITE_URL.'assets/js/plugin.js"></script>
    <script src="'.SITE_URL.'assets/js/script.js"></script>
    <script>
        $("input[name=\'adm_email\']").on("input",function () {
            if(this.value.length >= 5){
                const rep = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (rep.test(String(this.value).toLowerCase())) {
                    $("#email_error").html("");
                    $("#submit_forgot_password").prop("disabled", false);                    
                    $.ajax({
                        url: "'.SITE_URL.'include/ajax/check_username.php", 
                        type: "POST",
                        data : {email : this.value},
                        dataType : "json",
                        success: function(response) {
                            console.log(response);
                            if (response.status == "success") {
                                $("#email_error").html("<span class=\'text-success\'>Email Exists.</span>");
                                $("#submit_forgot_password").prop("disabled", false);
                            } else if (response.status == "error") {
                                $("#email_error").html("<span class=\'text-danger\'>Email doesn\'t match.</span>");
                                $("#submit_forgot_password").prop("disabled", true);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: " + status, error);
                        }
                    });
                } else {
                    $("#email_error").html("<span class=\'text-danger\'>Enter a valid email</span>");
                    $("#submit_forgot_password").prop("disabled", true);
                }
            }else{
                $("#email_error").html("<span class=\'text-danger\'>Email should be Greater than 5 characters</span>");
                $("#submit_forgot_password").prop("disabled", true);
            }
        });        
        function sendVerificationCode() {
            if ($("#adm_email").val() != "") {
                $("#submit_forgot_password").hide();
                $.ajax({
                     url    : "'.SITE_URL.'include/ajax/get_VerificationCodeSend.php"
                    ,type   : "POST"
                    ,data   : { 
                        "_method"       : "forgot-account-password",
                        "adm_email"     : $("#adm_email").val()
                    }
                    ,success: function(response) {
                        if (response == "code-sent") {
                            Toastify({
                                newWindow: !0,
                                text: "Forgot password! Forgot account password mail has been send",
                                gravity: "top",
                                position: "right",
                                className: "bg-success",
                                stopOnFocus: !0,
                                offset: "50",
                                duration: "2000",
                                close: true,
                                style: "style",
                            }).showToast();
                            
                            setTimeout(function() {
                                $("#submit_forgot_password").show();
                                Toastify({
                                    newWindow: !0,
                                    text: "Forgot password! Link is expired please send that email again",
                                    gravity: "top",
                                    position: "right",
                                    className: "bg-danger",
                                    stopOnFocus: !0,
                                    offset: "50",
                                    duration: "2000",
                                    close: true,
                                    style: "style",
                                }).showToast();
                            }, 180000);
                        } else if (response == "cant-find-any-email") {
                            Toastify({
                                newWindow: !0,
                                text: "Forgot password! We cant matching email with this",
                                gravity: "top",
                                position: "right",
                                className: "bg-danger",
                                stopOnFocus: !0,
                                offset: "50",
                                duration: "2000",
                                close: true,
                                style: "style",
                            }).showToast();
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>';
?>