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
		
		<style>
			.leaflet-container {
				height: 400px;
				width: 600px;
				max-width: 100%;
				max-height: 100%;
			}
		</style>
		
		<!-- START OF CONTENTS -->
		
		<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik bg-white">  <img width="30px" height="30px" src="<?= "../uploads/profile/store/".$id."/".$merchantArr['vLogo'];?>" class="table-user-thumb" alt="<?= constants::BASE_URL."uploads/store/".$merchantArr['vLogo'];?>"></i>

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
	                    <h3>Edit Store Information</h3>
	                </div>
	                <div class="card-body">
	                   
	                    	<div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Name</label>
	                            <div class="col-sm-9">
	                            	<input type="hidden" class="form-control" id="StoreId" placeholder="" value="<?= $id; ?>">

	                            	<input type="text" class="form-control" id="StoreName" placeholder="Store Name" value="<?= $merchantArr['vStoreName']; ?>">
	                            	
	                            </div>
	                        </div>
	                        <div class="form-group row">
                                <label for="exampleInputMobile" class="col-sm-3 col-form-label">Store Type</label>
                                <div class="col-sm-9">
									<select class="form-control" id="StoreType">
                                    	
                                	<?php for($x = 0; $x<count($merchantTypes); $x++){ ?>
                                		
                                		<option value="<?= $merchantTypes[$x]['iTypeId']; ?>"><?= $merchantTypes[$x]['vMerType']; ?></option>
                                    	
                                	
                                	<?php } ?>
                                	</select>

                                	
                                </div>
                                
                            </div>
                            <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Username</label>
	                            <div class="col-sm-9">
	                              <input type="text" class="form-control" id="StoreUsername" placeholder="Store Name" value="<?= $merchantArr['vUsername']; ?>">
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Description</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="StoreDescription" placeholder="Store Name" value="<?= $merchantArr['vStoreDesc']; ?>">
	                               
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Theme</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="StoreTheme" placeholder="Store Name" value="<?= $merchantArr['vStoreTheme']; ?>">
	                               
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Location</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="StoreLocation" placeholder="Store Location" value="<?= $merchantArr['vStoreLocation']; ?>">
	                              
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Address</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="StoreAddress" placeholder="Store Address" value="<?= $merchantArr['vStoreAddress']; ?>">
	                              
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Contact Name</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="ContactName" placeholder="Contact Name" value="<?= $merchantArr['vContactName']; ?>">
	                               
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Contact Email</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="ContactEmail" placeholder="Contact Email" value="<?= $merchantArr['vEmail']; ?>">
	                               
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Contact Mobile</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="ContactMobile" placeholder="Contact Mobile" value="<?= $merchantArr['vPhone']; ?>">
	                               
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Contact Telephone</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="ContactTelephone" placeholder="Contact Telephone" value="<?= $merchantArr['vTelephone']; ?>">
	                               
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Email Verified</label>
	                            <div class="col-sm-9">

	                            	<select class="form-control" id="EmailVerified">
                                    	
                                	
                                		
                                		<option value="Yes" <?= ($merchantArr['eEmailVerified'] == "Yes" ) ? 'selected' : '' ; ?> >Yes</option>

                                		<option value="No" <?= ($merchantArr['eEmailVerified'] == "No" ) ? 'selected' : '' ; ?> >No</option>
                                    	
                                	
                                
                                	</select>
	                              
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Phone Verified</label>
	                            <div class="col-sm-9">
	                            	<select class="form-control" id="PhoneVerified">
                                    	
                                	
                                		
                                		<option value="Yes" <?= ($merchantArr['ePhoneVerified'] == "Yes" ) ? 'selected' : '' ; ?> >Yes</option>

                                		<option value="No" <?= ($merchantArr['ePhoneVerified'] == "No" ) ? 'selected' : '' ; ?> >No</option>
                                    	
                                	
                                
                                	</select>
	                              
	                            </div>
	                        </div>

	                        <div class="form-group row">
	                            <label for="Firstname" class="col-sm-3 col-form-label">Store Ratings</label>
	                            <div class="col-sm-9">
	                            	<input type="text" class="form-control" id="StoreRatings" placeholder="Contact Email" value="<?= $merchantArr['vRatings']; ?>">
	                               
	                            </div>
	                        </div>
	                      
	                 
	                </div>
	            </div>

	            <div class="card">
	                <div class="card-header">
	                    <h3>Edit Store Location</h3>
	                </div>
	                <div class="card d-flex">
	                	<div id="map"style="width: 100%; height: 380px"></div>
<script>

	var map = L.map('map').setView([14.064282036257191, 121.63258737975941], 13);

	var tiles = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
			'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		id: 'mapbox/streets-v11',
		tileSize: 512,
		zoomOffset: -1
	}).addTo(map);

	// var marker = L.marker([51.5, -0.09]).addTo(map)
	// 	.bindPopup('<b>Hello world!</b><br />I am a popup.').openPopup();

	// var circle = L.circle([51.508, -0.11], {
	// 	color: 'red',
	// 	fillColor: '#f03',
	// 	fillOpacity: 0.5,
	// 	radius: 500
	// }).addTo(map).bindPopup('I am a circle.');

	// var polygon = L.polygon([
	// 	[51.509, -0.08],
	// 	[51.503, -0.06],
	// 	[51.51, -0.047]
	// ]).addTo(map).bindPopup('I am a polygon.');


	var popup = L.popup()
		.setLatLng([14.064854432928476, 121.63250154928996])
		.setContent('Wandertreats Headquearters.')
		.openOn(map);

	function onMapClick(e) {
		var lat = e.latlng.lat;
		var lng = e.latlng.lng;
		document.getElementById("StoreLatitude").value = lat;
		document.getElementById("StoreLongitude").value = lng ;
		popup
			.setLatLng(e.latlng)
			.setContent('Set this location for <?= $merchantArr['vStoreName']; ?> ' )//+ e.latlng.toString()
			.openOn(map);
	}

	map.on('click', onMapClick);

</script>
	                   
					    <input id="StoreLatitude" type="hidden" class="form-control"  placeholder="Store Latitude" value="<?= $merchantArr['vLatitude']; ?>">
                       <input id="StoreLongitude" type="hidden" class="form-control" placeholder="Store Longitude" value="<?= $merchantArr['vLongitude']; ?>">
                      	
                      
	                </div>
	            </div>

	           

	            <div class="card">
	                <div class="card-header">
	                    <h3>Edit Store Documents</h3>
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

                            <label class="col-sm-3 col-form-label">Delete this Store</label>
                            <div class="col-sm-9">
                            	 <label class="col-form-label"> <a href="javascript:void(0)" class="list-delete" onclick="deleteItem('iMerchantId', <?= $id; ?>, 'merchants')"> Delete <i class="ik ik-trash-2"></i></a></label>
                            </div>
                        </div>
	                </div>
	            </div>
	        </div>
	        <div class="col-md-4">
	            <div class="card">
	                <div class="card-header">
	                    <h3>Change Store Banner</h3>
	                </div>

	                <div class="card-body">
	                   	<form class="forms-sample" id="storeBannerForm" method="post" enctype="multipart/form-data">
							<div class="form-group">
		                 
		                        <input type="file" name="fileBanner" id="fileBanner" class="file-upload-default">
		                        <div class="input-group col-xs-12">
		                            <input type="text" id="StoreBannerName" class="form-control file-upload-info" disabled placeholder="Upload Image" value="<?= $merchantArr['vImages'];?>">
		                            <span class="input-group-append">
		                            <button class="file-upload-browse btn btn-default" type="button">Choose</button>
		                             <button type="button" id="uploadBanner" class="btn btn-primary" type="button">Upload</button>
		                            </span>
		                            </span>
		                           
		                        </div>
		                    </div>
		                </form>
	                </div>
	                <div class="card d-flex ">
                        <img id="storeBanner" src="<?= "../uploads/banners/store/".$id."/".$merchantArr['vImages'];?>" alt="<?= $merchantArr['vImages'];?>" class="responsive border-0">
                    </div>
	            </div>
	            <div class="card">
	                <div class="card-header">
	                    <h3>Change Store Logo</h3>
	                </div>
	                <div class="card-body">
	                	<form class="forms-sample" id="storeBannerForm" method="post" enctype="multipart/form-data">
							<div class="form-group">
		                   
		                        <input type="file" name="fileLogo" id="fileLogo" class="file-upload-default">
		                        <div class="input-group col-xs-12">
		                            <input type="text" id="StoreLogoName" class="form-control file-upload-info" disabled placeholder="Upload Image" value="<?= $merchantArr['vLogo'];?>">
		                            <span class="input-group-append">
		                            <button class="file-upload-browse btn btn-default" type="button">Choose</button>
		                             <button type="button" id="uploadLogo" class="btn btn-primary" type="button">Upload</button>
		                            </span>
		                            </span>
		                           
		                        </div>
		                    </div>
		                </form>
	                	<!--  <form>
						    <h2 align="center" style="color:blue">Image Upload using AJAX in PHP/MySQLi</h2>
						    <label>Select Image:</label>
						    serviceType
						    <input type="file" name="fileLogo" id="fileLogo"><br>
						    <button type="button" id="uploadLogo" class="btn btn-primary">Upload</button>
						  </form> -->
	                </div>

              
	               
	                <div class="card d-flex ">
                        <img id="storeLogo" src="<?= "../uploads/profile/store/".$id."/".$merchantArr['vLogo'];?>" alt="<?= $merchantArr['vLogo'];?>" class="responsive border-0">
                    </div>
	               
	            </div>
	        </div>
	    </div>

        <div class="row">
 			<div class="col-md-12">
	            <div class="card">
	                <div class="card-body">
                        <button id="Submit" type="button" class="btn btn-primary mr-2">Save Changes</button>
                        <button class="btn btn-light"><a href="store_view.php?id=<?= $id; ?>">Cancel</a>
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

  	validateForms();
  	$('#Submit').click(function(){
  		 validateForms();
  		 console.log($("#StoreLogoName").val());
  		 console.log($("#StoreBannerName").val());
    	$.ajax({
	      url: "ajax/ajax_update_store.php",
	      cache: true,
	      type: "POST",
	      data:{
	    
	          StoreId : $("#StoreId").val(),
	          StoreName :  $("#StoreName").val(),
	          StoreType : $("#StoreType").val(),
	          StoreDescription : $("#StoreDescription").val(),
	          StoreTheme : $("#StoreTheme").val(),
	          StoreLogo: $("#StoreLogoName").val(),
	          StoreBanner : $("#StoreBannerName").val(),
	          StoreLocation : $("#StoreLocation").val(),
	          StoreAddress : $("#StoreAddress").val(),
	          ContactName : $("#ContactName").val(),
	          ContactEmail :  $("#ContactEmail").val(),

	          ContactMobile : $("#ContactMobile").val(),
	          ContactTelephone : $("#ContactTelephone").val(),
	          EmailVerified : $("#EmailVerified").val(),
	          PhoneVerified : $("#PhoneVerified").val(),
	          StoreRatings : $("#StoreRatings").val(),
	          StoreLatitude : $("#StoreLatitude").val(),
	          StoreLongitude : $("#StoreLongitude").val()
	      },
	    beforeSend: function() {
        	$("#loaderView").show();
      	},

        success: function(data) {
        	console.log(data);
            setTimeout(function() {
                $("#loaderView").hide();
            }, 4000);
	      	var id = $("#StoreId").val();
	      	window.location = "store_view.php?id="+id+"&success=true";
	      	

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


	$(document).on('click', '#uploadLogo', function() {
        var name = document.getElementById("fileLogo").files[0].name;
        var form_data = new FormData();
        var ext = name.split('.').pop().toLowerCase();
        if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
            alert("Invalid Image File");
        }
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("fileLogo").files[0]);
        var f = document.getElementById("fileLogo").files[0];
        var fsize = f.size || f.fileSize;
        if (fsize > 2000000) {
            alert("Image File Size is very big");
        } else {
            form_data.append("file", document.getElementById('fileLogo').files[0]);
            form_data.append("serviceType", "UPLOAD_STORE_LOGO");
            form_data.append("storeId", <?= $id; ?>);
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
                	var obj = JSON.parse(data);
                	$("#storeLogo").attr("src","../uploads/profile/store/"+<?= $id; ?>+"/"+obj.filename);
                	alert("success");
                    // showPhoto();
                },
                error: function (xhr, ajaxOptions, thrownError) {
			    
			        alert("Error :"+xhr.status);
			        
			    }
            });
        }
    });

    $(document).on('click', '#uploadBanner', function() {
        var name = document.getElementById("fileBanner").files[0].name;
        var form_data = new FormData();
        var ext = name.split('.').pop().toLowerCase();
        if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
            alert("Invalid Image File");
        }
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("fileBanner").files[0]);
        var f = document.getElementById("fileBanner").files[0];
        var fsize = f.size || f.fileSize;
        if (fsize > 2000000) {
            alert("Image File Size is very big");
        } else {
            form_data.append("file", document.getElementById('fileBanner').files[0]);
            form_data.append("serviceType", "UPLOAD_STORE_BANNER");
            form_data.append("storeId", <?= $id; ?>);
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
                	$("#storeBanner").attr("src","../uploads/banners/store/"+<?= $id; ?>+"/"+obj.filename);
                	alert("success");
                    // showPhoto();
                },
                error: function (xhr, ajaxOptions, thrownError) {
			    
			        alert("Error :"+xhr.status);
			        
			    }
            });
        }
    });




	
	

  	 function validateForms() {

  	 	let storeIdValue = $("#StoreId").val();
	    if (storeIdValue.length == '') {



	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
          	throw new Error('controlledError');
	    }

	    
	        
	          

	    let storeNameValue = $("#StoreName").val();
	    if (storeNameValue.length == '') {

	    	$("#StoreName").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
      		throw new Error('controlledError');
	    }



	    let storeTypeValue = $("#StoreType").val();
	    if (storeTypeValue.length == '') {

	    	$(this).addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   // return false;
          	throw new Error('controlledError');
	    }

	    
	         
	          

	    let storeDescriptionValue = $("#StoreDescription").val();
	    if (storeDescriptionValue.length == '') {

	    	$(this).addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
     		throw new Error('controlledError');
	    }

	    let storeThemeValue = $("#StoreTheme").val();
	    if (storeThemeValue.length == '') {

	    	$(this).addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		    // return false;
      		throw new Error('controlledError');
	    }


	     

	    let storeLocationValue = $("#StoreLocation").val();
	    if (storeLocationValue.length == '') {

	    	$("#StoreLocation").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    
	  

	    let storeAddressnValue = $("#StoreAddress").val();
	    if (storeAddressnValue.length == '') {

	    	$("#StoreAddress").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	      
	       
	         


	    let contactEmailValue = $("#ContactEmail").val();
	    if (contactEmailValue.length == '') {

	    	$("#ContactEmail").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let contactMobileValue = $("#ContactMobile").val();
	    if (contactMobileValue.length == '') {

	    	$("#ContactMobile").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let contactTelephone = $("#ContactTelephone").val();
	    if (contactTelephone.length == '') {

	    	$("#ContactTelephone").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }


	   

	    let emailVerifiedValue = $("#EmailVerified").val();
	    if (emailVerifiedValue.length == '') {

	    	$("#EmailVerified").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let phoneVerifiedValue = $("#PhoneVerified").val();
	    if (phoneVerifiedValue.length == '') {

	    	$("#PhoneVerified").addClass( "form-control-danger" );

	    	$("#AlertError").show( "d-none" );
	    	$("#AlertError").addClass( "d-block" );
			setTimeout(function() { 
				$("#AlertError").removeClass( "d-block" );
		        $("#AlertError").addClass( "d-none" );
	    		
		    }, 3000);
		   //  return false;
     		throw new Error('controlledError');
	    }

	    let storeRatingsValue = $("#StoreRatings").val();
	    if (storeRatingsValue.length == '') {

	    	$("#StoreRatings").addClass( "form-control-danger" );

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
       
		
        
     