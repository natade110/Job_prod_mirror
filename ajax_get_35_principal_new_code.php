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
	
	$m35_sql = "
	
		select
			*
		from
			curator a
				join
					lawfulness b
						on
						a.curator_lid = b.lid
						
		where
			year in ( $the_year )
			
			and
			curator_parent = 0
						
			and
				curator_id not in (
				
					select
						meta_curator_id
					from
						curator_meta
					where
						meta_for = 'is_extra_35'
						and
						meta_value = 1
				
				)
			
			and
			LID not in (
			
				select
					p_lid
				from
					lawful_35_principals
			
			)
			
	
	";
	
	/*
	le_cid in (			
		84061			
	)
	*/
	
	
	$m35_result = mysql_query($m35_sql);
	
	while($m35_row = mysql_fetch_array($m35_result)){
	
		//echo "<br>".$m35_row[le_id];
	
		get35Flows($m35_row[curator_id]);
		
		//print_r($arr);
		
	
	}
	
	echo "principal new code done!";
	
	
	
	
?>