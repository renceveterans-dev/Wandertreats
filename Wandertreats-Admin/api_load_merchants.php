<?php
	//RENCEVTERANS 12/03/2021
header('Content-type: application/json');
    ini_set('display_errors',1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once('general_functions.php');

    $baseUrl = "https://wanderlustphtravel.com/wandertreats/";

	$messageArray = array();
    $merchantsArray = array();
    $productsArr = array();
    $featuredItem1 = array();
    $featuredItem2 = array();
	$where = array();
	$result = array();
    $update = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
    $merchantType = isset($_REQUEST['merchantType']) ? trim($_REQUEST['merchantType']) : 'Dine';

    
    $sql = "SELECT mt.iTypeId, mt.vMerType, me.* FROM merchant_types as mt LEFT JOIN merchants as me ON mt.iTypeId = me.iTypeId WHERE  mt.vMerType = '".$merchantType."' AND me.vStoreName != '' AND me.eStatus = 'Active' "; 
    $statement = $obj->query($sql); 
    $merchants = $statement ->fetchAll(); 

    for($x= 0; $x < count($merchants); $x++){
        $productsArr = array();
        //$merchantsArray = array();
        $id = $merchants[$x]['iMerchantId'];
        $merchantsArray[$x] = $merchants[$x];
        
        $sql = "SELECT me.iMerchantId, me.vUserName, me.vStoreName, me.vStoreAddress, pc.vCategoryName, pi.* FROM merchants as me LEFT JOIN product_category as pc ON me.iMerchantId = pc.iMerchantId LEFT JOIN products as pi ON pi.iCategoryId = pc.iCategoryId WHERE pi.vProductName != '' AND pi.eStatus = 'Active' AND me.iMerchantId = ".$merchantsArray[$x]['iMerchantId'];
        $statement = $obj->query($sql); 
        $products = $statement ->fetchAll();

        for ($i = 0; $i < count($products); $i++) {
          
            $productsArr[$i] = $products[$i];
            $imageData = array();
            $imgArr = explode(",", $productsArr[$i]['vImages']);
            for ($k = 0; $k < count($imgArr); $k++) {
                $imageData[$k]['vImage'] = $baseUrl."uploads/products/".$id."/".$imgArr[$k];
            }

            $productsArr[$i]['vThumbnail'] = $baseUrl."uploads/products/".$id."/".$imgArr[0];
            $productsArr[$i]['vImages'] = $imageData;
            $productsArr[$i]['vLogo'] = $baseUrl."uploads/profile/store/".$id."/".$merchants[$x]['vLogo'];
        }

        $merchantsArray[$x]['vImages'] = $baseUrl ."uploads/banners/store/".$id."/".$merchants[$x]['vImages'];
        $merchantsArray[$x]['vLogo'] = $baseUrl."uploads/profile/store/".$id."/".$merchants[$x]['vLogo'];

        $merchantsArray[$x]['productData'] = $productsArr;
        


    }

 

    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['data'] = $merchantsArray;
  
  
        

    echo safe_json_encode($messageArray);
   


?>