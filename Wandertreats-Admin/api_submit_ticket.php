<?php
	//RENCEVTERANS 01/08/2022

    ini_set('display_errors',1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once('general_functions.php');

	$messageArray = array();
    $purchaseData = array();
    $purchaseDetails = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $productId  = isset($_REQUEST['productId']) ? trim($_REQUEST['productId']) : '2';
    $qty  = isset($_REQUEST['qty']) ? trim($_REQUEST['qty']) : '1';

    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
    $paymenType = isset($_REQUEST['paymenType']) ? trim($_REQUEST['paymenType']) : '';

    //GET USER
    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['data'] =  $purchaseArray;
  

    echo safe_json_encode($messageArray);

    
   
  

?>