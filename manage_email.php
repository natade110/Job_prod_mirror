<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($sess_accesslevel != 1 ){ 
	
		//this function is admin-only
		header("location: index.php");
		exit();
	}
?>



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >ระบบแจ้งเตือนสถานะสถานประกอบการ (Email Scheduler) </h2>
                   
                    
                   

                    <strong>ระบบแจ้งเตือนสถานะสถานประกอบการ (Email Scheduler) </strong>
                    
                  
                    
                  	
                    
                    <form method="post">
                   <table style=" padding:10px 0 0px 0;">
                    
                    <tr>
                    	  <td bgcolor="#efefef">ประจำปี:</td>
                    	  <td><?php include "ddl_year.php";?></td>
                    	  <td >&nbsp;</td>
                    	  <td>&nbsp;</td>
                  	  </tr>
                    
                    
                     <tr>
                        <td bgcolor="#efefef">จังหวัด: </td>
                        <td colspan="3"><?php include "ddl_org_province.php"?></td>
                       
                      </tr>
                    
                    	<tr>
                    	  <td bgcolor="#efefef">ประเภทของ email แจ้งเตือน: </td>
                          <td colspan="3">
                          
                          <select name="alert_type" id="alert_type" >
                            <option value="" selected="selected">-- เลือก --</option>
                            <option value="2" <?php if($_POST["alert_type"] == "2"){echo "selected='selected'";}?>>ปฏิบัติตามกฏหมายแล้ว</option>
                            <option value="0" <?php if($_POST["alert_type"] == "0"){echo "selected='selected'";}?>>ไม่ทำตามกฏหมาย</option>
                            <option value="3" <?php if($_POST["alert_type"] == "3"){echo "selected='selected'";}?>>ปฏิบัติตามกฏหมายแต่ไม่ครบอัตราส่วน</option>
                            <option value="1" <?php if($_POST["alert_type"] == "1"){echo "selected='selected'";}?>>พบข้อมูลการใช้สิทธิซ้ำซ้อน</option>
							<option value="6" <?php if($_POST["alert_type"] == "6"){echo "selected='selected'";}?>>การแจ้งแนบไฟล์ สปส 1-10 ส่วนที่ 2</option>
							<option value="6" <?php if($_POST["alert_type"] == "7"){echo "selected='selected'";}?>>การแจ้งแนบเอกสารสมัคร e-service ไม่ครบถ้วน</option>
                            
                            
                        </select>
							</td>
                            
                        
                   	  </tr>
                    	<tr>
                    	  <td bgcolor="#efefef">สถานะการส่ง email </td>
                    	  <td colspan="3">  
                          <select name="email_status" id="email_status" >
                            <option value="" selected="selected">แสดงรายชื่อทั้งหมด</option>
                            <option value="1" <?php if($_POST["email_status"] == "1"){echo "selected='selected'";}?>>แสดงรายชื่อที่ยังไม่เคยได้รับ email เท่านั้น</option>
                            <option value="2" <?php if($_POST["email_status"] == "2"){echo "selected='selected'";}?>>แสดงรายชื่อที่เคยได้รับ email แล้ว</option>
                           
                            
                        </select></td>
                  	  </tr>
                      
                      
                      
                      
                        
                        
                    	<tr>
                    	  <td colspan="6" align="right">
                          
                           
                            <input type="submit" value="แสดง" name="mini_search"/>
                            
                            
                            |
                            
                            email รออยู่ใน queue การส่ง <a href="mail_queue.php"><?php echo getFirstItem("select count(*) from email_log where email_status = 0 ");?></a> ฉบับ
                            
                            
                          <hr />
                          
                          </td>
                   	  </tr>
                      
                      
                    </table>
                    </form>
                                        
                                        
                    <?php 
					
						//try building a list
					
						$ddl_year = $_POST[ddl_year]*1;
						$alert_type = doCleanInput($_POST[alert_type]);
						
						if($_POST[Province]){
						
							$the_province = doCleanInput($_POST[Province]);	
							
							$province_sql = "and a.Province = '$the_province'";
						}
						
						
						if($_POST[email_status] == "1"){
						
							//status for - never sent mail only
							$email_status_sql = " and c.user_id not in (
												
												
												select
													user_id
												from
													email_log
												where
													email_year = '$ddl_year'
													and
													email_status = 1
													and
													email_type = '$alert_type'
							
							
												)";
						}elseif($_POST[email_status] == "2"){
						
							//status for - sent mail only
							$email_status_sql = " and c.user_id in (
												
												
												select
													user_id
												from
													email_log
												where
													email_year = '$ddl_year'
													and
													email_status = 1
													and
													email_type = '$alert_type'
							
							
												)";
						}
						
						
						
						if($alert_type == "0"){
							
						
							$sql = "
							
								select
									*
									, b.employees as lawfulEmployees
								from
									company a
										join
											lawfulness b
												on a.cid = b.cid
										left outer join
											users c
												on a.cid = c.user_meta
													and c.AccessLevel = 4
													and c.user_enabled = 1
													
										
										
								where
									year = '$ddl_year'
									and
									lawfulStatus = 0									
									and
									(
										trim(c.user_email) like '%@%'
									)
									
									$province_sql
									
									$email_status_sql
							
							
							";
							
							//echo $sql;
							
						}elseif($alert_type == "1"){
							
							
							$sql = "
							
								select
									*
									, b.employees as lawfulEmployees
								from
									company a
										join
											lawfulness b
												on a.cid = b.cid
										left outer join
											users c
												on a.cid = c.user_meta
													and c.AccessLevel = 4
													and c.user_enabled = 1
								where
									year = '$ddl_year'									
									and
									(
										trim(c.user_email) like '%@%'
									)
									and a.cid in (
									
										SELECT le_cid FROM `lawful_employees` where le_year = '$ddl_year' group by le_code having count(le_code) > 1 
									
									)
									
									$province_sql
									
									$email_status_sql
							
							
							";
							
							
						
							
						}elseif($alert_type == "2"){
							
							
							$sql = "
							
								select
									*
									, b.employees as lawfulEmployees
								from
									company a
										join
											lawfulness b
												on a.cid = b.cid
										left outer join
											users c
												on a.cid = c.user_meta
													and c.AccessLevel = 4
													and c.user_enabled = 1
								where
									year = '$ddl_year'
									and
									lawfulStatus = 1
									and
									(
										trim(c.user_email) like '%@%'
									)
									
									$province_sql
									
									$email_status_sql
							
							
							";
							
						}elseif($alert_type == "3"){
							
							
							$sql = "
							
								select
									*
									, b.employees as lawfulEmployees
								from
									company a
										join
											lawfulness b
												on a.cid = b.cid
										left outer join
											users c
												on a.cid = c.user_meta
													and c.AccessLevel = 4
													and c.user_enabled = 1
								where
									year = '$ddl_year'
									and
									lawfulStatus = 2
									and
									(
										trim(c.user_email) like '%@%'
									)
									
									$province_sql
									
									$email_status_sql
							
							
							";
							
							
						}elseif($alert_type == "6"){
							
							
							$sql = "
							
								select
									*
								from
									company a
										join
											lawfulness_company b
												on 
												a.cid = b.cid
												and
												b.year = '$ddl_year'
												and
												b.lawful_submitted = 2
												
										join
											users c
												on
												a.cid = c.user_meta
												and
												c.AccessLevel = 4
												and
												user_enabled = 1
												
								where 
									
									1 = 1 
								
									$province_sql
										
									$email_status_sql
							
							
							";
							
							
						}
						
						
						//echo $sql; exit();
						
						$the_result = mysql_query($sql);
					
					
					
					?>
                                        
                    <?php 
					
						if($the_result){
					
							echo "พบผู้ติดต่อ: ".mysql_num_rows($the_result) . " ราย";					
						
						?>
                        
                        
                        <form method="post">
                        	 <input 
                             	type="submit" 
                                value="ส่งเมล์แจ้งเตือน '<?php echo getMailAlertText($alert_type);?>'  จำนวน <?php echo mysql_num_rows($the_result)?> ฉบับ" name="do_send_mail"
                                
                                onclick="return confirm('ส่งเมล์แจ้งเตือน <?php echo getMailAlertText($alert_type);?> - email จำนวน <?php echo mysql_num_rows($the_result)?> ฉบับจะถูกนำไปเพิ่มใน email queue เพื่อส่งออกต่อไป?');"
                                
                                />
                                
                                <?php if(1==1){?>
                                <input name="ddl_year" type="hidden" value="<?php echo $_POST[ddl_year];?>" />
                                <input name="Province" type="hidden" value="<?php echo $_POST[Province];?>" />
                                <input name="alert_type" type="hidden" value="<?php echo $_POST[alert_type];?>" />
                                <input name="email_status" type="hidden" value="<?php echo $_POST[email_status];?>" />
                               <?php }?>
                               
                        </form>
                        
                        <?php
					
						}
						
						
					?>  
                    
                                  
                    <table cellpadding="3" width="800" bgcolor="#FFFFFF" border="1" style="border-collapse: collapse;">
                    
                   		 <tr>
                        
                        
                        	<td style="background-color:#efefef">
                            	เลขที่บัญชีนายจ้าง                            </td>
                        	<td style="background-color:#efefef">
                            	ชื่อ นายจ้างหรือ สถานประกอบการ                      </td>
                            <td style="background-color:#efefef">
                            	จำนวนลูกจ้างรวมทุกสาขา                      </td>
                            
                            <td style="background-color:#efefef">
                            สถานะ
                            </td>
                             <td style="background-color:#efefef">
                            ชื่อผู้ติดต่อ
                            </td>
                            
                             <td style="background-color:#efefef">
                            สถานะการส่ง email
                            </td>
               		  </tr>
                         
                         
                          <?php 
												
							while($the_row = mysql_fetch_array($the_result)){
						
						?>
                         
                   		 <tr>
                   		   <td >
						   
                                <a href="organization.php?id=<?php echo doCleanOutput($the_row["CID"]);?>&year=<?php echo $ddl_year;?>" target="_blank">
                                    <?php echo $the_row[CompanyCode]?>
                                </a>
                                
                           </td>
                   		   <td ><?php echo doCleanOutput($the_row["CompanyNameThai"]);?></td>
                   		   <td ><?php echo number_format($the_row[lawfulEmployees],0)?></td>
                   		   <td ><?php echo getLawfulImage($the_row[LawfulStatus])?></td>
                           <td >
						   
                           <a href="view_user.php?id=<?php echo doCleanOutput($the_row["user_id"]);?>" target="_blank">
							   <?php echo $the_row[FirstName]." ".$the_row[LastName]?>
                               
                               <?php 
							   	
								echo "<br>";
							   
								echo $the_row[user_email];
								
								if($the_row[email]){
									echo ", ".$the_row[email];
								}
								/*if($the_row[ContactEmail1]){
									echo ", ".$the_row[ContactEmail1];
								}
								if($the_row[ContactEmail2]){
									echo ", ".$the_row[ContactEmail2];
								}*/
							
							?>
                           </a>
                           
                           </td>
                   		  
                            
                            <td >
                            
                            
                            <?php 
							
								//also see if this user has some email status
								
								
								$email_status_sql = "
								
								
									select
										*
									from 
										email_log
									where																			
										user_id = '$the_row[user_id]'
										and email_type = '$alert_type'
										and email_year = '$ddl_year'		
										and email_status > 0											
										
								";
								
								//echo $email_status_sql;
								
								$email_status_result = mysql_query($email_status_sql);
								
								while($email_status_row = mysql_fetch_array($email_status_result)){
									
									echo "<br>email แจ้ง ".getMailAlertText($email_status_row[email_type])
										. " - " . getMailStatusText($email_status_row[email_status])
										. " วันที่ " . formatDateThaiShort($email_status_row[email_date])
										;
									
								}
							
							?>
                            
                            </td>
                   		  
           		      </tr>
                      
                      
                      <?php 
					  	
						
						//yoes 20151025 -- also add mail to queue if this is "sending mail"
						if($_POST[do_send_mail]){
						
							$insert_sql = "
											
											replace into 
												email_log(												
													user_id
													, email_type
													, email_year
													, email_status
													, email_date												
												)
												values(
													
													'$the_row[user_id]'
													,'$alert_type'
													,'$ddl_year'
													,'0'
													, now()
												
												)
												
										
										
											";
											
								mysql_query($insert_sql);
							
						}
					  
					  
					  ?>
                      
                      <?php } ?>
                         
                         
                    </table>
                     
                     
                    <?php 
					
						if($_POST[do_send_mail]){?>
						
							<script>
								alert("email ถูกเพิ่มไปใน email queue เพื่อรอการส่งออกแล้ว");
								window.location.href = "manage_email.php";
							</script>
					<?php		
						}
					?>
                    
                   
                </td>
			</tr>
             
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

<script language="javascript">

function checkOrUncheck(){
	if(document.getElementById('chk_all').checked == true){
		checkAll();
	}else{
		uncheckAll();
	}
}

function checkAll(){
	<?php echo $js_do_check; ?>
}

function uncheckAll(){
	<?php echo $js_do_uncheck; ?>
}
</script>
</body>
</html>