<?php

ini_set('display_errors', 0);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);RATE

date_default_timezone_set("Asia/Manila");

session_start();

$sessionId = session_id();

//TRIKAROO WEBSERVICE

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

include_once('general_functions.php');


$database = new Connection();

$db = $database->openConnection();



$servicetype  = isset($_POST['ServiceType']) ? trim($_POST['ServiceType']) : '';

$messageArray = array();

$messageArray['response'] = 0;




    automation();




    // $sql = "SELECT * FROM expense";


    // $statement = $db->query($sql);

    // $bookings = $statement->fetchAll();

    // $fieldId = getTableId($bookings);

    


    // echo $fieldId ;







$database->closeConnection();
