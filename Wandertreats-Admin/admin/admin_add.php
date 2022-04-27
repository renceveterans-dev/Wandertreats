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

    $sql = "SELECT * FROM administrator"; 
    $statement = $obj->query($sql); 
    $administrators = $statement ->fetchAll(); 

   

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
                            <h5>Add New Admin</h5>
                            <span>New Administration Registration</span>
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

         <div id="AlertError" class="alert alert-danger d-none" role="alert">
                                        Please complete the required fields.
                                    </div>

	    <div class="row">
	        <div class="col-md-6">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Account Information</h3>
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                    	<div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Username</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="Username" placeholder="Username">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Email Address</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="Email" placeholder="Email">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Password</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="Password" placeholder="Email">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Retype Password</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="RetypePassword" placeholder="Email">
	                            </div>
	                        </div>
	                       
	                   
	                        
	                      
	                    </form>
	                </div>
	            </div>
	        </div>
	        <div class="col-md-6">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Personal Information</h3>
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Firstname</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="Firstname" placeholder="Username">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Lastname" class="col-sm-3 col-form-label">Lastname</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="Lastname" placeholder="Username">
	                            </div>
	                        </div>
	                       
	                        <div class="form-group row">
	                            <label for="exampleInputMobile" class="col-sm-3 col-form-label">Mobile</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="Mobile" placeholder="Mobile number">
	                            </div>
	                        </div>

	                        <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Admin Level</label>
                                <div class="col-sm-9">
                                	<select class="form-control" id="AdminLevel">
                                    	<option value="Admin">Admin</option>
                                    	<option value="Biller">Biller</option>
                                	</select>
                                </div>
                                
                            </div>

              <!--               <div class="form-group">
                                <label>File upload</label>
                                <input type="file" name="img[]" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                             </div>
	                       -->
	                    </form>
	                </div>
	            </div>
	        </div>
	    </div>

        <div class="row">
 			<div class="col-md-12">
	            <div class="card">
	               <!--  <div class="card-header">
	                    <h3>Account Information</h3>
	                </div> -->
	                <div class="card-body">
	                   
	                    	
	                        
	                        <button id="Submit" type="button" class="btn btn-primary mr-2">Submit</button>
	                        <button class="btn btn-light"><a href="admin_all.php">Cancel</a></button>
	                   
	                </div>
	            </div>
	        </div>
        </div>


		
           
		
		
		
		
		<!-- END OF CONTENTS -->
		

    </div>
</div>



<?php include("include/bottom-include.php"); ?>



<script>
  $( document ).ready(function() {


  	$('#Submit').click(function(){
  		 validateForms();
    	$.ajax({
	      url: "ajax/ajax_add_admin.php",
	      cache: true,
	      type: "POST",
	      data:{
	    
	          userType : "Admin",
	          userName :  $("#Username").val(),
	          email : $("#Email").val(),
	          password : $("#Password").val(),
	          firstName : $("#Firstname").val(),
	          lastName : $("#Lastname").val(),
	          mobileNumber : $("#Mobile").val(),
	          displayPhoto : "profile.jpg"
	      },
	    	 beforeSend: function() {
            	$("#loaderView").show();
          	},

	        success: function(data) {
	            setTimeout(function() {
	                $("#loaderView").hide();
	            }, 4000);

	      	$('.form-control').each(function(){
				$(this).val("");
			});
	
	      	window.location = "admin_all.php?success=true";
	     
	      },
	      error: function (xhr, ajaxOptions, thrownError) {
	        alert("Error :"+xhr.status);
	        
	      }
	    });
	});

  	 function validateForms() {

  	 	$('.form-control').each(function(){

			let inputValue = $(this).val();
		    if (inputValue.length == '') {

		    	$("#AlertError").show( "d-none" );
		    	$("#AlertError").addClass( "d-block" );
				setTimeout(function() { 
					$("#AlertError").removeClass( "d-block" );
			        $("#AlertError").addClass( "d-none" );
		    		
			    }, 5000);
	          	throw new Error('controlledError');
		    }

		});
	     
  	 }


  	


  });
</script>
       
		
        
     