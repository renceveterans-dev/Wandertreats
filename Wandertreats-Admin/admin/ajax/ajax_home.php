<?php

	ini_set('display_errors', 0);
	date_default_timezone_set("Asia/Manila");

	//TRIKAROO LOAD
     
     unset($where);
     unset($update);

    
    include_once('../webservice/wanderlust_config.php');
    include_once('../webservice/wanderlust_general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();
    


    $sql = "SELECT * FROM passengers";

    $statement = $db->query($sql);

    $passengers = $statement->fetchAll();

    $messageArray['passengers'] = count($passengers);




    $sql = "SELECT * FROM flight_booking WHERE vFlightStatus  = 'Active' AND  vDateFlight > CURDATE() AND YEAR(dDateBooked) = YEAR(CURRENT_DATE()) ";

    $statement = $db->query($sql);

    $Activeflights = $statement->fetchAll();

    $messageArray['totalActiveFlights'] = count($Activeflights);



    $sql = "SELECT * FROM flight_booking WHERE MONTH(dDateBooked) = MONTH(CURRENT_DATE()) AND YEAR(dDateBooked) = YEAR(CURRENT_DATE())";

    $statement = $db->query($sql);

    $Totalflights = $statement->fetchAll();

    $messageArray['totalFlights'] = count($Totalflights);



    $sql = "SELECT * FROM flight_booking WHERE vFlightStatus = 'Cancelled' AND MONTH(dDateBooked) = MONTH(CURRENT_DATE()) AND YEAR(dDateBooked) = YEAR(CURRENT_DATE())";

    $statement = $db->query($sql);

    $Cancelledflights = $statement->fetchAll();

    $messageArray['totalCancelledflights'] = count($Cancelledflights );


    $sql = "SELECT * FROM flight_booking WHERE vFlightStatus = 'Successful' AND MONTH(vDateFlight) = MONTH(CURRENT_DATE()) AND YEAR(dDateBooked) = YEAR(CURRENT_DATE())";

    $statement = $db->query($sql);

    $Successfulflights = $statement->fetchAll();


    $messageArray['totalSuccessfulflights'] = count($Successfulflights);


    $sql = "SELECT * FROM ferry_booking WHERE vFerryStatus  = 'Active'";

    $statement = $db->query($sql);

    $Activeferries = $statement->fetchAll();


    $sql = "SELECT * FROM ferry_booking";

    $statement = $db->query($sql);

    $Totalferries = $statement->fetchAll();


    $sql = "SELECT * FROM ferry_booking WHERE vFerryStatus = 'Cancelled'";

    $statement = $db->query($sql);

    $Cancelledferries = $statement->fetchAll();


    $sql = "SELECT * FROM ferry_booking WHERE vFerryStatus  = 'Successful'";

    $statement = $db->query($sql);

    $Successfulferries = $statement->fetchAll();


    $sql = "SELECT SUM(fEarnedAmount) as earned FROM ferry_booking";

    $statement = $db->query($sql);

    $Earnedferries = $statement->fetchAll();

    $sql = "SELECT SUM(fEarnedAmount) as earned FROM flight_booking";

    $statement = $db->query($sql);

    $Earnedflights = $statement->fetchAll();


    echo json_encode($messageArray);


           
 ?>
