<?php 
header('Content-type: application/json');
ini_set('display_errors',1);
include_once('general_functions.php');
  
  $messageArray = array();
  $purchaseArray = array();
  $update = array();
  $where = array();

  $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
  $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
  $latiude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
  $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
  $vPurchaseNo = isset($_REQUEST['vPurchaseNo']) ? trim($_REQUEST['vPurchaseNo']) : 'WT211215PN4TI';
  $storeid = isset($_REQUEST['storeId']) ? trim($_REQUEST['storeId']) : 'WT211215PN4TI';
  $storeUserName = isset($_REQUEST['storeUserName']) ? trim($_REQUEST['storeUserName']) : 'WT211215PN4TI';

  $sql = "SELECT * FROM purchase WHERE vPurchaseNo = '".$vPurchaseNo."' AND iMerchantId = '".$storeid."' ORDER BY tPurchaseRequestDate DESC";
  $statement = $obj->query($sql); 
  $purchase = $statement ->fetchAll();


  if(count($purchase) > 0){

    $where['vPurchaseNo'] = $vPurchaseNo;
    $updateData['iStatusCode'] = 5;
    $updateData['dRequestClaimDate'] = @date('Y-m-d H:i:s');
    $resultUpdate = myQuery("purchase",  $updateData, "update",  $where);

    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['data'] = $purchase;

    echo safe_json_encode($messageArray);

  }else{

    $messageArray['action'] = 0;  
    $messageArray['message'] = "error";
    $messageArray['error'] = "Claim failed";

    echo safe_json_encode($messageArray);
  }

 



?>