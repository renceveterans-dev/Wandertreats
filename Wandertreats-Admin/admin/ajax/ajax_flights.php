
<?php

    ini_set('display_errors', 1);
    date_default_timezone_set("Asia/Manila");

    //TRIKAROO AJAX_GET ALL DRIVERS LOCATION
     
    
    include_once('../webservice/wanderlust_config.php');
    include_once('../webservice/wanderlust_general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();

    $sql = "SELECT * FROM flight_booking ORDER BY dDateBooked DESC";

    $statement = $db->query($sql);

    $Totalflights = $statement->fetchAll();

    $x = 0;
    $count = 1;
    while ($x < count($Totalflights)) {

        if($Totalflights[$x]["vAirline"] == "Cebu Pacific" || $Totalflights[$x]["vAirline"] == "Cebu PAcific" || $Totalflights[$x]["vAirline"] == "Cebu Pacific Air" ){

                                            $imageSrc = "../img/ceb.png";
                                        }else if($Totalflights[$x]["vAirline"] == "Philippine Airlines"){
                                            $imageSrc = "../img/pal.png";

                                        }else if($Totalflights[$x]["vAirline"] == "Air Asia"){
                                            $imageSrc = "../img/airasia.png";
                                        }

        echo '
        <tr>
        <td>' . $count . '</td>
        <td>' .  $Totalflights[$x]["vName"] . '</td>
        <td><img width="20" height="20" src="' .  $imageSrc . '" /></td>
        <td>' .  $Totalflights[$x]["vBookingRef"] . '</td>
        <td>' .  $Totalflights[$x]["vRoute"] . '</td>
        <td>' .  $Totalflights[$x]["dDateBooked"] . '</td>
        <td>' .  $Totalflights[$x]["vDateFlight"] . '</td>
        <td> ' .  $Totalflights[$x]["vFlightStatus"] . '</td>
        <td>
            <div class="table-actions">
                <span onclick="editFlightStatus('.$Totalflights[$x]["iFlightId"].')"><i class="ik ik-settings"></i></span>&nbsp;&nbsp;
                <span onclick="viewFlight(\''.  $Totalflights[$x]["vBookingRef"] . '\')"><i class="ik ik-eye"></i></span>&nbsp;&nbsp;
                <span><a style="text-decoration:none;color:black;padding:0px;margin:0px;font-size:13px" href="update_flight.php?flightId='.$Totalflights[$x]["iFlightId"].'"><i class="ik ik-edit-2"></i></a></span>&nbsp;&nbsp;
                <span onclick="editFlightStatus('.$Totalflights[$x]["iFlightId"].')"><i class="ik ik-trash"></i></span>&nbsp;&nbsp;
            </div>
        </td>
    </tr>
        
        ';

        $x++;
        $count++;
    }
  
    
    
    

?>