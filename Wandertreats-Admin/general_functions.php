<?php 

  ini_set('display_errors',1);
  date_default_timezone_set('Asia/Manila');
    //GENERAL FUNCIONS - 10-04-19
    //GENERAL FUNCIONS - 06-23-20
    //renceveterans.dev
     // use PHPMailer\PHPMailer\PHPMailer;
     // use PHPMailer\PHPMailer\SMTP;
    
    // require 'vendor/autoload.php';
    // require_once 'vendor/twilio/Services/Twilio.php';
    include_once('db_info.php');
    include_once('config.php');
    
    $database = new Connection();
    
    $obj = $database->openConnection();
    
    //QUERY//
    
    function myQuery($tablename, $data, $type, $where = null, $others = ""){
        global $obj ; global $database;
        
        switch ($type){
            
            
            case "insert":
                
                $fieldnames =setFieldnames($data);
                
                $values = setValues($data);
                
                $sql = "INSERT INTO ";
                $sql .= $tablename;
                $sql .= " (".$fieldnames .") ";
                $sql .= "VALUES";
                $sql .= " (".$values.") ";
                
                
                $statement = $obj -> prepare($sql);
                
                $result = $statement -> execute();
                
                if(!$result){
                     echo $sql;
                }
                
                break;
                
            case "insert_getlastid":
                
                $fieldnames =setFieldnames($data);
                
                $values = setValues($data);
                
                $sql = "INSERT INTO ";
                $sql .= $tablename;
                $sql .= " (".$fieldnames .") ";
                $sql .= "VALUES";
                $sql .= " (".$values.") ";
                
                
                $statement = $obj -> prepare($sql);
                
                $result1 = $statement -> execute();
                
                $result =  $obj -> lastInsertId();
                
                if(!$result){
                     echo $sql;
                }
                
                break;
                
            case "update":
                
                $where_clause = setWhereClause($where);
                $set_clasue = setAssignment($data);
                
                $sql = "UPDATE ";
                $sql .= $tablename;
                $sql .= " SET ".$set_clasue. " ". $where_clause;
                
                
                $statement = $obj -> prepare($sql);
                
                $result = $statement -> execute();
                
                if(!$result){
                     echo $sql;
                }
                
               
        
                
                break;
                
            case "selectall":
                
                $fieldnames = getFieldnames($data);
                
                if($where != null){
                    $where_clause = set_Binded_WhereClause($where);
                    
                }else{
                    $where_clause = "";
                }
                
                if($others == null){
                    $others = "";
                }
                
                

                $sql = "SELECT ";
                $sql .= $fieldnames;
                $sql .= " FROM ".$tablename." ";
                $sql .= $where_clause;
                $sql .= $others;
        
                $parameters = array();
               
                $statement = $obj->prepare($sql);
                
                if($where != null){
                    foreach ($where as $key => $val) {
                        $parameters[':'.$key] = $val;
                    }
                    
                    $statement->execute($parameters);
                }else{
                   $statement->execute();
                }
                
             
                
               
                $result = $statement ->fetchAll();  
                
                if(!$result){
                    // echo $sql;
                }
               
                break;
                
             case "select":
                
                $fieldnames = getFieldnames($data);
                if($where != null){
                    $where_clause = set_Binded_WhereClause($where);
                }else{
                    $where_clause = "";
                }
                
                if($others == null){
                    $others = "";
                }
                
                

                $sql = "SELECT ";
                $sql .= $fieldnames;
                $sql .= " FROM ".$tablename." ";
                $sql .= $where_clause;
                $sql .= $others;
        
                $parameters = array();
               
                $statement = $obj->prepare($sql);
                
                if($where != null){
                    foreach ($where as $key => $val) {
                        $parameters[':'.$key] = $val;
                    }
                    
                    $statement->execute($parameters);
                    
                }else{
                    
                    $statement->execute();
                   
                }
                
                $result = $statement ->fetch();  
                
                if(!$result){
                    echo $sql;
                }
               
                break;
            
                
                
        }
    
         
        $database -> closeConnection();
        
        return $result; 
         
    }
    
     function getFieldnames($data){
        $lastvalue = array_pop($data);
        
        $values = "";
                
        foreach($data as $key){
           
            $values .= " ".$key." ,  ";
        }
        
        return $values . "  ".$lastvalue." ";
    }
    
    function setValues($data){
        $lastvalue = array_pop($data);
        $values = "";
                
        foreach($data as $key){
            
            $values .= " '".$key."',  ";
        }
        
        return $values . " '".$lastvalue."' ";
    }
    
    function setFieldnames($data){
        
        $fieldnames = array_keys($data);
        $lastfield = array_pop($fieldnames);
        $fields = "";
        
        foreach($fieldnames as $key){
          
            $fields .= $key.",  ";
        }
        
        return $fields ." ". $lastfield;
    }
    
    function setAssignment($data){
        
        $clause = "";
        $fieldnames = array_keys($data);
        $lastfield = array_pop($fieldnames);
        $values = array_values($data);
        $lastvalue = array_pop($data);
                
        for($i = 0; $i < count($fieldnames) ; $i++){
            $clause .= $fieldnames[$i]."  = '".$values[$i]."' ,  ";
        }
        
        $clause .=  $lastfield." = '".$lastvalue."' ";
        
        return $clause;
        
    }
    
    function setWhereClause($data){
         $clause = "";
        $fieldnames = array_keys($data);
        $lastfield = array_pop($fieldnames);
        $values = array_values($data);
        $lastvalue = array_pop($data);
        
        $clause .= " WHERE ";
                
        for($i = 0; $i < count($fieldnames) ; $i++){
            $clause .= $fieldnames[$i]."  = '".$values[$i]."'  AND  ";
        }
        
        $clause .=  $lastfield." = '".$lastvalue."' ";
        
        return $clause;
        
    }
    
    function set_Binded_WhereClause($data){
        $clause = "";
        $fieldnames = array_keys($data);
        $lastfield = array_pop($fieldnames);
        $values = array_values($data);
        $lastvalue = array_pop($data);
        
        $clause .= " WHERE ";
                
        for($i = 0; $i < count($fieldnames) ; $i++){
            $clause .= $fieldnames[$i]."  = :".$fieldnames[$i]."  AND  ";
        }
        
        $clause .=  $lastfield." = :".$lastfield." ";
        
        return $clause;
        
    }
    
    
    
     //SEND NOTIFICATION//
    
    function notify($userType, $iUserId, $data){
        
        
        if($userType == "driver" || $userType == "Driver"){
            $userType = "driver";
            $tablename = "register_driver";
            $where['iDriverId'] = $iUserId;
              $key = constants::FIREBASE_KEY;
            $notificationCounter = countNotifications($iUserId, $userType);
        }
        
        if($userType == "user" || $userType == "User"){
            $userType = "user";
            $tablename = "register_user";
            $where['iUserId'] = $iUserId;
             $key = constants::FIREBASE_KEY;
            $notificationCounter = countNotifications($iUserId, $userType);
        }
        
        if($userType == "store" || $userType == "Store"){
            $userType = "store";
            $tablename = "company";
            $where['iCompanyId'] = $iUserId;
            $key = constants::FIREBASE_KEY2;
        }
        
        
        
      
        
        $select = array("vFirebaseDeviceToken");
        
        $driverid = myQuery("".$tablename, $select, "selectall", $where);
        
        $deviceToken =  trim($driverid[0]['vFirebaseDeviceToken']);

            //         'notification' => array(
            //     'title' => $data['title'],
            //     'body' =>  $data['description']
            // ),
        
        $fields = array(
            'to' =>  $deviceToken,
            

            
            'data' => array(
                'activity' => $data['activity'],
                 'title' => $data['title'],
                'message' =>$data['message'],
                'notifCounter' => $notificationCounter
            )
        );

 
        $headers = array(
            'Authorization:key=' . $key,
            'Content-Type:application/json'
        );  

         //echo safe_json_encode($fields);
        //echo safe_json_encode($headers);
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, constants::CLOUD_MESSAGING_URL); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
        $result = curl_exec($ch);
       
        curl_close($ch);

        $jsonString = $result;

        $jsonObject = json_decode($jsonString);
        
        if(isset($jsonObject->success) && $jsonObject->success == 1){ 
            
             $returnArr['message'] = "Success!" ;
        }else{
             $returnArr['message'] =  $jsonString ;
        }
        
        
        $returnArr['token'] =   $deviceToken ;
    }
    
    
    
    function sendRequestToUser($iUserId, $activity,$title = null,  $message){
        
        $where['iUserId'] = $iUserId;
        
        $select = array("vFirebaseDeviceToken");
        
        $driverid = myQuery("register_user", $select, "selectall", $where);
        
        $deviceToken =  trim($driverid[0]['vFirebaseDeviceToken']);
    
        $Rmessage  = "CabRequested";
        
        $fields = array(
            'to' =>  $deviceToken,
            
            'notification' => array(
                'title' => $title,
                'body' =>  $message
            ),
            
            'data' => array(
                'activity' => $activity,
                'message' => $message
            )
        );
 
        $headers = array(
            'Authorization:key=' . constants::FIREBASE_KEY,
            'Content-Type:application/json'
        );  
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, constants::CLOUD_MESSAGING_URL); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
        $result = curl_exec($ch);
       
        curl_close($ch);

        $jsonString = $result;

        $jsonObject = json_decode($jsonString);
        
        if(isset($jsonObject->success) && $jsonObject->success == 1){ 
            
             $returnArr['message'] = "Success!" ;
        }else{
             $returnArr['message'] =  $jsonString ;
        }
        
        
         $returnArr['token'] =   $deviceToken ;
    }
    
    
    function sendRequestToDriver($iDriverId, $activity, $message){
        
        $where['iDriverId'] = $iDriverId;
        
        $select = array("vFirebaseDeviceToken", "vName");
        
        $driverid = myQuery("register_driver", $select, "selectall", $where);
        
        $deviceToken =  trim($driverid[0]['vFirebaseDeviceToken']);
    
        $Rmessage  = "CabRequested";
        
        //  'notification' => array(
        //         'title' => "",
        //         'body' => ""
        //     ),
        
        $fields = array(
            'to' =>  $deviceToken,
            
           
            
            'data' => array(
                'activity' => $activity,
                'message' => $message
            )
        );
 
        $headers = array(
            'Authorization:key=' . constants::FIREBASE_KEY,
            'Content-Type:application/json'
        );  
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, constants::CLOUD_MESSAGING_URL); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
        $result = curl_exec($ch);
       
        curl_close($ch);

        $jsonString = $result;

        $jsonObject = json_decode($jsonString);
        
        if(isset($jsonObject->success) && $jsonObject->success == 1){ 
            
             $returnArr['message'] = "Success!" ;
        }else{
             $returnArr['message'] =  $jsonString ;
        }
        
        
      $returnArr['token'] =   $deviceToken ;
        
        
    
    }
    
    function get_Address($lat, $lon) {
        
        // echo ' Latitude : '.$lat;
        // echo ' Longitude : '.$lon;
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key='.constants::GOOGLE_API_KEY;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if($status == "OK"){
            foreach($data->results[0]->address_components as $address_component) {
                if(in_array('street_number', $address_component->types)) {
                    $street_number = $address_component->long_name;
                }
                if(in_array('route', $address_component->types)) {
                     $route = $address_component->long_name;
                }
            }
        }
       // return $street_number." ".$route;
        
        return  $data->results[0]->formatted_address;
    }
    
    function get_Address2($lat, $lon) {
        
        // echo ' Latitude : '.$lat;
        // echo ' Longitude : '.$lon;
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key='.constants::GOOGLE_API_KEY;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if($status == "OK"){
            foreach($data->results[0]->address_components as $address_component) {
                if(in_array('street_number', $address_component->types)) {
                    $street_number = $address_component->long_name;
                }
                if(in_array('route', $address_component->types)) {
                     $route = $address_component->long_name;
                }
            }
        }
       // return $street_number." ".$route;
        
        return  $data->results[1]->formatted_address;
    }
    
     function get_Address3($lat, $lon) {
        
        // echo ' Latitude : '.$lat;
        // echo ' Longitude : '.$lon;
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key='.constants::GOOGLE_API_KEY;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if($status == "OK"){
            foreach($data->results[0]->address_components as $address_component) {
                if(in_array('street_number', $address_component->types)) {
                    $street_number = $address_component->long_name;
                }
                if(in_array('route', $address_component->types)) {
                     $route = $address_component->long_name;
                }
            }
        }
       // return $street_number." ".$route;
        
        return  $data->results[3]->formatted_address;
    }
    
    
      function get_Address4($lat, $lon) {
        
        // echo ' Latitude : '.$lat;
        // echo ' Longitude : '.$lon;
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key='.constants::GOOGLE_API_KEY;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if($status == "OK"){
            foreach($data->results[0]->address_components as $address_component) {
                if(in_array('street_number', $address_component->types)) {
                    $street_number = $address_component->long_name;
                }
                if(in_array('route', $address_component->types)) {
                     $route = $address_component->long_name;
                }
            }
        }
       // return $street_number." ".$route;
        
        return  $data->results[4]->formatted_address;
    }
    
      function get_Address5($lat, $lon) {
        
        // echo ' Latitude : '.$lat;
        // echo ' Longitude : '.$lon;
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key='.constants::GOOGLE_API_KEY;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if($status == "OK"){
            foreach($data->results[0]->address_components as $address_component) {
                if(in_array('street_number', $address_component->types)) {
                    $street_number = $address_component->long_name;
                }
                if(in_array('route', $address_component->types)) {
                     $route = $address_component->long_name;
                }
            }
        }
       // return $street_number." ".$route;
        
        return  $data->results[5]->formatted_address;
    }
    
     function isLandArea($lat, $lon) {
        
        // echo ' Latitude : '.$lat;
        // echo ' Longitude : '.$lon;
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key='.constants::GOOGLE_API_KEY;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        
        echo $data;
    //     if($status == "OK"){
    //         foreach($data->results[0]->address_components as $address_component) {
    //             if(in_array('street_number', $address_component->types)) {
    //                 $street_number = $address_component->long_name;
    //             }
    //             if(in_array('route', $address_component->types)) {
    //                  $route = $address_component->long_name;
    //             }
    //         }
    //     }
    //   // return $street_number." ".$route;
        
    //     return  $data->results[0]->formatted_address;
    }
    
    function isItOnWater($lat,$lng) {

        $url = "https://api.onwater.io/api/v1/results/$lat,$lng";
        $json = file_get_contents($url);
        $data = json_decode($json);
        
      
        return $data->water;
        
    }
    
    function check_Address_restriction($lat, $lon, $place) {
        
        // echo ' Latitude : '.$lat;
        // echo ' Longitude : '.$lon;
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key='.constants::GOOGLE_API_KEY;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if($status == "OK"){
            foreach($data->results[0]->address_components as $address_component) {
                if($address_component->long_name == $place || $address_component->short_name == $place) {
                   return  "Yes";
                }

            }
        }
       // return $street_number." ".$route;
        
        return  "No";
    }
    
    
    function get_CompleteAddress($lat, $lon) {
        
        // $region = "";
        // $state = "";
        // $city = "";
        // $country = "";
        
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&key='.constants::GOOGLE_API_KEY;
        $json = file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = array();
        if($status == "OK"){
            foreach($data->results[0]->address_components as $address_component) {
                if($address_component->types[0] == "locality") {
                  $address["city"] =   $address_component->long_name;
                }
                
                if($address_component->types[0] == "administrative_area_level_2"){
                    
                   $address["state"] = $address_component->long_name;
                }
                
                if($address_component->types[0] == "administrative_area_level_1"){
                    
                    $address["region"] = $address_component->long_name;
                }
                
                if($address_component->types[0] == "country"){
                    
                   $address["country"] = $address_component->short_name;
                }

            }
        }
        
        
        // $address["city"] = $city;
        // $address["state"] = $state;
        // $address["region"] = $region;
        // $address["country"] = $country;
        
        return  $address;

    }



    function get_CompleteAddress2($sourceLat, $sourceLong) {
        
        $USERAGENT = $_SERVER['HTTP_USER_AGENT'];
    

        $opts = array('http'=>array('header'=>"User-Agent: $USERAGENT\r\n"));
        $context = stream_context_create($opts);
        $url4 = file_get_contents("https://nominatim.openstreetmap.org/reverse?format=json&lat=$sourceLat&lon=$sourceLong&zoom=18&addressdetails=1", false, $context);
        $osmaddress = json_decode($url4);  
        $location = array();
        
        $location['type'] =  "nominatim";
        $location['address'] =  $osmaddress ->display_name;
        $location['latitude'] =  $osmaddress ->lat;
        $location['longitude'] =  $osmaddress ->lon;
        $location['address_name'] = $osmaddress->address ->building;
        $location['housenumber'] = $osmaddress->addresss ->house_number;
        $location['street'] = $osmaddress->address ->road;
        $location['locality'] = $osmaddress->address ->quarter; 
        $location['town'] = $osmaddress->address -> town; 
       
        $location['district'] = ($osmaddress->address ->city_district == null )? $osmaddress->address -> district.'' : $osmaddress->address ->city_district.'';
        $location['city'] =  $osmaddress->address ->city;
        $location['state'] = $osmaddress->address->region == "Metro Manila" ? "Metro Manila" : $osmaddress->address->state;
        $location['region'] = $osmaddress->address->region == "Metro Manila" ? "National Capital Region" : $osmaddress->address->region;
        $location['country'] = $osmaddress->address ->country;
        
        
        // $address["city"] = $city;
        // $address["state"] = $state;
        // $address["region"] = $region;
        // $address["country"] = $country;
        
        return  $location;
    }



    
    
    function get_Duration($address1, $address2, $unit = null){
        
        // store
        $address1 = str_replace(" ", "+", $address1);
        // buyer
        $address2 = str_replace(" ", "+", $address2);
        
        
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins='.$address1.'&destinations='.$address2.'&avoid=highways&mode=bicycling&traffic=best_guess&key='.constants::GOOGLE_API_KEY;
        
         //$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=501 Epifanio de los Santos Ave, Quezon City, 1111 Metro Manila, Philippines&destinations=Atlanta Center Building, 31 Annapolis, San Juan, Metro Manila, Philippines&mode=driving&traffic=best_guess&key=AIzaSyD2Ku8qGzmjx2k97qDVkWSPION8R0MJTbQ';
       // https://maps.googleapis.com/maps/api/geocode/json?latlng=14.61106014366259,121.05477824807167&sensor=false&key=AIzaSyD2Ku8qGzmjx2k97qDVkWSPION8R0MJTbQ

        $json = file_get_contents($url);
        $json = json_decode($json);
        
        $duration = $json->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'text'};
        $duration2 = $json->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'value'};
        
        if($unit != null){
            if($unit == "s"){
                return $duration2;
            }else if($unit == "m"){
                $val = (int)$duration2;
                
                return round($val/60);
            }
        }else{
            return  $duration;
        }
      
    }
    
    
    // function get_Duration2(){
        
        
        
    // }
    
      
    
     function get_Distance($address1, $address2, $unit = null){
        
        // store
        $address1 = str_replace(" ", "+", $address1);
        // buyer
        $address2 = str_replace(" ", "+", $address2);
        
        
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins='.$address1.'&destinations='.$address2.'&mode=driving&traffic=best_guess&key='.constants::GOOGLE_API_KEY;
        
        
        $json = file_get_contents($url);
        $json = json_decode($json);
        
        $distance = $json->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'text'};
        $distance2 = $json->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'};
        
        if($unit != null){
            if($unit == "km"){
                return $distance2/1000;
            }else if($unit == "m"){
                return $distance2;
            }
        }else{
            return $distance;
        }
      
      
    }

    
    
    function get_lat_long($address){

        $address = str_replace(" ", "+", $address);
    
        // $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key=".constants::GOOGLE_API_KEY);
        // $json = json_decode($json);
    
        // $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        // $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        
        
        
        $geo = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key=".constants::GOOGLE_API_KEY);
        $geo = json_decode($geo, true);

       // If everything is cool
       if ($geo['status'] = 'OK') {
          $latitude = $geo['results'][0]['geometry']['location']['lat'];
          $longitude = $geo['results'][0]['geometry']['location']['lng'];
          $array = array('lat'=> $latitude ,'lng'=>$longitude);
       }
       
       return $latitude.','.$longitude;
       
    }
    
    function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit) {
      if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
      }
      else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
    
        if ($unit == "K") {
          return ($miles * 1.609344);
        } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
          return $miles;
        }
      }
    }
    
    // Function to calculate speed 
    function cal_speed($dist, $time) { 
        echo "\n Distance(km) : " . $dist ; 
        echo "\n Time(hr) : " . $time ; 
                  
        return $dist / $time; 
    } 
      
    // Function to calculate  
    // distance traveled 
    function cal_dis($speed, $time) { 
        echo "\n Time(hr) : " . $time ; 
        echo "\n Speed(km / hr) : " . $speed ; 
                  
        return $speed * $time; 
    } 
      
    // Function to calculate 
    // time taken 
    function cal_time($dist, $speed) { 
        // echo "\n Distance(km) : " . $dist ; 
        // echo "\n Speed(km / hr) : " . $speed ; 
        
        $time = $dist/$speed;
        
                  
        return $time*60*60; 
    } 
    
     function distance($lat1, $lon1, $lat2, $lon2, $unit) {
      if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
      }
      else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
    
        if ($unit == "K") {
          return ($miles * 1.609344);
        } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
          return $miles;
        }
      }
    }
    
    
    function getLanguage($lang){
        

        $where['vTitle'] = $lang;
        
        $result = myQuery("language_master", array("vCode"), "selectall",  $where);
        
        return  $result[0]["vCode"];
        
    }
    
    
    
    function GenerateUniqueOrderNo($code){
        
        $random = rand(0, 600000);
        return $code."".date("y")."".date("m")."".date("d")."".getRandom(5);
    }
    
    function GenerateToken(){
        
        $random = rand(0, 600000);
        return date("y")."".date("m")."".date("d")."".getRandom(5);
    }
    
    
    // Generate token
    
    function getRandom($length){
        
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        // $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited
        
        for ($i=0; $i < $length; $i++) {
         $token .= $codeAlphabet[random_int(0, $max-1)];
        }
        
        return $token;
        
    }
    
    function getToken($length){
        
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited
        
        for ($i=0; $i < $length; $i++) {
         $token .= $codeAlphabet[random_int(0, $max-1)];
        }
        
        return $token;
    }
    
    function checkEmail($userType, $username){
        global $obj;
        unset($where);
        
        if($userType == "Driver"){

            $sql = "SELECT * FROM register_driver WHERE vEmail = '".$username."' AND eEmailVerified = 'Yes' AND eStatus = 'active'";
                   
            $statement = $obj->query($sql); 
            $result = $statement ->fetchAll();


            
            // $fieldname = array("iDriverId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "eGender", "tDeviceSessionId", "tDeviceData", "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");
        
            // $where['vEmail'] = $username;
            // $where['eEmailVerified'] = "Yes";
            // $where['eStatus'] = "active";
            
            // $result = myQuery("register_driver", $fieldname, "selectall",  $where);
            
               
            if(  count($result) > 0 ){
                 $emailResult = strcmp($result[0]['vEmail'], $username);
            }else{
                $emailResult = 1;
            }
            
           
            
            // $md5 = md5($salt.$email);
            
         
        }
        
        if($userType == "User"){
            
              
            $sql = "SELECT * FROM register_user WHERE vEmail = '".$username."'";
                   
            $statement = $obj->query($sql); 
            $result = $statement ->fetchAll();

            // echo "sql : ".$sql ."<br>";
            // echo "username : ".$username."<br>";
            // echo "Email : ".$result[0]['vEmail']."<br>";
            
            // $fieldname = array("iUserId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");
        
            // $where['vEmail'] = $username;
            // $where['eEmailVerified'] = "Yes";
            
            //$result = myQuery("register_user", $fieldname, "selectall",  $where);
            
             if(count($result) > 0 ){
                $emailResult = strcmp($result[0]['vEmail'], $username);
            }else{
                $emailResult = 1;
            }
        }
        
        
        if($userType == "Store"){
            
            $fieldname = array("vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");
            $where['vEmail'] = $username;
            $where['eEmailVerified'] = "Yes";
            
            $result = myQuery("register_seller", $fieldname, "selectall",  $where);
            
             if(count($result) > 0 ){
                 $emailResult = strcmp($result[0]['vEmail'], $username);
            }else{
                $emailResult = 1;
            }
        }
        
        return  $emailResult;
    }
    
    
    function checkEmailExist($userType, $username){

          global $obj;
        
        unset($where);
        
        if($userType == "Driver"){
            
            $fieldname = array("iDriverId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "eGender", "tDeviceSessionId", "tDeviceData", "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");
        
            $where['vEmail'] = $username;
    
            
            $result = myQuery("register_driver", $fieldname, "selectall",  $where);
            
               
            if(  count($result) > 0 ){
                 $emailResult = strcmp($result[0]['vEmail'], $username);
            }else{
                $emailResult = 1;
            }
            
           
            
            // $md5 = md5($salt.$email);
            
         
        }
        
        if($userType == "User"){
            
              
            $sql = "SELECT * FROM register_user WHERE vEmail = '".$username."'";
                   
            $statement = $obj->query($sql); 
            $result = $statement ->fetchAll();
            
             if(count($result) > 0 ){
                 $emailResult = strcmp($result[0]['vEmail'], $username);
            }else{
                $emailResult = 1;
            }
        }
        
        return  $emailResult;
    }
    
   

    
    function checkPassword($userType,$email, $password){
         global $obj;
        unset($where);
        
 
        //$passwordMd5 = md5(constants::SALT.$password);
        
        // $password = decryptString($password);
        $where['vPassword'] = $password ; //$passwordMd5;
      
            
        if($userType == "Driver"){
            
            $fieldname = array("iDriverId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "eGender", "tDeviceSessionId", "tDeviceData", "vFirebaseDeviceToken", "eLogout", "ePhoneVerified", "eEmailVerified");
        
            $result = myQuery("register_driver", $fieldname, "selectall",  $where, " AND ( vEmail = '".$email."' OR vPhone = '".$email."' )");
        }
        
        if($userType == "User"){


            $sql = "SELECT * FROM register_user WHERE vPassword = '".$password."' AND ( vEmail = '".$email."' OR vPhone = '".$email."' )";
                   
            $statement = $obj->query($sql); 
            $result = $statement ->fetchAll();


            
            // $fieldname = array("iUserId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout", "ePhoneVerified", "eEmailVerified");
         
            // $result = myQuery("register_user", $fieldname, "selectall",  $where, " AND ( vEmail = '".$email."' OR vPhone = '".$email."' )");
        }
        
        if($userType == "Store"){
            
            $fieldname = array("iSellerId", "iCompanyId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout", "ePhoneVerified", "eEmailVerified");
         
            $result = myQuery("register_seller", $fieldname, "selectall",  $where, " AND ( vEmail = '".$email."' OR vPhone = '".$email."' )");
        }
        
        return $result;
        
    }
    
    function checkPassword2($userType,$mobile, $password){
        unset($where);
        
        // $password = decryptString($password);
        $where['vPassword'] = $password;
        $where['vPhone'] = $email;
            
        if($userType == "Driver"){
            
            $fieldname = array("iDriverId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "eGender", "tDeviceSessionId", "tDeviceData", "vFirebaseDeviceToken", "eLogout", "ePhoneVerified", "eEmailVerified");
        
            $result = myQuery("register_driver", $fieldname, "selectall",  $where);
        }
        
        if($userType == "User"){
            
            $fieldname = array("iUserId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout",   "ePhoneVerified", "eEmailVerified");
        
            $result = myQuery("register_user", $fieldname, "selectall",  $where, " OR vPhone = '".$email."'");
        }
        
        return $result;
        
    }
    
     function checkMobileNumber($userType, $mobile){
        global $obj ; global $database;
        unset($where);
        
        $where['vPhone'] =  $mobile;
       
            
        if($userType == "Driver"){
            
            $sql = "SELECT * FROM register_driver WHERE vPhone = '".$mobile."'";
                   
            $statement = $obj->query($sql); 
            $result = $statement ->fetchAll();
            
              
            if(  count($result) > 0 ){
                 $phoneResult = strcmp($result[0]['vPhone'],  $mobile);
            }else{
                 $phoneResult = 1;
            }
        }
        
        if($userType == "User"){

            $sql = "SELECT * FROM register_user WHERE vPhone = '".$mobile."'";
                   
            $statement = $obj->query($sql); 
            $result2 = $statement ->fetchAll();
            
    
            
            if(  count($result2) > 0 ){
                 $phoneResult = strcmp($result2[0]['vPhone'],  $mobile);
            }else{
                 $phoneResult = 1;
            }
            
        }
        
      //  echo  $phoneResult."</br>";
        
        return $phoneResult;
        
    }
    
     function checkMobileNumberIsApprove($userType, $mobile){
        global $obj ; global $database;
        unset($where);
        
        $where['vPhone'] =  $mobile;
        $where['eStatus'] = "active";
            
        if($userType == "Driver"){
            
            $fieldname = array("iDriverId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "eGender", "tDeviceSessionId", "tDeviceData", "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");
        
            $result = myQuery("register_driver", $fieldname, "selectall",  $where);
            
              
            if(  count($result) > 0 ){
                 $phoneResult = strcmp($result[0]['vPhone'],  $mobile);
            }else{
                 $phoneResult = 1;
            }
        }
        
        if($userType == "User"){
            
        
            
            $fieldname = array("iUserId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");
        
            $result2 = myQuery("register_user", $fieldname, "selectall",  $where);
            
            if(  count($result2) > 0 ){
                 $phoneResult = strcmp($result2[0]['vPhone'],  $mobile);
            }else{
                 $phoneResult = 1;
            }
            
        }
        
      //  echo  $phoneResult."</br>";
        
        return $phoneResult;
        
    }
    
    
     
    function roundOff($value){
        
       return number_format($value, 2, '.', '');
       
    }
    
    function sendVerifivationCode($mobilenumber, $SMSmessage){

        $number = $mobilenumber;
        $message = $SMSmessage;
        $apicode = "DE-HENGY005538_VF6II";
        $passwd = "{(k%gygg#{";
        $result = itexmo($number,$message,$apicode,$passwd);
        
        //  $account_sid = "AC6fb45a05498750a9ffd49e84154e16f6";
        //  $auth_token = "3f37e731f441f5dc82ed1c8abf8aa118";

        // $twilioMobileNum = "+19389991544";
        // $toMobileNum = $mobilenumber;//"+639398296855";
        // $message = $SMSmessage;

        // $SMSclient->account->messages->sendMessage($twilioMobileNum,$toMobileNum,$message);
       
    }





    function itexmo($number,$message,$apicode,$passwd){
        $ch = curl_init();
        $itexmo = array('1' => $number, '2' => $message, '3' => $apicode, 'passwd' => $passwd);
        curl_setopt($ch, CURLOPT_URL,"https://www.itexmo.com/php_api/api.php");
        curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, 
                  http_build_query($itexmo));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec ($ch);
        curl_close ($ch);
    }
        
    
    function number_PH($number){
        //strip out everything but numbers
        $number =  preg_replace("/[^0-9]/", "", $number);
        //Strip out leading zeros:
        $number = ltrim($number, '0');
        //The default country code
        $default_country_code  = '+63';
        //Check if the number doesn't already start with the correct dialling code:
        if ( !preg_match('/^[+]'.$default_country_code.'/', $number)  ) {
            $number = $default_country_code.$number;
        }
        
        
        return $number;
    
    }
    
    
    function sendVerificationEmail($email, $fullname, $token, $userType, $userId){
        try{
            
            // $greetings = "<h2>Hello ".$fullname.",</h2></br>";
            
            // $link = "<a href='http://mallody.ph/resources/verify.php?userType=".$userType."&token=".$token."&id=".$userId."'>Verify now</a>";

            $message = ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                  <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <meta name="x-apple-disable-message-reformatting" />
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    <meta name="color-scheme" content="light dark" />
                    <meta name="supported-color-schemes" content="light dark" />
                    <title></title>
                    <style type="text/css" rel="stylesheet" media="all">
                    
                    @import url("https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap");
                    body {
                      width: 100% !important;
                      height: 100%;
                      margin: 0;
                      -webkit-text-size-adjust: none;
                    }
                    
                    a {
                      color: #3869D4;
                    }
                    
                    a img {
                      border: none;
                    }
                    
                    td {
                      word-break: break-word;
                    }
                    
                    .preheader {
                      display: none !important;
                      visibility: hidden;
                      mso-hide: all;
                      font-size: 1px;
                      line-height: 1px;
                      max-height: 0;
                      max-width: 0;
                      opacity: 0;
                      overflow: hidden;
                    }
                    
                    body,
                    td,
                    th {
                      font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
                    }
                    
                    h1 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 22px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    h2 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 16px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    h3 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 14px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    td,
                    th {
                      font-size: 16px;
                    }
                    
                    p,
                    ul,
                    ol,
                    blockquote {
                      margin: .4em 0 1.1875em;
                      font-size: 16px;
                      line-height: 1.625;
                    }
                    
                    p.sub {
                      font-size: 13px;
                    }
                    
                    .align-right {
                      text-align: right;
                    }
                    
                    .align-left {
                      text-align: left;
                    }
                    
                    .align-center {
                      text-align: center;
                    }
                    
                    .button {
                      background-color: #3869D4;
                      border-top: 10px solid #3869D4;
                      border-right: 18px solid #3869D4;
                      border-bottom: 10px solid #3869D4;
                      border-left: 18px solid #3869D4;
                      display: inline-block;
                      color: #FFF;
                      text-decoration: none;
                      border-radius: 3px;
                      box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
                      -webkit-text-size-adjust: none;
                      box-sizing: border-box;
                    }
                    
                    .button--green {
                      background-color: #22BC66;
                      border-top: 10px solid #22BC66;
                      border-right: 18px solid #22BC66;
                      border-bottom: 10px solid #22BC66;
                      border-left: 18px solid #22BC66;
                    }
                    
                    .button--red {
                      background-color: #FF6136;
                      border-top: 10px solid #FF6136;
                      border-right: 18px solid #FF6136;
                      border-bottom: 10px solid #FF6136;
                      border-left: 18px solid #FF6136;
                    }
                    
                    @media only screen and (max-width: 500px) {
                      .button {
                        width: 100% !important;
                        text-align: center !important;
                      }
                    }
                    
                    .attributes {
                      margin: 0 0 21px;
                    }
                    
                    .attributes_content {
                      background-color: #F4F4F7;
                      padding: 16px;
                    }
                    
                    .attributes_item {
                      padding: 0;
                    }
                
                    
                    .related {
                      width: 100%;
                      margin: 0;
                      padding: 25px 0 0 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .related_item {
                      padding: 10px 0;
                      color: #CBCCCF;
                      font-size: 15px;
                      line-height: 18px;
                    }
                    
                    .related_item-title {
                      display: block;
                      margin: .5em 0 0;
                    }
                    
                    .related_item-thumb {
                      display: block;
                      padding-bottom: 10px;
                    }
                    
                    .related_heading {
                      border-top: 1px solid #CBCCCF;
                      text-align: center;
                      padding: 25px 0 10px;
                    }
                
                    
                    .discount {
                      width: 100%;
                      margin: 0;
                      padding: 24px;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #F4F4F7;
                      border: 2px dashed #CBCCCF;
                    }
                    
                    .discount_heading {
                      text-align: center;
                    }
                    
                    .discount_body {
                      text-align: center;
                      font-size: 15px;
                    }
                 
                    
                    .social {
                      width: auto;
                    }
                    
                    .social td {
                      padding: 0;
                      width: auto;
                    }
                    
                    .social_icon {
                      height: 20px;
                      margin: 0 8px 10px 8px;
                      padding: 0;
                    }
                 
                    
                    .purchase {
                      width: 100%;
                      margin: 0;
                      padding: 35px 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .purchase_content {
                      width: 100%;
                      margin: 0;
                      padding: 25px 0 0 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .purchase_item {
                      padding: 10px 0;
                      color: #51545E;
                      font-size: 15px;
                      line-height: 18px;
                    }
                    
                    .purchase_heading {
                      padding-bottom: 8px;
                      border-bottom: 1px solid #EAEAEC;
                    }
                    
                    .purchase_heading p {
                      margin: 0;
                      color: #85878E;
                      font-size: 12px;
                    }
                    
                    .purchase_footer {
                      padding-top: 15px;
                      border-top: 1px solid #EAEAEC;
                    }
                    
                    .purchase_total {
                      margin: 0;
                      text-align: right;
                      font-weight: bold;
                      color: #333333;
                    }
                    
                    .purchase_total--label {
                      padding: 0 15px 0 0;
                    }
                    
                    body {
                      background-color: #F4F4F7;
                      color: #51545E;
                    }
                    
                    p {
                      color: #51545E;
                    }
                    
                    p.sub {
                      color: #6B6E76;
                    }
                    
                    .email-wrapper {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #F4F4F7;
                    }
                    
                    .email-content {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                 
                    
                    .email-masthead {
                      padding: 25px 0;
                      text-align: center;
                    }
                    
                    .email-masthead_logo {
                      width: 94px;
                    }
                    
                    .email-masthead_name {
                      font-size: 16px;
                      font-weight: bold;
                      color: #A8AAAF;
                      text-decoration: none;
                      text-shadow: 0 1px 0 white;
                    }
                
                    
                    .email-body {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #FFFFFF;
                    }
                    
                    .email-body_inner {
                      width: 570px;
                      margin: 0 auto;
                      padding: 0;
                      -premailer-width: 570px;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #FFFFFF;
                    }
                    
                    .email-footer {
                      width: 570px;
                      margin: 0 auto;
                      padding: 0;
                      -premailer-width: 570px;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      text-align: center;
                    }
                    
                    .email-footer p {
                      color: #6B6E76;
                    }
                    
                    .body-action {
                      width: 100%;
                      margin: 30px auto;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      text-align: center;
                    }
                    
                    .body-sub {
                      margin-top: 25px;
                      padding-top: 25px;
                      border-top: 1px solid #EAEAEC;
                    }
                    
                    .content-cell {
                      padding: 15px;
                    }
                 
                    
                    @media only screen and (max-width: 600px) {
                      .email-body_inner,
                      .email-footer {
                        width: 100% !important;
                      }
                        td,
                        th,
                        p {
                          font-size: 12px;
                        }
                        
                        h3{
                            font-size: 14px;
                        }
                        
                    .purchase_total, 
                    .purchase_total--label{
                        font-size: 12px;
                    }
                      
                    }
                    
                  
                    
                  
                    
                    @media (prefers-color-scheme: dark) {
                      body,
                      .email-body,
                      .email-body_inner,
                      .email-content,
                      .email-wrapper,
                      .email-masthead,
                      .email-footer {
                        background-color: #333333 !important;
                        color: #FFF !important;
                      }
                      p,
                      ul,
                      ol,
                      blockquote,
                      h1,
                      h2,
                      h3 {
                        color: #FFF !important;
                      }
                      .attributes_content,
                      .discount {
                        background-color: #222 !important;
                      }
                      .email-masthead_name {
                        text-shadow: none !important;
                      }
                    }
                    
                    :root {
                      color-scheme: light dark;
                      supported-color-schemes: light dark;
                    }
                    </style>
                
                  </head>
                  <body>
                 
                    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                      <tr>
                        <td align="center">
                          <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                              <td class="email-masthead">
                                <a href="https://example.com" class="f-fallback email-masthead_name">
                                Trikaroo PH
                              </a>
                              </td>
                            </tr>
                            <!-- Email Body -->
                            <tr>
                              <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                                <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                  <!-- Body content -->
                                  <tr>
                                    <td class="content-cell">
                                      <div class="f-fallback">
                                        <h1>Hi '.$fullname.'</h1>
                                        <p>Thank you for registering an account to Trikaroo.</br> Before we get started and use the Pabii and Pasakay services, please verifiy your email </p>
                                       
                                        <!-- Action -->
                                        <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                          <tr>
                                            <td align="center">
                                              <!-- Border based button
                           https://litmus.com/blog/a-guide-to-bulletproof-buttons-in-email-design -->
                                              <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                                <tr>
                                                  <td align="center">
                                                    <a href="http://mallody.ph/resources/verify.php?userType='.$userType.'&token='.$token.'&id='.$userId.'&name='.$fullname.'" class="f-fallback button button--green" target="_blank">Verify Email Address</a>
                                                  </td>
                                                </tr>
                                              </table>
                                            </td>
                                          </tr>
                                        </table>
                                    
                                        <p>If you did not create account using this address, please ignore this email.</p>
                                        <p>Cheers,
                                          <br>The Trikaroo Team</p>
                                        <!-- Sub copy -->
                                        <table class="body-sub" role="presentation">
                                          <tr>
                                            <td>
                                              <p class="f-fallback sub">If you’re having trouble with the button above, copy and paste the URL below into your web browser.</p>
                                              <p class="f-fallback sub"><a href="wwww.trikaroo.com.ph">wwww.trikaroo.com.ph</a></p>
                                            </td>
                                          </tr>
                                        </table>
                                      </div>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                  <tr>
                                    <td class="content-cell" align="center">
                                      <p class="f-fallback sub align-center">&copy; 2020 Trikaroo. All rights reserved.</p>
                                      <p class="f-fallback sub align-center">
                                        Heng Yen E-Commerce Inc.
                                        <br>1602 Atlanta Center, 31 Annapolis Street,
                                        <br>Greenhills, San Juan City
                                      </p>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </body>
                </html>';
            
            
            // $message = $greetings;
            // $message .= $body;
            // $message .= $footer;
            
            
             $mail = new PHPMailer;
             $mail->isSMTP();
            
             $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            
        
                    
             $mail->Host = 'mail.mallody.com.ph';
             $mail->Port = 587;
            //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->SMTPAuth = true;
            
             $mail->Username = 'verifications@mallody.com.ph';
             $mail->Password = 'hengyen66888';
            
             $mail->setFrom( 'verifications@trikaroo.com.ph', 'Trikaroo Verifications' );
             $mail->addAddress($email, $fullname);
            //$mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
             $mail->Subject = 'Trikaroo - Verify your Email Address';
    
             $mail->isHTML(true);  
             $mail->Body    = $message;
             $mail->AltBody = '';
            //$mail->addAttachment($applicantId);
    
            //if( $none_co_borrower != "true"){
            //    $mail->addAttachment($co_applicantId);
            //}
            //$account_sid = "AC6fb45a05498750a9ffd49e84154e16f6";
            //$auth_token = "3f37e731f441f5dc82ed1c8abf8aa118";
            //$twilioMobileNum = "+19389991544";
            //$toMobileNum = "+639398296855";
            //$message= "NEW AUTO LOAN APPLICATION!. Check your email.";
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            
             if (!$mail->send()) {
                 echo 'Mailer Error: '. $mail->ErrorInfo;
             } else {
                 echo $firstname;
                // header("Location: ../success.php?first-name=".$firstname);
             }
    
        
        }catch(Exception $e){
          echo 'Mailer Error';
        }
    }
    
    
    function sendResetEmail($email, $fullname, $token, $userType, $userId){
     
            
          
            
         
            $message = '';
            
            $message = '    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                  <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <meta name="x-apple-disable-message-reformatting" />
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    <meta name="color-scheme" content="light dark" />
                    <meta name="supported-color-schemes" content="light dark" />
                    <title></title>
                    <style type="text/css" rel="stylesheet" media="all">
                    
                    @import url("https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap");
                    body {
                      width: 100% !important;
                      height: 100%;
                      margin: 0;
                      -webkit-text-size-adjust: none;
                    }
                    
                    a {
                      color: #ffffff;
                    }
                    
                    a img {
                      border: none;
                    }
                    
                    td {
                      word-break: break-word;
                    }
                    
                    .preheader {
                      display: none !important;
                      visibility: hidden;
                      mso-hide: all;
                      font-size: 1px;
                      line-height: 1px;
                      max-height: 0;
                      max-width: 0;
                      opacity: 0;
                      overflow: hidden;
                    }
                    
                    body,
                    td,
                    th {
                      font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
                    }
                    
                    h1 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 22px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    h2 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 16px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    h3 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 14px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    td,
                    th {
                      font-size: 16px;
                    }
                    
                    p,
                    ul,
                    ol,
                    blockquote {
                      margin: .4em 0 1.1875em;
                      font-size: 16px;
                      line-height: 1.625;
                    }
                    
                    p.sub {
                      font-size: 13px;
                    }
                    
                    .align-right {
                      text-align: right;
                    }
                    
                    .align-left {
                      text-align: left;
                    }
                    
                    .align-center {
                      text-align: center;
                    }
                    
                    .button {
                      background-color: #3869D4;
                      border-top: 10px solid #3869D4;
                      border-right: 18px solid #3869D4;
                      border-bottom: 10px solid #3869D4;
                      border-left: 18px solid #3869D4;
                      display: inline-block;
                      color: #FFF;
                      text-decoration: none;
                      border-radius: 3px;
                      box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
                      -webkit-text-size-adjust: none;
                      box-sizing: border-box;
                    }
                    
                    .button--green {
                      background-color: #22BC66;
                      border-top: 10px solid #22BC66;
                      border-right: 18px solid #22BC66;
                      border-bottom: 10px solid #22BC66;
                      border-left: 18px solid #22BC66;
                    }
                    
                    .button--red {
                      background-color: #FF6136;
                      border-top: 10px solid #FF6136;
                      border-right: 18px solid #FF6136;
                      border-bottom: 10px solid #FF6136;
                      border-left: 18px solid #FF6136;
                    }
                    
                    @media only screen and (max-width: 500px) {
                      .button {
                        width: 100% !important;
                        text-align: center !important;
                      }
                    }
                    
                    .attributes {
                      margin: 0 0 21px;
                    }
                    
                    .attributes_content {
                      background-color: #F4F4F7;
                      padding: 16px;
                    }
                    
                    .attributes_item {
                      padding: 0;
                    }
                
                    
                    .related {
                      width: 100%;
                      margin: 0;
                      padding: 25px 0 0 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .related_item {
                      padding: 10px 0;
                      color: #CBCCCF;
                      font-size: 15px;
                      line-height: 18px;
                    }
                    
                    .related_item-title {
                      display: block;
                      margin: .5em 0 0;
                    }
                    
                    .related_item-thumb {
                      display: block;
                      padding-bottom: 10px;
                    }
                    
                    .related_heading {
                      border-top: 1px solid #CBCCCF;
                      text-align: center;
                      padding: 25px 0 10px;
                    }
                
                    
                    .discount {
                      width: 100%;
                      margin: 0;
                      padding: 24px;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #F4F4F7;
                      border: 2px dashed #CBCCCF;
                    }
                    
                    .discount_heading {
                      text-align: center;
                    }
                    
                    .discount_body {
                      text-align: center;
                      font-size: 15px;
                    }
                 
                    
                    .social {
                      width: auto;
                    }
                    
                    .social td {
                      padding: 0;
                      width: auto;
                    }
                    
                    .social_icon {
                      height: 20px;
                      margin: 0 8px 10px 8px;
                      padding: 0;
                    }
                 
                    
                    .purchase {
                      width: 100%;
                      margin: 0;
                      padding: 35px 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .purchase_content {
                      width: 100%;
                      margin: 0;
                      padding: 25px 0 0 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .purchase_item {
                      padding: 10px 0;
                      color: #51545E;
                      font-size: 15px;
                      line-height: 18px;
                    }
                    
                    .purchase_heading {
                      padding-bottom: 8px;
                      border-bottom: 1px solid #EAEAEC;
                    }
                    
                    .purchase_heading p {
                      margin: 0;
                      color: #85878E;
                      font-size: 12px;
                    }
                    
                    .purchase_footer {
                      padding-top: 15px;
                      border-top: 1px solid #EAEAEC;
                    }
                    
                    .purchase_total {
                      margin: 0;
                      text-align: right;
                      font-weight: bold;
                      color: #333333;
                    }
                    
                    .purchase_total--label {
                      padding: 0 15px 0 0;
                    }
                    
                    body {
                      background-color: #F4F4F7;
                      color: #51545E;
                    }
                    
                    p {
                      color: #51545E;
                    }
                    
                    p.sub {
                      color: #6B6E76;
                    }
                    
                    .email-wrapper {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #F4F4F7;
                    }
                    
                    .email-content {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                 
                    
                    .email-masthead {
                      padding: 25px 0;
                      text-align: center;
                    }
                    
                    .email-masthead_logo {
                      width: 94px;
                    }
                    
                    .email-masthead_name {
                      font-size: 16px;
                      font-weight: bold;
                      color: #A8AAAF;
                      text-decoration: none;
                      text-shadow: 0 1px 0 white;
                    }
                
                    
                    .email-body {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #FFFFFF;
                    }
                    
                    .email-body_inner {
                      width: 570px;
                      margin: 0 auto;
                      padding: 0;
                      -premailer-width: 570px;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #FFFFFF;
                    }
                    
                    .email-footer {
                      width: 570px;
                      margin: 0 auto;
                      padding: 0;
                      -premailer-width: 570px;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      text-align: center;
                    }
                    
                    .email-footer p {
                      color: #6B6E76;
                    }
                    
                    .body-action {
                      width: 100%;
                      margin: 30px auto;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      text-align: center;
                    }
                    
                    .body-sub {
                      margin-top: 25px;
                      padding-top: 25px;
                      border-top: 1px solid #EAEAEC;
                    }
                    
                    .content-cell {
                      padding: 15px;
                    }
                 
                    
                    @media only screen and (max-width: 600px) {
                      .email-body_inner,
                      .email-footer {
                        width: 100% !important;
                      }
                        td,
                        th,
                        p {
                          font-size: 12px;
                        }
                        
                        h3{
                            font-size: 14px;
                        }
                        
                    .purchase_total, 
                    .purchase_total--label{
                        font-size: 12px;
                    }
                      
                    }
                    
                  
                    
                  
                    
                    @media (prefers-color-scheme: dark) {
                      body,
                      .email-body,
                      .email-body_inner,
                      .email-content,
                      .email-wrapper,
                      .email-masthead,
                      .email-footer {
                        background-color: #333333 !important;
                        color: #FFF !important;
                      }
                      p,
                      ul,
                      ol,
                      blockquote,
                      h1,
                      h2,
                      h3 {
                        color: #FFF !important;
                      }
                      .attributes_content,
                      .discount {
                        background-color: #222 !important;
                      }
                      .email-masthead_name {
                        text-shadow: none !important;
                      }
                    }
                    
                    :root {
                      color-scheme: light dark;
                      supported-color-schemes: light dark;
                    }
                    </style>
                
                  </head>
                  <body>
                    <span class="preheader">This is an invoice for your purchase on '.$orderDetails["orderTime"].'</span>
                    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                      <tr>
                        <td align="center">
                          <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                              <td class="email-masthead">
                                <a href="https://example.com" class="f-fallback email-masthead_name">
                                Trikaroo PH
                              </a>
                              </td>
                            </tr>
                            <!-- Email Body -->
                            <tr>
                              <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                                <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                  <!-- Body content -->
                                  <tr>
                                    <td class="content-cell">
                                      <div class="f-fallback">
                                        <h1>Hi  '.$fullname.'</h1>
                                        <p>You recently requested to reset your password for your Trikaoo account. Click the button below to reset it. This password reset is only valid for the next 24 hours.</p>
                                        
                                        <!-- Action -->
                                        <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                          <tr>
                                            <td align="center">
                                              <!-- Border based button
                           https://litmus.com/blog/a-guide-to-bulletproof-buttons-in-email-design -->
                                              <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                                                <tr>
                                                  <td align="center">
                                                    <a color="white" style="color:#fff" href="http://mallody.ph/resources/reset_password.php?userType='.$userType.'&token='.$token.'&id='.$userId.'" class="f-fallback button button--green" target="_blank">Reset password</a>
                                                  </td>
                                                </tr>
                                              </table>
                                            </td>
                                          </tr>
                                        </table>
                                        
                                       <p>If you did not request a password reset, please ignore this email. <a href="www.trikaroo.com.ph">contact support</a></p>
                                                <p>Thanks,
                                                  <br>The Trikaroo Team</p>
                                        <!-- Sub copy -->
                                        <table class="body-sub" role="presentation">
                                          <tr>
                                            <td>
                                              <p class="f-fallback sub">If you are having trouble with the button above, copy and paste the URL below into your web browser.</p>
                                              <p class="f-fallback sub"><a href="wwww.trikaroo.com.ph">wwww.trikaroo.com.ph</a></p>
                                            </td>
                                          </tr>
                                        </table>
                                      </div>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                  <tr>
                                    <td class="content-cell" align="center">
                                      <p class="f-fallback sub align-center">&copy; 2020 Trikaroo. All rights reserved.</p>
                                      <p class="f-fallback sub align-center">
                                        Heng Yen E-Commerce Inc.
                                        <br>1602 Atlanta Center, 31 Annapolis Street,
                                        <br>Greenhills, San Juan City
                                      </p>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </body>
                </html>';
                        
                        
                        
          try{
            
            
             $mail = new PHPMailer;
             $mail->isSMTP();
            
             $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            
            
            $mail->Host = 'mail.mallody.com.ph';
             $mail->Port = 587;
            //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->SMTPAuth = true;
            
             $mail->Username = 'verifications@mallody.com.ph';
             $mail->Password = 'hengyen66888';
            
             $mail->setFrom( 'verifications@trikaroo.com.ph', 'Trikaroo Verifications' );
             $mail->addAddress($email, $fullname);
            // $mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
             $mail->Subject = 'Trikaroo Reset Password';
    
             $mail->isHTML(true);  
             $mail->Body    = $message;
             $mail->AltBody = '';
            //$mail->addAttachment($applicantId);
    
            //if( $none_co_borrower != "true"){
            //    $mail->addAttachment($co_applicantId);
            //}
            //$account_sid = "AC6fb45a05498750a9ffd49e84154e16f6";
            //$auth_token = "3f37e731f441f5dc82ed1c8abf8aa118";
            //$twilioMobileNum = "+19389991544";
            //$toMobileNum = "+639398296855";
            //$message= "NEW AUTO LOAN APPLICATION!. Check your email.";
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            
             if (!$mail->send()) {
                 echo 'Mailer Error: '. $mail->ErrorInfo;
             } else {
                 echo $firstname;
                // header("Location: ../success.php?first-name=".$firstname);
             }
    
        
        }catch(Exception $e){
          echo 'Mailer Error';
        }
    }
    
    
    
    function setOrderLogs($statusCode, $orderId){
        
        
        $insert['iStatusCode'] = (int) $statusCode;
        $insert['iOrderId'] = (int) $orderId;
        $insert['dDate'] = @date("Y-m-d H:i:s");
            
        $result = myQuery("order_status_logs", $insert, "insert");
        
    }
    
    
    
    //CHECKNG LOCATION INSIDE A BOUNDARY
    
    
    function pointStringToCoordinates($pointString) {
            $coordinates = explode(",", $pointString);
            return array("x" => trim($coordinates[0]), "y" => trim($coordinates[1]));
    }

    function isWithinBoundary($point,$polygon){
        $result =FALSE;
        $point = pointStringToCoordinates($point);
        $vertices = array();
        foreach ($polygon as $vertex) 
        {
            $vertices[] = pointStringToCoordinates($vertex); 
        }
        // Check if the point is inside the polygon or on the boundary
        $intersections = 0; 
        $vertices_count = count($vertices);
        for ($i=1; $i < $vertices_count; $i++) 
        {
            $vertex1 = $vertices[$i-1]; 
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) 
            { 
                // This point is on an horizontal polygon boundary
                $result = TRUE;
                // set $i = $vertices_count so that loop exits as we have a boundary point
                $i = $vertices_count;
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) 
            { 
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x']; 
                if ($xinters == $point['x']) 
                { // This point is on the polygon boundary (other than horizontal)
                    $result = TRUE;
                    // set $i = $vertices_count so that loop exits as we have a boundary point
                    $i = $vertices_count;
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) 
                {
                    $intersections++; 
                }
            } 
        }
        // If the number of edges we passed through is even, then it's in the polygon. 
        // Have to check here also to make sure that we haven't already determined that a point is on a boundary line
        if ($intersections % 2 != 0 && $result == FALSE) 
        {
            $result = TRUE;
        } 
        return $result;
    }
    
    function contains($point, $polygon) {
        if ($polygon[0] != $polygon[count($polygon) - 1])
            $polygon[count($polygon)] = $polygon[0];
        $j = 0;
        $oddNodes = false;
        $x = $point[1];
        $y = $point[0];
        $n = count($polygon);
        for ($i = 0; $i < $n; $i++) {
            $j++;
            if ($j == $n) {
                $j = 0;
            }
            if ((($polygon[$i][0] < $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] < $y) && ($polygon[$i][0] >= $y))) {
                if ((float)$polygon[$i][1] + ($y - (float)$polygon[$i][0]) / ((float)$polygon[$j][0] - (float)$polygon[$i][0]) *  ( (float)$polygon[$j][1] - (float)$polygon[$i][1]) < $x) {
                    $oddNodes = !$oddNodes;
                }
            }
        }
        return $oddNodes;
    }

    
    function isLocationRestricted($Address_Array) {
        
        if (!empty($Address_Array)) {
            unset($where);
            
            $where['eStatus'] = "Active";
            $where['eFor'] = "Restrict";
            
            $fieldname = array("tLatitude", "tLongitude");
            $allowed_data = myQuery("location_master", $fieldname, "selectall",  $where);
            
            if (count($allowed_data) > 0) {
                $allowed_ans = false;
                $polygon = array();
                foreach ($allowed_data as $key => $val) {
                    $latitude = explode(",", $val['tLatitude']);
                    $longitude = explode(",", $val['tLongitude']);
                    for ($x = 0; $x < count($latitude); $x++) {
                        if (!empty($latitude[$x]) || !empty($longitude[$x])) {
                            $polygon[$key][] = array(
                                $latitude[$x],
                                $longitude[$x]
                            );
                        }
                    }
                    //print_r($polygon[$key]);
                    if ($polygon[$key]) {
                        $address = contains($Address_Array, $polygon[$key]) ? 'IN' : 'OUT';
                        if ($address == 'IN') {
                            $allowed_ans = true;
                            break;
                        }
                    }
                }
            } else {
                $allowed_ans = false;
            }
          
        }
         return $allowed_ans;
    }
    
    function isLocationAllowed($Address_Array) {
        
        if (!empty($Address_Array)) {
            
            unset($where);
            
            $where['eStatus'] = "Active";
            $where['eFor'] = "Allow";
            
            $fieldname = array("tLatitude", "tLongitude");
        
            $allowed_data = myQuery("location_master", $fieldname, "selectall",  $where);
            
            if (count($allowed_data) > 0) {
                $allowed_ans = false;
                $polygon = array();
                foreach ($allowed_data as $key => $val) {
                    $latitude = explode(",", $val['tLatitude']);
                    $longitude = explode(",", $val['tLongitude']);
                    for ($x = 0; $x < count($latitude); $x++) {
                        if (!empty($latitude[$x]) || !empty($longitude[$x])) {
                            $polygon[$key][] = array(
                                $latitude[$x],
                                $longitude[$x]
                            );
                        }
                    }
                    //print_r($polygon[$key]);
                    if ($polygon[$key]) {
                        $address = contains($Address_Array, $polygon[$key]) ? 'IN' : 'OUT';
                        if ($address == 'IN') {
                            $allowed_ans = true;
                            break;
                        }
                    }
                }
            } else {
                $allowed_ans = false;
            }
          
        }
         return $allowed_ans;
    }
    
    
    function isLocationAllowedForPabili($Address_Array) {
        
        if (!empty($Address_Array)) {
            
            unset($where);
            
            $where['eStatus'] = "Active";
            $where['ePabiliEnable'] = "Enable";
            $where['eFor'] = "Allow";
            
            $fieldname = array("tLatitude", "tLongitude");
        
            $allowed_data = myQuery("location_master", $fieldname, "selectall",  $where);
            
            if (count($allowed_data) > 0) {
                $allowed_ans = false;
                $polygon = array();
                foreach ($allowed_data as $key => $val) {
                    $latitude = explode(",", $val['tLatitude']);
                    $longitude = explode(",", $val['tLongitude']);
                    for ($x = 0; $x < count($latitude); $x++) {
                        if (!empty($latitude[$x]) || !empty($longitude[$x])) {
                            $polygon[$key][] = array(
                                $latitude[$x],
                                $longitude[$x]
                            );
                        }
                    }
                    //print_r($polygon[$key]);
                    if ($polygon[$key]) {
                        $address = contains($Address_Array, $polygon[$key]) ? 'IN' : 'OUT';
                        if ($address == 'IN') {
                            $allowed_ans = true;
                            break;
                        }
                    }
                }
            } else {
                $allowed_ans = false;
            }
          
        }
         return $allowed_ans;
    }
    
    
    function getLocationArea($Address_Array) {
        
        if (!empty($Address_Array)) {
            
            unset($where);
            
            $where['eStatus'] = "Active";
            $where['eFor'] = "Allow";
            
            $fieldname = array("iLocationId","tLatitude", "tLongitude");
        
            $allowed_data = myQuery("location_master", $fieldname, "selectall",  $where, " ORDER BY iLocationId DESC");
            
            if (count($allowed_data) > 0) {
                $allowed_ans = false;
                $locationId = null;
                $polygon = array();
                foreach ($allowed_data as $key => $val) {
                    $latitude = explode(",", $val['tLatitude']);
                    $longitude = explode(",", $val['tLongitude']);
                    for ($x = 0; $x < count($latitude); $x++) {
                        if (!empty($latitude[$x]) || !empty($longitude[$x])) {
                            $polygon[$key][] = array(
                                $latitude[$x],
                                $longitude[$x]
                            );
                        }
                    }
                    //print_r($polygon[$key]);
                    if ($polygon[$key]) {
                        $address = contains($Address_Array, $polygon[$key]) ? 'IN' : 'OUT';
                        if ($address == 'IN') {
                            $allowed_ans = true;
                            $locationId = $allowed_data[$key]['iLocationId'];
                            
                            break;
                        }
                    }
                }
            } else {
                $allowed_ans = false;
            }
          
        }
         return $locationId;
    }
    
    
    function getApplicationSettingsMode($AppName) {
        
        
        $where['vTitle'] =  $AppName;
        $where['eStatus'] = "Active";
            
        $fieldname = array("vValue", "vTitle", "eStatus");
    
        $result = myQuery("admin_settings", $fieldname, "selectall",  $where);
        
        
        return $result[0];

        
    }
    
    function getLGUArea($sourceLat, $sourceLong){
        
        $USERAGENT = $_SERVER['HTTP_USER_AGENT'];

        $opts = array('http'=>array('header'=>"User-Agent: $USERAGENT\r\n"));
        $context = stream_context_create($opts);
        $url4 = file_get_contents("https://nominatim.openstreetmap.org/reverse?format=json&lat=$sourceLat&lon=$sourceLong&zoom=18&addressdetails=1", false, $context);
        $osmaddress = json_decode($url4);  
        $location = array();
        
        $location['type'] =  "nominatim";
        $location['address'] =  $osmaddress ->display_name;
        $location['latitude'] =  $osmaddress ->lat;
        $location['longitude'] =  $osmaddress ->lon;
        $location['address_name'] = $osmaddress->address ->building;
        $location['housenumber'] = $osmaddress->addresss ->house_number;
        $location['street'] = $osmaddress->address ->road;
        $location['locality'] = $osmaddress->address ->quarter; 
        $location['district'] = ($osmaddress->address ->city_district == null ) ? $osmaddress->address -> district.'' : $osmaddress->address ->city_district.'';
        $location['city'] =  $osmaddress->address ->city;
        $location['state'] = $osmaddress->address->region == "Metro Manila" ? "Metro Manila" : $osmaddress->address->state;
        $location['region'] = $osmaddress->address->region == "Metro Manila" ? "National Capital Region" : $osmaddress->address->region;
        $location['country'] = $osmaddress->address ->country;
        
        if($osmaddress->address ->city == null){
            
            if($osmaddress->address ->city_district == null ){
                if($osmaddress->address ->district == null ){
                
                    // $serviceArea = ""
                }else{
                     $serviceArea = $osmaddress->address ->city_district;
                }
                
            }else{
                
                $serviceArea = $osmaddress->address ->city_district;
                
            }
        }else{
            $serviceArea = $osmaddress->address ->city;
        }
        
        
    }
    
    function getApplication_CheckoutRange_Mode($AppName) {
        
        
        $where['vTitle'] =  "Checkout_Range";
        $where['eStatus'] = "Active";
            
        $fieldname = array("vValue", "vTitle", "eStatus");
        $result = myQuery("admin_settings", $fieldname, "selectall",  $where);
        
        
        return $result[0];

        
    }
    
    
    
    
    function sendPabiliInvoice($orderId){
         global $obj ; global $database;
        $db = $obj;
        
        $orderDetails = array();
        
        $sql = "SELECT * FROM orders WHERE iOrderId = $orderId ";

        $statement = $db->query($sql);
        
        $result = $statement ->fetchAll(); 
        
        // if($result[0]['iDriverId'] == "" || $result[0]['iDriverId'] == 0){
            
        //     $orderDetails['orderDeliveryFee_min'] = 40;
        //     $orderDetails['orderDeliveryFee_max'] = 80;
            
        // }else{
        
            $userId =  $result[0]['iUserId'];
        
            //USER DATA
            
            
            $sql = "SELECT * FROM register_user WHERE iUserId = '". $result[0]['iUserId']."'";
    
            $statement = $db->query($sql);
            
            $userData = $statement ->fetchAll(); 
            
            
            $where['iCompanyId'] = $result[0]['iCompanyId'];
                
            $companyData = myQuery("company", array("vCompany", "vRestuarantLocation", "vRestuarantLocationLong", "vRestuarantLocationLat"), "selectall",  $where);
            
            $LatLong = get_lat_long($result[0]['vDeliveryAddress']);
            $addressLatLong = explode(",",$LatLong);
            
            $storeLocationArr = array( $companyData[0]['vRestuarantLocationLat'],$companyData[0]['vRestuarantLocationLong']);
        
            // FILTERING THE LOCATIONS 
            $destinationLocationId = getLocationArea($storeLocationArr);
            
            $sql = "SELECT * FROM register_toda WHERE iLocationId = '".$destinationLocationId."'";
            $statement = $db->query($sql);
            $todaData = $statement ->fetchAll();
            
            
            // RETRIVING CONSTANTS FARE SYSTEM PER LOCATIONS
            $todaId = $todaData[0]['iTodaId'];
            $todaName = $todaData[0]['vTodaName'];
            $todaRouteNo = $todaData[0]['vTodaRouteNo'];
            $baseFare = (float) $todaData[0]['iPabiliBaseFare'];
            $serviceCharge = (float) $todaData[0]['fServiceCharge'];
            $farePricePerKm =(float) $todaData[0]['fPricePerKM'];
            $farePricePerMin = (float) $todaData[0]['fPricePerMin'];
            $radiusDistance = (int) $todaData[0]['fRadius'];
            
            $sql2 = "SELECT sum(iQty) as itemQty FROM order_details WHERE iOrderId = '". $orderId."'";
            $statement = $db->query($sql2);
            $itemQty = $statement ->fetchAll();
            
            $orderDetails['orderId'] = $result[0]['iOrderId'];
            $orderDetails['orderNo'] = $result[0]['vOrderNo'];
            $orderDetails['orderQty'] = $itemQty[0]['itemQty'];
            $orderDetails['orderDate'] = $result[0]['tOrderRequestDate'];
            $orderDetails['orderType'] = $result[0]['vOrderType'];
            
            $orderDetails['orderStoreName'] = $companyData[0]['vCompany'];
            $orderDetails['orderStoreLocation'] = $companyData[0]['vRestuarantLocation'];
            $orderDetails['orderStoreLat'] = $companyData[0]['vRestuarantLocationLat'];
            $orderDetails['orderStoreLong'] = $companyData[0]['vRestuarantLocationLong'];
            
        
            $orderDetails['orderDeliveryName'] = $result[0]['vName'];
            $orderDetails['orderDeliveryAddress'] = $result[0]['vDeliveryAddress'];
            $orderDetails['orderDeliveryAddressLat'] = $addressLatLong[0];
            $orderDetails['orderDeliveryAddressLong'] =$addressLatLong[1];
            
            if($result[0]['vDeliveryAddress_2']  != ""){
                
               $orderDetails['orderDeliveryName2'] = $result[0]['vDeliveryAddress_2'];
               $orderDetails['orderDeliveryAddress2']  = explode(",", get_lat_long($result[0]['vDeliveryAddress_2'])) ;
               $orderDetails['orderDeliveryAddressLat2']  =   $deliveryAddressTemp2[0];
               $orderDetails['orderDeliveryAddressLong2'] =   $deliveryAddressTemp2[1];
               
            } else {
                
                $orderDetails['orderDeliveryName2'] = "";
                $orderDetails['orderDeliveryAddress2'] = "";
                $orderDetails['orderDeliveryAddressLat2']  =  "0";
                $orderDetails['orderDeliveryAddressLong2'] =   "0";
                
            }
            
          
            
            $date = date_create($result[0]['dDate']);
    
            $orderDetails['orderTime'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $orderDetails['orderInstruction'] = $result[0]['vDeliveryInstruction'];
            
            $sql2 = "SELECT od.iMenuItemId, od.vItemName as itemName, od.fOriginalPrice as itemPrice, od.fSubTotal as itemSubtotal, od.iQty as itemQty, od.vDescription as itemDesc, od.vCancel as itemCancel, vImage as itemImage FROM order_details as od WHERE od.iOrderId = ".$result[0]['iOrderId'];
            
            $statement = $db->query($sql2);
            
            $items = $statement ->fetchAll(); 
            
            
            $itemsCount = 0;
            
            
            for($i = 0; $i < count($items); $i++) {
                
                 $itemsCount = $itemsCount+(int)$items[$i]['itemQty'];
                
                $orderDetails['orderItems'][] =  $items[$i];
                
            }
            
            $orderDetails['orderSubtotalAmount'] = $result[0]['fSubTotal'];
            $orderDetails['orderDeliveryFeeAmount'] = $result[0]['fDeliveryCharge'];
            $orderDetails['orderTotalAmount'] = $result[0]['fTotalGenerateFare'];
            
            
            $orderDetails['orderQty'] = $itemsCount;
            $orderDetails['orderPaymentMethod'] = $result[0]['ePaymentOption'];
            $orderDetails['pabili_rate'] = $baseFare;
            $orderDetails['grocery_rate'] = $serviceCharge;
            
            
            if($result[0]['iDriverId'] == "" || $result[0]['iDriverId'] == 0){
                
                $orderDetails['orderSubtotalAmount'] = $result[0]['fSubTotal'];
                 $orderDetails['min_deliveryCharge'] = $baseFare;
                $orderDetails['max_deliveryCharge'] =  $baseFare+($radiusDistance*$farePricePerMin);
                
            }else{
                
                $orderDetails['min_deliveryCharge'] = $baseFare;
                $orderDetails['max_deliveryCharge'] =  $baseFare+($radiusDistance*$farePricePerMin);
                $orderDetails['orderSubtotalAmount'] = $result[0]['fSubTotal'];
                $orderDetails['orderDeliveryFeeAmount'] = $result[0]['fDeliveryCharge'];
                $orderDetails['orderTotalAmount'] = $result[0]['fTotalGenerateFare'];
        
            }
            
    
                   
            $messageArray['response'] = 1;
            $messageArray['status'] = "Okay";
            $messageArray['result'] = $orderDetails;
            $messageArray['iDriverId'] = $result[0]['iDriverId'];
            
            
            
            $messageArray['userId'] = $userData[0]['iUserId'];
            $messageArray['userLastName'] = $userData[0]['vLastName'];
            $messageArray['userFullName'] = $userData[0]['vName'];
            $messageArray['userImage'] = $userData[0]['vImgName'];
            $messageArray['userLat'] = $userData[0]['vLatitude'];
            $messageArray['userLong'] = $userData[0]['vLongitude'];
            
            
            $contentItems = "";                       
            for($x = 0; $x<count($orderDetails['orderItems']);$x++){
                $contentItems .=' <tr>
                  <td width="80%" class="purchase_item"><span class="f-fallback">'.$orderDetails['orderItems'][$x]['itemQty'].'x - '.$orderDetails['orderItems'][$x]['itemName'].'</span></td>
                  <td class="align-right" width="20%" class="purchase_item"><span class="f-fallback">&#8369;'.$orderDetails['orderItems'][$x]['itemSubtotal'].'</span></td>
                </tr>';
                
            }
            
            $message = '
              <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                  <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                    <meta name="x-apple-disable-message-reformatting" />
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    <meta name="color-scheme" content="light dark" />
                    <meta name="supported-color-schemes" content="light dark" />
                    <title></title>
                    <style type="text/css" rel="stylesheet" media="all">
                    
                    @import url("https://fonts.googleapis.com/css?family=Nunito+Sans:400,700&display=swap");
                    body {
                      width: 100% !important;
                      height: 100%;
                      margin: 0;
                      -webkit-text-size-adjust: none;
                    }
                    
                    a {
                      color: #3869D4;
                    }
                    
                    a img {
                      border: none;
                    }
                    
                    td {
                      word-break: break-word;
                    }
                    
                    .preheader {
                      display: none !important;
                      visibility: hidden;
                      mso-hide: all;
                      font-size: 1px;
                      line-height: 1px;
                      max-height: 0;
                      max-width: 0;
                      opacity: 0;
                      overflow: hidden;
                    }
                    
                    body,
                    td,
                    th {
                      font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
                    }
                    
                    h1 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 22px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    h2 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 16px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    h3 {
                      margin-top: 0;
                      color: #333333;
                      font-size: 14px;
                      font-weight: bold;
                      text-align: left;
                    }
                    
                    td,
                    th {
                      font-size: 16px;
                    }
                    
                    p,
                    ul,
                    ol,
                    blockquote {
                      margin: .4em 0 1.1875em;
                      font-size: 16px;
                      line-height: 1.625;
                    }
                    
                    p.sub {
                      font-size: 13px;
                    }
                    
                    .align-right {
                      text-align: right;
                    }
                    
                    .align-left {
                      text-align: left;
                    }
                    
                    .align-center {
                      text-align: center;
                    }
                    
                    .button {
                      background-color: #3869D4;
                      border-top: 10px solid #3869D4;
                      border-right: 18px solid #3869D4;
                      border-bottom: 10px solid #3869D4;
                      border-left: 18px solid #3869D4;
                      display: inline-block;
                      color: #FFF;
                      text-decoration: none;
                      border-radius: 3px;
                      box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
                      -webkit-text-size-adjust: none;
                      box-sizing: border-box;
                    }
                    
                    .button--green {
                      background-color: #22BC66;
                      border-top: 10px solid #22BC66;
                      border-right: 18px solid #22BC66;
                      border-bottom: 10px solid #22BC66;
                      border-left: 18px solid #22BC66;
                    }
                    
                    .button--red {
                      background-color: #FF6136;
                      border-top: 10px solid #FF6136;
                      border-right: 18px solid #FF6136;
                      border-bottom: 10px solid #FF6136;
                      border-left: 18px solid #FF6136;
                    }
                    
                    @media only screen and (max-width: 500px) {
                      .button {
                        width: 100% !important;
                        text-align: center !important;
                      }
                    }
                    
                    .attributes {
                      margin: 0 0 21px;
                    }
                    
                    .attributes_content {
                      background-color: #F4F4F7;
                      padding: 16px;
                    }
                    
                    .attributes_item {
                      padding: 0;
                    }
                
                    
                    .related {
                      width: 100%;
                      margin: 0;
                      padding: 25px 0 0 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .related_item {
                      padding: 10px 0;
                      color: #CBCCCF;
                      font-size: 15px;
                      line-height: 18px;
                    }
                    
                    .related_item-title {
                      display: block;
                      margin: .5em 0 0;
                    }
                    
                    .related_item-thumb {
                      display: block;
                      padding-bottom: 10px;
                    }
                    
                    .related_heading {
                      border-top: 1px solid #CBCCCF;
                      text-align: center;
                      padding: 25px 0 10px;
                    }
                
                    
                    .discount {
                      width: 100%;
                      margin: 0;
                      padding: 24px;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #F4F4F7;
                      border: 2px dashed #CBCCCF;
                    }
                    
                    .discount_heading {
                      text-align: center;
                    }
                    
                    .discount_body {
                      text-align: center;
                      font-size: 15px;
                    }
                 
                    
                    .social {
                      width: auto;
                    }
                    
                    .social td {
                      padding: 0;
                      width: auto;
                    }
                    
                    .social_icon {
                      height: 20px;
                      margin: 0 8px 10px 8px;
                      padding: 0;
                    }
                 
                    
                    .purchase {
                      width: 100%;
                      margin: 0;
                      padding: 35px 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .purchase_content {
                      width: 100%;
                      margin: 0;
                      padding: 25px 0 0 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                    
                    .purchase_item {
                      padding: 10px 0;
                      color: #51545E;
                      font-size: 15px;
                      line-height: 18px;
                    }
                    
                    .purchase_heading {
                      padding-bottom: 8px;
                      border-bottom: 1px solid #EAEAEC;
                    }
                    
                    .purchase_heading p {
                      margin: 0;
                      color: #85878E;
                      font-size: 12px;
                    }
                    
                    .purchase_footer {
                      padding-top: 15px;
                      border-top: 1px solid #EAEAEC;
                    }
                    
                    .purchase_total {
                      margin: 0;
                      text-align: right;
                      font-weight: bold;
                      color: #333333;
                    }
                    
                    .purchase_total--label {
                      padding: 0 15px 0 0;
                    }
                    
                    body {
                      background-color: #F4F4F7;
                      color: #51545E;
                    }
                    
                    p {
                      color: #51545E;
                    }
                    
                    p.sub {
                      color: #6B6E76;
                    }
                    
                    .email-wrapper {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #F4F4F7;
                    }
                    
                    .email-content {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                    }
                 
                    
                    .email-masthead {
                      padding: 25px 0;
                      text-align: center;
                    }
                    
                    .email-masthead_logo {
                      width: 94px;
                    }
                    
                    .email-masthead_name {
                      font-size: 16px;
                      font-weight: bold;
                      color: #A8AAAF;
                      text-decoration: none;
                      text-shadow: 0 1px 0 white;
                    }
                
                    
                    .email-body {
                      width: 100%;
                      margin: 0;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #FFFFFF;
                    }
                    
                    .email-body_inner {
                      width: 570px;
                      margin: 0 auto;
                      padding: 0;
                      -premailer-width: 570px;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      background-color: #FFFFFF;
                    }
                    
                    .email-footer {
                      width: 570px;
                      margin: 0 auto;
                      padding: 0;
                      -premailer-width: 570px;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      text-align: center;
                    }
                    
                    .email-footer p {
                      color: #6B6E76;
                    }
                    
                    .body-action {
                      width: 100%;
                      margin: 30px auto;
                      padding: 0;
                      -premailer-width: 100%;
                      -premailer-cellpadding: 0;
                      -premailer-cellspacing: 0;
                      text-align: center;
                    }
                    
                    .body-sub {
                      margin-top: 25px;
                      padding-top: 25px;
                      border-top: 1px solid #EAEAEC;
                    }
                    
                    .content-cell {
                      padding: 15px;
                    }
                 
                    
                    @media only screen and (max-width: 600px) {
                      .email-body_inner,
                      .email-footer {
                        width: 100% !important;
                      }
                        td,
                        th,
                        p {
                          font-size: 12px;
                        }
                        
                        h3{
                            font-size: 14px;
                        }
                        
                    .purchase_total, 
                    .purchase_total--label{
                        font-size: 12px;
                    }
                      
                    }
                    
                  
                    
                  
                    
                    @media (prefers-color-scheme: dark) {
                      body,
                      .email-body,
                      .email-body_inner,
                      .email-content,
                      .email-wrapper,
                      .email-masthead,
                      .email-footer {
                        background-color: #333333 !important;
                        color: #FFF !important;
                      }
                      p,
                      ul,
                      ol,
                      blockquote,
                      h1,
                      h2,
                      h3 {
                        color: #FFF !important;
                      }
                      .attributes_content,
                      .discount {
                        background-color: #222 !important;
                      }
                      .email-masthead_name {
                        text-shadow: none !important;
                      }
                    }
                    
                    :root {
                      color-scheme: light dark;
                      supported-color-schemes: light dark;
                    }
                    </style>
                
                  </head>
                  <body>
                    <span class="preheader">This is an invoice for your purchase on '.$orderDetails["orderTime"].'</span>
                    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                      <tr>
                        <td align="center">
                          <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                              <td class="email-masthead">
                                <a href="http://trikaroo.com.ph/" class="f-fallback email-masthead_name">
                                Trikaroo PH
                              </a>
                              </td>
                            </tr>
                            <!-- Email Body -->
                            <tr>
                              <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                                <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                  <!-- Body content -->
                                  <tr>
                                    <td class="content-cell">
                                      <div class="f-fallback">
                                        <h1>Hi  '.$messageArray["userFullName"].'</h1>
                                        <p>Thank you for using Trikaroo. This is an invoice for your recent purchase.</p>
                                        <table class="attributes" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                          <tr>
                                            <td class="attributes_content">
                                              <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                                <tr>
                                                  <td class="attributes_item">
                                                    <span class="f-fallback">
                              <strong>Amount Due:</strong> &#8369;'.$orderDetails["orderTotalAmount"].'
                            </span>
                                                  </td>
                                                </tr>
                                                <tr>
                                                  <td class="attributes_item">
                                                    <span class="f-fallback">
                              <strong>Due By:</strong> '.$messageArray["userFullName"].'
                            </span>
                                                  </td>
                                                </tr>
                                              </table>
                                            </td>
                                          </tr>
                                        </table>
                                        <!-- Action -->
                                        <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                          <tr>
                                            <td align="center">
                                              <!-- Border based button
                           https://litmus.com/blog/a-guide-to-bulletproof-buttons-in-email-design -->
                                             
                                            </td>
                                          </tr>
                                        </table>
                                        <table class="purchase" width="100%" cellpadding="0" cellspacing="0">
                                          <tr>
                                            <td>
                                              <h3>'.$orderDetails["orderNo"].'</h3>
                                            </td>
                                            <td>
                                              <h3 class="align-right">'.$orderDetails["orderTime"].'</h3>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td colspan="2">
                                              <table class="purchase_content" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <th class="purchase_heading" align="left">
                                                    <p class="f-fallback">Description</p>
                                                  </th>
                                                  <th class="purchase_heading" align="right">
                                                    <p class="f-fallback">Amount</p>
                                                  </th>
                                                </tr>
                                                
                                                
                                                
                                                
                                                
                                               '.$contentItems.'
                                                
                                                
                                                
                                                
                                                
                                               
                                           
                                                 <tr>
                                                  <td width="80%" class="purchase_footer" valign="middle">
                                                    <p class="f-fallback purchase_total purchase_total--label">Subtotal</p>
                                                  </td>
                                                  <td width="20%" class="purchase_footer" valign="middle">
                                                    <p class="f-fallback purchase_total"> &#8369;'.$orderDetails["orderSubtotalAmount"].'</p>
                                                  </td>
                                                </tr>
                                                <tr>
                                                  <td width="80%" class="purchase_footer" valign="middle">
                                                    <p class="f-fallback purchase_total purchase_total--label">Delivery Fee</p>
                                                  </td>
                                                  <td width="20%" class="purchase_footer" valign="middle">
                                                    <p class="f-fallback purchase_total"> &#8369;'.$orderDetails["orderDeliveryFeeAmount"].'</p>
                                                  </td>
                                                </tr>
                                        
                                                <tr>
                                                  <td width="80%" class="purchase_footer" valign="middle">
                                                    <p class="f-fallback purchase_total purchase_total--label">Total</p>
                                                  </td>
                                                  <td width="20%" class="purchase_footer" valign="middle">
                                                    <p class="f-fallback purchase_total"> &#8369;'.$orderDetails["orderTotalAmount"].'</p>
                                                  </td>
                                                </tr>
                                              </table>
                                            </td>
                                          </tr>
                                        </table>
                                        <p>If you have any questions about this invoice, simply reply to this email or reach out to our <a href="http://trikaroo.com.ph/">support team</a> for help.</p>
                                        <p>Cheers,
                                          <br>The Trikaroo Team</p>
                                        <!-- Sub copy -->
                                        <table class="body-sub" role="presentation">
                                          <tr>
                                            <td>
                                              <p class="f-fallback sub">If you are having trouble with the button above, copy and paste the URL below into your web browser.</p>
                                              <p class="f-fallback sub"><a href="http://trikaroo.com.ph/">wwww.trikaroo.com.ph</a></p>
                                            </td>
                                          </tr>
                                        </table>
                                      </div>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                  <tr>
                                    <td class="content-cell" align="center">
                                      <p class="f-fallback sub align-center">&copy; 2020 Trikaroo. All rights reserved.</p>
                                      <p class="f-fallback sub align-center">
                                        Heng Yen E-Commerce Inc.
                                        <br>1602 Atlanta Center, 31 Annapolis Street,
                                        <br>Greenhills, San Juan City
                                      </p>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </body>
                </html>';
                
        try{
            
             $mail = new PHPMailer;
             $mail->isSMTP();
            
             $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            
            
             $mail->Host = 'mail.mallody.com.ph';
             $mail->Port = 587;
            //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->SMTPAuth = true;
            
             $mail->Username = 'invoice@mallody.com.ph';
             $mail->Password = 'hengyen66888';
            
             $mail->setFrom( 'invoice@trikaroo.com.ph', 'Trikaroo Invoice' );
             $mail->addAddress($result[0]['vUserEmail'], $result[0]['vName']);
            //$mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
             $mail->Subject = 'Trikaroo Invoice';
    
             $mail->isHTML(true);  
             $mail->Body    = $message;
             $mail->AltBody = '';
            //$mail->addAttachment($applicantId);
    
            //if( $none_co_borrower != "true"){
            //    $mail->addAttachment($co_applicantId);
            //}
            //$account_sid = "AC6fb45a05498750a9ffd49e84154e16f6";
            //$auth_token = "3f37e731f441f5dc82ed1c8abf8aa118";
            //$twilioMobileNum = "+19389991544";
            //$toMobileNum = "+639398296855";
            //$message= "NEW AUTO LOAN APPLICATION!. Check your email.";
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            
             if (!$mail->send()) {
                 echo 'Mailer Error: '. $mail->ErrorInfo;
             } else {
                 echo $firstname;
                // header("Location: ../success.php?first-name=".$firstname);
             }
    
        
        }catch(Exception $e){
          echo 'Mailer Error';
        }    
        
        
        
    }
    
    
    function encryptString( $q ) {
        // $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
        // $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
        $qEncoded = crypt($q);
       
        return( $qEncoded );
    }
    
    function decryptString( $q ) {
        $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
        $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded );
    }
    
    
    
    function checkReferralCode( $code ) {
        
        unset($where);
        unset( $fieldname );
        
        $where['vRefCode'] = $code;
        
        $fieldname = array("iUserId", "vName","fRewardPointsBalance");
    
        $data = myQuery("register_user", $fieldname, "selectall",  $where);
        
        if(count($data) > 0){
            $response['response'] = "true";
            $response['iUserId'] =  $data[0]['iUserId'];
            $response['vName'] =   $data[0]['vName'];
            $response['rewardBalance'] = $data[0]['fRewardPointsBalance'];
            
            return $response;
            
        }else{
        
            $response['response'] = "false";
        
            
            return $response;
            
        }
        
        
    }
    
    function timeAgo($timestamp){
        $datetime1=new DateTime("now");
        $datetime2=date_create($timestamp);
        $diff=date_diff($datetime1, $datetime2);
        $timemsg='';
        if($diff->y > 0){
            $timemsg = $diff->y .' year'. ($diff->y > 1?"s":'');
    
        }
        else if($diff->m > 0){
         $timemsg = $diff->m . ' month'. ($diff->m > 1?"s":'');
        }
        else if($diff->d > 0){
         $timemsg = $diff->d .' day'. ($diff->d > 1?"s":'');
        }
        else if($diff->h > 0){
         $timemsg = $diff->h .' hour'.($diff->h > 1 ? "s":'');
        }
        else if($diff->i > 0){
         $timemsg = $diff->i .' minute'. ($diff->i > 1?"s":'');
        }
        else if($diff->s > 0){
         $timemsg = $diff->s .' second'. ($diff->s > 1?"s":'');
        }
    
        $timemsg = $timemsg.' ago.';
        return $timemsg;
    }


     function timeDifference($timestamp){

        // $date = new DateTime( 'now' );
        // $date2 = new DateTime( '2009-10-05 18:11:08' );

        $res_seconds = 0;

        $start = new DateTime("now");
        $end = new DateTime($timestamp);
        $diff = $end->diff($start);

        $diff_sec = $diff->format('%r').( // prepend the sign - if negative, change it to R if you want the +, too
                ($diff->s)+ // seconds (no errors)
                (60*($diff->i))+ // minutes (no errors)
                (60*60*($diff->h))+ // hours (no errors)
                (24*60*60*($diff->d))+ // days (no errors)
                (30*24*60*60*($diff->m))+ // months (???)
                (365*24*60*60*($diff->y)) // years (???)
                );

        if($diff_sec <= 1800){
            $res_seconds =  1800 - $diff_sec;
        }else{
            $res_seconds = 0;
        }

        // $daysInSecs = $diff->format('%r%a') * 24 * 60 * 60;
        // $hoursInSecs = $diff->h * 60 * 60;
        // $minsInSecs = $diff->i * 60;

        // $seconds = $daysInSecs + $hoursInSecs + $minsInSecs + $diff->s;

        //echo $seconds; // output: 235

        // $datetime1=new DateTime("now");
        // $datetime2=date_create($timestamp);
        // $diff=date_diff($datetime1, $datetime2);
        // $timemsg='';
        // if($diff->y > 0){
        //     $year =$diff->y;
        //     $timemsg = $diff->y .' year'. ($diff->y > 1?"'s":'');
    
        // }
        // else if($diff->m > 0){
        //     $month =$diff->m;
        //     $timemsg = $diff->m . ' month'. ($diff->m > 1?"'s":'');
        // }
        // else if($diff->d > 0){
        //     $day =$diff->d;
        //     $timemsg = $diff->d .' day'. ($diff->d > 1?"'s":'');
        // }
        // else if($diff->h > 0){
        //     $hour =$diff->h;
        //     $timemsg = $diff->h .' hour'.($diff->h > 1 ? "'s":'');
        // }
        // else if($diff->i > 0){
        //     $minute =$diff->i;
        //     $timemsg = $diff->i .' minute'. ($diff->i > 1?"'s":'');
        // }
        // else if($diff->s > 0){
        //     $second =$diff->s;
        //     $timemsg = $diff->s .' second'. ($diff->s > 1?"'s":'');
        // }
        
        // $timemsg = $year.' '.$month.' '.$day. ' '.$hour.' '.$minute.' '.$second;
        return ($res_seconds);
    }
    
    function minuteAgo($timestamp){
        $datetime1=new DateTime("now");
        $datetime2=date_create($timestamp);
        $diff=date_diff($datetime1, $datetime2);
        $timemsg='';
        
        if($diff->i > 0){
            
         $minutes = 30 - (int)$diff->i;
            
         $timemsg = $minutes .' minute'. ($minutes > 1?"'s":''). " to accept the order." ;
        }
        
    
        $timemsg = $timemsg;
        return $timemsg;
    }
    
    
    
    function countNotification($userId){
        
         global $obj ; global $database;
        //COUNT NOTIFICATIONS
    
        $sql = "SELECT * FROM notifications  WHERE iUserId =  '". $userId ."' AND eStatus = 'unread' ORDER BY dDateCreated DESC";

        $statement = $obj->query($sql);
            
        $notifData = $statement ->fetchAll(); 
        
        
        return count($notifData);
        
    };
    
    function countNotifications($userId, $userType){
        
         global $obj ; global $database;
        //COUNT NOTIFICATIONS
        
        if($userType == "User"){
            $sql = "SELECT * FROM notifications  WHERE iUserId =  '". $userId ."' AND vUsertype =  '". $userType ."' AND eStatus = 'unread' ORDER BY dDateCreated DESC";

            $statement = $obj->query($sql);
                
            $notifData = $statement ->fetchAll(); 
        }else{
            $sql = "SELECT * FROM notifications  WHERE iUserId =  '". $userId ."' AND vUsertype =  '". $userType ."' AND eStatus = 'unread' ORDER BY dDateCreated DESC";

            $statement = $obj->query($sql);
                
            $notifData = $statement ->fetchAll(); 
        }
    
        
        
        
        return count($notifData);
        
    };
    
        
    function createNotification($notification){
        
        global $obj ; global $database;
         
         
        $createNotif['iUserId'] = $notification['iUserId'];
        $createNotif['vUserType'] = $notification['vUserType'];
        $createNotif['vTitle'] = $notification['vTitle'];
        $createNotif['vDescription'] = $notification['vDescription'];
        $createNotif['vType'] = $notification['vType'];
        $createNotif['vImage'] = $notification['vImage'];
        $createNotif['vUrl'] = $notification['vUrl'];
        $createNotif['vIntent'] = $notification['vIntent'];
        $createNotif['vSent'] = $notification['vSent'];
        $createNotif['eStatus'] = "unread"; 
        $createNotif['dDateCreated'] = @date("Y-m-d H:i:s");
        $result = myQuery("notifications", $createNotif, "insert");
        
        //NOTIFCATION BACKGROUND
        $data['title'] = $notification['vTitle'];
        $data['description'] = $notification['vDescription'];
        //NOTIFCATION FOREGROUND
        $data['activity'] = $notification['vIntent'];
        $data['message'] = $notification['vDescription'];
    
        notify($notification['vUserType'] , $notification['iUserId'], $data);
       
        
    };
    
    function isweekend($date){
        $date = strtotime($date);
        $date = date("l", $date);
        $date = strtolower($date);
        // echo $date;
        if($date == "saturday" || $date == "sunday") {
            return "true";
        } else {
            return "false";
        }
    }
    
    function get_starred($str) {
        $len = strlen($str);

        return substr($str, 0, 3).str_repeat('*', $len - 2);
    }
    
    function isTodayWeekend() {
        $currentDate = new DateTime("now", new DateTimeZone("Europe/Amsterdam"));
        return $currentDate->format('N') >= 6;
    }
    
    
    function isUserHasExistingPabili($userId) {
       
         global $obj ; global $database;
        $sql = "SELECT orders.iUserId, DATE_FORMAT(orders.tOrderRequestDate, '%Y-%m-%d') FROM orders WHERE iUserId = '".$userId."' AND DATE(orders.tOrderRequestDate) = CURDATE() AND  ( iStatusCode <> '3009' OR  iStatusCode <> '3010') ";

        $statement = $obj->query($sql);
            
        $result = $statement ->fetchAll(); 
        
        return count($result);
         
    }
    

    function verifyTransaction($data) {
        $enableSandbox = true;
        $paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
    
        $req = 'cmd=_notify-validate';
        foreach ($data as $key => $value) {
            $value = urlencode(stripslashes($value));
            $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
            $req .= "&$key=$value";
        }
    
        $ch = curl_init($paypalUrl);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);
    
        if (!$res) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }
    
        $info = curl_getinfo($ch);
    
        // Check the http response
        $httpCode = $info['http_code'];
        if ($httpCode != 200) {
            throw new Exception("PayPal responded with http code $httpCode");
        }
    
        curl_close($ch);
    
        return $res === 'VERIFIED';
    }
    
    
    function sendPasakayInvoice($bookingId){
        global $obj ; global $database;
    
        
        $sql = "SELECT * FROM cab_booking WHERE iCabBookingId = '".$bookingId."'";
        $statement = $obj->query($sql);
        $bookingData = $statement ->fetchAll(); 
        $userId = $bookingData[0]['iUserId'];
            
        $waitingTime = $bookingData[0]['vWaitingTime'];
     
    
        if(count($bookingData)> 0){
            
            
            $WaitingTime_in_minutes = ((int) $waitingTime % 3600) / 60;
            
            $messageArray['WaitingTime_in_minutes'] =  intval($WaitingTime_in_minutes);
            
            $WaitingTime_in_seconds = ((int) $waitingTime % 60);
            
            $messageArray['WaitingTime_in_seconds'] = $WaitingTime_in_seconds;
            
            if($WaitingTime_in_seconds >= 1){
                
                $final_WaitingTime_in_minutes = intval($WaitingTime_in_minutes)+1;
            }else{
                $final_WaitingTime_in_minutes = $WaitingTime_in_minutes;
            }
            
            $totalWaitingFee =   $final_WaitingTime_in_minutes * constants::WAITINGTIME_RATE_PER_MIN;
            
            $totalFareAmount =  (float) $bookingData[0]['fTripGenerateFare'] +  $totalWaitingFee ;
            
            unset($where);
            $where['iCabBookingId'] = $bookingId;
            $booking_status['vWaitingTime'] = $waitingTime;
            $booking_status['fWaitingCharge'] = (float)$totalWaitingFee;
            $booking_status['fTripTotalAmountFare'] = (float)  roundOff($totalFareAmount);
            $bookingUpdate = myQuery("cab_booking", $booking_status, "update", $where);
            
            
            $sql = "SELECT * FROM cab_booking WHERE iCabBookingId = '".$bookingId."'";
            $statement = $obj->query($sql);
            $bookingData = $statement ->fetchAll(); 
            
            
            unset($where);
            $where['iUserId'] =  $userId;
            $userData = myQuery("register_user", array( "vName", "vLastName", "vImgName", "vLatitude", "vLongitude", "fWalletBalance", "vEmail"), "selectall",  $where);
            
            
            $sql = "SELECT vWalletBalance FROM register_driver WHERE iDriverId = '".$bookingData[0]['iDriverId']."'";
            $statement = $obj->query($sql);
            $driverData = $statement ->fetchAll(); 
        
            
            unset($where);
            $where['iDriverId'] =   $bookingData[0]['iDriverId'];
            $driverData = myQuery("register_driver", array( "vName", "vLastName", "vLatitude", "vLongitude", "vWalletBalance", "vImage", "fPocketMoney", "vTripStatus"), "selectall",  $where);
        
        
            $messageArray['driverId'] = $bookingData[0]['iDriverId'];
            $messageArray['driverName'] =  $driverData[0]['vName'];
            $messageArray['driverLastName'] =  $driverData[0]['vLastName'];
            $messageArray['driverImage'] =  $driverData[0]['vImage'];
            $messageArray['driverLat'] =  $driverData[0]['vLatitude'];
            $messageArray['driverLong'] =  $driverData[0]['vLongitude'];
            $messageArray['driverWalletBalance'] =  $driverData[0]['vWalletBalance'];
            $messageArray['driverPocketMoney'] =  $driverData[0]['fPocketMoney'];
            $messageArray['driverTripStatus'] =  $driverData[0]['vTripStatus'];
            
                
            $messageArray['userId'] =  $userId;
            $messageArray['userName'] =  $userData[0]['vName'];
            $messageArray['userLastName'] =  $userData[0]['vLastName'];
            $messageArray['userEmail'] =  $userData[0]['vEmail'];
            $messageArray['userImage'] =  $userData[0]['vImgName'];
            $messageArray['userLat'] =  $userData[0]['vLatitude'];
            $messageArray['userLong'] =  $userData[0]['vLongitude'];
            $messageArray['userWalletBalance'] =  $userData[0]['fWalletBalance'];
            $messageArray['userWalletBalance'] =  $userData[0]['fWalletBalance'];
            
            $messageArray['bookingWaitingTime'] = $bookingData[0]['vWaitingTime'];
            $messageArray['bookingId'] =  $bookingData[0]['iCabBookingId'];
            $messageArray['bookingStatus'] =  $bookingData[0]['eStatus'];
            $messageArray['bookingNo'] =  $bookingData[0]['vBookingNo'];
            $messageArray['bookingBaseFare'] =  $bookingData[0]['iBaseFare'];
            $messageArray['bookingDriverDistance'] =  $bookingData[0]['vDriverDistance'];
            $messageArray['bookingAdditionalFare'] =  $bookingData[0]['fAdditionalFare'];
            //$messageArray['bookingBaseFare'] =  $bookingData[0]['iBaseFare'];
            $messageArray['bookingTotalAmount'] = roundOff($bookingData[0]['fTripGenerateFare']);
            $messageArray['bookingTotalFareAmount'] = roundOff($bookingData[0]['fTripTotalAmountFare']);
            $messageArray['bookingEarnings'] = roundOff($bookingData[0]['fCommision']);
            $messageArray['bookingTrasactionFee'] = roundOff($bookingData[0]['fWalletDebit']);
            $messageArray['bookingPayment'] = $bookingData[0]['ePayType'];
            $date = date_create($bookingData[0]['dBooking_date']);
            $messageArray['bookingDate'] = date_format($date,"Y-m-d"). ", ".date_format($date,"H:i");
            $messageArray['bookingOrigin'] = $bookingData[0]['vSourceAddress'];
            $messageArray['bookingOriginLat'] = $bookingData[0]['vSourceLatitude'];
            $messageArray['bookingOriginLong'] = $bookingData[0]['vSourceLongitude'];
            $messageArray['bookingDestination'] = $bookingData[0]['tDestAddress'];
            $messageArray['bookingDestinationLat'] = $bookingData[0]['vDestLatitude'];
            $messageArray['bookingDestinationLong'] = $bookingData[0]['vDestLongitude'];
            $messageArray['bookingWaitingFeePerMinute'] = 2;
            $messageArray['bookingWaitingFee'] =  $totalWaitingFee;
           
           
        
            $messageArray['response'] = 1;
            $messageArray['service'] = $servicetype;
            $messageArray['status'] =  "OKAY";
            $messageArray['message'] =  "Successfull Updated!";
            
            if($WaitingTime_in_minutes > 1){
                
                if(intval($WaitingTime_in_minutes) == 1){
                    
                    if(intval(  $WaitingTime_in_seconds) == 0){
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min ";
                    }else if(intval(  $WaitingTime_in_seconds) == 1){
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min, ".intval(  $WaitingTime_in_seconds)." second";
                    }else{
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." min, ".intval(  $WaitingTime_in_seconds)." seconds";
                    }
                   
                }else{
                     if(intval(  $WaitingTime_in_seconds) == 0){
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins ";
                    }else if(intval(  $WaitingTime_in_seconds) == 1){
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins, ".intval(  $WaitingTime_in_seconds)." second";
                    }else{
                         $messageArray['waiting_time'] = intval($WaitingTime_in_minutes)." mins, ".intval(  $WaitingTime_in_seconds)." seconds";
                    }
                }
                
                // $messageArray['waiting_time'] = intval($WaitingTime_in_minutes) == 1 ? intval($WaitingTime_in_minutes)." min ".intval(  $WaitingTime_in_seconds)." seconds" : intval($WaitingTime_in_minutes)." mins ".intval(  $WaitingTime_in_seconds)." seconds";
                 
            }else{
                
            
                 $messageArray['waiting_time'] = intval(  $WaitingTime_in_seconds)." second";
            }
            

         
            $messageArray['waiting_time_charge'] = roundOff(   $totalWaitingFee ) ;
            $messageArray['Total_FARE'] = roundOff($totalFareAmount);
            
            if($totalWaitingFee != 0.0 || $totalWaitingFee != 0 || $totalWaitingFee != "0.0"){
                
                $waitingBody = ' <tr>
                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:11px;color:#000000;line-height:18px;">Waiting Fare</span> </td>
                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:11px;color:#000000;line-height:18px;">&nbsp;&nbsp;<span>&#8369;</span>'.number_format((float)$messageArray["bookingWaitingFee"], 2, '.', '').'</span> </td>
                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                 </tr>
                       
                 <tr>
                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                    <td height="10px" colspan="2" align="left" class="yiv6299921691tdp5"></td>
                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                 </tr>
                 <tr>
                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                    <td height="10px" colspan="2" align="left" class="yiv6299921691tdp5" style="border-top:1px dashed #4b4c2c;"></td>
                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                 </tr>';
            }else{
                $waitingBody = "";
            }
            
            
        }
        
        $invoice = '
        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
         <tbody><tr>
            <td align="center" valign="top">
               <table width="600" align="center" border="0" cellspacing="0" cellpadding="0" class="yiv6299921691borderPerTab" style="border:1px solid #EDEDED;">
                  <tbody><tr>
                     <td bgcolor="#ffffff">
                         
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                           <tbody><tr>
                              <td> <img class="yiv6299921691produceImg" src="http://mallody.ph/uploads/iStock-527674858-1000x430.png" width="600" height="258" alt="E-receipt Trikaroo Ph" style="border:0;"> </td>
                           </tr>
                        </tbody></table>
                        
                        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
                           <tbody><tr>
                              <td valign="top" class="yiv6299921691vspacer15" width="45"></td>
                              <td valign="top" style="font-family: Arial, sans-serif;color:#000000;font-size:11px;">
                                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tbody><tr>
                                       <td height="15"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="15" border="0"></td>
                                    </tr>
                                    <tr>
                                       <td align="left" style="font-family: Arial, sans-serif;font-size:20px;line-height:24px;font-weight:bold;line-height:26px;text-align:left;color:#797641;">We Hope you had an enjoyable tricycle ride! </td>
                                    </tr>
                                    <tr>
                                       <td height="15"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="15" border="0"></td>
                                    </tr>
                                    <tr>
                                       <td>
                                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                             <tbody><tr>
                                                <td width="44%" align="left" valign="top" class="yiv6299921691produceTdLast" style="font-size:12px;line-height:21px;font-weight:bold;">TOTAL<br> <span style="font-size:28px;line-height:32px;font-weight:bold;color:#797641;"><span>&#8369;</span>'.number_format((float)$messageArray['bookingTotalFareAmount'], 2, '.', '').'</span></span></td>
                                                <td width="56%" align="left" valign="top" class="yiv6299921691produceTdLast" style="font-size:12px;line-height:21px;font-weight:bold;">DATE&nbsp;&nbsp;|&nbsp; TIME<br> Pick-up time: <span style="font-size:12px;font-weight:bold;color:#797641;">'.$messageArray['bookingDate'].'</span></td>
                                          </tr></tbody></table>
                                          <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                             <tbody><tr>
                                                <td height="12"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="12" border="0"></td>
                                             </tr>
                                          </tbody></table>
                                       </td>
                                    </tr>
                                 </tbody></table>
                              </td>
                              <td valign="top" class="yiv6299921691vspacer15" width="45"></td>
                           </tr>
                        </tbody></table>
                        
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                           <tbody><tr>
                              <td height="5"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="10" height="5" border="0"></td>
                           </tr>
                        </tbody></table>
                        
                        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#f8f6ee">
                           <tbody><tr>
                              <td valign="top" class="yiv6299921691vspacer15" width="45"> <img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="10" border="0"> </td>
                              <td align="center" valign="top">
                                 <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tbody><tr>
                                       <td align="left" height="20"></td>
                                    </tr>
                                 </tbody></table>
                                 <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tbody><tr>
                                       <td width="55%" align="left" style="font-size:14px;font-weight:bold;color:#797641;"></td>
                                    </tr>
                                 </tbody></table>
                                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tbody><tr>
                                       <td valign="top" width="207" style="max-width:207px;display:block;" class="yiv6299921691produceTd">
                                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                             <tbody><tr>
                                                <td align="left" valign="top" class="yiv6299921691tdp5" style="font-family: Arial, sans-serif;font-size:14px;font-weight:bold;color:#797641;">Booking Details</td>
                                             </tr>
                                             <tr>
                                                <td align="center" valign="middle" height="10" class="yiv6299921691img_1"> <img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="170" height="10" border="0"> </td>
                                             </tr>
                                             <tr>
                                                <td valign="top">
                                                   <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                      <tbody><tr>
                                                         <td align="left" valign="top" style="padding:0cm 0cm 0cm 0cm;">
                                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                               <tbody><tr>
                                                                  <td class="yiv6299921691t3_1" valign="top">
                                                                     <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tbody>
                                                                        <tr>
                                                                           <td height="3"> <img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="3" border="0"> </td>
                                                                        </tr>
                                                                        
                                                                        <tr>
                                                                           <td align="left" valign="top" class="yiv6299921691tdp5">
                                                                              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                 <tbody><tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-size:10px;color:#4b4c2c;line-height:14px;">Issued by</span><br> <span style="font-family:Arial, sans-serif;font-size:12px;line-height:16px;font-weight:bold;">'.$messageArray['driverName'].'</span> </td>
                                                                                 </tr>
                                                                              </tbody></table>
                                                                           </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td height="3"> <img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="3" border="0"> </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td align="left" valign="top" class="yiv6299921691tdp5">
                                                                              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                 <tbody><tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:Arial, sans-serif;font-size:10px;color:#4b4c2c;line-height:16px;">Issued to</span><br> <span style="font-family:  Arial, sans-serif;font-size:12px;line-height:16px;font-weight:bold;">'.$messageArray['userName'].'</span> </td>
                                                                                 </tr>
                                                                              </tbody></table>
                                                                           </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td height="3"> <img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="5" border="0"> </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td align="left" valign="top" class="yiv6299921691tdp5">
                                                                              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                 <tbody><tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:Arial, sans-serif;font-size:10px;color:#4b4c2c;line-height:16px;">Booking code</span><br> <span style="font-family:Arial, sans-serif;font-size:12px;line-height:16px;font-weight:bold;">'.$messageArray["bookingNo"].'</span> </td>
                                                                                 </tr>
                                                                              </tbody></table>
                                                                           </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td height="3"> <img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="5" border="0"> </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td align="left" valign="top" class="yiv6299921691tdp5">
                                                                              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                 <tbody><tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:10px;color:#4b4c2c;line-height:16px;">Pick up location:</span><br> <span style="font-family: Arial, sans-serif;font-size:12px;line-height:16px;font-weight:bold;">'.$messageArray["bookingOrigin"].'</span> </td>
                                                                                 </tr>
                                                                              </tbody></table>
                                                                           </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td height="3"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="5" border="0"></td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td align="left" valign="top" class="yiv6299921691tdp5">
                                                                              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                 <tbody><tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:10px;color:#4b4c2c;line-height:16px;">Drop off location:</span><br> <span style="font-family:  Arial, sans-serif;font-size:12px;line-height:16px;font-weight:bold;">'.$messageArray["bookingDestination"].'</span> </td>
                                                                                 </tr>
                                                                              </tbody></table>
                                                                           </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td height="3"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="5" border="0"></td>
                                                                        </tr>
                                                                        
                                                                        <tr>
                                                                           <td align="left" valign="top" class="yiv6299921691tdp5">
                                                                              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                 <tbody><tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:10px;color:#4b4c2c;line-height:16px;">Profile:</span><br> <span style="font-family:  Arial, sans-serif;font-size:12px;line-height:16px;font-weight:bold;">Personal</span> </td>
                                                                                 </tr>
                                                                              </tbody></table>
                                                                           </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td height="3"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="3" border="0"></td>
                                                                        </tr>
                                                                          
                                                                     </tbody></table>
                                                                  </td>
                                                               </tr>
                                                            </tbody></table>
                                                         </td>
                                                      </tr>
                                                   </tbody></table>
                                                </td>
                                             </tr>
                                          </tbody></table>
                                       </td>
                                       <td valign="top" class="yiv6299921691noneMobile" width="9"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="9" height="10" border="0"></td>
                                       <td valign="top" class="yiv6299921691noneMobile" width="10" bgcolor="#f8f6ee"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="10" height="10" border="0"></td>
                                       <td valign="top" width="280" style="max-width:280px;" class="yiv6299921691produceTd">
                                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                             <tbody><tr>
                                                <td align="left" valign="top" class="yiv6299921691tdp5" style="font-family:  Arial, sans-serif;font-size:14px;font-weight:bold;color:#797641;">Receipt Summary</td>
                                             </tr>
                                             <tr>
                                                <td align="center" valign="middle" height="10" class="yiv6299921691img_1"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="170" height="10" border="0"></td>
                                             </tr>
                                             <tr>
                                                <td valign="top">
                                                   <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border:1px solid #dddddd;">
                                                      <tbody><tr>
                                                         <td align="left" valign="top" style="padding:0cm 0cm 0cm 0cm;">
                                                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                               <tbody><tr>
                                                                  <td class="yiv6299921691t3_1" valign="top">
                                                                     <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                        <tbody><tr>
                                                                           <td align="left" valign="top" class="yiv6299921691tdp5">
                                                                              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                 <tbody><tr>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="10px" colspan="2" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td height="5px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="5px" colspan="2" align="left" class="yiv6299921691tdp5" style="font-family:  Arial, sans-serif;font-size:11px;line-height:18px;"> Payment Method:<br>
                                                                                       
                                                                                          <span style="font-family:  Arial, sans-serif;font-weight:bold;color:#000000;">'.$messageArray['bookingPayment'].'&nbsp;&nbsp;</span>
                                                                                       
                                                                                    </td>
                                                                                    <td height="5px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td height="5px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="5px" colspan="2" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="5px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td height="3px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="3px" colspan="2" align="left" class="yiv6299921691tdp5" style="border-top:1px dashed #4b4c2c;"></td>
                                                                                    <td height="3px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                                                                                    <td width="171" align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:11px;color:#4b4c2c;line-height:21px;">Description:</span> </td>
                                                                                    <td width="80" align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:11px;color:#4b4c2c;line-height:28px;">&nbsp;&nbsp;Amount:</span> </td>
                                                                                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td height="3px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="3px" colspan="2" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="3px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td height="5px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="5px" colspan="2" align="left" class="yiv6299921691tdp5" style="border-top:1px dashed #4b4c2c;"></td>
                                                                                    <td height="5px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:11px;color:#000000;line-height:18px;">Base Fare</span> </td>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:11px;color:#000000;line-height:18px;">&nbsp;&nbsp;<span>&#8369;</span>'.number_format((float)$messageArray["bookingBaseFare"], 2, '.', '').'</span> </td>
                                                                                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                                                                                 </tr>
                                                                                       
                                                                                 <tr>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="10px" colspan="2" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="10px" colspan="2" align="left" class="yiv6299921691tdp5" style="border-top:1px dashed #4b4c2c;"></td>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 
                                                                                 <tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:11px;color:#000000;line-height:18px;">Additional Fare</span> </td>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family:  Arial, sans-serif;font-size:11px;color:#000000;line-height:18px;">&nbsp;&nbsp;<span>&#8369;</span>'.number_format((float)$messageArray["bookingAdditionalFare"], 2, '.', '').'</span> </td>
                                                                                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                                                                                 </tr>
                                                                                       
                                                                                 <tr>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="10px" colspan="2" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 <tr>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                    <td height="10px" colspan="2" align="left" class="yiv6299921691tdp5" style="border-top:1px dashed #4b4c2c;"></td>
                                                                                    <td height="10px" align="left" class="yiv6299921691tdp5"></td>
                                                                                 </tr>
                                                                                 
                                                                                 '.$waitingBody.'
                                                                                
                                                                                 
                                                                                 <tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                                                                                    <td align="right" class="yiv6299921691tdp5" style=""> <span style="font-family: Arial, sans-serif;font-size:12px;font-weight:bolder;color:#000000;line-height:28px;">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</span> </td>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""> <span style="font-family: Arial, sans-serif;font-size:12px;font-weight:bolder;color:#000000;line-height:28px;">&nbsp;&nbsp;<span>&#8369;</span>'.number_format((float)$messageArray["bookingTotalFareAmount"], 2, '.', '').'</span> </td>
                                                                                    <td align="left" class="yiv6299921691tdp5" width="15"></td>
                                                                                 </tr>
                                                                              </tbody></table>
                                                                           </td>
                                                                        </tr>
                                                                        <tr>
                                                                           <td align="left" valign="top" class="yiv6299921691tdp5">
                                                                              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                                                                 <tbody><tr>
                                                                                    <td align="left" class="yiv6299921691tdp5" style=""><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="10" border="0"></td>
                                                                                 </tr>
                                                                              </tbody></table>
                                                                           </td>
                                                                        </tr>
                                                                     </tbody></table>
                                                                  </td>
                                                               </tr>
                                                            </tbody></table>
                                                         </td>
                                                      </tr>
                                                   </tbody></table>
                                                </td>
                                             </tr>
                                          </tbody></table>
                                       </td>
                                    </tr>
                                 </tbody></table>
                                 <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tbody>
                                       <tr>
                                          <td height="20"></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                              <td valign="top" class="yiv6299921691vspacer15" width="45"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="20" height="10" border="0"></td>
                           </tr>
                        </tbody></table>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                           <tbody><tr>
                              <td height="20"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="10" height="30" border="0"></td>
                           </tr>
                        </tbody></table>
                        
                        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="border-bottom:1px solid #CBCBCB;">
                           <tbody><tr>
                              <td valign="top" class="yiv6299921691vspacer15" width="45"></td>
                              <td valign="top" style="font-family:Helvetica, Arial, sans-serif;color:#000000;font-size:11px;">
                                 
                              </td>
                              <td valign="top" class="yiv6299921691vspacer15" width="45"></td>
                           </tr>
                        </tbody></table>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#9dae11">
                           <tbody><tr>
                              <td height="20"><img style="display:block;" src="https://ecp.yusercontent.com/mail?url=https%3A%2F%2Fgrabtaxi-marketing.s3.amazonaws.com%2Femail%2Fimg%2F_blank.gif&amp;t=1605584962&amp;ymreqid=ef41f392-816e-6817-1c96-98000d01dd00&amp;sig=NVDKI0o1V_YXKigUwboEug--~D" alt="" width="10" height="20" border="0"></td>
                           </tr>
                        </tbody></table>
                        
                        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" bgcolor="#9dae11">
                           <tbody><tr>
                              <td valign="top" class="yiv6299921691vspacer15" width="45"></td>
                              <td valign="top" style="font-family:Helvetica, Arial, sans-serif;color:#000000;font-size:11px;">
                                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tbody><tr>
                                       <td align="left" style="font-family: Arial, sans-serif;font-family:Helvetica, Arial, sans-serif;text-align:center;color:#ffffff;"></td>
                                    </tr>
                                    <tr>
                                       <td align="left" style="font-family:Helvetica, Arial, sans-serif;text-align:center;">
                                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                             <tbody><tr>
                                                <td width="55%" align="left" valign="top" class="yiv6299921691produceTdLast" style="font-family:Helvetica, Arial, sans-serif;color:#666666;padding-right:10%;">
                                                   <span style="color:#ffffff;"> <a rel="nofollow" target="_blank" href="http://trikaroo.com.ph/user_faq.php" style="font-size:10px;line-height:12px;font-weight:bold;color:#ffffff;">Help Center </a> </span><br> <span style="color:#666666;"> <a rel="nofollow" target="_blank" href="http://trikaroo.com.ph/user_faq.php" style="font-size:10px;line-height:12px;font-weight:bold;color:#ffffff;">FAQ</a> </span><br><br>
                                                   <span style="font-family:Arial, sans-serif;font-size:10px;font-weight:normal;line-height:16px;color:#ffffff;">
                                                      Copyright © 2020 <br> Heng Yen E-Commerce Inc.<br>
                                                        1602 Atlanta Center, 31 Annapolis Street,<br>
                                                        Greenhills, San Juan City<a rel="nofollow" style="color:#666666;text-decoration:underline;font-style:italic;line-height:21px;"></a>
                                                   </span>
                                                </td>
                                                <td width="45%" align="left" valign="top" class="yiv6299921691produceTdLast" style="font-family:Arial, sans-serif;font-family:Helvetica, Arial, sans-serif;color:#666666;"> <span style="font-family: Arial, sans-serif;font-size:10px;line-height:12px;font-weight:bold;">Stay connected with us.</span><br><br> <a rel="nofollow" target="_blank" href="https://www.facebook.com/TrikarooPHILIPPINES"><img width="30px" src="https://cdn.fastly.picmonkey.com/content4/previews/icons_facebook/icons_facebook_04_384.png"></a>&nbsp;&nbsp; <a rel="nofollow" target="_blank" href="https://twitter.com/TrikarooPH"><img width="30px" src="https://cdn.iconscout.com/icon/free/png-512/twitter-1865886-1581902.png"></a>&nbsp;&nbsp; <a rel="nofollow" target="_blank" href="https://www.instagram.com/trikaroo_ph/"><img width="30px" src="https://cdn4.iconfinder.com/data/icons/social-messaging-ui-color-shapes-2-free/128/social-instagram-new-circle-512.png"></a>&nbsp;&nbsp; <br><br> </td>
                                             </tr>
                                          </tbody></table>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td height="20"></td>
                                    </tr>
                                 </tbody></table>
                              </td>
                              <td valign="top" class="yiv6299921691vspacer15" width="45"></td>
                           </tr>
                        </tbody></table>
                     </td>
                  </tr>
               </tbody></table>
            </td>
         </tr>
      </tbody></table>';
    
    echo $userData[0]['vEmail'];
     
      try{
            
             $mail = new PHPMailer;
             $mail->isSMTP();
            
             $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            
            
             $mail->Host = 'mail.mallody.com.ph';
             $mail->Port = 587;
            //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
             $mail->SMTPAuth = true;
            
             $mail->Username = 'invoice@mallody.com.ph';
             $mail->Password = 'hengyen66888';
            
             $mail->setFrom( 'invoice@trikaroo.com.ph', 'Trikaroo Invoice' );
             $mail->addAddress( $userData[0]['vEmail'] ,$userData[0]['vName']);
            //$mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
             $mail->Subject = 'Trikaroo Invoice';
    
             $mail->isHTML(true);  
             $mail->Body    = $invoice;
             $mail->AltBody = '';
            //$mail->addAttachment($applicantId);
    
            //if( $none_co_borrower != "true"){
            //    $mail->addAttachment($co_applicantId);
            //}
            //$account_sid = "AC6fb45a05498750a9ffd49e84154e16f6";
            //$auth_token = "3f37e731f441f5dc82ed1c8abf8aa118";
            //$twilioMobileNum = "+19389991544";
            //$toMobileNum = "+639398296855";
            //$message= "NEW AUTO LOAN APPLICATION!. Check your email.";
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');
            
             if (!$mail->send()) {
                 echo 'Mailer Error: '. $mail->ErrorInfo;
             } else {
                 echo $firstname;
                // header("Location: ../success.php?first-name=".$firstname);
             }
    
        
        }catch(Exception $e){
          echo 'Mailer Error';
        }    
        
        
    }
    
    
    function isNightTime(){
 
       if(date("H") > 6 && date("H") < 12){
     
         return false;
     
       }elseif(date("H") > 11 && date("H") < 19){
     
         return false;
     
       }elseif(date("H") > 17 || date("H") < 7 ){
     
         return true;
     
       }
     
    } 
    
     function isSurge(){
 
       if(constant::RIDEPASAKAY_SURGE == "Enable"){
     
            return true;
     
       }else{
            return false;
       }
     
    } 
    
    
    function cancelledLogs($data){
        
        // $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '';
        // $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUESTT['sourceLong']) :'';
        // $userType = isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'User';
        // $transactionType = isset($_REQUEST['transactionType']) ? trim($_REQUEST['transactionType']) :'PABILI';
        // $iUserId = isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'24';
        // $transactionNo = isset($_REQUEST['transactionNo']) ? trim($_REQUEST['transactionNo']) :'PB1234723321';
        // $cancelReason = isset($_REQUEST['reason']) ? trim($_REQUEST['reason']) :'ayoko na';
        // $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) :'lau@gmail.com';
        // $description = isset($_REQUEST['description']) ? trim($_REQUEST['description']) :'hays ';
        // $status = isset($_REQUEST['status']) ? trim($_REQUEST['status']) :'At the store';
        // $image =  isset($_REQUEST['image']) ? trim($_REQUEST['image']) :'24232323232323';
        
        // $cancelled_report['vUserType'] =  $userType;
        // $cancelled_report['vTransactionType'] = $transactionType;
        // $cancelled_report['iUserId'] = (int)$iUserId;
        // $cancelled_report['vCancelledReason'] = $transactionNo;
        // $cancelled_report['vTransactionNo'] = $cancelReason;
        // $cancelled_report['vEmail'] = $email;
        // $cancelled_report['vDescription'] =  $description;
        // $cancelled_report['vStatus'] =   $status ;
        // $cancelled_report['vImage'] =  $image;
        // $cancelled_report['dDate'] = @date("Y-m-d H:i:s");
        
        
        $cancelled_report['vUserType'] = $data['vUserType'];
        $cancelled_report['vTransactionType'] = $data['vTransactionType'];
        $cancelled_report['iUserId'] = (int) $data['iUserId'];
        $cancelled_report['vCancelledReason'] = $data['vCancelledReason'];
        $cancelled_report['vTransactionNo'] = $data['vTransactionNo'];
        $cancelled_report['vEmail'] = $data['vEmail'];
        $cancelled_report['vDescription'] = $data['vDescription'];
        $cancelled_report['vStatus'] =  $data['vStatus'];
        $cancelled_report['vImage'] =  $data['vImage'];
        $cancelled_report['dDate'] = @date("Y-m-d H:i:s");
        
        // echo json_encode($cancelled_report);
        
        $result = myQuery("cancelled_transactions", $cancelled_report, "insert");
        
        
        
    }
    
    
    function automation(){
        
        global $obj;
        
         //
        $str_date = @date('Y-m-d H:i:s');
        $sql = "SELECT *, iCabBookingId, vBookingNo, eStatus, dBooking_date, TIMESTAMPDIFF(HOUR, dBooking_date, '".$str_date."') AS hours FROM cab_booking WHERE eStatus != 'Finished' AND eStatus != 'Cancelled' AND eStatus !=  'Booking Expired' ORDER BY hours ASC";
               
        $statement = $obj->query($sql); 
        $bookingData = $statement ->fetchAll();
        
       // echo json_encode($bookingData);
    
        for($x = 0 ; $x < count($bookingData); $x++){
            
            if((int)$bookingData[$x]['hours'] >= 2){
                
                $driverId = $bookingData[$x]['iDriverId'];
                $bookingId = $bookingData[$x]['iCabBookingId'];
                $userId = $bookingData[$x]['iUserId'];
                
                if($bookingData[$x]['eStatus'] == "Searching for drivers"){
                    
                    // echo "Booking Id : ".$bookingData[$x]['iCabBookingId']." | BookingNo : ".$bookingData[$x]['vBookingNo']." | Booking Date : ".$bookingData[$x]['dBooking_date']." | Status : Booking Expired"  ;
                    // echo "</br>";
                    
                    
                    unset($where);
                    $where['iCabBookingId'] = $bookingId;
                    $searchStatus['eStatus'] = "Booking Expired";
                    $searchStatusResult = myQuery("cab_booking",  $searchStatus, "update", $where);
                    
                    // echo json_encode($where);
                    // echo json_encode($searchStatus);
        
                }else{
                    
                    // echo "Booking Id : ".$bookingData[$x]['iCabBookingId']." | BookingNo : ".$bookingData[$x]['vBookingNo']." | Booking Date : ".$bookingData[$x]['dBooking_date']." | Status : Finsihed"  ;
                    // echo "</br>".$driverId;
                    // echo "</br>";
                     
                    unset($where);
                    $where['iDriverId'] = $driverId ;
                    $tripData = myQuery("register_driver", array("iTripId","vWalletBalance"), "selectall",  $where);
                    
                    //CHECK IF TRANSACTION ALREADY EXIST!!
                    unset($where);
                    $where['vTransactionNo'] = $bookingData[$x]['vBookingNo'];
                    $isExist = myQuery("user_wallet_logs", array("vTransactionNo"), "selectall",  $where);
                    
                    if(count($isExist) <= 0){
                        
                        
                        $Trikaroo_transactionFee = (float) $bookingData[$x]['fTripTotalAmountFare'] * (float) $bookingData[$x]['fCompanyPercentage'];
                        $Trikaroo_driver_earnings = (float)$bookingData[$x]['fTripTotalAmountFare'] - $Trikaroo_transactionFee;
                        
                        unset($where);
                        $where['iCabBookingId'] = $bookingId;
                        $fare_status['iBaseFare'] =  (float)$bookingData[$x]['iBaseFare'];
                        $fare_status['fPricePerMin'] = (float)$bookingData[$x]['fPricePerMin'];
                        $fare_status['fPricePerKM'] = (float)$bookingData[$x]['fPricePerKM'];
                        $fare_status['fCommision'] = (float) $Trikaroo_driver_earnings;
                        $fare_status['fWalletDebit'] = (float) $Trikaroo_transactionFee ;
                        $fare_status['tTripEnded'] = @date("Y-m-d H:i:s");
                        $fare_status['eStatus'] = "Finished";
                        $fareResult = myQuery("cab_booking",  $fare_status, "update", $where);
                        
                        $driverWallet = (float)$tripData[0]['vWalletBalance'] - (float)$Trikaroo_transactionFee ;
                        
                        unset($where);
                        $where['iDriverId'] = $driverId;
                        $driver_status['vTripStatus'] = trim("ARRIVED");
                        $driver_status['vWalletBalance'] = $driverWallet;
                        $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                             
                        $walletlogs['iDriverId'] =  $driverId;
                        $walletlogs['vUserType'] =  "Driver";
                        $walletlogs['vTransactionType'] = "PASAKAY";
                        $walletlogs['vLabel'] = "- Debited";
                        $walletlogs['vDescription'] = "";
                        $walletlogs['vTransactionNo'] =  $bookingData[$x]['vBookingNo'];
                        $walletlogs['fAmount'] = (float)$Trikaroo_transactionFee ;
                        $walletlogs['fWalletBalance'] = (float) $driverWallet;
                        $walletlogs['vReceiveBy'] = "";
                        $walletlogs['iReceiveId'] = "";
                        $walletlogs['eStatus'] = "Completed";
                        $walletlogs['dDate'] = @date("Y-m-d H:i:s");
                              
                        $result = myQuery("user_wallet_logs",  $walletlogs, "insert");
                        
                          //USER DATA
                        unset($where);
                        $where['iUserId'] = $userId;
                        $userData = myQuery("register_user", array("vName", "vLastName", "fRewardPointsBalance"), "selectall",  $where);
                        
                        $earnedPoints = (float)$Trikaroo_transactionFee * constants::REWARDS_POINTS_RATE;
                        $totalRewardPointsBalance = (float)$userData[0]['fRewardPointsBalance']+$earnedPoints;
                        
                        unset($where);
                        $where['iUserId'] = $userId;
                        $userReward_status['fRewardPointsBalance'] =  $totalRewardPointsBalance ;
                        $result = myQuery("register_user", $userReward_status, "update", $where);
                        
                        $transactionNo = GenerateUniqueOrderNo("RP");
            
                        $rewardslogs['iUserId'] = $userId ;
                        $rewardslogs['vUserType'] = "User";
                        $rewardslogs['vTransactionType'] = "PASAKAY";
                        $rewardslogs['vLabel'] = "Earned points";
                        $rewardslogs['vDescription'] = "";
                        $rewardslogs['vTransactionNo'] = $bookingData[$X]['vBookingNo'];
                        $rewardslogs['fPoints'] = (float)  $earnedPoints ;
                        $rewardslogs['fTotalPointsAmount'] = (float)    $totalRewardPointsBalance;
                        $rewardslogs['eStatus'] = "Earned";
                        $rewardslogs['dDateCreated'] = @date("Y-m-d H:i:s");
                              
                        $result = myQuery("rewards_user_logs", $rewardslogs, "insert");
                    }
                    
                    
                    unset($where);
                    $where['iDriverId'] = $driverId;
                    $driver_status['vTripStatus'] = trim("FINISHED");
                    $driver_status['iTripId'] = 0;
                    $driverResult = myQuery("register_driver", $driver_status, "update", $where);
                    
                    unset($where);
                    $where['iUserId'] = $userId;
                    $user_status['vTripStatus'] = "NONE";
                    $user_status['iTripId'] = 0;
                    $result3 = myQuery("register_user", $user_status, "update", $where);
                    
                    unset($where);
                    $where['iTripId'] =  $tripData[0]['iTripId'];
                    $updatePreviousTrip['iActive'] = "Finished" ;
                    $updatePreviousTripResult = myQuery("trips",  $updatePreviousTrip, "update",  $where);
                }
            }
            
        }
    
    }

    function sentMessageToAllDriver( $SMSmessage){

        global $obj;
        // $SMSmessage = "Hi Trikaroo Tropa!, salamat sa iyong pag-register bilang isang Trikaroo Driver, idownload ang app sa link na http://trikaroo.com.ph/driverzone_login.php at gamitin ang password na 40513.";
        // $mobileNumber ="09398296855";


        // $number = $mobileNumber;
        // $message = $SMSmessage;
        // $apicode = "DE-HENGY005538_VF6II";
        // $passwd = "{(k%gygg#{";
        // $result = itexmo($number,$message,$apicode,$passwd);


        $sql = "SELECT vName, vPhone FROM register_driver";
        $statement = $obj->query($sql); 
        $registerDriver = $statement ->fetchAll(); 

        for($x=0; $x<count( $registerDriver ) ; $x++){
            echo "</br>";
            echo "Driver Name : ".$registerDriver[$x]['vName']."</br>";
            echo "Driver Number : ".$registerDriver[$x]['vPhone']."</br>";
            echo "Message : ".$SMSmessage."</br>";
            echo "Status : Message Sent!</br>";

            $number = $registerDriver[$x]['vPhone'];
            $message = $SMSmessage;
            $apicode = "DE-HENGY005538_VF6II";
            $passwd = "{(k%gygg#{";
            $result = itexmo($number,$message,$apicode,$passwd);
       
        }

   
    }


    function paging_from_multi_arr($page, $A, $show_per_page){
        
        $start = ($page-1) * $show_per_page;
        $end   = $page * $show_per_page;
    
        // $flat_array = array();
        
        // foreach ($A as $sub_array) {
        //     $flat_array = array_merge($flat_array, $sub_array);
        // }
    
        $slice = array_slice($A, $start, $show_per_page);
    
        return $slice;
         
    }
    


     function sentMessageToSingleDriver( $SMSmessage,  $mobileNumber){

        global $obj;
        // $SMSmessage = "Hi Trikaroo Tropa!, salamat sa iyong pag-register bilang isang Trikaroo Driver, idownload ang app sa link na http://trikaroo.com.ph/driverzone_login.php at gamitin ang password na 40513.";
        // $mobileNumber ="09398296855";

        echo "Driver Number : ".$mobileNumber."</br>";
        echo "Message : ".$SMSmessage."</br>";
        echo "Status : Message Sent!</br>";
        echo "Charactes : ".strlen($SMSmessage);

        $number = $mobileNumber;
        $message = $SMSmessage;
        $apicode = "DE-HENGY005538_VF6II";
        $passwd = "{(k%gygg#{";
        $result = itexmo($number,$message,$apicode,$passwd);


        // $sql = "SELECT vName, vPhone FROM register_driver";
        // $statement = $obj->query($sql); 
        // $registerDriver = $statement ->fetchAll(); 

        // for($x=0; $x<count( $registerDriver ) ; $x++){
        //     echo "</br>";
        //     echo "Driver Name : ".$registerDriver[$x]['vName']."</br>";
        //     echo "Driver Number : ".$registerDriver[$x]['vPhone']."</br>";
        //     echo "Message : ".$SMSmessage."</br>";
        //     echo "Status : Message Sent!</br>";

        //     $number = $registerDriver[$x]['vPhone'];
        //     $message = $SMSmessage;
        //     $apicode = "DE-HENGY005538_VF6II";
        //     $passwd = "{(k%gygg#{";
        //     $result = itexmo($number,$message,$apicode,$passwd);
       
        // }

   
    }


    
    
    // function  getAllOnlineDrivers($sourceLocationArr){
        
    //     $str_date = @date('Y-m-d H:i:s', strtotime('-410 minutes'));
    //     $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' AND vAvailability = 'Available' AND eStatus = 'active' AND (vTripStatus != 'FINISHED' OR vTripStatus != 'NONE') AND tLocationUpdateDate > '".$str_date."' having distance <= 5 order by distance";
    //     $statement = $obj->query($sql);
    //     $allDriverData = $statement ->fetchAll();
        
    //     return $allDriverData;
    // }
    
    // function  getAllOnlineAvailableDrivers($sourceLocationArr){
        
    //     $str_date = @date('Y-m-d H:i:s', strtotime('-410 minutes'));
    //     //SELECT ALL DRIVERS AVAILABLE
    //     $sql = "SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( ".$sourceLocationArr[0]." - vLatitude) * pi()/180 / 2), 2) +COS( ".$sourceLocationArr[0]." * pi()/180) * COS(vLatitude * pi()/180) * POWER(SIN(( ". $sourceLocationArr[1]." - vLongitude) * pi()/180 / 2), 2) ))) as distance, vFirebaseDeviceToken, vLatitude, vLongitude, iDriverId, vName, vLastName from register_driver where iTodaId = '".$todaId."' AND vAvailability = 'Available' AND eStatus = 'active' AND (vTripStatus = 'FINISHED' OR vTripStatus = 'NONE') AND tLocationUpdateDate > '".$str_date."' having distance <= 5 order by distance";
    //     $statement = $obj->query($sql);
    //     $availableDriverData = $statement ->fetchAll();
        
    //     return $availableDriverData;
    // }

    

    //RENCEVETERANS 12-06-2021 

   function safe_json_encode($value){

      if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
          $encoded = json_encode($value, JSON_PRETTY_PRINT);
      } else {
          $encoded = json_encode($value);
      }
      switch (json_last_error()) {
          case JSON_ERROR_NONE:
              return $encoded;
          case JSON_ERROR_DEPTH:
              return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
          case JSON_ERROR_STATE_MISMATCH:
              return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
          case JSON_ERROR_CTRL_CHAR:
              return 'Unexpected control character found';
          case JSON_ERROR_SYNTAX:
              return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
          case JSON_ERROR_UTF8:
              $clean = utf8ize($value);
              return safe_json_encode($clean);
          default:
              return 'Unknown error'; // or trigger_error() or throw new 
      Exception();
      }
    }


    function utf8ize($mixed) {
      if (is_array($mixed)) {
          foreach ($mixed as $key => $value) {
              $mixed[$key] = utf8ize($value);
          }
      } else if (is_string ($mixed)) {
          return utf8_encode($mixed);
      }
      return $mixed;
    }



?>