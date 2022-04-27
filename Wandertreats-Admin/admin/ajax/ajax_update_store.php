<?php
    header('Content-type: application/json');
    ini_set('display_errors',1);
    date_default_timezone_set("Asia/Manila");

    //WANDERTREATS
    $where =array();
    $updateData = array();
    $messageArray = array();
    unset($where);

    
   
    include_once('../webservice/config.php');
    include_once('../webservice/db_info.php');
    include_once('../webservice/general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();
    
    $updateData = array();

    $StoreId = isset($_REQUEST['StoreId']) ? trim($_REQUEST['StoreId']) : '6';
    $StoreName  = isset($_REQUEST['StoreName']) ? trim($_REQUEST['StoreName']) : 'Store Name 1';
    $StoreType = isset($_POST['StoreType']) ? trim($_POST['StoreType']) : '1';
    $StoreDescription  = isset($_REQUEST['StoreDescription']) ? trim($_REQUEST['StoreDescription']) : 'John';
    $StoreTheme = isset($_REQUEST['StoreTheme']) ? trim($_REQUEST['StoreTheme']) : 'Doe';
    $StoreLocation = isset($_REQUEST['StoreLocation']) ? trim($_REQUEST['StoreLocation']) : 'johndoe';
    $StoreAddress = isset($_REQUEST['StoreAddress']) ? trim($_REQUEST['StoreAddress']) : 'Store Addresss';
    $ContactName = isset($_REQUEST['ContactName']) ? trim($_REQUEST['ContactName']) : '09309296855';
    $ContactEmail = isset($_REQUEST['ContactEmail']) ? trim($_REQUEST['ContactEmail']) : 'demo@demo.com';
    $ContactMobile = isset($_REQUEST['ContactMobile']) ? trim($_REQUEST['ContactMobile']) : '09760449723';
    $ContactTelephone = isset($_REQUEST['ContactTelephone']) ? trim($_REQUEST['ContactTelephone']) : '434734834';

    $EmailVerified = isset($_REQUEST['EmailVerified']) ? trim($_REQUEST['EmailVerified']) : 'Yes';
    $PhoneVerified = isset($_REQUEST['PhoneVerified']) ? trim($_REQUEST['PhoneVerified']) : 'Yes';
    $StoreRatings = isset($_REQUEST['StoreRatings']) ? trim($_REQUEST['StoreRatings']) : '5.0';
    $StoreLatitude = isset($_REQUEST['StoreLatitude']) ? trim($_REQUEST['StoreLatitude']) : '14.0000';
    $StoreLongitude = isset($_REQUEST['StoreLongitude']) ? trim($_REQUEST['StoreLongitude']) : '121.000000';
    $StoreLogo = isset($_REQUEST['StoreLogo']) ? trim($_REQUEST['StoreLogo']) : '121.000000';
    $StoreBanner = isset($_REQUEST['StoreBanner']) ? trim($_REQUEST['StoreBanner']) : '121.000000';

    unset($updateData);
    $where['iMerchantId'] = $StoreId;

    $updateData['vStoreName'] = $StoreName;
    $updateData['iTypeId'] =  $StoreType;
    $updateData['vStoreDesc'] =  $StoreDescription;
    $updateData['vStoreTheme'] =  $StoreTheme;
    $updateData['vStoreLocation'] = $StoreLocation;
    $updateData['vStoreAddress'] = $StoreAddress;

    $updateData['vContactName'] = $ContactName;
    $updateData['vEmail'] = $ContactEmail;
    $updateData['vPhone'] = $ContactMobile;
    $updateData['vTelephone'] =  $ContactTelephone;
    $updateData['vRatings'] =  $StoreRatings;
    $updateData['eEmailVerified'] =  $EmailVerified;
    $updateData['ePhoneVerified'] = $PhoneVerified;
    $updateData['vLatitude'] = $StoreLatitude;
    $updateData['vLongitude'] = $StoreLongitude;
    $updateData['vLogo'] = $StoreLogo;
    $updateData['vImages'] = $StoreBanner;

    $result = myQuery("merchants", $updateData, "update", $where);



    $sql = "SELECT * FROM merchants WHERE iMerchantId = ". $StoreId ;               
    $statement = $obj->query($sql); 
    $adminData = $statement ->fetchAll(); 

    unset($messageArray);
    $messageArray['action'] = 1;  
    $messageArray['iMerchantId'] =  $StoreId;
    $messageArray['message'] = "success";
    $messageArray['fields'] = $updateData;
    // $messageArray['result'] = $adminData;
        

 

   echo safe_json_encode( $messageArray);
    //echo json_encode($administrators);


           
 ?>

