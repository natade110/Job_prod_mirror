<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($sess_accesslevel != 1 && $sess_accesslevel != 2 ){	
		header("location: index.php");	
		exit();		
	}
	
?>


<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >สถานประกอบการ ที่มีสาขาย่อย แต่ไม่มีสำนักงานใหญ่</h2>
                   
                   
                   <form method="post">
                   
                   <?php 
				   
				   //special for this page
				   $dll_year_start = 2017;
				   
				   if($_POST[ddl_year]){
						$this_year = $_POST[ddl_year]*1;  
				   }else{
						$this_year = date("Y")+1;   
				   }
					?>
                   
                   ประจำปี <?php include "ddl_year.php";?> 
                   <input type="submit" name="mini_search" id="button" value="แสดง" />
                   </form>

					<table cellpadding="5" bgcolor="#FFFFFF" border="1" style=" margin: 10px 0; border: 1px solid #000; border-collapse: collapse;">
                    	<tr>
                    	  <td bgcolor="#efefef" style="text-align: center">
                          เลขที่สถานประกอบการ
                          </td>
                    	  <td bgcolor="#efefef" style="text-align: center">เลขที่สาขา
                         </td>
                    	  <td bgcolor="#efefef" style="text-align: center">เป็นสาขาแรก?</td>
                    	  
                          <?php if(1==0){?>
                          <td bgcolor="#efefef" style="text-align: center">ประเภทธุรกิจ
                          </td>
                          <?php }?>
                          
                    	  <td bgcolor="#efefef" style="text-align: center">ชื่อสถานประกอบการ
                         </td>
                    	  <td bgcolor="#efefef" style="text-align: center">ที่อยู่</td>
                    	  <td bgcolor="#efefef" style="text-align: center">ตำบล/แขวง</td>
                    	  <td bgcolor="#efefef" style="text-align: center">อำเภอ/เขต</td>
                    	  <td bgcolor="#efefef" style="text-align: center">จังหวัด
                         </td>
                    	  <td bgcolor="#efefef" style="text-align: center">รหัสไปรษณีย์</td>
                    	  <td bgcolor="#efefef" style="text-align: center">เบอร์โทรศัพท์</td>
                           <?php if(1==0){?>
                    	  <td bgcolor="#efefef" style="text-align: center">ประเภทกิจการ
                          </td>
                          <?php }?>
                    	  <td bgcolor="#efefef" style="text-align: center">จำนวนลูกจ้าง</td>
               	      </tr>
                      
                       <?php 
					  
					  	$sql = "
						
							select	
								*
							from
								company		
							where
								last_modified_lid_year = '$this_year'
								and
								branchcode > 1
								and
								companycode not in (
									
									select companycode from (
										
										select 
											companycode
										FROM 
											company a
												join 
												lawfulness b 
												on 
												a.cid = b.cid 
												and 
												b.year = '$this_year'
												and
												a.branchcode < 1			
										
									) bbb
								)		
								order by
								BranchCode, companyCode asc	
						
						";
						
						$result = mysql_query($sql);
						
						$last_company_code = "";
						
						while($row = mysql_fetch_array($result)){
							
						?>
                       
                       
                       <tr>
                    	  <td ><?php echo $row[CompanyCode]?></td>
                          <td ><?php echo $row[BranchCode]?></td>
                          <td ><?php 
						  
						  	if($row[CompanyCode] != $last_company_code){
								
								
								//see if have have main branch in other years?
								$the_cid = getFirstItem("select cid from company where CompanyCode = '$row[CompanyCode]' and branchcode < 1");
								
								//echo "select cid from company where CompanyCode = '$row[CompanyCode]' and branchcode < 1";
								
							?>
                            
                            	<?php if($the_cid){?>
                                
                                <a href="organization.php?id=<?php echo $the_cid;?>" target="_blank" style="font-weight: normal;">
                                พบสาขาหลักในปีอื่น คลิกที่นี่เพื่อดูสาขาหลัก
                                </a>
                                
                                <?php }else{?>
                            
                                <a href="organization.php?mode=new&companycode=<?php echo $row[CompanyCode];?>" target="_blank" style="font-weight: normal; color: #F90;">
                                คลิกที่นี่เพื่อเพิ่มข้อมูลสาขาหลัก
                                </a>
                                
                                <?php }?>
                            
						  <?php	
							}
						  
						  ?></td>
                         
                         	 <?php if(1==0){?>
                          <td ><?php echo $row[CompanyTypeCode]?></td>
                          <?php }?>
                         
                          <td ><?php echo $row[CompanyNameThai]?></td>
                          <td ><?php echo $row[Address1]?></td>
                          
                          <td ><?php echo $row[Subdistrict]?></td>
                          <td ><?php echo $row[District]?></td>
                          <td ><?php echo getFirstItem("select province_name from provinces where province_id = '".$row[Province]."'");?></td>
                          <td ><?php echo $row[Zip]?></td>
                          <td ><?php echo $row[Telephone]?></td>
                          
                           <?php if(1==0){?>
                          <td ><?php echo $row[BusinessTypeCode]?></td>
                          <?php }?>
                          
                          <td ><?php echo $row[Employees]?></td>
                    	 
               	      </tr>
                       
                      <?php
						
						$last_company_code = $row[CompanyCode];
							
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