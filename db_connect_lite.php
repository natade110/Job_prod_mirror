<?php

	//echo $import_sql;
	//commmit this to .... PROD or TEST
	if ($server_ip == "127.0.0.1" || $server_ip == "::1"){	
		$host = "localhost";
		$db = "hire_project";
		
		$username = "sanroku";
		$password = "qwerty789";
	}else{
		//produciton
		/**/
		$host = "production_db"; 
		$db="hire_project";
		$username = "dba";
		$password = "iI5DLV3f7TdJY@3D";
		
	}
	$connect_lite = mysql_connect($host,$username,$password) ;
	mysql_select_db($db) or die(mysql_error()) ;
	mysql_query("SET NAMES 'utf8'");

?>