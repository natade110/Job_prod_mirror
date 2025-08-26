<?php

	include "db_connect.php";
		
	
	//what is current date
	$cur_date = date("Y-m-d");
	
	//echo $cur_date;
	
	
	//(0) check if need to do this processes
	//doing so by checking if we have "minus one day from now" records in database
	$sql = "
	
		select
			count(*)
		from
			company_snapshot
		where
			DATE(snapshot_date) = DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) 	
			
	
	";
	
	//echo $sql; exit();
	
	$still_have_records = getFirstItem($sql);
	
	
	
	
	
	if($still_have_records){
		
		//do nothing if still have yesterday's record on file	
		
	}else{
		
		//do the recoring processes
	
		//(1) delete minus three days
		
		$sql = "
			
			delete
				
			from
				company_snapshot
			where
				snapshot_date = DATE_SUB(DATE(NOW()), INTERVAL 3 DAY) 			
		
		";
				
		//echo $sql;
		mysql_query($sql);
		
		
		
		$sql = "
			
			delete
				
			from
				lawfulness_snapshot
			where
				snapshot_date = DATE_SUB(DATE(NOW()), INTERVAL 3 DAY) 			
		
		";
				
		//echo $sql;
		mysql_query($sql);
		
		
		//(2) move today into minus one day
		
		
		$sql = "		
			
			insert into company_snapshot(
			
				CID
				,BranchCode	
				,CompanyCode
				,CompanyNameThai
				,CompanyTypeCode
				,Province
				,LastModifiedDateTime	
				,snapshot_date
				
			)
			
			select
			
				CID
				,BranchCode	
				,CompanyCode
				,CompanyNameThai
				,CompanyTypeCode
				,Province
				,LastModifiedDateTime	
				,DATE_SUB(NOW(), INTERVAL 1 DAY)
				
			from
				company
		
		
		";
		
		
		//echo $sql;
		mysql_query($sql);
		
		
		//lawfulness here
		$sql = "
		

			insert into lawfulness_snapshot(
				LID
				,CID
				,Year
				,LawfulStatus
				,Conc_status
				,Employees
				,Hire_NumofEmp
				,Hire_status	
				,pay_status
				,snapshot_date
			
			)
			
			select
				LID
				,CID
				,Year
				,LawfulStatus
				,Conc_status
				,Employees
				,Hire_NumofEmp
				,Hire_status	
				,pay_status
				,DATE_SUB(NOW(), INTERVAL 1 DAY)
				
			from
				lawfulness	
		
		";
		
		mysql_query($sql);
		
	}

?>