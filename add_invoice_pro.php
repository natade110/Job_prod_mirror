<?php

	include "db_connect.php";

	if($_GET['law_access']){
		$law_access = $_GET["law_access"]*1;
        $sess_accesslevel = 8;
        //echo $sess_accesslevel; exit();
	}

	$the_year = $_GET["for_year"]*1;
	$the_cid = $_GET["search_id"]*1;


	include "session_handler.php";

	//20230425 bank fix MA hire 20-28

	if($_GET["temp"]){
		$table_prefix = "_temp";
		$this_day = $_GET["day"];
		$this_month = $_GET["month"];
		$this_year = $_GET["year"];
	}
	
	$cid_province = getFirstItem("select province from company where cid = '$the_cid'");
	

	//yoes 20181024 --> move this here
	$this_lawful_year = $the_year;
									
	//get lawfulness's details
	$lawfulness_row = getFirstRow("select * from lawfulness where cid = '$the_cid' and year = '$the_year'");
	$the_lid = $lawfulness_row["LID"];
	
	//yoes 20211103 -- reset LID
	resetLawfulnessByLID($the_lid);
	
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
                    
	
					<?php
	
						//syncInvoiceAndReceiptMeta(26406, 73051);
					?>
                    
					
                    
                     <form target="_blank" method="post" action="scrp_generate_invoice.php" onsubmit="delayedReload();" enctype="multipart/form-data">
                    
                    
                    <table border="0" cellpadding="0">
                          <tr>
                            <td><table border="0" style="padding:10px 0 0 50px;" >
                            
                              <?php if($sess_accesslevel !=4){?>
                              <tr>
                                <td colspan="4" >
								
								<span style="font-weight: bold">ใบชำระเงิน</span></td>
                                
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
									<td><span class="style86" style="padding: 10px 0 10px 0;">วันที่ต้องการจ่ายเงิน</span></td>
									<td><span class="style86" style="padding: 10px 0 10px 0;">
									  
									  <strong>	
													<?php
													
													//yoes 20240715
													//open this for all roles
													if($sess_accesslevel == 1 || 1==1){
													
														if($_GET["temp"]){
															$this_date_time = $this_year ."-". $this_month ."-". $this_day;
															
														}else{
															$this_date_time = date("d-m-Y");
														}
														
														
														$selector_name = "add_invoice_date";
														include ("date_selector.php");
													
														?>
														
														<input type="submit" value="คำนวณเงินตามวันที่" onClick="showSelectedValue(event);" />
													
													<?php
													
													
												    }else{
														
														$this_date_time = date("d-m-Y");
														echo formatDateThai(date("Y-m-d"));
														
													}
													
													?> 
													
										</strong>
										
									</span></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								  </tr>
								
                             <!-- <tr>
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
                              </tr> -->
                              
                              
                              
                              
                             
                              
                              
                              
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
                                                ต้องส่งเงินเข้ากองทุนฯแทนการรับคนพิการ ม.34:
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
											<td colspan="4">
												<hr>
											</td>
										</tr>
                                        
                                        <tr>
                                            <td>
												 ต้องจ่ายเงินแทนการรับคนพิการ ม.34:
                                            
                                            </td>
                                            <td>
                                           </td>
                                            
                                            <td>
												<font color='red'>
													<div align="right">
													
													<?php
													
													
														$sql = "
																	select 
																		sum(p_interests)
																		
																	 from 
																			lawful_34_principals$table_prefix
																		where 
																			p_lid = '".$the_lid."'

																		";

														//yoes 20211110
														$interest_pay_34 = getFirstItem($sql);
												
														$sql = "
																	select 
																		*
																	 from 
																			lawful_34_principals$table_prefix
																		where 
																			p_lid = '".$the_lid."'

																		order by
																			p_uid asc
																		limit 0,1

																		";
																		
																		
														//echo $sql;

														
														$pay_34_row = getFirstRow($sql);
													
														//$pay_34_row[p_pending_amount] = max($pay_34_row[p_pending_amount],0);
														$pay_34_row[p_amount] = max($pay_34_row[p_amount],0);
												
													
														echo formatNumber($pay_34_row[p_amount]);
														
														
														
														
														
														?> 
													</div>
												</font>
                                            <td>
                                            บาท                                                        
                                            
                                            
                                             <input name="money_per_person" type="hidden" value="<?php echo $wage_rate * $year_date;?>" />           
                                            
                                            </td>
                                        </tr>
										
										<tr>
                                            <td>
												 ต้องจ่ายดอกเบี้ยเงินแทนการรับคนพิการ ม.34:
                                            
                                            </td>
                                            <td>
                                            </td>
                                            
                                            <td>
												<font color='red'>
													<div align="right">
													<?php
												
													
														//$pay_34_row[p_pending_interests] = max($pay_34_row[p_pending_interests],0);
														
														$pay_34_row[p_pending_interests] = $interest_pay_34;
												
													
														echo formatNumber($pay_34_row[p_pending_interests]);?> 
													</div>
													</div>
												</font>
                                            <td>
                                            บาท                                                       
                                            
                                            
                                             <input name="money_per_person" type="hidden" value="<?php echo $wage_rate * $year_date;?>" />           
                                            
                                            </td>
                                        </tr>
										
										
										
										
										<?php 
										
											//yoes 20181125
											if($this_lawful_year >= 2018 && $this_lawful_year < 2050){
											
												/*$m33_total_reduction_array = get33DeductionByCIDYearArray($the_cid, $the_year);
												//print_r($m33_total_reduction);
												$m33_total_reduction = $m33_total_reduction_array[m33_total_reduction];
												$m33_total_missing = $m33_total_reduction_array[m33_total_missing];
												$m33_total_interests = $m33_total_reduction_array[m33_total_interests];*/
												
												
												
												//yoes 20211020	--> use this instead
												$m33_principal_row = getFirstRow("

													select
														sum(p_amount) as the_principals
														, sum(p_interests) as the_interests
														, sum(p_pending_amount) as the_pending_principals
														, sum(p_pending_interests) as the_pending_interests
													from
														lawful_33_principals$table_prefix
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
														lawful_35_principals$table_prefix
													where
														p_lid = '$the_lid'

												");

																		
												
												
												//yoes 20181108
												/*$m35_total_reduction_array = get35DeductionByCIDYearArray($the_cid, $the_year);
												$m35_total_reduction = $m35_total_reduction_array[m35_total_reduction];
												$m35_total_missing = $m35_total_reduction_array[m35_total_missing];
												$m35_total_interests = $m35_total_reduction_array[m35_total_interests];*/
										
										
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
												<?Php echo formatNumber($m33_principal_row[the_principals]); ?>                                                        
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
												<?Php echo formatNumber($m33_principal_row[the_interests]); ?>                                                       
											</font>
											
											</div>
											
											
											<td>
												บาท
											</td>
										</tr>
										
										
										<?php //yoes 20230108
										//ดูว่ามีการจ่ายเงินเกินในบาง 33 ไหม
										
										
											$check_minus_pending = getFirstItem("
												
												SELECT 
													count(*)
												FROM 
													`lawful_33_principals$table_prefix`
												WHERE 
													`p_lid` = '$the_lid'
													and
													p_pending_amount < 0
											
											");
										
										
											if($check_minus_pending){
												
												
										?>
										
										<tr>
											<td colspan=4>
											
												<font color="orangered">**  พบการการแทนเงิน มาตรา 33 ที่มีการแทนการชำระเงินเกินกว่าที่ต้องจ่ายจริง <br>- กรุณาตรวจสอบข้อมูลการแทนเงิน ม33 ในหน้าจอการปฏิบัติตามกฎหมาย **</font>
												
												<table  cellpadding="3" style="border-collapse:collapse; " border="1">
													
													
													
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
																	จ่ายสำหรับ
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	ช่วงวันที่
																</span>
															</div>
														</td>
																											
														
														<td>
															<div align="center">
																<span class="column_header"> จ่ายเกิน </span>
															</div>
														</td>
														
													</tr>
													
													<?php
													
														$minus_sql = "
												
															SELECT 
																*
																, a.p_lid as the_p_lid
																, a.p_from as the_p_from
																, a.p_to as the_p_to
																, b.le_name as le_from_name
																, c.le_name as le_to_name
															from
																lawful_33_principals$table_prefix a
																
																	join
																		lawfulness law
																		on
																		a.p_lid = law.lid
																
																	left join
																		lawful_employees b 
																		on 
																		a.p_from = b.le_id
																	left join
																		lawful_employees c 
																		on 
																		a.p_to = c.le_id
															WHERE 
																p_lid = '$the_lid'
																and
																p_pending_amount < 0
														
														";
														
														//echo $minus_sql;
													
														$check_minus_pending_result = mysql_query($minus_sql);
														
														$minus_count = 0;
														
														while($minus_row = mysql_fetch_array($check_minus_pending_result)){
															
															$minus_count++;
														
														
														?>
												
													<tr>
														<td>			
															<div align="center">
																
																<?php echo $minus_count;?>
																
															</div>
														</td>
														
														
														<td>															
															<?php echo $minus_row[le_from_name]?$minus_row[le_from_name]:"1 ม.ค.".($the_year+543);?> 
															
															<?php if($minus_row[le_from_name] && $minus_row[le_to_name] || 1==1){
															
																echo "->";
														
															}?> 
															
															<?php echo $minus_row[le_to_name]?$minus_row[le_to_name]:"31 ธ.ค.".($the_year+543);?> 
														</td>
														<td>															
															<?php echo formatDateThai($minus_row[p_date_from],0);?> -> <?php echo formatDateThai($minus_row[p_date_to],0);?>
														</td>
														<td>														
															<div align="right">
																<font color=red>
																	<b>
																		<?php echo formatNumber($minus_row[p_pending_amount]);?>
																	</b>
																</font>
																
																
															</div>
														</td>
														
													
													</tr>
													
													<?php } // end while while($minus_row}?>
													
													
												</table>
												
												
											</td>
										</tr>
										
										
										<?php } //ends check minus pending?>
										
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
												<?Php echo formatNumber($m35_principal_row[the_principals]); ?>                                                     
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
												<?Php echo formatNumber($m35_principal_row[the_interests]); ?>                                                           
											</font>
											
											</div>
											
											
											<td>
												บาท
											</td>
										</tr>
										
										
										<?php //yoes 20230108
										//ดูว่ามีการจ่ายเงินเกินในบาง 33 ไหม
										
										
											$check_minus_pending = getFirstItem("
												
												SELECT 
													count(*)
												FROM 
													`lawful_35_principals$table_prefix`
												WHERE 
													`p_lid` = '$the_lid'
													and
													p_pending_amount < 0
											
											");
										
										
											if($check_minus_pending){
												
												
										?>
										
										<tr>
											<td colspan=4>
											
												<font color="orangered">**  พบการการแทนเงิน มาตรา 35 ที่มีการแทนการชำระเงินเกินกว่าที่ต้องจ่ายจริง <br>- กรุณาตรวจสอบข้อมูลการแทนเงิน ม35 ในหน้าจอการปฏิบัติตามกฎหมาย **</font>
												
												<table  cellpadding="3" style="border-collapse:collapse; " border="1">
													
													
													
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
																	จ่ายสำหรับ
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	ช่วงวันที่
																</span>
															</div>
														</td>
																											
														
														<td>
															<div align="center">
																<span class="column_header"> จ่ายเกิน </span>
															</div>
														</td>
														
													</tr>
													
													<?php
													
														$minus_sql = "
												
															SELECT 
																*
																, a.p_lid as the_p_lid
																, a.p_from as the_p_from
																, a.p_to as the_p_to
																, b.curator_name as curator_from_name
																, c.curator_name as curator_to_name
															from
																lawful_35_principals$table_prefix a
																	
																	join lawfulness law
																		on
																		a.p_lid = law.lid
																	
																	left join
																		curator b 
																		on 
																		a.p_from = b.curator_id
																	left join
																		curator c 
																		on 
																		a.p_to = c.curator_id
															WHERE 
																p_lid = '$the_lid'
																and
																p_pending_amount < 0
														
														";
														
														//echo $minus_sql;
													
														$check_minus_pending_result = mysql_query($minus_sql);
														
														$minus_count = 0;
														
														while($minus_row = mysql_fetch_array($check_minus_pending_result)){
															
															$minus_count++;
														
														
														?>
												
													<tr>
														<td>			
															<div align="center">
																
																<?php echo $minus_count;?>
																
															</div>
														</td>
														
														
														<td>															
															<?php echo $minus_row[curator_from_name]?$minus_row[curator_from_name]:"1 ม.ค.".($the_year+543);?> 
															
															<?php if($minus_row[curator_from_name] && $minus_row[curator_to_name] || 1==1){
															
																echo "->";
														
															}?> 
															
															<?php echo $minus_row[curator_to_name]?$minus_row[curator_to_name]:"31 ธ.ค.".($the_year+543);?> 
														</td>
														<td>															
															<?php echo formatDateThai($minus_row[p_date_from],0);?> -> <?php echo formatDateThai($minus_row[p_date_to],0);?>
														</td>
														<td>														
															<div align="right">
																<font color=red>
																	<b>
																		<?php echo formatNumber($minus_row[p_pending_amount]);?>
																	</b>
																</font>
																
																
															</div>
														</td>
														
													
													</tr>
													
													<?php } // end while while($minus_row}?>
													
													
												</table>
												
												
											</td>
										</tr>
										
										
										<?php } //ends check minus pending?>
										
										
										
										<tr>
											<td colspan="4">
												<hr>
											</td>
										</tr>
										
										<?php }?>
										
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
												
													$total_money_to_pay = 
														0
														//+$pay_34_row[p_pending_amount]
														+$pay_34_row[p_amount]
														//+$pay_34_row[p_pending_interests]
														+$interest_pay_34
														+$m33_principal_row[the_principals]
														+$m33_principal_row[the_interests]
														+$m35_principal_row[the_principals]
														+$m35_principal_row[the_interests];
												
													echo formatNumber(
										
													$total_money_to_pay
										
													); ?> 
											</font>
											
											</div>
											
											
											<td>
												บาท
											</td>
										</tr>
										
										
										
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
												
													echo formatNumber($paid_amount); 
												
													//$paid_amount = 0;
												
												?>
											</div>
                                            
                                            
                                            <td>
												บาท                                                        
											</td>
                                        </tr>
										
										<tr>
                                            <td>
												ออกใบชำระเงินแล้ว:
                                            </td>
                                            <td>
                                            <div align="right">
                                            = </div></td>
                                            
                                            <td>
                                            <div align="right">
                                            	<?Php 
													
												$invoice_money = getFirstItem("
							
														select
															sum(invoice_amount)
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
											</div>
                                            
                                            
                                            <td>
												บาท                                                        
											</td>
                                        </tr>
										
										 <tr>
                                            <td>
												เหลือต้องจ่าย
                                            </td>
                                            <td>
                                            <div align="right">
                                            = </div></td>
                                            
                                            <td>
                                            <div align="right">
                                            	
													<font color="orangered"><?php
												
														echo formatNumber($total_money_to_pay-$paid_amount-$invoice_money);

													?></font>
												
											</div>
                                            
                                            
                                            <td>
												บาท                                                        
											</td>
                                        </tr>
										
										<tr>
											<td colspan="4">
												<hr>
											</td>
										</tr>
										
										 <tr>
											<td colspan="4">


												<strong>จำนวนเงินที่ต้องการจ่าย</strong>
												<br>
												
												 <?php

													include_once "js_format_currency.php";

												  ?>
												
												
												<?php 
												
												
												$sql = "
															
															select
																*
																, a.p_amount as the_34_p_amount
																, a.p_interests as the_34_p_interests
																, a.p_lid as the_p_lid
																, a.p_from as the_p_from
																, a.p_to as the_p_to
																/*, GREATEST(p_pending_amount - coalesce(ini.ini_principal,0),0) as p_pending_amount
																, GREATEST(p_pending_interests - coalesce(ini.ini_interests,0),0) as p_pending_interests*/
																
																, p_pending_amount - coalesce(inv.ini_principal,0) as p_pending_amount
																, p_pending_interests - coalesce(inv.ini_interests,0) as p_pending_interests
																
															from
																lawful_34_principals$table_prefix a
																
																
																	join lawfulness zz
																		on
																		a.p_lid = zz.lid

																	LEFT JOIN 
																		(
																			select
																				invoice_cid
																				, invoice_lawful_year
																				, invoice_status
																				, sum(ini_principal) as ini_principal
																				, sum(ini_interests) as ini_interests
																				, sum(ini_amount) as ini_amount
																			from
																				invoices inv
																					join
																						invoice_items ini
																							ON ini.invoice_id = inv.invoice_id
																			where
																				ini_type = 34
																				/*and
																				invoice_cid = 12647
																				and
																				invoice_lawful_year = 2021	*/
																				AND
																				invoice_status = 1
																			group by
																				invoice_cid
																				, invoice_lawful_year
																				, invoice_status
																		) inv
																		 
																			ON inv.invoice_cid = zz.cid
																			and inv.invoice_lawful_year = zz.year
																			 AND invoice_status = 1
																		
																	
																	
															where
																a.p_lid = '$the_lid'
																														
																
															order by
																a.p_uid desc
																
															limit 0,1
														
														";
													
														//echo $sql;
													
														$pay_34_row = getFirstRow($sql);
														
														//print_r($pay_34_row);
														
														//yoes 20220214
														//if ...
														// $interest_pay_34 = เป็นค่า sum interests ทั้งหมด
														// ** อาจเกิดกรณีที่ การจ่ายก่อนหน้าเป็นการจ่าย "ดอกเท่านั้นได้"
														// ดังนั้น interst ตรงนี้ ต้องเป็น sum interests ของทั้งหมด / ห้าม assume ว่าเป็นการจ่ายดอกครบ
														//ตย -> บริษัท ลลิล พร็อพเพอร์ตี้ จำกัด(มหาชน)  ปี 59
														//แต่ไม่เสมอไป -- leave be for now
														
														//yoes 20240625
														//yoes 20240710
														//ตย -> บริษัท ธ ศักดิ์สิทธิ์ อัลลอย สเตนเลส อินเตอร์ จำกัด ปี 59
														// yoes 20241002
														// ตบ  	บริษัท ย่งเส็ง อินเตอร์เนชั่นแนล เทรดดิ้ง จำกัด  ปี 64 - จ่ายดอก 34 รอบแรกไม่ครบ แต่จอแสดงแสดงแค่ top 1 interest เพราะ assume ว่าผู้ใ้งานจะจ่ายยอดครบตลอด
														if(
														$the_lid == 112378
														||
														$the_lid == 2050627437
														||
														($the_lid == 118163 && 1 == 0)
														){
														
															$sql = "
																		select 
																			sum(p_pending_interests)
																			
																		 from 
																				lawful_34_principals
																			where 
																				p_lid = '".$the_lid."'

																			";

															//yoes 20211110
															$pay_34_row[p_pending_interests] = getFirstItem($sql);
														
														}
														
														//yoes 20240625
														//yoes 20240710
														//ตย -> บริษัท ธ ศักดิ์สิทธิ์ อัลลอย สเตนเลส อินเตอร์ จำกัด ปี 59
														if(
														
														$the_lid == 118163 && $pay_34_row[p_pending_interests]
														
														&& date("Y-m-d") == "2024-07-11"
														
														){
														
															
															$pay_34_row[p_pending_interests] = 24390;
														
														}
														
														//yoes 20241106
														//ตย -> บริษัท เหล็กร้อยล้าน จำกัด  ปี 57
														if(
														
														(
                                                                $the_lid == 93964
                                                                //|| $the_lid = 2050620785
                                                        )
														&&
														$interest_pay_34 > $paid_amount
														
														){
														
															
															//$pay_34_row[p_pending_interests] = 24390;
															
															$pay_34_row[p_pending_interests] = ($interest_pay_34)-$paid_amount; //
															//echo "($pay_34_row[p_amount] + $interest_pay_34)-$paid_amount;";

														
														}
														
												
												//if($pay_34_row[p_pending_amount] > 0){ 
												if($pay_34_row[p_pending_amount] > 0 || 1==1){ 
												
													
													//$pay_34_key = "";
													$pay_34_key = "".$the_lid."x0x0";
												
												?>												
												<strong>ม.34</strong>	
												
												<?php
												
													if($pay_34_row[p_pending_amount] == 0){
												
												?>
												
													<br>
													<font color=blue>
														--- ไม่พบยอดคงเหลือที่ต้องจ่าย ม34 --- 
														<span style="font-size: 9px;"><br>ในกรณีที่ต้องการจ่ายเงินเพิ่มเติมในกรณีใดๆ <a href="#" onClick="$('#to_pay_m34_table').toggle(); return false;">คลิกที่นี่</a> เพื่อกรอกจำนวนเงินที่ต้องการจ่าย --- </span>
													</font>
													<br>
												<?php }?>
												
												
												<table id="to_pay_m34_table" cellpadding="3" style="border-collapse:collapse; <?php if($pay_34_row[p_pending_amount] == 0){ echo "display: none;";} ?>" border="1">
													
													
													
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
																	เงินต้น
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	ดอกเบี้ย
																</span>
															</div>
														</td>
														<td>
															<div align="center"> <span class="column_header"> ต้องการจ่าย </span> </div>
														</td>
														<td><div align="center"> <span class="column_header"> จ่ายเป็นดอกเบี้ย </span> </div></td>
														
														<td><div align="center"> <span class="column_header"> จ่ายเป็นเงินต้น </span> </div></td>
														
														<td><div align="center"> <span class="column_header"> คงเหลือหลังชำระเงิน </span> </div></td>
														
														<td><div align="center"> <span class="column_header"> หมายเหตุ </span> </div></td>
														
													</tr>
													
													
													<tr>
														<td>			
															<div align="center">
																1
															</div>
														</td>
														
														
														<td>					
															<div align="right">
																<input type="hidden" name="pay_34_principals_<?php echo $pay_34_key;?>" 
																	 value="<?php echo $pay_34_row[p_pending_amount];?>" >
																<?php echo formatNumber($pay_34_row[p_pending_amount]);?>
															</div>
														</td>
														<td>														
															<div align="right">
																<input type="hidden" name="pay_34_interests_<?php echo $pay_34_key;?>" 
																	 value="<?php echo $pay_34_row[p_pending_interests];?>" >
																<?php echo formatNumber($pay_34_row[p_pending_interests]);?>
															</div>
														</td>
														
														<td>					
															<div align="right">
																	 <input 
																			id="pay_34_amount_<?php echo $pay_34_key;?>" 
																			v-model="pay_34_amount_<?php echo $pay_34_key;?>"
																			name="pay_34_amount_<?php echo $pay_34_key;?>" 
																			type="text"  style="text-align:right;" value="<?php 
																			
																			
																				//yoes 20220304 -> if older years then show 0 instead of ยอดติดลบ
																				if($this_lawful_year < 2022 && ($pay_34_row[p_pending_amount]+$pay_34_row[p_pending_interests]) < 0){
																					echo "0";
																				}else{
																					echo default_value(formatNumber($pay_34_row[p_pending_amount]+$pay_34_row[p_pending_interests]),0);																				
																				}
																				
																				?>" onchange="addCommas('pay_34_amount');"/>
																	  
																	   บาท
															</div>
															<?php //echo $pay_34_row[p_pending_amount]."+".$pay_34_row[p_pending_interests];?>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_34_interests_<?php echo $pay_34_key;?>.replace(/,/g, ''))																		
																	).toLocaleString()
																
																}}
															</div>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_34_amount_<?php echo $pay_34_key;?>.replace(/,/g, ''))		
																		-parseFloat(pay_34_interests_<?php echo $pay_34_key;?>.replace(/,/g, ''))
																	).toLocaleString()
																
																}}
															</div>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_34_total_<?php echo $pay_34_key;?>.replace(/,/g, ''))																		
																		-parseFloat(pay_34_amount_<?php echo $pay_34_key;?>.replace(/,/g, ''))
																		
																	).toLocaleString()
																
																}}
															</div>
														</td>
														<td>
														
															<?php 
																
																if($pay_34_row[p_pending_amount] < 0){
																
																	echo "<font color=blue>
																		* พบจำนวนการระบุจ่าย ม34 เกินจากที่ต้องจ่าย - กรุณาทำการตรวจสอบว่ามีการทำแทนใบเสร็จ ม33/ม35 ครบถ้วนแล้วหรือไม่
																	</font>";
																	
																}
															
															?>
														
														</td>
													
													</tr>
													
													
												</table>
												
												<?php 
														
													$vue_34 .= ", pay_34_amount_$pay_34_key : $('#pay_34_amount_$pay_34_key').val()";
													$vue_34 .= ", pay_34_principal_$pay_34_key : '$pay_34_row[p_pending_amount]'";
													//$vue_34 .= ", pay_34_principal_$pay_34_key : this.pay_34_amount_$pay_34_key+this.pay_34_amount_$pay_34_key";
													$vue_34 .= ", pay_34_interests_$pay_34_key : '$pay_34_row[p_pending_interests]'";
													$vue_34 .= ", pay_34_total_$pay_34_key : '".($pay_34_row[p_pending_amount]+$pay_34_row[p_pending_interests])."'";
														
													$vue_34_parse .= " +parseFloat(pay_34_amount_$pay_34_key.replace(/,/g, ''))";
													$vue_34_parse_this .= " +parseFloat(this.pay_34_amount_$pay_34_key.replace(/,/g, ''))";
													
												?>
												
												
												<?php } //ends if($pay_34_row[p_pending_amount]){?>
												
												
												
												<?php
												
													if($this_lawful_year < 2018){
														
														$no_3335_filter = " and 1=0";
														
													}
												
													$sql = "
															
															select
																*
																, a.p_lid as the_p_lid
																, a.p_from as the_p_from
																, a.p_to as the_p_to
																, p_pending_amount - coalesce(inv.ini_principal,0) as p_pending_amount
																, p_pending_interests - coalesce(inv.ini_interests,0) as p_pending_interests
																, b.le_name as le_from_name
																, c.le_name as le_to_name
															from
																lawful_33_principals$table_prefix a
																
																	join
																		lawfulness law
																		on
																		a.p_lid = law.lid
																
																	left join
																		lawful_employees b 
																		on 
																		a.p_from = b.le_id
																	left join
																		lawful_employees c 
																		on 
																		a.p_to = c.le_id
																	
																	left join
																		(
																			select
																				invoice_cid
																				, invoice_lawful_year
																				, invoice_status
																				, p_from
																				, p_to
																				, sum(ini_principal) as ini_principal
																				, sum(ini_interests) as ini_interests
																				, sum(ini_amount) as ini_amount
																			from
																				invoices inv
																					join
																						invoice_items ini
																							ON ini.invoice_id = inv.invoice_id
																			where
																				ini_type = 33
																				AND
																				invoice_status = 1
																			group by
																				invoice_cid
																				, invoice_lawful_year
																				, invoice_status
																				, p_from
																				, p_to
																		) inv
																		
																			ON 
																				inv.invoice_cid = law.cid																				
																				and 
																				inv.invoice_lawful_year = law.year																						
																				and
																				inv.p_from = a.p_from
																				and
																				inv.p_to = a.p_to
																			
																				AND
																				invoice_status = 1
																	
																	
															where
																a.p_lid = '$the_lid'
																and
																
																p_pending_amount - coalesce(inv.ini_principal,0) > 0
																
																$no_3335_filter

																
															order by
																b.le_id asc
																, p_date_from asc
														
														";
													
														//echo $sql;
													
														$pay_33_result = mysql_query($sql);
												
												
												?>
												<?php if(mysql_num_rows($pay_33_result) > 0){ ?>												
												<strong>ม.33</strong>
												
												<table cellpadding="3" style="border-collapse:collapse;<?php if(mysql_num_rows($pay_33_result) <= 0){echo "display: none;";} ?>" border="1">
													
													
													
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
																	จ่ายสำหรับ
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	ช่วงวันที่
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	เงินต้น
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	ดอกเบี้ย
																</span>
															</div>
														</td>
														<td><div align="center"> <span class="column_header"> ต้องการจ่าย </span> </div></td>
														
														<td><div align="center"> <span class="column_header"> จ่ายเป็นดอกเบี้ย </span> </div></td>
														
														<td><div align="center"> <span class="column_header"> จ่ายเป็นเงินต้น </span> </div></td>
														
														<td><div align="center"> <span class="column_header"> คงเหลือหลังชำระเงิน </span> </div></td>
														
													</tr>
													
													<?php
													
														
													
														$pay_33_count = 0;
													
														while($pay_33_row = mysql_fetch_array($pay_33_result)){
													
															$pay_33_count++;
															
															$pay_33_key = "".$pay_33_row[the_p_lid]."x".$pay_33_row[the_p_from]."x".$pay_33_row[the_p_to];
															
													?>
													
													<tr>
														<td>			
															<div align="center">
																<?php echo $pay_33_count;?>														
															</div>
														</td>
														<td>															
															<?php echo $pay_33_row[le_from_name]?$pay_33_row[le_from_name]:"1 ม.ค.".($the_year+543);?> 
															
															<?php if($pay_33_row[le_from_name] && $pay_33_row[le_to_name] || 1==1){
															
																echo "->";
														
															}?> 
															
															<?php echo $pay_33_row[le_to_name]?$pay_33_row[le_to_name]:"31 ธ.ค.".($the_year+543);?> 
														</td>
														<td>															
															<?php echo formatDateThai($pay_33_row[p_date_from],0);?> -> <?php echo formatDateThai($pay_33_row[p_date_to],0);?>
														</td>
														<td>														
															<div align="right">
																<?php echo formatNumber($pay_33_row[p_pending_amount]);?>
																
																<input type="hidden" name="pay_33_principals_<?php echo $pay_33_key;?>" 
																	  value="<?php echo $pay_33_row[p_pending_amount];?>" >
																
															</div>
														</td>
														<td>					
															<div align="right">
																<?php echo formatNumber($pay_33_row[p_pending_interests]);?>
																
																<input type="hidden" name="pay_33_interests_<?php echo $pay_33_key;?>" 
																	  value="<?php echo $pay_33_row[p_pending_interests];?>" >
															</div>
														</td>
														<td>					
															<div align="right">
																	 <input 
																			id="pay_33_amount_<?php echo $pay_33_key;?>" 
																			v-model="pay_33_amount_<?php echo $pay_33_key;?>"
																			name="pay_33_amount_<?php echo $pay_33_key;?>" 
																			type="text"  style="text-align:right;" value="<?php echo default_value(formatNumber($pay_33_row[p_pending_amount]+$pay_33_row[p_pending_interests]),0);?>" onchange="addCommas('pay_33_amount');"/>
																	  
																	   บาท
																
																		<!-- - pay_33_amount_<?php echo $pay_33_key;?> -->
																
																	<!--<input 
																	   type="text"  v-model="pay_33_principal_<?php echo $pay_33_key;?>"  />-->
															</div>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_33_interests_<?php echo $pay_33_key;?>.replace(/,/g, ''))																		
																	).toLocaleString()
																
																}}
															</div>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_33_amount_<?php echo $pay_33_key;?>.replace(/,/g, ''))		
																		-parseFloat(pay_33_interests_<?php echo $pay_33_key;?>.replace(/,/g, ''))
																	).toLocaleString()
																
																}}
															</div>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_33_total_<?php echo $pay_33_key;?>.replace(/,/g, ''))																		
																		-parseFloat(pay_33_amount_<?php echo $pay_33_key;?>.replace(/,/g, ''))
																		
																	).toLocaleString()
																
																}}
															</div>
														</td>
													
													</tr>
													
													<?php 
														
														$vue_33 .= ", pay_33_amount_$pay_33_key : $('#pay_33_amount_$pay_33_key').val()";
														$vue_33 .= ", pay_33_principal_$pay_33_key : '$pay_33_row[p_pending_amount]'";
														//$vue_33 .= ", pay_33_principal_$pay_33_key : this.pay_33_amount_$pay_33_key+this.pay_33_amount_$pay_33_key";
														$vue_33 .= ", pay_33_interests_$pay_33_key : '$pay_33_row[p_pending_interests]'";
														$vue_33 .= ", pay_33_total_$pay_33_key : '".($pay_33_row[p_pending_amount]+$pay_33_row[p_pending_interests])."'";
															
														$vue_33_parse .= " +parseFloat(pay_33_amount_$pay_33_key.replace(/,/g, ''))";
														$vue_33_parse_this .= " +parseFloat(this.pay_33_amount_$pay_33_key.replace(/,/g, ''))";
														
														}?>
													
													
												</table>
												<?php }?>
												
												<?php
												
												
													$sql = "

															select
																*
																, a.p_lid as the_p_lid
																, a.p_from as the_p_from
																, a.p_to as the_p_to
																, p_pending_amount - coalesce(inv.ini_principal,0) as p_pending_amount
																, p_pending_interests - coalesce(inv.ini_interests,0) as p_pending_interests
																, b.curator_name as curator_from_name
																, c.curator_name as curator_to_name
															from
																lawful_35_principals$table_prefix a
																	
																	join lawfulness law
																		on
																		a.p_lid = law.lid
																	
																	left join
																		curator b 
																		on 
																		a.p_from = b.curator_id
																	left join
																		curator c 
																		on 
																		a.p_to = c.curator_id
																	
																	
																	
																	LEFT JOIN 

																		(
																			select
																				invoice_cid
																				, invoice_lawful_year
																				, invoice_status
																				, p_from
																				, p_to
																				, sum(ini_principal) as ini_principal
																				, sum(ini_interests) as ini_interests
																				, sum(ini_amount) as ini_amount
																			from
																				invoices inv
																					join
																						invoice_items ini
																							ON ini.invoice_id = inv.invoice_id
																			where
																				ini_type = 35
																				AND
																				invoice_status = 1
																			group by
																				invoice_cid
																				, invoice_lawful_year
																				, invoice_status
																				, p_from
																				, p_to
																		) inv
																		
																			ON 
																				inv.invoice_cid = law.cid																				
																				and 
																				inv.invoice_lawful_year = law.year																						
																				and
																				inv.p_from = a.p_from
																				and
																				inv.p_to = a.p_to
																			
																				AND
																				invoice_status = 1


															where
																a.p_lid = '$the_lid'
																and
																p_pending_amount - coalesce(inv.ini_principal,0) > 0
																
																$no_3335_filter
																
															order by
																b.curator_id asc
																, p_date_from asc

														";

														

														$pay_35_result = mysql_query($sql);
												
												
												?>
												
												
												<?php if(mysql_num_rows($pay_35_result) > 0){ ?>												
												<strong>ม.35</strong>
												
												
												<table cellpadding="3" style="border-collapse:collapse; <?php if(mysql_num_rows($pay_35_result) <= 0){ echo "display: none;";} ?>" border="1">
													
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
																	จ่ายสำหรับ
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	ช่วงวันที่
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	เงินต้น
																</span>
															</div>
														</td>
														<td>
															<div align="center">
																<span class="column_header">
																	ดอกเบี้ย
																</span>
															</div>
														</td>
														<td><div align="center"> <span class="column_header"> ต้องการจ่าย </span> </div></td>

														<td><div align="center"> <span class="column_header"> จ่ายเป็นดอกเบี้ย </span> </div></td>

														<td><div align="center"> <span class="column_header"> จ่ายเป็นเงินต้น </span> </div></td>

														<td><div align="center"> <span class="column_header"> คงเหลือหลังชำระเงิน </span> </div></td>

													</tr>

													<?php
														

														$pay_35_count = 0;

														while($pay_35_row = mysql_fetch_array($pay_35_result)){

															$pay_35_count++;

															$pay_35_key = "".$pay_35_row[the_p_lid]."x".$pay_35_row[the_p_from]."x".$pay_35_row[the_p_to];

													?>

													<tr>
														<td>			
															<div align="center">
																<?php echo $pay_35_count;?>														
															</div>
														</td>
														<td>															
															<?php echo $pay_35_row[curator_from_name]?$pay_35_row[curator_from_name]:"1 ม.ค.".($the_year+543);?> 
															-> 
															<?php echo $pay_35_row[curator_to_name]?$pay_35_row[curator_to_name]:"31 ธ.ค.".($the_year+543);?> 
														</td>
														<td>															
															<?php echo formatDateThai($pay_35_row[p_date_from],0);?> -> <?php echo formatDateThai($pay_35_row[p_date_to],0);?>
														</td>
														<td>														
															<div align="right">
																<?php echo formatNumber($pay_35_row[p_pending_amount]);?>

																<input type="hidden" name="pay_35_principals_<?php echo $pay_35_key;?>" 
																	  value="<?php echo $pay_35_row[p_pending_amount];?>" >

															</div>
														</td>
														<td>					
															<div align="right">
																<?php echo formatNumber($pay_35_row[p_pending_interests]);?>

																<input type="hidden" name="pay_35_interests_<?php echo $pay_35_key;?>" 
																	  value="<?php echo $pay_35_row[p_pending_interests];?>" >
															</div>
														</td>
														<td>					
															<div align="right">
																	 <input 
																			id="pay_35_amount_<?php echo $pay_35_key;?>" 
																			v-model="pay_35_amount_<?php echo $pay_35_key;?>"
																			name="pay_35_amount_<?php echo $pay_35_key;?>" 
																			type="text"  style="text-align:right;" value="<?php echo default_value(formatNumber($pay_35_row[p_pending_amount]+$pay_35_row[p_pending_interests]),0);?>" onchange="addCommas('pay_35_amount');"/>

																	   บาท
																
																		<!-- - pay_35_amount_<?php echo $pay_35_key;?> -->

																	<!--<input 
																	   type="text"  v-model="pay_35_principal_<?php echo $pay_35_key;?>"  />-->
															</div>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_35_interests_<?php echo $pay_35_key;?>.replace(/,/g, ''))																		
																	).toLocaleString()

																}}
															</div>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_35_amount_<?php echo $pay_35_key;?>.replace(/,/g, ''))		
																		-parseFloat(pay_35_interests_<?php echo $pay_35_key;?>.replace(/,/g, ''))
																	).toLocaleString()

																}}
															</div>
														</td>
														<td>					
															<div align="right">
																{{
																	(
																		parseFloat(pay_35_total_<?php echo $pay_35_key;?>.replace(/,/g, ''))																		
																		-parseFloat(pay_35_amount_<?php echo $pay_35_key;?>.replace(/,/g, ''))

																	).toLocaleString()

																}}
															</div>
														</td>

													</tr>

													<?php 

														$vue_35 .= ", pay_35_amount_$pay_35_key : $('#pay_35_amount_$pay_35_key').val()";
														$vue_35 .= ", pay_35_principal_$pay_35_key : '$pay_35_row[p_pending_amount]'";
														//$vue_35 .= ", pay_35_principal_$pay_35_key : this.pay_35_amount_$pay_35_key+this.pay_35_amount_$pay_35_key";
														$vue_35 .= ", pay_35_interests_$pay_35_key : '$pay_35_row[p_pending_interests]'";
														$vue_35 .= ", pay_35_total_$pay_35_key : '".($pay_35_row[p_pending_amount]+$pay_35_row[p_pending_interests])."'";

														$vue_35_parse .= " +parseFloat(pay_35_amount_$pay_35_key.replace(/,/g, ''))";
														$vue_35_parse_this .= " +parseFloat(this.pay_35_amount_$pay_35_key.replace(/,/g, ''))";

														}?>


												</table>
												<?php }?>
																								
											 </td>
										</tr>
										
										<tr>
											<td colspan="4">
												<hr>
												<div align="center">
												<strong>รวมต้องการจ่าย: <font color="green" size="+6"> {{(0<?php echo $vue_34_parse;?><?php echo $vue_33_parse;?><?php echo $vue_35_parse;?>).toLocaleString()}} </font> บาท</strong>
													
													<input type="hidden" name="invoice_amount" v-model="moomin" value="{{moomin}}" />
													
												</div>
											</td>
										</tr>

										
										
										
									
                                        
                                        
                                    </table>
                                    
                                    
                                    </span>
                                
                                
								<script>
									
									var vm = new Vue({
									  // options
										el: '#calculated_34_table'
										, data: {
											
											
											mooin: 0
											<?php echo $vue_34;?>
											<?php echo $vue_33;?>
											<?php echo $vue_35;?>
											
											// , pay_33_amount_1 : $('#pay_33_amount_1').val()
											
										}
										
										,computed: {
											// a computed getter
											moomin: function () {
											  // `this` points to the vm instance
											  return 0<?php echo $vue_34_parse_this;?><?php echo $vue_33_parse_this;?><?php echo $vue_35_parse_this;?>;
											}
										  }
										
									})
									
									
								</script>
                                
                                
                               
                                
                                </td>
                                
                             </tr>
                              

                             
                              
                             <!-- </form> -->
                              
                              
                              
                             
                              
                              
                             
                              
								  <tr>
									<td colspan="4">
										<hr>
									</td>
								</tr>

                              
                              <tr>
                                <td valign="top">หมายเหตุ</td>
                                <td colspan="3"><label>
                                <textarea name="invoice_remarks" cols="50" rows="4" id="invoice_remarks"></textarea>
                                </label></td>
                              </tr>
                              
                              
                              <tr>
                                        <td valign="top">เจ้าหน้าที่</td>
                                        <td colspan="3"><?php
                                            if($_GET['user_name']) {
                                                $encoded_username = $_GET['user_name'];

                                                // Then base64 decode
                                                $username = base64_decode($encoded_username);

                                                // Display
                                                $invoice_userid_text = $username . " (".($_GET['user_id']*1).")";

                                                echo $invoice_userid_text;

                                            }else {

                                                echo $sess_userfullname;

                                            }
										
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
									
									 
									 <input name="invoice_principal_amount" type="hidden" value="<?php echo $pay_for_start?>" />
									 <input name="invoice_interest_amount" type="hidden" value="<?php echo $pay_for_interest?>" />
									
									 <input name="invoice_userid" type="hidden" value="<?php echo $sess_userid?>" />
									
								   
									 <input name="invoice_payment_date" type="hidden" value="<?php echo $this_date_time?>" />
									
									 <?php //yoes 20211129 
										if($this_lawful_year >= 2018 && $this_lawful_year < 2050){
									?>
									  <input name="invoice_owned_principal" type="hidden" value="<?php echo $owned_money_total*1?>" />
									  <input name="invoice_owned_interest" type="hidden" value="<?php echo ($interest_money+$total_pending_interest)*1?>" />
									<?php }else{?>
										<input name="invoice_owned_principal" type="hidden" value="<?php echo $pay_34_row[p_pending_amount]*1?>" />
									  <input name="invoice_owned_interest" type="hidden" value="<?php echo $pay_34_row[p_pending_interests]*1?>" />
									<?php }?>
									
									<input name="invoice_employees" type="hidden" value="<?php echo $lawfulness_employees;?>" />
									<input name="invoice_33" type="hidden" value="<?php echo $hire_numofemp;?>" />
									<input name="invoice_35" type="hidden" value="<?php echo $the_35;?>" />
									
									<input name="m33_total_missing" type="hidden" value="<?php echo $m33_total_missing;?>" />
									<input name="m33_total_interests" type="hidden" value="<?php echo $m33_total_interests;?>" />
									<input name="m35_total_missing" type="hidden" value="<?php echo $m35_total_missing;?>" />
									<input name="m35_total_interests" type="hidden" value="<?php echo $m35_total_interests;?>" />

                                    <?php if($invoice_userid_text){?>
                                    <input name="invoice_userid_text" type="hidden" value="<?php echo $invoice_userid_text;?>" />
                                    <?php }?>

									<input name="real_invoice_submit" type="submit" value="เพิ่มข้อมูลการจ่ายเงิน และพิมพ์ใบชำระเงิน" onClick="return confirm('ต้องการเพิ่มข้อมูลใบชำระเงินนี้? กรณีที่เพิ่มข้อมูลใบชำระเงิน แล้วไม่ได้ใช้ชำระเงินจริง จะต้องลบข้อมูลใบชำระเงินที่ไม่ใช้งานออกจากระบบ ไม่เช่นนั้นระบบจะคำนวณยอดเงินที่ต้องจ่าย โดยลบยอดเงินต้นจากใบชำระเงินที่ยังไม่ได้ชำระ');  window.location.reload();" />
									
									
									<?php if($sess_accesslevel == 1){ ?>
										<br>
										<?php //20230425 bank add id = "demo_checkbox" MA hire 20-28 ?>
										<font color=blue>
											<input type="checkbox" name="is_demo"  id="demo_checkbox"
												
												<?php if($_GET["temp"]){?>checked<?php }?>
											
											> ออกเป็นใบชำระเงินตัวอย่าง (ใบชำระเงินตัวอย่างจะไม่สามารนำไปชำระเงินที่ระบบใบเสร็จออนไลน์ได้)
										</font>
									<?php }?>
									
								
								<?php }elseif($pay_for_start <= 0){?>
                               
								** ไม่สามารถชำระเงินได้ เพราะจำนวนเงินที่ต้องการชำระเป็นการชำระดอกเบี้ย โดยไม่ชำระเงินต้น **
							   
								<?php } ?>
                               
                              </div> 
                    		</td>
                            
                          </tr>
						
						<tr>
							<td colspan="4">
								<hr>
							</td>
						</tr>
                            
                         </table>
                    
                  
                        
                        
				</form>                        
                 
					
					<script>
					
						function delayedReload(){
							
							//alert('eh');
							setTimeout(function () {
								window.location.reload();
							}, 500);
							
						}
					
					</script>
					
					
					
					
					
                    
                    
                        
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
                  
                  	
                  	<strong>รายการใบชำระเงินที่มีการพิมพ์ออกไปแล้ว <?php echo $invoice_count;?> รายการ</strong>
					
					
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



<!-- 20230425 bank Add function get/check date to checked box then call axios to temp table MA hire 20-28 -->
<script> 

  function showSelectedValue(event) {
    event.preventDefault(); // prevent form from submitting
    const day = document.getElementById("add_invoice_date_day");
    const month = document.getElementById("add_invoice_date_month");
    const year = document.getElementById("add_invoice_date_year");
    const dayValue = day.value;
    const monthValue = month.value;
    const yearValue = year.value;
	const temp = "temp";
	const payment = "payment";
    const selectedDate = new Date(yearValue, monthValue - 1, dayValue); // Month value is 0-based, so we subtract 1
    const currentDate = new Date();
	const formattedDate = `${dayValue.padStart(2, '0')}-${monthValue.padStart(2, '0')}-${yearValue}`;

    // Set the time portion of the dates to midnight
    selectedDate.setHours(0, 0, 0, 0);
    currentDate.setHours(0, 0, 0, 0);

    if (selectedDate.getTime() === currentDate.getTime()) {
     // document.getElementById("demo_checkbox").checked = false;
	  
       axios.post('', {
				  day: dayValue,
				  month: monthValue,
				  year: yearValue,
				  temp: temp
				})
				.then(response => {
				  // Reload the page with query string
				  localStorage.setItem("demo_checkbox_state", "unchecked");
				  window.location.href = 'add_invoice_pro.php?'+ 'search_id=' + <?php echo $the_cid; ?> + '&mode=' + payment + '&for_year=' + <?php echo $the_year;?>;
				})
				.catch(error => {
				  console.error(error);
				});
				
    } else {
		
     // document.getElementById("demo_checkbox").checked = true;
      //console.log('Full URL:', 'add_invoice_pro.php?day=' + dayValue + '&month=' + monthValue + '&year=' + yearValue + '&temp=' + temp);

     axios.post('', {
				  day: dayValue,
				  month: monthValue,
				  year: yearValue,
				  temp: temp
				})
				.then(response => {
				  // Reload the page with query string
				  localStorage.setItem("demo_checkbox_state", "checked");
				  window.location.href = 'add_invoice_pro.php?'+ 'search_id=' + <?php echo $the_cid; ?> + '&mode=' + payment + '&for_year=' + <?php echo $the_year;?> + '&day=' + dayValue + '&month=' + monthValue + '&year=' + yearValue + '&temp=' + temp;
				})
				.catch(error => {
				  console.error(error);
				});
        
      getDataTemp(formattedDate); // pass formattedDate as a parameter
    } 
  }
	
	function getDataTemp(formattedDate) {
	  const job = "<?php echo "job"; ?>";
	  const source = axios.CancelToken.source();
	 
		axios.get('https://job.dep.go.th/ajax_get_lawfulness_info_from_lid.php', {
		  params: {
			the_lid: <?php echo $the_lid; ?>,
			the_mode: job,
			current_date: formattedDate // Use the formattedDate parameter directly
		  },
		  cancelToken: source.token
		})
		.then(response => {
		  console.log(response.data);
		  //alert('เลือกวันที่คำนวณเงินเป็นวันที่ : ' + thaiDate);
		  //location.reload();
		})
		.catch(error => {
		  if (axios.isCancel(error)) {
			console.log('Request canceled', error.message);
		  } else {
			console.error(error);
		  }
		});

		// Check if the request is still running after 30 seconds
		setTimeout(() => {
		  if (source.token.reason == null) {
			source.cancel('Request timed out');
		  }
		}, 30000);
	}
	
	window.onload = function() {
	  //var demo_checkbox_state = localStorage.getItem("demo_checkbox_state");
	  if (demo_checkbox_state === "checked") {
		//document.getElementById("demo_checkbox").checked = true;
	  }else{
		//document.getElementById("demo_checkbox").checked = false;  
	  }
	};
  
</script>

</body>
</html>