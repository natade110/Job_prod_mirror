<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
	}else{
		exit();
	}
	
	
	$the_sql = "
				delete from docrequestcompany
				where 
					RID = '$this_id'
				";
	
	mysql_query($the_sql);
	
	$the_sql = "
				delete from documentrequest
				where 
					RID = '$this_id'
				";
	
	mysql_query($the_sql);
	
	
	if($_GET["type"] == "hold"){
		header("location: holding_list.php");
	}else{
		header("location: letter_list.php");
	}
	
	

?>