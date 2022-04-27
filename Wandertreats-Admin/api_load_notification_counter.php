<?php
	//RENCEVTERANS 12/03/2021
    header('Content-type: application/json');
    ini_set('display_errors',1);
    include_once('general_functions.php');

	$messageArray = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '6';
    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
   
        
    //CHECK ACTIVE TREATS
    $sql = "SELECT * FROM purchase WHERE iUserId = '".$userId."' AND iStatusCode != 6 ORDER BY tPurchaseRequestDate DESC";
    $statement = $obj->query($sql); 
    $purchase = $statement ->fetchAll(); 

    $sql = "SELECT * FROM notifications WHERE iUserId = '".$userId."' AND eStatus = 'Unread'";
    $statement = $obj->query($sql); 
    $notifications = $statement ->fetchAll(); 

    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['treatsCounter'] = count($purchase);
    $messageArray['feedsCounter'] = 1;
    $messageArray['accountCounter'] = 0;
    $messageArray['homeCounter'] = 0;//$featuredProducts;
    $messageArray['notifCounter'] = count($notifications);//$featuredProducts;
    

    
    $messageArray['notificationCount'] = 6;//$featuredProducts;
        

    echo safe_json_encode($messageArray);
   


?>