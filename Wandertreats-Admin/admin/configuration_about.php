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

    $success = isset($_REQUEST['success']) ? trim($_REQUEST['success']) : '1';

    $sql = "SELECT * FROM configurations_about"; 
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
                            <h5>General - About</h5>
                            <span>Contains Terms and Condition, Privcacy Policy and About</span>
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
	        <div class="col-md-4">
	            <div class="card">
	                <div class="card-header">
                      
                            <h3>About</h3>&nbsp;&nbsp;<a href="configuration_about_edit.php"><i class="ik ik-edit-2"></i></a>
                      

	                   	<!-- <div class="col-lg-12">  -->
	                		<!-- 	 
	                				
						        
						    </div> -->
	                
	                   
	                </div>
	                <div class="card-body">
	                    <form class="forms-sample">


	                    	<?php for($x = 0 ; $x < count($configurations); $x++){  ?>
	                    	<?php $config = $configurations[$x]; ?>
                    	    <?php if($config['vConfigName'] != ""){ ?>  
   							
                            <div class="btn-ripple">
       							<div class="form-group row configuration_rows" id="config_<?= $config['iConfigId']; ?>">
                                    <label for="Firstname" class="col-sm-12 col-form-label"><?= displayName($config['vConfigName']); ?></label>
                                    <input type="hidden" name="" id="<?= $config['iConfigId']; ?>" value="<?= $config['vConfigValue']; ?>">
    	                        </div>
                            </div>
                            
                        <?php } ?>

	                    <?php } ?>
	                    	
	                    </form>
	                </div>
	            </div>
	        </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        
                        <div class="container">
                            
                            <div class="d-inline">
                                <button id="Submit" type="button" class="btn btn-primary mr-2 float-right">Save Changes</button>
                            </div>
                          
                            <input id="about_config_id" type="hidden" >
                            <h3 id="about_title" class="float-left mr-2">Tiltles</h3>
                           
                        </div>


                       
                    </div>
                    <div class="card-body">
                        <textarea id="about_content" class="form-control html-editor" rows="30"></textarea>
                    <!--   <textarea  class="form-control html-editor product-input-text" rows="10"></textarea> -->
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

  	var count = 0;



    $('.configuration_rows').each(function(){
        console.log(count);
       

        $(this).click(function(){
           
            var title = $(this).children("label").text();
            var content = $(this).children("input").attr("value");
            var id = $(this).children("input").attr("id");
            $('#about_config_id').val(id);
            $('#about_title').html(title);
            $('#about_content').val(content);
        });

        count++;

    });


       console.log($("#config_1").children("label").text());

    $('#about_config_id').val($("#config_1").children("input").attr("id"));
    $('#about_title').html($("#config_1").children("label").text());
    $('#about_content').val($("#config_1").children("input").attr("value"));

     $('#Submit').click(function(){
             //alert($('#about_config_id').val() + " | "+  $('#about_title').val()+" | "+$('#about_content').val());
       
        $.ajax({
          url: "ajax/ajax_edit_configuration_about.php",
          cache: true,
          type: "POST",
          dataType: 'json', //**** REMOVE THIS LINE ****//
          data:{
        
              ConfigId :  $('#about_config_id').val(),
              ConfigValue : $('#about_content').val()
            
          },
        beforeSend: function() {
            $("#loaderView").show();
        },

        success: function(data) {

            console.log(data.result[0].iConfigId);
            setTimeout(function() {
                $("#loaderView").hide();
                 $.toast({
                    heading: 'Success',
                    text: $('#about_title').val()+' successfully updated!',
                    showHideTransition: 'slide',
                    icon: 'success',
                    loaderBg: '#f96868',
                    position: 'top-right'
                });
            }, 2000);

            var count = 0;
             $('.configuration_rows').each(function(){
                   // console.log(data.result[count].vConfigValue);
                    $(this).children("input").val(data.result[count].vConfigValue);
                    // $(this).children("input").id = data.result[count].iConfigId;
                
                count++;

            });

        
         
          },
          error: function (xhr, ajaxOptions, thrownError) {
            alert("Error :"+xhr.status);
             alert("Error :"+ thrownError);
            
          }
        });
    });


 
  });
</script>

<?php

function displayName($data){

	$resStr = str_replace('_', ' ', $data);
	return ucwords(strtolower($resStr));
}


?>
       
		
        
     