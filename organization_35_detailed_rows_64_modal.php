<?php

	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";
	
	

	//yoes 20211028 - add this
	$post_row = $_POST;

	$this_id = $_POST[this_id];
	$this_lawful_year = $_POST[this_lawful_year];
	$this_lid = $_POST[this_lid];
	
	//yoes 20240215
	if(!$this_lid){exit();}
	

	$total_records = $_POST[total_records];
	$sub_count_text = $_POST[sub_count_text];

	//yoes 20160707 - moved this here from widget_check_35-35_duped.php
	//8
	//include_once("organization_35_detailed_rows_js_64.php"); 
	
	//yoes 20211028 - legacy variables
	$this_curator_idcard = $post_row["curator_idcard"];
	$this_curator_id = $post_row["curator_id"];
	$this_le_cid = $post_row["le_cid"];
	$this_le_year = $post_row["le_year"];

	$curator_table_name = $post_row["curator_table_name"];
	
	
	
	if($this_curator_id == $last_child){
		
		//$sub_count++;
		//$sub_count_text = ".".$sub_count;
		
	}else{
		
		//$sub_count = 0;
		//$sub_count_text = "";
		
	}
	
	//yoes 20181108 -> see if have child
	$this_curator_have_child = getChildOfCurator($this_curator_id);
	
	
	$last_child = $this_curator_have_child;
	
/*
<tr class="bb_top"  bgcolor='#ABB2B9'>
		<td style="border-top:1px solid #999999; "><div align="center"><strong>
			<?php echo "this_curator_have_child . $this_curator_have_child";?>
			
			<?php echo "last_child . $last_child";?>
		</strong></div></td>
	</tr>

*/

?>
	

<?php 
	if($this_curator_have_child){
?>

	<tr class="bb_top" id="curator_<?php echo $this_curator_id;?>_alt" bgcolor='#ABB2B9'>
		<td style="border-top:1px solid #999999; "><div align="center"><strong>
			<?php echo $total_records; echo $sub_count_text;?>
		</strong></div></td>
	  
	  <td style="border-top:1px solid #999999;">
	  
		<a href='#' onclick='doToggle35Row(<?php echo $post_row["curator_id"];?>); return false;' style="font-weight: normal;">
			<?php echo doCleanOutput($post_row["curator_name"]);?>
		</a>
		
		<?php if(getFirstItem("select meta_value from curator_meta where meta_curator_id = '".$post_row["curator_id"]."' and meta_for = 'is_extra_35'")){
			 ?>
			<font color="purple">(เป็นการทำ ม35 เกินอัตราส่วน)</font>
		 <?php }?>
	  
	  </td>
	  <td style="border-top:1px solid #999999;"><?php echo formatGender($post_row["curator_gender"]);?></td>
	  <td style="border-top:1px solid #999999;">
	  
		<?php echo doCleanOutput($post_row["curator_age"]);?>
		
		<?php
										
			if($post_row["curator_dob"] && $post_row["curator_dob"] != "0000-00-00"){
		?>
			<br>
			<font color=green>(วันเกิด <?php
			
				echo formatDateThai($post_row["curator_dob"],0);
				
			?>)</font>
		<?php
			}
		?>
	  
	  
	  </td>
	  
	  <td style="border-top:1px solid #999999;">
		<?php echo doCleanOutput($post_row["curator_idcard"]);?>
		
		
		<?php 
							  
			//yoes 20160707 -- only check this if "is_disable
			
			if($post_row["curator_is_disable"]){
			
				//yoes 20160503 --> turned this into widget
				include "widget_check_35-33_duped.php";
				
				
				//yoes 20160503 --> turned this into widget
				include "widget_check_35-35_duped.php";
			
			}
		
		?>
	
		</td>
		<td style="border-top:1px solid #999999;">
		  <?php if($post_row["curator_is_disable"] == 1){
			
				echo "<font color='green'>คนพิการ : " . $post_row["curator_disable_desc"]. "</font>";
				
			}else{
			
				echo "<font color='blue'>ผู้ดูแลคนพิกา</font>ร";
				
			}?>
		  
		  </td>
		  
		  <td style="border-top:1px solid #999999;"><?php echo $post_row["curator_contract_number"];?></td>
			<td style="border-top:1px solid #999999;"><?php 
			
			
				echo formatDateThai($post_row["curator_start_date"]);
				
				
				if($post_row["curator_end_date"]){
					echo "-". formatDateThai($post_row["curator_end_date"]);
				}
				
				?>
				
				<br>
				<b><span id="curator_<?php echo $post_row["curator_id"];?>_alt_amount"></span></b>
			
			</td>
		
			<td style="border-top:1px solid #999999;"><?php 
                                
                                
			//echo $post_row["curator_start_date"];
			//echo $post_row["curator_end_date"];
			 //echo number_format(dateDiffTs(strtotime($post_row["curator_start_date"]), strtotime($post_row["curator_end_date"])),0);
			 
			 //yoes 20231009 -- add extra days here
			 echo number_format(dateDiffTs(strtotime($post_row["curator_start_date"]), strtotime($post_row["curator_end_date"]), 1),0);
                                
			
			?> วัน</td>
			
		   
		  
		   <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_event"]);?></td>
		   
		   <td style="border-top:1px solid #999999;"><div align="right"><?php echo formatNumber($post_row["curator_value"]);?></div></td>
		   
		   
		    <td style="border-top:1px solid #999999;" colspan=3><?php 
				                
                    echo doCleanOutput($post_row["curator_event_desc"]);
										
			?></td>
		
	</tr>

<?php }?>





<tr class="bb_top" id="curator_<?php echo $this_curator_id;?>_main" 

	<?php 
		
		if($this_curator_have_child){
			echo "style='display: none;'"; 
			echo "bgcolor='#ABB2B9'";
		}else{
			
			echo $the_bg;
		}
		
		?>
	
	
>
                              <td valign="top" style="border-top:1px solid #999999; "><div align="center"><strong><?php echo $total_records;  echo $sub_count_text;?></strong></div></td>
                              <td valign="top" style="border-top:1px solid #999999;">
							  
							  
							  <?php if($this_curator_have_child){?>
							  <a href='#' onclick='doToggle35Row(<?php echo $post_row["curator_id"];?>); return false;' style="font-weight: normal;">
							  <?php }?>
								
								<?php echo doCleanOutput($post_row["curator_name"]);?>
								
							  <?php if($this_curator_have_child){ ?>
							  </a>
							  <?php }?>
							  
							  
							  <?php if(getFirstItem("select meta_value from curator_meta where meta_curator_id = '".$post_row["curator_id"]."' and meta_for = 'is_extra_35'")){
								 ?>
								<font color="purple">(เป็นการทำ ม35 เกินอัตราส่วน)</font>
							 <?php }?>
							  
							  
							  </td>
                              <td valign="top"  style="border-top:1px solid #999999;"><?php echo formatGender($post_row["curator_gender"]);?></td>
                              <td valign="top" style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_age"]);?>
							  
							  
							  <?php
																
									if($post_row["curator_dob"] && $post_row["curator_dob"] != "0000-00-00"){
								?>
									<br>
									<font color=green>(วันเกิด <?php
									
										echo formatDateThai($post_row["curator_dob"],0);
										
									?>)</font>
								<?php
									}
								?>
							 
							  
							  </td>
                              
                              <td valign="top" style="border-top:1px solid #999999;">
                              
                              
                              <?php echo doCleanOutput($post_row["curator_idcard"]);?>
                              
                              
						 	  <?php 
							  
							  	//yoes 20160707 -- only check this if "is_disable
							  	
								if($post_row["curator_is_disable"]){
								
									//yoes 20160503 --> turned this into widget
									include "widget_check_35-33_duped.php";
									
									
									//yoes 20160503 --> turned this into widget
									include "widget_check_35-35_duped.php";
								
								}
								
								?>
                                                      
                              
                              </td>
                              
                              
                              
                              <td valign="top" style="border-top:1px solid #999999;">
                              <?php if($post_row["curator_is_disable"] == 1){
                                
                                    echo "<font color='green'>คนพิการ : " . $post_row["curator_disable_desc"]. "</font>";
                                    
                                }else{
                                
                                    echo "<font color='blue'>ผู้ดูแลคนพิกา</font>ร";
                                    
                                }?>
                              
                              </td>
                              
                              
                              <td valign="top" style="border-top:1px solid #999999;"><?php echo $post_row["curator_contract_number"];?></td>
                                <td valign="top" style="border-top:1px solid #999999;"><?php 
								
								
									echo formatDateThai($post_row["curator_start_date"]);
									
									
									if($post_row["curator_end_date"]){
										echo "-". formatDateThai($post_row["curator_end_date"]);
									}
									
									?>
									
								

								<?php
								
									//yoes 20181107 - show payment details here
									//print_r(get35DeductionByCuratorIdArray($this_curator_id));
									//include "organization_35_detailed_rows_2018_law_widget.php";
									
									
									//yoes 20200626
									//add beta status
									$is_beta = getLidBetaStatus($this_lid);
									
									if($is_beta){
										
										$principal_sql = "
												
													select
														*
													from
														lawful_35_principals
													where
														p_from = '".$post_row["curator_id"]."'
														or
														(
															p_from = 0
															and
															p_to = '".$post_row["curator_id"]."'
														)
												
												";
												
												
										//echo $principal_sql;
									
										$principal_result = mysql_query($principal_sql);
										
										$interests_row = array();
										
										//$principal_row = getFirstRow($principal_sql);
										while($principal_row = mysql_fetch_array($principal_result)){
											
											if($principal_row && $this_lawful_year >= 2018){
															
												//yoes 20200724 for https://app.asana.com/0/794303922168293/1185797049999353
												$day_display_offset = 0;
												if($principal_row[p_from] && $principal_row[p_to]){
													$day_display_offset = -1;															
												}


                                                if(

                                                    (
                                                        trim($post_row["curator_event"]) == "จัดสถานที่จำหน่ายสินค้าหรือบริการ"
                                                        || trim($post_row["curator_event"]) == "การให้ความช่วยเหลืออื่นใด"
                                                        || trim($post_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"
                                                    )

                                                    && $this_lawful_year >= 2025 && $this_lawful_year <= 2500

                                                ){

                                                    echo "<br><font color=orangered>";
                                                    echo $post_row["curator_event"] . " มูลค่า " . number_format($post_row["curator_value"],2). " บาท " ;
                                                    echo " ต้องจ่ายแทน ". number_format($principal_row[p_amount], 2) . " บาท ";
                                                    echo "</font>";

                                                }else {

                                                    //echo "<br>---- beta ----";
                                                    echo "<br><font color=orangered>ต้องจ่ายเงินแทน "
                                                        . number_format(dateDiffTs(strtotime($principal_row[p_date_from]), strtotime($principal_row[p_date_to]), $day_display_offset), 0) . " วัน "
                                                        . number_format($principal_row[p_amount], 2) . " บาท ";

                                                    echo "</font>";

                                                }
												
												//yoes 20200624 -- total for each chain
												//$m33row_total_principal += $principal_row[p_amount];
												
												
												//yoes 20200618 try get interests function here...
												$interests_row = generateInterestsFromPrincipals($this_lid, $principal_row[p_from],  $principal_row[p_to], "m35");
												
												//print_r($interests_row);
												
												$interest_details = $interests_row[interest_details];
												
												$m33row_total_paid = 0;
												
												for($iii = 0; $iii < count($interest_details) ; $iii++){
													
													echo "<br>1. เงินต้นต้องชำระ ".number_format($interest_details[$iii][pre_pending_principal], 2)." ดอกเบี้ย " . number_format($interest_details[$iii][this_interest], 2);
													
													if($interest_details[$iii][last_loop_left_over_interest]){
														echo " ดอกเบี้ย " . number_format($interest_details[$iii][this_interest]-$interest_details[$iii][last_loop_left_over_interest], 2)
																	."+<font color=purple>" . number_format($interest_details[$iii][last_loop_left_over_interest],2) . "</font>";
													}else{
														echo " ดอกเบี้ย " . number_format($interest_details[$iii][this_interest], 2);
													}
													
													if($interest_details[$iii][interest_days] > 0){
														echo "<br>ดอกเบี้ยคิดจากวันที่ " . formatDateThai($interest_details[$iii][interest_start_date], 0) . " ถึง " . formatDateThai($interest_details[$iii][interest_end_date], 0) . " (".$interest_details[$iii][interest_days]." วัน)";
														if($interest_details[$iii][pre_principal_to_calculate_interests] != $interest_details[$iii][pre_pending_principal]){
																
															echo "<font color=purple>";
															echo "<br>ดอกเบี้ยคิดจากเงินต้น " . number_format($interest_details[$iii][pre_principal_to_calculate_interests], 2) . " บาท";
															echo "<br>** เงินต้นคิดจนถึงวันที่ 31 ธค เนื่องจากเป็นการจ่ายเงินก่อนที่มีคนใหม่มาแทน";
															echo "</font>";
															
														}
													}
													
													$the_this_receipt_sum_to_pay = 0;
													if($interest_details[$iii][pre_pending_principal]+$interest_details[$iii][this_interest] >= 0){
														echo "<br><b>รวมต้องชำระ " . number_format($interest_details[$iii][pre_pending_principal]+$interest_details[$iii][this_interest], 2) . " บาท</b>";
													}else{
														echo "<br><b>จ่ายเกิน " . (number_format($interest_details[$iii][pre_pending_principal]+$interest_details[$iii][this_interest], 2)) . " บาท</b>";
													}
																										
													//print_r($interest_details[$iii]);
													if($interest_details[$iii][meta_value]){
														echo "<br><font color=blue>";
														echo "มีการจ่ายเงินวันที่ " . formatDateThai($interest_details[$iii][ReceiptDate], 0);
														echo " เล่มที่ ".$interest_details[$iii][BookReceiptNo]." เลขที่ ".$interest_details[$iii][ReceiptNo];
														
														echo " จำนวนเงิน " . number_format($interest_details[$iii][meta_value], 2) . " บาท";
														
														if($interest_details[$iii][left_over_interest]){
															echo "<br><font color=purple>เหลือดอกเบี้ย ".number_format($interest_details[$iii][left_over_interest],2)." บาท</font>";
														}
														
														echo "</font>";
													}
													
													$m33row_total_paid += $interest_details[$iii][meta_value];
													
													/*echo "<br>เงินต้นคงเหลือ ณ วันนี้ " . number_format($interest_details[$iii][pre_pending_principal], 2) . " บาท";
													echo "<br>ดอกเบี้ย ณ วันนี้ " . number_format($interest_details[$iii][this_interest], 2) . " บาท";
													echo "<br>ดอกเบี้ยคิดจากวันที่ " . formatDateThai($interest_details[$iii][interest_start_date], 0) . " ถึง " . formatDateThai($interest_details[$iii][interest_end_date], 0) . " (".$interest_details[$iii][interest_days]." วัน)";
													echo "<br>ดอกเบี้ยคิดจากเงินต้น " . number_format($interest_details[$iii][pre_principal_to_calculate_interests], 2) . " บาท";
													*/
													//$m33row_total_interests += $interest_details[$iii][this_interest];
													
													
												}
												
												echo "
												<br>2. เงินต้นที่เหลือ ".number_format($interests_row[p_principal_after],2)." บาท ดอกเบี้ย ".number_format($interests_row[pending_interests],2)."";
												$the_this_receipt_sum_to_pay = 0;
												if($interests_row[p_principal_after]+$interests_row[pending_interests] >= 0){
													echo "<br><b>รวมต้องชำระ " . number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2) . " บาท</b>";
												}else{
													echo "<br><b>จ่ายเกิน " . (number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2)) . " บาท</b>";
												}
												
											}
											
											//yoes 20200624
											//summary of the row
											/*echo "<br>รวมต้องจ่ายเงินต้น+ดอกเบี้ย " . number_format($principal_row[p_amount],2) . "+".number_format($principal_row[p_interests],2)."=".number_format($principal_row[p_amount]+$principal_row[p_interests],2)."บาท";
											echo "<br>มีการจ่ายเงินแล้ว " . number_format($m33row_total_paid,2) . " บาท";
											echo "<br>คงเหลือ " . number_format(($principal_row[p_amount]+$principal_row[p_interests])-$m33row_total_paid,2) . " บาท";*/
											
											include "organization_35_detailed_rows_2020_law_widget.php";
											
										}
										
									}
									
								
								?>
								
								
								</td>
                                
                                <td valign="top" style="border-top:1px solid #999999;"><?php 
                                
                                
                                //echo $post_row["curator_start_date"];
                                //echo $post_row["curator_end_date"];
                                echo number_format(dateDiffTs(strtotime($post_row["curator_start_date"]), strtotime($post_row["curator_end_date"]), 1),0);
                                
                                ?> วัน</td>
                                
                               
                              
                               <td valign="top" style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_event"]);?></td>
                               
                               <td valign="top" style="border-top:1px solid #999999;"><div align="right"><?php echo formatNumber($post_row["curator_value"]);?></div></td>
                               
                                <td valign="top" style="border-top:1px solid #999999;"><?php 
										$required_doc_35_1 = 1;
										$required_doc_35_2 = 1;
										$required_doc_35_3 = 1;
                                
                                        echo doCleanOutput($post_row["curator_event_desc"]);
                                        
                                        //also see if there are any attached files
										//yoes 20160120 --> add "extra" suffix here
                                        $curator_file_path = mysql_query("select 
                                                                                * 
                                                                           from 
                                                                                 files 
                                                                            where 
                                                                                file_for = '".$post_row["curator_id"]."'
                                                                               
																				and
																					(
																					
																						file_type = 'curator_docfile$is_extra_table'																						
																						or
																						file_type = 'curator_docfile_2$is_extra_table'
																						or
																						file_type = 'curator_docfile_3$is_extra_table'
																					)
                                                                                ");
                                                                                
                                        while ($file_row = mysql_fetch_array($curator_file_path)) {
												$file_count_35++;
												
												if($file_count_35 > 1){echo "<br>";}
                                        
                                        ?>
                                            
											
                                            
                                            <?php 
												
													//echo substr($file_row["file_name"],0,4);
													if(substr($file_row["file_name"],0,4)=="ejob"){
												?>
													<a href="https://ejob.dep.go.th/ejob//hire_docfile/<?php echo substr($file_row["file_name"],5);?>" target="_blank">
                                              <?php }else{?>
                                              		<a href="hire_docfile/<?php echo $file_row["file_name"];?>" target="_blank">
                                              <?php }?>
                                            

												 <?php 
                                                        if($file_row["file_type"] == "curator_docfile$is_extra_table"){
                                                            echo "สำเนาหนังสือแจ้งขอใช้สิทธิ";
                                                            $required_doc_35_1--;
                                                        }elseif($file_row["file_type"] == "curator_docfile_2$is_extra_table"){
                                                            echo "สำเนาหนังสือแจ้งผลการดำเนินการ";																												
                                                            $required_doc_35_2--;
                                                            
                                                        }elseif($file_row["file_type"] == "curator_docfile_3$is_extra_table"){
                                                            echo "สำเนาสัญญาสัมปทาน";																												
                                                            $required_doc_35_3--;
                                                            
                                                        }else{
                                                            echo "ไฟล์แนบ";	
                                                        }
                                                        
                                                    ?>
                                            
                                            </a>
											
                                            
                                            <?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && !$is_read_only && !$case_closed){?>
                                            <a href="scrp_delete_curator_file.php?id=<?php echo $file_row["file_id"];?>&curator_id=<?php echo $curator_id;?>&return_id=<?php echo $this_id;?>" title="ลบไฟล์แนบ" onClick="return deleteCuratorFile(<?php echo $file_row["file_id"];?>,<?php echo $this_id;?>);"><img src="decors/cross_icon.gif" alt="" height="10"  border="0" /></a>
                                            <?php }?>
        
                                            <!--<a href="force_load_file.php?file_for=<?php echo $file_row["curator_id"];?>&file_type=curator_docfile" target="_blank">ไฟล์แนบ</a>-->
											
											
											
											
											
											
											
                                        <?php
                                        
                                        
                                        }
                                        
                                        
                                        ?>
										
										
										<?php 
										  if($required_doc_35_1 && $_SERVER[SERVER_ADDR] != "10.0.116.6"){
											
											$required_doc++;
											
											$file_count_35++;
											if($file_count_35 > 1){echo "<br>";}
											///echo "<font color='red'>กรุณาแนบไฟล์สำเนาหนังสือแจ้งขอใช้สิทธิ</font>";  
											
										  }
										  if($required_doc_35_2 && $_SERVER[SERVER_ADDR] != "10.0.116.6"){
											  
											$required_doc++;
											  
											$file_count_35++;
											if($file_count_35> 1){echo "<br>";}
											echo "<font color='red'>กรุณาแนบไฟล์สำเนาหนังสือแจ้งผลการดำเนินการ</font>";  
											
										  }
										  if($required_doc_35_3 && $_SERVER[SERVER_ADDR] != "10.0.116.6"){
											  
											$required_doc++;
											  
											$file_count_35++;
											if($file_count_35> 1){echo "<br>";}
											if($this_lawful_year >= 2022 && $this_lawful_year < 2100){
												echo "<font color='red'>กรุณาแนบไฟล์สำเนาบัตรคนพิการ</font>"; 
											}else{
												//echo "$this_lawful_year";
												echo "<font color='red'>กรุณาแนบไฟล์สำเนาสัญญาสัมปทาน</font>";  
											}
											
										  }
										  ?>
										
										
										
										
										</td>
                                
                                <?php 
								
								
								
								if(!$company_lawful_submitted_for_m35 && $sess_accesslevel != 5 && !$is_read_only && (!$case_closed || $is_extra_table) && !$this_curator_have_child){
								
								?>
                                
                                      <td>
                                        <div align="center">
                                            <a href="scrp_delete_curator_new.php?id=<?php echo doCleanOutput($post_row[curator_id]);?>&cid=<?php echo $this_cid;?>&year=<?php echo $this_lawful_year;?><?php if( $is_extra_table){echo '&extra=1';}?>" title="ลบข้อมูล" onClick="return deleteCurator(<?php echo doCleanOutput($post_row[curator_id]);?>,<?php echo $this_lawful_year;?>,<?php echo $is_extra_table?'true':'false'; ?>);"><img src="decors/cross_icon.gif" alt="" border="0" /></a>
                                        </div>
                                        
                                        </td>
                                
                                  <td>
                                      <div align="center">
                                      
                                     <!--
                                      <a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $this_lawful_year;?>&curator_id=<?php echo doCleanOutput($post_row["curator_id"]);?><?php if( $is_extra_table){echo "&extra=1";}?>">
                                        <img src="decors/create_user.gif" alt="" border="0" />
                                      </a>
									  <br>----<br>-->
									   <a href="#" data-toggle="modal" data-target=".bs-example-modal-lg-m35" onClick="getCuratorForm(<?php echo $post_row["curator_id"];?>);">
											<img src="decors/create_user.gif" alt="" border="0" />
										  </a>
                                      </div>	
                                  </td>
	
									
                                 
								<?php }else{?>
								
									<td colspan=2>
									</td>								
								
								<?php }?>
                                  
                                  
                                  
                             
                             
                            </tr>
                             
                             
                             <?php 
							 	
								/*if($sess_accesslevel == 1){						
									$endtime = microtime(true);
									$timediff = $endtime - $starttime; echo $timediff;
								}*/
							 ?>
                            
                            
                            <?php 
							
							
							//for parent -> get child
							
							
							if(!$post_row["curator_is_disable"]){
								$count_usee = getFirstItem("select count(*) from $curator_table_name where curator_parent = '".doCleanOutput($post_row["curator_id"])."'");
							}else{
								$count_usee = 0;
							}
							//$count_usee = 0;
							
							if($sess_accesslevel != 5 && !$is_read_only && !$case_closed && $count_usee > 1){
							?>
                            
                            <tr <?php echo $the_bg;?>>
                               <td colspan="14" style="border-top:1px solid #999999; color:#F00;  ">ข้อมูลผู้ใช้สิทธิ มีผู้ถูกใช้สิทธิมากกว่า 1 คน กรุณาเลือกผู้ถูกใช้สิทธิที่ต้องการจากรายชื่อด้านล่าง</td>
                             </tr>
                             
                             <?php }?>
                            
                            <?php
							
							//get sub-curator
							$sql = "select 
										* 
									from 
										$curator_table_name 
									where curator_parent = '".doCleanOutput($post_row["curator_id"])."'";
							//echo $sql;
							
							$sub_result = mysql_query($sql);
							$total_sub = 0;
							while ($sub_row = mysql_fetch_array($sub_result)) {	
							
							
								//
								/**/
								$this_curator_idcard = $sub_row["curator_idcard"];
								$this_curator_id = $sub_row["curator_id"];
								//$this_lawful_year = "";
								
								//echo "<br>".$this_curator_idcard;
								//echo "<br>".$this_curator_id;
								//echo "<br>".$this_lawful_year;
							
								
							?>
                            
                            
                            
							<tr id="curator_<?php echo $post_row["curator_id"];?>_main_2" 
							
							
								<?php 
								
									if($this_curator_have_child){
										echo "style='display: none;'"; 
										echo "bgcolor='#ABB2B9'";
									}else{
										
										echo $the_bg;
									}
									
								?>
								
							>
                               
                               <td valign="top">
                               
                               
							<?php if($sess_accesslevel != 5 && !$is_read_only && !$case_closed && $count_usee > 1){?>
                             
                                <div align="center">
                                    
                                    
                                    	 <a href="scrp_select_curator_new.php?id=<?php echo doCleanOutput($sub_row["curator_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" onClick="return confirm('ยืนยันเลือกผู้ถูกใช้สิทธิ?');" style="font-weight: normal;">
                                    	คลิกที่นี่เพื่อเลือกผู้ถูกใช้สิทธิ
                                        
                                        </a>
                                                  
                                    
                                   
                                </div>
                             
                              <?php }?>
                               
                               </td>
                               
                               
                               
                                  <td valign="top" ><?php echo doCleanOutput($sub_row["curator_name"]);?></td>
                                  <td valign="top" ><?php echo formatGender($sub_row["curator_gender"]);?></td>
                                  <td valign="top" ><?php echo doCleanOutput($sub_row["curator_age"]);?></td>
                                  <td valign="top" >
								  
								  <?php echo doCleanOutput($sub_row["curator_idcard"]);?>
                                  
                                  
                                  
                                  <?php 
								  	
									//yoes 20160707 --- also check duped here									
									include "widget_check_35-33_duped.php";
									include "widget_check_35-35_duped.php";
								  
								  ?>
                                  
                                  
                                  </td>
                                  <td  valign="top"  colspan="9">ผู้ถูกใช้สิทธิ: <?php echo doCleanOutput($sub_row["curator_disable_desc"]);?></td>
                              
                             </tr>      
                        
                        
                        	<?php } //end loop for child?>
                        
							<script>
								if ($("#curator_<?php echo $post_row["curator_id"];?>_alt_amount").length){
									//alert("#<?php echo $post_row["curator_id"];?>_alt_amount");
									
									<?php
										if($interests_row[p_principal_after]+$interests_row[pending_interests] == 0){
											
										}elseif($interests_row[p_principal_after]+$interests_row[pending_interests] >= 0){
										?>
											$("#curator_<?php echo $post_row["curator_id"];?>_alt_amount").html("รวมต้องชำระ <?php echo number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2);?> บาท");
										<?php
											//echo "<br><b>รวมต้องชำระ " . number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2) . " บาท</b>";
										}else{
											
											?>													
											$("#curator_<?php echo $post_row["curator_id"];?>_alt_amount").html("จ่ายเกิน <?php echo number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2);?> บาท");
											<?php
											
										}
									
									?>
									
									
								}
							</script>
						