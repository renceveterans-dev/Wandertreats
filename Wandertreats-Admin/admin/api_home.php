<?php
	//RENCEVTERANS 12/03/2021
    header('Content-type: application/json');
    // ini_set('display_errors',1);
    include_once('general_functions.php');

    $baseUrl = "https://wanderlustphtravel.com/wandertreats/";
	$messageArray = array();
    $featuredProducts = array();
    $featuredItem1 = array();
    $featuredItem2 = array();
    $featuredItem3 = array();
	$where = array();
	$result = array();
    $update = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
   
        
    $sql = "SELECT * FROM merchant_types WHERE eStatus = 'Active'"; 
    $statement = $obj->query($sql); 
    $merchantTypeData = $statement ->fetchAll(); 


    $sql = "SELECT me.iMerchantId, me.vUserName, me.vStoreName, me.vStoreAddress, me.vLatitude, me.vLongitude, me.vLogo, pc.vCategoryName, pi.* FROM merchants as me LEFT JOIN products as pi ON pi.iMerchantId = me.iMerchantId LEFT JOIN product_category as pc ON pc.iCategoryId = pi.iCategoryId WHERE pi.vProductName != '' AND pi.eStatus = 'Active'";
    $statement = $obj->query($sql); 
    $products = $statement ->fetchAll();

    for ($i = 0; $i < count($products); $i++) {
        $id = $products[$i]['iMerchantId'];
        $productsArr[$i] = $products[$i];
        $imageData = array();
        $imgArr = explode(",", $productsArr[$i]['vImages']);
        for ($k = 0; $k < count($imgArr); $k++) {
            $imageData[$k]['vImage'] = $baseUrl."uploads/products/".$id."/".$imgArr[$k];
        }

        $productsArr[$i]['vThumbnail'] = $baseUrl."uploads/products/".$id."/".$imgArr[0];
        $productsArr[$i]['vLogo'] = $baseUrl."uploads/profile/store/".$id."/".$products[$i]['vLogo'];
        $productsArr[$i]['vImages'] = $imageData;
    }


  
    $featuredItem1['title'] = "Sulit Deals!";
    $featuredItem1['Desc'] = "";
    $featuredItem1['type'] = "HORIZONTAL";
    $featuredItem1['products'] =  $productsArr;

    array_push($featuredProducts, $featuredItem1);

    $featuredItem2['title'] = "FoodTrip and Eat!";
    $featuredItem2['Desc'] = "";
    $featuredItem2['type'] = "HORIZONTAL";
    $featuredItem2['products'] =  $productsArr;

    array_push($featuredProducts, $featuredItem2);


    $featuredItem3['title'] = "FoodTrip and Eat!";
    $featuredItem3['Desc'] = "";
    $featuredItem3['type'] = "VERTICAL";
    $featuredItem3['products'] =  $productsArr;

    array_push($featuredProducts, $featuredItem3);



    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['merchantsTypes'] = $merchantTypeData;
    $messageArray['featured'] =$featuredProducts;//$featuredProducts;
    $messageArray['productList'] = $productsArr;//$featuredProducts;
    $messageArray['notificationCount'] = 6;//$featuredProducts;
        

    echo safe_json_encode($messageArray);
   


?>