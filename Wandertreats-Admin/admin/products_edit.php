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

    $sql = "SELECT * FROM product_category"; 
    $statement = $obj->query($sql); 
    $productCategory = $statement ->fetchAll(); 

    $sql = "SELECT mt.iTypeId, mt.vMerType, me.* FROM merchant_types as mt LEFT JOIN merchants as me ON mt.iTypeId = me.iTypeId WHERE me.vStoreName != '' "; 
    $statement = $obj->query($sql); 
    $merchants = $statement ->fetchAll();

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


   		 <div class="row">
	        <div class="col-md-6">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Edit Product Details</h3>
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">
	                    	<div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store</label>
	                            <div class="col-sm-9">
	                               
                                                    
                                    <select id="StoreName" class="form-control select2 product-input-text'">

  									<?php for($x = 0 ; $x < count($merchants); $x++){ ?>
  									
  									<?php $selected = ($merchants[$x]['vStoreName'] == $prod['vStoreName']) ? 'selected' : ''; ?>

                                    	
                                        <option value="<?= $merchants[$x]['iMerchantId']; ?>" <?= $selected; ?>  ><?= $merchants[$x]['vStoreName']; ?></option>
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

	                            	<select id="ProductCategory" class="form-control select2 product-input-text'">
	                            	<?php for($x = 0 ; $x < count($productCategory); $x++){ ?>
	                            	<?php $selected = ($productCategory[$x]['iCategoryId'] == $prod['iCategoryId']) ? 'selected' : ''; ?>


                                    	
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
	                                <input type="text" class="form-control product-input-text" id="ProductName" placeholder="Product Name" value="<?=  $prod['vProductName']; ?>">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Price</label>
	                            <div class="col-sm-9">
	                                <input type="number" class="form-control product-input-text" placeholder="0.00" required name="price" min="0" title="Currency" pattern="^\d+(?:\.\d{1,2})?$" id="ProductPrice" value="<?=  $prod['fPrice']; ?>">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Base Price</label>
	                            <div class="col-sm-9">
	                                <input type="number" class="form-control product-input-text" placeholder="0.00" required name="price" min="0" title="Currency" pattern="^\d+(?:\.\d{1,2})?$" id="ProductBasePrice" value="<?=  $prod['fBasePrice']; ?>">
	                            </div>
	                        </div>
	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Stocks</label>
	                            <div class="col-sm-9">
	                                <input  type="number" class="form-control product-input-text" id="ProductStocks" placeholder="Product Stocks" value="<?=  $prod['iStocks']; ?>">
	                            </div>
	                        </div>

	                         <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Discount</label>
	                            <div class="col-sm-9">
	                                <input  type="number" class="form-control product-input-text" id="ProductDiscount" placeholder="Product Discount" value="<?=  $prod['fDiscount']; ?>">
	                            </div>
	                        </div>

	                        
	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Description</label>
	                            <div class="col-sm-9">
	                                 <textarea id="ProductDescription" class="form-control html-editor product-input-text" rows="10"><?=  $prod['vProductDesc']; ?></textarea>
	                            </div>
	                        </div>

	                       

	                        <div class="form-group row">
	                            <label class="col-sm-3 col-form-label">Product Terms</label>
	                            <div class="col-sm-9">
	                                 <textarea id="ProductTerms" class="form-control html-editor product-input-text" rows="10"><?=  $prod['vTerms']; ?></textarea>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Product Claim Terms</label>
	                            <div class="col-sm-9">
	                                <textarea id="ProductClaimTerms" class="form-control html-editor product-input-text" rows="10"><?=  $prod['vHowToClaim']; ?></textarea>
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Promo End Date</label>
	                            <div class="col-sm-9">

	                            	<input class="form-control" id="promoDate" type="datetime-local" value="<?= date('Y-m-d\TH:i',strtotime($prod['dPromoEnds'])); ?>"/>
	                            	 <!-- <input id="promoDate" class="form-control" type="date" value=""/> -->
	                            
	                        
	                            </div>
	                        </div>

	                         <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Product Status</label>
                                <div class="col-sm-9">
                                	<select class="form-control product-input-text" id="productStatus">


                                		<option value="Active" <?= ($prod['eStatus'] == "Active" ) ? 'selected' : '' ; ?> >Active</option>

                                		<option value="Inactive" <?= ($prod['eStatus'] == "Inactive" ) ? 'selected' : '' ; ?> >Inactive</option>

                                		<option value="Deleted" <?= ($prod['eStatus'] == "Deleted" ) ? 'selected' : '' ; ?> >Deleted</option>

                                    	
                                	</select>
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

                    	<?php $imageData = array(); ?>
						<?php $imgArr = explode(",", $prod['vImages']); ?>


	                	<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
							<div class="carousel-inner">
								<?php if(count($imgArr) > 0){ ?>
								<?php for($i = 0; $i < count($imgArr); $i ++ ){ ?>
								<?php $imgSrc = "../uploads/products/".$id."/".$imgArr[$i]; ?>

								<div class="carousel-item <?= ($i == 0) ? 'active': ''; ?>"><img class="d-block w-100" src="<?= $imgSrc; ?>"></div>
								<?php } ?>
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

						<div class="card-body">
							
	                       	
	                      
	                        <div class="form-group">
                                <label>Product Image 1</label>
                                <input type="file" name="image2[]" class="file-upload-default">
                                <form class="forms-sample" id="storeBannerForm" method="post" enctype="multipart/form-data">
									<div class="form-group">

										
				                 
				                        <input type="file" name="fileProduct1" id="fileProduct1" class="file-upload-default">
				                        <div class="input-group col-xs-12">
				                            <input type="text" id="ProdImgName1" class="form-control file-upload-info" disabled placeholder="Upload Image" value="<?= (0 < count($imgArr)) ? $imgArr[0] : '';  ?>">
				                            <span class="input-group-append">
				                            <button class="file-upload-browse btn btn-default" type="button">Choose</button>
				                             <button type="button" id="uploadProdImg1" class="btn btn-primary" type="button">Upload</button>
				                            </span>
				                            </span>
				                           
				                        </div>
				                    </div>
				                </form>
                            </div>

                            <div class="form-group">
                                <label>Product Image 2</label>
                                <input type="file" name="image2[]" class="file-upload-default">
                                <form class="forms-sample" id="storeBannerForm" method="post" enctype="multipart/form-data">
									<div class="form-group">
				                 
				                        <input type="file" name="fileProduct2" id="fileProduct2" class="file-upload-default">
				                        <div class="input-group col-xs-12">
				                            <input type="text" id="ProdImgName2" class="form-control file-upload-info" disabled placeholder="Upload Image" value="<?= (1 < count($imgArr)) ? $imgArr[1] : '';  ?>">
				                            <span class="input-group-append">
				                            <button class="file-upload-browse btn btn-default" type="button">Choose</button>
				                             <button type="button" id="uploadProdImg2" class="btn btn-primary" type="button">Upload</button>
				                            </span>
				                            </span>
				                           
				                        </div>
				                    </div>
				                </form>
                            </div>



                            <div class="form-group">
                                <label>Product Image 3</label>
                                <input type="file" name="image2[]" class="file-upload-default">
                                <form class="forms-sample" id="storeBannerForm" method="post" enctype="multipart/form-data">
									<div class="form-group">
				                 
				                        <input type="file" name="fileProduct3" id="fileProduct3" class="file-upload-default">
				                        <div class="input-group col-xs-12">
				                            <input type="text" id="ProdImgName3" class="form-control file-upload-info" disabled placeholder="Upload Image" value="<?= (2 < count($imgArr)) ? $imgArr[2] : '';  ?>">
				                            <span class="input-group-append">
				                            <button class="file-upload-browse btn btn-default" type="button">Choose</button>
				                             <button type="button" id="uploadProdImg3" class="btn btn-primary" type="button">Upload</button>
				                            </span>
				                            </span>
				                           
				                        </div>
				                    </div>
				                </form>
                            </div>

                            <div class="form-group">
                                <label>Product Image 4</label>
                                <input type="file" name="image2[]" class="file-upload-default">
                                <form class="forms-sample" id="storeBannerForm" method="post" enctype="multipart/form-data">
									<div class="form-group">
				                 
				                        <input type="file" name="fileProduct4" id="fileProduct4" class="file-upload-default">
				                        <div class="input-group col-xs-12">
				                            <input type="text" id="ProdImgName4" class="form-control file-upload-info" disabled placeholder="Upload Image" value="<?= (3 < count($imgArr)) ? '../uploads/products/'.$imgArr[3] : '';  ?>">
				                            <span class="input-group-append">
				                            <button class="file-upload-browse btn btn-default" type="button">Choose</button>
				                             <button type="button" id="uploadProdImg4" class="btn btn-primary" type="button">Upload</button>
				                            </span>
				                            </span>
				                           
				                        </div>
				                    </div>
				                </form>
                            </div>


                            <div class="form-group">
                                <label>Product Image 5</label>
                                <input type="file" name="image2[]" class="file-upload-default">
                                <form class="forms-sample" id="storeBannerForm" method="post" enctype="multipart/form-data">
									<div class="form-group">
				                 
				                        <input type="file" name="fileProduct5" id="fileProduct5" class="file-upload-default">
				                        <div class="input-group col-xs-12">
				                            <input type="text" id="ProdImgName5" class="form-control file-upload-info" disabled placeholder="Upload Image" value="<?= (4 < count($imgArr)) ? $imgArr[4] : '';  ?>">
				                            <span class="input-group-append">
				                            <button class="file-upload-browse btn btn-default" type="button">Choose</button>
				                             <button type="button" id="uploadProdImg5" class="btn btn-primary" type="button">Upload</button>
				                            </span>
				                            </span>
				                           
				                        </div>
				                    </div>
				                </form>
                            </div>
	                      
	                    </form>
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
	               <!--  <div class="card-header">
	                    <h3>Account Information</h3>
	                </div> -->
	                <div class="card-body">
	                   
                        <button id="Submit" type="button" class="btn btn-primary mr-2">Save Changes</button>
                        <button class="btn btn-light"><a href="products_all.php">Cancel</a></button>
	                   
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
   //      var ProductPrice = parseFloat($("#ProductPrice").val());
   //      $("#ProductPrice").val(""+ ProductPrice);
   //  });  
   //   $("#ProductBasePrice").keyup(function(){  
   //      var ProductBasePrice = parseFloat($("ProductBasePrice").val());
        
   //      $("#ProductBasePrice").val(""+ (ProductBasePrice));
   //  });  




   

  	$('#Submit').click(function(){
  		console.log($("#promoDate").val());
  		console.log($("#StoreName").val());
	    console.log( $("#ProductCategory").val());
	     console.log($("#productStatus").val());
  		 validateForms();
    	$.ajax({
	      url: "ajax/ajax_edit_product.php",
	      cache: true,
	      type: "POST",
	      data:{
	    		
	    	  productId : <?= $id; ?>,
	          storeId :  $("#StoreName").val(),
	          productCategory : $("#ProductCategory").val(),
	          productName : $("#ProductName").val(),
	          productPrice : $("#ProductPrice").val(),
	          productBasePrice : $("#ProductBasePrice").val(),
	          productDiscount : $("#ProductDiscount").val(),
	          productDesc : $("#ProductDescription").val(),
	          productStatus : $("#productStatus").val(),
	          productStocks : $("#ProductStocks").val(),
	          productSold : $("#ProductSold").val(),
	          productTerms : $("#ProductTerms").val(),
	          productClaimTerms : $("#ProductClaimTerms").val(),
	          productPromoEnds : $("#promoDate").val(),
	          prodImgName1 : $("#ProdImgName1").val(),
	          prodImgName2 : $("#ProdImgName2").val(),
	          prodImgName3 : $("#ProdImgName3").val(),
	          prodImgName4 : $("#ProdImgName4").val(),
	          prodImgName5 : $("#ProdImgName5").val(),
	      },
		beforeSend: function() {
        	$("#loaderView").show();
      	},

        success: function(data) {
            setTimeout(function() {
                $("#loaderView").hide();
            }, 4000);

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

	        var id = <?= $id; ?>+'';
	      	window.location = "products_view.php?id="+id+"&success=true";
	         
	    
	      },
	      error: function (xhr, ajaxOptions, thrownError) {
	        alert("Error :"+xhr.status);
	        
	      }
	    });
	});

	productImagePreview();


	for (let i = 1; i <= 5; i++) {
	 	$(document).on('click', '#uploadProdImg'+i, function() {

	        var name = document.getElementById("fileProduct"+i).files[0].name;
	        var form_data = new FormData();
	        var ext = name.split('.').pop().toLowerCase();
	        if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
	            alert("Invalid Image File");
	        }
	        var oFReader = new FileReader();
	        oFReader.readAsDataURL(document.getElementById("fileProduct"+i).files[0]);
	        var f = document.getElementById("fileProduct"+i).files[0];
	        var fsize = f.size || f.fileSize;
	        if (fsize > 2000000) {
	            alert("Image File Size is very big");
	        } else {
	            form_data.append("file", document.getElementById("fileProduct"+i).files[0]);
	            form_data.append("serviceType", "UPLOAD_PRODUCTS");
	            form_data.append("productId", <?= $id; ?>);
	            console.log(form_data);
	            $.ajax({
	                url: "ajax/ajax_upload_image.php",
	                method: "POST",
	                data: form_data,
	                contentType: false,
	                cache: false,
	                processData: false,
	                beforeSend: function() {
		            	$("#loaderView").show();
		          	},

			        success: function(data) {
			            setTimeout(function() {
			                $("#loaderView").hide();
			            }, 4000);
	                
	                	console.log(data);
	                	var obj = JSON.parse(data)
	                	productImagePreview();
	                    // showPhoto();
	                },
	                error: function (xhr, ajaxOptions, thrownError) {
				    
				        alert("Error :"+xhr.status);
				        
				    }
	            });
	        }
	    });

	}

	function productImagePreview(){

		console.log("productImagePreview");

		$('.carousel-inner').empty();

		var count = 0;

		$('.file-upload-info').each(function(){

			
			var imageSrc = $(this).val();

			if(imageSrc != '' || imageSrc == null){
				console.log(imageSrc+"");
				// 
				var srcStr = "../uploads/products/"+<?= $id ; ?>+"/"+imageSrc;
				var active = "active";


				if(count != 0){
					active = "";
				}

				$('.carousel-inner').append('<div class="carousel-item '+active+'"><img class="d-block w-100" src="'+srcStr+'"></div>');


				count++;
			}

			

		});
	}

	
	

  	 function validateForms() {

  // 	 	 $('.product-input-text').each(function(){

		// 	let inputValue = $(this).val();
		//     if (inputValue.length == '') {

		//     	$("#AlertError").show( "d-none" );
		//     	$("#AlertError").addClass( "d-block" );
		// 		setTimeout(function() { 
		// 			$("#AlertError").removeClass( "d-block" );
		// 	        $("#AlertError").addClass( "d-none" );
		    		
		// 	    }, 3000);
	 //          	throw new Error('controlledError');
		//     }

		// });

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

	    let productPromoEndValue = $("#promoDate").val();
	    if (productPromoEndValue.length == '') {

	    	$("#promoDate").addClass( "form-control-danger" );

	    	

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
       
		
        
     