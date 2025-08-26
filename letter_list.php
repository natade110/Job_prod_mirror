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
						
						
		//prepare MAIN SQL
		
		//make it so it filter as %LIKE% instead
		if(strlen($_POST["GovDocumentNo"]) > 0){
			
			
			$name_exploded_array = explode(" ",doCleanInput($_POST["GovDocumentNo"]));
			
			//print_r($name_exploded_array);
			for($i=0; $i<count($name_exploded_array);$i++){
			
				if(strlen(trim($name_exploded_array[$i]))>0){
					//echo $name_exploded_array[$i];
					$use_condition = 1;
					$condition_sql .= " and j.GovDocumentNo like '%".doCleanInput($name_exploded_array[$i])."%'";
					
				}
			
			}
			
		}
		
		//"Year" filter
		if(strlen($_POST["ddl_year"]) > 0){
			$condition_sql .= " and j.Year = '".doCleanInput($_POST["ddl_year"])."'";
		}
		
		//"RequestNum" filter
		if(strlen($_POST["RequestNum"]) > 0){
			$condition_sql .= " and j.RequestNum = '".doCleanInput($_POST["RequestNum"])."'";
		}
		
		
		//"date" filter
		if($_POST["RequestDate_year"] > 0 && $_POST["RequestDate_month"] > 0 && $_POST["RequestDate_day"] > 0){
			$this_date_time = $_POST["RequestDate_year"]."-".$_POST["RequestDate_month"]."-".$_POST["RequestDate_day"];
			$condition_sql .= " and j.RequestDate = '$this_date_time 00:00:00'";
	   }else{
			$this_date_time = "0000-00-00";
	   }	
		
		
		
		
		if($sess_accesslevel == '3'){
		
			//provincial staff only see its own province
			$the_sql = "
						select count(*) from 
							documentrequest j
						WHERE
							j.RID
						IN 
						(
							SELECT distinct(a.RID)
							FROM 
							documentrequest a, docrequestcompany b, company c
							where 
							a.RID = b.RID
							and
							c.CID = b.CID
							and c.Province = '$sess_meta'
							
							and c.CompanyTypeCode < 200 
							
						)
						$condition_sql
						
						and is_hold_letter = '0'
						";
						
						
		}elseif($sess_accesslevel == 6 || $sess_accesslevel == 7){
			
			
			//provincial staff only see its own province
			$the_sql = "
						select count(*) from 
							documentrequest j
						WHERE
							j.RID
						IN 
						(
							SELECT distinct(a.RID)
							FROM 
							documentrequest a, docrequestcompany b, company c
							where 
							a.RID = b.RID
							and
							c.CID = b.CID
							
							and c.CompanyTypeCode >= 200 and z.CompanyTypeCode < 300
							
							
						)
						$condition_sql
						
						and is_hold_letter = '0'
						";
					
		}else{
			
			
				$the_sql = "
						select count(*) from 
							documentrequest j
						WHERE
							j.RID
						IN 
						(
							SELECT distinct(a.RID)
							FROM 
							documentrequest a, docrequestcompany b, company c
							where 
							a.RID = b.RID
							and
							c.CID = b.CID
							
							and c.CompanyTypeCode < 200 
							
							
							
							
						)
						$condition_sql
						
						and is_hold_letter = '0'
						";							
		}
		
		//echo $the_sql;
		
		
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
		
		/////////////////
		
		
		if($sess_accesslevel == '3'){
		
			//provincial staff only see its own province
			$get_org_sql = "
						select * from 
							documentrequest j
						WHERE
							j.RID
						IN 
						(
							SELECT distinct(a.RID)
							FROM 
							documentrequest a, docrequestcompany b, company c
							where 
							a.RID = b.RID
							and
							c.CID = b.CID
							and c.Province = '$sess_meta'
							
							and c.CompanyTypeCode < 200 
						)
						$condition_sql
						
						and is_hold_letter = '0'
						
						order by RID asc
						
						$the_limit
						
						";
		}elseif($sess_accesslevel == 6 || $sess_accesslevel == 7){
			
			
			$the_sql = "
			
						select * from 
							documentrequest j
						WHERE
							j.RID
						IN 
						(
							SELECT distinct(a.RID)
							FROM 
							documentrequest a, docrequestcompany b, company c
							where 
							a.RID = b.RID
							and
							c.CID = b.CID
							
							and c.CompanyTypeCode >= 200 and z.CompanyTypeCode < 300
							
							
						)
						$condition_sql
						
						and is_hold_letter = '0'
						
						order by RID asc
						
						$the_limit
						
						";
					
		}else{
			
			
				$get_org_sql = "
						select * from 
							documentrequest j
						WHERE
							j.RID
						IN 
						(
							
							SELECT distinct(a.RID)
							FROM 
							documentrequest a, docrequestcompany b, company c
							where 
							a.RID = b.RID
							and
							c.CID = b.CID
							
							and c.CompanyTypeCode < 200 
							
							
						)
						$condition_sql
						
						and is_hold_letter = '0'
						
						order by RID asc 
						
						$the_limit
						";							
		}
		
		//echo $get_org_sql;
		$org_result = mysql_query($get_org_sql);
	
		//total records 
		$total_records = 0;
	
?>


<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0;"  >
                    จดหมายแจ้ง<?php echo $the_company_word;?>ทั้งหมด
                    
                    
                  </h2>
                    <br />
                    
                    <form method="post">
                    <table style=" padding:10px 0 0px 0;">
                    
                        <tr>
                          <td bgcolor="#efefef">หนังสือเลขที่:</td>
                          <td><input type="text" name="GovDocumentNo" value="<?php echo $_POST["GovDocumentNo"];?>" /></td>
                          <td bgcolor="#efefef">ครั้งที่: </td>
                          <td><input type="text" name="RequestNum" style="width:100px;" value="<?php echo $_POST["RequestNum"];?>" /></td>
                          <td bgcolor="#efefef">&nbsp;</td>
                        </tr>
                        <tr>
                          <td bgcolor="#efefef">ประจำปี:</td>
                          <td><?php include "ddl_year_with_blank.php";?></td>
                          <td bgcolor="#efefef">วันที่:</td>
                          <td><?php
											   
							   $selector_name = "RequestDate";
							   
							   
							  // echo $this_date_time;
							 
							   if($this_date_time != "0000-00-00"){
								   $this_selected_year = date("Y", strtotime($this_date_time));
								   $this_selected_month = date("m", strtotime($this_date_time));
								   $this_selected_day = date("d", strtotime($this_date_time));
							   }else{
								   $this_selected_year = 0;
								   $this_selected_month = 0;
								   $this_selected_day = 0;
							   }
							   
							   include ("date_selector.php");
							   
							   ?></td>
                          <td bgcolor="#efefef"><input type="submit" value="แสดง" name="mini_search"/></td>
                        </tr>
                        
                          <tr>
                          	<td colspan="5">
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
                                </div>                            </td>
                          </tr>
                    </table>
                    </form>
                    
                    <table border="1"  cellspacing="0" cellpadding="5" style="border-collapse:collapse; " width="100%">
                    	
                       
                        
                       
                    
                    	<tr bgcolor="#9C9A9C" align="center" >
                        	
                            <td >
                            	<div align="center"><span class="column_header">ลำดับที่</span></div>                             </td>
                            
           	  <td >
                            	<div align="center"><span class="column_header">หนังสือเลขที่</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">ครั้งที่</span> </div></td>
                      <td><div align="center"><span class="column_header">ประจำปี</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">วันที่</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">
                                
                                
                                   <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
                                   จำนวนหน่วยงาน
                                    <?php }else{?>
                                         จำนวน<?php echo $the_company_word;?>
                                    <?php }?>
                                
                                </span> </div></td>
                            
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ //exec wont see these?>
                          <td><div align="center"><span class="column_header">ลบข้อมูล</span> </div></td>
                          <?php }?>
                          
                    	</tr>
                       
                        
                        <?php
					  	
						
						$total_records = $starting_index;
						
						//main loop
						while ($post_row = mysql_fetch_array($org_result)) {
					
							$total_records++;
							
						?>     
                        <tr bgcolor="#ffffff" align="center" >
                        	
                            <td >
                              <div align="center"><a href="view_letter.php?id=<?php echo doCleanOutput($post_row["RID"]);?>"><?php echo $total_records;?></a> </div></td>
                            
                       	  <td >
<a href="view_letter.php?id=<?php echo doCleanOutput($post_row["RID"]);?>"><?php echo doCleanOutput($post_row["GovDocumentNo"]);?></a>                          </td>
                             <td>
                            	<div align="right"><?php echo doCleanOutput($post_row["RequestNum"]);?></div></td>
                                <td><div align="right"><?php echo formatYear($post_row["Year"]);?></div></td>
                                <td>
                            	<?php echo formatDateThai($post_row["RequestDate"]);?>                          </td>
                          
                          <td>
                          		<?php 
									$the_count = getFirstItem("select count(*) from docrequestcompany where RID = '".$post_row["RID"]."'");
									
									?>
                                    
                                <?php if($the_count < 1){?><font color="#FF0000"><?php }?>
                            	<div align="right"><?php echo number_format($the_count,0,".",",");?> แห่ง</div>                          
                                <?php if($the_count < 1){?></font><?php }?>                                </td>
                           
                         
                            
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ //exec wont see these?>
                          <td>
                          
                            <div align="center"><a href="scrp_delete_doc_request.php?id=<?php echo doCleanOutput($post_row["RID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div></td>
                            
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