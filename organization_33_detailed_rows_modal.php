<?php			

include "db_connect.php";
include "scrp_config.php";
include "session_handler.php";

//print_r($_POST);
//echo "asdasd"; exit();
$post_row = $_POST;

$this_id = $_POST[this_id];
$this_lawful_year = $_POST[this_lawful_year];
$this_lid = $_POST[this_lid];

//echo "whatis"; exit();

$row_is_parent = 0;

//echo "-".$post_row["parent_meta_value"];

if($post_row["parent_meta_value"]){
	
	$row_is_parent = 1;
	$total_day_deducted = 0;
	
}



$row_is_child = 0;

if($post_row["child_meta_value"]){
	
	$row_is_child = 1;
	
}


//yoes 20181018 add stand-alone row
$row_is_standalone = 0;

if(!$post_row["parent_meta_value"] && !$post_row["child_meta_value"]){

    $row_is_standalone = 1;

}

//echo "row is alone: " . $row_is_standalone;
	
if($post_row["group_count"]%2 == 0){	
	$the_bg = "bgcolor='#F4F6F6'";
}else{
	$the_bg = "bgcolor='#ffffff'";
}


if($sess_userid == 1){
	
	$time_start = microtime(true);
}


//yoes 20220628
//see if this is a request from ejob tables (lawful_employees, etc)
if($post_row["is_ejob"]){
	
	$is_ejob = 1;
	
}




?>


<?php 
//yoes 20181018
//parent can be also be child
if($row_is_parent &&  !$row_is_child ){
	$sub_group_count = 0;
}else{
	$sub_group_count++;
}

?>


<?php if($row_is_parent){ $the_bg = "bgcolor='#ABB2B9'"; ?>

	<tr bgcolor='#ABB2B9' id="<?php echo $post_row["le_id"];?>_alt">
		<td  valign="top" style="margin-left: 20px;"><div align="center">

                <?php
                    echo $post_row["group_count"];
                    if($sub_group_count && !$row_is_standalone){echo ".".$sub_group_count;}

                    ?></div></td>
		<td valign="top">
		
		
		<a href='#' onclick='doToggle33Row(<?php echo $post_row["le_id"];?>); return false;' style="font-weight: normal;">
			<?php echo doCleanOutput($post_row["le_name"]);?>
			
			<?php if(getFirstItem("select meta_value from lawful_employees_meta where meta_leid = '".$post_row["le_id"]."' and meta_for = 'is_extra_33'")){
				 ?>
				<font color="purple">(เป็นการจ้างเกินอัตราส่วน)</font>
			 <?php }?>
		</a>
		
		</td>
		<td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
		<td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
		<td valign="top"> <?php echo doCleanOutput($post_row["le_code"]);?>
		
			<?php 
							
				if(!$is_ejob){
					
					//yoes 20151201 -- move this to other file instead
					include "widget_check_33-33_duped.php";
				 
					//yoes 20151201 -- move this to other file instead
					include "widget_check_33-35_duped.php";
				
				}
			 
			 ?>
		
		</td>
		<td>
		</td>
		<td colspan=8>
			<?php
			
				echo formatDateThai($post_row["le_start_date"],0);														
														
				if($post_row["le_end_date"] && $post_row["le_end_date"] != '0000-00-00'){
					echo "-".formatDateThai($post_row["le_end_date"],0);
				}
				
				
				
				if($this_lawful_year >= 2018){
															
					if($post_row["le_start_date"] != '0000-00-00'){

					    $the_end_date = $post_row["le_end_date"];
                        //echo  $the_end_date;

					    if(!$the_end_date || $the_end_date == '0000-00-00') {
                            $the_end_date = $this_lawful_year ."-12-31";
                        }

						/*
						//array to get deduction result
						$deduct_33 = get33DeductionByLeidArray($post_row["le_id"]);

						echo "<font>(แทน ม.34 ได้ "
							. $deduct_33[reduction_days] ." วัน "
							. number_format($deduct_33[reduction_amount],0) ." บาท)</font>";
							
						echo "<font>(ดอกเบี้ย ". number_format($deduct_33[interest_days],0) ." วัน)</font>";
						echo "<font>(ดอกเบี้ย ". number_format($deduct_33[interest_amount],0) ." บาท)</font>";
						*/
						
						
					}
					
				}
			
			?>
			
			<?php 
			//yoes 20241010 -- cp all *- 1289 is slow - skip this
			//1631 is big c
			if($is_ejob && $post_row["le_cid"] != 1289 && $post_row["le_cid"] != 1631000000){?>
			<br><span id="submitted_employees_table_ejob33_head_<?php echo $post_row["le_id"];?>" v-html="the_text"></span>
			<script>

				var submitted_employees_table_ejob33_head_<?php echo $post_row["le_id"];?> = new Vue({
				  el: '#submitted_employees_table_ejob33_head_<?php echo $post_row["le_id"];?>',
				  data: {
						   the_text: ''
						 }
				   });													   
				  
				   axios
					.post("https://job.dep.go.th/ajax_get_le_end_date_sso.php?le_id=<?php echo $post_row["le_id"];?>&the_cid=<?php echo $post_row["le_cid"];?>")
					.then(response => {
						submitted_employees_table_ejob33_head_<?php echo $post_row["le_id"];?>["the_text"] = response.data;
					})
			</script>	
			<?php }?>
			
			<br>
			<b><span id="<?php echo $post_row["le_id"];?>_alt_amount"></span></b>
			
		</td>
		
		
		
	</tr>



<?php }?>




  <tr  class="bb" id="<?php echo $post_row["le_id"];?>_main"
	<?php echo $the_bg; ?> 
	<?php if($row_is_parent){echo "style='display: none;'"; }?>>
  
  
                                      <td  valign="top" style="margin-left: 20px;"><div align="center">

                                              <?php

                                                echo $post_row["group_count"]; if($sub_group_count && !$row_is_standalone){echo ".".$sub_group_count;}


                                                ?>



                                          </div></td>
                                      
									  
									  <td valign="top">
									  
									 
									  
										  <?php if($row_is_parent){ ?>
											  <a href='#' onclick='doToggle33Row(<?php echo $post_row["le_id"];?>); return false;' style="font-weight: normal;">
												<?php echo doCleanOutput($post_row["le_name"]);?> 
												</a>
										  <?php }else{ ?>
										  
											<?php echo doCleanOutput($post_row["le_name"]);?> 
										  
										  <?php }?>
										  
										 <?php if(getFirstItem("select meta_value from lawful_employees_meta where meta_leid = '".$post_row["le_id"]."' and meta_for = 'is_extra_33'")){
											 ?>
											<font color="purple">(เป็นการจ้างเกินอัตราส่วน)</font>
										 <?php }?>
									  </td>
                                      
									  
									  <td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
                                      
									  
									  
									  <td valign="top">
										<?php echo doCleanOutput($post_row["le_age"]);?>
										
										
										<?php
										
											if($post_row["le_dob"] && $post_row["le_dob"] != "0000-00-00"){
										?>
											<br>
											<font color=green>(วันเกิด <?php
											
												echo formatDateThai($post_row["le_dob"],0);
											
											?>)</font>
										<?php
											}
										?>
										
										</td>
									  
									  
									  
                                      <td valign="top">
									  <?php echo doCleanOutput($post_row["le_code"]);?>
                                      
                                      
                                      
                                      	<?php if($sess_accesslevel != 4 && !$is_ejob){ // yoes 20140910 ---- show status whether it's in oracle or not?>
                                        
                                        
                                        		<?php if($post_row["le_is_dummy_row"]){?>
                                                
                                                	                                                    <div style="color:#F60">
                                                    <strong>! เป็นข้อมูลชั่วคราว</strong>
                                                  </div>

                                        
												<?php }elseif(!$post_row["le_from_oracle"]){?>
                                                    <div style="color:#660">
                                                    <strong>! ไม่พบข้อมูลในฐานข้อมูลการออกบัตร</strong>
                                                  </div>
                                                  
                                                  <?php }else{?>
                                                  <div style="color:#6C0">
                                                    พบข้อมูลในฐานข้อมูลการออกบัตร
                                                  </div>
                                                  
                                                  <?php }?>
                                      
                                     	 <?php }?>
                                      
                                      
                                      	<?php if($post_row["is_extra_row"]){ // yoes 20150118?>
                                        	  <div style="color:#F60">
                                                    <strong>! เป็นข้อมูลที่ถูกเพิ่มมาหลังจากมีการปิดงาน<br />และจะไม่ถูกนำไปใช้ในการคิดการปฏิบัติตามกฏหมาย</strong>
                                                  </div>
                                        <?php }?>
                                      
                                      
										 <?php 
										 
											//yoes 20220628
											if(!$is_ejob){
										 
												//yoes 20151201 -- move this to other file instead
												include "widget_check_33-33_duped.php";
											 
												//yoes 20151201 -- move this to other file instead
												include "widget_check_33-35_duped.php";
											
											}
										 
										 ?>
                                      
                                      </td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_disable_desc"]);?></td>
                                      <td valign="top">
									  	<?php 
														
														
											echo formatDateThai($post_row["le_start_date"],0);														
											
											if($post_row["le_end_date"] && $post_row["le_end_date"] != '0000-00-00'){
												echo "-".formatDateThai($post_row["le_end_date"],0);
											}
											
											
										?>
										
										
											<?php 
											
											
											//yoes 20241010 -- cp all *- 1289 is slow - skip this
											if($is_ejob && $post_row["le_cid"] != 1289 && $post_row["le_cid"] != 1631000000){?>
											<br><span id="submitted_employees_table_ejob33_child_<?php echo $post_row["le_id"];?>" v-html="the_text"></span>
											<script>

												var submitted_employees_table_ejob33_child_<?php echo $post_row["le_id"];?> = new Vue({
												  el: '#submitted_employees_table_ejob33_child_<?php echo $post_row["le_id"];?>',
												  data: {
														   the_text: ''
														 }
												   });													   
												  
												   axios
													.post("https://job.dep.go.th/ajax_get_le_end_date_sso.php?le_id=<?php echo $post_row["le_id"];?>&the_cid=<?php echo $post_row["le_cid"];?>")
													.then(response => {
														submitted_employees_table_ejob33_child_<?php echo $post_row["le_id"];?>["the_text"] = response.data;
													})
											</script>	
											<?php }?>
										
										
											<?php
											
											//
												
											//yoes 20181120
											//validate with sso
											//-- todo by pDang :D											
											/*
											
												(1) นำ $post_row["le_code"] และ $output_values["CompanyCode"] ไป run กับ "web service ของประกันสังคม" ที่ใช้ใน ajax_get_sso.php
												(2) นำค่า $empResignDate จาก ajax_get_sso.php มา fill ลง value ด้านล่างในรูปแบบ "yyyy-mm-dd"
												
											*/			
											
											
											//pDang to fill here as "yyyy-mm-dd"
											//$empResignDate = $empResignDate;
											
											//yoes 20181121
											$empResignDate = "";
											$sso_le_code = $post_row["le_code"];
											$sso_company_code = $output_values["CompanyCode"];											
											
											
											if(
												
												$this_id == 1289
												
												|| $this_id == 1397
												
												|| $this_id == 2041
												
												|| 1 == 1 //yoes 20190321 --- just disable this for good for now
											
											){
												//yoes 20190305 - this is slow so will move this to ajax tomorrow
												//$ssoDat = "";
											}else{
												include("scrp_get_emp_resign_date_from_sso.php");
												$ssoDat = getSSOdata($sso_le_code,$sso_company_code);
											}
											
											
											$empResignDateFromSso = "";
											
											
											foreach($ssoDat as $ssoRec){
												if(
													
													$ssoRec[CompanyCode] == $sso_company_code 
													&& !$empResignDateFromSso
													
												){ 
													
													//echo "<br>ResignDate: ".$ssoRec[ResignDate];
													
													
													
													// yoes 20190319 -- only get top most record only
													$empResignDateFromSso = $ssoRec[ResignDate];
													
													if(!$empResignDateFromSso){
														
														//replace blank value with 1 jan next year
														$empResignDateFromSso = ($this_lawful_year + 1)."-01-01";
														
														//echo "<br>empResignDateFromSso: ".$empResignDateFromSso;
														
													}
												}
											}
											
											$empResignDate = $empResignDateFromSso;
											
											//echo "$empResignDate < ".$post_row["le_end_date"];
											
											
											if(!is_array($sso_validated_array)){
												$sso_validated_array = array();
											}
											
											
											$empResignDate_year = substr($empResignDate, 0, 4);
											
											if($sess_accesslevel == 1){
												
												//print_r($ssoDat);												
												//echo "<br> $empResignDate vs " . $post_row["le_end_date"] . " - " . $empResignDate_year;
												
											}
											
											if(
													(											
														$empResignDate 	// -> person already left
														&& $empResignDate != $post_row["le_end_date"] // -> left date not equal end date
														&& $empResignDate_year <= $this_lawful_year //-> left year is this less than this year
														
														
													)
													
													|| 
													
													(
														$empResignDate // -> person already left
														&& !$post_row["le_end_date"]	//job.dep.go.th didn't have end date
														&& $empResignDate_year <= $this_lawful_year	//left year is less than this year
														
													)
													
													//yoes 20190319
													//
													
													
												
												){
												
												$sso_validated_failed = 1;		


												array_push($sso_validated_array, $post_row["le_code"]. " : " . doCleanOutput($post_row["le_name"]));
											
												?>
												<span class="blink_me" style="color:#cc00cc; font-weight: bold; ">
													<br>พบการออกจากงานวันที่ <?php /*get this value from sso*/ echo formatDateThai($empResignDate); ?> (ตามข้อมูล สปส)
												</span>											
											
											<?php 
											
													//yoes 20181121
													//mark it so it show on dashboard...
													$meta_sql = "
													
														replace into
															lawful_employees_meta(
																
																meta_leid
																, meta_for
																, meta_value
															
															)
														values(
														
															'".$post_row["le_id"]."'
															, 'sso_failed'
															, '".$empResignDate."'
														
														)
													
													";
													
													mysql_query($meta_sql);
												
											
												}else{
													
													
													//sso not failed -> check if it is failed before
													if($post_row["sso_failed"]){
														
														//if "yes" then -> it no longer not failed
														$meta_sql = "
													
															delete from
																lawful_employees_meta
															where
															
																meta_leid = '".$post_row["le_id"]."'
																and meta_for = 'sso_failed'
														
														";
														
														mysql_query($meta_sql);
														//echo "meta_is_failed";
														
													}
														
													
													
												}
												
												
												
											?>
												
												
												
										
											
										
										<?php
											
											//yoes 20201207 -- no longer need this for new bta thing
											//include "organization_33_detailed_rows_2018_law_widget.php";
											//yoes 20200612 - new kind of way to show 33-principal
											
											$is_beta = getLidBetaStatus($this_lid);
											if($is_beta && !$is_ejob){
											
												$principal_sql = "
												
													select
														*
													from
														lawful_33_principals
													where
														p_from = '".$post_row["le_id"]."'
														or
														(
															p_from = 0
															and
															p_to = '".$post_row["le_id"]."'
														)
												
												";
												
												
												//echo $principal_sql;
												
												$principal_result = mysql_query($principal_sql);
												
												$interests_row = array();
												
												//$principal_row = getFirstRow($principal_sql);
												while($principal_row = mysql_fetch_array($principal_result)){
													
													//print_r($principal_row);
													
													if($principal_row && $this_lawful_year >= 2018){
														
														
														//yoes 20200724 for https://app.asana.com/0/794303922168293/1185797049999353
														$day_display_offset = 0;
														if($principal_row[p_from] && $principal_row[p_to]){
															$day_display_offset = -1;															
														}
														
														//echo "<br>---- beta ----";
														echo "<br><font color=orangered>ต้องจ่ายเงินแทน "
															. number_format(dateDiffTs(strtotime($principal_row[p_date_from]), strtotime($principal_row[p_date_to]), $day_display_offset),0) ." วัน "
															. number_format($principal_row[p_amount],2) ." บาท ";
														
														echo "</font>";	
														
														//yoes 20200624 -- total for each chain
														//$m33row_total_principal += $principal_row[p_amount];
														
														
														//yoes 20200618 try get interests function here...
														//echo $this_lid ." -- ". $principal_row[p_from] ." -- ".  $principal_row[p_to];
														$interests_row = generateInterestsFromPrincipals($this_lid, $principal_row[p_from],  $principal_row[p_to]);
														
														
														$interest_details = $interests_row[interest_details];
														
														//print_r($interest_details);
														
														$m33row_total_paid = 0;
														
														for($iii = 0; $iii < count($interest_details) ; $iii++){
															
															echo "<br>1. เงินต้นต้องชำระ ".number_format($interest_details[$iii][pre_pending_principal], 2);
															
															
															
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
															
															if($interest_details[$iii][ReceiptDate]){
																//echo "<br>เงินต้นคงเหลือ ณ  " . formatDateThai($interest_details[$iii][ReceiptDate], 0) . " " . number_format($interest_details[$iii][pre_pending_principal], 2) . " บาท";
															}
															//echo "<br>ดอกเบี้ย ณ วันนี้ " . number_format($interest_details[$iii][this_interest], 2) . " บาท";
															//echo "<br>ดอกเบี้ยคิดจากวันที่ " . formatDateThai($interest_details[$iii][interest_start_date], 0) . " ถึง " . formatDateThai($interest_details[$iii][interest_end_date], 0) . " (".$interest_details[$iii][interest_days]." วัน)";
															//echo "<br>ดอกเบี้ยคิดจากเงินต้น " . number_format($interest_details[$iii][pre_principal_to_calculate_interests], 2) . " บาท";
															
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
													/*
													echo "<br>รวมต้องจ่ายเงินต้น+ดอกเบี้ย " . number_format($principal_row[p_amount],2) . "+".number_format($principal_row[p_interests],2)."=".number_format($principal_row[p_amount]+$principal_row[p_interests],2)."บาท";
													echo "<br>มีการจ่ายเงินแล้ว " . number_format($m33row_total_paid,2) . " บาท";
													echo "<br>คงเหลือ " . number_format(($principal_row[p_amount]+$principal_row[p_interests])-$m33row_total_paid,2) . " บาท";
													*/
													
													if(!$is_ejob){
														include "organization_33_detailed_rows_2020_law_widget.php";
													}
												
												}
												
												
												
												
												
											}
											
										?>
										
										
											
									</td>
														
														
                                      
                                      <td valign="top"><div align="right">
									  
									  <?php echo formatNumber($post_row["le_wage"]);?>
                                      
                                      
                                      <?php echo getWageUnit($post_row["le_wage_unit"]);?>
                                      
                                      </div></td>
                                      
                                      <td valign="top"><?php 
									  
									  	if(is_numeric($post_row["le_position"])){
									  		echo formatPositionGroup($post_row["le_position"]);									  
										}else{
									  		echo doCleanInput($post_row["le_position"]);
										}
										
										?></td>
                                      
                                      <td valign="top"><?php echo formatEducationLevel(doCleanOutput($post_row["le_education"]));?></td>
										<td valign="top">
									  
										  <?php 
                                          	//file แนบ here
											
											//doc count
											$required_doc_33_1 = 1;
											$required_doc_33_2 = 1;
											
											
											$leid_to_get_file = $post_row["le_id"];
											
											if($is_ejob){										
											
												if($post_row["job_leid"]){
													$leid_to_get_file = $post_row["job_leid"];
													$le_file_url = "https://job.dep.go.th";
												}else{												
													$leid_to_get_file = $post_row["le_id"];
													$le_file_url = "https://ejob.dep.go.th/ejob";
												}
											
											}
											
											//yoes 20160427 -->
											//also see if there are any attached files											 
											$curator_file_path = mysql_query("select 
																					* 
																			   from 
																					 files 
																				where 
																					file_for = '".$leid_to_get_file."'
																					and
																					(
																					
																						file_type = 'docfile_33_1'																						
																						or
																						file_type = 'docfile_33_2'
																						or
																						file_type = 'docfile_33_71'
																					)
																					");
											
											$file_count_33 = 0;
											
											/*if($this_id == 66636 && $leid_to_get_file == 638128){
													echo "select 
																					* 
																			   from 
																					 files 
																				where 
																					file_for = '".$leid_to_get_file."'
																					and
																					(
																					
																						file_type = 'docfile_33_1'																						
																						or
																						file_type = 'docfile_33_2'
																						or
																						file_type = 'docfile_33_71'
																					)
																					";
											}	*/										
											
											//echo $leid_to_get_file;
											//echo "is_ejob: $is_ejob";
																		
											while ($file_row = mysql_fetch_array($curator_file_path)) {
											
												$file_count_33++;
												
												
												
												if($file_count_33 > 1){echo "<br>";}
											?>
                                            	
												
												<?php 
													
													//yoes 20220628
													if($is_ejob){ ?>
                                            
											
															
														<?php 
													
															//yoes 20221221 - quickfix?
															if(substr($file_row["file_name"],0,4)=="ejob"){
														?>
																
																<a href="https://ejob.dep.go.th/ejob/hire_docfile/<?php echo substr($file_row["file_name"],5);?>" 
																target="_blank">
															
														<?php	}else{ ?>
																
														
																<a href="<?php echo $le_file_url; ?>/hire_docfile/<?php 
														
														
																	if($post_row["job_leid"]){
																		echo str_replace("ejob","",$file_row["file_name"]);
																	}else{
																		echo $file_row["file_name"];
																	}
																 
																 
																 ?>" target="_blank">
																 
														<?php }?>
													
                                            	<?php 
												
													//echo substr($file_row["file_name"],0,4);
													}elseif(substr($file_row["file_name"],0,4)=="ejob"){
												?>
													<a href="https://ejob.dep.go.th/ejob/hire_docfile/<?php echo substr($file_row["file_name"],5);?>" target="_blank">
												<?php	
													}else{
												?>
													<a href="hire_docfile/<?php echo $file_row["file_name"];?>" target="_blank">
                                                 <?php }?>
                                                
                                                <?php 
													if($file_row["file_type"] == "docfile_33_1"){
														echo "สำเนาสัญญาจ้าง";
														$required_doc_33_1--;
													}elseif($file_row["file_type"] == "docfile_33_2"){
														echo "สำเนาบัตรประจำตัวคนพิการ/ผู้ดูแลคนพิการ";
														$required_doc_33_2--;
														
													}elseif($file_row["file_type"] == "docfile_71"){
														echo "จพ 7/1";																												
														$required_doc_33_2--;
														
													}else{
														echo "ไฟล์แนบ";	
													}
													
												?>
                                                
                                                </a>
												
												<?php if(!$read_only && !$case_closed && !$row_is_parent && !$is_ejob){ //yoes 20160816 --> add this?>
												<a href="scrp_delete_curator_file.php?id=<?php echo $file_row["file_id"];?>&curator_id=<?php echo $post_row["le_id"];?>&return_id=<?php echo $this_id;?>" title="ลบไฟล์แนบ" onClick="return deleteFile(<?php echo $file_row["file_id"].",".$post_row["le_id"].",".$this_id; ?>);" style="color: red;">ลบ</a>
                                                <?php }?>
												
										
												<!--<a href="force_load_file.php?file_for=<?php echo $file_row["curator_id"];?>&file_type=curator_docfile" target="_blank">ไฟล์แนบ</a>-->
											<?php
											
											
											}
											
											
                                          ?>
                                          
                                          <?php 
										  
										   /*yoes 20230130 --> hide this for 2023 and up*/ 
										  if($this_lawful_year >= 2023 && $this_lawful_year < 2500){
											  $required_doc_33_1 = 0;
											  
										  }
										  
										  if($required_doc_33_1){
											
											$required_doc++;
											
											$file_count_33++;
											if($file_count_33 > 1){echo "<br>";}
											echo "<font color='red'>กรุณาแนบไฟล์สำเนาสัญญาจ้าง</font>";  
											
										  }
										  if($required_doc_33_2){
											  
											$required_doc++;
											  
											$file_count_33++;
											if($file_count_33 > 1){echo "<br>";}
											echo "<font color='red'>กรุณาแนบไฟล์สำเนาบัตรประจำตัวคนพิการ/ผู้ดูแลคนพิการ</font>";  
											
										  }
										  ?>
                                          
                                          <?php if($required_doc_33_1 || $required_doc_33_2){?>
                                          
                                          	<script>
                                            	
												$("#alert_33_files").show();
												
												$("#submit_doc").hide();
												
												$("#js_doc_warning").show();
                                            
                                            </script>
                                          
                                          <?php										  
											  
										  }?>
                                      
                                      </td>
                                     
                                     <?php

										//yoes 20220628
									 if($is_ejob){

											//do nothing

									 }elseif($sess_accesslevel != 5 && !$is_read_only && !$case_closed && !$row_is_parent){?>
                                         
                                         
                                         
                                         <?php if($post_row["is_extra_row"]){?>
                                         
                                              <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&is_extra_row=1" title="ลบข้อมูล" onClick="return deletePerson(<?php echo doCleanOutput($post_row["le_id"]).",".$this_id.",".$this_lawful_year; ?>);" style="color: red;">ลบ</a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leidex=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล">แก้ไข</a></div></td>
                                         
                                          <?php }else{?>
                                          
                                          
                                              <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onClick="return deletePerson(<?php echo doCleanOutput($post_row["le_id"]).",".$this_id.",".$this_lawful_year; ?>);" style="color: red;">ลบ</a></div></td>
                                              
	  											<td valign="top">
													
                                              
													<div align="center">
														<a href="#" onClick='getLeidForm(<?php echo $post_row["le_id"];?>); $("#my_popup").animate({ scrollTop: 0 }, "smooth"); return false;' title="แก้ไขข้อมูล">
															แก้ไข
														</a>
													</div>
												</td>
                                          
                                          <?php }?>
                                          
                                      <?php }elseif($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only &&  $post_row["is_extra_row"]){ //extra row allow edit no matter what?>
                                         
                                         
                                          <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&is_extra_row=1" title="ลบข้อมูล" onClick="return deletePerson(<?php echo doCleanOutput($post_row["le_id"]).",".$this_id.",".$this_lawful_year; ?>);" style="color: red;">ลบ</a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leidex=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล">แก้ไข</a></div></td>
                                          
                                          
                                      <?php }else{?>
									  
										<td></td>
										<td></td>
										
									  
									  <?php } ?>



                                      <td>

                                          <?php


                                          //yoes 20250103
                                          //print_r($post_row);
                                          //echo $the_job_leid ;
                                          //if($submitted_company_lawful == 3){



                                              if($post_row["job_leid"]){
                                                  //echo "<font color=green>* เป็นการปรับปรุงข้อมูลเดิม</font>";
                                              }else{
                                                  //echo "<font color=orange>* เป็นการกรอกข้อมูลใหม่</font>";

                                              }
                                          //}


                                          ?>


                                      </td>
                                      
                                      
                                      
                                      
                                    </tr>
									<script>
										if ($("#<?php echo $post_row["le_id"];?>_alt_amount").length){
											//alert("#<?php echo $post_row["le_id"];?>_alt_amount");
											
											<?php
												if($interests_row[p_principal_after]+$interests_row[pending_interests] == 0){
													
												}elseif($interests_row[p_principal_after]+$interests_row[pending_interests] >= 0){
												?>
													$("#<?php echo $post_row["le_id"];?>_alt_amount").html("รวมต้องชำระ <?php echo number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2);?> บาท");
												<?php
													//echo "<br><b>รวมต้องชำระ " . number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2) . " บาท</b>";
												}else{
													
													?>													
													$("#<?php echo $post_row["le_id"];?>_alt_amount").html("จ่ายเกิน <?php echo number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2);?> บาท");
													<?php
													
												}
											
											?>
											
											
										}
									</script>
									
<?php

	if($sess_userid == 1){
	
		$time_elapsed_secs = microtime(true) - $time_start;
		//echo "time_elapsed_secs: ".$time_elapsed_secs;
	}

?>