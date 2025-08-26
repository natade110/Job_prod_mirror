<?php

	include "db_connect.php";
	include "session_handler.php";
		
	$skip_html_head = 1;
	
	include "header_html.php";
	
	$the_id = $_GET[the_id]?$_GET[the_id]:$_POST[the_id];
	$the_id = $the_id*1;
	
?>
<body>
<table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                    	  <td><div align="center"><span class="column_header">ปี</span> </div></td>
           	           	   
                            
                            <td colspan=2>
                            	<div align="center"><span class="column_header">จำนวน<?php echo $the_employees_word;?> (ราย)</span> </div>                            </td>
                            
                             <td>
                            	<div align="center"><span class="column_header">อัตราส่วน</span> </div>                            </td>
                            
                            <td ><div align="center"><span class="column_header">รับคนพิการเข้าทำงาน<br />
                              ตามมาตรา 33 (ราย)</span></div></td>
                            
                           
                            
							
							<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
							
							<?php }else{?>
							
								<td ><div align="center"><span class="column_header">จ่ายเงินแทนการรับคนพิการ<br />
								ตามมาตรา 34</span> </div></td>
								
							<?php }?>
                           
                            <td ><div align="center"><span class="column_header">การให้สัมปทาน<br />
                            ตามมาตรา 35 (ราย)</span></div></td>
                            <td ><div align="center"><span class="column_header">สถานะ</span> </div></td>
                    	</tr>
                        
                        <?php
						
						
						
							//echo ".... $is_merged";
							//generate letter history
							$get_history_sql = "
										select
											 *
											 , a.Employees as Employees
										from 
											lawfulness a
												join
													company b
													on
													a.cid = b.cid
										where
											a.CID = '$the_id'
											and
											a.Year <= '".(date("Y")+1)."'
											and
											a.Year >= '$dll_year_start'	
											or a.cid in (
											
												select
													meta_cid
												from
													company_meta
												where
													meta_value = '$the_id'
												and
													meta_for = 'dbd_merged_to'
											
											)
										
										order by 
											a.Year  desc
											, a.cid  asc
										
										";
							
							//echo $get_letter_sql;
							
							$history_result = mysql_query($get_history_sql);
							
							$lawful_row_array = array();
							$year_span_array = array();
							
							while ($lawful_row = mysql_fetch_array($history_result)) {							
								
								$lawful_row_array[] = $lawful_row;								
								$year_span_array[$lawful_row[Year]] ++;
															
							}
							
							//print_r($year_span_array);							
							
							for($i = 0; $i < count($lawful_row_array); $i++){
								
								$lawful_row = $lawful_row_array[$i];
							
								$this_cid = $lawful_row[CID]; //echo $this_cid;
							
								$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_".$lawful_row["Year"]."'"),100);
																
								$employees_to_use = $lawful_row["Employees"];
								
								//echo $employees_to_use; 
								
								$final_employee = getEmployeeRatio( $employees_to_use,$ratio_to_use);
								
								
									
								$the_sql = "select sum(receipt.Amount) 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_cid'
														and
														is_payback != 1
														";
														
									$paid_money_history = getFirstItem("$the_sql");
									
									$the_sql = "select sum(receipt.Amount) 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_cid'
														and
														is_payback = 1
														";
														
									$back_money_history = getFirstItem("$the_sql");
									
									$paid_money_history = $paid_money_history - $back_money_history;
																		
									//-----------									
							
							?>
                            
                                <tr >
								 
									<?php if($year_span_array[$lawful_row[Year]] == 0){?>
										
									<?php }else{ ?>
										
										
										
											  <?php if($year_span_array[$lawful_row[Year]] > 1){?>
													<td valign="top" rowspan=<?php echo $year_span_array[$lawful_row[Year]]; ?> >
											  <?php }else{?>
													<td valign="top">
											  <?php }?>
											  
											 
													<div align="center">
													
														<?php if($year_span_array[$lawful_row[Year]] > 1){ 
															
															//rendered this td row
															$year_span_array[$lawful_row[Year]] =0;
															
														?>
															<?php 
															
																
																if($is_merged){
																	echo formatYear($lawful_row["Year"]-1000);
																}else{
																	echo formatYear($lawful_row["Year"]);
																}
																
																?>
														<?php }else{?>
															<a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>">
												   
																<?php 
																
																	
																	if($is_merged){
																		echo formatYear($lawful_row["Year"]-1000);
																	}else{
																		echo formatYear($lawful_row["Year"]);
																	}
																	
																	?>
													   
															</a>
														<?php }?>
														
													
													
													<?php 
														
															//yoes -> show submmited info
															$submitted_row = getFirstRow("
																select 
																	lawful_submitted
																	, lawful_submitted_on
																	, lawful_approved_on
																	, lawful_approved_by
																from
																	lawfulness_company
																where
																	CID = '" . $this_cid . "'
																	and
																	Year = '".$lawful_row["Year"]."'
																");
																
															//echo $this_cid;
															//print_r($submitted_row);
															
															if($submitted_row[lawful_submitted] == 1 || $submitted_row[lawful_submitted] == 2){
																
																
																echo "<br><font color='#003300'>มีการยื่นแบบฟอร์มออนไลน์มาเมื่อวันที่ ".formatDateThai($submitted_row[lawful_submitted_on],1, 1) ."</font>";
																
															}
															
															if($submitted_row[lawful_submitted] == 2){
																
																$approved_by_name = getFirstItem("select user_name from users where user_id = ".$submitted_row[lawful_approved_by]);
																
																echo "<br><font color='#003300'>เจ้าหน้าที่ทำการบันทึกข้อมูลเข้าระบบแล้วเมื่อวันที่ ".formatDateThai($submitted_row[lawful_approved_on],1, 1) ." โดย $approved_by_name</font>";
																
															}
													   
													   ?>
													
													   </div>                                 

												   </td>
								   
										<?php } ///end else row span = 0 for first td?>
                                  
                                   
								   
									<?php if($lawful_row[CID] == $the_id){ 
										
											
											
										?>
											 
											 <td valign="top" colspan=1>
												<a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>">
														<?php echo formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);?>
													</a>
												</td>
											 
											 
											<td valign="top" colspan=1>
												 <div align="right">
												 
												
												 <?php echo number_format($employees_to_use,0);?>
												 
												 </div>                                   
											 
											 </td>  
											   
									<?php }else{ ?>
											
											<td valign="top">
												 <div align="left">
												 
													<a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>">
														<?php echo formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);?>
													</a>
												 
												 </div>                                   
											 
											 </td>  
											
											<td valign="top">
												 <div align="right">
												 
												 <?php echo number_format($employees_to_use,0);?>
												 
												 </div>                                   
											 
											 </td> 
									
									
									<?php }?>
									
                                     
                                     <td valign="top">
                                    
                                    <div align="center">
                                        <?php echo $ratio_to_use;?> ต่อ 1 = <?php echo $final_employee;?> ราย                                    </div>                                    </td>
                                    
                                    
                                        
                                    <td valign="top">
                                    
                                    
                                    <div align="right">
                                        
                                        <?php 
                                        
                                        //yoes 20150803 -> company shouldn't be able to see this
                                        //if($sess_accesslevel != 4 || ($sess_accesslevel == 4 && $lawful_row["Year"] >= 2015)){
                                        if($sess_accesslevel != 4){ ?>
											<a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&le=le&year=<?php echo $lawful_row["Year"];?>">
                                        <?php }?>
                                        
												<?php echo $lawful_row["Hire_NumofEmp"]?>
                                        
                                        <?php //yoes 20150803 -> company shouldn't be able to see this
                                        if($sess_accesslevel != 4){ ?>
                                        </a>
                                        <?php }?>
                                    
                                    	
                                    
                                    </div>                                    
                                    
                                    
                                    
                                    </td>
                                    
                                    
                                    
                                    <td valign="top" 
									
									<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
									style="display:none;"
									<?php }?>
									
									>
                                    	<div align="right">
                                        
                                        <?php
										
										$the_sql = "select * 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_cid'
														
														order by
														
														PaymentDate asc
														
														";
														
										$paid_money_history_result = mysql_query($the_sql);
										
										
										if(mysql_num_rows($paid_money_history_result)){
										
										?>
                                        
                                        
                                        <table border="1" style="border-collapse:collapse;" cellpadding="3" cellspacing="0">
                                        	<tr style="background-color:#CCCCCC;">
                                            	<td>
                                                 <div align="center">เล่มที่                                                </div></td>
                                                <td>
                                                  <div align="center">เลขที่                                                </div></td>
                                                <td>
                                                  <div align="center">จำนวนเงิน (บาท)                                                </div></td>
                                            </tr>
                                            
                                        
                                        
                                        <?php
										
										
										}
										
										while ($pmh_row = mysql_fetch_array($paid_money_history_result)) {
										
										?>
										
                                        <tr>
                                        	<td>
                                              <div align="left">
											  
                                              <?php if($sess_accesslevel != 4){ ?>
											  <a href="view_payment.php?id=<?php echo $pmh_row["RID"];?>">
											  <?php }?>
                                              
											  <?php echo $pmh_row["BookReceiptNo"];?>
                                              
                                              <?php if($sess_accesslevel != 4){ ?>
                                              </a>
                                              <?php }?>
                                              
                                              </div></td>
                                             <td>
                                               <div align="left"><?php echo $pmh_row["ReceiptNo"];?>                                            </div></td>
                                            	<td>
                                                  <div align="right">
                                                  
                                                  
                                                  
												  <?php if($pmh_row["is_payback"]){echo "<font >-";}?>
                                                  
												  <?php echo formatNumber($pmh_row["Amount"]);?>                                            
                                                  <?php if($pmh_row["is_payback"]){echo "</font>";}?>
                                                  
                                                  
                                                  </div></td>
                                        </tr>
                                        
                                        
                                        <?php	
										
										}
										
										?>
                                        
                                        
                                        
                                        
                                        <?php if(mysql_num_rows($paid_money_history_result)){?>
                                        
                                         <tr>
                                        	<td>
                                           </td>
                                             <td>
                                               รวม</td>
                                            	<td>
                                                  <div align="right">
                                                  
                                                  <?php if($sess_accesslevel != 4){ ?>
                                                  <a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>#the_payment_details">
                                                  <?php }?>
												  
												  <?php echo formatMoney($paid_money_history) ;?>                                       
                                                   
                                                   <?php if($sess_accesslevel != 4 ){ ?>
                                                   </a>
                                                   <?php }?>
                                                    
                                                    
                                                    </div></td>
                                        </tr>
                                        
                                        
                                        	</table>
                                        
                                        <?php }else{ //end if if(mysql_num_rows($paid_money_history_result)){?>
                                        
                                        
                                        	<?php if($sess_accesslevel != 4 ){ ?>
    	                                    	<a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>#the_payment_details">
                                            <?php }?>
                                                 0.00                                    
                                                 
                                            <?php if($sess_accesslevel != 4 ){ ?>
	                                            </a>
                                            <?php }?>
                                        
                                        <?php }?>
                                        
                                        
                                        
                                      </div>                                    </td>
                                   
                                    <td valign="top">
                                    
                                    	<?php if($sess_accesslevel != 4 ){ ?>
                                   	 	<a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>&curate=curate">
                                        <?php }?>
                                        
                                        <div align="right"><?php 
										
											
											//yoes 20181108 -> change this to new standard function
											//$this_curator_usee = getFirstItem($this_curator_usee_sql);
											$this_year_lid = getFirstItem("select lid from lawfulness where Year = '".$lawful_row["Year"]."' and CID = '$this_cid'");
											$this_curator_usee = getNumCuratorFromLid($this_year_lid);
											
											echo $this_curator_usee;
										
										
										?></div>       
                                        
                                        <?php if($sess_accesslevel != 4 ){ ?>
                                         </a>                            
                                         <?php }?>
                                         
                                         </td>
                                    
                                    <td valign="top">
                                    
                                    	<div align="center"><?php 
										
											//echo getLawfulImage($lawful_row["LawfulStatus"]);
											
											echo getLawfulImageFromLID($lawful_row["LID"]);
											
											?></div>                                    </td>
                                </tr>
                            
                            
                            <?php 
									
									
									//yoes 20160613
									$lawful_history_count++;
							
								} //end while -> history table?>
                      </table>

</body>
</html>