<?php 
if($total_records == 1){ // see if we have to draw headers
?>

	<style>

		.blink_me {
		  animation: blinker 1s linear infinite;
		}

		@keyframes blinker {
		  50% {
			opacity: 0.3;
			/*color: #000000;*/
			/*font-weight: bold;*/
		  }
		}
	</style>
    <tr bgcolor="#efefef">
      <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
      <td><div align="center">ชื่อ</div></td>
      <td><div align="center">เพศ</div></td>
      <td><div align="center">อายุ</div></td>
      <td><div align="center">เลขที่บัตรประชาชน</div></td>
      <td width="140px"><div align="center">ลักษณะความพิการ</div></td>
      <td><div align="center">เริ่มบรรจุงาน </div></td>
      <td><div align="center">ค่าจ้าง </div></td>
      <td ><div align="center">ตำแหน่งงาน</div></td>
      <td ><div align="center">การศึกษา</div></td>
      <td ><div align="center">ไฟล์แนบ</div></td>
      <?php if($sess_accesslevel != 5 && !$is_read_only && !$case_closed ){?>
      <td><div align="center">ลบข้อมูล</div></td>
      <td><div align="center">แก้ไขข้อมูล</div></td>
      <?php }?>
    </tr>

<?php

}			//ends $total_records == 1				

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



include_once("organization_33_detailed_rows_js.php");

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
		</a>
		
		</td>
		<td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
		<td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
		<td valign="top"> <?php echo doCleanOutput($post_row["le_code"]);?></td>
		<td>
		</td>
		<td colspan=7>
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
									  </td>
                                      
									  
									  <td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
                                      <td valign="top">
									  <?php echo doCleanOutput($post_row["le_code"]);?>
                                      
                                      
                                      
                                      	<?php if($sess_accesslevel != 4){ // yoes 20140910 ---- show status whether it's in oracle or not?>
                                        
                                        
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
										 
										 	//yoes 20151201 -- move this to other file instead
										 	include "widget_check_33-33_duped.php";
										 
										 ?>
                                          
                                          
                                           <?php 
										 
										 	//yoes 20151201 -- move this to other file instead
										 	include "widget_check_33-35_duped.php";
										 
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
											
											//include("scrp_get_emp_resign_date_from_sso.php");
											$ssoDat = getSSOdata($sso_le_code,$sso_company_code);											
											//$ssoDat = "";
											$empResignDateFromSso = "";
											foreach($ssoDat as $ssoRec){
												if($ssoRec[CompanyCode] == $sso_company_code)
													$empResignDateFromSso = $ssoRec[ResignDate];
											}
											
											$empResignDate = $empResignDateFromSso;
											
											//echo "$empResignDate < ".$post_row["le_end_date"];

											$sso_validated_array = array();
											
											if(($empResignDate && $empResignDate != $post_row["le_end_date"])
													|| ($empResignDate && !$post_row["le_end_date"])
													//|| 1==1
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
											
											include "organization_33_detailed_rows_2018_law_widget.php";
											
											
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
											
											//yoes 20160427 -->
											//also see if there are any attached files											 
											$curator_file_path = mysql_query("select 
																					* 
																			   from 
																					 files 
																				where 
																					file_for = '".$post_row["le_id"]."'
																					and
																					(
																					
																						file_type = 'docfile_33_1'																						
																						or
																						file_type = 'docfile_33_2'
																					)
																					");
											
											$file_count_33 = 0;
																		
											while ($file_row = mysql_fetch_array($curator_file_path)) {
											
												$file_count_33++;
												
												if($file_count_33 > 1){echo "<br>";}
											?>
                                            	
                                                
                                            
                                            
                                            	<?php 
												
													//echo substr($file_row["file_name"],0,4);
													if(substr($file_row["file_name"],0,4)=="ejob"){
												?>
													<a href="http://ejob.dep.go.th/ejob/hire_docfile/<?php echo substr($file_row["file_name"],5);?>" target="_blank">
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
														
													}else{
														echo "ไฟล์แนบ";	
													}
													
												?>
                                                
                                                </a>
												
												<?php if(!$read_only && !$case_closed && !$row_is_parent){ //yoes 20160816 --> add this?>
												<a href="scrp_delete_curator_file.php?id=<?php echo $file_row["file_id"];?>&curator_id=<?php echo $post_row["le_id"];?>&return_id=<?php echo $this_id;?>" title="ลบไฟล์แนบ" onClick="return confirm('คุณแน่ใจหรือว่าจะลบไฟล์แนบ? การลบไฟล์ถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบไฟล์ได้');"><img src="decors/cross_icon.gif" alt="" height="10"  border="0" /></a>
                                                <?php }?>
												
										
												<!--<a href="force_load_file.php?file_for=<?php echo $file_row["curator_id"];?>&file_type=curator_docfile" target="_blank">ไฟล์แนบ</a>-->
											<?php
											
											
											}
											
											
                                          ?>
                                          
                                          <?php 
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
                                     
                                     <?php if($sess_accesslevel != 5 && !$is_read_only && !$case_closed && !$row_is_parent){?>
                                         
                                         
                                         
                                         <?php if($post_row["is_extra_row"]){?>
                                         
                                              <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&is_extra_row=1" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leidex=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div></td>
                                         
                                          <?php }else{?>
                                          
                                          
                                              <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                              
                                              
                                              <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leid=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div></td>
                                          
                                          <?php }?>
                                          
                                      <?php }elseif($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only &&  $post_row["is_extra_row"]){ //extra row allow edit no matter what?>
                                         
                                         
                                          <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&is_extra_row=1" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leidex=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div></td>
                                          
                                          
                                      <?php }else{?>
									  
										<td></td>
										<td></td>
										
									  
									  <?php } ?>
                                      
                                      
                                      
                                      
                                    </tr>