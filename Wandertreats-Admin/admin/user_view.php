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

    $sql = "SELECT * FROM register_user WHERE iUserId = '".$id ."' ORDER BY dRegistrationDate DESC"; 
    $statement = $obj->query($sql); 
    $registerUsers = $statement ->fetchAll(); 
    $user =  $registerUsers[0];
   

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
                            <h5><?= $user['vName']." ".$user['vLastName']; ?></h5>
                            <span><?= $user['vEmail']; ?></span>
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
                                <a href="#">Pages</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Register User</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

	    <div class="row">
	        <div class="col-md-9">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Personal Information</h3>&nbsp;&nbsp;<a href="user_edit.php?id=<?= $id; ?>"><i class="ik ik-edit-2"></i></a>
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Firstname</label>
	                            <div class="col-sm-9">
	                            	 <label for="Firstname" class="col-sm-12 col-form-label"><?= $user['vName']; ?></label>

	                            </div>

	                            <input type="hidden" class="form-control user-input-text" id="UserId" placeholder="Firstname" value="<?= $id; ?>">
	                            
	                           
	                        </div>
	                        <div class="form-group row">
	                            <label for="Lastname" class="col-sm-3 col-form-label">Lastname</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['vLastName']; ?></label>
 									
	                            </div>
	                            
	                        </div>
	                        <div class="form-group row">
	                            <label for="Email" class="col-sm-3 col-form-label">Email address</label>
	                            <div class="col-sm-9">
                            		<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['vEmail']; ?></label>
 									
	                            </div>
	                           
	                        </div>
	                        <div class="form-group row">
	                            <label for="MobileNumber" class="col-sm-3 col-form-label">Mobile Number</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['vPhone']; ?></label>
 									
	                            </div>
	                            
	                        </div>
	                        <div class="form-group row">
	                            <label for="Country" class="col-sm-3 col-form-label">Country</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['vState']; ?></label>
 									
	                            </div>
	                            
	                        </div>
	                        <div class="form-group row">
	                            <label for="Region" class="col-sm-3 col-form-label">Region</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['vRegion']; ?></label>
 									
	                            </div>
	                            
	                        </div>
	                        <div class="form-group row">
	                            <label for="City" class="col-sm-3 col-form-label">City</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['vCity']; ?></label>
 									
	                            </div>
	                            
	                        </div>
							<div class="form-group row">
	                            <label for="Gender" class="col-sm-3 col-form-labels">Gender</label>
	                           	<div class="col-sm-9">
	                           		<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['eGender']; ?></label>
									
                                </div>
	                        </div>

	                       
	                        <div class="form-group row">
	                            <label for="EmailVerified" class="col-sm-3 col-form-label">Email Verified</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['eEmailVerified']; ?></label>
	                        	 
	                        	</div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="PhoneVerified" class="col-sm-3 col-form-label">Phone Verified</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['ePhoneVerified']; ?></label>
	                        	 	
	                        	</div>
	                        </div>
	                            
	                            

	                        <div class="form-group row">
	                        	 <label for="ReferralCode" class="col-sm-3 col-form-label">Referral Code</label>
	                        	 <div class="col-sm-9">
	                        	 	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['vRefCode']; ?></label>
	                        	 	
	                        	 </div>
	                           
	                            
	                        </div>

                          	<div class="form-group row">
	                            <label for="Logout" class="col-sm-3 col-form-label">Logout</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['eLogout']; ?></label>
	                            	
	                            </div>
	                           
	                            
	                        </div>

	                        <div class="form-group row">
	                            <label for="Blocked" class="col-sm-3 col-form-label">Blocked</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['eIsBlocked']; ?></label>
	                            </div>
	                            
	                            
	                        </div>

	                        <div class="form-group row" class="col-sm-3 col-form-label">
	                            <label for="AppVersion" class="col-sm-3 col-form-label" >App Version</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['iAppVersion']; ?></label>
 								
	                            </div>
	                            
	                        </div>

	                        <div class="form-group row">
	                            <label for="DeviceData" class="col-sm-3 col-form-label">Device Data</label>
	                            <div class="col-sm-9">
	                            	<label for="Firstname" class="col-sm-12 col-form-label"><?= $user['tDeviceData']; ?></label>
 									
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="LastOnline" class="col-sm-3 col-form-label">Firebase Token</label>
	                            <div class="col-sm-9" class="col-sm-3 col-form-label user-input-text">
 									<input type="LastOnline" class="form-control user-input-text" id="LastOnline" placeholder="Firebase Token" value="<?= $user['vFirebaseDeviceToken']; ?>" disabled> 
	                            </div>
	                        </div>


	                        <div class="form-group row">
	                            <label for="LastOnline" class="col-sm-3 col-form-label">Last Online</label>
	                            <div class="col-sm-9" class="col-sm-3 col-form-label user-input-text">
 									<input type="LastOnline" class="form-control user-input-text" id="LastOnline" placeholder="LastOnline" value="<?= date('F j, Y i:s',strtotime($user['tLastOnline'])); ?>" disabled> 
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="RegistrationDate" class="col-sm-3 col-form-label">Registration Date</label>
	                            <div class="col-sm-9">
 									<input type="text" class="form-control user-input-text" id="RegistrationDate" placeholder="Registration Date" value="<?= date('F j, Y i:s',strtotime($user['dRegistrationDate'])); ?>" disabled>
	                            </div>
	                        </div>

	                    </form>
	                </div>
	            </div>
	        </div>


	        <div class="col-md-3">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Profile</h3>
	                </div>

	                
	                <div class="card d-flex ">
                        <img id="ProfilePhoto" src="<?= "../uploads/profile/user/".$id."/".$user['vImage'];?>" alt="<?= $user['vImage'];?>" class="responsive border-0">
                    </div>
	            </div>
	        </div>
	    </div>

		<div class="row">
 			<div class="col-md-12">
 				<div class="card">
	                <div class="card-header">
	                    <h3>User Account</h3>
	                </div>
	                <div class="card-body">
	                   	<div class="form-group row">
                            <label class="col-sm-3 col-form-label">Change Password</label>
                            <div class="col-sm-9">
                            	 <label class="col-form-label"></label>
                            </div>

                            <label class="col-sm-3 col-form-label">Delete this Store</label>
                            <div class="col-sm-9">
                            	 <label class="col-form-label"> <a href="javascript:void(0)" class="list-delete" onclick="deleteItem('iMerchantId', <?= $id; ?>, 'merchants')"> Delete <i class="ik ik-trash-2"></i></a></label>
                            </div>
                        </div>
	                </div>
	            </div>
 			</div>

 		</div>
	  

 		<div id="AlertError" class="alert alert-danger d-none" role="alert">
	        Please complete the required fields.
	    </div>


           
		<div class="row">
 			<div class="col-md-12">
	            <div class="card">
	                <div class="card-body">
                        <button id="Submit" type="button" class="btn btn-primary mr-2">Save Changes</button>
                        <button class="btn btn-light"><a href="user_view.php?id=<?= $id; ?>">Cancel</a>
	                </div>
	            </div>
	        </div>
        </div>

		
		<!-- END OF CONTENTS -->
		

    </div>
</div>

<?php include("include/bottom-include.php"); ?>

       
<script type="text/javascript">

	$( document ).ready(function() {


	  	$('#Submit').click(function(){

	  	
	  		validateForms();
	  		
	  		// 

	    	$.ajax({
		      url: "ajax/ajax_update_user.php",
		      cache: true,
		      type: "POST",
		      data:{
		    	
		    	  UserId : $("#UserId").val(),
		          Firstname : $("#Firstname").val(),
		          Lastname :  $("#Lastname").val(),
		          Email : $("#Email").val(),
		          MobileNumber : $("#MobileNumber").val(),
		          ProfilePhotoName : $("#ProfilePhotoName").val();
		          Country : "Ph",
		          Region : $("#Region").val(),
		          City : $("#City").val(),
		          Gender : $("#Gender").val(),
		          PhoneVerified : $("#PhoneVerified").val(),
		          EmailVerified : $("#EmailVerified").val(),
		          ReferralCode :  $("#ReferralCode").val(),
		          Logout : $("#Logout").val(),
		          Blocked : $("#Blocked").val(),
		          AppVersion : $("#AppVersion").val(),
		          DeviceData : $("#DeviceData").val(),
		         
		      
		      },
		      success: function(data){
		      	console.log(data);
		      	var id = $("#UserId").val(),
		      	window.location = "user_view.php?id="+id+"&success=true";
		      	

		       // 	 $("#StoreName").val("");
		       //   $("#StoreType").val("");
		       //   $("#Password").val("");
		       //   $("#RetypePassword").val("");
		       //   $("#Firstname").val("");
		       //   $("#Lastname").val("");
		       //   $("#Mobile").val("");
		    
		     
		      },
		      error: function (xhr, ajaxOptions, thrownError) {
		        alert("Error :"+xhr.status);
		        
		      }
	    	});
		});
	});

	function validateForms() {

	  	 $('.user-input-text').each(function(){

			let inputValue = $(this).val();
			console.log(inputValue);
			

		    if (inputValue == '' || inputValue == null) {

		    	$(this).addClass( "form-control-danger" );

		    	$("#AlertError").show( "d-none" );
		    	$("#AlertError").addClass( "d-block" );
				setTimeout(function() { 
					$("#AlertError").removeClass( "d-block" );
			        $("#AlertError").addClass( "d-none" );
		    		
			    }, 3000);
	          	throw new Error('controlledError');
		    }

		});
	}



	$(document).on('click', '#uploadProfilePhoto', function() {
        var name = document.getElementById("filePhoto").files[0].name;
        var form_data = new FormData();
        var ext = name.split('.').pop().toLowerCase();
        if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
            alert("Invalid Image File");
        }
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("filePhoto").files[0]);
        var f = document.getElementById("filePhoto").files[0];
        var fsize = f.size || f.fileSize;
        if (fsize > 2000000) {
            alert("Image File Size is very big");
        } else {
            form_data.append("file", document.getElementById('filePhoto').files[0]);
            form_data.append("serviceType", "UPLOAD_PROFILE_PHOTO");
            form_data.append("userId", <?= $id; ?>);
            console.log(form_data);
            $.ajax({
                url: "ajax/ajax_upload_image.php",
                method: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                	console.log(data);
                	var obj = JSON.parse(data);
                	$("#ProfilePhoto").attr("src","../uploads/profile/user/"+<?= $id; ?>+"/"+obj.filename);
                	alert("success");
                    // showPhoto();
                },
                error: function (xhr, ajaxOptions, thrownError) {
			    
			        alert("Error :"+xhr.status);
			        
			    }
            });
        }
    });


	

</script>
        
     