<?php

	include "db_connect.php";
		
	$cur_date = date("Y-m-d");
	
	
	//(0) check if some WS come in from last 1 day	
	$sql = "
	
		SELECT 
			a.id
			, a.lid
			, b.log_id 
		FROM 
			bill_payment a
				left join
					generic_log b
						on
						a.id = b.log_meta
						and
						b.log_type = 'ktb_lawful_sync'
		WHERE 
			a.paymentStatus =1
			AND 
			DATE( a.paymentDate ) >= DATE_SUB( DATE( NOW( ) ) , INTERVAL 10 DAY ) 
			and
			b.log_id is null
			
	
	";
	
	$meta_result = mysql_query($sql);
	
	while($meta_row = mysql_fetch_array($meta_result)){			
		
		//
		//for each LID in the bill -> do reset lawfulness of that LID
		resetLawfulnessByLID($meta_row["lid"]);
		
		//then mark log as "synced"
		$sql_log = "
		
				insert into
					generic_log(
					
						log_type
						, log_date
						, log_meta
					
					)values(
					
						'ktb_lawful_sync'
						, now()
						, '".$meta_row["id"]."'
					
					)
		";
		
		mysql_query($sql_log);
		
		//that's it?
		
	}

?>