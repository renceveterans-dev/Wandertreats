
<?php

	ini_set('display_errors', 1);
	date_default_timezone_set("Asia/Manila");


	$sessionId = session_id();

	//TRIKAROO AJAX_SUBMIT_PABILI_SETTINGS
    
    include_once('../webservice/trikaroo_config.php');
    include_once('../webservice/trikaroo_general_functions.php');
  

    $database = new Connection();
    
    $db = $database->openConnection();
    $pabili_distance_scope = isset($_POST['pabili_distance_scope']) ? trim($_POST['pabili_distance_scope']) : '';
    $pabli_delivery_fee = isset($_POST['pabli_delivery_fee']) ? trim($_POST['pabli_delivery_fee']) : '';
    $pabili_delivery_fee_grocery  = isset($_POST['pabili_delivery_fee_grocery']) ? trim($_POST['pabili_delivery_fee_grocery']) :'';
    $pabili_deliveryfee_per_km  = isset($_POST['pabili_deliveryfee_per_km']) ? trim($_POST['pabili_deliveryfee_per_km']) :'';
    $pabili_waiting_time_fare  = isset($_POST['pabili_waiting_time_fare']) ? trim($_POST['pabili_waiting_time_fare']) :'18';
    $pabili_transaction_charge  = isset($_POST['pabili_transaction_charge']) ? trim($_POST['pabili_transaction_charge']) :'PAS201008228919';
    


    $sql = "SELECT vName, vLatitude, vLongitude FROM register_driver WHERE eStatus  = 'Active'";
           
    $statement = $db->query($sql); 

    $drivers = $statement ->fetchAll();  
    
    $message['response'] = $drivers ;
    
    echo json_encode( $message);
    

?>