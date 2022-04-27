<?php
 
/*
 * All database connection variables
 */
 
define('DB_USER', "trikaroo_user"); // db user
define('DB_PASSWORD', "Mallody66888"); // db password (mention your db password here)
define('DB_DATABASE', "trikaroo_bsd"); // database name
define('DB_SERVER', "localhost"); // db server


// Valid constant names
define("GCM_API_KEY", '');
define("GCM_URL_PATH",    '');
define("FOO_BAR", "something more");


class constants {
    
    
    /////////////////////////
    // GOOGLE API SERVICES //
    /////////////////////////
    
    //const FIREBASE_KEY = 'AAAA9zHgfRU:APA91bGDi967-kmwSV8DtfTd2w4_nb8I8FDww9L6iEIGADMjlyrEB8CeJTt9cMYAn1MrHA1HZ2LytdK70llDTrSsxqjRv3px_JwSkXw8NIvTCR5WPJAFGOnyJ9cq7q0ce2Gxmep5VO5r';
    const FIREBASE_KEY = 'AAAAVxjmIeQ:APA91bHfYd9UHK4ERjCRTmeXew8-pzi-44KDCOZckD2UgoaFuCbs3_AkhNzwkRjtm2Ia8MhQzH9fRZVp_IbzIJXwkLuSg9pusIo1PsEo3laK37DwpX05uKhtYgmLmX35fB1JoE7xSivQ';
    
    const CLOUD_MESSAGING_URL= 'https://fcm.googleapis.com/fcm/send';
    
    const GOOGLE_API_KEY = 'AIzaSyD2Ku8qGzmjx2k97qDVkWSPION8R0MJTbQ';
    
    ////////////////////////
    // TRIKAROO APP       //
    ////////////////////////
  
    const LIST_RESTAURANT_LIMIT_BY_DISTANCE = 5; //in kilometer
  
    const SCOPE_DISTANCE = 5; //in kilometer
  
    const FLAT_RATE_PASAKAY = 55; 
   
    const FLAT_RATE_PABILI= 55;
    
    const GROCERY_SERVICE_CHARGE= 30;
    
    const FLAT_RATE_GROCERY_MIN= 55; 
    
    const FLAT_RATE_GROCERY_MAX= 55; 
    
    const RATE_PER_KM= 5; 
    
    const RATE_PER_MIN= 2;
    
    const SURGE_RATE = 1.2; 
    
    const MAXIMUM_DISTANCE_RANGE = 5; 
    
    const TRANSACTION_FEE = 0.10; 
    
    const WAITINGTIME_RATE_PER_MIN= 2; 
    
    const DEFAULT_REFERRAL_POINTS_EARNED = 5; 
    
    const REWARDS_POINTS_RATE = 0.05; 
    
    const MINIMUM_DISCOUNT = 5; 
    
    
    const SALT = "randomstringforsaltfortrikarooapplication_hengyenecommeceinc";
    
    
    /////////////////////////
    //  TWILLIO            //
    /////////////////////////
    
     
    const Account_SID = "AC63c04e303db6ad9c00cb39f433c0fc5c";
    
	const Auth_Token = "e79257c780c5b3599c6d5ba34ec834d9";
	
	const TwilioMobileNum = "+14702604943";
	
	
	 /////////////////////////
    //  APP VERSION           //
    /////////////////////////
    
     
    const TRIKAROO_VERSION = "2.1.10";
    
	const TRIKAROO_VERSION_CODE = "29";
	
    const TRIKAROO_VERSION_PRIORITY = "LOW";
	
    const TRIKAROODRIVER_VERSION = "1.1";
    
    const TRIKAROODRIVER_VERSION_CODE = "2";
    
    const TRIKAROODRIVER_VERSION_PRIORITY= "LOW";
    
    
    
    const RIDEPASAKAY_CONFIGURATION = "NEAREST";


}

?>


