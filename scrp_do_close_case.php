<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST); exit();
	include "session_handler.php";
	
	
	
	//yoes 20160104  --- do a close case thing
	$the_lid = doCleanInput($_POST["the_lid"]); 
	$the_cid = doCleanInput($_POST["the_cid"]); 
	$the_year = doCleanInput($_POST["the_year"]);
	
	$sql = "
			
			update
				lawfulness
			set
				close_case_date = now()
				, close_case_by = '$sess_userid'
				, close_case_ip = '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
			where
				lid = '$the_lid'
				
	
	
			";
	
	
	//echo $sql ;
	
	mysql_query($sql);
	
	
	header("location: organization.php?id=$the_cid&focus=lawful&closed=closed&year=$the_year"); 
	exit();
	
?>