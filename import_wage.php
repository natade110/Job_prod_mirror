<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($_GET["mode"]=="search"){
		$mode = "search";
		
	}elseif($_GET["mode"]=="letters"){
		$mode = "letters";
	}

?>
<?php



//manag minimum wage
if($_POST["var_value"]){

	$sql = "replace into vars values('wage_".$_POST["ddl_year"]."','".($_POST["var_value"]*1)."')";
	mysql_query($sql) or die (mysql_error());
	
}




	
?>	



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >จัดการค่าจ้างขั้นต่ำ </h2>
                   
                    
                   

                    <strong>จัดการค่าจ้างขั้นต่ำ</strong>
                    
                  
                   
                    
                    
                    
                    
                  	 <form method="post" >
                    <table>
                    	<tr>
                        
                        
                        	<td>
                            	ประจำปี
                            </td>
                        	<td>
                            	<?php include "ddl_year_wage.php";?>
                            </td>
                            <td>
                            	ค่าจ้าง
                            </td>
                        	<td>
                            	<input name="var_value" type="text" />
                            </td>
                            <td>
                            	<input name="" type="submit" value="เพิ่มข้อมูล" />
                            </td>
                        </tr>
                    
                    </table>
                    </form>
                    
                    
                    <table cellpadding="3">
                    
                   		 <tr>
                        
                        
                        	<td style="background-color:#efefef">
                            	ประจำปี                            </td>
                        	<td style="background-color:#efefef">
                            	ค่าจ้างขั้นต่ำ (บาท)                            </td>
                            
                            <td style="background-color:#efefef">ลบ</td>
                   		 </tr>
                         
                         <?php
						 
						 	$sql = "select * from vars where var_name like 'wage_%' order by var_name desc";
						 
						 	$var_result = mysql_query($sql);
		  
		 					while ($var_row = mysql_fetch_array($var_result)) {
						 
						 ?>
                         
                         
                         <tr>
                        
                        
                        	<td valign="middle">
                           	<div align="center"><?php echo formatYear(str_replace("wage_","",$var_row["var_name"]));?>                            </div></td>
                        	<td valign="middle">
                           	<div align="right"><?php echo formatMoney($var_row["var_value"]);?>                            </div></td>
                            
                           <td valign="middle"><div align="center"><a href="scrp_delete_wage.php?id=<?php echo doCleanOutput($var_row["var_name"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" width="15" /></a>                              </div></td>
                         </tr>
                         
                         <?php }?>
                    </table>
                    
                    
                    <hr />
                    
                    
                     <table cellpadding="3">
                    
                   		 <tr>
                        
                        
                        	<td style="background-color:#efefef" colspan="2">
                            	
                            	ค่าจ้างขั้นต่ำปี 2554 แบ่งตามจังหวัด
                            </td>
                        	
                   		 </tr>
                         
                          <tr>
                        
                        
                        	<td style="background-color:#efefef">
                            	
                            	จังหวัด    
                            </td>
                        	<td style="background-color:#efefef">
                            	ค่าจ้างขั้นต่ำ (บาท)                            </td>
                            
                           
                   		 </tr>
                         
                         
                         <?php 
						 
						 $sql = "
						 	
								select
									*
								from
									provinces
								order by
									province_name
								asc
						 		
								";
						 
						 	$wage_result = mysql_query($sql);
							
							
							while($wage_row = mysql_fetch_array($wage_result)){								
								
						 
						 ?>
                         
                         	 <tr>
                        
                        
                        	<td >
                            	
                            	<?php echo $wage_row[province_name]?> 
                            </td>
                        	<td >
                            	
                                <?php echo $wage_row[province_54_wage]?> 
                                
                                
                                </td>
                            
                           
                   		 </tr>
                         
                         
                         
                         <?php }?>
                         
                         
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