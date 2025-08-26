<?php

	include "db_connect.php";
	include "session_handler.php";
	
	

?>

<?php
					
	//MAIN SQL			
	if(strlen($_POST["GovDocNo"]) > 0){
			
			
			$name_exploded_array = explode(" ",doCleanInput($_POST["GovDocNo"]));
			
			//print_r($name_exploded_array);
			for($i=0; $i<count($name_exploded_array);$i++){
			
				if(strlen(trim($name_exploded_array[$i]))>0){
					//echo $name_exploded_array[$i];
					$use_condition = 1;
					$condition_sql .= " and GovDocNo like '%".doCleanInput($name_exploded_array[$i])."%'";
					
				}
			
			}
			
		}
	
	// Pagination Stuffs
	$the_sql = "SELECT count(*)
					FROM announcement
					
					WHERE
						AID
					IN 
					(
						SELECT distinct(AID)
						FROM 
						announcecomp
						
					)
					$condition_sql
					";
	
	
	$record_count_all = getFirstItem($the_sql);			
						
	
	$per_page = 20;
	$num_page = ceil($record_count_all/$per_page);
	
	$cur_page = 1;
	if(is_numeric($_POST["start_page"]) && $_POST["start_page"] <= $num_page && $_POST["start_page"] > 0){
		$cur_page = $_POST["start_page"];
	}
		
	$starting_index = 0;
	if($cur_page > 1){
		$starting_index = ($cur_page-1) * $per_page;						
	}
	
	$the_limit = "limit $starting_index, $per_page";
	
						
	
	$get_org_sql = "SELECT *
					FROM announcement
					
					WHERE
						AID
					IN 
					(
						SELECT distinct(AID)
						FROM 
						announcecomp
						
					)
					$condition_sql
					order by AID asc
					$the_limit
					";
					
	
	//echo $get_org_sql;
	$org_result = mysql_query($get_org_sql);

	//total records 
	//$total_records = 0;
	
	$total_records = 0;
						
?>						

<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0;"  >
                   การประกาศผ่านสื่อทั้งหมด
                    
                    
                  </h2>
                    <br />
                    
                      <form method="post">
                    <table style=" padding:10px 0 0px 0;">
                    
                        <tr>
                              <td bgcolor="#efefef">ค้นหาเลขที่หนังสือ:</td>
                              <td><input type="text" name="GovDocNo" value="<?php echo $_POST["GovDocNo"];?>" /></td>
                              <td bgcolor="#efefef"><input type="submit" value="แสดง" name="mini_search"/></td>
                              
                          </tr>
                          <tr>
                          	<td colspan="3">
                            	<div align="left">
                                <select name="start_page" onchange="this.form.submit()">
                                    <?php 
                                        for($i = 1; $i <= $num_page; $i++){
                                    ?>
                                    <option value="<?php echo $i;?>" <?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
                                    <?php
                                        }
                                    ?> 
                                </select>
                                </div>
                            </td>
                          </tr>
                      
                    </table>
                    </form>
                    
                    <table border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                        	
           	 				 <td >
                            	<div align="center"><span class="column_header">ลำดับที่</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">หนังสือเลขที่</span> </div></td>
                                
                                <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ //exec wont see these?>
                          <td><div align="center"><span class="column_header">ลบข้อมูล</span></div></td>
                          <?php }?>
                          
                    	</tr>
                        <?php
						
						$total_records = $starting_index;
						while ($post_row = mysql_fetch_array($org_result)) {
					
							$total_records++;
							
						?>     
                        <tr bgcolor="#ffffff" align="center" >
                        	
                       	 
                          
                          <td >
                            	<div align="center"><a href="view_announce.php?id=<?php echo doCleanOutput($post_row["AID"]);?>"><?php echo $total_records;?></a> </div></td>
                            
                            <td>
                            	<a href="view_announce.php?id=<?php echo doCleanOutput($post_row["AID"]);?>"><?php echo doCleanOutput($post_row["GovDocNo"]);?></a>                            </td>
                            
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ //exec wont see these?>
                            <td>
                              <div align="center"><a href="scrp_delete_announce.php?id=<?php echo doCleanOutput($post_row["AID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div></td>
                            <?php }?>  
                              
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