<?php
    header('Content-type: application/json');
    ini_set('display_errors',1);
	date_default_timezone_set("Asia/Manila");

	//WANDERTREATS
     
    unset($where);
    unset($update);

   
    include_once('../webservice/config.php');
    include_once('../webservice/db_info.php');
    include_once('../webservice/general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();
    
    $registerData = array();
    $registerData = array();


    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'Admin';
    $storeId  = isset($_REQUEST['storeId']) ? trim($_REQUEST['storeId']) : '';
    $productName = isset($_REQUEST['productName']) ? trim($_REQUEST['productName']) : '';
    $productCategory = isset($_REQUEST['productCategory']) ? trim($_REQUEST['productCategory']) : '';
    $productPrice = isset($_REQUEST['productPrice']) ? trim($_REQUEST['productPrice']) : '1';
    $productBasePrice = isset($_REQUEST['productBasePrice']) ? trim($_REQUEST['productBasePrice']) : '1';
    $productDiscount  = isset($_REQUEST['productDiscount']) ? trim($_REQUEST['productDiscount']) : '10';
    $productStocks = isset($_POST['productStocks']) ? trim($_POST['productStocks']) : '10';
    $productDesc = isset($_POST['productDesc']) ? trim($_POST['productDesc']) : 'Desc';
    $productTerms = isset($_POST['productTerms']) ? trim($_POST['productTerms']) : 'ATerms';
    $productClaimTerms = isset($_REQUEST['productClaimTerms']) ? trim($_REQUEST['productClaimTerms']) : 'Claim Terms';
    $productPromoEnds = isset($_REQUEST['productPromoEnds']) ? trim($_REQUEST['productPromoEnds']) : '';


    unset($registerData);
    $registerData['iCategoryId'] = $productCategory;
     $registerData['iMerchantId'] = $storeId;
    $registerData['vProductName'] = $productName;
    $registerData['vProductDesc'] = $productDesc;
    $registerData['fPrice'] = $productPrice;
    $registerData['fBasePrice'] = $productBasePrice;
    $registerData['iStocks'] = $productStocks;
    $registerData['iStock'] = $productStocks;
    $registerData['fDiscount'] = $productDiscount;
    $registerData['vTerms'] = $productTerms;
    $registerData['vHowToClaim'] = $productClaimTerms ;
    



    $productId = myQuery("products", $registerData, "insert_getlastid");


    $sql = "SELECT * FROM products WHERE iproductId = '".$productId."'";               
    $statement = $obj->query($sql); 
    $data = $statement ->fetchAll(); 

    unset($messageArray);
    $messageArray['action'] = 1;  
    $messageArray['iUserId'] = $userId;
    $messageArray['message'] = "success";
    $messageArray['result'] = $data;
        

   echo safe_json_encode($messageArray);
    //echo json_encode($administrators);


           
 ?>
