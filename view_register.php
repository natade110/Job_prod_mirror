<?php

	header("location: index.php"); exit();	
		
	include "db_connect.php";
	include "scrp_config.php";
	
	
	if(is_numeric($_GET["id"]) && $sess_accesslevel == 1){
		
		$mode = "edit";	
		$this_id = $_GET["id"];
		
		$post_row = getFirstRow("select * 
								from 
									register
								where 
									register_id  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'register_id'
						,'register_name'
						,'register_password'
						
						,'register_org_name'
						,'register_province'
						,'register_contact_name'
						,'register_contact_phone'
						,'register_position'
						,'register_email'

						
						);
				//echo "asdasd";
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			//echo $i;
			$register_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
		}								
		
	}else{
	
		//only has "ADD" mode for now
		$mode = "add";	
		$this_id = "new";
	
	}
	
?>
<?php 
	include "header_html.php";
	include "global.js.php";
?>
              <td valign="top">
                	
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                  
                  	<?php if($mode == "edit"){?>
                    แก้ไขข้อมูล User สถานประกอบการ
                    <?php }else{?>
                	สถานประกอบการสมัครเข้าใช้งาน
                    <?php }?>
                
                </h2>
                    
                    <div style="padding:5px 0 0px 2px">
                   
                    
                   
                    
                   
                <?php 
						if($_GET["user_added"]=="user_added"){
							
							$register_id = $_GET["id"];
							$register_row = getFirstRow("select * from users where user_id = '$register_id'");
							
							
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลการใช้งานเสร็จสิ้น</div>
                         
                         <table border="0">
                          <tr>
                            <td colspan="2"><hr /><strong>ข้อมูลการใช้งานระบบ</strong>
                            
                            <!--
                            <br />คุณสามารถ <a href="submit_forms.php">ส่งเอกสารการปฏิบัติตามกฏหมาย</a> ได้ด้วย user name และ password ด้านล่าง
                            -->
                            
                            <hr /></td>
                           </tr>
                          <tr>
                            <td>User name:</td>
                            <td><?php echo $register_row["user_name"];?></td>
                          </tr>
                          <tr>
                            <td>Password:</td>
                            <td><?php echo $register_row["user_password"];?></td>
                          </tr>
                         <tr>
                            <td>ชื่อสถานประกอบการ:</td>
                            <td><?php 
							
							$my_company_row = getFirstRow("select CompanyNameThai, CompanyTypeCode from company where CID = '".$register_row["user_meta"]."'");
							
							echo formatCompanyName($my_company_row["CompanyNameThai"],$my_company_row["CompanyTypeCode"]);
							
							?></td>
                          </tr>
                          
                           <tr>                            
                            <td colspan="2">
                            <hr />
                            
                            <span style="color:#369; ">
                           	กรุณารอการยืนยัน เริ่มการใช้งานผ่านทาง email ของคุณ (<?php echo $register_row["user_email"];?>)
                            </span>
                            
                            <hr />
                            </td>
                          </tr>
                          
                        </table>

                         
                    <?php
						}					
					?>
                   
                   	<?php 
						if($_GET["updated"]=="updated"){
					?>							
                <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["duped"]=="duped"){
					?>							
                <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* User Name ที่ใช้สมัครมีอยู่ในระบบแล้ว กรุณาใช้ user name อื่นในการสมัคร - ลืมรหัสผ่าน <a href="view_register_password.php">คลิกที่นี่</a></div>
                    <?php
						}					
					?>
                    
                    <?php 
						if($_GET["mailed"]=="mailed"){
					?>							
                <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* Email ที่ใช้สมัครมีอยู่ในระบบแล้ว กรุณาใช้ email อื่นในการสมัคร - ลืมรหัสผ่าน <a href="view_register_password.php">คลิกที่นี่</a></div>
                    <?php
						}					
					?>
                   
                                      
                <form 
                	method="post" 
                    id="view_user_form" 
                    action="scrp_update_register.php" 
                    onsubmit="return validate_register(this);"               
					
                    enctype="multipart/form-data"
                    
                
                >
                     <input name="register_id" type="hidden" value="<?php echo $this_id;?>" />
                     
                     <script>
					 
					
					 $().ready(function() {
						 
						 //alert("whaattt");
						 // validate signup form on keyup and submit
						$("#view_user_form").validate({
							
							
							rules: {
								register_contact_name: "required",
								register_contact_lastname: "required",
								register_contact_phone: {
									required: true,
									number: true
								},
								register_email: {
									required: true,
									email: true
								},
								register_position: {
									required: true
								},
								register_employee_card: {
								  required: true,
								  accept: "image/*"
								},
								register_id_card: {
								  required: true,
								  accept: "image/*"
								},
								user_commercial_code: {
									required: true,
									number: true,
									maxlength: 13,
									minlength: 13
									
								}
							},
							messages: {
								register_contact_name: "กรุณาใส่ ชื่อผู้ติดต่อ",
								register_contact_lastname: "กรุณาใส่ นามสกุลผู้ติดต่อ",
								register_contact_phone: "กรุณาใส่ เบอร์โทรศัพท์ ที่เป็นตัวเลขเท่านั้น",
								register_email: "กรุณาใส่ email ให้ถูกต้อง",
								register_position: "กรุณาใส่ ตำแหน่ง ให้ถูกต้อง",
								register_employee_card: "กรุณาแนบรูป เป็นไฟล์ jpg, gif หรือ png เท่านั้น",
								register_id_card: "กรุณาแนบรูป  เป็นไฟล์ jpg, gif หรือ png เท่านั้น",
								user_commercial_code: "กรุณาใส่ เลขทะเบียนนิติบุคคล เป็นตัวเลข 13 หลักเท่านั้น"
							}
						});
						 
						 
						 
					 }); /**/
					 
					 </script>
                   <table border="0" cellpadding="0">
                        <tr>
                          <td> <hr /><table border="0" style="padding:0px 0 0 50px;" >
                              <tr>
                                <td colspan="4">
                                	<hr />
                                	<span style="font-weight: bold">ข้อมูลการใช้งานระบบ</span>                                </td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">User Name</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  
                                  		<?php if($mode == "edit"){?>
                                        
											<?php echo $register_values["register_name"];?> 
	                                   
                                        <?php }else{?>
	                                   <input 
                                       
                                       	name="register_name" type="text" id="register_name" value="<?php echo $output_values["user_name"];?>"
                                       
                                       	onchange="doCheckUserName();"
                                        
                                         />
	                                   <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>
                                       
                                       <br />
                                       
                                       <span class="style86" id="register_name_used" style="padding: 10px 0 10px 0; display: none;"><font color="red">user name นี้ถูกใช้งานแล้ว - กรุณาใช้ user name อื่น</font></span>
	                                   <?php }?>
                                  
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                              <script>
							  	
								function doCheckUserName(){
								
									//alert($('#register_name').val());	
									$.ajax({ url: './ajax_check_user_name.php',
										 data: {user_name: $('#register_name').val()},
										 type: 'post',
										 success: function(output) {
											 //alert(output);
											 if(output == 1){
												$('#register_name_used').css("display",""); 
											 }else{
												 $('#register_name_used').css("display","none"); 
											 }
											 //
										  }
									});
									
								}
							  
							  </script>
                              
                             
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">Password</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_password" type="password" id="register_password"  value="<?php echo $register_values["register_password"];?>"  />
                                  <font color="red">*</font>
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                              <?php if($mode != "edit"){?>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ยืนยัน Password</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                
                                  <input name="register_password_2" type="password" id="register_password_2"  value=""  />
                                  <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <?php }?>
                              
                             
                              
                             
                              
                            
                              <tr>
                                <td colspan="4"><hr />
                                <span style="font-weight: bold">ข้อมูสถานประกอบการ</span></td>
                              </tr>
                              
                              
                              <tr id="tr_textbox">
                                <td >เลขที่บัญชีนายจ้าง </td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_org_code" type="text" id="register_org_code" value="<?php echo $register_values["register_org_code"];?>"  />
                                  
                                  <input name="register_org_name" type="hidden" id="register_org_name" value="<?php echo $register_values["register_org_name"];?>"  />
                                  <input name="register_cid" type="hidden" id="register_cid" value="<?php echo $register_values["register_cid"];?>"  />
                                  <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span><br />
                                  <input id="btn_get_data" type="button" value="ตรวจสอบเลขที่บัญชีนายจ้าง" onClick="return doGetData();" />
                                  
                                  
                                  <script>
											
												function doGetData(){
												
													var the_id = "";
													
													//
													the_id = the_id + document.getElementById('register_org_code').value;
												
													var checkOK = "1234567890";
												   var checkStr = the_id;
												   var allValid = true;
												   for (i = 0;  i < checkStr.length;  i++)
												   {
													 ch = checkStr.charAt(i);
													 for (j = 0;  j < checkOK.length;  j++)
													   if (ch == checkOK.charAt(j))
														 break;
													 if (j == checkOK.length)
													 {
													   allValid = false;
													   break;
													 }
												   }
												   if (!allValid)
												   {
													 alert("เลขที่บัญชีนายจ้างต้องเป็นเลข 10 หลักเท่านั้น");
													 document.getElementById('register_org_code').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 10)
													{
														alert("เลขที่บัญชีนายจ้างต้องเป็นเลข 10 หลักเท่านั้น");
														document.getElementById('register_org_code').focus();
														return (false);
													}
												
													//alert("do get data");
													//document.getElementById('btn_get_data').style.display = 'none';
													//document.getElementById('img_get_data').style.display = '';
													
													var parameters = "the_id="+the_id;
													//alert(parameters);
													//return false;
													//send requests
													http_request = false;
													 if (window.XMLHttpRequest) { // Mozilla, Safari,...
														 http_request = new XMLHttpRequest();
														 if (http_request.overrideMimeType) {										
															http_request.overrideMimeType('text/html');
														 }
													  } else if (window.ActiveXObject) { // IE
														 try {
															http_request = new ActiveXObject("Msxml2.XMLHTTP");
														 } catch (e) {
															try {
															   http_request = new ActiveXObject("Microsoft.XMLHTTP");
															} catch (e) {}
														 }
													  }
													  if (!http_request) {
														 alert('Cannot create XMLHTTP instance');
														 return false;
													  }
													
													http_request.onreadystatechange = alertContents3;
													http_request.open('POST', "./ajax_get_company.php", true);
													http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;");
													http_request.setRequestHeader("Content-length", parameters.length);
													http_request.setRequestHeader("Connection", "close");
													
													http_request.send(parameters);
													
													return true;
												
												}
												
												function alertContents3(){
													
													if (http_request.readyState == 4) {
													
														if (http_request.status == 200) {
															
															//alert("response recieved");
															//return false;
															
															if(http_request.responseText == "no_result"){
															
																alert("ไม่พบข้อมูลบัญชีนายจ้าง");
																//no result
																
															}else{
															
																var JSONFile = http_request.responseText;  
																eval(JSONFile); 	
																
																//alert(someVar.company_name_thai);
																alert("เลขบัญชีนายจ้างถูกต้อง");
																//document.getElementById('le_age').value = someVar.BIRTH_DATE;
																document.getElementById('tr_textbox').style.display = 'none';
																document.getElementById('tr_result').style.display = '';
																
																document.getElementById('tr_result_2').style.display = '';
																
																document.getElementById('span_org_code').innerHTML = document.getElementById('register_org_code').value;
																document.getElementById('span_org_name').innerHTML = someVar.company_name_thai;
																
																document.getElementById('register_org_name').value = someVar.company_name_thai;
																document.getElementById('register_cid').value = someVar.company_cid;
															
																
															
															}
															//
															
														} else {
															alert('การเชื่อมต่อผิดพลาด โปรดลองอีกครั้ง');
														}
													}
												
												}
											
											</script>
                                  
                                  
                                  
                                </span></td>
                                <td></td>
                                <td></td>
                              </tr>
                              
                              <tr id="tr_result" style="display: none;">
                                <td >เลขที่บัญชีนายจ้าง</td>
                                <td><span id="span_org_code" style="font-weight: bold;"></span></td>
                                <td>ชื่อบริษัท (ภาษาไทย)</td>
                                <td><span id="span_org_name" style="font-weight: bold;"></span></td>
                              </tr>
                              
                              <tr id="tr_result_2" style="display: none;">
                                <td >เลขทะเบียนนิติบุคคลของกระทรวงพาณิชย์</td>
                                <td colspan="3"><span id="span_org_code" style="font-weight: bold;">
                                
                             	   <input name="user_commercial_code" type="text" id="user_commercial_code" value="<?php echo $register_values["user_commercial_code"];?>" maxlength="13"  />
                             	   <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></span></td>
                               
                              </tr>
                              
                              <tr>
                                <td colspan="4"><hr />
                                <strong>ข้อมูลผู้ติดต่อ</strong></td>
                              </tr>
                              
                              <tr>
                                <td valign="top">ชื่อ</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_name" type="text" id="register_contact_name" value="<?php echo $register_values["register_contact_name"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                <td valign="top">นามสกุล</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_lastname" type="text" id="register_contact_lastname" value="<?php echo $register_values["register_contact_lastname"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                              </tr>
                              <tr>
                               <td valign="top">เบอร์โทรศัพท์</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_phone" type="text" id="register_contact_phone" value="<?php echo $register_values["register_contact_phone"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                
                                <td valign="top">อีเมล์</td>
                                 <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                   <input name="register_email" type="text" id="register_email" value="<?php echo $register_values["register_email"];?>" 
                                   
                                   onchange="doCheckEmail();"
                                   
                                   />
                                 <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                 </span></span>
                                 
                                 <br />
                                 <span class="style86" id="email_used" style="padding: 10px 0 10px 0; display: none;"><font color="red">email นี้ถูกใช้งานแล้ว - กรุณาใช้ email อื่น</font></span>
                                 
                                 </td>
                              </tr>
                              
                              
                               <script>
							  	
								function doCheckEmail(){
								
									//alert($('#register_name').val());	
									$.ajax({ url: './ajax_check_email.php',
										 data: {email: $('#register_email').val()},
										 type: 'post',
										 success: function(output) {
											 //alert(output);
											 if(output == 1){
												$('#email_used').css("display",""); 
											 }else{
												 $('#email_used').css("display","none"); 
											 }
											 //
										  }
									});
									
								}
							  
							  </script>
                              
                              
                               <tr>
                                 
                                 <td valign="top">ตำแหน่ง</td>
                                 <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                   <input name="register_position" type="text" id="register_position" value="<?php echo $register_values["register_position"];?>" />
                                 <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                 </span></span></td>
                                 <td valign="top">&nbsp;</td>
                                <td valign="top">&nbsp;</td>
                               </tr>
                               
                               
                               <tr>
                                <td colspan="4"><hr />
                                <strong>แนบเอกสารยืนยันตัวเอง</strong></td>
                              </tr>
                              
                               <tr>
                                 <td valign="top">1) บัตรประจำตัวพนักงาน<br /> 
                                 หรือเอกสารการยืนยันเป็นพนักงาน <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></td>
                                 <td valign="top" colspan="3">
                                 
                                 <input name="register_employee_card"  type="file"  />
                                 </td>
                                
                               </tr>
                               
                               <tr>
                                 <td valign="top">2) บัตรประจำตัวประชาชน <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></td>
                                 <td valign="top" colspan="3">
                                 
                                 <input name="register_id_card"  type="file" />
                                 </td>
                                
                               </tr>
                              
                              
                          </table></td>
                        </tr>
                        
                        
                        
                        
                        <tr>
                          <td><hr />
                              <div align="center">
                              
                              	<?php if($mode == "edit"){?>
                                <input type="submit" value="แก้ไขข้อมูล" />
                                <?php }else{?>
                                <input type="submit" value="สมัครเข้าใช้งาน" />
                   				 <?php }?>
                                
                          </div></td>
                        </tr>
                        
                        
                        
                        <?php if($sess_accesslevel == 1){?>
                        
                        <tr>
                          <td><hr />
                              <div align="center">
                              
                              <a href="report_20.php?mod_register_id=<?php echo $register_values["register_id"];?>" target="_blank">
                              ดูรายงานการบันทึกข้อมูลเจ้าหน้าที่ของสถานประกอบการ
                              </a>
                                
                          </div></td>
                        </tr>
                        
                         <tr>
                          <td>
                          
                          <hr />
                          <strong>เอกสารที่เคยส่งไปแล้ว</strong>
                          
                          </td>
                        </tr>
                        
                        <tr>
                            <td>
                            
                            	<table border="0" cellpadding="5" style="border-collapse:collapse;">
                                  <tr bgcolor="#9C9A9C" align="center" >
                                    <td><span class="column_header">สำหรับปี</span></td>
                                    <td><span class="column_header">ไฟล์</span></td>
                                    <td><span class="column_header">วันที่ส่งไฟล์</span></td>
                                    <td><span class="column_header"></span></td>
                                    <td>&nbsp;</td>
                                  </tr>
                                  
                                  <?php
								  
								  	$pay_sql = "select 
													* 
												from 
													modify_history_register 
												where 
													mod_register_id = '".$register_values["register_id"]."'
													and mod_type = 3
												order by mod_year desc
												";
												
									//echo $pay_sql;
								  
								  	$pay_result = mysql_query($pay_sql);
						
									while ($pay_row = mysql_fetch_array($pay_result)) {

								  
								  ?>
                                  <tr>
                                    <td><?php echo formatYear($pay_row["mod_year"]);?></td>
                                    <td><a href="register_doc/<?php echo $pay_row["mod_file"];?>"><?php echo $pay_row["mod_file"];?></a></td>
                                    <td><?php echo formatDateThai($pay_row["mod_date"]);?></td>
                                    <td><?php echo $pay_row["mod_desc"];?></td>
                                    <td></td>
                                  </tr>
                                  <?php }?>
                                  
                                </table>

                            
                            
                            </td>
                        </tr>
                        
                        
                        <?php }//$sess_accesslevel == 1?>
                        
                      </table>
                      
                </form>
                   <script language='javascript'>
						<!--
						function validate_register(frm) {
							
							
							if($('#register_name_used').css("display") != "none"){
								alert("กรุณาเลือกชื่อ user name ใหม่");
								frm.register_name.focus();
								return false;	
							}
							if($('#email_used').css("display") != "none"){
								alert("กรุณาเลือกชื่อ email ใหม่");
								frm.register_email.focus();
								return false;	
							}
							
							
							
							<?php if($mode == "add"){ ?> 
							
							
							
							
							
							if(frm.register_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อ user name");
								frm.register_name.focus();
								return (false);
							}
							
							
							var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.-_";
						   var checkStr = frm.register_name.value;
						   var allValid = true;
						   for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("ชื่อ user name สามารถเป็นภาษาอังกฤษหรือตัวเลขเท่านั้น");
							 frm.register_name.focus();
							 return (false);
						   }


							if(frm.register_password.value != frm.register_password_2.value)
							{
								alert("กรุณาใส่ข้อมูล: ยืนยัน password ใหม่ไม่ถูกต้อง");
								frm.register_password_2.focus();
								return (false);
							}
							
							
							if(frm.register_org_name.value.length < 1)
							{
								alert("เลขที่บัญชีนายจ้างไม่ถูกต้อง กรุณาใส่เลขที่บัญชีนายจ้าง และทำการ 'ตรวจสอบเลขที่บัญชีนายจ้าง' อีกครั้ง");								
								return (false);
							}
							
							
							if(frm.register_employee_card.value.length < 1){
								alert("กรุณาแนบไฟล์: บัตรประจำตัวพนักงาน หรือเอกสารการยืนยันเป็นพนักงาน");								
								return (false);
							}
							
							if(frm.register_id_card.value.length < 1){
								alert("กรุณาแนบไฟล์: บัตรประจำตัวประชาชน");								
								return (false);
							}
							
							
							<?php } ?>
							
							
							if(frm.register_password.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: password");
								frm.register_password.focus();
								return (false);
							}
							
							
							
							
							if(frm.register_org_code.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่บัญชีนายจ้าง");
								frm.register_org_code.focus();
								return (false);
							}
							
							//----
							if(frm.Province.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: จังหวัด");
								frm.Province.focus();
								return (false);
							}
							
							
							if(frm.register_contact_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อผู้ติดต่อ");
								frm.register_contact_name.focus();
								return (false);
							}
							
							if(frm.register_contact_lastname.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: นามสกุลผู้ติดต่อ");
								frm.register_contact_lastname.focus();
								return (false);
							}
														
							if(frm.register_contact_phone.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เบอร์โทรศัพท์");
								frm.register_contact_phone.focus();
								return (false);
							}
							
							if(frm.register_email.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: อีเมล์");
								frm.register_email.focus();
								return (false);
							}
							
							
							
							
							
							//----
							return(true);									
						
						}
						-->
					
					</script>
                        
                   
                   
                   
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

<?php if($_GET["user_added"]=="user_added"){ ?>
                         <script>
                         document.getElementById("view_user_form").style.display = "none";
						 </script>
                    
<?php }?>

</body>
</html>
