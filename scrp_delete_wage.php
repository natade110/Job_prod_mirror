<?php

	include "db_connect.php";
	
	
	if(($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
	}else{
		exit();
	}
	
	$the_sql = "
				delete from vars
				where 
					var_name = '$this_id'
				limit 1
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	header("location: import_org.php");
	
	

?>