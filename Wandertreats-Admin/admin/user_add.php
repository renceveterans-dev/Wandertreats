<?php 


    ini_set('display_errors',0);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set("Asia/Manila");

    session_start();

    $sessionId = session_id();

    //WANDERTREATS WEBSERVICE
     
    // use PHPMailer\PHPMailer\PHPMailer;
    // use PHPMailer\PHPMailer\SMTP;
    // require 'vendor/autoload.php';
    // require_once 'vendor/twilio/Services/Twilio.php';

    include_once('webservice/config.php');
    include_once('webservice/db_info.php');
    include_once('webservice/general_functions.php');

    $sql = "SELECT * FROM register_user ORDER BY dRegistrationDate DESC"; 
    $statement = $obj->query($sql); 
    $registerUsers = $statement ->fetchAll(); 

   

?>


<?php include("include/top-include.php"); ?>
				
<div class="main-content">
    <div class="container-fluid">
		
		
		<!-- START OF CONTENTS -->
		
		<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-file-text bg-blue"></i>
                        <div class="d-inline">
                            <h5>Add New User</h5>
                            <span>New User Registration</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="../index.html"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">Pages</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Add New User</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

	    <div class="row">
	        <div class="col-md-6">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Account Information</h3>
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                        <div class="form-group">
	                            <label for="Username">Username</label>
	                            <input type="text" class="form-control" id="Username" placeholder="Username">
	                        </div>
	                        <div class="form-group">
	                            <label for="Email">Email address</label>
	                            <input type="email" class="form-control" id="Email" placeholder="Email">
	                        </div>
	                        <div class="form-group">
	                            <label for="Password">Password</label>
	                            <input type="password" class="form-control" id="Password" placeholder="Password">
	                        </div>
	                        <div class="form-group">
	                            <label for="ConfirmPassword">Confirm Password</label>
	                            <input type="password" class="form-control" id="ConfirmPassword" placeholder="Confirm Password">
	                        </div>
	                        
	                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
	                        <button class="btn btn-light">Cancel</button>
	                    </form>
	                </div>
	            </div>
	        </div>
	        <div class="col-md-6">
	            <div class="card" style="min-height: 484px;">
	                <div class="card-header">
	                    <h3>Personal Information</h3>
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Firstname</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="exampleInputUsername2" placeholder="Username">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="exampleInputEmail2" class="col-sm-3 col-form-label">Email</label>
	                            <div class="col-sm-9">
	                                <input type="email" class="form-control" id="exampleInputEmail2" placeholder="Email">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="exampleInputMobile" class="col-sm-3 col-form-label">Mobile</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="exampleInputMobile" placeholder="Mobile number">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="exampleInputPassword2" class="col-sm-3 col-form-label">Password</label>
	                            <div class="col-sm-9">
	                                <input type="password" class="form-control" id="exampleInputPassword2" placeholder="Password">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="exampleInputConfirmPassword2" class="col-sm-3 col-form-label">Re Password</label>
	                            <div class="col-sm-9">
	                                <input type="password" class="form-control" id="exampleInputConfirmPassword2" placeholder="Password">
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="custom-control custom-radio">
	                                <input type="radio" class="custom-control-input">
	                                <span class="custom-control-label">&nbsp;Remember me</span>
	                            </label>
	                        </div>
	                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
	                        <button class="btn btn-light">Cancel</button>
	                    </form>
	                </div>
	            </div>
	        </div>
	    </div>


		
           
		
		
		
		
		<!-- END OF CONTENTS -->
		

    </div>
</div>

<?php include("include/bottom-include.php"); ?>

       
		
        
     