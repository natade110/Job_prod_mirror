<?php 

	//reset for each row
	$all_34 = 0;
	$report_32_row_count++;
	
	

?>
<?php if(1==0){?>
<table>
<?php }?>
<tr>
        <td width="50" valign="top"><div align="center"><?php echo $report_32_row_count?></div></td>
        <td  valign="top"><div align="left">
        
        <?php echo $the_province_name;?>
        
</div>          </td>
        <td align="right"  valign="top"><?php 
		
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					b.LawfulStatus != 3
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$result_1 = getFirstItem($sql);	
			$total_1 += $result_1;
			
			echo formatEmployee($result_1);
			
			
		
		?></td>
        <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					sum(
						if(
							b.employees % $the_limit <= $half_limit
							, floor(b.employees/$the_limit)
							, ceil(b.employees/$the_limit)
	
						) 
					)                    as company_ratio
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					b.Employees >= $the_limit
					and
					a.province = '$the_province'
					
					$typecode_filter
										
					$branch_codition
				
				";
				
			$all_ratio = getFirstItem($sql);
			$total_2 += $all_ratio;
			
			echo formatEmployee($all_ratio);
			
			
		
		?></td>
        <td align="right" valign="top"><?php 
		
			//รับคนพิการเข้าทำงานตาม ม.33
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					Hire_status = 1
					and
					pay_status = 0
					and
					conc_status = 0
					and
					LawfulStatus = 1
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
			
			$result_3 = getFirstItem($sql);
			$total_3 += $result_3;
							
			echo formatEmployee($result_3);
		
		?></td>
          <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					Hire_status = 0
					and
					pay_status = 1
					and
					conc_status = 0
					and
					LawfulStatus = 1
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$result_4 = getFirstItem($sql);
			$total_4 += $result_4;
			echo formatEmployee($result_4);
		
		?></td>
       <td align="right" valign="top"><?php 
		
			//ให้สัมปทานฯ ตาม ม.35
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					Hire_status = 0
					and
					pay_status = 0
					and
					conc_status = 1
					and
					LawfulStatus = 1
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$result_5 = getFirstItem($sql);
			$total_5 += $result_5;
			echo formatEmployee($result_5);
		
		?></td>
        <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					Hire_status = 1
					and
					pay_status = 1
					and
					conc_status = 0
					and
					LawfulStatus = 1
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$result_6 = getFirstItem($sql);
			$total_6 += $result_6;
			echo formatEmployee($result_6);
		
		?></td>
        <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					Hire_status = 1
					and
					pay_status = 0
					and
					conc_status = 1
					and
					LawfulStatus = 1
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
				
			$result_7 = getFirstItem($sql);
			$total_7 += $result_7;
			echo formatEmployee($result_7);
		
		?></td>
        <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					Hire_status = 0
					and
					pay_status = 1
					and
					conc_status = 1
					and
					LawfulStatus = 1
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$result_8 = getFirstItem($sql);
			$total_8 += $result_8;
			echo formatEmployee($result_8);
		
		?></td>
        <td align="right" valign="top"><?php 
		
			//รับคนพิการ ตาม ม.33 ส่งเงิน ตาม ม.34 และให้สัมปทานฯ ตาม ม.35
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					Hire_status = 1
					and
					pay_status = 1
					and
					conc_status = 1
					and
					LawfulStatus = 1
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$result_9 = getFirstItem($sql);
			$total_9 += $result_9;
			echo formatEmployee($result_9);
		
		?></td>
        <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					lawfulStatus = 2
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$result_10 = getFirstItem($sql);
			$total_10 += $result_10;
			echo formatEmployee($result_10);
		
		?></td>
        <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					lawfulStatus = 0
					and
					a.province = '$the_province'
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$result_11 = getFirstItem($sql);
			$total_11 += $result_11;
			echo formatEmployee($result_11);
		
		?></td>
        <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					sum(Hire_NumofEmp)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
				where
					a.province = '$the_province'
					
					and
					lawfulStatus != 3
					
					$typecode_filter
					
					$branch_codition
				
				";
				
			$all_33 = getFirstItem($sql);
			$total_12 += $all_33;
			
			echo formatEmployee($all_33);
		
		?></td>
        <td  align="right" valign="top"><?php 
		
		
			//something's here ....
			//first - see how many compnay is paid for 34
			//then see how many ppl need to be paid for this ratio
			
			
			$sql_01 = "
			
				select 
					a.cid
					, a.province
					, b.lid
					,sum(
					
						if(b.employees >= $the_limit
							,
							if(
								b.employees % $the_limit <= $half_limit
								, floor(b.employees/$the_limit)
								, ceil(b.employees/$the_limit)
		
							)
							,0
						) 
						
						
					)                    as company_ratio
					, b.Hire_NumofEmp as num_hired
					, COALESCE(max(sum_curator),0) as num_curated
					, 
					
					sum(
						if(b.employees >= $the_limit
							,
							if(
								b.employees % $the_limit <= $half_limit
								, floor(b.employees/$the_limit)
								, ceil(b.employees/$the_limit)
		
							)
							,0
						) 
					)  - b.Hire_NumofEmp - COALESCE(max(sum_curator),0) as num_needed
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
							
							
							
						left outer join
							(
							
								select 
									a.cid
									, count(*) as sum_curator
								from
									company a
										join lawfulness b
											on a.cid = b.cid
											and b.year = '$the_year'
										join curator c
											on b.lid = c.curator_lid
											and c.curator_parent = 0
											
								where
									a.province = '$the_province'
									
									$typecode_filter
									
									$branch_codition
									
									
									
									
									
								group by
									a.cid
							
							) cccc
								on a.cid = cccc.cid
				join
				 (
			
						select 
							distinct(c.lid)
						from
							company a
								join lawfulness b
									on a.cid = b.cid
									and b.year = '$the_year'
								join payment c
									on b.lid = c.lid
								join receipt d
									on c.rid = d.rid
						where
							a.province = '$the_province'
							
							$typecode_filter
							
							$branch_codition
							
							
						
					) mnm
					
					on 
					b.lid = mnm.lid
					
				group by
					a.cid
					, b.lid
					, b.Hire_NumofEmp
			
			";
			
			//echo $sql_01; //exit();
			
			
			$the_sql = "
						select 
							*
							
							, receipt.amount as receipt_amount 
							, lawfulness.lid as lawfulness_lid
							, zzz.cid as company_cid
						from 
							payment
							, receipt
							, lawfulness
							join (
							
								$sql_01
							
							) zzz on lawfulness.lid = zzz.lid
							
						where 
						
							receipt.RID = payment.RID
							and
							lawfulness.LID = payment.LID
							
							and
							lawfulness.year = '".$the_year."' 
							
							
							and 
							(
								(LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '0')
								or (LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '0')
								or (LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1')
								or (LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '0')								
								or (LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '1')
								or (LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '1')
								or (LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '1')
								
								or (LawfulStatus = '2')
								or ((LawfulStatus = '0' or LawfulStatus is null))
								
							)
							
							and
							is_payback = 0
							and
							main_flag = 1
							
							order by lawfulness.lid,ReceiptDate, BookReceiptNo, ReceiptNo asc";
			
			//echo $the_sql; exit();
			
			
			
			//$result_to_query = mysql_query($the_sql); //this result is fast...	
			
			
			$the_result = mysql_query($the_sql) or die(mysql_error()); //this one is slow...
			
			//get this same as report_1.php
			$the_row = 0;
			$last_lid = "";
			$last_cid = "";
			$last_num_needed  = "";
			$paid_from_last_bill = 0;
			$last_payment_date = 0;
			$paid_money = 0;
			$start_money = 0;
			$interest_money = 0;
			$maimad_paid = 0;
			
			$this_lawful_year = $the_year;
			
			//$all_34 = 0;
			
			include "scrp_get_34_details_from_lid_result_set.php.php";
			/*	
			echo "<br>FINAL ----".$result_row[cid]."<br>FINAL start_money: ".$start_money;
			echo "<br>FINAL paid_money: ".$paid_money;
			echo "<br>FINAL interest_money: ".$interest_money;
			echo "<br>FINAL maimad_paid: ".$maimad_paid;
				*/
			
			
			
			//echo "<br>---> ". $all_maimad_paid;
			//echo $the_row;
			//echo "<br>---> ".$all_34;
			$total_13 += $all_34;
			echo formatEmployee($all_34);
			
			
			
		
		
		?></td>
        <td align="right" valign="top"><?php 
		
			$sql = "
				select 
					count(*)
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
						join curator c
							on b.lid = c.curator_lid
							and c.curator_parent = 0
							and c.curator_is_dummy_row = 0
							and
							curator_is_disable in (1,0)
							
				where
					a.province = '$the_province'
					
					$typecode_filter
																				
					$branch_codition
					
					and 
					(
						(LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '0')
						or (LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '0')
						or (LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1')
						or (LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '0')								
						or (LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '1')
						or (LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '1')
						or (LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '1')
						
						or (LawfulStatus = '2')
						or ((LawfulStatus = '0' or LawfulStatus is null))
						
					)
				
				";
			
			//echo $sql;
			
			$all_35 = getFirstItem($sql);
			$total_14 += $all_35;
			echo formatEmployee($all_35);
		
		?></td>
        
        <td width="120" align="right" valign="top">
        	<div align="right">
			<?php 
				
				$last_total =  $all_ratio - $all_33 - $all_34 - $all_35;
				
				if($last_total < 0){
					echo "0";
					$total_15 += 0;
				}else{
					echo formatEmployee($last_total);	
					$total_15 += $last_total;
				}
				
				
				
				?>
            </div>
            </td>
        
        </tr>
<?php if(1==0){?>
</table>
<?php }?>