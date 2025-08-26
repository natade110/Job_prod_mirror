<?php

	$script_array = array();
	
	//1
	array_push($script_array
				, "
				
					UPDATE 
						company a 
					sET 
						district_cleaned = trim(District)
					where
						district_cleaned = '' or district_cleaned is null
						and
						cid = '$district_to_clean_cid'
				
				");
	
	//		
	array_push($script_array
				, "
				
					update
						company
					set	
						district_cleaned = trim(REPLACE(district, 'อ.', ''))
					where
						district like 'อ.%'
						and
						cid = '$district_to_clean_cid'
				
				");

	//3
	array_push($script_array
				, "
					update
						company
					set	
						district_cleaned = trim(REPLACE(district, 'อำเภอ', ''))
					where
						district like 'อำเภอ%'	
						and
						cid = '$district_to_clean_cid'
				
				");
				
	//4
	array_push($script_array
				, "
					update
						company
					set	
						district_cleaned = trim(REPLACE(district, 'เขต', ''))
					where
						district like 'เขต%'	
						and
						cid = '$district_to_clean_cid'
				
				");

	//5
	array_push($script_array
				, "
					UPDATE 
						company a 
							JOIN provinces b ON a.province = b.province_id  
					sET 
						district_cleaned = concat('เมือง', province_name)
					where
						district = 'อ.เมือง'	
						and
						cid = '$district_to_clean_cid'
				
				");				

	//6
	array_push($script_array
				, "
					UPDATE 
						company a 
							JOIN provinces b ON a.province = b.province_id  
					sET 
						district_cleaned = concat('เมือง', province_name)
					where
						trim(district) = 'เมือง'		
						and
						cid = '$district_to_clean_cid'
				
				
				");	
				
	//7
	array_push($script_array
				, "
					
				UPDATE 
					company a 
						JOIN provinces b ON a.province = b.province_id  
				sET 
					district_cleaned = concat('เมือง', province_name)
				where
					trim(district_cleaned) = 'เมือง'	
					and
					cid = '$district_to_clean_cid'
				
				");		
				
	for($i=0;$i<count($script_array);$i++){
		mysql_query($script_array[$i]) or die(mysql_error());	
	}

?>