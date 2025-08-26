<?php

	include "db_connect.php";
	
	
	if($_POST["LID"]){
		
		$this_id = $_POST["LID"]*1;
		
		$this_cid = doCleanInput($_POST["CID"]);
		$this_year = doCleanInput($_POST["this_year"]);
		
	}else{
		exit();
	}
	
	
	//dummy rows added -> now mark a "ตรวจสอบแล้ว" flag to lawfulness
	
	$sql = "
		update 
			lawfulness
		set
			verified_by = '$sess_userid'
			, verified_date = now()
		where
			lid = $this_id
		";
	
	
	mysql_query($sql);
	
	
	if(is_numeric($this_cid)){
		header("location: organization.php?id=$this_cid&focus=dummy&year=$this_year&auto_post=1");
	}else{
		header("location: org_list.php");
	}
	
	exit();

?>