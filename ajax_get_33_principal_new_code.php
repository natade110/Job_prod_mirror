<?php

	include "db_connect.php";
		
	$skip_html_head = 1;
	
	include "header_html.php";
	
	
	if($_GET[the_year]){
		$the_year  = $_GET[the_year]*1;
	}else{
		$the_year = "2019, 2020";
	}

	//try to create principals for all 33 using old codes...
	
	$m33_sql = "
	
		select
			*
		from
			lawful_employees a
				join
					lawfulness b
						on
						a.le_cid = b.cid
						and
						a.le_year = b.year
		where
			le_year in ( $the_year )
			and
			LID not in (
			
				select
					p_lid
				from
					lawful_33_principals
			
			)
		
			
	
	";
	
	/*
	le_cid in (			
		84061			
	)
	*/
	
	
	$m33_result = mysql_query($m33_sql);
	
	while($m33_row = mysql_fetch_array($m33_result)){
	
		//echo "<br>".$m33_row[le_id];
	
		get33Flows($m33_row[le_id]);
		
		//print_r($arr);
		
	
	}
	
	echo "principal new code done!";
	
	
	
	
?>