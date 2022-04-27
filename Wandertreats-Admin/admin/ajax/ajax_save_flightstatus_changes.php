<?php

	ini_set('display_errors', 1);
	date_default_timezone_set("Asia/Manila");

	//TRIKAROO AJAX_GET ALL DRIVERS LOCATION
     
     unset($where);
     unset($update);

    
    include_once('../webservice/wanderlust_config.php');
    include_once('../webservice/wanderlust_general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();

    $flightId = $_POST['id'];

    $flightStatus= $_POST['status'];

    $remarks= $_POST['remarks'];

    $where['iFlightId'] = $flightId;

    $update['vFlightStatus'] = $flightStatus;

    $update['vRemarks'] = $remarks;

    $res = myQuery("flight_booking", $update, "update", $where);
    


    $sql = "SELECT * FROM flight_booking ORDER BY dDateBooked DESC";

    $statement = $db->query($sql);

    $Totalflights = $statement->fetchAll();

    $x = 0;
    $count = 1;
    while ($x < count($Totalflights)) {

        if($Totalflights[$x]["iFlightId"] ==  $flightId){
            $class = 'class="alert alert-success"';
         }else{
            $class = '';
         }
$haha = "haha";
           

        echo '


        
        <tr>
        <td>' . $count . '</td>
        <td>' .  $Totalflights[$x]["vName"] . '</td>
        <td>' .  $Totalflights[$x]["vAirline"] . '</td>
        <td>' .  $Totalflights[$x]["vBookingRef"] . '</td>
        <td>' .  $Totalflights[$x]["vRoute"] . '</td>
        <td>' .  $Totalflights[$x]["dDateBooked"] . '</td>
        <td>' .  $Totalflights[$x]["vDateFlight"] . '</td>
        <td '.$class.'  > ' .  $Totalflights[$x]["vFlightStatus"] . '</td>
        <td>
            <div class="table-actions">
                <span onclick="editFlightStatus('.$Totalflights[$x]["iFlightId"].')"><i class="ik ik-settings"></i></span>&nbsp;&nbsp;
                <span onclick="viewFlight(\''.  $Totalflights[$x]["vBookingRef"] . '\')"><i class="ik ik-eye"></i></span>&nbsp;&nbsp;
                <span onclick="editFlightStatus('.$Totalflights[$x]["iFlightId"].')"><i class="ik ik-edit-2"></i></span>&nbsp;&nbsp;
                <span onclick="editFlightStatus('.$Totalflights[$x]["iFlightId"].')"><i class="ik ik-trash"></i></span>&nbsp;&nbsp;
            </div>
        </td>
    </tr>
        
        ';

        $x++;
        $count++;
    }

           
 ?>
