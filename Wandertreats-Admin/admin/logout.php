<?php
session_start();
$_SESSION = array();
session_destroy();
unset($_SESSION);
session_unset();
header('location:login.php');
exit();
?>