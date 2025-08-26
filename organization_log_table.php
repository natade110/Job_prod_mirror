<table id="<?php echo $table_id;?>" border="1"  cellspacing="0" cellpadding="5" style="border-collapse:collapse; " width="100%">
                      
        <?php if(1==0){?>
            <tr bgcolor="#9C9A9C" align="center" >
                
                <td >
                    <div align="center"><span class="column_header">ข้อมูลก่อนการแก้ไข ณ วันที่</span></div>                             </td>
                
		  <td >
                    <div align="center"><span class="column_header">ข้อมูลที่ถูกแก้ไข</span> </div></td>
          <td>
                    <div align="center"><span class="column_header">ผู้ที่แก้ไข</span></div></td>
          <td><div align="center"><span class="column_header">source</span></div></td>
         
            </tr>
            <?php }?>
            
            
          
                  	
                
           
            
            <?php
            
            
              //yoes 20160113 --- first try to get lawfulness log...?
       
           $sql = $log_sql;
           
           //echo $sql;
           
           $result = mysql_query($sql);
           
		   //"last" variable
		   $last_row = array();
                                
            //main loop
			
			$count_row = 0;
			
             while($post_row = mysql_fetch_array($result)){
           
        
				$count_row++;
            ?>     
            <?php if(1==0){?>
            <tr bgcolor="#ffffff" align="center" >
                
                <td >
                  <div align="center">
                 
                    <a href="#" onclick="$('#<?php echo $table_id.$post_row["log_id"];?>').toggle(); return false;">
                    <?php echo $post_row[log_date];?> 
                    </a>
                    
                  
                  </div>
                 </td>
                
              <td >
                    <div align="center">
                      <?php 
                        echo $post_row["log_type"];
                        
                        ?>
                    </div>
             </td>
                 <td>
                    <div align="center">
                        <?php 
                            
                            echo getFirstItem("select user_name from users where user_id = '".$post_row["log_by"]."'");
                            
                            echo "(".str_replace("-----","",$post_row["log_ip"]).")"; //--- IP
                            
                            ?>
                        
                    </div>
                    </td>
                    <td>
                    <div align="center"><?php echo doCleanOutput($post_row["log_source"]);?></div>
                    </td>
                
            </tr>
            <?php }?>
            
            <tr>
            	 <td colspan="4" >
                 	  ข้อมูลวันที่
						<strong><?php echo $post_row[log_date];?> </strong>
                        
                        
                        แก้ไขโดย <strong><?php 
                            
                            echo getFirstItem("select user_name from users where user_id = '".$post_row["log_by"]."'");
                            
                            echo "(".str_replace("-----","",$post_row["log_ip"]).")"; //--- IP
                            
                            ?></strong>
                            
                            <?php if(1==1){?>
                      		การกระทำ: 
                            <strong><?php echo doGetLogSourceName($post_row["log_source"]);?></strong>
                            <?php }?>
                 
                 </td>
           </tr>
            
            
              <?php 
              
                //for each row also generate detail rows
                if( $post_row["log_type"] == "ข้อมูลสถานประกอบการ"){
                  
                  
                  $post_row2 = getFirstRow("
                    select 
                        * 
                    from
                        company_full_log
                    where
                        log_id = '". $post_row["log_id"]."'
                        
                    ");
					
					
					if($last_row){
						
						//print_r($last_row);
						$name_array = array_keys($post_row2);
						
						$style_array = array();
						
						//compare which data in this row is different from last row
						for($ii = 0; $ii < count($post_row2); $ii++){
							
							if($post_row2[$name_array[$ii]] != $last_row[$name_array[$ii]]){
								//$post_row2[$name_array[$ii]] = "<font color='green'>".$post_row2[$name_array[$ii]]."</font>";
								//$post_row2[$name_array[$ii]] = $post_row2[$name_array[$ii]];								
								$style_array[$name_array[$ii]] = 'style="color:red; font-weight:bold;"';
								
							}else{
								$style_array[$name_array[$ii]] = '';
							}
							
						}
					}
					
				
                    
                  
                  ?>
                  
            <tr id="<?php echo $table_id.$post_row["log_id"];?>" bgcolor="#ffffff" align="center" <?php if(1==0){?>style="display:none;"<?php }?>>
            <td colspan="4" >
                    
               
                 <div align="center">
                 <table>
                    
                     <tr bgcolor="#efefef">
                         
                          <td><div align="center">เลขทะเบียนนายจ้าง</div></td>
                          <td><div align="center">เลขที่สาขา</div></td>
                          <td><div align="center">เลขที่ประจำตัวผู้เสียภาษีอากร</div></td>                                      
                          <td><div align="center">ประเภทธุรกิจ</div></td>
                          <td><div align="center">ประเภทกิจการ</div></td>
                          
                          <td><div align="center">ชื่อสถานประกอบการ</div></td>
                          <td><div align="center">ชื่อสถานประกอบการ (อังกฤษ)</div></td>                                      
                          <td><div align="center">จำนวนลูกจ้าง</div></td>
                          <td><div align="center">สถานะของกิจการ</div></td>                                      
                          
                          </tr>
                         
                         <tr>
                              <td <?php echo $style_array["CompanyCode"]?>><?php echo doCleanOutput($post_row2["CompanyCode"]);?></td>
                              <td <?php echo $style_array["BranchCode"]?>><?php echo doCleanOutput($post_row2["BranchCode"]);?></td>
                              <td <?php echo $style_array["TaxID"]?>><?php echo doCleanOutput($post_row2["TaxID"]);?></td>
                              <td <?php echo $style_array["CompanyTypeCode"]?>><?php 
                              
                                echo getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".$post_row2["CompanyTypeCode"]."'")
                                ?></td>
                              <td <?php echo $style_array["BusinessTypeCode"]?>><?php 
                              
                                echo getFirstItem("select BusinessTypeName from businesstype where BusinessTypeCode = '".$post_row2["BusinessTypeCode"]."'")
                                ?></td>
                              
                               <td <?php echo $style_array["CompanyNameThai"]?>><?php echo doCleanOutput($post_row2["CompanyNameThai"]);?></td>
                              <td <?php echo $style_array["CompanyNameEng"]?>><?php echo doCleanOutput($post_row2["CompanyNameEng"]);?></td>
                              <td <?php echo $style_array["Employees"]?>><?php echo doCleanOutput($post_row2["Employees"]);?></td>
                              <td <?php echo $style_array["Status"]?>><?php echo getCompanyStatusText($post_row2["Status"]);?></td>
                           
                            </tr>
                            
                            
                          <tr bgcolor="#efefef">
                          
                                 <td ><div align="center">ที่อยู่</div></td>
                          
                                  <td><div align="center">โทรศัพท์</div></td>
                                  <td><div align="center">email</div></td>
                                  <td><div align="center">เวปไซต์</div></td>                                      
                                    <td><div align="center">ชื่อผู้ติดต่อ 1</div></td>
                                    <td><div align="center">เบอร์โทรศัพท์</div></td>
                                    
                                    <td><div align="center">ตำแหน่ง</div></td>
                                    <td><div align="center">อีเมล์</div></td>
                                    <td><div align="center">ชื่อผู้ติดต่อ 2</div></td>
                                    <td><div align="center">เบอร์โทรศัพท์</div></td>
                                    <td><div align="center">ตำแหน่ง</div></td>
                                    
                                    <td><div align="center">อีเมล์</div></td>
                                  
                             </tr>
                                 
                            
                            
                            
                          </tr>   
                              
                           
                              <td <?php echo $style_array["address"]?>><?php echo doCleanOutput(getAddressText($post_row2));?></td>
                              
                              <td <?php echo $style_array["Telephone"]?>><?php echo doCleanOutput($post_row2["Telephone"]);?></td>
                              <td <?php echo $style_array["email"]?>><?php echo doCleanOutput($post_row2["email"]);?></td>
                              <td <?php echo $style_array["org_website"]?>><?php echo doCleanOutput($post_row2["org_website"]);?></td>
                              <td <?php echo $style_array["ContactPerson1"]?>><?php echo doCleanOutput($post_row2["ContactPerson1"]);?></td>
                              <td <?php echo $style_array["ContactPhone1"]?>><?php echo doCleanOutput($post_row2["ContactPhone1"]);?></td>
                              <td <?php echo $style_array["ContactPosition1"]?>><?php echo doCleanOutput($post_row2["ContactPosition1"]);?></td>
                              <td <?php echo $style_array["ContactEmail1"]?>><?php echo doCleanOutput($post_row2["ContactEmail1"]);?></td>
                              
                              <td <?php echo $style_array["ContactPerson2"]?>><?php echo doCleanOutput($post_row2["ContactPerson2"]);?></td>
                              <td <?php echo $style_array["ContactPhone2"]?>><?php echo doCleanOutput($post_row2["ContactPhone2"]);?></td>
                              <td <?php echo $style_array["ContactPosition2"]?>><?php echo doCleanOutput($post_row2["ContactPosition2"]);?></td>
                              <td <?php echo $style_array["ContactEmail2"]?>><?php echo doCleanOutput($post_row2["ContactEmail2"]);?></td>
                              
                          </tr>
                         
                  </table>
                  </div>
                </td>
            </tr>
                     
            <?php 
			
				$last_row = $post_row2;
			}?>
            
            
              
              
              <?php 
              
			  	
			  
                //for each row also generate detail rows
                if( $post_row["log_type"] == "มาตรา 33 จ้างคนพิการเข้าทำงาน"){
                  
                  
                    
					//yoes 20160209
					if($post_row["log_id"] === 0){
					
						$post_row2 = getFirstRow("
						select 
							* 
						from
							lawful_employees
						where
							le_cid = $the_id 
						
							
						");
						
						
					
					
					}else{
						
						 $post_row2 = getFirstRow("
							select 
								* 
							from
								lawful_employees_full_log
							where
								log_id = '". $post_row["log_id"]."'
								
							");	
					}
					
					if($last_row && $last_row[le_id] == $post_row2[le_id]){
						
						//print_r($last_row);
						$name_array = array_keys($post_row2);
						
						$style_array = array();
						
						//compare which data in this row is different from last row
						for($ii = 0; $ii < count($post_row2); $ii++){
							
							if($post_row2[$name_array[$ii]] != $last_row[$name_array[$ii]]){
								//$post_row2[$name_array[$ii]] = "<font color='green'>".$post_row2[$name_array[$ii]]."</font>";
								//$post_row2[$name_array[$ii]] = $post_row2[$name_array[$ii]];								
								$style_array[$name_array[$ii]] = 'style="color:red; font-weight:bold;"';
								
							}else{
								$style_array[$name_array[$ii]] = '';
							}
							
						}
					}else{
						$last_row = array();	
						$style_array = array();
					}
                  
                  ?>
                  
            <tr id="<?php echo $table_id.$post_row["log_id"];?>" bgcolor="#ffffff" align="center" <?php if(1==0){?>style="display:none;"<?php }?> >
            <td colspan="4" >
                    
                 <div align="center">
                 <table>
                     <tr bgcolor="#efefef">
                          
                          <td><div align="center">ROWID</div></td>
                          <td><div align="center">ชื่อ</div></td>
                          <td><div align="center">เพศ</div></td>
                          <td><div align="center">อายุ</div></td>
                          <td><div align="center">เลขที่บัตรประชาชน</div></td>
                          <td width="140px"><div align="center">ลักษณะความพิการ</div></td>
                          <td><div align="center">เริ่มบรรจุงาน </div></td>
                          <td><div align="center">ค่าจ้าง </div></td>
                          <td ><div align="center">ตำแหน่งงาน</div></td>
                          <td ><div align="center">การศึกษา</div></td>
                         
                        </tr>
                         
                         <tr>
                             
                             <td valign="top" <?php echo $style_array["le_id"]?> ><?php echo $post_row2["le_id"];?></td>
                              <td valign="top" <?php echo $style_array["le_name"]?> ><?php echo doCleanOutput($post_row2["le_name"]);?></td>
                             <td valign="top" <?php echo $style_array["le_gender"]?>><?php echo formatGender($post_row2["le_gender"]);?></td>
                             <td valign="top" <?php echo $style_array["le_age"]?>><?php echo doCleanOutput($post_row2["le_age"]);?></td>
                              <td valign="top" <?php echo $style_array["le_code"]?>>
                              <?php echo doCleanOutput($post_row2["le_code"]);?>
                              
                              
                              </td>
                              <td valign="top" <?php echo $style_array["le_disable_desc"]?>><?php echo doCleanOutput($post_row2["le_disable_desc"]);?></td>
                              <td valign="top" <?php echo $style_array["le_start_date"]?>><?php echo formatDateThai($post_row2["le_start_date"],0);?></td>
                              
                              <td valign="top" <?php echo $style_array["le_wage"]?>><div align="right">
                              
                              <?php echo formatNumber($post_row2["le_wage"]);?>
                              
                              
                              <?php echo getWageUnit($post_row2["le_wage_unit"]);?>
                              
                              </div></td>
                              
                               
                              <td valign="top" <?php echo $style_array["le_position"]?>><?php 
                              
                                if(is_numeric($post_row2["le_position"])){
                                    echo formatPositionGroup($post_row2["le_position"]);									  
                                }else{
                                    echo doCleanInput($post_row2["le_position"]);
                                }
                                
                                ?></td>
                              
                              <td valign="top" <?php echo $style_array["le_education"]?>><?php echo formatEducationLevel(doCleanOutput($post_row2["le_education"]));?></td>
                             
                             
                              
                            </tr>
                         
                  </table>
                  </div>
                </td>
            </tr>
                     
            <?php 
				
				 $last_row = $post_row2;
			
			}?>
            
            
             <?php 
              
                //for each row also generate detail rows
                if( $post_row["log_type"] == "การปฏิบัติตามกฎหมาย"){
                  
                  
                  $post_row2 = getFirstRow("
                    select 
                        * 
                    from
                        lawfulness_full_log
                    where
                        log_id = '". $post_row["log_id"]."'
                        
                    ");
					
					
				if($last_row){
										
					//print_r($last_row);
					$name_array = array_keys($post_row2);
					
					$style_array = array();
					
					//compare which data in this row is different from last row
					for($ii = 0; $ii < count($post_row2); $ii++){
						
						if($post_row2[$name_array[$ii]] != $last_row[$name_array[$ii]]){
							//$post_row2[$name_array[$ii]] = "<font color='green'>".$post_row2[$name_array[$ii]]."</font>";
							//$post_row2[$name_array[$ii]] = $post_row2[$name_array[$ii]];								
							$style_array[$name_array[$ii]] = 'style="color:red; font-weight:bold;"';
							
						}else{
							$style_array[$name_array[$ii]] = '';
						}
						
					}
				}

                    
                  
                  ?>
                  
            <tr id="<?php echo $table_id.$post_row["log_id"];?>" bgcolor="#ffffff" align="center" <?php if(1==0){?>style="display:none;"<?php }?>>
            <td colspan="4" >
                    
                 <div align="center">
                 <table>
                     <tr bgcolor="#efefef">
                          
                          <td><div align="center">ปี</div></td>
                          <td><div align="center">การปฏิบัติตามกฏหมาย</div></td>
                          <td><div align="center">จำนวนลูกจ้าง</div></td>
                          <td><div align="center">มีการทำมาตรา 33</div></td>
                          <td><div align="center">มีการทำมาตรา 34</div></td>
                           <td><div align="center">มีการทำมาตรา 35</div></td>
                         
                         
                        </tr>
                         
                         <tr>
                             
                              <td  valign="top" <?php echo $style_array["Year"]?>><?php echo doCleanOutput($post_row2["Year"]+543);?></td>
                             <td valign="top" <?php echo $style_array["LawfulStatus"]?>><?php echo getLawfulText($post_row2["LawfulStatus"]);?></td>
                             <td  valign="top" <?php echo $style_array["Employees"]?>><?php echo doCleanOutput($post_row2["Employees"]);?></td>
                             <td valign="top" <?php echo $style_array["Hire_status"]?>><?php if($post_row2["Hire_status"]){echo "มี";}else{echo "ไม่มี";}?></td>
                             <td valign="top" <?php echo $style_array["pay_status"]?>><?php if($post_row2["pay_status"]){echo "มี";}else{echo "ไม่มี";}?></td>
                             <td valign="top" <?php echo $style_array["Conc_status"]?>><?php if($post_row2["Conc_status"]){echo "มี";}else{echo "ไม่มี";}?></td>
                            
                             
                            </tr>
                         
                  </table>
                  </div>
                </td>
            </tr>
                     
            <?php $last_row = $post_row2;
			
			}?>
            
            
            
            <?php 
              
                //for each row also generate detail rows
                if( $post_row["log_type"] == "มาตรา 35 ให้สัมปทานฯ"){
                  
                  
                  $post_row2 = getFirstRow("
                    select 
                        * 
                    from
                        curator_full_log
                    where
                        log_id = '". $post_row["log_id"]."'
                        
                    ");
					
					if($last_row && $last_row[curator_id] == $post_row2[curator_id]){
						
						//print_r($last_row);
						$name_array = array_keys($post_row2);
						
						$style_array = array();
						
						//compare which data in this row is different from last row
						for($ii = 0; $ii < count($post_row2); $ii++){
							
							if($post_row2[$name_array[$ii]] != $last_row[$name_array[$ii]]){
								//$post_row2[$name_array[$ii]] = "<font color='green'>".$post_row2[$name_array[$ii]]."</font>";
								//$post_row2[$name_array[$ii]] = $post_row2[$name_array[$ii]];								
								$style_array[$name_array[$ii]] = 'style="color:red; font-weight:bold;"';
								
							}else{
								$style_array[$name_array[$ii]] = '';
							}
							
						}
					}else{
						$last_row = array();	
						$style_array = array();
					}
                    
                  
                  ?>
                  
            <tr id="<?php echo $table_id.$post_row["log_id"];?>" bgcolor="#ffffff" align="center" <?php if(1==0){?>style="display:none;"<?php }?>>
            <td colspan="4" >
                    
                 <div align="center">
                 <table>
                    
                     <tr bgcolor="#efefef">
                         
                         	<td><div align="center">ROWID</div></td>
                          <td><div align="center">ชื่อ-นามสกุล</div></td>
                          <td><div align="center">เพศ</div></td>
                          <td><div align="center">อายุ</div></td>
                          <td><div align="center">เลขที่บัตรประชาชน</div></td>
                          <td><div align="center">ผู้ใช้สิทธิเป็น</div></td>
                          
                          <td><div align="center">วันเริ่มต้นสัญญา</div></td>
                          <td><div align="center">วันสิ้นสุดสัญญา</div></td>
                          <td><div align="center">ระยะเวลา</div></td>
                          <td><div align="center">กิจกรรม</div></td>
                          <td><div align="center">มูลค่า (บาท)</div></td>
                          <td><div align="center">รายละเอียด</div></td>
                          
                     </tr>
                         
                         <tr>
                         	<td <?php echo $style_array["curator_id"]?>><?php echo doCleanOutput($post_row2["curator_id"]);?></td>
                              <td <?php echo $style_array["curator_name"]?>><?php echo doCleanOutput($post_row2["curator_name"]);?></td>
                              <td <?php echo $style_array["curator_gender"]?>><?php echo formatGender($post_row2["curator_gender"]);?></td>
                              <td <?php echo $style_array["curator_age"]?>><?php echo doCleanOutput($post_row2["curator_age"]);?></td>
                              <td <?php echo $style_array["curator_idcard"]?>><?php echo doCleanOutput($post_row2["curator_idcard"]);?></td>
                              <td <?php echo $style_array["curator_disable_desc"]?>>
                              <?php if($post_row["curator_is_disable"] == 1){                                            
                                    echo "<font color='green'>คนพิการ : " . $post_row2["curator_disable_desc"]. "</font>";
                                                                                }else{                                            
                                    echo "<font color='blue'>ผู้ดูแลคนพิกา</font>ร";                                                
                                }?>
                              </td>
                                <td <?php echo $style_array["curator_start_date"]?>><?php echo formatDateThai($post_row2["curator_start_date"]);?></td>
                              <td <?php echo $style_array["curator_end_date"]?>><?php echo formatDateThai($post_row2["curator_end_date"]);?></td>
                             
                                <td <?php echo $style_array["curator_start_date"]?>><?php 
                                
                                echo number_format(dateDiffTs(strtotime($post_row2["curator_start_date"]), strtotime($post_row2["curator_end_date"])),0);
                                
                                ?> วัน</td>
                                
                                
                                 <td <?php echo $style_array["curator_event"]?>><?php echo doCleanOutput($post_row2["curator_event"]);?></td>
                   
                                 <td <?php echo $style_array["curator_value"]?>><div align="right"><?php echo formatNumber($post_row2["curator_value"]);?></div></td>
                                 
                                   <td <?php echo $style_array["curator_event_desc"]?>><?php 
                    
                                    echo doCleanOutput($post_row2["curator_event_desc"]);
                                    ?>
                                </td>
                             
                          </tr>
                         
                  </table>
                  </div>
                </td>
            </tr>
                     
            <?php $last_row = $post_row2; 
			}?>
              
             
           
            
            <?php } //end loop to generate rows?>
            
            <?php if(!$count_row){?>
            
            <tr>
            	<td colspan="4">
            		<div align="center" style="padding: 10px; font-weight: bold;">
                    	ไม่พบข้อมูล log
                    </div>
            	</td>
            </tr>
            <?php }?>
            
      </table>