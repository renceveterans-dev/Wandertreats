<?php
//Start session
ob_start();
session_start();
//Check whether the session variable SESS_MEMBER_ID is present or not
if (!isset($_SESSION['user_id']) || (trim($_SESSION['user_id']) == "")) {
    header("location: login.php");
    exit();
}

echo "HERE : ".$_SESSION['user_id']."           jajajajahahaha";

$session_id=$_SESSION['user_id'];
ob_end_flush();
?>