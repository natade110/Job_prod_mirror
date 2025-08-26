<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
	}else{
		exit();
	}
	
	$the_sql = "
				update company
				set
				CompanyTypeCode = '14'
				where 
					CID = '$this_id'
				limit 1
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	header("location: import_org.php");
	
	

?>