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
										 	//include "widget_check_33-33_duped.php";
										 
										 ?>
                                          
                                          
                                           <?php 
										 
										 	//yoes 20151201 -- move this to other file instead
										 	//include "widget_check_33-35_duped.php";
										 
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
									  
										 
                                      
                                      </td>
                                     
                                     <?php if($sess_accesslevel != 5 && !$is_read_only && !$case_closed && !$row_is_parent){?>
                                         
                                         
                                         
                                         <?php if($post_row["is_extra_row"]){?>
                                         
                                              <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&is_extra_row=1" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');">ลบ</a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leidex=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล">แก้ไข</a></div></td>
                                         
                                          <?php }else{?>
                                          
                                          
                                              <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');">ลบ</a></div></td>
                                              
                                              
                                              <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leid=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล">แก้ไข</a></div></td>
                                          
                                          <?php }?>
                                          
                                      <?php }elseif($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only &&  $post_row["is_extra_row"]){ //extra row allow edit no matter what?>
                                         
                                         
                                          <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&is_extra_row=1" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');">ลบ</a></div></td>
                                          
                                          
                                          <td valign="top"><div align="center"><a href="organization.php?id=<?php echo $this_id;?>&le=le&focus=lawful&year=<?php echo $this_lawful_year;?>&leidex=<?php echo doCleanOutput($post_row["le_id"]);?>" title="แก้ไขข้อมูล">แก้ไข</a></div></td>
                                          
                                          
                                      <?php }else{?>
									  
										<td></td>
										<td></td>
										
									  
									  <?php } ?>
                                      
                                      
                                      
                                      
                                    </tr>