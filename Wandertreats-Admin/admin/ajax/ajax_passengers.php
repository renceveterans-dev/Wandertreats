
<?php

    ini_set('display_errors', 1);
    date_default_timezone_set("Asia/Manila");

    //TRIKAROO AJAX_GET ALL DRIVERS LOCATION
     
    
    include_once('../webservice/wanderlust_config.php');
    include_once('../webservice/wanderlust_general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();

    $sql = "SELECT fl.vBookingRef, fl.dDateBooked, pas.* FROM flight_booking as fl JOIN passengers as pas ON fl.iFlightId = pas.iFlightId ORDER BY dDateBooked DESC";

    $statement = $db->query($sql);

    $passengers = $statement->fetchAll();

    $x = 0;
    $count = 1;
    while ($x < count($passengers)) {


        echo '
        <tr>

     
        <td>' . $count . '</td>
        <td>' .  $passengers[$x]["vFullname"] . '</td>
        <td>' .  $passengers[$x]["vPhone"] . '</td>
        <td>' .  $passengers[$x]["eGender"] . '</td>
        <td>' .  $passengers[$x]["vBookingRef"] . '</td>
       
    </tr>
        
        ';

        $x++;
        $count++;
    }
  
    
    
    

?>