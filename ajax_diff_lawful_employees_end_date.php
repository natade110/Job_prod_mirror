<?php

	include "db_connect.php";
	include "session_handler.php";
		
	$skip_html_head = 1;
	
	//standard vars
	$the_year = date("Y");
	
	//yoes 20200925
	//get lawful employees that in lawful company and didnt have end date (yet)
	
	if($_GET[the_year]){
		
		$the_year = $_GET[the_year];
		
	}
	
	//populate table
	$sql = "
		
		insert ignore into 
			lawful_employees_sso_end_date (
				le_id
				, le_code
				, le_origin_start_date
				, le_origin_end_date
				, le_checked_end_date
				, le_checked_datetime
				, le_origin_cid
				, le_origin_year
				, le_origin_name
				, le_origin_lawfulStatus
			)
		
		select
			le_id
			, le_code
			, le_start_date
			, le_end_date
			, le_end_date
			, '0000-00-00'
			, le_cid
			, le_year
			, le_name
			, b.lawfulStatus 
		from
			lawful_employees a
				join
					lawfulness b
					on
					a.le_cid = b.cid
					and
					a.le_year = b.year
					and
					b.lawfulstatus = 1
					and
					b.year = '$the_year'
					and
					a.le_end_date = '0000-00-00'
				join
					company c
						on
						b.cid = c.cid
						and CompanyTypeCode < 200
						and CompanyTypeCode != '14'
						and CompanyTypeCode != '07'
		where
			a.le_id not in (
			
				select
					le_id
				from
					lawful_employees_sso_end_date
			
			)
	
	";
	
	
	//dang or someone to do...
	//for eeach lawful_employees_sso_end_date where le_checked_datetime = '0000-00-00'
	//query to check sso for lastest 
	
	echo "$sql"; exit();
	
	//mysql_query($sql);
	
	echo "... synced sso done";