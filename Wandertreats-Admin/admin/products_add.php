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

    $sql = "SELECT * FROM product_category"; 
    $statement = $obj->query($sql); 
    $productCategory = $statement ->fetchAll(); 

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
                            <h5>Add New Products</h5>
                            <span>New Products</span>
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
	                    <h3>Product Details</h3>
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

                                    <?php } ?>
                                    </select>
                              
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Category</label>
	                            <div class="col-sm-9">

	                            	<select id="ProductCategory" class="form-control select2">
	                            	<?php for($x = 0 ; $x < count($productCategory); $x++){ ?>

                                    	
                                        <option value="<?= $productCategory[$x]['iCategoryId']; ?>"><?= $productCategory[$x]['vCategoryName']; ?></option>
                                        <!-- <option value="tomatoes">Tomatoes</option>
                                        <option value="mozarella">Mozzarella</option>
                                        <option value="mushrooms">Mushrooms</option>
                                        <option value="pepperoni">Pepperoni</option>
                                        <option value="onions">Onions</option> -->

                                    <?php } ?>
                                     </select>

	                               
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Name</label>
	                            <div class="col-sm-9">
	                                <input type="text" class="form-control" id="ProductName" placeholder="Product Name">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Price</label>
	                            <div class="col-sm-9">
	                                <input type="number" placeholder="0.00" required name="price" min="0" value="0"  title="Currency" pattern="^\d+(?:\.\d{1,2})?$" class="form-control" id="ProductPrice">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Base Price</label>
	                            <div class="col-sm-9">
	                                <input type="number" placeholder="0.00" required name="price" min="0" value="0" title="Currency" pattern="^\d+(?:\.\d{1,2})?$" class="form-control" id="ProductBasePrice" >
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Stocks</label>
	                            <div class="col-sm-9">
	                                <input  type="number" class="form-control" id="ProductStocks" placeholder="Product Stocks">
	                            </div>
	                        </div>

	                         <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Discount</label>
	                            <div class="col-sm-9">
	                                <input  type="number" class="form-control" id="ProductDiscount" placeholder="Product Discount">
	                            </div>
	                        </div>

	                        
	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Description</label>
	                            <div class="col-sm-9">
	                                 <textarea id="ProductDescription" class="form-control html-editor" rows="10"></textarea>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Terms</label>
	                            <div class="col-sm-9">
	                                 <textarea id="ProductTerms" class="form-control html-editor" rows="10"></textarea>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Terms</label>
	                            <div class="col-sm-9">
	                                <textarea id="ProductClaimTerms" class="form-control html-editor" rows="10"></textarea>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Promo End Date</label>
	                            <div class="col-sm-9">
	                                <input  type="text" class="form-control" id="ProductPromoEnd" placeholder="Product Stocks">
	                            </div>
	                        </div>
	                      
	                      
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
	                        <div class="form-group">
                                <label>Image 1</label>
                                <input type="file" name="image1[]" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                             </div>
	                      
	                        <div class="form-group">
                                <label>Image 2</label>
                                <input type="file" name="image2[]" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Image 3</label>
                                <input type="file" name="image3[]" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                             </div>

                            <div class="form-group">
                                <label>Image 4</label>
                                <input type="file" name="image4[]" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Image 5</label>
                                <input type="file" name="image5[]" class="file-upload-default">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
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


  	// $("#ProductPrice").keyup(function(){  
   //      var ProductPrice = parseFloat($("#ProductPrice").val())
   //      $("#ProductPrice").val(""+ roductPrice);
   //  });  
   //   $("#ProductBasePrice").keyup(function(){  
   //      var ProductBasePrice = parseFloat($("ProductBasePrice").val())
        
   //      $("#ProductBasePrice").val(""+ (ProductBasePrice));
   //  });  
   

  	$('#Submit').click(function(){
  		 validateForms();
    	$.ajax({
	      url: "ajax/ajax_add_product.php",
	      cache: true,
	      type: "POST",
	      data:{
	    
	          
	          storeId :  $("#StoreName").val(),
	          productCategory : $("#ProductCategory").val(),
	          productName : $("#ProductName").val(),
	          productPrice : $("#ProductPrice").val(),
	          productBasePrice : $("#ProductBasePrice").val(),
	          productDiscount : $("#ProductDiscount").val(),
	          productDesc : $("#ProductDescription").val(),
	          productStocks : $("#ProductStocks").val(),
	          productSold : $("#ProductSold").val(),
	          productTerms : $("#ProductTerms").val(),
	          productClaimTerms : $("#ProductClaimTerms").val(),
	          productPromoEnd : $("#ProductPromoEnd").val(),
	          displayPhoto : "profile.jpg"
	      },
	      success: function(data){
	      	console.log(data);
	      	//alert(data);
	      	$.toast({
	            heading: 'Success',
	            text: 'New Product successfully registered!',
	            showHideTransition: 'slide',
	            icon: 'success',
	            loaderBg: '#f96868',
	            position: 'top-right'
	        });
	       	 // $("#StoreName").val("");
	         // $("#ProductCategory").val("");
	         // $("#ProductName").val("");
	         // $("#ProductPrice").val("");
	         // $("#ProductBasePrice").val("");
	         // $("#ProductDiscount").val("");
	         // $("#ProductDescription").val("");
	         // $("#ProductStocks").val("");
	         // $("#ProductSold").val("");
	         // $("#ProductTerms").val("");
	         // $("#ProductClaimTerms").val("");
	         // $("#ProductPromoEnd").val("");
	         
	    
	     
	      },
	      error: function (xhr, ajaxOptions, thrownError) {
	        alert("Error :"+xhr.status);
	        
	      }
	    });
	});

  	 function validateForms() {

  	 	let storeNameValue = $("#StoreName").val();
	    if (storeNameValue.length == '') {

	    	$("#StoreName").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
          	throw new Error('controlledError');
	    }

	    let productCategoryValue = $("#ProductCategory").val();
	    if (productCategoryValue.length == '') {

	    	$("#ProductCategory").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
      		throw new Error('controlledError');
	    }


	    let productNameValue = $("#ProductName").val();
	    if (productNameValue.length == '') {

	    	$("#ProductName").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   // return false;
          	throw new Error('controlledError');
	    }

	    let productPriceValue = $("#ProductPrice").val();
	    if (productPriceValue.length == '') {

	    	$("#ProductPrice").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
     		throw new Error('controlledError');
	    }

	    let productBasePriceValue = $("#ProductBasePrice").val();
	    if (productBasePriceValue.length == '') {

	    	$("#ProductBasePrice").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
      		throw new Error('controlledError');
	    }


	    let productDiscountValue = $("#ProductDiscount").val();
	    if (productDiscountValue.length == '') {

	    	$("#ProductDiscount").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let productDescValue = $("#ProductDescription").val();
	    if (productDescValue.length == '') {

	    	$("#ProductDescription").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let productStocksValue = $("#ProductStocks").val();
	    if (productStocksValue.length == '') {

	    	$("#ProductStocks").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let productTermsValue = $("#ProductTerms").val();
	    if (productTermsValue.length == '') {

	    	$("#ProductTerms").addClass( "form-control-danger" );


	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let productClaimTermsValue = $("#ProductClaimTerms").val();
	    if (productClaimTermsValue.length == '') {

	    	$("#ProductClaimTerms").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let productPromoEndValue = $("#ProductPromoEnd").val();
	    if (productPromoEndValue.length == '') {

	    	$("#ProductPromoEnd").addClass( "form-control-danger" );

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
       
		
        
     