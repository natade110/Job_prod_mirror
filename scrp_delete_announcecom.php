<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$this_aid = doCleanInput($_GET["aid"]);
	}else{
		exit();
	}
	
	//table name
	
	$the_sql = "
	
				delete from announcecomp
				where 
					ACID = '$this_id'
				
				";
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	if(is_numeric($this_cid)){
		header("location: organization.php?id=$this_cid&delletter=delletter&focus=official");
	}elseif(is_numeric($this_aid) ){
		header("location: view_announce.php?id=$this_aid&delannounce=delannounce");
	}else{
		header("location: index.php");
	}

?>