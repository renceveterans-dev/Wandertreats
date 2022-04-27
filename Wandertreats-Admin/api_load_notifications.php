<?php
	//RENCEVTERANS 12/03/2021
    header('Content-type: application/json');
    ini_set('display_errors',1);
    include_once('general_functions.php');

	$messageArray = array();

    unset($messageArray);
    
    $userId  = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) : '6';
    $userType  = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) : '';
    $latitude  = isset($_REQUEST['latitude']) ? trim($_REQUEST['latitude']) : '';
    $longitude  = isset($_REQUEST['longitude']) ? trim($_REQUEST['longitude']) : '';
    $notifId  = isset($_REQUEST['notifId']) ? trim($_REQUEST['notifId']) : '';
    $openNotification  = isset($_REQUEST['openNotification']) ? trim($_REQUEST['openNotification']) : 'false';
   
    if($openNotification == "false"){


        //LOAD USER NOTIFICAIONS

        $sql = "SELECT * FROM  notifications WHERE iUserId = '".$userId."' ORDER BY dDateCreated DESC";
        $statement = $obj->query($sql); 
        $notifications = $statement ->fetchAll(); 


        if(count($notifications)>0){

            $messageArray['action'] = 1;  
            $messageArray['message'] = "success";
            $messageArray['notificationCount'] = count($notifications);
            $messageArray['data'] =  $notifications;
            
        }else{

            $messageArray['action'] = 0; 
             $messageArray['userId'] = $userId;  
            $messageArray['message'] = "error";
            $messageArray['warnings'] = "No notifications";
        }
    }
    


    if($notifId != "" && $openNotification == "true"){

        $where = array();
        $where['iNotificationId'] = $notifId;
        $update = array();
        $update['eStatus'] = "Read";
        $result = myQuery("notifications", $update, "update", $where);

    }
   

   echo safe_json_encode($messageArray);

?>