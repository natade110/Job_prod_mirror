<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$return_id = doCleanInput($_GET["return_id"]);
	}else{
		exit();
	}
	
	$the_sql = "
				delete from files
				where 
					file_id = '$this_id'
				limit 1
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	//header("location: view_curator.php?curator_id=".doCleanInput($_GET["curator_id"]));	
	header("location: organization.php?id=".$return_id."&focus=lawful");
	
	

?>