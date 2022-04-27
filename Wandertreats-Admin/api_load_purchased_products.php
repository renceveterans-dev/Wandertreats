<?php 
header('Content-type: application/json');
ini_set('display_errors',1);
include_once('general_functions.php');
  
  $messageArray = array();
  $purchaseArray = array();

  $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '6';
  $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
  $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
  $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
  $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';

  $sqll = "";

  if($type == "ACTIVE"){
    $sqll = " AND iStatusCode IN(1,2,5) ";
  }else if($type == "HISTORY"){
    $sqll = " AND iStatusCode IN(6,7) ";
  }else if($type == "FAVORITE"){
    $sqll = " AND iStatusCode = 8 ";
  }
 
  $sql = "SELECT * FROM purchase WHERE iUserId = '".$userId."' ".$sqll."ORDER BY tPurchaseRequestDate DESC";
  $statement = $obj->query($sql); 
  $purchase = $statement ->fetchAll(); 

  for($x= 0; $x < count($purchase); $x++){


      $dbtimestamp = strtotime($purchase[$x]['dRequestClaimDate']);
      //CHECK STATUS IF TIME IS VALID TO CLAIMED
      if ((time() - $dbtimestamp > 15 * 60) && $purchase[$x]['iStatusCode'] == "5")  {
        //SET STATUS CLAIMED
        unset($where);
        $where['vPurchaseNo'] = $purchase[$x]['vPurchaseNo'];
        $updatePurchase['iStatusCode'] = 6;
        $updatePurchase['dReceivedDate'] = @date("Y-m-d H:i:s");
        $result = myQuery("purchase", $updatePurchase, "update", $where);
      }

      $expirytimestamp = strtotime($purchase[$x]['tPurchaseExpiryDate']);
      if ((time() < $dbtimestamp) && $purchase[$x]['iStatusCode'] != "7")  {
        //SET STATUS EXPIRED
        unset($where);
        $where['vPurchaseNo'] = $purchase[$x]['vPurchaseNo'];
        $updatePurchase['iStatusCode'] = 7;
        $result = myQuery("purchase", $updatePurchase, "update", $where);
      }

      $tPurchaseExpiryDate = "Valid until " .date('d F Y, h:i:s A', strtotime($purchase[$x]['tPurchaseExpiryDate']));
      $purchaseArray[$x]['dValidity'] = $tPurchaseExpiryDate ;
      $purchaseArray[$x] = $purchase[$x];

      $sql = "SELECT iMerchantId, vUserName, vStoreName, vStoreAddress, vLatitude, vLongitude, vRatings,  vLogo, vImages FROM `merchants` WHERE iMerchantId = '".$purchase [$x]['iMerchantId']."' AND eStatus = 'Active'"; 
      $statement = $obj->query($sql); 
      $merchantData = $statement ->fetchAll();

      $sql = "SELECT pr.*, pd.* FROM products as pr JOIN purchase_details as pd ON pr.iProductId = pd.iProductId WHERE iPurchaseId = '".$purchase[$x]['iPurchaseId']."'  AND pr.eStatus = 'Active'";   
      $statement = $obj->query($sql); 
      $productData = $statement ->fetchAll(); 

      // $purchaseArray[$x]['purchaseData'] = $purchaseDetails;
      $purchaseArray[$x]['productData'] = $productData;
      $purchaseArray[$x]['merchantData'] = $merchantData;

  }


  $messageArray['action'] = 1;  
  $messageArray['message'] = "success";
  $messageArray['data'] = $purchaseArray;

  echo safe_json_encode($messageArray);


?>