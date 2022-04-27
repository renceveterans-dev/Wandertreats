<?php
	//RENCEVTERANS 11/14/2021

ini_set('display_errors',1);
include_once('general_functions.php');

	$messageArray = array();
    $deviceArr = array();
	$where = array();
	$result = array();

    unset($messageArray);
    
    $username  = isset($_POST['username']) ? trim($_POST['username']) : '09398296855';
    $password  = isset($_POST['password']) ? trim($_POST['password']) : 'K-anne07k';
    $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'User';
    $deviceInfo = isset($_POST['deviceInfo']) ? trim($_POST['deviceInfo']) : '';
    $vFirebaseDeviceToken = isset($_POST['vFirebaseDeviceToken']) ? trim($_POST['vFirebaseDeviceToken']) : '';
    $sourceLat = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
    $sourceLong  = isset($_POST['longitude']) ? trim($_POST['longitude']) :'';
    $deviceArray['deviceHeight'] = isset($_POST['deviceHeight']) ? trim($_POST['deviceHeight']) : '';
    $deviceArray['deviceWidth'] = isset($_POST['deviceWidth']) ? trim($_POST['deviceWidth']) : '';
    $deviceArray['GeneralAppVersionCode'] =  isset($_POST['GeneralAppVersionCode']) ? trim($_POST['GeneralAppVersionCode']) : '';
    $deviceArray['GeneralAppVersion'] = isset($_POST['GeneralAppVersion']) ? trim($_POST['GeneralAppVersion']) : '';
    $deviceArray['GeneralDeviceType'] = isset($_POST['GeneralDeviceType']) ? trim($_POST['GeneralDeviceType']) : '';
    $deviceArray['vUserDeviceCountry'] = isset($_POST['vUserDeviceCountry']) ? trim($_POST['vUserDeviceCountry']) : '';

   
    $result1 =  checkEmail($userType, $username);
    $result2 =  checkMobileNumber($userType, $username);
   

    if($result1 == 0 || $result2 == 0){

    	// echo "<br>hello";
        
        unset($where);
        
        $result = checkPassword($userType, $username, $password);

        // echo "Check Pasword : ".$result;
        
        if(count( $result) > 0){
            unset($where);
            
            $token = getToken(10);
            
            
            if($userType == "User"){
                
  				$sql = "SELECT * FROM register_user WHERE iUserId = '". $result[0]['iUserId']."'  AND eStatus = 'Active'";
                   
                $statement = $obj->query($sql); 
        
                $profileData = $statement ->fetchAll(); 
                                
              
                
                // if($deviceInfo != $profileData[0]['tDeviceData']){
                      
                //      //NOTIFCATION FOR LOGIN FROM OTHER DEVICE
                //     $data['title'] = "Login Alert";
                //     $data['description'] = "You have login from other device.";
                //     //NOTIFCATION FOREGROUND
                //     $data['activity'] = "AUTO_LOGOUT";
                //     $data['message'] = "You have login from other device.";
                
                //     notify("User", $profileData[0]['iUserId'], $data);
                // }
              
                
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
                    $updateSession['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
                    $updateSession['vLoginAttemptCount'] = 0;
                    $resultUpdate = myQuery("register_user",  $updateSession, "update",  $where);


                    $messageArray['action'] = 1;
                    $messageArray['iUserId'] = $result[0]['iUserId'];
                    $messageArray['message'] = "SUCCESSS";
                    $messageArray['service'] = $servicetype;
                    $messageArray['sessionId'] = $sessionId;
                    $messageArray['success'] = "Logged in";
                    $messageArray['result'] = $profileData[0];
                    
                }
                        
                
            
            }
                 
            
        }else{

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
                
            $messageArray['response'] = 0;
            $messageArray['label'] = "password";
            $messageArray['error'] = "Invalid or wrong password";
            $messageArray['attempt'] = $attemptCountfinal[0]['vLoginAttemptCount'];
                
        }
       
        
    }else{
        
        $messageArray['response'] = 0;
        $messageArray['label'] = "email";
        $messageArray['error'] = "Invalid or wrong email";
    }
  
    
   echo json_encode($messageArray);
   
// }


?>