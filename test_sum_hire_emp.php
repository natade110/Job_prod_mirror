<?php

	include "db_connect.php";
	
	//echo getHireNumOfEmpFromLid(2050535448);
	
	
	$sql = "
	
		select
			a.cid
			, a.lid
			, b.companynameThai
			, b.companycode
			, a.lawfulStatus
			, a.Year
			, a.hire_numofemp
			, IFNULL(c.real_count,0)
		from
			lawfulness a
				join
					company b
				on
					a.cid = b.cid
					
				
				left outer join (
				
					select
						le_cid
						, le_year
						, count(le_cid) as real_count
					from 
						lawful_employees
					group by
						le_cid
						, le_year
				) c
					on 
					a.cid = c.le_cid 
					and a.year = c.le_year
				
					
		where
			
			(
			
				real_count != a.hire_numofemp
				
				
				
				or
				(
					a.hire_numofemp > 0
					and
					IFNULL(real_count,0) = 0
				)
			
			)
			
			
			and
			(
			a.Year = '2020'
			)
			
			and
			a.cid = 4
			
			
	";
	
	
	$org_result = mysql_query($sql);
					
	//total records 
	$total_records = 0;

	while ($post_row = mysql_fetch_array($org_result)) {
		
		echo "<br>analysing cid " . $post_row[cid] ;
		
		if($post_row[hire_numofemp] != getHireNumOfEmpFromLid($post_row[lid])){
			
			
			echo "<br>cid " . $post_row[cid] . " not matched";
			
		}
		
	}
	
	
?><br>--end [process--