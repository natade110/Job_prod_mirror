<?php

	include "db_connect.php";
	include "session_handler.php";
	
	
	
	$the_year = $_GET[for_year]*1;
	$the_cid = $_GET[search_id]*1;
	
	$cid_province = getFirstItem("select province from company where cid = '$the_cid'");
	

	//yoes 20181024 --> move this here
	$this_lawful_year = $the_year;
									
	//get lawfulness's details
	$lawfulness_row = getFirstRow("select * from lawfulness where cid = '$the_cid' and year = '$the_year'");
	$the_lid = $lawfulness_row["LID"];
	
	
	//yoes 20160622 -- check permisison
	if($sess_accesslevel != 1 && $sess_accesslevel != 2 && $sess_accesslevel != 3){
		
		//echo $the_lid . getLawfulnessMeta($the_lid,"courted_flag"); exit();
		
		if($sess_accesslevel == 8 && getLawfulnessMeta($the_lid,"courted_flag")){
			
			$can_do_payment = 1;
			
		}else{
		
			header("location: index.php");	
			exit();
			
		}
		
		
	}
	
?>


<?php include "header_html.php";?>






<td valign="top" style="padding-left:5px;">
                
                	
                	
                    
                    
                    
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >การส่งเงินเข้ากองทุนฯ</h2>
                    
                    
                    
                    <form method="post" >
                    
                    
                    <table border="0" cellpadding="0">
                          <tr>
                            <td><table border="0" style="padding:10px 0 0 50px;" >
                            
                              <?php if($sess_accesslevel !=4){?>
                              <tr>
                                <td colspan="4" >
								
								<span style="font-weight: bold">ข้อมูลใบเสร็จ</span></td>
                                
                              </tr>
                              <?php } ?>
                              
                              <tr>
                                <td>สถานประกอบการ</td>
                                <td colspan="3">
                                
                                
                                <strong>
								
                                <a href="organization.php?id=<?php echo $the_cid; ?>&year=<?php echo $the_year;?>&focus=lawful">
									<?php 
                                    
                                    
                                    
                                    $company_row = getFirstRow("select * from company where cid = '$the_cid'");
                                    
                                    echo formatCompanyName($company_row[CompanyNameThai],$company_row[CompanyTypeCode]);
                                    
                                    
                                    ?>
                                </a>
                                
                                
                                </strong></td>
                              </tr>
                              
                              <tr>
                                <td>เลขทะเบียนนายจ้าง</td>
                                <td colspan="3">
                                
                                
                                <strong>
								
                                <?php 
								
								echo $company_row[CompanyCode];
								
								?>
                                
                                </strong></td>
                              </tr>
                              
                              
                              
                              <tr>
                                    <td>สำหรับปี</td>
                                    <td><strong><?php 
										//**toggle payment
										
										
										echo $the_year+543;
										
										
										// ddl_year_payments will only allow to add payment year 2015?></strong></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                  </tr>
                              <tr>
                                <td>ข้อมูลการจ่ายเงินสำหรับวันที่</td>
                                <td>
								
								<strong><?php 
								
								
									if($_POST["the_date_year"] && $_POST["the_date_month"] && $_POST["the_date_day"]){
										$this_date_time = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];	
									}else{
										$this_date_time = date("Y-m-d");
									}								
								
									echo formatDateThai($this_date_time);?></strong>
                                
                                </td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                              
                              
                              
                             
                              
                              
                              
                              <tr>
                              
                              	<td colspan="4">
                                
                                
                                
                                <?php 
								
								
									
									
									//echo "select * from lawfulness where cid = '$the_cid' and year = '$the_year'";
									
									
									//basic values
									$lawfulness_year = $lawfulness_row[Year];	
									$lawfulness_employees = $lawfulness_row[Employees];
									
									$lid_to_get_34 = $lawfulness_row[LID];
									//yoes 20170119
									$the_lid = $lid_to_get_34;
									
									//echo $lid_to_get_34;
									
									
									
									
									$lawfulness_ratio = getThisYearRatio($lawfulness_year);
									$need_for_lawful = getEmployeeRatio($lawfulness_employees,$lawfulness_ratio);
									
									
									$year_date = 365; //days in years
									$the_wage = getThisYearWage($lawfulness_year, $cid_province); //ค่าจ้างประขำปี
									//echo "-".$lawfulness_year."-";
									//echo "-".$cid_province."-";
									$wage_rate = $the_wage;
									
									if($this_lawful_year == 2011){
										
										$wage_rate = $wage_rate/2;
										$the_wage = $the_wage/2;
										
										
										$do_54_budget = getFirstItem("
							
													select
														meta_value
													from
														lawfulness_meta
													where
														meta_for = 'do_54_budget'
														and
														meta_lid = '". $lid_to_get_34."'
													
													
													");
													
																	
										$the_54_budget_date = getFirstItem("

												select
													meta_value
												from
													lawfulness_meta
												where
													meta_for = 'do_54_budget_start_date'
													and
													meta_lid = '". $lid_to_get_34."'
												
												
												");
												
											//echo $the_54_budget_date;
										
									}
									
									
									$cid_province = getFirstItem("select province from company where cid = '".$lawfulness_row[CID]."'");
									
									//also re-sync m33 here just in case
									/*$hire_numofemp = getFirstItem("
										SELECT 
											count(*)
										FROM 
											lawful_employees
										where
											le_cid = '".$the_cid."'
											and le_year = '".$the_year."'");
									*/
									//yoes 20181125
									//get m33 as per new law
									$hire_numofemp =  getHireNumOfEmpFromLid($the_lid);




									
									
									//yoes
									/*
									$the_35_sql = "
		
											select
												count(*)
											from
												curator
											where
												curator_lid = '$the_lid'
												and
												curator_parent = 0
									
											";
									
									$the_35 = getFirstItem($the_35_sql);
									*/
									//yoes 20181125
									//get m35 as per new law
									$the_35 = getNumCuratorFromLid($the_lid);
									
									$extra_employee = $need_for_lawful-$the_35-$hire_numofemp;
									
									//echo $need_for_lawful ."-".$the_35 ."+".$hire_numofemp;
									$employees_ratio = $extra_employee;
									
									$start_money = $employees_ratio*$year_date*$the_wage;
									
									
									
									//
									$the_sql = "select *
												, receipt.amount as receipt_amount
												, lawfulness.year as lawfulness_year
												 from payment, receipt , lawfulness
													where 
													receipt.RID = payment.RID
													and
													lawfulness.LID = payment.LID
													
													and
													lawfulness.lid = '".$lid_to_get_34."' 
													
													and
													is_payback != 1
													and 
													main_flag = 1
													order by ReceiptDate, BookReceiptNo, ReceiptNo asc";
									
									//echo $the_sql; exit();
											
									$the_result = mysql_query($the_sql) or die(mysql_error()); //this one is slow...
									
									//resets
									$paid_money = 0;
									$extra_money = 0;
																		
									//echo "start_money -- $start_money --";
									
									//echo "<br>employees_ratiooo ".$employees_ratio." oo";
									
									$owned_money = $start_money;
									$paid_from_last_bill = 0;
									$this_lid_interests = 0;
									$last_payment_date = 0;
									
									while($result_row = mysql_fetch_array($the_result)){
										
											$have_some_34 = 1;
										
											$owned_money = $owned_money - $paid_from_last_bill;
											
											$this_paid_amount = $result_row["receipt_amount"];	
											
											//echo "<br>owned_money;;".$owned_money.";;";								
											
											//echo "<br>this_paid_amount**".$this_paid_amount."**";
											
											$this_lawful_year = $result_row[lawfulness_year];
																						
											if(!$last_payment_date){
																								
												if($the_54_budget_date){
		
													$last_payment_date = "$the_54_budget_date 00:00:00";
												
												}else{
													
													$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
												}
											}
											
											//echo "---".$last_payment_date;
											
											//echo $the_54_budget_date;
																	
											if(strtotime(date($last_payment_date)) 
												< 
												strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year)))){
											
												$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
											
											}
											
											//echo $last_payment_date;
											
											$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $result_row["ReceiptDate"]);
											//echo "<br>interest_date,".$interest_date.",";										
								
											$last_payment_date_to_show = $last_payment_date;
											$last_payment_date = $result_row["ReceiptDate"];
											
											if($this_lawful_year >= 2012 || $do_54_budget){ //only show interests when 2012+
												
												//echo "<br>doGetInterests($interest_date,$owned_money,$year_date)";
												$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
											}else{
												$interest_money = 0;
											}
											
											$this_lid_interests += $interest_money;
											
											
											//echo "<br>interest_money::".$interest_money."::";	
											
											if($total_pending_interest > 0){																
												$interest_money += $total_pending_interest;					
											}
											
											
											if($this_paid_amount < $interest_money){
												$have_pending_interest = 1;
												
											}					
											
											
											$this_paid_money = $this_paid_amount-$interest_money;
											
											//echo "<br> $this_paid_money = $this_paid_amount - $interest_money ;"; 
											
											if($this_paid_money < 0){
												$this_paid_money = 0;
											}
											
											
											$paid_money += $this_paid_money;
											
											$paid_from_last_bill = $this_paid_money;
											
										
											if($this_paid_amount < $interest_money){
												$pending_interest = (($interest_money - $this_paid_amount ));
												
												$total_pending_interest = $pending_interest;
											
											 }else{
											
												$total_pending_interest = 0;
											
											}
											
											
											//echo "sss";
											
											
									}//end while for looping to display payment details	
									
									//exit();
									
									//exit();
									
									//echo "($paid_money/($year_date*$the_wage)"; //exit();
									
									//yoes 20160201 --> if จ่ายเกิน then move it somewhere else
									//echo "if( $paid_money > $start_money ){"; exit();
									if( $paid_money > $start_money){
										
										$paid_money_origin = $paid_money;
										
										$extra_money = $paid_money - $start_money;
										$paid_money = $start_money;
									}else{
										$paid_money_origin = $paid_money;
									}
									
									
									//echo $paid_money_origin; 
									
									
									//echo $deducted_33 ;
								
								?>
                                
                                <hr>
                                <span id="calculated_34_table">
                                    <table >
                                    

                                        <tr>
                                            <td>
                                                จำนวนลูกจ้าง:
                                            </td>
                                            <td>

                                            </td>

                                            <td>
                                                <div align="right">
                                                    <?php echo number_format($lawfulness_employees);?>
                                                </div>
                                            <td>
                                            คน
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                อัตราส่วนลูกจ้างต่อคนพิการ:
                                            </td>
                                            <td>
                                                <div align="right">
                                                    <?php echo $lawfulness_ratio; ?>: 1 =
                                                </div>
                                            </td>

                                            <td>
                                                <div align="right">
                                                     <?php echo number_format($need_for_lawful);?>
                                                </div>
                                            <td>
                                            คน
                                            </td>
                                        </tr>



                                        <tr>
                                            <td>
                                                รับคนพิการเข้าทำงานตาม ม.33:
                                            </td>
                                            <td>

                                            </td>

                                            <td>
                                                <div align="right">
                                                     <?php echo number_format($hire_numofemp);?>
                                                </div>
                                            <td>
                                            คน
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                ให้สัมปทานฯ ตาม ม.35:
                                            </td>
                                            <td>

                                            </td>

                                            <td>
                                                <div align="right">
                                                     <?php echo number_format($the_35);?>
                                                </div>
                                            <td>
                                            คน
                                            </td>
                                        </tr>


                                         <tr>
                                            <td>
                                                ต้องจ่ายเงินแทนการรับคนพิการ:
                                            </td>
                                            <td>

                                            </td>

                                            <td>
                                                <div align="right">
                                                     <?php echo number_format($extra_employee);?>
                                                </div>
                                            <td>
                                            คน
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td>
												เงินที่ต้องส่งเข้ากองทุน:                                             
                                            
                                            
                                            
                                           
                                            
                                            
                                            </td>
                                            <td>
                                            <div align="right">
                                            <?php echo $extra_employee;?> x <?php 
                                            
                                            //yoes 20151230 
                                            //special for year 2011
                                            
                                            if($this_lawful_year == 2011){
                                                echo ($wage_rate*2) . "/2";
                                            }else{																	
                                                echo $wage_rate;
                                            }
                                            
                                            
                                            ?> x <?php echo $year_date;?> = </div></td>
                                            
                                            <td>
                                            <div align="right">
                                            <?Php echo formatNumber($start_money);?>                                                        </div>
                                            <td>
                                            บาท                                                        
                                            
                                            
                                             <input name="money_per_person" type="hidden" value="<?php echo $wage_rate * $year_date;?>" />           
                                            
                                            </td>
                                        </tr>
										
										
										
										
										
										<?php 
										
											//yoes 20181125
											if($this_lawful_year >= 2018 && $this_lawful_year < 2050){ 											
											
												$m33_total_reduction_array = get33DeductionByCIDYearArray($the_cid, $the_year);
												//print_r($m33_total_reduction);
												$m33_total_reduction = $m33_total_reduction_array[m33_total_reduction];
												$m33_total_missing = $m33_total_reduction_array[m33_total_missing];
												$m33_total_interests = $m33_total_reduction_array[m33_total_interests];
												
												
												
												//yoes 20181108
												$m35_total_reduction_array = get35DeductionByCIDYearArray($the_cid, $the_year);
												$m35_total_reduction = $m35_total_reduction_array[m35_total_reduction];
												$m35_total_missing = $m35_total_reduction_array[m35_total_missing];
												$m35_total_interests = $m35_total_reduction_array[m35_total_interests];
										
										
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
												<?Php echo formatNumber($m33_total_missing); ?>                                                        
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
												<?Php echo formatNumber($m33_total_interests); ?>                                                        
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
												<?Php echo formatNumber($m35_total_missing); ?>                                                        
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
												<?Php echo formatNumber($m35_total_interests); ?>                                                        
											</font>
											
											</div>
											
											
											<td>
												บาท
											</td>
										</tr>
										<?php }?>
										
                                        <tr>
                                            <td>
												ยอดเงินที่จ่ายเข้ากองทุนแล้ว:
                                            </td>
                                            <td>
                                            <div align="right">
                                            = </div></td>
                                            
                                            <td>
                                            <div align="right">
                                            <?Php echo formatNumber($paid_money_origin); ?>                                                        </div>
                                            
                                            
                                            <td>
												บาท                                                        
											</td>
                                        </tr>
										
										
										
										
										
										<tr>
                                            <td>
												ออกใบชำระเงินเป็น เงินต้น แล้ว:
                                            </td>
                                            <td>
                                            <div align="right">
                                            = </div></td>
                                            
                                            <td>
                                            <div align="right">
												
												<font color=orangered>
												<?Php 
												
												
												
													$invoice_money = getFirstItem("
							
														select
															sum(invoice_principal_amount)
														from
															invoices
														where
															invoice_cid = '$the_cid'
															and
															invoice_lawful_year = '$the_year'
															and
															invoice_status = 1
															
													
													");
													
													
													
													
													
													
													echo formatNumber($invoice_money); 
													
													
													?>
												</font>	
												
											</div>
                                            
                                            
                                            <td>
												บาท                                                        
											</td>
                                        </tr>
										
										
										<tr>
                                            <td>
												ออกใบชำระเงินเป็น ดอกเบี้ย แล้ว:
                                            </td>
                                            <td>
                                            <div align="right">
                                            = </div></td>
                                            
                                            <td>
                                            <div align="right">
												
												<font color=orangered>
												<?Php 
												
												
												
													$invoice_interest_money = getFirstItem("
							
														select
															sum(invoice_interest_amount)
														from
															invoices
														where
															invoice_cid = '$the_cid'
															and
															invoice_lawful_year = '$the_year'
															and
															invoice_status = 1
															
													
													");
													
													
													
													
													
													
													echo formatNumber($invoice_interest_money); 
													
													
													?>
												</font>	
												
											</div>
                                            
                                            
                                            <td>
												บาท                                                        
											</td>
                                        </tr>
                                        
                                         
                                        
                                        <tr>
                                            <td>
                                            เงินต้นคงเหลือ 
											
											
											<?php if($invoice_money){ ?>
												<span style="font-size: 11px; color: orangered">
												(หลังจากหักใบชำระเงิน)
												<br> ในกรณีที่ไม่ต้องการรวมยอดจากใบชำระเงิน
												<br> กรุณาลบใบชำระเงินที่ยังไม่ได้ทำการจ่ายเงินออกจากระบบ
												</span>
											<?php }?>
											
											:
											
                                            </td>
                                            <td>
                                            <div align="right">
                                            = </div></td>
                                            
                                            <td>
                                            <div align="right">
                                            
                                            <?Php 
                                                
                                                //update owned money here
                                                $owned_money = $start_money + $m33_total_missing + $m35_total_missing - $paid_money_origin - $invoice_money ;// - $payback_money
												
												//echo "xxx $start_money + $m33_total_missing + $m35_total_missing - $paid_money_origin - $invoice_money  xxx ";
                                                
												if($owned_money < 0){
													echo "0.00";
												}else{
													echo formatNumber($owned_money);
												}
												
                                                
                                            
                                                
                                            
                                            
                                            ?>                                                        </div>
                                            <td>
                                            บาท                                                        </td>
                                        </tr>
                                        
                                       
                                        
                                        <tr>
                                            <td>
                                            วันที่จ่ายเงิน/หรือออกใบจ่ายเงินเข้ากองทุนล่าสุด:                                                        </td>
                                            <td>
                                            <div align="right">
                                           
                                            </div>
                                            
                                            
                                            </td>
                                            <td colspan="2">
                                            <div align="right">
                                             <?php 
                                            
                                            
                                            $the_sql = "select max(paymentDate) from payment, receipt , lawfulness
                                                where 
                                                receipt.RID = payment.RID
                                                and
                                                lawfulness.LID = payment.LID
                                                and
                                                ReceiptYear = '$the_year'
                                                and
                                                lawfulness.CID = '".$the_cid."' 
                                                
                                                and
                                                is_payback != 1
                                                ";
                                            
                                            //echo $the_sql ;
                                            
                                            $actual_interest_date = getFirstItem($the_sql);
                                            //echo "----".$actual_interest_date;
											
											
											
											//yoes 20190410
											//get printed-out pay date
											$the_sql = "select
															max(invoice_payment_date)
														from
															invoices
														where
															invoice_cid = '$the_cid'
															and
															invoice_lawful_year = '$the_year'
															and
															invoice_status = 1
                                                ";
                                            
                                            //echo $the_sql ;
                                            
                                            $printed_interest_date = getFirstItem($the_sql);
                                            
                                            
                                            //////////
                                            //
                                            //
                                            // 	20140224
                                            //	clean this
                                            //
                                            //
                                            //////////
                                            
                                            
                                            //new vars
											//yoes 20190410
											//get max payment for actual or printed
                                            $interest_date_for_calculate_summary = max($actual_interest_date,   $printed_interest_date);
                                            
                                            
                                             if(!$interest_date_for_calculate_summary){
                                               
											   
											   if($the_54_budget_date){
												
													$interest_date_for_calculate_summary = "$the_54_budget_date 00:00:00";
												
												}else{
													
													$interest_date_for_calculate_summary = getDefaultLastPaymentDateByYear($this_lawful_year);	
												}
											   
                                            }
                                                                    
                                            //echo "$this_lawful_year-02-01 00:00:00";		
                                            
                                            
                                            //if last payment date is less than FEB 01 then detaulit it to FEB 01
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
                                            
                                            
                                            
                                            if($interest_date_for_calculate_summary && $interest_date_for_calculate_summary != '0000-00-00 00:00:00'){
                                                echo formatDateThai($interest_date_for_calculate_summary);
                                            }else{
                                                echo "ไม่เคยมีการจ่ายเงิน/ออกใบจ่ายเงิน";
                                            }
                                            
                                            ?>                                                        </div>                                                        </td>
                                             
                                        </tr>
                                        
                                        <?php
                                        
                                        //cal culate interest money
                                        
                                        if($owned_money <= 0){
                                        
                                            //no longer calculate interests
                                            $interest_date = 0;
                                        }else{
                                            $interest_date = getInterestDate($interest_date_for_calculate_summary, $this_lawful_year, $this_date_time);
                                        }
                                        
                                        //echo "<br>$actual_interest_date" . " / ". $this_lawful_year . " / ".  strtotime(date("Y-m-d"))."<br>";
                                        
                                        
                                        //yoes 20170108
                                        //interests for 2011
                                        
                                        if($this_lawful_year >= 2012 || $do_54_budget){ //only show interests when 2012+
                                            $interest_money = doGetInterests($interest_date,($start_money-$paid_money_origin),$year_date);
											
											
											//yoes 20190410
											$interest_money = $interest_money ;
										
											
                                        }else{
                                            $interest_money = 0;
                                        }
                                        
										
										
                                        ?>
                                        
                                        
                                         
                                         <?php 
                                         
                                         
                                         //yoes 20170108
                                        //interests for 2011
                                         
                                         if($this_lawful_year >= 2012 || $do_54_budget){//?>
                                         
                                                <tr>
                                                    <td>
													
													
													
                                                    ดอกเบี้ย ม34 ณ วันที่ <br><strong><?php echo formatDateThai($this_date_time)?></strong>:                                                        </td>
                                                    <td>
                                                    <div align="right">
                                                    <?php echo formatNumber($start_money-$paid_money_origin);?> x 7.5/100/<?php echo $year_date;?> x <?php echo $interest_date;?> = 
                                                    </div>
                                                    
                                                    
                                                    </td>
                                                    <td>
                                                    <div align="right">
                                                    <?Php echo formatNumber($interest_money);?>                                                        </div>                                                        </td>
                                                     <td>
                                                    บาท                                                        </td>
                                                </tr>
                                        <?php }?>
                                        
                                        
                                        
                                        
                                        
                                        <?php 
                                        
                                        //yoes 20170108
                                        //interests for 2011
                                        
                                        
                                        
                                        if($this_lawful_year >= 2012 || $do_54_budget){//?>
                                         <tr>
                                            <td>
                                            ดอกเบี้ยค้างชำระ ม33 +ดอกเบี้ย ม34 และ ม35 (ถ้ามี):
                                            </td>
                                            <td>
                                            <div align="right">
                                            = </div></td>
                                            
                                            <td>
                                            <div align="right">
                                            <?Php 
											
											
											//yoes 20190417
											$total_pending_interest = $total_pending_interest +$m33_total_interests +$m35_total_interests;
											
											
											echo formatNumber($total_pending_interest);?>                                                        </div>
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
                                                $the_final_money = $owned_money + $interest_money +$payback_money +$total_pending_interest
																	
																	
																		
																		//- $invoice_interest_money
												
																	;
                                                //$the_final_money = $owned_money;
												
												/*
												echo "$owned_money + $interest_money +$payback_money +$total_pending_interest
																	
																	+$m33_total_missing +$m33_total_interests
																		+$m35_total_missing +$m35_total_interests";
												*/
												
                                                
                                                //yoes 20130801 - add proper decimal to final monty
                                                //$the_final_money = number_format($the_final_money,2);
                                                $the_final_money = round($the_final_money,2);
                                            
                                                if($the_final_money < 0){
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
                                            
                                            <?Php 
                                            
                                                
                                            
                                                
                                                if(floor($the_final_money) > 0){
                                                    echo "<font color='red'>";
                                                }else if($the_final_money < 0){
                                                    echo "<font color='green'>";
                                                    $the_final_money = $the_final_money * -1;
                                                }else{
                                                    echo "<font>";
                                                }
                                            
                                                echo formatNumber($the_final_money);
                                                
                                                echo "</font>";
                                                
                                                ?>
                                                
                                                
                                             </div>
                                            </td>
                                            
                                             <td>
                                            บาท                                                        </td>
                                        </tr>
                                    </table>
                                    
                                    
                                    </span>
                                
                                
                                
                                
                                
                                
                                
                                <hr>
                                
                                </td>
                                
                             </tr>
                              
                              
                              
                               <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">วันที่ต้องการจ่ายเงิน</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php
											   
											   $selector_name = "the_date";
											  // 
											  
											  if(
											  	$_POST["the_date_year"] 
												&& $_POST["the_date_month"] 
												&& $_POST["the_date_day"]
											  ){
											  
											  $this_date_time = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];
											  
											  }else{
											  
											   $this_date_time = date("Y-m-d");
											   
											  }
											   
											  
											    //*toggles_payment*
											   //
											   include ("date_selector.php");
											   
											   ?>
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
							  
							  <?php 
							  
								if($the_year >= 2018 && $the_year <= 2500){ 
							  
									//yoes 20190206
									//skip this if new law for now
									
									
									$do_hide_invoice_details = 1;
								
								}else{
								  
								  ?>
							  
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">เงินต้นคงเหลือ <br>(ม34+ทดแทน ม33+ทดแทน ม35)</span></td>
                                <td colspan="5">
								
									<?php echo number_format(($owned_money),2);?> 
									
									+ <?php echo number_format(($m33_total_missing),2); ?>
									
									+ <?php echo number_format(($m35_total_missing),2); ?>

									<br>
									=	<b><?php echo number_format(($owned_money +$m33_total_missing +$m35_total_missing ),2);?></b>
									
									
									บาท
									
									</td>
                              </tr>
							  
							  <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ดอกเบี้ยค้างชำระ<br>(ม34+ทดแทน ม33+ทดแทน ม35)</span></td>
                                <td colspan="5">
								
								<?php echo formatNumber($interest_money + $total_pending_interest); ?> 
								
								
								+ <?php echo number_format(($m33_total_interests),2); ?>
									
									+ <?php echo number_format(($m35_total_interests),2); ?>
									
									<br>
									=	<b><?php echo number_format(($interest_money + $total_pending_interest  +$m33_total_interests +$m35_total_interests ),2);?></b>								
									
								
								บาท</td>
                              </tr>
							  
							  <!--
							  <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จ่ายทดแทน ม.33</span></td>
                                <td colspan="3"><?php echo number_format(($m33_total_missing),2);?> บาท</td>
                              </tr>
							   <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ดอกเบี้ยทดแทน ม.33</span></td>
                                <td colspan="3"><?php echo number_format(($m33_total_interests),2);?> บาท</td>
                              </tr>
							  
							  <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จ่ายทดแทน ม.35</span></td>
                                <td colspan="3"><?php echo number_format(($m35_total_missing),2);?> บาท</td>
                              </tr>
							  <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ดอกเบี้ยทดแทน ม.35</span></td>
                                <td colspan="3"><?php echo number_format(($m35_total_interests),2);?> บาท</td>
                              </tr>
							  -->
                              
                               <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ยอดเงินค้างชำระ</span></td>
                                <td colspan="5"><b><font color=red><?php echo formatNumber($the_final_money); ?> บาท</b></td>
                              </tr>
							  
							  
							<?php } //ends if($the_year >= 2018 && $the_year <= 2500){  ?>
							  
							  
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จำนวนเงินที่ต้องการจ่าย</span></td>
                                <td colspan="3">
                                	
                                    <input name="Amount" type="text" id="Amount" style="text-align:right;" value="<?php echo default_value($_POST["Amount"],0);?>" onchange="addCommas('Amount');"/>
                                  <?php
								  	
									include "js_format_currency.php";
								  
								  ?>
                                   บาท
                                
                                </td>
                                
                              </tr>
                            
                              
                              
                              <tr>
                                <td>&nbsp;</td>
                                <td colspan="3"><input type="submit" name="do_calc" value="คำนวณเงิน" /></td>
                              </tr>
                              
                                
                              
                              
                              
                              
                              <tr>
                              	<td colspan="4">
                                	<hr />
                                </td>
                             </tr>
                             
                             
                              
                              
                              </form>
                              
                              
                              
                  <?php if($_POST[do_calc]){?>
                              
                              
                              <?php 
							  	
								$the_amount = deleteCommas($_POST["Amount"]);
							  
							  
							  ?>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จำนวนเงินที่ต้องการจ่าย</span></td>
                                <td colspan="3"><?php echo number_format(default_value($the_amount,0),2);?> บาท</td>
                              </tr>
                              <tr <?php if($do_hide_invoice_details && 1==0){?>style="display:none;"<?php }?> >
                                <td>จ่ายเป็นดอกเบี้ย</td>
                                <td colspan="3"><?php 
								
								
									//yoes 20181125
									$interest_money_total = $interest_money +$total_pending_interest;
									
									//echo "$interest_money + $m33_total_interests + $m35_total_interests;";
								
									//echo $the_amount ;
								
									if($the_amount){
										
										//yoes 20180311
										if($interest_money_total > $the_amount){
											
											$pay_for_interest = $the_amount;
											
										}else{
										
											$pay_for_interest = ($interest_money_total);
										
										}
										
										
									}else{
										$pay_for_interest = 0;	
									}
									
									
									echo number_format($pay_for_interest,2);
									
								?> บาท <?php
								
								
								if($interest_money_total-$pay_for_interest){
									
									echo "<font color='red'>(จ่ายดอกเบี้ยขาด ".number_format($interest_money_total-$pay_for_interest,2)." บาท)</font>";	
									
								}
								
								?></td>
                              </tr>
                              <tr <?php if($do_hide_invoice_details){?>style="display:none;"<?php }?> >
                                <td>จ่ายเป็นเงินต้น</td>
                                <td colspan="3"><?php 
								
								
									//
									
									
									
									if($the_amount){
										
										$pay_for_start = ($the_amount - $interest_money_total);
										
										//echo "$the_amount - $interest_money_total = ";
										
										if($pay_for_start < 0){
											$pay_for_start = 0;
										}
										
									}else{
										$pay_for_start = 0;	
									}
									
									
									//echo $pay_for_start;
									
									
									//yoes 20170307
									//จ่ายเกิน vs จ่ายขาด
									
									
									$owned_money_total = $owned_money + $m33_total_missing + $m35_total_missing;
									
									if($pay_for_start < 0 && 1==0){
										
										//yoes 20180311
										$pay_for_start = 0;
										$missing_paid = $owned_money - $pay_for_start;
										echo number_format($pay_for_start,2);
										
									}elseif($owned_money_total < $pay_for_start){
										
										echo number_format($owned_money_total,2);
										$extra_paid = $pay_for_start- $owned_money_total;
										
									}elseif($owned_money_total > $pay_for_start){
										
										echo number_format($pay_for_start,2);
										$missing_paid = $owned_money_total - $pay_for_start;
										
									}else{
									
										echo number_format($pay_for_start,2);
									
									}
									
									
									
								?> บาท
                                
                                
                                
                                <?php
								
								
								if($extra_paid){
									
									echo "<font color='green'>(จ่ายเงินต้นเกิน ".number_format($extra_paid,2)." บาท)</font>";	
									
								}
								
								?>
                                
                                
                                 <?php
								
								
								if($missing_paid){
									
									echo "<font color='red'>(จ่ายเงินต้นขาด ".number_format($missing_paid,2)." บาท)</font>";	
									
								}
								
								?>
                                
                                
                                
                                </td>
                              </tr>
                             
                              
                              <tr>
                                <td colspan="4">
                                
                                <hr />
                                
                                </td>
                              </tr>
                              
                              
                              <form target="_blank" method="post" action="scrp_generate_invoice.php" enctype="multipart/form-data">
                              
                              
                              <tr>
                                <td valign="top">หมายเหตุ</td>
                                <td colspan="3"><label>
                                <textarea name="invoice_remarks" cols="50" rows="4" id="invoice_remarks"></textarea>
                                </label></td>
                              </tr>
                              
                              
                              <?php if(1==0){?>
                              <tr>
                                <td>เอกสารประกอบ</td>
                                <td colspan="3">
                                  
                                  	<?php 
									
										//$this_id = "$the_invoice_id";
										//$file_type = "invoice_docfile";
										
										//include "doc_file_links.php";
										
										?>
                                  
                                    <input type="file" name="invoice_docfile" id="invoice_docfile" /></td>
                              </tr>
                              <?php }?>
                              
                              <tr>
                                        <td valign="top">เจ้าหน้าที่</td>
                                        <td colspan="3"><?php 
										
											echo $sess_userfullname;
										
										?></td>
                                      </tr>
                                      
                                       <tr>
                                        <td valign="top">วันที่ทำเรื่องจ่ายเงิน</td>
                                        <td colspan="3"><?php 
										
											echo formatDateThai(date("Y-m-d"));
										
										?></td>
                                      </tr>                          

                            
							
							</td>
                          </tr>
                          
                          
                          <tr>
                            <td colspan=2>
                            
                            <hr />
                              <div align="center">
                               
                                <?php 
								
								//**toggles_payment
								//yoes 20160111 -- just allow this
								//if(1==1){ //swap this line with line below
								
								//yoes 20160111 -- just allow this
								//yoes 20160118 -- except for excutives
								if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3 || $can_do_payment)){ // && $pay_for_start > 0 ){
								//if($sub_mode == "payback" && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3)) { 
								
								?>
                                
								
									 <input name="invoice_cid" type="hidden" value="<?php echo $the_cid?>" />                                
									 <input name="invoice_lawful_year" type="hidden" value="<?php echo $the_year?>" />
									
									 <input name="invoice_amount" type="hidden" value="<?php echo $the_amount?>" />
									 <input name="invoice_principal_amount" type="hidden" value="<?php echo $pay_for_start?>" />
									 <input name="invoice_interest_amount" type="hidden" value="<?php echo $pay_for_interest?>" />
									
									 <input name="invoice_userid" type="hidden" value="<?php echo $sess_userid?>" />
									
								   
									 <input name="invoice_payment_date" type="hidden" value="<?php echo $this_date_time?>" />
									
									  <input name="invoice_owned_principal" type="hidden" value="<?php echo $owned_money_total*1?>" />
									  <input name="invoice_owned_interest" type="hidden" value="<?php echo ($interest_money+$total_pending_interest)*1?>" />
									
									<input name="invoice_employees" type="hidden" value="<?php echo $lawfulness_employees;?>" />
									<input name="invoice_33" type="hidden" value="<?php echo $hire_numofemp;?>" />
									<input name="invoice_35" type="hidden" value="<?php echo $the_35;?>" />
									
									<input name="m33_total_missing" type="hidden" value="<?php echo $m33_total_missing;?>" />
									<input name="m33_total_interests" type="hidden" value="<?php echo $m33_total_interests;?>" />
									<input name="m35_total_missing" type="hidden" value="<?php echo $m35_total_missing;?>" />
									<input name="m35_total_interests" type="hidden" value="<?php echo $m35_total_interests;?>" />
									
									<input type="submit" value="เพิ่มข้อมูลการจ่ายเงิน และพิมพ์ใบชำระเงิน" onClick="return confirm('ต้องการเพิ่มข้อมูลใบชำระเงินนี้? กรณีที่เพิ่มข้อมูลใบชำระเงิน แล้วไม่ได้ใช้ชำระเงินจริง จะต้องลบข้อมูลใบชำระเงินที่ไม่ใช้งานออกจากระบบ ไม่เช่นนั้นระบบจะคำนวณยอดเงินที่ต้องจ่าย โดยลบยอดเงินต้นจากใบชำระเงินที่ยังไม่ได้ชำระ');" />
									

									
								
								<?php }elseif($pay_for_start <= 0){?>
                               
								** ไม่สามารถชำระเงินได้ เพราะจำนวนเงินที่ต้องการชำระเป็นการชำระดอกเบี้ย โดยไม่ชำระเงินต้น **
							   
								<?php } ?>
                               
                              </div> 
                    		</td>
                            
                          </tr>
                            
                         </table>
                    
                  
                        
                        
				</form>                        
                    <?php } //end POST do_calc ?>
					
					
					
					
					
					
					
                    
                    
                        
				</td>
      		</tr>
            
            
			
			
			<?php
			
				$invoice_count_sql = "
							
								select
									count(*)
								from
									invoices
								where
									invoice_cid = '$the_cid'
									and
									invoice_lawful_year = '$the_year'
									and
									invoice_status not in (98,99)
									
							
							";
							
				//echo $invoice_count_sql;
							
				$invoice_count = getFirstItem($invoice_count_sql);
			
			
			?>
            
			
			<?php if($invoice_count){?>
            		
             
             <tr>
                <td colspan="2">
                  
                  	
                  	<strong>รายการใบชำระเงินที่มีการพิมพ์ออกไปแล้ว</strong>
					
					
					<div align=right>
						<font color=blue>
						** สามารถลบใบจ่ายเงินรายการล่าสุดที่ยังไม่มีการจ่ายเงินเท่านั้น
						<br> ในกรณีที่ต้องการลบใบจ่ายเงินหลายรายการ ให้ลบรายการล่าสุดก่อน และลบรายการถัดไปตามลำดับ
						</font>
					</div>
                  
                   <table cellpadding="3" style="border-collapse:collapse;" border="1">
						<tr bgcolor="#9C9A9C" align="center">
							 <td>
								<div align="center">
									<span class="column_header">
											ลำดับที่
									</span>
								</div>
							 </td> 
							<td>
								<div align="center">
									<span class="column_header">
										วันที่ออกใบเสร็จ
									</span>
								</div>
							</td>
							<td>
								<div align="center">
									<span class="column_header">
										วันที่ต้องการจ่ายเงิน
									</span>
								</div>
							</td>
							<td><div align="center"> <span class="column_header"> จำนวนเงิน </span> </div></td>
							<td <?php if($do_hide_invoice_details){?>style="display:none;"<?php }?>><div align="center" > <span class="column_header"> เป็นเงินต้น </span> </div></td>
							<td <?php if($do_hide_invoice_details){?>style="display:none;"<?php }?>><div align="center"> <span class="column_header"> เป็นดอกเบี้ย </span> </div></td>
							<td><div align="center"> <span class="column_header"> หมายเหตุ </span> </div></td>
							<td><div align="center"> <span class="column_header">ออกใบจ่ายโดย</span></div></td>
							<td><div align="center"> <span class="column_header">สถานะใบชำระเงิน</span></div></td>
							<td><div align="center"> <span class="column_header"></span></div></td>
							<td></td>
																	
						</tr>
						
						
						
						<?php
						
							$invoice_sql = "
							
								select
									*
								from
									invoices
								where
									invoice_cid = '$the_cid'
									and
									invoice_lawful_year = '$the_year'
									and
									invoice_status not in (98,99)
								order by
									invoice_id asc
									
							
							";
							
							
							$invoice_result = mysql_query($invoice_sql);
						
						
						while($invoice_row = mysql_fetch_array($invoice_result)){
						
							$the_count++;
						
						?>
						
						
							<tr >
							  <td><div align=center><?php echo $the_count;?></div></td>
							  <td><?php echo formatDateThai($invoice_row[invoice_date],0);?></td>
							  <td><?php echo formatDateThai($invoice_row[invoice_payment_date],0);?></td>
							  <td><div align=right><?php echo number_format($invoice_row[invoice_amount],2);?></div></td>
							  <td <?php if($do_hide_invoice_details){?>style="display:none;"<?php }?>><div align=right><?php echo number_format($invoice_row[invoice_principal_amount],2);?></div></td>
							  <td <?php if($do_hide_invoice_details){?>style="display:none;"<?php }?>><div align=right><?php echo number_format($invoice_row[invoice_interest_amount],2);?></div></td>
							  <td><?php echo $invoice_row[invoice_remarks];?></td>
							  <td><?php echo $invoice_row[invoice_userid_text];?></td>
							  <td>
							  
							  
							  <?php if($invoice_row[invoice_status] == 1){
								
									echo "<font color=blue>";
								  
							  }
							  ?>
							  <?php if($invoice_row[invoice_status] == 2){
								
									echo "<font color=green>";
								  
							  }
							  ?>
							  
							  <?php 
							  
								echo formatInvoiceStatus($invoice_row[invoice_status]);
								
							   ?>
							   
							   
							   <?php if($invoice_row[invoice_status] == 1 || $invoice_row[invoice_status] == 2){
								
									echo "</font>";
								  
							  }
							  ?>
							  
							  </td>
							  <td>	
								<div align=center>
							  
									
									<a href="invoice.php?invoice_id=<?php echo $invoice_row[invoice_id];?>" target="_blank" style="font-weight: normal">
										ดูข้อมูลใบจ่ายเงิน
									</a>
									
								</div>	
							  
							  </td>
							  <td>
								<div align=center>
							  
									
									<?php if(($invoice_row[invoice_status] == 1 && $the_count == $invoice_count) 
												|| ($invoice_row[invoice_status] == 1 && $sess_accesslevel == 1)
												|| ($invoice_row[invoice_status] == 1)
											
											){?>
										<a href="scrp_delete_invoice.php?invoice_id=<?php echo $invoice_row[invoice_id];?>" style="font-weight: normal"
											onclick="return confirm('ยืนยันลบใบจ่ายเงินนี้?');"
										>
											ลบใบจ่ายเงิน
										</a>										
									<?php }?>
							  
									
									
								</div>	
							  
							  </td>
						  </tr>
					  
					  
						<?php }?>
					</table>
                </td>
            </tr>  
			
			
			<?php }?>
             
             
             <tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
                </td>
            </tr>  
            
		</table>                            
       
        </td>
    </tr>
    
</table>   



</div><!--end page cell-->
</td>
</tr>
</table>


</body>
</html>