<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($_GET["mode"]=="search"){
		$mode = "search";
		
	}elseif($_GET["mode"]=="letters"){
		$mode = "letters";
	}
	
	
	//yoes 20141007 -- also check permission
	if($sess_accesslevel == 1 ||  $sess_can_manage_user){	
		//can pass		
	}else{
		//nope
		header ("location: index.php");	
	}

?>
<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0;"  >
                    Users ทั้งหมด
                    
                    
                  </h2>
                   
                    
                    <div style="padding:10px 0 10px 0"><a href="view_user.php?mode=add">+ เพิ่ม user ใหม่เข้าไปในระบบ</a></div>
                    
                    <div style="padding:0 0 5px 0">
	                    จำนวน Users ทั้งหมด: <strong><?php echo getFirstItem("select count(user_id) from users");?></strong> users
                    </div>
                    
                    <table border="1"  cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                        	
           	  <td >
                            	<div align="center"><span class="column_header">ลำดับที่</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">User name</span> </div></td>
                     	<!--
                      <td>
                            	<div align="center"><span class="column_header">Password</span> </div></td>
                                -->
                                
                      <td>
                            	<div align="center"><span class="column_header">ชนิดของ user</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">ชื่อ-นามสกุล</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">หน่วยงาน</span> </div></td>
                           
                             
                          
                           
                            
                          <td><div align="center"><span class="column_header">ลบข้อมูล</span></div></td>
                    	</tr>
                        <?php
					
						
						//yoes 20141007 --> also set if this is not admin then can only see own's province
						if(($sess_can_manage_user && $sess_meta) && $sess_accesslevel == 3){	
							
							$filter_sql = " 
							
								where 
									user_meta = '$sess_meta'
									or 
									
									(
										accessLevel = 4
										and
										user_meta in (
										
											select cid from company where province	= '$sess_meta'							
											
										
										)
									)
									
									";
						
						}
						
						
						$get_org_sql = "SELECT *
											FROM users										
										
										$filter_sql
										
										order by user_id asc
										
										";
						//echo $get_org_sql;
						$org_result = mysql_query($get_org_sql);
					
						//total records 
						$total_records = 0;
					
						while ($post_row = mysql_fetch_array($org_result)) {
					
							$total_records++;
							
						?>     
                        <tr bgcolor="#ffffff" align="center" >
                        	
                       	  <td >
                            <div align="center"><a href="view_user.php?id=<?php echo doCleanOutput($post_row["user_id"]);?>"><?php echo $total_records;?></a> </div></td>
                            
                      <td>
                            	<a href="view_user.php?id=<?php echo doCleanOutput($post_row["user_id"]);?>"><?php echo ($post_row["user_name"]);?></a>                            </td>
                            
                            <!--<td>
                            	<?php //echo ($post_row["user_password"]);?>  </td>-->
                            
                            <td>
                            	<?php echo formatAccessLevel($post_row["AccessLevel"]);?>                            </td>
                            <td>
                            	<?php echo $post_row["FirstName"] ." ". $post_row["LastName"];?>                            </td>
                            <td>
                            	
								
								<?php 
								
									if($post_row["AccessLevel"] == 4){
										
										
										$this_company_row = getFirstRow("select * from company where cid = '".$post_row["user_meta"]."'");
										
										
										echo formatCompanyName($this_company_row["CompanyNameThai"] , $this_company_row["CompanyTypeCode"]);
																				
									}else{
										
										echo $post_row["Department"];
										
									}
									
								?>
                                
                                
                           </td>
                           
                         
                            
                            <td>
                             <div align="center"><a href="scrp_delete_user.php?id=<?php echo doCleanOutput($post_row["user_id"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" /></a>                              </div></td>
                      </tr>
                        <?php } //end loop to generate rows?>
				  </table>
                   
                    
                  
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