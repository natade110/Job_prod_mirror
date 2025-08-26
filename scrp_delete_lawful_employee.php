<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$this_cid = doCleanInput($_GET["cid"]);
		$this_year = doCleanInput($_GET["year"]);
	}else{
		exit();
	}
	
	
	$table_name = "lawful_employees";
	$lawful_table_name = "lawfulness";
	
	$auto_post = 1;
	
	//for company, record this to company table instead
	if($sess_accesslevel == 4){
			$table_name = "lawful_employees_company";
			$lawful_table_name = "lawfulness_company";
			
			$auto_post = 0;
	}
	
	
	//yoes 20150118 --- extra table deletion
	if($_GET[is_extra_row]){
		$table_name = "lawful_employees_extra";
		$lawful_table_name = "lawfulness_company_extra"; //-- dummy table which doesnt existed
		
		$auto_post = 0;
	}
	
	//table name
	
	//yoes 20160104 -- do a full "change log" here
	doLawfulEmployeesFullLog($sess_userid, $this_id, basename($_SERVER["SCRIPT_FILENAME"]));
	//
	
	$the_sql = "
	
				delete from $table_name
				where 
					le_id = '$this_id'
				
				";
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	
	
	//yoes 20180220 -> also delete all related metas
	$the_sql = "
	
		delete
		from
		lawful_employees_meta
		where
		meta_leid = '$this_id'
		and
		meta_for in (
		
			'child_of'
		
		)
	
	";
	mysql_query($the_sql);
	
	
	$the_lid = getFirstItem("select lid from lawfulness where Year = '$this_year' and CID = '$this_cid'");
	
	
	//yoes 20220106 - more metas to delete	
	$the_sql = "
	
		delete
		from
		receipt_meta
		where
		meta_for LIKE '$the_lid%$this_id%'
		limit 1
	
	";
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	//sync this value to lawful employee		
	/*$hire_numofemp = getFirstItem("
									SELECT 
										count(*)
									FROM 
										$table_name
									where
										le_cid = '$this_cid'
										and le_year = '$this_year'");*/
										
	$hire_numofemp = getHireNumOfEmpFromLid($the_lid);
										
	//yoes 20160104 -- do a full "change log" here
	
	doLawfulnessFullLog($sess_userid, $the_lid, basename($_SERVER["SCRIPT_FILENAME"]));
	//
										
	mysql_query("update $lawful_table_name set Hire_NumofEmp = '$hire_numofemp' where Year = '$this_year' and CID = '$this_cid'");
	
	
	//also add modify stats
	//$history_sql = "insert into modify_history values('$sess_userid','$this_cid',now(), 3)";
	//mysql_query($history_sql);
	doAddModifyHistory($sess_userid,$this_cid,3,$the_lid);
	
	
	
	
	//yoes 20160208
	resetLawfulnessByLID($the_lid);
	
	
	if(is_numeric($this_cid)){
	
		if($this_year >= 2013 || $is_2013){
			header("location: organization.php?id=$this_cid&delle=delle&focus=lawful&year=$this_year&auto_post=$auto_post");
		}else{
			//yoes 20151222
			//header("location: organization.php?id=$this_cid&delle=delle&focus=lawful&year=$this_year");
			header("location: organization.php?id=$this_cid&delle=delle&focus=lawful&year=$this_year&auto_post=$auto_post");
		}
		
	}else{
		header("location: org_list.php");
	}

?>