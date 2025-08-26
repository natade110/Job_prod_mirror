<?php

	include "db_connect.php";
	include "session_handler.php";
	
	
	
	
	
	$this_id = $_GET[id]*1;
	
	
	$zone_row = getFirstRow("select * from zones where zone_id = '$this_id'");
	
	
	
	if($sess_accesslevel != 1 || !$this_id){
		header("location: index.php");
		exit();	
	}
	
?>



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >จัดการพื้นที่การทำงาน <?php echo $zone_row[zone_name]?></h2>
                   
                    
                   

                   
                    
                    
                    เลือกเขต ที่อยู่ภายใต้พื้นที่การทำงาน <?php echo $zone_row[zone_name]?> จากรายชื่อด้านล่าง
                    
                    <br />
                     <br />
                    
                    เขตพื้นที่นี้ เป็นของจังหวัด   <strong><?php echo getFirstItem("select province_name from provinces where province_code = '$zone_row[zone_province_code]'");?> </strong> 
                    
                    
                    
                  
                    
                  	
                    
                    
                    <table cellpadding="3">
                    
                   		
                         <?php 
						 
						 	$sql = "
								select 
									* 
								from 
									districts
								where
									province_code = '$zone_row[zone_province_code]'									
								order by
									district_name  asc
									
								";
								
							$district_result = mysql_query($sql);
							
							
							
							while($district_row = mysql_fetch_array($district_result)){
							
								$the_count++;
								
								//for this zone see if something is selected								
								$selected_user = getFirstItem("select user from user_zone where zone = '$district_row[district_area_code]'");
								
								
								if($the_count == 1){
									
									echo "<tr>";
								}
						 
						 ?>
                         
                         
                                                 
                        
                        	<td >      
                            <input name="<?php echo $district_row[district_area_code];?>"
                            id="<?php echo $district_row[district_area_code];?>"
                            type="checkbox" value="<?php echo $district_row[district_area_code];?>"
                            
                            onclick="doUpdateZoneDistrict(<?php echo $district_row[district_area_code];?>);"
                            
                            <?php 
							
							//see if checked
							$checked = getFirstItem("
							
										select 
											count(*) 
										from 
											zone_district 
										where 
											zone_id = '$this_id' 
											and 
											district_area_code = '$district_row[district_area_code]'
											
									");
							
							if($checked){
							?>
                            checked="checked"
                            <?php }?>
                            
                            
                            
                            <?php
							
							//also see if this already checked in another zone
							$disabled = getFirstItem("
							
										select 
											zone_id
										from 
											zone_district 
										where 
											zone_id != '$this_id' 
											and 
											district_area_code = '$district_row[district_area_code]'
											
									");
							
							if($disabled){
								
								$used_title = "เขตถูกเลือกใช้ไปแล้วในพื้นที่การทำงาน ".getFirstItem("select zone_name from zones where zone_id = '$disabled'");
								
							?>                            
                            disabled="disabled"        
                            checked="checked"                    
                            <?php }?>
                            
                            
                             />
                             
                             
                            <span <?php if($disabled){?>style="color:#039;" title="<?php echo $used_title;?>"<?php }?> >
                            <?php echo $district_row[district_name]?>
                            </span>
                            
                            
                            </td>
                            
                        	
                   		 <?php 
						 
							 if($the_count == 5){
									
									echo "</tr>";
									$the_count = 0;
								}
						 
						 ?>
                        
                         
                         <?php } ?>
                         
                         
                          <tr>
                           <td >&nbsp;</td>
                           <td >
                           
                           	
                             
                           </td>
                           <td ></td>
                         </tr>
                         
						  <script>
										
									function doUpdateZoneDistrict(what){
										
										//alert(what); 
										
										if ($('#' + what).is(":checked")) {
										
											
											$.ajax({ url: './ajax_update_zone_district.php',
												 data: {district: what, zone: <?php echo $this_id;?>, mode: 1},
												 type: 'post',
												 success: function(output) {
															 //alert(output);
															 //$('#cid_'+what+'_saving').css("display","none");
														  }
											});
											/**/
											
											//alert('www');
										
										}else{
											
											
											
											$.ajax({ url: './ajax_update_zone_district.php',
												 data: {district: what, zone: <?php echo $this_id;?>, mode: 2},
												 type: 'post',
												 success: function(output) {
															 //alert(output);
															 //$('#cid_'+what+'_saving').css("display","none");
														  }
											});
										
										
											
										}
										
										
										
									}
								
								
								</script>
                         
                       
                    </table>
                    
                    
                     <input id="exit" type="reset" name="form1:exit" value=" ปรับปรุงข้อมูล " onclick="window.location.href='manage_zone_view.php?id=<?php echo $this_id;?>';" style="width: 115px" />
                             
                              <input id="exit" type="reset" name="form1:exit" value=" กลับไปหน้าที่แล้ว " onclick="window.location.href='manage_zone_list.php';" style="width: 115px" />
                    
                   
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