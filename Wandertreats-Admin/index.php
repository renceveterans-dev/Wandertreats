<?php 

  ini_set('display_errors',1);
  include_once('general_functions.php');

  $messageArray = array();

  $servicetype  = isset($_REQUEST['refNo']) ? trim($_REQUEST['refNo']) : '';


  $sql = "SELECT * FROM sms_in ORDER BY sent_dt DESC";      
  // $sql = "SELECT * FROM sms_in WHERE sent_dt >= NOW() - INTERVAL 1 HOUR";                
  $statement = $obj->query($sql); 
  $smsTransactions = $statement ->fetchAll(); 

  for($x = 0; $x<count($smsTransactions); $x++){

      $sms = $smsTransactions[$x];

      echo " | ".$sms['sender_number']." | ".$sms['sms_text']." | ".$sms['sent_dt'],"<br>";

      $string = $sms['sms_text'];
      $prefix = "Ref. No. ";
      $index = strpos($string, $prefix) + strlen($prefix);
      $result = substr($string, $index);
      echo "Reference No :  ". $result."<br>";
      echo "<br>";

  }

?>