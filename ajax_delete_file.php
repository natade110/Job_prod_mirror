<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if(is_numeric($_POST["id"]) && $_POST["yoes"]=="san"){
		$this_id = doCleanInput($_POST["id"]);
	}else{
		exit();
	}
	
	if(is_numeric($_POST["id"]) && !is_null($_POST["parenttable"])){
		$this_id = doCleanInput($_POST["id"]);
		$parent_table = $_POST["parenttable"];
		$the_sql = "		
			delete from $parent_table
			where
			file_id = '$this_id'
			";
			
			mysql_query($the_sql);
			error_log(mysql_error());
	}
	
	//table name
	
	$the_sql = "
	
				delete from files
				where 
					file_id = '$this_id'
				limit 1
				
				";
	
	mysql_query($the_sql);
	error_log(mysql_error());
				
	echo trim("$this_id");

?>