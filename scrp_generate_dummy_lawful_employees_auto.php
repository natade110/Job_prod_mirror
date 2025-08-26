<?php

	include "db_connect.php";
	
	//first see how many lawfulness need fix for this
	$sql = "
		
		select
			a.cid
			, a.CompanyCode
			, b.year
			, b.Hire_NumofEmp
			, c.real_count
		from 
			company a
				join lawfulness b
					on a.cid = b.cid
				left outer join(
				
					select
						le_cid
						, le_year
						, count(*) as real_count
					from 
						lawful_employees
					group by
						le_cid
						, le_year
				) c
					on 
					b.cid = c.le_cid 
					and b.year = c.le_year
		where
			CompanyTypeCode != '14' 
			and CompanyTypeCode < 200 
			and 
			c.real_count is null
			and
			b.Hire_NumofEmp > 0
			and
			a.cid = 13
	
	";
	
	
	$target_result = mysql_query($sql);
	
	
	while($target_row = mysql_fetch_array($target_result)){
		
		//generate sql for insert dummy 34
		
		
		$table_name = "lawful_employees";
		
		//for each row -> Create extra input array
		$post_array = array(
		
					'le_name' =>'ไม่ระบุ'
					, 'le_cid' => $target_row[cid]
					, 'le_year'	=> $target_row[year]
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
		
		
		echo "<br>".$the_sql;
		mysql_query($the_sql) or die(mysql_error());
		
	}
	
	
?>