<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($sess_accesslevel != 1){	
		header("location: index.php");	
		exit();		
	}
	
?>


<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >log การนำเข้าข้อมูลสถานประกอบการ</h2>
                   
                   
                   


					<table cellpadding="5" bgcolor="#FFFFFF" border="1" style=" margin: 10px 0; border: 1px solid #000; border-collapse: collapse;">
                    	<tr>
                    	  <td bgcolor="#efefef" style="text-align: center">
                          วันที่</td>
                    	  <td bgcolor="#efefef" style="text-align: center">สถานะ</td>
                    	  <td bgcolor="#efefef" style="text-align: center">ทำโดย</td>
                    	  <td bgcolor="#efefef" style="text-align: center">ไฟล์ที่เกี่ยวข้อง</td>
                   	  </tr>
                      
                      <?php 
					  
					  	$sql = "
						
							select
								*
							from
								upload_org_log
							order by
								upload_id 
							desc
							limit 
								0,1000
						
						";
						
						$result = mysql_query($sql);
						
						while($row = mysql_fetch_array($result)){
							
						?>
                       
                       
                       <tr>
                    	  <td ><?php echo $row[upload_date]?></td>
                          <td ><?php echo $row[upload_event]?></td>
                          <td >						  
						  <?php echo getFirstItem("select user_name from users where user_id = '".$row[upload_by]."'");?></td>
                          <td >
						  <a href="./to_import/old_files/<?php echo $row[upload_file]?>" target="_blank" download>
						  <?php echo $row[upload_file]?>
                          </a>
                          </td>
                      </tr>
                       
                      <?php
							
						}
					  
					  ?>
                      
                      
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


</body>
</html>