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

    $id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '1';
    $success = isset($_REQUEST['success']) ? trim($_REQUEST['success']) : '1';

    $sql = "SELECT * FROM merchant_types"; 
    $statement = $obj->query($sql); 
    $merchantTypes = $statement ->fetchAll(); 


    $sql = "SELECT mt.iTypeId, mt.vMerType, me.* FROM merchant_types as mt LEFT JOIN merchants as me ON mt.iTypeId = me.iTypeId WHERE me.vStoreName != '' AND me.iMerchantId = '". $id."'"; 
    $statement = $obj->query($sql); 
    $merchants = $statement ->fetchAll(); 

    $merchantArr = $merchants[0];

   

?>


<?php include("include/top-include.php"); ?>
				
<div class="main-content">
    <div class="container-fluid">
		
		
		<!-- START OF CONTENTS -->
		
		<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik bg-white">  <img width="30px" height="30px" src="<?= "../uploads/profile/store/".$id."/".$merchantArr['vLogo'];?>" class="table-user-thumb"></i>

                        <div class="d-inline">
                            <h5><?= $merchantArr['vStoreName']; ?></h5>
                            <span><?= $merchantArr['vUsername']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="dashboard.php"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="store_all.php">Store</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $merchantArr['vStoreName']; ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

         <div id="AlertError" class="alert alert-danger d-none" role="alert">
                                        Please complete the required fields.
                                    </div>

	    <div class="row">
	        <div class="col-md-8">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Store Information</h3>&nbsp;&nbsp;<a href="store_edit.php?id=<?= $id; ?>"><i class="ik ik-edit-2"></i></a>
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                    	<div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Name</label>
	                            <div class="col-sm-9">
	                            	 <label class="col-form-label"><?= $merchantArr['vStoreName']; ?></label>
	                            
	                            	
	                            </div>
	                        </div>
	                        <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Store Type</label>
                                <div class="col-sm-9">
                                	 <label class="col-form-label"><?= $merchantArr['vMerType']; ?></label>
									

                                	
                                </div>
                                
                            </div>
                            <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Username</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vStoreName']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Description</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vStoreDesc']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Theme</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vStoreTheme']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Location</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vStoreLocation']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Address</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vStoreAddress']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Contact Name</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vContactName']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Contact Email</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vEmail']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Contact Mobile</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vPhone']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Contact Telephone</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vTelephone']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Email Verified</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['eEmailVerified']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Phone Verified</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['ePhoneVerified']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Ratings</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $merchantArr['vRatings']; ?></label>
	                            </div>
	                        </div>
	                       
	                        <!-- <div class="form-group row">
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
	                        </div> -->
	                    </form>
	                </div>
	            </div>

	             <div class="card">
	                <div class="card-header">
	                    <h3>Store Documents</h3>
	                </div>
	                <div class="card-body">
	                   <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Documents</label>
                            <div class="col-sm-9">
                            	 <label class="col-form-label">N/A</label>
                            </div>
                        </div>
	                </div>
	            </div>

	             <div class="card">
	                <div class="card-header">
	                    <h3>Store Account</h3>
	                </div>
	                <div class="card-body">
	                   	<div class="form-group row">
                            <label class="col-sm-3 col-form-label">Change Password</label>
                            <div class="col-sm-9">
                            	 <label class="col-form-label"></label>
                            </div>
                        </div>
	                </div>
	            </div>
	        </div>
	        <div class="col-md-4">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Store Banner</h3>
	                </div>
	                <div class="card d-flex ">
                        <img src="<?= "../uploads/banners/store/".$id."/".$merchantArr['vImages'];?>" alt="<?= $merchantArr['vImages'];?>" class="responsive border-0">
                    </div>
	            </div>
	            <div class="card">
	                <div class="card-header">
	                    <h3>Store Logo</h3>
	                </div>
	                <div class="card d-flex ">
                        <img src="<?= "../uploads/profile/store/".$id."/".$merchantArr['vLogo'];?>" alt="<?= $merchantArr['vLogo'];?>" class="responsive border-0">
                    </div>
	               
	            </div>
	        </div>
	    </div>

      <!--   <div class="row">
 			<div class="col-md-12">
	            <div class="card">
	                <div class="card-body">
                        <button id="Submit" type="button" class="btn btn-primary mr-2">Submit</button>
                        <button class="btn btn-light"><a href="admin_all.php">Cancel</a>
	                </div>
	            </div>
	        </div>
        </div> -->


		
           
		
		
		
		
		<!-- END OF CONTENTS -->
		

    </div>
</div>



<?php include("include/bottom-include.php"); ?>



<script>
  $( document ).ready(function() {

  	var success = '<?= $success; ?>';
  	if(success == 'true'){
  		$.toast({
            heading: 'Success',
            text: 'Store Information successfully updated!',
            showHideTransition: 'slide',
            icon: 'success',
            loaderBg: '#f96868',
            position: 'top-right'
        });
  	}


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
	      success: function(data){
	      	//alert(data);
	      	$.toast({
	            heading: 'Success',
	            text: 'New administrator successfully registered!',
	            showHideTransition: 'slide',
	            icon: 'success',
	            loaderBg: '#f96868',
	            position: 'top-right'
	        });
	       	 $("#Username").val("");
	         $("#Email").val("");
	         $("#Password").val("");
	         $("#RetypePassword").val("");
	         $("#Firstname").val("");
	         $("#Lastname").val("");
	         $("#Mobile").val("");
	    
	     
	      },
	      error: function (xhr, ajaxOptions, thrownError) {
	        alert("Error :"+xhr.status);
	        
	      }
	    });
	});

  	 function validateForms() {

  	 	let usernameValue = $("#Username").val();
	    if (usernameValue.length == '') {

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
          	throw new Error('controlledError');
	    }

	    let lastNameValue = $("#Lastname").val();
	    if (lastNameValue.length == '') {

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
      		throw new Error('controlledError');
	    }


	    let firstNameValue = $("#Firstname").val();
	    if (firstNameValue.length == '') {

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   // return false;
          	throw new Error('controlledError');
	    }

	    let emailValue = $("#Email").val();
	    if (emailValue.length == '') {

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
     		throw new Error('controlledError');
	    }

	    let mobileValue = $("#Mobile").val();
	    if (mobileValue.length == '') {

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
      		throw new Error('controlledError');
	    }


	    let adminLevelValue = $("#AdminLevel").val();
	    if (adminLevelValue.length == '') {

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }
	     
  	 }


  	


  });
</script>
       
		
        
     