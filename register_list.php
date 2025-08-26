<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($_GET["mode"]=="search"){
		$mode = "search";
		
	}elseif($_GET["mode"]=="letters"){
		$mode = "letters";
	}

?>
<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0;"  >
                    ผู้ใช้งานของสถานประกอบการ                    
                  </h2>
                   
                    
                   
                    <div style="padding:0 0 5px 0">
	                    จำนวน Users ทั้งหมด: <strong><?php echo getFirstItem("select count(register_id) from register");?></strong> users
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
                                
                      <td width="<?php echo $w125;?>" ><div align="center" ><span class="column_header">ชื่อสถานประกอบการ</span> </div></td>
                        <td width="<?php echo $w100;?>" ><div align="center" ><span class="column_header">จังหวัด</span></div></td>
                        <td width="<?php echo $w100;?>" ><div align="center" ><span class="column_header">ชื่อผู้ติดต่อ</span></div></td>
                        <td width="<?php echo $w100;?>" ><div align="center" ><span class="column_header">เบอร์โทรศัพท์</span></div></td>
                        <td width="<?php echo $w100;?>" ><div align="center" ><span class="column_header">อีเมล์</span></div></td>
                        <td width="<?php echo $w100;?>" ><div align="center" ><span class="column_header">ตำแหน่ง</span></div></td>
                           
                             
                          <td>&nbsp;</td>
                    	</tr>
                        <?php
					
						
						
						
						$get_org_sql = "
						
										select
										*
										 from
											 register b
											 ,provinces c
											
										 where
											 c.province_id = b.register_province
										
										
										order by register_id asc
										
										";
						//echo $get_org_sql;
						$org_result = mysql_query($get_org_sql);
					
						//total records 
						$total_records = 0;
					
						while ($lawful_row = mysql_fetch_array($org_result)) {
					
							$total_records++;
							
						?>     
                        <tr bgcolor="#ffffff" align="center" >
                        	
                       	  <td >
                            <div align="center"><a href="view_register.php?id=<?php echo doCleanOutput($lawful_row["register_id"]);?>"><?php echo $total_records;?></a> </div></td>
                            
                      <td>
                            	<div align="left">
								
								<a href="view_register.php?id=<?php echo doCleanOutput($lawful_row["register_id"]);?>"><?php echo ($lawful_row["register_name"]);?></a>
                                
                                </div>
       	                        </td>
                            
                            <!--<td>
                            	<?php //echo ($post_row["user_password"]);?>  </td>-->
                            
                            <td><div align="left"><?php echo $lawful_row["register_org_name"]?></div></td>
                            <td><?php echo $lawful_row["province_name"];?></td>
                            <td valign="top"><div align="left"><?php echo $lawful_row["register_contact_name"]?></div></td>
                            <td valign="top"><div align="left"><?php echo $lawful_row["register_contact_phone"]?></div></td>
                            <td valign="top"><div align="left"><?php echo $lawful_row["register_email"]?></div></td>
                            <td valign="top"><div align="left"><?php echo $lawful_row["register_position"]?></div></td>
                            <td>&nbsp;</td>
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