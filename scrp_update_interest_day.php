<?php

	include "db_connect.php";
	
	
	if($_POST["LID"]){
		
		$this_id = $_POST["LID"]*1;
		
		$this_cid = doCleanInput($_POST["CID"]);
		$this_year = doCleanInput($_POST["this_year"]);
		
		//yoes 20160511
		//re-calculate this in case if this is school....		
		
		$company_row = getFirstRow("select * from company where cid = '$this_cid'");
		
		$the_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];	
		
		if($_POST[do_54_budget]){
			
			
			$meta_sql = "
								replace into
								lawfulness_meta
								(
									meta_lid
									, meta_for
									, meta_value
								)values(
									
									'".$this_id."'
									,'do_54_budget'
									,'".deleteCommas($_POST[do_54_budget])."'
								)
									
						";
			
			//echo "<br>".$meta_sql;
			
			mysql_query($meta_sql);
			
			
			
			$meta_sql = "
								replace into
								lawfulness_meta
								(
									meta_lid
									, meta_for
									, meta_value
								)values(
									
									'".$this_id."'
									,'do_54_budget_start_date'
									,'".$the_date."'
								)
									
						";
						
			//echo "<br>".$meta_sql;
			
			mysql_query($meta_sql);
			
			//exit();
		
		
		}else{
			
			$meta_sql = "
								delete from
									lawfulness_meta
								where
									meta_lid = '".$this_id."'
									and
									meta_for in (
									
										'do_54_budget'
										,'do_54_budget_start_date'
									)
									
									
						";
			
			mysql_query($meta_sql);
			
		}
		
		
	}else{
		exit();
	}
	
	//table name
	
	//echo $this_employess; exit();
	
	//yoes 20160208
	resetLawfulnessByLID($this_id);
				
	
	if(is_numeric($this_cid)){
		
		
		//yoes 20151208		
		header("location: organization.php?id=$this_cid&focus=lawful&year=$this_year&auto_post=1");
		
		
	}else{
		header("location: org_list.php");
	}

?>