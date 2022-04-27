<?php
ini_set('allow_url_fopen',1);
ini_set('display_errors',1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);RATE
date_default_timezone_set("Asia/Manila");

session_start();

$sessionId = session_id();

    //TRIKAROO WEBSERVICE
    //renceveterans.dev 12.10.20
     
    // use PHPMailer\PHPMailer\PHPMailer;
    // use PHPMailer\PHPMailer\SMTP;
    //  require 'vendor/autoload.php';
    //  require_once 'vendor/twilio/Services/Twilio.php';
    //  include_once('trikaroo_config.php');
    //  include_once('trikaroo_general_functions.php');
  

    // $database = new Connection();
    
    // $db = $database->openConnection();
    $servicetype  = isset($_REQUEST['ServiceType']) ? trim($_REQUEST['ServiceType']) : '';
    $messageArray = array();
    
    $messageArray['response'] = 0;

    // $servicetype = "GET_ROUTES";
    
    if($servicetype == "GET_ROUTES"){
        
        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '14.6560953';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUEST['sourceLong']) :'120.9969619';
        $destLat  = isset($_REQUEST['destLong']) ? trim($_REQUEST['destLong']) :'14.60477915';
        $destLong  = isset($_REQUEST['destLat']) ? trim($_REQUEST['destLat']) :'121.0531086901567';
        $userId =  isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'1';
        $userType =  isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'User';
        $storeId =  isset($_REQUEST['storeId']) ? trim($_REQUEST['userType']) :'User';
        $favorite =  isset($_REQUEST['favorite']) ? trim($_REQUEST['favorite']) :'User';
        
        $url = 'http://router.project-osrm.org/route/v1/biking/'.$sourceLong.','. $sourceLat.';'. $destLong.','.  $destLat.'?steps=true';
        $json = file_get_contents($url);
        $data = json_decode($json);
        
        $location = array();
        
        foreach($data->routes[0]->legs[0]->steps as $directions){
        
            $locationStr['long'] = $directions->maneuver->location[0];
            $locationStr['lat'] = $directions->maneuver->location[1];
            $location['points'][] =  $locationStr;
            
            // echo $directions->maneuver->location[0].",".$directions->maneuver->location[1];
            // echo '</br>';
        }

        foreach($data->routes[0]->legs[0]->steps as $directions){
        
      
            $location['distance'][] =  $directions->distance;
            $temp = $temp+ (int)$directions->distance;
            $location['totalDistance'] = $temp;
            // echo $directions->maneuver->location[0].",".$directions->maneuver->location[1];
            // echo '</br>';
            
        }
        
        echo json_encode($location);
    
    }
    
    
    // $servicetype = "GET_ADDRESS_FROM_COORDINATES";
    if($servicetype == "GET_ADDRESS_FROM_COORDINATES"){

        $sourceLat = isset($_REQUEST['sourceLat']) ? trim($_REQUEST['sourceLat']) : '14.604762669084213';
        $sourceLong  = isset($_REQUEST['sourceLong']) ? trim($_REQUEST['sourceLong']) :'121.05306900486548';
        $destLat  = isset($_REQUEST['destLong']) ? trim($_REQUEST['destLong']) :'14.604124164791147';
        $destLong  = isset($_REQUEST['destLat']) ? trim($_REQUEST['destLat']) :'121.04419089330584';
        $userId =  isset($_REQUEST['userId']) ? trim($_REQUEST['userId']) :'1';
        $userType =  isset($_REQUEST['userType']) ? trim($_REQUEST['userType']) :'User';
        $storeId =  isset($_REQUEST['storeId']) ? trim($_REQUEST['userType']) :'User';
        $favorite =  isset($_REQUEST['favorite']) ? trim($_REQUEST['favorite']) :'User';
        
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
       
        
        // $url = file_get_contents("https://photon.komoot.io/reverse?lat=$sourceLat&lon=$sourceLong");
        // $photon = json_decode($url); 
        
        
        // $country = $photon -> features[0]-> properties->country;
        // $city = $photon -> features[0]-> properties->city;
        // $locality = $photon -> features[0]-> properties->locality;
        // $housenumber = $photon -> features[0]-> properties->housenumber;
        // $street = $photon -> features[0]-> properties->street;
        // $district = $photon -> features[0]-> properties->district;
        // $name = $photon -> features[0]-> properties->name;
        // $state = $photon -> features[0]-> properties->state;
        
        
        // $address = (($name != null)? $name.' ' : '') . ''.(($housenumber != null)? $housenumber.' ' : '').''. (($street != null)? $street.' ' : '').''.(($district != null)? $district.' ' : '').''.(($locality != null)? $locality.' ' : '').''.(( $city != null)?  $city.' ' : '').''.
        //             (($state != null)? $state.' ' : '').''.(($country != null)? $country.' ' : '').'';
        
    
        // $location['type'] =  "photon";
        // $location['address'] =  $address;
        // $location['latitude'] =   $sourceLat;
        // $location['longitude'] =   $sourceLong;
        // $location['address_name'] =   $name;
        // $location['housenumber'] = $housenumber;
        // $location['street'] =  $street;
        // $location['locality'] = $locality;
        // $location['district'] = $district;
        // $location['city'] =  $city;
        // $location['state'] = $state;
        // $location['country'] = $country;
        
        echo json_encode($location);
      
    }
    
  //  $servicetype = "SEARCH_ADDRESS";
    if($servicetype == "SEARCH_ADDRESS"){
        
      

        $query  = isset($_REQUEST['input']) ? trim($_REQUEST['input']) :'Bahay ni Makoy';
        $query = $query." Philippines";
        $address = str_replace(" ", "%20", $query);
        $location = array();
     
        $USERAGENT = $_SERVER['HTTP_USER_AGENT'];


        //$messageArray['data'] = get_web_page("https://nominatim.openstreetmap.org/?format=json&addressdetails=1&q=".$address."&country=PH");
    

        $opts = array('http'=>array('header'=>"User-Agent: $USERAGENT\r\n"));
        $context = stream_context_create($opts);
        $url4 = file_get_contents("https://nominatim.openstreetmap.org/?format=json&addressdetails=1&q=".$address."&country=PH", false, $context);
        $osmaddress = json_decode($url4);  
        
        $count = 0;
        
        foreach( $osmaddress as $address){
             
            $place['main_text'] = $address ->display_name;
            $place['secondary_text'] = ($address ->address ->state != null)? $address ->address ->state.', '.$address ->address ->region : $address ->address ->region;
            $place['place_id'] = $address ->display_name;
            $place['description'] = $address ->display_name;
            $place['long'] = $address ->lon;
            $place['lat'] = $address ->lat;
            
            if($count <= 5){
                $location['predictions'][] = $place;
                $count++;
            }
           
            
           
        }

        $location['servicetype'] = $servicetype;
        $location['query'] =  $query;
        
        // $url = file_get_contents("https://photon.komoot.io/api/?q=".$address);
        // $photon = json_decode($url);  
        
        
        // foreach( $photon->features as $address){
            
            
        //     $country = $address-> properties->country;
        //     $city = $address-> properties->city;
        //     $locality = $address-> properties->locality;
        //     $housenumber = $address-> properties->housenumber;
        //     $street = $address-> properties->street;
        //     $district = $address-> properties->district;
        //     $name = $address-> properties->name;
        //     $state =$address-> properties->state;
            
        //     $long =$address-> geometry->coordinates[0];
        //     $lat =$address-> geometry->coordinates[1];
            
        //     $displayaddress = (($name != null)? $name.' ' : '') . ''.(($housenumber != null)? $housenumber.' ' : '').''. (($street != null)? $street.', ' : '').''.(($district != null)? $district.', ' : '').''.(($locality != null)? $locality.' ' : '').''.(( $city != null)?  $city.' ' : '').''.
        //             (($state != null)? $state.' ' : '').'';
                    
        //     $displayaddress2 = (( $city != null)?  $city.', ' : '').''.
        //             (($state != null)? $state.', ' : ''). $country;
                    
        //     // echo $address ->properties->country;
        //     // echo $name." ".$housenumber." ".$street." ".$locality. " ". $city." ".$district." ".$state." ".$country;
             
        //     $place['main_text'] = $displayaddress;
        //     $place['secondary_text'] = $displayaddress2;
        //     $place['place_id'] = $displayaddress2;
        //     $place['description'] = $displayaddress2;
            
        //     //FOR PHOTON ONLY
        //     $place['long'] = $long;
        //     $place['lat'] =  $lat;
        
            
         
        //     $location['predictions'][] = $place;
            
           
        // }
        
        

        
      
        
       
        
        echo json_encode($location);
        //echo json_encode($osmaddress);
          
    
    }
    
   

    
   
  
  

    
function get_web_page( $url, $cookiesIn = '' ){
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => true,     //return headers in addition to content
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_SSL_VERIFYPEER => true,     // Validate SSL Cert
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_COOKIE         => $cookiesIn
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $rough_content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header_content = substr($rough_content, 0, $header['header_size']);
        $body_content = trim(str_replace($header_content, '', $rough_content));
        $pattern = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m"; 
        preg_match_all($pattern, $header_content, $matches); 
        $cookiesOut = implode("; ", $matches['cookie']);

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['headers']  = $header_content;
        $header['content'] = $body_content;
        $header['cookies'] = $cookiesOut;
    return $header;
}


?>