
		<table <?php if($this_lawful_year >= 2022 && $this_lawful_year < 2100){?>style="display: none;"<?php }?> >
		
			
			<tr>
				<td>
					เงินที่ต้องส่งเข้ากองทุน ม34:                                             
				
				</td>
				<td>
			   

				   <div align="right">
				   
						<?php 
						
						
						if($this_lawful_year >= 2018 && $this_lawful_year < 2050){
							echo $extra_employee;
						}else{
							echo $extra_employee;
						}
						
						?> x <?php 
						
						//yoes 20151230 
						//special for year 2011
						
						if($this_lawful_year == 2011){
							echo ($wage_rate*2) . "/2";
						}else{																	
							echo $wage_rate;
						}
						
						
						?> x <?php echo $year_date;?> = 
					
					</div>
				
				</td>
				
				<td>
				<div align="right">
				<?Php echo formatNumber($start_money);?>                                                        </div>
				<td>
						บาท     
				
				 <input name="money_per_person" type="hidden" value="<?php echo $wage_rate * $year_date;?>" />           
				
				</td>
			</tr>
			
			
			
			
			
			
			
			<?php if(
			
					($this_lawful_year >= 2018 && $this_lawful_year < 2050	)															
					|| $force_new_law
			){ ?>
			
			
			
			<tr>
				<td>
					ต้องจ่ายเงินทดแทนการจ้างงาน ม.33:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<font color='red'>
					<?Php //echo formatNumber($m33_total_missing); ?>    

					<?php 
					
					//yoes 20200624 -- for beta code
					$is_beta = getLidBetaStatus($this_lid);
					if($is_beta){
						
						$m33_principal_row = getFirstRow("
						
							select
								sum(p_amount) as the_principals
								, sum(p_interests) as the_interests
								, sum(p_pending_amount) as the_pending_principals
								, sum(p_pending_interests) as the_pending_interests
							from
								lawful_33_principals
							where
								p_lid = '$this_lid'
						
						");
						
						echo "".formatNumber($m33_principal_row[the_principals])."";
					}
					
					?>
					
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			<tr>
				<td>
					ดอกเบี้ยการจ่ายเงินทดแทนการจ้างงาน ม.33:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<font color='red'>
					<?Php //echo formatNumber($m33_total_interests); ?>   


					<?php 
					
					//yoes 20200624 -- for beta code
					if($is_beta){
						
						echo "".formatNumber($m33_principal_row[the_interests])."";
					}
					
					?>
					
					
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			
			
			
			<tr>
				<td>
					ต้องจ่ายเงินทดแทนสัมปทาน ม.35:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<font color='red'>
					<?Php //echo formatNumber($m35_total_missing); ?>     


					<?php 
					
					//yoes 20200624 -- for beta code
					
					if($is_beta){
						
						$m35_principal_row = getFirstRow("
						
							select
								sum(p_amount) as the_principals
								, sum(p_interests) as the_interests
							from
								lawful_35_principals
							where
								p_lid = '$this_lid'
						
						");
						
						echo "".formatNumber($m35_principal_row[the_principals])."";
					}
					
					?>																		
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			<tr>
				<td>
					ดอกเบี้ยการจ่ายเงินทดแทนสัมปทาน ม.35:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<font color='red'>
					<?Php //echo formatNumber($m35_total_interests); ?>      


					<?php 
					
						//yoes 20200624 -- for beta code
						if($is_beta){
							
							echo "".formatNumber($m35_principal_row[the_interests])."";
						}
					
					?>
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			<?php }?>
			
			
			
		   
			
			
			<!-- yoes new 20210902-->
			<?php if(
					$lawful_values["LID"] == 164036 
					|| $lawful_values["LID"] == 2050531917
					|| $lawful_values["LID"] == 2050532523
					|| $lawful_values["LID"] == 2050542121
					|| $lawful_values["LID"] == 191330
					|| $lawful_values["LID"] == 1000185931
					
					){?>
			<tr>
				<td>
					ยอดเงินที่จ่ายเข้ากองทุนแล้ว:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				<?Php  
				
				$the_sum_paid_sql = "select sum(receipt.amount)
							 from 
								payment, receipt , lawfulness
								where 
								receipt.RID = payment.RID
								and
								lawfulness.LID = payment.LID
								
								and
								lawfulness.lid = '".($lawful_values["LID"])."' 
								
								and
								is_payback != 1
								and 
								main_flag = 1
								";
				
				echo formatNumber(getFirstItem($the_sum_paid_sql));
				
				 ?>                                                        </div>
				
			  
				<td>
					บาท
				</td>
			</tr>
			
			
			
				<tr>
					<td>
						จ่ายเป็นดอกเบี้ย ม34:
					</td>
					<td>
					<div align="right">
					= </div></td>
					
					<td>
					<div align="right">
					<?Php  
					
					$the_sum_paid_sql = "select sum(p_interests)
								 from 
									lawful_34_principals
									where 
									p_lid = '".($lawful_values["LID"])."' 
									
									";
					
					echo formatNumber(getFirstItem($the_sum_paid_sql));
					
					 ?>                                                        </div>
					
				  
					<td>
						บาท
					</td>
				</tr>
				
				<tr>
					<td>
						เงินต้นคงเหลือ ม34:
					</td>
					<td>
					<div align="right">
					= </div></td>
					
					<td>
					<div align="right">
					<?Php  
					
					$the_sum_paid_sql = "
								select 
									p_pending_amount
								 from 
										lawful_34_principals
									where 
										p_lid = '".($lawful_values["LID"])."'
									
									order by
										p_uid desc
									limit 0,1
									
									";
									
					//echo $the_sum_paid_sql;
					
					
					$owned_money = getFirstItem($the_sum_paid_sql);
					echo formatNumber($owned_money);
					
					 ?>                                                        </div>
					
				  
					<td>
						บาท
					</td>
				</tr>
				
			
			
			<?php }else{ ?>
			
				 <tr>
					<td>
						ยอดเงินที่จ่ายเข้ากองทุนแล้ว:
					</td>
					<td>
					<div align="right">
					= </div></td>
					
					<td>
					<div align="right">
					<?Php echo formatNumber($paid_money); ?>                                                        </div>
					
					<input name="total_paid" type="hidden" value="<?php echo $paid_money;?>" />
					<td>
						บาท                                                        </td>
				</tr>
				
				<tr>
					<td>
							เงินต้นคงเหลือ:
					</td>
					<td>
					<div align="right">
					= </div></td>
					
					<td>
					
						<font color=red>
							<div align="right">
							
							<?Php 
								
								//update owned money here
								//yyoes 20181108 -> change this
								$owned_money = $start_money - $paid_money ; //- $m33_total_reduction;// - $payback_money
								
								//echo "$start_money - $paid_money - $m33_total_reduction;";
								
								if($owned_money < 0){
									echo "0.00";
								}else{
									echo formatNumber($owned_money);
								}
							
								
							
							
							?>                                                        
							
							</div>
						</font>
						
						
					<td>
						บาท                                                        </td>
				</tr>
			
			<?php } ?>
			
			
			 
			
			
			<tr>
				<td>
				วันที่จ่ายเงินเข้ากองทุนล่าสุด:                                                        </td>
				<td>
				<div align="right">
			   
				</div>
				
				
				</td>
				<td colspan="2">
				<div align="right">
				 <?php 
				
				
				$the_sql = "select max(ReceiptDate) from payment, receipt , lawfulness
					where 
					receipt.RID = payment.RID
					and
					lawfulness.LID = payment.LID
					and
					ReceiptYear = '$this_lawful_year'
					and
					lawfulness.CID = '".$this_id."' 
					
					and
					is_payback != 1
					";
				
				//echo $the_sql ;
				
				$actual_interest_date = getFirstItem($the_sql);
				//echo "----".$actual_interest_date;
				
				
				
				
				//////////
				//
				//
				// 	20140224
				//	clean this
				//
				//
				//////////
				
				
				//new vars
				$interest_date_for_calculate_summary = $actual_interest_date;
				
				
				 if(!$interest_date_for_calculate_summary){
					
					if($the_54_budget_date){
							
						$interest_date_for_calculate_summary = "$the_54_budget_date 00:00:00";
					
					}else{
						
						$interest_date_for_calculate_summary = getDefaultLastPaymentDateByYear($this_lawful_year);	
					}
				}
										
				//echo "$this_lawful_year-02-01 00:00:00";		
				
				
				//if last payment date is less than FEB 01 then detaulit it to FEB 01
				
				//yoes 20170123
				
				if(strtotime(date($interest_date_for_calculate_summary)) 
					< 
					strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year)))){
				
					$interest_date_for_calculate_summary = getDefaultLastPaymentDateByYear($this_lawful_year);
				
				}
				
				
				//////////
				//
				//
				// 	20140224
				//	END clean this
				//
				//
				//////////
				
				
				
				if($actual_interest_date && $actual_interest_date != '0000-00-00 00:00:00'){
					echo formatDateThai($actual_interest_date);
				}else{
					echo "ไม่เคยมีการจ่ายเงิน";
				}
				
				?>                                                        </div>                                                        </td>
				 
			</tr>
			
			<?php
			
			//cal culate interest money
			

			//yoes 20170415
			
			if(
			
			
			$the_54_budget_date &&
			
			strtotime(date($interest_date_for_calculate_summary)) 
						<= 
						strtotime(date("$the_54_budget_date 00:00:00"))){
							
				$interest_date_for_calculate_summary = "$the_54_budget_date 00:00:00";
			
			}

			
			if($owned_money <= 0){
			
				//no longer calculate interests
				$interest_date = 0;
			}else{
				$interest_date = getInterestDate($interest_date_for_calculate_summary, $this_lawful_year, "Y-m-d",$the_54_budget_date);
			}
			
			//echo "<br>$actual_interest_date" . " / ". $this_lawful_year . " / ".  strtotime(date("Y-m-d"))."<br>";
			
			
			//yoes 20170108
			//interests for 2011
			
			if($this_lawful_year >= 2012 || $do_54_budget){ //only show interests when 2012+
				$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
			}else{
				$interest_money = 0;
			}
			
			?>
			
			
		   <?php //if($the_54_budget_date){
			   
			   if($the_54_budget_date && (strtotime(date($last_payment_date_to_show)) 
						<= 
						strtotime(date("$the_54_budget_date 00:00:00")))){
			   
			   ?>
		   
			
			
			
			 <tr>
						<td colspan="4">
					   <span style="color: #060; " >
				มีการคิดดอกเบี้ยสำหรับปี 2554 ตั้งแต่วันที่ <?php echo formatDateThai($the_54_budget_date);?>
			</div>                                        
						</span>
						</td>
					   
					</tr>
			
			<?php }?>
			
			
		  
			
			
			 <?php if($last_payment_date_to_show && 1==0){?>
		   
			
			
			
			 <tr>
						<td colspan="4">
					   <span style="color: #060; " >
				  วันที่จ่ายล่าสุด ของใบเสร็จนี้ <?php echo formatDateThai($last_payment_date);?>
			</div>                                        
						</span>
						</td>
					   
					</tr>
			
			<?php }?>
			
			
			 
			 <?php 
			 
			 //yoes 20170108
			//interests for 2011
			 
			 if($this_lawful_year >= 2012 || $do_54_budget){//?>
			 
					<tr>
						<td>
							ดอกเบี้ย ณ วันนี้: 
						</td>
						<td>
						<div align="right">
						<?php echo formatNumber(max($owned_money,0));?> x 7.5/100/<?php echo $year_date;?> x <?php echo $interest_date;?> = 
						</div>
						
						
						</td>
						<td>
							
							<font color=red>
								<div align="right">
									<?Php echo formatNumber($interest_money);?>                                                        
								</div>
							</font>
						
						</td>
						 <td>
							บาท 
						 </td>
					</tr>
			<?php }?>
			
			
			<?php if($this_lawful_year >= 2018 && $this_lawful_year < 2050 && 1==0){ ?>
			
				<tr>
						<td>
							ทดดอกเบี้ย<br>กรณีจ้าง ม.33 แทนภายใน 45 วัน: 
						</td>
						<td>
						<div align="right">
						
						</div>
						
						
						</td>
						<td>
						<div align="right">
						<?Php echo formatNumber(0);?>                                                        </div>                                                        </td>
						 <td>
							บาท 
						 </td>
					</tr>
			
			<?php }?>
			
			
			
			<?php 
			
			//yoes 20170108
			//interests for 2011
			
			
			
			if($this_lawful_year >= 2012 || $do_54_budget){//?>
			 <tr>
				<td>
				ดอกเบี้ยค้างชำระ:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				<?Php echo formatNumber($total_pending_interest);?>                                                        </div>
				<td>
				บาท                                                        </td>
			</tr>
			<?php }?>
			
			
			
			<tr>
				<td>
				ขอเงินคืนจากกองทุนฯ:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				<?Php echo formatNumber($payback_money);?>                                                        </div>
				<td>
				บาท                                                        </td>
			</tr>
			
			
			
			<tr>
				<td>
				
				<?php 
					
					//yoes 20181108 - add interest from m35
					//yoes 20181030 - add interests from m33
					//yoes 20170509 - deduct payback money from this vaalue																		
					$the_final_money = $owned_money + $interest_money +$payback_money +$total_pending_interest 
										+$m33_total_missing +$m33_total_interests
										+$m35_total_missing +$m35_total_interests
										
										;
										
					
					//yoes 20200624 -- ยอดคงเหลือ as per beta
					$the_final_money_beta = $owned_money + $interest_money +$payback_money +$total_pending_interest
										+$m33_principal_row[the_principals]+$m33_principal_row[the_interests]
										+$m35_principal_row[the_principals]+$m35_principal_row[the_interests]
										
										
										;
										
					if(	
						$lawful_values["LID"] == 164036 
						|| $lawful_values["LID"] == 2050531917
						|| $lawful_values["LID"] == 2050532523
						|| $lawful_values["LID"] == 2050542121
						|| $lawful_values["LID"] == 191330
						|| $lawful_values["LID"] == 1000185931
						
						){
						
						$the_final_money_beta = $owned_money + $interest_money
										+$m33_principal_row[the_pending_principals]+$m33_principal_row[the_pending_interests]
										+$m35_principal_row[the_pending_principals]+$m35_principal_row[the_pending_interests]
										;
						
					}																		
					
					
					//$the_final_money = $owned_money;
					
					//echo "pay back money: " . $payback_money;
					
					//yoes 20130801 - add proper decimal to final monty
					//$the_final_money = number_format($the_final_money,2);
					$the_final_money = round($the_final_money,2);
				
				   // if($the_final_money < 0){
					if($the_final_money_beta < 0){   
				?>
						ต้องส่งเงินคืน:
						
				<?php }else{?>
				
				
				
						ยอดเงินค้างชำระ:      
					  
											   
				<?php }?>
				
				
				
				
				
				</td>
				<td>&nbsp;</td>
				<td>
				<div align="right">
				
				<input name="the_final_money" type="hidden" value="<?php echo $the_final_money;?>" />
				
					<b>
						<?Php 
					
						
					
						
						
						
						
						//yoes 20200624 -- for beta code
						if($is_beta){
							
							$the_final_money_beta_to_show = $the_final_money_beta;
							
							if(floor($the_final_money_beta) > 0){
								echo "<font color='red'>";
							}else if($the_final_money_beta < 0){
								echo "<font color='green'>";
								$the_final_money_beta_to_show = $the_final_money_beta * -1;
							}else{
								echo "<font>";
							}
						
							echo formatNumber($the_final_money_beta_to_show);
							
						}
						
						echo "</font>";
						
						
						//yoes 20201208 - for older years...
						if(strlen($the_final_money_beta_to_show) == 0){
							
							if(floor($the_final_money) > 0){
								echo "<font color='red'>";
							}else if($the_final_money < 0){
								echo "<font color='green'>";
								$the_final_money = $the_final_money * -1;
							}else{
								echo "<font>";
							}
						
							echo formatNumber($the_final_money);
							
						}
						
						?>
					</b>
					
					
				 </div>
				</td>
				
				 <td>
				บาท                                                        </td>
			</tr>
			
			
		</table>
		
		