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
    $purchaseId  = isset($_REQUEST['purchaseId']) ? trim($_REQUEST['purchaseId']) : '2';
    $verificationCode = isset($_REQUEST['verificationCode']) ? trim($_REQUEST['verificationCode']) : '1';
    $fAmount = isset($_REQUEST['fAmount']) ? trim($_REQUEST['fAmount']) : '';
    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';

    $amount = number_format((float)$fAmount, 2, '.', '');

    $sql = "SELECT * FROM `sms_transaction_logs` WHERE vReferenceNo LIKE '%".$verificationCode."%' AND vAmount = '".$amount."'";
    $statement = $obj->query($sql); 
    $paymentVerifiedData = $statement ->fetchAll(); 

    if(count($paymentVerifiedData) > 0){

        //RETRIEVING DATA

        unset($where);
        $where['iPurchaseId'] = $purchaseId ;
        $updatePurchase['iStatusCode'] = 2;
        $result = myQuery("purchase", $updatePurchase, "update", $where);

        $sql = "SELECT * FROM purchase WHERE iPurchaseId = '".$purchaseId."' ORDER BY tPurchaseRequestDate DESC";
        $statement = $obj->query($sql); 
        $purchase = $statement ->fetchAll(); 

        $purchaseArray = $purchase[0];
        // $claimEndTime = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime(  $purchase[0]['dRequestClaimDate'])));
        // $purchaseArray['claimEndTime'] = $claimEndTime;

        $sql = "SELECT iMerchantId, vUserName, vStoreName, vStoreAddress, vLatitude, vLongitude, vRatings, vLogo, vImages FROM `merchants` WHERE iMerchantId = '".$purchase [0]['iMerchantId']."'"; 
        $statement = $obj->query($sql); 
        $merchantData = $statement ->fetchAll();
         
        $sql = "SELECT pr.*, pd.* FROM products as pr JOIN purchase_details as pd ON pr.iProductId = pd.iProductId WHERE iPurchaseId = '".$purchase[0]['iPurchaseId']."'";   
        $statement = $obj->query($sql); 
        $productData = $statement ->fetchAll(); 

          // $purchaseArray[$x]['purchaseData'] = $purchaseDetails;
        $purchaseArray['productData'] = $productData;
        $purchaseArray['merchantData'] = $merchantData;

        $messageArray['action'] = 1;  
        $messageArray['$userId'] = $userId;
        $messageArray['purchaseId'] = $purchaseId;
        $messageArray['verificationCode'] = $verificationCode;
        $messageArray['fAmount'] = $fAmount ;
        $messageArray['userType'] = $userType;
        $messageArray['data'] = $purchaseArray;///$paymentVerifiedData;
        
    }else{
        $messageArray['action'] = 0;  
        $messageArray['$userId'] = $userId;
        $messageArray['purchaseId'] = $purchaseId;
        $messageArray['verificationCode'] = $verificationCode;
        $messageArray['fAmount'] = $fAmount ;
        $messageArray['userType'] = $userType;
        $messageArray['warnings'] = "Payment Not Verified";
    }

  

    //GET USER
   
    

    echo safe_json_encode($messageArray);

    
   
  

?>