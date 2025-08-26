<?php

	include "db_connect.php";
	include "session_handler.php";
	
	
?>



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >จัดการพื้นที่การทำงาน</h2>
                   
                    
                   

                    <strong>จัดการพื้นที่การทำงาน</strong>
                    
                  
                    
                  	
                    
                    
                    <table cellpadding="3">
                    
                   		 <tr>
                        
                        
                        	<td style="background-color:#efefef">
                            	ชื่อพื้นที่การทำงาน                            </td>
                        	<td style="background-color:#efefef">จังหวัด</td>
                        	<td style="background-color:#efefef">
                            	จำนวนเขตภายในพื้นที่                      </td>
                        	<td style="background-color:#efefef">ผู้รับผิดชอบ</td>
                            
                            <td style="background-color:#efefef"></td>
                   		 </tr>
                         
                         <?php 
						 
						 	$sql = "
								select 
									* 
								from 
									zones
								order by
									zone_name  asc
									
								";
								
							$zone_result = mysql_query($sql);
							
							while($zone_row = mysql_fetch_array($zone_result)){
								
								//for this zone see if something is selected																
						 
						 ?>
                         <tr>                        
                        
                        	<td >      
                            
                            <a href="manage_zone_view.php?id=<?php echo $zone_row[zone_id];?>">
                            <?php echo $zone_row[zone_name]?>         
                            </a>
                                         
                            </td>
                        	<td >
                            
                            <?php echo getFirstItem("select province_name from provinces where province_code = '$zone_row[zone_province_code]'");?>  
                            
                            </td>
                            
                        	<td >
                            
                            
                            <a href="manage_zone_view.php?id=<?php echo $zone_row[zone_id];?>">
                            <?php echo getFirstItem("select count(*) from zone_district where zone_id = '$zone_row[zone_id]'");?>       
                            </a>
                            
                            
                            </td>
                        	<td >
                            
                            
                            <?php 
							
							
								$user_row = getFirstRow("
										select 
											*
										from 
											zone_user a
												join 
													users b
														on
															a.user_id = b.user_id
										where 
											a.zone_id = '$zone_row[zone_id]'
										");
										
										
								if($user_row[user_name]){
								
									echo "<a href='view_user.php?id=$user_row[user_id]'>$user_row[user_name]</a>";
									
								}else{
									
									echo "ไม่ระบุ";
									
								}
								
								
							
							?></td>
                            
                            <td >
                            
                            	<a href="scrp_delete_zone.php?id=<?php echo doCleanOutput($zone_row[zone_id]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" height="15" /></a>
                            
                            </td>
                   		 </tr>
                        
                         
                         <?php } ?>
                         
                         
                          <tr>
                          
                           <td colspan="6" >
                           
                           
                           
                           <form method="post" action="scrp_add_zone.php" enctype="" >
                            
                              <table border="0" cellpadding="0" >
                                <tr>
                                  <td> <hr /><table border="0" style="padding:0px 0 0 50px;" >
                                      <tr>
                                        <td colspan="2">
                                            <span style="font-weight: bold">เพิ่มพื้นที่การทำงานใหม่</span></td>
                                       
                                      </tr>
                                      <tr>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">ชื่อพื้นที่การทำงาน</span></td>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">
                                           
                                               <input name="zone_name" type="text" id="zone_name" value="" required="required"  />
                                                                          *</span></td>
                                      </tr>
                                      <tr>
                                        <td>จังหวัด</td>
                                        <td><?php
										
										$_POST["Province"] = 1;
										 include "ddl_org_province_no_null.php";
										 
										 ?></td>
                                      </tr>
                                                                    
                                  </table></td>
                                </tr>
                                <tr>
                                  <td><hr />
                                      <div align="center">
                                        <input type="submit" value="เพิ่มพื้นที่การทำงาน" />
                                    </div></td>
                                </tr>
                              </table>
                              
                        </form>
                             
                           </td>
                         </tr>
                         
                         
                       
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