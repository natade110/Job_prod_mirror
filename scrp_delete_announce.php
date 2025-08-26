<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
	}else{
		exit();
	}
	
	$the_sql = "
				delete from announcecomp
				where 
					AID = '$this_id'
				";
				
	mysql_query($the_sql);
	
	$the_sql = "
				delete from announcement
				where 
					AID = '$this_id'
				";
	
	mysql_query($the_sql);
	
	header("location: announce_list.php");
	
	

?>