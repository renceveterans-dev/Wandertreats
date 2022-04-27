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

    $sql = "SELECT mt.iTypeId, mt.vMerType, me.* FROM merchant_types as mt LEFT JOIN merchants as me ON mt.iTypeId = me.iTypeId WHERE me.vStoreName != '' "; 
    $statement = $obj->query($sql); 
    $merchants = $statement ->fetchAll();

   

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
                            <h5>Add New Products Category</h5>
                            <span>New New Products Category</span>
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
                            <li class="breadcrumb-item active" aria-current="page">Add New Products Category</li>
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
	                    <h3>Product Category Details</h3>
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                    	<div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store</label>
	                            <div class="col-sm-9">
	                               
                                                    
                                    <select id="StoreName" class="form-control select2">

  									<?php for($x = 0 ; $x < count($merchants); $x++){ ?>

                                    	
                                        <option value="<?= $merchants[$x]['iMerchantId']; ?>"><?= $merchants[$x]['vStoreName']; ?></option>
                                        <!-- <option value="tomatoes">Tomatoes</option>
                                        <option value="mozarella">Mozzarella</option>
                                        <option value="mushrooms">Mushrooms</option>
                                        <option value="pepperoni">Pepperoni</option>
                                        <option value="onions">Onions</option> -->

                                    <?php } ?>s
                                    </select>
                              
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Category </label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="CategoryName" placeholder="Product Category">
	                            </div>
	                        </div>
	                        

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Description</label>
	                            <div class="col-sm-9">
	                                 <textarea id="CategoryDescription" class="form-control html-editor" rows="10"></textarea>
	                            </div>
	                        </div>

	                        <!--  <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Status</label>
	                            <div class="col-sm-9">
	                                   <input type="checkbox" class="js-small" checked />
	                            </div>
	                        </div> -->
	                       

	                       
	                   
	                        
	                      
	                    </form>
	                </div>
	            </div>
	        </div>
	        <div class="col-md-6">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Product Photos</h3>
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
	                        <button class="btn btn-light"><a href="products_categories.php">Cancel</a></button>
	                   
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
	      url: "ajax/ajax_add_product_categories.php",
	      cache: true,
	      type: "POST",
	      data:{
	    
	          
	          CategoryName :  $("#CategoryName").val(),
	          CategoryDescription : $("#CategoryDescription").val(),
	          StoreId : $("#StoreName").val(),
	        
	      },
	      success: function(data){
	      	//alert(data);
	      	$.toast({
	            heading: 'Success',
	            text: 'New Product Category successfully registered!',
	            showHideTransition: 'slide',
	            icon: 'success',
	            loaderBg: '#f96868',
	            position: 'top-right'
	        });
	       	 $("#CategoryName").val("");
	         $("#CategoryDescription").val("");
	         
	    
	     
	      },
	      error: function (xhr, ajaxOptions, thrownError) {
	        alert("Error :"+xhr.status);
	        
	      }
	    });
	});

  	 function validateForms() {

  	 	let categoryDescriptionValue = $("#CategoryDescription").val();
	    if (categoryDescriptionValue.length == '') {

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
          	throw new Error('controlledError');
	    }

	    let categoryNameValue = $("#CategoryName").val();
	    if (categoryNameValue.length == '') {

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
      		throw new Error('controlledError');
	    }


  	 }

  });
</script>
       
		
        
     