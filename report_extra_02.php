<?php

include "db_connect.php";

$the_year = "2021";



if($_GET[mode] == 2){
	
	$mode = 2;
	$lawful_status_in = "1,2";
	
}elseif($_GET[mode] == 3){
	
	$mode = 3;
	$lawful_status_in = "1";
	
}else{
	
	$mode = 1;
	$lawful_status_in = "1,2,3";
	
	
}
//$lawful_status_in = "1";
?>

สถิติด้านการมีงานทำของคนพิการ 2564

<?php

	if($mode == 1){
		
		echo "ปฏิบัติครบ+ปฏิบัติไม่ครบ+ไม่เข้าข่าย";
		
	}elseif($mode == 2){
		
		echo "ปฏิบัติครบ+ปฏิบัติไม่ครบ";
		
	}elseif($mode == 3){
		
		echo "ปฏิบัติครบเท่านั้น";
		
	}

?>

<?php

	$sql = "
					
		SELECT 
			CASE
				WHEN companytypecode < 200 and companytypecode != 14 THEN 'ภาคเอกชน'
				WHEN companytypecode = 201 THEN 'รัฐวิสาหกิจ'
				WHEN companytypecode > 201 THEN 'ภาครัฐ'
				ELSE '??'
			END as companytype
			
		FROM   company
			   JOIN lawfulness
				 ON ( company.cid = lawfulness.cid
					  AND year = '$the_year' )
		WHERE  
				lawfulstatus in ($lawful_status_in)
			   
			   AND branchcode < 1  
			   
	
	";
	
	//echo $sql;
	$company_type_array = array();
	
	//$the_result = mysql_query($sql);	
	
	//while($the_row = mysql_fetch_array($the_result)){			
		
		
		//$company_type_array[$the_row[companytype]][the_count]++;
		
	//}
	
	//print_r($company_type_array);
	
	
	
	
	
	//echo "<br>". $the_35_sql;
	


	$disable_desc_array = array(
	
		"ความพิการทางการเห็น"
		,"ความพิการทางการได้ยินหรือสื่อความหมาย"
		,"ความพิการทางการเคลื่อนไหวหรือร่างกาย"
		,"ความพิการทางจิตใจหรือพฤติกรรม"
		,"ความพิการทางสติปัญญา"
		,"ความพิการทางการเีรียนรู้"
		,"ความพิการทางออทิสติก"
		,"ไม่ระบุ"
	
	);

?>
<table border=1>
	<tr>
		<td>
			
		</td>
		
		<td colspan=4>
			รัฐ 33
		</td>
		<td colspan=4>
			รัฐ 35
		</td>
		
		<td colspan=4>
			รัฐวิสาหกิจ 33
		</td>
		<td colspan=4>
			รัฐวิสาหกิจ 35
		</td>
		<td colspan=4>
			เอกชน 33
		</td>
		<td colspan=4>
			เอกชน 35
		</td>
	<tr>
	<tr>
		<td>
			ประเภทวามพิการ
		</td>
		
		<td>
			แห่ง
		</td>
		<td>
			ชาย
		</td>
		<td>
			หญิง
		</td>
		<td>
			ไม่ระบุเพศ
		</td>
		
		<td>
			แห่ง
		</td>
		<td>
			ชาย
		</td>
		<td>
			หญิง
		</td>
		<td>
			ไม่ระบุเพศ
		</td>
		
		<td>
			แห่ง
		</td>
		<td>
			ชาย
		</td>
		<td>
			หญิง
		</td>
		<td>
			ไม่ระบุเพศ
		</td>
		
		<td>
			แห่ง
		</td>
		<td>
			ชาย
		</td>
		<td>
			หญิง
		</td>
		<td>
			ไม่ระบุเพศ
		</td>
		
		<td>
			แห่ง
		</td>
		<td>
			ชาย
		</td>
		<td>
			หญิง
		</td>
		<td>
			ไม่ระบุเพศ
		</td>
		
		<td>
			แห่ง
		</td>
		<td>
			ชาย
		</td>
		<td>
			หญิง
		</td>
		<td>
			ไม่ระบุเพศ
		</td>
	<tr>
	
	<?php
		for($i=0; $i < count($disable_desc_array); $i++){			
			
			
			if($i == 0){
				//count le of cid
				$the_33_sql = "
					
					SELECT 
						
						le_cid
						, case
							when le_gender = 'm' then 'm'
							when le_gender = 'f' then 'f'
							else '?'
						end as le_gender
						, case
							when le_disable_desc != '' then le_disable_desc							
							else 'ไม่ระบุ'
						end as le_disable_desc
						, count(*) as the_count
					FROM 
						lawful_employees
					where
						
						le_year in ($the_year)
						and
							le_id not in (
							
								select
									meta_value
								from
									lawful_employees_meta
								where
									meta_for = 'child_of'
									
							
							)
							
						and
							le_id not in (
							
								select
									meta_leid
								from
									lawful_employees_meta
								where
									meta_for = 'is_extra_33'
									and
									meta_value = 1
							
							)
							
						and
						le_cid in (
						
							SELECT 
								
						 		company.cid
								
							FROM   company
								   JOIN lawfulness
									 ON ( company.cid = lawfulness.cid
										  AND year = '$the_year' )
							WHERE  
									lawfulstatus in ($lawful_status_in)
								  
								  *company_type_code_condition*
								  
								   AND 
										branchcode < 1  
						
						
						)
							
					group by
						le_cid
						, le_gender
						, le_disable_desc
						
				
				";
				
				
				//for รัฐ
				//and companytypecode > 201 
				
				$the_33_gov_sql = str_replace("*company_type_code_condition*", " and (companytypecode > 201 or companytypecode = 14) ", $the_33_sql);
				$the_33_ent_sql = str_replace("*company_type_code_condition*", " and companytypecode = 201 ", $the_33_sql);
				$the_33_priv_sql = str_replace("*company_type_code_condition*", " and (companytypecode < 200 and companytypecode != 14) ", $the_33_sql);
				
				//echo "<br>".$the_33_priv_sql ;
				
				$gov_33_cid_array = array();
				$gov_33_array = array();
				
				$ent_33_cid_array = array();
				$ent_33_array = array();
				
				$priv_33_cid_array = array();
				$priv_33_array = array();
		
				$the_result = mysql_query($the_33_gov_sql);					
				while($the_row = mysql_fetch_array($the_result)){			
										
					$gov_33_cid_array[$the_row[le_disable_desc]][$the_row[le_cid]] = 1;					
					$gov_33_array[$the_row[le_disable_desc]][$the_row[le_gender]][the_count] += $the_row[the_count];
					
				}
				
				$the_result = mysql_query($the_33_ent_sql);					
				while($the_row = mysql_fetch_array($the_result)){						
					
					$ent_33_cid_array[$the_row[le_disable_desc]][$the_row[le_cid]] = 1;					
					$ent_33_array[$the_row[le_disable_desc]][$the_row[le_gender]][the_count] += $the_row[the_count];
					
				}
				
				$the_result = mysql_query($the_33_priv_sql);					
				while($the_row = mysql_fetch_array($the_result)){						
					
					$priv_33_cid_array[$the_row[le_disable_desc]][$the_row[le_cid]] = 1;					
					$priv_33_array[$the_row[le_disable_desc]][$the_row[le_gender]][the_count] += $the_row[the_count];
					
				}
				
				
				//count curator of cid
				//parent
				$new_law_35_join_condition = "
						
								left join
				
						(
						
							SELECT distinct(meta_curator_id) as the_child_curator
									  FROM   curator_meta
									  WHERE  meta_for = 'child_of'
											 AND meta_value != 0
						
						) aa
						
						on 
						
						curator_id = the_child_curator
						
					left join
					
						(
						
									 SELECT distinct(meta_curator_id)
									  FROM   curator_meta
									  WHERE  meta_for = 'is_extra_35'
											 AND meta_value = 1
						
						) bb
						
						on 
						
						curator_id = meta_curator_id
				
				";
				
				
				$new_law_35_where_condition = "

						and (
								the_child_curator is null 
								or 
								the_child_curator = ''
							)
						and (
								meta_curator_id is null 
								or 
								meta_curator_id = ''
							)

				";
				
				
				$the_curator_maimad_sql = "
				
						select
							curator_lid
							,

								case
									when curator_disable_desc != '' then curator_disable_desc							
									else 'ไม่ระบุ'
								end 

									as curator_disable_desc
							
							
							, case
								when curator_gender = 'm' then 'm'
								when curator_gender = 'f' then 'f'
								else '?'
							end as curator_gender
							
							
							,count(*)  as the_count
						from 
							curator 
						
							$new_law_35_join_condition
							
						where 
						
							curator_lid in
							(
							
								SELECT 
										lawfulness.lid
										
									FROM   company
										   JOIN lawfulness
											 ON ( company.cid = lawfulness.cid
												  AND year = '$the_year' )
									WHERE  lawfulstatus in ($lawful_status_in)							  
										  
										  *company_type_code_condition*
										  
										   AND branchcode < 1  
										  
							
							)
							
							and 
							curator_parent = 0
							and
							curator_is_disable = 1
							
							$new_law_35_where_condition
							
						group by
							curator_lid
							, curator_disable_desc
							,case
								when curator_gender = 'm' then 'm'
								when curator_gender = 'f' then 'f'
								else '?'
							end
					
				
				
				";
				
				
				$the_35_gov_sql = str_replace("*company_type_code_condition*", " and (companytypecode >= 201 or companytypecode = 14) ", $the_curator_maimad_sql);
				$the_35_ent_sql = str_replace("*company_type_code_condition*", " and companytypecode = 201 ", $the_curator_maimad_sql);
				$the_35_priv_sql = str_replace("*company_type_code_condition*", " and (companytypecode < 200 and companytypecode != 14) ", $the_curator_maimad_sql);
				
				//echo $the_35_gov_sql;
				
				$gov_35_cid_array = array();
				$gov_35_maimad_array = array();
				
				$ent_35_cid_array = array();
				$ent_35_maimad_array = array();
				
				$priv_35_cid_array = array();
				$priv_35_maimad_array = array();
		
				$the_result = mysql_query($the_35_gov_sql);					
				while($the_row = mysql_fetch_array($the_result)){			
										
					$gov_35_cid_array[$the_row[curator_disable_desc]][$the_row[curator_lid]] = 1;					
					$gov_35_maimad_array[$the_row[curator_disable_desc]][$the_row[curator_gender]][the_count] += $the_row[the_count];
					
				}
				
				$the_result = mysql_query($the_35_ent_sql);					
				while($the_row = mysql_fetch_array($the_result)){			
										
					$ent_35_cid_array[$the_row[curator_disable_desc]][$the_row[curator_lid]] = 1;					
					$ent_35_maimad_array[$the_row[curator_disable_desc]][$the_row[curator_gender]][the_count] += $the_row[the_count];
					
				}
				
				
				$the_result = mysql_query($the_35_priv_sql);					
				while($the_row = mysql_fetch_array($the_result)){			
										
					$priv_35_cid_array[$the_row[curator_disable_desc]][$the_row[curator_lid]] = 1;					
					$priv_35_maimad_array[$the_row[curator_disable_desc]][$the_row[curator_gender]][the_count] += $the_row[the_count];
					
				}
				
				
				
				//next do 35 for disabled of curator user
				$the_curator_usee_sql = "
				
				
						select
							curator_lid
							
							, case
									when curator_disable_desc != '' then curator_disable_desc							
									else 'ไม่ระบุ'
								end 

									as curator_disable_desc
							


							, case
								when curator_gender = 'm' then 'm'
								when curator_gender = 'f' then 'f'
								else '?'
							end as curator_gender


							,count(*)  as the_count
						from
							curator
						where
							curator_parent in (
						
				
									select							
										curator_id						
										
									from 
										curator 
									
										$new_law_35_join_condition
										
									where 
									
										curator_lid in
										(
										
											SELECT 
													lawfulness.lid
													
												FROM   company
													   JOIN lawfulness
														 ON ( company.cid = lawfulness.cid
															  AND year = '$the_year' )
												WHERE  lawfulstatus in ($lawful_status_in)							  
													  
													  *company_type_code_condition*
													  
													   AND branchcode < 1  
													  
										
										)
										
										
										and
										curator_is_disable = 0
										
										$new_law_35_where_condition
							
							
							)
													
							
						group by
							curator_lid
							, curator_disable_desc
							,case
								when curator_gender = 'm' then 'm'
								when curator_gender = 'f' then 'f'
								else '?'
							end
						
					
				
				
				";
				
				//echo $the_curator_usee_sql;
				$the_35_gov_sql = str_replace("*company_type_code_condition*", " and (companytypecode > 201 or companytypecode = 14) ", $the_curator_usee_sql);
				$the_35_ent_sql = str_replace("*company_type_code_condition*", " and companytypecode = 201 ", $the_curator_usee_sql);
				$the_35_priv_sql = str_replace("*company_type_code_condition*", " and (companytypecode < 200 and companytypecode != 14) ", $the_curator_usee_sql);
				
				//echo $the_35_priv_sql;
				
				//$gov_35_cid_array = array();
				$gov_35_usee_array = array();
				
				//$ent_35_cid_array = array();
				$ent_35_usee_array = array();
				
				//$priv_35_cid_array = array();
				$priv_35_usee_array = array();
		
				$the_result = mysql_query($the_35_gov_sql);					
				while($the_row = mysql_fetch_array($the_result)){			
										
					$gov_35_cid_array[$the_row[curator_disable_desc]][$the_row[curator_lid]] = 1;					
					$gov_35_usee_array[$the_row[curator_disable_desc]][$the_row[curator_gender]][the_count] += $the_row[the_count];
					
				}
				
				$the_result = mysql_query($the_35_ent_sql);					
				while($the_row = mysql_fetch_array($the_result)){			
										
					$ent_35_cid_array[$the_row[curator_disable_desc]][$the_row[curator_lid]] = 1;					
					$ent_35_usee_array[$the_row[curator_disable_desc]][$the_row[curator_gender]][the_count] += $the_row[the_count];
					
				}
				
				
				$the_result = mysql_query($the_35_priv_sql);					
				while($the_row = mysql_fetch_array($the_result)){			
										
					$priv_35_cid_array[$the_row[curator_disable_desc]][$the_row[curator_lid]] = 1;					
					$priv_35_usee_array[$the_row[curator_disable_desc]][$the_row[curator_gender]][the_count] += $the_row[the_count];
					
				}
				
			
			}
			
	?>
	<tr>
		<td>
			<?php echo $disable_desc_array[$i];?>
		</td>
		
		
		<!-- GOv 33 ---->
		
		<td>
			<?php echo count($gov_33_cid_array[$disable_desc_array[$i]])*1;?>
		</td>
		<td>
			<?php echo $gov_33_array[$disable_desc_array[$i]][m][the_count]*1;?>
		</td>
		<td>
			<?php echo $gov_33_array[$disable_desc_array[$i]][f][the_count]*1;?>
		</td>
		<td>
			<?php echo $gov_33_array[$disable_desc_array[$i]]['?'][the_count]*1;?>
		</td>
		
		<!-- GOv 35 ---->
		<td>
			<?php echo count($gov_35_cid_array[$disable_desc_array[$i]])*1;?>
		</td>
		<td>
			<?php echo ($gov_35_maimad_array[$disable_desc_array[$i]][m][the_count]+$gov_35_usee_array[$disable_desc_array[$i]][m][the_count])*1;?>
		</td>
		<td>
			<?php echo ($gov_35_maimad_array[$disable_desc_array[$i]][f][the_count]+$gov_35_usee_array[$disable_desc_array[$i]][f][the_count])*1;?>
		</td>
		<td>
			<?php echo ($gov_35_maimad_array[$disable_desc_array[$i]]['?'][the_count]+$gov_35_usee_array[$disable_desc_array[$i]]['?'][the_count])*1;?>
		</td>
		
		
		
		<!-- ENT 33---->
		
		<td>
			<?php echo count($ent_33_cid_array[$disable_desc_array[$i]])*1;?>
		</td>
		<td>
			<?php echo $ent_33_array[$disable_desc_array[$i]][m][the_count]*1;?>
		</td>
		<td>
			<?php echo $ent_33_array[$disable_desc_array[$i]][f][the_count]*1;?>
		</td>
		<td>
			<?php echo $ent_33_array[$disable_desc_array[$i]]['?'][the_count]*1;?>
		</td>
		
		
		<!-- Ent 35 ---->
		<td>
			<?php echo count($ent_35_cid_array[$disable_desc_array[$i]])*1;?>
		</td>
		<td>
			<?php echo ($ent_35_maimad_array[$disable_desc_array[$i]][m][the_count]+$ent_35_usee_array[$disable_desc_array[$i]][m][the_count])*1;?>
		</td>
		<td>
			<?php echo ($ent_35_maimad_array[$disable_desc_array[$i]][f][the_count]+$ent_35_usee_array[$disable_desc_array[$i]][f][the_count])*1;?>
		</td>
		<td>
			<?php echo ($ent_35_maimad_array[$disable_desc_array[$i]]['?'][the_count]+$ent_35_usee_array[$disable_desc_array[$i]]['?'][the_count])*1;?>
		</td>
		
		
		<!-- PRIV 33---->
		
		<td>
			<?php echo count($priv_33_cid_array[$disable_desc_array[$i]])*1;?>
		</td>
		<td>
			<?php echo $priv_33_array[$disable_desc_array[$i]][m][the_count]*1;?>
		</td>
		<td>
			<?php echo $priv_33_array[$disable_desc_array[$i]][f][the_count]*1;?>
		</td>
		<td>
			<?php echo $priv_33_array[$disable_desc_array[$i]]['?'][the_count]*1;?>
		</td>
		
		<!-- Priv 35 ---->
		<td>
			<?php echo count($priv_35_cid_array[$disable_desc_array[$i]])*1;?>
		</td>
		<td>
			<?php echo ($priv_35_maimad_array[$disable_desc_array[$i]][m][the_count]+$priv_35_usee_array[$disable_desc_array[$i]][m][the_count])*1;?>
		</td>
		<td>
			<?php echo ($priv_35_maimad_array[$disable_desc_array[$i]][f][the_count]+$priv_35_usee_array[$disable_desc_array[$i]][f][the_count])*1;?>
		</td>
		<td>
			<?php echo ($priv_35_maimad_array[$disable_desc_array[$i]]['?'][the_count]+$priv_35_usee_array[$disable_desc_array[$i]]['?'][the_count])*1;?>
		</td>
		
		
		
	<tr>
	
	<?php
		}
	?>
	
</table>