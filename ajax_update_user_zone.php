<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if(is_numeric($_POST["zone"]) && is_numeric($_POST["user"])){
		//$this_id = doCleanInput($_POST["id"]);
		$zone = $_POST["zone"]*1;
		$user = $_POST["user"]*1;
	}else{
		exit();
	}
	
	//table name
	
	$the_sql = "
	
				replace into user_zone(
					zone
					 , user
				)
				values(
				
					$zone
					, $user
				
				)
				
				";
	
	mysql_query($the_sql);
				
	echo trim($zone.":".$user);

?>