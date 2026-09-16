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
                            <input type="text" name="login_id" class="form-control" required placeholder="Enter your email or username" value="'.($username ?? '').'">
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
                                <input type="checkbox" name="remember_me">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" name="submit_signin">Sign in</button>
                        </div>
                        <h6 class="text-center mt-3">OR </h6>
                        <div class="text-center mt-3">
                            <a href="'.SITE_URL.'include/google_login.php" class="btn btn-outline-danger w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="21" height="21" fill="currentColor">
                                    <path d="M32.582 370.734C15.127 336.291 5.12 297.425 5.12 256c0-41.426 10.007-80.291 27.462-114.735C74.705 57.484 161.047 0 261.12 0c69.12 0 126.836 25.367 171.287 66.793l-73.31 73.309c-26.763-25.135-60.276-38.168-97.977-38.168-66.56 0-123.113 44.917-143.36 105.426-5.12 15.36-8.146 31.65-8.146 48.64 0 16.989 3.026 33.28 8.146 48.64l-.303.232h.303c20.247 60.51 76.8 105.426 143.36 105.426 34.443 0 63.534-9.31 86.341-24.67 27.23-18.152 45.382-45.148 51.433-77.032H261.12v-99.142h241.105c3.025 16.757 4.654 34.211 4.654 52.364 0 77.963-27.927 143.592-76.334 188.276-42.356 39.098-100.305 61.905-169.425 61.905-100.073 0-186.415-57.483-228.538-141.032v-.233z"/>
                                </svg> Continue with Google
                            </a>
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

<script>
    document.addEventListener(\'keydown\', function(event) {
        // ctrl+shift+c
        if (event.ctrlKey && event.shiftKey && event.keyCode === 67) {
            event.preventDefault();
        }
        // ctrl+shift+i
        if (event.ctrlKey && event.shiftKey && event.keyCode === 73) {
            event.preventDefault();
        }
        // ctrl+u
        if (event.ctrlKey && event.keyCode === 85) {
            event.preventDefault();
        }
        // ctrl+s
        if (event.ctrlKey && event.keyCode === 83) {
            event.preventDefault();
        }
        // ctrl+p 
        if (event.ctrlKey && event.keyCode === 80) {
            event.preventDefault();
        }
        // ctrl+e
        if (event.ctrlKey && event.keyCode === 69) {
            event.preventDefault();
        }
    });
    // right click disable
    document.addEventListener(\'contextmenu\', function(event) {
        event.preventDefault();
    });
</script>

</body>

</html>';
?>