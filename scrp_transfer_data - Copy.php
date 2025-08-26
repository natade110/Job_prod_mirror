<?php

	include "db_connect.php";
	
	//parameters
	$the_lid = doCleanInput($_POST["the_lid"]);
	$the_cid = doCleanInput($_POST["the_cid"]);
	$the_year = doCleanInput($_POST["the_year"]);
	
	//echo "<br>".$the_lid;
	//echo "<br>".$the_cid;
	//echo "<br>".$the_year;
	
	//exit();
	
	
	
	//Transfer data from _company to real table

	////
	//1. start with number of employees 
	////
	
	$sql = "
			
			
				UPDATE 
					lawfulness a 
				JOIN 				
					lawfulness_company b 

				ON 
					a.LID = b.LID 
				
				SET 
					a.Employees = b.Employees
					, a.Hire_NumOfEmp = b.Hire_NumOfEmp
				
				
				where	
					a.LID = '$the_lid'
	
			";
			
	mysql_query($sql);
	
	
	/////////
	/////// number of employees moved - delete it
	/////////	
	//mysql_query("delete from lawfulness_company where LID = '$the_lid'");
	
	
	
	////
	//
	//	2# then do lawful employees
	//
	////
	
	
	$sql = "
	
			insert into
				lawful_employees(
				
				 	le_name
					,le_gender
					,le_age
					,le_code
					,le_disable_desc
					,le_start_date
					,le_wage
					,le_position
					,le_year
					,le_cid
					,le_wage_unit	
					
					,le_from_oracle			
				
				)
			select
				le_name
				,le_gender
				,le_age
				,le_code
				,le_disable_desc
				,le_start_date
				,le_wage
				,le_position
				,le_year
				,le_cid
				,le_wage_unit	
				
				,le_from_oracle
			from 				
				lawful_employees_company
			where	
				le_cid = '$the_cid'
				and
				le_year = '$the_year'
	
			";
	
	//echo $sql; exit();
	mysql_query($sql);
	
	
	/// delete the transferred info
	//mysql_query("delete from lawful_employees_company where le_cid = '$the_cid' and le_year = '$the_year'");
	
	
	////
	//
	//	3# then do CURATOR
	//
	////
	
	
	
	
	//echo $sql; exit();
	//mysql_query($sql);
	
	//First -> do parent curator...
	$sql = "select * from curator_company where curator_lid = '$the_lid' and curator_parent = '0'";
	
	//echo $sql; exit();
	
	$sub_result = mysql_query($sql);
	
	while ($sub_row = mysql_fetch_array($sub_result)) {			

		//$total_sub++;
		//add parent
		$sql = "
	
				insert into
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
					
					
					)
					select
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
					from 
						curator_company
					where
						curator_id = '".$sub_row["curator_id"]."'
	
				";
		
		//echo "<br>". $sql; exit();
		
		mysql_query($sql);
		
		//last inserted ID to "real" data
		$last_id = mysql_insert_id();		
		
		//after add parent, see if have child
		$child_sql = "select * from curator_company where curator_parent = '".$sub_row["curator_id"]."'";
		
		$child_result = mysql_query($child_sql);
		
		while ($child_row = mysql_fetch_array($child_result)) {		
			
			//if have any child....
			$sql = "
	
				insert into
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
					
					
					)
					select
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,'$last_id'
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
					from 
						curator_company
					where
						curator_id = '".$child_row["curator_id"]."'
	
				";
			
			//add child..	
			mysql_query($sql);
			
		}
		
	}
	
	
	/// delete the transferred info
	//mysql_query("delete from curator_company where curator_lid = '$the_lid'");
	
	//exit();
	
	
	//update flag so we know we've moved this company's data
	$sql = "update lawfulness_company set lawful_submitted = 2 where Year = '$the_year' and CID = '$the_cid'";
	mysql_query($sql);
	
	
	
	//that's that..........
	//do a redirect backkkkkkkk...........
	
	
	header("location: organization.php?id=$the_cid&focus=lawful&year=".$the_year."&auto_post=1");

	

?>