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

   	$sql = "SELECT me.iMerchantId, me.vUserName, me.vStoreName, me.vStoreAddress, me.vLatitude, me.vLongitude, me.vLogo, pc.vCategoryName, pi.* FROM merchants as me LEFT JOIN products as pi ON pi.iMerchantId = me.iMerchantId LEFT JOIN product_category as pc ON pc.iCategoryId = pi.iCategoryId WHERE pi.vProductName != '' AND pi.iProductId = '". $id."'";

    $statement = $obj->query($sql); 
    $products = $statement ->fetchAll();


    $prod = $products[0];
   

   

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
                            <h5><?=  $prod['vProductName']; ?></h5>
                            <span><?=  $prod['vUserName']; ?></span>
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
	                    <h3>Product Details</h3>&nbsp;&nbsp;<a href="products_edit.php?id=<?= $id; ?>"><i class="ik ik-edit-2"></i></a>


	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                    	<div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store</label>
	                            <div class="col-sm-9">
	                                <label class="col-form-label"><?= $prod['vStoreName']; ?></label>
                              
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Category</label>
	                            <div class="col-sm-9">
	                            	<label class="col-form-label"><?= $prod['vCategoryName']; ?></label>

	                            	

	                               
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Name</label>
	                            <div class="col-sm-9">
	                            	<label class="col-form-label"><?= $prod['vProductName']; ?></label>
	                               
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Price</label>
	                            <div class="col-sm-9">
	                            	<label class="col-form-label">&#8369; <?= number_format($prod['fPrice'],2); ?></label>
	                                
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Base Price</label>
	                            <div class="col-sm-9">
	                                <label class="col-form-label">&#8369; <?= number_format($prod['fBasePrice'],2); ?></label>
	                            </div>
	                        </div>
	                        

	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Discount</label>
	                            <div class="col-sm-9">
	                                <label class="col-form-label"><?= $prod['fDiscount']; ?> %</label>
	                            </div>
	                        </div>


	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Promo End Date</label>
	                            <div class="col-sm-9">
	                            	<label id="ProductDescription" class="col-form-label"><?= date('m/d/Y i:s',strtotime($prod['dPromoEnds'])); ?></label>
	                                
	                            	
	                               
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Stocks</label>
	                            <div class="col-sm-9">
	                               <label class="col-form-label"><?= $prod['iStocks']; ?></label>
	                            </div>
	                        </div>

	                        
	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Description</label>
	                            <div class="col-sm-9">
	                                 <label  id="ProductDescription" class="col-form-label"><?= $prod['vProductDesc']; ?></label>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Terms</label>
	                            <div class="col-sm-9">
	                            	<label id="ProductTerms" class="col-form-label"><?= $prod['vTerms']; ?></label>
	                                 
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Terms</label>
	                            <div class="col-sm-9">
	                            	<label id="ProductDescription" class="col-form-label" ><?= $prod['vHowToClaim']; ?></label>
	                                
	                            </div>
	                        </div>

	                        <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Product Status</label>
                                <div class="col-sm-9">
                                	<label id="ProductDescription" class="col-form-label" ><?= $prod['eStatus']; ?></label>
                                	
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
                    <div class="card d-flex ">


	                	<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
							<div class="carousel-inner">

								<?php $imageData = array(); ?>
        						<?php $imgArr = explode(",", $prod['vImages']); ?>
        						<?php for ($k = 0; $k < count($imgArr); $k++) { ?>
          
       
							    <div class="carousel-item <?= ($k == 0) ? 'active':'';?> ">
							    	<img class="d-block w-100" src="<?= "../uploads/products/".$id."/".$imgArr[$k]; ?>" alt="<?= $imgArr[$k]; ?>">
							    </div>
							    <!-- <div class="carousel-item">
							    	<img class="d-block w-100" src="https://wanderlustphtravel.com/gallery/photos/243131403_2968291523414389_5041570692621501253_n.jpg" alt="Second slide">
							    </div>
							    <div class="carousel-item">
							    	<img class="d-block w-100" src="https://wanderlustphtravel.com/gallery/photos/243131403_2968291523414389_5041570692621501253_n.jpg" alt="Third slide">
							    </div> -->

								<?php } ?>
							</div>
							<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
						    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
						    <span class="sr-only">Previous</span>
						  	</a>
						  	<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
							    <span class="carousel-control-next-icon" aria-hidden="true"></span>
							    <span class="sr-only">Next</span>
						  	</a>
						</div>




	                  <!--   <form class="forms-sample">
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
	                      
	                    </form> -->
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


  	$("#ProductPrice").keyup(function(){  
        var ProductPrice = parseFloat($("#ProductPrice").val())
        $("#ProductPrice").val(""+ roductPrice);
    });  
     $("#ProductBasePrice").keyup(function(){  
        var ProductBasePrice = parseFloat($("ProductBasePrice").val())
        
        $("#ProductBasePrice").val(""+ (ProductBasePrice));
    });  
   

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
	      	//alert(data);
	      	$.toast({
	            heading: 'Success',
	            text: 'New Product successfully registered!',
	            showHideTransition: 'slide',
	            icon: 'success',
	            loaderBg: '#f96868',
	            position: 'top-right'
	        });
	       	 $("#StoreName").val("");
	         $("#ProductCategory").val("");
	         $("#ProductName").val("");
	         $("#ProductPrice").val("");
	         $("#ProductBasePrice").val("");
	         $("#ProductDiscount").val("");
	         $("#ProductDescription").val("");
	         $("#ProductStocks").val("");
	         $("#ProductSold").val("");
	         $("#ProductTerms").val("");
	         $("#ProductClaimTerms").val("");
	         $("#ProductPromoEnd").val("");
	         
	    
	     
	      },
	      error: function (xhr, ajaxOptions, thrownError) {
	        alert("Error :"+xhr.status);
	        
	      }
	    });
	});




  	 function validateForms() {

  	 	let storeNameValue = $("#StoreName").val();
	    if (storeNameValue.length == '') {

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
       
		
        
     