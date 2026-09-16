<?php
require_once 'head.php';
if (isset($_POST['signup_as_instructor'])) {
    $errorMsg = checkCpanelLMSSTDLogin();
}
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
                <div class="img-logo">
                    <img src="'.SITE_URL.'assets/img/logo/logo.png" class="img-fluid" alt="Logo">
                    <div class="back-home">
                        <a href="'.SITE_URL.'">Back to Home</a>
                    </div>
                </div>
                <h1>Sign up as Instructor</h1>
                <form action="'.SITE_URL.'signup-as-instructor" method="POST">
                    <span class="text-danger">'.((isset($errorMsg))? $errorMsg: '').'</span>
                    <div class="form-group">
                        <label class="form-control-label">Full Name</label>
                        <input type="text" class="form-control" name="adm_fullname" placeholder="Enter your Full Name" required="" value="'.((isset($_POST['adm_fullname']))? $_POST['adm_fullname']: '').'">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">User Name</label>
                        <input type="text" class="form-control" name="adm_username" placeholder="Enter your User Name" required="" value="'.((isset($_POST['adm_username']))? $_POST['adm_username']: '').'">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Email</label>
                        <input type="email" class="form-control" name="adm_email" placeholder="Enter your email address" required="" value="'.((isset($_POST['adm_email']))? $_POST['adm_email']: '').'">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Password</label>
                        <div class="pass-group" id="passwordInput">
                            <input type="password" class="form-control pass-input" name="adm_userpass" placeholder="Enter your password" required="">
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
                    <div class="form-check remember-me">
                        <label class="form-check-label mb-0">
                            <input class="form-check-input" type="checkbox" name="remember"> I agree to the
                            <a href="#">Terms of Service</a>
                            and
                            <a href="#">Privacy Policy.</a>
                        </label>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary btn-start instructor_btn" name="signup_as_instructor" type="submit">Create Account</button>
                    </div>
                </form>
                <div class="google-bg text-center">
                    <p class="mb-0">Already have an account? <a href="'.SITE_URL.'signin-as-instructor">Sign in</a></p>
                </div>
            </div>                    
        </div>
    </div>
</div>
</div>
<script src="'.SITE_URL.'assets/js/jquery-3.6.0.min.js"></script>
<script src="'.SITE_URL.'assets/js/bootstrap.bundle.min.js"></script>
<script src="'.SITE_URL.'assets/js/owl.carousel.min.js"></script>
<script src="'.SITE_URL.'assets/js/validation.js"></script>
<script src="'.SITE_URL.'assets/js/script.js"></script>
</body>
</html>';
?>