
<?php

    ini_set('display_errors', 1);
    date_default_timezone_set("Asia/Manila");

    //TRIKAROO AJAX_GET ALL DRIVERS LOCATION
     
    
    include_once('../webservice/wanderlust_config.php');
    include_once('../webservice/wanderlust_general_functions.php');

    $database = new Connection();

    $db = $database->openConnection();

    $starttDateTime = @date("Y-m-d")." 00:00:00";
    $endDateTime = @date("Y-m-d")." 23:59:59";

    $sql = "SELECT * FROM flight_booking WHERE vDateFlight BETWEEN '".$starttDateTime."'  AND  '".$endDateTime."' ORDER BY vDateFlight DESC";
    // $sql = "SELECT * FROM flight_booking WHERE CAST(vDateFlight AS DATE) = CAST( curdate() AS DATE) ";

    $statement = $db->query($sql);

    $flight = $statement->fetchAll();

    $x = 0;
    $count = 1;

    if(count($flight) > 0){
    	 while ($x < count($flight)) {


	        echo '
	       <li>
	        <div class="bullet bg-pink"></div>
	        <div class="time"></div>
	        <div class="desc">
	            <h3>'.$flight[$x]['vName'].'</h3>
	            <h4>'.$flight[$x]['vRoute'].'</h4>
	        </div>
	    </li>

	        ';

	        $x++;
	        $count++;
	    }

    }else{


	        echo '
	       <li>
	        <div class="bullet bg-pink"></div>
	        <div class="time">11am</div>
	        <div class="desc">
	            <h3>No flight today.</h3>
	            <h4></h4>
	        </div>
	    </li>

	        ';

    }
   
  
    
    
    

?>