<?php

ini_set('display_errors', 0);

date_default_timezone_set("Asia/Manila");

session_start();

$sessionId = session_id();

//TRIKAROO UPLOAD WEBSERVICE

    include_once('config.php');
    include_once('general_functions.php');
     

    $database = new Connection();
    
    $db = $database->openConnection();
    
    $servicetype  = isset($_POST['ServiceType']) ? trim($_POST['ServiceType']) : '';
    $usertype  = isset($_POST['userType']) ? trim($_POST['userType']) : '';
    $userId  = isset($_POST['userId']) ? trim($_POST['userId']) : '';
    $filename =  isset($_FILES['image']['name']) ?  $_FILES['image']['name'] : '';
    
    $messageArray = array();
    
    $messageArray['response'] = 0;
    
    $dir = $_SERVER['DOCUMENT_ROOT']."/uploads/";
    
    $target_path = "";
    
    if($servicetype == "UPLOAD_PROFILE_PHOTO" &&  $filename != "" ){
        
        unset($messageArray);
        unset($where);
        
        $path =  $dir."/Profile/". $usertype."/".  $userId;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }
        
        $update['vImgName'] = $filename;
        
        
        if( $usertype == "Driver"){
            $where['iDriverId'] =  $userId;
            $result = myQuery("register_driver",  $update, "update",  $where);
        }
        
        if( $usertype == "User"){
            $where['iUserId'] =  $userId;
            $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename);
            
            //$messageArray['success'] = "Update";
        }
    
       
    }
    
    
    if($servicetype == "CASHIN_FROM_GCASH" &&  $filename != "" ){
        
        unset($messageArray);
        unset($where);
        
        $path =  $dir."/CashIn/GCash";
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }
        
        
        
        if( $usertype == "User"){
            // $where['iUserId'] =  $userId;
            // $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename);
            
            //$messageArray['success'] = "Update";
        }
         
    }
    
    
     if($servicetype == "CASHIN_FROM_PAYMAYA" &&  $filename != "" ){
        
        unset($messageArray);
        unset($where);
        
        $path =  $dir."/CashIn/Paymaya";
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }
        
        
        
        if( $usertype == "User"){
            // $where['iUserId'] =  $userId;
            // $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename);
            
            //$messageArray['success'] = "Update";
        }
         
    }
    
    
    if($servicetype == "UPLOAD_PRODUCT_PHOTO_MANUALLY"){
        
        unset($messageArray);
        unset($where);
        
        $path =  $dir."/Products/AddManually";
        
        $publicpath = "http://mallody.ph/uploads/Products/AddManually/".$filename;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }
        
        
        
        if( $usertype == "User"){
            // $where['iUserId'] =  $userId;
            // $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename,  $publicpath);
            
            //$messageArray['success'] = "Update";
        }
    }
    
    
    if($servicetype == "REPORT_ORDER"){
        
        unset($messageArray);
        unset($where);
        
        $path =  $dir."/Reports/". $usertype;
        
         $publicpath = "http://mallody.ph/uploads/Reports/". $usertype."/".$filename;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }
        
        
        
        if( $usertype == "User"){
            // $where['iUserId'] =  $userId;
            // $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename);
            
            //$messageArray['success'] = "Update";
        }
        
         if( $usertype == "Driver"){
            // $where['iUserId'] =  $userId;
            // $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename);
            
            //$messageArray['success'] = "Update";
        }
    }
    
    if($servicetype == "DRIVER_REGISTRATION"){
        
        unset($messageArray);
        unset($where);
        
        $path =  $dir."/Registrations/". $usertype;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }
        
        
        
        if( $usertype == "User"){
            // $where['iUserId'] =  $userId;
            // $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename);
            
            //$messageArray['success'] = "Update";
        }
        
         if( $usertype == "Driver"){
            // $where['iUserId'] =  $userId;
            // $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename);
            
            //$messageArray['success'] = "Update";
        }
    }
    
    
    
     if($servicetype == "CANCEL_ORDER"){
        
        unset($messageArray);
        unset($where);
        
        $path =  $dir."/CancelOrder/". $usertype;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }
        
        
        
        if( $usertype == "User"){
            // $where['iUserId'] =  $userId;
            // $result = myQuery("register_user",  $update, "update",  $where);
            
             $target_path = $path."/". basename($_FILES['image']['name']);
             moveUploadFile($target_path, $filename);
            
            //$messageArray['success'] = "Update";
        }
    }
    
    
    
  
    
    
    
    function moveUploadFile($targetpath,  $filename, $publicpath = null){
        
        $extension = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
    
        if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') {
            
            try {
                // Throws exception incase file is not being moved
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetpath)) {
                    // make error flag true
                    echo json_encode(array('status'=>'fail', 'message'=>'could not move file'));
                }
                // File successfully uploaded
                echo json_encode(array('status'=>'success', 'message'=>'File Uploaded', 'filename'=>$filename, 'url'=> $publicpath));
            } catch (Exception $e) {
                // Exception occurred. Make error flag true
                echo json_encode(array('status'=>'fail', 'message'=>$e->getMessage()));
            }
           
        } else {
            // File parameter is missing
            echo json_encode(array('status'=>'fail', 'message'=>'Not received any file'));
            
        }
        
    }
    


   


    
    
    
    
    //echo json_encode( $messageArray) ;
