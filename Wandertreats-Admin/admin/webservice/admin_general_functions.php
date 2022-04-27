<?php

//GENERAL FUNCIONS - 10-04-19
//GENERAL FUNCIONS - 06-23-20
//renceveterans.dev
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';
require_once '../vendor/twilio/Services/Twilio.php';
require_once('wanderlust_db_info.php');

$database = new Connection();

$obj = $database->openConnection();

//QUERY//

function myQuery($tablename, $data, $type, $where = null, $others = "")
{
    global $obj;
    global $database;

    $result = null;

    switch ($type) {


        case "insert":

            $fieldnames = setFieldnames($data);

            $values = setValues($data);

            $sql = "INSERT INTO ";
            $sql .= $tablename;
            $sql .= " (" . $fieldnames . ") ";
            $sql .= "VALUES";
            $sql .= " (" . $values . ") ";


            $statement = $obj->prepare($sql);

            $result = $statement->execute();

            if (!$result) {
                echo $sql;
            }

            break;

        case "update":

            $where_clause = setWhereClause($where);
            $set_clasue = setAssignment($data);

            $sql = "UPDATE ";
            $sql .= $tablename;
            $sql .= " SET " . $set_clasue . " " . $where_clause;


            $statement = $obj->prepare($sql);

            $result = $statement->execute();

            if (!$result) {
                echo $sql;
            }




            break;

        case "selectall":

            $fieldnames = getFieldnames($data);

            if ($where != null) {
                $where_clause = set_Binded_WhereClause($where);
            } else {
                $where_clause = "";
            }

            if ($others == null) {
                $others = "";
            }



            $sql = "SELECT ";
            $sql .= $fieldnames;
            $sql .= " FROM " . $tablename . " ";
            $sql .= $where_clause;
            $sql .= $others;

            $parameters = array();

            $statement = $obj->prepare($sql);

            if ($where != null) {
                foreach ($where as $key => $val) {
                    $parameters[':' . $key] = $val;
                }

                $statement->execute($parameters);
            } else {
                $statement->execute();
            }




            $result = $statement->fetchAll();

            if (!$result) {
                // echo $sql;
            }

            break;

        case "select":

            $fieldnames = getFieldnames($data);
            if ($where != null) {
                $where_clause = set_Binded_WhereClause($where);
            } else {
                $where_clause = "";
            }

            if ($others == null) {
                $others = "";
            }



            $sql = "SELECT ";
            $sql .= $fieldnames;
            $sql .= " FROM " . $tablename . " ";
            $sql .= $where_clause;
            $sql .= $others;

            $parameters = array();

            $statement = $obj->prepare($sql);

            if ($where != null) {
                foreach ($where as $key => $val) {
                    $parameters[':' . $key] = $val;
                }

                $statement->execute($parameters);
            } else {
                $statement->execute();
            }




            $result = $statement->fetch();

            if (!$result) {
                echo $sql;
            }

            break;
    }






    $database->closeConnection();

    return $result;
}

function getFieldnames($data)
{
    $lastvalue = array_pop($data);

    $values = "";

    foreach ($data as $key) {

        $values .= " " . $key . " ,  ";
    }

    return $values . "  " . $lastvalue . " ";
}

function setValues($data)
{
    $lastvalue = array_pop($data);
    $values = "";

    foreach ($data as $key) {

        $values .= " '" . $key . "',  ";
    }

    return $values . " '" . $lastvalue . "' ";
}

function setFieldnames($data)
{

    $fieldnames = array_keys($data);
    $lastfield = array_pop($fieldnames);
    $fields = "";

    foreach ($fieldnames as $key) {

        $fields .= $key . ",  ";
    }

    return $fields . " " . $lastfield;
}

function setAssignment($data)
{
    $clause = "";
    $fieldnames = array_keys($data);
    $lastfield = array_pop($fieldnames);
    $values = array_values($data);
    $lastvalue = array_pop($data);

    for ($i = 0; $i < count($fieldnames); $i++) {
        $clause .= $fieldnames[$i] . "  = '" . $values[$i] . "' ,  ";
    }

    $clause .=  $lastfield . " = '" . $lastvalue . "' ";

    return $clause;
}

function setWhereClause($data)
{
    $clause = "";
    $fieldnames = array_keys($data);
    $lastfield = array_pop($fieldnames);
    $values = array_values($data);
    $lastvalue = array_pop($data);

    $clause .= " WHERE ";

    for ($i = 0; $i < count($fieldnames); $i++) {
        $clause .= $fieldnames[$i] . "  = '" . $values[$i] . "'  AND  ";
    }

    $clause .=  $lastfield . " = '" . $lastvalue . "' ";

    return $clause;
}

function set_Binded_WhereClause($data)
{
    $clause = "";
    $fieldnames = array_keys($data);
    $lastfield = array_pop($fieldnames);
    $values = array_values($data);
    $lastvalue = array_pop($data);

    $clause .= " WHERE ";

    for ($i = 0; $i < count($fieldnames); $i++) {
        $clause .= $fieldnames[$i] . "  = :" . $fieldnames[$i] . "  AND  ";
    }

    $clause .=  $lastfield . " = :" . $lastfield . " ";

    return $clause;
}



//SEND NOTIFICATION//

function notify($userType, $iUserId, $activity, $message)
{

    if ($userType == "driver") {
        $where['iDriverId'] = $iUserId;
    }

    if ($userType == "user") {
        $where['iUserId'] = $iUserId;
    }

    $select = array("vFirebaseDeviceToken");

    $driverid = myQuery("register_" . $userType, $select, "selectall", $where);

    $deviceToken =  trim($driverid[0]['vFirebaseDeviceToken']);

    $fields = array(
        'to' =>  $deviceToken,

        'notification' => array(
            'title' => 'Working Good',
            'body' =>  $activity
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
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);

    curl_close($ch);

    $jsonString = $result;

    $jsonObject = json_decode($jsonString);

    if (isset($jsonObject->success) && $jsonObject->success == 1) {

        $returnArr['message'] = "Success!";
    } else {
        $returnArr['message'] =  $jsonString;
    }


    $returnArr['token'] =   $deviceToken;
}



function sendRequestToUser($iUserId, $activity, $message)
{

    $where['iUserId'] = $iUserId;

    $select = array("vFirebaseDeviceToken");

    $driverid = myQuery("register_user", $select, "selectall", $where);

    $deviceToken =  trim($driverid[0]['vFirebaseDeviceToken']);

    $Rmessage  = "CabRequested";

    $fields = array(
        'to' =>  $deviceToken,

        'notification' => array(
            'title' => 'Working Good',
            'body' =>  $activity
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
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);

    curl_close($ch);

    $jsonString = $result;

    $jsonObject = json_decode($jsonString);

    if (isset($jsonObject->success) && $jsonObject->success == 1) {

        $returnArr['message'] = "Success!";
    } else {
        $returnArr['message'] =  $jsonString;
    }


    $returnArr['token'] =   $deviceToken;
}


function sendRequestToDriver($iDriverId, $activity, $message)
{

    $where['iDriverId'] = $iDriverId;

    $select = array("vFirebaseDeviceToken");

    $driverid = myQuery("register_driver", $select, "selectall", $where);

    $deviceToken =  trim($driverid[0]['vFirebaseDeviceToken']);

    $Rmessage  = "CabRequested";

    $fields = array(
        'to' =>  $deviceToken,

        'notification' => array(
            'title' => 'Working Good',
            'body' =>  $activity
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
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);

    curl_close($ch);

    $jsonString = $result;

    $jsonObject = json_decode($jsonString);

    if (isset($jsonObject->success) && $jsonObject->success == 1) {

        $returnArr['message'] = "Success!";
    } else {
        $returnArr['message'] =  $jsonString;
    }


    $returnArr['token'] =   $deviceToken;
}

function get_Address($lat, $lon)
{

    // echo ' Latitude : '.$lat;
    // echo ' Longitude : '.$lon;

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&key=' . constants::GOOGLE_API_KEY;
    $json = file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    $address = '';
    if ($status == "OK") {
        foreach ($data->results[0]->address_components as $address_component) {
            if (in_array('street_number', $address_component->types)) {
                $street_number = $address_component->long_name;
            }
            if (in_array('route', $address_component->types)) {
                $route = $address_component->long_name;
            }
        }
    }
    // return $street_number." ".$route;

    return  $data->results[0]->formatted_address;
}

function get_Address2($lat, $lon)
{

    // echo ' Latitude : '.$lat;
    // echo ' Longitude : '.$lon;

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&key=' . constants::GOOGLE_API_KEY;
    $json = file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    $address = '';
    if ($status == "OK") {
        foreach ($data->results[0]->address_components as $address_component) {
            if (in_array('street_number', $address_component->types)) {
                $street_number = $address_component->long_name;
            }
            if (in_array('route', $address_component->types)) {
                $route = $address_component->long_name;
            }
        }
    }
    // return $street_number." ".$route;

    return  $data->results[1]->formatted_address;
}

function get_Address3($lat, $lon)
{

    // echo ' Latitude : '.$lat;
    // echo ' Longitude : '.$lon;

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&key=' . constants::GOOGLE_API_KEY;
    $json = file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    $address = '';
    if ($status == "OK") {
        foreach ($data->results[0]->address_components as $address_component) {
            if (in_array('street_number', $address_component->types)) {
                $street_number = $address_component->long_name;
            }
            if (in_array('route', $address_component->types)) {
                $route = $address_component->long_name;
            }
        }
    }
    // return $street_number." ".$route;

    return  $data->results[3]->formatted_address;
}


function get_Address4($lat, $lon)
{

    // echo ' Latitude : '.$lat;
    // echo ' Longitude : '.$lon;

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&key=' . constants::GOOGLE_API_KEY;
    $json = file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    $address = '';
    if ($status == "OK") {
        foreach ($data->results[0]->address_components as $address_component) {
            if (in_array('street_number', $address_component->types)) {
                $street_number = $address_component->long_name;
            }
            if (in_array('route', $address_component->types)) {
                $route = $address_component->long_name;
            }
        }
    }
    // return $street_number." ".$route;

    return  $data->results[4]->formatted_address;
}

function get_Address5($lat, $lon)
{

    // echo ' Latitude : '.$lat;
    // echo ' Longitude : '.$lon;

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&key=' . constants::GOOGLE_API_KEY;
    $json = file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    $address = '';
    if ($status == "OK") {
        foreach ($data->results[0]->address_components as $address_component) {
            if (in_array('street_number', $address_component->types)) {
                $street_number = $address_component->long_name;
            }
            if (in_array('route', $address_component->types)) {
                $route = $address_component->long_name;
            }
        }
    }
    // return $street_number." ".$route;

    return  $data->results[5]->formatted_address;
}

function check_Address_restriction($lat, $lon, $place)
{

    // echo ' Latitude : '.$lat;
    // echo ' Longitude : '.$lon;

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&key=' . constants::GOOGLE_API_KEY;
    $json = file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    $address = '';
    if ($status == "OK") {
        foreach ($data->results[0]->address_components as $address_component) {
            if ($address_component->long_name == $place || $address_component->short_name == $place) {
                return  "Yes";
            }
        }
    }
    // return $street_number." ".$route;

    return  "No";
}


function get_CompleteAddress($lat, $lon)
{

    // $region = "";
    // $state = "";
    // $city = "";
    // $country = "";

    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&sensor=false&key=' . constants::GOOGLE_API_KEY;
    $json = file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    $address = array();
    if ($status == "OK") {
        foreach ($data->results[0]->address_components as $address_component) {
            if ($address_component->types[0] == "locality") {
                $address["city"] =   $address_component->long_name;
            }

            if ($address_component->types[0] == "administrative_area_level_2") {

                $address["state"] = $address_component->long_name;
            }

            if ($address_component->types[0] == "administrative_area_level_1") {

                $address["region"] = $address_component->long_name;
            }

            if ($address_component->types[0] == "country") {

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



function get_Duration($address1, $address2, $unit = null)
{

    // store
    $address1 = str_replace(" ", "+", $address1);
    // buyer
    $address2 = str_replace(" ", "+", $address2);


    $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=' . $address1 . '&destinations=' . $address2 . '&mode=driving&traffic=best_guess&key=' . constants::GOOGLE_API_KEY;

    //$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=501 Epifanio de los Santos Ave, Quezon City, 1111 Metro Manila, Philippines&destinations=Atlanta Center Building, 31 Annapolis, San Juan, Metro Manila, Philippines&mode=driving&traffic=best_guess&key=AIzaSyD2Ku8qGzmjx2k97qDVkWSPION8R0MJTbQ';
    // https://maps.googleapis.com/maps/api/geocode/json?latlng=14.61106014366259,121.05477824807167&sensor=false&key=AIzaSyD2Ku8qGzmjx2k97qDVkWSPION8R0MJTbQ

    $json = file_get_contents($url);
    $json = json_decode($json);

    $duration = $json->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'text'};
    $duration2 = $json->{'rows'}[0]->{'elements'}[0]->{'duration'}->{'value'};

    if ($unit != null) {
        if ($unit == "s") {
            return $duration2;
        } else if ($unit == "m") {
            $val = (int)$duration2;

            return round($val / 60);
        }
    } else {
        return  $duration;
    }
}


// function get_Duration2(){



// }



function get_Distance($address1, $address2, $unit = null)
{

    // store
    $address1 = str_replace(" ", "+", $address1);
    // buyer
    $address2 = str_replace(" ", "+", $address2);


    $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=' . $address1 . '&destinations=' . $address2 . '&mode=driving&traffic=best_guess&key=' . constants::GOOGLE_API_KEY;


    $json = file_get_contents($url);
    $json = json_decode($json);

    $distance = $json->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'text'};
    $distance2 = $json->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'};

    if ($unit != null) {
        if ($unit == "km") {
            return $distance2 / 1000;
        } else if ($unit == "m") {
            return $distance2;
        }
    } else {
        return $distance;
    }
}



function get_lat_long($address)
{

    $address = str_replace(" ", "+", $address);

    $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key=" . constants::GOOGLE_API_KEY);
    $json = json_decode($json);

    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

    return $lat . ',' . $long;
}

function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit)
{
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
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
function cal_speed($dist, $time)
{
    echo "\n Distance(km) : " . $dist;
    echo "\n Time(hr) : " . $time;

    return $dist / $time;
}

// Function to calculate  
// distance traveled 
function cal_dis($speed, $time)
{
    echo "\n Time(hr) : " . $time;
    echo "\n Speed(km / hr) : " . $speed;

    return $speed * $time;
}

// Function to calculate 
// time taken 
function cal_time($dist, $speed)
{
    echo "\n Distance(km) : " . $dist;
    echo "\n Speed(km / hr) : " . $speed;

    return $speed * $dist;
}

function distance($lat1, $lon1, $lat2, $lon2, $unit)
{
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
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


function getLanguage($lang)
{


    $where['vTitle'] = $lang;

    $result = myQuery("language_master", array("vCode"), "selectall",  $where);

    return  $result[0]["vCode"];
}



function GenerateUniqueOrderNo($code)
{

    $random = rand(0, 600000);
    return $code . "" . date("y") . "" . date("m") . "" . date("d") . "" . $random;
}

function GenerateToken()
{

    $random = rand(0, 600000);
    return date("y") . "" . date("m") . "" . date("d") . "" . $random;
}


// Generate token
function getToken($length)
{

    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }

    return $token;
}

function checkEmail($userType, $username)
{

    unset($where);

    if ($userType == "Driver") {

        $fieldname = array("iDriverId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "eGender", "tDeviceSessionId", "tDeviceData", "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");

        $where['vEmail'] = $username;

        $result = myQuery("register_driver", $fieldname, "selectall",  $where);


        // $sql = "SELECT * FROM register_driver WHERE vEmail = '". $username."' OR vPhone = '". $username."'";

        // $statement = $db->query($sql); 

        // $result = $statement ->fetchAll();  
    }

    if ($userType == "User") {


        // $sql = "SELECT * FROM register_user WHERE vEmail = '". $username."' OR vPhone = '". $username."'";

        // $statement = $db->query($sql); 

        // $result = $statement ->fetchAll();  

        $fieldname = array("iUserId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");

        $where['vEmail'] = $username;

        $result = myQuery("register_user", $fieldname, "selectall",  $where, " OR vPhone = '" . $username . "'");
    }

    return $result;
}


function checkPassword($userType, $email, $password)
{
    unset($where);

    $where['vPassword'] = $password;
    $where['vEmail'] = $email;

    if ($userType == "Driver") {

        $fieldname = array("iDriverId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "eGender", "tDeviceSessionId", "tDeviceData", "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");

        $result = myQuery("register_driver", $fieldname, "selectall",  $where);
    }

    if ($userType == "User") {

        $fieldname = array("iUserId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");

        $result = myQuery("register_user", $fieldname, "selectall",  $where, " OR vPhone = '" . $email . "'");
    }

    return $result;
}

function checkMobileNumber($userType, $mobile)
{
    unset($where);

    $where['vPhone'] =  $mobile;

    if ($userType == "Driver") {

        $fieldname = array("iDriverId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "eGender", "tDeviceSessionId", "tDeviceData", "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");

        $result = myQuery("register_driver", $fieldname, "selectall",  $where);
    }

    if ($userType == "User") {

        $fieldname = array("iUserId", "vName", "vLastName", "vPassword", "vEmail", "vPhone", "vAge", "eGender", "tDeviceSessionId", "tDeviceData",  "vFirebaseDeviceToken", "eLogout",  "vRefCode", "ePhoneVerified", "eEmailVerified");

        $result = myQuery("register_user", $fieldname, "selectall",  $where);
    }

    return $result;
}



function roundOff($value)
{

    return number_format($value, 2, '.', '');
}

function sendVerifivationCode($SMSclient, $mobilenumber, $SMSmessage)
{

    //     $account_sid = "AC6fb45a05498750a9ffd49e84154e16f6";
    // 	$auth_token = "3f37e731f441f5dc82ed1c8abf8aa118";

    $twilioMobileNum = "+19389991544";
    $toMobileNum = $mobilenumber; //"+639398296855";
    $message = $SMSmessage;


    $SMSclient->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $message);
}

function number_PH($number)
{
    //strip out everything but numbers
    $number =  preg_replace("/[^0-9]/", "", $number);
    //Strip out leading zeros:
    $number = ltrim($number, '0');
    //The default country code
    $default_country_code  = '+63';
    //Check if the number doesn't already start with the correct dialling code:
    if (!preg_match('/^[+]' . $default_country_code . '/', $number)) {
        $number = $default_country_code . $number;
    }


    return $number;
}


function sendVerificationEmail($email, $fullname, $token, $userType, $userId)
{
    try {

        $greetings = "<h2>Hello " . $fullname . ",</h2></br>";

        $link = "<a href='http://mallody.ph/resources/verify.php?userType=" . $userType . "&token=" . $token . "&id=" . $userId . "'>Verify now</a>";

        $body = "<p>Click this link to verifiy your email address for your Trikaroo account.</br> " . $link . " </p></br>";

        $body .= "</br>";

        $footer = "<p>Thanks,</br> Heng Yen E-Commerce Inc. team</p>";


        $message = $greetings;
        $message .= $body;
        $message .= $footer;


        $mail = new PHPMailer;
        $mail->isSMTP();

        $mail->SMTPDebug = SMTP::DEBUG_SERVER;


        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;

        $mail->Username = 'renceveterans.dev@gmail.com';
        $mail->Password = 'nlylkkbmccauilgi';

        $mail->setFrom('renceveterans.dev@gmail.com', 'Hengyen E-Commerce Inc.');
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
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            // header("Location: ../success.php?first-name=".$firstname);
        }
    } catch (Exception $e) {
        echo 'Mailer Error';
    }
}


function sendResetEmail($email, $fullname, $token, $userType, $userId)
{
    try {

        $greetings = "<h2>Hello " . $fullname . ",</h2></br>";

        $link = "<a href='http://mallody.ph/resources/reset_password.php?userType=" . $userType . "&token=" . $token . "&id=" . $userId . "'> Reset your password</a>";

        $body = "<p>Follow this link to reset your password for your Trikaroo account.</br> " . $link . " </br>";

        $body .= "If you didn’t ask to reset your password, you can ignore this email.</br></p>";

        $footer = "<p>Thanks,</br> Heng Yen E-Commerce Inc. team</p>";


        $message = $greetings;
        $message .= $body;
        $message .= $footer;


        $mail = new PHPMailer;
        $mail->isSMTP();

        $mail->SMTPDebug = SMTP::DEBUG_SERVER;


        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;

        $mail->Username = 'renceveterans.dev@gmail.com';
        $mail->Password = 'nlylkkbmccauilgi';

        $mail->setFrom('renceveterans.dev@gmail.com', 'Hengyen E-Commerce Inc.');
        $mail->addAddress($email, $fullname);
        //$mail->addAddress('rovirareymark21@gmail.com', 'Reymark Rovira');
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
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {

            // header("Location: ../success.php?first-name=".$firstname);
        }
    } catch (Exception $e) {
        echo 'Mailer Error';
    }
}


function setOrderLogs($statusCode, $orderId)
{


    $insert['iStatusCode'] = (int) $statusCode;
    $insert['iOrderId'] = (int) $orderId;
    $insert['dDate'] = @date("Y-m-d H:i:s");

    $result = myQuery("order_status_logs", $insert, "insert");
}



//CHECKNG LOCATION INSIDE A BOUNDARY


function pointStringToCoordinates($pointString)
{
    $coordinates = explode(",", $pointString);
    return array("x" => trim($coordinates[0]), "y" => trim($coordinates[1]));
}

function isWithinBoundary($point, $polygon)
{
    $result = FALSE;
    $point = pointStringToCoordinates($point);
    $vertices = array();
    foreach ($polygon as $vertex) {
        $vertices[] = pointStringToCoordinates($vertex);
    }
    // Check if the point is inside the polygon or on the boundary
    $intersections = 0;
    $vertices_count = count($vertices);
    for ($i = 1; $i < $vertices_count; $i++) {
        $vertex1 = $vertices[$i - 1];
        $vertex2 = $vertices[$i];
        if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) {
            // This point is on an horizontal polygon boundary
            $result = TRUE;
            // set $i = $vertices_count so that loop exits as we have a boundary point
            $i = $vertices_count;
        }
        if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
            $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
            if ($xinters == $point['x']) { // This point is on the polygon boundary (other than horizontal)
                $result = TRUE;
                // set $i = $vertices_count so that loop exits as we have a boundary point
                $i = $vertices_count;
            }
            if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                $intersections++;
            }
        }
    }
    // If the number of edges we passed through is even, then it's in the polygon. 
    // Have to check here also to make sure that we haven't already determined that a point is on a boundary line
    if ($intersections % 2 != 0 && $result == FALSE) {
        $result = TRUE;
    }
    return $result;
}

function contains($point, $polygon)
{
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
            if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] - $polygon[$i][1]) < $x) {
                $oddNodes = !$oddNodes;
            }
        }
    }
    return $oddNodes;
}


function checkAllowedArea($Address_Array)
{

    if (!empty($Address_Array)) {

        unset($where);

        $where['eStatus'] = "Active";
        $where['eFor'] = "Restrict";

        $fieldname = array("tLatitude", "tLongitude");

        $allowed_data = myQuery("location_master", $fieldname, "selectall",  $where);

        if (count($allowed_data) > 0) {
            $allowed_ans = 'No';
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
                        $allowed_ans = 'Yes';
                        break;
                    }
                }
            }
        } else {
            $allowed_ans = 'Yes';
        }
    }
    return $allowed_ans;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//  ADMIN

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function clearAllOrders()
{


    global $obj;

    $sql = "TRUNCATE TABLE orders";

    $statement = $obj->prepare($sql);

    //Execute the statement.
    $statement->execute();

    $sql = "TRUNCATE TABLE order_details";

    $statement = $obj->prepare($sql);

    //Execute the statement.
    $statement->execute();

    $sql = "TRUNCATE TABLE order_status_logs";

    $statement = $obj->prepare($sql);

    //Execute the statement.
    $statement->execute();
}


clearAllOrders();
