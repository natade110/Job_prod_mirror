<?php

	include "db_connect.php";
	
	
	if($_POST["user_name"]){
		//$this_id = doCleanInput($_POST["id"]);
		$user_name = doCleanInput($_POST["user_name"]);
		
	}else{
		exit();
	}
	
				
	echo trim(getFirstItem("select count(*) from users where user_name = '$user_name'"));

?>