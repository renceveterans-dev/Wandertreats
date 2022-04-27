<?php
	//RENCEVTERANS 01/14/2022
    header('Content-type: application/json');
    ini_set('display_errors',1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once('general_functions.php');

	$messageArray = array();
    $merchantTypeData = array();
    $searchData = array();
    $dataArr = array();
	$where = array();
	$result = array();
    $update = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $keyword  = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : 'Fries';
    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';


    $sqlc = "";
    if($keyword != ""){
        $sqlc = " AND (me.vStoreName LIKE '%".$keyword."%' OR pi.vProductName LIKE '%".$keyword."%') AND me.eStatus = 'Active' AND pi.eStatus = 'Active'";
    }
    
    $sql = "SELECT me.iMerchantId, me.vUserName, me.vStoreName, me.vStoreAddress, me.vLatitude, me.vLongitude, me.vLogo, pc.vCategoryName, pi.* FROM merchants as me LEFT JOIN products as pi ON pi.iMerchantId = me.iMerchantId LEFT JOIN product_category as pc ON pc.iCategoryId = pi.iCategoryId WHERE pi.vProductName != '' AND pi.eStatus = 'Active' ".$sqlc ;

    $statement = $obj->query($sql); 
    $searchresults = $statement ->fetchAll();

    for ($i = 0; $i < count($searchresults); $i++) {
        $dataArr[$i] =  $searchresults[$i];
        $imageData = array();
        $imgArr = explode(",",   $dataArr[$i]['vImages']);
        for ($k = 0; $k < count($imgArr); $k++) {
            $imageData[$k]['vImage'] = $imgArr[$k];
        }

        $dataArr[$i]['vImages'] = $imageData;
    }

 
    $item['title'] = "Results";
    $item['Desc'] = "";
    $item['type'] = "VERTICAL";
    $item['products'] =  $dataArr;

    array_push($searchData, $item);



    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['merchantsTypes'] = $merchantTypeData;
    $messageArray['featured'] = $searchData;//$featuredProducts;
    $messageArray['productList'] = $dataArr;//$featuredProducts;

    
   // $messageArray['notificationCount'] = 6;//$featuredProducts;

    // $messageArray['action'] = 1;  
    // $messageArray['message'] = "success";
    // $messageArray['data'] = $dataArr;
  
  
        

    echo safe_json_encode($messageArray);
   


?>