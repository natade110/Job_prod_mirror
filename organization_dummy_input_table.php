<div align="center">
<table style=" padding:10px 0 0px 0; <?php echo "display: none;"; ?>" cellpadding="5" id="dummy" width="80%">
          
             <tr>
                        <td><div style="font-weight: bold; padding:0 0 5px 0;">ส่งข้อมูลชำระเงิน</div></td>
                      </tr>
             
             
             <form action="?id=<?php echo $this_id;?>&focus=dummy" method="post">
             	
                  <tr>
                  
                  	<td bgcolor="#fcfcfc" style="padding-right:20px;" colspan="2"><strong style="color:#006600;">
                          
                       ข้อมูลประจำปี <?php //echo $this_year +543;    ?>
                                    
                       </strong>             
                                    
                      
                      
                        <?php
                                     
									 
									    
                                //print_r($_POST);
                                include "ddl_year_auto_submit_lawful_dummy.php";
                          
                            ?>                   
                                    
                                    
                      </td>
                      
                      
                      
                     
                      
                      
                  </tr>
          
          
        	  </form>
          
          
            
             <?php if($the_year == 2011){?>
            <?php }?>
            
                        <tr>
                          <td valign="top" bgcolor="#fcfcfc">จำนวนลูกจ้างทั่วประเทศ
                          
                          
                          </td>
                          <td valign="top"> 
                                <strong><?php 
								
								 
								 echo formatEmployee($employee_to_use_from_lawful);
								 
								 ?></strong>
                                     
                               คน 
                               |
                               
                               <?php if(!$is_read_only){?>
                                <a href="#" onclick="fireMyPopup('employees_popup',500,250); $('#employees_popup_focus').val('dummy'); return false;">ปรับปรุงข้อมูล</a>
                                <?php }?>
                               </td>
                         
                      </tr>
                      
                      
                       <tr>
                              <td  style="padding-right:20px;" bgcolor="#fcfcfc">อัตราส่วน<?php echo $the_employees_word;?>ต่อคนพิการ: </td>
                              <td><?php 
                                
                                echo ($ratio_to_use);
                                
                              
                              ?>:1 = <strong id="employee_ratio"><?php 
                                
                                echo formatEmployee($final_employee);
                                
                                ?></strong> คน</td>
                            </tr>
                      
                      
                      	<tr>
                              <td bgcolor="#fcfcfc" style="padding-right:20px;" colspan="2"><strong style="color:#006600;">สรุปการดำเนินการตามกฎหมาย</strong></td>
                              
                            </tr>
                            
                            <tr>
                              <td bgcolor="#fcfcfc" style="padding-right:20px;">รับคนพิการเข้าทำงานตาม ม.33</td>
                              <td><strong><span id="summary_33_dummy"></span></strong> คน || 
                              
                              <a href="#" onClick="fireMyPopup('le_number_only_popup',500,250); return false;">แก้ไขจำนวนคนพิการที่ได้รับเข้าทำงาน</a></td>
                            </tr>
                            
                            <tr>
                              <td bgcolor="#fcfcfc" style="padding-right:20px;">ให้สัมปทานฯ ตาม ม.35</td>
                              <td><strong><span id="summary_35_dummy"></span></strong> คน
                              
                              || <a href="#" onClick="fireMyPopup('curator_number_only_popup',500,250); return false;">แก้ไขจำนวนการให้สัมปทานฯ</a>
                              
                              </td>
                            </tr>
                            
                            <tr>
                              <td bgcolor="#fcfcfc" style="padding-right:20px;">ต้องจ่ายเงินแทนการรับคนพิการ</td>
                              <td><strong><span id="summary_34_dummy"></span></strong> คน</td>
                            </tr>
                      
                      
                      		<script>
								//yoes 20151118 -- retroactively change span value
								$("#summary_33_dummy").html("<?php echo formatEmployee(default_value($hire_numofemp,"0"));?>");
								$("#summary_35_dummy").html("<?php echo $curator_user;?>");
								$("#summary_34_dummy").html("<?php echo formatEmployee(default_value($extra_emp,"0"));?>");
								</script>
                      
                        
                      
                       <tr>
                              <td bgcolor="#fcfcfc" style="padding-right:20px;" colspan="2"><strong style="color:#006600;">
                              
                              มาตรา 34 ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ
                              
                              </strong></td>
                              
                            </tr>
                      
                      
                      
                        <tr>
                          
                          <td colspan="5">
                            
                            
                           
                           <span id="calculated_34_table_duped">
                           
                           </span> 
                           
                           
                           </td>
                        </tr>
                        
                        <script>
						$('#calculated_34_table_duped').html($('#calculated_34_table').html());
						</script>
                        
                        <form  method="post" action="scrp_update_verified_flag.php">
                        <tr>
                          <td colspan="2">
                          
                          <div align="center">
                              <input type="submit" name="button" id="button" value="ตรวจสอบข้อมูลแล้ว เพื่อการจ่ายเงิน" <?php if($is_read_only){?>disabled="disabled"<?php }?>>
                              
                              <input name="LID" type="hidden" value="<?php echo $lawful_values["LID"];?>" />
                              <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                              <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                              
                          </div>
                          
                          <hr />
                          
                          <?php if(
							$sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && !$case_closed
							 && !$is_blank_lawful && 1==1 //&& $this_lawful_year <= 2015 
							 && 1==0 //yoes 20221027 -- close this again?
							 ){
								   
								   
								?>
								
							   
                               <div align="center">
                               
								   <a href="add_invoice.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;" target="_blank">+ พิมพ์ใบชำระเงินสำหรับระบบใบเสร็จออนไลน์</a>
                               
                               </div>
							   <?php }?>
                          
                          </td>
                          </tr>
                      </form>
      </table>
</div>



<div id="le_number_only_popup" style=" position:absolute; padding:3px; background-color:#006699; width: 500px; display:none; " >

	
	<table  bgcolor="#FFFFFF" width="500" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
    
    	<form  method="post" action="scrp_update_lawful_employees_number_only.php"><!--- curator information just get posted into this page-->
    	<tr>
            <td colspan="2">
                    <div style="font-weight: bold;color:#006600;  " >
                    ปรับปรุงจำนวนคนพิการที่ได้รับเข้าทำงานตามมาตรา 33
                    </div> 
				</td>
        </tr>
    
    	<tr>
        	<td>
             จำนวนคนพิการที่ได้รับเข้าทำงานตามมาตรา 33:
            </td>
            <td>
            <input name="update_employees" id="update_employees" style="width:50px" type="text" value="<?php echo formatEmployee($hire_numofemp_origin); //yoes 20151118 -- always use original values ?>" onchange="addEmployeeCommas('update_employees');"  /> คน
            
            <br />
            <span style="font-size: 11px;">
            
            <?php 
				
				//get dummy lawful employees
				$sql = "
						select 
							count(*) 
						from 
							lawful_employees 
						where
							le_cid = '".doCleanOutput($output_values["CID"])."'
							and
							le_year = '".$this_year."'
							and
							le_is_dummy_row = 1
						";
						
					$hire_numofemp_dummy = getFirstItem($sql);
			
			?>
            
            มีรายละเอียดการจ้างงานแล้ว <?php echo $hire_numofemp_origin-$hire_numofemp_dummy ;?> คน
            <br />
            ยังไม่มีรายละเอียด <?php echo $hire_numofemp_dummy ;?>  คน
            </span>
            </td>
        </tr>
        
        <tr>
            <td colspan="2">
            	<div align="center">
                   <input name="" type="submit" value="ตรวจสอบข้อมูลแล้ว เพื่อการจ่ายเงิน" <?php if($is_read_only){?>disabled="disabled"<?php }?>/>
                   <input name="" type="button" onClick="fadeOutMyPopup('le_number_only_popup'); return false;" value="ปิดหน้าต่าง"/>
                   
                  	<input name="LID" type="hidden" value="<?php echo $lawful_values["LID"];?>" />
                    
                    <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                    <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                  
                </div>
			</td>
        </tr>
        
        
        </form>
        
        <?php if(1==0){?>
        
        <form  method="post" action="scrp_update_lawful_employees_number_only_delete.php"><!--- curator information just get posted into this page-->
         <tr>
            <td colspan="2">
            	 <div align="center">
                    ต้องการลบข้อมูลคนพิการตามมาตรา 33 ที่ยังไม่ได้ใส่รายละเอียด  <input name="" type="submit" value="คลิกที่นี่" onclick="return confirm('ต้องการลบข้อมูล ข้อมูลคนพิการตามมาตรา 33 ที่ยังไม่ได้ใส่รายละเอียด?');"/>
                    <input name="LID" type="hidden" value="<?php echo $lawful_values["LID"];?>" />
                    
                    <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                    <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                </div>
			</td>
        </tr>
        
         </form>
         
         <?php }?>
    	
    </table>
    
   


</div>    


<div id="curator_number_only_popup" style=" position:absolute; padding:3px; background-color:#006699; width: 500px; display:none; " >

	
	<table  bgcolor="#FFFFFF" width="500" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
    
    	<form  method="post" action="scrp_update_curator_number_only.php"><!--- curator information just get posted into this page-->
    	<tr>
            <td colspan="2">
                    <div style="font-weight: bold;color:#006600;  " >
                    ปรับปรุงจำนวนการให้สัมปทานมาตรา 35
                    </div> 
				</td>
        </tr>
    
    	<tr>
        	<td>
             จำนวนผู้ใช้สิทธิมาตรา 35:
            </td>
            <td>
            <input name="update_curator" id="update_curator" style="width:50px" type="text" value="<?php echo formatEmployee($curator_user); //yoes 20151118 -- always use original values ?>" onchange="addEmployeeCommas('update_curator');"  /> คน
            
            
            <span style="font-size: 11px;">
            
            <?php 
				
				//get dummy lawful employees
				$sql = "
						select 
							count(*) 
						from 
							curator 
						where
							curator_lid = '".$lawful_values["LID"]."'
							and
							curator_is_dummy_row = 1
						";
						
					$curator_dummy = getFirstItem($sql);
			
			?>
            <br />
            มีรายละเอียดการใช้สิทธิมาตรา 35 แล้ว <?php echo $curator_user-$curator_dummy ;?> คน
            <br />
            ยังไม่มีรายละเอียด <?php echo $curator_dummy ;?>  คน
            </span>
            
            
            </td>
        </tr>
        
        <tr>
            <td colspan="2">
            	<div align="center">
                
                
                   <input name="" type="submit" value="ตรวจสอบข้อมูลแล้ว เพื่อการจ่ายเงิน" <?php if($is_read_only){?>disabled="disabled"<?php }?>/>
                   
                   
                   <input name="" type="button" onClick="fadeOutMyPopup('curator_number_only_popup'); return false;" value="ปิดหน้าต่าง"/>
                   
                  	<input name="LID" type="hidden" value="<?php echo $lawful_values["LID"];?>" />
                    
                    <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                    <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                  
                </div>
			</td>
        </tr>
        
        
        </form>
        
    	
    </table>
    
   


</div>    