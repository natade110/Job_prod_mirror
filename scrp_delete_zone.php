<?php

	include "db_connect.php";
	
	
	if(($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
	}else{
		exit();
	}
	
	$the_sql = "
				delete from zone_district
				where 
					zone_id = '$this_id'				
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	
	
	$the_sql = "
				delete from zones
				where 
					zone_id = '$this_id'				
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	
	$the_sql = "
				delete from zone_user
				where 
					zone_id = '$this_id'				
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	header("location: manage_zone_list.php");
	
	

?>