<?php 

	include "db_connect.php";
	
	$sql = "
		select 
			a.cid
			, a.year 
			, a.lid
			, b.hire_numofemp
		from 
			company_to_reconcile_with_year a
				join lawfulness b
					on a.lid = b.lid			
			
		where 
			reconciled = 0 
		limit 
			0, 5000
		";
	
	//$reconcile_row = array();
	
	//$reconcile_row = getFirstRow($sql);
	
	//print_r($reconcile_row);
	
	$reconcile_result = mysql_query($sql);
	
	while($reconcile_row = mysql_fetch_array($reconcile_result)){
	
		$cid_to_reconcile = $reconcile_row["cid"];
		$lid_to_reconcile = $reconcile_row["lid"];
		$year_to_reconcile = $reconcile_row["year"];
		$hirenumofemp_to_reconcile = $reconcile_row["hire_numofemp"];
		
		
		
		//try get actual m33 for this cd & year
		$actual_m33 = getFirstItem("select count(*) from lawful_employees where le_cid = '$cid_to_reconcile' and le_year = '$year_to_reconcile' ");
		
		//echo "<br>select count(*) from lawful_employees where le_cid = '$cid_to_reconcile' and le_year = '$year_to_reconcile' ";
		
		if($actual_m33 < $hirenumofemp_to_reconcile){
			echo "<br>$cid_to_reconcile - $year_to_reconcile";
			
			
			
			///// add dummy m33
		
			//start variables
		
			$this_id = $lid_to_reconcile;
			$this_employess = $hirenumofemp_to_reconcile;
			
			$this_cid = $cid_to_reconcile;
			$this_year = $year_to_reconcile;
			
			//end variables
			
			echo "<br>$this_cid - $this_year ";
				
			
			
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
			
			
			
			
			
			
			
			
			
			
			
		}
		
		
		mysql_query("update company_to_reconcile_with_year set reconciled = 1 where lid = '".$lid_to_reconcile."'");
		
	}


	if(!$cid_to_reconcile){echo "all company reconciled successfully"; exit();}


	

?>

