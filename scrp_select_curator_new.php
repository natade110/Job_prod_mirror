<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$this_cid = doCleanInput($_GET["cid"]);
		$this_year = doCleanInput($_GET["year"]);
		
	}else{
		exit();
	}
	
	//echo $this_return_id; exit();
	
	//table name
	$table_name = "curator";
	
	$auto_post = 1;
	
	//for company, record this to company table instead
	if($sess_accesslevel == 4){
		
			$table_name = "curator_company";
					
			$auto_post = 0;
			
	}
	
	/////////////////////////////
	
	//yoes 20160105
	
	$selected_parent = getFirstItem("select curator_parent from $table_name where curator_id = '$this_id'");
	
	$sql = "delete from $table_name where curator_parent = '$selected_parent' and curator_id != '$this_id'";
	//echo $sql; exit();
	
	mysql_query($sql);
	
	
	header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year&auto_post=$auto_post");
	exit();

?>