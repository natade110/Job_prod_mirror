<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($sess_accesslevel != 1 ){ 
	
		//this function is admin-only
		header("location: index.php");
		exit();
	}

	//yoes 20151110	
	if($_POST[do_clean_queue]){
	
		mysql_query("delete from email_log where email_status = 0");	
		
	}
	
?>
                    



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  > Email รออยู่ใน queue การส่ง</h2>
                   
                    <strong>email ที่อยู่ใน queue การส่งด้านล่าง - จะถูกส่งออกในเวลาหลังเที่ยงคืน ถึงหกโมงเช้าของทุกวัน</strong>           
                            
                    
                    
                    <a href="manage_email.php">กลับไปหน้าที่แล้ว</a>
                    <br /><br />
 					
                                
                    <?php 
					
						
						
						$sql = "select 
									* 
								from 
									email_log  a
										join users b
											on a.user_id = b.user_id
								where 
									email_status = 0";
						
						$the_result = mysql_query($sql);
						
						
					?>  
                    
                    
                    พบ email ใน queue <strong><?php echo mysql_num_rows($the_result);?></strong> ฉบับ <br /><br />
                    
                    
                    คลิกที่นี่เพื่อล้าง email ใน queue ทิ้งทั้งหมด 
                    
                    <form method="post">
                        	 <input 
                             	type="submit" 
                                value="ล้าง email ใน queue" name="do_clean_queue"
                                
                                onclick="return confirm('ต้องการล้าง email ทั้งหมดใน queue ทิ้ง?');"
                                
                                />
                               
                        </form>
                        
                       
                    
                    <br /><br />
                                        
                    <table cellpadding="3" width="800" bgcolor="#FFFFFF" border="1" style="border-collapse: collapse;">
                    
                   		 <tr>
                        
                        
                        	<td style="background-color:#efefef">
                            	email                            </td>
                            
                            
                           
                            
                            
                            <td style="background-color:#efefef">
                            ประเภทของ	mail ที่จะส่งออก                      </td>
                            
                            
               		  </tr>
                         
                         
                          <?php 
												
							while($the_row = mysql_fetch_array($the_result)){
						
						?>
                         
                   		 <tr>
                   		   <td >
                           
                           
                           	<a href="view_user.php?id=<?php echo doCleanOutput($the_row["user_id"]);?>" target="_blank">
							   <?php echo $the_row[FirstName]." ".$the_row[LastName]?>
                               
                               <?php 
							   	
								echo "<br>";
							   
								echo $the_row[user_email];
								
								if($the_row[email]){
									echo ", ".$the_row[email];
								}
							
							?>
                           </a>
                                
                            </td>
                   		   <td ><?php echo getMailAlertText($the_row["email_type"]);?> - ปี <?php echo $the_row["email_year"]+543?></td>
                   		   
                   		   
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