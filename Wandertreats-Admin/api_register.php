<?php
	//RENCEVTERANS 11/14/2021

    ini_set('display_errors',1);
    include_once('general_functions.php');

	$messageArray = array();
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

    $result1 =  checkEmailExist($userType, $email);
    $result2 =  checkMobileNumber($userType, $mobileNumber);
   

    if($result1 == 0){
        $messageArray['action'] = 0;  
        $messageArray['message'] = "fail";
        $messageArray['error'] = "Email already registered.";
        echo json_encode($messageArray);
        exit();
    }

    if($result2 == 0){
        $messageArray['action'] = 0;  
        $messageArray['message'] = "fail";
        $messageArray['error'] = "Mobile number already registered.";
        echo json_encode($messageArray);
        exit();
    }


    unset($registerData);
    $registerData['vName'] = $firstName;
    $registerData['vLastName'] = $lastName;
    $registerData['vEmail'] = $email;
    $registerData['vPhone'] = $mobileNumber;
    $registerData['vPassword'] =  $password;
    $registerData['vImage'] = $displayPhoto;

    // REGISTER PROFILE
    $userId = myQuery("register_user", $registerData, "insert_getlastid");


    $sql = "SELECT * FROM register_user WHERE iUserId = '".$userId."'  AND eStatus = 'Active'";               
    $statement = $obj->query($sql); 
    $profileData = $statement ->fetchAll(); 

    unset($messageArray);
    $messageArray['action'] = 1;  
    $messageArray['iUserId'] =  $userId;
    $messageArray['message'] = "success";
    $messageArray['result'] = $profileData[0];
        

    echo json_encode($messageArray);
   


?>