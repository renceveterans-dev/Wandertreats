<?php 
header('Content-type: application/json');
ini_set('display_errors',1);
include_once('general_functions.php');
  
  $baseUrl = "https://wanderlustphtravel.com/wandertreats/";

  $messageArray = array();
  $products = array();
  $productData = array();
  $buyButtonEnable = true;

  $currentDate = @date("Y-m-d H:i:s");

  $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '6';
  $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
  $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
  $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
  $productId  = isset($_REQUEST['userId']) ? trim($_REQUEST['iProductId']) : '1';
  $MerchantId  = isset($_REQUEST['userId']) ? trim($_REQUEST['iMerchantId']) : '1';
  $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';

  $sqll = "";


  $sql = "SELECT iMerchantId, vUserName, vStoreName, vStoreAddress, vLatitude, vLongitude, vRatings,  vLogo, vImages FROM `merchants` WHERE iMerchantId = '". $MerchantId."' AND eStatus = 'Active'"; 
  $statement = $obj->query($sql); 
  $merchantData = $statement ->fetchAll();

  $sql = "SELECT pr.* FROM products as pr WHERE pr.iProductId = '".$productId."' AND pr.eStatus = 'Active'";   
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

  $productData['productData'] = array_merge($productData[0], $merchantData[0]);

  //CHECK
  $sql = "SELECT * FROM purchase WHERE iUserId = '".$userId."' AND iStatusCode != 6 ORDER BY tPurchaseRequestDate DESC";
  $statement = $obj->query($sql); 
  $purchase = $statement ->fetchAll(); 

  for($x= 0; $x < count($purchase); $x++){

      $sql = "SELECT pr.*, pd.* FROM products as pr JOIN purchase_details as pd ON pr.iProductId = pd.iProductId WHERE iPurchaseId = '".$purchase[$x]['iPurchaseId']."' AND pr.eStatus = 'Active'";   

      $statement = $obj->query($sql); 
      $purchaseDetails = $statement ->fetchAll(); 

      for($i = 0; $i < count($purchaseDetails); $i++){

        //CHECK THE BUY BUTOM ENABLE
        $expirytimestamp = strtotime($purchase[$x]['tPurchaseExpiryDate']);
        
        if($purchaseDetails[$i]['iProductId'] ==  $productId && time() < $dbtimestamp){
            $buyButtonEnable = false;
            break;
        }
      }

     

      $purchaseArray[$x] =  $purchase[$x];
      $purchaseArray[$x]['purchaseDetails'] = $purchaseDetails;

  }



  $messageArray['action'] = 1;  
  $messageArray['message'] = "success";
  $messageArray['data'] =  $productData;
  $messageArray['purchaseData'] =  $purchaseArray;
  $messageArray['buyButtonEnable'] = $buyButtonEnable;
  $messageArray['timestamp'] = $currentDate;

  echo safe_json_encode($messageArray);


?>