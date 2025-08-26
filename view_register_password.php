<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	
?>
<?php 
	include "header_html.php";
	
?>          
                      
<td valign="top">
                	
                    
                    
                    
  <div align="center" style="padding: 25px 0 10px 0;">
      <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                      
                   ลืมรหัสผ่าน
                    
      </h2>
  </div>
                
  
  <div align="center">
  
  	<form method="post" id="email_form" name="email_form">
  
    <table style="width: 600px;" >
        <tr>
           <TD height="30" colspan="3" valign="top" class="text" style="PADDING-LEFT: 8px" align="left">
           
                      
           <div align="center">
           
           
           	<?php if($_POST[the_user_name] && $_POST[the_email]){
            
            	
				
				$the_user_row = getFirstRow("select * from users where user_name = '".doCleanInput($_POST[the_user_name])."' and user_email = '".doCleanInput($_POST[the_email])."' limit 0,1");            
				
				
				//echo "select * from users where user_name = '".doCleanInput($_POST[the_user_name])."' user_email = '".doCleanInput($_POST[the_email])."' limit 0,1"; //exit();
				
				$the_user_name = $the_user_row[user_name];
				
				$the_user_id = $the_user_row[user_id];
				
				$the_access_level = $the_user_row[AccessLevel];
				$the_commercial_code = $the_user_row[user_commercial_code];

            ?>
           
           		<?php 
				
				//yoes 20151118 -- special for company users - must have correct commercial_code
				if($the_access_level == 4){
					
					if($the_commercial_code != $_POST[the_commercial_code]){
					
						//wrong commercial code
						//give back nothing
						$the_user_name = "";	
						
					}
					
				
				}
				
				
				if($the_user_name && $the_user_row[user_enabled] == 2){
				
					//yoes 20151122 user is rejected...
					?>
                    
                     <div style="padding: 10px; color:#900;">
                      	user name ของท่าน ไม่อนุญาตให้ใช้งานระบบ โปรดติดต่อสอบถามเจ้าหน้าที่ ที่เบอร์ 0 2106 9327-31 ในเวลาราชการ  
                     </div>
                    
                    
                    <?php
				
				}elseif($the_user_name && $the_user_row[user_enabled] == "0"){
				
					//yoes 20151122 user is rejected...
					?>
                    
                     <div style="padding: 10px; color:#900;">
                      	user name ของท่าน เจ้าหน้าที่กำลังตรวจสอบข้อมูล  โปรดติดต่อสอบถามเจ้าหน้าที่ ที่เบอร์ 0 2106 9327-31 ในเวลาราชการ    
                     </div>
                    
                    
                    <?php
				
				}elseif($the_user_name){
					
					
					$do_skip_form = 1;
					
					
					//yoes 20211108
					//do update
					$pwd = bin2hex(openssl_random_pseudo_bytes(4));
					//echo "$pwd"; //exit();
					
					$sql = "update users set user_password = md5('$pwd') where user_id = '$the_user_id'";
					mysql_query($sql);
					
					
					//yoes 20151102 --> sending email here
					$mail_address = doCleanInput($_POST[the_email]);
			
					$the_header = "รหัสผ่านสำหรับเข้าใช้ระบบรายงานผลการจ้างงานคนพิการ";
					
					$the_body = "<table><tr><td>เรียนคุณ ".doCleanInput($the_user_row[FirstName]). " " .doCleanInput($the_user_row[LastName])."<br><br>";
					
					//$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบรายงานผลการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว <br><br>";
					//$the_body .= "หลังจากผู้ดูแลระบบได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว <br>";
					$the_body .= "ระบบได้สร้างรหัสผ่านในการเข้าระบบให้ใหม่แล้ว คุณจะสามารถเข้าใช้ระบบได้โดยใช้ username/password ทางด้านล่าง<br>";
					$the_body .= "หลังจากเข้าสู่ระบบด้วยรหัสผ่านใหม่แล้ว ท่านสามารถเปลี่ยนรหัสผ่านเป็นรหัสผ่านที่ท่านต้องการได้ผ่านเมนู 'เปลี่ยนรหัสผ่าน'<br>";
					//$the_body .= "โดยการคุณจะได้รับ email ยืนยันการใช้งานระบบอีกครั้ง<br><br>";
					$the_body .= "username: ".doCleanInput($the_user_row["user_name"])."<br>";
					$the_body .= "password: ".doCleanInput($pwd)."<br><br>";
					$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
					
					
					if ($server_ip == "203.146.215.187"){
						//ictmerlin.com use default mail
						mail($mail_address, $the_header, $the_body);
					
					}elseif ($server_ip == "127.0.0.1"){
						
						//donothin	
						
					}else{
						//use smtp
						doSendMail($mail_address, $the_header, $the_body);	
					}
					
					
					?>
           
                     <div style="padding: 10px;">
                        ข้อมูล user name และ password สำหรับการเข้าสู่ระบบได้ส่งไปที่อีเมล์ <strong><?php echo $_POST[the_email];?></strong> แล้ว
                     </div>
                   
                    <input id="" type="reset" value="กลับสู่หน้าหลัก" onclick="window.location.href='index.php';"  />
           
           		<?php }else{?>
                
                		
                     <div style="padding: 10px; color:#900;">
                      	ไม่พบ email <strong><?php echo doCleanInput($_POST[the_email]);?></strong> ที่ใช้กับชื่อผู้ใช้งาน <?php echo doCleanInput($_POST[the_user_name]);?> ในระบบ - กรุณาลองอีกครั้ง
                     </div>
                    
                
                <?php }?>
          
           <?php } ?>
           
           
           <?php if(!$do_skip_form){?>
           
               1) กรุุณากรอกชื่อผู้ใช้งานของท่าน
               
               <div style="padding: 10px;">
               <input name="the_user_name" id="the_user_name" type="text" required="required" />
               </div>
			   
			   
			 2) กรุุณากรอกอีเมล์ที่ผูกกับชื่อผู้ใช้งาน เพื่อรับข้อมูล login
               
               <div style="padding: 10px;">
               <input name="the_email" id="the_email" type="text" required="required" />
               </div>
               
              
               
               <input name="" type="submit" value="ค้นหา" /> 
               <input id="" type="reset" value=" ยกเลิก" onclick="window.location.href='index.php';"  />
               
               <script>
					 
					
					 $().ready(function() {
						$("#email_form").validate({							
							
							rules: {								
								the_user_name: {
									required: true
									
								},the_email: {
									required: true,
									email: true
								}/*,
								the_commercial_code: {
									required: true
									, rangelength: [13, 13]
									, digits: true
								}*/
							},
							messages: {
								
								the_email: "กรุณาใส่ email ให้ถูกต้อง"
								//,the_commercial_code: "กรุณาใส่ เลขที่นิติบุคคล 13 หลัก ให้ถูกต้อง"
							}
						});
						 
						 
					 });
					 
					 </script>
               
           <?php }?>
          
           
           </div>
           
           
									
					  </TD>
        </tr>
    </table>
    
    
    </form>
    
   </div>
  
  
            

  
  
  
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


</body>
</html>

