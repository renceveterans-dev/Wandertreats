<?php
	//RENCEVTERANS 11/14/2021

    ini_set('display_errors',1);
    include_once('general_functions.php');

	$messageArray = array();
	$where = array();
	$result = array();
    $update = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '1';
    $firstName  = isset($_REQUEST['firstName']) ? trim($_REQUEST['firstName']) : 'K-anne07k';
    $lastName = isset($_REQUEST['lastName']) ? trim($_REQUEST['lastName']) : 'User';
    $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
    $mobileNumber = isset($_REQUEST['mobileNumber']) ? trim($_REQUEST['mobileNumber']) : '';

    unset($update);
    $update['vName'] = $firstName;
    $update['vLastName'] = $lastName;
    $update['vEmail'] = $email;
    $update['vPhone'] = $mobileNumber;
    
    // //UPDATE PROFILE
    $where['iUserId'] =  $userId;
    $result = myQuery("register_user",  $update, "update",  $where);


    $sql = "SELECT * FROM register_user WHERE iUserId = '".$userId."'  AND eStatus = 'Active'";               
    $statement = $obj->query($sql); 
    $profileData = $statement ->fetchAll(); 


    $messageArray['action'] = 1;  
    $messageArray['message'] = "success";
    $messageArray['data'] = $profileData[0];
        

    echo json_encode($messageArray);
   


?>