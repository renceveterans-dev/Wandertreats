
<?php

    ini_set('display_errors', 1);
    date_default_timezone_set("Asia/Manila");

    //TRIKAROO AJAX_GET ALL DRIVERS LOCATION
     
   
    include_once('../webservice/config.php');
    include_once('../webservice/db_info.php');
    include_once('../webservice/general_functions.php');

   

    $id = $_POST['id'];

    $key = $_POST['key'];

    $tablename = $_POST['tablename'];

    $database = new Connection();

    $db = $database->openConnection();

    // $sql = "DELETE FROM $tablename WHERE $key = '".$id."'";

    // $statement = $db ->prepare($sql);

    // $result = $statement->execute();

    unset($updateData);
    $where[$key] = $id;
    $updateData['eStatus'] ='Deleted';

    $result = myQuery($tablename, $updateData, "update", $where);


    // $sql = "UPDATE $tablename SET eStatus = 'Deleted' WHERE $key = '".$id."'";

    // $statement = $db ->prepare($sql);

    // $result = $statement->execute();

?>