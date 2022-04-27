
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
    $adminLevel = isset($_REQUEST['adminLevel']) ? trim($_REQUEST['adminLevel']) : '123456';
    $displayPhoto = isset($_REQUEST['displayPhoto']) ? trim($_REQUEST['displayPhoto']) : 'profile.png';
    $eStatus = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : 'Active';

    unset($updateData);
    $where['iAdminId'] = $adminId;
    $updateData['vFirstName'] = $firstName;
    $updateData['vLastName'] = $lastName;
    $updateData['vEmail'] = $email;
    $updateData['vUserName'] = $userName;
    $updateData['vPassword'] =  $password;
    $updateData['vMobile'] =  $mobileNumber;
    $updateData['vAdminLevel'] =  $adminLevel;
    $updateData['vImage'] = $displayPhoto;
    $updateData['eStatus'] = $eStatus ;



    $adminId = myQuery("administrator", $updateData, "update", $where);


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
