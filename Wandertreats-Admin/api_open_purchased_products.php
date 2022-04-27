<?php 
header('Content-type: application/json');
ini_set('display_errors',1);
include_once('general_functions.php');
  
  $baseUrl = "https://wanderlustphtravel.com/wandertreats/";
  $messageArray = array();
  $purchaseArray = array();

  $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '6';
  $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
  $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
  $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
  $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'ACTIVE';
  $purchaseNo = isset($_REQUEST['purchaseNo']) ? trim($_REQUEST['purchaseNo']) : 'ACTIVE';

 
  $sql = "UPDATE purchase SET eRead = 'Yes' ".$sqll." WHERE vPurchaseNo = '".  $purchaseNo."'";
  $statement = $obj->query($sql); 
  $purchase = $statement ->execute(); 

  $messageArray['action'] = 1;  
  $messageArray['message'] = "success";
  $messageArray['data'] =  $purchase;

  echo safe_json_encode($messageArray);


?>