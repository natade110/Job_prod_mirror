<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
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
	
	header("location: organization.php?id=".doCleanInput($_GET["this_cid"])."&reg=reg&focus=official&year=".doCleanInput($_GET["this_year"])."");
	
	

?>