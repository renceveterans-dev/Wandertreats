<?php
	//RENCEVTERANS 12/03/2021
    header('Content-type: application/json');
    ini_set('display_errors',1);
    include_once('general_functions.php');

	$messageArray = array();
    $configArray = array();
    $featuredItem1 = array();
    $featuredItem2 = array();
    $featuredItem3 = array();
	$where = array();
	$result = array();
    $update = array();

    unset($messageArray);
    

    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
   
        
    $sql = "SELECT * FROM configurations WHERE eStatus = 'Active'"; 
    $statement = $obj->query($sql); 
    $configurations = $statement ->fetchAll(); 

    foreach($configurations as $name => $val) {
        $key = $val['vConfigName'];
        $value = $val['vConfigValue'];
        $configArray[$key] =  $value ;
    }

    $sql = "SELECT * FROM payment_methods WHERE eStatus = 'Enable' AND eDisplay = 'Yes'"; 
    $statement = $obj->query($sql); 
    $paymentMethods = $statement ->fetchAll(); 


    $configArray['paymentMethods'] = $paymentMethods;


    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['data'] = $configArray;

    echo safe_json_encode($messageArray);
   


?>