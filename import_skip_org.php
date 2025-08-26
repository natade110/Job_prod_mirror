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




/////////
//yoes 20140107
//manage import files for skipable gov
if($_POST["upload_file_gov"]){
	
	
	$do_upload_files_gov = 1;
	
	$file_size = $_FILES["input_file"]['size'];
	$file_type = $_FILES["input_file"]['type'];
	$file_name = $_FILES["input_file"]['name'];
	$file_new_path = "to_import/".date("ymdhis").rand(00,99)."_".$file_name;
	
	
	//see if this is csv
	if(strpos($file_type,"csv") === false && strpos($file_type,"text/comma-separated-values") === false
		&& strpos($file_type,"text/plain") === false && strpos($file_type,"application/vnd.ms-excel") === false
	
	){
		echo "This is not a CSV file - you've uploaded a $file_type file.";
		exit();
	}
	
	
	if($file_size > 0){
		//echo "CSV file found, copying file to server...  $file_new_path";
		
		
		if(move_uploaded_file($_FILES["input_file"]['tmp_name'], $file_new_path)){
			
						
			//do file read operation
			$max_input_column = 2;
			
			//now read each line in csv file
			$file_handle = fopen($file_new_path, "r");
			
			$count = 0;
			
			
			$the_row_num = 0;
			
			//echo $file_new_path;
			
			$row_completed = 0;
			$row_failed = 0;
			
			while (!feof($file_handle) ) {
				
				
				//for each line in the file
				$the_row_num++;
				//echo "?";
				
				if($count == 0){
					//skip first row
					$count++;
					//continue;					
				}
				
				
				$line_of_text = fgets($file_handle);
				$line_of_text = to_utf($line_of_text);
				
				$line_of_text = str_replace("\r","",$line_of_text);
				$line_of_text = str_replace("\n","",$line_of_text);
				
				//echo "<br>".($line_of_text);
				
				//for each line of text, explode it
				$parts = explode(',', $line_of_text);
				
				
				
				if(count($parts) > $max_input_column){
				
					//failed rows
					$row_failed_to_show .= "<font color='red'><br>import row $the_row_num fail, make sure the row didn't have ','(comma) in it</font>";
					$row_failed++;
				
				}else{		
				
				
					$company_code = $parts[0];
					$company_code = doCleanInput($company_code);
					
					$branch_code = $parts[1];
					$branch_code = doCleanInput($branch_code);
					$branch_code = addLeadingZeros($branch_code,6);
					
					if(!is_numeric($company_code) || !is_numeric($branch_code)){
					
						//skip header
						//echo "?";
						continue;
					
					}else{
						
						//echo "<br>$company_code - $branch_code";
						//update these compnay/branch of this year into something else...
						$sql = "update company set CompanyTypeCode = '299' where CompanyCode = '$company_code' and BranchCode = '$branch_code'";
						//echo "<br>$sql";
						mysql_query($sql);
						
						
						//row is completed
						$row_completed++;					
						
						
					}
				
				
				}
				
				
			}
			
			
		}
		
	
	}
	
	
	
}
/////////////
////////
///////////

	
?>	



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >
                    การนำเข้าข้อมูลหน่วยงานภาครัฐ 
                    
                    
                  </h2>
                   
                   
                    <hr />
                    
                     <strong>นำเข้าข้อมูลหน่วยงานภาครัฐ & ราชการ</strong>
                     
                     <br />
                     
                    <div style="color:#CC3300; padding:20px 0;">** ข้อมูลหน่วยงานภาครัฐ จะไม่แสดงขึ้นที่รายงานของสถานประกอบการ และจะไปแสดงในฝั่งของหน่วยงานภาครัฐแทน โดยมีประเภทองค์กรเป็น 'หน่วยงานอื่นใด'</div>
                     
                    
                     
                      <form method="post" enctype="multipart/form-data">
                    <table>
                    	<tr>
                        	<td>
                            นำเข้าข้อมูลหน่วยงานภาครัฐ & ราชการ ประจำปี                            </td>
                          
                            <td>
                            <input name="input_file" type="file" /> <input name="upload_file_gov" type="submit" value="Upload File" />                            </td>
                        </tr>
                    	<tr>
                    	  <td>&nbsp;</td>
                    	 
                    	  <td>
                          
                          <a href="gov_to_remove.csv">ตัวอย่างไฟล์นำเข้า</a>
                          
                          <br />
                          - ไฟล์ csv ที่นำเข้า ห้ามมีเครื่องหมาย comma(,)
                         
                          </td>
                  	  </tr>
                   </table>
                   
                   
                   
                   <?php 
				   
				   	$count_299 = getFirstItem("select count(*) from company where CompanyTypeCode = '299'");
				   
				   	if($count_299){
				   
				   ?>
                   
                       <br />
                       รายชื่อหน่วยงานภาครัฐ & ราชการที่ถูกนำเข้าไปแล้ว และยังถูกระบุเป็น "หน่วยงานอื่นใด"
                       
                        <table border="0" cellpadding="2" cellspacing="2">
                          <tr>
                            <td align="center" bgcolor="#CCCCCC">#</td>
                            <td align="center" bgcolor="#CCCCCC">เลขที่องค์กร</td>
                            <td align="center" bgcolor="#CCCCCC">เลขที่สาขา</td>
                            <td align="center" bgcolor="#CCCCCC">ชื่อองค์กร</td>
                            <td align="center" bgcolor="#CCCCCC">ประเภทองค์กร</td>
                            <td align="center" bgcolor="#CCCCCC"></td>
                          </tr>
                          
                          <?php 
							  
							  $get_org_sql = "SELECT 
							  					*
											FROM 
												company 
											where 
												CompanyTypeCode = '299'
											
											order by CompanyNameThai asc
											
											";
								//echo $get_org_sql;
								$org_result = mysql_query($get_org_sql);
							
								//total records 
								$total_records = 0;
							
								while ($post_row = mysql_fetch_array($org_result)) {
						  
						  		$total_records += 1;
						  
						  
						  ?>
                          
                          <tr>
                            <td><?php echo $total_records;?>  </td>
                            <td><?php echo ($post_row["CompanyCode"]);?>  </td>
                            <td><?php echo addLeadingZeros($post_row["BranchCode"],6);?></td>
                            <td><?php echo $post_row["CompanyNameThai"];?> </td>
                            <td><?php echo  getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".$post_row["CompanyTypeCode"]."'");?></td>
                            <td><a href="scrp_return_gov_org.php?id=<?php echo ($post_row["CID"]);?>" 
                            	onclick="return confirm('ต้องการปรับข้อมูลองค์กรกลับคืน? ประเภทองค์กรจะถูกปรับกลับไปเป็น \'หน่วยราชการ\' และข้อมูลทั้งหมดจะกลับไปแสดงในรายงานของฝั่งสถานประกอบการ');">ปรับข้อมูลคืน</a></td>
                          </tr>
                          
                          <?php 
						  	}//end while ($post_row = mysql_fetch_array($org_result)) {
						?>
                          
                          
                        </table>
					
					
					<?php 
						}else{//end count 299
					?>
                    
                     
                    <?php }?>
                    
                    
                  
                   
                   
                    </form>
                    
                     <?php if($do_upload_files_gov){?>
                   
                       <div style="color:#003300">นำเข้าข้อมูลสำเร็จ <strong><?php echo $row_completed;?></strong> หน่วยงาน</div>
                       <div style="color:#CC3300">นำเข้าข้อมูลไม่สำเร็จ <strong><?php echo $row_failed;?></strong> หน่วยงาน</div>
                       <?php echo $row_failed_to_show;?>
                   
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