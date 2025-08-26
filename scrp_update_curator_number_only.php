<?php

	include "db_connect.php";
	
	
	if($_POST["LID"]){
		
		$this_id = $_POST["LID"]*1;
		$this_curator = deleteCommas($_POST["update_curator"])*1;
		
		$this_cid = doCleanInput($_POST["CID"]);
		$this_year = doCleanInput($_POST["this_year"]);
		
	}else{
		exit();
	}
	
	//first -- see how many lawful_employees are currently in the system
	
	$sql = "select count(*) from curator where curator_lid = '$this_id' and curator_parent = '0'";
	
	$curator_count = getFirstItem($sql);
	
	//echo "--".$curator_count; exit();
	
	
	//how many extra employees is needed?
	
	$extra_curator = $this_curator - $curator_count;
	
	//echo $extra_employees;
	
	
	for($i = 0; $i < $extra_curator; $i++){
		
		
		//query to add extra dummy employees
		
		$table_name = "curator";
		
		//for each row -> Create extra input array
		$post_array = array(
		
					'curator_name' =>'ไม่ระบุ'
					, 'curator_lid' => $this_id					
					, 'curator_idcard'	=> '0000000000000'//rand(100000, 999999).rand(1000000, 9999999)
					, 'curator_is_dummy_row' => 1					
					, 'curator_parent' => 0
					, 'curator_is_disable' => 1
					
					);
		
		$input_fields = array(
						
						'curator_name'						
						,'curator_lid'						
						,'curator_idcard'									
						,'curator_is_dummy_row'		
						, 'curator_parent'
						, 'curator_is_disable'

						);
		
		
		$the_sql = generateInsertSQL($post_array,$table_name,$input_fields,$special_fields,$special_values,"replace");
		
		//echo "<br>".$the_sql;
		mysql_query($the_sql);
		
		
		
	}
	
	
	//yoes 20151201 = what if you want to delete dummy data ..?
	//just do it
	if($extra_curator < 0){
		
		
		$sql = "
	
		delete from 
			curator 
		where 
			curator_lid = '$this_id' 
			and
			curator_is_dummy_row = 1
		limit 
			".($extra_curator *-1)."
			
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
	doAddModifyHistory($sess_userid,$this_cid,21,$this_id);
	
	//yoes 20160208
	resetLawfulnessByLID($this_id);
	
	if(is_numeric($this_cid)){
		header("location: organization.php?id=$this_cid&focus=dummy&year=$this_year&auto_post=1");
	}else{
		header("location: org_list.php");
	}
	
	exit();

?>