<?php
	header('Content-type: application/json');
    ini_set('display_errors',1);
    date_default_timezone_set("Asia/Manila");

    //WANDERTREATS
     
   
    include_once('../webservice/config.php');
    include_once('../webservice/db_info.php');
    include_once('../webservice/general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();
    
    $data = array();
    $notifData = array();


    $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '10';
    $title = isset($_POST['title']) ? trim($_POST['title']) : 'Admin';
    $message  = isset($_POST['message']) ? trim($_POST['message']) : 'sasas';
    $description  = isset($_POST['description']) ? trim($_POST['description']) : 'asasas';
    $target = isset($_POST['target']) ? trim($_POST['target']) : 'User';
    $image = isset($_POST['image']) ? trim($_POST['image']) : 'asasasas';


    $sql = "SELECT * FROM register_user";

    $statement = $db->query($sql);

    $users = $statement->fetchAll();

    for($x = 0; $x < count($users); $x++){

        $user = $users[$x];




        $data['title'] = $title;
   
        //NOTIFCATION FOREGROUND
        $data['activity'] = "AUTO_LOGOUT";
        $data['message'] = $message;
        $data['description'] = $description;
        $data['image'] = $image;


        $notifData['iUserId'] = $user['iUserId'];
        $notifData['vUserType'] = $target;
        $notifData['vTitle'] = $title;
        $notifData['vDescription'] = $description;
        $notifData['vType'] = '';
        $notifData['vImage'] = '';
        $notifData['vUrl'] = '';
        $notifData['vIntent'] = '';
        $notifData['vSent'] = '';

        $notifData['eStatus'] = 'Unread';


        $adminId = myQuery("notifications",  $notifData, "insert_getlastid");

        $result = notify($target, $user['iUserId'], $data);


    }

   

    echo safe_json_encode($result);


   //echo "USER ID:". $userId;


?>