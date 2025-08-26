<?php

	include "db_connect.php";
	include "session_handler.php";
	
	$import_org_year = getFirstItem("select var_value from vars where var_name = 'import_org_year'");
	
?>


<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม) </h2>
                    
                    
                     <div align="left" style="padding: 10px 0;">
                      
                            <a href="import_org_full_log.php" target="_blank" style="font-weight: bold;">>> ดู log การทำงานทั้งหมด คลิกที่นี่ <<</a>
                      </div>
                   
                   

                    <strong>1. อัพโหลดไฟล์ <font color="#CC6600">.zip</font> ที่มีไฟล์ <font color="#CC6600">.txt</font> ที่มาจากประกันสังคมขึ้นระบบ และมีขนาดน้อยกว่า <font color="#CC6600">25mb</font></strong>
                    
                    <br />layout ไฟล์จากประกันสังคม <a href="layout.xls">คลิกที่นี่</a>
                    <bR />ตัวอย่างไฟล์นำเข้า <a href="nay301-mini.zip">คลิกที่นี่</a>
                    <br />
                 
                    
                  
                    <form method="post" action="upload_org_file.php" enctype="multipart/form-data" style="padding: 10px;">
                    <table>
                    	<tr>
                        	<td>
                            
                            
                            <div style="padding: 0 0 10px 0">
                            <input  name="input_file" type="file" />
                            </div>
                            
                            
                              
                            
                            <?php if($_GET["zip"]){ ?>
								
                                <font color="red">
                                error: ไฟล์ที่ upload ไม่ใช่ file .zip
                                </font>
                                <br />
                                
							<?php }?>
                            
                            <?php if($_GET["filesize"]){ ?>
								
                                <font color="red">
                                error: ไฟล์ที่ upload มีขนาดเกิน 25mb
                                </font>
                                <br />
                                
							<?php }?>
                            
                             <?php if($_GET["unzip"]){ ?>
								
                                <font color="red">
                                error: ไม่สามารถแตก .zip ไฟล์ได้
                                </font>
                                <br />
                                
							<?php }?>
                            
                             <?php if($_GET["multiplezip"]){ ?>
								
                                <font color="red">
                                error: zip ไฟล์มีไฟล์อยู่ข้างในมากกว่า 1 ไฟล์
                                </font>
                                <br />
                                
							<?php }?>
                            
                            
                             <?php if($_GET["notext"]){ ?>
								
                                <font color="red">
                                error: ไม่พบไฟล์ .txt ใน zip
                                </font>
                                <br />
                                
							<?php }?>
                            
                             <?php if($_GET["moveupload"]){ ?>
								
                                <font color="red">
                                error: Move-upload ไฟล์ไม่ได้
                                </font>
                                <br />
                                
							<?php }?>
                            
                              <?php if($_GET["ok"]){ ?>
								
                                <font color="green">
                                อัพโหลดไฟล์เรียบร้อยแล้ว
                                </font>
                                <br />
                                
							<?php }?>
                            
                             
                          
                            
                             <span id="step_1">
                                 
                             </span>
                             <script>
                             $( "#step_1" ).load( "ajax_upload_org_file.php?step=1" );
                             </script>
                            
                           
                            
                            
                          </td>
                        </tr>
                    	<tr>
                    	  <td>
                    	    
                   	      </td>
                  	  </tr>
                   </table>
                  
                    </form>
                    
                    <hr />
                    
                    
                    
                   
                    
                    <?php
					
						$upload_folder = "./to_import/";
	
						$files = glob($upload_folder . '*.txt');
						
						//yoes 20191202 --> check also support .csv
						if(!$files){
							$files = glob($upload_folder . '*.csv');
						}
						
					
						foreach ($files as $filename) {
							$import_filename = $filename;	
							
							?>
                            
                             <strong>2. มีการ upload file ขึ้นไปแล้ว - ถ้าต้องการเปลี่ยนไฟล์ประกันสังคม ให้ลบไฟล์นี้ และ upload file ในข้อ (1) อีกครั้ง</strong>
                            
                            <div style="padding: 10px;">
                            
                                
                        
                                <div style="padding: 10px 0;">
                                ไฟล์ที่ upload ณ ตอนนี้:
                                
                                <a href="<?php echo $import_filename;?>" target="_blank" download><?php echo str_replace($upload_folder,"",$import_filename);?></a>
                                
                               
                                
                                 <span id="delete_icon">
                                 
                                 </span>
									 <script>
                                     $( "#delete_icon" ).load( "ajax_upload_org_file.php?step=del" );
                                     </script>
                                    
                                  </div>
                                 
                                 คลิกที่นี่ เพื่อทำการตรวจสอบไฟล์ เพื่อเตรียมการนำเข้าข้อมูล
                                 
                                 
                                 <div id="ajax_upload_org_file">
                                 
                                 </div>
                                 <script>
								 $( "#ajax_upload_org_file" ).load( "ajax_upload_org_file.php?step=2" );
								 </script>
                                 
                              
                              
                    </div>
                             
                     <hr />         
                             
                        <?php
						}					
					
					?>
                    
                   
                    
                    
                    
                    <?php 
					
					$temp_count = getFirstItem("select count(*) from company_temp_all");
					
					if($temp_count){
						
						
						
							//find invalid details
							$sql = "
							
								select
									count(*)
								from
									company_temp_all
								where
									is_error = 1
									
							
							
							";
							
							
							 $error_rows = getFirstItem($sql);
						
						
					?>
                   
                   
                   <strong>3. ตรวจสอบไฟล์เสร็จสิ้น - ถ้าต้องการเปลี่ยนไฟล์ประกันสังคม ให้ลบไฟล์นี้ และ upload file ในข้อ (1) อีกครั้ง</strong>
                   
                   	<table cellpadding="5" bgcolor="#FFFFFF" border="1" style=" margin: 10px 0; border: 1px solid #000; border-collapse: collapse;">
                    	<tr>
                    	  <td colspan="2" bgcolor="#efefef">
                          <div align="center">
                          สรุปข้อมูลจากไฟล์ประกันสังคม
                          </div>
                          </td>
                   	  </tr>
                    	<tr>
                       	  <td bgcolor="#efefef">
                            จำนวนสถานประกอบการทั้งหมด(รวมสาขา)ที่พบใน file
                            </td>
                            <td>
                            <div align="right">
                            <?php
							
								echo formatEmployee($temp_count);
							
							?> แห่ง
                             </div>
                            </td>
                        </tr>
                        <tr>
                       	  <td bgcolor="#efefef">
                          	 จำนวนสถานประกอบการที่ข้อมูลถูกต้อง
                            </td>
                            <td>
                            <div align="right">
                            <?php
							
								echo formatEmployee($temp_count- $error_rows);
							
							?> แห่ง
                             </div>
                            </td>
                        </tr>
                         <tr>
                       	   <td bgcolor="#efefef">
                          	 จำนวนสถานประกอบการที่ข้อมูลไม่ถูกต้อง
                            </td>
                            <td>
                            <div align="right">
                             <font color="red">
                            <?php 
							 
							 echo formatEmployee($error_rows);
						   
							
							?>
                             </font>
                             แห่ง
                             
                             
                             <?php if($error_rows){?>
                                 <br />
                                 <a href="import_org_invalid_list.php" target="_blank" style="font-weight: normal;">
                                    คลิกที่นี่เพื่อดูตัวอย่างข้อมูลที่ไม่ถูกต้อง
                                 </a>
                             <?php }?>
                             
                             </div>
                            </td>
                        </tr>
                        <tr>
                           <td bgcolor="fefefe">จำนวนสถานประกอบการ(รวมสาขา)ที่เป็นหน่วยงานภาครัฐ<br />
                           และจะไม่ถูกนำเข้าระบบ</td>
                           <td><div align="right">
                             <?php
						   
						   
						   $is_in_case = getFirstItem("select count(*) from company_temp_all where is_government = 1");
						   echo formatEmployee( $is_in_case);
						   
						   ?>
                           แห่ง </div></td>
                      </tr>
                        <tr>
                       	  <td bgcolor="fefefe">
                            จำนวนสถานประกอบการ(รวมสาขา)<br />                            ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย</td>
                            <td>
                            <div align="right">
                           
                           <?php
						   
						   
						   $is_in_case = getFirstItem("select count(*) from company_temp_all where is_in_case = 1");
						   echo formatEmployee( $is_in_case);
						   
						   ?> 
                           
                           แห่ง
                          
                             </div>
                            </td>
                        </tr>
                        <tr>
                          <td bgcolor="#fefefe">จำนวนสถานประกอบการ(รวมสาขา)<br />
ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย แต่มีการปฏิบัติตามกฎหมายแล้ว และจะไม่ถูกนำเข้าระบบ</td>
                          <td><div align="right">
                            <?php
						   
						   $has_lawfulness = getFirstItem("select count(*) from company_temp_all where is_in_case = 1 and has_lawfulness = 1");
						   echo formatEmployee($has_lawfulness);
						   
						   ?>
                          แห่ง </div></td>
                        </tr>
                        
                        
                         <tr>
                       	   <td bgcolor="#fefefe">
                            จำนวนสถานประกอบการ(รวมสาขา)<br />
                            ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย มีสำนักงานใหญ่ และจะถูกนำเข้าระบบ
                            </td>
                            <td>
                            <div align="right">
                             <?php
						   
						   
						   	   $no_main_branch = getFirstItem("select count(*) from company_temp_all where is_in_case = 1 and no_main_branch = 1");
								
							   echo formatEmployee($is_in_case-$has_lawfulness-$no_main_branch);
							   
							   ?> แห่ง
                            </div>
                            </td>
                        </tr>
                        
                         <tr>
                       	  <td bgcolor="#fefefe">จำนวนสถานประกอบการ(รวมสาขา)<br />
                   	       ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย ไม่มีสำนักงานใหญ่  และจะถูกนำเข้าระบบ </td>
                            <td>
                            <div align="right">
                           <?php
						   
						    
						   echo formatEmployee($no_main_branch);
						   
						   ?> แห่ง
                             </div>
                            </td>
                        </tr>
						
						
						<tr>
                       	   <td bgcolor="#fefefe">
                            จำนวนสถานประกอบการ(รวมสาขา)<br />
                            ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย มีสำนักงานใหญ่ และจะถูกนำเข้าระบบ
                            </td>
                            <td>
                            <div align="right">
                             <?php
						   
						   
						   	   $no_main_branch = getFirstItem("select count(*) from company_temp_all where is_in_case = 1 and no_main_branch = 1");
								
							   echo formatEmployee($is_in_case-$has_lawfulness-$no_main_branch);
							   
							   ?> แห่ง
                            </div>
                            </td>
                        </tr>
                        
						
						
						 <tr>
                       	   <td >
                            จำนวนสถานประกอบการ(นับสาขาหลักเท่านั้น)<br />ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย และจะถูกนำเข้าระบบ ...
                            </td>
                            <td>
                            <div align="right">
                             <?php
						   
							  echo 
								
								formatEmployee(

									getFirstItem("
									select 
										count(*) 
									from 
										company_temp_all 
									where 
										is_in_case = 1 
										and 
										has_lawfulness = 0
										and
										is_error = 0
										and
										is_government = 0
										and
										branchCode < 1
										
										
									")
									
									
									);
							   
							   ?> แห่ง
                            </div>
                            </td>
                        </tr>
						
						
						<tr>
                       	   <td >
                            จำนวนสถานประกอบการ(นับสาขาหลักเท่านั้น)<br />ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย และจะถูกนำเข้าระบบ เพราะเป็นสถานประกอบการที่เคยมีในระบบ แต่ไม่ปฏิบัติตามกฏหมาย
                            </td>
                            <td>
                            <div align="right">
                             <?php
						   
							  echo 
								
								formatEmployee(

									getFirstItem("
									select 
										count(*) 
									from 
										company_temp_all 
									where 
										is_in_case = 1 
										and 
										has_lawfulness = 0
										and
										is_error = 0
										and
										is_government = 0
										and
										branchCode < 1
										and
										companyCode in (
										
											select 
												companyCode
											from
												company a
													join lawfulness b
														on a.cid = b.cid
														and
														b.year = '$import_org_year'
										
										
										)
										
									")
									
									
									);
							   
							   ?> แห่ง
                            </div>
                            </td>
                        </tr>
						
						
						
                        
                        
                         <tr>
                       	   <td >
                            จำนวนสถานประกอบการ(นับสาขาหลักเท่านั้น)<br />ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย และจะถูกนำเข้าระบบ เพราะป็นสถานประกอบการที่ไม่เคยมีในระบบ
                            </td>
                            <td>
                            <div align="right">
                             <?php
						   
							  echo 
								
								formatEmployee(

									getFirstItem("
									select 
										count(*) 
									from 
										company_temp_all 
									where 
										is_in_case = 1 
										and 
										has_lawfulness = 0
										and
										is_error = 0
										and
										is_government = 0
										and
										branchCode < 1
										and
										companyCode not in (
										
											select 
												companyCode
											from
												company a
													join lawfulness b
														on a.cid = b.cid
														and
														b.year = '$import_org_year'
										
										
										)
										
									")
									
									
									);
							   
							   ?> แห่ง 
                            </div>
                            </td>
                        </tr>
						
						
						<tr>
                       	   <td >
                            จำนวนสถานประกอบการที่ไม่มีสาขาหลัก(distinct(CompanyCode))<br />ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย และจะถูกนำเข้าระบบ เพราะป็นสถานประกอบการที่ไม่เคยมีในระบบ
                            </td>
                            <td>
                            <div align="right">
                             <?php
						   
							  echo 
								
								formatEmployee(

									getFirstItem("
									
									select 
										count(distinct(CompanyCode)) 
									from 
										company_temp_all 
									where 
										is_in_case = 1 
										and 
										has_lawfulness = 0
										and
										is_error = 0
										and
										is_government = 0
										
										and
										no_main_branch = 1
										and
										companyCode not in (
										
											select 
												companyCode
											from
												company a
													join lawfulness b
														on a.cid = b.cid
														and
														b.year = '$import_org_year'
										
										
										)
										
									")
									
									
									);
							   
							   ?> แห่ง
                            </div>
                            </td>
                        </tr>
                       
                       
                        
                        
                        <tr>
                       	   <td bgcolor="#efefef">
                            ข้อมูลการปฏิบัติตามกฎหมายที่นำเข้า<br />จะเป็นของปีงบประมาณ
                            </td>
                            <td>
                            <div align="right">
                            <strong>
							<?php
							
								echo $import_org_year+543;
							?>
							</strong>
                            </div>
                            </td>
                        </tr>
                    </table>
                   
                   
                   <strong>
                   
                   คลิกที่นี่เพื่อเริ่มทำการนำข้อมูลสถานประกอบการ และการปฏิบัติตามกฎหมายเข้าระบบ
                   
                   
                   
                   <br />
                   หลังจากนำข้อมูลเข้าระบบแล้ว จะไม่สามารถแก้ไขข้อมูลที่นำเข้าไปแล้วได้
                   
                   </strong>
                    
                    
                    <?php if($error_rows){?>
                    
                    	<br />
                    	<font color="#FF0000">ไม่อนุญาตให้นำข้อมูลเข้าระบบ เนื่องจากยังมีข้อมูลที่ไม่ถูกต้อง กรุณาลบไฟล์นี้ และ upload file ในข้อ (1) อีกครั้ง</font>
                        <br>
                    	<input  name="upload_file" type="button" disabled="disabled" value="นำข้อมูลเข้าระบบ"/> 
                    <?php }else{?>
                    
                    	
                         <div id="ajax_step_3">
                         
                         </div>
                         <script>
                         $( "#ajax_step_3" ).load( "ajax_upload_org_file.php?step=3" );
                         </script>
                    	
                    	
                    <?php }?>
                    
                    
                    
                    <hr />
                    
                    <?php } //ends if $temp_count?>
                    
                    
                    
                    
                    
                    <?php if($_GET[org_created]){?>
                    <div align="center" style="background-color:#E2FFDD; padding: 10px;">
                    	นำเข้าข้อมูลสถานประกอบการเรียบร้อยแล้ว <a href="import_org_full_log.php" target="_blank">คลิกที่นี่</a> เพื่อดู log การทำงานทั้งหมด
                    </div>
                    <?php }?>
                    
                    
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