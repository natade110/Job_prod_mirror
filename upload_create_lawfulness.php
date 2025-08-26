<?php

	include "db_connect.php";
	include "session_handler.php";
	

	//(6) --- create dummy lawfulness
	//this should go to another script...?
	$the_lawful_year = "2017";
	$sql = "
	
		insert ignore into
			lawfulness(CID, year)	
		select
			cid, '$the_lawful_year'
		from
			company a
			, company_temp_all b
		where
			a.companyCode = b.CompanyCode
			and
			a.branchCode = b.branchCode
			and
			a.BranchCode < 1
	
	";
	mysql_query($sql) or die(mysql_error());
	
	
	//(7) also insert lawfulness for company that have BRANCH but no MAIN BRANCH in the file - but already have MAIN BRANCH in the database 
	$sql = "
	
	
		update	
			company a
			, lawfulness b
			, (
			
			SELECT companyCode as the_company_code, sum( employees ) as summed_employees
			FROM company_temp_all
			GROUP BY companyCode	
			
			)e
		set
			b.employees = summed_employees
		where
			a.cid = b.cid
			and
			a.companyCode = e.the_company_code
			
			and
			b.year = $the_lawful_year
			and
			branchcode < 1
			
			
	
	";
	
	mysql_query($sql) or die(mysql_error());
	
	
	
	echo "all-done for import lawfulness";
	
?>