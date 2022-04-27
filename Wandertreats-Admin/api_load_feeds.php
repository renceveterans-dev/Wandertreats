<?php
	//RENCEVTERANS 12/03/2021
header('Content-type: application/json');
    ini_set('display_errors',1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once('general_functions.php');

	$messageArray = array();
    $purchaseData = array();
    $purchaseDetails = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $productId  = isset($_REQUEST['productId']) ? trim($_REQUEST['productId']) : '1';
    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
    $paymenType = isset($_REQUEST['paymenType']) ? trim($_REQUEST['paymenType']) : '';

    //GET USER

    $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";            
    $statement = $obj->query($sql); 
    $userData = $statement ->fetchAll(); 

    //GET PRODUCTS

    $sql = "SELECT * FROM feed_post ORDER BY dDate DESC";   
    $statement = $obj->query($sql); 
    $feedPostData = $statement ->fetchAll(); 

    for($i = 0; $i < count($feedPostData); $i ++){

        $feedPostData[$i]['dDate'] =   timeAgo($feedPostData[$i]['dDate'] );
      
    }


    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['data'] = $feedPostData;


    if(count($feedPostData) == 0){
        unset($messageArray);
        $messageArray['action'] = 0;  
        $messageArray['message'] = "failed";

    }


    echo safe_json_encode($messageArray);
   


?>