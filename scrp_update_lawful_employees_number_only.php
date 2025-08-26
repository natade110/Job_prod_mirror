<?php

	include "db_connect.php";
	
	
	if($_POST["LID"]){
		
		$this_id = $_POST["LID"]*1;
		$this_employess = deleteCommas($_POST["update_employees"])*1;
		
		$this_cid = doCleanInput($_POST["CID"]);
		$this_year = doCleanInput($_POST["this_year"]);
		
	}else{
		exit();
	}
	
	//first -- see how many lawful_employees are currently in the system
	
	$sql = "select count(*) from lawful_employees where le_cid = '$this_cid' and le_year = '$this_year'";
	
	$le_count = getFirstItem($sql);
	
	//echo "--".$le_count;
	
	
	//how many extra employees is needed?
	
	$extra_employees = $this_employess - $le_count;
	
	//echo $extra_employees;
	
	
	for($i = 0; $i < $extra_employees; $i++){
		
		
		//query to add extra dummy employees
		
		$table_name = "lawful_employees";
		
		//for each row -> Create extra input array
		$post_array = array(
		
					'le_name' =>'ไม่ระบุ'
					, 'le_cid' => $this_cid
					, 'le_year'	=> $this_year
					, 'le_code'	=> ''//rand(100000, 999999).rand(1000000, 9999999)
					, 'le_is_dummy_row' => 1
					
					);
		
		$input_fields = array(
						
						'le_name'
						
						,'le_cid'						
						,'le_year'			
						
						,'le_code'			
						
						, 'le_is_dummy_row'

						);
		
		
		$the_sql = generateInsertSQL($post_array,$table_name,$input_fields,$special_fields,$special_values,"replace");
		
		//echo "<br>".$the_sql;
		mysql_query($the_sql);
		
		
		
	}
	
	
	//yoes 20151201 = what if you want to delete dummy data ..?
	//just do it
	if($extra_employees < 0){
		
		
		$sql = "
	
		delete from 
			lawful_employees 
		where 
			le_cid = '$this_cid' 
			and 
			le_year = '$this_year'
			and 
			le_is_dummy_row = 1
		limit 
			".($extra_employees *-1)."
			
			";
	
	
		mysql_query($sql);
		
		
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
	
	
	
	
	//yoes 20151201 -- also add this to modify history just in case..	
	doAddModifyHistory($sess_userid,$this_cid,20,$this_id);
		
	//yoes 20160208
	resetLawfulnessByLID($this_id);
	
	if(is_numeric($this_cid)){
		header("location: organization.php?id=$this_cid&focus=dummy&year=$this_year&auto_post=1");
	}else{
		header("location: org_list.php");
	}
	
	exit();

?>