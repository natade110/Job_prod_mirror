<?php if($_GET["ws"]){
	
	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";
	//include "functions.php";

	
	$the_lid = $_POST["the_lid"];
	$wage_rate = $_POST["wage_rate"];
	$year_date = $_POST["year_date"];
	$this_lawful_year = $_POST["this_lawful_year"];	
	$extra_employee = $_POST["extra_employee"];
	$this_id = $_POST["this_id"];

	//print_r($_POST);
	
	resetLawfulnessByLID($the_lid);
	
	//yoes 20230228
	$md_34_lawful_row = getFirstRow("select * from lawfulness where lid = '$the_lid'");
	
	//print_r($md_34_lawful_row);
	
	if($md_34_lawful_row["Year"] < 2012 && $md_34_lawful_row["LawfulStatus"] == 1){
		
		$md_34_do_mask_payment = 1;
	}else{
		$md_34_do_mask_payment = 0;
	}
	
	  $the_sql = "select sum(receipt.Amount) 
						from payment, receipt , lawfulness
						where 
						receipt.RID = payment.RID
						and
						lawfulness.LID = payment.LID
						and
						ReceiptYear = '$this_lawful_year'
						and
						lawfulness.CID = '".$this_id."'
						and
						is_payback = 1
						";
						
					$payback_money = getFirstItem("$the_sql");
																
																
																//echo $the_sql; exit();

}

?><table width=100%>

			<?php if(!$md_34_do_mask_payment){?>
			<tr>
				<td>
					 ต้องจ่ายเงินแทนการรับคนพิการ ม.34:
				
				</td>
				<td>
				
				<div align=right>
					<?php echo $extra_employee ." x ".$wage_rate." x ".$year_date;?> =
				</div>
				
			   </td>
				
				<td>
					
					
				
					<font color='red'>
						<div align="right">
						
						<?php
						
						
							//yoes 20211109?
							$sql = "
										select 
											*
											
										 from 
												lawful_34_principals
											where 
												p_lid = '".$the_lid."'

											order by
												p_uid asc
											limit 0,1

											";

							
							$max_pay_34_row = getFirstRow($sql);
							
							
							
							$sql = "
										select 
											sum(p_interests)
											
										 from 
												lawful_34_principals
											where 
												p_lid = '".$the_lid."'

											";

							//yoes 20211110
							$pending_interest_pay_34 = getFirstItem($sql);
					
							$sql = "
										select
											*
										 from 
												lawful_34_principals
											where 
												p_lid = '".$the_lid."'

											order by
												p_uid desc
											limit 0,1

											";

							
							$pay_34_row = getFirstRow($sql);
						
							$pay_34_row[p_pending_amount] = max($pay_34_row[p_pending_amount],0);
							
							
							
							
					
						
							
						
							//echo formatNumber($pay_34_row[p_pending_amount]);
							echo formatNumber($max_pay_34_row[p_amount]);
							
							?> 
						</div>
					</font>
				<td>
				บาท                                                        
				
				
				 <input name="money_per_person" type="hidden" value="<?php echo $wage_rate * $year_date;?>" />           
				
				</td>
			</tr>
			<?php }?>
			
			
			<?php if($pending_interest_pay_34 && !$md_34_do_mask_payment){?>
			<tr>
				<td>
					 ดอกเบี้ยเงินแทนการรับคนพิการ ม.34 สะสม:
				
				</td>
				<td>
					
					<div align=right>
						<?php //echo formatNumber(pending_interest_pay_34);?>
					</div>
				
				</td>
				
				<td>
					<font color='red'>
						<div align="right">
							<a href="#" onClick="$('#md_detailed_34_table').toggle(); return false;"; style='color: red; font-weight: normal;'>
								<u><?php echo formatNumber($pending_interest_pay_34);?></u>
							</a>
						</div>
						
						
						
					</font>
				<td>
				บาท                 
				
				</td>
			</tr>
			
			<tr>
				<td colspan=4>
					
					<div align=right>
						<table id="md_detailed_34_table" border=1 cellpadding=2 style="border-collapse:collapse; display: none;">
							<tr>
								
								<td>
									<div align=center>
										
										ใบเสร็จ
									
									</div>
								</td>
								
								<td>
									<div align=center>
										
										ช่วงวันที่
									
									</div>
								</td>
								<td>
									<div align=center>
										
										จำนวนเงินในใบเสร็จ
									
									</div>
								</td>
								<td>
									<div align=center>
										
										เงินต้น 34 วันนี้
									
									</div>
								</td>
								
								<td>
									<div align=center>
										
										ดอกเบี้ย 34 วันนี้
									
									</div>
								</td>
								<td>
									<div align=center>
										
										จ่ายเป็นเงินต้น ม34
									
									</div>
								</td>
								<td>
									<div align=center>
										
										เงินต้น 34 คงเหลือ
									
									</div>
								</td>
								<td>
									<div align=center>
										
										หมายเหตุ
									
									</div>
								</td>
							</tr>
							
							<?php
							
								$sql = "
										select
											*
											, concat(bb.BookReceiptNo,'/', bb.ReceiptNo) as receipt_to
											, bb.Amount as amount_to
										 from 
												lawful_34_principals a
													left join
														receipt b
														on
														a.p_from = b.RID
													left join
														receipt bb
														on
														a.p_to = bb.RID
											where 
												p_lid = '".$the_lid."'

											order by
												p_uid desc
											

											";
											
								
							
								$pay_34_detailed_result = mysql_query($sql);
								
								
								while($pay_34_detailed_row = mysql_fetch_array($pay_34_detailed_result)){
							
							?>
							
								<tr>
								
									<td>
										<div align=left>
										<?php 
										
											echo default_value($pay_34_detailed_row[receipt_to],'-ยังไม่มีการจ่ายเงิน-');
										?>
										</div>
									</td>
									
									<td>
										<div align=left>
										<?php 
										
											echo formatDateThai($pay_34_detailed_row[p_date_from],0);
											echo "->";
											echo formatDateThai($pay_34_detailed_row[p_date_to],0);
										?>
										</div>
									</td>
									<td>
										<div align=right>
										<?php 
										
											echo number_format($pay_34_detailed_row[amount_to],2);
										?>
										</div>
									</td>
									<td>
										<div align=right>
										<?php 
										
											//echo number_format(max($pay_34_detailed_row[p_amount],0),2);
											echo number_format($pay_34_detailed_row[p_amount],2);
										?>
										</div>
									</td>
									<td>
										<div align=right>
										<?php 
										
											echo number_format($pay_34_detailed_row[p_interests],2);
										?>
										</div>
									</td>
									<td>
										<div align=right>
										<?php 
										
											echo number_format($pay_34_detailed_row[p_paid],2);
										?>
										</div>
									</td>
									<td>
										<div align=right>
										<?php 
										
											echo number_format($pay_34_detailed_row[p_pending_amount],2);
										?>
										</div>
									</td>
									<td>
										<div align=left>
										<?php 
										
											echo ($pay_34_detailed_row[p_remarks]);
										?>
										</div>
									</td>
								</tr>
							
							<?php
									
								}
							
							?>
							
						</table>
						
						<?php //echo $sql; ?>
					</div>
				
				</td>
			</tr>
			
			<?php }?>
			
			<?php if(!$md_34_do_mask_payment){?>
			<tr>
				<td>
					 วันที่จ่ายเงินเข้าทดแทน ม.34 ล่าสุด:
				
				</td>
				<td>
					
				</td>
				
				<td>
					<font color=''>
						<div align="right">
						<?php echo formatDateThai($pay_34_row[p_date_from]) ?>
						</div>
						
					</font>
				
				</td>
				<td>
			
					
				</td>
			</tr>
			
			
			
			<tr>
				<td>
					 ต้องจ่ายดอกเบี้ยเงินแทนการรับคนพิการ ม.34 วันนี้:
				
				</td>
				<td>
					
					<div align=right>
						<?php 
						
							//echo $pay_34_row[p_start_date]. " ---- ";
							
							echo formatNumber($pay_34_row[p_pending_amount]) ." x 7.5/100/365 x ".getInterestDate($pay_34_row[p_start_date], $this_lawful_year, date("Y-m-d"));?> =
					</div>
				
				</td>
				
				<td>
					<font color='red'>
						<div align="right">
						<?php
					
						
							$pay_34_row[p_pending_interests] = max($pay_34_row[p_pending_interests],0);
					
						
							echo formatNumber($pay_34_row[p_pending_interests]);
							
							
							//echo "($pending_interest_pay_34)";
							
							
							
							?> 
						</div>
						</div>
					</font>
				<td>
				บาท                 
				
				</td>
			</tr>
			<?php }?>
			
			
			
			<?php 
			
				//yoes 20181125
				if($this_lawful_year >= 2018 && $this_lawful_year < 2050){ 											
				
					//yoes 20211020	--> use this instead
					$m33_principal_row = getFirstRow("

						select
							sum(p_amount) as the_principals
							, sum(p_interests) as the_interests
							, sum(p_pending_amount) as the_pending_principals
							, sum(p_pending_interests) as the_pending_interests
						from
							lawful_33_principals
						where
							p_lid = '$the_lid'

					");

					$m35_principal_row = getFirstRow("
												
						select
							sum(p_amount) as the_principals
							, sum(p_interests) as the_interests
							, sum(p_pending_amount) as the_pending_principals
							, sum(p_pending_interests) as the_pending_interests						
						from
							lawful_35_principals
						where
							p_lid = '$the_lid'

					");

											
					
			
			
			?>
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
					<?Php 
					
						//echo formatNumber($m33_principal_row[the_principals]);

						echo "".formatNumber($m33_principal_row[the_pending_principals])."";

					?>                                                        
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			<tr>
				<td>
					ต้องจ่ายดอกเบี้ยเงินทดแทนการจ้างงาน ม.33:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<font color='red'>
					<?Php
						//echo formatNumber($m33_principal_row[the_interests]);
						echo "".formatNumber($m33_principal_row[the_pending_interests])."";	

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
					<?Php 
					
						//echo formatNumber($m35_principal_row[the_principals]); 
						echo "".formatNumber($m35_principal_row[the_pending_principals]).""; 
					
					
					?>                                                     
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			<tr>
				<td>
					ต้องจ่ายดอกเบี้ยเงินทดแทนสัมปทาน ม.35:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<font color='red'>
					<?Php
						//echo formatNumber($m35_principal_row[the_interests]);

						echo "".formatNumber($m35_principal_row[the_interests])."";
						?>                                                           
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			
			<?php } //ends if($this_lawful_year >= 2018 && $this_lawful_year < 2050)?>
			
			<?php if(!$md_34_do_mask_payment){ ?>
			<tr>
				<td>
					รวมต้องจ่ายเงิน:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<font color='red'>
					<?Php 
					
						/*$total_money_to_pay = 
							0
							//+$pay_34_row[p_amount]
							+$max_pay_34_row[p_amount]
							+$pay_34_row[p_pending_interests]
							//+$max_pay_34_row[p_amount]
							+$m33_principal_row[the_principals]
							+$m33_principal_row[the_interests]
							+$m35_principal_row[the_principals]
							+$m35_principal_row[the_interests];
					
						echo formatNumber($total_money_to_pay);*/
						
						/*echo "$total_money_to_pay = 
							0
							//+$pay_34_row[p_pending_amount]
							+$max_pay_34_row[p_amount]
							//+$pay_34_row[p_pending_interests]
							+$pending_interest_pay_34 
							//+$max_pay_34_row[p_amount]
							+$m33_principal_row[the_principals]
							+$m33_principal_row[the_interests]
							+$m35_principal_row[the_principals]
							+$m35_principal_row[the_interests]";*/
							

						
						
						$total_money_to_pay = 
							0
							//+$pay_34_row[p_pending_amount]
							+$max_pay_34_row[p_amount]
							//+$pay_34_row[p_pending_interests]
							+$pending_interest_pay_34 
							//+$max_pay_34_row[p_amount]
							+$m33_principal_row[the_principals]
							+$m33_principal_row[the_interests]
							+$m35_principal_row[the_principals]
							+$m35_principal_row[the_interests];
					
						echo "". formatNumber(
			
							$total_money_to_pay 
			
						) .""  ;
						
						//. "/".$pending_interest_pay_34
						
						/*echo "
							<br>+$max_pay_34_row[p_amount]
							<br>+$pending_interest_pay_34 
							<br>+$m33_principal_row[the_principals]
							<br>+$m33_principal_row[the_interests]
							<br>+$m35_principal_row[the_principals]
							<br>+$m35_principal_row[the_interests];";*/
						

						/*echo "
							*****
							+ $max_pay_34_row[p_amount]
							
							+ $pending_interest_pay_34 
							
							+ $m33_principal_row[the_principals]
							+ $m33_principal_row[the_interests]
							+ $m35_principal_row[the_principals]
							+ $m35_principal_row[the_interests];
						
						";*/


						?> 
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			<?php }?>
			
			<?php if(1==1){?> 
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
						
					$sql = "select 
								sum(receipt.Amount) as the_amount
							from 
								payment
								, receipt
								, lawfulness
							where 
								receipt.RID = payment.RID
								and
								lawfulness.LID = payment.LID
						
								and
								lawfulness.lid = '".$the_lid."' 
						
								and
								is_payback != 1
								and 
								main_flag = 1
							group by 
								lawfulness.LID";
					
						$lid_receipt_row = getFirstRow($sql);
						
						$paid_amount = $lid_receipt_row[the_amount];
					
						echo "<font color=green>";
							echo formatNumber($paid_amount); 
						echo "</font>";
					
						//$paid_amount = 0;
					
					?>
				</div>
				
				
				<td>
					บาท                                                        
				</td>
			</tr>
			
			
			<?php }?>
			
			<?php if($payback_money){?>
			<tr>
				<td>
					ยอดเงินที่ส่งคืน:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<font color='purple'>
					<?Php 
					
							//echo formatNumber($total_money_to_pay);
							echo formatNumber($payback_money);
					
						 ?> 
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			

			</tr>
			<?php }?>
			
			<?php if(!$md_34_do_mask_payment){?>
			<tr>
				<td>
					ยอดเงินค้างชำระ:
				</td>
				<td>
				<div align="right">
				= </div></td>
				
				<td>
				<div align="right">
				
				<?php 
					$total_money_to_display = $total_money_to_pay-$paid_amount+$payback_money;
				?>
				
				<font color='<?php 
						
						if(number_format($total_money_to_display,2) > 0){
							
							echo "red";
							
						}
					
					?>'>
					<?Php 
					
						echo "<strong>";
							//echo formatNumber($total_money_to_pay);
							
							echo formatNumber($total_money_to_display);
						echo "<strong>";
					
						 ?> 
				</font>
				
				</div>
				
				
				<td>
					บาท
				</td>
			</tr>
			<?php }?>
		
		
		</table>