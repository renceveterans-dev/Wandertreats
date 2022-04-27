<?php

ob_start();
session_start();

require_once('webservice/general_functions.php');

$database = new Connection();

$db = $database->openConnection();

    if (isset($_POST['login'])){

        $username = $_POST['username'];
        $password = $_POST['password'];
        $userType = "Admin";

        $result =  checkEmail($userType, $username);

        if (count($result) > 0) {

            unset($where);

            $result = checkPassword($userType, $username, $password);

           // echo json_encode($result);
           

            if (count($result) > 0) {
                unset($where);

                $messageArray['response'] = 1;

                unset($messageArray);
                $messageArray['success'] = "Succeddfuly logged in.";

                $_SESSION['user_id'] = $result[0]['iAdminId'];
               
                header("location: index.php");
                exit();

            
            } else {
                $messageArray['response'] = 0;
                $messageArray['error'] = "Invalid or wrong password";
            }
        } else {

            $messageArray['response'] = 0;
            $messageArray['error'] = "Invalid or wrong email";
        }

         

    }


?>


<!DOCTYPE html>
<html class="no-js" lang="en">
    
    <?php include("include/header.php"); ?>

    <body>
        <div class="auth-wrapper">
            <div class="container-fluid h-100">
                <div class="row flex-row h-100 bg-white">
                    <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                        <div class="lavalite-bg" style="background-image:url(img/home_slider.jpg)">
                            <div class="lavalite-overlay"></div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                        <div class="authentication-form mx-auto">
                            <div class="logo-centered">
                                <a href="index.php"><img width="40" height="50" src="img/logo.png" alt=""></a>
                            </div>

                             <?php if (isset($_POST['login']) && isset($messageArray['response']) && $messageArray['response'] == 0) { ?>

                                <div >
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $messageArray['error'] ;?>
                                    </div>
                                </div>

                            <?php   } 

                            if (isset($_POST['login']) && isset($messageArray['response']) && $messageArray['response'] == 1){ ?>

                    

                                <div >
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $messageArray['success'] ;?>
                                    </div>
                                </div>

                            <?php   }  ?>

                            <h3>Sign In to Wanderlust PH - Travel</h3>
                            <p>Happy to see you admin.!</p>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <input type="text" name="username" class="form-control" placeholder="Email" required="">
                                    <i class="ik ik-user"></i>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" placeholder="Password" required="">
                                    <i class="ik ik-lock"></i>
                                </div>
                                <div class="row">
                                    <div class="col text-left">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="item_checkbox" name="item_checkbox" value="option1">
                                            <span class="custom-control-label">&nbsp;Remember Me</span>
                                        </label>
                                    </div>
                                    <div class="col text-right">
                                        <a href="forgot-password.html">Forgot Password ?</a>
                                    </div>
                                </div>
                                <div class="sign-btn text-center">

                                    <input type="hidden" name="login" value="login">
                                    <button type="submit" class="btn sign-btn">Sign In</button>
                                </div>
                            </form>
                            <div class="register">
                                <p>Don't have an account? <a href="register.html">Create an account</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script>window.jQuery || document.write('<script src="../src/js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="plugins/popper.js/dist/umd/popper.min.js"></script>
        <script src="plugins/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
        <script src="plugins/screenfull/dist/screenfull.js"></script>
        <script src="dist/js/theme.js"></script>
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
    </body>
</html>

<?php 
ob_end_flush();
?>
