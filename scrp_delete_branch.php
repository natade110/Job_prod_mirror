<?php

	include "db_connect.php";
	
	
	if(($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
	}else{
		exit();
	}
	
	$the_sql = "
				delete from company_company
				where 
					CID = '$this_id'
				limit 1
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	header("location: organization.php&focus=general");
	
	

?>