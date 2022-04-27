<?php
	//RENCEVTERANS 12/03/2021
    header('Content-type: application/json');
    ini_set('display_errors',1);
    include_once('general_functions.php');

    $data['title'] = "Sample Notification";
    $data['description'] = "Sample notification Description.";
    //NOTIFCATION FOREGROUND
    $data['activity'] = "AUTO_LOGOUT";
    $data['message'] = "Sample notification message";

    notify("User", "6", $data);


?>