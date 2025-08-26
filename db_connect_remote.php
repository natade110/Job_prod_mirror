<?php



$host = "202.151.176.107";
	$db = "hire_project";
	$username = "root";
	$password = "";

///

$connect = mysql_connect($host,$username,$password) ;
mysql_select_db($db) or die(mysql_error()) ;
mysql_query("SET NAMES 'utf8'");


echo "dbconnect!";

?>
