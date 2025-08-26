<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if(is_numeric($_POST["district"]) && is_numeric($_POST["zone"]) && is_numeric($_POST["mode"])){
		//$this_id = doCleanInput($_POST["id"]);
		$district = $_POST["district"]*1;
		$zone = $_POST["zone"]*1;
		$mode = $_POST["mode"]*1;
		
	}else{
		exit();
	}
	
	//table name
	if($mode == 1){
		$the_sql = "
	
				replace into zone_district(
					zone_id
					 , district_area_code
				)
				values(
				
					$zone
					, $district
				
				)
				
				";
				
	}elseif($mode == 2){
		$the_sql = "
	
				delete from 
					zone_district
				where
				
				zone_id = $zone
				and
				district_area_code = $district
				
				";
				
	}
	
	mysql_query($the_sql);
				
	echo trim($zone.":".$user);

?>