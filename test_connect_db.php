<?php
$host = "192.168.3.110"; 
$db="hire_project";
$username = "root";
$password = "Next@now@nep";

echo "Try connecting to: $host";
echo "Database: $db";

$connect = mysql_connect($host,$username,$password) ;
mysql_select_db($db) or die(mysql_error()) ;



echo "<br> DB connected succesfully!";

?>
