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
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >ข้อมูลสถานประกอบการที่ไม่ถูกต้อง</h2>
                   
                   
                    <?php
					
						$upload_folder = "./to_import_school/";
	
						$files = glob($upload_folder . '*.xlsx');
					
						foreach ($files as $filename) {
							$import_filename = $filename;	
							
							?>
                            
                           
                    <div style="padding: 10px;">
                        
                                
                                ไฟล์ที่ upload ณ ตอนนี้:
                                
                                <a href="<?php echo $import_filename;?>" target="_blank" download><?php echo str_replace($upload_folder,"",$import_filename);?></a>
                                      
                                       
                                      
                            </div>
                             
                        <?php
						}					
					
					?>


					<table cellpadding="5" bgcolor="#FFFFFF" border="1" style=" margin: 10px 0; border: 1px solid #000; border-collapse: collapse;">
                    	<tr>
                    	  <td bgcolor="#efefef" style="text-align: center">
                          เลขที่สถานประกอบการ
                          <br />(ต้องเป็นเลข 10 หลักเท่านั้น)
                          </td>
                    	  <td bgcolor="#efefef" style="text-align: center">เลขที่สาขา
                          <br />
                          (ต้องเป็นเลข 6 หลักเท่านั้น)</td>
                    	  <td bgcolor="#efefef" style="text-align: center">ประเภทธุรกิจ
                          <br />
                          (ต้องเป็นรหัส 2 หลักเท่านั้น)</td>
                    	  <td bgcolor="#efefef" style="text-align: center">ชื่อสถานประกอบการ
                          <br />
                          (ห้ามเป็นค่าว่าง)</td>
                    	  <td bgcolor="#efefef" style="text-align: center">ที่อยู่</td>
                    	  <td bgcolor="#efefef" style="text-align: center">ตำบล/แขวง</td>
                    	  <td bgcolor="#efefef" style="text-align: center">อำเภอ/เขต</td>
                    	  <td bgcolor="#efefef" style="text-align: center">จังหวัด
                          <br />
                          (ต้องเป็นจังหวัดในประเทศไทย)</td>
                    	  <td bgcolor="#efefef" style="text-align: center">รหัสไปรษณีย์</td>
                    	  <td bgcolor="#efefef" style="text-align: center">เบอร์โทรศัพท์</td>
                    	  <td bgcolor="#efefef" style="text-align: center">ประเภทกิจการ
                          <br />
                          (ต้องเป็นรหัส 4 หลักเท่านั้น)</td>
                    	  <td bgcolor="#efefef" style="text-align: center">จำนวนลูกจ้าง
                          (ห้ามเป็นค่าว่าง)</td>
               	      </tr>
                      
                      <?php 
					  
					  	$sql = "
						
							select
								*
							from
								company_temp_school
							where
								is_error = 1
							limit 
								0,1000
						
						";
						
						$result = mysql_query($sql);
						
						while($row = mysql_fetch_array($result)){
							
						?>
                       
                       
                       <tr>
                    	  <td ><?php echo $row[CompanyCode]?></td>
                          <td ><?php echo $row[BranchCode]?></td>
                          <td ><?php echo $row[CompanyTypeCode]?></td>
                          <td ><?php echo $row[CompanyNameThai]?></td>
                          <td ><?php echo $row[Address1]?></td>
                          
                          <td ><?php echo $row[Subdistrict]?></td>
                          <td ><?php echo $row[District]?></td>
                          <td ><?php echo $row[Province]?></td>
                          <td ><?php echo $row[Zip]?></td>
                          <td ><?php echo $row[Telephone]?></td>
                          
                          <td ><?php echo $row[BusinessTypeCode]?></td>
                          <td ><?php echo $row[Employees]?></td>
                    	 
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