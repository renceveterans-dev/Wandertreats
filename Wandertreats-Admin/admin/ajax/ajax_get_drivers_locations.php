
<?php

	ini_set('display_errors', 1);
	date_default_timezone_set("Asia/Manila");


	$sessionId = session_id();

	//TRIKAROO AJAX_GET ALL DRIVERS LOCATION
     
    
    include_once('../webservice/trikaroo_config.php');
    include_once('../webservice/trikaroo_general_functions.php');
  

    $database = new Connection();
    
    $db = $database->openConnection();

    $sql = "SELECT vName, vLatitude, vLongitude FROM register_driver WHERE eStatus  = 'Active'";
           
    $statement = $db->query($sql); 

    $drivers = $statement ->fetchAll();  
    
    $message['response'] = $drivers ;
    
    echo json_encode( $message);
    

?>
