
<?php

    ini_set('display_errors', 1);
    date_default_timezone_set("Asia/Manila");

    //TRIKAROO AJAX_GET ALL DRIVERS LOCATION
     
    
    include_once('../webservice/wanderlust_config.php');
    include_once('../webservice/wanderlust_general_functions.php');

    $database = new Connection();

    $bookingRef = $_POST['id'];

    $db = $database->openConnection();

    $sql = "SELECT * FROM flight_booking WHERE vBookingRef = '".$bookingRef."'";

    $statement = $db->query($sql);

    $flight = $statement->fetchAll();


    echo '<div class="row">
            <div class="col-md-4 ml-auto mr-auto">
                <div class="input-wrap">
                   <h4>Departure</h4>
                   <p>'.$flight [0]['vOrigin'].'</br>'.$flight [0]['vDateFlight'].'</p>
                   
                </div>
            </div>

            <div class="col-md-4 ml-auto mr-auto">
                <div class="input-wrap">
                      <h4>Arrival</h4>
                     <p>'.$flight [0]['vDestination'].'</br>'.$flight [0]['vDateFlight'].'</p>
                   
                </div>
            </div>

             <div class="col-md-4 ml-auto mr-auto">
                <div class="input-wrap">
                      <h4>'.$flight [0]['vAirline'].'</h4>
                   <p></p>
                </div>
            </div>

        </div>

        <div class="row">

             <table id="advanced_table" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Birthday</th>
                    </tr>
                </thead>
                <tbody id="passengersTable">';
                    


    $sql = "SELECT fl.vBookingRef, fl.dDateBooked, pas.* FROM flight_booking as fl JOIN passengers as pas ON fl.iFlightId = pas.iFlightId WHERE vBookingRef = '".$bookingRef."' ";

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
        <td>' .  $passengers[$x]["dBirthday"] . '</td>
    </tr>
        
        ';

        $x++;
        $count++;
    }
  

  echo ' </tbody>
            </table>
        </div>';
    
    
    

?>