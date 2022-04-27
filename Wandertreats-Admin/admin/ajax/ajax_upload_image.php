<?php

    ini_set('display_errors', 0);

    date_default_timezone_set("Asia/Manila");

    session_start();

    $sessionId = session_id();

    //UPLOAD WEBSERVICE
    include_once('../webservice/config.php');
    include_once('../webservice/db_info.php');
    include_once('../webservice/general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();

    $messageArray = array();
    
    $messageArray['response'] = 0;
    
    $dir = $_SERVER['DOCUMENT_ROOT']."/wandertreats/uploads/";
    
    $target_path = "";

    //PARAMETERS
    $servicetype  = isset($_POST['serviceType']) ? trim($_POST['serviceType']) : '';
    $filename =  isset($_FILES['file']['name']) ?  $_FILES['file']['name'] : '';
  

    $messageArray['filename'] = $filename;
    $messageArray['servicetype'] = $servicetype;
    
    $messageArray['base_directory'] =  $dir;


    //UPLOAD_STORE_LOGO

    if($servicetype == "UPLOAD_STORE_LOGO" &&  $filename != "" ){

        $storeId =  isset($_POST['storeId']) ?  $_POST['storeId'] : '';

        $path =  $dir."profile/store/".$storeId;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }

        $target_path = $path."/". basename($_FILES['file']['name']);

        moveUploadFile($target_path, $filename);

    }

     //UPLOAD_STORE_BANNER

    if($servicetype == "UPLOAD_STORE_BANNER" &&  $filename != "" ){

        $storeId =  isset($_POST['storeId']) ?  $_POST['storeId'] : '';

        $path =  $dir."banners/store/".$storeId;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }

        $target_path = $path."/". basename($_FILES['file']['name']);

        moveUploadFile($target_path, $filename);

    }

    //UPLOAD_PRODUCTS

    if($servicetype == "UPLOAD_PRODUCTS" &&  $filename != "" ){

        $storeId =  isset($_POST['storeId']) ?  $_POST['storeId'] : '';
        $productId =  isset($_POST['productId']) ?  $_POST['productId'] : '';


        $path =  $dir."products/".$productId ;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }

        $target_path = $path."/". basename($_FILES['file']['name']);

        moveUploadFile($target_path, $filename);

    }


    if($servicetype == "UPLOAD_PROFILE_PHOTO" &&  $filename != "" ){

        $userId =  isset($_POST['userId']) ?  $_POST['userId'] : '';

        $path =  $dir."profile/user/".$userId;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            
        }

        $target_path = $path."/". basename($_FILES['file']['name']);

        moveUploadFile($target_path, $filename);

    }

    


     



// if($_FILES["file"]["name"] != '')
// {
//     $newFilename = time() . "_" . $_FILES["file"]["name"];
//     $location = 'upload/' . $newFilename;  
//     move_uploaded_file($_FILES["file"]["tmp_name"], $location);

// }


// 


    
//     $servicetype  = isset($_POST['serviceType']) ? trim($_POST['ServiceType']) : '';
   
    
//     $messageArray = array();
    
//     $messageArray['response'] = 0;
    
//     $dir = $_SERVER['DOCUMENT_ROOT']."/uploads/";
    
//     $target_path = "";
    
//     // if($servicetype == "UPLOAD_STORE_LOGO" &&  $filename != "" ){

//     //     $usertype  = isset($_POST['userType']) ? trim($_POST['userType']) : 'Store';
//     //     $storeId  = isset($_POST['storeId']) ? trim($_POST['storeId']) : '';
//     //     $filename =  isset($_FILES['file']['name']) ?  $_FILES['file']['name'] : '';
        
//     //     unset($messageArray);
//     //     unset($where);
        
//     //     $path =  $dir."/Profile/Store/";
        
//     //     if (!is_dir($path)) {
//     //         mkdir($path, 0777, true);
            
//     //     }
        
//     //     // $update['vLogo'] = $filename;

//     //     // $where['iMerchantId'] =  $userId;
//     //     // $result = myQuery("merchants",  $update, "update",  $where);
//     //     $filename = time() . "_" . $_FILES["file"]["name"];
//     //     $target_path = $path."/". basename($_FILES['file']['name']);
//     //     moveUploadFile($target_path, $filename);
    
       
//     // }
    


//     if($_FILES["file"]["name"] != ''){

//         $path =  $dir."/Profile/Store/";
//         $filename = time() . "_" . $_FILES["file"]["name"];
//         $target_path = $path."/". basename($_FILES['file']['name']);
//         moveUploadFile($target_path, $filename);


//         // $newFilename = time() . "_" . $_FILES["file"]["name"];
//         // $location = 'upload/' . $newFilename;  
//         // move_uploaded_file($_FILES["file"]["tmp_name"], $location);
        
//         // mysqli_query($conn,"insert into photo (location) values ('$location')");
//     }

    
   
    
    
    function moveUploadFile($targetpath,  $filename, $publicpath = null){
        
        $extension = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
    
        if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') {
            
            try {
                // Throws exception incase file is not being moved
                if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetpath)) {
                    // make error flag true
                    echo json_encode(array('status'=>'fail', 'message'=>'could not move file'));
                }
                // File successfully uploaded
                echo json_encode(array('status'=>'success', 'message'=>'File Uploaded', 'filename'=>$filename, 'url'=> $targetpath));
            } catch (Exception $e) {
                // Exception occurred. Make error flag true
                echo json_encode(array('status'=>'fail', 'message'=>$e->getMessage()));
            }
           
        } else {
            // File parameter is missing
            echo json_encode(array('status'=>'fail', 'message'=>'Not received any file'));
            
        }
        
    }
    


   


    
    
    
    
//     //echo json_encode( $messageArray) ;
