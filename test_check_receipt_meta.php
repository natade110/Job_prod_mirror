<?php

	//yoes 20200720 -- sync ws codes with actual receipt bookno booknum
	include "db_connect.php";
	
	$sql = "
		
		SELECT 
			law.cid
			, law.year
			, law.lid
			, rem.meta_for
			-- , concat('c',rem.meta_for)			
			, rem.*
		FROM 
			receipt_meta rem
				join
					receipt re
					on
					rem.meta_rid = re.rid
				join
					payment pay
					on
					pay.rid = re.rid
				join
					lawfulness law
					on
					law.lid = pay.lid
		where
			meta_rid < 100000
			and
			meta_rid > 67706
			and
			rem.meta_for not in (
			
				select
					concat(p_lid, p_from, p_to)
					
				from
					lawful_33_principals l33
			
			)
			and
			rem.meta_for not in (
			
				select
					concat('c',p_lid, p_from, p_to)
					
				from
					lawful_35_principals l35
			
			)
		limit
			0, 100
	
	
	";
	
	$the_result = mysql_query($sql);
				
	while ($the_row = mysql_fetch_array($the_result)) {
		
		echo "<br>".$the_row[meta_for];
		
	}
	
	