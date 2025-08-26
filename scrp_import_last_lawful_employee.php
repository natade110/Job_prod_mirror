<?php

	include "db_connect.php";
	
	//table name
	$table_name = "lawful_employees";
	$this_cid = doCleanInput($_POST["le_cid"]);
	$this_year = doCleanInput($_POST["le_year"]);
	$last_year = $this_year - 1;
	
	
	//yoes 20140910 -- add le_wage_unit and le_from_oracle
	$the_sql = "insert into lawful_employees(
							le_name
							,le_gender
							,le_age
							,le_code
							,le_disable_desc
							
							,le_start_date
							,le_end_date
							
							,le_wage
							,le_position 								
							,le_cid
							,le_year
							
							, le_wage_unit
							, le_from_oracle
							
						) select 
							le_name
							,le_gender
							,le_age
							,le_code
							,le_disable_desc
							
							,le_start_date
							,le_end_date
							
							,le_wage
							,le_position 								
							,le_cid
							,'$this_year'
							
							, le_wage_unit
							, le_from_oracle
							
						 from lawful_employees 
						where le_cid = '$this_cid'
						and le_year = '$last_year';
					 ";
	//echo $the_sql;exit();
	mysql_query($the_sql);
	
	
	
	
	
	//yoes 20160907 
	//also update hire_numofemp and lawful status
	$this_lid = getFirstItem("select * from lawfulness where cid = '$this_cid' and year = '$this_year'");
	resetLawfulnessByLID($this_lid);
	//
	
	header("location: organization.php?id=$this_cid&le=le&focus=lawful&year=".$_POST["le_year"]);

?>