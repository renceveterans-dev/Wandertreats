<?php 
$link = mysql_connect('renceveteransdev19047.ipagemysql.com', 'wanderlustph', 'K-anne050915$'); 
if (!$link) { 
	die('Could not connect: ' . mysql_error()); 
} 
echo 'Connected successfully'; 
mysql_select_db(wanderlustph); 
?>