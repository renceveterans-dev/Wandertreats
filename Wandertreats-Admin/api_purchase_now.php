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
    $productId  = isset($_REQUEST['productId']) ? trim($_REQUEST['productId']) : '2';
    $qty  = isset($_REQUEST['qty']) ? trim($_REQUEST['qty']) : '1';

    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
    $paymenType = isset($_REQUEST['paymenType']) ? trim($_REQUEST['paymenType']) : '';

    //GET USER

    $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";            
    $statement = $obj->query($sql); 
    $userData = $statement ->fetchAll(); 

    //GET PRODUCTS

    $sql = "SELECT me.iMerchantId, me.vUserName, me.vStoreName, me.vStoreAddress, pc.vCategoryName, pi.* FROM merchants as me LEFT JOIN product_category as pc ON me.iMerchantId = pc.iMerchantId LEFT JOIN products as pi ON pi.iCategoryId = pc.iCategoryId WHERE pi.vProductName != '' AND pi.iProductId = '". $productId."'";
    $statement = $obj->query($sql); 
    $productData = $statement ->fetchAll();

    $iTotalSold = (int)$productData[0]['iTotalSold']+(int)$qty;
    $iTotalStocks = (int) $productData[0]['iStocks'] -(int)$qty ;

    if($iTotalStocks < 0){

        $messageArray['action'] = 0;  
        $messageArray['message'] = "failed";
        $messageArray['error'] =  "Item is out of stocks.";
        echo safe_json_encode($messageArray);
       
        exit();
    }

    //UPDATE  PRODUCTS
    unset($where);
    $where['iProductId'] = $productId;
    $updateProducts['iTotalSold'] = $iTotalSold;
    $updateProducts['iStocks'] = $iTotalStocks;
    $result = myQuery("products",$updateProducts, "update", $where);

    // $merchantsArray[$x]['productData'] = $products;
    // $sql = "SELECT * FROM products WHERE iProductId = '". $productId."'";   
    // $statement = $obj->query($sql); 
    // $productData = $statement ->fetchAll(); 

    $vName = $userData[0]['vName'];
    $vLastName = $userData[0]['vLastName'];
    $vPhone = $userData[0]['vPhone'];
    $vUserEmail = $userData[0]['vEmail'];
    $vDeliveryAddress = "";

    $iStatusCode  = 1 ;

    //CHARGES

    $fServiceCharge = 0;
    $fOffersDiscount = 0;
    $fTax = 0;
    $fDiscount = 0;
    $fOffersDiscount = 0;

    $iQty = (int) $qty;
    $fSubTotal = $iQty * (float) $productData[0]['fPrice'];
    $fNetTotal = $fSubTotal;
    $fTotalGenerateFare = $fNetTotal;

    $vPurchaseType = "";
    $ePaid = "No";
    $vCouponCode = "";


    $purchaseData['iUserId']  = $userId;
    $purchaseData['vPurchaseNo']  = GenerateUniqueOrderNo("WT");
    $purchaseData['vPurchaseName']  = $productData[0]['vProductName'];
    $purchaseData['iMerchantId']  = $productData[0]['iMerchantId'];
    $purchaseData['vStoreName']  = $productData[0]['vStoreName'];
    $purchaseData['tPurchaseRequestDate']  =  @date("Y-m-d H:i:s");
    $purchaseData['tPurchaseExpiryDate'] = @date("Y-m-d H:i:s", strtotime('+3 weeks'));
   
    $purchaseData['fSubTotal']  = $fSubTotal;
    $purchaseData['fOffersDiscount']  =  $fOffersDiscount;
    $purchaseData['fServiceCharge']  =  $fServiceCharge;
    $purchaseData['fTax']  =  $fTax;
    $purchaseData['fDiscount']  =  $fDiscount;
    $purchaseData['fNetTotal']  =  $fNetTotal;
    $purchaseData['fTotalGenerateFare']  =  $fTotalGenerateFare;

    $purchaseData['vName']  = $vName;
    $purchaseData['vPhone']  = $vPhone;
    $purchaseData['vLastName']  =  $vLastName;
    $purchaseData['vUserEmail']  = $vUserEmail;
    $purchaseData['vDeliveryAddress']  =  $vDeliveryAddress;
    $purchaseData['vPurchaseType']  =  $vPurchaseType;
    $purchaseData['ePaid']  =  $ePaid;
    $purchaseData['iStatusCode']  =  $iStatusCode;
    $purchaseData['vCouponCode']  =  $vCouponCode;


    $id = myQuery("purchase", $purchaseData, "insert_getlastid");


    $purchaseDetails['iPurchaseId']  = $id;
    $purchaseDetails['iProductCatId']  = $productData[0]['iCategoryId'];
    $purchaseDetails['iProductId']  = $productData[0]['iProductId'];
    $purchaseDetails['vProductName']  = $productData[0]['vProductName'];
    $purchaseDetails['fOriginalPrice']  = $productData[0]['fPrice'];
    $purchaseDetails['fDiscountPrice']  = $productData[0]['fBasePrice'];
    $purchaseDetails['fPrice']  = $productData[0]['fPrice'];
    $purchaseDetails['fSubTotal']  = $fSubTotal;
    $purchaseDetails['iQty']  = $iQty;
    $purchaseDetails['vDescription']  = $productData[0]['vProductDesc'];
    $purchaseDetails['dDate']  =  @date("Y-m-d H:i:s");
    
    
    $id2 = myQuery("purchase_details", $purchaseDetails, "insert_getlastid");



    if($id2 != 0 ){

        //RETRIEVING DATA

        $sql = "SELECT * FROM purchase WHERE iPurchaseId = '".$id."' ORDER BY tPurchaseRequestDate DESC";
        $statement = $obj->query($sql); 
        $purchase = $statement ->fetchAll(); 

        $purchaseArray = $purchase[0];
        $claimEndTime = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime(  $purchase[0]['dRequestClaimDate'])));
        $purchaseArray['claimEndTime'] = $claimEndTime;

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
        $messageArray['purchasedId'] = $id;
        $messageArray['purchasedNo'] = $purchase[0]['vPurchaseNo'];
        $messageArray['message'] = "success";
        $messageArray['data'] =  $purchaseArray;
  

        echo safe_json_encode($messageArray);

  

    }  else {
         

        $messageArray['action'] = 0;  
        $messageArray['message'] = "failed";
        $messageArray['data'] = "Purchase dclined.";

        echo safe_json_encode($messageArray);

        exit();

    }

    // else{

       

       
    // }


    // 

    
   
  

?>