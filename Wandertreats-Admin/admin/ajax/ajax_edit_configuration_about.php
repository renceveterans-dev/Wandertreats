<?php
    header('Content-type: application/json');
    ini_set('display_errors',1);
	date_default_timezone_set("Asia/Manila");

	//WANDERTREATS
    $where = array();
    unset($where);
    unset($updateData);

   
    include_once('../webservice/config.php');
    include_once('../webservice/db_info.php');
    include_once('../webservice/general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();
    
    $registerData = array();
    $registerData = array();


    $id  = isset($_REQUEST['ConfigId']) ? clean_data($_REQUEST['ConfigId']) : '222';
    $value  = isset($_REQUEST['ConfigValue']) ? clean_data($_REQUEST['ConfigValue']) : '222';
    //echo safe_json_encode(  $data );
    // foreach ($dataArray as $key => $value) {

    $where['iConfigId'] = $id;
    $updateData['vConfigValue'] = stripcslashes($value);
    $id = myQuery("configurations_about",  $updateData, "update", $where);
    $sql = "SELECT * FROM configurations_about";               
    $statement =  $db->query($sql); 
    $result = $statement ->fetchAll(); 

    unset($messageArray);
    $messageArray['action'] = 1;  
    $messageArray['result'] = $result;
        

    echo safe_json_encode($messageArray);

    function clean_data($data) {
            /* trim whitespace */
        $data = trim($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    //echo json_encode($administrators);


           
 ?>
