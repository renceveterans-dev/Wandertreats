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


    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '6';
    $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'Admin';
    $categoryName  = isset($_REQUEST['CategoryName']) ? trim($_REQUEST['CategoryName']) : '';
    $categoryDescription = isset($_REQUEST['CategoryDescription']) ? trim($_REQUEST['CategoryDescription']) : '';
    $storeId = isset($_REQUEST['StoreId']) ? trim($_REQUEST['StoreId']) : '';
    
    unset($registerData);
    $registerData['iMerChantId'] = $storeId;
    $registerData['vCategoryName'] = $categoryName;
    $registerData['vCategoryDesc'] = $categoryDescription;
    $registerData['eStatus'] = "Active";
    $registerData['dCreated'] = @date("Y-m-d H:i:s");

    $categoryId = myQuery("product_category", $registerData, "insert_getlastid");


    $sql = "SELECT * FROM product_category WHERE iCategoryId = '".$categoryId."'";               
    $statement = $obj->query($sql); 
    $data = $statement ->fetchAll(); 

    unset($messageArray);
    $messageArray['action'] = 1;  
    $messageArray['iUserId'] =  $categoryId;
    $messageArray['message'] = "success";
    $messageArray['result'] = $data;
        

 

   echo safe_json_encode($messageArray);
    //echo json_encode($administrators);


           
 ?>
