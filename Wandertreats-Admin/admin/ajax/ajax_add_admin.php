
<?php
    header('Content-type: application/json');
    ini_set('display_errors',1);
    date_default_timezone_set("Asia/Manila");

    //WANDERTREATS
     
    unset($where);
    unset($update);

    
   
    include_once('../webservice/config.php');
    include_once('../webservice/db_info.php');
    include_once('../webservice/general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();
    
    $registerData = array();


    $sql = "SELECT * FROM administrator";

    $statement = $db->query($sql);

    $administrators = $statement->fetchAll();

    $adminId  = isset($_REQUEST['adminId']) ? trim($_REQUEST['adminId']) : '1';
    $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'Admin';
    $firstName  = isset($_REQUEST['firstName']) ? trim($_REQUEST['firstName']) : 'John';
    $lastName = isset($_REQUEST['lastName']) ? trim($_REQUEST['lastName']) : 'Doe';
    $userName = isset($_REQUEST['userName']) ? trim($_REQUEST['userName']) : 'johndoe';
    $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : 'demo@demo.com';
    $mobileNumber = isset($_REQUEST['mobileNumber']) ? trim($_REQUEST['mobileNumber']) : '09309296855';
    $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '123456';
    $displayPhoto = isset($_REQUEST['displayPhoto']) ? trim($_REQUEST['displayPhoto']) : 'profile.png';
    $eStatus = isset($_REQUEST['eStatus']) ? trim($_REQUEST['eStatus']) : 'Active';

    unset($registerData);
    $registerData['vFirstName'] = $firstName;
    $registerData['vLastName'] = $lastName;
    $registerData['vEmail'] = $email;
    $registerData['vUserName'] = $userName;
    $registerData['vPassword'] =  $password;
    $registerData['vImage'] = $displayPhoto;



    $adminId = myQuery("administrator", $registerData, "insert_getlastid");


    $sql = "SELECT * FROM administrator WHERE iAdminId = '".$adminId."'";               
    $statement = $obj->query($sql); 
    $adminData = $statement ->fetchAll(); 

    unset($messageArray);
    $messageArray['action'] = 1;  
    $messageArray['iUserId'] =  $adminId;
    $messageArray['message'] = "success";
    $messageArray['result'] = $adminData;
        

 

   echo safe_json_encode($messageArray);
    //echo json_encode($administrators);


           
 ?>
