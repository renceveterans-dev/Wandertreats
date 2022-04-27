<?php 
header('Content-type: application/json');
ini_set('display_errors',1);
include_once('general_functions.php');
  
  $baseUrl = "https://wanderlustphtravel.com/wandertreats/";

  $messageArray = array();
  $purchaseArray = array();
  $products = array();
  $productData = array();

  $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '6';
  $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
  $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
  $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
  $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
  $vPurchaseNo = isset($_REQUEST['vPurchaseNo']) ? trim($_REQUEST['vPurchaseNo']) : 'WT220410VX4KJ';

  $sql = "SELECT * FROM purchase WHERE vPurchaseNo = '".$vPurchaseNo."' ORDER BY tPurchaseRequestDate DESC";
  $statement = $obj->query($sql); 
  $purchase = $statement ->fetchAll(); 


  $dbtimestamp = strtotime($purchase[0]['dRequestClaimDate']);
  if ((time() - $dbtimestamp > 15 * 60) && $purchase[0]['iStatusCode'] == "5")  {
        unset($where);
    unset($where);
    $where['vPurchaseNo'] = $vPurchaseNo;
    $updatePurchase['iStatusCode'] = 6;
    $updatePurchase['dReceivedDate'] = @date("Y-m-d H:i:s");
    $result = myQuery("purchase", $updatePurchase, "update", $where);
  }

  $purchaseArray = $purchase[0];
  $purchaseArray['tPurchaseExpiryDate'] = date("M j, Y h:i",  strtotime($purchase[0]['tPurchaseExpiryDate']));
  
  $claimEndTime = date('Y-m-d H:i:s',strtotime('+15 minutes', strtotime($purchase[0]['dRequestClaimDate'])));
  $purchaseArray['claimEndTime'] = $claimEndTime;

  $sql = "SELECT iMerchantId, vUserName, vStoreName, vStoreAddress, vLatitude, vLongitude, vRatings, vLogo, vImages FROM `merchants` WHERE iMerchantId = '".$purchase [0]['iMerchantId']."'  AND eStatus = 'Active'"; 
  $statement = $obj->query($sql); 
  $merchantData = $statement ->fetchAll();
 
  $sql = "SELECT pr.*, pd.* FROM products as pr JOIN purchase_details as pd ON pr.iProductId = pd.iProductId WHERE iPurchaseId = '".$purchase[0]['iPurchaseId']."'  AND pr.eStatus = 'Active'";   
  $statement = $obj->query($sql); 
  $products = $statement ->fetchAll(); 

  for ($i = 0; $i < count( $products); $i++) {
      $productData[$i] = $products[$i];
      $imageData = array();
      $imgArr = explode(",", $productData[$i]['vImages']);
      for ($k = 0; $k < count($imgArr); $k++) {
          $imageData[$k]['vImage'] = $baseUrl."uploads/products/".$products[$i]['iProductId']."/".$imgArr[$k];
      }
      $productData[$i]['vThumbnail'] = $baseUrl."uploads/products/".$products[$i]['iProductId']."/".$imgArr[0];
      $productData[$i]['vImages'] = $imageData;

  }

  // $purchaseArray[$x]['purchaseData'] = $purchaseDetails;
  $purchaseArray['productData'] = $productData;
  $purchaseArray['merchantData'] = $merchantData;

  $messageArray['action'] = 1;  
  $messageArray['message'] = "success";
  $messageArray['data'] = $purchaseArray;

  echo safe_json_encode($messageArray);


?>