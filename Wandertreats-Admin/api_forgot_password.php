<?php
	//RENCEVTERANS 11/14/2021

ini_set('display_errors',1);
include_once('general_functions.php');

	$messageArray = array();
	$where = array();
	$result = array();

    unset($messageArray);

    $servicetype = isset($_POST['ServiceType']) ? trim($_POST['ServiceType']) : 'CHECK_EMAIL';
    
    

    if($servicetype == "RESET_PASSWORD"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'17';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'laurencevegerano@gmail.com';
    
    
        $token = getToken(49);
        
        if( $userType == "User"){
            
            $sql = "SELECT * FROM register_user WHERE vEmail = '". $email."'";
            $statement = $obj->query($sql); 
            $profileData = $statement ->fetchAll();  
            
            unset($where);
            $where['vEmail'] = $email;
            $updatepasswordToken['vPasswordToken'] = $token;
            $updatepasswordToken['vPasswordTokenDate'] = @date("Y-m-d H:i:s");
            $result = myQuery("register_user", $updatepasswordToken, "update", $where);
            
            if(count($profileData ) > 0){
                
                sendResetEmail($email, $profileData[0]['vName'], $token, $userType, $profileData[0]['iUserId']);
                
                $messageArray['response'] = 1;
                $messageArray['service'] = $servicetype;
                $messageArray['userType'] = $userType;
                $messageArray['token'] = $token;
                $messageArray['email'] = $email;
                $messageArray['status'] =  "OKAY";
                $messageArray['message'] =  "Email Exist";
                
            }else{
                
                $messageArray['response'] = 0;
                $messageArray['service'] = $servicetype;
                $messageArray['userType'] = $userType;
                $messageArray['token'] = $token;
                $messageArray['email'] = $email;
                $messageArray['status'] =  "Failed";
                $messageArray['message'] =  "Email not exist";
                
            }
            
         
        }
        
        echo json_encode( $messageArray);
      
    }


    if($servicetype == "CHECK_EMAIL"){
        
        unset($messageArray);
        unset($where);
        
        $sourceLat = isset($_POST['sourceLat']) ? trim($_POST['sourceLat']) : '1212';
        $sourceLong  = isset($_POST['sourceLong']) ? trim($_POST['sourceLong']) :'212';
        $userId =  isset($_POST['userId']) ? trim($_POST['userId']) :'';
        $userType =  isset($_POST['userType']) ? trim($_POST['userType']) :'User';
        $email =  isset($_POST['email']) ? trim($_POST['email']) :'laurencevegerano@gmail.com';
        
        $result =  checkEmailExist($userType, $email);

        if($result ==  0){
            
                
            $messageArray['response'] = 0;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "FAILED";
            $messageArray['message'] =  "Email Exist!";
      
            
            echo json_encode( $messageArray);
            
        }else{
            
                
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "OKAY";
            $messageArray['message'] =  "Successfull Updated!";
            
            echo json_encode( $messageArray);
            
        }
        
        
        
    }


?>