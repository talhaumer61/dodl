<?php
if(isset($_SESSION['userlogininfo']['LOGINIDA'])) {
	sessionMsg("Success", "Login Successfully.", "success");
	header("Location: http://localhost/GPT/portal.odl.edu.pk/dashboard.php", true, 301);
} else { 
    if (isset($_POST['signin_as_instructor'])) {
        $errorMsg = checkCpanelLMSSTDLogin();
    }
    require_once 'head.php';
    echo '
            <div class="row">
                <div class="col-md-6 login-bg">
                    <div class="owl-carousel login-slide owl-theme">
                        <div class="welcome-login">
                            <div class="login-banner">
                                <img src="'.SITE_URL.'assets/img/login-img.png" class="img-fluid" alt="Logo">
                            </div>
                            <div class="mentor-course text-center">
                                <h2>Welcome to <br>Distance Learning.</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>
                            </div>
                        </div>
                        <div class="welcome-login">
                            <div class="login-banner">
                                <img src="'.SITE_URL.'assets/img/login-img.png" class="img-fluid" alt="Logo">
                            </div>
                            <div class="mentor-course text-center">
                                <h2>Welcome to <br>Distance Learning.</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>
                            </div>
                        </div>
                        <div class="welcome-login">
                            <div class="login-banner">
                                <img src="'.SITE_URL.'assets/img/login-img.png" class="img-fluid" alt="Logo">
                            </div>
                            <div class="mentor-course text-center">
                                <h2>Welcome to <br>Distance Learning.</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>
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
                                </div>
                                <h1>Sign in as Instructor</h1>
                                <form action="'.SITE_URL.'signin-as-instructor" method="POST">
                                    <span class="text-danger">'.((isset($errorMsg))? $errorMsg: '').'</span>
                                    <div class="form-group">
                                        <label class="form-control-label">Email</label>
                                        <input type="text" name="login_id" class="form-control" required placeholder="Enter your email address">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label">Password</label>
                                        <div class="pass-group">
                                            <input type="password" name="login_pass" value="'.((isset($_POST['login_id']))? $_POST['login_id']: '').'" class="form-control pass-input" required placeholder="Enter your password">
                                            <span class="feather-eye toggle-password"></span>
                                        </div>
                                    </div>
                                    <div class="forgot">
                                        <span><a class="forgot-link" href="#">Forgot Password?</a></span>
                                    </div>
                                    <div class="remember-me">
                                        <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> Remember me
                                            <input type="checkbox" name="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-start" name="signin_as_instructor" type="submit">Sign In</button>
                                    </div>
                                    <div class="google-bg text-center">
                                        <p class="mb-0">New User ? <a href="'.SITE_URL.'signup">Create an Account</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <script src="'.SITE_URL.'assets/js/jquery-3.6.0.min.js"></script>
        <script src="'.SITE_URL.'assets/js/bootstrap.bundle.min.js"></script>
        <script src="'.SITE_URL.'assets/js/owl.carousel.min.js"></script>
        <script src="'.SITE_URL.'assets/js/script.js"></script>
    </body>

    </html>';
}
?>