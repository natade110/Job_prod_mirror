<?php

	
	//only has "ADD" mode for now
	$mode = "add";	
	$this_id = "new";
	
	include "db_connect.php";
	include "scrp_config.php";
	
	if(!isset($_SESSION['sess_registerid'])){
		
		header("location: register_login.php");
		exit();
		
	}else{
	
		$sess_registerid = $_SESSION['sess_registerid'];	
		$this_id = $sess_registerid;
		
	}
	
?>
<?php 
	include "header_html.php";
	include "global.js.php";
?>
              <td valign="top">
                	
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                  
                	ส่งเอกสารการปฏิบัติตามกฏหมาย
                
                </h2>
                    
                    <div style="padding:5px 0 0px 2px">
                   
                    
                   
                    
                   
                <?php 
						if($_GET["added"]=="added"){
							
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ส่งเอกสารการปฏิบัติตามกฏหมายเสร็จสิ้น</div>
                         
                <?php
						}					
					?>
                   
                   	<?php 
						if($_GET["format"]=="format"){
					?>							
                <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* สามารถ upload ไฟล์ .pdf, .doc หรือ .docx เท่านั้น</div>
                    <?php
						}					
					?>
                   
                                      
                <form action="scrp_add_register_doc.php" method="post" enctype="multipart/form-data" onsubmit="return validate_register(this);"
               
                >
                     <input name="register_id" type="hidden" value="<?php echo $this_id;?>" />
                   <table border="0" cellpadding="0">
                        <tr>
                          <td> <hr /><table border="0" style="padding:0px 0 0 50px;" >
                              <tr>
                                <td colspan="2">
                                	<hr />
                                	<span style="font-weight: bold">สถานประกอบการ: </span><?php echo getFirstItem("select register_org_name from register where register_id = '".$sess_registerid."'");?> <hr /></td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">เอกสารการปฏิบัติตามกฏหมายประจำปี</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  
	                                   <?php include "ddl_year_with_blank.php";?>
	                                   *
                                  
                                </span></td>
                              </tr>
                              
                             
                              <tr>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;">แนบไฟล์:</span></td>
                                <td>
                                <input type="file" name="mod_file" id="mod_file" />
                                *
                                <table border="0" cellspacing="5">
                                  <tr>
                                    <td colspan="2"><strong>ตัวอย่างแบบฟอร์ม</strong></td>
                                  </tr>
                                  <tr>
                                    <td>
                                    
                                    <a href="bangkok_56_editable.pdf">กรุงเทพ ปี 56</a></td>
                                    <td><a href="province_56_editable.pdf">ตจว. ปี 56</a></td>
                                  </tr>
                                  <tr>
                                    <td colspan="2"><hr />
                                    <img src="decors/pdf_small.jpg" /> <a href="http://get.adobe.com/reader/direct/" style="font-size:11px;">Download AdobeReader</a></td>
                                  </tr>
                                  
                                </table></td>
                              </tr>
                              <tr>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;">รายละเอียดเพิ่มเติม:</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <textarea name="mod_desc" cols="50" rows="5" id="mod_desc"></textarea>
                                </span></td>
                              </tr>
                              
                              
                          </table></td>
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
													mod_register_id = '".$sess_registerid."'
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
                                    <td><div align="center"><a href="scrp_delete_register.php?id=<?php echo doCleanOutput($pay_row["mod_id"]);?>&register_id=<?php echo doCleanOutput($pay_row["mod_register_id"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" /></a>                              </div></td>
                                  </tr>
                                  <?php }?>
                                  
                                </table>

                            
                            
                            </td>
                        </tr>
                        
                        
                        
                        
                        <tr>
                          <td><hr />
                              <div align="center">
                                <input type="submit" value="ส่งข้อมูล" />
                          </div></td>
                        </tr>
                      </table>
                      
                </form>
                   <script language='javascript'>
						<!--
						function validate_register(frm) {
							
							
							
							
							
							
							//----
							if(frm.ddl_year.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: เอกสารการปฏิบัติตามกฏหมายประจำปี");
								frm.ddl_year.focus();
								return (false);
							}
							
							if(frm.mod_file.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: แนบไฟล์");
								frm.mod_file.focus();
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
