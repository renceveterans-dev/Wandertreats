<?php

if ($_SERVER['REQUEST_METHOD']!='POST') 
{
  echo 'This script is not supposed to be viewed in a browser!';
  exit;
}

// Connecting to the database.
// Change the parameter values to the actual ones
$con = new mysqli('renceveteransdev19047.ipagemysql.com','wandertreats','K-anne050915$','wandertreats');


$insert_sms_success = FALSE;

if (mysqli_connect_errno()==0){ 
  // if successfully connected
  
  // date and time the message was sent in the local time zone of the computer on which SMS Enabler is running
  $sent_dt = @date("Y-m-d H:i:s");//$_POST['sc_datetime'];
  // date and time the message was sent in UTC
  // $sent_dt = $_POST['sc_datetime_utc']; 

  // text of the message
  $txt = $_POST['text']; 
  // sender's phone number
  $sender_number = $_POST['from'];
  // important: escape string values 
  $txt = mysqli_real_escape_string($con, $txt);

  $sender_number = mysqli_real_escape_string($con, $sender_number);

  if($sender_number == "GCash" || $sender_number == "METROFRESH"){
      // // important: trim the gcash reference Nunber
    $string =  $txt;
    $words = array();
    $words = explode(' ', $string);

    if($words[2] == "received"){
       // find String after word Ref. No.
      $prefix = "Ref. No. ";
      $index = strpos($string, $prefix) + strlen($prefix);
      $refNo = substr($string, $index);

      $amount = get_string_between($string, "PHP ", "of GCash");

          // creating an sql statement to insert the message into the sms_in table
      $sql="INSERT INTO  sms_transaction_logs ( vType, vSender, vMessage, vAmount, vReferenceNo) VALUES ('PAYMENT','$sender_number', '$txt', '$amount', '$refNo' )";
      // executing the sql statement
      $insert_smspayment_success = mysqli_query($con,$sql);
      
      // closing the connection
      

    }
   
  }




  // creating an sql statement to insert the message into the sms_in table
  $sql="INSERT INTO sms_in(sms_text,sender_number) VALUES ('$txt','$sender_number')";
  // executing the sql statement
  $insert_sms_success = mysqli_query($con,$sql);
  
  // closing the connection
  mysqli_close($con);

}

if ($insert_sms_success){
  // if we have succeeded to insert sms into the database 
  echo "SUCCESS";
}
else 
{  
  // if we have failed to insert sms into the database.  
  // Here you can do something upon failure to insert sms into the database.
  // For example, let SMS Enabler know an error has occured
  http_response_code(500);
}


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}