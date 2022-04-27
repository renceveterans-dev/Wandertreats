<?php
    header('Content-type: application/json');
    ini_set('display_errors',1);
	date_default_timezone_set("Asia/Manila");

	//WANDERTREATS
     
    unset($where);
    unset($updateData);

   
    include_once('../webservice/config.php');
    include_once('../webservice/db_info.php');
    include_once('../webservice/general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();
    
    $registerData = array();
    $registerData = array();


    $data  = isset($_REQUEST['data']) ? trim($_REQUEST['data']) : '1';
    $dataArray = json_decode($data, true);

    foreach ($dataArray as $key => $value) {

        $where['iConfigId'] = $value['id'];
        $updateData['vConfigValue'] = $value['value'];
        $id = myQuery("configurations",  $updateData, "update", $where);
    }
    
   
    



   


    $sql = "SELECT * FROM configurations";               
    $statement =  $db->query($sql); 
    $result = $statement ->fetchAll(); 

    unset($messageArray);
    $messageArray['action'] = 1;  
    $messageArray['data'] = $dataArray;
    $messageArray['result'] = $result;
        

   echo safe_json_encode($messageArray);
    //echo json_encode($administrators);


           
 ?>
