<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if(is_numeric($_POST["id"]) && is_numeric($_POST["year"]) && is_numeric($_POST["value"])){
		//$this_id = doCleanInput($_POST["id"]);
		$id = $_POST["id"]*1;
		$year = $_POST["year"]*1;
		$value = $_POST["value"]*1;
	}else{
		exit();
	}
	
	//table name
	
	$the_sql = "
	
				replace into company_employees_company(
					cid
					 , lawful_year
					 , employees
				)
				values(
				
					$id
					, $year
					, $value
				
				)
				
				";
	
	mysql_query($the_sql);
				
	echo trim($id.":".$year.":".$value);

?>