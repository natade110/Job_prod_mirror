<?php

include "db_connect.php";

$the_year = "2020";

$the_mode = "1";

$lawful_status_in = "0,1,2";
//$lawful_status_in = "1";

//$sql = "select * from provinces where province_code > 0 order by province_code asc limit 0,10";
$sql = "select * from provinces where province_code > 0 order by province_code asc";
$the_result = mysql_query($sql);


?>

การปฏิบัติตามกฎหมาย <?php echo $the_year+543;?> - <?php if($the_mode == 1){echo "ปฏิบัติตามกฎหมาย+ปฏิบัติไม่ครบ+ไม่ปฏิบัติ";} ?>
<table border=1>
	<tr>
		<td>
		
		</td>
		<td>
		
		</td>
		<td>
		
		</td>
		<td>
		
		</td>
		<td>
		
		</td>
		<td>
		
		</td>
		<td>
		
		</td>
		<td colspan=2>
		 33+34
		</td>
		<td colspan=2>
		33+35
		</td>
		<td colspan=2>
		34+35
		</td>
		<td colspan=3>
		33+34+35
		</td>
		
		<td colspan=3>
			รายได้จากสถานประกอบการ - ดึงข้อมูลการจ่ายเงินจากข้อมูลใบเสร็จทั้งหมดในระบบ
		</td>
		
		<td colspan=2>
			เพิกเฉย ไม่ปฏิบัติ
		</td>
		<td colspan=2>
			ปฏิบัติไม่ครบ
		</td>
		
		
	</tr>
	<tr>
		<td>
		code
		</td>
		<td>
		จังหวัด
		</td>
		<td>
		จำนวนลูกจ้าง
		</td>
		<td>
		อัตราส่วนที่ต้องรับ
		</td>
		<td>
		ม33
		</td>
		
		<td>
		ม34
		</td>
		<td>
		ม35
		</td>
		<td>
		ม33 
		</td>
		<td>
		ม34
		</td>
		<td>
		ม33 
		</td>
		<td>
		ม35 
		</td>
		<td>
		ม34
		</td>
		<td>
		ม35 
		</td>
		<td>
		ม33 
		</td>
		<td>
		ม34
		</td>
		<td>
		ม35
		</td>
		
		<td>
			(ม34/คน - รวมจ่ายเกิน)
		</td>
		<td>
			(ม34/บาท - รวมดอกเบี้ย)
		</td>
		<td>
			(เงินแทน ม33+ม35/บาท  - รวมดอกเบี้ย)
		</td>
		
		
		
		<td>
			เพิกเฉย-ไม่ปฏิบัติ(คน)
		</td>
		<td>
			เพิกเฉย-ไม่ปฏิบัติ (เงินต้น ม34/บาท)
		</td>
		
		<td>
			ปฏิบัติไม่ครบ(ขาดจ่ายเงินต้น ม34/บาท)
		</td>
		<td>
			ปฏิบัติไม่ครบ(ขาดจ่ายแทน33+35/บาท)
		</td>
		<td>
			
		</td>
		
		
	</tr>
	<?php
	
	while($the_province_row = mysql_fetch_array($the_result)){			
		//
		//$output_values[$meta_row[meta_for]] = (doCleanOutput($meta_row[meta_value]));		
		

	
	?>
		<tr>
			<td>
				<?php 
					echo $the_province_row[province_code]; 
					
					$sql = "
					
						SELECT 
							sum(lawfulness.Employees) as the_sum
							, sum(
							
								CASE
									WHEN lawfulness.Employees < 100 THEN 0	
									WHEN lawfulness.Employees % 100 <= 50 THEN floor(lawfulness.Employees/100)										
									ELSE ceil(lawfulness.Employees/100)
								END
							
							) as the_sum_ratio
							
						FROM   company
							   JOIN lawfulness
								 ON ( company.cid = lawfulness.cid
									  AND year = '$the_year' )
						WHERE  lawfulstatus in ($lawful_status_in)
							   AND companytypecode != '14'
							   AND companytypecode < 200
							   AND branchcode < 1  
							   and
							   province = '".$the_province_row[province_id]."'
					
					";
					
					//echo "<br>".$sql;
					
					$the_array = getFirstRow($sql);
					
					?>
			</td>
			<td>
				<?php echo $the_province_row[province_name]; ?>
			</td>
			<td>
				<?php 
					
					//จำนวนลูกจ้าง 
					/*
					ไม่ทำตามกฏหมาย 0
					ทำตามกฏหมาย 1
					ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน 2
					ไม่เข้าข่ายจำนวนลูกจ้าง 3
					*/ 
					
					
					
					echo $the_array[the_sum]*1;

				?>
			</td>
			<td>
				<?php
					echo $the_array[the_sum_ratio];
				?>
			</td>
			
			
			<td>
				<?php
					//33 only
					
					$main_column_sql = "
					
						SELECT 
							sum(lawfulness.Hire_NumofEmp) as the_sum
							
						FROM   company
							   JOIN lawfulness
								 ON ( company.cid = lawfulness.cid
									  AND year = '$the_year' )
						WHERE  lawfulstatus in ($lawful_status_in)							  
							   AND companytypecode != '14'
							   AND companytypecode < 200
							   AND branchcode < 1  
							   and
							   province = '".$the_province_row[province_id]."'
					
					";
					
					$column_sql = $main_column_sql . " 
						and (Hire_status = 1 and pay_status = 0 and Conc_status = 0)
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					echo $the_column_array[the_sum]*1;
					
				?>
			</td>
			
			<td>
				
				<?php
				
					//34 of partials
					$main_34_column_sql = "
					
						SELECT 
						
							sum(floor(the_sum_paid_amount/112420)) as the_sum
							
							
						FROM   company
							   JOIN lawfulness
								 ON ( company.cid = lawfulness.cid
									  AND year = '$the_year' )
								
								
								join
								
								(
								
									select
										payment.LID
										, sum(receipt.Amount-coalesce(sum_x_for,0)) as the_sum_paid_amount
									from
										payment 
											
										join
											receipt
											on
											receipt.RID = payment.RID
											
										left join
										
											
											
											(
												select
													meta_rid
													, sum(meta_value) as sum_x_for
												from
													receipt_meta
												where
													meta_for like '3%_for-%-amount'
													
												group by
													meta_rid
												
											) bbb
											on
											receipt.rid = bbb.meta_rid
												
												
										where
											receipt.is_payback = 0
											
										group by
											payment.LID
										
								) aaa
								
									on
									aaa.lid = lawfulness.lid
									
									
						WHERE  lawfulstatus in ($lawful_status_in)							  
							   AND companytypecode != '14'
							   AND companytypecode < 200
							   AND branchcode < 1  
							   and
							   province = '".$the_province_row[province_id]."'
							 
					
					";
					
					$column_sql = $main_34_column_sql . " 
						and (Hire_status = 0 and pay_status = 1 and Conc_status = 0)
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					echo $the_column_array[the_sum]*1;
					
				
					//34 thing
					/*$the_34_column_sql = "
					
						SELECT 
							sum(lawfulness.Employees) as the_sum
							, sum(
							
								CASE
									WHEN lawfulness.Employees < 100 THEN 0	
									WHEN lawfulness.Employees % 100 <= 50 THEN floor(lawfulness.Employees/100)										
									ELSE ceil(lawfulness.Employees/100)
								END
							
							) as the_sum
							
						FROM   company
							   JOIN lawfulness
								 ON ( company.cid = lawfulness.cid
									  AND year = '$the_year' )
						WHERE  lawfulstatus in (1)
							   AND companytypecode != '14'
							   AND companytypecode < 200
							   AND branchcode < 1  
							   and
							   province = '".$the_province_row[province_id]."'
						
						
						
					";
					
					$the_34_sql = $the_34_column_sql . " 
						and (Hire_status = 0 and pay_status = 1 and Conc_status = 0)
					";
					
					$the_column_array = getFirstRow($the_34_sql);
					
					echo " | ". $the_column_array[the_sum];*/
				
				?>
			
			</td>
			<td>
				
				<?php
					
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
				
					
					$the_35_sql = "
												
						select count(*) as the_sum
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
									   AND companytypecode != '14'
									   AND companytypecode < 200
									   AND branchcode < 1  
									   and
									   province = '".$the_province_row[province_id]."'
								
										*the_condition*
							
							)
							
							and 
							curator_parent = 0
							and
							curator_is_disable in (0,1)
							
							$new_law_35_where_condition
												
						
					";
					
					/*$column_sql = $the_35_sql . " 
						and (Hire_status = 0 and pay_status = 0 and Conc_status = 1)
					";*/
					
					$column_sql = str_replace("*the_condition*", " and (Hire_status = 0 and pay_status = 0 and Conc_status = 1) ", $the_35_sql) ;
					
					//echo $column_sql;
					
					$the_35_array = getFirstRow($column_sql);
					
					echo $the_35_array[the_sum]*1;
				
				?>
			
			</td>
			
			<td>
				<?php
					//33+34 only
					//the 33
					
					$column_sql = $main_column_sql . " 
						and (Hire_status = 1 and pay_status = 1 and Conc_status = 0)
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					echo $the_column_array[the_sum]*1;
					
				?>
			</td>
			<td>
				<?php
					
					
					
					
					//echo $the_34_of_3334_sql;
					
					$column_sql = $main_34_column_sql . " 
						and (Hire_status = 1 and pay_status = 1 and Conc_status = 0)
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					
					echo $the_column_array[the_sum]*1;
					
				?>
			</td>
			<td>
				<?php
					//33+35 only
					
					$column_sql = $main_column_sql . " 
						and (Hire_status = 1 and pay_status = 0 and Conc_status = 1)
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					echo $the_column_array[the_sum]*1;
					
				?>
			</td>
			<td>
				
				<?php
										
					
					$column_sql = str_replace("*the_condition*", " and (Hire_status = 1 and pay_status = 0 and Conc_status = 1) ", $the_35_sql) ;
					
					//echo $column_sql;
					
					$the_35_array = getFirstRow($column_sql);
					
					echo $the_35_array[the_sum]*1;
				?>
			
			</td>
			
			
			<td>
				<?php
					//34+35 
					
					$column_sql = $main_34_column_sql . " 
						and (Hire_status = 0 and pay_status = 1 and Conc_status = 1)
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					
					echo $the_column_array[the_sum]*1;
					
					
				?>
			</td>
			
			<td>
				<?php
										
					
					$column_sql = str_replace("*the_condition*", " and (Hire_status = 0 and pay_status = 1 and Conc_status = 1) ", $the_35_sql) ;
					
					//echo $column_sql;
					
					$the_35_array = getFirstRow($column_sql);
					
					echo $the_35_array[the_sum]*1;
				?>
			</td>
			
			<td>
				<?php
					//33+34+35 only
					
					$column_sql = $main_column_sql . " 
						and (Hire_status = 1 and pay_status = 1 and Conc_status = 1)
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					echo $the_column_array[the_sum]*1;
					
				?>
			</td>
			<td>
				<?php
				
					$column_sql = $main_34_column_sql . " 
						and (Hire_status = 1 and pay_status = 1 and Conc_status = 1)
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					
					echo $the_column_array[the_sum]*1;
				
				?>
			</td>
			<td>
				<?php
										
					
					$column_sql = str_replace("*the_condition*", " and (Hire_status = 1 and pay_status = 1 and Conc_status = 1) ", $the_35_sql) ;
					
					//echo $column_sql;
					
					$the_35_array = getFirstRow($column_sql);
					
					echo $the_35_array[the_sum]*1;
				?>
			</td>
			
			<td>
			
				<?php
				
					$column_sql = $main_34_column_sql . " 
						
					";
					
					$the_column_array = getFirstRow($column_sql);
					
					echo $the_column_array[the_sum]*1;
				
				?>
			
			</td>
			
			<td>
				<?php
				
					$main_34_amount_sql = "
					
						SELECT 
						
							sum(the_sum_paid_amount) as the_sum
							
							
						FROM   company
							   JOIN lawfulness
								 ON ( company.cid = lawfulness.cid
									  AND year = '$the_year' )
								
								
								join
								
								(
								
									select
										payment.LID
										, sum(receipt.Amount-coalesce(sum_x_for,0)) as the_sum_paid_amount
									from
										payment 
											
										join
											receipt
											on
											receipt.RID = payment.RID
											
										left join
										
											
											
											(
												select
													meta_rid
													, sum(meta_value) as sum_x_for
												from
													receipt_meta
												where
													meta_for like '3%_for-%-amount'
													
												group by
													meta_rid
												
											) bbb
											on
											receipt.rid = bbb.meta_rid
												
										where
											receipt.is_payback = 0		
										
											
										group by
											payment.LID
										
								) aaa
								
									on
									aaa.lid = lawfulness.lid
									
									
						WHERE  lawfulstatus in ($lawful_status_in)							  
							   AND companytypecode != '14'
							   AND companytypecode < 200
							   AND branchcode < 1  
							   and
							   province = '".$the_province_row[province_id]."'
							 
					
					";
				
					$the_column_array = getFirstRow($main_34_amount_sql);
					
					echo $the_column_array[the_sum]*1;
				
				
				?>
			
			</td>
			
			<td>
				<?php
				
					$main_34_amount_sql = "
					
						SELECT 
						
							sum(the_sum_paid_amount) as the_sum
							
							
						FROM   company
							   JOIN lawfulness
								 ON ( company.cid = lawfulness.cid
									  AND year = '$the_year' )
								
								
								join
								
								(
								
									select
										payment.LID
										, sum(coalesce(sum_x_for,0)) as the_sum_paid_amount
									from
										payment 
											
										join
											receipt
											on
											receipt.RID = payment.RID
											
										left join
										
											
											
											(
												select
													meta_rid
													, sum(meta_value) as sum_x_for
												from
													receipt_meta
												where
													meta_for like '3%_for-%-amount'
													
												group by
													meta_rid
												
											) bbb
											on
											receipt.rid = bbb.meta_rid
												
									where
										receipt.is_payback = 0
										
											
										group by
											payment.LID
										
								) aaa
								
									on
									aaa.lid = lawfulness.lid
									
									
						WHERE  lawfulstatus in ($lawful_status_in)							  
							   AND companytypecode != '14'
							   AND companytypecode < 200
							   AND branchcode < 1  
							   and
							   province = '".$the_province_row[province_id]."'
							 
					
					";
				
					$the_column_array = getFirstRow($main_34_amount_sql);
					
					echo $the_column_array[the_sum]*1;
				
				
				?>
			
			</td>
			
			<td>
				<?php 
					//echo $the_province_row[province_code]; 
					
					$sql = "
					
						SELECT 
							sum(
							
								greatest(
								
									CASE
										WHEN lawfulness.Employees < 100 THEN 0	
										WHEN lawfulness.Employees % 100 <= 50 THEN floor(lawfulness.Employees/100)										
										ELSE ceil(lawfulness.Employees/100)
									END
									
									
									- Hire_NumofEmp 
									
									
									- coalesce(the_curator_sum,0)
								
								, 0)
							
							)  as the_sum_ratio
							
						FROM   company
							   JOIN lawfulness
								 ON ( company.cid = lawfulness.cid
									  AND year = '$the_year' )
									  
								
								left join (
								
									select 
										curator_lid
										,count(*) as the_curator_sum
									
									from 
										curator 
										
											$new_law_35_join_condition
										
									where 
									
									
										curator_parent = 0
										and
										curator_is_disable in (0,1)
										
										$new_law_35_where_condition	  
									
									group by
										curator_lid
									
								) aa
								
								on
								aa.curator_lid = lawfulness.lid
									  
								
								
						WHERE  lawfulstatus in (0)
							   AND companytypecode != '14'
							   AND companytypecode < 200
							   AND branchcode < 1  
							   and
							   province = '".$the_province_row[province_id]."'
							   
							
					
					";
					
					//echo "<br>".$sql;
					
					$the_array = getFirstRow($sql);
					
					echo $the_array[the_sum_ratio]*1;
					
					?>
			</td>
			
			<td>
				
				<?php echo $the_array[the_sum_ratio]*112420; ?>
			
			</td>
			
			
			<td>
				<?php 
					//echo $the_province_row[province_code]; 
					
					$sql = "
					
						SELECT 
							sum(
							
								greatest(
								
									CASE
										WHEN lawfulness.Employees < 100 THEN 0	
										WHEN lawfulness.Employees % 100 <= 50 THEN floor(lawfulness.Employees/100)										
										ELSE ceil(lawfulness.Employees/100)
									END
									
									
									- Hire_NumofEmp 
									
									
									- coalesce(the_curator_sum,0)
									
									- coalesce(the_34_sum,0)
								
								, 0)
							
							)  as the_sum_ratio
							
						FROM   company
							   JOIN lawfulness
								 ON ( company.cid = lawfulness.cid
									  AND year = '$the_year' )
									  
								
								left join (
								
									select 
										curator_lid
										,count(*) as the_curator_sum
									
									from 
										curator 
										
											$new_law_35_join_condition
										
									where 
									
									
										curator_parent = 0
										and
										curator_is_disable in (0,1)
										
										$new_law_35_where_condition	  
									
									group by
										curator_lid
									
								) aa
								
								on
								aa.curator_lid = lawfulness.lid
								
								
								left join (
								
									SELECT 
						
										lawfulness.lid
										, sum(floor(the_sum_paid_amount/112420)) as the_34_sum
										
										
									FROM   company
										   JOIN lawfulness
											 ON ( company.cid = lawfulness.cid
												  AND year = '$the_year' )
											
											
											join
											
											(
											
												select
													payment.LID
													, sum(receipt.Amount-coalesce(sum_x_for,0)) as the_sum_paid_amount
												from
													payment 
														
													join
														receipt
														on
														receipt.RID = payment.RID
														
													left join
													
														
														
														(
															select
																meta_rid
																, sum(meta_value) as sum_x_for
															from
																receipt_meta
															where
																meta_for like '3%_for-%-amount'
																
															group by
																meta_rid
															
														) bbb
														on
														receipt.rid = bbb.meta_rid
															
															
													where
														receipt.is_payback = 0
														
													group by
														payment.LID
													
											) aaa
											
												on
												aaa.lid = lawfulness.lid
												
										group by
											lawfulness.lid
								
								
								
								) bb	

								on
								bb.lid = lawfulness.lid
								
								
						WHERE  lawfulstatus in (2)
							   AND companytypecode != '14'
							   AND companytypecode < 200
							   AND branchcode < 1  
							   and
							   province = '".$the_province_row[province_id]."'
							   
							
					
					";
					
					//echo "<br>".$sql;
					
					$the_array = getFirstRow($sql);
					
					//echo 
					$ratio_partial = $the_array[the_sum_ratio]*1;
					
					echo $ratio_partial*112420;
					
					?>
					
					
					<td>
				
						<?php
						
							$sql = "
								
								select
									sum(p_pending_amount) as the_sum
								from
									lawful_33_principals
								where
									p_lid in (
									
										SELECT 
											lawfulness.lid
											
										FROM   company
											   JOIN lawfulness
												 ON ( company.cid = lawfulness.cid
													  AND year = '$the_year' )
										WHERE  lawfulstatus in (2)
											   AND companytypecode != '14'
											   AND companytypecode < 200
											   AND branchcode < 1  
											   and
											   province = '".$the_province_row[province_id]."'
									
									)
							
							";
							
							$the_3335_pending = getFirstItem($sql);
							
							
							$sql = "
								
								select
									sum(p_pending_amount) as the_sum
								from
									lawful_35_principals
								where
									p_lid in (
									
										SELECT 
											lawfulness.lid
											
										FROM   company
											   JOIN lawfulness
												 ON ( company.cid = lawfulness.cid
													  AND year = '$the_year' )
										WHERE  lawfulstatus in (2)
											   AND companytypecode != '14'
											   AND companytypecode < 200
											   AND branchcode < 1  
											   and
											   province = '".$the_province_row[province_id]."'
									
									)
							
							";
							
							$the_3335_pending += getFirstItem($sql);
							
							
							echo $the_3335_pending ;
							
						
						?>
					
					</td>
			</td>
			
		</tr>
	<?php }?>
</table>