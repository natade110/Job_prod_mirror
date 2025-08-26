<?php

	include "db_connect.php";
	include "session_handler.php";
	
	
?>



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >จัดการพื้นที่การทำงานของ เจ้าหน้าที่ พก. </h2>
                   
                    
                   

                    <strong>จัดการพื้นที่การทำงานของ เจ้าหน้าที่ พก.</strong>
                    
                  
                    
                  	
                    
                    
                    <table cellpadding="3">
                    
                   		 <tr>
                        
                        
                        	<td style="background-color:#efefef">
                            	เขต                            </td>
                        	<td style="background-color:#efefef">
                            	ผู้รับผิดชอบ                      </td>
                            
                            <td style="background-color:#efefef"></td>
                   		 </tr>
                         
                         <?php 
						 
						 	$sql = "
								select 
									* 
								from 
									districts
								where
									province_code = 10									
								order by
									district_name  asc
									
								";
								
							$district_result = mysql_query($sql);
							
							while($district_row = mysql_fetch_array($district_result)){
								
								//for this zone see if something is selected								
								$selected_user = getFirstItem("select user from user_zone where zone = '$district_row[district_area_code]'");
						 
						 ?>
                         <tr>                        
                        
                        	<td >      
                            
                            <?php echo $district_row[district_name]?>                      
                            </td>
                            
                        	<td >       
                               
                               <select name="nep_user_<?php echo $district_row[district_area_code];?>" 
                               id="nep_user_<?php echo $district_row[district_area_code];?>" 
                               
                               	onchange="doUpdateUserZone(<?php echo $district_row[district_area_code];?>);"
                                
                               	>
                                    <option value="">-- เลือก --</option>
                                    
                                    <?php
                                   
                                        $get_user_sql = "select *
                                            from users
                                            where 
                                            AccessLevel = 2
                                            order by user_name asc
                                            ";
                                    
                                    //all photos of this profile
                                    
                                  
                                    $user_result = mysql_query($get_user_sql);
                                    
                                    
                                    
                                    while ($user_row = mysql_fetch_array($user_result)) {
                                    
                                    
                                    ?>              
                                        <option <?php 										
										
										
										if($selected_user == $user_row["user_id"]){echo "selected='selected'";}?> value="<?php echo $user_row["user_id"];?>"
                                        
                                        ><?php echo $user_row["user_name"] . " - " . $user_row["FirstName"] ." " .$user_row["LastName"] . " : " . $user_row["Department"];?></option>
                                    
                                    <?php
                                    }
                                    ?>
                                    
                                  
                                </select>
                               
                               
                               
                               
                            
                            
                            </td>
                            
                            <td >
                            </td>
                   		 </tr>
                        
                         
                         <?php } ?>
                         
                         
                          <tr>
                           <td >&nbsp;</td>
                           <td >
                           
                           	 <input id="exit" type="reset" name="form1:exit" value=" ปรับปรุงข้อมูล " onclick="window.location.href='manage_zone.php';" style="width: 115px" />
                             
                           </td>
                           <td ></td>
                         </tr>
                         
						  <script>
										
									function doUpdateUserZone(what){
										
										//alert(what); 
										//alert( $('#nep_user_'+what).val());
										/*$('#cid_'+what+'_saving').css("display","inline");*/
										$.ajax({ url: './ajax_update_user_zone.php',
											 data: {zone: what, user: $('#nep_user_'+what).val()},
											 type: 'post',
											 success: function(output) {
														 //alert(output);
														 //$('#cid_'+what+'_saving').css("display","none");
													  }
										});
										
									}
								
								
								</script>
                         
                       
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