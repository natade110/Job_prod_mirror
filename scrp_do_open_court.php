<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST); exit();
	include "session_handler.php";
	
	
	
	//yoes 20160104  --- do a close case thing
	$the_lid = doCleanInput($_POST["the_lid"]); 
	$the_cid = doCleanInput($_POST["the_cid"]); 
	$the_year = doCleanInput($_POST["the_year"]);
	
	
	
	if(in_array($sess_accesslevel, array(1,8))){
		$can_reopen_case = 1;
	}
	
	
	if($can_reopen_case ){
	
		$timestamp = date("YmdHis");
		
		$sql = "
		
				update
					lawfulness_meta
				set
					meta_for = concat(meta_for, '$timestamp')
				where
					meta_lid = '$the_lid'
					and
					meta_for in (
					
						'courted_flag'
						, 'courted_by'
						, 'courted_ip'
						, 'courted_date'
					
					)
					
				";
		
		//echo $sql; exit();
		
		mysql_query($sql);
		
		
		$sql = "
			replace into lawfulness_meta(
			
				meta_lid
				, meta_for
				, meta_value
			
			)values(
			
				'$the_lid'
				, 'courted_reopen_by$timestamp'
				, '$sess_userid'
			
			)
			,(
			
				'$the_lid'
				, 'courted_reopen_ip$timestamp'
				, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
			
			)
			,(
			
				'$the_lid'
				, 'courted_reopen_date$timestamp'
				, now()
			
			)
			";
	
	
		mysql_query($sql);
		
		
		
		
		header("location: organization.php?id=$the_cid&focus=lawful&reopen=reopen&year=$the_year");
		exit();
		
		
	}else{
		
		header("location: organization.php?id=$the_cid&focus=lawful&notreopen=notreopen&year=$the_year");
		exit();
			
	}
	
	
	
	

?>