<?php

ini_set('display_errors',0);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Manila");

session_start();

$sessionId = session_id();

//TRIKAROO WEBSERVICE
     
    // use PHPMailer\PHPMailer\PHPMailer;
    // use PHPMailer\PHPMailer\SMTP;
    
    // require 'vendor/autoload.php';
    // require_once 'vendor/twilio/Services/Twilio.php';
    include_once('config.php');
    include_once('general_functions.php');
  

    $database = new Connection();
    
    $db = $database->openConnection();
    $servicetype  = isset($_POST['ServiceType']) ? trim($_POST['ServiceType']) : '';
    $messageArray = array();
    
    $messageArray['response'] = 0;
    
    
  // $servicetype = "LOAD_USER_CONFIGURATION";
   if($servicetype == "LOAD_USER_CONFIGURATION"){
        
        unset($messageArray);
        unset($where);


        
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '0';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '0';
        $fcmRegTokenId  = isset($_POST['token']) ? trim($_POST['token']) : '12121212121212121212121212121212';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '8';
        $userType = isset($_POST['userType']) ? trim ($_POST['userType']) : 'User';
        $appVersion = isset($_POST['appVersion']) ? trim ($_POST['appVersion']) : '0';
        
        $userStatus = "NONE";
        $update['vFirebaseDeviceToken'] = trim($fcmRegTokenId);
        $update['vLatitude'] = $latitude ;
        $update['iAppVersion'] =  $appVersion;
        $update['vLongitude'] = $longitude;
        
        
        if($userType == "Driver"){
            
            $where['iDriverId'] =  $userId;
            $result = myQuery("register_driver",  $update, "update",  $where);
            $messageArray['UPDATE DRIVER'] = "SUCCESSS";
            
              
            $sql = "SELECT * FROM register_driver WHERE iDriverId = '".$userId."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            $userStatus = $profileData[0]['vTripStatus'];
            
            $serviceMode = $profileData[0]['vAppServiceType'];
            
            if($userStatus != "FINISHED" || $userStatus != "ON_REQUEST" || $userStatus != "NONE"){
                
                $sql = "SELECT * FROM trips WHERE iTripId = '".$profileData[0]['iTripId']."'";
                $statement = $db->query($sql); 
                $tripData = $statement ->fetchAll(); 
                
                $messageArray['customerId'] = $tripData[0]['iUserId'];
                $messageArray['orderId'] = $tripData[0]['iOrderId'];
                $messageArray['bookingId'] = $tripData[0]['iCabBookingId'];
            }
            
        
                
            $messageArray['response'] = 1;
            $messageArray['profileData'] = $profileData;
            $messageArray['service'] = $servicetype;
            $messageArray['userType'] = $userType;
            $messageArray['userType'] = $userType;
            $messageArray['userId'] = $profileData[0]['iDriverId'];
            $messageArray['userStatus'] = $userStatus;
            $messageArray['serviceMode'] =$serviceMode;
            $messageArray['notificationCounter'] = countNotifications($userId, $userType);
            $messageArray['deviceInfo'] = $deviceInfo;
            $messageArray['regId'] =  $profileData[0]['vFirebaseDeviceToken'];
            $messageArray['LatestAppVersion'] = constants::TRIKAROODRIVER_VERSION;
            $messageArray['LatestAppVersionCode'] = constants::TRIKAROODRIVER_VERSION_CODE;
            $messageArray['LatestAppVersionPriority'] = constants::TRIKAROODRIVER_VERSION_PRIORITY;
           
            
            $appMode = getApplicationSettingsMode("Trikaroo Driver");
            
            if( $appMode['vValue'] == "Debug"){
                
                $messageArray['userStatus'] = "Debug";
                
            }else if($appMode['vValue'] == "Released"){
                
                $messageArray['userStatus'] = $userStatus;
               
                
            }
            
            if($appVersion != constants::TRIKAROODRIVER_VERSION_CODE && ($profileData[0]['iAppVersionStatus'] == "1"  || $profileData[0]['iAppVersionStatus'] == 1)){
                
                unset($where);
                $where['iDriverId'] =  $userId;
                $update2['iAppVersionStatus'] = 0;
                $result = myQuery("register_driver",  $update2, "update",  $where);
                
                $notification['iUserId'] = $profileData[0]['iDriverId'];
                $notification['vUserType'] = $userType;
                $notification['vTitle'] = "UPDATE YOUR TRIKAROO DRIVER APP";
                $notification['vDescription'] = "New version of TriKaRoo driver app is available. Download now from www.trikaroo.com.ph";
                $notification['vType'] = "APPLICATION_UPDATES";
                $notification['vImage'] = "";
                $notification['vUrl'] = "";
                $notification['vIntent'] = "";
                $notification['vSent'] = "";
                
                createNotification($notification);
                
            }
            
            
            if($deviceInfo != $profileData[0]['tDeviceData']){
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['userType'] = $userType;
                $messageArray['error'] = "AUTO_LOGOUT";
                $messageArray['deviceInfo'] = $deviceInfo;
                $messageArray['olddeviceInfo'] = $profileData[0]['tDeviceData'];
              
            }
            
            $messageArray['notificationCounter'] = countNotifications($userId, $userType);
            
        }
    
        
        if($userType == "User"){

            // echo "HAHA 1";  
            //  echo "</br>"; 
            
            $address = get_CompleteAddress($latitude, $longitude);
        
            $update['vState'] = $address['state'];
            
            $update['vCity'] = $address['city'];


            // echo "HAHA ".  $userId;
            // echo "</br>"; 
            
            

            
            $where['iUserId'] =  $userId;
            $result = myQuery("register_user",  $update, "update",  $where);
            $messageArray['UPDATE_USER'] = "SUCCESSS";

            //  echo "HAHA ".  $userId;
            // echo "</br>"; 

            //      echo "".json_encode($result);
            // echo "</br>";
            
                  
            $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
            $statement = $db->query($sql);
            $profileData = $statement ->fetchAll();  
            
            $profileData[0]['vImgName']  =  $_SERVER['DOCUMENT_ROOT']."/uploads/Profile/". $userType."/".  $userId."/".$profileData[0]['vImgName'];
            
            if($profileData[0]['vTripStatus'] == "BOOKED" || $profileData[0]['vTripStatus'] == "ON_GOING" ){
                
                $userStatus = $profileData[0]['vTripStatus'];
                
                $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$profileData[0]['vBookingNo']."'";
            
                $statement = $db->query($sql);
                
                
                $bookingData = $statement ->fetchAll(); 
                
                $messageArray['userStatus'] = $userStatus;
                $messageArray['bookingNo'] = $profileData[0]['vBookingNo'];
                $messageArray['origin'] =  $bookingData[0]['vSourceAddress'];
                $messageArray['originLat'] = $bookingData[0]['vSourceLatitude'];
                $messageArray['originLong'] = $bookingData[0]['vSourceLongitude'];
                $messageArray['destination'] =  $bookingData[0]['tDestAddress'];
                $messageArray['destinationLat'] = $bookingData[0]['vDestLatitude'];
                $messageArray['destinationLong'] =  $bookingData[0]['vDestLongitude'];
               
                
            }else{
                $userStatus = "NONE";
            }
            
            if(isUserHasExistingPabili($userId) > 0 ){
                
                $messageArray['userPabiliExisting'] = "true";    
                    
            }else{
               $messageArray['userPabiliExisting'] = "false";    
            }
            
            
            $messageArray['response'] = 1;
            $messageArray['profileData'] = $profileData;
            $messageArray['service'] = $servicetype;
            $messageArray['userType'] = $userType;
            $messageArray['userId'] = $userId;
            $messageArray['userStatus'] = $userStatus;
            $messageArray['sessionId'] = $sessionId;
            $messageArray['deviceInfo'] = $deviceInfo;
            $messageArray['regId'] =  $fcmRegTokenId;
            $messageArray['LatestAppVersion'] = constants::TRIKAROO_VERSION;
            $messageArray['LatestAppVersionCode'] = constants::TRIKAROO_VERSION_CODE;
            $messageArray['LatestAppVersionPriority'] = constants::TRIKAROO_VERSION_PRIORITY;
            
            $appMode = getApplicationSettingsMode("Trikaroo");
            
            if( $appMode['vValue'] == "Debug"){
                
                $messageArray['userStatus'] = "Debug";
                
            }else if($appMode['vValue'] == "Released"){
                
                $messageArray['userStatus'] = $userStatus;
               
            }
            
             
            if($appVersion != constants::TRIKAROO_VERSION_CODE && ($profileData[0]['iAppVersionStatus'] == "1"  || $profileData[0]['iAppVersionStatus'] == 1)){
                  unset($where);
                  
                $where['iUserId'] =  $userId;
                $update2['iAppVersionStatus'] = 0;
                $result = myQuery("register_user",  $update2, "update",  $where);
               
            
                $notification['iUserId'] = $profileData[0]['iUserId'];
                $notification['vUserType'] = $userType;
                $notification['vTitle'] = "UPDATE YOUR TRIKAROO APP";
                $notification['vDescription'] = "New version of TriKaRoo app is available. Download now from google play store.";
                $notification['vType'] = "APPLICATION_UPDATES";
                $notification['vImage'] = "";
                $notification['vUrl'] = "";
                $notification['vIntent'] = "";
                $notification['vSent'] = "";
                
                createNotification($notification);
                
            }
            
        
            
            // if($deviceInfo != $profileData[0]['tDeviceData']){
                
            //     $messageArray['iUserId'] = $userId;
            //     $messageArray['response'] = 0;
            //     $messageArray['service'] = $servicetype;
            //     $messageArray['userType'] = $userType;
            //     $messageArray['error'] = "AUTO_LOGOUT";
            //     $messageArray['deviceInfo'] = $deviceInfo;
            //     $messageArray['olddeviceInfo'] = $profileData[0]['tDeviceData'];
              
            // }
            
            $messageArray['notificationCounter'] = countNotifications($userId, $userType);
            
        }
        
    
        if($userType == "Store"){
            unset($where);
            unset($update);
            $update['vFirebaseDeviceToken'] = trim($fcmRegTokenId);
            $update['iAppVersion'] =  $appVersion;

            // unset($where);
            $where['iCompanyId'] =  $userId;
            $result = myQuery("company",  $update, "update",  $where);
            $messageArray['UPDATE_COMPANY'] = "SUCCESSS";
            
            $sql = "SELECT * FROM company WHERE iCompanyId = '". $userId."'";
            $statement = $db->query($sql);
            $companyData = $statement ->fetchAll();
            $companyData[0]['vImage']  =  $_SERVER['DOCUMENT_ROOT']."/uploads/Company/". $companyData[0]['vImage'];
            
            
            $sql = "SELECT * FROM register_seller WHERE iCompanyId = '". $userId."' AND eAcessLevel = 'Manager'";
            $statement = $db->query($sql);
            $managerData = $statement ->fetchAll();
            // $managerData[0]['vImage']  =  $_SERVER['DOCUMENT_ROOT']."/uploads/Profile/". $userType."/".  $managerData[0]['iSellerId']."/".$managerData[0]['vImgName'];
            
            
            $messageArray['response'] = 1;
            $messageArray['companyData'] = $companyData;
            $messageArray['managerData'] = $managerData;
            $messageArray['service'] = $servicetype;
            $messageArray['userType'] = $userType;
            $messageArray['storeId'] = $companyData[0]['iCompanyId'];
            $messageArray['userStatus'] = $userStatus;
            $messageArray['sessionId'] = $sessionId;
            $messageArray['deviceInfo'] = $deviceInfo;
            $messageArray['regId'] =  $fcmRegTokenId;
            $messageArray['LatestAppVersion'] = constants::TRIKAROO_VERSION;
            $messageArray['LatestAppVersionCode'] = constants::TRIKAROO_VERSION_CODE;
            $messageArray['LatestAppVersionPriority'] = constants::TRIKAROO_VERSION_PRIORITY;
            
            $appMode = getApplicationSettingsMode("Trikaroo");
            
            if( $appMode['vValue'] == "Debug"){
                
                $messageArray['userStatus'] = "Debug";
                
            }else if($appMode['vValue'] == "Released"){
                
                $messageArray['userStatus'] = $userStatus;
               
            }
            
             
            if($appVersion != constants::TRIKAROO_VERSION_CODE && ($companyData[0]['iAppVersionStatus'] == "1"  || $companyData[0]['iAppVersionStatus'] == 1)){
                unset($where);
                $where['iUserId'] =  $userId;
                $update2['iAppVersionStatus'] = 0;
                $result = myQuery("company",  $update2, "update",  $where);
               
            
                $notification['iUserId'] = $companyData[0]['iCompanyId'];
                $notification['vUserType'] = $userType;
                $notification['vTitle'] = "UPDATE YOUR TRIKAROO APP";
                $notification['vDescription'] = "New version of TriKaRoo Merchant app is available. Download now from google play store.";
                $notification['vType'] = "APPLICATION_UPDATES";
                $notification['vImage'] = "";
                $notification['vUrl'] = "";
                $notification['vIntent'] = "";
                $notification['vSent'] = "";
                
                createNotification($notification);
                
            }
            
        
            
            // if($deviceInfo != $profileData[0]['tDeviceData']){
                
            //     $messageArray['iUserId'] = $userId;
            //     $messageArray['response'] = 0;
            //     $messageArray['service'] = $servicetype;
            //     $messageArray['userType'] = $userType;
            //     $messageArray['error'] = "AUTO_LOGOUT";
            //     $messageArray['deviceInfo'] = $deviceInfo;
            //     $messageArray['olddeviceInfo'] = $profileData[0]['tDeviceData'];
              
            // }
            
            $messageArray['notificationCounter'] = countNotifications($userId, $userType);
            
        }
        
    
       echo json_encode( $messageArray);
       
    }



       // $servicetype = "LOGIN";
      if($servicetype == "LOGIN"){
        
        //echo "Hello Login";
        
        unset($messageArray);
        
        $username  = isset($_POST['username']) ? trim($_POST['username']) : '09398296855';
        $password  = isset($_POST['password']) ? trim($_POST['password']) : 'K-anne09';
        $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'User';
        $deviceInfo = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : '';
        
        $result = array();
       
        $result1 =  checkEmail($userType, $username);
        $result2 =  checkMobileNumber($userType, $username);
        
    
     //  echo  $result1."</br>".$result2;

        if($result1 == 0 || $result2 == 0){
            
            unset($where);
            
            $result = checkPassword($userType, $username, $password);
            
            if(count( $result) > 0){
                unset($where);
                
                $token = getToken(10);
                
                // $where['tDeviceData'] = $deviceInfo;
                
               
                
                if($userType == "Driver"){
                    
                              
                    $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $result[0]['iDriverId']."'";
                       
                    $statement = $db->query($sql); 
            
                    $profileData = $statement ->fetchAll();  
                    
                    if($deviceInfo != $profileData[0]['tDeviceData']){
                          
                         //NOTIFCATION FOR LOGIN FROM OTHER DEVICE
                        $data['title'] = "Login Alert";
                        $data['description'] = "You have login from other device.";
                        //NOTIFCATION FOREGROUND
                        $data['activity'] = "AUTO_LOGOUT";
                        $data['message'] = "You have login from other device.";
                    
                        notify("Driver", $profileData[0]['iDriverId'], $data);
                    }
                    
                    if($profileData[0]['eIsBlocked'] == "Yes"){
                        $messageArray['response'] = 0;
                        $messageArray['service'] = $servicetype;
                        $messageArray['sessionId'] = $sessionId;
                        $messageArray['success'] = "Failed";
                        $messageArray['error'] = "Blocked";
                        // $messageArray['result'] = $profileData[0];
                    }else if($profileData[0]['eStatus'] == "inactive"){
                    
                        $messageArray['response'] = 0;
                        $messageArray['service'] = $servicetype;
                        $messageArray['sessionId'] = $sessionId;
                        $messageArray['success'] = "Failed";
                        $messageArray['error'] = "inactive";
                    
                    } else {
                        
                        
                        //notify("driver", $result[0]['iDriverId'], "LOGOUT_ALERT", "auto Logout");
                        
                        
                        $where['iDriverId'] =  $profileData[0]['iDriverId'];
                        $updateSession['tSessionId'] =  $token;
                        $updateSession['tDeviceSessionId'] =  $token;
                        $updateSession['tDeviceData'] = $deviceInfo;
                        $updateSession['vLoginAttemptCount'] = 0;
                        $resultUpdate = myQuery("register_driver",  $updateSession, "update",  $where);
                        $messageArray['UPDATE DRIVER'] = "SUCCESSS";
                        $messageArray['iDriverId'] = $profileData[0]['iDriverId'];
                        
                        $messageArray['response'] = 1;
                        $messageArray['service'] = $servicetype;
                        $messageArray['sessionId'] = $sessionId;
                        $messageArray['success'] = "Logged in";
                        $messageArray['tDeviceData'] = $deviceInfo;
                        $messageArray['result'] = $profileData;
                        $messageArray['userData'] = $result;
                    }
                            
                    
                    
                   
                    
                }
            
                
                if($userType == "User"){
                    
                    
                    $sql = "SELECT * FROM register_user WHERE iUserId = '". $result[0]['iUserId']."'";
                       
                    $statement = $db->query($sql); 
            
                    $profileData = $statement ->fetchAll(); 
                    
                    
                    if($deviceInfo != $profileData[0]['tDeviceData']){
                          
                         //NOTIFCATION FOR LOGIN FROM OTHER DEVICE
                        $data['title'] = "Login Alert";
                        $data['description'] = "You have login from other device.";
                        //NOTIFCATION FOREGROUND
                        $data['activity'] = "AUTO_LOGOUT";
                        $data['message'] = "You have login from other device.";
                    
                        notify("User", $profileData[0]['iUserId'], $data);
                    }
                  
                    
                    if($profileData[0]['eIsBlocked'] == "Yes"){
                        $messageArray['response'] = 0;
                        $messageArray['service'] = $servicetype;
                        $messageArray['sessionId'] = $sessionId;
                        $messageArray['success'] = "Failed";
                        $messageArray['error'] = "Blocked";
                        // $messageArray['result'] = $profileData[0];
                    }else{
                       
                        
                     
                        $where['iUserId'] = $result[0]['iUserId'];
                        $updateSession['tSessionId'] =  $token;
                        $updateSession['tDeviceSessionId'] =  $token;
                        $updateSession['tDeviceData'] = $deviceInfo;
                        $updateSession['vLoginAttemptCount'] = 0;
                        $resultUpdate = myQuery("register_user",  $updateSession, "update",  $where);
                        $messageArray['UPDATE  USER'] = "SUCCESSS";
                        $messageArray['iUserId'] = $result[0]['iUserId'];
                        
                        
                        $messageArray['response'] = 1;
                        $messageArray['service'] = $servicetype;
                        $messageArray['sessionId'] = $sessionId;
                        $messageArray['success'] = "Logged in";
                        $messageArray['result'] = $profileData[0];
                        
                    }
                            
                    
                    
                   
                }
                
                if($userType == "Store"){
                    
                    
                    $sql = "SELECT * FROM company WHERE iCompanyId = '". $result[0]['iCompanyId']."'";
                       
                    $statement = $db->query($sql); 
            
                    $profileData = $statement ->fetchAll(); 
                    
                    
                    // if($deviceInfo != $profileData[0]['tDeviceData']){
                          
                    //      //NOTIFCATION FOR LOGIN FROM OTHER DEVICE
                    //     $data['title'] = "Login Alert";
                    //     $data['description'] = "You have login from other device.";
                    //     //NOTIFCATION FOREGROUND
                    //     $data['activity'] = "AUTO_LOGOUT";
                    //     $data['message'] = "You have login from other device.";
                    
                    //     notify("Store", $profileData[0]['iUserId'], $data);
                    // }
                  
  
                    $where['iCompanyId'] = $result[0]['iCompanyId'];
                    $updateSession['tSessionId'] =  $token;
                    $updateSession['tDeviceSessionId'] =  $token;
                    $updateSession['tDeviceData'] = $deviceInfo;
                    // $updateSession['vLoginAttemptCount'] = 0;  
                    $resultUpdate = myQuery("company",  $updateSession, "update",  $where);
                    $messageArray['UPDATE  USER'] = "SUCCESSS";
                    $messageArray['storeId'] = $profileData[0]['iCompanyId'];
                    $messageArray['storeUsername'] = $result[0]['vName'];
                    $messageArray['response'] = 1;
                    $messageArray['storeId'] = $profileData[0]['iCompanyId'];
                    $messageArray['service'] = $servicetype;
                    $messageArray['sessionId'] = $sessionId;
                    $messageArray['success'] = "Logged in";
                    $messageArray['result'] = $profileData[0];
                    $messageArray['profileData'] = $result;
                    $messageArray['userData'] = $result;
                   
                    
                   
                }
       
                
                
              
               
                
                
            }else{
               
                
                
               
 
                
        
            
                      
                    if($userType == "Driver"){
                        
                        if( $result2 == 0){
                            unset($where);
                            $where['vPhone'] = $username;
                            $attemptCount = myQuery("register_driver", array("vLoginAttemptCount"), "selectall", $where);
                            
                  
                            
                            unset($where);
                            $where['vPhone'] = $username;
                            $update_attempt['vLoginAttemptCount'] =(int) $attemptCount[0]['vLoginAttemptCount'] + 1;
                            $result = myQuery("register_driver",  $update_attempt, "update", $where);
                            
                            unset($where);
                            $where['vPhone'] = $username;
                            $attemptCountfinal = myQuery("register_driver", array("vLoginAttemptCount"), "selectall", $where);
                            
                        }
                        
                        
                        if( $result1 == 0){
                             unset($where);
                            $where['vEmail'] = $username;
                            $attemptCount = myQuery("register_driver", array("vLoginAttemptCount"), "selectall", $where);
                         
                            
                            unset($where);
                            $where['vEmail'] = $username;
                            $update_attempt['vLoginAttemptCount'] = (int) $attemptCount[0]['vLoginAttemptCount'] + 1;
                            $result = myQuery("register_driver",  $update_attempt, "update", $where);
                            
                            unset($where);
                            $where['vEmail'] = $username;
                            $attemptCountfinal = myQuery("register_driver", array("vLoginAttemptCount"), "selectall", $where);
                        }
                        
                          
                    }
                    
                    
                    if($userType == "User"){
                        
                        if( $result2 == 0){
                            unset($where);
                            $where['vPhone'] = $username;
                            $attemptCount = myQuery("register_user", array("vLoginAttemptCount"), "selectall", $where);
                            
                  
                            
                            unset($where);
                            $where['vPhone'] = $username;
                            $update_attempt['vLoginAttemptCount'] =(int) $attemptCount[0]['vLoginAttemptCount'] + 1;
                            $result = myQuery("register_user",  $update_attempt, "update", $where);
                             
                            unset($where);
                            $where['vPhone'] = $username;
                            $attemptCountfinal = myQuery("register_user", array("vLoginAttemptCount"), "selectall", $where);
                            
                        }
                        
                        
                        if( $result1 == 0){
                            unset($where);
                            $where['vEmail'] = $username;
                            $attemptCount = myQuery("register_user", array("vLoginAttemptCount"), "selectall", $where);
                         
                            
                            unset($where);
                            $where['vEmail'] = $username;
                            $update_attempt['vLoginAttemptCount'] = (int) $attemptCount[0]['vLoginAttemptCount'] + 1;
                            $result = myQuery("register_user",  $update_attempt, "update", $where);
                            
                            unset($where);
                            $where['vEmail'] = $username;
                            $attemptCountfinal = myQuery("register_user", array("vLoginAttemptCount"), "selectall", $where);
                        }
                        
                          
                    }
                    
                    if($userType == "Store"){
                        
                        $attemptCountfinal[0]['vLoginAttemptCount'] = 0;
                       
                    }
                    
                    
                $messageArray['response'] = 0;
                $messageArray['error'] = "Invalid or wrong password";
                $messageArray['attempt'] = $attemptCountfinal[0]['vLoginAttemptCount'];
                    
            }
           
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['error'] = "Invalid or wrong email";
        }
      
    
       
        
        
       echo json_encode( $messageArray);
       
    }
    
    
    //$servicetype = "LOAD_NEAREST_STORES";

    if($servicetype == "LOAD_NEAREST_STORES"){
        
       
        
        unset($messageArray);
        
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung'; 
        $sourceLat = isset($_POST['sourceLat']) ? trim((float)$_POST['sourceLat']) : '14.660801205030149';
        $sourceLong  = isset($_POST['sourceLong']) ? trim((float)$_POST['sourceLong']) :'120.98053881083287';
        $userId  = isset($_POST['userId']) ? trim((float)$_POST['userId']) :'55';
        $search = isset($_POST['search']) ? trim($_POST['search']) :'Jollibee';
        $userServiceArea = isset($_POST['userServiceArea']) ? trim($_POST['userServiceArea']) :'';
        $storeCategory = isset($_POST['storeCategory']) ? trim($_POST['storeCategory']) :'Restaurant';
        
       // $locationCheck =  check_Address_restriction($sourceLat,$sourceLong,  $userServiceArea);
          
        $sourceLocationArr = array($sourceLat,$sourceLong);
        
       // $restricted = isLocationRestricted( $sourceLocationArr);
        
        
        if(isLocationAllowed($sourceLocationArr)){
            
            
            if(isLocationAllowedForPabili($sourceLocationArr)){
                
                $iServiceId  = 1;
            
                if($search != ""){
                    $searchSql = " WHERE eStatus = 'Active' AND vStoreCategory = '".$storeCategory."' AND (vMainCompany LIKE '%$search%' OR vMainCompany LIKE '$search%' OR vMainCompany LIKE '%$search' OR vCompany LIKE '%$search%' OR vCompany LIKE '$search%' OR vCompany LIKE '%$search')";
                   //  $searchSql = "WHERE co.eStatus = 'Active' AND co.vMainCompany LIKE '%Jollibee%' OR co.vMainCompany LIKE 'Jollibee%' OR co.vMainCompany LIKE '%Jollibee' OR co.vCompany LIKE '%Jollibee%' OR co.vCompany LIKE 'Jollibee%' OR co.vCompany LIKE '%Jollibee'";
                
                }else{
                   $searchSql = " WHERE eStatus = 'Active' AND vStoreCategory = '".$storeCategory."' ";
                  //  $searchSql = " WHERE co.eStatus = 'Active'";
                }
                
            
               
                
                $sql = "SELECT ROUND(( 6371 * acos( cos( radians($sourceLat) ) 
            
                        * cos( radians( vRestuarantLocationLat ) ) 
            
                        * cos( radians( vRestuarantLocationLong ) - radians($sourceLong) ) 
            
                        + sin( radians( $sourceLat) ) 
            
                        * sin( radians( vRestuarantLocationLat ) ) ) ),2) AS distance, vMainCompany, vAvgRating as totalOrders,   FROM company
        
                        HAVING distance < " . constants::LIST_RESTAURANT_LIMIT_BY_DISTANCE . " ORDER BY iCompanyId ASC";
                        
              $sql = "SELECT vCaddress, vRestuarantLocationLat,  vRestuarantLocationLong, vRestuarantLocationLat as distance, vAvgRating as totalOrders, vAvgRating as openHour, vAvgRating as closeHour, vAvgRating as storeStatus, vMainCompany,  company.* FROM company ";
              $sql .= $searchSql ;
              
                   
                $statement = $db->query($sql); 
        
                $result = $statement ->fetchAll();  
                
                $restaurant = array();
                $Nearestrestaurant = array();
            
                
                if(count($result) > 0){
                    
                    $count = 0;
                    
                    for($x = 0; $x < count($result) ; $x++){
                        
                        $distance = distance( $sourceLat, $sourceLong, $result[$x]['vRestuarantLocationLat'], $result[$x]['vRestuarantLocationLong'], "K");
                        
                        $sqlo = "SELECT count(orders.iCompanyId) as totalOrders, DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') FROM orders WHERE DATE(tOrderRequestDate) = CURDATE() AND iCompanyId = '".$result[$x]['iCompanyId']."'";
                        
                        $statement = $db->query($sqlo); 
        
                        $resultTotalOrders = $statement ->fetchAll();  
                        
                        $totalOrdersToday = $resultTotalOrders[0]['totalOrders'];
                      
                        $result[$x]['totalOrders'] = $totalOrdersToday;
                        
                        $result[$x]['distance'] =  number_format((float)$distance, 2, '.', '');
                        
                        $result[$x]['vImage'] =  "http://mallody.ph/uploads/Company/".$result[$x]['vImage'];
                        
                        
                        $result[$x]['vRestuarantLocationLat'] =  $result[$x]['vRestuarantLocationLat'];
                        
                        $result[$x]['vRestuarantLocationLong'] = $result[$x]['vRestuarantLocationLong'];
                        
                        if(isTodayWeekend()){
                            
                            $result[$x]['openHour'] = $result[$x]['vFromSatSunTimeSlot1'];
                            $result[$x]['closeHour'] = $result[$x]['vToSatSunTimeSlot1'];
                            
                        }else{
                            
                            $result[$x]['openHour'] = $result[$x]['vFromMonFriTimeSlot1'];
                            $result[$x]['closeHour'] = $result[$x]['vToMonFriTimeSlot1'];
                        }
                        
                        $current_time = date("h:i a");
                        $begin = $result[$x]['openHour'];
                        $end   = $result[$x]['closeHour'];
                        
                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                        $date2 = DateTime::createFromFormat('H:i:s', $begin);
                        $date3 = DateTime::createFromFormat('H:i:s', $end);
                        
                        if($date1 > $date2 && $date1 < $date3 ){
                            
                            $result[$x]['storeStatus'] = "Open";
                            
                        }else{
                            
                            if( $date3 <= $date2){
                                
                               if( $date1 > $date2 || $date1 < $date3  ){
                                   
                                    $result[$x]['storeStatus'] = "Open";
                               }else{
                                    $result[$x]['storeStatus'] = "Close";
                               }
                                
                            }else{
                                 $result[$x]['storeStatus'] = "Close";
                            }
                            
                           
                        }
                        
                        if($distance <= constants::LIST_RESTAURANT_LIMIT_BY_DISTANCE){
                            
                            array_push($Nearestrestaurant,  $result[$x]);
                        }
                        
                        
                       //  
                        
                    }
                    
                    // for($i=0; $i<count($result)-1; $i++) {
                    //     for($j=0; $j<count($result)-1; $j++)
                    //     {
                    //         if($result[$j]['distance'] > $result[$j+1]['distance']){
                    //           $restaurant= $result[$j+1];
                    //             $result[$j+1]= $result[$j];
                    //             $result[$j]=$restaurant;
                    //         }
                    //     }
                
                    // }
                    
                    for($i=0; $i<count($Nearestrestaurant)-1; $i++) {
                        for($j=0; $j<count($Nearestrestaurant)-1; $j++)
                        {
                            if($Nearestrestaurant[$j]['distance'] > $Nearestrestaurant[$j+1]['distance']){
                              $restaurant= $Nearestrestaurant[$j+1];
                                $Nearestrestaurant[$j+1]= $Nearestrestaurant[$j];
                                $Nearestrestaurant[$j]=$restaurant;
                            }
                        }
                
                    }
                    
                    
                        
                    $messageArray['response'] = 1;
                    $messageArray['service'] = $servicetype;
                    $messageArray['notificationCounter'] = countNotifications($userId, "User");
                    $messageArray['success'] = count($result). " stores found";
                    $messageArray['result'] =  $Nearestrestaurant;
                    //  $messageArray['result'] =  $result;
        
                 
                }else{
                    
                    $messageArray['response'] = 0;
                    $messageArray['error'] = "No stores found";
                    
                    
                   
                }
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['error'] = "Coming Soon.";
            }
            
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['error'] = "Out of Service Area";
          
          
        }
        
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
        
        
        if($deviceInfo != $profileData[0]['tDeviceData']){
                
                unset($messageArray);
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['userType'] = $userType;
                $messageArray['error'] = "AUTO_LOGOUT";
                $messageArray['deviceInfo'] = $deviceInfo;
              
        }

      
        
        
    
        
        echo json_encode( $messageArray) ;
        
        
    }
    

   // $servicetype = "LOAD_FAVORITE_STORES";
    
    if($servicetype == "LOAD_FAVORITE_STORES"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim((float)$_POST['sourceLat']) : '14.6047315';
        $sourceLong  = isset($_POST['sourceLong']) ? trim((float)$_POST['sourceLong']) :'121.0530789';
        $userId  = isset($_POST['userId']) ? trim((float)$_POST['userId']) :'1';
        $userServiceArea = isset($_POST['userServiceArea']) ? trim($_POST['userServiceArea']) :'Quezon City';
        $storeCategory = isset($_POST['storeCategory']) ? trim($_POST['storeCategory']) :'Quezon City';
        
        $sql = "SELECT vFavoriteStores FROM register_user WHERE iUserId = '".$userId."'";
        $statement = $db->query($sql); 
        $userData = $statement ->fetchAll(); 
        
        if($userData[0]['vFavoriteStores'] ==  null || $userData[0]['vFavoriteStores'] == ""){
            $storeFavList = "0";
        }else{
            $storeFavList = $userData[0]['vFavoriteStores'];
        }
        
           //echo 'Favorite stores '.$userData[0]['vFavoriteStores'];
        $sql = "SELECT vCaddress, vRestuarantLocationLat,  vRestuarantLocationLong, vRestuarantLocationLat as distance, vAvgRating as totalOrders, vAvgRating as openHour, vAvgRating as closeHour, vAvgRating as storeStatus, vMainCompany, vStoreCategory as storeCategory, vImage as image, company.* FROM company ";
        $sql .= " WHERE eStatus = 'Active' AND iCompanyId IN(".$storeFavList.")" ;
         
        $statement = $db->query($sql); 
        $result = $statement ->fetchAll();  
        
        $restaurant = array();
        $Nearestrestaurant = array();
        
            
        if(count($result) > 0){
            
            $count = 0;
            
            for($x = 0; $x < count($result) ; $x++){
                
                $distance = distance( $sourceLat, $sourceLong, $result[$x]['vRestuarantLocationLat'], $result[$x]['vRestuarantLocationLong'], "K");
                
                $sqlo = "SELECT count(orders.iCompanyId) as totalOrders, DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') FROM orders WHERE DATE(tOrderRequestDate) = CURDATE() AND iCompanyId = '".$result[$x]['iCompanyId']."'";
                
                $statement = $db->query($sqlo); 

                $resultTotalOrders = $statement ->fetchAll();  
                
                $totalOrdersToday = $resultTotalOrders[0]['totalOrders'];
              
                $result[$x]['totalOrders'] = $totalOrdersToday;
                
                $result[$x]['distance'] =  number_format((float)$distance, 2, '.', '');
                
                $result[$x]['image'] =  "http://mallody.ph/uploads/Company/".$result[$x]['image'];
                
                if(isTodayWeekend()){
                    
                    $result[$x]['openHour'] = $result[$x]['vFromSatSunTimeSlot1'];
                    $result[$x]['closeHour'] = $result[$x]['vToSatSunTimeSlot1'];
                    
                }else{
                    
                    $result[$x]['openHour'] = $result[$x]['vFromMonFriTimeSlot1'];
                    $result[$x]['closeHour'] = $result[$x]['vToMonFriTimeSlot1'];
                }
                
                $current_time = date("h:i a");
                $begin = $result[$x]['openHour'];
                $end   = $result[$x]['closeHour'];
                
                $date1 = DateTime::createFromFormat('H:i a', $current_time);
                $date2 = DateTime::createFromFormat('H:i:s', $begin);
                $date3 = DateTime::createFromFormat('H:i:s', $end);
                
                if($date1 > $date2 && $date1 < $date3 ){
                    
                    $result[$x]['storeStatus'] = "Open";
                    
                }else{
                    
                    if( $date3 <= $date2){
                        
                       if( $date1 > $date2 || $date1 < $date3  ){
                           
                            $result[$x]['storeStatus'] = "Open";
                       }else{
                            $result[$x]['storeStatus'] = "Close";
                       }
                        
                    }else{
                         $result[$x]['storeStatus'] = "Close";
                    }
                    
                   
                }
                
                
                    
                array_push($Nearestrestaurant,  $result[$x]);
               
                
                
               //  
                
            }
        
            for($i=0; $i<count($Nearestrestaurant)-1; $i++) {
                for($j=0; $j<count($Nearestrestaurant)-1; $j++)
                {
                    if($Nearestrestaurant[$j]['distance'] > $Nearestrestaurant[$j+1]['distance']){
                      $restaurant= $Nearestrestaurant[$j+1];
                        $Nearestrestaurant[$j+1]= $Nearestrestaurant[$j];
                        $Nearestrestaurant[$j]=$restaurant;
                    }
                }
        
            }
            
            
                
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['notificationCounter'] = countNotifications($userId, "User");
            $messageArray['success'] = count($result). " stores found";
            $messageArray['result'] =  $Nearestrestaurant;
            //  $messageArray['result'] =  $result;

         
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['error'] = "No stores found";
            
           
        }
    
        echo json_encode( $messageArray) ;
    }
    
   // $servicetype = "LOAD_STORE_CATEGORIES";
    
    if($servicetype == "LOAD_STORE_CATEGORIES"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.6047198';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'121.053085';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
     
        
        $sql = "SELECT vStoreCategory as storeCategory, vImage as image,  vRestuarantLocationLat,  vRestuarantLocationLong, vRestuarantLocationLat as distance, vCompanyColor as categoryColor FROM company";
           
        $statement = $db->query($sql); 

        $result = $statement ->fetchAll(); 
        
        $restaurant = array();
        $Nearestrestaurant = array();
        $category = array();
        
        
        //array_push( $result[] ,  array("Favorite Stores","http://mallody.ph/uploads/StoreCategories/hardware.jpg", "", "", "" ))
        
        if(count($result) > 0){
                
            $count = 0;
            
            for($x = 0; $x < count($result) ; $x++){
                
                $distance = distance( $sourceLat, $sourceLong, $result[$x]['vRestuarantLocationLat'], $result[$x]['vRestuarantLocationLong'], "K");
              
                $result[$x]['image'] = $result[$x]['image'];
                
                $result[$x]['distance'] =  number_format((float)$distance, 2, '.', '');
                
                $result[$x]['storeCategory'] =  $result[$x]['storeCategory'];
                
                
                switch($result[$x]['storeCategory']){
                    
                    case "Restaurant":
                        
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/fastfood-300-2.jpg";
                        $result[$x]['categoryColor'] = "#db0107";
                        
                        
                            
                      
        
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                       
                       
                           
                        
                        break;
                        
                    case "Convenience Store":
                      
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/conv-300.jpg";
                        $result[$x]['categoryColor'] = "#f9a61a";
                        
                      
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                       
                        
                        break;
                    
                    case "Grocery":
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/GROCERY 1 LANDSCAPE.png";
                        $result[$x]['categoryColor'] = "#179448";
                        
                        
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                       
                        
                        break;
                    
                    case "Milktea Shops":
                        
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/milktea-300.jpg";
                        $result[$x]['categoryColor'] = "#7e6023";
                         
                     
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                     
                         
                        break;
                        
                    case "Pharmacy":
                        
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/pharmacy-300.jpg";
                        $result[$x]['categoryColor'] = "#f9a61a";
                        
                       
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                      
                        
                        break;
                    
                    case "Bake Shop":
                        
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/bakery-300.jpg";
                        $result[$x]['categoryColor'] = "#7e6023";
                         
                       
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                       
                         
                        break;
                        
                    case "Office Supply":
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/office_supply.jpg";
                        $result[$x]['categoryColor'] = "#f9a61a";
                        
                        
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                        
                        
                        break;
                        
                    case "General Merchandise":
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/general_merchandise.jpg";
                        $result[$x]['categoryColor'] = "#273d90";
                        
                        
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                    
                        
                        break;
                        
                    case "Cosmetics":
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/cosmetics.jpg";
                        $result[$x]['categoryColor'] = "#273d90";
                        
                       
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                       
                        
                        break;
                        
                    case "Hardware":
                        
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/hardware-300.jpg";
                        $result[$x]['categoryColor'] = "#273d90";
                        
                       
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                       
                        
                        break;
                        
                    case "Beverages":
                        
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/BEVERAGES 1 LANDSCAPE.png";
                        $result[$x]['categoryColor'] = "#273d90";
                        
                       
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
                       
                        
                        break;
                        
                    default:
                        $result[$x]['image'] = "http://mallody.ph/uploads/StoreCategories/milktea-300.jpg";
                        $result[$x]['categoryColor'] = "#273d90";
                        
                       
                            if(!in_array( $result[$x]['storeCategory'] ,$category ) ){
                            
                                array_push($Nearestrestaurant,  $result[$x]);
                                array_push($category,  $result[$x]['storeCategory']);
                            }
               
                        
                      
                        break;
                        
                }

                
            }
            
            
            // $result[$total+1]['image'] = "http://mallody.ph/uploads/StoreCategories/hardware.jpg";
            // $result[$total+1]['distance'] = "";
            // $result[$total+1]['storeCategory'] = "Favorite Stores";
            // $result[$total+1]['vRestuarantLocationLat'] = "";
            // $result[$total+1]['vRestuarantLocationLong'] = "";
            // array_push($Nearestrestaurant, $result[$total+1]);
            // array_push($category,  $result[$total+1]['storeCategory']);
            
            
            
            for($i=0; $i<count($Nearestrestaurant)-1; $i++) {
                for($j=0; $j<count($Nearestrestaurant)-1; $j++)
                {
                    if($Nearestrestaurant[$j]['distance'] > $Nearestrestaurant[$j+1]['distance']){
                      $restaurant= $Nearestrestaurant[$j+1];
                        $Nearestrestaurant[$j+1]= $Nearestrestaurant[$j];
                        $Nearestrestaurant[$j]=$restaurant;
                    }
                }
        
            }
            
            
                
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['success'] = count($result). " stores found";
            $messageArray['result'] =  $Nearestrestaurant;


         
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['error'] = "No stores found";
            
            
           
        }
        
        
        echo json_encode( $messageArray) ;
    
    }
    
    
   // $servicetype = "GET_LATLONG_FROM_ADDRESS";
    
     
    if($servicetype == "GET_LATLONG_FROM_ADDRESS"){
        
        unset($messageArray);
        
         
        $address = isset($_REQUEST['address']) ? trim($_REQUEST['address']) : '31 Annapolis San Juan philippines';
        
        $latlong = get_lat_long($address);
        
        $result = explode(",",$latlong);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['latitude'] = $result[0];
        $messageArray['longitude'] = $result[1];
        // $messageArray['result'] =  $result;
        
        echo json_encode( $messageArray) ;
          
    }
    
    
  //  $servicetype = "LOAD_STORE_DATA";
    
    if($servicetype == "LOAD_STORE_DATA"){
        header('Content-type: text/html; charset=utf-8');
        unset($messageArray);
        
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong ']) :'';
        $mainStore  = isset($_POST['mainStore']) ? trim($_POST['mainStore']) :'';
        $storeId  = isset($_POST['StoreId']) ? trim($_POST['StoreId']) :'3';
        $keyword  = isset($_POST['keyword']) ? trim($_POST['keyword']) :'';
        $userId  = isset($_POST['userId']) ? trim((float)$_POST['userId']) :'55';
        $language = getLanguage("English");
        
        $menu = array();
        $products = array();
        
        $sql = "SELECT vMenu_$language as Menu, fm.iDisplayOrder as displayOrder FROM food_menu as fm WHERE fm.iCompanyId = '" . $storeId . "' AND fm.eStatus='Active' AND (select count(iMenuItemId) from menu_items as mi where mi.iFoodMenuId=fm.iFoodMenuId AND mi.eStatus='Active' AND mi.eAvailable = 'Yes') > 0 ORDER BY fm.iDisplayOrder ASC";
           
        $statement = $db->query($sql); 

        $result = $statement ->fetchAll(); 
        
        $menu = $result;
      
        
        if($keyword == "" || $keyword == '' || $keyword == null){
            
            $sqlf = "SELECT mi.iMenuItemId as itemId, mi.iFoodMenuId as itemMenuId, fm.vMenu_EN as Menu, mi.vItemType_EN as itemName, mi.vItemDesc_EN as itemDesc, mi.fPrice as itemPrice, mi.vImage, mi.iDisplayOrder, mi.vHighlightName
            FROM menu_items as mi LEFT JOIN food_menu as fm on mi.iFoodMenuId = fm.iFoodMenuId WHERE fm.iCompanyId = $storeId AND mi.eStatus='Active' AND (mi.eAvailable = 'Yes' ||  mi.eAvailable = 'No')  ORDER BY mi.iDisplayOrder ASC";
            
        }else{
            
            $sqlf = "SELECT mi.iMenuItemId as itemId, mi.iFoodMenuId as itemMenuId, fm.vMenu_EN as Menu, mi.vItemType_EN as itemName, mi.vItemDesc_EN as itemDesc, mi.fPrice as itemPrice, mi.vImage, mi.iDisplayOrder, mi.vHighlightName
            FROM menu_items as mi LEFT JOIN food_menu as fm on mi.iFoodMenuId = fm.iFoodMenuId WHERE fm.iCompanyId = $storeId AND mi.eStatus='Active' AND mi.eAvailable = 'Yes' AND ( fm.vMenu_EN LIKE '%". $keyword."%' OR  mi.vItemType_EN LIKE '%". $keyword."%') ORDER BY mi.iDisplayOrder ASC";
        
        }
        
        
        $statement = $db->query($sqlf);
        
        $result = $statement ->fetchAll(); 
        
        $products = $result;
        
        
        for($x = 0; $x < count($result) ; $x++){
            
        
            $products[$x]['vImage'] =  "http://mallody.com.ph/grab/webimages/upload/MenuItem/". $products[$x]['vImage'];
            
            $iMenuItemId = $products[$x]['itemId'];
            
            $sql = "SELECT iOptionId,vOptionName,fPrice,eOptionType,eDefault FROM menuitem_options WHERE iMenuItemId = '" . $iMenuItemId . "' AND eStatus = 'Active'";
            
            $statement = $db->query($sql);
        
            $menuOptions = $statement ->fetchAll(); 
            
            if (count( $menuOptions) > 0) {

                for ($i = 0; $i < count( $menuOptions); $i++) {
        
                    $fPrice =  $menuOptions[$i]['fPrice'];
        
                    $fUserPrice = number_format($fPrice * $Ratio, 2);
        
                    $fUserPriceWithSymbol = $currencySymbol . " " . $fUserPrice;
        
                    $menuOptions[$i]['fUserPrice'] = $fUserPrice;
        
                    $menuOptions[$i]['fUserPriceWithSymbol'] = $fUserPriceWithSymbol;
        
                    if ($menuOptions[$i]['eOptionType'] == "Options") {
        
                        $suboptions['options'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Crust"){
                         $suboptions['crust'][] =  $menuOptions[$i];
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Fillings"){
                         $suboptions['fillings'][] =  $menuOptions[$i];
                    }
                        
                    if( $menuOptions[$i]['eOptionType'] == "Sugar Level"){
                         $suboptions['sugarlevel'][] =  $menuOptions[$i];
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Ice Level"){
                         $suboptions['icelevel'][] =  $menuOptions[$i];
                    }
        
        
                    if( $menuOptions[$i]['eOptionType'] == "Size") {
        
                       $suboptions['size'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Drinks") {
        
                       $suboptions['drinks'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "First Drink") {
        
                       $suboptions['drinks1'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Second Drink") {
        
                       $suboptions['drinks2'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Third Drink") {
        
                       $suboptions['drinks3'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Fourth Drink") {
        
                       $suboptions['drinks4'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Fifth Drink") {
        
                       $suboptions['drinks5'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "First Burger") {
        
                       $suboptions['burger1'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Second Burger") {
        
                       $suboptions['burger2'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Third Burger") {
        
                       $suboptions['burger3'][] =  $menuOptions[$i];
        
                    }
                    
                     if( $menuOptions[$i]['eOptionType'] == "Fourth Burger") {
        
                       $suboptions['burger4'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Flavor") {
        
                       $suboptions['flavor'][] =  $menuOptions[$i];
        
                    }
        
        
        
                    if( $menuOptions[$i]['eOptionType'] == "Addon") {
        
                       $suboptions['addon'][] =  $menuOptions[$i];
        
                    }
        
                }
                
               
        
            }

            
            $products[$x]['customization'] = $suboptions;
           
            $products[$x]['currencySymbol'] = "&#x20B1;";
            
            $suboptions = array();
        }
        
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Ok";
        $messageArray['categories'] =  $menu;
        $messageArray['products'] = $products;
        
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
        
        
        // if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
              
        // }
        
        echo json_encode( $messageArray) ;
          
    }
    
    //$servicetype = "CHECKOUT";
    if($servicetype == "CHECKOUT"){
        
        unset($messageArray);
        unset($where);

       // echo "Hello";
                
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung'; 
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '16.6461217';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'120.97959';
        $userAddress  = isset($_POST['userAddress']) ? trim($_POST['userAddress']) :'';
        $deliveryAddress1  = isset($_POST['deliveryAddress1']) ? trim($_POST['deliveryAddress1']) :'Caloocan Metro Manila';
        $deliveryAddress2  = isset($_POST['deliveryAddress2']) ? trim($_POST['deliveryAddress2']) :'';
        $iUserAddressId1  = isset($_POST['deliveryAddress1_id']) ? trim($_POST['deliveryAddress1_id']) :'31';
        $iUserAddressId2  = isset($_POST['deliveryAddress2_id']) ? trim($_POST['deliveryAddress2_id']) :'0';
        $deliveryInstruction  = isset($_POST['deliveryInstruction']) ? trim($_POST['deliveryInstruction']) :'';
        $subtotalAmount  = isset($_POST['subtotalAmount']) ? trim($_POST['subtotalAmount']) :'';
        $totalAmount  = isset($_POST['totalAmount']) ? trim($_POST['totalAmount']) :'640';
        $deliveryChargeAmount  = isset($_POST['deliveryChargeAmount']) ? trim($_POST['deliveryChargeAmount']) :'';
        $isGrocery  = isset($_POST['isGrocery']) ? trim($_POST['isGrocery']) : 'false'; 
        $paymetnMethod  = isset($_POST['paymetnMethod']) ? trim($_POST['paymetnMethod']) :'';
        $discountPrice  = isset($_POST['discountPrice']) ? trim($_POST['discountPrice']) :'';
        $storeName = isset($_POST['storeName']) ? trim($_POST['storeName']) :'Mandarin Sky Seafodd Restaurant';
        $storeId = isset($_POST['storeId']) ? trim($_POST['storeId']) :'67';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'8';
        $userRegId = isset($_POST['userRegId']) ? trim($_POST['userRegId']) :'';
        $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) :'';
        $orderNo = isset($_POST['orderNo']) ? trim($_POST['orderNo']) :'PB21062522115';
        $OrderList = $_POST['cartOrderList'];

        
        unset($where);
        $where['iCompanyId'] = $storeId;
        $companyData = myQuery("company", array("vRestuarantLocationLat",  "vRestuarantLocationLong", "vCompany"), "selectall",  $where);
        
        $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '". $iUserAddressId1."'";
        $statement = $db->query($sql4);
        $serviceAddress = $statement ->fetchAll();

        echo "</br>";
        
        
        $dstanceFromStoretoDeliveryAddres = distance( $companyData[0]['vRestuarantLocationLat'],$companyData[0]['vRestuarantLocationLong'],  $serviceAddress[0]['vLatitude'], $serviceAddress[0]['vLongitude'], "K");

         echo "Distance : ". $dstanceFromStoretoDeliveryAddres ;
        
        if($dstanceFromStoretoDeliveryAddres <= constants::LIST_RESTAURANT_LIMIT_BY_DISTANCE){
            unset($where);
            $where['iUserId'] = $userId;
            $userData = myQuery("register_user", array("vName","vLastName","vPhone", "vEmail", "fRewardPointsBalance"), "selectall",  $where);
            
            $userFirstname = isset($_POST['userFirstname']) ? trim($_POST['userFirstname']) :'';
            $userLastname = isset($_POST['userLastname']) ? trim($_POST['userLastname']) :'';
            $OrderType = "Pabili";
            
            $iUserAddressId = "";
            $iServiceId = 1;
            $vTimeZone = "Asia/Manila";
            
             echo "1 ";
               echo "</br>";
            // date_default_timezone_set('UTC');
    
            $Data_insert['iUserId'] = $userId;
        
            $Data_insert['iCompanyId'] = $storeId;
        
            $Data_insert['iUserAddressId'] = $iUserAddressId1;
            
            $Data_insert['iUserAddressId2'] =  $iUserAddressId2;
            
            $Data_insert['vDeliveryAddress'] = $deliveryAddress1;
            
            $Data_insert['vDeliveryInstruction'] = $deliveryInstruction;
            
            $Data_insert['vDeliveryAddress_2'] = $deliveryAddress2;
            
            $Data_insert['vDeliveryInstruction'] = $deliveryInstruction;
            
            $Data_insert['iDriverId'] = 0;

            $Data_insert['iTripId'] = 0;

            $Data_insert['fDeliveryCharge'] = 0;
           
           // $Data_insert['ePaymentOption'] = $paymetnMethod;
            
            $Data_insert['ePaymentOption'] = $paymetnMethod;
        
            $Data_insert['vOrderNo'] = $orderNo;//GenerateUniqueOrderNo("PB")
        
            $Data_insert['tOrderRequestDate'] = @date("Y-m-d H:i:s");
        
            $Data_insert['dDeliveryDate'] = @date("Y-m-d H:i:s");
        
            $Data_insert['vUserEmail'] = $userData[0]['vEmail'];
        
            $Data_insert['vName'] =  $userData[0]['vName'];
            
            $Data_insert['vPhone'] =  $userData[0]['vPhone'];
            
            $Data_insert['vOrderType'] = $OrderType;
        
            $Data_insert['vLastName'] = $userData[0]['vLastName'];
        
            $Data_insert['vCompany'] = $storeName;
        
            $Data_insert['dDate'] = @date("Y-m-d H:i:s");
            
            $Data_insert['iStatusCode'] = 3001;
             
            $Data_insert['vInstruction'] = $vInstruction;
    
            $Data_insert['vTimeZone'] = $vTimeZone;
        
            $Data_insert['iServiceId'] = $iServiceId;
            
            $result = myQuery("orders",  $Data_insert, "insert");
            
            $Status = "NOT";
            
            $orderId = "";

            echo "</br>";
            echo "Data to insert : ";
            
            if($result){
                
               //  $cartOrderList = array();
                
               //  $cartOrderList = json_decode(stripcslashes($OrderList), true);
                
               //  $where['vOrderNo'] = $Data_insert['vOrderNo'];
                
               //  $iOrderid = myQuery("orders", array("iOrderId"), "selectall",  $where);
                
               //  $orderId = $iOrderid[0]['iOrderId'];
                
               //  $itemNames = "";
                
               //  for($i = 0; $i < count($cartOrderList); $i++) {
                    
               //      if($i == 0){
               //           $itemNames = $cartOrderList[$i]['itemName'];
               //      }else{
               //           $itemNames .= $itemNames.", ".$cartOrderList[$i]['itemName'];
               //      }
                    
                   
    
               //      $Data = array();
                    
               //      $Data['iOrderId'] =  $iOrderid[0]['iOrderId'];
    
               //      $Data['iMenuItemId'] = $cartOrderList[$i]['itemId'];
        
               //      $Data['iFoodMenuId'] = $cartOrderList[$i]['itemCategoryId'];
                    
               //      $Data['iQty'] = $cartOrderList[$i]['itemQty'];
                    
               //      $Data['fOriginalPrice'] = $cartOrderList[$i]['itemPrice'];
                    
               //      $Data['vItemName'] = $cartOrderList[$i]['itemName'];
                        
               //      $Data['fDiscountPrice'] = $cartOrderList[$i]['itemPrice'];
    
               //      $Data['fPrice'] = $cartOrderList[$i]['itemPrice'];
    
               //      $Data['fTotalDiscountPrice'] = $cartOrderList[$i]['itemPrice'];
                    
               //      $Data['vOptionId'] = $cartOrderList[$i]['itemOptionId'];
                    
               //      $Data['vSizeId'] = $cartOrderList[$i]['itemSizeId'];
                    
               //      $Data['vSizePrice'] = $cartOrderList[$i]['itemmSizePrice'];
                    
               //      $Data['vFlavorId'] = $cartOrderList[$i]['itemFlavorId'];
                    
               //      $Data['vFlavorPrice'] = $cartOrderList[$i]['itemmFlavorPrice'];
                    
               //      $Data['vDrinksId'] = $cartOrderList[$i]['itemDrinksId'];
                    
               //      $Data['vDrinksPrice'] = $cartOrderList[$i]['itemmDrinksPrice'];
                    
               //      $Data['vImage'] = $cartOrderList[$i]['itemImage'];
    
               //      $Data['vOptionPrice'] = $cartOrderList[$i]['itemOptionPrice'];
    
               //      $Data['vAddonId'] = $cartOrderList[$i]['itemAddonId'];
    
               //      $Data['vAddonPrice'] = $cartOrderList[$i]['itemAddonPrice'];
                    
               //      $Data['fSubTotal'] = $cartOrderList[$i]['itemSubtotal'];
    
               //      $Data['fTotalPrice'] = $cartOrderList[$i]['itemSubtotal'];
    
               //      $Data['dDate'] = @date("Y-m-d H:i:s");
    
               //      $Data['eAvailable'] = "Yes";
    
               //      $Data['tOptionIdOrigPrice'] = 0;
    
               //      $Data['tAddOnIdOrigPrice'] = 0;
                    
               //      $Data['tOptionAddonAttribute'] = "";
                    
               //      $Data['vDescription'] = $cartOrderList[$i]['itemDesc'];
                    
               //      $Data['vSpecInstruction'] = $cartOrderList[$i]['itemSpecInstruction'];
                    
               //      $Data['vNoItemInstruction'] = $cartOrderList[$i]['itemNoAvailableSpecInstruction'];
                    
               //      $Data['vNoItemInstruction'] = $cartOrderList[$i]['itemNoAvailableSpecInstruction'];
                    
               //      $Data['eManually'] = $cartOrderList[$i]['eManually'];
                    
               //      $result = myQuery("order_details",  $Data, "insert");
                    
               //      $Status = "SUCCESS!!";
                    
               //  }

               //  $serviceCharge = 0;
               //  if($isGrocery == "true" || $isGrocery == "True" || $isGrocery == "Yes"){
               //      $serviceCharge = constants::GROCERY_SERVICE_CHARGE;
               //  }
                
               //  $FnetTotal = (float) $deliveryChargeAmount + (float)  $subtotalAmount;
                
               //  $FGeneratedTotal = (float) $FnetTotal - (float) $discountPrice;
                        
               //  $Data_update_order['fSubTotal'] = $subtotalAmount;
            
               //  $Data_update_order['fOffersDiscount'] = 0;
            
               //  $Data_update_order['fServiceCharge'] = (float) $serviceCharge;
            
               //  $Data_update_order['fDeliveryCharge'] = 0;
            
               //  $Data_update_order['fTax'] = 0;
            
               //  $Data_update_order['fDiscount'] = (float) $discountPrice ;
            
               //  $Data_update_order['vDiscount'] = $discountPrice ;
            
               //  $Data_update_order['fCommision'] = 0;
            
               //  $Data_update_order['fNetTotal'] =  $FnetTotal;
            
               //  $Data_update_order['fTotalGenerateFare'] = $FGeneratedTotal;
            
               //  $Data_update_order['fOutStandingAmount'] = 0;
            
               //  $Data_update_order['fWalletDebit'] = 0;
                
               //  $where['iOrderId'] = $iOrderid[0]['iOrderId'];
                
               //  $result = myQuery("orders",   $Data_update_order, "update", $where);
                
               //  setOrderLogs("3001", $iOrderid[0]['iOrderId']);
                
                
               // $discountPrice = (float) $discountPrice;
                
               //  if($discountPrice != 0 || $discountPrice != 0.0 ){
               //          //USER DATA
            
               //      $useddPoints = (float) constants::MINIMUM_DISCOUNT;
               //      $totalRewardPointsBalance = (float)$userData[0]['fRewardPointsBalance']- $useddPoints;
                   
                    
               //      unset($where);
               //      $where['iUserId'] = $userId;
               //      $userReward_status['fRewardPointsBalance'] =  $totalRewardPointsBalance ;
               //      $result = myQuery("register_user", $userReward_status, "update", $where);
                    
               //      $transactionNo = GenerateUniqueOrderNo("RP");
        
               //      $rewardslogs['iUserId'] = $userId ;
               //      $rewardslogs['vUserType'] = "User";
               //      $rewardslogs['vTransactionType'] = "PABILI";
               //      $rewardslogs['vLabel'] = "Used points";
               //      $rewardslogs['vDescription'] = "";
               //      $rewardslogs['vTransactionNo'] = $Data_insert['vOrderNo'];
               //      $rewardslogs['fPoints'] = (float)   $useddPoints ;
               //      $rewardslogs['fTotalPointsAmount'] = (float)    $totalRewardPointsBalance;
               //      $rewardslogs['eStatus'] = "Used";
               //      $rewardslogs['dDateCreated'] = @date("Y-m-d H:i:s");
                          
               //      $result = myQuery("rewards_user_logs", $rewardslogs, "insert");
                    
               //  }
              
                
               
    
            }
            
            
            $data['title'] = "New Order Received";
            $data['description'] = $orderNo." - ".$itemNames;
            //NOTIFCATION FOREGROUND
            $data['activity'] = "New Order Received";
            $data['message'] = $orderNo." - ".$itemNames;
            notify("Store", $storeId, $data);
            
            $messageArray['response'] = 1;
            $messageArray['notificationCounter'] = countNotifications($userId, "User");
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = $Status;
            $messageArray['orderId'] = $orderId;
            $messageArray['storeId'] = $storeId;
            
            $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            
            // if($deviceInfo != $profileData[0]['tDeviceData']){
                    
            //         unset($messageArray);
            //         $messageArray['response'] = 0;
            //         $messageArray['service'] = $servicetype;
            //         $messageArray['userType'] = $userType;
            //         $messageArray['error'] = "AUTO_LOGOUT";
            //         $messageArray['deviceInfo'] = $deviceInfo;
                  
            // }
            
       }else{
          
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['userType'] = $userType;
            $messageArray['error'] = "OUT OF SERVICE AREA";
            $messageArray['distance'] = $dstanceFromStoretoDeliveryAddres;
            $messageArray['userAddressId'] = $iUserAddressId1;
            $messageArray['storeId'] = $storeId;
            $messageArray['storeName'] = $companyData[0]['vCompany'];
            $messageArray['deviceInfo'] = $deviceInfo;
            
       }
        
        echo json_encode($messageArray) ;
          
    }
    
    // $servicetype = "LOAD_CHECKOUT_SUMMARY";
    
    if($servicetype == "LOAD_CHECKOUT_SUMMARY"){
         
        unset($messageArray);
          
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.605333';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'121.0482399';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung'; 
        $isGrocery  = isset($_POST['isGrocery']) ? trim($_POST['isGrocery']) : 'false'; 

        
        // $range = getApplication_CheckoutRange_Mode();
        $sourceLocationArr = array( $sourceLat, $sourceLong );
        // $destinationLocationArr = array($destinationLat, $destinationLong);
        $sourcelocationId = getLocationArea($sourceLocationArr);
        // $destinationLocationId = getLocationArea($destinationLocationArr);
        
        $sql = "SELECT * FROM register_toda WHERE iLocationId = '".$sourcelocationId."'";
        $statement = $db->query($sql);
        $todaData = $statement ->fetchAll(); 
        
        $todaId = $todaData[0]['iTodaId'];
        $todaName = $todaData[0]['vTodaName'];
        $todaRouteNo = $todaData[0]['vTodaRouteNo'];
        $baseFare = (float) $todaData[0]['iPabiliBaseFare'];
        $farePricePerKm = (float) $todaData[0]['fPricePerKM'];
        $farePricePerMin = (float) $todaData[0]['fPricePerMin'];
        $serviceCharge = (int) $todaData[0]['fServiceCharge'];
        $transactionFee = (float) $todaData[0]['fCommision'];
        $radiusDistance = (float) $todaData[0]['fRadius'];
        
         
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
        $statement = $db->query($sql); 
        $profileData = $statement ->fetchAll();  
        
        $rewardpoints = (float) $profileData[0]['fRewardPointsBalance'];
        
        $discountRate = ($baseFare*$transactionFee) > $rewardpoints ? $discountRate : '0';
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['locationId'] = $sourcelocationId;
        $messageArray['result'] =   $todaData;
        $messageArray['min_deliveryCharge'] = $baseFare;
        $messageArray['max_deliveryCharge'] =  $baseFare+($radiusDistance*$farePricePerKm);
        $messageArray['pabili_rate'] =  $baseFare;
        $messageArray['grocery_rate'] =  constants::GROCERY_SERVICE_CHARGE;
        $messageArray['discount_rate'] =  constants::MINIMUM_DISCOUNT;
      
        echo json_encode( $messageArray) ;
       
        
        
        // if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
              
        // }
        
        
         
    }
    
    
    if($servicetype == "PAY_ORDER"){
        
        unset($messageArray);
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $latlong = get_lat_long($address);
        $result = explode(",",$latlong);
        
              
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['latitude'] = $result[0];
        $messageArray['longitude'] = $result[1];
        $messageArray['result'] = $Data_insert;
        
        echo json_encode($messageArray) ;
          
    }
    
    
   // $servicetype = "LOAD_ORDER_DETAILS";
    
    // echo $servicetype;

    if($servicetype == "LOAD_ORDER_DETAILS"){
        
        unset($messageArray);
        unset($where);
              
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'56';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'28';
        
        
        $orderDetails = array();
        
        //ORDER DATA
        $sql = "SELECT * FROM orders WHERE iOrderId = $orderId ";
        $statement = $db->query($sql);
        $result = $statement ->fetchAll(); 
        
        
        //USER DATA
        $userId =  $result[0]['iUserId'];
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $result[0]['iUserId']."'";
        $statement = $db->query($sql);
        $userData = $statement ->fetchAll(); 
        
        // STORE DATA
        unset($where);
        $where['iCompanyId'] = $result[0]['iCompanyId'];
        $companyData = myQuery("company", array("vCompany", "vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
        
        // $LatLong = get_lat_long($result[0]['vDeliveryAddress']);
        // $addressLatLong = explode(",",$LatLong);
        
        $sql2 = "SELECT sum(iQty) as itemQty FROM order_details WHERE iOrderId = '". $orderId."'";
        $statement = $db->query($sql2);
        $itemQty = $statement ->fetchAll();
        
        $orderDetails['orderId'] = $result[0]['iOrderId'];
        $orderDetails['orderNo'] = $result[0]['vOrderNo'];
        $orderDetails['orderQty'] = $itemQty[0]['itemQty'];
        $orderDetails['orderDate'] = $result[0]['tOrderRequestdate'];
        $orderDetails['orderType'] = $result[0]['vOrderType'];
        
        $orderDetails['orderStoreName'] = $companyData[0]['vCompany'];
        $orderDetails['orderStoreLocation'] = $companyData[0]['vRestuarantLocation'];
        $orderDetails['orderStoreLat'] = $companyData[0]['vRestuarantLocationLat'];
        $orderDetails['orderStoreLong'] = $companyData[0]['vRestuarantLocationLong'];
        
        $storeLocationArr = array( $companyData[0]['vRestuarantLocationLat'],$companyData[0]['vRestuarantLocationLong']);
        
        // FILTERING THE LOCATIONS 
        $destinationLocationId = getLocationArea($storeLocationArr);
        
        $sql = "SELECT * FROM register_toda WHERE iLocationId = '".$destinationLocationId."'";
        $statement = $db->query($sql);
        $todaData = $statement ->fetchAll();
        
        
        // RETRIVING CONSTANTS FARE SYSTEM PER LOCATIONS
        $todaId = $todaData[0]['iTodaId'];
        $todaName = $todaData[0]['vTodaName'];
        $todaRouteNo = $todaData[0]['vTodaRouteNo'];
        $baseFare = (float) $todaData[0]['iPabiliBaseFare'];
        $serviceCharge = (float) $todaData[0]['fServiceCharge'];
        $farePricePerKm =(float) $todaData[0]['fPricePerKM'];
        $farePricePerMin = (float) $todaData[0]['fPricePerMin'];
        $radiusDistance = (int) $todaData[0]['fRadius'];
        
        $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '". $result[0]['iUserAddressId']."'";
        $statement = $db->query($sql4);
        $serviceAddress = $statement ->fetchAll();

        $orderDetails['orderDeliveryName'] =  $serviceAddress[0]['vName']." / ". $serviceAddress[0]['vPhone'];
        $orderDetails['orderDeliveryAddress'] =  $serviceAddress[0]['vServiceAddress'];
        $orderDetails['orderDeliveryAddressLat'] = $serviceAddress[0]['vLatitude'];
        $orderDetails['orderDeliveryAddressLong'] = $serviceAddress[0]['vLongitude'];
        
        if($orderData[0]['vDeliveryAddres_2']  != ""){
            
           $orderDetails['orderDeliveryName2'] =  $serviceAddress[0]['vName']." / ". $serviceAddress[0]['vPhone'];
           $orderDetails['orderDeliveryAddress2'] =  $serviceAddress[0]['vServiceAddress'];
           $orderDetails['orderDeliveryAddressLat2'] = $serviceAddress[0]['vLatitude'];
           $orderDetails['orderDeliveryAddressLong2'] = $serviceAddress[0]['vLongitude'];
            
        //   $orderDetails['orderDeliveryName2'] = $orderData[0]['vDeliveryAddress_2'];
        //   $orderDetails['orderDeliveryAddress2']  = explode(",", get_lat_long($orderData[0]['vDeliveryAddress_2'])) ;
        //   $orderDetails['orderDeliveryAddressLat2']  =   $deliveryAddressTemp2[0];
        //   $orderDetails['orderDeliveryAddressLong2'] =   $deliveryAddressTemp2[1];
           
        } else {
            
            $orderDetails['orderDeliveryName2'] = "";
            $orderDetails['orderDeliveryAddress2'] = "";
            $orderDetails['orderDeliveryAddressLat2']  =  "0";
            $orderDetails['orderDeliveryAddressLong2'] =   "0";
            
        }
        
      
        
        $date = date_create($result[0]['dDate']);

        $orderDetails['orderTime'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
        $orderDetails['orderInstruction'] = $result[0]['vDeliveryInstruction'];
        
        $sql2 = "SELECT od.iMenuItemId, od.vItemName as itemName, od.fOriginalPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc, od.vCancel as itemCancel, vImage as itemImage, vSpecInstruction as specialInstruction, vNoItemInstruction as noItemInstruction FROM order_details as od WHERE od.iOrderId = ".$result[0]['iOrderId'];
        
        $statement = $db->query($sql2);
        
        $items = $statement ->fetchAll(); 
        
        
        $itemsCount = 0;
        
        
        for($i = 0; $i < count($items); $i++) {
            
             $itemsCount = $itemsCount+(int)$items[$i]['itemQty'];
            
            $orderDetails['orderItems'][] =  $items[$i];
            
        }
        
        $orderDetails['orderSubtotalAmount'] = $result[0]['fSubTotal'];
        $orderDetails['orderDeliveryFeeAmount'] = $result[0]['fDeliveryCharge'];
        $orderDetails['orderDiscount'] = $result[0]['fDiscount'];
        $orderDetails['orderEarnings'] = $result[0]['fCommision'];
        $orderDetails['orderCommssion'] = $result[0]['fWalletDebit'];
        $orderDetails['orderServiceCharge'] = $result[0]['fServiceCharge'];
        $orderDetails['orderTotalAmount'] = $result[0]['fTotalGenerateFare'];
        
        $orderDetails['orderQty'] = $itemsCount;
        $orderDetails['orderPaymentMethod'] = $result[0]['ePaymentOption'];
        $orderDetails['pabili_rate'] = $baseFare;
        $orderDetails['grocery_rate'] = $serviceCharge;
        
        if($result[0]['iStatusCode'] == "3009"){
            $orderDetails['orderStatus'] = "Completed";
            
        }else if($result[0]['iStatusCode'] == "3010"){
            $orderDetails['orderStatus'] = "Cancelled";
        }else{
            $orderDetails['orderStatus'] = "Unfinished";
        }
        
        
        if($result[0]['iDriverId'] == "" || $result[0]['iDriverId'] == 0){
            
            $orderDetails['orderSubtotalAmount'] = $result[0]['fSubTotal'];
             $orderDetails['min_deliveryCharge'] = $baseFare;
            $orderDetails['max_deliveryCharge'] =  $baseFare+( $radiusDistance*$farePricePerKm);
            
        }else{
            
             $orderDetails['min_deliveryCharge'] = $baseFare;
             $orderDetails['max_deliveryCharge'] =  $baseFare+( $radiusDistance*$farePricePerKm);
            $orderDetails['orderSubtotalAmount'] = $result[0]['fSubTotal'];
            $orderDetails['orderDeliveryFeeAmount'] = $result[0]['fDeliveryCharge'];
            $orderDetails['orderTotalAmount'] = $result[0]['fTotalGenerateFare'];
    
        }
        

               
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['result'] = $orderDetails;
        $messageArray['iDriverId'] = $result[0]['iDriverId'];
        
        
        
        $messageArray['userId'] = $userData[0]['iUserId'];
        $messageArray['userLastName'] = $userData[0]['vLastName'];
        $messageArray['userFullName'] = $serviceAddress[0]['vName']." / ". $serviceAddress[0]['vPhone'];
        $messageArray['userImage'] = $userData[0]['vImgName'];
             $messageArray['vPhone'] = $userData[0]['vPhone'];
        $messageArray['userLat'] = $userData[0]['vLatitude'];
        $messageArray['userLong'] = $userData[0]['vLongitude'];
        
        
        echo json_encode($messageArray);
        
    // }
         
    }
    
    
    if($servicetype == "CANCEL_ORDER_BY_DRIVER"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'52';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'3';
        $userType  = isset($_POST['userType']) ? trim($_POST['userType']) :'3';
        
        
        $cancelledUpdate['eCancelledBy'] = $userType;
        $cancelledUpdate['iStatusCode'] = "3010";
    
        $where['iOrderId'] = $orderId ;
            
        $result = myQuery("orders",  $cancelledUpdate, "update", $where);
        
              
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['notificationCounter'] = countNotifications($userId, $userType);
        $messageArray['status'] = "Okay";
      
        
        echo json_encode( $messageArray) ;
          
    }
    
    
    
    
    
    if($servicetype == "CANCEL_ORDER"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'52';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'3';
        $userType  = isset($_POST['userType']) ? trim($_POST['userType']) :'3';
        
        
        $cancelledUpdate['eCancelledBy'] = $userType;
        $cancelledUpdate['iStatusCode'] = "3010";
    
        $where['iOrderId'] = $orderId ;
            
        $result = myQuery("orders",  $cancelledUpdate, "update", $where);
        
        sendRequestToUser($userId, "TrackorderActivity","Pabili Order.", "Your have been cancelled your current order transaction.");
        
        setOrderLogs("3010", $orderId);
        
              
        $messageArray['response'] = 1;
        $messageArray['notificationCounter'] = countNotifications($userId, $userType);
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
      
        
        echo json_encode( $messageArray) ;
          
    }
    
    
    
    
    // $servicetype = "SEND_REQUEST_TO DRIVERS";
  
    // if($servicetype == "SEND_REQUEST_TO DRIVERS"){
        
    //     unset($messageArray);
    //     unset($where);
        
    //     $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
    //     $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
    //     $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'56';
    //     $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'1';
        
        
    //     $sql = "SELECT * FROM orders WHERE iOrderId = $orderId ";
    //     $statement = $db->query($sql);
    //     $orderData = $statement ->fetchAll(); 
        
    //     $where['iCompanyId'] = $orderData[0]['iCompanyId'];
    //     $companyAddress = myQuery("company", array("vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
    //     $LatLong = get_lat_long($orderData[0]['vDeliveryAddress']);
    //     $addressLatLong = explode(",",$LatLong);
        
        
    //     $sql = "SELECT vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName FROM register_driver WHERE vAvailability = 'Available' AND (vTripStatus = 'FINISHED' OR vTripStatus = 'NONE')  AND eStatus = 'active'";
    //     $statement = $db->query($sql);
    //     $driverData = $statement ->fetchAll();

    //     $storeAddress = $companyAddress[0]['vRestuarantLocation'];
    //     $storeAddressLat = $companyAddress[0]['vRestuarantLocationLat'];
    //     $storeAddressLong = $companyAddress[0]['vRestuarantLocationLong'];
        
    //     $sql4 = "SELECT vName, vPhone,   vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '".  $orderData[0]['iUserAddressId']."'";
    //     $statement = $db->query($sql4);
    //     $serviceAddress = $statement ->fetchAll();

    //     $deliveryAddress =  $serviceAddress[0]['vServiceAddress'];
    //     $deliveryAddressLat = $serviceAddress[0]['vLatitude'];
    //     $deliveryAddressLong = $serviceAddress[0]['vLongitude'];
       
    //     // $estimatedDuration = get_Duration($storeAddress,  $deliveryAddress, "s");
    //     // $distance = get_Distance($storeAddress,  $deliveryAddress, "km");
        
    //     // if($estimatedDuration == null || $distance == null){
           
    //     //      $address1 = get_Address2($storeAddressLat, $storeAddressLong);
    //     //      $address2 = get_Address2($deliveryAddressLat, $deliveryAddressLong);
    //     //      $estimatedDuration = get_Duration( $address1,  $address2 , "s");
    //     //      $distance = get_Distance($address1,  $address2 , "km");
            
    //     // }
        
    //     $distance = distance( $deliveryAddressLat,$deliveryAddressLong, $storeAddressLat, $storeAddressLong, "K");
    //     $estimatedDuration  = cal_time( $distance, 10);
    
          
    //     $sqld = "SELECT iOrderId, iQty FROM order_details WHERE iOrderId = $orderId";
    //     $statement = $db->query($sqld);
    //     $orderQty = $statement ->fetchAll(); 
    //     $numberOfItems = 0;
        
    //     for($x = 0 ; $x < count( $orderQty ); $x++){
            
    //         $numberOfItems = $numberOfItems + (int)$orderQty[$x]['iQty'];
            
    //     }
        
    //     //GENERATED FARE
        
    //     $itemAmount = (double) $orderData[0]['fNetTotal'];
    //     $generatedFare = constants::FLAT_RATE_PABILI + $itemAmount;
    
    //     $requestArray = array();
    //     $driverArray = array();
    
        
    //     $requestMessage['userId'] = $userId;
    //     $requestMessage['requestType'] = $orderData[0]['vOrderType'];
    //     $requestMessage['amount'] = $orderData[0]['fNetTotal'];
    //     $requestMessage['itemsQty'] = $numberOfItems;
    //     $requestMessage['paymentMethod'] = $orderData[0]['ePaymentOption'];
    //     $requestMessage['orderId'] = $orderId;
    //     $requestMessage['orderId'] = $orderId;
    //     $requestMessage['orderNo'] =  $orderData[0]['vOrderNo'];
    //     $requestMessage['storeId'] = $orderData[0]['iCompanyId'];
        
    //     $requestMessage['storeName'] =  $orderData[0]['vCompany'];
    //     $requestMessage['storeAddress'] =  $companyAddress[0]['vRestuarantLocation'];
    //     $requestMessage['storeAddressLat'] =  $companyAddress[0]['vRestuarantLocationLat'];
    //     $requestMessage['storeAddressLong'] =  $companyAddress[0]['vRestuarantLocationLong'];
        
    //     $requestMessage['deliveryName'] =  get_starred($orderData[0]['vName'])." / ".get_starred($orderData[0]['vPhone']); 
    //     $requestMessage['deliveryAddress'] =  $orderData[0]['vDeliveryAddress'];
    //     $requestMessage['deliveryAddressLat'] =    $addressLatLong[0];
    //     $requestMessage['deliveryAddressLong'] =    $addressLatLong[1];
        
 
    //     $requestMessage['deliveryAddress2'] =  "";
    //     $requestMessage['deliveryAddressLat2'] = "";
    //     $requestMessage['deliveryAddressLong2'] =  "";
        
    //     $requestMessage['distance'] = number_format($distance_Store_to_DeliveryAddress, 2, '.', '') ;
    //     $requestMessage['ETA'] = $estimatedTImeOfArrival;
        

    //     for($i = 0; $i < count( $driverData); $i++) {
            
    //         $driveraddress = get_Address2($driverData[$i]['vLatitude'], $driverData[$i]['vLongitude']);
    //         $driverdistance = get_Distance( $driveraddress,  $storeAddress, "km");
            
    //         if($driveraddress == null || $distance == null){
               
    //              $address1 = get_Address2($driverData[$i]['vLatitude'], $driverData[$i]['vLongitude']);
    //              $address2 = get_Address2($storeAddressLat, $storeAddressLong);
    //              $driverdistance = get_Distance($address1,  $address2 , "km");
                
    //         }
            
    //         if($driverdistance <= constants::MAXIMUM_DISTANCE_RANGE){
                
               
                
    //             $TotalFarePerKM_fromDriver = ($driverdistance > 0.1 ? ceil($driverdistance) : $driverdistance ) * constants::RATE_PER_KM;
    //             $finalDistance = $distance;
                
    //             // $requestMessage['amount'] =  $generatedFare+ $TotalFarePerKM_fromDriver;
                
    //             $requestMessage['amount'] = $orderData[0]['fNetTotal'];
    //             $requestMessage['deliveryFee'] = constants::FLAT_RATE_PABILI+$TotalFarePerKM_fromDriver;
    //             $requestMessage['distance'] = number_format($distance, 2, '.', '') ;
    //             $requestMessage['driverDistance'] = number_format($driverdistance, 2, '.', '') ;
    //             $requestArray[] = $requestMessage;
    //             $driverArray[] = $driverData[$i];
                
                
    //         }
        
    //     }
        
        
    //     for($i = 0; $i < count( $driverArray); $i++) {
        
    //         sendRequestToDriver($driverArray[$i]['iDriverId'], "PABILI_REQUEST", json_encode($requestArray[$i]));
        
    //     }
        
    

    // }
    
      
    if($servicetype == "SEND_REQUEST_TO DRIVERS"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'21';
        
        
        $sql = "SELECT * FROM orders WHERE iOrderId = $orderId ";
        $statement = $db->query($sql);
        $orderData = $statement ->fetchAll(); 
        
        $where['iCompanyId'] = $orderData[0]['iCompanyId'];
        $companyAddress = myQuery("company", array("vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
        // $LatLong = get_lat_long($orderData[0]['vDeliveryAddress']);
        // $addressLatLong = explode(",",$LatLong);
        
        
        $sql = "SELECT vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName FROM register_driver WHERE vAvailability = 'Available' AND (vTripStatus = 'FINISHED' OR vTripStatus = 'NONE')  AND eStatus = 'active'";
        $statement = $db->query($sql);
        $driverData = $statement ->fetchAll();

        $storeAddress = $companyAddress[0]['vRestuarantLocation'];
        $storeAddressLat = $companyAddress[0]['vRestuarantLocationLat'];
        $storeAddressLong = $companyAddress[0]['vRestuarantLocationLong'];
        
        $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '".  $orderData[0]['iUserAddressId']."'";
        $statement = $db->query($sql4);
        $serviceAddress = $statement ->fetchAll();

        $deliveryAddress =  $serviceAddress[0]['vServiceAddress'];
        $deliveryAddressLat = $serviceAddress[0]['vLatitude'];
        $deliveryAddressLong = $serviceAddress[0]['vLongitude'];
       
        $destinationLocationArr = array( $storeAddressLat, $storeAddressLong);
        
        // FILTERING THE LOCATIONS 
        $destinationLocationId = getLocationArea($destinationLocationArr);
        
        $sql = "SELECT * FROM register_toda WHERE iLocationId = '".$destinationLocationId."'";
        $statement = $db->query($sql);
        $todaData = $statement ->fetchAll();
        
        
        // RETRIVING CONSTANTS FARE SYSTEM PER LOCATIONS
        $todaId = $todaData[0]['iTodaId'];
        $todaName = $todaData[0]['vTodaName'];
        $todaRouteNo = $todaData[0]['vTodaRouteNo'];
        $baseFare = (float) $todaData[0]['iBaseFare'];
        $farePricePerKm =(float) $todaData[0]['fPricePerKM'];
        $farePricePerMin = (float) $todaData[0]['fPricePerMin'];
        $radiusDistance = (int) $todaData[0]['fRadius'];
        
        
        // CALCULATION FOR DISATNCE
        $distance = distance( $deliveryAddressLat, $deliveryAddressLong, $storeAddressLat, $storeAddressLong, "K");
        $estimatedDuration  = cal_time( $distance, 10);
    
    
        $sqld = "SELECT iOrderId, iQty FROM order_details WHERE iOrderId = $orderId";
        $statement = $db->query($sqld);
        $orderQty = $statement ->fetchAll(); 
        $numberOfItems = 0;
        
        for($x = 0 ; $x < count( $orderQty ); $x++){
            
            $numberOfItems = $numberOfItems + (int)$orderQty[$x]['iQty'];
            
        }
        
        //GENERATED FARE
        $itemAmount = (double) $orderData[0]['fNetTotal'];
        $generatedFare = $baseFare + $itemAmount;
    
        $requestArray = array();
        $driverArray = array();
    
        
        $requestMessage['userId'] = $userId;
        $requestMessage['requestType'] = $orderData[0]['vOrderType'];
        $requestMessage['amount'] = $orderData[0]['fNetTotal'];
        $requestMessage['itemsQty'] = $numberOfItems;
        $requestMessage['paymentMethod'] = $orderData[0]['ePaymentOption'];
        $requestMessage['orderId'] = $orderId;
        $requestMessage['orderId'] = $orderId;
        $requestMessage['orderNo'] =  $orderData[0]['vOrderNo'];
        $requestMessage['storeId'] = $orderData[0]['iCompanyId'];
        
        $requestMessage['storeName'] =  $orderData[0]['vCompany'];
        $requestMessage['storeAddress'] =  $companyAddress[0]['vRestuarantLocation'];
        $requestMessage['storeAddressLat'] =  $companyAddress[0]['vRestuarantLocationLat'];
        $requestMessage['storeAddressLong'] =  $companyAddress[0]['vRestuarantLocationLong'];
        
        $requestMessage['deliveryName'] =  get_starred($orderData[0]['vName'])." / ".get_starred($orderData[0]['vPhone']); 
        $requestMessage['deliveryAddress'] =  $orderData[0]['vDeliveryAddress'];
        $requestMessage['deliveryAddressLat'] =    $addressLatLong[0];
        $requestMessage['deliveryAddressLong'] =    $addressLatLong[1];
        
        $requestMessage['deliveryAddress2'] =  "";
        $requestMessage['deliveryAddressLat2'] = "";
        $requestMessage['deliveryAddressLong2'] =  "";
        
        $requestMessage['distance'] = number_format($distance_Store_to_DeliveryAddress, 2, '.', '') ;
        $requestMessage['ETA'] = $estimatedTImeOfArrival;
        
        for($i = 0; $i < count( $driverData); $i++) {
            
            $driverdistance  = distance($driverData[$i]['vLatitude'], $driverData[$i]['vLongitude'], $deliveryAddressLat, $deliveryAddressLong, "K");
            $driverdistanceDuration   = cal_time($driverdistance, 10);
            
            if($driverdistance <=  $radiusDistance){
            
                $distance_Double = $distance*2;
                
                $TotalFarePerKM_fromDriver = ($driverdistance > 0.1 ? ceil($distance_Double) : ceil($distance_Double)) * $farePricePerKm;
                $finalDistance = $distance;
                
                // $requestMessage['amount'] =  $generatedFare+ $TotalFarePerKM_fromDriver;
                
                $requestMessage['amount'] = $orderData[0]['fNetTotal'];
                $requestMessage['deliveryFee'] = $baseFare+$TotalFarePerKM_fromDriver;
                $requestMessage['distance'] = number_format($distance_Double, 2, '.', '') ;
                $requestMessage['driverDistance'] = number_format($driverdistance, 2, '.', '') ;
                $requestArray[] = $requestMessage;
                $driverArray[] = $driverData[$i];
                
                
            }
        
        }
        
        
        for($i = 0; $i < count( $driverArray); $i++) {
        
            sendRequestToDriver($driverArray[$i]['iDriverId'], "PABILI_REQUEST", json_encode($requestArray[$i]));
            //echo "Driver : ".$driverArray[$i]['iDriverId']." : Reg Id : ".$driverArray[$i]['vFirebaseDeviceToken']." <br/>";
        
        }
        
    

    }
 
 
 
    if($servicetype == "CLEAR_REQUEST_TO DRIVERS"){
        
        unset($messageArray);
        unset($where);
       
      
        $where['vTripStatus'] = 'ON_REQUEST';
        $driver_status['vTripStatus'] = 'FINISHED';
        $driver_status['iTripId'] = 0;
        
        $result2 = myQuery("register_driver", $driver_status, "update", $where);
        
        unset($where);
        $where['vTripStatus'] = 'ON_GOING';
        $driver_status['vTripStatus'] = 'FINISHED';
        $driver_status['iTripId'] = 0;
        
        $result2 = myQuery("register_driver", $driver_status, "update", $where);
        
        echo "UPDATE";

    }
    
  // $servicetype = "LOAD_TRACK_DETAILS";

    if($servicetype == "LOAD_TRACK_DETAILS"){
         
        unset($messageArray);
        unset($where);
              
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'8';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'6';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) :'54';
        
        $tracklist = array();
        
        //ORDER DATA
    
                
        $sql = "SELECT * FROM orders WHERE iOrderId = '". $orderId."'";

        $statement = $db->query($sql);
        
        $orderData = $statement ->fetchAll(); 
        
        $storeId = $orderData[0]['iCompanyId'];
        
        $tripId = $orderData[0]['iTripId'];
        
        // $sql2 = "SELECT mi.iMenuItemId as itemId,  mi.vItemType_EN as itemName, mi.fPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc, od.vCancel as itemCancel FROM menu_items as mi 
        // LEFT JOIN order_details as od ON mi.iMenuItemId = od.iMenuItemId WHERE od.iOrderId = ". $orderId;
        
        $sql2 = "SELECT od.iMenuItemId, od.vItemName as itemName, od.fOriginalPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc,
        od.vCancel as itemCancel FROM order_details as od WHERE od.iOrderId = ". $orderId;
       

        $statement = $db->query($sql2);
        
        $items = $statement ->fetchAll(); 
        
        $orderQty = count($items);
        
        $subtotal =  (float)$orderData[0]['fSubTotal'];
        $deliveryCharge = (float)$orderData[0]['fDeliveryCharge'];
        
        $orderTotalAmount =  $subtotal+ $deliveryCharge ;
        
        
        $orderDate =  $orderData[0]['tOrderRequestDate'] ;
        
        for($i = 0; $i < count($items); $i++) {
            
            $orderDetails['orderItems'][] =  $items[$i];
            
        }
        
        $driverId = $orderData[0]['iDriverId'];
        
        unset($where);
        
        unset($where);
        $where['iCompanyId'] = $orderData[0]['iCompanyId'];
            
        $companyAddress = myQuery("company", array("vStoreCategory", "vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
        
        $storeAddress = $companyAddress[0]['vRestuarantLocation'];
        $storeAddressLat = $companyAddress[0]['vRestuarantLocationLat'];
        $storeAddressLong = $companyAddress[0]['vRestuarantLocationLong'];
        
        $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '".   $orderData[0]['iUserAddressId']."'";
        $statement = $db->query($sql4);
        $serviceAddress = $statement ->fetchAll();

        // $orderDetails['orderDeliveryName'] =  ;
        // $orderDetails['orderDeliveryAddress'] =  $serviceAddress[0]['vServiceAddress'];
        // $orderDetails['orderDeliveryAddressLat'] = $serviceAddress[0]['vLatitude'];
        // $orderDetails['orderDeliveryAddressLong'] = $serviceAddress[0]['vLongitude'];
        
        $deliveryAddress = $serviceAddress[0]['vServiceAddress'];
        $deliveryAddressLat  =   $serviceAddress[0]['vLatitude'];
        $deliveryAddressLong =   $serviceAddress[0]['vLongitude'];
        
        if($orderData[0]['vDeliveryAddres_2']  != ""){
            
            $deliveryAddress_2 = $serviceAddress[0]['vServiceAddress'];
            $deliveryAddressLat_2  =   $serviceAddress[0]['vLatitude'];
            $deliveryAddressLong_2 =   $serviceAddress[0]['vLongitude'];
            
            // $deliveryAddress_2 = $orderData[0]['vDeliveryAddress_2'];
            // $deliveryAddressTemp2 = explode(",", get_lat_long($orderData[0]['vDeliveryAddress_2'])) ;
            // $deliveryAddressLat_2  =   $deliveryAddressTemp2[0];
            // $deliveryAddressLong_2 =   $deliveryAddressTemp2[1];
        }else {
            
            $deliveryAddress_2 = "";
            $deliveryAddressTemp2 = "";
            $deliveryAddressLat_2  =  "0";
            $deliveryAddressLong_2 =   "0";
            
        }
        
        
        //STILL NO DRIVER ASSIGNED
        
        if($driverId == '0' || $driverId == 0 || $driverId == '0' || $driverId == ''){
            
            unset($where);
            
          

         //DRIVER ASSIGNED
        
        }else{
             
            unset($where);
            
            $where['iDriverId'] = $orderData[0]['iDriverId'];
            $driverData = myQuery("register_driver", array("iDriverId", "vName", "vLastName", "vPhone", "vLatitude", "vLongitude", "vImage" , "vLatitude", "vLongitude", "iTripId", "vTodaLine", "vPlateNo", "vAvgRating"), "selectall",  $where);
            
            
            // $driverAddress = get_Address($driverData[0]['vLatitude'],$driverData[0]['vLongitude']);
            $sql = "SELECT * FROM trips WHERE iTripId = '".$tripId."'";
            
            $statement = $db->query($sql);
            $tripData = $statement ->fetchAll(); 
            
           // $orderId = $tripData[0]['iOrderId'];
            
            $distance = calculateDistance($tripData[0]['tStartLat'],$tripData[0]['tStartLong'], $tripData[0]['tEndLat'], $tripData[0]['tEndLong'], "K");
            
            $duration =  get_Duration($tripData[0]['tSaddress'], $tripData[0]['tDaddress']);
          
            $messageArray['driverData'] = $driverData;
            $messageArray['tripStatus'] = $tripData[0]['iActive'];
            $messageArray['startLat'] = $tripData[0]['tStartLat'];
            $messageArray['startLong'] = $tripData[0]['tStartLong'];
            $messageArray['destLat'] = $tripData[0]['tEndLat'];
            $messageArray['destLong'] = $tripData[0]['tEndLong'];
            $messageArray['tripItinerary'] = $tripData[0]['vTripItinerary'];
            $messageArray['distance'] = $distance;
            $messageArray['duration'] = $duration;
            $messageArray['processedTime'] = "--";
            
        }
        
       
        //TRACK PROGRESS
        
        
        if($orderData[0]['iStatusCode'] != "3010"){

            //NOT CANCELLED
           
            if($companyAddress[0]['vStoreCategory'] == "Partner Store"){
                
                 $sql2 = "SELECT vStatus, vStatus_Track, iStatusCode FROM order_status WHERE iOrderStatusId IN(15, 16, 17, 19, 20) ORDER BY iDisplayOrder ASC";
            
                $trackSql = "SELECT orders.iOrderId,  DATE_FORMAT(order_status_logs.dDate,'%H:%i') as timeStamp, order_status.vStatus, order_status.vStatus_Track, order_status.iStatusCode FROM orders
                    LEFT JOIN  order_status_logs ON orders.iOrderId = order_status_logs.iOrderId
                    LEFT JOIN order_status ON order_status.iStatusCode = order_status_logs.iStatusCode  WHERE iOrderStatusId IN(15, 16, 17, 19,20) AND orders.iOrderId = ". $orderId." ORDER BY order_status.iDisplayOrder ASC";
                    
            
            }else{
               
                $sql2 = "SELECT vStatus, vStatus_Track, iStatusCode FROM order_status WHERE iOrderStatusId IN(15, 16, 17, 19) ORDER BY iDisplayOrder ASC";
            
                $trackSql = "SELECT orders.iOrderId,  DATE_FORMAT(order_status_logs.dDate,'%H:%i') as timeStamp, order_status.vStatus, order_status.vStatus_Track, order_status.iStatusCode FROM orders
                    LEFT JOIN  order_status_logs ON orders.iOrderId = order_status_logs.iOrderId
                    LEFT JOIN order_status ON order_status.iStatusCode = order_status_logs.iStatusCode  WHERE iOrderStatusId IN(15, 16, 17, 19) AND orders.iOrderId = ". $orderId." ORDER BY order_status.iDisplayOrder ASC";
            }

                    
        }else{
            
            // echo "</br>HAHAHAHAHAHAHAH</br>";
            
            
            if($companyAddress[0]['vStoreCategory'] == "Partner Store"){

                $sql2 = "SELECT orders.iOrderId,  DATE_FORMAT(order_status_logs.dDate,'%H:%i') as timeStamp, order_status.vStatus, order_status.vStatus_Track, order_status.iStatusCode FROM orders
                    LEFT JOIN  order_status_logs ON orders.iOrderId = order_status_logs.iOrderId
                    LEFT JOIN order_status ON order_status.iStatusCode = order_status_logs.iStatusCode  WHERE iOrderStatusId IN(15, 16, 17, 18,20) AND orders.iOrderId = ". $orderId." ORDER BY order_status.iStatusCode ASC";

                $trackSql = "SELECT orders.iOrderId,  DATE_FORMAT(order_status_logs.dDate,'%H:%i') as timeStamp, order_status.vStatus, order_status.vStatus_Track, order_status.iStatusCode FROM orders
                            LEFT JOIN  order_status_logs ON orders.iOrderId = order_status_logs.iOrderId
                            LEFT JOIN order_status ON order_status.iStatusCode = order_status_logs.iStatusCode  WHERE iOrderStatusId IN(15, 16, 17, 18,20) AND orders.iOrderId = ". $orderId." ORDER BY order_status.iStatusCode ASC";

            }else{


                $sql2 = "SELECT orders.iOrderId,  DATE_FORMAT(order_status_logs.dDate,'%H:%i') as timeStamp, order_status.vStatus, order_status.vStatus_Track, order_status.iStatusCode FROM orders
                    LEFT JOIN  order_status_logs ON orders.iOrderId = order_status_logs.iOrderId
                    LEFT JOIN order_status ON order_status.iStatusCode = order_status_logs.iStatusCode  WHERE iOrderStatusId IN(15, 16, 17, 18,20) AND orders.iOrderId = ". $orderId." ORDER BY order_status.iStatusCode ASC";

                $trackSql = "SELECT orders.iOrderId,  DATE_FORMAT(order_status_logs.dDate,'%H:%i') as timeStamp, order_status.vStatus, order_status.vStatus_Track, order_status.iStatusCode FROM orders
                            LEFT JOIN  order_status_logs ON orders.iOrderId = order_status_logs.iOrderId
                            LEFT JOIN order_status ON order_status.iStatusCode = order_status_logs.iStatusCode  WHERE iOrderStatusId IN(15, 16, 17, 18) AND orders.iOrderId = ". $orderId." ORDER BY order_status.iStatusCode ASC";

            }
            
            //CANCELLED
           // $sql2 = "SELECT vStatus, vStatus_Track, iStatusCode FROM order_status WHERE iOrderStatusId IN(15, 16, 17, 18) ORDER BY iStatusCode ASC";
        
            
        }
        
        
        
        
       
        
        $statement = $db->query($sql2);
        
        $track = $statement ->fetchAll(); 

       
        
        
        $statementChecked = $db->query($trackSql);
        
        $trackCheck =  $statementChecked ->fetchAll(); 
        
        for($i = 0; $i < count( $track); $i++) {
            
            $tracklist['track'][] = $track[$i];
            
        }
        
        for($i = 0; $i < count($trackCheck); $i++) {
            
            $trackChecklist['trackCheck'][] = $trackCheck[$i];
            
        }
        
       
        $messageArray['notificationCounter'] = countNotification($userId, "User");
        $messageArray['response'] = $orderData[0]['vOrderNo'];
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['orderStatus'] =  $orderData[0]['iStatusCode'];
        $messageArray['orderNo'] =  $orderData[0]['vOrderNo'];
        $messageArray['orderId'] =  $orderData[0]['iOrderId'];
        $messageArray['orderQty'] =  $orderQty;
        $messageArray['orderDeliveryFee'] =  $orderData[0]['fDeliveryCharge'];
        $messageArray['orderTotalAmount'] =  $orderTotalAmount ;
        $date = date_create($orderData[0]['tOrderRequestDate']);
        $messageArray['orderDate'] =  date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
        
        $messageArray['storeAddress'] = $companyAddress[0]['vRestuarantLocation'];
        $messageArray['storeAddressLat'] =   $companyAddress[0]['vRestuarantLocationLat'];
        $messageArray['storeAddressLong'] = $companyAddress[0]['vRestuarantLocationLong'];
      
        $messageArray['deliveryAddress'] = $deliveryAddress;
        $messageArray['deliveryAddressLat'] = $deliveryAddressLat;
        $messageArray['deliveryAddressLong'] = $deliveryAddressLong;
        
       
        $messageArray['deliveryAddress2'] = $deliveryAddress_2;
        $messageArray['deliveryAddressLat2'] = $deliveryAddressLat_2;
        $messageArray['deliveryAddressLong2'] = $deliveryAddressLong_2;
         
        $messageArray['result'] = $tracklist;
        $messageArray['resultTrack'] =  $trackChecklist;
       
      
        echo json_encode($messageArray);
              
    }
    
  
 /// $servicetype = "UPDATE_TRACK_STATUS";
  
    
    if($servicetype == "UPDATE_TRACK_STATUS"){
        
        unset($messageArray);
        unset($order_update_status);
        unset($where);
              
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'52';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'61';
        $status  = isset($_POST['status']) ? trim($_POST['status']) :'On the Way to Deliver';
        $driverId = isset($_POST['driverId']) ? trim($_POST['driverId']) :'17';
        
        $where['iOrderId'] =  $orderId ;
        $orderData = myQuery("orders", array("iDriverId","iOrderId", "iUserId", "iCompanyId","vOrderNo", "iUserAddressId", "vDeliveryAddress", "vDeliveryAddress_2", "fTotalGenerateFare", "fDeliveryCharge", "fSubTotal"), "selectall",  $where);

        $userId = $orderData[0]['iUserId'];
        $storeId = $orderData[0]['iCompanyId'];
        $orderNo = $orderData[0]['vOrderNo'];
         
        unset($where);
        $where['iDriverId'] = $driverId ;
        $tripData = myQuery("register_driver", array("iTripId","vWalletBalance","iTodaId"), "selectall",  $where);
         
        if($status == "Driver Found"){
            
            $newStatusCode = "3002";

            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = trim("ON_GOING");
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);
            
            //sendRequestToUser($userId, "TrackorderActivity", "Pabili Order.", "Driver found. Driver is on the way to the store.");

            $data['title'] = "Pabili order";
            $data['description'] = "Driver found. Driver is on the way to the store.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_FOUND";
            $data['message'] = "Driver found. Driver is on the way to the store.";
            notify("User", $userId, $data);



            $data['title'] = "Pabili order";
            $data['description'] = "Driver found. Driver is on the way to the store.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_FOUND";
            $data['message'] = "Driver found. Driver is on the way to the store.";
            notify("Store",  $storeId , $data);
            
            setOrderLogs("3002", $orderId);
            
            // $notification['iUserId'] = $userId;
            // $notification['vUserType'] = $userType;
            // $notification['vTitle'] = "Order No. ".$orderData[0]['vOrderNo'];
            // $notification['vDescription'] = "Driver found. Driver is on the way to the store.";
            // $notification['vType'] = "ORDER_TRACK";
            // $notification['vImage'] = "";
            // $notification['vUrl'] = "";
            // $notification['vIntent'] =  $orderData[0]['iOrderId'].",".$orderData[0]['iCompanyId'];
            // $notification['vSent'] = "";
            
            // createNotification($notification);
              
        }else if($status == "Going to Store"){
            
            $newStatusCode = "3003";
            
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = trim("ON_GOING");
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);
            


            $data['title'] = "Pabili order";
            $data['description'] = "Driver found. Driver is on the way to the store.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_FOUND";
            $data['message'] = "Driver found. Driver is on the way to the store.";
            notify("User", $userId, $data);


            $data['title'] = "Pabili Order ".$orderNo;
            $data['description'] = "Driver found. Driver is on the way to the store.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_FOUND";
            $data['message'] = "Driver found. Driver is on the way to the store.";
            notify("Store",  $storeId , $data);
            
           
            
            setOrderLogs("3003", $orderId);
            
        }else if($status == "At the Store"){
            
            $newStatusCode = "3004";
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = trim("ON_PROCESS");
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);


            $data['title'] = "Driver Arrived";
            $data['description'] = "Driver has arrived at the store and currently processing your order. There may be adjustment on your order.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_ARRIVED";
            $data['message'] = "Driver has arrived at the store and currently processing your order. There may be adjustment on your order.";
            notify("User", $userId, $data);


            $data['title'] = "Pabili Order ".$orderNo;
            $data['description'] = "Driver has arrived at your store and currently processing the order.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_ARRIVED";
            $data['message'] = "Driver has arrived at your store and currently processing the order.";
            notify("Store",  $storeId , $data);


            // sendRequestToUser($userId, "DRIVER_ARRIVED","Pabili Order.", "Driver has arrived at the store and currently processing your order. There may be adjustment on your order.");
            // $createNotif['iUserId'] = $userId;
            // $createNotif['vUserType'] = "User";
            // $createNotif['vTitle'] = "Order No. ".$orderData[0]['vOrderNo'];;
            // $createNotif['vDescription'] = "Driver has arrived at the store and currently processing your order";
            // $createNotif['vType'] =  "ORDER_TRACK";
            // $createNotif['vImage'] = "";
            // $createNotif['vUrl'] = "";
            // $createNotif['vIntent'] = $orderData[0]['iOrderId'].",".$orderData[0]['iCompanyId'];
            // $createNotif['vSent'] = "";
            // $createNotif['eStatus'] = "unread";  
            // $createNotif['dDateCreated'] = @date("Y-m-d H:i:s");
            // $result = myQuery("notifications", $createNotif, "insert");
            
            // createNotification($notification);
            
            
            
            setOrderLogs("3004", $orderId);
            
             
        }else if($status == "Order at Process"){
            
            $newStatusCode = "3005";
            
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = trim("ON_PROCESS");
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);
            
            $data['title'] = "Pabili Order";
            $data['description'] = "Your order now is being process.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_ARRIVED";
            $data['message'] = "Your order now is being process.";
            notify("User", $userId, $data);

  
            // $data['title'] = "Pabili Order";
            // $data['description'] = "Your order now is being process.";
            // //NOTIFCATION FOREGROUND
            // $data['activity'] = "DRIVER_ARRIVED";
            // $data['message'] = "Your order now is being process.";
            // notify("Store",  $storeId , $data);





            //sendRequestToUser($userId, "TrackorderActivity","Pabili Order.", "Your order now is being process.");



            // $data['title'] = "Pabili order";
            // $data['description'] = "Driver has arrived at the store and currently processing your order. There may be adjustment on your order.";
            // //NOTIFCATION FOREGROUND
            // $data['activity'] = "DRIVER_ARRIVED";
            // $data['message'] = "Your order now is being process.";
            // notify("User", $userId, $data);
            
            setOrderLogs("3005", $orderId);
              
            // $createNotif['iUserId'] = $userId;
            // $createNotif['vUserType'] = "User";
            // $createNotif['vTitle'] = "Order No. ".$orderData[0]['vOrderNo'];
            // $createNotif['vDescription'] = "Your order now is being process.";
            // $createNotif['vType'] =  "ORDER_TRACK";
            // $createNotif['vImage'] = "";
            // $createNotif['vUrl'] = "";
            // $createNotif['vIntent'] = $orderData[0]['iOrderId'].",".$orderData[0]['iCompanyId'];
            // $createNotif['vSent'] = "";
            // $createNotif['eStatus'] = "unread";  
            // $createNotif['dDateCreated'] = @date("Y-m-d H:i:s");
            // $result = myQuery("notifications", $createNotif, "insert");
             
        }else if($status == "On the Way to Deliver"){
            
            $newStatusCode = "3006";
            
            unset($where);
            $where['iTripId'] =  $tripData[0]['iTripId'];
            $updatePreviousTrip['iActive'] =  "Finished" ;
            $updatePreviousTrip['tEndDate'] =   @date("Y-m-d H:i:s");
            $updatePreviousTripResult = myQuery("trips",  $updatePreviousTrip, "update",  $where);
            
            unset($where);
            $where['iCompanyId'] = $orderData[0]['iCompanyId'];
            $companyData = myQuery("company", array("vRestuarantLocationLat", "vRestuarantLocationLong", "vRestuarantLocation"), "selectall",  $where);
            
            
            $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '". $orderData[0]['iUserAddressId']."'";
            $statement = $db->query($sql4);
            $serviceAddress = $statement ->fetchAll();
   
            // $orderDetails['orderDeliveryName'] =  $serviceAddress[0]['vName'];
            // $orderDetails['orderDeliveryAddress'] =  $serviceAddress[0]['vServiceAddress'];
            // $orderDetails['orderDeliveryAddressLat'] = $serviceAddress[0]['vLatitude'];
            // $orderDetails['orderDeliveryAddressLong'] = $serviceAddress[0]['vLongitude'];
            
            // $userLatLong = get_lat_long($orderData[0]['vDeliveryAddress']);
            // $destAddressLatLong = explode(",", $userLatLong);
            
            // CREATE TRIP
        
            $createTrip['iOrderId'] =  $orderId;
            $createTrip['iCompanyId'] =  $orderData[0]['iCompanyId'];
            $createTrip['iUserId'] =   $userId;
            $createTrip['tStartDate'] =  @date("Y-m-d H:i:s");
        
            $createTrip['tStartLat'] = $companyData[0]['vRestuarantLocationLat'];
            $createTrip['tStartLong'] = $companyData[0]['vRestuarantLocationLong'];
            
            $createTrip['tEndLat'] =   $serviceAddress[0]['vLatitude'];
            $createTrip['tEndLong'] =  $serviceAddress[0]['vLongitude'];
            
            $createTrip['tSaddress'] =$companyData[0]['vRestuarantLocation'];
            $createTrip['tDaddress'] = $serviceAddress[0]['vServiceAddress'];
            
            if($orderData[0]['vDeliveryAddress_2'] == ""){
                $createTrip['vTripItinerary'] = "LastTrip";
            }else{
                $createTrip['vTripItinerary'] = "SecondTrip";
            }
            
            
            $result5 = myQuery("trips", $createTrip, "insert");
            
         
            
            
            unset($where);
            $where['iOrderId'] = $orderId;
            $where['iActive'] =  "Active" ;
            $newTripData = myQuery("trips", array("iTripId"), "selectall",  $where);
            
            
               
            unset($where);
            $where['iOrderId'] =  $orderId ;
            $order_update['iStatusCode'] = $newStatusCode;
            $order_update['iTripId'] =  (int) $newTripData[0]['iTripId'];
            $result1 = myQuery("orders",   $order_update, "update",  $where);
            
           
        
            unset($where);
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = trim("ON_THE_WAY_TO_DESTINATION");
            $driver_status['iTripId'] = $newTripData[0]['iTripId'];
            
            $result2 = myQuery("register_driver", $driver_status, "update", $where);
            
           
            $data['title'] = "Pabili Order";
            $data['description'] = "Order now is ready and receipt has been uploaded. Please prepare your payment and the driver is on the way to the delivery address.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "ORDER_PROCESSED";
            $data['message'] = "Order now is ready and receipt has been uploaded. Please prepare your payment and the driver is on the way to the delivery address.";
            notify("User", $userId, $data);


            $data['title'] = "Pabili Order ".$orderNo;
            $data['description'] = "The driver is on the way to the delivery address.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "ORDER_PROCESSED";
            $data['message'] = "The driver is on the way to the delivery address.";
            notify("Store", $storeId, $data);
            
            // sendRequestToUser($userId, "ORDER_PROCESSED","Pabili Order.", "Order now is ready and receipt has been uploaded. Please prepare your payment and the driver is on the way to the delivery address.");
            
            
            setOrderLogs("3006", $orderId);
            
        
        }else if($status == "Deliver to first Drop Off"){
            
            $newStatusCode = "3007";
            
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = trim("ON_GOING");
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);
            
            //sendRequestToUser($userId, "TrackorderActivity","Pabili Order.", "Your order has been successfully delivered.");

            $data['title'] = "Pabili Order";
            $data['description'] = "Your order has been successfully delivered.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_ARRIVED_AT_THE_DELIVERYADDRESS";
            $data['message'] = "Your order has been successfully delivered.";
            notify("User", $userId, $data);
            
            setOrderLogs("3007", $orderId);
              
        }else if($status == "Deliver to Destination"){
            
            $newStatusCode = "3008";
            
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = trim("ON_GOING");
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);

            $data['title'] = "Pabili Order";
            $data['description'] = "The driver is within the area. Please check the item, variation and quantity before receiving. Any discrepancy or complain after signing will not be honored.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "ORDER_DELIVERED";
            $data['message'] = "The driver is within the area. Please check the item, variation and quantity before receiving. Any discrepancy or complain after signing will not be honored.";
            notify("User", $userId, $data);


            $data['title'] = "Pabili Order ".$orderNo;
            $data['description'] = "The driver arrived at the delivery address.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "ORDER_DELIVERED";
            $data['message'] = "The driver arrived at the delivery address.";
            notify("Store", $storeId, $data);
            
            
            //sendRequestToUser($userId, "DRIVER_ARRIVED_AT_THE_DELIVERYADDRESS","Pabili Order.", "The driver is within the area. Please check the item, variation and quantity before receiving. Any discrepancy or complain after signing will not be honored.");
            
            setOrderLogs("3008", $orderId);
        
        }else if($status == "Order Delivered"){


            $sql = "SELECT fCommision from register_toda WHERE iTodaId = '".$tripData[0]['iTodaId']."'";
            $statement = $db->query($sql);
            $todaData = $statement ->fetchAll();

            $transactionFee = $todaData[0]['fCommision'];
            
            $newStatusCode = "3009";
            
            unset($where);
            $where['iTripId'] = $tripData[0]['iTripId'] ;
            $updateCurrentTrip['iActive'] =  "Finished" ;
            $updateCurrentTrip['tEndDate'] =   @date("Y-m-d H:i:s");
            $updateCurrentTripResult = myQuery("trips",  $updateCurrentTrip, "update",  $where);
  
            $Trikaroo_transactionFee = (float)$orderData[0]['fDeliveryCharge'] * (float)$transactionFee;
            $trikaroo_initial = (float)$orderData[0]['fTotalGenerateFare'] -  (float) $Trikaroo_transactionFee;
            $Trikaroo_driver_earnings =  (float) $trikaroo_initial - (float)$orderData[0]['fSubTotal'];
            
            unset($where);
            $where['iOrderId'] = $orderId;
            
            $fare_status['fCommision'] = (float) $Trikaroo_driver_earnings;
            $fare_status['fWalletDebit'] = (float)$Trikaroo_transactionFee ;
            $fareResult = myQuery("orders",  $fare_status, "update", $where);
            
            
            $driverWallet = (float)$tripData[0]['vWalletBalance']-(float)$Trikaroo_transactionFee ;
            
            unset($where);
            $where['iDriverId'] = $driverId;
            $driver_status['iTripId'] = "0";
            $driver_status['vTripStatus'] =trim("FINISHED");
            $driver_status['vWalletBalance'] = $driverWallet;
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);
            
        
            $walletlogs['iDriverId'] =  $driverId;
            $walletlogs['vUserType'] =  "Driver";
            $walletlogs['vTransactionType'] = "PABILI";
            $walletlogs['vLabel'] = "- Debited";
            $walletlogs['vDescription'] = "";
            $walletlogs['vTransactionNo'] =  $orderData[0]['vOrderNo'];
            $walletlogs['fAmount'] = (float)$Trikaroo_transactionFee ;
            $walletlogs['fWalletBalance'] = (float) $driverWallet;
            $walletlogs['vReceiveBy'] = "";
            $walletlogs['iReceiveId'] = "";
            $walletlogs['eStatus'] = "Completed";
            $walletlogs['dDate'] = @date("Y-m-d H:i:s");
                  
            $result = myQuery("user_wallet_logs",  $walletlogs, "insert");
            
        
            
             //USER DATA
            unset($where);
            $where['iUserId'] = $userId;
            $userData = myQuery("register_user", array("vName", "vLastName", "fRewardPointsBalance"), "selectall",  $where);
            
            $earnedPoints = (float)$Trikaroo_transactionFee * constants::REWARDS_POINTS_RATE;
            $totalRewardPointsBalance = (float)$userData[0]['fRewardPointsBalance']+$earnedPoints;
            
            unset($where);
            $where['iUserId'] = $userId;
            $userReward_status['fRewardPointsBalance'] =  $totalRewardPointsBalance ;
            $result = myQuery("register_user", $userReward_status, "update", $where);
            
            $transactionNo = GenerateUniqueOrderNo("RP");

            $rewardslogs['iUserId'] = $userId ;
            $rewardslogs['vUserType'] = "User";
            $rewardslogs['vTransactionType'] = "PABILI";
            $rewardslogs['vLabel'] = "Earned points";
            $rewardslogs['vDescription'] = "";
            $rewardslogs['vTransactionNo'] = $orderData[0]['vOrderNo'];
            $rewardslogs['fPoints'] = (float)  $earnedPoints ;
            $rewardslogs['fTotalPointsAmount'] = (float)    $totalRewardPointsBalance;
            $rewardslogs['eStatus'] = "Earned";
            $rewardslogs['dDateCreated'] = @date("Y-m-d H:i:s");
                  
            $result = myQuery("rewards_user_logs", $rewardslogs, "insert");


            $data['title'] = "Pabili Order";
            $data['description'] = "Your order has been successfully delivered.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "ORDER_DELIVERED";
            $data['message'] = "Your order has been successfully delivered.";
            notify("User", $userId, $data);


            $data['title'] = "Pabili Order ".$orderNo;
            $data['description'] = "Order has been successfully delivered.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "ORDER_DELIVERED";
            $data['message'] = "Order has been successfully delivered.";
            notify("Store", $storeId, $data);
            
            
    
            //sendRequestToUser($userId, "ORDER_DELIVERED","Pabili Order.", "Your order has been successfully delivered.");
    
            setOrderLogs("3009", $orderId);
            
            // $notification['iUserId'] = $userId;
            // $notification['vUserType'] = $userType;
            // $notification['vTitle'] = "Order No. ".$orderData[0]['vOrderNo'];
            // $notification['vDescription'] = "Your order has been successfully delivered.";
            // $notification['vType'] = "ORDER_TRACK";
            // $notification['vImage'] = "";
            // $notification['vUrl'] = "";
            // $notification['vIntent'] =  $orderData[0]['iOrderId'].",".$orderData[0]['iCompanyId'];
            // $notification['vSent'] = "";
            
            // createNotification($notification);
            
        }else if($status == "Order Cancelled"){
            
            $newStatusCode = "3010";
            
            
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = "FINISHED";
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);
            
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = "FINISHED";
            $driverResult = myQuery("register_driver", $driver_status, "update", $where);



            $data['title'] = "Pabili Order";
            $data['description'] = "Sorry, your order has been cancelled.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "ORDER_CANCELLED";
            $data['message'] = "Sorry, your order has been cancelled.";
            notify("User", $userId, $data);


            $data['title'] = "Pabili Order ".$orderNo;
            $data['description'] = "Order has been cancelled.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "ORDER_CANCELLED";
            $data['message'] = "Order has been cancelled.";
            notify("Store", $storeId, $data);
            
            
           // sendRequestToUser($userId, "TrackorderActivity","Pabili Order cancelled.", "Sorry, your order has been cancelled.");
            
            setOrderLogs("3010", $orderId);
            
            $notification['iUserId'] = $userId;
            $notification['vUserType'] = $userType;
            $notification['vTitle'] = "Order No. ".$orderData[0]['vOrderNo'];
            $notification['vDescription'] = "Your order has been cancelled.";
            $notification['vType'] = "ORDER_TRACK";
            $notification['vImage'] = "";
            $notification['vUrl'] = "";
            $notification['vIntent'] =  '{"orderId":"'.$orderData[0]['iOrderId'].'","storeId":"'.$orderData[0]['iCompanyId'].'"}';
            $notification['vSent'] = "";
            
            createNotification($notification);
              
        }else{
            
            $newStatusCode = "0000";
            
        }
        
        unset($where);
        $where['iOrderId'] = $orderId ;
        if($status == "Order Delivered" || $status == "Order Cancelled"){
            $order_update_status['tTripEnded'] =  @date("Y-m-d H:i:s");
        }
        $order_update_status['iStatusCode'] = $newStatusCode;
        $order_update_status['iDriverId'] =  $driverId;
        $result1 = myQuery("orders",  $order_update_status, "update",  $where);
        
        
        $where['iOrderId'] =  $orderId ;
        $iStatusCode = myQuery("orders", array("iStatusCode", "vOrderNo"), "selectall",  $where);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['notificationCounter'] = countNotifications($userId, "User");
        $messageArray['statusCode'] =  $iStatusCode[0]['iStatusCode'];
        $messageArray['orderNo'] =  $iStatusCode[0]['vOrderNo'];
        
        echo json_encode($messageArray);
        
    }
    
   
   //$servicetype = "ACCEPT_USER_ORDER_REQUEST";
   
    
    if($servicetype == "ACCEPT_USER_ORDER_REQUEST"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.6046971';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '121.0531128';
        $orderId = isset($_POST['orderId']) ? trim($_POST['orderId']) : '3';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '52';
        $storeId = isset($_POST['storeId']) ? trim($_POST['storeId']) : '3';
        $driverId = isset($_POST['driverId']) ? trim ($_POST['driverId']) : '17';
       
        $finalFare = isset($_POST['finalFare']) ? trim ($_POST['finalFare']) : '17';
        $finalDistance = isset($_POST['finalDistance']) ? trim ($_POST['finalDistance']) : '17';
        $finalDeliveryFee = isset($_POST['finalDeliveryFee']) ? trim ($_POST['finalDeliveryFee']) : '17';
        $finalDriverDistance = isset($_POST['finalDriverDistance']) ? trim ($_POST['finalDriverDistance']) : '17';
        
        $where['iOrderId'] =  $orderId ;
        $driverIdIsSet = myQuery("orders", array("iDriverId", "iUserId", "iCompanyId", "fDiscount", "fServiceCharge", "iUserAddressId"), "selectall",  $where);

        $userId = $driverIdIsSet[0]['iUserId'];
        
        $discount =   $driverIdIsSet[0]['fDiscount'];
        $serviceCharge = $driverIdIsSet[0]['fServiceCharge'];
        
        if($driverIdIsSet[0]['iDriverId'] == "" || $driverIdIsSet[0]['iDriverId'] == "0" || $driverIdIsSet[0]['iDriverId'] == 0 || $driverIdIsSet[0]['iDriverId'] == null || $driverIdIsSet[0]['iDriverId'] == $driverId){
            $newStatusCode = "3002";
           
            unset($messageArray);
            unset($where);
           
            // $where['iDriverId'] = $driverId;
            // $driver_status['vTripStatus'] = trim("ON_GOING");
            // $driver_status['vAppServiceType'] = "PABILI";
            // $result2 = myQuery("register_driver", $driver_status, "update", $where);
                
            unset($where);
            $where['iDriverId'] = $driverId;
            $driverData = myQuery("register_driver", array("vLatitude","vLongitude"), "selectall",  $where);
            
            unset($where);
            $where['iCompanyId'] =  $storeId;
            $companyData = myQuery("company", array("vRestuarantLocationLat", "vRestuarantLocationLong", "vRestuarantLocation"), "selectall",  $where);

            //CREATE TRIP
        
            $tripData['iOrderId'] =  $orderId;
            $tripData['iCompanyId'] =  $storeId;
            $tripData['iUserId'] =   $userId;
            $tripData['tStartDate'] =  @date("Y-m-d H:i:s");
        
            $tripData['tStartLat'] = $driverData[0]['vLatitude'];
            $tripData['tStartLong'] = $driverData[0]['vLongitude'];
            
            $tripData['tEndLat'] =  $companyData[0]['vRestuarantLocationLat'];
            $tripData['tEndLong'] = $companyData[0]['vRestuarantLocationLong'];
            
            $tripData['tSaddress'] = "";   //stripslashes($origin);
        
            $tripData['tDaddress'] = addcslashes($companyData[0]['vRestuarantLocation'], "'");
            $tripData['vTripItinerary'] = "FirstTrip";
            $result5 = myQuery("trips",  $tripData, "insert");
            
            unset($where);
            $where['iOrderId'] =  $orderId ;
            $TripData = myQuery("trips", array("iTripId"), "selectall",  $where);
            
            
            
            
            // $driveraddress = get_Address2($driverData[0]['vLatitude'], $driverData[0]['vLongitude']);
            // $driverdistance = get_Distance($driveraddress,  $companyData[0]['vRestuarantLocation'], "km");
            
            // if($driveraddress == null || $distance == null){
               
            //      $address1 = get_Address2($driverData[0]['vLatitude'], $driverData[0]['vLongitude']);
            //      $address2 = get_Address2($companyData[0]['vRestuarantLocationLat'], $companyData[0]['vRestuarantLocationLong']);
            //      $driverdistance = get_Distance($address1,  $address2 , "km");
                
            // }
            
            
                
            // $TotalFarePerKM_fromDriver = $driverdistance * constants::RATE_PER_KM;
            // $deliveryFee = constants::FLAT_RATE_PABILI+$TotalFarePerKM_fromDriver;
            
            // $requestMessage['amount'] =  $generatedFare+ $TotalFarePerKM_fromDriver;
            // $requestMessage['deliveryFee'] = $deliveryFee;
            // $requestMessage['distance'] = number_format($finalDistance, 2, '.', '') ;
            // $requestArray[] = $requestMessage;
                
     
            
            
            unset($where);
            $where['iDriverId'] = $driverId;
            $driver_status['vTripStatus'] = "ON_GOING";
            $driver_status['vAppServiceType'] = "PABILI";
            $driver_status['iTripId'] = $TripData[0]['iTripId'];
            
            $result2 = myQuery("register_driver", $driver_status, "update", $where);
            
        
            
            unset($where);
            $where['iOrderId'] =  $orderId ;
            $order_update_status['iStatusCode'] = $newStatusCode;
            $order_update_status['iDriverId'] =  $driverId;
            $order_update_status['vDriverDistance'] =  $finalDriverDistance;
            $order_update_status['tTripStarted'] =  @date("Y-m-d H:i:s");
            $order_update_status['vDistance'] =  $finalDistance;
            $order_update_status['fTotalGenerateFare'] =  ((float) $finalFare + (float)$finalDeliveryFee) + (float) $serviceCharge - (float) $discount;
            $order_update_status['fDeliveryCharge'] =  (float)$finalDeliveryFee;
            $order_update_status['iTripId'] =  (int) $TripData[0]['iTripId'];
            $result1 = myQuery("orders",  $order_update_status, "update",  $where);
            
           
            
           // sendRequestToUser($userId, "DRIVER_FOUND","Pabili Order.", "Driver found. Driver is on the way to the store.");
            
            setOrderLogs("3002", $orderId);
            
            unset($where);
            $where['iOrderId'] =  $orderId ;
            $iStatusCode = myQuery("orders", array("iStatusCode", "vOrderNo"), "selectall",  $where);


            $data['title'] = "Pabili Order.";
            $data['description'] = "Driver found. Driver is on the way to the store.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_FOUND";
            $data['message'] = "Driver found. Driver is on the way to the store.";
            notify("User", $userId, $data);

            $data['title'] = "Pabili Order ".$iStatusCode[0]['vOrderNo'];
            $data['description'] = "Driver found. Driver is on the way to the store.";
            //NOTIFCATION FOREGROUND
            $data['activity'] = "DRIVER_FOUND";
            $data['message'] = "Driver found. Driver is on the way to the store.";
            notify("Store",  $storeId , $data);


            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";
            $messageArray['statusCode'] =  $iStatusCode[0]['iStatusCode'];
            $messageArray['userId'] =  $userId;
            $messageArray['orderId'] = $orderId;
            $messageArray['orderNo'] = $iStatusCode[0]['vOrderNo'];
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Fail";
        }

        echo json_encode($messageArray);
        
    }
    
    
    //$servicetype = "ACCEPT_USER_BOOKING_REQUEST";
    
    
    if($servicetype == "ACCEPT_USER_BOOKING_REQUEST"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.6046971';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '121.0531128';
        $bookingId = isset($_POST['bookingId']) ? trim($_POST['bookingId']) : '84';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '55';
        $driverId = isset($_POST['driverId']) ? trim ($_POST['driverId']) : '17';
        $additionalFare = isset($_POST['additionalFare']) ? trim ($_POST['additionalFare']) : '17';
        $bookingType = isset($_POST['bookingType']) ? trim ($_POST['bookingType']) : '';
        $isSurge = isset($_POST['isSurge']) ? trim ($_POST['isSurge']) : '17';
        $baseFare = isset($_POST['baseFare']) ? trim ($_POST['baseFare']) : '17';
        $finalFare = isset($_POST['finalFare']) ? trim ($_POST['finalFare']) : '17';
        $finalDistance = isset($_POST['finalDistance']) ? trim ($_POST['finalDistance']) : '17';
        $finalDriverDistance = isset($_POST['finalDriverDistance']) ? trim ($_POST['finalDriverDistance']) : '17';
        unset($where);
        $where['iCabBookingId'] =  $bookingId ;
        $driverIdIsSet = myQuery("cab_booking", array("iDriverId", "eStatus", "eCancelBy", "iCancelByUserId"), "selectall",  $where);
        
        unset($where);
         
        $where['iDriverId'] = $driverId;
        $driverUpdate_location['vLatitude'] = $latitude;
        $driverUpdate_location['vLongitude'] =  $longitude;
        
        $resultLocation = myQuery("register_driver", $driverUpdate_location, "update", $where);
        
        unset($where);
        $where['iDriverId'] = $driverId;
        $driverWalletData = myQuery("register_driver", array("vWalletBalance","fPocketMoney"), "selectall",  $where);
        $driverWalletBalance = (float) $driverWalletData[0]["vWalletBalance"];
        if(($driverWalletData[0]["vWalletBalance"] != "0" ||  $driverWalletBalance != 0) && $driverWalletBalance >= 50 ){
            if($driverIdIsSet[0]['iDriverId'] == "" || $driverIdIsSet[0]['iDriverId'] == "0" || $driverIdIsSet[0]['iDriverId'] == 0 || $driverIdIsSet[0]['iDriverId'] == null || $driverIdIsSet[0]['iDriverId'] ==  $driverId){
                if( $driverIdIsSet[0]['eStatus'] != "Cancelled" || $driverIdIsSet[0]['eCancelBy']  == "" || $driverIdIsSet[0]['eCancelBy']  == null || $driverIdIsSet[0]['iCancelByUserId'] == "" || $driverIdIsSet[0]['iCancelByUserId'] == "0"  || $driverIdIsSet[0]['iCancelByUserId'] == 0){
                    
                    unset($messageArray);
                    unset($where);
                    $where['iDriverId'] = $driverId;
                    $driverData = myQuery("register_driver", array("vLatitude","vLongitude"), "selectall",  $where);
                    
                    unset($where);
                    $where['iCabBookingId'] = $bookingId;
                    $bookingData = myQuery("cab_booking", array("vSourceLatitude", "vSourceLongitude", "vSourceAddress", "fPricePerKM", "iBaseFare"), "selectall",  $where);
                    
                    //CREATE TRIP
                
                    $tripData['iCabBookingId'] =  $bookingId;
                    $tripData['iUserId'] =   $userId;
                    $tripData['iDriverId'] =  $driverId;
                    $tripData['tStartDate'] =  @date("Y-m-d H:i:s");
                
                    $tripData['tStartLat'] = $driverData[0]['vLatitude'];
                    $tripData['tStartLong'] = $driverData[0]['vLongitude'];
                    
                    $tripData['tEndLat'] = $bookingData[0]['vSourceLatitude'];
                    $tripData['tEndLong'] = $bookingData[0]['vSourceLongitude'];
                    
                    $tripData['tSaddress'] = "";
                    $tripData['tDaddress'] = addcslashes($bookingData[0]['vSourceAddress'], "'");
                    $tripData['vTripItinerary'] = "FirstTrip";
                    $result5 = myQuery("trips",  $tripData, "insert");
                    
                    $driverdistance = ceil(distance($driverData[0]['vLatitude'], $driverData[0]['vLongitude'], $bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude'], "K"));
                    $TotalFarePerKM_fromDriver = 0;
                    
                    $farePricePerKm = (float) $bookingData[0]['fPricePerKM'];
                    $generatedFare = (float) $bookingData[0]['iBaseFare'];
                    
                    $farePriceFirstKm = 20;
                    $farePricePerKm = 20;
                    $farePriceSucceedingKm = 10;
                    
                    $finaldriverdistance = $driverdistance > 0 ? ceil($driverdistance) : $driverdistance;
                    if($farePriceFirstKm != null && $farePriceSucceedingKm != null){
                        
                        if($finaldriverdistance > 1){ //GREATER 1 Kilometer
                            for($a = 0; $a < $finaldriverdistance; $a++){
                                if($a == 0){
                                    $TotalFarePerKM_fromDriver += $farePriceFirstKm; //FIRST KILOMETER
                                }else{
                                    $TotalFarePerKM_fromDriver += $farePriceSucceedingKm; //SUCCEEDING KILOMETER
                                }
                            }
                        }else{
                           $TotalFarePerKM_fromDriver += $farePricePerKm; //FIRST KILOMETER 
                        }
                        
                        
                    }else{
                        $TotalFarePerKM_fromDriver = $finaldriverdistance * $farePricePerKm;
                    }
                    
            
                    // $sqlf = "SELECT iTripId FROM trips WHERE iCabBookingId = '". $bookingId."' ORDER BY iTripId DESC";
                    // $statement = $db->query($sqlf);
                    // $taskData = $statement ->fetchAll(); 
                    
                    
                    unset($where);
                    $where['iCabBookingId'] =  $bookingId ;
                    $TripData = myQuery("trips", array("iTripId"), "selectall",  $where, " ORDER BY iTripId DESC");
                    
                    
                    unset($where);
                    $where['iDriverId'] = $driverId;
                    $driver_status['vTripStatus'] = "ON_GOING";
                    $driver_status['vAppServiceType'] = "PASAKAY";
                    $driver_status['iTripId'] = $TripData[0]['iTripId'];
                    
                    $result2 = myQuery("register_driver", $driver_status, "update", $where);
                    
                    unset($where);
                    $where['iUserId'] = $userId;
                    $user_status['vTripStatus'] = "ON_GOING";
                    $user_status['iTripId'] = $TripData[0]['iTripId'];
                    $result3 = myQuery("register_user", $user_status, "update", $where);
                    
                    $fPricePerKM = (float) $bookingData[0]['fPricePerKM'];
                   // $additionalFare = ceil($finalDriverDistance)*$fPricePerKM;
                  //  $additionalFare = $TotalFarePerKM_fromDriver;
                    $bookingType = (isNightTime())? "Early/Night Booking" : "Normal";
                    unset($where);
                    $where['iCabBookingId'] =  $bookingId ;
                    $booking_update_status['eStatus'] = "Assign";
                    $booking_update_status['iDriverId'] =  $driverId;
                    $booking_update_status['fTripGenerateFare'] = (float) $finalFare;
                    $booking_update_status['vDriverDistance'] = $finalDriverDistance;
                    $booking_update_status['fTripTotalAmountFare'] = (float) $finalFare;
                    $booking_update_status['eBookingType'] = "Early/Night Booking";
                    // $booking_update_status['fAdditionalFare'] = (float) $TotalFarePerKM_fromDriver;
                    $booking_update_status['fAdditionalFare'] = (float) $additionalFare;
                    $booking_update_status['iBaseFare'] = (float) $baseFare;
                    // $booking_update_status['vDistance'] =  $finalDistance;
                    $booking_update_status['tTripStarted'] = @date("Y-m-d H:i:s");
                    $booking_update_status['iTripId'] =  (int) $TripData[0]['iTripId'];
                    $result1 = myQuery("cab_booking",  $booking_update_status, "update",  $where);
                    
                
                    sendRequestToUser($userId, "SearchDriverActivity","Trikaoo driver Found.", "Your booking has been accepted.");
                    
                    //setOrderLogs("3002", $orderId);
                    
                    unset($where);
                    $where['iCabBookingId'] =  $bookingId ;
                    $iStatusCode = myQuery("cab_booking", array("eStatus", "vBookingNo"), "selectall",  $where);
                    
                    $messageArray['response'] = 1;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Okay";
                    $messageArray['status'] =  $iStatusCode[0]['eStatus'];
                    $messageArray['userId'] =  $userId;
                    $messageArray['bookingId'] = $bookingId;
                    $messageArray['bookingNo'] = $iStatusCode[0]['vBookingNo'];
                    
                    
                }else{
                    
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Booking has been already cancelled by passenger";
                
                }
                
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Already assigned to other driver";
            }
            
        }else{
                
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Insufficient wallet balance";
        }
        
        
        
        

        echo json_encode($messageArray);
        
    }
    
    
    //$servicetype = "LOAD_ORDER_PROCESS_RESUME";
 
    
    if($servicetype == "LOAD_ORDER_PROCESS_RESUME"){
        
        unset($messageArray);
        unset($where);
           
        
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $orderId = isset($_POST['orderId']) ? trim($_POST['orderId']) : '34';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '56';
        $driverId = isset($_POST['driverId']) ? trim ($_POST['driverId']) : '24';
        
        //DRIVER DATA
        
        $sql = "SELECT vName, vLastName, iDriverId, vImage, iTripId, vTripStatus, vLatitude, vLongitude, vAppServiceType, eStatus FROM register_driver WHERE iDriverId = '".$driverId."'";
        
        $statement = $db->query($sql);
        
        $taskData = $statement ->fetchAll(); 
        
        $driverName =  $taskData[0]['vName']. " ".$taskData[0]['vLastName'];
        
        $driverId = $taskData[0]['iDriverId'];
        
        $driverImage = $taskData[0]['vImage'];
       
        //ORDER DATA
        
        $sql = "SELECT * FROM orders WHERE iOrderId = '". $orderId."'";

        $statement = $db->query($sql);
        
        $orderData = $statement ->fetchAll(); 
        
        $storeId = $orderData[0]['iCompanyId'];
        
        
        $sql = "SELECT vCompany, vStoreCategory FROM company WHERE iCompanyId = '". $storeId ."'";

        $statement = $db->query($sql);
        
        $storeData = $statement ->fetchAll(); 
        
      
        
        //USER DATA
        
        
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $orderData[0]['iUserId']."'";

        $statement = $db->query($sql);
        
        $userData = $statement ->fetchAll(); 
        
       
        
        // $sql2 = "SELECT mi.iMenuItemId as itemId,  mi.vItemType_EN as itemName, mi.fPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc, od.vCancel as itemCancel FROM menu_items as mi 
        // LEFT JOIN order_details as od ON mi.iMenuItemId = od.iMenuItemId WHERE od.iOrderId = ". $orderId;
        
        $sql2 = "SELECT od.iOrderDetailId as orderDetailId, od.iMenuItemId as itemId, od.vItemName as itemName, od.fOriginalPrice as itemPrice, vOptionId as optionId,  vOptionPrice as optionPrice, vAddonId as addonId, vAddonPrice as addonPrice, vDrinksId as drinksId, vDrinksPrice as drinksPrice, vSizeId as sizeId, vSizePrice as sizePrice, vFlavorId as flavorId, vFlavorPrice as flavorPrice,  od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc, od.vSpecInstruction as specialInstruction, od.vNoItemInstruction as noItemInstruction, vImage as itemImage,
        od.vCancel as itemCancel,  eManually as eManually FROM order_details as od WHERE od.iOrderId = ". $orderId;
       

        $statement = $db->query($sql2);
        
        $items = $statement ->fetchAll(); 
        
        $itemCount = 0;
        
        
        for($xi = 0; $x < count($items); $x++) {
            
        }
        
        
        for($i = 0; $i < count($items); $i++) {
            
            
            // if($items[$i]['optionId'] != null && $items[$i]['optionId'] != "" && $items[$i]['optionId'] !="0" ){
                
            //     $sql = "SELECT * FROM menuitem_options WHERE iOptionId = '".$items[$i]['OptionId']."'";

            //     $statement = $db->query($sql);
                
            //     $optionData = $statement ->fetchAll(); 
                
            //     if(count($optionData ) > 0){
            //       $menuOption['options'] = $optionData;
            //     }
                
                
                
            // }
            
            
            //  if($items[$i]['addonId'] != null && $items[$i]['addonId'] !="" && $items[$i]['optionId'] !="0"){
                
            //     $sql = "SELECT * FROM menuitem_options WHERE iOptionId = '".$items[$i]['addonId']."'";

            //     $statement = $db->query($sql);
                
            //     $optionData = $statement ->fetchAll(); 
                
            //     if(count($optionData ) > 0){
            //         $menuOption['addons'] = $optionData;
            //     }
                
               
                
            // }
            
            // if($items[$i]['drinksId'] != null && $items[$i]['drinksId'] !="" && $items[$i]['optionId'] !="0"){
                
            //     $sql = "SELECT * FROM menuitem_options WHERE iOptionId = '".$items[$i]['drinksId']."'";

            //     $statement = $db->query($sql);
                
            //     $optionData = $statement ->fetchAll(); 
                
            //     if(count($optionData ) > 0){
            //         $menuOption['drinks'] = $optionData;
            //     }
                
               
                
            // }
            
            // if($items[$i]['flavorId'] != null && $items[$i]['flavorId'] !="" && $items[$i]['optionId'] !="0" ){
                
            //     $sql = "SELECT * FROM menuitem_options WHERE iOptionId = '".$items[$i]['flavorId']."'";

            //     $statement = $db->query($sql);
                
            //     $optionData = $statement ->fetchAll(); 
                
            //     if(count($optionData ) > 0){
            //          $menuOption['flavor'] = $optionData;
            //     }
                
              
                
            // }
            
            // if($items[$i]['sizeId'] != null && $items[$i]['sizeId'] !="" && $items[$i]['optionId'] !="0"){
                
            //     $sql = "SELECT * FROM menuitem_options WHERE iOptionId = '".$items[$i]['sizeId']."'";

            //     $statement = $db->query($sql);
                
            //     $optionData = $statement ->fetchAll(); 
                
            //     if(count($optionData ) > 0){
            //          $menuOption['size'] = $optionData;
            //     }
                
               
                
            // }
            
            
            // if($items[$i]['addonId'] != null && $items[$i]['addonId'] !="" && $items[$i]['optionId'] !="0"){
              
            //     $addonArr = explode(",", $items[$i]['addonId']);
                
            //     $menuOption['addons'] = array();
            
            //     for($x = 0 ;  $x < count(  $addonArr); $x++ ){
                    
            //         $sql = "SELECT * FROM menuitem_options WHERE iOptionId = '".$addonArr[$x]."'";
    
            //         $statement = $db->query($sql);
                    
            //         $optionData = $statement ->fetchAll(); 
                    
            //          $menuOption['size'] = $optionData;
                     
            //         if(count($optionData ) > 0){
            //           // array_push($menuOption['addons'], $optionData);
                       
            //           $menuOption['addons'][] =  $optionData;
                       
                       
            //         }
                    
                   
                    
            //     }
                
            // }
            
            // array_push($items, $menuOption );
            
            
            $itemCount = $itemCount + (int)$items[$i]['itemQty'];
            
            $orderDetails['orderItems'][] =  $items[$i];
            
        }
    
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['statusCode'] = $orderData[0]['iStatusCode'];
        
        
        $messageArray['storeName'] = $storeData[0]['vCompany'];
        $messageArray['storeCatgory'] =  $storeData[0]['vStoreCategory'];
        $messageArray['transactionNo'] =  $orderData[0]['vOrderNo'];
        $messageArray['orderSummary'] = $orderDetails;
        
        // $messageArray['orderOptions'] =  $menuOption;
        $messageArray['orderTotalPrice'] = $orderData[0]['fNetTotal'];
        $messageArray['driverName'] =  $driverName;
        $messageArray['driverId'] = $driverId;
        $messageArray['driverImage'] = $driverImage;
        
        $messageArray['orderId'] = $orderId;
        $date = date_create($bookingData[0]['tOrderRequestDate']);
        $messageArray['orderDate'] =date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
        $messageArray['orderTotalAmount'] = $orderData[0]['fTotalGenerateFare'];
        $messageArray['orderQty'] = $itemCount;
        
        $messageArray['userId'] = $userData[0]['iUserId'];
        $messageArray['userLastName'] = $userData[0]['vLastName'];
        $messageArray['userFullName'] = $userData[0]['vName'];
        $messageArray['userImage'] = $userData[0]['vImgName'];
         $messageArray['vPhone'] = $userData[0]['vPhone'];
        $messageArray['userLat'] = $userData[0]['vLatitude'];
        $messageArray['userLong'] = $userData[0]['vLongitude'];
        
        
        
        
        echo json_encode($messageArray);
        
    }
    
    
    
    if($servicetype == "CONFIRM_ORDER_QTY_PRICE_UPDATE"){
        
        unset($messageArray);
        unset($where);
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.6046971';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '121.0531128';
        $orderId = isset($_POST['orderId']) ? trim($_POST['orderId']) : '1';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '52';
        $storeId = isset($_POST['storeId ']) ? trim($_POST['storeId ']) : '3';
        $driverId = isset($_POST['driverId']) ? trim ($_POST['driverId']) : '17';
        $orderSummaryDetails = isset($_POST['orderSummaryDetails']) ? trim ($_POST['orderSummaryDetails']) : '17';
        $selectAll = isset($_POST['selectAll']) ? trim ($_POST['selectAll']) : '17';
        
        $SelectedItems = isset($_POST['SelectedItems']) ? trim ($_POST['SelectedItems']) : '17';
    
        $newOrderSummaryList = array();
        $itemSelectedList = array();
        $SubtotalAmount = 0;
        
         
        if($selectAll == "false"){
            
        }
        
        $newOrderSummaryList = json_decode(stripcslashes( $orderSummaryDetails), true);
        $itemSelectedList =json_decode(stripcslashes($SelectedItems), true);
        
        for($i = 0; $i < count($newOrderSummaryList); $i++) {

            $newData = array();
            
            $where['iOrderId'] =  $orderId;
            
            if($selectAll == "false"){
                if (!in_array($newOrderSummaryList[$i]['itemId'],  $itemSelectedList)){
                    $newData['vCancel'] = "Yes";
                }else{
                    $newData['vCancel'] = "No";
                }
            }
            
           
            
            $where['iMenuItemId'] = $newOrderSummaryList[$i]['itemId'];
            
            $newData['fOriginalPrice'] = (float) $newOrderSummaryList[$i]['itemPrice'];
            
            $newData['fSubTotal'] = (float) $newOrderSummaryList[$i]['itemSubtotal'];
            
            $newData['fDiscountPrice'] = (float) $newOrderSummaryList[$i]['itemPrice'];
            
            $newData['fPrice']  = (float) $newOrderSummaryList[$i]['itemPrice'];
            
            $newData['iQty'] = (int) $newOrderSummaryList[$i]['itemQty'];
            
            $result = myQuery("order_details",  $newData, "update", $where);
            
            $SubtotalAmount = $SubtotalAmount + ((float) $newOrderSummaryList[$i]['itemSubtotal']);
           

        }
        
        
        $sql = "SELECT * FROM orders WHERE iOrderId = '". $orderId."'";

        $statement = $db->query($sql);
        
        $orderData = $statement ->fetchAll(); 

        $serviceCharge = (float) $orderData[0]['fServiceCharge'];

        $discountPrice = (float) $orderData[0]['fDiscount'];
        
    
        $deliveryCharge = (float) $orderData[0]['fDeliveryCharge'];
        
        $GenerateFare = $SubtotalAmount + $deliveryCharge +  $serviceCharge - $discountPrice;  
        
        unset($where);
        
        $where['iOrderId'] =  $orderId;
        
        $updateOrderData['fSubTotal'] = (float) $SubtotalAmount;
         
        $updateOrderData['fNetTotal'] = (float)  $GenerateFare;
          
        $updateOrderData['fTotalGenerateFare'] =(float)  $GenerateFare; 
            
            
        
        $result = myQuery("orders",  $updateOrderData, "update", $where);
        
         setOrderLogs("3005", $orderId);
       
        
        sendRequestToUser($userId, "ORDER_UPDATED","Pabili Order.", "The total has been updated by your driver, please check your order summary");
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['orderSummaryDetails'] =  $orderSummaryDetails;
        $messageArray['orderSummaryDetails'] =  $orderSummaryDetails;
        
        $messageArray['test'] = "Total selected : ".$itemSelectedList[0];
        
        echo json_encode($messageArray);
           
    }
    
    
    
    
    if($servicetype == "DECLINE_USER_REQUEST"){
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $orderId = isset($_POST['orderId']) ? trim($_POST['orderId']) : '36';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '32';
        $driverId = isset($_POST['driverId']) ? trim ($_POST['driverId']) : '';
        
        unset($where);
        $where['iDriverId'] = $driverId;
        $driver_status['vTripStatus'] = "FINISHED";
        $driver_status['iTripId'] = 0;
        $result2 = myQuery("register_driver", $driver_status, "update", $where);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['tripStatus'] = "FINISHED";
        
        echo json_encode($messageArray);
        
    }
    
    if($servicetype == " DECLINE_USER_NOOKING_REQUEST"){
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $bookingId = isset($_POST['orderId']) ? trim($_POST['orderId']) : '36';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '32';
        $driverId = isset($_POST['driverId']) ? trim ($_POST['driverId']) : '';
        
        unset($where);
        $where['iDriverId'] = $driverId;
        $driver_status['vTripStatus'] = "FINISHED";
        $driver_status['iTripId'] = 0;
        $result2 = myQuery("register_driver", $driver_status, "update", $where);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['tripStatus'] = "FINISHED";
        
        echo json_encode($messageArray);
        
    }
    
    
    
    
    //$servicetype = "LOAD_PABILI_ACTIVITIES";
    
    if($servicetype == "LOAD_PABILI_ACTIVITIES"){
         
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '67';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung'; 
    
        $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iStatusCode IN(3001, 3002, 3003,3004,3005,3006,3007,3008,3009,3010,3011) AND od.iUSerId =  $userId ORDER BY tOrderRequestDate DESC";

        $statement = $db->query($sql);
        
        $result = $statement ->fetchAll(); 
        
        $orderData = array();
        
        for($i = 0; $i < count($result); $i++) {
            
            $itemId_array = "";
            
              
            $sql2 = "SELECT sum(iQty) as itemQty FROM order_details WHERE iOrderId = '".$result[$i]['iOrderId']."'";
            
            $statement = $db->query($sql2);
            
            $itemQty = $statement ->fetchAll();
            
          
            
            
            
            $sql2 = "SELECT * FROM order_details WHERE iOrderId = '".$result[$i]['iOrderId']."'";
            
            $statement = $db->query($sql2);
            
            $itemId = $statement ->fetchAll();
            
            $orderDetails = $itemId;
            
            for($x = 0; $x < count($itemId); $x++){
                
                if($x+1 == count($itemId)){
                    
                     $itemId_array .= $itemId[$x]['iMenuItemId']."";
                }else{
                    
                    $itemId_array .= $itemId[$x]['iMenuItemId'].",";
                }
                
            }
            
            $sql2 = "SELECT vCompanyColor FROM company WHERE iCompanyId = '".$result[$i]['iCompanyId']."'";
            
            $statement = $db->query($sql2);
            
            $company = $statement ->fetchAll();
           
            $orderData[$i]['orderId'] = $result[$i]['iOrderId'];
            $orderData[$i]['orderNo'] =  $result[$i]['vOrderNo'];
            $orderData[$i]['orderDate'] =  $result[$i]['dDate'];
            $orderData[$i]['storeName'] =  $result[$i]['vCompany'];
            $orderData[$i]['itemQty'] = $itemQty[0]['itemQty'];
            $orderData[$i]['orderPrice'] =  $result[$i]['fTotalGenerateFare'];
            $orderData[$i]['orderStatus'] =  $result[$i]['vStatus'];
            $orderData[$i]['orderPaidFrom'] =  $result[$i]['ePaymentOption'];
            $orderData[$i]['storeId'] = $result[$i]['iCompanyId'];
            $orderData[$i]['storeColor'] = $company[0]['vCompanyColor'];
            $orderData[$i]['instruction'] = $result[$i]['vInstruction'];
            $orderData[$i]['itemArray'] = $itemId_array;
            $orderData[$i]['orderdetails'] = $orderDetails;
            
        }
        
        unset($where);
        $where['iCompanyId'] = $result[0]['iCompanyId'];
        $companyAddress = myQuery("company", array("vCompany", "vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
        
        $storeName =  $companyAddress[0]['vCompany'];
        $storeAddress =  $companyAddress[0]['vRestuarantLocation'];
        $storeLat =  $companyAddress[0]['vRestuarantLocationLat'];
        $storeLong =  $companyAddress[0]['vRestuarantLocationLong'];
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['notificationCounter'] = countNotifications($userId, "User");
        $messageArray['result'] =  $orderData;
        $messageArray['storeLat'] =  $storeLat;
        $messageArray['storeLong'] =  $storeLong;
        $messageArray['storeAddress'] = $storeAddress;
        
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
        
        
        // if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
              
        // }
        
        echo json_encode($messageArray);
        
    }
    
    
    ///////////////////////////////////////////////////////////////
    //   
    //                  PASKAY
    //
    //////////////////////////////////////////////////////////////
    
  //$servicetype = "CALCULATE_ESTIMATED_FARE";
   
     
    if($servicetype == "CALCULATE_ESTIMATED_FARE"){
         
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.647130793185664';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '120.99170777633799';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '32';
        $origin  = isset($_POST['origin']) ? trim($_POST['origin']) : '31 Annapolis, San Juan, 1503 Metro Manila';
        $originLat  = isset($_POST['originLat']) ? trim($_POST['originLat']) : '14.647130793185664';
        $originLong  = isset($_POST['originLong']) ? trim($_POST['originLong']) : '120.99170777633799';
        $destination  = isset($_POST['destination']) ? trim($_POST['destination']) : '228 Ortigas Ave, San Juan, 1503 Metro Manila';
        $destinationLat  = isset($_POST['destinationLat']) ? trim($_POST['destinationLat']) : '14.647317636047623';
        $destinationLong  = isset($_POST['destinationLong']) ? trim($_POST['destinationLong']) : '120.98510954207894';
        $userServiceArea = isset($_POST['userServiceArea']) ? trim($_POST['userServiceArea']) :'San Juan City';
        $mode = isset($_POST['mode']) ? trim($_POST['mode']) :'';
        $bookingNo = isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'';
        
      
        //$locationCheck =  check_Address_restriction($latitude,$longitude, $userServiceArea);
        
        $userLocationArr = array($latitude , $longitude);
        $sourceLocationArr = array( $originLat, $originLong);
        $destinationLocationArr = array($destinationLat, $destinationLong);
    
        // $locationCheck =  check_Address_restriction( $latitude,$longitude,  $userServiceArea);
        $test = false;

        //LOGCAT
    
        // echo "Is Source Location Allowed : ".(isLocationAllowed($sourceLocationArr)? "true": "false");
        // echo "<br>";
        // echo "Is Destination Location Allowed : ".(isLocationAllowed($destinationLocationArr)? "true": "false");
        // echo "<br>";
        // echo "Is Source Location Restricted : ".(isLocationRestricted($sourceLocationArr)? "true": "false");
        // echo "<br>";
        // echo "Is Destination Location Restricted : ".(isLocationRestricted($destinationLocationArr)? "true": "false");
        // echo "<br>";
        // echo "Is Source Location on Water : ".(isItOnWater( $originLat, $originLong)? "true": "false");
        // echo "<br>";
        // echo "Is Destination Location on Water : ".(isItOnWater(  $destinationLat, $destinationLong)? "true": "false");
        // echo "<br>";

       
        if(isLocationAllowed($sourceLocationArr)  && isLocationAllowed($destinationLocationArr)){
            
            $userlocationId = getLocationArea($userLocationArr);
            $sourcelocationId = getLocationArea($sourceLocationArr);
            $destinationLocationId = getLocationArea($destinationLocationArr);
        
            $suggestedToda = array();

              //LOGCAT
            // echo "User Location Id : ".getLocationArea($userLocationArr);
            // echo "<br>";
            // echo "Source Location Id : ".getLocationArea($sourceLocationArr);
            // echo "<br>";
            // echo "Destination Location Id : ".getLocationArea($destinationLocationArr);
            // echo "<br>";
           
        
            
            //SPECIAL FARE FOR ACCORDING TO SERVICE AREA
            
            if($sourcelocationId == 22 && $destinationLocationId == 24){
                
                //DAGAT DAGATAN TO HULONG DUHAT
                
                $todaId = 1;
                $todaName = "Caloocan Area";
                $todaRouteNo = "C-001";
                $baseFare = (float)200;//250
                $radiusDistance = 5;
                $roundOffDistance = "Yes";
                $farePricePerKm = 5;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                $surgeRate = "Disable";
                
                
                
            }else if($sourcelocationId == 22 && $destinationLocationId == 23){
                
                //DAGAT DAGATAN TO CONCEPTION
                
                $todaId = 1;
                $todaName = "Caloocan Area";
                $todaRouteNo = "C-001";
                $baseFare = (float) 100;//120
                $radiusDistance = 5;
                $roundOffDistance = "Yes";
                $farePricePerKm = 5;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                $surgeRate = "Disable";
                
                
            }else if($sourcelocationId == 22 && $destinationLocationId == 25){
                
                //DAGAT DAGATAN TO CATMON
                
                $todaId = 1;
                $todaName = "Caloocan Area";
                $todaRouteNo = "C-001";
                $baseFare = (float) 150;
                $radiusDistance = 5;
                $roundOffDistance = "Yes";
                $farePricePerKm = 5;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                $surgeRate = "Disable";
                
            
            }else if($sourcelocationId == 22 && $destinationLocationId == 26){
                
                //DAGAT DAGATAN TO FRANCIS
                
                $todaId = 1;
                $todaName = "Caloocan Area";
                $todaRouteNo = "C-001";
                $baseFare = (float) 100;
                $radiusDistance = 5;
                $roundOffDistance = "Yes";
                $farePricePerKm = 5;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                $surgeRate = "Disable";
                
            }else if(($sourcelocationId == 31 || $sourcelocationId == 32 || $sourcelocationId == 33 || $destinationLocationId == 31 || $destinationLocationId == 32 || $destinationLocationId == 33 )){
                
                //SPECIAL AREA TERESA RIZAL
                
                //31 LOCATION ID - LA HACIENDA
                //32 LOCATION ID - MAY-IBA, ONDOY, ABUYOD
                //33 LOCATION ID - PRINZA
                
                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $todaData = $statement ->fetchAll();
                
                $todaId = $todaData[0]['iTodaId'];
                $todaName = $todaData[0]['vTodaName'];
                $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                $fCommisionPercentage =  $todaData[0]['fCommision'];
                $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                $surgeRate = $todaData[0]['eSurge'];
                $radiusDistance = 3;
                $roundOffDistance = "Yes";
                $farePricePerKm = 20;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                    
                    
            }else if( $sourcelocationId == 30 && $destinationLocationId == 30){
                        
                    //REGULAR AREA TERESA RIZAL
            
                    
                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $todaData = $statement ->fetchAll();
                
                $todaId = $todaData[0]['iTodaId'];
                $todaName = $todaData[0]['vTodaName'];
                $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                $fCommisionPercentage =  $todaData[0]['fCommision'];
                $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                $surgeRate = $todaData[0]['eSurge'];
                $radiusDistance = 3;
                $roundOffDistance = "Yes";
                $farePricePerKm = 20;
                $farePricePerMin = 2;
                $farePriceFirstKm = 20;
                $farePriceSucceedingKm = 10;
                
                        
            }else{
            
                

                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $todaData = $statement ->fetchAll();
                
                if(count($todaData) > 0){
                    
                    for($x = 0 ; $x < count($todaData) ; $x++){
                        
                        $todaId = $todaData[$x]['iTodaId'];
                        $todaName = $todaData[$x]['vTodaName'];
                        $todaRouteNo = $todaData[$x]['vTodaRouteNo'];
                        $baseFare = $todaData[$x]['iBaseFare'];
                        $surgeRate = $todaData[0]['eSurge'];
                        $farePricePerKm = $todaData[$x]['fPricePerKM'];
                        $farePricePerMin = $todaData[$x]['fPricePerMin'];
                        $radiusDistance = (int) $todaData[$x]['fRadius'];
                        $roundOffDistance = "Yes";
                        $farePriceFirstKm = 0;
                        $farePriceSucceedingKm = 0;
                       
                        $suggestedToda[$x]['todaId'] = $todaId;
                        $suggestedToda[$x]['todaName'] = $todaName;
                        $suggestedToda[$x]['todaRoute'] = $todaRouteNo;
                        $suggestedToda[$x]['baseFare'] = $todaRouteNo;
                        $suggestedToda[$x]['farePerKM'] = $todaRouteNo;
                        $suggestedToda[$x]['farePerMin'] = $todaRouteNo;
                        $suggestedToda[$x]['radiusDistance'] = $radiusDistance;
                        $suggestedToda[$x]['duration'] = null;;
                        $suggestedToda[$x]['distance'] = null;
                        $suggestedToda[$x]['generatedFare'] = null;
                        $suggestedToda[$x]['generatedFare_max'] = null;
                        $suggestedToda[$x]['ETA'] = null;
                        $suggestedToda[$x]['ETA_max'] = null;
                        
                    
                    }
                    
                }else{
                    
                    $todaId = null;
                    $todaName = null;
                    $todaRouteNo = null;
                    $baseFare = null;
                    $farePricePerKm = null;
                    $farePricePerMin = null;
                    $radiusDistance = null;
                     
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Out of Service Area. No Drivers on the Area";
        
                }
            
                
            }
            
            if($todaId != null || $todaName != null || $todaRouteNo != null || $baseFare != null || $farePricePerKm != null ||  $farePricePerMin != null){
                $str_date = @date('Y-m-d H:i:s', strtotime('-410 minutes'));
                 //SELECT ALL DRIVERS ONLINE
                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' AND vAvailability = 'Available' AND eStatus = 'active' AND (vTripStatus != 'FINISHED' OR vTripStatus != 'NONE') AND tLocationUpdateDate > '".$str_date."' having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $allDriverData = $statement ->fetchAll();
                
                 //SELECT ALL DRIVERS AVAILABLE
                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' AND vAvailability = 'Available' AND eStatus = 'active' AND (vTripStatus = 'FINISHED' OR vTripStatus = 'NONE') AND tLocationUpdateDate > '".$str_date."' having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $availableDriverData = $statement ->fetchAll();
                
                if(count($allDriverData) > 0){
                    if(round(count($allDriverData)*0.2) >= count($availableDriverData) ){
                        $surgeRate = "Enable";
                    }else{
                        $surgeRate = "Disable";
                    }
                }else{
                    $surgeRate = "Disable";
                }
                
                //UPDATE IF SURGE is ENABLE/DISABLE;
                unset($where);
                $where['iTodaId'] = $todaId;
                $updateToda['eSurge'] = $surgeRate;
                $result6 = myQuery("register_toda", $updateToda, "update", $where);
                
                if($surgeRate == "Enable"){
                    $baseFare = (float) $baseFare*1.1;
                    
                     //SURGE
                    $farePricePerKm = $farePricePerKm*1.2;
                    $farePriceFirstKm = $farePriceFirstKm*1.2;
                    $farePriceSucceedingKm = $farePriceSucceedingKm*1.2;
                    $isSurge = "Yes";
                    $bookingType = "Surge";

                }else{
                    
                     //NIGHT TIME
                    $farePricePerKm = (isNightTime())? ($farePricePerKm*1.2) : $farePricePerKm;
                    $farePriceFirstKm = (isNightTime())? ($farePriceFirstKm*1.2) : $farePriceFirstKm;
                    $farePriceSucceedingKm =  (isNightTime())? ($farePriceSucceedingKm*1.2) : $farePriceSucceedingKm;
                    $bookingType = (isNightTime())? "Early/Night Booking" : "Normal";
                    $isSurge = "No";

                }
               
                
                $distance = ceil(distance($originLat,  $originLong, $destinationLat,  $destinationLong, "K"));
                $estimatedDuration  = cal_time( $distance, 10);
            
                if( $distance < $radiusDistance){

                    $generatedFare =  $baseFare;
                    $additionalFare = ($radiusDistance*$farePricePerKm);
                    $additionalSuccedingFare = 0;
                    $generatedFare_max = $generatedFare + $additionalFare+$additionalSuccedingFare;
                   
                    
                }else{
                    $generatedFare =  $baseFare;
                    $additionalFare = ($radiusDistance*$farePricePerKm);
                    $additionalSuccedingFare = 0;
                    $remainingDistance = (int) $distance - $radiusDistance;
                    
                    
                    
                    if($farePriceFirstKm != null || $farePriceSucceedingKm != null ){
                
                        for($x=0; $x<$remainingDistance; $x++){
                            if($x == 0){
                                $additionalSuccedingFare = $additionalSuccedingFare + $farePriceFirstKm; 
                            }else{
                                $additionalSuccedingFare =  $additionalSuccedingFare + $farePriceSucceedingKm; 
                            }
                        }
                    }else{
                        $additionalSuccedingFare = ($remainingDistance*$farePricePerKm);
                    }
                    
                    $generatedFare_max = $generatedFare+$additionalFare+$additionalSuccedingFare;
                    
                }
                
                $timeDuration = '+'.$estimatedDuration.' seconds';
                $startTime = @date("Y-m-d H:i:s");
                $convertedTime = @date('Y-m-d H:i:s',(strtotime($startTime)+$timeDuration));
                $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
                
                $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
                $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
                
                $suggestedToda[0]['todaId'] = $todaId;
                $suggestedToda[0]['todaName'] = $todaName;
                $suggestedToda[0]['todaRoute'] = $todaRouteNo;
                $suggestedToda[0]['baseFare'] = $baseFare;
                $suggestedToda[0]['farePerKM'] = $farePricePerKm;
                $suggestedToda[0]['farePerMin'] = $farePricePerMin;
                $suggestedToda[0]['radiusDistance'] = $radiusDistance;
                $suggestedToda[0]['duration'] = $estimatedDuration;
                $suggestedToda[0]['distance'] = $estimatedDuration;
                $suggestedToda[0]['generatedFare'] = $generatedFare;
                $suggestedToda[0]['generatedFare_max'] = $generatedFare_max;
                $suggestedToda[0]['generatedFare_max'] = $generatedFare_max;
                $suggestedToda[0]['ETA'] = $newEstimatedTime;
                $suggestedToda[0]['ETA_max'] = $newConvertedTimeAllowance;
                
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['userLocationId'] = $userlocationId;
                $messageArray['originLocationId'] = $sourcelocationId;
                $messageArray['destinationLocationId'] = $destinationLocationId;
                $messageArray['notificationCounter'] = countNotifications($userId, "User");
                $messageArray['status'] = "Okay";
                $messageArray['isSurge'] = $isSurge;
                $messageArray['todaId'] = $todaId;
                $messageArray['distance'] = $distance;
                $messageArray['originLat'] = $originLat;
                $messageArray['originLong'] = $originLong;
                $messageArray['destLat'] = $destinationLat;
                $messageArray['destLong'] = $destinationLong;
                $messageArray['Staring time'] = "Starting Time: ".$startTime;
                $messageArray['duration'] = $estimatedDuration;
                $messageArray['New Time'] = "Converted Time (added 1 hour): '.$convertedTime";
                $messageArray['estimatedTime'] = $newEstimatedTime." - ". $newConvertedTimeAllowance ;
                $messageArray['estimatedFare'] =  roundOff($generatedFare);
                $messageArray['estimatedFare_min'] =  roundOff($generatedFare);
                $messageArray['estimatedFare_max'] =  roundOff($generatedFare_max);
                $messageArray['isSurge'] = $surgeRate;
                $messageArray['totalDrivers'] = count($allDriverData);
                $messageArray['20PecentOfTOtalDrivers'] = round(count($allDriverData)*0.2);
                $messageArray['availableDrivers'] = count($availableDriverData);

                echo json_encode($messageArray);
                
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Out of Service Area";
                
                echo json_encode($messageArray);
            }
        
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Out of Service Area";
            
            echo json_encode($messageArray);
    
        }
        
       
        
    }

    // $servicetype = "SEARCH_FOR_DRIVERS";
    
     if($servicetype == "SEARCH_FOR_DRIVERS"){
         
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.61941743045686';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '121.05686118108471';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '32';
        $userServiceArea = isset($_POST['userServiceArea']) ? trim($_POST['userServiceArea']) :'Quezon City';
        $bookingNo = isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'PS21040321983';
        
        $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
        $statement = $db->query($sql);
        $bookingData = $statement ->fetchAll(); 

        $origin =  $bookingData[0]['vSourceAddress'];
        $originLat = $bookingData[0]['vSourceLatitude'];
        $originLong = $bookingData[0]['vSourceLongitude'];
        $destination = $bookingData[0]['tDestAddress'];
        $destinationLat = $bookingData[0]['vDestLatitude'];
        $destinationLong = $bookingData[0]['vDestLongitude'];
    
        //BOOKING ORIGIN POINTS AND DESTINATION POINTS 
        $sourceLocationArr = array($bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude']);
        $destinationLocationArr = array($bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude']);

         $userlocationId = getLocationArea($userLocationArr);
        $sourcelocationId = getLocationArea($sourceLocationArr);
        $destinationLocationId = getLocationArea($destinationLocationArr);
    
    
        if($bookingData[0]['eStatus'] == "Searching for drivers" || $bookingData[0]['eStatus'] == "Re-assigning for drivers"){
                    
             if(isLocationAllowed($sourceLocationArr)  && isLocationAllowed($destinationLocationArr) &&  !isLocationRestricted($sourceLocationArr) && !isLocationRestricted($destinationLocationArr) && !isItOnWater( $originLat, $originLong) && !isItOnWater(  $destinationLat,     $destinationLong)){
                
                if($bookingData[0]['eStatus'] == "Searching for drivers"){
                    //FILTERING THE LOCATIONS 
                
                   
                
                    $suggestedToda = array();
                    
            
                    //SPECIAL FARE FOR ACCORDING TO SERVICE AREA
                
                    if($sourcelocationId == 22 && $destinationLocationId == 24){
                        
                        //DAGAT DAGATAN TO HULONG DUHAT
                        
                        $todaId = 1;
                        $todaName = "Caloocan Area";
                        $todaRouteNo = "C-001";
                        $baseFare = (float)200;//250
                        $radiusDistance = 5;
                        $roundOffDistance = "Yes";
                        $farePricePerKm = 5;
                        $farePricePerMin = 2;
                        $farePriceFirstKm = 0;
                        $farePriceSucceedingKm = 0;
                        $surgeRate = "Disable";
                        
                        
                        
                    }else if($sourcelocationId == 22 && $destinationLocationId == 23){
                        
                        //DAGAT DAGATAN TO CONCEPTION
                        
                        $todaId = 1;
                        $todaName = "Caloocan Area";
                        $todaRouteNo = "C-001";
                        $baseFare = (float) 100;//120
                        $radiusDistance = 5;
                        $roundOffDistance = "Yes";
                        $farePricePerKm = 5;
                        $farePricePerMin = 2;
                        $farePriceFirstKm = 0;
                        $farePriceSucceedingKm = 0;
                        $surgeRate = "Disable";
                        
                        
                    }else if($sourcelocationId == 22 && $destinationLocationId == 25){
                        
                        //DAGAT DAGATAN TO CATMON
                        
                        $todaId = 1;
                        $todaName = "Caloocan Area";
                        $todaRouteNo = "C-001";
                        $baseFare = (float) 150;
                        $radiusDistance = 5;
                        $roundOffDistance = "Yes";
                        $farePricePerKm = 5;
                        $farePricePerMin = 2;
                        $farePriceFirstKm = 0;
                        $farePriceSucceedingKm = 0;
                        $surgeRate = "Disable";
                        
                    
                    }else if($sourcelocationId == 22 && $destinationLocationId == 26){
                        
                        //DAGAT DAGATAN TO FRANCIS
                        
                        $todaId = 1;
                        $todaName = "Caloocan Area";
                        $todaRouteNo = "C-001";
                        $baseFare = (float) 100;
                        $radiusDistance = 5;
                        $roundOffDistance = "Yes";
                        $farePricePerKm = 5;
                        $farePricePerMin = 2;
                        $farePriceFirstKm = 0;
                        $farePriceSucceedingKm = 0;
                        $surgeRate = "Disable";
                        
                    }else if(($sourcelocationId == 31 || $sourcelocationId == 32 || $sourcelocationId == 33 || $destinationLocationId == 31 || $destinationLocationId == 32 || $destinationLocationId == 33 )){
                        
                        //SPECIAL AREA TERESA RIZAL
                        
                        //31 LOCATION ID - LA HACIENDA
                        //32 LOCATION ID - MAY-IBA, ONDOY, ABUYOD
                        //33 LOCATION ID - PRINZA
                        
                        $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                        $statement = $db->query($sql);
                        $todaData = $statement ->fetchAll();
                        
                        $todaId = $todaData[0]['iTodaId'];
                        $todaName = $todaData[0]['vTodaName'];
                        $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                        $fCommisionPercentage =  $todaData[0]['fCommision'];
                        $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                        $surgeRate = $todaData[0]['eSurge'];
                        $radiusDistance = 3;
                        $roundOffDistance = "Yes";
                        $farePricePerKm = 20;
                        $farePricePerMin = 2;
                        $farePriceFirstKm = 0;
                        $farePriceSucceedingKm = 0;
                            
                            
                    }else if( $sourcelocationId == 30 && $destinationLocationId == 30){
                                
                            //REGULAR AREA TERESA RIZAL
                    
                            
                        $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                        $statement = $db->query($sql);
                        $todaData = $statement ->fetchAll();
                        
                        $todaId = $todaData[0]['iTodaId'];
                        $todaName = $todaData[0]['vTodaName'];
                        $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                        $fCommisionPercentage =  $todaData[0]['fCommision'];
                        $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                        $surgeRate = $todaData[0]['eSurge'];
                        $radiusDistance = 3;
                        $roundOffDistance = "Yes";
                        $farePricePerKm = 20;
                        $farePricePerMin = 2;
                        $farePriceFirstKm = 20;
                        $farePriceSucceedingKm = 10;
                        
                                
                    }else{
                        
                        
                        // $sql = "SELECT * FROM register_toda WHERE iLocationId = '".$destinationLocationId."'";
                        // $statement = $db->query($sql);
                        // $todaData = $statement ->fetchAll(); 
                        // $sourceLat = 14.542703712920263;
                        // $sourceLong = 121.21699943045694;
                        
                        //SELECT NEAREST TODA
                        $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                        $statement = $db->query($sql);
                        $todaData = $statement ->fetchAll();
                        
                        if(count($todaData) > 0){
                                
                           for($x = 0 ; $x < count($todaData) ; $x++){
                               
                                $todaId = $todaData[0]['iTodaId'];
                                $todaName = $todaData[0]['vTodaName'];
                                $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                                $fCommisionPercentage =  $todaData[0]['fCommision'];
                                $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                                $surgeRate = $todaData[0]['eSurge'];
                                $roundOffDistance = "Yes";
                                $radiusDistance = (int) $todaData[$x]['fRadius'];
                                $farePricePerKm = $todaData[$x]['fPricePerKM'];
                                $farePricePerMin = $todaData[$x]['fPricePerMin'];
                                $farePriceFirstKm = 20;
                                $farePriceSucceedingKm = 10;
                                
                                
                            }
                        }
                        
                    }
                    
                    
                    $str_date = @date('Y-m-d H:i:s', strtotime('-410 minutes'));
                     //SELECT ALL DRIVERS ONLINE
                    $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' AND vAvailability = 'Available' AND eStatus = 'active' AND (vTripStatus != 'FINISHED' OR vTripStatus != 'NONE') AND tLocationUpdateDate > '".$str_date."' having distance <= 5 order by distance";
                    $statement = $db->query($sql);
                    $allDriverData = $statement ->fetchAll();
                    
                     //SELECT ALL DRIVERS AVAILABLE
                    $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' AND vAvailability = 'Available' AND eStatus = 'active' AND (vTripStatus = 'FINISHED' OR vTripStatus = 'NONE') AND tLocationUpdateDate > '".$str_date."' having distance <= 5 order by distance";
                    $statement = $db->query($sql);
                    $availableDriverData = $statement ->fetchAll();
                    
                    if(count($allDriverData) > 0){
                        if(round(count($allDriverData)*0.2) >= count($availableDriverData) ){
                            $surgeRate = "Enable";
                        }else{
                            $surgeRate = "Disable";
                        }
                    }else{
                        $surgeRate = "Disable";
                    }
                    
                    //UPDATE IF SURGE is ENABLE/DISABLE;
                    unset($where);
                    $where['iTodaId'] = $todaId;
                    $updateToda['eSurge'] = $surgeRate;
                    $result6 = myQuery("register_toda", $updateToda, "update", $where);
                    
                    if($surgeRate == "Enable"){
                        $baseFare = (float) $baseFare*1.1;
                         //SURGE
                        $farePricePerKm = $farePricePerKm*1.2;
                        $farePriceFirstKm = $farePriceFirstKm*1.2;
                        $farePriceSucceedingKm = $farePriceSucceedingKm*1.2;
                        $isSurge = "Yes";
                        $bookingType = "Surge";
                    }else{
                        
                         //NIGHT TIME
                        $farePricePerKm = (isNightTime())? ($farePricePerKm*1.2) : $farePricePerKm;
                        $farePriceFirstKm = (isNightTime())? ($farePriceFirstKm*1.2) : $farePriceFirstKm;
                        $farePriceSucceedingKm =  (isNightTime())? ($farePriceSucceedingKm*1.2) : $farePriceSucceedingKm;
                        $bookingType = (isNightTime())? "Early/Night Booking" : "Normal";
                    }
                    
                
                    $distance = ceil(distance($originLat,  $originLong, $destinationLat,  $destinationLong, "K"));
                    $estimatedDuration  = cal_time( $distance, 10);
                    
                    // echo "<br>";
                    // echo "SourceLocation Id : ".$sourcelocationId;
                    // echo "<br>";
                    // echo "DestinationLocation Id : ".$destinationLocationId;
                    // echo "<br>";
                    // echo "Toda Id : ".$todaId;
                    // echo "<br>";
                    // echo "Toda Name : ".$todaName;
                    // echo "<br>";
                    // echo "Base fare : ".$baseFare;
                    // echo "<br>";
                    // echo "Price/Km : ". $farePricePerKm;
                    // echo "<br>";
                    // echo "Price/min : ".$farePricePerMin;
                    // echo "<br>";
                    // echo "Distance Radius : ".$radiusDistance;
                    // echo "<br>";
                    // echo "Company Percentage : ".$fCommisionPercentage;
                    // echo "<br>";
                    // echo "Distance : ".$distance;
                    // echo "<br>";
                    // echo "Estimate Duration : ".$estimatedDuration;
                    // echo "<br>";
                    
                    
                    if( $distance < $radiusDistance){
    
                        $generatedFare =  $baseFare;
                        $additionalFare = ($radiusDistance*$farePricePerKm);
                        $additionalSuccedingFare = 0;
                        $generatedFare_max = $generatedFare + $additionalFare+$additionalSuccedingFare;
                       
                        
                    }else{
                        $generatedFare =  $baseFare;
                        $additionalFare = ($radiusDistance*$farePricePerKm);
                        $additionalSuccedingFare = 0;
                        $remainingDistance = (int) $distance - $radiusDistance;
                        
                        
                        
                        if($farePriceFirstKm != null || $farePriceSucceedingKm != null ){
                    
                            for($x=0; $x<$remainingDistance; $x++){
                                if($x == 0){
                                    $additionalSuccedingFare = $additionalSuccedingFare + $farePriceFirstKm; 
                                }else{
                                    $additionalSuccedingFare =  $additionalSuccedingFare + $farePriceSucceedingKm; 
                                }
                            }
                        }else{
                            $additionalSuccedingFare = ($remainingDistance*$farePricePerKm);
                        }
                        
                        $generatedFare_max = $generatedFare+$additionalFare+$additionalSuccedingFare;
                        
                    }
                    
                //     echo "<br>";
                //     echo "Remaining distance: ".$remainingDistance;
                //     echo "<br>";
                //     echo "Base fare: ".($baseFare);
                //     echo "<br>";
                //     echo "Additional fare: ".($additionalFare);
                //     echo "<br>";
                    
                //     if($farePriceFirstKm != null || $farePriceFirstKm != 0 ){
                //         echo "Additional Succeding fare: ".($additionalSuccedingFare);
                //     }
                    
                //     echo "<br>";
                //     echo "Generated Fare min: ".$generatedFare;
                //     echo "<br>";
                //     echo "Generated Fare max : ".$generatedFare_max;
                //     echo "<br>";
                    
                //     echo "<br>";
                //   // echo "Generated Fare min: ".$generatedFare;
                //     echo "<br>";
                    
                    $timeDuration = '+'.$estimatedDuration.' seconds';
                    $startTime = @date("Y-m-d H:i:s");
                    $convertedTime = date('Y-m-d H:i:s',strtotime($startTime)+$timeDuration);
                    $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
                    
                    $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
                    $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
                    
                    $suggestedToda[0]['todaId'] = $todaId;
                    $suggestedToda[0]['todaName'] = $todaName;
                    $suggestedToda[0]['todaRoute'] = $todaRouteNo;
                    $suggestedToda[0]['baseFare'] = $todaRouteNo;
                    $suggestedToda[0]['farePerKM'] = $todaRouteNo;
                    $suggestedToda[0]['farePerMin'] = $todaRouteNo;
                    $suggestedToda[0]['radiusDistance'] = $radiusDistance;
                    $suggestedToda[0]['duration'] = $estimatedDuration;
                    $suggestedToda[0]['distance'] = $estimatedDuration;
                    $suggestedToda[0]['generatedFare'] = $generatedFare;
                    $suggestedToda[0]['generatedFare_max'] = $generatedFare_max;
                    $suggestedToda[0]['generatedFare_max'] = $generatedFare_max;
                    $suggestedToda[0]['ETA'] = $newEstimatedTime;
                    $suggestedToda[0]['ETA_max'] = $newConvertedTimeAllowance;
                }else{
                    
                    $sql = "SELECT * from register_toda WHERE iTodaId = '".$bookingData[0]['iTodaId']."'";
                    $statement = $db->query($sql);
                    $todaData = $statement ->fetchAll();
                    
                    $todaId = $todaData[0]['iTodaId'];
                    $todaName = $todaData[0]['vTodaName'];
                    $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                    $fCommisionPercentage =  $todaData[0]['fCommision'];
                    $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                    $surgeRate = $todaData[0]['eSurge'];
                    $roundOffDistance = "Yes";
                    $radiusDistance = (int) $todaData[$x]['fRadius'];
                    $farePricePerKm = $todaData[$x]['fPricePerKM'];
                    $farePricePerMin = $todaData[$x]['fPricePerMin'];
    
                
                    $distance = ceil(distance($originLat,  $originLong, $destinationLat,  $destinationLong, "K"));
                    $estimatedDuration  = cal_time( $distance, 10);

                    $timeDuration = '+'.$estimatedDuration.' seconds';
                    $startTime = @date("Y-m-d H:i:s");
                    $convertedTime = date('Y-m-d H:i:s',strtotime($startTime)+$timeDuration);
                    $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
                    
                    $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
                    $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
                    
                    $generatedFare = $bookingData[0]['fTripTotalAmountFare'];
                    $generatedFare_max =  $bookingData[0]['fTripTotalAmountFare'];
                }
                
                
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['eStatus'] = $bookingData[0]['eStatus'];
                $messageArray['userLocationId'] = $userlocationId;
                $messageArray['sourcelocationId'] = $sourcelocationId;
                $messageArray['isSurge'] = $isSurge;
                $messageArray['destinationLocationId'] = $destinationLocationId;
                $messageArray['notificationCounter'] = countNotifications($userId, "User");
                $messageArray['status'] = "Okay";
                $messageArray['bookingPaymentMethod'] =  $bookingData[0]['ePayType'];
                
                $messageArray['todaId'] = $todaId;
                $messageArray['distance'] = $distance;
                $messageArray['originLat'] = $originLat;
                $messageArray['originLong'] = $originLong;
                $messageArray['destLat'] = $destinationLat;
                $messageArray['destLong'] = $destinationLong;
                $messageArray['Staring time'] = "Starting Time: ".$startTime;
                $messageArray['duration'] = $estimatedDuration;
                $messageArray['New Time'] = "Converted Time (added 1 hour): '.$convertedTime";
                $messageArray['estimatedTime'] = $newEstimatedTime." - ". $newConvertedTimeAllowance ;
                $messageArray['estimatedFare'] =  roundOff($generatedFare);
                $messageArray['estimatedFare_min'] =  roundOff($generatedFare);
                $messageArray['estimatedFare_max'] =  roundOff($generatedFare_max);
                
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Out of Service Area";
        
            }
            
        }else{

             // if($bookingData[0]['eStatus'] != "Searching for drivers" || $bookingData[0]['eStatus'] != "Re-assigning for drivers"){

                 $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Already accepted by the driver";
             


            
           
        }
       
        
      echo json_encode($messageArray);
        
    }

    
    
    // if($servicetype == "REASSIGN_FOR_DRIVERS"){
         
    //     unset($where);
    //     unset($messageArray);
        
    //     $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.61941743045686';
    //     $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '121.05686118108471';
    //     $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '32';
    //     $userServiceArea = isset($_POST['userServiceArea']) ? trim($_POST['userServiceArea']) :'Quezon City';
    //     $bookingNo = isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'PS21032218791';
        
    //     $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
    //     $statement = $db->query($sql);
    //     $bookingData = $statement ->fetchAll(); 

    //     $origin =  $bookingData[0]['vSourceAddress'];
    //     $originLat = $bookingData[0]['vSourceLatitude'];
    //     $originLong = $bookingData[0]['vSourceLongitude'];
    //     $destination = $bookingData[0]['tDestAddress'];
    //     $destinationLat = $bookingData[0]['vDestLatitude'];
    //     $destinationLong = $bookingData[0]['vDestLongitude'];
    
    //     //BOOKING ORIGIN POINTS AND DESTINATION POINTS 
    //     $sourceLocationArr = array($bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude']);
    //     $destinationLocationArr = array($bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude']);
    
    
    //     if($bookingData[0]['eStatus'] != "Assign" || $bookingDataStatus[0]['eStatus'] != "At the Pickup Point" ||
    //             $bookingDataStatus[0]['eStatus'] != "In Transit" || $bookingDataStatus[0]['eStatus'] = "Deliver to first Drop Off" ||
    //             $bookingDataStatus[0]['eStatus'] != "Arrived at the destination" || $bookingDataStatus[0]['eStatus'] != "Finished"){
                    
    //          if(isLocationAllowed($sourceLocationArr)  && isLocationAllowed($destinationLocationArr) &&  !isLocationRestricted($sourceLocationArr) && !isLocationRestricted($destinationLocationArr) && !isItOnWater( $originLat, $originLong) && !isItOnWater(  $destinationLat,     $destinationLong)){
                
    //             $sql = "SELECT * from register_toda WHERE iTodaId = '".$bookingData[0]['iTodaId']."'";
    //             $statement = $db->query($sql);
    //             $todaData = $statement ->fetchAll();
                    
    //             $todaId = $todaData[0]['iTodaId'];
    //             $todaName = $todaData[0]['vTodaName'];
    //             $todaRouteNo = $todaData[0]['vTodaRouteNo'];
    //             $fCommisionPercentage =  $todaData[0]['fCommision'];
    //             $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
    //             $surgeRate = $todaData[0]['eSurge'];
    //             $roundOffDistance = "Yes";
    //             $radiusDistance = (int) $todaData[$x]['fRadius'];
    //             $farePricePerKm = $todaData[$x]['fPricePerKM'];
    //             $farePricePerMin = $todaData[$x]['fPricePerMin'];

            
    //             $distance = ceil(distance($originLat,  $originLong, $destinationLat,  $destinationLong, "K"));
    //             $estimatedDuration  = cal_time( $distance, 10);
                
    //             $generatedFare = $bookingData[0]['fTripTotalAmountFare'];
    //             $generatedFare_max =  $bookingData[0]['fTripTotalAmountFare'];
                
    //             // echo "<br>";
    //             // echo "SourceLocation Id : ".$sourcelocationId;
    //             // echo "<br>";
    //             // echo "DestinationLocation Id : ".$destinationLocationId;
    //             // echo "<br>";
    //             // echo "Toda Id : ".$todaId;
    //             // echo "<br>";
    //             // echo "Toda Name : ".$todaName;
    //             // echo "<br>";
    //             // echo "Base fare : ".$baseFare;
    //             // echo "<br>";
    //             // echo "Price/Km : ". $farePricePerKm;
    //             // echo "<br>";
    //             // echo "Price/min : ".$farePricePerMin;
    //             // echo "<br>";
    //             // echo "Distance Radius : ".$radiusDistance;
    //             // echo "<br>";
    //             // echo "Company Percentage : ".$fCommisionPercentage;
    //             // echo "<br>";
    //             // echo "Distance : ".$distance;
    //             // echo "<br>";
    //             // echo "Estimate Duration : ".$estimatedDuration;
    //             // echo "<br>";
                
                
                
                
    //         //     echo "<br>";
    //         //     echo "Remaining distance: ".$remainingDistance;
    //         //     echo "<br>";
    //         //     echo "Base fare: ".($baseFare);
    //         //     echo "<br>";
    //         //     echo "Additional fare: ".($additionalFare);
    //         //     echo "<br>";
                
    //         //     if($farePriceFirstKm != null || $farePriceFirstKm != 0 ){
    //         //         echo "Additional Succeding fare: ".($additionalSuccedingFare);
    //         //     }
                
    //         //     echo "<br>";
    //         //     echo "Generated Fare min: ".$generatedFare;
    //         //     echo "<br>";
    //         //     echo "Generated Fare max : ".$generatedFare_max;
    //         //     echo "<br>";
                
    //         //     echo "<br>";
    //         //   // echo "Generated Fare min: ".$generatedFare;
    //         //     echo "<br>";
                
    //             $timeDuration = '+'.$estimatedDuration.' seconds';
    //             $startTime = @date("Y-m-d H:i:s");
    //             $convertedTime = date('Y-m-d H:i:s',strtotime($startTime)+$timeDuration);
    //             $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
                
    //             $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
    //             $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
                
    //             $suggestedToda[0]['todaId'] = $todaId;
    //             $suggestedToda[0]['todaName'] = $todaName;
    //             $suggestedToda[0]['todaRoute'] = $todaRouteNo;
    //             $suggestedToda[0]['baseFare'] = $todaRouteNo;
    //             $suggestedToda[0]['farePerKM'] = $todaRouteNo;
    //             $suggestedToda[0]['farePerMin'] = $todaRouteNo;
    //             $suggestedToda[0]['radiusDistance'] = $radiusDistance;
    //             $suggestedToda[0]['duration'] = $estimatedDuration;
    //             $suggestedToda[0]['distance'] = $estimatedDuration;
    //             $suggestedToda[0]['generatedFare'] = $generatedFare;
    //             $suggestedToda[0]['generatedFare_max'] = $generatedFare_max;
    //             $suggestedToda[0]['generatedFare_max'] = $generatedFare_max;
    //             $suggestedToda[0]['ETA'] = $newEstimatedTime;
    //             $suggestedToda[0]['ETA_max'] = $newConvertedTimeAllowance;
                
                
    //             $messageArray['response'] = 1;
    //             $messageArray['service'] = $servicetype;
    //             $messageArray['eStatus'] = $bookingData[0]['eStatus'];
    //             $messageArray['userLocationId'] = $userlocationId;
    //             $messageArray['sourcelocationId'] = $sourcelocationId;
            
    //             $messageArray['destinationLocationId'] = $destinationLocationId;
    //             $messageArray['notificationCounter'] = countNotifications($userId, "User");
    //             $messageArray['status'] = "Okay";
    //             $messageArray['bookingPaymentMethod'] =  $bookingData[0]['ePayType'];
                
    //             $messageArray['todaId'] = $todaId;
    //             $messageArray['distance'] = $distance;
    //             $messageArray['originLat'] = $originLat;
    //             $messageArray['originLong'] = $originLong;
    //             $messageArray['destLat'] = $destinationLat;
    //             $messageArray['destLong'] = $destinationLong;
    //             $messageArray['Staring time'] = "Starting Time: ".$startTime;
    //             $messageArray['duration'] = $estimatedDuration;
    //             $messageArray['New Time'] = "Converted Time (added 1 hour): '.$convertedTime";
    //             $messageArray['estimatedTime'] = $newEstimatedTime." - ". $newConvertedTimeAllowance ;
    //             $messageArray['estimatedFare'] =  roundOff($generatedFare);
    //             $messageArray['estimatedFare_min'] =  roundOff($generatedFare);
    //             $messageArray['estimatedFare_max'] =  roundOff($generatedFare_max);
                
    //         }else{
                
    //             $messageArray['response'] = 0;
    //             $messageArray['service'] = $servicetype;
    //             $messageArray['status'] = "Out of Service Area";
        
    //         }
    //     }else{
    //         $messageArray['response'] = 0;
    //         $messageArray['service'] = $servicetype;
    //         $messageArray['status'] = "Already accepted by the driver";
    //     }
       
        
    //   echo json_encode($messageArray);
        
    // }
    
    
// $servicetype = "REASSIGN";
    
     if($servicetype == "REASSIGN"){
        unset($messageArray);
        unset($where);
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.62707097';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '121.0619957';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '1';
        $bookingNo = isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'PS21033186624';
        $bookingId = isset($_POST['bookingId']) ? trim($_POST['bookingId']) :'368';
       
        //GETTING THE NEW DESTINATION ADDRESS
        $USERAGENT = $_SERVER['HTTP_USER_AGENT'];

        $opts = array('http'=>array('header'=>"User-Agent: $USERAGENT\r\n"));
        $context = stream_context_create($opts);
        $url4 = file_get_contents("https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitude&lon=$longitude&zoom=18&addressdetails=1", false, $context);
        $osmaddress = json_decode($url4);  
        $location = array();
        
        // $location['type'] =  "nominatim";
        // $location['address'] =  $osmaddress ->display_name;
        // $location['latitude'] =  $osmaddress ->lat;
        // $location['longitude'] =  $osmaddress ->lon;
        // $location['address_name'] = $osmaddress->address ->building;
        // $location['housenumber'] = $osmaddress->addresss ->house_number;
        // $location['street'] = $osmaddress->address ->road;
        // $location['locality'] = $osmaddress->address ->quarter; 
        // $location['town'] = $osmaddress->address -> town; 

        unset($where);
        //UPDATE USER STATUS
        $where['iUserId'] = $userId;
        
        $user_status['vLatitude'] = $latitude;
        $user_status['vLongitude'] = $longitude;
        $user_status['vTripStatus'] = "BOOKED";
        $user_status['vBookingNo'] =  $bookingNo;
        $result2 = myQuery("register_user",  $user_status, "update", $where);
        
        //UPDATE BOOKING STATUS
        unset($where);
        unset( $bookingStatus);
        // $where['iCabBookingId'] = $bookingId;
         $where['vBookingNo'] = $bookingNo;
        $bookingStatus['eStatus'] = "Re-assigning for drivers";
        $bookingStatus['iDriverId'] = "0";
        $bookingStatus['vSourceLatitude'] = $latitude;
        $bookingStatus['vSourceLongitude'] = $longitude;
        $bookingStatus['vSourceAddress'] = $osmaddress ->display_name;
        $result2 = myQuery("cab_booking",  $bookingStatus, "update", $where);
        
       // echo '</br>',json_encode($where);
       // echo '</br>',json_encode($bookingStatus);
        $sql = "SELECT vBookingNo, iDriverId, vSourceAddress, eStatus FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
        $statement = $db->query($sql);
        $bookingData = $statement ->fetchAll(); 
        
        // echo '</br>',json_encode($bookingData);
        
        $messageArray['response'] = 1;
        $messageArray['notificationCounter'] = countNotifications($userId, "User");
        $messageArray['service'] = $servicetype;
        ///$messageArray['duration'] =  $estimatedDuration;
        $messageArray['eStatus'] =  $bookingData[0]['eStatus'];
        $messageArray['bookingId'] =  $bookingId;
        $messageArray['bookingNo'] =  $bookingNo;
        
        
        echo json_encode( $messageArray);
        
    }
    
    
    
    // if($servicetype == "SEARCH_FOR_DRIVERS"){
         
    //     unset($where);
    //     unset($messageArray);
        
    //     $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.61941743045686';
    //     $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '121.05686118108471';
    //     $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '32';
    //     $userServiceArea = isset($_POST['userServiceArea']) ? trim($_POST['userServiceArea']) :'Quezon City';
    //     $bookingNo = isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'';
        
    //     $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
    //     $statement = $db->query($sql);
    //     $bookingData = $statement ->fetchAll(); 

                
    //     $origin =  $bookingData[0]['vSourceAddress'];
    //     $originLat = $bookingData[0]['vSourceLatitude'];
    //     $originLong = $bookingData[0]['vSourceLongitude'];
    //     $destination = $bookingData[0]['tDestAddress'];
    //     $destinationLat = $bookingData[0]['vDestLatitude'];
    //     $destinationLong = $bookingData[0]['vDestLongitude'];
               
                 
    //     $distance = distance($originLat,  $originLong, $destinationLat,  $destinationLong, "K");
    //     $estimatedDuration  = cal_time( $distance, 10);
        
    //     $sourceLocationArr = array( $originLat, $originLong);
    //     $destinationLocationArr = array(   $destinationLat,     $destinationLong);
    
        
    //     $restricted = isLocationRestricted( $sourceLocationArr);
        
    //     if( !isLocationRestricted($sourceLocationArr) && !isLocationRestricted($destinationLocationArr) && !isItOnWater( $originLat, $originLong) && !isItOnWater(  $destinationLat,     $destinationLong)){
            
    //         $timeDuration = '+'.$estimatedDuration.' seconds';
    //         $startTime = @date("Y-m-d H:i:s");
    //         $convertedTime = date('Y-m-d H:i:s',strtotime($startTime)+$timeDuration);
    //         $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
            
    //         $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
    //         $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
            
            
    //         if( $distance <= constants::MAXIMUM_DISTANCE_RANGE){
                
    //             // $TotalFarePerKM = $distance * constants::RATE_PER_KM;
    //             // $MaxDistanceFare = constants::MAXIMUM_DISTANCE_RANGE*constants::RATE_PER_KM;
    //             //$generatedFare =  constants::FLAT_RATE_PASAKAY + $TotalFarePerKM;
    //             $generatedFare =  constants::FLAT_RATE_PASAKAY;
    //             $MaxDistanceFare = constants::MAXIMUM_DISTANCE_RANGE*constants::RATE_PER_KM;
                
    //         }else{
                
    //             $remainingDistance = ceil($distance) -  constants::MAXIMUM_DISTANCE_RANGE;
                
                
    //             //$TotalFarePerKM = $distance * constants::RATE_PER_KM;
    //             $TotalFarePerKM_for_RemainingDistance =  $remainingDistance * constants::RATE_PER_KM;
    //             $MaxDistanceFare = constants::MAXIMUM_DISTANCE_RANGE*constants::RATE_PER_KM;
    //             $generatedFare =  constants::FLAT_RATE_PASAKAY + $TotalFarePerKM_for_RemainingDistance;
    //         }
            
    
          
            
    //         if($mode == "Search Driver"){
                
    //             $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
                
    //             $statement = $db->query($sql);
                
    //             $bookingRequest = $statement ->fetchAll(); 
                
    //             $messageArray['paymentMethod'] = $bookingData[0]['ePayType'];
               
                
    //         }
            
        
    //         $messageArray['response'] = 1;
    //         $messageArray['service'] = $servicetype;
    //         $messageArray['notificationCounter'] = countNotifications($userId, "User");
    //         $messageArray['status'] = "Okay";
    //         $messageArray['distance'] = $distance;
    //         $messageArray['paymentMethod'] = $bookingData[0]['ePayType'];
    //         $messageArray['Staring time'] = "Starting Time: ".$startTime;
    //         $messageArray['New Time'] = "Converted Time (added 1 hour): '.$convertedTime";
    //         $messageArray['estimatedTime'] = $newEstimatedTime." - ". $newConvertedTimeAllowance ;
    //         $messageArray['estimatedFare'] =  roundOff($generatedFare);
    //         $messageArray['estimatedFare_min'] =  roundOff($generatedFare);
    //         $messageArray['estimatedFare_max'] =  roundOff($generatedFare+$MaxDistanceFare);
    //         $messageArray['test2'] =  roundOff($distance);
            
    //         // $locationCheck 
            
    //         $messageArray['Origin Point'] =  isLocationRestricted($sourceLocationArr)."";
    //         $messageArray['Destination Point'] =  isLocationRestricted($destinationLocationArr)."";
    //         $messageArray['Origin Water'] =  isItOnWater( $originLat, $originLong)."";
    //         $messageArray['Destination Water'] =isItOnWater(  $destinationLat,     $destinationLong)."";
    //     }else{
    //         $messageArray['response'] = 0;
    //         $messageArray['service'] = $servicetype;
    //         $messageArray['status'] = "Out of Service Area Exceed on 5 kilometers range.";
    //         $messageArray['sourceAllow'] =isLocationAllowed( $sourceLocationArr) == true ? "true" : "false";
    //         $messageArray['destinationAllow'] =isLocationAllowed($destinationLocationArr)== true ? "true" : "false";
    
    //     }
        
        
        
    //     echo json_encode($messageArray);
        
    // }
    
    
    

    
    //$servicetype = "BOOK_PASAKAY";
    
    if($servicetype == "BOOK_PASAKAY"){
        
        unset($messageArray);
        unset($where);
        unset($Data);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.54472385189021';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'121.2200008639756';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $originLat  = isset($_POST['originLat']) ? trim($_POST['originLat']) : '14.54472385189021';
        $originLong  = isset($_POST['originLong']) ? trim($_POST['originLong']) : '121.2200008639756';
        $origin  = isset($_POST['origin']) ? trim($_POST['origin']) : 'Carissa 1';
        $destinationLat  = isset($_POST['destinationLat']) ? trim($_POST['destinationLat']) : '14.550040906383154';
        $destinationLong  = isset($_POST['destinationLong']) ? trim($_POST['destinationLong']) : '121.21856320002733';
        $destination  = isset($_POST['destination']) ? trim($_POST['destination']) : 'Bagumbayan';
        $payment  = isset($_POST['payment']) ? trim($_POST['payment']) : 'Cash';
        
        $distance = distance($originLat,  $originLong, $destinationLat,  $destinationLong, "K");
        $estimatedDuration  = cal_time( $distance, 10);
        
        $userLocationArr = array($latitude , $longitude);
        $sourceLocationArr = array($originLat, $originLong);
        $destinationLocationArr = array($destinationLat, $destinationLong);
    
        // // $locationCheck =  check_Address_restriction( $latitude,$longitude,  $userServiceArea);
        // if(isLocationAllowed($sourceLocationArr)  && isLocationAllowed($destinationLocationArr) &&  !isLocationRestricted($sourceLocationArr) && !isLocationRestricted($destinationLocationArr) && !isItOnWater( $originLat, $originLong) && !isItOnWater(  $destinationLat,     $destinationLong)){
            
        $userlocationId = getLocationArea($userLocationArr);
        $sourcelocationId = getLocationArea($sourceLocationArr);
        $destinationLocationId = getLocationArea($destinationLocationArr);
        

        //SPECIAL FARE FOR ACCORDING TO SERVICE AREA
        $suggestedToda = array();
        
        if($sourcelocationId == 22 && $destinationLocationId == 24){
            
            //DAGAT DAGATAN TO HULONG DUHAT
            
            $todaId = 1;
            $todaName = "Caloocan Area";
            $todaRouteNo = "C-001";
            $baseFare = (float)200;//250
            $fCommisionPercentage = 0.15;
            $farePricePerKm = 5;
            $farePricePerMin = 2;
            $radiusDistance = 5;
            $roundOffDistance = "Yes";
            $farePriceFirstKm = $farePricePerKm;
            $farePriceSucceedingKm = $farePricePerKm;
            
            
            
        }else if($sourcelocationId == 22 && $destinationLocationId == 23){
            
            //DAGAT DAGATAN TO CONCEPTION
            
            $todaId = 1;
            $todaName = "Caloocan Area";
            $todaRouteNo = "C-001";
            $baseFare = (float) 100;//120
            $fCommisionPercentage = 0.15;
            $farePricePerKm = 5;
            $farePricePerMin = 2;
            $radiusDistance = 5;
            $roundOffDistance = "Yes";
            $farePriceFirstKm = $farePricePerKm;
            $farePriceSucceedingKm = $farePricePerKm;
            
            
        }else if($sourcelocationId == 22 && $destinationLocationId == 25){
            
            //DAGAT DAGATAN TO CATMON
            
            $todaId = 1;
            $todaName = "Caloocan Area";
            $todaRouteNo = "C-001";
            $baseFare = (float) 150;
            $fCommisionPercentage = 0.15;
            $farePricePerKm = 5;
            $farePricePerMin = 2;
            $radiusDistance = 5;
            $roundOffDistance = "Yes";
            $farePriceFirstKm = $farePricePerKm;
            $farePriceSucceedingKm = $farePricePerKm;
            
        
        }else if($sourcelocationId == 22 && $destinationLocationId == 26){
            
            //DAGAT DAGATAN TO FRANCIS
            
            $todaId = 1;
            $todaName = "Caloocan Area";
            $todaRouteNo = "C-001";
            $baseFare = (float) 100;
            $fCommisionPercentage = 0.15;
            $farePricePerKm = 5;
            $farePricePerMin = 2;
            $radiusDistance = 5;
            $roundOffDistance = "Yes";
            $farePriceFirstKm = $farePricePerKm;
            $farePriceSucceedingKm = $farePricePerKm;
            
        }else if(($sourcelocationId == 31 || $sourcelocationId == 32 || $sourcelocationId == 33 || $destinationLocationId == 31 || $destinationLocationId == 32 || $destinationLocationId == 33 )){
                
            //SPECIAL AREA TERESA RIZAL
            
            //31 LOCATION ID - LA HACIENDA
            //32 LOCATION ID - MAY-IBA, ONDOY, ABUYOD
            //33 LOCATION ID - PRINZA
            
            $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$originLat." - vLatitude) * pi()/180 / 2), 2) +COS( ".$originLat." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $originLong ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
            $statement = $db->query($sql);
            $todaData = $statement ->fetchAll();
            
            $todaId = $todaData[0]['iTodaId'];
            $todaName = $todaData[0]['vTodaName'];
            $todaRouteNo = $todaData[0]['vTodaRouteNo'];
            $fCommisionPercentage =  $todaData[0]['fCommision'];
            $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
            $farePricePerKm = 20;
            $farePricePerMin = 2;
            $radiusDistance = 2;
            $roundOffDistance = "Yes";
            if($todaData[0]['eSurge'] == "Enable"){
                $baseFare = (float) $baseFare*1.1;
                //SURGE
                $farePricePerKm = $farePricePerKm*1.2;
                $farePriceFirstKm = $farePriceFirstKm*1.2;
                $farePriceSucceedingKm = $farePriceSucceedingKm*1.2;
            }
            
            
        }else if( $sourcelocationId == 30 && $destinationLocationId == 30){
                
            //REGULAR AREA TERESA RIZAL
    
            
            $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$originLat." - vLatitude) * pi()/180 / 2), 2) +COS( ".$originLat." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $originLong ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
            $statement = $db->query($sql);
            $todaData = $statement ->fetchAll();
            
            $todaId = $todaData[0]['iTodaId'];
            $todaName = $todaData[0]['vTodaName'];
            $todaRouteNo = $todaData[0]['vTodaRouteNo'];
            $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
            $fCommisionPercentage = (float) $todaData[0]['fCommision'];
            $farePricePerKm = 20;
            $farePricePerMin = 2;
            $radiusDistance = 2;
            $roundOffDistance = "Yes";
            $farePriceFirstKm = 20;
            $farePriceSucceedingKm = 10;
            if($todaData[0]['eSurge'] == "Enable"){
                $baseFare = (float) $baseFare*1.1;
                 //SURGE
                $farePricePerKm = $farePricePerKm*1.2;
                $farePriceFirstKm = $farePriceFirstKm*1.2;
                $farePriceSucceedingKm = $farePriceSucceedingKm*1.2;
            }
                   
                
                
        }else{
            
            
            // $sql = "SELECT * FROM register_toda WHERE iLocationId = '".$destinationLocationId."'";
            // $statement = $db->query($sql);
            // $todaData = $statement ->fetchAll(); 
            // $sourceLat = 14.542703712920263;
            // $sourceLong = 121.21699943045694;
            
            //SELECT NEAREST TODA
            $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLat." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLat." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLong ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
            $statement = $db->query($sql);
            $todaData = $statement ->fetchAll(); 
            
            if(count($todaData) > 0){
                    
                $todaId = $todaData[0]['iTodaId'];
                $todaName = $todaData[0]['vTodaName'];
                $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                $fCommisionPercentage = (float) $todaData[0]['fCommision'];
                $farePricePerKm = (float) $todaData[0]['fPricePerKM'];
                $farePricePerMin = (float) $todaData[0]['fPricePerMin'];
                $radiusDistance = (int) $todaData[0]['fRadius'];
                $roundOffDistance = "Yes";
                if($todaData[0]['eSurge'] == "Enable"){
                    $baseFare = (float) $baseFare*1.1;
                     //SURGE
                    $farePricePerKm = $farePricePerKm*1.2;
                    $farePriceFirstKm = $farePriceFirstKm*1.2;
                    $farePriceSucceedingKm = $farePriceSucceedingKm*1.2;
                }
            }
            
        }
        
        //  //SELECT ALL DRIVERS ONLINE
        // $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' AND vAvailability = 'Available' AND eStatus = 'active' having distance <= 5 order by distance";
        // $statement = $db->query($sql);
        // $allDriverData = $statement ->fetchAll();
        
        //  //SELECT ALL DRIVERS AVAILABLE
        // $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' AND vAvailability = 'Available' AND eStatus = 'active' AND (vTripStatus = 'FINISHED' OR vTripStatus = 'NONE') having distance <= 5 order by distance";
        // $statement = $db->query($sql);
        // $availableDriverData = $statement ->fetchAll();
        
        // if(round(count($allDriverData)*0.2) >= count($availableDriverData) ){
        //     unset($where);
        //     $where['iTodaId'] = $todaId;
        //     $updateToda['eSurge'] = "Enable";
        //     $result6 = myQuery("register_toda", $updateToda, "update", $where);
        //     $messageArray['isSurge'] = "Yes";
           
        //     $baseFare = (float) $baseFare*1.1;
        //     //SURGE
        //     $farePricePerKm = $farePricePerKm*1.2;
        //     $farePriceFirstKm = $farePriceFirstKm*1.2;
        //     $farePriceSucceedingKm = $farePriceSucceedingKm*1.2;
  
        // }else{
        //     unset($where);
        //     $where['iTodaId'] = $todaId;
        //     $updateToda['eSurge'] = "Disable";
        //     $messageArray['isSurge'] = "No";
        //     $result6 = myQuery("register_toda", $updateToda, "update", $where);
        // }
        

        $suggestedToda = array();
            
        $timeDuration = '+'.$estimatedDuration.' seconds';
    
        $startTime = date("Y-m-d H:i:s");
        
        $convertedTime = date('Y-m-d H:i:s',strtotime($timeDuration,strtotime($startTime)));
        
        $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
        
        $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
        
        $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
        
        
         //NIGHT TIME
        $farePricePerKm = (isNightTime())? ($farePricePerKm*1.2) : $farePricePerKm;
        $farePriceFirstKm = (isNightTime())? ($farePriceFirstKm*1.2) : $farePriceFirstKm;
        $farePriceSucceedingKm =  (isNightTime())? ($farePriceSucceedingKm*1.2) : $farePriceSucceedingKm;

        $TotalFarePerKM = $distance * (float) $farePricePerKm;
        $generatedFare =  (float) $baseFare +  (float) $TotalFarePerKM;
            
        $bookingNo = GenerateUniqueOrderNo("PS");

        $Data['vBookingNo'] =  $bookingNo;

        $Data['iUserId'] = (int) $userId ;
        
        $Data['vSourceLatitude'] = $originLat;
        
        $Data['vSourceLongitude'] =  $originLong;
        
        $Data['vDestLatitude'] = $destinationLat;
        
        $Data['vDestLongitude'] = $destinationLong;
        
        $Data['vSourceAddress'] =  addcslashes($origin, "'"); //stripslashes($origin);
        
        $Data['tDestAddress'] = addcslashes($destination, "'");
        
        $Data['vDistance'] = $distance;

        $Data['vDuration'] = $estimatedDuration ;

        $Data['dBooking_date'] = @date("Y-m-d H:i:s");

        $Data['eStatus'] = "Searching for drivers";

        $Data['ePayType'] = $payment;
        
        $Data['fFlatTripPrice'] = 40.00;
       
        $Data['fPricePerKM'] =  (float) $farePricePerKm;
        
        $Data['fPricePerMin'] = (float) $farePricePerMin;
        
        $Data['iBaseFare'] =  (float) $baseFare;
        
        $Data['iTodaId'] =  (int) $todaId;
       
        $Data['ePayWallet'] = "No";
        
        $Data['fTripGenerateFare'] = (float)  $generatedFare;
        
        $Data['fTripTotalAmountFare'] = (float)  $generatedFare;
        
        $Data['fCompanyPercentage'] = (float)  $fCommisionPercentage;
        
            
        // $result = myQuery("cab_booking",  $Data, "insert");
        
        // $where['iUserId'] = $userId;
        // $user_status['vTripStatus'] = "BOOKED";
        // $user_status['vBookingNo'] =  $bookingNo;

        // $result2 = myQuery("register_user",  $user_status, "update", $where);
                
        // $messageArray['response'] = 1;
        // $messageArray['notificationCounter'] = countNotifications($userId, "User");
        // $messageArray['service'] = $servicetype;
        // $messageArray['status'] =  $Data;
        // $messageArray['duration'] =  $estimatedDuration;
        // $messageArray['bookingId'] =  $distance;
        // $messageArray['bookingNo'] =  $Data['vBookingNo'];
    
        //  echo json_encode( $messageArray);
        
        $lastInsertedId = myQuery("cab_booking",  $Data, "insert_getlastid");
        if($lastInsertedId != ""){
           
            $where['iUserId'] = $userId;
            $user_status['vTripStatus'] = "BOOKED";
            $user_status['vBookingNo'] =  $bookingNo;
    
            $result2 = myQuery("register_user",  $user_status, "update", $where);
                    
            
            $messageArray['response'] = 1;
            $messageArray['notificationCounter'] = countNotifications($userId, "User");
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  $Data;
            $messageArray['duration'] =  $estimatedDuration;
            $messageArray['bookingId'] =  $distance;
            $messageArray['bookingNo'] =  $Data['vBookingNo'];
            
            
            echo json_encode( $messageArray);
        }else{
            $messageArray['response'] = 0;
            $messageArray['servicetype'] = $servicetype;
            $messageArray['data'] = $Data;
            
             echo json_encode( $messageArray);
          
        }
        
       
       
    
    }
    
  
  
    
 if($servicetype == "LOAD_CURRENT_TASK"){
        
          
        unset($messageArray);
        unset($where);
        
      
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'45';
        $userType  = isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $deviceInfo = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) :'Driver';
        
        $sql = "SELECT * FROM register_driver WHERE iDriverId = '".$userId."'";
        
        $statement = $db->query($sql);
        
        $taskData = $statement ->fetchAll(); 
        
        $driverName =  $taskData[0]['vName']. " ".$taskData[0]['vLastName'];
        
        $driverId = $taskData[0]['iDriverId'];
        
        $driverImage = $taskData[0]['vImage'];
        
        $driverToda = $taskData[0]['vTodaLine'];
        
        $driverPlateNo = $taskData[0]['vPlateNo'];
        
        
        if($taskData[0]['eStatus'] != "active"){
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "Failed";
            $messageArray['message'] =  "Driver is not Active";
        
            echo json_encode($messageArray);
            
        }else{
            // echo "Active";
        }
        
       
        if($taskData[0]['vTripStatus'] == "ON_GOING" || $taskData[0]['vTripStatus'] == "ON_THE_WAY_TO_DESTINATION" || $taskData[0]['vTripStatus'] == "ON_THE_WAY_TO_DROPOFF" || $taskData[0]['vTripStatus'] == "IN_TRANSIT" || $taskData[0]['vTripStatus'] == "ARRIVED_AT_PICKUP_POINT"){
            
       
            if($taskData[0]['vAppServiceType'] == "PABILI"){
                
                
                $sql = "SELECT * FROM trips WHERE iTripId = '".$taskData[0]['iTripId']."'";
                
                $statement = $db->query($sql);
                
                $tripData = $statement ->fetchAll(); 
                
                $storeId = $tripData[0]['iCompanyId'];
                
                $orderId = $tripData[0]['iOrderId'];
                
                $userId = $tripData[0]['iUserId'];
                
                //USER DATA
                unset($where);
                $where['iUserId'] = $userId;
                $userData = myQuery("register_user", array("vName", "vLastName", "vLatitude", "vLongitude", "vImgName", "vPhone"), "selectall", $where);
                
                //ORDER DATA
                
                $sql = "SELECT * FROM orders WHERE iOrderId = '". $orderId."'";
        
                $statement = $db->query($sql);
                
                $orderData = $statement ->fetchAll(); 
                
                $storeId = $orderData[0]['iCompanyId'];
                
                // $sql2 = "SELECT mi.iMenuItemId as itemId,  mi.vItemType_EN as itemName, mi.fPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc, od.vCancel as itemCancel FROM menu_items as mi 
                // LEFT JOIN order_details as od ON mi.iMenuItemId = od.iMenuItemId WHERE od.iOrderId = ". $orderId;
                
                $sql2 = "SELECT od.iMenuItemId, od.vItemName as itemName, od.fOriginalPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc,
                od.vCancel as itemCancel FROM order_details as od WHERE od.iOrderId = ". $orderId;
       
        
                $statement = $db->query($sql2);
                
                $items = $statement ->fetchAll(); 
                
                $itemsCount = 0;
                
                
                for($i = 0; $i < count($items); $i++) {
                    
                    $itemsCount = $itemsCount+(int)$items[$i]['itemQty'];
                    
                    $orderDetails['orderItems'][] =  $items[$i];
                    
                }
               
               //STORE DATA
                unset($where);
                $where['iCompanyId'] = $storeId;
                $companyAddress = myQuery("company", array("vCompany", "vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
                $storeName =  $companyAddress[0]['vCompany'];
                $storeAddress =  $companyAddress[0]['vRestuarantLocation'];
                
                
                $distance = distance($tripData[0]['tStartLat'], $tripData[0]['tStartLong'],$tripData[0]['tEndLat'], $tripData[0]['tEndLong'] , "K");
                $duration = cal_time( $distance, 10);
                $durationFGDTime = cal_time( $distance, 10);
                
                //UPDATE TRIPS
                
                $sql = "UPDATE trips SET fDistance = '".$distance."', fDuration = '".$duration."', fGDtime = '".$durationFGDTime."' WHERE iTripId = ".$taskData[0]['iTripId'];
        
                $statement = $db->query($sql);
                
                $tripResult = $statement ->execute(); 
            
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['serviceMode'] = "PABILI";
                $messageArray['status'] =  "OKAY";
                $messageArray['message'] =  "PABILI";
                
                $messageArray['tripStatus'] = $tripData[0]['iActive'];
                $messageArray['statusCode'] = $orderData[0]['iStatusCode'];
                $messageArray['startLat'] = $tripData[0]['tStartLat'];
                $messageArray['startLong'] = $tripData[0]['tStartLong'];
                $messageArray['destLat'] = $tripData[0]['tEndLat'];
                $messageArray['destLong'] = $tripData[0]['tEndLong'];
                $messageArray['distance'] = $distance;
                $messageArray['duration'] = $duration;
                
                $messageArray['driverName'] =  $driverName;
                $messageArray['driverId'] = $driverId;
                $messageArray['driverImage'] = $driverImage;
                $messageArray['driverToda'] = $driverToda;
                $messageArray['driverPlateNo'] = $driverPlateNo;
                $messageArray['tripItinerary'] = $tripData[0]['vTripItinerary'];
                
                
                if($tripData[0]['vTripItinerary'] == "FirstTrip"){
                    $messageArray['tripDestination'] =  $storeName;
                    $messageArray['tripDestinationAddress'] = $tripData[0]['tDaddress'];
                }else if($tripData[0]['vTripItinerary'] == "SecondTrip"){
                    $messageArray['tripDestination'] = $orderData[0]['vName'];
                    $messageArray['tripDestinationAddress'] = $tripData[0]['tDaddress'];
                }else if($tripData[0]['vTripItinerary'] == "LastTrip"){
                    
                    if($orderData[0]['vDeliveryAddress_2'] != ""){
                        $messageArray['tripDestination'] = $orderData[0]['vName'] ." / ".$orderData[0]['vPhone'];
                        $messageArray['tripDestinationAddress'] =$tripData[0]['tDaddress'];
                    }else{
                        $messageArray['tripDestination'] = $orderData[0]['vName'] ." / ".$orderData[0]['vPhone'];
                        $messageArray['tripDestinationAddress'] = $tripData[0]['tDaddress'];
                    }
                   
                }
                
                
                //get_Address($tripData[0]['tEndLat'],$tripData[0]['tEndLong']);
                
                
                $messageArray['userId'] =  $userId;
                $messageArray['userName'] =  $userData[0]['vName'];
                $messageArray['userLastName'] =  $userData[0]['vLastName'];
                $messageArray['userImage'] =  $userData[0]['vImgName'];
                $messageArray['vPhone'] =  $userData[0]['vPhone'];
                $messageArray['userLat'] =  $userData[0]['vLatitude'];
                $messageArray['userLong'] =  $userData[0]['vLongitude'];
                
                $messageArray['orderId'] = $orderId;
                $messageArray['orderNo'] = $orderData[0]['vOrderNo'];
                $messageArray['orderQty'] = $itemsCount;
                $messageArray['orderDate'] = $orderData[0]['dDate'];
                $messageArray['orderName'] = $orderData[0]['vName'];
                $messageArray['orderType'] = $orderData[0]['vOrderType'];
                $messageArray['orderStatus'] = $orderData[0]['iStatusCode'];
                $messageArray['orderPayment'] = $orderData[0]['ePaymentOption'];
                $messageArray['orderSummary'] = $orderDetails;
                $messageArray['orderTotalPrice'] = $orderData[0]['fTotalGenerateFare'];
                $messageArray['orderStatus'] = $taskData[0]['vTripStatus'];
                
    
                echo json_encode( $messageArray);
                
            }
            
            if($taskData[0]['vAppServiceType'] == "PASAKAY"){
                
                $sql = "SELECT * FROM trips WHERE iTripId = '".$taskData[0]['iTripId']."'";
                
                $statement = $db->query($sql);
                
                $tripData = $statement ->fetchAll(); 
                
                $bookingId = $tripData[0]['iCabBookingId'];
                
                $userId = $tripData[0]['iUserId'];
                
                //USER DATA
                unset($where);
                $where['iUserId'] = $userId;
                $userData = myQuery("register_user", array("vName", "vLastName", "vLatitude", "vLongitude", "vImgName", "vPhone"), "selectall",  $where);
                
                
                 //BOOKING DATA
                
                $sql = "SELECT * FROM cab_booking WHERE iCabBookingId = '".$bookingId."'";
        
                $statement = $db->query($sql);
                
                $bookingData = $statement ->fetchAll(); 
                
                $distance = distance($tripData[0]['tStartLat'], $tripData[0]['tStartLong'],$tripData[0]['tEndLat'], $tripData[0]['tEndLong'] , "K");
                $duration = cal_time( $distance, 10);
                $durationFGDTime = cal_time( $distance, 10);
        
                
                //UPDATE TRIPS
                
                $sql = "UPDATE trips SET fDistance = '".$distance."', fDuration = '".$duration."' WHERE iTripId = ".$taskData[0]['iTripId'];
        
                $statement = $db->query($sql);
                
                $tripResult = $statement ->execute(); 
                
                
                
                
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "OKAY";
                $messageArray['serviceMode'] = "PASAKAY";
                $messageArray['message'] =  "PASAKAY";
                
                $messageArray['tripStatus'] = $tripData[0]['iActive'];
                $messageArray['statusCode'] = $bookingData[0]['eStatus'];
                $messageArray['startLat'] = $tripData[0]['tStartLat'];
                $messageArray['startLong'] = $tripData[0]['tStartLong'];
                $messageArray['destLat'] = $tripData[0]['tEndLat'];
                $messageArray['destLong'] = $tripData[0]['tEndLong'];
                $messageArray['distance'] = $distance;
                
                $messageArray['driverName'] =  $driverName;
                $messageArray['driverId'] = $driverId;
                $messageArray['driverImage'] = $driverImage;
                
                $messageArray['tripItinerary'] = $tripData[0]['vTripItinerary'];
                
                
            
                
                if($tripData[0]['vTripItinerary'] == "FirstTrip"){
                    $messageArray['tripDestination'] =   $userData[0]['vName'];
                      $messageArray['tripDestinationAddress'] = $tripData[0]['tDaddress'];
              
                }else if($tripData[0]['vTripItinerary'] == "SecondTrip"){
                    $messageArray['tripDestination'] =  $userData[0]['vName'];
                    $messageArray['tripDestinationAddress'] = $tripData[0]['tDaddress'];
                }else if($tripData[0]['vTripItinerary'] == "LastTrip"){
                    
                    if($orderData[0]['vDeliveryAddress_2'] != ""){
                        $messageArray['tripDestination'] = $userData[0]['vName'];
                       // $messageArray['tripDestinationAddress'] = get_Address($tripData[0]['tEndLat'],$tripData[0]['tEndLong']);
                        $messageArray['tripDestinationAddress'] = $bookingData[0]['tDestAddress'];
        
                    }else{
                        $messageArray['tripDestination'] = $userData[0]['vName'];
                        //$messageArray['tripDestinationAddress'] = get_Address($tripData[0]['tEndLat'],$tripData[0]['tEndLong']);
                         $messageArray['tripDestinationAddress'] = $bookingData[0]['tDestAddress'];
                    }
                   
                }
                
                //get_Address($tripData[0]['tEndLat'],$tripData[0]['tEndLong']);
              
                $startTime = @date("Y-m-d H:i:s");
                $diff = strtotime($startTime) - strtotime($bookingData[0]['tWaitingStartTime']);
                
                $messageArray['userId'] =  $userId;
                $messageArray['userName'] =  $userData[0]['vName'];
                $messageArray['userLastName'] =  $userData[0]['vLastName'];
                $messageArray['userImage'] =  $userData[0]['vImgName'];
                $messageArray['userLat'] =  $userData[0]['vLatitude'];
                $messageArray['userLong'] =  $userData[0]['vLongitude'];
                $messageArray['vPhone'] =  $userData[0]['vPhone'];
                
                $messageArray['bookingId'] =  $bookingData[0]['iCabBookingId'];
                $messageArray['bookingWaitingStatus'] =  $bookingData[0]['eWaitingStatus'];
                $messageArray['bookingWaitingTime'] =  $bookingData[0]['vWaitingTime'];
                $messageArray['bookingStartWaitingTime'] =  $bookingData[0]['tWaitingStartTime'];
                 $messageArray['bookingWaitingTimeFromStartTime'] = $diff;
                $messageArray['bookingStatus'] =  $bookingData[0]['eStatus'];
                $messageArray['bookingStatusCancelledBy'] =  $bookingData[0]['eCancelBy'];
                $messageArray['bookingNo'] =  $bookingData[0]['vBookingNo'];
                $messageArray['bookingTotalAmount'] = $bookingData[0]['fTripGenerateFare'];
                $messageArray['bookingPayment'] = $bookingData[0]['ePayType'];
                $date = date_create($bookingData[0]['dBooking_date']);
                $messageArray['bookingDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
                $messageArray['bookingOrigin'] = $bookingData[0]['vSourceAddress'];
                $messageArray['bookingOriginLat'] = $bookingData[0]['vSourceLatitude'];
                $messageArray['bookingOriginLong'] = $bookingData[0]['vSourceLongitude'];
                $messageArray['bookingDestination'] = $bookingData[0]['tDestAddress'];
                $messageArray['bookingDestinationLat'] = $bookingData[0]['vDestLatitude'];
                $messageArray['bookingDestinationLong'] = $bookingData[0]['vDestLongitude'];
            
               
            }
            
        }else{
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "Failed";
            $messageArray['message'] =  "No Available Task";
        
            
        }

        if($deviceInfo != $taskData[0]['tDeviceData']){
                
            unset($messageArray);
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['userType'] = $userType;
            $messageArray['error'] = "AUTO_LOGOUT";
            $messageArray['deviceInfo'] = $deviceInfo;
            $messageArray['currenmtdeviceInfo'] = $taskData[0]['tDeviceData'];
              
        }

        echo json_encode( $messageArray);
        
    }
    
    //$servicetype = "SEND_BOOKING_REQUEST_TO DRIVERS";
    
    
      if($servicetype == "SEND_BOOKING_REQUEST_TO DRIVERS"){
        
        unset($messageArray);
        unset($where);

        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.647130793185664';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'120.99170777633799';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'8';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'18';
        $bookingNo  = isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'PS21061947121';
        
        $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
        $statement = $db->query($sql);
        $bookingData = $statement ->fetchAll(); 
        
        //BOOKING ORIGIN POINTS AND DESTINATION POINTS 
        $sourceLocationArr = array($bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude']);
        $destinationLocationArr = array($bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude']);
        
        //FILTERING THE LOCATIONS 
        $sourcelocationId = getLocationArea($sourceLocationArr);
        $destinationLocationId = getLocationArea($destinationLocationArr);

        //LOGCAT

        // echo "Source Location Id : ".$sourcelocationId;
        // echo "<br>";
        // echo "Destination Location Id : ".$destinationLocationId;
        // echo "<br>";
    
        $suggestedToda = array();
        $isSurge = "No";
        $bookingType = "Normal";
        $bookingType = (isNightTime())? "Early/Night Booking" : "Normal";
        
        if($bookingData[0]['eStatus'] == "Searching for drivers"){
                       
            //SPECIAL FARE FOR ACCORDING TO SERVICE AREA
            
            if($sourcelocationId == 22 && $destinationLocationId == 24){
                
                //DAGAT DAGATAN TO HULONG DUHAT
                
                $todaId = 1;
                $todaName = "Caloocan Area";
                $todaRouteNo = "C-001";
                $baseFare = (float)200;//250
                $fCommisionPercentage = 0.15;
                $surgeRate = "Disable";
                $roundOffDistance = "Yes";
                $radiusDistance = 5;
                $farePricePerKm = 5;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
    
                
            }else if($sourcelocationId == 22 && $destinationLocationId == 23){
                
                //DAGAT DAGATAN TO CONCEPTION
                
                $todaId = 1;
                $todaName = "Caloocan Area";
                $todaRouteNo = "C-001";
                $baseFare = (float) 100;//120
                $fCommisionPercentage = 0.15;
                $surgeRate = "Disable";
                $roundOffDistance = "Yes";
                $radiusDistance = 5;
                $farePricePerKm = 5;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                
                
            }else if($sourcelocationId == 22 && $destinationLocationId == 25){
                
                //DAGAT DAGATAN TO CATMON
                
                $todaId = 1;
                $todaName = "Caloocan Area";
                $todaRouteNo = "C-001";
                $baseFare = (float) 150;
                $fCommisionPercentage = 0.15;
                $surgeRate = "Disable";
                $roundOffDistance = "Yes";
                $radiusDistance = 5;
                $farePricePerKm = 5;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                
            
            }else if($sourcelocationId == 22 && $destinationLocationId == 26){
                
                //DAGAT DAGATAN TO FRANCIS
                
                $todaId = 1;
                $todaName = "Caloocan Area";
                $todaRouteNo = "C-001";
                $baseFare = (float) 100;
                $fCommisionPercentage = 0.15;
                $surgeRate = "Disable";
                $roundOffDistance = "Yes";
                $radiusDistance = 5;
                $farePricePerKm = 5;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                
            }else if(($sourcelocationId == 31 || $sourcelocationId == 32 || $sourcelocationId == 33 || $destinationLocationId == 31 || $destinationLocationId == 32 || $destinationLocationId == 33 )){
                    
                //SPECIAL AREA TERESA RIZAL
                
                //31 LOCATION ID - LA HACIENDA
                //32 LOCATION ID - MAY-IBA, ONDOY, ABUYOD
                //33 LOCATION ID - PRINZA
                
                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $todaData = $statement ->fetchAll();
                
                $todaId = $todaData[0]['iTodaId'];
                $todaName = $todaData[0]['vTodaName'];
                $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                $fCommisionPercentage =  $todaData[0]['fCommision'];
                $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                $surgeRate = $todaData[0]['eSurge'];
                $roundOffDistance = "Yes";
                $radiusDistance = 3;
                $farePricePerKm = 20;
                $farePricePerMin = 2;
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                
                
            }else if( $sourcelocationId == 30 && $destinationLocationId == 30){
                    
                //REGULAR AREA TERESA RIZAL
                
                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $todaData = $statement ->fetchAll();
                
                $todaId = $todaData[0]['iTodaId'];
                $todaName = $todaData[0]['vTodaName'];
                $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                $fCommisionPercentage =  $todaData[0]['fCommision'];
                $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                $surgeRate = $todaData[0]['eSurge'];
                $roundOffDistance = "Yes";
                $radiusDistance = 3;
                $farePricePerKm = 20;
                $farePricePerMin = 2;
                $farePriceFirstKm = 20;
                $farePriceSucceedingKm = 10;
                    
            }else{
                
                
                // $sql = "SELECT * FROM register_toda WHERE iLocationId = '".$destinationLocationId."'";
                // $statement = $db->query($sql);
                // $todaData = $statement ->fetchAll(); 
                // $sourceLat = 14.542703712920263;
                // $sourceLong = 121.21699943045694;
                
                //SELECT NEAREST TODA
                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1] ." - vLongitude) * pi()/180 / 2), 2) ))) as distance from register_toda having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $todaData = $statement ->fetchAll();

                // echo '<br>';
                // echo json_encode($todaData);
                
                if(count($todaData) > 0){
                        
                    $todaId = $todaData[0]['iTodaId'];
                    $todaName = $todaData[0]['vTodaName'];
                    $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                    $fCommisionPercentage =  $todaData[0]['fCommision'];
                    $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                    $surgeRate = $todaData[0]['eSurge'];
                    $roundOffDistance = "Yes";
                    $radiusDistance = 3;
                    $farePricePerKm = $todaData[0]['fPricePerKM'];
                    $farePricePerMin = $todaData[0]['fPricePerMin'];
                    $farePriceFirstKm = 0;
                    $farePriceSucceedingKm = 0;
                }
                
            }

            //LOGCAT
            // echo "Toda Id : ".$todaId;
            // echo "<br>";
            // echo "Todaname : ".$todaName;
            // echo "<br>";
            // echo "Todaname : ".$todaRouteNo;
            // echo "<br>";
            // echo "Commision : ".$fCommisionPercentage;
            // echo "<br>";
            // echo "baseFare : ".$baseFare;
            // echo "<br>";
            
            
          
           if($surgeRate == "Enable"){
                $baseFare = (float) $baseFare*1.1;
                 //SURGE
                $farePricePerKm = $farePricePerKm*1.2;
                $farePriceFirstKm = $farePriceFirstKm*1.2;
                $farePriceSucceedingKm = $farePriceSucceedingKm*1.2;
                $isSurge = "Yes";
                $bookingType = "Surge";
            }else{
                
                 //NIGHT TIME
                $farePricePerKm = (isNightTime())? ($farePricePerKm*1.2) : $farePricePerKm;
                $farePriceFirstKm = (isNightTime())? ($farePriceFirstKm*1.2) : $farePriceFirstKm;
                $farePriceSucceedingKm =  (isNightTime())? ($farePriceSucceedingKm*1.2) : $farePriceSucceedingKm;
                $bookingType = (isNightTime())? "Early/Night Booking" : "Normal";
            }
          
          
            //SELECT ALL DRIVERS
            $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' having distance <= 5 order by distance";
            $statement = $db->query($sql);
            $driverData = $statement ->fetchAll();
            
            $distance = distance($sourceLocationArr[0], $sourceLocationArr[1], $destinationLocationArr [0], $destinationLocationArr [1], "K");
            $estimatedDuration  = cal_time( $distance, 10);


            //  //LOGCAT
            // echo "Drivers : ".count($driverData);
            // echo "<br>";
            // echo "Todaname : ".$todaName;
            // echo "<br>";
            // echo "Todaname : ".$todaRouteNo;
            // echo "<br>";
            // echo "Commision : ".$fCommisionPercentage;
            // echo "<br>";
            // echo "baseFare : ".$baseFare;
            // echo "<br>";
            
            
            if( ceil($distance) < 3){
    
                $generatedFare =  $baseFare;
                
            }else{
               
                $remainingDistance = (int) ceil($distance)- $radiusDistance;
               
                $additionalSuccedingFare = 0;
                if($farePriceFirstKm != 0 || $farePriceSucceedingKm != 0 ){
            
                    for($x=0; $x<$remainingDistance; $x++){
                        if($x == 0){
                            $additionalSuccedingFare = $additionalSuccedingFare + $farePriceFirstKm; 
                        }else{
                            $additionalSuccedingFare =  $additionalSuccedingFare + $farePriceSucceedingKm; 
                        }
                    }
                }else{
                    $additionalSuccedingFare = ($remainingDistance*$farePricePerKm);
                }
                
                $generatedFare =  $baseFare+$additionalSuccedingFare;
                
            }
            
            
            $timeDuration = '+'.$estimatedDuration.' seconds';
            $startTime = @date("Y-m-d H:i:s");
            $convertedTime = date('Y-m-d H:i:s',strtotime($startTime)+$timeDuration);
            $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
            
            $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
            $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");



             //LOGCAT
            // echo "Drivers : ".count($driverData);
            // echo "<br>";
           
         
            $requestMessage['userId'] = $userId;
            $requestMessage['requestType'] = "Pasakay";
            $requestMessage['bookingType'] = $bookingType;
            $requestMessage['amount'] =  $generatedFare;
            $requestMessage['itemsQty'] = "0";
            $requestMessage['paymentMethod'] = $bookingData[0]['ePayType'];
            $requestMessage['orderId'] = $bookingData[0]['iCabBookingId'];
            $requestMessage['bookingId'] = $bookingData[0]['iCabBookingId'];
            $requestMessage['orderNo'] =  $bookingData[0]['vBookingNo'];
            $requestMessage['bookingNo'] =  $bookingData[0]['vBookingNo'];
            $requestMessage['storeId'] = "0";
    
            $requestMessage['sourceAddress'] =  $bookingData[0]['vSourceAddress'];
            $requestMessage['sourceAddressLat'] = $bookingData[0]['vSourceLatitude'];
            $requestMessage['sourceAddressLong'] =  $bookingData[0]['vSourceLongitude'];
            
            $requestMessage['deliveryName'] =  "";
            $requestMessage['deliveryAddress'] =  $bookingData[0]['tDestAddress'];
            $requestMessage['deliveryAddressLat'] = $bookingData[0]['vDestLatitude'];
            $requestMessage['deliveryAddressLong'] =  $bookingData[0]['vDestLongitude'];
            
            $requestMessage['deliveryName'] =  "";
            $requestMessage['deliveryAddress2'] =  "";
            $requestMessage['deliveryAddressLat2'] = "";
            $requestMessage['deliveryAddressLong2'] =  "";
            
            $requestMessage['distance'] = $distance;
            $requestMessage['ETA'] = $estimatedDuration;
    
            $requestArray = array();
        
            for($i = 0; $i < count( $driverData); $i++) {
                
                $driverdistance = ceil(distance($driverData[$i]['vLatitude'], $driverData[$i]['vLongitude'], $sourceLocationArr[0], $sourceLocationArr[1], "K"));
                $estimatedDuration  = cal_time( $distance, 10);

                //echo "Driver Id : ".$driverData[$i]['iDriverId']." Distance : ". $driverdistance ."</br>";
            
                if($driverdistance <= $radiusDistance){
                    
                    
                    $TotalFarePerKM_fromDriver = 0;
                    
                    $finaldriverdistance = $driverdistance > 0.1 ? ceil($driverdistance) : $driverdistance;
                    if($farePriceFirstKm != 0 && $farePriceSucceedingKm != 0){
                        
                        if($finaldriverdistance > 1){ //GREATER 1 Kilometer
                            for($a = 0; $a < $finaldriverdistance; $a++){
                                if($a == 0){
                                    $TotalFarePerKM_fromDriver = $TotalFarePerKM_fromDriver + $farePriceFirstKm; //FIRST KILOMETER
                                }else{
                                    $TotalFarePerKM_fromDriver = $TotalFarePerKM_fromDriver + $farePriceSucceedingKm; //SUCCEEDING KILOMETER
                                }
                            }
                        }else{
                           $TotalFarePerKM_fromDriver += $farePriceFirstKm; //FIRST KILOMETER 
                        }
                        
                        
                    }else{
                        $TotalFarePerKM_fromDriver = $finaldriverdistance * $farePricePerKm;
                    }
                    // echo "Driver Fare : ". $TotalFarePerKM_fromDriver  ."</br>";
                    // $finalDistance = $driverdistance+$distance;
                    $finalDistance = $distance;
                    
                    $requestMessage['isSurge'] = $isSurge;
                    $requestMessage['baseFare'] =  $generatedFare;
                    $requestMessage['additionalFare'] = $TotalFarePerKM_fromDriver;
                    $requestMessage['amount'] =  $generatedFare+ $TotalFarePerKM_fromDriver;
                    $requestMessage['distance'] = number_format($finalDistance, 2, '.', '') ;
                    $requestMessage['driverDistance'] = number_format($finaldriverdistance, 2, '.', '') ;
                    
                    // echo "Final Fare : ".$requestMessage['amount'] ."</br></br></br>";
                    
                    $driver_for_Request[] = $driverData[$i];
                    $requestArray[] = $requestMessage;
                    
                }

                
            }
            
            for($i = 0; $i < count( $driver_for_Request); $i++) {
            
                sendRequestToDriver($driver_for_Request[$i]['iDriverId'], "PASAKAY_REQUEST", json_encode($requestArray[$i]));
                //echo "Driver : ".$driver_for_Request[$i]['iDriverId']." : Reg Id : ".$driver_for_Request[$i]['vFirebaseDeviceToken']." <br/>";
                
            }
            
            
        }else if($bookingData[0]['eStatus'] == "Re-assigning for drivers"){
            
            // echo 'REASSIGNING';
            // echo '</br>';
            $sql = "SELECT * FROM register_toda WHERE iTodaId = '".$bookingData[0]['iTodaId']."'";
            $statement = $db->query($sql);
            $todaData = $statement ->fetchAll();
        
             
            if(count($todaData) > 0){
                
                // echo 'Toda Id : '.$todaData[0]['iTodaId'];
                // echo '</br>';
                    
                $todaId = $todaData[0]['iTodaId'];
                $todaName = $todaData[0]['vTodaName'];
                $todaRouteNo = $todaData[0]['vTodaRouteNo'];
                $fCommisionPercentage =  $todaData[0]['fCommision'];
                $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
                $surgeRate = $todaData[0]['eSurge'];
                $roundOffDistance = "Yes";
                $radiusDistance = (int) $todaData[0]['fRadius'];
                $farePricePerKm = $todaData[0]['fPricePerKM'];
                $farePricePerMin = $todaData[0]['fPricePerMin'];
                $farePriceFirstKm = 0;
                $farePriceSucceedingKm = 0;
                
                $distance = distance($sourceLocationArr[0], $sourceLocationArr[1], $destinationLocationArr [0], $destinationLocationArr [1], "K");
                $estimatedDuration  = cal_time( $distance, 10);
                
                
                $timeDuration = '+'.$estimatedDuration.' seconds';
                $startTime = @date("Y-m-d H:i:s");
                $convertedTime = date('Y-m-d H:i:s',strtotime($startTime)+$timeDuration);
                $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
                
                $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
                $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
                
                 //SELECT ALL DRIVERS
                $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' having distance <= 5 order by distance";
                $statement = $db->query($sql);
                $driverData = $statement ->fetchAll();
                // echo '</br>';
                // echo "Distance : ". $distance ."</br>";
             
                $requestMessage['userId'] = $userId;
                $requestMessage['requestType'] = "Pasakay";
                $requestMessage['bookingType'] = $bookingType;
                $requestMessage['amount'] =  $bookingData[0]['fTripTotalAmountFare'];
                $requestMessage['itemsQty'] = "0";
                $requestMessage['paymentMethod'] = $bookingData[0]['ePayType'];
                $requestMessage['orderId'] = $bookingData[0]['iCabBookingId'];
                $requestMessage['bookingId'] = $bookingData[0]['iCabBookingId'];
                $requestMessage['orderNo'] =  $bookingData[0]['vBookingNo'];
                $requestMessage['bookingNo'] =  $bookingData[0]['vBookingNo'];
                $requestMessage['storeId'] = "0";
        
                $requestMessage['sourceAddress'] =  $bookingData[0]['vSourceAddress'];
                $requestMessage['sourceAddressLat'] = $bookingData[0]['vSourceLatitude'];
                $requestMessage['sourceAddressLong'] =  $bookingData[0]['vSourceLongitude'];
                
                $requestMessage['deliveryName'] =  "";
                $requestMessage['deliveryAddress'] =  $bookingData[0]['tDestAddress'];
                $requestMessage['deliveryAddressLat'] = $bookingData[0]['vDestLatitude'];
                $requestMessage['deliveryAddressLong'] =  $bookingData[0]['vDestLongitude'];
                
                $requestMessage['deliveryName'] =  "";
                $requestMessage['deliveryAddress2'] =  "";
                $requestMessage['deliveryAddressLat2'] = "";
                $requestMessage['deliveryAddressLong2'] =  "";
                
                $requestMessage['distance'] = $distance;
                $requestMessage['ETA'] = $estimatedDuration;
        
                $requestArray = array();
            
                for($i = 0; $i < count( $driverData); $i++) {
                    
                    
                    
                    $driverdistance = ceil(distance($driverData[$i]['vLatitude'], $driverData[$i]['vLongitude'], $sourceLocationArr[0], $sourceLocationArr[1], "K"));
                    $estimatedDuration  = cal_time( $distance, 10);
                    
                //      echo "Driver Id : ".$driverData[$i]['iDriverId']." Distance : ". $driverdistance ."</br>";
                //      echo "Radius : ".$radiusDistance;
                // echo '</br>';
                    if($driverdistance <= $radiusDistance){
                        
                      // echo "Driver Id : ".$driverData[$i]['iDriverId']." Distance : ". $driverdistance ."</br>";
                        $TotalFarePerKM_fromDriver = 0;
                        $finaldriverdistance = $driverdistance > 0.1 ? ceil($driverdistance) : $driverdistance;
                        echo "Driver Fare : ". $bookingData[0]['fAdditionalFare']  ."</br>";
                        $finalDistance = $driverdistance+$distance;
                        $finalDistance = $distance;
                        
                        $requestMessage['isSurge'] = "No";
                        $requestMessage['baseFare'] = $bookingData[0]['iBaseFare'];
                        $requestMessage['additionalFare'] = $bookingData[0]['fAdditionalFare'];
                        $requestMessage['amount'] =  $bookingData[0]['fTripTotalAmountFare'];
                        $requestMessage['distance'] = number_format($finalDistance, 2, '.', '') ;
                        $requestMessage['driverDistance'] = number_format($finaldriverdistance, 2, '.', '') ;
                        
                        // echo "Final Fare : ".$requestMessage['amount'] ."</br></br></br>";
                        // echo '</br>';
                        $driver_for_Request[] = $driverData[$i];
                        $requestArray[] = $requestMessage;
                        
                    }
                    
                }
                
                for($i = 0; $i < count( $driver_for_Request); $i++) {
                
                    sendRequestToDriver($driver_for_Request[$i]['iDriverId'], "PASAKAY_REQUEST", json_encode($requestArray[$i]));
                    //echo "Driver : ".$driver_for_Request[$i]['iDriverId']." : Reg Id : ".$driver_for_Request[$i]['vFirebaseDeviceToken']." <br/>";
                    
                }
            }
            
            $messageArray['response'] =  1;
            $messageArray['serviceType'] = $servicetype;
            $messageArray['status'] = $bookingData[0]['eStatus'];
            
            echo json_encode($messageArray);
            
        }else{
            
            $messageArray['response'] =  0;
            $messageArray['serviceType'] = $servicetype;
            $messageArray['status'] = $bookingData[0]['eStatus'];
            
            echo json_encode($messageArray);
            
        }
        

    }
    
  // $servicetype = "LOAD_ACTIVE_TASK_PASKAY";
  
   
    if($servicetype == "LOAD_ACTIVE_TASK_PASKAY"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'56';
        $userType = isset($_POST['userType']) ? trim ($_POST['userType']) : 'User';
        $bookingNo =  isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'PAS200919349524';
        $deviceInfo = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) :'';
        
        
        // //PROFILE DATA
        // $sql = "UPDATE register_user SET tDeviceData = '".$deviceInfo."' WHERE iUserId = '". $userId."'";
        // $statement = $db->query($sql); 
        // $profileData = $statement ->fetchAll(); 
        
        
        $where['iUserId'] =  $userId;
        $updateDeviceInfo['tDeviceData'] = $deviceInfo;
        $result = myQuery("register_user",  $updateDeviceInfo, "update",  $where);
        
        //PROFILE DATA
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
        $statement = $db->query($sql); 
        $profileData = $statement ->fetchAll();  
     
     
        //BOOKING DATA
        $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
        $statement = $db->query($sql);
        $bookingData = $statement ->fetchAll(); 
        
        
        if($bookingData[0]['iDriverId'] != 0 && ($bookingData[0]['eStatus'] == "Assign" ||  $bookingData[0]['eStatus'] == "At the Pickup Point" || $bookingData[0]['eStatus'] == "In Transit" || $bookingData[0]['eStatus'] == "Arrived at the destination" || $bookingData[0]['eStatus'] == "Finished" ) && $bookingData[0]['eRatingFinished'] == "No"){
            
            $destinationLocationArr = array($bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude']);
            $sourceLocationArr = array($bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude']);
            
            $destinationLocationId = getLocationArea($destinationLocationArr);
            $suggestedToda = array();
            
            unset($where);
            
            $where['iDriverId'] = $bookingData[0]['iDriverId'];
            $fieldnames = array("iDriverId", "vName", "vLastName", "vPhone",  "vImage" , "vLatitude", "vLongitude", "vTodaLine", "vPlateNo", "vAvgRating", "iTodaId");
            $driverData = myQuery("register_driver", $fieldnames, "selectall",  $where);
            
            
            $sql = "SELECT * FROM register_toda WHERE iTodaId = '".$driverData[0]['iTodaId']."'";
            $statement = $db->query($sql);
            $todaData = $statement ->fetchAll(); 
            
            
            $todaId = $todaData [0]['iTodaId'];
            $todaName = $todaData [0]['vTodaName'];
            $todaRouteNo = $todaData [0]['vTodaRouteNo'];
            $baseFare = (float) $bookingData[0]['iBaseFare'];
            $farePricePerKm = (float)  $bookingData[0]['fPricePerKM'];
            $farePricePerMin = (float)$bookingData[0]['fPricePerMin'];
            $radiusDistance = (int) $todaData [0]['fRadius'];
            
            $distance = ceil(distance($bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude'], $bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude'], "K"));
            $estimatedDuration  = cal_time( $distance, 10);
            
            
            if($distance < 20){
                
                $timeDuration = '+'.$estimatedDuration.' seconds';
        
                $startTime = @date("Y-m-d H:i:s");
                
                $convertedTime = date('Y-m-d H:i:s',strtotime($startTime)+$timeDuration);
                
                $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
                
                $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
                
                $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
                
        
                //$TotalFarePerKM = $distance * constants::RATE_PER_KM;
                
                $generatedFare =  $baseFare + $TotalFarePerKM;
               
                
                
                $waitingTime = $bookingData[0]['vWaitingTime'];
       
                $WaitingTime_in_minutes = ((int) $waitingTime % 3600) / 60;
                
                $messageArray['WaitingTime_in_minutes'] =  intval($WaitingTime_in_minutes);
                
                $WaitingTime_in_seconds = ((int) $waitingTime % 60);
                
                $messageArray['WaitingTime_in_seconds'] = $WaitingTime_in_seconds;
                
                if($WaitingTime_in_seconds >= 1){
                    
                    $final_WaitingTime_in_minutes = intval($WaitingTime_in_minutes)+1;
                    
                }else{
                    $final_WaitingTime_in_minutes = $WaitingTime_in_minutes;
                }
                
                $totalWaitingFee =   $final_WaitingTime_in_minutes * $farePricePerMin;
                
                if($WaitingTime_in_minutes > 1){
                
                    if(intval($WaitingTime_in_minutes) == 1){
                        
                        if(intval(  $WaitingTime_in_seconds) == 0){
                             $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min ";
                        }else if(intval(  $WaitingTime_in_seconds) == 1){
                             $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min, ".intval(  $WaitingTime_in_seconds)." second";
                        }else{
                             $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min, ".intval(  $WaitingTime_in_seconds)." seconds";
                        }
                       
                    }else{
                         if(intval(  $WaitingTime_in_seconds) == 0){
                             $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins ";
                        }else if(intval(  $WaitingTime_in_seconds) == 1){
                             $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins, ".intval(  $WaitingTime_in_seconds)." second";
                        }else{
                             $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins, ".intval(  $WaitingTime_in_seconds)." seconds";
                        }
                    }
                    
                    // $messageArray['waiting_time'] = intval($WaitingTime_in_minutes) == 1 ? intval($WaitingTime_in_minutes)." min ".intval(  $WaitingTime_in_seconds)." seconds" : intval($WaitingTime_in_minutes)." mins ".intval(  $WaitingTime_in_seconds)." seconds";
                     
                }else{
                    
                
                     $messageArray['waiting_time'] = intval(  $WaitingTime_in_seconds)." second";
                }
                
                
                
                $totalFareAmount =  (float) $bookingData[0]['fTripGenerateFare'] +  $totalWaitingFee ;
                
                
                
                
                $driverProfile = array();
                foreach ( $driverData as $value ){
                    $driverProfile = $driverData[0];
                } 
                
                $generatedFare = (float)$bookingData[0]['fTripGenerateFare'];
                                
            
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['notificationCounter'] = countNotifications($userId, "User");
                $messageArray['status'] = "Okay";
                $messageArray['baseFare'] = $baseFare;
                $messageArray['additionalFare'] = $bookingData[0]['fAdditionalFare'];
                // $messageArray['estimatedTime'] = $newEstimatedTime." - ". $newConvertedTimeAllowance ;
                $messageArray['estimatedTime'] = $newEstimatedTime ;
                $messageArray['estimatedFare'] =  roundOff($generatedFare);
                $messageArray['distance'] =  roundOff($distance);
                // $messageArray['distance'] =  $driverData  vAvgRating;
              
                $messageArray['origin'] =  $bookingData[0]['vSourceAddress'];
                $messageArray['originLat'] = $bookingData[0]['vSourceLatitude'];
                $messageArray['originLong'] = $bookingData[0]['vSourceLongitude'];
                $messageArray['destination'] =  $bookingData[0]['tDestAddress'];
                $messageArray['destinationLat'] = $bookingData[0]['vDestLatitude'];
                $messageArray['destinationLong'] =  $bookingData[0]['vDestLongitude'];
                $messageArray['driverData'] =  $driverData;
                $messageArray['bookingDate'] = $bookingData[0]['dBooking_date'];
                $messageArray['bookingId'] = $bookingData[0]['iCabBookingId'];
                $messageArray['bookingNo'] = $bookingData[0]['vBookingNo'];
                $messageArray['bookingTotalAmount'] = $bookingData[0]['fTripTotalAmountFare'];
                $messageArray['bookingWaitingStatus'] = $bookingData[0]['eWaitingStatus'];
                $messageArray['bookingWaitingFeePerMinute'] = 2;
                $messageArray['bookingWaitingFee'] =  $totalWaitingFee;
                $messageArray['bookingStatus'] = $bookingData[0]['eStatus'];
                
                
                if($deviceInfo != $profileData[0]['tDeviceData']){
                
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['userType'] = $userType;
                    $messageArray['message'] = "AUTO_LOGOUT";
                    $messageArray['deviceInfo'] = $deviceInfo;
                  
                }
                
                echo json_encode( $messageArray);
                
                
            }else{
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Out of Service Area";
                
                echo json_encode( $messageArray);
                
        
            }
        

        }else{
            
            unset($where);
            $where['iUserId'] = $userId;
            $updateUserStatus['iCabBookingId']  = 0;
            $updateUserStatus['iTripId']  = 0;
            $updateUserStatus['vTripStatus'] = "NONE";
            $res = myQuery("register_user", $updateUserStatus, "update", $where);
            
            if($bookingData[0]['eStatus'] == "Cancelled" ){
               
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Okay";
                $messageArray['eCancelBy'] = $bookingData[0]['eCancelBy'];
                $messageArray['message'] = "Cancelled";
               
                
                echo json_encode( $messageArray);
            }else{
                
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Okay";
                $messageArray['message'] = "Driver Not Assigned";
               
                
                
                echo json_encode( $messageArray);
                
                
            }
            
           
          
            
        }
        

    }
    

    
    // if($servicetype == "LOAD_ACTIVE_TASK_PASKAY"){
        
    //     unset($messageArray);
    //     unset($where);
        
    //     $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
    //     $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
    //     $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'56';
    //     $userType = isset($_POST['userType']) ? trim ($_POST['userType']) : 'User';
    //     $bookingNo =  isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'PAS200919349524';
     
    //     $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
        
    //     $statement = $db->query($sql);
        
    //     $bookingData = $statement ->fetchAll(); 
        
        
    //     if( $bookingData[0]['iDriverId'] != 0 && $bookingData[0]['eStatus'] == "Assign" || $bookingData[0]['eStatus'] == "At the Pickup Point" || $bookingData[0]['eStatus'] == "In Transit"){
            
    //         // $destinationLocationArr = array($bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude']);
    //         // $destinationLocationId = getLocationArea($destinationLocationArr);
        
    //         // $suggestedToda = array();
            
    //         // $sql = "SELECT * FROM register_toda WHERE iLocationId = '".$destinationLocationId."'";
    //         // $statement = $db->query($sql);
    //         // $todaData = $statement ->fetchAll(); 
            
            
    //         // $estimatedDuration = get_Duration($bookingData[0]['vSourceAddress'],  $bookingData[0]['tDestAddress'], "s");
    //         // $distance = get_Distance($bookingData[0]['vSourceAddress'],  $bookingData[0]['tDestAddress'], "km");
            
    //         // if($estimatedDuration == null || $distance == null){
               
                 
    //         //      $address1 = get_Address2($bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude']);
    //         //      $address2 = get_Address2($bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude']);
    //         //      $estimatedDuration = get_Duration( $address1,  $address2 , "s");
    //         //      $distance = get_Distance($address1,  $address2 , "km");
    //         // }
            
    //         // if($estimatedDuration == null || $distance == null || $estimatedDuration == "") {
              
    //         //     $distance = ceil(distance($bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude'], $bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude'], "K"));
    //         //     $estimatedDuration  = cal_time( $distance, 10);

    //         // }
            
            
    //         $distance = ceil(distance($bookingData[0]['vSourceLatitude'], $bookingData[0]['vSourceLongitude'], $bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude'], "K"));
    //         $estimatedDuration  = cal_time( $distance, 10);
            
            
    //         if($distance < 20){
                
    //             $timeDuration = '+'.$estimatedDuration.' seconds';
        
    //             $startTime = @date("Y-m-d H:i:s");
                
    //             $convertedTime = date('Y-m-d H:i:s',strtotime($startTime)+$timeDuration);
                
    //             $convertedTimeAllowance = date('Y-m-d H:i:s',strtotime('+15 minutes',strtotime( $convertedTime )));
                
    //             $newEstimatedTime =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTime)->format("g:i A");
                
    //             $newConvertedTimeAllowance =  $dateNew = date_create_from_format("Y-m-d H:i:s", $convertedTimeAllowance)->format("g:i A");
                
        
    //             //$TotalFarePerKM = $distance * constants::RATE_PER_KM;
                
    //             $generatedFare =  constants::FLAT_RATE_PASAKAY + $TotalFarePerKM;
                
    //             unset($where);
                
    //             $where['iDriverId'] = $bookingData[0]['iDriverId'];
                
    //             $fieldnames = array("iDriverId", "vName", "vLastName", "vPhone",  "vImage" , "vLatitude", "vLongitude", "vTodaLine", "vPlateNo", "vAvgRating");
                
    //             $driverData = myQuery("register_driver", $fieldnames, "selectall",  $where);
                
                
    //             $waitingTime = $bookingData[0]['vWaitingTime'];
       
    //             $WaitingTime_in_minutes = ((int) $waitingTime % 3600) / 60;
                
    //             $messageArray['WaitingTime_in_minutes'] =  intval($WaitingTime_in_minutes);
                
    //             $WaitingTime_in_seconds = ((int) $waitingTime % 60);
                
    //             $messageArray['WaitingTime_in_seconds'] = $WaitingTime_in_seconds;
                
    //             if($WaitingTime_in_seconds >= 1){
                    
    //                 $final_WaitingTime_in_minutes = intval($WaitingTime_in_minutes)+1;
                    
    //             }
                
    //             $totalWaitingFee =   $final_WaitingTime_in_minutes * constants::WAITINGTIME_RATE_PER_MIN;
                
    //             if($WaitingTime_in_minutes > 1){
                
    //                 if(intval($WaitingTime_in_minutes) == 1){
                        
    //                     if(intval(  $WaitingTime_in_seconds) == 0){
    //                          $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min ";
    //                     }else if(intval(  $WaitingTime_in_seconds) == 1){
    //                          $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min, ".intval(  $WaitingTime_in_seconds)." second";
    //                     }else{
    //                          $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min, ".intval(  $WaitingTime_in_seconds)." seconds";
    //                     }
                       
    //                 }else{
    //                      if(intval(  $WaitingTime_in_seconds) == 0){
    //                          $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins ";
    //                     }else if(intval(  $WaitingTime_in_seconds) == 1){
    //                          $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins, ".intval(  $WaitingTime_in_seconds)." second";
    //                     }else{
    //                          $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins, ".intval(  $WaitingTime_in_seconds)." seconds";
    //                     }
    //                 }
                    
    //                 // $messageArray['waiting_time'] = intval($WaitingTime_in_minutes) == 1 ? intval($WaitingTime_in_minutes)." min ".intval(  $WaitingTime_in_seconds)." seconds" : intval($WaitingTime_in_minutes)." mins ".intval(  $WaitingTime_in_seconds)." seconds";
                     
    //             }else{
                    
                
    //                  $messageArray['waiting_time'] = intval(  $WaitingTime_in_seconds)." second";
    //             }
                
                
                
    //             $totalFareAmount =  (float) $bookingData[0]['fTripGenerateFare'] +  $totalWaitingFee ;
                
                
                
                
    //             $driverProfile = array();
    //             foreach ( $driverData as $value ){
    //                 $driverProfile = $driverData[0];
    //             } 
                
    //             $generatedFare = (float)$bookingData[0]['fTripGenerateFare'];
                                
            
    //             $messageArray['response'] = 1;
    //             $messageArray['service'] = $servicetype;
    //             $messageArray['notificationCounter'] = countNotifications($userId, "User");
    //             $messageArray['status'] = "Okay";
    //             $messageArray['baseFare'] = constants::FLAT_RATE_PASAKAY;
    //             $messageArray['additionalFare'] = $bookingData[0]['fAdditionalFare'];
    //             // $messageArray['estimatedTime'] = $newEstimatedTime." - ". $newConvertedTimeAllowance ;
    //             $messageArray['estimatedTime'] = $newEstimatedTime ;
    //             $messageArray['estimatedFare'] =  roundOff($generatedFare);
    //             $messageArray['distance'] =  roundOff($distance);
    //             // $messageArray['distance'] =  $driverData  vAvgRating;
              
    //             $messageArray['origin'] =  $bookingData[0]['vSourceAddress'];
    //             $messageArray['originLat'] = $bookingData[0]['vSourceLatitude'];
    //             $messageArray['originLong'] = $bookingData[0]['vSourceLongitude'];
    //             $messageArray['destination'] =  $bookingData[0]['tDestAddress'];
    //             $messageArray['destinationLat'] = $bookingData[0]['vDestLatitude'];
    //             $messageArray['destinationLong'] =  $bookingData[0]['vDestLongitude'];
    //             $messageArray['driverData'] =  $driverData;
    //             $messageArray['bookingDate'] = $bookingData[0]['dBooking_date'];
    //             $messageArray['bookingId'] = $bookingData[0]['iCabBookingId'];
    //             $messageArray['bookingNo'] = $bookingData[0]['vBookingNo'];
    //             $messageArray['bookingTotalAmount'] = $bookingData[0]['fTripTotalAmountFare'];
    //             $messageArray['bookingWaitingFeePerMinute'] = 2;
    //             $messageArray['bookingWaitingFee'] =  $totalWaitingFee;
                
    //             echo json_encode( $messageArray);
                
                
    //         }else{
    //             $messageArray['response'] = 0;
    //             $messageArray['service'] = $servicetype;
    //             $messageArray['status'] = "Out of Service Area";
                
    //             echo json_encode( $messageArray);
                
        
    //         }
        

    //     }else{
            
    //         if($bookingData[0]['eStatus'] == "Cancelled" ){
                
    //             $messageArray['response'] = 0;
    //             $messageArray['service'] = $servicetype;
    //             $messageArray['status'] = "Okay";
    //             $messageArray['message'] = "Cancelled";
    //             echo json_encode( $messageArray);
    //         }else{
    //             $messageArray['response'] = 0;
    //             $messageArray['service'] = $servicetype;
    //             $messageArray['status'] = "Okay";
    //             $messageArray['message'] = "Driver Not Assigned";
    //             echo json_encode( $messageArray);
    //         }
            
           
          
            
    //     }
        

    // }
    
 
    
    
    if($servicetype == "CANCELLED_BOOKING"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'32';
        $userType = isset($_POST['userType']) ? trim ($_POST['userType']) : 'Passenger';
        $bookingNo =  isset($_POST['bookingNo']) ? trim($_POST['bookingNo']) :'PAS200722458909';
     
        
        if($userType == "User"){
            
            $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '".$bookingNo."'";
            $statement = $db->query($sql);
            $bookingDataStatus = $statement ->fetchAll(); 

            if($bookingDataStatus[0]['eStatus'] == "Searching for drivers" || $bookingDataStatus[0]['eStatus'] == "Re-assigning for drivers" ){

                 unset($where);
                $where['vBookingNo'] = $bookingNo;
                $booking_status['eCancelBy'] = "User";
                $booking_status['iCancelByUserId'] = $userId;
                $booking_status['dCancelDate'] = @date("Y-m-d H:i:s");
                $booking_status['tTripEnded'] = @date("Y-m-d H:i:s");
                $booking_status['eStatus'] = "Cancelled";
                // $booking_status['eViewNotif'] = 1;
                
                $result = myQuery("cab_booking", $booking_status, "update", $where);
                
                 
                unset($where);
                $where['iUserId'] = $userId;
                $user_status['vTripStatus'] = "NONE";
                $user_status['iTripId'] = 0;
                $result3 = myQuery("register_user", $user_status, "update", $where);
                
                $messageArray['response'] = 1;
               // $messageArray['notificationCounter'] = countNotifications($userId, $userType);
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "Okay";
            
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "Already Assiged";
            }
            
            echo json_encode( $messageArray);
           
            
        }else{
            $where['vBookingNo'] = $bookingNo;
            $booking_status['eCancelledBy'] = "Driver";
            $booking_status['iCancelByUserId'] = $userId;
            $booking_status['dCancelDate'] = @date("Y-m-d H:i:s");
            $booking_status['eStatus'] = "Cancelled";
            
            $result = myQuery("cab_booking", $booking_status, "update", $where);
            
            //notify("user", $userId, "ActiveBookingActivity", "Cancelled");
            
            
            $messageArray['response'] = 1;
            $messageArray['notificationCounter'] = countNotifications($userId, $userType);
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "Okay";
        }
        
        
       

    
        

    }
    
    // $servicetype = "LOAD_PASAKAY_ACTIVITIES";
    
    if($servicetype == "LOAD_PASAKAY_ACTIVITIES"){
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '55';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung'; 
        
        $sql = "SELECT * FROM cab_booking WHERE iUserId =  $userId ORDER BY dBooking_date DESC LIMIT 50";

        $statement = $db->query($sql);
        
        $result = $statement ->fetchAll(); 
        
        $bookingData = array();
        
        for($i = 0; $i < count($result); $i++) {
            
            
            if($result[$i]['eStatus'] != "Completed" || $result[$i]['eStatus'] != "Finished"){
                
                 $bookingData [$i]['bookingPrice'] = $result[$i]['fTripTotalAmountFare'];
            }else{
                $bookingData [$i]['bookingPrice'] = $result[$i]['fTripTotalAmountFare'];
            }
                
       
            $bookingData [$i]['bookingStatus'] = $result[$i]['eStatus'];
            $bookingData [$i]['paymentMethod'] = $result[$i]['ePayType'];
            $bookingData [$i]['bookingId'] = $result[$i]['iCabBookingId'];
            $bookingData [$i]['bookingNo'] =  $result[$i]['vBookingNo'];
            $bookingData [$i]['originAddress'] =  $result[$i]['vSourceAddress'];
            $bookingData [$i]['destinationAddress'] =  $result[$i]['tDestAddress'];
            $bookingData [$i]['originAddressLat'] =  $result[$i]['vSourceLatitude'];
            $bookingData [$i]['originAddressLong'] =  $result[$i]['vSourceLongitude'];
            $bookingData [$i]['destinationAddressLat'] =  $result[$i]['vDestLatitude'];
            $bookingData [$i]['destinationAddressLong'] =  $result[$i]['vDestLongitude'];
            
            $date = date_create($result[$i]['dBooking_date']);

            $bookingData [$i]['bookingDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            
         
         
        }
        
        $messageArray['response'] = 1;
        $messageArray['notificationCounter'] = countNotifications($userId, "User");
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['result'] =  $bookingData;
        $messageArray['total'] = count($result);
         
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
        
        
        if($deviceInfo != $profileData[0]['tDeviceData']){
                
                unset($messageArray);
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['userType'] = $userType;
                $messageArray['error'] = "AUTO_LOGOUT";
                $messageArray['deviceInfo'] = $deviceInfo;
              
        }
        
        echo json_encode($messageArray);
         
         
    }
    
    //$servicetype = "SEND_VERIFICATION_CODE";
  

    if($servicetype == "SEND_VERIFICATION_CODE"){
        
        unset($messageArray);
        unset($where);
        
        $mobileNumber = isset($_POST['mobileNumber']) ? trim($_POST['mobileNumber']) : '09465450045';
        $userType = isset($_POST['userType']) ? trim ($_POST['userType']) : 'Driver';
        $mode = isset($_POST['mode']) ? trim ($_POST['mode']) : 'Driver';
       
        
        $result =  checkMobileNumber( $userType, $mobileNumber);
        $code = mt_rand(100000, 999999);
        if( $result == 0){
            
            if($mode == "reverse"){
                
                
                $SMSmessage = "<Trikaroo> Use $code as your verification, valid for 15 minutes";
            
                $where['iUserId'] = $userId;
                $verify_status['ePhoneVerified'] = "No";
        
                $result = myQuery("register_user",  $verify_status, "update", $where);

                $number = $mobileNumber;
                $message = $SMSmessage;
                $apicode = "DE-HENGY005538_VF6II";
                $passwd = "{(k%gygg#{";
                $result = itexmo($number,$message,$apicode,$passwd);
                
                //sendVerifivationCode($SMSclient, number_PH( $mobileNumber), $SMSmessage);
                // $account_sid = constants::Account_SID;
                // $auth_token = constants::Auth_Token;
                // $twilioMobileNum = constants::TwilioMobileNum;
                // $client = new Services_Twilio($account_sid, $auth_token);
            
                // $sms = $client->account->messages->sendMessage($twilioMobileNum,number_PH($mobileNumber),$SMSmessage);
                
                
                $messageArray['response'] = 0;
                $messageArray['service'] =  $mobileNumber;
                $messageArray['status'] =  "Okay";
                $messageArray['code'] =  $code;
                $messageArray['dateTime'] = @date("Y-m-d H:i:s");
                
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "FAILED";
                $messageArray['message'] = "Mobile number Already exist";
                $messageArray['code'] =  $code;
                $messageArray['dateTime'] = @date("Y-m-d H:i:s");
            }
            
 
            
    
            
        }else{
            
            
             if($mode == "reverse"){
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "FAILED";
                $messageArray['message'] = "Mobile number Already exist";
                $messageArray['code'] =  $code;
                $messageArray['dateTime'] = @date("Y-m-d H:i:s");
                
            }else{
                
              
                $SMSmessage = "<Trikaroo> Use $code as your verification, valid for 15 minutes";
            
                $where['iUserId'] = $userId;
                $verify_status['ePhoneVerified'] = "No";
        
                $result = myQuery("register_user",  $verify_status, "update", $where);
                
                $number = $mobileNumber;
                $message = $SMSmessage;
                $apicode = "DE-HENGY005538_VF6II";
                $passwd = "{(k%gygg#{";
                $result = itexmo($mobileNumber,$message,$apicode,$passwd);
                
                // $account_sid = constants::Account_SID;
                // $auth_token = constants::Auth_Token;
                // $twilioMobileNum = constants::TwilioMobileNum;
            
                // $client = new Services_Twilio($account_sid, $auth_token);
                
                // //"+63".mobileNumber
            
                // $sms = $client->account->messages->sendMessage($twilioMobileNum,number_PH($mobileNumber),$SMSmessage);
                
                $messageArray['response'] = 1;
                $messageArray['service'] =  $mobileNumber;
                $messageArray['status'] =  "Okay";
                $messageArray['code'] =  $code;
                $messageArray['dateTime'] = @date("Y-m-d H:i:s");
              
              
            }
            
           
        }
         
        
        echo json_encode( $messageArray);
        
    }
    
    
   
   //$servicetype = "SIGNUP";
    
    if($servicetype == "SIGNUP"){
        
        unset($messageArray);
        unset($where);
        
        $mobileNUmber = isset($_POST['mobileNUmber']) ? trim($_POST['mobileNUmber']) : '09398296855';
        $userType = isset($_POST['userType']) ? trim ($_POST['userType']) : 'User';
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '14.0000';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'21.0000';
        $email = isset($_POST['email']) ? trim($_POST['email']) : 'haha@gmail.com';
        $name  = isset($_POST['name']) ? trim($_POST['name']) :'Sample Lang';
        $password  = isset($_POST['password']) ? trim($_POST['password']) :'111111111111111111';
        $birthday  = isset($_POST['birthday']) ? trim($_POST['birthday']) :'2020-12-12';
        $gender  = isset($_POST['gender']) ? trim($_POST['gender']) :'Male';
        $deviceInfo = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : '';
        $referralCode  = isset($_POST['referralCode']) ? trim($_POST['referralCode']) :'Z2UYPF';
        
        
        
        
        $refCode = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,6);
        
        $passwordmd5 = md5(constants::SALT.$password);
        
        $vEmailVerificationtoken = GenerateToken();
        
        
        $register_Data['vName'] = $name;
       
        $register_Data['vPhone']  = $mobileNUmber;
        
        $register_Data['vLatitude']  =  $sourceLat;
        
        $register_Data['vLongitude']  = $sourceLong;
        
        $register_Data['vEmail']  =  $email ;
        
        $register_Data['vPassword']  = $passwordmd5;
        
        $register_Data['dBirthDate']  = date('Y-m-d', strtotime($birthday)); 
        
        $register_Data['eGender']  = "Male";
        
        $address = get_CompleteAddress2($sourceLat, $sourceLong);
        
        $register_Data['vState'] = $address['state'];
        
        $register_Data['vCity'] = $address['city'];

        $register_Data['vRegion'] = $address['region'];
        
        $register_Data['vRefCode'] =  $refCode;
    
        $register_Data['vCountry'] = "PH";
        
        $register_Data['tDeviceData'] =  $deviceInfo;
        
        $register_Data['vEmailVerificationCode'] =  $vEmailVerificationtoken;
        
        $result = myQuery("register_user",   $register_Data, "insert");
        
        
        
        
        $sql = "SELECT * FROM register_user WHERE vPhone = '".$mobileNUmber."' AND vRefCode = '".$refCode."'";
       
        $statement = $db->query($sql); 
    
        $profileData = $statement ->fetchAll();  
        
        
        $result = checkReferralCode($referralCode);
        
        if($result['response'] == "true"){
            unset($where);
    
            $where['iUserId'] = $result['iUserId'];
            $referral_Sender['fRewardPointsBalance'] =  (float)$result['rewardBalance'] + (float) constants::DEFAULT_REFERRAL_POINTS_EARNED;
            $data = myQuery("register_user", $referral_Sender, "update",  $where);
          
            
            $notification['iUserId'] = $result['iUserId'];
            $notification['vUserType'] = $userType;
            $notification['vTitle'] = "Your Earn a Rewards!";
            $notification['vDescription'] = "You have earned ".constants::DEFAULT_REFERRAL_POINTS_EARNED.". Your friend ".$name." registers and completed ".(($gender == "Male") ? "his": "her")." first transaction on TriKaRoo";
            $notification['vType'] = "REWARD_POINTS";
            $notification['vImage'] = "";
            $notification['vUrl'] = "";
            $notification['vIntent'] = "";
            $notification['vSent'] = "";
            
            createNotification($notification);
            
            $transactionNo = GenerateUniqueOrderNo("RP");

            $rewardslogs['iUserId'] = $result['iUserId'];
            $rewardslogs['vUserType'] = "User";
            $rewardslogs['vTransactionType'] = "REFERRAL";
            $rewardslogs['vLabel'] = "Earned points";
            $rewardslogs['vDescription'] = "";
            $rewardslogs['vTransactionNo'] = $transactionNo;
            $rewardslogs['fPoints'] = (float) constants::DEFAULT_REFERRAL_POINTS_EARNED;
            $rewardslogs['fTotalPointsAmount'] = (float)$result['rewardBalance'] + (float) constants::DEFAULT_REFERRAL_POINTS_EARNED;
            $rewardslogs['iSenderId'] = (int) $profileData [0]['iUserId'];
            $rewardslogs['eStatus'] = "Earned";
            $rewardslogs['dDateCreated'] = @date("Y-m-d H:i:s");
                  
            $result = myQuery("rewards_user_logs", $rewardslogs, "insert");
           
        }
        
        

        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['userId'] = $profileData [0]['iUserId'];
        $messageArray['mobileNUmber'] =   $mobileNUmber;
        $messageArray['userType'] = $userType;
        $messageArray['$sourceLat'] = $sourceLat;
        $messageArray['sourceLong'] =  $sourceLong;
        $messageArray['email'] = $email;
        $messageArray['name'] = $name;
        $messageArray['password'] =  $passwordmd5;
        $messageArray['birthday'] = $birthday;
        $messageArray['gender'] = $gender;
        $messageArray['referralCode'] = $referralCode;
        $messageArray['profileData'] =  $profileData;
        
        
         //WELCOME NOTIFCATION
        $notification['iUserId'] = $profileData[0]['iUserId'];
        $notification['vUserType'] = $userType;
        $notification['vTitle'] = "Welcome Trika-Tropa!";
        $notification['vDescription'] = "Enjoy the Pasakay and Pabili services of the Trikaroo App from your Tricycle drivers in your community";
        $notification['vType'] = "ANNOUNCEMENT";
        $notification['vImage'] = "";
        $notification['vUrl'] = "";
        $notification['vIntent'] = "";
        $notification['vSent'] = "";
        
        createNotification($notification);
        
        // //SEND_EMAIL_VERIFICATION
       
        // sendVerificationEmail($email, $name, $vEmailVerificationtoken, $userType, $userData[0]['iUserId']);


        echo json_encode( $messageArray);
        
    }
    
    
    // $servicetype = "DRIVER_APPLICATION";
    
    if($servicetype == "DRIVER_APPLICATION"){
        
        unset($messageArray);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $password  = isset($_POST['password']) ? trim($_POST['password']) : '111';
        $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'Driver';
        $deviceInfo = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : '12121212';
        
        $name = isset($_POST['name']) ? trim($_POST['name']) : 'Heng We Yen';
        $email = isset($_POST['email']) ? trim($_POST['email']) : 'wewe';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '111';
        $mobileNo = isset($_POST['mobileNo']) ? trim($_POST['mobileNo']) : '111';
        $birthday = isset($_POST['birthday']) ? trim($_POST['birthday']) : '1212';
        $gender = isset($_POST['gender']) ? trim($_POST['gender']) : 'Male';
        $address = isset($_POST['address']) ? trim($_POST['address']) : 'diyan Lang';
        $profilePhoto = isset($_POST['profilePhoto']) ? trim($_POST['profilePhoto']) : 'hihihihi';
        $driversLicense = isset($_POST['DriversLicense']) ? trim($_POST['DriversLicense']) : 'sssss';
        $driversTodaId = isset($_POST['todaId']) ? trim($_POST['todaId']) : 'ddddddd';
        $driversNBIClearance = isset($_POST['NBIClearance']) ? trim($_POST['NBIClearance']) : 'ffffff';
        $todaName = isset($_POST['todaName']) ? trim($_POST['todaName']) : 'sssss';
        $plateNo = isset($_POST['plateNo']) ? trim($_POST['plateNo']) : '2121';
        $franchise = isset($_POST['franchise']) ? trim($_POST['franchise']) : 'sdsd';
        $officialReceipt = isset($_POST['officialReceipt']) ? trim($_POST['officialReceipt']) : 'sdsds';
        $certificateOfRegistration = isset($_POST['certificateOfRegistration']) ? trim($_POST['certificateOfRegistration']) : 'sdsdsd';
        $tricyclePhoto = isset($_POST['tricyclePhoto']) ? trim($_POST['tricyclePhoto']) : 'sdsdsd';
        
        
        $passwordmd5 = md5(constants::SALT.$password);
        
        $application['vName'] = $name;
        $application['vEmail'] = $email;
        $application['vPassword'] = md5(constants::SALT.$password);
        $application['dBirthDate'] = date('Y-m-d', strtotime($birthday));
        $application['vLatitude']  =  $sourceLat;
        $application['vLongitude']  = $sourceLong;
        $application['vPhone']  = $mobileNo;
        $application['eGender'] = $gender;
        $application['vAddress'] = $address;
        $application['vImage'] = $profilePhoto;
        $application['vPlateNo'] = $plateNo;
        $application['vTodaLine'] =  $todaName;
        $application['eStatus'] = 'inactive';
        $application['vLicense'] = $driversLicense;
        $application['vCerti'] = $certificateOfRegistration;
        $application['tRegistrationDate'] = @date("Y-m-d H:i:s");
        $application['ePhoneVerified'] = 'Yes';
        $application['eEmailVerified'] = 'No';
        $application['vOffReceipt'] =  $officialReceipt;
        $application['vFranchise'] = $franchise;
        $application['vTodaId'] =   $driversTodaId;
        $application['vCarType'] = "Tricycle";
        $application['vCarPhoto'] = $tricyclePhoto;
        $application['vTripStatus'] = "FINISHED";
        $application['vAvailability'] = "Available";
        
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                 .'0123456789'); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, 5) as $k) $rand .= $seed[$k];
        
        
        $application['vRefCode'] =  $rand ;
        
        $result = myQuery("register_driver",  $application, "insert");
        
      
        
    
         
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['sessionId'] = $sessionId;
        $messageArray['success'] = "SUCEESS APPLICATION";

    
       
        
        
       echo json_encode( $messageArray);
       
    }
    
    
      
    if($servicetype == "SAVE_PASSWORD"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $password  = isset($_POST['password']) ? trim($_POST['password']) :'1212';
        $oldpassword  = isset($_POST['oldpassword']) ? trim($_POST['oldpassword']) :'1212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1212';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'1212';
        $redirect =  isset($_POST['redirect']) ? trim($_POST['redirect']) :'1212';
        $mobile =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'1212';
       
        if( $redirect == "Forgot Password"){
            
            if( $userType == "User"){
                $passwordMd5 = md5(constants::SALT.$password);
                $where['vPhone'] = $mobile;
                $update['vPassword'] = $passwordMd5;
                $result = myQuery("register_user",  $update, "update", $where);
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Okay";
                $messageArray['userId'] =  $userId;
                $messageArray['password'] =  $password;
                $messageArray['mobile'] =$mobile;
                
                
            }else if( $userType == "Driver"){
                
                $passwordMd5 = md5(constants::SALT.$password);
                $where['vPhone'] = $mobile;
                $update['vPassword'] =  $passwordMd5;
                $result = myQuery("register_driver",  $update, "update", $where);
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Okay";
                $messageArray['userId'] =  $userId;
                $messageArray['password'] =  $password;
                $messageArray['mobile'] =$mobile;
                
            }
            
        }else{
            
            unset($where);
           
            
            if( $userType == "User"){
                
                
                 
                $oldpasswordMd5 = md5(constants::SALT.$oldpassword);
                $where['iUserId'] = $userId;
                $where['vPassword'] =  $oldpasswordMd5;
                $result = myQuery("register_user", array('vPassword'), "selectall", $where);
                
                if(count($result) > 0){
                    
                    if(  $password == $oldpassword){
                        
                        
                        $messageArray['response'] = 0;
                        $messageArray['service'] = $servicetype;
                        $messageArray['status'] = "Same password";
                        $messageArray['userId'] =  $userId;
                        $messageArray['password'] = $password;
                        $messageArray['mobile'] =$mobile;
                        
                        
                    }else{
                        
                        unset($where);
                        $passwordMd5 = md5(constants::SALT.$password);
                        $where['iUserId'] = $userId;
                        $update['vPassword'] = $passwordMd5;
                        $result = myQuery("register_user",  $update, "update", $where);
                        
                        $messageArray['response'] = 1;
                        $messageArray['service'] = $servicetype;
                        $messageArray['status'] = "Okay";
                        $messageArray['userId'] =  $userId;
                        $messageArray['oldpassword'] = $oldpassword;
                        $messageArray['password'] =  $password;
                        $messageArray['mobile'] =$mobile;
                    }
                    
                    
                    
                    
                }else{
                     
                     
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Failed";
                    $messageArray['userId'] =  $userId;
                    $messageArray['password'] = $password;
                    $messageArray['mobile'] =$mobile;
                    
                }
            
            
                
            }else if( $userType == "Driver"){
                
                unset($where);
                $oldpasswordMd5 = md5(constants::SALT.$oldpassword);
                $where['iDriverId'] = $userId;
                $where['vPassword'] =  $oldpasswordMd5;
                $result = myQuery("register_driver", array('vPassword'), "selectall", $where);
                
                if(count($result) > 0){
                
                   if(  $password == $oldpassword){
                        
                        
                        $messageArray['response'] = 0;
                        $messageArray['service'] = $servicetype;
                        $messageArray['status'] = "Same password";
                        $messageArray['userId'] =  $userId;
                        $messageArray['password'] = $password;
                        $messageArray['mobile'] =$mobile;
                        
                        
                    }else{
                        
                    unset($where);
                    $passwordMd5 = md5(constants::SALT.$password);
                    $where['iDriverId'] = $userId;
                    $update['vPassword'] = $passwordMd5;
                    $result = myQuery("register_driver",  $update, "update", $where);
                    
                    $messageArray['response'] = 1;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Okay";
                    $messageArray['userId'] =  $userId;
                    $messageArray['oldpassword'] = $oldpassword;
                    $messageArray['password'] =  $password;
                    $messageArray['mobile'] =$mobile;
                    
                    }
                  
                }else{
                     
                     
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Failed";
                    $messageArray['userId'] =  $userId;
                    $messageArray['password'] = $password;
                    $messageArray['mobile'] =$mobile;
                    
                }
                
            }else if( $userType == "Store"){
                
                unset($where);
                $oldpasswordMd5 = md5(constants::SALT.$oldpassword);
                $where['iSellerId'] = $userId;
                $where['vPassword'] =  $oldpasswordMd5;
                $result = myQuery("register_seller", array('vPassword'), "selectall", $where);
                
                if(count($result) > 0){
                
                   if(  $password == $oldpassword){
                        
                        
                        $messageArray['response'] = 0;
                        $messageArray['service'] = $servicetype;
                        $messageArray['status'] = "Same password";
                        $messageArray['userId'] =  $userId;
                        $messageArray['password'] = $password;
                        $messageArray['mobile'] =$mobile;
                        
                        
                    }else{
                        
                    unset($where);
                    $passwordMd5 = md5(constants::SALT.$password);
                    $where['iSellerId'] = $userId;
                    $update['vPassword'] = $passwordMd5;
                    $result = myQuery("register_seller",  $update, "update", $where);
                    
                    $messageArray['response'] = 1;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Okay";
                    $messageArray['userId'] =  $userId;
                    $messageArray['oldpassword'] = $oldpassword;
                    $messageArray['password'] =  $password;
                    $messageArray['mobile'] =$mobile;
                    
                    }
                  
                }else{
                     
                     
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Failed";
                    $messageArray['userId'] =  $userId;
                    $messageArray['password'] = $password;
                    $messageArray['mobile'] =$mobile;
                    
                }
                
            }
        }
        
  
        
       
        

        echo json_encode( $messageArray);
        
    }
    
    
     
    if($servicetype == "SAVE_PROFILE"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $password  = isset($_POST['password']) ? trim($_POST['password']) :'1212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1212';
        $name =  isset($_POST['name']) ? trim($_POST['name']) :'1212';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'1212';
        $mobile =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'1212';
        $gender =  isset($_POST['gender']) ? trim($_POST['gender']) :'1212';
        $birthday =  isset($_POST['birthday']) ? trim($_POST['birthday']) :'1212';
        
        $where['iUserId'] = trim($userId);
        $update['vName'] = trim($name);
        $update['vPhone'] = trim($mobile);
        $update['vEmail'] = trim($email);
        $update['dBirthDate'] = date('Y-m-d', strtotime(trim($birthday)));
        $update['eGender'] = trim($gender);
        $result = myQuery("register_user",  $update, "update", $where);
        
        
              
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId ."'";
           
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['profileData'] =  $profileData ;
     

        echo json_encode( $messageArray);
        
    }
    
    //$servicetype = "SAVE_DRIVER_PROFILE";
    
      
    if($servicetype == "SAVE_DRIVER_PROFILE"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $password  = isset($_POST['password']) ? trim($_POST['password']) :'1122';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'17';
        $name =  isset($_POST['name']) ? trim($_POST['name']) :'1212';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'1212';
        $mobile =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'1212';
        $plateNo =  isset($_POST['plateNo']) ? trim($_POST['plateNo']) :'1212';
        $todaLine =  isset($_POST['todaLine ']) ? trim($_POST['todaLine']) :'1212';
        
        $where['iDriverId'] = trim($userId);
        $update['vName'] = trim($name);
        $update['vPhone'] = trim($mobile);
        $update['vEmail'] = trim($email);
        $update['vPlateNo'] = trim($plateNo);
        $update['vTodaLine'] = trim($todaLine);
        $result = myQuery("register_driver",  $update, "update", $where);
        
        
              
        $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $userId ."'";
           
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['profileData'] =  $profileData ;
     

        echo json_encode( $messageArray);
        
    }
    
    
    if($servicetype == "SAVE_STORE_PROFILE"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $password  = isset($_POST['password']) ? trim($_POST['password']) :'1122';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'17';
        $managerId =  isset($_POST['managerId']) ? trim($_POST['managerId']) :'17';
        $name =  isset($_POST['name']) ? trim($_POST['name']) :'1212';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'1212';
        $mobile =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'1212';
        $address = isset($_POST['address']) ? trim($_POST['address']) :'1212';
        $contactperson = isset($_POST['contactperson']) ? trim($_POST['contactperson']) :'1212';
        
        unset($where);
        unset($update);
        $where['iCompanyId'] = trim($userId);
        $update['vCompany'] = trim($name);
        $update['vPhone'] = trim($mobile);
        $update['vEmail'] = trim($email);
        $update['vCaddress'] = $address;
      
        $result = myQuery("company",  $update, "update", $where);
        
        unset($where);
        unset($update);
        $where['iSellerId'] = trim($managerId);
        $update['vName'] = trim($contactperson);
      
        $result = myQuery("register_seller",  $update, "update", $where);
        
        $sql = "SELECT * FROM company WHERE iCompanyId = '". $userId ."'";
        $statement = $db->query($sql); 
        $companyData = $statement ->fetchAll();  
        
        $sql = "SELECT * FROM register_seller WHERE iSellerId = '". $managerId ."'";
        $statement = $db->query($sql); 
        $managerData = $statement ->fetchAll();  
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['companyData'] = $companyData ;
        $messageArray['managerData'] = $managerData ;
     

        echo json_encode( $messageArray);
        
    }
    
   // $servicetype = "LOAD_SAVED_ADDRESS";
    
    if($servicetype == "LOAD_SAVED_ADDRESS"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'56';
        
        $sql = "SELECT register_user.vName, register_user.vPhone, user_address.* FROM register_user LEFT JOIN user_address ON user_address.iUserId = register_user.iUserId WHERE user_address.iUserId = '". $userId."'";

        $statement = $db->query($sql);
        
        $result = $statement ->fetchAll(); 
        
        
        if(count($result) > 0){
              
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";
            $messageArray['result'] = $result;
        }else{
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Failed";
            $messageArray['userId'] =   $userId;
          
            $messageArray['result'] = $result;
        }
      

        echo json_encode( $messageArray);
        
    }
    
    if($servicetype == "SAVED_NEW_ADDRESS"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $address  = isset($_POST['address']) ? trim($_POST['address']) :'212';
        $userType  = isset($_POST['userType']) ? trim($_POST['userType']) :'212';
        $buildingNo  = isset($_POST['buildingNo']) ? trim($_POST['buildingNo']) :'212';
        $landmark  = isset($_POST['landmark']) ? trim($_POST['landmark']) :'212';
        $addressType  = isset($_POST['addressType']) ? trim($_POST['addressType']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1212';
        
        $address_data['iUserId'] =  $userId;
       
        $address_data['eUserType']  = $userType;
        
        $address_data['vLatitude']  =  $sourceLat;
        
        $address_data['vLongitude']  = $sourceLong;
        
        $address_data['vBuildingNo']  =  $buildingNo;
        
        $address_data['vLamdamark']  =  $landmark;
        
        $address_data['vAddresstype']  = $addressType; 
        
        $address_data['dAddedDate']  = @date("Y-m-d H:i:s");
        
        $result = myQuery("user_address",  $address_data, "insert");
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['result'] = $result;
        

        echo json_encode( $messageArray);
        
    }
   
    
    if($servicetype == "DELETE_SAVED_ADDRESS"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $addressId  = isset($_POST['addressId']) ? trim($_POST['addressId']) :'2';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1212';
        
        $sql = "DELETE FROM user_address WHERE iUserAddressId = $addressId";

        $statement = $db->query($sql);
        
        $result = $statement ->execute(); 
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['result'] = $addressId;
        

        echo json_encode( $messageArray);
        
    }
    
    
    //$servicetype = "LOAD_RECENT_WALLET_TRANSACTIONS";
    

    if($servicetype == "LOAD_RECENT_WALLET_TRANSACTIONS"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'52';
        $userType = isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        
        if( $userType == "User"){
            
            $sql = "SELECT * FROM user_wallet_logs WHERE iUserId = ".$userId." ORDER BY dDate DESC LIMIT 5";

            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";
            $messageArray['result'] =  $result;
            
    
            echo json_encode( $messageArray);
                
        }else {
            
            
            
        }
        
      
        
    }

    //$servicetype = "SEND_MONEY";
    
    
     if($servicetype == "SEND_MONEY"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'17';
        $userType  = isset($_POST['userType']) ? trim($_POST['userType']) : 'Driver';
        $mobilnumber = isset($_POST['mobilnumber']) ? trim($_POST['mobilnumber']) : '09398296855';
        $recipientName = isset($_POST['recipientName']) ? trim($_POST['recipientName']) : '32';
        $amount =  isset($_POST['amount']) ? trim($_POST['amount']) : '200'; 
        
        
        $sql = "SELECT vPhone, vWalletBalance FROM register_driver WHERE iDriverId = $userId";

        $statement = $db->query($sql);
        
        $senderData = $statement ->fetchAll();

        
        $sql = "SELECT iDriverId, vPhone, vWalletBalance FROM register_driver WHERE vPhone = $mobilnumber";

        $statement = $db->query($sql);
        
        $receiverData = $statement ->fetchAll(); 


        
        if( $senderData[0]['vPhone'] != $mobilnumber){
            if(count($receiverData) > 0 ) {

                $senderWalletBalamce = (float) $senderData[0]['vWalletBalance'];
                $receiverWalletBalamce = (float) $receiverData[0]['vWalletBalance'];
    
                // echo "Sender : ". $senderWalletBalamce."</br>";
                // echo "Receiver : ". $receiverWalletBalamce."</br>";

                $newSenderWalletBalance = (float)$senderWalletBalamce - (float) $amount;
                $newReceiverWalletBalamce =  (float)$receiverWalletBalamce  + (float) $amount;

                if($newSenderWalletBalance > 0){

                    unset($where);
                    $where['iDriverId'] = $userId;
                    $where['vPhone'] = $senderData[0]['vPhone'];
                    $updateWallet['vWalletBalance'] =  $newSenderWalletBalance;
                    $result = myQuery("register_driver", $updateWallet, "update", $where);
                    
                    $transactionNo = GenerateUniqueOrderNo("SM");

                    $walletlogs['iDriverId'] = $userId ;
                    $walletlogs['vUserType'] =  $userType;
                    $walletlogs['vTransactionType'] = "SEND";
                    $walletlogs['vLabel'] = "Send Trikoins";
                    $walletlogs['vDescription'] = "";
                    $walletlogs['vTransactionNo'] = $transactionNo;
                    $walletlogs['fAmount'] = (float) $amount;
                    $walletlogs['fWalletBalance'] = (float) $newSenderWalletBalance;
                    $walletlogs['vReceiveBy'] = "";
                    $walletlogs['iReceiveId'] = $receiverData[0]['iDriverId'];
                    $walletlogs['eStatus'] = "Completed";
                    $walletlogs['dDate'] = @date("Y-m-d H:i:s");
                          
                    $result = myQuery("user_wallet_logs", $walletlogs, "insert");
    
                    unset($where);
                    $where['vPhone'] = $mobilnumber;
                    $where['iDriverId'] = $receiverData[0]['iDriverId'];
                    $updateWallet['vWalletBalance'] =  $newReceiverWalletBalamce;
                    $result = myQuery("register_driver", $updateWallet, "update", $where);
        
                    $transactionNo = GenerateUniqueOrderNo("SM");
                   
                    $walletlogs['iDriverId'] =  $receiverData[0]['iDriverId'] ;
                    $walletlogs['vUserType'] =  $userType;
                    $walletlogs['vTransactionType'] = "RECEIVED";
                    $walletlogs['vLabel'] = "Received Trikoins";
                    $walletlogs['vDescription'] = "";
                    $walletlogs['vTransactionNo'] = $transactionNo;
                    $walletlogs['fAmount'] = (float) $amount;
                    $walletlogs['fWalletBalance'] = (float) $newReceiverWalletBalamce;
                    $walletlogs['vReceiveBy'] = "";
                    $walletlogs['iSenderId'] = $userId;
                    $walletlogs['eStatus'] = "Completed";
                    $walletlogs['dDate'] = @date("Y-m-d H:i:s");
                          
                    $result = myQuery("user_wallet_logs", $walletlogs, "insert");
                
                    $messageArray['response'] = 1;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Okay";
                    $messageArray['amount'] = $amount;
                    $messageArray['recipientName'] =  $recipientName;
                    $messageArray['mobilnumber'] = $mobilnumber;
                    $messageArray['new_fWalletBalance'] = (float) $newSenderWalletBalance;
                }else{

                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] = "Failed";
                    $messageArray['error'] = "not enough balance";
                }

            }else{
                  
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] = "Failed";
                $messageArray['error'] = "not exist";
             
            }

        }else{

            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Failed";
            $messageArray['error'] = "Own Number";
         
        }
       
        
    

        echo json_encode( $messageArray);
        
    }
    
    //$servicetype = "LOAD_WALLET_BALANCE";
    
    if($servicetype == "LOAD_WALLET_BALANCE"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'35';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
    
        
        if($userType == "Driver"){
             
            $sql = "SELECT * FROM register_driver WHERE iDriverId=  $userId";

            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
            
            $messageArray['vWalletBalance'] =  $result[0]['vWalletBalance'];
            
        }
        
        
        if($userType == "User"){
             
            $sql = "SELECT * FROM register_user WHERE iUserId =  $userId";

            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
        }
        
       
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['userType'] = $userType;
        $messageArray['profileData'] = $result;
        
      

        echo json_encode( $messageArray);
        
    }
    
    
    if($servicetype == "SUBMIT_CASH_IN"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1212';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'1212';
        $amount =  isset($_POST['amount']) ? trim($_POST['amount']) :'1212';
        $refNumber =  isset($_POST['referenceNumber']) ? trim($_POST['referenceNumber']) :'1212';
        $cashType =  isset($_POST['cashType']) ? trim($_POST['cashType']) :'1212';
        $receipt =  isset($_POST['receipt']) ? trim($_POST['receipt']) :'';
        
        $CashIn_transactionNo = GenerateUniqueOrderNo("CI");
        
        $cashIn['vTransactionNo'] = $CashIn_transactionNo;
        $cashIn['vReferenceNo'] = $refNumber;
        $cashIn['vAmount'] =  $amount;
        $cashIn['vPaymentType'] = $cashType;
        $cashIn['vImage'] =   $receipt;
        $cashIn['iUserId'] =  $userId ;
        $cashIn['vUserType'] = $userType;
        $cashIn['eStatus'] = "Pending";
        $cashIn['dDate'] = @date("Y-m-d H:i:s");

        $result = myQuery("cashin_transactions", $cashIn, "insert");
        
        
        $walletlogs['iDriverId'] = $userId ;
     
        $walletlogs['vUserType'] =  $userType;
        $walletlogs['vTransactionType'] = "CASH IN";
        $walletlogs['vLabel'] = "Cash in";
        $walletlogs['vDescription'] = "";
        $walletlogs['vTransactionNo'] = $CashIn_transactionNo;
        $walletlogs['fAmount'] = $amount;
        $walletlogs['vReceiveBy'] = "";
        $walletlogs['iReceiveId'] = 0;
        $walletlogs['iSenderId'] = 0;
        $walletlogs['iUserId'] = 0 ;
        $walletlogs['ePaymentMethod'] =  $cashType;
        $walletlogs['eStatus'] = "Pending";
        $walletlogs['dDate'] = @date("Y-m-d H:i:s");
              
        $result = myQuery("user_wallet_logs", $walletlogs, "insert");
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['usertype'] = $userType;
        $messageArray['transaction_no'] = $CashIn_transactionNo;
        
        $messageArray['ref'] =  $refNumber;
        $messageArray['amount'] =  $amount;
        $messageArray['cashType'] = $cashType;
        $messageArray['receipt'] = $receipt;

        echo json_encode( $messageArray);
        
    }
    
    
    if($servicetype == "SUBMIT_CASH_OUT"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1212';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'1212';
        $amount =  isset($_POST['amount']) ? trim($_POST['amount']) :'1212';
        $refNumber =  isset($_POST['referenceNumber']) ? trim($_POST['referenceNumber']) :'1212';
        $cashType =  isset($_POST['cashType']) ? trim($_POST['cashType']) :'1212';
        $receipt =  isset($_POST['receipt']) ? trim($_POST['receipt']) :'';
        
        $CashIn_transactionNo = GenerateUniqueOrderNo("CI");

        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['usertype'] = $userType;
        $messageArray['transaction_no'] = $CashIn_transactionNo;
        
        $messageArray['ref'] =  $refNumber;
        $messageArray['amount'] =  $amount;
        $messageArray['cashType'] = $cashType;
        $messageArray['receipt'] = $receipt;

        echo json_encode( $messageArray);
        
    }
    
    
    
   // $servicetype = "LOAD_BOOKING_TASK";
    
    if($servicetype == "LOAD_BOOKING_TASK"){
        
          
        unset($messageArray);
        unset($where);
        
      
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'17';
        $userType  = isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        
        $sql = "SELECT vName, vLastName, iDriverId, vImage, iTripId, vTripStatus, vLatitude, vLongitude, vAppServiceType, eStatus FROM register_driver WHERE iDriverId = '".$userId."'";
        
        $statement = $db->query($sql);
        
        $taskData = $statement ->fetchAll(); 
        
        $driverName =  $taskData[0]['vName']. " ".$taskData[0]['vLastName'];
        
        $driverId = $taskData[0]['iDriverId'];
        
        $driverImage = $taskData[0]['vImage'];
        
        
        
        
        if($taskData[0]['eStatus'] != "active"){
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "Failed";
            $messageArray['message'] =  "Driver is not Active";
        
            echo json_encode($messageArray);
            
        }else{
            
            // echo "Active";
            
        }
        
       
        if($taskData[0]['vTripStatus'] == "ON_GOING" || $taskData[0]['vTripStatus'] == "ON_THE_WAY_TO_DESTINATION" || $taskData[0]['vTripStatus'] == "ON_THE_WAY_TO_DROPOFF" || $taskData[0]['vTripStatus'] == "IN_TRANSIT" ){
            
       
            
            if($taskData[0]['vAppServiceType'] == "PABILI"){
                
                
                $sql = "SELECT * FROM trips WHERE iTripId = '".$taskData[0]['iTripId']."'";
                
                $statement = $db->query($sql);
                
                $tripData = $statement ->fetchAll(); 
                
                $storeId = $tripData[0]['iCompanyId'];
                
                $orderId = $tripData[0]['iOrderId'];
                
                $userId = $tripData[0]['iUserId'];
                
                //USER DATA
                unset($where);
                $where['iUserId'] = $userId;
                $userData = myQuery("register_user", array("vName", "vLastName", "vLatitude", "vLongitude", "vImgName"), "selectall",  $where);
                
              
        
                //ORDER DATA
                
                $sql = "SELECT * FROM orders WHERE iOrderId = '". $orderId."'";
        
                $statement = $db->query($sql);
                
                $orderData = $statement ->fetchAll(); 
                
                $storeId = $orderData[0]['iCompanyId'];
                
                // $sql2 = "SELECT mi.iMenuItemId as itemId,  mi.vItemType_EN as itemName, mi.fPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc, od.vCancel as itemCancel FROM menu_items as mi 
                // LEFT JOIN order_details as od ON mi.iMenuItemId = od.iMenuItemId WHERE od.iOrderId = ". $orderId;
                
                $sql2 = "SELECT od.iMenuItemId, od.vItemName as itemName, od.fOriginalPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc,
                od.vCancel as itemCancel FROM order_details as od WHERE od.iOrderId = ". $orderId;
       
        
                $statement = $db->query($sql2);
                
                $items = $statement ->fetchAll(); 
                
                
                for($i = 0; $i < count($items); $i++) {
                    
                    $orderDetails['orderItems'][] =  $items[$i];
                    
                }
               
               //STORE DATA
                
                unset($where);
                $where['iCompanyId'] = $storeId;
                $companyAddress = myQuery("company", array("vCompany", "vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
                
                $storeName =  $companyAddress[0]['vCompany'];
                
                $storeAddress =  $companyAddress[0]['vRestuarantLocation'];
                
                $distance = ceil(distance($taskData[0]['vLatitude'],$taskData[0]['vLongitude'], $companyAddress[0]['vRestuarantLocationLat'], $companyAddress[0]['vRestuarantLocationLong'], "K"));
                $duration  = cal_time( $distance, 10);
            
                // $driverAddress = get_Address($taskData[0]['vLatitude'],$taskData[0]['vLongitude']);
                
                // $distance = calculateDistance(, $taskData[0]['vLatitude'], $taskData[0]['vLongitude'], "K");
                
                // $duration =  get_Duration($companyAddress[0]['vRestuarantLocation'], $driverAddress);
                
                
                //UPDATE TRIPS
                
                $sql = "UPDATE trips SET fDistance = '".$distance."', fDuration = '".$duration."' WHERE iTripId = ".$taskData[0]['iTripId'];
        
                $statement = $db->query($sql);
                
                
                $tripResult = $statement ->execute(); 
                
                
                $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '". $orderData[0]['iUserAddressId']."'";
                $statement = $db->query($sql4);
                $serviceAddress = $statement ->fetchAll();
        
                $orderDetails['orderDeliveryName'] =  $serviceAddress[0]['vName']." / ". $serviceAddress[0]['vPhone'];
                $orderDetails['orderDeliveryAddress'] =  $serviceAddress[0]['vServiceAddress'];
                $orderDetails['orderDeliveryAddressLat'] = $serviceAddress[0]['vLatitude'];
                $orderDetails['orderDeliveryAddressLong'] = $serviceAddress[0]['vLongitude'];
        
            
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "OKAY";
                $messageArray['message'] =  "PABILI";
                
                $messageArray['tripStatus'] = $tripData[0]['iActive'];
                $messageArray['startLat'] = $tripData[0]['tStartLat'];
                $messageArray['startLong'] = $tripData[0]['tStartLong'];
                $messageArray['destLat'] = $tripData[0]['tEndLat'];
                $messageArray['destLong'] = $tripData[0]['tEndLong'];
                $messageArray['distance'] = $distance;
                
                $messageArray['driverName'] =  $driverName;
                $messageArray['driverId'] = $driverId;
                $messageArray['driverImage'] = $driverImage;
                
                $messageArray['tripItinerary'] = $tripData[0]['vTripItinerary'];
                
                
                if($tripData[0]['vTripItinerary'] == "FirstTrip"){
                    $messageArray['tripDestination'] =  $storeName;
                    $messageArray['tripDestinationAddress'] = $storeAddress;
                }else if($tripData[0]['vTripItinerary'] == "SecondTrip"){
                    $messageArray['tripDestination'] = $orderData[0]['vDeliveryAddress'];
                    $messageArray['tripDestinationAddress'] = $serviceAddress[0]['vServiceAddress'];
                }else if($tripData[0]['vTripItinerary'] == "LastTrip"){
                    
                    if($orderData[0]['vDeliveryAddress_2'] != ""){
                        $messageArray['tripDestination'] = $orderData[0]['vDeliveryAddress_2'];
                        $messageArray['tripDestinationAddress'] = $serviceAddress[0]['vServiceAddress'];
                    }else{
                        $messageArray['tripDestination'] = $orderData[0]['vDeliveryAddress'];
                        $messageArray['tripDestinationAddress'] = $serviceAddress[0]['vServiceAddress'];
                    }
                   
                }
                
                
                //get_Address($tripData[0]['tEndLat'],$tripData[0]['tEndLong']);
                
                
                $messageArray['userId'] =  $userId;
                $messageArray['userName'] =  $userData[0]['vName'];
                $messageArray['userLastName'] =  $userData[0]['vLastName'];
                $messageArray['userImage'] =  $userData[0]['vImgName'];
                $messageArray['userLat'] =  $userData[0]['vLatitude'];
                $messageArray['userLong'] =  $userData[0]['vLongitude'];
                
                $messageArray['orderId'] = $orderId;
                $messageArray['orderNo'] = $orderData[0]['vOrderNo'];
                $messageArray['orderQty'] = count($items);
                $messageArray['orderDate'] = $orderData[0]['dDate'];
                $messageArray['orderName'] = $orderData[0]['vName'];
                $messageArray['orderType'] = $orderData[0]['vOrderType'];
                $messageArray['orderStatus'] = $orderData[0]['iStatusCode'];
                $messageArray['orderPayment'] = $orderData[0]['ePaymentOption'];
                $messageArray['orderSummary'] = $orderDetails;
                $messageArray['orderTotalPrice'] = $orderData[0]['fTotalGenerateFare'];
                
                echo json_encode( $messageArray);
                
            }
            
            if($taskData[0]['vAppServiceType'] == "PASAKAY"){
                
                echo 'PASAKAY';
                  
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "OKAY";
                $messageArray['message'] =  "PASAKAY";
                
                echo json_encode( $messageArray);
            }
            
        }else{
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "Failed";
            $messageArray['message'] =  "No Available Task";
        
            echo json_encode( $messageArray);
        }
        
    }
    
   // $servicetype = "SAVE_POCKET_MONEY";
    
    if($servicetype == "SAVE_POCKET_MONEY"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'17';
        $pocketMoney =  isset($_POST['pocketMoney']) ? trim($_POST['pocketMoney']) :'1212';
        $driverId = $userId;
        
        $where['iDriverId'] = $userId;
        $Pocket['fPocketMoney'] = $pocketMoney;
        $Pocket['vLatitude'] = $sourceLat;
        $Pocket['vLongitude'] = $sourceLong;
        $result = myQuery("register_driver", $Pocket, "update", $where);
        
        $sql = "SELECT * FROM register_driver WHERE iDriverId = '".$userId."'";
               
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
        
         $day = date('w');
        $week_start = date('m-d-Y', strtotime('-'.$day.' days'));
        $week_end = date('m-d-Y', strtotime('+'.(6-$day).' days'));
        
        $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $driverId."'";
               
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
    
                 
    
        $sql = "SELECT vOrderNo, DATE_FORMAT( tOrderRequestDate, '%m-%d-%Y') as date FROM orders WHERE iDriverId = '". $driverId."' AND iStatusCode = 3010 AND eCancelledBy = 'Driver' AND iCancelledById = '". $driverId."' AND DATE_FORMAT( tOrderRequestDate, '%m-%d-%Y') BETWEEN '". $week_start."' AND '". $week_end ."'";
        $statement = $db->query($sql);
        $result = $statement ->fetchAll(); 
        
        $cancelledOrders = count($result);
        
        $sql = "SELECT vBookingNo, DATE_FORMAT( dBooking_date, '%m-%d-%Y') as date FROM cab_booking WHERE iDriverId = '". $driverId."' AND eStatus = 'Cancelled' AND eCancelBy = 'Driver' AND iCancelByUserId = '". $driverId."' AND  DATE_FORMAT( dBooking_date, '%m-%d-%Y') BETWEEN '". $week_start."' AND '". $week_end ."'";
        $statement = $db->query($sql);
        $result = $statement ->fetchAll(); 
        
        $cancelledBookings= count($result);
        $totalCancel = $cancelledOrders+$cancelledBookings;
        
        // $totalCancel = 5;
    
        $sql = "SELECT fPocketMoney as balance FROM register_driver WHERE iDriverId = '". $driverId."' ";
        $statement = $db->query($sql);
        $data = $statement ->fetchAll(); 
        
        
        $sql = "SELECT ROUND (AVG(vRating),1) AS MyRatings FROM ratings_user_driver WHERE iDriverId = '". $driverId."' ";
        $statement = $db->query($sql);
        $rating = $statement ->fetchAll(); 
        
        unset($where);
        unset($update);
        $update['vAvgRating'] = $rating[0]['MyRatings'] == null ||  $rating[0]['MyRatings'] == "" ? "0.0" :  $rating[0]['MyRatings'];
        $where['iDriverId'] =  $driverId;
        $result = myQuery("register_driver",  $update, "update",  $where);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['totalCancellations'] = $totalCancel;
        $messageArray['MyCancellations'] =  $totalCancel;
        $messageArray['MyRatings'] = $rating[0]['MyRatings'] == null ||  $rating[0]['MyRatings'] == "" ? "0.0" :  $rating[0]['MyRatings'];
        $messageArray['MyPocketMoney'] =   $data[0]['balance'];
        $messageArray['profileData'] = $profileData;
        
        echo json_encode( $messageArray);
        
     
    }
    
     
   if($servicetype == "RESET_PASSWORD"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'17';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'wanderlustph.traveldeals@gmail.com';
    
    
        $token = getToken(49);
        
        if( $userType == "User"){
            
            $sql = "SELECT * FROM register_user WHERE vEmail = '". $email."'";
            $statement = $db->query($sql); 
            $profileData = $statement ->fetchAll();  
            
            unset($where);
            $where['vEmail'] = $email;
            $updatepasswordToken['vPasswordToken'] = $token;
            $updatepasswordToken['vPasswordTokenDate'] = @date("Y-m-d H:i:s");
            $result = myQuery("register_user", $updatepasswordToken, "update", $where);
            
            if(count($profileData ) > 0){
                
                sendResetEmail($email, $profileData[0]['vName'], $token, $userType, $profileData[0]['iUserId']);
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                 $messageArray['userType'] = $userType;
                 $messageArray['token'] = $token;
                 $messageArray['email'] = $email;
                $messageArray['status'] =  "OKAY";
                $messageArray['message'] =  "Email Exist";
                
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['userType'] = $userType;
                $messageArray['token'] = $token;
                $messageArray['email'] = $email;
                $messageArray['status'] =  "Failed";
                $messageArray['message'] =  "Email not exist";
                
            }
            
         
        }else{
            
            $sql = "SELECT * FROM register_driver WHERE vEmail = '".$email."'";
            $statement = $db->query($sql); 
            $profileData = $statement ->fetchAll(); 
            
            unset($where);
            $where['vEmail'] = $email;
            $updatepasswordToken['vPasswordToken'] = $token;
            $updatepasswordToken['vPasswordTokenDate'] = @date("Y-m-d H:i:s");
            $result = myQuery("register_driver",  $updatepasswordToken, "update", $where);
            
            
            if(count($profileData ) > 0){
                
                sendResetEmail($email, $profileData[0]['vName'], $token, $userType, $profileData[0]['iDriverId']);
                 
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['email'] = $email;
                $messageArray['userType'] = $userType;
                $messageArray['token'] = $token;
                $messageArray['status'] =  "OKAY";
                $messageArray['message'] =  "Email Exist";
            
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['userType'] = $userType;
                $messageArray['email'] = $email;
                $messageArray['token'] = $token;
                $messageArray['status'] =  "Failed";
                $messageArray['message'] =  "Email not exist";
            }
            
          
        }
        
        
        // $where['iDriverId'] = $userId;
        // $Pocket['fPocketMoney'] = $pocketMoney;
        // $Pocket['vLatitude'] = $sourceLat;
        // $Pocket['vLongitude'] = $sourceLong;
        // $result = myQuery("register_driver", $Pocket, "update", $where);
        
    
        
        echo json_encode( $messageArray);
      
    }
    
    //$servicetype = "SEND_EMAIL_VERIFICATION";
    
    if($servicetype == "SEND_EMAIL_VERIFICATION"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'72';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'laurencevegerano@gmail.com';
        $name =  isset($_POST['name']) ? trim($_POST['name']) :'';
        
        $token = GenerateToken();
        
        switch( $userType){
            
            case 'User':
                
                
                sendVerificationEmail($email, $name, $token, $userType, $userId);
                
                unset($where);
                $where['iUserId'] = $userId;
        
                $vupdate['vEmailVerificationCode'] =  $token ;
                 
                $result = myQuery("register_user", $vupdate, "update", $where);
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "Success";
                $messageArray['message'] =  "Verification sent!";
                
            
                break;
            case 'Driver':
                
                sendVerificationEmail($email, $name, $token, $userType, $userId);
                
                unset($where);
                $where['iDriverId'] = $userId;
        
                $vupdate['vEmailVerificationCode'] =  $token ;
                 
                $result = myQuery("register_driver", $vupdate, "update", $where);
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "Success";
                $messageArray['message'] =  "Verification sent!";
                
                break;
            
        }
    
        
        echo json_encode( $messageArray);
      
    }
    
    
  //  $servicetype = "CHECK_EMAIL_VERIFY";
    
    if($servicetype == "CHECK_EMAIL_VERIFY"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'39';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'renceveterans.devgmail.com';
        
        if( $userType == "User"){
            
            $sql = "SELECT * FROM register_user WHERE iUserId = '".$userId."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            if( $profileData[0]['eEmailVerified'] != "Yes"){
                
                // sendVerificationEmail($email, $profileData[0]['vName'], $token, $userType, $userId);
                
                // $where['iUserId'] = $userId;
    
                // $vupdate['vEmailVerificationCode'] =  $token ;
                 
                // $result = myQuery("register_user", $vupdate, "update", $where);
                
                 if($profileData[0]['vEmailVerificationCode'] != null || $profileData[0]['vEmailVerificationCode'] != ""){
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] =  "ALREADY_SENT";
                    $messageArray['message'] =  "Email not exist";
                    
                }else{
                      $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "Success";
        
                }
                
              
                
            }else{
                
               
                
                $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] =  "Failed";
                    $messageArray['message'] =  "Email not exist";
                
                
            }
            
            
           
        }else if($userType == "Driver"){
            
            $sql = "SELECT * FROM register_driver WHERE iDriverId = '".$userId."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll(); 
            if( $profileData[0]['eEmailVerified'] != "Yes"){
                
                
                if($profileData[0]['vEmailVerificationCode'] != null || $profileData[0]['vEmailVerificationCode'] != ""){
                    
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] =  "ALREADY_SENT";
                    $messageArray['message'] =  "Email not exist";
                    
                }else{
                    
                    $messageArray['response'] = 1;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] =  "Success";
        
                }
                
              
                
            }else{
                
               
                
                $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] =  "Failed";
                    $messageArray['message'] =  "Email not exist";
                
                
            }
            
        }
        
        
        
        echo json_encode( $messageArray);
      
    }
    
    
    
//   $servicetype = "LOAD_TRANSACTIONS";
    
    if($servicetype == "LOAD_TRANSACTIONS"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'24';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $transactionType =  isset($_POST['transactionType']) ? trim($_POST['transactionType']) :'TRANSACTIONS';
        
        if($userType == "User"){
            
            $sql = "SELECT * FROM user_wallet_logs WHERE iUserId = '".$userId."' AND vUserType = '".$userType."'  AND vTransactionType = '".$transactionType."' ORDER BY dDate DESC"  ;
               
            $statement = $db->query($sql); 
    
            $walletData = $statement ->fetchAll();  
            
        }else{

            // if($transactionType == "Transactions" || $transactionType == "TRANSACTIONS" ){
                 
            //     $sql = "SELECT * FROM user_wallet_logs WHERE (iDriverId = '".$userId."' AND vUserType = '".$userType."')  AND (vTransactionType = 'RECEIVED' OR vTransactionType = 'SEND' OR vTransactionType = 'CASH IN' OR vTransactionType = 'CASH OUT')"  ;
                
        
                
            //     $statement = $db->query($sql); 
        
            //      $walletData  = $statement ->fetchAll();  
                
       
                


            // }else{

            //     $sql = "SELECT * FROM user_wallet_logs WHERE iDriverId = '".$userId."' AND vUserType = '".$userType."'  AND vTransactionType = '".$transactionType."'"  ;
                
            //     $statement = $db->query($sql); 
        
            //     $walletData = $statement ->fetchAll();  

            // }
            
            
             $sql = "SELECT * FROM user_wallet_logs WHERE iDriverId = '".$userId."' AND vUserType = '".$userType."' ORDER BY dDate DESC"  ;
                
                $statement = $db->query($sql); 
        
                $walletData = $statement ->fetchAll();  
            
            $sql = "SELECT vPhone, vWalletBalance FROM register_driver WHERE iDriverId = $userId";

            $statement = $db->query($sql);
            
            $driverData = $statement ->fetchAll();
            
            $messageArray['walletBalance'] = $driverData[0]['vWalletBalance'] ;
           
        }
        
       
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['message'] =  "";
        $messageArray['walletData'] =   $walletData;
    
        
        echo json_encode( $messageArray);
        
        
    }
    
    //$servicetype = "CHECK_EMAIL";
    
    if($servicetype == "CHECK_EMAIL"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'laure@gmail.com';
        
        $result =  checkEmailExist($userType, $email);

        if($result ==  0){
            
                
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "FAILED";
            $messageArray['message'] =  "Email Exist!";
      
            
            echo json_encode( $messageArray);
            
        }else{
            
                
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "OKAY";
            $messageArray['message'] =  "Successfull Updated!";
            
            echo json_encode( $messageArray);
            
        }
        
        
        
    }
    
 //  $servicetype = "LOAD_TRANSACTION_DETAILS";
    
    
    if($servicetype == "LOAD_TRANSACTION_DETAILS"){
        
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'24';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $transactionId =  isset($_POST['transactionId']) ? trim($_POST['transactionId']) :'PAS20110278073';
        $transactionType =  isset($_POST['transactionType']) ? trim($_POST['transactionType']) :'PASAKAY';
        
        if($transactionType  == "PABILI"){
            
            //ORDER DATA
                
            $sql = "SELECT * FROM orders WHERE vOrderNo = '". $transactionId."'";
    
            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
            
            unset($where);
            $where['iCompanyId'] = $result[0]['iCompanyId'];
            $companyAddress = myQuery("company", array("vCompany", "vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
            
             
            unset($where);
            $where['iUserId'] = $result[0]['iUserId'];
            $userAddress = myQuery("register_user", array("vName", "vPhone"), "selectall",  $where);
            
            
            $storeName =  $companyAddress[0]['vCompany'];
            $storeAddress =  $companyAddress[0]['vRestuarantLocation'];
            
            $date = date_create($result[0]['dDate']);

            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";

            $messageArray['transactionId'] =  $result[0]['vOrderNo'];
            $messageArray['origin'] = $storeName;
            $messageArray['originAddress'] =  $storeAddress;
           
            $messageArray['destination'] = $userAddress[0]['vName']." / ". $userAddress[0]['vPhone'];
            $messageArray['destinationAddress'] = $result[0]['vDeliveryAddress'];

            $messageArray['destination2'] = $userAddress[0]['vName']."/". $userAddress[0]['vPhone'];
            $messageArray['destinationAddress2'] = $result[$i]['vDeliveryAddress_2'];
            
            $messageArray['date'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['orderPrice'] = $result[0]['fSubTotal'];
            $messageArray['deliveryCharge'] = $result[0]['fDeliveryCharge'];
            $messageArray['totalOrderAmount'] = $result[0]['fTotalGenerateFare'];
            $messageArray['totalEarnings'] = $result[0]['fCommision'];
            $messageArray['totalTransactionFee'] = $result[0]['fWalletDebit'];
            
        
            echo json_encode( $messageArray);
            
            
        }else if($transactionType  == "PASAKAY"){

            
            
            
            $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '". $transactionId."'";
    
            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
            
            $bookingData = array();

            
        
            // for($i = 0; $i < count($result); $i++) {
                
            
    
            //     $bookingData [$i]['bookingStatus'] = $result[$i]['eStatus'];
            //     $bookingData [$i]['paymentMethod'] = $result[$i]['ePayType'];
            //     $bookingData [$i]['bookingId'] = $result[$i]['iCabBookingId'];
            //     $bookingData [$i]['bookingNo'] =  $result[$i]['vBookingNo'];
            //     $bookingData [$i]['originAddress'] =  $result[$i]['vSourceAddress'];
            //     $bookingData [$i]['destinationAddress'] =  $result[$i]['tDestAddress'];
            //     $bookingData [$i]['bookingPrice'] = "40";
                
              
                
            
            
            // }

            $date = date_create($result[0]['dBooking_date']);

            $bookingData [$i]['bookingDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");


            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";

            $messageArray['transactionId'] =  $result[0]['vBookingNo'];
            $messageArray['origin'] = "";
            $messageArray['originAddress'] =  $result[0]['vSourceAddress'];
            
            $messageArray['destination'] = "";
            $messageArray['destinationAddress'] = $result[0]['tDestAddress'];

            $messageArray['destination2'] = "";
            $messageArray['destinationAddress2'] = $result[0]['tDestAddress'];

            $messageArray['date'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['orderPrice'] = $result[0]['fTripGenerateFare'];
            $messageArray['deliveryCharge'] = $result[0]['fWaitingCharge'];
            $messageArray['totalOrderAmount'] = $result[0]['fTripTotalAmountFare'];
            $messageArray['totalEarnings'] = $result[0]['fCommision'];
            $messageArray['totalTransactionFee'] = $result[0]['fWalletDebit'];
            

            echo json_encode($messageArray);
            
            
        }else if($transactionType  == "CASH IN"){
            
            
            $sql = "SELECT driver.iDriverId as iDriverId, driver.vPhone as vPhone, driver.vName as vName,  wallet.* FROM user_wallet_logs as wallet LEFT JOIN register_driver as driver ON driver.iDriverId = wallet.iDriverId WHERE wallet.vTransactionNo = '". $transactionId."' AND wallet.vUserType = 'Driver'";
    
            $statement = $db->query($sql);
            
            $walletLogData = $statement ->fetchAll(); 
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";

            $messageArray['transactionId'] =  $walletLogData[0]['vTransactionNo'];
            $date = date_create(walletLogData[0]['dDate']);
            $messageArray['date'] =  date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['name'] =  $walletLogData[0]['vName'];
            $messageArray['mobile_number'] =  $walletLogData[0]['vPhone'];
            $messageArray['status'] =  $walletLogData[0]['eStatus'];
            $messageArray['amount'] =  $walletLogData[0]['fAmount'];
            $messageArray['cashInMethod'] =  $walletLogData[0]['ePaymentMethod'];
            
            
        
            

            echo json_encode($messageArray);
            
        }else if($transactionType  == "CASH OUT"){
            
            
            $sql = "SELECT driver.iDriverId as iDriverId, driver.vPhone as vPhone, driver.vName as vName,  wallet.* FROM user_wallet_logs as wallet LEFT JOIN register_driver as driver ON driver.iDriverId = wallet.iDriverId WHERE wallet.vTransactionNo = '". $transactionId."' AND wallet.vUserType = 'Driver'";
    
            $statement = $db->query($sql);
            
            $walletLogData = $statement ->fetchAll(); 
            $date = date_create(walletLogData[0]['dDate']);
            $messageArray['date'] =  date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";

            $messageArray['transactionId'] =  $walletLogData[0]['vTransactionNo'];
            $messageArray['name'] =  $walletLogData[0]['vName'];
            $messageArray['mobile_number'] =  $walletLogData[0]['vPhone'];
            $messageArray['status'] =  $walletLogData[0]['eStatus'];
            $messageArray['amount'] =  $walletLogData[0]['fAmount'];
            

            echo json_encode($messageArray);
           
            
        }else if($transactionType  == "SEND"){
            
            
            $sql = "SELECT driver.iDriverId as iDriverId, driver.vPhone as vPhone, driver.vName as vName,  wallet.* FROM user_wallet_logs as wallet LEFT JOIN register_driver as driver ON driver.iDriverId = wallet.iReceiveId WHERE wallet.vTransactionNo = '". $transactionId."' AND wallet.vUserType = 'Driver'";
    
            $statement = $db->query($sql);
            
            $walletLogData = $statement ->fetchAll(); 
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";
            $date = date_create(walletLogData[0]['dDate']);
            $messageArray['date'] =  date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['transactionId'] =  $walletLogData[0]['vTransactionNo'];
            $messageArray['name'] =  $walletLogData[0]['vName'];
            $messageArray['mobile_number'] =  $walletLogData[0]['vPhone'];
            $messageArray['status'] =  $walletLogData[0]['eStatus'];
            $messageArray['amount'] =  $walletLogData[0]['fAmount'];

            echo json_encode($messageArray);
            
        }else if($transactionType  == "RECEIVED"){
            
           
            $sql = "SELECT driver.iDriverId as iDriverId, driver.vPhone as vPhone, driver.vName as vName,  wallet.* FROM user_wallet_logs as wallet LEFT JOIN register_driver as driver ON driver.iDriverId = wallet.iSenderId WHERE wallet.vTransactionNo = '". $transactionId."' AND wallet.vUserType = 'Driver'";
    
            $statement = $db->query($sql);
            
            $walletLogData = $statement ->fetchAll(); 
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";
            $date = date_create(walletLogData[0]['dDate']);
            $messageArray['date'] =  date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['transactionId'] =  $walletLogData[0]['vTransactionNo'];
            $messageArray['name'] =  $walletLogData[0]['vName'];
            $messageArray['mobile_number'] =  $walletLogData[0]['vPhone'];
            $messageArray['status'] =  $walletLogData[0]['eStatus'];
            $messageArray['amount'] =  $walletLogData[0]['fAmount'];
            

            echo json_encode($messageArray);
            
        }
        
        
      
        
    }
    
    // $servicetype = "RATE_DRIVER";
    
    
    if($servicetype == "RATE_DRIVER"){
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $serviceType =  isset($_POST['serviceType']) ? trim($_POST['serviceType']) :'PASAKAY';
        $driverId =  isset($_POST['driverId']) ? trim($_POST['driverId']) :'92';
        $tripId =  isset($_POST['tripId']) ? trim($_POST['tripId']) :'92';
        $orderId =  isset($_POST['orderId']) ? trim($_POST['orderId']) :'0';
        $bookingId =  isset($_POST['bookingId']) ? trim($_POST['bookingId']) :'0';
        $rating =  isset($_POST['rating']) ? trim($_POST['rating']) :'5';
        $remarks =  isset($_POST['remarks']) ? trim($_POST['remarks']) :'ah okay';
        $satisfaction =  isset($_POST['satisfaction']) ? trim($_POST['satisfaction']) :'ah okay';
        
        
        $eToUserType = "Driver";
        if($orderId != ""){
            $eFromUserType = "Buyer"; 
        }else{
            $eFromUserType = "Passenger"; 
        }
        
            
        $rate_insert['iOrderId'] = $orderId;
         
        $rate_insert['iTripId'] = $iTripId;

        $rate_insert['iBookingId'] = $bookingId;
    
        $rate_insert['iUserId'] = $userId;
        
        $rate_insert['vRating'] = $rating;
        
        $rate_insert['iDriverId'] =  $driverId;
         
        $rate_insert['tDate'] = @date("Y-m-d H:i:s");

        $rate_insert['vMessage'] = $remarks;
    
        $rate_insert['eUserType'] = $userType;
        
        $rate_insert['vSatisfaction'] = $satisfaction;
        
        $rate_insert['eFromUserType'] = $eFromUserType;
        
        $rate_insert['eToUserType'] =  $eToUserType;
    
        
        $result = myQuery("ratings_user_driver",  $rate_insert, "insert");
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['message'] =  "Rate Successful!!";
        
        echo json_encode( $messageArray);
        
    }
    
    
  //  $servicetype = "ADD_USER_ADDRESS";
    
    
    if($servicetype == "ADD_USER_ADDRESS"){
    
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $serviceAddress =  isset($_POST['serviceAddress']) ? trim($_POST['serviceAddress']) :'sdsd';
        $addressType =  isset($_POST['addressType']) ? trim($_POST['addressType']) :'Home';
        $region =  isset($_POST['region']) ? trim($_POST['region']) :'Home';
        $province=  isset($_POST['province']) ? trim($_POST['province']) :'Home';
        $city=  isset($_POST['city']) ? trim($_POST['city']) :'Home';
        $barangay =  isset($_POST['barangay']) ? trim($_POST['barangay']) :'Home';
        $exactAddress =  isset($_POST['exactAddress']) ? trim($_POST['exactAddress']) :'Home';
        $name =  isset($_POST['name']) ? trim($_POST['name']) :'Home';
        $mobile =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'Home';

        // $unitHouseNo =  isset($_POST['unitHouseNo']) ? trim($_POST['unitHouseNo']) :'sds';
        // $buildingStreetName =  isset($_POST['buildingStreetName']) ? trim($_POST['uildingStreetName']) :'sdd';
        // $landmark =  isset($_POST['landmark']) ? trim($_POST['landmark']) :'sdsd';
        // $forDeliveryAddress = isset($_POST['forDeliveryAddress']) ? trim($_POST['forDeliveryAddress']) :'No';
        
        if($addressType == "HOME" || $addressType == "Home"){
            $addressType = "Home";
        }
        
        if($addressType == "WORK" || $addressType == "Work"){
            $addressType = "Work";
        }
        
        if($addressType == "OTHER" || $addressType == "Other"){
            $addressType = "Other";
        }
        
        
        if($forDeliveryAddress == "Yes"){
            
            $sql = "SELECT * FROM user_address WHERE vServiceAddress = '".$serviceAddress."' AND vAddressType = '". $addressType."' AND iUserId = '". $userId."'";
        
            $statement = $db->query($sql);
                
            $addressCheck = $statement ->fetchAll(); 
            
            
            if(count($addressCheck)> 0){
                
                    
                $addAddress0['iUserId'] =  $userId;
                $addAddress0['eUserType'] = "Rider";
                $addAddress0['vServiceAddress'] = ucwords(strtolower($exactAddress)).", ". ucwords(strtolower($city)).", ".ucwords(strtolower($province)).", ". ucwords(strtolower($region)) ;
                $addAddress0['vAddressType'] = $addressType ;
                $addAddress0['vRegion'] = $region ;
                $addAddress0['vProvince'] = $province ;
                $addAddress0['vCity'] = $city ;
                $addAddress0['vBarangay'] = $barangay ;
                $addAddress0['vExactAddress'] =  $exactAddress ;
                $addAddress0['vName'] =  $name ;
                $addAddress0['vPhone'] =  $mobile ;
               
                $addAddress0['dAddedDate'] =  @date("Y-m-d H:i:s") ;
                $addAddress0['vTimeZone'] =  "Asia/Manila";
                $addAddress0['vLatitude'] = $sourceLat;
                $addAddress0['vLongitude'] = $sourceLong;
                
                $where['iUserId'] =  $userId;
                $where['vAddressType'] = $addressType ;
                $result = myQuery("user_address",  $addAddress0, "update",  $where);
                
                
            }else{
                
                $addAddress0['iUserId'] =  $userId;
                $addAddress0['eUserType'] = "Rider";
                $addAddress0['vServiceAddress'] = ucwords(strtolower($exactAddress)).", ". ucwords(strtolower($city)).", ".ucwords(strtolower($province)).", ". ucwords(strtolower($region)) ;
                $addAddress0['vAddressType'] = $addressType ;
                $addAddress0['vRegion'] = $region ;
                $addAddress0['vProvince'] = $province ;
                $addAddress0['vCity'] = $city ;
                $addAddress0['vBarangay'] = $barangay ;
                $addAddress0['vExactAddress'] =  $exactAddress ;
                $addAddress0['vName'] =  $name ;
                $addAddress0['vPhone'] =  $mobile ;
                $addAddress0['dAddedDate'] =  @date("Y-m-d H:i:s") ;
                $addAddress0['vTimeZone'] =  "Asia/Manila";
                $addAddress0['vLatitude'] = $sourceLat;
                $addAddress0['vLongitude'] = $sourceLong;
                
                $result = myQuery("user_address",    $addAddress0, "insert");
            }
            
            
            $sql = "SELECT * FROM user_address WHERE iUserId = '".$userId."' AND vServiceAddress = '". $addAddress0['vServiceAddress']."' AND dAddedDate = '". $addAddress0['dAddedDate']."' order by iUserAddressId DESC limit 1";
        
            $statement = $db->query($sql);
                
            $addressData = $statement ->fetchAll(); 
           
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "OKAY";
            $messageArray['message'] =  "Successfull Updated!";
            $messageArray['addressData'] = $addressData;
            
            echo json_encode( $messageArray);
            
        }else{
            
            $sql = "SELECT * FROM user_address WHERE iUserId = '".$userId."' AND vAddressType = '". $addressType."' AND iUserId = '". $userId."'";
        
            $statement = $db->query($sql);
                
            $addressCheck = $statement ->fetchAll(); 
            
            if(count($addressCheck)> 0){
                
                    
                $addAddress0['iUserId'] =  $userId;
                $addAddress0['eUserType'] = "Rider";
                $addAddress0['vServiceAddress'] = ucwords(strtolower($exactAddress)).", ". ucwords(strtolower($city)).", ".ucwords(strtolower($province)).", ". ucwords(strtolower($region)) ;
                $addAddress0['vAddressType'] = $addressType;
                $addAddress0['vRegion'] = $region ;
                $addAddress0['vProvince'] = $province ;
                $addAddress0['vCity'] = $city ;
                $addAddress0['vBarangay'] = $barangay ;
                $addAddress0['vExactAddress'] =  $exactAddress ;
                $addAddress0['vName'] =  $name ;
                $addAddress0['vPhone'] =  $mobile ;
                $addAddress0['dAddedDate'] =  @date("Y-m-d H:i:s") ;
                $addAddress0['vTimeZone'] =  "Asia/Manila";
                $addAddress0['vLatitude'] = $sourceLat;
                $addAddress0['vLongitude'] = $sourceLong;
                
                $where['iUserId'] =  $userId;
                $where['vAddressType'] = $addressType ;
                $result = myQuery("user_address",  $addAddress0, "update",  $where);
                
                
            }else{
                
                $addAddress0['iUserId'] =  $userId;
                $addAddress0['eUserType'] = "Rider";
                $addAddress0['vServiceAddress'] = ucwords(strtolower($exactAddress)).", ". ucwords(strtolower($city)).", ".ucwords(strtolower($province)).", ". ucwords(strtolower($region)) ;
                $addAddress0['vAddressType'] = $addressType ;
                $addAddress0['vRegion'] = $region ;
                $addAddress0['vProvince'] = $province ;
                $addAddress0['vCity'] = $city ;
                $addAddress0['vBarangay'] = $barangay ;
                $addAddress0['vExactAddress'] =  $exactAddress ;
                $addAddress0['vName'] =  $name ;
                $addAddress0['vPhone'] =  $mobile ;
                $addAddress0['dAddedDate'] =  @date("Y-m-d H:i:s") ;
                $addAddress0['vTimeZone'] =  "Asia/Manila";
                $addAddress0['vLatitude'] = $sourceLat;
                $addAddress0['vLongitude'] = $sourceLong;
                
                $result = myQuery("user_address",    $addAddress0, "insert");
            }
            
            
            $sql = "SELECT * FROM user_address WHERE iUserId = '".$userId."' AND vServiceAddress = '".  $addAddress0['vServiceAddress']."' AND dAddedDate = '". $addAddress0['dAddedDate']."' order by iUserAddressId DESC limit 1";
        
            $statement = $db->query($sql);
                
            $addressData = $statement ->fetchAll(); 
           
            
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "OKAY";
            $messageArray['message'] =  "Successfull Updated!";
            $messageArray['addressData'] = $addressData;
            
            echo json_encode( $messageArray);
        }
        
       
        
    
    }
    
    
    
    //$servicetype = "UPDATE_BOOKING_TRACK_STATUS";
    
    
    if($servicetype == "UPDATE_BOOKING_TRACK_STATUS"){
        
        unset($messageArray);
        unset($where);
              
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $bookingId  = isset($_POST['bookingId']) ? trim($_POST['bookingId']) :'92';
        $status  = isset($_POST['status']) ? trim($_POST['status']) :'In Transit';
        $driverId = isset($_POST['driverId']) ? trim($_POST['driverId']) :'13';
        
      
        $sql = "SELECT * FROM cab_booking WHERE iCabBookingId = '".$bookingId ."'";
    
        $statement = $db->query($sql);
            
        $bookingData = $statement ->fetchAll(); 
         
        unset($where);
        $where['iDriverId'] = $driverId ;
        $tripData = myQuery("register_driver", array("iTripId","vWalletBalance"), "selectall",  $where);
    
        if($bookingData[0]['eStatus'] != "Cancelled"){
         
            if($status == "At the Pickup Point"){
                unset($where);
                $where['iDriverId'] = $driverId;
                $driver_status['vTripStatus'] = trim("ARRIVED_AT_PICKUP_POINT");
                $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                
                unset($where);
                $where['iTripId'] =  $tripData[0]['iTripId'];
                $updatePreviousTrip['iActive'] = "Finished" ;
                $updatePreviousTripResult = myQuery("trips",  $updatePreviousTrip, "update",  $where);
                
                
                $createtripData['iCabBookingId'] =  $bookingId;
                $createtripData['iUserId'] =   $userId;
                $createtripData['iDriverId'] =  $driverId;
                $createtripData['tStartDate'] =  @date("Y-m-d H:i:s");
            
                $createtripData['tStartLat'] = $bookingData[0]['vSourceLatitude'];
                $createtripData['tStartLong'] = $bookingData[0]['vSourceLongitude'];
                
                $createtripData['tEndLat'] = $bookingData[0]['vDestLatitude'];
                $createtripData['tEndLong'] = $bookingData[0]['vDestLongitude'];
                
                $createtripData['tSaddress'] = $bookingData[0]['vSourceAddress'];
                $createtripData['tDaddress'] = $bookingData[0]['tDestAddress'];
                $createtripData['vTripItinerary'] = "LastTrip";
                //$lastInsertedId = myQuery("trips",  $Data, "insert_getlastid");
                $result5 = myQuery("trips", $createtripData, "insert");
                
                unset($where);
                $where['iCabBookingId'] = $bookingId;
                $where['iActive'] =  "Active" ;
                $newTripData = myQuery("trips", array("iTripId"), "selectall",  $where);
                
              
                unset($where);
                $where['iDriverId'] = $driverId;
                $driver_status['iTripId'] = $newTripData[0]['iTripId'];
                $driver_status['vTripStatus'] = trim("ARRIVED_AT_PICKUP_POINT");
                $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                
                   
                sendRequestToUser($userId, "DRIVER_HAS_ARRIVED","Pasakay booking.", "Driver had arrived at the pickup point");
                
                  
            }else if($status == "In Transit"){
                
                 unset($where);
                $where['iDriverId'] = $driverId;
                $driver_status['vTripStatus'] = trim("IN_TRANSIT");
                $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                
                
                sendRequestToUser($userId, "IN_TRANSIT","Pasakay booking.", "You are on the way to your drop off location. Sit back and relax.");
            
                
            
            }else if($status == "Deliver to first Drop Off"){
                
              
            }else if($status == "Arrived at the destination"){
                
                unset($where);
                $where['iDriverId'] = $driverId;
                $driver_status['vTripStatus'] = trim("ARRIVED");
                $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                
                //CHECK IF TRANSACTION ALREADY EXIST!!
                unset($where);
                $where['vTransactionNo'] = $bookingData[0]['vBookingNo'];
                $isExist = myQuery("user_wallet_logs", array("vTransactionNo"), "selectall",  $where);
                
                if(count($isExist) <= 0){
                    
                    $Trikaroo_transactionFee = (float) $bookingData[0]['fTripTotalAmountFare'] * (float) $bookingData[0]['fCompanyPercentage'];
                    $Trikaroo_driver_earnings = (float)$bookingData[0]['fTripTotalAmountFare'] - $Trikaroo_transactionFee;
                    
                    unset($where);
                    $where['iCabBookingId'] = $bookingId;
                    $fare_status['iBaseFare'] =  (float)$bookingData[0]['iBaseFare'];
                    $fare_status['fPricePerMin'] = (float)$bookingData[0]['fPricePerMin'];
                    $fare_status['fPricePerKM'] = (float)$bookingData[0]['fPricePerKM'];
                    $fare_status['fCommision'] = (float) $Trikaroo_driver_earnings;
                    $fare_status['fWalletDebit'] = (float) $Trikaroo_transactionFee ;
                    $fare_status['tTripEnded'] = @date("Y-m-d H:i:s");
                    $fareResult = myQuery("cab_booking",  $fare_status, "update", $where);
                    
                    
                    $driverWallet = (float)$tripData[0]['vWalletBalance'] - (float)$Trikaroo_transactionFee ;
                    
                    unset($where);
                    $where['iDriverId'] = $driverId;
                    $driver_status['vTripStatus'] = trim("ARRIVED");
                    $driver_status['vWalletBalance'] = $driverWallet;
                    $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                    
                    
                         
                    $walletlogs['iDriverId'] =  $driverId;
                    $walletlogs['vUserType'] =  "Driver";
                    $walletlogs['vTransactionType'] = "PASAKAY";
                    $walletlogs['vLabel'] = "- Debited";
                    $walletlogs['vDescription'] = "";
                    $walletlogs['vTransactionNo'] =  $bookingData[0]['vBookingNo'];
                    $walletlogs['fAmount'] = (float)$Trikaroo_transactionFee ;
                    $walletlogs['fWalletBalance'] = (float) $driverWallet;
                    $walletlogs['vReceiveBy'] = "";
                    $walletlogs['iReceiveId'] = "";
                    $walletlogs['eStatus'] = "Completed";
                    $walletlogs['dDate'] = @date("Y-m-d H:i:s");
                          
                    $result = myQuery("user_wallet_logs",  $walletlogs, "insert");
                    
                    
                      //USER DATA
                    unset($where);
                    $where['iUserId'] = $userId;
                    $userData = myQuery("register_user", array("vName", "vLastName", "fRewardPointsBalance"), "selectall",  $where);
                    
                    $earnedPoints = (float)$Trikaroo_transactionFee * constants::REWARDS_POINTS_RATE;
                    $totalRewardPointsBalance = (float)$userData[0]['fRewardPointsBalance']+$earnedPoints;
                    
                    unset($where);
                    $where['iUserId'] = $userId;
                    $userReward_status['fRewardPointsBalance'] =  $totalRewardPointsBalance ;
                    $result = myQuery("register_user", $userReward_status, "update", $where);
                    
                    $transactionNo = GenerateUniqueOrderNo("RP");
        
                    $rewardslogs['iUserId'] = $userId ;
                    $rewardslogs['vUserType'] = "User";
                    $rewardslogs['vTransactionType'] = "PASAKAY";
                    $rewardslogs['vLabel'] = "Earned points";
                    $rewardslogs['vDescription'] = "";
                    $rewardslogs['vTransactionNo'] = $bookingData[0]['vBookingNo'];
                    $rewardslogs['fPoints'] = (float)  $earnedPoints ;
                    $rewardslogs['fTotalPointsAmount'] = (float)    $totalRewardPointsBalance;
                    $rewardslogs['eStatus'] = "Earned";
                    $rewardslogs['dDateCreated'] = @date("Y-m-d H:i:s");
                          
                    $result = myQuery("rewards_user_logs", $rewardslogs, "insert");
                    
                    sendRequestToUser($userId, "ARRIVED","Pasakay booking.", "You had arrived at your destination.");
                    
                }
                
            
            }else if($status == "Finished"){
              
                unset($where);
                $where['iDriverId'] = $driverId;
                $driver_status['vTripStatus'] = trim("FINISHED");
                $driver_status['iTripId'] = 0;
                $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                
                
                
                unset($where);
                $where['iUserId'] = $userId;
                $user_status['vTripStatus'] = "NONE";
                $user_status['iTripId'] = 0;
                $result3 = myQuery("register_user", $user_status, "update", $where);
                
                
                unset($where);
                $where['iTripId'] =  $tripData[0]['iTripId'];
                $updatePreviousTrip['iActive'] = "Finished" ;
                $updatePreviousTripResult = myQuery("trips",  $updatePreviousTrip, "update",  $where);
                
                // sendPasakayInvoice($bookingData[0]['vBookingNo']);
              
                
            }else if($status == "Booking Cancelled"){
                
                $newStatusCode = "3010";
                
                unset($where);
                $where['iCabBookingId'] =  $bookingId;
                $trip_status['tTripEnded'] = @date("Y-m-d H:i:s");
                $tripResult = myQuery("cab_booking",  $trip_status, "update",  $where);
              
                
                 unset($where);
                $where['iDriverId'] = $driverId;
                $driver_status['vTripStatus'] = "FINISHED";
                $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                
                 unset($where);
                $where['iDriverId'] = $driverId;
                $driver_status['vTripStatus'] = "FINISHED";
                $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                
                   setOrderLogs("3010", $orderId);
                  
            }else{
                
                $newStatusCode = "0000";
                
            }
            
            
            unset($where);
            $where['iCabBookingId'] =  $bookingId;
            $booking_update['eStatus'] = $status;
            $result1 = myQuery("cab_booking",  $booking_update, "update",  $where);
              
            
            unset($where);
            $where['iCabBookingId'] =  $bookingId;
            $iStatusCode = myQuery("cab_booking", array( "eStatus", "vBookingNo"), "selectall",  $where);
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  $iStatusCode[0]['eStatus'];
            $messageArray['bookingNo'] =  $iStatusCode[0]['vBookingNo'];
        
        }else{
            
            unset($where);
            $where['iCabBookingId'] =  $bookingId;
            $iStatusCode = myQuery("cab_booking", array( "eStatus", "vBookingNo", "eCancelBy"), "selectall",  $where);
        
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  $iStatusCode[0]['eStatus'];
            $messageArray['eCancelBy'] =  $iStatusCode[0]['eCancelBy'];
            $messageArray['bookingNo'] =  $iStatusCode[0]['vBookingNo'];
        }
        
        echo json_encode($messageArray);
        
    }
    
   //$servicetype == "LOAD_BOOKING_DETAILS"
    if($servicetype == "LOAD_BOOKING_DETAILS"){
    
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $bookingId =  isset($_POST['bookingId']) ? trim($_POST['bookingId']) :'8';
        $waitingTime =  isset($_POST['waitingTime']) ? trim($_POST['waitingTime']) :'';
    
        
        $sql = "SELECT * FROM cab_booking WHERE iCabBookingId = '".$bookingId."'";
        $statement = $db->query($sql);
        $bookingData = $statement ->fetchAll(); 
        
        // $destinationLocationArr = array($bookingData[0]['vDestLatitude'], $bookingData[0]['vDestLongitude']);
        // $destinationLocationId = getLocationArea($destinationLocationArr);
    
        // $suggestedToda = array();
        
        // $sql = "SELECT * FROM register_toda WHERE Where = '".$destinationLocationId."'";
        // $statement = $db->query($sql);
        // $todaData = $statement ->fetchAll(); 
        
        // $todaId = $todaData[0]['iTodaId'];
        // $todaName = $todaData[0]['vTodaName'];
        // $todaRouteNo = $todaData[0]['vTodaRouteNo'];
        // $baseFare = (float) $todaData[0]['iPasakayBaseFare'];
        // $farePricePerKm = (float) $todaData[0]['fPricePerKM'];
        // $farePricePerMin = (float) $todaData[0]['fPricePerMin'];
        // $radiusDistance = (int) $todaData[0]['fRadius'];
        
        
    
        if($waitingTime != ''){
            
            unset($where);
            $where['iCabBookingId'] = $bookingId;
            $booking_status['vWaitingTime'] = $waitingTime;
            $bookingUpdate = myQuery("cab_booking", $booking_status, "update", $where);
            
        }else{
            
            
            $waitingTime = $bookingData[0]['vWaitingTime'];
        }
     
        $waitingTime = $bookingData[0]['vWaitingTime'];
    
        if(count($bookingData)> 0){
            
            $farePricePerMin = (float) $bookingData[0]['fPricePerMin'];
            
            $WaitingTime_in_minutes = ((int) $waitingTime % 3600) / 60;
            
            $messageArray['WaitingTime_in_minutes'] =  intval($WaitingTime_in_minutes);
            
            $WaitingTime_in_seconds = ((int) $waitingTime % 60);
            
            $messageArray['WaitingTime_in_seconds'] = $WaitingTime_in_seconds;
            
            if($WaitingTime_in_seconds >= 1){
                
                $final_WaitingTime_in_minutes = intval($WaitingTime_in_minutes)+1;
            }else{
                $final_WaitingTime_in_minutes = $WaitingTime_in_minutes;
            }
            
            $totalWaitingFee =   $final_WaitingTime_in_minutes *  $farePricePerMin;
            
            $totalFareAmount =  (float) $bookingData[0]['fTripGenerateFare'] +  $totalWaitingFee ;
            
            unset($where);
            $where['iCabBookingId'] = $bookingId;
            $booking_status['vWaitingTime'] = $waitingTime;
            $booking_status['fWaitingCharge'] = (float)$totalWaitingFee;
            $booking_status['fTripTotalAmountFare'] = (float)  roundOff($totalFareAmount);
            $bookingUpdate = myQuery("cab_booking", $booking_status, "update", $where);
            
            
            $sql = "SELECT * FROM cab_booking WHERE iCabBookingId = '".$bookingId."'";
            $statement = $db->query($sql);
            $bookingData = $statement ->fetchAll(); 
            
            
            unset($where);
            $where['iUserId'] =  $userId;
            $userData = myQuery("register_user", array( "vName", "vLastName", "vImgName", "vLatitude", "vLongitude", "fWalletBalance"), "selectall",  $where);
            
            
            $sql = "SELECT vWalletBalance FROM register_driver WHERE iDriverId = '".$bookingData[0]['iDriverId']."'";
            $statement = $db->query($sql);
            $driverData = $statement ->fetchAll(); 
        
            
            unset($where);
            $where['iDriverId'] =   $bookingData[0]['iDriverId'];
            $driverData = myQuery("register_driver", array( "vName", "vLastName", "vLatitude", "vLongitude", "vWalletBalance", "vImage", "fPocketMoney", "vTripStatus"), "selectall",  $where);
        
        
            $messageArray['driverId'] = $bookingData[0]['iDriverId'];
            $messageArray['driverName'] =  $driverData[0]['vName'];
            $messageArray['driverLastName'] =  $driverData[0]['vLastName'];
            $messageArray['driverImage'] =  $driverData[0]['vImage'];
            $messageArray['driverLat'] =  $driverData[0]['vLatitude'];
            $messageArray['driverLong'] =  $driverData[0]['vLongitude'];
            $messageArray['driverWalletBalance'] =  $driverData[0]['vWalletBalance'];
            $messageArray['driverPocketMoney'] =  $driverData[0]['fPocketMoney'];
            $messageArray['driverTripStatus'] =  $driverData[0]['vTripStatus'];
            
                
            $messageArray['userId'] =  $userId;
            $messageArray['userName'] =  $userData[0]['vName'];
            $messageArray['userLastName'] =  $userData[0]['vLastName'];
            $messageArray['userImage'] =  $userData[0]['vImgName'];
            $messageArray['userLat'] =  $userData[0]['vLatitude'];
            $messageArray['userLong'] =  $userData[0]['vLongitude'];
            $messageArray['userWalletBalance'] =  $userData[0]['fWalletBalance'];
            $messageArray['userWalletBalance'] =  $userData[0]['fWalletBalance'];
            
            $messageArray['bookingWaitingTime'] = $bookingData[0]['vWaitingTime'];
            $messageArray['bookingId'] =  $bookingData[0]['iCabBookingId'];
            $messageArray['bookingStatus'] =  $bookingData[0]['eStatus'];
            $messageArray['bookingNo'] =  $bookingData[0]['vBookingNo'];
            $messageArray['bookingTotalAmount'] = roundOff($bookingData[0]['fTripGenerateFare']);
            $messageArray['bookingTotalFareAmount'] = roundOff($bookingData[0]['fTripTotalAmountFare']);
            $messageArray['bookingEarnings'] = roundOff($bookingData[0]['fCommision']);
            $messageArray['bookingTrasactionFee'] = roundOff($bookingData[0]['fWalletDebit']);
            $messageArray['bookingPayment'] = $bookingData[0]['ePayType'];
            $date = date_create($bookingData[0]['dBooking_date']);
            $messageArray['bookingDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['bookingOrigin'] = $bookingData[0]['vSourceAddress'];
            $messageArray['bookingOriginLat'] = $bookingData[0]['vSourceLatitude'];
            $messageArray['bookingOriginLong'] = $bookingData[0]['vSourceLongitude'];
            $messageArray['bookingDestination'] = $bookingData[0]['tDestAddress'];
            $messageArray['bookingDestinationLat'] = $bookingData[0]['vDestLatitude'];
            $messageArray['bookingDestinationLong'] = $bookingData[0]['vDestLongitude'];
            $messageArray['bookingWaitingFeePerMinute'] = 2;
            $messageArray['bookingWaitingFee'] =  $totalWaitingFee;
            $messageArray['RateFinished'] = $bookingDate[0]['eRatingFinished'];
        
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "OKAY";
            $messageArray['message'] =  "Successfull Updated!";
            
            if($WaitingTime_in_minutes > 1){
                
                if(intval($WaitingTime_in_minutes) == 1){
                    
                    if(intval(  $WaitingTime_in_seconds) == 0){
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min ";
                    }else if(intval(  $WaitingTime_in_seconds) == 1){
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min, ".intval(  $WaitingTime_in_seconds)." second";
                    }else{
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min, ".intval(  $WaitingTime_in_seconds)." seconds";
                    }
                   
                }else{
                     if(intval(  $WaitingTime_in_seconds) == 0){
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins ";
                    }else if(intval(  $WaitingTime_in_seconds) == 1){
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins, ".intval(  $WaitingTime_in_seconds)." second";
                    }else{
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins, ".intval(  $WaitingTime_in_seconds)." seconds";
                    }
                }
                
                // $messageArray['waiting_time'] = intval($WaitingTime_in_minutes) == 1 ? intval($WaitingTime_in_minutes)." min ".intval(  $WaitingTime_in_seconds)." seconds" : intval($WaitingTime_in_minutes)." mins ".intval(  $WaitingTime_in_seconds)." seconds";
                 
            }else{
                
            
                 $messageArray['waiting_time'] = intval(  $WaitingTime_in_seconds)." second";
            }
            

         
            $messageArray['waiting_time_charge'] = roundOff(   $totalWaitingFee ) ;
            $messageArray['Total_FARE'] = roundOff($totalFareAmount);
            
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "OKAY";
            $messageArray['message'] =  "booking Id not Exist";
          
        }
    
       
    
        echo json_encode( $messageArray);

    }
    
    ///$servicetype = "LOAD_BOOKING_ACTIVITIES";
    
    
     if($servicetype == "LOAD_BOOKING_ACTIVITIES"){
         
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';

        $driverId =  isset($_POST['driverId']) ? trim($_POST['driverId']) :'48';
        $startDate =  isset($_POST['startDate']) ? trim($_POST['startDate']) :'2020-11-23';
        $endDate =  isset($_POST['endDate']) ? trim($_POST['endDate']) :'2020-11-29';

        
        $sql = "SELECT iCabBookingId as bookingId, eStatus as bookingStatus, vBookingNo as bookingNo, DATE_FORMAT(dBooking_date, '%Y-%m-%d') as bookingDate, fCommision as TotalAmountFare,   DAYNAME(dBooking_date) as day, DATE_FORMAT(dBooking_date, '%b %d') as date FROM cab_booking WHERE iDriverId = '". $driverId."' AND dBooking_date BETWEEN date('".$startDate."') and date('".$endDate."')";
        $statement = $db->query($sql);
        $results = $statement ->fetchAll();
    
        
        $bookingData = array();
        
        for($j = 0; $j < count($results); $j++){
            
            if($results[$j]['bookingStatus'] == 'Finished' || $results[$j]['bookingStatus'] == 'Cancelled'){
                
                 $bookingData[] = $results[$j];
                
            }
        
        }
        
        // echo count($bookingData)."</br>";
        
        
        $bookingActivities = array();
        
        
        $countMon = 0;  $countTue = 0;  $countWed = 0; $countThu = 0; $countFri = 0;  $countSat = 0;  $countSun = 0;
        $TotalAmountEarn_Mon = 0;  $TotalAmountEarn_Tue = 0;  $TotalAmountEarn_Wed = 0; $TotalAmountEarn_Thu = 0; $TotalAmountEarn_Fri = 0;  $TotalAmountEarn_Sat = 0;  $TotalAmountEarn_Sun = 0;
        
        
        for($x = 0; $x < 7; $x++){
        
            $bookingActivities[$x]['day'] =  "";
            $bookingActivities[$x]['date'] =  "";
            $bookingActivities[$x]['Jobs'] =  0;
            $bookingActivities[$x]['status'] = "";
            $bookingActivities[$x]['AmountEarn'] = 0.0;
        }
        
        
         
        for($x = 0; $x < count($bookingData); $x++){
            
          
                
            switch($bookingData[$x]['day']){
                
               
              
                
                 
                case "Sunday":
                      
                    $countSun++;
                    $TotalAmountEarn_Sun = $TotalAmountEarn_Sun+$bookingData[$x]['TotalAmountFare'];
                    

                    $bookingActivities[6]['day'] =  "Sunday";
                    $bookingActivities[6]['bookingDate'] =  $bookingData[$x]['bookingDate'];
                    $bookingActivities[5]['date'] =  $bookingData[$x]['date'];
                    $bookingActivities[6]['Jobs'] =  $countSun;
                    $bookingActivities[6]['AmountEarn'] =   $TotalAmountEarn_Sun;
                      
                      
                    break;
                    
                       
                case "Saturday":
                      
                    $countSat++;
                      
                    $TotalAmountEarn_Sat = $TotalAmountEarn_Sat+$bookingData[$x]['TotalAmountFare'];
                    
                    
                    $bookingActivities[5]['day'] =  "Saturday";
                    $bookingActivities[5]['bookingDate'] =  $bookingData[$x]['bookingDate'];
                    $bookingActivities[5]['date'] =  $bookingData[$x]['date'];
                    $bookingActivities[5]['Jobs'] =  $countSat;
                    $bookingActivities[5]['status'] =  $bookingData[$x]['bookingStatus'];
                    $bookingActivities[5]['AmountEarn'] =   $TotalAmountEarn_Sat;
                      
                      
                    break;
                    
                case "Friday":
                      
                    $countFri++;
                    $TotalAmountEarn_Fri = $TotalAmountEarn_Fri+$bookingData[$x]['TotalAmountFare'];
                    
                    
                    $bookingActivities[4]['day'] =  "Friday";
                    $bookingActivities[4]['bookingDate'] =  $bookingData[$x]['bookingDate'];
                    $bookingActivities[4]['date'] =  $bookingData[$x]['date'];
                    $bookingActivities[4]['Jobs'] =  $countFri;
                    $bookingActivities[4]['status'] =  $bookingData[$x]['bookingStatus'];
                    $bookingActivities[4]['AmountEarn'] =   $TotalAmountEarn_Fri;
                    break;
                    
                case "Thursday":
                      
                    $countThu++;
                    
                    $TotalAmountEarn_Thu = $TotalAmountEarn_Thu+$bookingData[$x]['TotalAmountFare'];
                    
                    $bookingActivities[3]['day'] =  "Thursday";
                    $bookingActivities[3]['bookingDate'] =  $bookingData[$x]['bookingDate'];
                    $bookingActivities[3]['date'] =  $bookingData[$x]['date'];
                    $bookingActivities[3]['Jobs'] =  $countThu;
                    $bookingActivities[3]['status'] =  $bookingData[$x]['bookingStatus'];
                    $bookingActivities[3]['AmountEarn'] =   $TotalAmountEarn_Thu;
                    
                    break;
                    
                case "Wednesday":
                    
                    $countWed++;
                
                    $TotalAmountEarn_Wed = $TotalAmountEarn_Wed+$bookingData[$x]['TotalAmountFare'];
                    
                    $bookingActivities[2]['day'] =  "Wednesday";
                    $bookingActivities[2]['bookingDate'] =  $bookingData[$x]['bookingDate'];
                    $bookingActivities[2]['date'] =  $bookingData[$x]['date'];
                    $bookingActivities[2]['Jobs'] =  $countWed;
                    $bookingActivities[2]['status'] =  $bookingData[$x]['bookingStatus'];
                    $bookingActivities[2]['AmountEarn'] =   $TotalAmountEarn_Wed;
                    
                    
                    break;
                    
                 case "Tuesday":
                      
                    $countTue++;
                    $TotalAmountEarn_Tue = $TotalAmountEarn_Tue+$bookingData[$x]['TotalAmountFare'];
                    
                    
                    $bookingActivities[1]['day'] =  "Tuesday";
                    $bookingActivities[1]['bookingDate'] =  $bookingData[$x]['bookingDate'];
                    $bookingActivities[1]['date'] =  $bookingData[$x]['date'];
                    $bookingActivities[1]['Jobs'] =  $countTue;
                    $bookingActivities[1]['status'] =  $bookingData[$x]['bookingStatus'];
                    $bookingActivities[1]['AmountEarn'] =   $TotalAmountEarn_Tue;
                      
                    break;
                    
                case "Monday":
                    $countMon++;
                    $TotalAmountEarn_Mon = $TotalAmountEarn_Mon+$bookingData[$x]['TotalAmountFare'];
                    
                    
                    $bookingActivities[0]['day'] =  "Monday";
                    $bookingActivities[0]['bookingDate'] =  $bookingData[$x]['bookingDate'];
                    $bookingActivities[0]['date'] =  $bookingData[$x]['date'];
                    $bookingActivities[0]['Jobs'] =   $countMon;
                    $bookingActivities[0]['status'] =  $bookingData[$x]['bookingStatus'];
                    $bookingActivities[0]['AmountEarn'] =  $TotalAmountEarn_Mon;

                    break;
                 
             
                    
                
            }
            
     
            
        }
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['bookingData'] = $bookingActivities;
        $messageArray['bookingTotalJobs'] = count($bookingData);
        
        
          echo json_encode( $messageArray);
       
        
     }
     
    // $servicetype = "LOAD_PASAKAY_JOBS_ACTIVITIES";
     
     
     if($servicetype == "LOAD_PASAKAY_JOBS_ACTIVITIES"){
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '';
        $driverId  = isset($_POST['driverId']) ? trim($_POST['driverId']) : '48';
        $date  = isset($_POST['date']) ? trim($_POST['date']) : '2020-11-26';
        
        
        $sql = "SELECT DATE_FORMAT(bookings.dBooking_date, '%Y-%m-%d') as date, bookings.* FROM cab_booking as bookings WHERE bookings.iDriverId = ".$driverId." AND DATE_FORMAT(bookings.dBooking_date, '%Y-%m-%d') = '".$date."'";

        $statement = $db->query($sql);
        
        $result = $statement ->fetchAll(); 
        
        $bookingData = array();
        
        for($i = 0; $i < count($result); $i++) {
            
            
            
            
            if($result[$i]['eStatus'] != "Completed" || $result[$i]['eStatus'] != "Finished"){
                
                // $bookingData [$i]['bookingPrice'] = $result[$i]['fTripGenerateFare'];
                $bookingData [$i]['bookingPrice'] = $result[$i]['fCommision'] == null ? '0' : $result[$i]['fCommision'];
            }else{
                $bookingData [$i]['bookingPrice'] = $result[$i]['fCommision'] == null ? '0' : $result[$i]['fCommision'];
            }
            
            
             if($result[$i]['eStatus'] == "Finished"){
                
                $bookingData [$i]['bookingStatus'] = "Completed";
                
             }else{
                $bookingData [$i]['bookingStatus']  = "(Cancelled)";
             }
                
       
            
            $bookingData [$i]['paymentMethod'] = $result[$i]['ePayType'];
            $bookingData [$i]['bookingId'] = $result[$i]['iCabBookingId'];
            $bookingData [$i]['bookingNo'] =  $result[$i]['vBookingNo'];
            $bookingData [$i]['originAddress'] =  $result[$i]['vSourceAddress'];
            $bookingData [$i]['destinationAddress'] =  $result[$i]['tDestAddress'];
            $bookingData [$i]['bookingId'] =  $result[$i]['iCabBookingId'];
            
            $date = date_create($result[$i]['dBooking_date']);

            $bookingData [$i]['bookingDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            
         
         
        }
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['bookingData'] =  $bookingData;
        
         $messageArray['total'] = count($result);
        
        echo json_encode($messageArray);
         
         
    }
    
    
   //$servicetype = "LOAD_PABILI_JOBS_ACTIVITIES";
         
      if($servicetype == "LOAD_PABILI_JOBS_ACTIVITIES"){
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '';
        $driverId  = isset($_POST['driverId']) ? trim($_POST['driverId']) : '48';
        $date  = isset($_POST['date']) ? trim($_POST['date']) : '2020-11-24';
        
        
        $newDate = date("Y-m-d", strtotime($date));
        
        
        $sql = "SELECT DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') as date, orders.* FROM orders WHERE orders.iDriverId = ".$driverId." AND iStatusCode >= 3009  AND DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') = '".$date."'";

        $statement = $db->query($sql);
        
        $result = $statement ->fetchAll(); 
        
        $orderData = array();
        
        for($i = 0; $i < count($result); $i++) {
            
            if($result[$i]['iStatusCode'] == "3009"){
                
                $orderData [$i]['orderStatus'] = "Completed";
                
            }else{
               
                $orderData [$i]['orderStatus'] = "(Cancelled)";
            }
            
            $date = date_create($result[$i]['tOrderRequestDate']);

            $orderData [$i]['orderDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $orderData [$i]['orderTotalPrice'] = $result[$i]['fCommision'];
            $orderData [$i]['paymentMethod'] = $result[$i]['ePaymentOption'];
            $orderData [$i]['orderId'] = $result[$i]['iOrderId'];
            $orderData [$i]['orderNo'] =  $result[$i]['vOrderNo'];
            $orderData [$i]['deliveryAddress'] =  $result[$i]['vDeliveryAddress'];
            
            if( $result[$i]['vDeliveryaddress'] != ""){
                
                  $orderData [$i]['deliveryaddress2'] =  $result[$i]['vDeliveryAddress_2'];
            }
            
        }
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['orderData'] =  $orderData;
        
         $messageArray['total'] = count($result);
        
        echo json_encode($messageArray);
         
         
    }
    
   // $servicetype = "LOAD_ORDER_ACTIVITIES";
    
     if($servicetype == "LOAD_ORDER_ACTIVITIES"){
         
         
    
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';

        $driverId =  isset($_POST['driverId']) ? trim($_POST['driverId']) :'13';
        $startDate =  isset($_POST['startDate']) ? trim($_POST['startDate']) :'13';
        $endDate =  isset($_POST['endDate']) ? trim($_POST['endDate']) :'13';

        $sql = "SELECT iOrderId as orderId, iStatusCode, vOrderNo as orderNo, DATE_FORMAT(tOrderRequestDate, '%Y-%m-%d') as orderDate, fCommision as TotalAmountPrice,   DAYNAME(tOrderRequestDate) as day, DATE_FORMAT(tOrderRequestDate, '%b %d') as date FROM orders WHERE iDriverId = '". $driverId."' AND iStatusCode >= 3009 AND tOrderRequestDate BETWEEN date('".$startDate."') and date('".$endDate."')";
        $statement = $db->query($sql);
        $orderData = $statement ->fetchAll(); 
        
        $orderActivities = array();
        $countMon = 0;  $countTue = 0;  $countWed = 0; $countThu = 0; $countFri = 0;  $countSat = 0;  $countSun = 0;
        $TotalAmountEarn_Mon = 0;  $TotalAmountEarn_Tue = 0;  $TotalAmountEarn_Wed = 0; $TotalAmountEarn_Thu = 0; $TotalAmountEarn_Fri = 0;  $TotalAmountEarn_Sat = 0;  $TotalAmountEarn_Sun = 0;
        
        for($x = 0; $x < 7; $x++){
        
            $orderActivities[$x]['day'] =  "";
            $orderActivities[$x]['date'] =  "";
            $orderActivities[$x]['Jobs'] =  0;
            $orderActivities[$x]['status'] = "";
            $orderActivities[$x]['AmountEarn'] = 0.0;
        }
        
        
         
        for($x = 0; $x < count($orderData); $x++){
            
          
                
            switch($orderData[$x]['day']){
                
                case "Sunday":
                      
                    $countSun++;
                    $TotalAmountEarn_Sun = $TotalAmountEarn_Sun+$orderData[$x]['TotalAmountPrice'];
                    

                    $orderActivities[6]['day'] =  "Sunday";
                    $orderActivities[6]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[5]['date'] =  $orderData[$x]['date'];
                    $orderActivities[6]['Jobs'] =  $countSun;
                    $orderActivities[6]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[6]['AmountEarn'] =   $TotalAmountEarn_Sun;
                      
                      
                    break;
                    
                case "Saturday":
                      
                    $countSat++;
                      
                    $TotalAmountEarn_Sat = $TotalAmountEarn_Sat+$orderData[$x]['TotalAmountPrice'];
                    
                    
                    $orderActivities[5]['day'] =  "Saturday";
                    $orderActivities[5]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[5]['date'] =  $orderData[$x]['date'];
                    $orderActivities[5]['Jobs'] =  $countSat;
                    $orderActivities[5]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[5]['AmountEarn'] =   $TotalAmountEarn_Sat;
                      
                      
                      
                    break;
                case "Friday":
                      
                     $countFri++;
                     $TotalAmountEarn_Fri = $TotalAmountEarn_Fri+$orderData[$x]['TotalAmountPrice'];
                    
                    
                    $orderActivities[4]['day'] =  "Friday";
                    $orderActivities[4]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[4]['date'] =  $orderData[$x]['date'];
                    $orderActivities[4]['Jobs'] =  $countFri;
                    $orderActivities[4]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[4]['AmountEarn'] =   $TotalAmountEarn_Fri;
                    break;
                    
                case "Thursday":
                      
                    $countThu++;
                    
                    $TotalAmountEarn_Thu = $TotalAmountEarn_Thu+$orderData[$x]['TotalAmountPrice'];
                    
                    
                    $orderActivities[3]['day'] =  "Thursday";
                    $orderActivities[3]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[3]['date'] =  $orderData[$x]['date'];
                    $orderActivities[3]['Jobs'] =  $countThu;
                    $orderActivities[3]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[3]['AmountEarn'] =   $TotalAmountEarn_Thu;
                    
                    break;
                    
                case "Wednesday":
                    $countWed++;
                
                    $TotalAmountEarn_Wed = $TotalAmountEarn_Wed+$orderData[$x]['TotalAmountPrice'];
                    
                    $orderActivities[2]['day'] =  "Wednesday";
                    $orderActivities[2]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[2]['date'] =  $orderData[$x]['date'];
                    $orderActivities[2]['Jobs'] =  $countWed;
                    $orderActivities[2]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[2]['AmountEarn'] =   $TotalAmountEarn_Wed;
                    
                    
                    break;
                    
                case "Tuesday":
                      
                    $countTue++;
                    $TotalAmountEarn_Tue = $TotalAmountEarn_Tue+$orderData[$x]['TotalAmountPrice'];
                    
                    
                    $orderActivities[1]['day'] =  "Tuesday";
                    $orderActivities[1]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[1]['date'] =  $orderData[$x]['date'];
                    $orderActivities[1]['Jobs'] =  $countTue;
                    $orderActivities[1]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[1]['AmountEarn'] =   $TotalAmountEarn_Tue;
                      
                    break;
                    
                case "Monday":
                    
                    $countMon++;
                    $TotalAmountEarn_Mon = $TotalAmountEarn_Mon+$orderData[$x]['TotalAmountPrice'];
                    
                    $orderActivities[0]['day'] =  "Monday";
                    $orderActivities[0]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[0]['date'] =  $orderData[$x]['date'];
                    $orderActivities[0]['Jobs'] =   $countMon;
                    $orderActivities[0]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[0]['AmountEarn'] =  $TotalAmountEarn_Mon;

                    break;
                 
                
                
            }
            
     
            
        }
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['orderData'] = $orderActivities;
        $messageArray['orderTotalJobs'] = count($orderData);
        
        echo json_encode( $messageArray);
        
    }
    
    
    if($servicetype == "LOAD_STORE_ORDER_ACTIVITIES"){
         
         
    
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';

        $storeId =  isset($_POST['storeId']) ? trim($_POST['storeId']) :'13';
        $startDate =  isset($_POST['startDate']) ? trim($_POST['startDate']) :'13';
        $endDate =  isset($_POST['endDate']) ? trim($_POST['endDate']) :'13';

        $sql = "SELECT iOrderId as orderId, iStatusCode, vOrderNo as orderNo, DATE_FORMAT(tOrderRequestDate, '%Y-%m-%d') as orderDate, fSubtotal as TotalAmountPrice,   DAYNAME(tOrderRequestDate) as day, DATE_FORMAT(tOrderRequestDate, '%b %d') as date FROM orders WHERE iCompanyId = '". $storeId."' AND iStatusCode >= 3009 AND tOrderRequestDate BETWEEN date('".$startDate."') and date('".$endDate."')";
        $statement = $db->query($sql);
        $orderData = $statement ->fetchAll(); 
        
        $orderActivities = array();
        $countMon = 0;  $countTue = 0;  $countWed = 0; $countThu = 0; $countFri = 0;  $countSat = 0;  $countSun = 0;
        $TotalAmountEarn_Mon = 0;  $TotalAmountEarn_Tue = 0;  $TotalAmountEarn_Wed = 0; $TotalAmountEarn_Thu = 0; $TotalAmountEarn_Fri = 0;  $TotalAmountEarn_Sat = 0;  $TotalAmountEarn_Sun = 0;
        
        for($x = 0; $x < 7; $x++){
        
            $orderActivities[$x]['day'] =  "";
            $orderActivities[$x]['date'] =  "";
            $orderActivities[$x]['Jobs'] =  0;
            $orderActivities[$x]['status'] = "";
            $orderActivities[$x]['AmountEarn'] = 0.0;
            
        }
         
        for($x = 0; $x < count($orderData); $x++){
            
          
                
            switch($orderData[$x]['day']){
                
                case "Sunday":
                      
                    $countSun++;
                    $TotalAmountEarn_Sun = $TotalAmountEarn_Sun+$orderData[$x]['TotalAmountPrice'];
                    

                    $orderActivities[6]['day'] =  "Sunday";
                    $orderActivities[6]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[5]['date'] =  $orderData[$x]['date'];
                    $orderActivities[6]['Jobs'] =  $countSun;
                    $orderActivities[6]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[6]['AmountEarn'] =   $TotalAmountEarn_Sun;
                      
                      
                    break;
                    
                case "Saturday":
                      
                    $countSat++;
                      
                    $TotalAmountEarn_Sat = $TotalAmountEarn_Sat+$orderData[$x]['TotalAmountPrice'];
                    
                    
                    $orderActivities[5]['day'] =  "Saturday";
                    $orderActivities[5]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[5]['date'] =  $orderData[$x]['date'];
                    $orderActivities[5]['Jobs'] =  $countSat;
                    $orderActivities[5]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[5]['AmountEarn'] =   $TotalAmountEarn_Sat;
                      
                      
                      
                    break;
                case "Friday":
                      
                     $countFri++;
                     $TotalAmountEarn_Fri = $TotalAmountEarn_Fri+$orderData[$x]['TotalAmountPrice'];
                    
                    
                    $orderActivities[4]['day'] =  "Friday";
                    $orderActivities[4]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[4]['date'] =  $orderData[$x]['date'];
                    $orderActivities[4]['Jobs'] =  $countFri;
                    $orderActivities[4]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[4]['AmountEarn'] =   $TotalAmountEarn_Fri;
                    break;
                    
                case "Thursday":
                      
                    $countThu++;
                    
                    $TotalAmountEarn_Thu = $TotalAmountEarn_Thu+$orderData[$x]['TotalAmountPrice'];
                    
                    
                    $orderActivities[3]['day'] =  "Thursday";
                    $orderActivities[3]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[3]['date'] =  $orderData[$x]['date'];
                    $orderActivities[3]['Jobs'] =  $countThu;
                    $orderActivities[3]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[3]['AmountEarn'] =   $TotalAmountEarn_Thu;
                    
                    break;
                    
                case "Wednesday":
                    $countWed++;
                
                    $TotalAmountEarn_Wed = $TotalAmountEarn_Wed+$orderData[$x]['TotalAmountPrice'];
                    
                    $orderActivities[2]['day'] =  "Wednesday";
                    $orderActivities[2]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[2]['date'] =  $orderData[$x]['date'];
                    $orderActivities[2]['Jobs'] =  $countWed;
                    $orderActivities[2]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[2]['AmountEarn'] =   $TotalAmountEarn_Wed;
                    
                    
                    break;
                    
                case "Tuesday":
                      
                    $countTue++;
                    $TotalAmountEarn_Tue = $TotalAmountEarn_Tue+$orderData[$x]['TotalAmountPrice'];
                    
                    
                    $orderActivities[1]['day'] =  "Tuesday";
                    $orderActivities[1]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[1]['date'] =  $orderData[$x]['date'];
                    $orderActivities[1]['Jobs'] =  $countTue;
                    $orderActivities[1]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[1]['AmountEarn'] =   $TotalAmountEarn_Tue;
                      
                    break;
                    
                case "Monday":
                    
                    $countMon++;
                    $TotalAmountEarn_Mon = $TotalAmountEarn_Mon+$orderData[$x]['TotalAmountPrice'];
                    
                    $orderActivities[0]['day'] =  "Monday";
                    $orderActivities[0]['orderDate'] =  $orderData[$x]['orderDate'];
                    $orderActivities[0]['date'] =  $orderData[$x]['date'];
                    $orderActivities[0]['Jobs'] =   $countMon;
                    $orderActivities[0]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                    $orderActivities[0]['AmountEarn'] =  $TotalAmountEarn_Mon;

                    break;
                 
                
                
            }
            
     
            
        }
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['orderData'] = $orderActivities;
        $messageArray['orderTotalJobs'] = count($orderData);
        
        echo json_encode( $messageArray);
        
    }
    
    
    if($servicetype == "LOAD_ALL_TRANSACTION_ACTIVITIES"){
             
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $driverId =  isset($_POST['driverId']) ? trim($_POST['driverId']) :'48';
        $startDate =  isset($_POST['startDate']) ? trim($_POST['startDate']) :'2020-11-23';
        $endDate =  isset($_POST['endDate']) ? trim($_POST['endDate']) :'2020-11-29';
    
        $transactionActivities = array();
        $countMon = 0;  $countTue = 0;  $countWed = 0; $countThu = 0; $countFri = 0;  $countSat = 0;  $countSun = 0;
        $TotalAmountEarn_Mon = 0;  $TotalAmountEarn_Tue = 0;  $TotalAmountEarn_Wed = 0; $TotalAmountEarn_Thu = 0; $TotalAmountEarn_Fri = 0;  $TotalAmountEarn_Sat = 0;  $TotalAmountEarn_Sun = 0;
        
    
        
        $sql = "SELECT iCabBookingId as bookingId, eStatus as bookingStatus, vBookingNo as bookingNo, DATE_FORMAT(dBooking_date, '%Y-%m-%d') as bookingDate, fCommision as TotalAmountFare,   DAYNAME(dBooking_date) as day, DATE_FORMAT(dBooking_date, '%b %d') as date FROM cab_booking WHERE iDriverId = '". $driverId."' AND (eStatus = 'Finished' OR eStatus = 'Cancelled') AND  dBooking_date BETWEEN date('".$startDate."') and date('".$endDate."')";
        $statement = $db->query($sql);
        $bookingData = $statement ->fetchAll();
    
    
        $sql = "SELECT iOrderId as orderId, iStatusCode, vOrderNo as orderNo, DATE_FORMAT(tOrderRequestDate, '%Y-%m-%d') as orderDate, fCommision as TotalAmountPrice,   DAYNAME(tOrderRequestDate) as day, DATE_FORMAT(tOrderRequestDate, '%b %d') as date FROM orders WHERE iDriverId = '". $driverId."' AND iStatusCode >= 3009 AND tOrderRequestDate BETWEEN date('".$startDate."') and date('".$endDate."')";
        $statement = $db->query($sql);
        $orderData = $statement ->fetchAll(); 
    
    
        
        for($x = 0; $x < 7; $x++){
        
            $transactionActivities[$x]['day'] =  "";
            $transactionActivities[$x]['date'] =  "";
            $transactionActivities[$x]['Jobs'] =  0;
            $transactionActivities[$x]['status'] = "";
            $transactionActivities[$x]['AmountEarn'] = 0.0;
        }
        
        
         
        for($x = 0; $x < count($bookingData); $x++){
            
          
                
            switch($bookingData[$x]['day']){
                
               
              
                
                 
                case "Sunday":
                      
                    $countSun++;
                    $TotalAmountEarn_Sun = $TotalAmountEarn_Sun+$bookingData[$x]['TotalAmountFare'];
                    
    
                    $transactionActivities[6]['day'] =  "Sunday";
                    $transactionActivities[6]['transactionDate'] =  $bookingData[$x]['bookingDate'];
                    $transactionActivities[5]['date'] =  $bookingData[$x]['date'];
                    $transactionActivities[6]['Jobs'] =  $countSun;
                    $transactionActivities[6]['AmountEarn'] =   $TotalAmountEarn_Sun;
                      
                      
                    break;
                    
                       
                case "Saturday":
                      
                    $countSat++;
                      
                    $TotalAmountEarn_Sat = $TotalAmountEarn_Sat+$bookingData[$x]['TotalAmountFare'];
                    
                    
                    $transactionActivities[5]['day'] =  "Saturday";
                    $transactionActivities[5]['transactionDate'] =  $bookingData[$x]['bookingDate'];
                    $transactionActivities[5]['date'] =  $bookingData[$x]['date'];
                    $transactionActivities[5]['Jobs'] =  $countSat;
                    $transactionActivities[5]['status'] =  $bookingData[$x]['bookingStatus'];
                    $transactionActivities[5]['AmountEarn'] =   $TotalAmountEarn_Sat;
                      
                      
                    break;
                    
                case "Friday":
                      
                    $countFri++;
                    $TotalAmountEarn_Fri = $TotalAmountEarn_Fri+$bookingData[$x]['TotalAmountFare'];
                    
                    
                    $transactionActivities[4]['day'] =  "Friday";
                    $transactionActivities[4]['transactionDate'] =  $bookingData[$x]['bookingDate'];
                    $transactionActivities[4]['date'] =  $bookingData[$x]['date'];
                    $transactionActivities[4]['Jobs'] =  $countFri;
                    $transactionActivities[4]['status'] =  $bookingData[$x]['bookingStatus'];
                    $transactionActivities[4]['AmountEarn'] =   $TotalAmountEarn_Fri;
                    break;
                    
                case "Thursday":
                      
                    $countThu++;
                    
                    $TotalAmountEarn_Thu = $TotalAmountEarn_Thu+$bookingData[$x]['TotalAmountFare'];
                    
                    $transactionActivities[3]['day'] =  "Thursday";
                    $transactionActivities[3]['transactionDate'] =  $bookingData[$x]['bookingDate'];
                    $transactionActivities[3]['date'] =  $bookingData[$x]['date'];
                    $transactionActivities[3]['Jobs'] =  $countThu;
                    $transactionActivities[3]['status'] =  $bookingData[$x]['bookingStatus'];
                    $transactionActivities[3]['AmountEarn'] =   $TotalAmountEarn_Thu;
                    
                    break;
                    
                case "Wednesday":
                    
                    $countWed++;
                
                    $TotalAmountEarn_Wed = $TotalAmountEarn_Wed+$bookingData[$x]['TotalAmountFare'];
                    
                    $transactionActivities[2]['day'] =  "Wednesday";
                    $transactionActivities[2]['transactionDate'] =  $bookingData[$x]['bookingDate'];
                    $transactionActivities[2]['date'] =  $bookingData[$x]['date'];
                    $transactionActivities[2]['Jobs'] =  $countWed;
                    $transactionActivities[2]['status'] =  $bookingData[$x]['bookingStatus'];
                    $transactionActivities[2]['AmountEarn'] =   $TotalAmountEarn_Wed;
                    
                    
                    break;
                    
                 case "Tuesday":
                      
                    $countTue++;
                    $TotalAmountEarn_Tue = $TotalAmountEarn_Tue+$bookingData[$x]['TotalAmountFare'];
                    
                    
                    $transactionActivities[1]['day'] =  "Tuesday";
                    $transactionActivities[1]['transactionDate'] =  $bookingData[$x]['bookingDate'];
                    $transactionActivities[1]['date'] =  $bookingData[$x]['date'];
                    $transactionActivities[1]['Jobs'] =  $countTue;
                    $transactionActivities[1]['status'] =  $bookingData[$x]['bookingStatus'];
                    $transactionActivities[1]['AmountEarn'] =   $TotalAmountEarn_Tue;
                      
                    break;
                    
                case "Monday":
                    $countMon++;
                    $TotalAmountEarn_Mon = $TotalAmountEarn_Mon+$bookingData[$x]['TotalAmountFare'];
                    
                    
                    $transactionActivities[0]['day'] =  "Monday";
                    $transactionActivities[0]['transactionDate'] =  $bookingData[$x]['bookingDate'];
                    $transactionActivities[0]['date'] =  $bookingData[$x]['date'];
                    $transactionActivities[0]['Jobs'] =   $countMon;
                    $transactionActivities[0]['status'] =  $bookingData[$x]['bookingStatus'];
                    $transactionActivities[0]['AmountEarn'] =  $TotalAmountEarn_Mon;
    
                    break;
                 
             
                    
                
            }
            
     
            
        }
    
    
        for($x = 0; $x < count($orderData); $x++){
                
              
                    
            switch($orderData[$x]['day']){
                
                case "Sunday":
                      
                    $countSun++;
                    $TotalAmountEarn_Sun = $TotalAmountEarn_Sun+$orderData[$x]['TotalAmountPrice'];
                    
    
                     $transactionActivities[6]['day'] =  "Sunday";
                     $transactionActivities[6]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[5]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[6]['Jobs'] =  $countSun;
                     $transactionActivities[6]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[6]['AmountEarn'] =   $TotalAmountEarn_Sun;
                      
                      
                    break;
                    
                case "Saturday":
                      
                    $countSat++;
                      
                    $TotalAmountEarn_Sat = $TotalAmountEarn_Sat+$orderData[$x]['TotalAmountPrice'];
                    
                    
                     $transactionActivities[5]['day'] =  "Saturday";
                     $transactionActivities[5]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[5]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[5]['Jobs'] =  $countSat;
                     $transactionActivities[5]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[5]['AmountEarn'] =   $TotalAmountEarn_Sat;
                      
                      
                      
                    break;
                case "Friday":
                      
                     $countFri++;
                     $TotalAmountEarn_Fri = $TotalAmountEarn_Fri+$orderData[$x]['TotalAmountPrice'];
                    
                    
                     $transactionActivities[4]['day'] =  "Friday";
                     $transactionActivities[4]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[4]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[4]['Jobs'] =  $countFri;
                     $transactionActivities[4]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[4]['AmountEarn'] =   $TotalAmountEarn_Fri;
                    break;
                    
                case "Thursday":
                      
                    $countThu++;
                    
                    $TotalAmountEarn_Thu = $TotalAmountEarn_Thu+$orderData[$x]['TotalAmountPrice'];
                    
                    
                     $transactionActivities[3]['day'] =  "Thursday";
                     $transactionActivities[3]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[3]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[3]['Jobs'] =  $countThu;
                     $transactionActivities[3]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[3]['AmountEarn'] =   $TotalAmountEarn_Thu;
                    
                    break;
                    
                case "Wednesday":
                    $countWed++;
                
                    $TotalAmountEarn_Wed = $TotalAmountEarn_Wed+$orderData[$x]['TotalAmountPrice'];
                    
                     $transactionActivities[2]['day'] =  "Wednesday";
                     $transactionActivities[2]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[2]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[2]['Jobs'] =  $countWed;
                     $transactionActivities[2]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[2]['AmountEarn'] =   $TotalAmountEarn_Wed;
                    
                    
                    break;
                    
                case "Tuesday":
                      
                    $countTue++;
                    $TotalAmountEarn_Tue = $TotalAmountEarn_Tue+$orderData[$x]['TotalAmountPrice'];
                    
                    
                     $transactionActivities[1]['day'] =  "Tuesday";
                     $transactionActivities[1]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[1]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[1]['Jobs'] =  $countTue;
                     $transactionActivities[1]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[1]['AmountEarn'] =   $TotalAmountEarn_Tue;
                      
                    break;
                    
                case "Monday":
                    
                    $countMon++;
                    $TotalAmountEarn_Mon = $TotalAmountEarn_Mon+$orderData[$x]['TotalAmountPrice'];
                    
                     $transactionActivities[0]['day'] =  "Monday";
                     $transactionActivities[0]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[0]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[0]['Jobs'] =   $countMon;
                     $transactionActivities[0]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[0]['AmountEarn'] =  $TotalAmountEarn_Mon;
    
                    break;
                 
                
                
            }
            
     
            
        }
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['data'] = $transactionActivities;
        $messageArray['totalJobs'] = count($bookingData)+count($orderData);
        
        
        echo json_encode( $messageArray);

        
    }
    
    //$servicetype = "LOAD_ALL_STORE_TRANSACTION";
    if($servicetype == "LOAD_ALL_STORE_TRANSACTION"){
             
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $storeId =  isset($_POST['storeId']) ? trim($_POST['storeId']) :'192';
        $startDate =  isset($_POST['startDate']) ? trim($_POST['startDate']) :'2021-04-24';
        $endDate =  isset($_POST['endDate']) ? trim($_POST['endDate']) :'2021-04-29';
    
        $transactionActivities = array();
        $countMon = 0;  $countTue = 0;  $countWed = 0; $countThu = 0; $countFri = 0;  $countSat = 0;  $countSun = 0;
        $TotalAmountEarn_Mon = 0;  $TotalAmountEarn_Tue = 0;  $TotalAmountEarn_Wed = 0; $TotalAmountEarn_Thu = 0; $TotalAmountEarn_Fri = 0;  $TotalAmountEarn_Sat = 0;  $TotalAmountEarn_Sun = 0;
        

    
    
        $sql = "SELECT iOrderId as orderId, iStatusCode, vOrderNo as orderNo, DATE_FORMAT(tOrderRequestDate, '%Y-%m-%d') as orderDate, fSubtotal as TotalAmountPrice,   DAYNAME(tOrderRequestDate) as day, DATE_FORMAT(tOrderRequestDate, '%b %d') as date FROM orders WHERE iCompanyId = '". $storeId."' AND iStatusCode >= 3009 AND tOrderRequestDate BETWEEN date('".$startDate."') and date('".$endDate."')";
        $statement = $db->query($sql);
        $orderData = $statement ->fetchAll(); 
        
       // echo $sql;
    
        // echo json_encode($orderData);
        
        for($x = 0; $x < 7; $x++){
        
            $transactionActivities[$x]['day'] =  "";
            $transactionActivities[$x]['date'] =  "";
            $transactionActivities[$x]['Jobs'] =  0;
            $transactionActivities[$x]['status'] = "";
            $transactionActivities[$x]['AmountEarn'] = 0.0;
        }
        
       
    
        for($x = 0; $x < count($orderData); $x++){
                
              
                    
            switch($orderData[$x]['day']){
                
                case "Sunday":
                      
                    $countSun++;
                    $TotalAmountEarn_Sun = $TotalAmountEarn_Sun+$orderData[$x]['TotalAmountPrice'];
                    
    
                     $transactionActivities[6]['day'] =  "Sunday";
                     $transactionActivities[6]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[5]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[6]['Jobs'] =  $countSun;
                     $transactionActivities[6]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[6]['AmountEarn'] =   $TotalAmountEarn_Sun;
                      
                      
                    break;
                    
                case "Saturday":
                      
                    $countSat++;
                      
                    $TotalAmountEarn_Sat = $TotalAmountEarn_Sat+$orderData[$x]['TotalAmountPrice'];
                    
                    
                     $transactionActivities[5]['day'] =  "Saturday";
                     $transactionActivities[5]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[5]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[5]['Jobs'] =  $countSat;
                     $transactionActivities[5]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[5]['AmountEarn'] =   $TotalAmountEarn_Sat;
                      
                      
                      
                    break;
                case "Friday":
                      
                     $countFri++;
                     $TotalAmountEarn_Fri = $TotalAmountEarn_Fri+$orderData[$x]['TotalAmountPrice'];
                    
                    
                     $transactionActivities[4]['day'] =  "Friday";
                     $transactionActivities[4]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[4]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[4]['Jobs'] =  $countFri;
                     $transactionActivities[4]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[4]['AmountEarn'] =   $TotalAmountEarn_Fri;
                    break;
                    
                case "Thursday":
                      
                    $countThu++;
                    
                    $TotalAmountEarn_Thu = $TotalAmountEarn_Thu+$orderData[$x]['TotalAmountPrice'];
                    
                    
                     $transactionActivities[3]['day'] =  "Thursday";
                     $transactionActivities[3]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[3]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[3]['Jobs'] =  $countThu;
                     $transactionActivities[3]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[3]['AmountEarn'] =   $TotalAmountEarn_Thu;
                    
                    break;
                    
                case "Wednesday":
                    $countWed++;
                
                    $TotalAmountEarn_Wed = $TotalAmountEarn_Wed+$orderData[$x]['TotalAmountPrice'];
                    
                     $transactionActivities[2]['day'] =  "Wednesday";
                     $transactionActivities[2]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[2]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[2]['Jobs'] =  $countWed;
                     $transactionActivities[2]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[2]['AmountEarn'] =   $TotalAmountEarn_Wed;
                    
                    
                    break;
                    
                case "Tuesday":
                      
                    $countTue++;
                    $TotalAmountEarn_Tue = $TotalAmountEarn_Tue+$orderData[$x]['TotalAmountPrice'];
                    
                    
                     $transactionActivities[1]['day'] =  "Tuesday";
                     $transactionActivities[1]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[1]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[1]['Jobs'] =  $countTue;
                     $transactionActivities[1]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[1]['AmountEarn'] =   $TotalAmountEarn_Tue;
                      
                    break;
                    
                case "Monday":
                    
                    $countMon++;
                    $TotalAmountEarn_Mon = $TotalAmountEarn_Mon+$orderData[$x]['TotalAmountPrice'];
                    
                     $transactionActivities[0]['day'] =  "Monday";
                     $transactionActivities[0]['transactionDate'] =  $orderData[$x]['orderDate'];
                     $transactionActivities[0]['date'] =  $orderData[$x]['date'];
                     $transactionActivities[0]['Jobs'] =   $countMon;
                     $transactionActivities[0]['status'] =  $orderData[$x]['iStatusCode'] == "3009" ? "Completed" : "Cancelled";
                     $transactionActivities[0]['AmountEarn'] =  $TotalAmountEarn_Mon;
    
                    break;
                 
                
                
            }
            
     
            
        }
        
        
        $messageArray['response'] = 1;
        $messageArray['storeId'] = $storeId;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['data'] = $transactionActivities;
        $messageArray['totalJobs'] = count($bookingData)+count($orderData);
        
        
        echo json_encode( $messageArray);

        
    }
    
    
    if($servicetype == "LOAD_JOBS_ACTIVITIES"){
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '';
        $driverId  = isset($_POST['driverId']) ? trim($_POST['driverId']) : '48';
        $date  = isset($_POST['date']) ? trim($_POST['date']) : '2020-11-26';
        $jobsArray = array();
        
        $sql = "SELECT DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') as date, orders.* FROM orders WHERE orders.iDriverId = ".$driverId." AND iStatusCode >= 3009  AND DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') = '".$date."'";
        $statement = $db->query($sql);
        $result1 = $statement ->fetchAll(); 
        $orderData = array();
        
        $sql = "SELECT DATE_FORMAT(bookings.dBooking_date, '%Y-%m-%d') as date, bookings.* FROM cab_booking as bookings WHERE bookings.iDriverId = ".$driverId." AND DATE_FORMAT(bookings.dBooking_date, '%Y-%m-%d') = '".$date."'";
        $statement = $db->query($sql);
        $result2 = $statement ->fetchAll(); 
        $bookingData = array();
        
        
        for($i = 0; $i < count($result1); $i++) {
    
            $orderData [$i]['transactionPrice'] = $result1[$i]['fCommision'];
            
            if($result1[$i]['iStatusCode'] == "3009"){
                
                $orderData [$i]['transactionStatus'] = "Completed";
                
            }else{
               
                $orderData [$i]['transactionStatus'] = "(Cancelled)";
            }
            
            $date = date_create($result1[$i]['tOrderRequestDate']);
            $orderData[$i]['transactionDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $orderData[$i]['paymentMethod'] = $result1[$i]['ePaymentOption'];
            $orderData[$i]['transactionId'] = $result1[$i]['iOrderId'];
            $orderData[$i]['transactionNo'] =  $result1[$i]['vOrderNo'];
            $orderData[$i]['transactionAddress'] =  $result1[$i]['vDeliveryAddress'];
            $orderData[$i]['transactionType'] = "PABILI";
        
        }
        
       
        
        for($i = 0; $i < count($result2); $i++) {
            
            if($result2[$i]['eStatus'] != "Completed" || $result2[$i]['eStatus'] != "Finished"){
                $bookingData[$i]['transactionPrice'] = $result2[$i]['fCommision'] == null ? '0' : $result2[$i]['fCommision'];
            }else{
                $bookingData[$i]['transactionPrice'] = $result2[$i]['fCommision'] == null ? '0' : $result2[$i]['fCommision'];
            }
            
            if($result2[$i]['eStatus'] == "Finished"){
            
                $bookingData[$i]['transactionStatus'] = "Completed";
            
            }else{
                $bookingData[$i]['transactionStatus']  = "(Cancelled)";
            }
                
            $date = date_create($result2[$i]['dBooking_date']);
            $bookingData[$i]['transactionDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $bookingData[$i]['paymentMethod'] = $result2[$i]['ePayType'];
            $bookingData[$i]['transactionId'] = $result2[$i]['iCabBookingId'];
            $bookingData[$i]['transactionNo'] =  $result2[$i]['vBookingNo'];
            $bookingData[$i]['transactionAddress'] =  $result2[$i]['tDestAddress'];
            $bookingData[$i]['transactionType'] = "PASAKAY";
            
        }
        
        $jobsArray = array_merge($bookingData,$orderData);
        
        
        // Comparison function 
        // Sort the array  
        usort($jobsArray, function($a, $b) {
            return strtotime($a['transactionDate']) - strtotime($b['transactionDate']);
        });
  
     
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['data'] = $jobsArray;
    
        
        echo json_encode( $messageArray);
     
    }
    
    //$servicetype = "LOAD_STORE_JOBS_ACTIVITIES";
    
    if($servicetype == "LOAD_STORE_JOBS_ACTIVITIES"){
        
        $latitude  = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $longitude  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '192';
        $date  = isset($_POST['date']) ? trim($_POST['date']) : '2021-04-27';
        $jobsArray = array();
        
        $sql = "SELECT DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') as date, orders.* FROM orders WHERE orders.iCompanyId = ".$storeId." AND iStatusCode >= 3009  AND DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') = '".$date."'";
        
        $statement = $db->query($sql);
        $result1 = $statement ->fetchAll(); 
        $orderData = array();
    
        
        
        for($i = 0; $i < count($result1); $i++) {
    
            $orderData [$i]['transactionPrice'] = $result1[$i]['fCommision'];
            
            if($result1[$i]['iStatusCode'] == "3009"){
                
                $orderData [$i]['transactionStatus'] = "Completed";
                
            }else{
               
                $orderData [$i]['transactionStatus'] = "(Cancelled)";
            }
            
            $date = date_create($result1[$i]['tOrderRequestDate']);
            $orderData[$i]['transactionDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $orderData[$i]['paymentMethod'] = $result1[$i]['ePaymentOption'];
            $orderData[$i]['transactionId'] = $result1[$i]['iOrderId'];
            $orderData[$i]['transactionNo'] =  $result1[$i]['vOrderNo'];
            $orderData[$i]['transactionAddress'] =  $result1[$i]['vDeliveryAddress'];
            $orderData[$i]['transactionType'] = "PABILI";
        
        }
        
       
        
        // for($i = 0; $i < count($result2); $i++) {
            
        //     if($result2[$i]['eStatus'] != "Completed" || $result2[$i]['eStatus'] != "Finished"){
        //         $bookingData[$i]['transactionPrice'] = $result2[$i]['fCommision'] == null ? '0' : $result2[$i]['fCommision'];
        //     }else{
        //         $bookingData[$i]['transactionPrice'] = $result2[$i]['fCommision'] == null ? '0' : $result2[$i]['fCommision'];
        //     }
            
        //     if($result2[$i]['eStatus'] == "Finished"){
            
        //         $bookingData[$i]['transactionStatus'] = "Completed";
            
        //     }else{
        //         $bookingData[$i]['transactionStatus']  = "(Cancelled)";
        //     }
                
        //     $date = date_create($result2[$i]['dBooking_date']);
        //     $bookingData[$i]['transactionDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
        //     $bookingData[$i]['paymentMethod'] = $result2[$i]['ePayType'];
        //     $bookingData[$i]['transactionId'] = $result2[$i]['iCabBookingId'];
        //     $bookingData[$i]['transactionNo'] =  $result2[$i]['vBookingNo'];
        //     $bookingData[$i]['transactionAddress'] =  $result2[$i]['tDestAddress'];
        //     $bookingData[$i]['transactionType'] = "PASAKAY";
            
        // }
        
       // $jobsArray = array_merge($bookingData,$orderData);
        $jobsArray = $orderData;
        
        
        // Comparison function 
        // Sort the array  
        usort($jobsArray, function($a, $b) {
            return strtotime($a['transactionDate']) - strtotime($b['transactionDate']);
        });
  
     
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['data'] = $jobsArray;
    
        
        echo json_encode( $messageArray);
     
    }
    
 
    //$servicetype = "LOAD_DRIVER_STATUS";
     
     
    if($servicetype == "LOAD_DRIVER_STATUS"){
         
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $driverId =  isset($_POST['driverId']) ? trim($_POST['driverId']) :'27';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        
        $day = date('w');
        $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
        $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));
        
          
        $sql = "SELECT * FROM cancelled_transactions WHERE iUserId = '".$driverId."' AND  dDate BETWEEN date('". $week_start."') and date('".$week_end."')";
        $statement = $db->query($sql);
        $result = $statement ->fetchAll(); 

        $cancelledTransactions = count($result);
    

        // if($cancelledTransactions >= 3){

        // }
    
                 
    
        // $sql = "SELECT vOrderNo, DATE_FORMAT( tOrderRequestDate, '%m-%d-%Y') as date FROM orders WHERE iDriverId = '". $driverId."' AND iStatusCode = 3010 AND eCancelledBy = 'Driver' AND iCancelledById = '". $driverId."' AND DATE(tOrderRequestDate) = CURDATE()";
        // $statement = $db->query($sql);
        // $result = $statement ->fetchAll(); 
        
        // $cancelledOrders = count($result);
        
       
    
        // $sql = "SELECT vBookingNo, DATE_FORMAT( dBooking_date, '%m-%d-%Y') as date FROM cab_booking WHERE iDriverId = '". $driverId."' AND eStatus = 'Cancelled' AND eCancelBy = 'Driver' AND iCancelByUserId = '". $driverId."' AND  DATE(dBooking_date) = CURDATE()";
        // $statement = $db->query($sql);
        // $result = $statement ->fetchAll(); 
        
        // $cancelledBookings= count($result);
       
       // $totalCancel = $cancelledOrders+$cancelledBookings;
       $totalCancel =  $cancelledTransactions;
        
        // $totalCancel = 2;
    
        $sql = "SELECT fPocketMoney as balance FROM register_driver WHERE iDriverId = '". $driverId."' ";
        $statement = $db->query($sql);
        $data = $statement ->fetchAll(); 
        
        
        $sql = "SELECT ROUND (AVG(vRating),1) AS MyRatings FROM ratings_user_driver WHERE iDriverId = '". $driverId."' ";
        $statement = $db->query($sql);
        $rating = $statement ->fetchAll(); 
        
        unset($where);
        unset($update);
        $update['vAvgRating'] = $rating[0]['MyRatings'] == null ||  $rating[0]['MyRatings'] == "" ? "0.0" :  $rating[0]['MyRatings'];
        $update['vDriverCancellationCount'] = $totalCancel;
        $where['iDriverId'] =  $driverId;
        $result = myQuery("register_driver",  $update, "update",  $where);
        
        
        $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $driverId."'";
               
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll(); 
    
    
    
        $totalCancelledOrders = count($cancelledOrder);
        $totalCancelledBookings = count($cancelledOrder);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        $messageArray['totalCancellations'] = $profileData[0]['vDriverCancellationCount'];
        $messageArray['MyCancellations'] = $profileData[0]['vDriverCancellationCount']."/3";
        $messageArray['MyRatings'] = $rating[0]['MyRatings'] == null ||  $rating[0]['MyRatings'] == "" ? "0.0" :  $rating[0]['MyRatings'];
        $messageArray['MyPocketMoney'] =   $data[0]['balance'];
        $messageArray['profileData'] = $profileData[0];
        
        if($deviceInfo != $profileData[0]['tDeviceData']){
                
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['userType'] = $userType;
            $messageArray['error'] = "AUTO_LOGOUT";
            $messageArray['deviceInfo'] = $deviceInfo;
          
        }
        
        echo json_encode( $messageArray);
       
        
     }
     
     
      if($servicetype == "SUBMIT_REPORT_REQUEST"){
         
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';

        $driverId =  isset($_POST['driverId']) ? trim($_POST['driverId']) :'13';
        $transactionNo =  isset($_POST['transactionNo']) ? trim($_POST['transactionNo']) :'13';
        $serviceType =  isset($_POST['serviceType']) ? trim($_POST['serviceType']) :'13';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'13';
        $issue =  isset($_POST['issue']) ? trim($_POST['issue']) :'13';
        $description =  isset($_POST['description']) ? trim($_POST['description']) :'13';
        
        
        // $insertReport['iUserId'] = "";
        // $insertReport['vUserType'] = "";
        // $insertReport['vTransactionNo'] = $transactionNo;
        // $insertReport['vSubject'] = "";
        // $insertReport['vDescription'] = "";
        // $insertReport['vMessage'] = "";
        // $insertReport['eStatus'] = "";
        // $insertReport['dDateCreated'] = "";
            
        

        
        // $sql = "SELECT * FROM orders WHERE eCancelledBy = 'Driver' AND iCancelledById = '". $driverId."' ";
        // $statement = $db->query($sql);
        // $cancelledOrder = $statement ->fetchAll(); 
        
        // $sql = "SELECT * FROM cab_booking WHERE eCancelBy = 'Driver' AND iCancelByUserId = '". $driverId."' ";
        // $statement = $db->query($sql);
        // $cancelledBookings = $statement ->fetchAll(); 
        
    
        // $sql = "SELECT fPocketMoney as balance FROM register_driver WHERE iDriverId = '". $driverId."' ";
        // $statement = $db->query($sql);
        // $data = $statement ->fetchAll(); 
        
        
    
        $totalCancelledOrders = count($cancelledOrder);
        $totalCancelledBookings = count($cancelledOrder);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] =  "OKAY";
        // $messageArray['MyCancellations'] = $totalCancelledOrders+$totalCancelledBookings;
        // $messageArray['MyRatings'] = 5.5;
        // $messageArray['MyPocketMoney'] =   $data[0]['balance'];
        
        
        echo json_encode( $messageArray);
       
        
     }
     
     
     
    //$servicetype = "SUBMIT_CANCEL_ORDER";
     
    if($servicetype == "SUBMIT_CANCEL_ORDER"){
         
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $orderId = isset($_POST['orderId']) ? trim($_POST['orderId']) : '26';
        $userId = isset($_POST['userId']) ? trim($_POST['userId']) : '1';
        $driverId =  isset($_POST['driverId']) ? trim($_POST['driverId']) :'48';
        $userType = isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $transactionNo =  isset($_POST['transactionNo']) ? trim($_POST['transactionNo']) :'PAS20112671176';
        $serviceMode =  isset($_POST['serviceMode']) ? trim($_POST['serviceMode']) :'PASAKAY';
        $description =  isset($_POST['description']) ? trim($_POST['description']) :'PABILI';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'gg@gmail.com';
        $reason =  isset($_POST['reason']) ? trim($_POST['reason']) :'1aasasasasasas';
        $image =  isset($_POST['image']) ? trim($_POST['image']) :'1aasasasasasas';
        $status =  isset($_POST['status']) ? trim($_POST['status']) :'1aasasasasasas';
        
        
        if($serviceMode == "PABILI"){
            
            $sql = "SELECT * FROM orders WHERE iOrderId = '". $orderId."' ";
            $statement = $db->query($sql);
            $orderdata = $statement ->fetchAll(); 
            
            if($orderdata[0]['iCancelledById'] == "0" || $orderdata[0]['iCancelledById'] == 0 || $orderdata[0]['iCancelledById'] == ""){
                
                unset($where);
                $cancelledUpdate['eCancelledBy'] = $userType;
                $cancelledUpdate['iCancelledById'] = (int)  $driverId;
                $cancelledUpdate['iStatusCode'] = "3010";
                $cancelledUpdate['vCancelReason'] = $reason;
            
                $where['iOrderId'] = $orderId ;
                    
                $result = myQuery("orders",  $cancelledUpdate, "update", $where);
                
                sendRequestToUser($orderdata[0]['iUserId'], "ORDER_CANCELLED","Pasakay booking cancelled.", "Your order has been cancelled by the delivery driver. \n*". $reason);
                
                setOrderLogs("3010", $orderId);
                
                
                $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $driverId."' ";
                $statement = $db->query($sql);
                $driverdata = $statement ->fetchAll(); 
                
                $tripId = $driverdata[0]['iTripId'];
                
                unset($where);
                $where['iDriverId'] = $driverId ;
                
                $CancelOrder['iTripId'] = 0;
                $CancelOrder['vTripStatus'] = "FINISHED";
                $CancelOrder['vLatitude'] = $sourceLat;
                $CancelOrder['vLongitude'] = $sourceLong;
                    
                $result = myQuery("register_driver",  $CancelOrder, "update", $where);
                
                
                unset($where);
                $where['iTripId'] = $tripId;
                $updatePreviousTrip['iActive'] =  "Cancelled" ;
                $updatePreviousTripResult = myQuery("trips",  $updatePreviousTrip, "update",  $where);
                
                
                $cancelled_report['vUserType'] = $userType;
                $cancelled_report['vTransactionType'] = "PASAKAY";
                $cancelled_report['iUserId'] = $driverId;
                $cancelled_report['vCancelledReason'] = $reason;
                $cancelled_report['vTransactionNo'] =  $transactionNo;
                $cancelled_report['vEmail'] =  $email;
                $cancelled_report['vDescription'] =  $description;
                $cancelled_report['vStatus'] =  $status;
                $cancelled_report['vImage'] =  $image;
                $cancelled_report['dDate'] = @date("Y-m-d H:i:s");
                
                $result4 = myQuery("cancelled_transactions", $cancelled_report, "insert");

                // $cancelled_report['vUserType'] = $userType;
                // $cancelled_report['vTransactionType'] = "PABILI";
                // $cancelled_report['iUserId'] = $driverId;
                // $cancelled_report['vCancelledReason'] = $reason;
                // $cancelled_report['vTransactionNo'] =  $transactionNo;
                // $cancelled_report['vEmail'] =  $email;
                // $cancelled_report['vDescription'] =  $description;
                // $cancelled_report['vStatus'] =  $status;
                // $cancelled_report['vImage'] =  $image;
         
                //
               // cancelledLogs($cancelled_report);
                
                 
    // // $cancelled_report['vUserType'] = "Driver";
    // // $cancelled_report['vTransactionType'] = "PABILI";
    // // $cancelled_report['iUserId'] ="48";
    // // $cancelled_report['vCancelledReason'] = "I dont want to complete this job.";
    // // $cancelled_report['vTransactionNo'] =  "PAB121627627";
    // // $cancelled_report['vEmail'] =  "laurencevegerbo@gmail.com";
    // // $cancelled_report['vDescription'] =  "sdssdd";
    // // $cancelled_report['vStatus'] =  "At the store";
    // // $cancelled_report['vImage'] =  "img_1217267186218.jpg";
  
    
  
                  
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "OKAY";
                $messageArray['serviceMode'] = $serviceMode;
                
                
            }else{
                
                
                
                
                          
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['serviceMode'] = $serviceMode;
                $messageArray['status'] =  "Failed";
                
              
            }
            
            
          
            
        }else if($serviceMode == "PASAKAY"){
              unset($where);
            
            $sql = "SELECT * FROM cab_booking WHERE iCabBookingId = '". $orderId."'";
            $statement = $db->query($sql);
            $bookingdata = $statement ->fetchAll(); 
            
            
            if( $bookingdata[0]['eStatus'] != "Cancelled"){

                $day = date('w');
                $week_start = date('Y-m-d', strtotime('-'.$day.' days'));
                $week_end = date('Y-m-d', strtotime('+'.(6-$day).' days'));

                $sql = "SELECT * FROM cancelled_transactions WHERE iUserId = '".$driverId."' AND  dDate BETWEEN date('". $week_start."') and date('".$week_end."')";
                $statement = $db->query($sql);
                $result = $statement ->fetchAll(); 

                $cancelledTransactions = count($result);

                unset($where);
                $cancelBookingUpdate['eStatus'] = "Cancelled";
                $cancelBookingUpdate['eCancelBy'] =   $userType; 
                $cancelBookingUpdate['dCancelDate'] =  @date("Y-m-d H:i:s"); 
                $cancelBookingUpdate['vCancelReason'] =  $description; 
                $cancelBookingUpdate['iCancelByUserId'] =  (int)$driverId;
                $cancelBookingUpdate['eViewNotif'] = 1;
                $cancelBookingUpdate['eNotifLevel'] = 1;
                unset($where);
                $where['iCabBookingId'] = $orderId ;
                    
                $result = myQuery("cab_booking", $cancelBookingUpdate, "update", $where);
                
                $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $driverId."' ";
                $statement = $db->query($sql);
                $driverdata = $statement ->fetchAll(); 
                
                $tripId = $driverdata[0]['iTripId'];
                
                unset($where);
                $where['iDriverId'] = $driverId ;
                
                $CancelOrder['iTripId'] = 0;
                $CancelOrder['vTripStatus'] = "FINISHED";
                $CancelOrder['vLatitude'] = $sourceLat;
                $CancelOrder['vLongitude'] = $sourceLong; 
                $CancelOrder['vDriverCancellationCount'] = $cancelledTransactions;   
                $result = myQuery("register_driver",  $CancelOrder, "update", $where);
                
                sendRequestToUser( $bookingdata[0]['iUserId'], "CANCELLED", "Pasakay booking cancelled.", "Sorry your booking has been cancelled. \n*". $reason); 
                
                unset($where);
                $where['iTripId'] = $tripId;
                $updatePreviousTrip['iActive'] =  "Cancelled" ;
                $updatePreviousTripResult = myQuery("trips",  $updatePreviousTrip, "update",  $where);
                
                
                unset($where);
                $where['iUserId'] = $bookingdata[0]['iUserId'];
                $user_status['vTripStatus'] = "NONE";
                $user_status['iTripId'] = 0;
                
                $result3 = myQuery("register_user", $user_status, "update", $where);
                
                
                $cancelled_report['vUserType'] = $userType;
                $cancelled_report['vTransactionType'] = "PASAKAY";
                $cancelled_report['iUserId'] = $driverId;
                $cancelled_report['vCancelledReason'] = $reason;
                $cancelled_report['vTransactionNo'] =  $transactionNo;
                $cancelled_report['vEmail'] =  $email;
                $cancelled_report['vDescription'] =  $description;
                $cancelled_report['vStatus'] =  $status;
                $cancelled_report['vImage'] =  $image;
                $cancelled_report['dDate'] = @date("Y-m-d H:i:s");
                
                $result4 = myQuery("cancelled_transactions", $cancelled_report, "insert");

               

                if($cancelledTransactions >= 0){
                    $cancelBookingUpdate['eViewNotif'] = 1;
                    $cancelBookingUpdate['eNotifLevel'] = 1;
                    unset($where);
                    $where['iCabBookingId'] = $orderId ;
                        
                    $result = myQuery("cab_booking", $cancelBookingUpdate, "update", $where);
                }


                
                    
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['serviceMode'] = $serviceMode;
                $messageArray['status'] =  "OKAY";
                
                
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['serviceMode'] = $serviceMode;
                $messageArray['status'] =  "Failed";
            }
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['serviceMode'] = "Cannot Identify";
            $messageArray['status'] =  "Failed";
        }
        
    
        
      
        
        echo json_encode( $messageArray);
       
        
     }
     
     
    if($servicetype == "LOAD_REORDER"){
        
        unset($messageArray);
        
         
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong ']) :'';
        $mainStore  = isset($_POST['mainStore']) ? trim($_POST['mainStore']) :'';
        $storeId  = isset($_POST['StoreId']) ? trim($_POST['StoreId']) :'3';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) :'3';
        
        $language = getLanguage("English");
        
        $menu = array();
        $products = array();
        
        
        $products = array();
        
        $sql2 = "SELECT * FROM orders WHERE iOrderId = '".$orderId."'";
        
        $statement = $db->query($sql2);
        
        $reOrderData = $statement ->fetchAll();
        
        
        
          
        $sql3 = "SELECT * FROM order_details WHERE iOrderId = '".$orderId."'";
        
        $statement = $db->query($sql3);
        
        $reOrderDetails = $statement ->fetchAll();
        
      
        
        
        

        
        
        
        $sql = "SELECT vMenu_$language as Menu FROM food_menu as fm WHERE fm.iCompanyId = '" . $storeId . "' AND fm.eStatus='Active' AND (select count(iMenuItemId) from menu_items as mi where mi.iFoodMenuId=fm.iFoodMenuId AND mi.eStatus='Active' AND mi.eAvailable = 'Yes') > 0 ORDER BY fm.iDisplayOrder ASC";
           
        $statement = $db->query($sql); 

        $result = $statement ->fetchAll(); 
        
        $menu = $result;
      
        
        
        $sqlf = "SELECT mi.iMenuItemId as itemId, mi.iFoodMenuId as itemMenuId, fm.vMenu_EN as Menu, mi.vItemType_EN as itemName, mi.vItemDesc_EN as itemDesc, mi.fPrice as itemPrice, mi.vImage, mi.iDisplayOrder, mi.vHighlightName
        FROM menu_items as mi LEFT JOIN food_menu as fm on mi.iFoodMenuId = fm.iFoodMenuId WHERE fm.iCompanyId = $storeId AND mi.eStatus='Active' AND mi.eAvailable = 'Yes'ORDER BY mi.iDisplayOrder ASC";
        
        $statement = $db->query($sqlf);
        
        $result = $statement ->fetchAll(); 
        
        $products = $result;
        
        
        for($x = 0; $x < count($result) ; $x++){
            
        
            $products[$x]['vImage'] =  "http://mallody.com.ph/grab/webimages/upload/MenuItem/". $products[$x]['vImage'];
            
            $iMenuItemId = $products[$x]['itemId'];
            
            $sql = "SELECT iOptionId,vOptionName,fPrice,eOptionType,eDefault FROM menuitem_options WHERE iMenuItemId = '" . $iMenuItemId . "' AND eStatus = 'Active'";
            
            $statement = $db->query($sql);
        
            $menuOptions = $statement ->fetchAll(); 
            
            if (count( $menuOptions) > 0) {

                for ($i = 0; $i < count( $menuOptions); $i++) {
        
                    $fPrice =  $menuOptions[$i]['fPrice'];
        
                    $fUserPrice = number_format($fPrice * $Ratio, 2);
        
                    $fUserPriceWithSymbol = $currencySymbol . " " . $fUserPrice;
        
                    $menuOptions[$i]['fUserPrice'] = $fUserPrice;
        
                    $menuOptions[$i]['fUserPriceWithSymbol'] = $fUserPriceWithSymbol;
        
                    if ($menuOptions[$i]['eOptionType'] == "Options") {
        
                        $suboptions['options'][] =  $menuOptions[$i];
        
                    }
                    
                    if ( $menuOptions[$i]['eOptionType'] == "Special") {
                        
                        if( $menuOptions[$i]['eLabel'] == "Sugar Level"){
                             $suboptions['sugarlevel'][] =  $menuOptions[$i];
                        }
                        
                        if( $menuOptions[$i]['eLabel'] == "Ice Level"){
                             $suboptions['icelevel'][] =  $menuOptions[$i];
                        }
        
        
                    }
                    
                    if ( $menuOptions[$i]['eOptionType'] == "Size Level") {
        
                       $suboptions['size'][] =  $menuOptions[$i];
        
                    }
                    
                     if ( $menuOptions[$i]['eOptionType'] == "Flavor Level") {
        
                       $suboptions['flavor'][] =  $menuOptions[$i];
        
                    }
        
        
        
                    if ( $menuOptions[$i]['eOptionType'] == "Addon") {
        
                       $suboptions['addon'][] =  $menuOptions[$i];
        
                    }
        
                }
                
               
        
            }

            
            $products[$x]['customization'] = $suboptions;
           
            $products[$x]['currencySymbol'] = "&#x20B1;";
            
            $suboptions = array();
        }
        
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Ok";
        $messageArray['categories'] =  $menu;
        $messageArray['products'] = $products;
        
        echo json_encode( $messageArray) ;
          
    }
    
    // sendResetEmail("laurencevegeranO@gmail.com", "Laurence Vegerano", "121625652323", "User", "55");
    // sendPabiliInvoice(29);
    
    
//   $servicetype = "LOAD_CALLER_PHOTO";
     
     if($servicetype == "LOAD_CALLER_PHOTO"){
        
        unset($messageArray);
        unset($where);
        
         
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong ']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'56';
        $userType  = isset($_POST['userType']) ? trim($_POST['userType']) :'User';

        
        if( $userType == "Driver"){
            
           $sql = "SELECT vImage FROM register_driver WHERE iDriverId = '". $userId."'";
            
            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Ok";
            $messageArray['DriverImage'] = $result[0]['vImage'];
            
        }else{
            
            $sql = "SELECT vImgName FROM register_user WHERE iUserId = '". $userId."'";
            
            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Ok";
            $messageArray['vImgName'] = $result[0]['vImgName'];
            
        }
        
        echo json_encode($messageArray);
        
        
     
     }
     
     
    //$servicetype = "SUMBIT_REPORT_REQUEST";
     
     if($servicetype == "SUMBIT_REPORT_REQUEST"){
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong ']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'56';
        $userType  = isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $customerId  = isset($_POST['customerId']) ? trim($_POST['customerId']) :'User';
        $serviceMode = isset($_POST['serviceMode']) ? trim($_POST['serviceMode']) :'PASAKAY';
        $transactionNo = isset($_POST['transactionNo']) ? trim($_POST['transactionNo']) :'PASAKAY';
        $description= isset($_POST['description']) ? trim($_POST['description']) :'User';
        $tripStatus= isset($_POST['status']) ? trim($_POST['status']) :'User';
        $email= isset($_POST['email']) ? trim($_POST['email']) :'User';
        $issue= isset($_POST['issue']) ? trim($_POST['issue']) :'Uimages ';
        $images = isset($_POST['image']) ? trim($_POST['image']) :'User';



        if($userType == "User"){
            
            $data_report['vUserType'] = $userType;
            $data_report['vServiceMode  '] = $serviceMode;
            $data_report['vEmail'] = $email;
            $data_report['vIssue'] =  $issue;
            $data_report['vTransactionNo'] = $transactionNo;
            $data_report['vDescription'] =  $description;
            $data_report['iUserId'] = $userId;
            $data_report['vImage'] =  $images;
            $data_report['dDateCreated'] = @date("Y-m-d H:i:s");
            
            $result = myQuery(" trasaction_reports",  $data_report, "insert");
        }else{

            $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '". $transactionNo."'";
            
            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 

        
            
            $data_report['vUserType'] = $userType;
            $data_report['vServiceMode'] = $serviceMode;
            $data_report['vEmail'] = $email;
            $data_report['vIssue'] =  $issue;
            $data_report['vTransactionNo'] = $transactionNo;
            $data_report['vDescription'] =  $description;
            $data_report['iDriverId'] = $result[0]['iDriverId'];
            $data_report['iUserId'] =$result[0]['iUserId'];
            $data_report['vTripStatus'] = $tripStatus;
            $data_report['vImage'] =  $images;
            $data_report['dDateCreated'] = @date("Y-m-d H:i:s");
            
            $result = myQuery(" trasaction_reports",  $data_report, "insert");
        }
        
      
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Ok";
      
            
        echo json_encode($messageArray);
        
     }
     
   //  $servicetype = "SET_DRIVER_STATUS_LOG";
     
    if($servicetype == "SET_DRIVER_STATUS_LOG"){
        unset($messageArray);
        unset($where);
        
         
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong ']) :'';
        $driverId  = isset($_POST['driverId']) ? trim($_POST['driverId']) :'24';
        $status  = isset($_POST['status']) ? trim($_POST['status']) :'Online';
        $session  = isset($_POST['session']) ? trim($_POST['session']) :'12121212';
     
        
        if($status == "Online"){
            
            $session = getToken(10);
            
            
            unset($where);
            $where['iDriverId'] = $driverId;
            $updateDriver['vAvailability'] = "Available";
            $updateDriver['tOnline'] = @date("Y-m-d H:i:s");
            $driverResult = myQuery("register_driver", $updateDriver, "update", $where);
            
            
            
            // $where['iDriverId'] = $driverId;
            // $updateDriver['vAvailability'] = "Available";
            // $updateDriver['tOnline'] = @date("Y-m-d H:i:s");
            // $result = myQuery("register_driver",  $updateDriver, "update");
            
            
            
            $data_log['iDriverId'] = $driverId;
            $data_log['dLoginDateTime'] = @date("Y-m-d H:i:s");
            $data_log['SessionLog'] = $session;
            
            $result = myQuery("driver_log_report",  $data_log, "insert");
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Ok";
            $messageArray['session'] = $session;
            
            
        }else{
            
            
            unset($where);
            $where['iDriverId'] = $driverId;
            $updateDriver['vAvailability'] = "Not Available";
            $updateDriver['tLastOnline'] = @date("Y-m-d H:i:s");
            $driverResult = myQuery("register_driver", $updateDriver, "update", $where);
            
            
            $where['SessionLog'] = $session;
            $where['iDriverId'] = $driverId;
        
            $data_log_update['dLogoutDateTime'] =  @date("Y-m-d H:i:s");
            $result = myQuery("driver_log_report", $data_log_update, "update",  $where);
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Ok";
            $messageArray['session'] = $session;
            
        }
        
        echo json_encode($messageArray);
      
         
    }
     
    
    
    if($servicetype == "LOAD_REWARD_BALANCE"){
        
        unset($messageArray);
        unset($where);
        unset($fieldname);
         
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong ']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'24';
     
        $sql = "SELECT * FROM register_user WHERE iUserId = '".  $userId."'";
           
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll(); 
        
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Ok";
        $messageArray['rewardpoints'] = $profileData[0]['fRewardPointsBalance'];
        $messageArray['profileData'] = $profileData;
            
        echo json_encode($messageArray);
      
         
    }
    
    //$servicetype = "LOAD_REWARD_TRANACTIONS";
    
     if($servicetype == "LOAD_REWARD_TRANACTIONS"){
        
        unset($messageArray);
        unset($where);
        unset($fieldname);
         
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong ']) :'';
        $userId  = isset($_POST['userId']) ? trim($_POST['userId']) :'56';
        $userType  = isset($_POST['vUserType']) ? trim($_POST['vUserType']) :'User';
        $transactionType = isset($_POST['transactionType']) ? trim($_POST['transactionType']) :'User';
        
        $sql = "SELECT * FROM rewards_user_logs WHERE iUserId = '".$userId."' AND vUserType = '".$userType."' AND (vTransactionType = 'PABILI' || vTransactionType = 'PASAKAY' || vTransactionType = 'REFERRAL') ORDER BY dDateCreated DESC"  ;
                
        $statement = $db->query($sql); 

        $rewardTransactionData  = $statement ->fetchAll();  
            
        
        if(count($rewardTransactionData)>0){
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Ok";
            $messageArray['result'] =  $rewardTransactionData;
        }else{
             $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Error";

        }
     
       
            
        echo json_encode($messageArray);
      
         
    }
    
    
   // $servicetype = "LOAD_REWARD_TRANSACTION_DETAILS";
    
    if($servicetype == "LOAD_REWARD_TRANSACTION_DETAILS"){
        
        // echo 'hello';
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $transactionId =  isset($_POST['transactionId']) ? trim($_POST['transactionId']) :'RP20111791913';
        $transactionType =  isset($_POST['transactionType']) ? trim($_POST['transactionType']) :'REFERRAL';
        
        if($transactionType  == "PABILI"){
            
            //ORDER DATA
                
            $sql = "SELECT * FROM orders WHERE vOrderNo = '". $transactionId."'";
    
            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
            
            unset($where);
            $where['iCompanyId'] = $result[0]['iCompanyId'];
            $companyAddress = myQuery("company", array("vCompany", "vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
            
             
            unset($where);
            $where['iUserId'] = $result[0]['iUserId'];
            $userAddress = myQuery("register_user", array("vName"), "selectall",  $where);
            
            
            $storeName =  $companyAddress[0]['vCompany'];
            $storeAddress =  $companyAddress[0]['vRestuarantLocation'];
            
            $date = date_create($result[0]['dDate']);

            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";

            $messageArray['transactionId'] =  $result[0]['vOrderNo'];
            $messageArray['origin'] = $storeName;
            $messageArray['originAddress'] =  $storeAddress;
            
            $messageArray['destination'] = $userAddress[0]['vName'];
            $messageArray['destinationAddress'] = $result[0]['vDeliveryAddress'];

            
            $messageArray['destination2'] = $userAddress[0]['vName'];
            $messageArray['destinationAddress2'] = $result[$i]['vDeliveryAddress_2'];
            
            $messageArray['date'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['orderPrice'] = $result[0]['fSubTotal'];
            $messageArray['deliveryCharge'] = $result[0]['fDeliveryCharge'];
            $messageArray['totalOrderAmount'] = $result[0]['fTotalGenerateFare'];
            $messageArray['totalEarnings'] = $result[0]['fCommision'];
            $messageArray['totalEarnings'] = $result[0]['fCommision'];
            $messageArray['totalTransactionFee'] = $result[0]['fWalletDebit'];
            $messageArray['totalEarnPoints'] = (float)$result[0]['fWalletDebit'] * constants::REWARDS_POINTS_RATE;
            
       
            
        
            echo json_encode( $messageArray);
            
            
        }else if($transactionType  == "PASAKAY"){

            
            
            
            $sql = "SELECT * FROM cab_booking WHERE vBookingNo = '". $transactionId."'";
    
            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
            
            $bookingData = array();

            $date = date_create($result[0]['dBooking_date']);

            $bookingData [$i]['bookingDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");


            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";

            $messageArray['transactionId'] =  $result[0]['vBookingNo'];
            $messageArray['origin'] = "";
            $messageArray['originAddress'] =  $result[0]['vSourceAddress'];
            
            $messageArray['destination'] = "";
            $messageArray['destinationAddress'] = $result[0]['tDestAddress'];

            $messageArray['destination2'] = "";
            $messageArray['destinationAddress2'] = $result[0]['tDestAddress'];

            $messageArray['date'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['orderPrice'] = $result[0]['fTripGenerateFare'];
            $messageArray['deliveryCharge'] = $result[0]['fWaitingCharge'];
            $messageArray['totalOrderAmount'] = $result[0]['fTripTotalAmountFare'];
            $messageArray['totalEarnings'] = $result[0]['fCommision'];
            $messageArray['totalTransactionFee'] = $result[0]['fWalletDebit'];
            $messageArray['totalEarnPoints'] = (float)$result[0]['fWalletDebit'] * constants::REWARDS_POINTS_RATE;
            

            echo json_encode($messageArray);
            
            
  
        
        }else if($transactionType  == "REFERRAL"){

            
            $sql = "SELECT * FROM rewards_user_logs WHERE vTransactionNo = '". $transactionId."'";
    
            $statement = $db->query($sql);
            
            $result = $statement ->fetchAll(); 
        
            $date = date_create($result[0]['dDateCreated']);
            
            
            $sql = "SELECT vName, vPhone FROM register_user WHERE iUserId = '". $result[0]['iSenderId']."'";
    
            $statement = $db->query($sql);
            
            $senderData = $statement ->fetchAll(); 



            $messageArray['response'] = 1;
            $messageArray['service'] = "";
            $messageArray['status'] = "Okay";

            $messageArray['transactionId'] = $result[0]['vTransactionNo'];
            $messageArray['origin'] = "";
            $messageArray['originAddress'] =  "";
            
            $messageArray['destination'] = "";
            $messageArray['destinationAddress'] = "";

            $messageArray['destination2'] = "";
            $messageArray['destinationAddress2'] = "";

            $messageArray['date'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['fPoints'] = $result[0]['fPoints'];
            $messageArray['vPhone'] = $senderData[0]['vPhone'];
            $messageArray['vName'] = $senderData[0]['vName'];
            

            echo json_encode($messageArray);
            
            
        }
      
        
    }
    
    
    
     
    if($servicetype == "START_WAITING_TIME"){
        
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'56';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $bookingId = isset($_POST['bookingId']) ? trim($_POST['bookingId']) :'User';
        $driverId = isset($_POST['driverId']) ? trim($_POST['driverId']) :'User';
        
        $where['iCabBookingId'] = $bookingId;
        $where['iDriverId'] = $driverId;
    
        $updateWaiting['eWaitingStatus'] = "Start waiting";
        $updateWaiting['tWaitingSTartTime'] = @date("Y-m-d H:i:s");
        
        $result = myQuery("cab_booking", $updateWaiting, "update",  $where);
        
    
        
        sendRequestToUser($userId, "WAITING_TIME_STARTED", "Pasakay Booking.", "Driver is waiting..");

        

    }
   
   
    if($servicetype == "STOP_WAITING_TIME"){
        
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'56';
        $waitingTime =  isset($_POST['waitingTime']) ? trim($_POST['waitingTime']) :'56';
        $bookingId = isset($_POST['bookingId']) ? trim($_POST['bookingId']) :'56';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $driverId = isset($_POST['driverId']) ? trim($_POST['driverId']) :'User';
        
        $where['iCabBookingId'] = $bookingId;
        $where['iDriverId'] = $driverId;
    
        $updateWaiting['eWaitingStatus'] = "Stop waiting";
        $updateWaiting['tWaitingStartTime'] = @date("Y-m-d H:i:s");
        $updateWaiting['vWaitingTime'] = $waitingTime;
        $result = myQuery("cab_booking", $updateWaiting, "update",  $where);
        
        unset($where);
        $where['iCabBookingId'] = $bookingId;
        $bookingData = myQuery("cab_booking", array("fTripGenerateFare"), "selectall", $where);
        
        
        $WaitingTime_in_minutes = ((int) $waitingTime % 3600) / 60;
            
        $messageArray['WaitingTime_in_minutes'] =  intval($WaitingTime_in_minutes);
        
        $WaitingTime_in_seconds = ((int) $waitingTime % 60);
        
        $messageArray['WaitingTime_in_seconds'] = $WaitingTime_in_seconds;
        
        if($WaitingTime_in_seconds >= 1){
            
            $final_WaitingTime_in_minutes = intval($WaitingTime_in_minutes)+1;
        }
        
        $totalWaitingFee =   $final_WaitingTime_in_minutes * constants::WAITINGTIME_RATE_PER_MIN;
        
        $totalFareAmount =  (float) $bookingData[0]['fTripGenerateFare'] +  $totalWaitingFee ;
        
        unset($where);
        $where['iCabBookingId'] = $bookingId;
        $booking_status['vWaitingTime'] = $waitingTime;
        $booking_status['fWaitingCharge'] = (float)$totalWaitingFee;
        $booking_status['fTripTotalAmountFare'] = (float)  roundOff($totalFareAmount);
        $bookingUpdate = myQuery("cab_booking", $booking_status, "update", $where);
        
        
        // unset($where);
        // $where['iCabBookingId'] = $bookingId;
        // $booking_status['vWaitingTime'] = $waitingTime;
        // $bookingUpdate = myQuery("cab_booking", $booking_status, "update", $where);
        
        sendRequestToUser($userId, "WAITING_CHARGE_ADDED","Pasakay Booking.", "Waiting charge added.");

    
   }
   
    if($servicetype == "GET_COMPLETE_ADDRESS"){
        

        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
       
        $address = get_CompleteAddress($sourceLat, $sourceLong);
        
        
        if($address["city"] == null){
            
            $sql = "SELECT * FROM refcitymun WHERE provCode = '".$code ."'";
    
            $statement = $db->query($sql);
                
            $cities = $statement ->fetchAll(); 
            
        }
        
        if($address["state"] ==null ){
            
            $sql = "SELECT * FROM refprovince WHERE regCode = '".$code ."'";
        
            $statement = $db->query($sql);
                
            $provinces = $statement ->fetchAll(); 
        }
        
          
        if($address["region"] == null ){
            
             $sql = "SELECT * FROM refregion";
    
            $statement = $db->query($sql);
                
            $regions = $statement ->fetchAll(); 
            
        }
        
        $messageArray['response'] = "1";
        $messageArray['city'] =  $address["city"];
        $messageArray['state'] =  $address["state"];
        $messageArray['region'] =  $address["region"];
        $messageArray['country'] = $address["country"];
        
        echo json_encode($messageArray);
    
   }
   
   
  // $servicetype = "GET_REGIONS_PROVINCES";
   
    if($servicetype == "GET_REGIONS_PROVINCES"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '1212';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'212';
        $key  = isset($_REQUEST['key']) ? trim($_REQUEST['key']) :'REGION';
        $code  = isset($_REQUEST['code']) ? trim($_REQUEST['code']) :'REGION';
        
        if( $key == "REGION"){
            
             $sql = "SELECT * FROM refregion";
    
            $statement = $db->query($sql);
                
            $regions = $statement ->fetchAll(); 
            
        }
        
        if( $key == "PROVINCE"){
            
            $sql = "SELECT * FROM refprovince WHERE regCode = '".$code ."'";
        
            $statement = $db->query($sql);
                
            $provinces = $statement ->fetchAll(); 
            
            
        }
        
        if( $key == "MUNICIPALITY"){
           
             
            $sql = "SELECT * FROM refcitymun WHERE provCode = '".$code ."'";
    
            $statement = $db->query($sql);
                
            $cities = $statement ->fetchAll(); 
        }
        
        if( $key == "BARANGAY"){
            
            $sql = "SELECT * FROM refbrgy  WHERE citymunCode =  '".$code ."'";
    
            $statement = $db->query($sql);
                
            $barangays = $statement ->fetchAll(); 
            
        }
       
        
       
        
        
       
        $messageArray['response'] = "1";
        $messageArray['type'] = $key;
        $messageArray['regions'] =  $regions;
        $messageArray['provinces'] =  $provinces;
        $messageArray['cities'] =  $cities;
        $messageArray['barangays'] = $barangays;
        
        echo json_encode($messageArray);
        
        
       
    
   }
   
   
  // $servicetype = "LOAD_NOTIFICATIONS";
   
    if($servicetype == "LOAD_NOTIFICATIONS"){
        
       // ini_set('display_errors',1);
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'192';
        $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'Store';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        
        $sql = "SELECT * FROM notifications  WHERE iUserId = '". $userId ."' AND vUserType ='". $userType ."'  ORDER BY dDateCreated DESC";
    
        $statement = $db->query($sql);
            
        $notifData = $statement ->fetchAll(); 
        
        if($userType == "User"){
             
            $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
        }else
        if($userType == "Store"){
             
            $sql = "SELECT * FROM company WHERE iCompanyId = '". $userId."'";
           
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
        }else{
            $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $userId."'";
           
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
        }
        
      
        
        if(count($notifData)  > 0 ){
            
            for($x = 0 ;  $x < count($notifData) ;  $x++){
                 $notifData[$x]['dDateCreated'] = timeAgo($notifData[$x]['dDateCreated'])." ";
            }
            
            $messageArray['response'] = 1;
            $messageArray['type'] = $servicetype;
            $messageArray['notificationCounter'] = countNotifications($userId, $userType);
            $messageArray['result'] = $notifData;
            $messageArray['profileData'] = $profileData;
    
        }else{
              
            $messageArray['response'] = 0;
            $messageArray['type'] = $servicetype;
        }
        
        
       
        
        
        // if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
             
              
        // }
        
       
         echo json_encode($messageArray);
        
        
    }
    
    //$servicetype = "OPEN_NOTIFICATION";
    if($servicetype == "OPEN_NOTIFICATION"){
         
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'56';
        $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'';
        $notifId  = isset($_REQUEST['notifId']) ? trim($_REQUEST['notifId']) :'';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        
        
        $where['iNotificationId'] = $notifId;
        $updateNotification['eStatus'] = "seen";
        $result = myQuery("notifications",   $updateNotification, "update",  $where);
        
        
        $sql = "SELECT * FROM notifications  WHERE iUserId =  '". $userId ."' AND vUserType =  '".  $userType ."' AND iNotificationId = '".$notifId."' ORDER BY dDateCreated DESC";
    
        $statement = $db->query($sql);
            
        $notifData = $statement ->fetchAll(); 
        
        $messageArray['response'] = 1;
        $messageArray['type'] = $servicetype;
        $messageArray['notifId'] = $notifData[0]['iNotificationId'];
        $messageArray['vType'] = $notifData[0]['vType'];
        $messageArray['vTitle'] = $notifData[0]['vTitle'];
        $messageArray['vDescription'] = $notifData[0]['vDescription'];
        $messageArray['vImage'] = $notifData[0]['vImage'];
        $messageArray['vUrl'] = $notifData[0]['vUrl'];
        $messageArray['vIntent'] = $notifData[0]['vIntent'];
        $messageArray['notificationCounter'] = countNotifications($userId, $userType);
        
        if($userType == "User"){
             
            $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
        }else
        if($userType == "Store"){
             
            $sql = "SELECT * FROM company WHERE iCompanyId = '". $userId."'";
           
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
        }else{
            $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $userId."'";
           
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
        }
        
        
        // if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
        //           $messageArray['tDeviceData'] = $profileData[0]['tDeviceData'];
              
        // }
       
        echo json_encode($messageArray);
         
    }
    
    if($servicetype == "LOAD_NOTIFICATION_DATA"){
         
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'56';
        $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'';
        $notifId  = isset($_REQUEST['notifId']) ? trim($_REQUEST['notifId']) :'';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        
        
        $where['iNotificationId'] = $notifId;
        $updateNotification['eStatus'] = "seen";
        $result = myQuery("notifications",   $updateNotification, "update",  $where);
        
        
        $sql = "SELECT * FROM notifications  WHERE iUserId =  '". $userId ."' AND vUserType =  '".  $userType ."' AND iNotificationId = '".$notifId."' ORDER BY dDateCreated DESC";
    
        $statement = $db->query($sql);
            
        $notifData = $statement ->fetchAll(); 
        
        $messageArray['response'] = 1;
        $messageArray['type'] = $servicetype;
        $messageArray['notifId'] = $notifData[0]['iNotificationId'];
        $messageArray['vType'] = $notifData[0]['vType'];
        $messageArray['date'] = $notifData[0]['dDateCreated'];
        $messageArray['vTitle'] = $notifData[0]['vTitle'];
        $messageArray['vDescription'] = $notifData[0]['vDescription'];
        $messageArray['vImage'] = $notifData[0]['vImage'];
        $messageArray['vUrl'] = $notifData[0]['vUrl'];
        $messageArray['vIntent'] = $notifData[0]['vIntent'];
        $messageArray['notificationCounter'] = countNotifications($userId, $userType);
        
       if($userType == "User"){
             
            $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
        }else{
            $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $userId."'";
           
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
        }
       
        echo json_encode($messageArray);
         
    }
    
    
    
   
    
    
   // $servicetype ="UPDATE_SINGLE_ITEM_QUANITY_PRICE";
     if($servicetype == "UPDATE_SINGLE_ITEM_QUANITY_PRICE"){
          unset($where);
         
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'56';
        $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'';
        $orderId = isset($_REQUEST['orderId']) ? trim($_REQUEST['orderId']) :'34';
        $orderDetailId = isset($_REQUEST['orderDetailId']) ? trim($_REQUEST['orderDetailId']) :'44';
        $qty = isset($_REQUEST['quantity']) ? trim($_REQUEST['quantity']) :'2';
        $price = isset($_REQUEST['price']) ? trim($_REQUEST['price']) :'150';
        $manually = isset($_REQUEST['manuall']) ? trim($_REQUEST['manually']) :'No';
        
        

        
        if( $manually == "Yes"){
            
           
            $update_item['fOriginalPrice'] = (float)$price;
            $update_item['fPrice'] = (float)$price;
            $update_item['fSubTotal'] = (float)$price * (int) $qty ;
            $update_item['iQty'] = (int) $qty ;
            
             unset($where);
            $where['iOrderDetailId'] = $orderDetailId;
            $result = myQuery("order_details",  $update_item, "update",  $where);
            
            $messageArray['response'] = 1;
            $messageArray['type'] = $servicetype;
      
            
            echo json_encode($messageArray);
            
            
            // $messageArray['UPDATE DRIVER'] = "SUCCESSS";
        }else{
            
            
               unset($where);
            $where['iOrderDetailId'] =  $orderDetailId;
            $itemDetail = myQuery("order_details", array("vOptionPrice","vAddonPrice", "vDrinksPrice", "vFlavorPrice",  "vSizePrice"), "selectall",  $where);
            
            if($itemDetail[0]['vOptionPrice'] != "0.0" || $itemDetail[0]['vOptionPrice'] != "0"){
                 $update_item['vOptionPrice'] = $price;
            }
            
            if($itemDetail[0]['vFlavorPrice'] != "0.0" || $itemDetail[0]['vFlavorPrice'] != "0"){
                 $update_item['vFlavorPrice'] = $price;
            }
            
            if($itemDetail[0]['vSizePrice'] != "0.0" || $itemDetail[0]['vFlavorPrice'] != "0"){
                 $update_item['vSizePrice'] = $price;
            }
            
            $update_item['fOriginalPrice'] = (float)$price;
            $update_item['fPrice'] = (float)$price;
            $update_item['fSubTotal'] = ((float)$price * (int) $qty)+ (float)$itemDetail[0]['vAddonPrice'] + (float)$itemDetail[0]['vDrinksPrice']  ;
            $update_item['iQty'] = (int)$qty ;
            
             unset($where);
            $where['iOrderDetailId'] = $orderDetailId;
            $result = myQuery("order_details",  $update_item, "update",  $where);
            
            $messageArray['response'] = 1;
            $messageArray['type'] = $servicetype;
      
            
             echo json_encode($messageArray);
            
            
        }
        
         
     }
     
     
    //$servicetype = "APPROVE_CASH_IN";
         
    if($servicetype == "APPROVE_CASH_IN"){
           unset($where);
            unset($messageArray);
        
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $driverId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'24';
        $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'Driver';
        $transactionNo = isset($_REQUEST['transactionNo']) ? trim($_REQUEST['transactionNo']) :'CI201029231783';
        $cashinId = isset($_REQUEST['cashinId']) ? trim($_REQUEST['cashinId']) :'5';
        $amount = isset($_REQUEST['amount']) ? trim($_REQUEST['amount']) :'300';
        
        
        unset($where);
        $where['iCashInId'] = $cashinId;
        $where['iUserId'] = $driverId;
        $cashinData = myQuery("cashin_transactions", array("vAmount","iCashInId", "vTransactionNo"), "selectall",  $where);
        
        if(count($cashinData) > 0){
            
             unset($where);
            $where['iCashInId'] = $cashinId;
            $where['iUserId'] = $userId;
            $where['vUserType'] = $userType;
            $approve['eStatus'] = "Approved";
            $approve['dDate'] = @date("Y-m-d H:i:s");
        
            $result = myQuery("cashin_transactions",  $approve, "update", $where);
            
            unset($where);
            $where['iDriverId'] = $driverId ;
            $driverData = myQuery("register_driver", array("vWalletBalance"), "selectall",  $where);
          
            $newWalletBalance = (float) $driverData[0]['vWalletBalance'] + (float) $cashinData[0]['vAmount'];
           
            
          
          
            unset($where);
            $where['iDriverId'] = $driverId;
            $update_walletB['vWalletBalance'] = $newWalletBalance;
        
            $result = myQuery("register_driver", $update_walletB, "update", $where);
            
        
            $walletlogs['iDriverId'] = $driverId;
            $walletlogs['vUserType'] =  $userType;
            $walletlogs['vTransactionType'] = "CASH IN";
            $walletlogs['vLabel'] = "- Top up";
            $walletlogs['vDescription'] = "";
            $walletlogs['vTransactionNo'] =  $cashinData[0]['vTransactionNo'];
            $walletlogs['fAmount'] = (float)$cashinData[0]['vAmount'] ;
            $walletlogs['fWalletBalance'] = (float) $newWalletBalance;
            $walletlogs['vReceiveBy'] = "";
            $walletlogs['iReceiveId'] = "";
            $walletlogs['eStatus'] = "Completed";
            $rewardslogs['dDateCreated'] = @date("Y-m-d H:i:s");
                  
            $result = myQuery("user_wallet_logs",  $walletlogs, "insert");
            
            sendRequestToUser($driverId, "CASH_IN_ADDED","Trikoins Cash in.", "Your cash in has been successfull topped up.");
            
            $messageArray['response'] = 1;
            $messageArray['type'] = $servicetype;
            $messageArray['status'] = $cashinData[0]['vAmount']." Loaded succedssfully";
        
        //     $SMSmessage = "Your cash in amount ".$cashinData[0]['vAmount']." to Trikaroo Trikoins has been successfully top up.";
            
        //     $account_sid = constants::Account_SID;
        //  $auth_token = constants::Auth_Token;
        //  $twilioMobileNum = constants::TwilioMobileNum;
        
        
        //  $client = new Services_Twilio($account_sid, $auth_token);
        
        //     $sms = $client->account->messages->sendMessage($twilioMobileNum,number_PH($mobileNUmber),$SMSmessage);
            
            $messageArray['response'] = 1;
            $messageArray['service'] =  $mobileNUmber;
            $messageArray['status'] =  "Okay";
            $messageArray['code'] =  $code;
            
            
            echo json_encode($messageArray);
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['type'] = $servicetype;
            $messageArray['status'] = "Transaction doesn't exist";
            
            
            echo json_encode($messageArray);
            
        }
    
       
        
        
        
    }
    
    if($servicetype == "DELETE_NOTIFICATION_ITEM"){
         unset($where);
            unset($messageArray);
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'24';
        $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'Driver';
        $notificationId = isset($_REQUEST['notificationId']) ? trim($_REQUEST['notificationId']) :'CI201029231783';
        
        
        $id = explode(",",  $notificationId);
        
        for($x = 0 ; $x < count($id) ; $x++){
            
            $sql = "DELETE FROM notifications WHERE iNotificationId = '". $id[$x]."'";

            $statement = $db->query($sql);
            
            $result = $statement ->execute(); 
            
        }
        
        $messageArray['response'] = 1;
        $messageArray['service'] =  $mobileNUmber;
        $messageArray['status'] =  "Okay";
        $messageArray['notificationCounter'] = countNotifications($userId, $userType);
       
        
        echo json_encode($messageArray);
        
        
    }
   // $servicetype = "REORDER_ITEMS";
    
    if($servicetype == "REORDER_ITEMS"){
         unset($where);
            unset($messageArray);
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'24';
        $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'Driver';
        $orderId = isset($_REQUEST['orderId']) ? trim($_REQUEST['orderId']) :'40';
        
        $sql = "SELECT * FROM order_details WHERE iOrderId = '". $orderId."'";

        $statement = $db->query($sql);
        
        $result = $statement ->fetchAll();  
        
        
        $messageArray['response'] = 1;
        $messageArray['result'] = $result;
        $messageArray['status'] =  "Okay";
     
       
        
        echo json_encode($messageArray);
        
        
    }
    
    if($servicetype == "SEND_PABILI_INVOICE"){
        unset($where);
        unset($messageArray);
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $orderId  = isset($_REQUEST['orderId']) ? trim($_REQUEST['orderId']) :'24';
 
        
        sendPabiliInvoice($orderId);
        
        
    }
    
    
     if($servicetype == "SEND_PASAKAY_INVOICE"){
        unset($where);
        unset($messageArray);
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        $bookingId  = isset($_REQUEST['orderId']) ? trim($_REQUEST['orderId']) :'24';
 
        
        sendPasakayInvoice($bookingId);
        
        
    }
    
    
     if($servicetype == "SAVE_DRIVER_ADDRESS"){
         
        unset($where);
        unset($messageArray);
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $serviceAddress =  isset($_POST['serviceAddress']) ? trim($_POST['serviceAddress']) :'sdsd';
        $addressType =  isset($_POST['addressType']) ? trim($_POST['addressType']) :'Home';
        $region =  isset($_POST['region']) ? trim($_POST['region']) :'Home';
        $province=  isset($_POST['province']) ? trim($_POST['province']) :'Home';
        $city=  isset($_POST['city']) ? trim($_POST['city']) :'Home';
        $barangay =  isset($_POST['barangay']) ? trim($_POST['barangay']) :'Home';
        $exactAddress =  isset($_POST['exactAddress']) ? trim($_POST['exactAddress']) :'Home';
        $name =  isset($_POST['name']) ? trim($_POST['name']) :'Home';
        $mobile =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'Home';


        $messageArray['response'] = 1;
        $messageArray['iUserId'] =  $userId;
        $messageArray['eUserType'] = "Rider";
        // $messageArray['vServiceAddress'] = ucwords(strtolower($exactAddress)).", ". ucwords(strtolower($city)).", ".ucwords(strtolower($province)).", ". ucwords(strtolower($region)) ;
        // $messageArray['vAddressType'] = $addressType ;
        // $messageArray['vRegion'] = $region ;
        // $messageArray['vProvince'] = $province ;
        // $messageArray['vCity'] = $city ;
        // $messageArray['vBarangay'] = $barangay ;
        // $messageArray['vExactAddress'] =  $exactAddress ;

            
        echo json_encode( $messageArray);
        
        
        
    }
    
    
      
    if($servicetype == "SAVE_DRIVER_ADDRESS"){
         
        unset($where);
        unset($messageArray);
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $serviceAddress =  isset($_POST['serviceAddress']) ? trim($_POST['serviceAddress']) :'sdsd';
        $addressType =  isset($_POST['addressType']) ? trim($_POST['addressType']) :'Home';
        $region =  isset($_POST['region']) ? trim($_POST['region']) :'Home';
        $province=  isset($_POST['province']) ? trim($_POST['province']) :'Home';
        $city=  isset($_POST['city']) ? trim($_POST['city']) :'Home';
        $barangay =  isset($_POST['barangay']) ? trim($_POST['barangay']) :'Home';
        $exactAddress =  isset($_POST['exactAddress']) ? trim($_POST['exactAddress']) :'Home';
        $name =  isset($_POST['name']) ? trim($_POST['name']) :'Home';
        $mobile =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'Home';


        $messageArray['response'] = 1;
        $messageArray['iUserId'] =  $userId;
        $messageArray['eUserType'] = "Rider";
        $messageArray['vServiceAddress'] = ucwords(strtolower($exactAddress)).", ". ucwords(strtolower($city)).", ".ucwords(strtolower($province)).", ". ucwords(strtolower($region)) ;
        // $messageArray['vAddressType'] = $addressType ;
        // $messageArray['vRegion'] = $region ;
        // $messageArray['vProvince'] = $province ;
        // $messageArray['vCity'] = $city ;
        // $messageArray['vBarangay'] = $barangay ;
        // $messageArray['vExactAddress'] =  $exactAddress ;

            
        echo json_encode( $messageArray);
        
        
        
    }
    
    
       
    if($servicetype == "ADD_DRIVER_ADDRESS"){
         
        unset($where);
        unset($messageArray);
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $region =  isset($_POST['region']) ? trim($_POST['region']) :'Home';
        $province=  isset($_POST['province']) ? trim($_POST['province']) :'Home';
        $city=  isset($_POST['city']) ? trim($_POST['city']) :'Home';
        $barangay =  isset($_POST['barangay']) ? trim($_POST['barangay']) :'Home';
        $exactAddress =  isset($_POST['exactAddress']) ? trim($_POST['exactAddress']) :'Home';
        $name =  isset($_POST['name']) ? trim($_POST['name']) :'Home';
        $mobile =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'Home';
        
        $messageArray['response'] = 1;
        $messageArray['iUserId'] =  $userId;
        $messageArray['eUserType'] = "Rider";
        $messageArray['region'] = $region ;
        $messageArray['province'] = $province ;
        $messageArray['city'] = $city ;
        $messageArray['barangay'] = $barangay ;
        $messageArray['exactAddress'] =  $exactAddress ;
        $messageArray['vServiceAddress'] = ucwords(strtolower($exactAddress)).", ". ucwords(strtolower($city)).", ".ucwords(strtolower($province)).", ". $region."" ;


        echo json_encode( $messageArray);
        
        
        
    }
    
    
   if($servicetype == "VERIFY_MOBILE_AND_PASSWORD"){
         
        unset($where);
        unset($messageArray);
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $userType = isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $mobileNumber =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'sdsd';
        $password =  isset($_POST['password']) ? trim($_POST['password']) :'sdsd';
        
        $result2 =  checkMobileNumber($userType, $mobileNumber);
        
        
        if($result2 == 0){
            
            unset($where);
            
            $result = checkPassword($userType, $mobileNumber, $password);
            
            if(count( $result) > 0){
                $messageArray['response'] = 1;
                $messageArray['error'] =  "";
                
            }else{
                
               
            
                
                $messageArray['response'] = 0;
                $messageArray['error'] =  "wrong password";
                
            }
            
        }else{
            $messageArray['response'] = 0;
            $messageArray['error'] =  "wrong number";
           
        }
       
        echo json_encode( $messageArray);
        
        
        
    }
    
    
     if($servicetype == "CHANGE_MOBILE_NUMBER"){
         
        unset($where);
        unset($messageArray);
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'55';
        $userType = isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $mobileNumber =  isset($_POST['mobile']) ? trim($_POST['mobile']) :'sdsd';
        
        if($userType == "Driver"){
              unset($where);
            $where['iDriverId'] =  $userId;
            $updateMobile['vPhone'] = $mobileNumber;
            $result = myQuery("register_driver",  $updateMobile, "update",  $where);
            $messageArray['UPDATE DRIVER'] = "SUCCESSS";
            
            $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $userId."'";
                       
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            
            $messageArray['response'] = 1;
            $messageArray['error'] =  "";
              $messageArray['profileData'] =  $profileData;
            
        }
        
        
        if($userType == "User"){
              unset($where);
            $where['iUserId'] =  $userId;
            $updateMobile['vPhone'] = $mobileNumber;
            $result = myQuery("register_user",  $updateMobile, "update",  $where);
            $messageArray['UPDATE DRIVER'] = "SUCCESSS";
            
            $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
                       
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            $messageArray['response'] = 1;
            $messageArray['error'] =  "";
              $messageArray['profileData'] =  $profileData;
            
        }
        
        
        if($userType == "Store"){
             
            unset($where);
            $where['iUserId'] =  $userId;
            $updateMobile['vPhone'] = $mobileNumber;
            $result = myQuery("register_seller",  $updateMobile, "update",  $where);
            $messageArray['UPDATE DRIVER'] = "SUCCESSS";
            
            $sql = "SELECT * FROM register_seller WHERE iSellerId = '". $userId."'";
                       
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            $messageArray['response'] = 1;
            $messageArray['error'] =  "";
            $messageArray['profileData'] =  $profileData;
            
        }
            
    
        
       
       
        echo json_encode( $messageArray);
        
        
        
    }
    
    //$servicetype = "CHANGE_EMAIL_ADDRESS";
     
     if($servicetype == "CHANGE_EMAIL_ADDRESS"){
         
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'39';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'Driver';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'rencevterans.dev@gmail.com';
        
        $token = GenerateToken();
        
        
        if($userType == "Driver"){
            
            unset($where);
            $sql = "SELECT vName FROM register_driver WHERE iDriverId = '".$userId."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            unset($where);
                
            $where['iDriverId'] = $userId;
    
            $vupdate['vEmailVerificationCode'] =  $token ;
            
            $vupdate['eEmailVerified'] =  "No";
            
            $vupdate['vEmail'] =  $email  ;
             
            $result = myQuery("register_driver", $vupdate, "update", $where);
            
            
            $sql = "SELECT * FROM register_driver WHERE iDriverId = '". $userId."'";
                       
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            
            sendVerificationEmail($email, $profileData[0]['vName'], $token, $userType, $userId);
            
         
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['profileData'] =  $profileData;
            $messageArray['status'] =  "DRIVER SUCCESS";
          
        }else
        
        if($userType == "User"){
            
            unset($where);
            $sql = "SELECT vName FROM register_user WHERE iUserId = '".$userId."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
             unset($where);
                
            $where['iUserId'] = $userId;
    
            $vupdate['vEmailVerificationCode'] =  $token ;
            
            $vupdate['eEmailVerified'] =  "No";
            
            $vupdate['vEmail'] =  $email  ;
             
            $result = myQuery("register_user", $vupdate, "update", $where);
            
            
            $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
                       
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            
            
            sendVerificationEmail($email, $profileData[0]['vName'], $token, $userType, $userId);
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['profileData'] =  $profileData;
            $messageArray['status'] =  "BUYER SUCCESS";
          
        }else
        
        
        if($userType == "Store"){
            
            unset($where);
            $sql = "SELECT vCompany FROM company WHERE iCompanyId = '".$userId."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            unset($where);
                
            $where['iCompanyId'] = $userId;
    
            $vupdate['vEmailVerificationCode'] =  $token ;
            
            $vupdate['eEmailVerified'] =  "No";
            
            $vupdate['vEmail'] =  $email  ;
             
            $result = myQuery("company", $vupdate, "update", $where);
            
            
            $sql = "SELECT * FROM company WHERE iCompanyId = '". $userId."'";
                       
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll();  
            
            
            
            sendVerificationEmail($email, $profileData[0]['vCompany'], $token, $userType, $userId);
            
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['profileData'] =  $profileData;
            $messageArray['status'] =  "STORE SUCCESS";
          
        }
        
        

        
        echo json_encode( $messageArray);
        
        
    }
    
     if($servicetype == "CHECK_CHANGE_EMAIL"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'laure@gmail.com';
        $newemail =  isset($_POST['newemail']) ? trim($_POST['newemail']) :'laure@gmail.com';
        
        
        if($userType == "User"){
            
            unset($where);
            $sql = "SELECT vEmail FROM register_user WHERE iUserId = '".$userId."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll(); 
            
            $emailResult = strcmp($profileData[0]['vEmail'],  $email);
            
            $emailResult2 = strcmp($profileData[0]['vEmail'],  $newemail);
            
            
            if( $emailResult == 0 ){
                
                if($emailResult2 == 0){
                    
                    $messageArray['response'] = 0;
                    $messageArray['service'] = $servicetype;
                    $messageArray['error'] =  "Same Email";
                    
                }else{
                    
                    $messageArray['response'] = 1;
                    $messageArray['service'] = $servicetype;
                    $messageArray['status'] =  "EQUAL"; 
                }
              
            }else{
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "NOT EQUAL";
            }
            
            
           
          
          
        }else
        
        
        if($userType == "Driver"){
            
            unset($where);
            $sql = "SELECT vEmail FROM register_driver WHERE vEmail = '".$newemail."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll(); 
            
            // $emailResult = strcmp($profileData[0]['vEmail'],  $email);
            
            // $emailResult2 = strcmp($profileData[0]['vEmail'],  $newemail);
            
             
            if( count($profileData) > 0 ){
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "EXIST"; 
                
            }else{
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "NOT EXIST";
            }
          
        }else
        
        
        if($userType == "Store"){
            
            unset($where);
            $sql = "SELECT vEmail FROM company WHERE vEmail = '".$newemail."'";
               
            $statement = $db->query($sql); 
    
            $profileData = $statement ->fetchAll(); 
            
            // $emailResult = strcmp($profileData[0]['vEmail'],  $email);
            
            // $emailResult2 = strcmp($profileData[0]['vEmail'],  $newemail);
            
             
            if( count($profileData) > 0 ){
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "EXIST"; 
                
            }else{
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['status'] =  "NOT EXIST";
            }
          
        }
     
            
         echo json_encode( $messageArray);
       
        
        
    }
    
    
    
    if($servicetype == "GET_CANCEL_REASONS"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $reasonType =  isset($_POST['reasonType']) ? trim($_POST['reasonType']) :'User';
        $tripStatus =  isset($_POST['tripStatus']) ? trim($_POST['tripStatus']) :'User';
  
     
        unset($where);
        $sql = "SELECT * FROM cancel_reason WHERE vTripStatus = '".$tripStatus."' AND eType = '".$reasonType."'";
           
        $statement = $db->query($sql); 

        $reasonsResult = $statement ->fetchAll(); 
            
           
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['reasons'] =  $reasonsResult ;    
      
        
        echo json_encode( $messageArray);
       
        
        
    }
    
    if($servicetype == "DRIVER_LOCATION_UPDATE"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
  
     
        unset($where);
        $updateLoc['vLatitude'] =  $sourceLat ;
        $updateLoc['vLongitude'] =  $sourceLong;
        $updateLoc['tLocationUpdateDate'] =  @date("Y-m-d H:i:s");
    
        
        if($userType == "Driver"){
            
            $where['iDriverId'] =  $userId;
            $result = myQuery("register_driver",  $updateLoc, "update",  $where);
            $messageArray['location'] = "Updated!";
            
        }
            
           
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['reasons'] =  $reasonsResult ;    
        $messageArray['latitude'] =  $sourceLat ;    
        $messageArray['longitude'] =  $sourceLong ;    
      
        
        echo json_encode( $messageArray);
       
        
        
    }
    
    
    
    if($servicetype == "CASHIN_WITH_PAYPAL"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $driverId =  isset($_POST['userId']) ? trim($_POST['userId']) :'';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $amount =  isset($_POST['amount']) ? trim($_POST['amount']) :'';
  
    
        $enableSandbox = true;
        // PayPal settings. Change these to your account details and the relevant URLs
        // for your site.
        $paypalConfig = [
            'email' => 'user@example.com',
            'return_url' => 'http://example.com/payment-successful.html',
            'cancel_url' => 'http://example.com/payment-cancelled.html',
            'notify_url' => 'http://example.com/payments.php'
        ];
        
        $paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
        
        // Product being purchased.
        $itemName = 'TRIKOINS';
        $itemAmount = $amount;
        
        // Check if paypal request or response
        if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {
        
            // Grab the post data so that we can set up the query string for PayPal.
            // Ideally we'd use a whitelist here to check nothing is being injected into
            // our post data.
            $data = [];
            foreach ($_POST as $key => $value) {
                $data[$key] = stripslashes($value);
            }
        
            // Set the PayPal account.
            $data['business'] = $paypalConfig['email'];
        
            // Set the PayPal return addresses.
            $data['return'] = stripslashes($paypalConfig['return_url']);
            $data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
            $data['notify_url'] = stripslashes($paypalConfig['notify_url']);
        
            // Set the details about the product being purchased, including the amount
            // and currency so that these aren't overridden by the form data.
            $data['item_name'] = $itemName;
            $data['amount'] = $itemAmount;
            $data['currency_code'] = 'GBP';
        
            // Add any custom fields for the query string.
            //$data['custom'] = USERID;
        
            // Build the query string from the data.
            $queryString = http_build_query($data);
        
            // Redirect to paypal IPN
        //  header('location:' . $paypalUrl . '?' . $queryString);
        //  exit();

            
                   
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['url'] =   $paypalUrl . '?' . $queryString ;    
            
            echo json_encode( $messageArray);
        
        } 
    }
    
   // $servicetype = "LOAD_NEW_STORE_ADDED";
      
    if($servicetype == "LOAD_NEW_STORE_ADDED"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        
        $sql = "SELECT * FROM notifications WHERE iUserId  = '".$userId."' AND vUserType = '".$userType."' AND vType = 'NEW_STORES' ORDER BY dDateCreated";
           
        $statement = $db->query($sql); 

        $result = $statement ->fetchAll(); 
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['newStores'] =  $result ;    
        
        echo json_encode( $messageArray);
    
    }
    
    
          
    if($servicetype == "SET_FAVORITE_STORE"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $storeId =  isset($_POST['storeId']) ? trim($_POST['userType']) :'User';
        $favorite =  isset($_POST['favorite']) ? trim($_POST['favorite']) :'User';
        
        $sql = "SELECT iFavorite FROM notifications WHERE iCompanyId  = '".$storeId."'";
           
        $statement = $db->query($sql); 
        $favoriteData = $statement ->fetchAll(); 
        
        if($favorite == "favorite"){
            $favoriteData = (int) $favoriteData[0]['iFavorite'] + 1;
        }else{
            $favoriteData = (int) $favoriteData[0]['iFavorite'] - 1;
        }
        
        
        
        $where['iCompanyId'] = $storeId;
        $updateFavoriteStore['iFavorite'] = $favoriteData;
        $result = myQuery("company",  $updateFavoriteStore, "update",  $where);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['newStores'] =  $result ;    
        
        echo json_encode( $messageArray);
    
    }
    
    
    if($servicetype == "SET_FAVORITE_STORE"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $storeId =  isset($_POST['storeId']) ? trim($_POST['userType']) :'User';
        $favorite =  isset($_POST['favorite']) ? trim($_POST['favorite']) :'User';
        
        $sql = "SELECT iFavorite FROM notifications WHERE iCompanyId  = '".$storeId."'";
           
        $statement = $db->query($sql); 
        $favoriteData = $statement ->fetchAll(); 
        
        if($favorite == "favorite"){
            $favoriteData = (int) $favoriteData[0]['iFavorite'] + 1;
        }else{
            $favoriteData = (int) $favoriteData[0]['iFavorite'] - 1;
        }
        
        
        
        $where['iCompanyId'] = $storeId;
        $updateFavoriteStore['iFavorite'] = $favoriteData;
        $result = myQuery("company",  $updateFavoriteStore, "update",  $where);
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['newStores'] =  $result ;    
        
        echo json_encode( $messageArray);
    
    }
    
    //$servicetype = "GET_ROUTES";
    
    if($servicetype == "GET_ROUTES"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $storeId =  isset($_POST['storeId']) ? trim($_POST['userType']) :'User';
        $favorite =  isset($_POST['favorite']) ? trim($_POST['favorite']) :'User';
        
    
        
        $url = 'https://router.project-osrm.org/route/v1/biking/121.05306900486548,14.604762669084213;121.04419089330584,14.604124164791147?steps=true';
        $json = file_get_contents($url);
        $data = json_decode($json);
        
        $location = array();
        
        foreach($data->routes[0]->legs[0]->steps as $directions){
            
          
            
            $locationStr['long'] = $directions->maneuver->location[0];
            
            $locationStr['lat'] = $directions->maneuver->location[1];
            
            $location['points'][] =  $locationStr;
            
            // echo $directions->maneuver->location[0].",".$directions->maneuver->location[1];
            // echo '</br>';
        }
        
        echo json_encode($location);
    
    }
    
    
    //$servicetype = "LOAD_STORE_MENU_ITEMS";
    
    if($servicetype == "LOAD_STORE_MENU_ITEMS"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'Store';
        $storeId =  isset($_POST['storeId']) ? trim($_POST['storeId']) :'192';
        $language = getLanguage("English");
        
        $menu = array();
        $products = array();
        
        $sql = "SELECT vMenu_$language as Menu, fm.iDisplayOrder as displayOrderm, fm.tOpenTime as openTime, fm.tCloseTime as closeTime,    fm.iFoodMenuId as iFoodMenuId, (select count(iMenuItemId) from menu_items as mi where mi.iFoodMenuId=fm.iFoodMenuId AND mi.eStatus='Active') as menuCount  FROM food_menu as fm WHERE fm.iCompanyId = '" . $storeId . "' AND fm.eStatus='Active' ORDER BY fm.iDisplayOrder ASC";
           
        $statement = $db->query($sql); 

        $result = $statement ->fetchAll(); 
        
        $menu = $result;
      
        
        if($keyword == "" || $keyword == '' || $keyword == null){
            
            $sqlf = "SELECT mi.iMenuItemId as itemId, mi.iFoodMenuId as itemMenuId, fm.vMenu_EN as Menu, mi.vItemType_EN as itemName, mi.vItemDesc_EN as itemDesc, mi.fPrice as itemPrice, mi.vImage, mi.iDisplayOrder, mi.vHighlightName, mi.eAvailable as eAvailable
            FROM menu_items as mi LEFT JOIN food_menu as fm on mi.iFoodMenuId = fm.iFoodMenuId WHERE fm.iCompanyId = $storeId AND mi.eStatus='Active' AND (mi.eAvailable = 'Yes' ||  mi.eAvailable = 'No')  ORDER BY mi.iDisplayOrder ASC";
            
        }else{
            
            $sqlf = "SELECT mi.iMenuItemId as itemId, mi.iFoodMenuId as itemMenuId, fm.vMenu_EN as Menu, mi.vItemType_EN as itemName, mi.vItemDesc_EN as itemDesc, mi.fPrice as itemPrice, mi.vImage, mi.iDisplayOrder, mi.vHighlightName
            FROM menu_items as mi LEFT JOIN food_menu as fm on mi.iFoodMenuId = fm.iFoodMenuId WHERE fm.iCompanyId = $storeId AND mi.eStatus='Active' AND mi.eAvailable = 'Yes' AND ( fm.vMenu_EN LIKE '%". $keyword."%' OR  mi.vItemType_EN LIKE '%". $keyword."%') ORDER BY mi.iDisplayOrder ASC";
        
        }
        
        
        $statement = $db->query($sqlf);
        
        $result = $statement ->fetchAll(); 
        
        $products = $result;
        
        
        for($x = 0; $x < count($result) ; $x++){
            
        
            $products[$x]['vImage'] =  "http://mallody.com.ph/grab/webimages/upload/MenuItem/". $products[$x]['vImage'];
            
            $iMenuItemId = $products[$x]['itemId'];
            
            $sql = "SELECT iOptionId,vOptionName,fPrice,eOptionType,eDefault FROM menuitem_options WHERE iMenuItemId = '" . $iMenuItemId . "' AND eStatus = 'Active'";
            
            $statement = $db->query($sql);
        
            $menuOptions = $statement ->fetchAll(); 
            
            
            // if(!in_array( $menuOptions[$x]['Menu'] ,$category ) ){
            //     array_push($Nearestrestaurant,  $result[$x]);
            //     array_push($category,  $result[$x]['storeCategory']);
            // }
            
            
            if (count( $menuOptions) > 0) {

                for ($i = 0; $i < count( $menuOptions); $i++) {
        
                    $fPrice =  $menuOptions[$i]['fPrice'];
        
                    $fUserPrice = number_format($fPrice * $Ratio, 2);
        
                    $fUserPriceWithSymbol = $currencySymbol . " " . $fUserPrice;
        
                    $menuOptions[$i]['fUserPrice'] = $fUserPrice;
        
                    $menuOptions[$i]['fUserPriceWithSymbol'] = $fUserPriceWithSymbol;
        
                    if ($menuOptions[$i]['eOptionType'] == "Options") {
        
                        $suboptions['options'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Crust"){
                         $suboptions['crust'][] =  $menuOptions[$i];
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Fillings"){
                         $suboptions['fillings'][] =  $menuOptions[$i];
                    }
                        
                    if( $menuOptions[$i]['eOptionType'] == "Sugar Level"){
                         $suboptions['sugarlevel'][] =  $menuOptions[$i];
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Ice Level"){
                         $suboptions['icelevel'][] =  $menuOptions[$i];
                    }
        
        
                    if( $menuOptions[$i]['eOptionType'] == "Size") {
        
                       $suboptions['size'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Drinks") {
        
                       $suboptions['drinks'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "First Drink") {
        
                       $suboptions['drinks1'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Second Drink") {
        
                       $suboptions['drinks2'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Third Drink") {
        
                       $suboptions['drinks3'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Fourth Drink") {
        
                       $suboptions['drinks4'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Fifth Drink") {
        
                       $suboptions['drinks5'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "First Burger") {
        
                       $suboptions['burger1'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Second Burger") {
        
                       $suboptions['burger2'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Third Burger") {
        
                       $suboptions['burger3'][] =  $menuOptions[$i];
        
                    }
                    
                     if( $menuOptions[$i]['eOptionType'] == "Fourth Burger") {
        
                       $suboptions['burger4'][] =  $menuOptions[$i];
        
                    }
                    
                    if( $menuOptions[$i]['eOptionType'] == "Flavor") {
        
                       $suboptions['flavor'][] =  $menuOptions[$i];
        
                    }
        
        
        
                    if( $menuOptions[$i]['eOptionType'] == "Addon") {
        
                       $suboptions['addon'][] =  $menuOptions[$i];
        
                    }
        
                }
                
               
        
            }
            
            $products[$x]['customization'] = $suboptions;
           
            $products[$x]['currencySymbol'] = "&#x20B1;";
            
            $suboptions = array();
        }
    
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Ok";
        $messageArray['categories'] =  $menu;
        $messageArray['products'] = $products;
        
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
        $statement = $db->query($sql); 

        $profileData = $statement ->fetchAll();  
        
        // if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
              
        // }
        
        echo json_encode( $messageArray) ;
         
         
    }
    
   
   //$servicetype = "LOAD_ORDERS";
    
    if($servicetype == "LOAD_ORDERS"){
        
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
        $orderType  = isset($_POST['orderType']) ? trim($_POST['orderType']) : 'ACTIVE';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung'; 
        
        if($orderType  == "NEW"){

            $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iStatusCode IN(3001) AND od.iCompanyId = $storeId ORDER BY tOrderRequestDate DESC";
            $statement = $db->query($sql);
            $result = $statement ->fetchAll(); 

            $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iStatusCode IN(3011,3002,3003,3004,3005, 3006,3007,3008) AND od.iCompanyId = $storeId ORDER BY tOrderRequestDate DESC";
            $statement = $db->query($sql);
            $result2 = $statement ->fetchAll(); 
        }
        
        if($orderType  == "ACTIVE"){
            $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iStatusCode IN(3011,3002,3003,3004,3005, 3006,3007,3008) AND od.iCompanyId = $storeId ORDER BY tOrderRequestDate DESC";
            $statement = $db->query($sql);
            $result = $statement ->fetchAll(); 

            $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iStatusCode IN(3001) AND od.iCompanyId = $storeId ORDER BY tOrderRequestDate DESC";
            $statement = $db->query($sql);
            $result2 = $statement ->fetchAll(); 
        }
        
        if($orderType  == "OUTBOUND"){
            $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iStatusCode IN(3005) AND od.iCompanyId = $storeId ORDER BY tOrderRequestDate DESC";
            $statement = $db->query($sql);
            $result = $statement ->fetchAll(); 
        }
    
        $orderData = array();
        $driverData = array();
        
        for($i = 0; $i < count($result); $i++) {
            
            $itemId_array = "";
            
            $sql2 = "SELECT sum(iQty) as itemQty FROM order_details WHERE iOrderId = '".$result[$i]['iOrderId']."'";
            $statement = $db->query($sql2);
            $itemQty = $statement ->fetchAll();
            
            $sql2 = "SELECT * FROM order_details WHERE iOrderId = '".$result[$i]['iOrderId']."'";
            $statement = $db->query($sql2);
            $itemId = $statement ->fetchAll();
            
            $orderDetails = $itemId;
            
            for($x = 0; $x < count($itemId); $x++){
                
                if($x+1 == count($itemId)){
                    $itemId_array .= $itemId[$x]['iMenuItemId']."";
                }else{
                    $itemId_array .= $itemId[$x]['iMenuItemId'].",";
                }
                
            }
            
            $str_date = @date('Y-m-d H:i:s', strtotime('-30 minutes'));
            $trd_date2 = @date('Y-m-d H:i:s', strtotime($result[$i]['dDate']));
            
            if($trd_date2 < $str_date){
                $timeStatus = "Order expired";
            }else{
                // $timeStatus = timeAgo($result[$i]['dDate']);
                $timeStatus = minuteAgo($result[$i]['dDate']);
                
            }

            if($result[$i]['iStatusCode'] == "3002"){
                $statusText = "Driver found.";
            }elseif ($result[$i]['iStatusCode'] == "3003") {
                $statusText = "Driver is on the way to store.";
            }elseif ($result[$i]['iStatusCode'] == "3004") {
                $statusText = "Driver is At the store.";
            }elseif ($result[$i]['iStatusCode'] == "3005") {
                $statusText = "Order has been pickup.";
            }elseif ($result[$i]['iStatusCode'] == "3006") {
                $statusText = "Driver is on the way to deliver.";
            }elseif ($result[$i]['iStatusCode'] == "3008") {
                $statusText = "Driver arrived at the delivery address.";
            }elseif ($result[$i]['iStatusCode'] == "3009") {
                $statusText = "Order has been successfully delivered.";
            }elseif ($result[$i]['iStatusCode'] == "3010") {
                $statusText = "Order has been cancelled.";
            }
            
           
            
            $sql2 = "SELECT vCompanyColor, vRestuarantLocation, vRestuarantLocationLat, vRestuarantLocationLong FROM company WHERE iCompanyId = '".$result[$i]['iCompanyId']."'";
            $statement = $db->query($sql2);
            $company = $statement ->fetchAll();
            $orderData[$i]['orderId'] = $result[$i]['iOrderId'];
            $orderData[$i]['orderNo'] =  $result[$i]['vOrderNo'];
            $orderData[$i]['orderDate'] =  $result[$i]['dDate'];
            $orderData[$i]['orderTimeStatus'] = $timeStatus;
            $orderData[$i]['orderTimeRemaining'] =  timeDifference($result[$i]['dDate']);
            $orderData[$i]['storeName'] =  $result[$i]['vCompany'];
            $orderData[$i]['vName'] =  $result[$i]['vName'];
            $orderData[$i]['itemQty'] = $itemQty[0]['itemQty'];
            $orderData[$i]['orderPrice'] =  $result[$i]['fTotalGenerateFare'];
            $orderData[$i]['orderDiscount'] =  $result[$i]['vDiscount'];
            $orderData[$i]['orderSubtotal'] =  $result[$i]['fSubTotal'];
            $orderData[$i]['orderStatus'] =  $statusText;
            $orderData[$i]['orderStatusCode'] =  $result[$i]['iStatusCode'];
            $orderData[$i]['orderPaidFrom'] =  $result[$i]['ePaymentOption'];
            $orderData[$i]['storeId'] = $result[$i]['iCompanyId'];
            $orderData[$i]['storeColor'] = $company[0]['vCompanyColor'];
            $orderData[$i]['storeLocation'] = $company[0]['vRestuarantLocation'];
            $orderData[$i]['storeLatitude'] = $company[0]['vRestuarantLocationLat'];
            $orderData[$i]['storeLongitude'] = $company[0]['vRestuarantLocationLong'];
            $orderData[$i]['instruction'] = $result[$i]['vInstruction'];
            $orderData[$i]['read'] = $result[$i]['eRead'];
            $orderData[$i]['driverId'] = $result[$i]['iDriverId'];
            $orderData[$i]['totalOrder'] = count($result);
            $orderData[$i]['totalOrder2'] = count($result2);
            $orderData[$i]['itemArray'] = $itemId_array;
            $orderData[$i]['orderdetails'] = $orderDetails;
            
            if($result[$i]['iDriverId'] != "0" || $result[$i]['iDriverId'] != ""){
                
                $sql3 = "SELECT iDriverId, vName, vLastName, vEmail, vPhone, vLatitude, vLongitude FROM register_driver WHERE iDriverId = '".$result[$i]['iDriverId']."'";
                $statement = $db->query($sql3);
                $driverData = $statement ->fetchAll();
                
            }
            
            $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '". $result[$i]['iUserAddressId']."'";
            $statement = $db->query($sql4);
            $serviceAddress = $statement ->fetchAll();
            
            $orderData[$i]['orderDeliveryName'] =  $serviceAddress[0]['vName']." / ". $serviceAddress[0]['vPhone'];
            $orderData[$i]['orderDeliveryAddress'] =  $serviceAddress[0]['vServiceAddress'];
            $orderData[$i]['orderDeliveryAddressLat'] = $serviceAddress[0]['vLatitude'];
            $orderData[$i]['orderDeliveryAddressLong'] = $serviceAddress[0]['vLongitude'];
            
            $orderData[$i]['driverData'] = $driverData;
            
        }
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";
        $messageArray['notificationCounter'] = countNotifications($userId, "User");
        $messageArray['result'] =  $orderData;
        // $messageArray['driverData'] = $driverData;
        
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
        $statement = $db->query($sql); 
        $profileData = $statement ->fetchAll();  
        
        
        
        //if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
              
        // }
        
        echo json_encode($messageArray);
    }
    
    //$servicetype = "LOAD_STORE_ORDER_DETAILS";
    
    if($servicetype == "LOAD_STORE_ORDER_DETAILS"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
        $orderType  = isset($_POST['orderType']) ? trim($_POST['orderType']) : 'NEW';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung'; 
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) : '3'; 
        
        unset($where);
        $where['iOrderId'] =  $orderId;
        $updateOrder['eRead'] = 0;
        $result = myQuery("orders", $updateOrder, "update", $where);
        
        $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iOrderId = $orderId AND od.iCompanyId = $storeId ORDER BY tOrderRequestDate DESC";
        $statement = $db->query($sql);
        $result = $statement ->fetchAll(); 
    
        $orderData = array();
        $driverData = array();
        
        $itemId_array = "";
        
        $sql2 = "SELECT sum(iQty) as itemQty FROM order_details WHERE iOrderId = '".$result[0]['iOrderId']."'";
        $statement = $db->query($sql2);
        $itemQty = $statement ->fetchAll();
        
        $sql2 = "SELECT * FROM order_details WHERE iOrderId = '".$result[0]['iOrderId']."'";
        $statement = $db->query($sql2);
        $itemId = $statement ->fetchAll();
        
        $orderDetails = $itemId;
        
        for($x = 0; $x < count($itemId); $x++){
            
            if($x+1 == count($itemId)){
                $itemId_array .= $itemId[$x]['iMenuItemId']."";
            }else{
                $itemId_array .= $itemId[$x]['iMenuItemId'].",";
            }
            
        }
        
        $str_date = @date('Y-m-d H:i:s', strtotime('-30 minutes'));
        $trd_date2 = @date('Y-m-d H:i:s', strtotime($result[0]['dDate']));
        
        if($trd_date2 < $str_date){
            $timeStatus = "Order expired";
        }else{
            $timeStatus = minuteAgo($result[0]['dDate']);
        }
        
       
        
        $sql2 = "SELECT vCompanyColor, vRestuarantLocation, vRestuarantLocationLat, vRestuarantLocationLong FROM company WHERE iCompanyId = '".$result[0]['iCompanyId']."'";
        $statement = $db->query($sql2);
        $company = $statement ->fetchAll();
        $orderData['orderId'] = $result[0]['iOrderId'];
        $orderData['orderNo'] =  $result[0]['vOrderNo'];
        $orderData['orderDate'] =  $result[0]['dDate'];
        $orderData['orderTimeStatus'] = $timeStatus;
        $orderData['storeName'] =  $result[0]['vCompany'];
        $orderData['vName'] =  $result[0]['vName'];
        $orderData['itemQty'] = $itemQty[0]['itemQty'];
        $orderData['orderPrice'] =  $result[0]['fTotalGenerateFare'];
        $orderData['orderDiscount'] =  $result[0]['vDiscount'];
        $orderData['orderSubtotal'] =  $result[0]['fSubTotal'];
        $orderData['orderStatus'] =  $result[0]['vStatus'];
        $orderData['orderPaidFrom'] =  $result[0]['ePaymentOption'];
        $orderData['storeId'] = $result[0]['iCompanyId'];
        $orderData['storeColor'] = $company[0]['vCompanyColor'];
        $orderData['storeLocation'] = $company[0]['vRestuarantLocation'];
        $orderData['storeLatitude'] = $company[0]['vRestuarantLocationLat'];
        $orderData['storeLongitude'] = $company[0]['vRestuarantLocationLong'];
        $orderData['instruction'] = $result[$i]['vInstruction'];
        $orderData['read'] = $result[0]['eRead'];
        $orderData['driverId'] = $result[0]['iDriverId'];
        $orderData['totalOrder'] = count($result);
        $orderData['itemArray'] = $itemId_array;
        $orderData['orderdetails'] = $orderDetails;
        
        $messageArray['driverData'] = "";
        
        if($orderData['driverId'] != "0" || $orderData['driverId'] != ""){
            
            $sql3 = "SELECT iDriverId, vName, vLastName, vEmail, vPhone, vLatitude, vLongitude, vTodaLine, vPlateNo FROM register_driver WHERE iDriverId = '".$orderData['driverId']."'";
            $statement = $db->query($sql3);
            $driverData = $statement ->fetchAll();
            $messageArray['driverData'] = $driverData[0];
            
        }else{
            $messageArray['driverData'] = "";
        }
        
        
        $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '". $result[0]['iUserAddressId']."'";
        $statement = $db->query($sql4);
        $serviceAddress = $statement ->fetchAll();
        
        $orderData['orderDeliveryName'] =  $serviceAddress[0]['vName']." / ". $serviceAddress[0]['vPhone'];
        $orderData['orderDeliveryAddress'] =  $serviceAddress[0]['vServiceAddress'];
        $orderData['orderDeliveryAddressLat'] = $serviceAddress[0]['vLatitude'];
        $orderData['orderDeliveryAddressLong'] = $serviceAddress[0]['vLongitude'];
       
            
 
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = $status;
        $messageArray['notificationCounter'] = countNotifications($userId, "User");
        $messageArray['result'] =  $orderData;
      
        // $messageArray['driverData'] = $driverData;
        
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
        $statement = $db->query($sql); 
        $profileData = $statement ->fetchAll();  
        
        
        
        //if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
              
        // }
        
        echo json_encode($messageArray);
    }
    
   
   
   
  // $servicetype = "ADD_MENU_CATEGORY";
   
    if($servicetype == "ADD_MENU_CATEGORY"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '59';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        $menuName = isset($_POST['menuName']) ? trim($_POST['menuName']) : 'Hehe';
        $openTime = isset($_POST['openTime']) ? trim($_POST['openTime']) : '12:00 AM';
        $closeTime = isset($_POST['closeTime']) ? trim($_POST['closeTime']) : '11:59 PM';
    
        $newOpenTime = @date('H:i:s',strtotime($openTime));
        $newCloseTime = @date('H:i:s',strtotime($closeTime));
        
        $menuInsert['iCompanyId'] = (int) $storeId;
        $menuInsert['vMenu_EN'] = $menuName;
        $menuInsert['tOpenTime'] = $newOpenTime;
        $menuInsert['tCloseTime'] = $newCloseTime;
    
        
        $lastInsertedId = myQuery("food_menu", $menuInsert, "insert_getlastid");
        
        
        $sql = "SELECT * FROM food_menu WHERE iFoodMenuId = '".  $lastInsertedId."'";
        $statement = $db->query($sql); 
        $menuData = $statement ->fetchAll();  
        
        if(count($menuData)){
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";
            $messageArray['menuName'] = $menuData[0]['vMenu_EN'];
            $messageArray['menuOpenTime'] = $menuData[0]['tOPenTime'];
            $messageArray['menuCloseTime'] = $menuData[0]['tCloseTime'];
            $messageArray['lastInsertedId'] = $result;
        }else{
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Failed";
          
        }
        
       

        echo json_encode($messageArray);
        
        
    }
    
    
    if($servicetype == "UPDATE_MENU_CATEGORY"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '59';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        $menuName = isset($_POST['menuName']) ? trim($_POST['menuName']) : 'Hehe';
        $openTime = isset($_POST['openTime']) ? trim($_POST['openTime']) : '12:00 AM';
        $closeTime = isset($_POST['closeTime']) ? trim($_POST['closeTime']) : '11:59 PM';
        $menuId = isset($_POST['menuId']) ? trim($_POST['menuId']) : '11:59 PM';
    
        $newOpenTime = @date('H:i:s',strtotime($openTime));
        $newCloseTime = @date('H:i:s',strtotime($closeTime));
    
        $where['iFoodMenuId'] =$menuId; 
        $menuUpate['iCompanyId'] = (int) $storeId;
        $menuUpate['vMenu_EN'] = $menuName;
        $menuUpate['tOpenTime'] = $newOpenTime;
        $menuUpate['tCloseTime'] = $newCloseTime;
    
        
        $lastInsertedId = myQuery("food_menu", $menuUpate, "update", $where);
        
        
        $sql = "SELECT * FROM food_menu WHERE iFoodMenuId = '".  $menuId."'";
        $statement = $db->query($sql);
       $menuData = $statement ->fetchAll();  
        
        if(count($menuData)){
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Okay";
            $messageArray['menuName'] = $menuData[0]['vMenu_EN'];
            $messageArray['menuOpenTime'] = $menuData[0]['tOPenTime'];
            $messageArray['menuCloseTime'] = $menuData[0]['tCloseTime'];
            $messageArray['lastInsertedId'] = $result;
        }else{
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] = "Failed";
          
        }
       

        echo json_encode($messageArray);
        
        
    }
    
    
    if($servicetype == "DELETE_MENU_CATEGORY"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '59';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : 'SAMSUung';
        $menuName = isset($_POST['menuName']) ? trim($_POST['menuName']) : 'Hehe';
        $openTime = isset($_POST['openTime']) ? trim($_POST['openTime']) : '12:00 AM';
        $closeTime = isset($_POST['closeTime']) ? trim($_POST['closeTime']) : '11:59 PM';
        $menuId = isset($_POST['menuId']) ? trim($_POST['menuId']) : '11:59 PM';
    
        $sql = "DELETE FROM food_menu WHERE iFoodMenuId = '".  $menuId."'";
    
        $statement = $db->query($sql); 
        $menuData = $statement ->fetchAll();  
        
        
        $sql = "DELETE FROM menu_items WHERE iFoodMenuId = '".  $menuId."'";
    
        $statement = $db->query($sql); 
        $menuitemsData = $statement ->fetchAll();  
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['status'] = "Okay";

        echo json_encode($messageArray);
        
        
    }
    
    
    //$servicetype = "LOAD_OPTION_TYPE";
    if($servicetype == "LOAD_OPTION_TYPE"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
        
        $optionData = array();
        $optionSpecialData = array();
        
        $optionDataValue = array("Size","Flavor","Options");
        $optionSpecialDataValue = array("Addon", "Drinks","Sugar Level","Ice Level", "Quantity", "Fillings", "Crust", "First Drink", "Second Drink", "Third Drink", "Fourth Drink", "Fifth Drink", "First Burger", "Second Burger", "Third Burger", "Fourth Burger");
        
        for($x=0;$x<count($optionDataValue);$x++){
            $optionData[$x]['type'] = $optionDataValue[$x];

        }
        
        for($y=0;$y<count($optionSpecialDataValue);$y++){
             $optionSpecialData[$y]['type'] = $optionSpecialDataValue[$y];
          
        }
       
        
        if(count($optionData)){
            $messageArray['response'] = 1;
            $messageArray['optionType'] = $optionData;
            $messageArray['optionSpecialType'] =  $optionSpecialData;
        }else{
            $messageArray['response'] = 0;
          
        }
        
       
        
        echo json_encode($messageArray);
        
        
    }
    

    if($servicetype == "ADD_PPRODUCT_ITEM"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
        $productName = isset($_POST['productName']) ? trim($_POST['productName']) : '54';
        $productCategory = isset($_POST['productCategory']) ? trim($_POST['productCategory']) : '54';
        $productCategoryId = isset($_POST['productCategoryId']) ? trim($_POST['productCategoryId']) : '54';
        $productPrice = isset($_POST['productPrice']) ? trim($_POST['productPrice']) : '';
        $productOptions = isset($_POST['productOptions']) ? trim($_POST['productOptions']) : '';
        
        $optionList = array();
        $optionList = json_decode(stripcslashes($productOptions), true);
        
        
        $newProduct['iFoodMenuId'] = $productCategoryId;
        $newProduct['vItemType_EN'] =$productName;
        $newProduct['fPrice'] = $productPrice;
        $newProduct['eFoodType'] = "NonVeg";
        $newProduct['fOfferAmt'] = $productPrice;
        $newProduct['vImage'] = "";
        $newProduct['eStatus'] = "Active";
        $newProduct['eAvailable'] = "Yes";
        $newProduct['eBestSeller'] = "No";
        $newProduct['eRecommended'] = "No";
        
        
        $lastInsertedId = myQuery("menu_items", $newProduct, "insert_getlastid");
        
        for($j = 0; $j < count($optionList); $j ++){
            
            $newOptions['iMenuItemId'] = $lastInsertedId;
            $newOptions['vOptionName'] = $optionList[$j]['optionName'];
            $newOptions['fPrice'] = $optionList[$j]['optionPrice'];
            $newOptions['eOptionType'] = $optionList[$j]['optionType'];
            $newOptions['eDefault'] = $optionList[$j]['eDefault'];
            $newOptions['eStatus'] = "Active";
            
            $res = myQuery("menuitem_options", $newOptions, "insert_getlastid");
            
        }
        
        
        if($lastInsertedId != null || $lastInsertedId != ""){
            $messageArray['response'] = 1;
            $messageArray['optionType'] = $optionData;
            $messageArray['product'] =   $newProduct;
            $messageArray['options'] =  $optionList;
        }else{
            $messageArray['response'] = 0;
        }
        
        echo json_encode($messageArray);
        
        
    }
    
    
    if($servicetype == "UPDATE_PPRODUCT_ITEM"){
        
        unset($where);
        unset($messageArray);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
        $productId = isset($_POST['productId']) ? trim($_POST['productId']) : '54';
        $productName = isset($_POST['productName']) ? trim($_POST['productName']) : '54';
        $productCategory = isset($_POST['productCategory']) ? trim($_POST['productCategory']) : '54';
        $productCategoryId = isset($_POST['productCategoryId']) ? trim($_POST['productCategoryId']) : '54';
        $productPrice = isset($_POST['productPrice']) ? trim($_POST['productPrice']) : '';
        $productOptions = isset($_POST['productOptions']) ? trim($_POST['productOptions']) : '';
        $productOptionsDelete = isset($_POST['productOptionsDelete']) ? trim($_POST['productOptionsDelete']) : '';
        
        $optionList = array();
        $deleteList = array();
        $optionList = json_decode(stripcslashes($productOptions), true);
        $deleteList = json_decode(stripcslashes($productOptionsDelete), true);
        
        $where['iMenuItemId'] = $productId;
        
        $updateProduct['iFoodMenuId'] = (int)$productCategoryId;
        $updateProduct['vItemType_EN'] =$productName;
        $updateProduct['fPrice'] = (float)$productPrice;
        $updateProduct['eFoodType'] = "NonVeg";
        $updateProduct['fOfferAmt'] = $productPrice;
        $updateProduct['vImage'] = "";
        $updateProduct['eStatus'] = "Active";
        $updateProduct['eAvailable'] = "Yes";
        $updateProduct['eBestSeller'] = "No";
        $updateProduct['eRecommended'] = "No";
        
        
        $result = myQuery("menu_items", $updateProduct, "update", $where);
        
        for($j = 0; $j < count($optionList); $j ++){
            
            if($optionList[$j]['optionId'] != ""){
                
                $where['iOptionId'] = $optionList[$j]['optionId'];
                $updateOptions['iMenuItemId'] = (int) $productId;
                $updateOptions['vOptionName'] = $optionList[$j]['optionName'];
                $updateOptions['fPrice'] = (float) $optionList[$j]['optionPrice'];
                $updateOptions['eOptionType'] = $optionList[$j]['optionType'];
                $updateOptions['eDefault'] = $optionList[$j]['eDefault'];
                $updateOptions['eStatus'] = "Active";
                $res = myQuery("menuitem_options", $updateOptions, "update", $where);
            }else{
                $newOptions['iMenuItemId'] = (int) $productId;
                $newOptions['vOptionName'] = $optionList[$j]['optionName'];
                $newOptions['fPrice'] =(float) $optionList[$j]['optionPrice'];
                $newOptions['eOptionType'] = $optionList[$j]['optionType'];
                $newOptions['eDefault'] = $optionList[$j]['eDefault'];
                $newOptions['eStatus'] = "Active";
                
                $res = myQuery("menuitem_options", $newOptions, "insert_getlastid");
            }
     
        }
        
        if(count($deleteList) > 0 ){
            for($j = 0; $j < count($deleteList); $j ++){
                
                $sql = "DELETE FROM menuitem_options WHERE iOptionId = '". $deleteList[$j]['optionId']."'";
    
                $statement = $db->query($sql); 
                $deleteData = $statement ->fetchAll();  
                
            }
        }
        
        
        $messageArray['response'] = 1;
        $messageArray['optionType'] = $updateProduct;
        $messageArray['product'] =  $newOptions;
        $messageArray['options'] = $updateProduct;
       
    
        echo json_encode($messageArray);
        
        
    }
    
    
    if($servicetype == "SWITCH_ITEM_AVAILABILITY"){
        
        unset($where);
        unset($messageArray);
        unset($update);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
        $productId = isset($_POST['productId']) ? trim($_POST['productId']) : '54';
        $productCategoryId = isset($_POST['productCategoryId']) ? trim($_POST['productCategoryId']) : '54';
        $eAvaialable= isset($_POST['eAvaialable']) ? trim($_POST['eAvaialable']) : '54';
        
        $updateAvailability['eAvailable'] = $eAvaialable;
        $where['iMenuItemId'] = $productId;
        $result = myQuery("menu_items", $updateAvailability, "update",  $where);
        
        $sql = "SELECT eAvailable, vItemType_EN FROM menu_items WHERE iMenuItemId = '".$productId."'";
        $statement = $db->query($sql);
        $data = $statement ->fetchAll();
        
        
        echo json_encode($data);
        
        
    }
    
    //$servicetype = "LOAD_STORE_EMPLOYEE";
    
    if($servicetype == "LOAD_STORE_EMPLOYEE"){
        
        unset($where);
        unset($messageArray);
        unset($update);
        
        
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '59';
        
        $sql = "SELECT * FROM register_seller WHERE iCompanyId = '".  $storeId."' AND eAcessLevel = 'Cashier'";
        $statement = $db->query($sql); 
        $employeeData = $statement ->fetchAll();  
        
        if(count($employeeData) > 0){
            $messageArray['response'] = 1;
            $messageArray['employee'] = $employeeData;
            $messageArray['servicetype'] = $servicetype;
        }else{
            $messageArray['response'] = 0;
            $messageArray['servicetype'] = $servicetype;
          
        }
        
        echo json_encode($messageArray);
      
    }
    
    
    if($servicetype == "ADD_EMPLOYEE"){
        
        unset($where);
        unset($messageArray);
        unset($update);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '59';
        $name  = isset($_POST['name']) ? trim($_POST['name']) : '';
        $password  = isset($_POST['password']) ? trim($_POST['password']) : '';
        $username  = isset($_POST['username ']) ? trim($_POST['username']) : '';
        $position  = isset($_POST['position']) ? trim($_POST['position']) : '';
        
        $addEmplyee['vName'] = $name;
        $addEmplyee['vPassword'] = md5(constants::SALT.$password);
        $addEmplyee['eAcessLevel'] = $position;
        $addEmplyee['vUsername'] = $username;
        $addEmplyee['vEmail'] = $username;
        $addEmplyee['iCompanyId'] = $storeId;
            
        $lastInsertedId = myQuery("register_seller", $addEmplyee, "insert_getlastid");
        if($lastInsertedId != ""){
            
            $messageArray['response'] = 1;
            $messageArray['employeeData'] = $addEmplyee;
            $messageArray['servicetype'] = $servicetype;
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['servicetype'] = $servicetype;
          
        }
        
        echo json_encode($messageArray);
      
    }
    
    if($servicetype == "DELETE_EMPLOYEE"){
        
        unset($where);
        unset($messageArray);
        unset($update);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '59';
        $deleteArr  = isset($_POST['deleteArr']) ? trim($_POST['deleteArr']) : '';
        
        
        $employeeList = array();
        $employeeList = json_decode(stripcslashes($deleteArr), true);
        
        for($x = 0 ; $x < count($employeeList) ; $x++){
            
            $sql = "DELETE FROM register_seller WHERE iSellerId = '". $employeeList[$x]['id']."'";

            $statement = $db->query($sql);
            
            $result = $statement ->execute(); 
            
        }
        
        $messageArray['response'] = 1;
        $messageArray['servicetype'] = $servicetype;
        $messageArray['deleteArr'] = $deleteArr;
        
        echo json_encode($messageArray);
      
    }
    
    
    
    
    if($servicetype == "UPDATE_EMPLOYEE"){
        
        unset($where);
        unset($messageArray);
        unset($update);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '59';
        $name  = isset($_POST['name']) ? trim($_POST['name']) : '';
        $password  = isset($_POST['password']) ? trim($_POST['password']) : '';
        $username  = isset($_POST['username ']) ? trim($_POST['username']) : '';
        $position  = isset($_POST['position']) ? trim($_POST['position']) : '';
        $employeeId  = isset($_POST['employeeId']) ? trim($_POST['employeeId']) : '';
        
        unset($where);
        $where['iSellerId'] = $employeeId;
        $updateEmplyee['vName'] = $name;
        $updateEmplyee['eAcessLevel'] = $position;
        $updateEmplyee['vUsername'] = $username;
        $updateEmplyee['vEmail'] = $username;
        $updateEmplyee['iCompanyId'] = $storeId;
            
        $result = myQuery("register_seller", $updateEmplyee, "update", $where);
       
            
        $messageArray['response'] = 1;
        $messageArray['employeeData'] = $updateEmplyee;
        $messageArray['servicetype'] = $servicetype;
        
       
        
        echo json_encode($messageArray);
      
    }
    
    
    
    
   // $servicetype = "LOAD_BUSINESS_HOURS";
    
    if($servicetype == "LOAD_BUSINESS_HOURS"){
        
        unset($where);
        unset($messageArray);
        unset($update);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '59';
        
        $where['iCompanyId'] =  $storeId;
        $result = myQuery("business_days", array("iBusinessDayId", "iCompanyId", "vWeekDay", "e24hours", "eStatus"), "selectall", $where);
        
      
        if(count($result) > 0){
            
            
            for($x = 0; $x < count($result); $x++){
                unset($where);
                 
                $where['iBusinessDayId'] =  $result[$x]['iBusinessDayId'];
                $timeSlots = myQuery("business_hours", array("iBusinessHoursId", "iBusinessDayId", "tOpenTime", "tCloseTime", "iSlot", "eStatus"), "selectall", $where);
                
                
                $weekDay[$x]['weekDay'] = $result[$x]['vWeekDay'];
                $weekDay[$x]['iCompanyId'] = $result[$x]['iCompanyId'];
                $weekDay[$x]['e24hours'] = $result[$x]['e24hours'];
                $weekDay[$x]['eStatus'] = $result[$x]['eStatus'];
                $weekDay[$x]['iBusinessDayId'] = $result[$x]['iBusinessDayId'];
                $weekDay[$x]['timeSlots'] = $timeSlots;
                
            }
            
            
            $messageArray['response'] = 1;
            $messageArray['results'] = $weekDay;
            
        }else{
            // echo "No Business Hours Available";
            // echo "<br>";
            // echo "Day : ";
            // echo "<br>";
            $timestamp = strtotime('next Sunday');
            $days = array();
            for ($i = 0; $i < 7; $i++) {
                // echo strftime('%A', $timestamp);
                // echo "<br>";
                $day = strftime('%A', $timestamp);
                $days[] = strftime('%A', $timestamp);
                $timestamp = strtotime('+1 day', $timestamp);
                
                $insertBusinessDay['iCompanyId'] = $storeId;
                $insertBusinessDay['vWeekDay'] = $day;
                $insertBusinessDay['eStatus'] = "Active";
                $insertBusinessDay['e24hours'] = "Yes";
                $lastInsertedId = myQuery("business_days",  $insertBusinessDay, "insert_getlastid");
                
                $insertBusinessHours['iBusinessDayId'] = $lastInsertedId;
                $insertBusinessHours['tOpenTime'] = "00:00:00";
                $insertBusinessHours['tCloseTime'] = "00:00:00";
                $insertBusinessHours['eStatus'] = "Active";
                $insertBusinessHours['iSlot'] = 1;
                $slot1 = myQuery("business_hours",  $insertBusinessHours, "insert_getlastid");
                
                $insertBusinessHours['iBusinessDayId'] = $lastInsertedId;
                $insertBusinessHours['tOpenTime'] = "00:00:00";
                $insertBusinessHours['tCloseTime'] = "00:00:00";
                $insertBusinessHours['eStatus'] = "Active";
                $insertBusinessHours['iSlot'] = 2;
                $slot1 = myQuery("business_hours",  $insertBusinessHours, "insert_getlastid");
                
                $insertBusinessHours['iBusinessDayId'] = $lastInsertedId;
                $insertBusinessHours['tOpenTime'] = "00:00:00";
                $insertBusinessHours['tCloseTime'] = "00:00:00";
                $insertBusinessHours['eStatus'] = "Active";
                $insertBusinessHours['iSlot'] = 3;
                $slot1 = myQuery("business_hours",  $insertBusinessHours, "insert_getlastid");
                
                // echo "Last Inserted Id : ".$lastInsertedId;
                // // e24hours 
                // // dDateCreated
                
                
            }
            
            unset($where);
            $where['iCompanyId'] =  $storeId;
            $result = myQuery("business_days", array("iBusinessDayId", "iCompanyId", "vWeekDay", "e24hours", "eStatus"), "selectall", $where);
        
            for($x = 0; $x < count($result); $x++){
                unset($where);
                 
                $where['iBusinessDayId'] =  $result[$x]['iBusinessDayId'];
                $timeSlots = myQuery("business_hours", array("iBusinessHoursId", "iBusinessDayId", "tOpenTime", "tCloseTime", "iSlot", "eStatus"), "selectall", $where);
                
               
                $weekDay[$x]['weekDay'] = $result[$x]['vWeekDay'];
                $weekDay[$x]['iCompanyId'] = $result[$x]['iCompanyId'];
                $weekDay[$x]['e24hours'] = $result[$x]['e24hours'];
                $weekDay[$x]['eStatus'] = $result[$x]['eStatus'];
                $weekDay[$x]['iBusinessDayId'] = $result[$x]['iBusinessDayId'];
                $weekDay[$x]['timeSlots'] = $timeSlots;
                
            }
        }
        
        $messageArray['response'] = 1;
        $messageArray['results'] = $weekDay;
       
        
        echo json_encode($messageArray);
      
    }
    
    
    // //$servicetype = "LOAD_STORE_ORDER_DETAILS";
    // if($servicetype == "LOAD_STORE_ORDER_DETAILS"){
        
    //     unset($where);
    //     unset($messageArray);
    //     unset($update);
        
    //     $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
    //     $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
    //     $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
    //     $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) : '4';
    //     $orderType  = isset($_POST['orderType']) ? trim($_POST['orderType']) : '';
    //     $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : ''; 
        
        
      


    //     $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iOrderId = $orderId AND od.iCompanyId = $storeId ORDER BY tOrderRequestDate DESC";
    //     $statement = $db->query($sql);
    //     $result = $statement ->fetchAll(); 

    //     $orderData = array();
    //     $driverData = array();
        
    //     for($i = 0; $i < count($result); $i++) {
            
    //         $itemId_array = "";
            
    //         $sql2 = "SELECT sum(iQty) as itemQty FROM order_details WHERE iOrderId = '".$result[$i]['iOrderId']."'";
    //         $statement = $db->query($sql2);
    //         $itemQty = $statement ->fetchAll();
            
    //         $sql2 = "SELECT * FROM order_details WHERE iOrderId = '".$result[$i]['iOrderId']."'";
    //         $statement = $db->query($sql2);
    //         $itemId = $statement ->fetchAll();
            
    //         $orderDetails = $itemId;
            
    //         for($x = 0; $x < count($itemId); $x++){
                
    //             if($x+1 == count($itemId)){
    //                 $itemId_array .= $itemId[$x]['iMenuItemId']."";
    //             }else{
    //                 $itemId_array .= $itemId[$x]['iMenuItemId'].",";
    //             }
                
    //         }
            
    //         $str_date = @date('Y-m-d H:i:s', strtotime('-30 minutes'));
    //         $trd_date2 = @date('Y-m-d H:i:s', strtotime($result[$i]['dDate']));
            
    //         if($trd_date2 < $str_date){
    //             $timeStatus = "Order expired";
    //         }else{
    //             $timeStatus = minuteAgo($result[$i]['dDate']);
    //         }
            
           
            
    //         $sql2 = "SELECT vCompanyColor, vRestuarantLocation, vRestuarantLocationLat, vRestuarantLocationLong FROM company WHERE iCompanyId = '".$result[$i]['iCompanyId']."'";
    //         $statement = $db->query($sql2);
    //         $company = $statement ->fetchAll();
    //         $orderData[$i]['orderId'] = $result[$i]['iOrderId'];
    //         $orderData[$i]['orderNo'] =  $result[$i]['vOrderNo'];
            
    //         $orderData[$i]['orderDate'] =  $result[$i]['dDate'];
    //         $orderData[$i]['orderTimeStatus'] = $timeStatus;
    //         $orderData[$i]['storeName'] =  $result[$i]['vCompany'];
    //         $orderData[$i]['vName'] =  $result[$i]['vName'];
    //         $orderData[$i]['itemQty'] = $itemQty[0]['itemQty'];
    //         $orderData[$i]['orderPrice'] =  $result[$i]['fTotalGenerateFare'];
    //         $orderData[$i]['orderStatus'] =  $result[$i]['vStatus'];
    //         $orderData[$i]['orderPaidFrom'] =  $result[$i]['ePaymentOption'];
    //         $orderData[$i]['storeId'] = $result[$i]['iCompanyId'];
    //         $orderData[$i]['storeColor'] = $company[0]['vCompanyColor'];
    //         $orderData[$i]['storeLocation'] = $company[0]['vRestuarantLocation'];
    //         $orderData[$i]['storeLatitude'] = $company[0]['vRestuarantLocationLat'];
    //         $orderData[$i]['storeLongitude'] = $company[0]['vRestuarantLocationLong'];
    //         $orderData[$i]['instruction'] = $result[$i]['vInstruction'];
    //         $orderData[$i]['read'] = $result[$i]['eRead'];
    //         $orderData[$i]['driverId'] = $result[$i]['iDriverId'];
    //         $orderData[$i]['totalOrder'] = count($result);
    //         $orderData[$i]['itemArray'] = $itemId_array;
    //         $orderData[$i]['orderdetails'] = $orderDetails;
            
    //         if($result[$i]['iDriverId'] != "0" || $result[$i]['iDriverId'] != ""){
                
    //             $sql3 = "SELECT iDriverId, vName, vLastName, vEmail, vPhone, vLatitude, vLongitude FROM register_driver WHERE iDriverId = '".$result[$i]['iDriverId']."'";
    //             $statement = $db->query($sql3);
    //             $driverData = $statement ->fetchAll();
                
    //         }
            
    //         $sql4 = "SELECT vName, vPhone,   vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '". $result[$i]['iUserAddressId']."'";
    //         $statement = $db->query($sql4);
    //         $serviceAddress = $statement ->fetchAll();
            
    //         $orderData[$i]['orderDeliveryName'] =  $serviceAddress[0]['vName']." / ". $serviceAddress[0]['vPhone'];
    //         $orderData[$i]['orderDeliveryAddress'] =  $serviceAddress[0]['vServiceAddress'];
    //         $orderData[$i]['orderDeliveryAddressLat'] = $serviceAddress[0]['vLatitude'];
    //         $orderData[$i]['orderDeliveryAddressLong'] = $serviceAddress[0]['vLongitude'];
            
    //         $orderData[$i]['driverData'] = $driverData;
            
    //     }
        
    //     $messageArray['response'] = 1;
    //     $messageArray['service'] = $servicetype;
    //     $messageArray['status'] = "Okay";
    //     $messageArray['notificationCounter'] = countNotifications($userId, "User");
    //     $messageArray['result'] =  $orderData;
    //     // $messageArray['driverData'] = $driverData;
        
    //     $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
    //     $statement = $db->query($sql); 
    //     $profileData = $statement ->fetchAll();  
        
        
        
    //     //if($deviceInfo != $profileData[0]['tDeviceData']){
                
    //     //         unset($messageArray);
    //     //         $messageArray['response'] = 0;
    //     //         $messageArray['service'] = $servicetype;
    //     //         $messageArray['userType'] = $userType;
    //     //         $messageArray['error'] = "AUTO_LOGOUT";
    //     //         $messageArray['deviceInfo'] = $deviceInfo;
              
    //     // }
        
      
        
       
        
    //     echo json_encode($messageArray);
      
    // }
    
    
    if($servicetype == "UPDATE_STORE_ORDER_STATUS"){
        
        unset($where);
        unset($messageArray);
        unset($update);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) : '4';
        $orderType  = isset($_POST['orderType']) ? trim($_POST['orderType']) : '';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : ''; 
        $status = isset($_POST['status']) ? trim($_POST['status']) : ''; 
        
        if($status == "Confirm Order"){
            
            unset($where);
            $where['iOrderId'] =  $orderId;
            $updateOrder['iStatusCode'] = 3011;
            $result = myQuery("orders", $updateOrder, "update", $where);

            setOrderLogs("3011", $orderId);
            
        }else if($status == "Cancel Order"){
            
            unset($where);
            $where['iOrderId'] =  $orderId;
            $updateOrder['iStatusCode'] = 3010;
            $result = myQuery("orders", $updateOrder, "update", $where);

            setOrderLogs("3010", $orderId);
            
        }
            
        
     
        $sql = "SELECT os.vStatus, od.* FROM orders as od LEFT JOIN order_status as os ON od.iStatusCode = os.iStatusCode  WHERE od.iOrderId = $orderId AND od.iCompanyId = $storeId ORDER BY tOrderRequestDate DESC";
        $statement = $db->query($sql);
        $result = $statement ->fetchAll(); 

        $orderData = array();
        $driverData = array();
        
        for($i = 0; $i < count($result); $i++) {
            
            $itemId_array = "";
            
            $sql2 = "SELECT sum(iQty) as itemQty FROM order_details WHERE iOrderId = '".$result[$i]['iOrderId']."'";
            $statement = $db->query($sql2);
            $itemQty = $statement ->fetchAll();
            
            $sql2 = "SELECT * FROM order_details WHERE iOrderId = '".$result[$i]['iOrderId']."'";
            $statement = $db->query($sql2);
            $itemId = $statement ->fetchAll();
            
            $orderDetails = $itemId;
            
            for($x = 0; $x < count($itemId); $x++){
                
                if($x+1 == count($itemId)){
                    $itemId_array .= $itemId[$x]['iMenuItemId']."";
                }else{
                    $itemId_array .= $itemId[$x]['iMenuItemId'].",";
                }
                
            }
            
            $str_date = @date('Y-m-d H:i:s', strtotime('-30 minutes'));
            $trd_date2 = @date('Y-m-d H:i:s', strtotime($result[$i]['dDate']));
            
            if($trd_date2 < $str_date){
                $timeStatus = "Order expired";
            }else{
                $timeStatus = minuteAgo($result[$i]['dDate']);
            }
            
           
            
            $sql2 = "SELECT vCompanyColor, vRestuarantLocation, vRestuarantLocationLat, vRestuarantLocationLong FROM company WHERE iCompanyId = '".$result[$i]['iCompanyId']."'";
            $statement = $db->query($sql2);
            $company = $statement ->fetchAll();
            $orderData[$i]['orderId'] = $result[$i]['iOrderId'];
            $orderData[$i]['orderNo'] =  $result[$i]['vOrderNo'];
            
            $orderData[$i]['orderDate'] =  $result[$i]['dDate'];
            $orderData[$i]['orderTimeStatus'] = $timeStatus;
            $orderData[$i]['storeName'] =  $result[$i]['vCompany'];
            $orderData[$i]['vName'] =  $result[$i]['vName'];
            $orderData[$i]['itemQty'] = $itemQty[0]['itemQty'];
            $orderData[$i]['orderPrice'] =  $result[$i]['fTotalGenerateFare'];
            $orderData[$i]['orderStatus'] =  $result[$i]['vStatus'];
            $orderData[$i]['orderPaidFrom'] =  $result[$i]['ePaymentOption'];
            $orderData[$i]['storeId'] = $result[$i]['iCompanyId'];
            $orderData[$i]['storeColor'] = $company[0]['vCompanyColor'];
            $orderData[$i]['storeLocation'] = $company[0]['vRestuarantLocation'];
            $orderData[$i]['storeLatitude'] = $company[0]['vRestuarantLocationLat'];
            $orderData[$i]['storeLongitude'] = $company[0]['vRestuarantLocationLong'];
            $orderData[$i]['instruction'] = $result[$i]['vInstruction'];
            $orderData[$i]['read'] = $result[$i]['eRead'];
            $orderData[$i]['driverId'] = $result[$i]['iDriverId'];
            $orderData[$i]['totalOrder'] = count($result);
            $orderData[$i]['itemArray'] = $itemId_array;
            $orderData[$i]['orderdetails'] = $orderDetails;
            
            if($result[$i]['iDriverId'] != "0" || $result[$i]['iDriverId'] != ""){
                
                $sql3 = "SELECT iDriverId, vName, vLastName, vEmail, vPhone, vLatitude, vLongitude FROM register_driver WHERE iDriverId = '".$result[$i]['iDriverId']."'";
                $statement = $db->query($sql3);
                $driverData = $statement ->fetchAll();
                
            }
            
            $sql4 = "SELECT vName, vPhone,  vServiceAddress, vLatitude, vLongitude FROM user_address WHERE iUserAddressId = '". $result[$i]['iUserAddressId']."'";
            $statement = $db->query($sql4);
            $serviceAddress = $statement ->fetchAll();
            
            $orderData[$i]['orderDeliveryName'] =  $serviceAddress[0]['vName']." / ". $serviceAddress[0]['vPhone'];
            $orderData[$i]['orderDeliveryAddress'] =  $serviceAddress[0]['vServiceAddress'];
            $orderData[$i]['orderDeliveryAddressLat'] = $serviceAddress[0]['vLatitude'];
            $orderData[$i]['orderDeliveryAddressLong'] = $serviceAddress[0]['vLongitude'];
            
            $orderData[$i]['driverData'] = $driverData;
            
        }
        
        $messageArray['response'] = 1;
        $messageArray['service'] = $servicetype;
        $messageArray['orderId'] = $orderId;
        $messageArray['status'] = $status;
        $messageArray['notificationCounter'] = countNotifications($userId, "User");
        // $messageArray['result'] =  $orderData;
        // $messageArray['driverData'] = $driverData;
        
        $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
        $statement = $db->query($sql); 
        $profileData = $statement ->fetchAll();  
    
        

        echo json_encode($messageArray);
      
    }

 
    if($servicetype == "CHECK_FOR_PARTNERED_STORES"){


        unset($where);
        unset($messageArray);
        unset($update);
        
        $latitude  = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
        $longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
        $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '54';
        $orderId  = isset($_POST['orderId']) ? trim($_POST['orderId']) : '4';
        $orderType  = isset($_POST['orderType']) ? trim($_POST['orderType']) : '';
        $deviceInfo  = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : ''; 
        $status = isset($_POST['status']) ? trim($_POST['status']) : ''; 


        $sql = "SELECT od.iCompanyId, co.* FROM company as co LEFT JOIN orders as od ON co.iCompanyId = od.iCompanyId  WHERE od.iOrderId = $orderId";
        $statement = $db->query($sql);
        $result = $statement ->fetchAll(); 

        if($result[0]['vStoreCategory'] == "Partner Store"){
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['orderId'] = $orderId;
        
        }else{
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['orderId'] = $orderId;
        }


        echo json_encode($messageArray);

    }


   //$servicetype = "LOAD_MORE_PAGINATION";

    if($servicetype == "LOAD_MORE_PAGINATION"){
        
        $sourceLat = isset($_POST['sourceLat']) ? trim((float)$_POST['sourceLat']) : '14.659654644582378';
        $sourceLong  = isset($_POST['sourceLong']) ? trim((float)$_POST['sourceLong']) :'120.99138591066527';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'1';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $storeId =  isset($_POST['storeId']) ? trim($_POST['userType']) :'User';
        $search = isset($_POST['search']) ? trim($_POST['search']) :'';
        $pagenumber = isset($_POST['pageno']) ? trim($_POST['pageno']) : "1";
        $storeCategory = isset($_POST['storeCategory']) ? trim($_POST['storeCategory']) :'Restaurant';
        
        if($pagenumber == 0 || $pagenumber == "0" || $pagenumber == "") {
            $pageno = 1;
        }else{
            $pageno = (int) $pagenumber;
        }
   
        $sourceLocationArr = array($sourceLat, $sourceLong);
        $restaurant = array();
        $Nearestrestaurant = array();



        if(isLocationAllowed($sourceLocationArr)){
            
            
            if(isLocationAllowedForPabili($sourceLocationArr)){
                
     
        
                //DETERMINE THE NUMBER OF ITEMS PER PAGE AND OFFSET
                $no_of_records_per_page = 30;
            
            
                if($search != ""){
                    $searchSql = " WHERE eStatus = 'Active' AND vStoreCategory = '".$storeCategory."' AND (vMainCompany LIKE '%$search%' OR vMainCompany LIKE '$search%' OR vMainCompany LIKE '%$search' OR vCompany LIKE '%$search%' OR vCompany LIKE '$search%' OR vCompany LIKE '%$search')";
                   //  $searchSql = "WHERE co.eStatus = 'Active' AND co.vMainCompany LIKE '%Jollibee%' OR co.vMainCompany LIKE 'Jollibee%' OR co.vMainCompany LIKE '%Jollibee' OR co.vCompany LIKE '%Jollibee%' OR co.vCompany LIKE 'Jollibee%' OR co.vCompany LIKE '%Jollibee'";
                
                }else{
                   $searchSql = " WHERE eStatus = 'Active' AND vStoreCategory = '".$storeCategory."' ";
                  //  $searchSql = " WHERE co.eStatus = 'Active'";
                }
                        
              
                       
                $sql = "SELECT ROUND(( 6371 * acos( cos( radians($sourceLat) ) 
            
                        * cos( radians( vRestuarantLocationLat ) ) 
            
                        * cos( radians( vRestuarantLocationLong ) - radians($sourceLong) ) 
            
                        + sin( radians( $sourceLat) ) 
            
                        * sin( radians( vRestuarantLocationLat ) ) ) ),2) AS distance, vMainCompany, vAvgRating as totalOrders,   FROM company

                        HAVING distance < " . constants::LIST_RESTAURANT_LIMIT_BY_DISTANCE . " ORDER BY iCompanyId ASC";
                        
                $sql = "SELECT vCaddress, vRestuarantLocationLat,  vRestuarantLocationLong, vRestuarantLocationLat as distance, vAvgRating as totalOrders, vAvgRating as openHour, vAvgRating as closeHour, vAvgRating as storeStatus, vMainCompany,  company.* FROM company ";
                $sql .= $searchSql ;
                      
                $statement = $db->query($sql); 
                $result = $statement ->fetchAll();  
                
                
                //TOTAL ROWS AND TOTAL PAGE
                $total_rows = count($result);
                $total_pages = ceil($total_rows / $no_of_records_per_page);
                
                $sql .= $searchSql ;
              
                
                       
                // $statement = $db->query($sql); 
                // $result = $statement ->fetchAll();  
                
                
                if(count($result) > 0){
                            
                    $count = 0;
                    
                    for($x = 0; $x < count($result) ; $x++){
                        
                        $distance = distance( $sourceLat, $sourceLong, $result[$x]['vRestuarantLocationLat'], $result[$x]['vRestuarantLocationLong'], "K");
                        
                        $sqlo = "SELECT count(*) as totalOrders FROM orders WHERE DATE(tOrderRequestDate) = CURDATE() AND iCompanyId = '".$result[$x]['iCompanyId']."'";
                        
                        $statement = $db->query($sqlo); 

                        $resultTotalOrders = $statement ->fetchAll();  
                        
                        $totalOrdersToday = $resultTotalOrders[0]['totalOrders'];
                      
                        $result[$x]['totalOrders'] = $totalOrdersToday;
                        
                        $result[$x]['distance'] =  number_format((float)$distance, 2, '.', '');
                        
                        $result[$x]['vImage'] =  "http://mallody.ph/uploads/Company/".$result[$x]['vImage'];
                        
                        
                        $result[$x]['vRestuarantLocationLat'] =  $result[$x]['vRestuarantLocationLat'];
                        
                        $result[$x]['vRestuarantLocationLong'] = $result[$x]['vRestuarantLocationLong'];
                        
                        if(isTodayWeekend()){
                            
                            $result[$x]['openHour'] = $result[$x]['vFromSatSunTimeSlot1'];
                            $result[$x]['closeHour'] = $result[$x]['vToSatSunTimeSlot1'];
                            
                        }else{
                            
                            $result[$x]['openHour'] = $result[$x]['vFromMonFriTimeSlot1'];
                            $result[$x]['closeHour'] = $result[$x]['vToMonFriTimeSlot1'];
                        }
                        
                        $current_time = date("h:i a");
                        $begin = $result[$x]['openHour'];
                        $end   = $result[$x]['closeHour'];
                        
                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                        $date2 = DateTime::createFromFormat('H:i:s', $begin);
                        $date3 = DateTime::createFromFormat('H:i:s', $end);
                        
                        if($date1 > $date2 && $date1 < $date3 ){
                            
                            $result[$x]['storeStatus'] = "Open";
                            
                        }else{
                            
                            if( $date3 <= $date2){
                                
                              if( $date1 > $date2 || $date1 < $date3  ){
                                   
                                    $result[$x]['storeStatus'] = "Open";
                              }else{
                                    $result[$x]['storeStatus'] = "Close";
                              }
                                
                            }else{
                                 $result[$x]['storeStatus'] = "Close";
                            }
                            
                           
                        }
                        
                        if($distance <= constants::LIST_RESTAURANT_LIMIT_BY_DISTANCE){
                            
                            array_push($Nearestrestaurant,  $result[$x]);
                        }

                    }

                    //SORTED
                    for($i=0; $i<count($Nearestrestaurant)-1; $i++) {
                        for($j=0; $j<count($Nearestrestaurant)-1; $j++)
                        {
                            if($Nearestrestaurant[$j]['distance'] > $Nearestrestaurant[$j+1]['distance']){
                              $restaurant= $Nearestrestaurant[$j+1];
                                $Nearestrestaurant[$j+1]= $Nearestrestaurant[$j];
                                $Nearestrestaurant[$j]=$restaurant;
                            }
                        }
                    }
                
                    $paginatedNearestrestaurants = paging_from_multi_arr($pageno, $Nearestrestaurant, $no_of_records_per_page);

                    // for($i=0; $i<count($paginatedNearestrestaurants); $i++) {
                    //    echo "Name ".$paginatedNearestrestaurants[$i]['vCompany'];
                    //                           echo "<br>";
                    // }

                    
                    
                    $messageArray['response'] = 1;
                    $messageArray['service'] = $servicetype;
                    $messageArray['notificationCounter'] = countNotifications($userId, "User");
                    $messageArray['success'] = count($result). " stores found";
                    $messageArray['result'] = $paginatedNearestrestaurants;
                    $messageArray['totalStores'] = count($Nearestrestaurant);
                    $messageArray['totalPage'] = ceil(count($Nearestrestaurant) / $no_of_records_per_page);
                    $messageArray['currentPage'] = $pageno;
                    $messageArray['NextPage'] = ($pageno < ceil(count($Nearestrestaurant) / $no_of_records_per_page))? ($pageno+1)."" : "0";
                    //  $messageArray['result'] =  $result;

                 
                }else{
                    
                    $messageArray['response'] = 0;
                    $messageArray['error'] = "No stores found";
                    
                }
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['error'] = "Coming Soon.";
            }
            
            
        }else{
            
            $messageArray['response'] = 0;
            $messageArray['error'] = "Out of Service Area";
          
          
        }
        
        // $sql = "SELECT * FROM register_user WHERE iUserId = '". $userId."'";
           
        // $statement = $db->query($sql); 

        // $profileData = $statement ->fetchAll();  
        
        
        // if($deviceInfo != $profileData[0]['tDeviceData']){
                
        //         unset($messageArray);
        //         $messageArray['response'] = 0;
        //         $messageArray['service'] = $servicetype;
        //         $messageArray['userType'] = $userType;
        //         $messageArray['error'] = "AUTO_LOGOUT";
        //         $messageArray['deviceInfo'] = $deviceInfo;
              
        // }
       
        
        echo json_encode($messageArray);
    }
    

    
    
    

    
    $database->closeConnection();
    
    



?>