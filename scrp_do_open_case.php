<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST); exit();
	include "session_handler.php";
	
	
	
	//yoes 20160104  --- do a close case thing
	$the_lid = doCleanInput($_POST["the_lid"]); 
	$the_cid = doCleanInput($_POST["the_cid"]); 
	$the_year = doCleanInput($_POST["the_year"]);
	$the_reopen_case_password = doCleanInput($_POST["reopen_case_password"]); //new asof 20160118
	
	//
	if($sess_accesslevel != 1){
		
		//non admin needs a password for unlock
		//see if have password
		//$can_reopen_case = getFirstItem("select count(*) from users where reopen_case_password = '$the_reopen_case_password' and user_id = '$sess_userid' ");
		$can_reopen_case = getFirstItem("select count(*) from users where reopen_case_password = '$the_reopen_case_password'  ");
	}elseif($sess_accesslevel == 1){
		$can_reopen_case = 1;
	}
	
	
	if($can_reopen_case ){
		$sql = "
				
				update
					lawfulness
				set
					reopen_case_date = now()
					, reopen_case_by = '$sess_userid'
					, reopen_case_ip = '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'				
				where
					lid = '$the_lid'
					
				";
		
		
		
		mysql_query($sql);
		
		header("location: organization.php?id=$the_cid&focus=lawful&reopen=reopen&year=$the_year");
		exit();
	}else{
		
		header("location: organization.php?id=$the_cid&focus=lawful&notreopen=notreopen&year=$the_year");
		exit();
			
	}
	
	
	
	

?>