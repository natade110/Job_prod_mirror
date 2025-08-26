<?php
//$host = "10.0.116.14"; 
$host = "testing_db";
$db="hire_project";
/*$username = "dba";
$password = "db@dmin+";
*/
$username = "dba";
$password = "db@dmin-";

echo "Try connecting to: $host";
echo "Database: $db";

$connect = mysql_connect($host,$username,$password) ;
mysql_select_db($db) or die(mysql_error()) ;



echo "<br> DB connected succesfully!";

?>
