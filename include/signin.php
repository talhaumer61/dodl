<?php
require_once 'head.php';
if (isset($_COOKIE['SWITCHTOSTUDENT']) && !empty($_COOKIE['SWITCHTOSTUDENT'])) {
    cpanelLMSSignInAsStudent();
}
if (isset($_POST['submit_signin'])) {
    $errorMsg = checkCpanelLMSSTDLogin();
    $username = $_POST['login_id'];
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
                    </div>
                    <h1>Sign in to your account</h1>
                    <small class="text-danger">'.(isset($errorMsg) ?$errorMsg : '').'</small>
                    <form action="" method="POST" autocomplete="off">
                        <div class="form-group">
                            <label class="form-control-label">Email or Username <span class="text-danger">*</span></label>
                            <input type="text" name="login_id" class="form-control" required placeholder="Enter your email or username" value="'.$username.'">
                        </div>
                        <div class="form-group">
                            <label class="form-control-label">Password <span class="text-danger">*</span></label>
                            <div class="pass-group">
                                <input type="password" name="user_pass" class="form-control pass-input" required placeholder="Enter your password">
                                <span class="feather-eye toggle-password"></span>
                            </div>
                        </div>
                        <div class="forgot">
                            <span><a class="forgot-link" href="'.SITE_URL.'forgot-password">Forgot Password?</a></span>
                        </div>
                        <div class="remember-me">
                            <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> Remember me
                                <input type="checkbox" name="radio">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-start" name="submit_signin">Sign in</button>
                        </div>
                        <div class="google-bg text-center">
                            <p class="mb-0">New User ? <a href="'.SITE_URL.'signup">Create your account</a></p>
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
<script src="'.SITE_URL.'assets/js/plugin.js"></script>
<script src="'.SITE_URL.'assets/js/script.js"></script>
</body>

</html>';
?>