<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	//current mode
	if(is_numeric($_GET["id"])){
		
		//
		if($sess_userid == $_GET["id"]){
			//this user is an owner of this page
			$is_owner = 1;
		}elseif($sess_accesslevel != 1 && !$sess_can_manage_user){ // yoes 20141007 ---> if is not admin AND didnt have permisison...
			//if not, check if this user is an admin
			header("location: index.php");
			exit();
		}
		
				
		$this_id = $_GET["id"];
		
		$post_row = getFirstRow("select * 
								from 
									users
								where 
									user_id  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'user_id'
						,'user_name'
						,'user_password'
						,'AccessLevel'
						,'Department'
						,'FirstName'
						,'LastName'
						,'LastLoginDatetime' 
						,'user_meta' 
						
						,'user_email' 
						,'user_position' 
						,'user_telephone' 
						
						, 'user_enabled'
						, 'user_can_manage_user'
						
						, 'user_commercial_code'
						
						);
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			$output_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
		}
		
		
		//yoes 20141007 --> also special session for พมจ users
		
		
		
		if(($sess_can_manage_user && $sess_meta == $output_values["user_meta"]) || $sess_accesslevel == 1 ){
			$can_edit_user = 1;
			
		}elseif($output_values["AccessLevel"] == 4 && $sess_can_manage_user){
				
			//if this user is company -> also check if it's company in responsible's province or not
			$respond_province = getFirstItem("select province from company where cid = '".$output_values["user_meta"]."'");
			
			if($respond_province == $sess_meta){
				$can_edit_user = 1;
			}
			
		}
		
		
		if(!$can_edit_user && !$is_owner){
			//cant edit this	
			header("location: index.php");
			exit();
		}
		
		
	}else if($_GET["mode"] == "add"){
	
		$mode = "add";
		$this_id = "new";
		
		if(is_numeric($_GET["cid"])){
			$this_cid = $_GET["cid"];
		}
	
		//yoes 20141007
		if($sess_can_manage_user){
			$can_edit_user = 1;			
		}
	
	}else{
		header("location: index.php");
	}	

?>
<?php 
	include "header_html.php";
	include "global.js.php";
?>
              <td valign="top">
                	
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                  
                <?php if($mode == "add"){ ?>  
	                เพิ่ม User ใหม่
                <?php }else{ ?>
                
                	 ข้อมูล User: <font color="#006699"><?php echo $output_values["user_name"];?></font>
                
                <?php } ?>
                
                </h2>
                    
                    <div style="padding:5px 0 0px 2px">
                    <?php if($sess_accesslevel == 1){ ?>
                    <a href="user_list.php">users ทั้งหมด</a> > 
                    <?php }?>
                    
                    
                    <?php if($is_owner == 1){ ?>
                    เปลี่ยนรหัสผ่าน >
                    <?php }?>
                    
                    <?php if($mode == "add"){ ?>  
                    	เพิ่ม User ใหม่
                    <?php }else{?>
	                    user name: <?php echo $output_values["user_name"];?>
                    <?php }?>
                    </div>
                    
                   
                    <?php 
						if($_GET["user_added"]=="user_added"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลเสร็จสิ้น</div>
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
						if($_GET["oldpass"]=="oldpass"){
					?>							
                         <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* Password เดิมไม่ถูกต้อง!</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["duped"]=="duped"){
					?>							
                         <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* User Name มีอยู่ในระบบแล้ว</div>
                    <?php
						}					
					?>
                    
                   
                                      
                    <form method="post" action="scrp_update_user.php" enctype="multipart/form-data" onsubmit="return validate_user(this);">
                    <input name="user_id" type="hidden" value="<?php echo $this_id;?>" />
                      <table border="0" cellpadding="0" >
                        <tr>
                          <td> <hr /><table border="0" style="padding:0px 0 0 50px;" >
                              <tr>
                                <td colspan="2">
                                	<span style="font-weight: bold">ข้อมูล user</span>
                                </td>
                               
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">User Name</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php if($mode == "add"){ ?> 
	                                   <input name="user_name" type="text" id="user_name" value="<?php echo $output_values["user_name"];?>"  />
                                  <?php }else{ ?>
                                  	  <strong><?php echo $output_values["user_name"];?></strong>
                                      <input name="user_name_origin" type="hidden" id="user_name_origin" value="<?php echo $output_values["user_name"];?>"  />
                                  <?php } ?>
                                </span></td>
                              </tr>
                              
                              
                              
                              
                              <?php if($is_owner){ ?>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">Password เดิม</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_password_old" type="password"  value=""  />
                                </span></td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">Password ใหม่</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_password_new_1" type="password"  value=""  />
                                </span></td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ยืนยัน Password ใหม่</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_password_new_2" type="password"  value=""  />
                                </span></td>
                              </tr>
                              
                              <?php }else{ ?>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">Password </span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_password" type="password" id="user_password" value="<?php echo $output_values["user_password"];?>"  />
                                </span></td>
                              </tr>
                              
                              <?php } ?>
                              
                              
                              <?php if($sess_accesslevel == 1 || $can_edit_user){ ?>
                              
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">เปิดใช้งาน</span></td>
                                <td>
                                
                                	<input name="user_enabled" <?php if( $output_values["user_enabled"]){?> checked="checked" <?php }?> type="checkbox" value="1" />
                                    <input name="user_enabled_origin" type="hidden" value="<?php echo $output_values["user_enabled"];?>" />
                                
                                </td>
                              </tr>
                              
                              
                              <tr>
                                <td>ชื่อ</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="FirstName" type="text" id="FirstName" value="<?php echo $output_values["FirstName"];?>" />
                                </span></span></td>
                              </tr>
                              <tr>
                                <td>นามสกุล</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="LastName" type="text" id="LastName" value="<?php echo $output_values["LastName"];?>"  />
                                </span> </td>
                              </tr>
                              <tr>
                                <td>หน่วยงาน</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="Department" type="text" id="Department" value="<?php echo $output_values["Department"];?>"  />
                                </span></td>
                              </tr>
                              
                               <tr>
                                <td>เบอร์โทรศัพท์</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_telephone" type="text" id="user_telephone" value="<?php echo $output_values["user_telephone"];?>"  />
                                </span></td>
                              </tr>
                              
                               <tr>
                                <td>email</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_email" type="text" id="user_email" value="<?php echo $output_values["user_email"];?>"  />
                                </span></td>
                              </tr>
                              
                               <tr>
                                <td>ตำแหน่ง</td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="user_position" type="text" id="user_position" value="<?php echo $output_values["user_position"];?>"  />
                                </span></td>
                              </tr>
                              
                              
							<?php if(isset($this_cid) || $output_values["AccessLevel"]=="4"){
								  
								  		if($output_values["AccessLevel"]=="4"){
								  			$this_cid = $output_values["user_meta"];
										}
								  ?>
                                  <tr>
                                    <td><hr />ชนิดของ User</td>
                                    <td><hr /> <strong style="color:#006699">เจ้าหน้าที่สถานประกอบการ: <?php 
										$company_row = getFirstRow("select CompanyNameThai,CompanyTypeCode from company where cid ='$this_cid'");
										echo formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]);
										?></strong></td>
                                  </tr>
                                  
                                  
                                  
                                   <tr>
                                    <td>เลขทะเบียนนิติบุคคลของกระทรวงพาณิชย์ <hr /></td>
                                    <td>
									<strong ><?php echo $output_values["user_commercial_code"];?></strong> <hr /></td>
                                  </tr>
                                  
                                  
                                  <input name="AccessLevel" type="hidden" value="4" />
                                  <input name="cid" type="hidden" value="<?php echo $this_cid;?>" />
                                  <tr>
                                    <td>บัตรประจำตัวพนักงาน<br /> หรือเอกสารการยืนยันเป็นพนักงาน</td>
                                    <td> 
                                    
                                    <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_employee_card";
											
											include "doc_file_links.php";
									
									?>
                                    <br />
                                    <input name="register_employee_card"  type="file"  />
                                    
                                    </td>
                                  </tr>
                                  
                                  <tr>
                                    <td>บัตรประจำตัวประชาชน </td>
                                    <td> <?php 
									
											$this_id = $this_id;
											
											$file_type = "register_id_card";
											
											include "doc_file_links.php";
									
									?>
                                    <br />
                                    <input name="register_id_card"  type="file"  />
                                    
                                     </td>
                                  </tr>
                                  <script>
																			
																			
									function alertContents() {
										if (http_request.readyState == 4) {
											if (http_request.status == 200) {
												//alert(http_request.responseText.trim()); 
												document.getElementById("loading_"+http_request.responseText.trim()).style.display = 'none';
											} else {
												//alert('There was a problem with the request.');
											}
										}
									}
								  </script>
                                 
                                  
                                  <?php }else{ ?>
                                  
                                  <tr>
                                    <td>ชนิดของ User</td>
                                    <td>
                                    
                                    	<?php if($sess_accesslevel == 1){ //admin can manage roles?>
	                                      <?php include "ddl_access_level.php";?>
                                        <?php }elseif($sess_accesslevel == 3){ // พมจ cant manage row?>
                                        	เจ้าหน้าที่ พมจ.
                                            <input name="AccessLevel" value="3" type="hidden" />
                                        <?php }?>
                                      
                                      
                                    </td>
                                  </tr>
                                  <tr id="the_province">
                                    <td>จังหวัด</td>
                                    <td><?php include "ddl_org_province.php";?></td>
                                  </tr>
                                  
                                  <tr id="can_edit_user">
                                    <td>สามารถจัดการ user ในจังหวัดได้?</td>
                                    <td><input name="user_can_manage_user" type="checkbox" value="1" <?php if($output_values["user_can_manage_user"]){?>checked="checked"<?php }?> /></td>
                                  </tr>
                                  
                                   <script>
                                                                
                                        function doToggleLevel(){
                                        
                                            AccessLevel = document.getElementById("AccessLevel").value;
                                        
                                            document.getElementById("the_province").style.display = "none";
											document.getElementById("can_edit_user").style.display = "none";
                                            
                                            if(AccessLevel == "3"){
                                                document.getElementById("the_province").style.display = "";
												document.getElementById("can_edit_user").style.display = "";
                                            }
                                        }	
                                        
                                        doToggleLevel();							
                                    
                                    
                                    </script>
                                  
                                  <?php } ?>
                              
                             
                              
                              <?php } ?>
                              
                          </table></td>
                        </tr>
                        <tr>
                          <td><hr />
                              <div align="center">
                                <input type="submit" value="ปรับปรุงข้อมูล" />
                            </div></td>
                        </tr>
                      </table>
                      
                </form>
                   <script language='javascript'>
						<!--
						function validate_user(frm) {
							<?php if($mode == "add"){ ?> 
							if(frm.user_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อ user name");
								frm.user_name.focus();
								return (false);
							}
							
							
							var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.-_";
						   var checkStr = frm.user_name.value;
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
							 frm.user_name.focus();
							 return (false);
						   }

							
							<?php } ?>
							
							<?php if($is_owner){ ?>
							
							if(frm.user_password_old.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: password เดิม");
								frm.user_password_old.focus();
								return (false);
							}
							if(frm.user_password_new_1.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: password ใหม่");
								frm.user_password_new_1.focus();
								return (false);
							}
							if(frm.user_password_new_1.value != frm.user_password_new_2.value)
							{
								alert("กรุณาใส่ข้อมูล: ยืนยัน password ใหม่ไม่ถูกต้อง");
								frm.user_password_new_2.focus();
								return (false);
							}
							
							<?php }else{?>
							
							if(frm.user_password.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: password");
								frm.user_password.focus();
								return (false);
							}
							
							<?php } ?>
							
							//----
							if(frm.AccessLevel.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: ชนิดของ User");
								frm.AccessLevel.focus();
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

</body>
</html>
