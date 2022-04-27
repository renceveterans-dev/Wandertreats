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
    
    $where = array();
    $updateData = array();
              

    $userId = isset($_REQUEST['UserId']) ? trim($_REQUEST['UserId']) : '6';
    $Firstname  = isset($_REQUEST['Firstname']) ? trim($_REQUEST['Firstname']) : 'Store Name Firstname';
    $Lastname = isset($_POST['Lastname']) ? trim($_POST['Lastname']) : '1';
    $Email  = isset($_REQUEST['Email']) ? trim($_REQUEST['Email']) : 'John';
    $MobileNumber = isset($_REQUEST['MobileNumber']) ? trim($_REQUEST['MobileNumber']) : 'Doe';
    $Country = isset($_REQUEST['Country']) ? trim($_REQUEST['Country']) : 'johndoe';
    $City = isset($_REQUEST['City']) ? trim($_REQUEST['City']) : 'johndoe';
    $Region = isset($_REQUEST['Region']) ? trim($_REQUEST['Region']) : 'Store Addresss';
    $Gender = isset($_REQUEST['Gender']) ? trim($_REQUEST['Gender']) : '09309296855';
    $EmailVerified = isset($_REQUEST['EmailVerified']) ? trim($_REQUEST['EmailVerified']) : 'Yes';
    $PhoneVerified = isset($_REQUEST['PhoneVerified']) ? trim($_REQUEST['PhoneVerified']) : 'Yes';
    $ReferralCode = isset($_REQUEST['ReferralCode']) ? trim($_REQUEST['ReferralCode']) : '5.0';
    $Logout = isset($_REQUEST['Logout']) ? trim($_REQUEST['Logout']) : '14.0000';
    $Blocked = isset($_REQUEST['Blocked']) ? trim($_REQUEST['Blocked']) : '121.000000';
    $AppVersion = isset($_REQUEST['AppVersion']) ? trim($_REQUEST['AppVersion']) : '121.000000';
    $DeviceData = isset($_REQUEST['DeviceData']) ? trim($_REQUEST['DeviceData']) : '121.000000';
    $ProfilePhotoName = isset($_REQUEST['ProfilePhotoName']) ? trim($_REQUEST['ProfilePhotoName']) : '121.000000';
  

    unset($updateData);
    $where['iUserId'] = $userId;
// 
    $updateData['vName'] = $Firstname;
    $updateData['vLastName'] =  $Lastname;
    $updateData['vEmail'] =  $Email;
    $updateData['vPhone'] =  $MobileNumber;
    $updateData['vCountry'] =  "Ph";
    $updateData['vPhoneCode'] =  "63";
    $updateData['vState'] =  "Philippines";
    $updateData['vRegion'] =  $Region;
    $updateData['vCity'] =  $City;
    $updateData['eGender'] = $Gender;
    $updateData['ePhoneVerified'] = $PhoneVerified;
    $updateData['eEmailVerified'] = $EmailVerified;
    $updateData['eLogout'] = $Logout;
    $updateData['eIsBlocked'] = $Blocked;
    $updateData['iAppVersion'] = $AppVersion;
    $updateData['tDeviceData'] = $DeviceData;
    $updateData['vImage'] = $ProfilePhotoName;

    $result = myQuery("register_user", $updateData, "update", $where);



    $sql = "SELECT * FROM register_user WHERE iUserId = '".$userId."'";               
    $statement = $obj->query($sql); 
    $userData = $statement ->fetchAll(); 

    unset($messageArray);
    $messageArray['action'] = 1;  
    $messageArray['iUserId'] =  $userId;
    $messageArray['message'] = "success";
    $messageArray['result'] = $userData;
        

    echo safe_json_encode($messageArray);
    //echo json_encode($administrators);


           
 ?>

