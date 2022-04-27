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

    $sql = "SELECT * FROM configurations"; 
    $statement = $obj->query($sql); 
    $configurations = $statement ->fetchAll(); 



   

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
                            <h5>Edit General Configuration</h5>
                            <span>Contains main configuration settings</span>
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
                            <li class="breadcrumb-item active" aria-current="page">Add New Store</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

         <div id="AlertError" class="alert alert-danger d-none" role="alert">
                                        Please complete the required fields.
                                    </div>

	    <div class="row">
	        <div class="col-md-12">
	            <div class="card">
	                <div class="card-header">

	               <h3>General </h3>&nbsp;&nbsp; 
	             


	                		<!-- <div class="col-lg-12">  -->
	                		<!-- 	 
	                				
						        
						    </div> -->
	                
	                   
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">


	                    	<?php for($x = 0 ; $x < count($configurations); $x++){  ?>
	                    	<?php $config = $configurations[$x]; ?>
	                    	<?php if($config['vConfigName'] != ""){ ?>	
   							

   							<div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label"><?= displayName($config['vConfigName']); ?></label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control configuration-input-text" id="<?= $config['iConfigId']; ?>" placeholder="<?= displayName($config['vConfigName']); ?>" value="<?= $config['vConfigValue']; ?>">
	                              
	                            </div>
	                        </div>

	                        <?php } ?>

	                    	<?php } ?>
	                    	
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

  	

	// alert(myData.toString());



  	$('#Submit').click(function(){

  		var myData = $('.configuration-input-text').map(function() {
		    return { 'id': this.id, 'value': this.value };
		}).get();

		var  myJsonString = JSON.stringify(myData);
	  	var myd= [];

	  	console.log(myData);



  		 validateForms();
    	$.ajax({
	      url: "ajax/ajax_edit_configuration_general.php",

	      type: "POST",
	      data:{
	    
	        data : myJsonString
	 
	        
	      },
	      success: function(data){
	      	console.log(data);
	      
	      	$('.configuration-input-text').each(function(){
				$(this).val("");
			});
	
	      	window.location = "configuration_general.php?success=true";
	 
	      },
	      error: function (xhr, ajaxOptions, thrownError) {
	        alert("Error :"+xhr.status);
	        
	      }
	    });
	});

  	 function validateForms() {

	  	 $('.configuration-input-text').each(function(){

			let inputValue = $(this).val();
		    if (inputValue.length == '') {

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

	

  });
</script>

<?php

function displayName($data){

	$resStr = str_replace('_', ' ', $data);
	return ucwords(strtolower($resStr));
}


?>
       
		
        
     