<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$this_cid = doCleanInput($_GET["cid"]);
		$this_rid = doCleanInput($_GET["rid"]);
	}else{
		exit();
	}
	
	//table name
	
	$the_sql = "
	
				delete from docrequestcompany
				where 
					DID = '$this_id'
				
				";
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	if(is_numeric($this_cid)){
		header("location: organization.php?id=$this_cid&delletter=delletter&focus=official");
	}elseif(is_numeric($this_rid)){
		header("location: view_letter.php?id=$this_rid&delletter=delletter");
	}else{
		header("location: index.php");
	}

?>