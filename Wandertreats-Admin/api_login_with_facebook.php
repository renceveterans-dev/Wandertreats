<?php
    //RENCEVTERANS 12/20/2021

    ini_set('display_errors',1);
    include_once('general_functions.php');

    $messageArray = array();
    $deviceArray = array();
    $where = array();
    $result = array();
    $registerData = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'User';
    $firstName  = isset($_REQUEST['firstName']) ? trim($_REQUEST['firstName']) : 'K-anne07k';
    $lastName = isset($_REQUEST['lastName']) ? trim($_REQUEST['lastName']) : 'User';
    $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
    $mobileNumber = isset($_REQUEST['mobileNumber']) ? trim($_REQUEST['mobileNumber']) : '';
    $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
    $displayPhoto = isset($_REQUEST['displayPhoto']) ? trim($_REQUEST['displayPhoto']) : '';

    $vFirebaseDeviceToken = isset($_POST['vFirebaseDeviceToken']) ? trim($_POST['vFirebaseDeviceToken']) : '';
    $sourceLat = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
    $sourceLong  = isset($_POST['longitude']) ? trim($_POST['longitude']) :'';
    $deviceArray['deviceHeight'] = isset($_POST['deviceHeight']) ? trim($_POST['deviceHeight']) : '';
    $deviceArray['deviceWidth'] = isset($_POST['deviceWidth']) ? trim($_POST['deviceWidth']) : '';
    $deviceArray['GeneralAppVersionCode'] =  isset($_POST['GeneralAppVersionCode']) ? trim($_POST['GeneralAppVersionCode']) : '';
    $deviceArray['GeneralAppVersion'] = isset($_POST['GeneralAppVersion']) ? trim($_POST['GeneralAppVersion']) : '';
    $deviceArray['GeneralDeviceType'] = isset($_POST['GeneralDeviceType']) ? trim($_POST['GeneralDeviceType']) : '';
    $deviceArray['vUserDeviceCountry'] = isset($_POST['vUserDeviceCountry']) ? trim($_POST['vUserDeviceCountry']) : '';

    $result1 =  checkEmailExist($userType, $email);
    $result2 =  checkMobileNumber($userType, $mobileNumber);

    if($result1 != 0 || $result2 != 0){


        unset($registerData);
        $registerData['vName'] = $firstName;
        $registerData['vLastName'] = $lastName;
        $registerData['vEmail'] = $email;
        $registerData['vPhone'] = $mobileNumber;
        $registerData['vPassword'] =  $password;
        $registerData['vImage'] = $displayPhoto;

        // REGISTER PROFILE
        $userId = myQuery("register_user", $registerData, "insert_getlastid");

    }



    $result1 =  checkEmailExist($userType, $email);
    $result2 =  checkMobileNumber($userType, $mobileNumber);


   

    if($result1 == 0 || $result2 == 0){

        //LOGING IN THE ACCOUNT
        
        unset($where);
        
        $result = checkPassword($userType, $username, $password);
        
        if(count( $result) > 0){
            unset($where);
            
            $token = getToken(10);

            if($userType == "User"){
                
                $sql = "SELECT * FROM register_user WHERE iUserId = '". $result[0]['iUserId']."'  AND eStatus = 'Active'";
                   
                $statement = $obj->query($sql); 
        
                $profileData = $statement ->fetchAll(); 
                
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
                    $updateSession['vLatitude'] =  $sourceLat;
                    $updateSession['vLongitude'] =  $sourceLong;
                    $updateSession['tDeviceData'] = json_encode($deviceArray);
                    $updateSession['vLoginAttemptCount'] = 0;
                    $updateSession['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
                    $resultUpdate = myQuery("register_user",  $updateSession, "update",  $where);


                    $messageArray['action'] = 1;
                    $messageArray['iUserId'] = $result[0]['iUserId'];
                    $messageArray['message'] = "SUCCESSS";
                    $messageArray['service'] = $servicetype;
                    $messageArray['sessionId'] = $sessionId;
                    $messageArray['success'] = "Logged in";
                    $messageArray['result'] = $profileData[0];
                    
                }

                echo json_encode($messageArray);
                exit();

            }
                 
        }else{

                
            $messageArray['response'] = 0;
            $messageArray['label'] = "password";
            $messageArray['error'] = "Invalid or wrong password";
            $messageArray['attempt'] = $attemptCountfinal[0]['vLoginAttemptCount'];

            echo json_encode($messageArray);
            exit();
                
        }
       
        
    }else{

        //REGISTERING THE ACCOUNT
        
        unset($registerData);
        $registerData['vName'] = $firstName;
        $registerData['vLastName'] = $lastName;
        $registerData['vEmail'] = $email;
        $registerData['vPhone'] = $mobileNumber;
        $registerData['vPassword'] =  $password;
        
        // REGISTER PROFILE
        $userId = myQuery("register_user", $registerData, "insert_getlastid");


        $sql = "SELECT * FROM register_user WHERE iUserId = '".$userId."'";               
        $statement = $obj->query($sql); 
        $profileData = $statement ->fetchAll(); 

        unset($messageArray);
        $messageArray['action'] = 1;  
        $messageArray['iUserId'] =  $userId;
        $messageArray['message'] = "success";
        $messageArray['result'] = $profileData[0];
            

        echo json_encode($messageArray);
        exit();
   
    }
   


?>