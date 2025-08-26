<?php

	$host = "192.168.2.38";
	$db = "hire_project";
	$username = "root";
	$password = "password";
	$password = "N9Af0a596f7Cb";
	
	
	$connect = mysql_connect($host,$username,$password) ;
	mysql_select_db($db) or die(mysql_error()) ;
	mysql_query("SET NAMES 'utf8'");

?>