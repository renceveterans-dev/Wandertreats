<?php

    include_once('db_info.php');
    /*
     * All database connection variables
     */
    define('BASE_PATH', realpath(dirname(__FILE__)));
    define('DB_USER', "wanderlustph"); // db user
    define('DB_PASSWORD', "K-anne09$"); // db password (mention your db password here)
    define('DB_DATABASE', "wanderlustph"); // database name
    define('DB_SERVER', "localhost"); // db server


    // Valid constant names
    define("GCM_API_KEY", '');
    define("GCM_URL_PATH",    '');
    define("FOO_BAR", "something more");

    $database = new Connection();
    
    $obj = $database->openConnection();

    $sql = "SELECT * FROM configurations"; 
    $statement = $obj->query($sql); 
    $configurations = $statement ->fetchAll(); 
    

    for($c = 0 ; $c < count($configurations); $c++){ 
        $config = $configurations[$c];
        if($config['vConfigName'] != '' || $config['vConfigValue'] != ''){
             define($config['vConfigName'], $config['vConfigValue']);
        }
       
    }

    


class constants
{


    /////////////////////////
    // GOOGLE API SERVICES //
    /////////////////////////

 
    const FIREBASE_KEY = 'AAAAVxjmIeQ:APA91bHfYd9UHK4ERjCRTmeXew8-pzi-44KDCOZckD2UgoaFuCbs3_AkhNzwkRjtm2Ia8MhQzH9fRZVp_IbzIJXwkLuSg9pusIo1PsEo3laK37DwpX05uKhtYgmLmX35fB1JoE7xSivQ';
    
    const CLOUD_MESSAGING_URL= 'https://fcm.googleapis.com/fcm/send';
    
    const GOOGLE_API_KEY = 'AIzaSyD2Ku8qGzmjx2k97qDVkWSPION8R0MJTbQ';
    

    ////////////////////////
    // TRIKAROO APP       //
    ////////////////////////

    const LIST_RESTAURANT_LIMIT_BY_DISTANCE = 5; //in kilometer

    const SCOPE_DISTANCE = 5; //in kilometer

    const FLAT_RATE_PASAKAY = 40;

    const FLAT_RATE_PABILI = 49;

    const FLAT_RATE_GROCERY_MIN = 55;

    const FLAT_RATE_GROCERY_MAX = 55;

    const RATE_PER_KM = 5;

    const SURGE_RATE = 1.2;


    /////////////////////////
    //  TWILLIO            //
    /////////////////////////


    const Account_SID = "AC6fb45a05498750a9ffd49e84154e16f6";

    const Auth_Token = "3f37e731f441f5dc82ed1c8abf8aa118";

    const TwilioMobileNum = "+13603104740";

    const BASE_URL = "https://wanderlustphtravel.com/wandertreats/";
}
