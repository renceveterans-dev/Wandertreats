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

    $sql = "SELECT * FROM products WHERE iProductId = '". $productId."'";   
    $statement = $obj->query($sql); 
    $productData = $statement ->fetchAll(); 

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

    $iQty = 1;
    $fSubTotal = 0;
    $fNetTotal = 0;
    $fTotalGenerateFare = 0;

    $vPurchaseType = "";
    $ePaid = "No";
    $vCouponCode = "";


    $purchaseData['iUserId']  = $userId;
    $purchaseData['vPurchaseNo']  = GenerateUniqueOrderNo("WT");
    $purchaseData['tPurchaseRequestDate']  =  @date("Y-m-d H:i:s");
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


  //  $id = myQuery("purchase", $purchaseData, "insert_getlastid");



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
    
    
   // $id = myQuery("purchase_details", $purchaseDetails, "insert_getlastid");

    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['previewData'] =  $purchaseData;
    $messageArray['previewProductData'] =  $purchaseDetails;
    $messageArray['userData'] =  $userData[0];
    
  
  
        
// $productData
    echo safe_json_encode($messageArray);
   


?>