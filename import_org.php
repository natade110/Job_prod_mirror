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



//manage import files
if($_POST["upload_file"]){

	$do_upload_files = 1;
	
	$file_size = $_FILES["input_file"]['size'];
	$file_type = $_FILES["input_file"]['type'];
	$file_name = $_FILES["input_file"]['name'];
	$file_new_path = "./to_import/".date("ymdhis").rand(00,99)."_".$file_name;
	
	//echo $file_new_path;
	
	
	if(strpos($file_type,"csv") === false && strpos($file_type,"text/comma-separated-values") === false
		&& strpos($file_type,"text/plain") === false && strpos($file_type,"application/vnd.ms-excel") === false
	
	){
		echo "This is not a CSV file - you've uploaded a $file_type file.";
		exit();
	}
	
	if($file_size > 0){
		//echo "CSV file found, copying file to server...  $file_new_path";
		
		
		if(move_uploaded_file($_FILES["input_file"]['tmp_name'], $file_new_path)){
		
			//echo "<br>CSV file copied";

			$input_fields = array(
				
				"CompanyCode"
				,"BranchCode"
				,"CompanyTypeCode"
				,"CompanyNameThai"
				
				,"Address1"
				,"Subdistrict"
				,"District"
				,"Province"
				
				,"Zip"
				,"Telephone"
				,"BusinessTypeCode"
				,"Employees"
			
			);
			
			//
			$selected_year = $_POST["ddl_year"];
			
			$max_input_column = count($input_fields);
			
			//now read each line in csv file
			$file_handle = fopen($file_new_path, "r");
			
			$count = 0;
			
			
			$the_row_num = 0;
			
			//echo $file_new_path;
			
			$row_completed = 0;
			$row_failed = 0;
			
			while (!feof($file_handle) ) {
			
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
				
					$row_failed_to_show .= "<font color='red'><br>import row $the_row_num fail, make sure the row didn't have ','(comma) in it</font>";
					$row_failed++;
				
				}else{		
				
				
					$company_code = $parts[array_search('CompanyCode', $input_fields)];
					$company_code = doCleanInput($company_code);
					
					if(!is_numeric($company_code)){
					
						//skip header
						//echo "?";
						continue;
					
					}else{
					
						//prepare SQLs
						
						
						$fields_to_use = "";
						$values_to_use = "";
						for($i = 0; $i < count($input_fields); $i++){
							
							$fields_to_use .= ",".$input_fields[$i];
							
							//cleanup Province, CompanyTypeCode,BusinessTypeCode, BranchCode							
							if($input_fields[$i] == "CompanyTypeCode"){
								
								$values_to_use .= ",'".doCleanInput(addLeadingZeros($parts[$i],2))."'";								
								$update_sql .= ", $input_fields[$i] = '".doCleanInput(addLeadingZeros($parts[$i],2))."'";
								
							}elseif($input_fields[$i] == "BusinessTypeCode" && strlen($parts[$i]) > 0){
								
								$values_to_use .= ",'".doCleanInput(addLeadingZeros($parts[$i],4))."'";
								$update_sql .= ", $input_fields[$i] = '".doCleanInput(addLeadingZeros($parts[$i],4))."'";
								
							}elseif($input_fields[$i] == "BranchCode"){
								
								$values_to_use .= ",'".doCleanInput(addLeadingZeros($parts[$i],6))."'";
								$this_branch_code = doCleanInput(addLeadingZeros($parts[$i],6));
								$update_sql .= ", $input_fields[$i] = '".doCleanInput(addLeadingZeros($parts[$i],6))."'";
								
							}elseif($input_fields[$i] == "CompanyCode"){
								
								$values_to_use .= ",'".doCleanInput($parts[$i])."'";
								$this_company_code = doCleanInput($parts[$i]);
								$update_sql .= ", $input_fields[$i] = '".doCleanInput($parts[$i])."'";
								
							}elseif($input_fields[$i] == "Province"){
							
								$the_province_id = getFirstItem("select province_id from provinces where province_name like '%".trim(doCleanInput($parts[$i]))."%'");							
								$values_to_use .= ",'".$the_province_id."'";
								$update_sql .= ", $input_fields[$i] = '$the_province_id'";
								
							}else{						
								
								$values_to_use .= ",'".doCleanInput($parts[$i])."'";
								$update_sql .= ", $input_fields[$i] = '".doCleanInput($parts[$i])."'";
								
							}
						
						}
												
						
						//echo "<br>$company_code";
						//do stuffs with company sso
						$sql = "
						
						
							replace into companysso(
							
								DataYear
								,LastModifiedDateTime
								$fields_to_use
							)values(
							
								'$selected_year'
								,now()
								
								$values_to_use
														
							)
						
						";
						
						//echo "<br>".$sql;//exit();
						
						mysql_query($sql) or die(mysql_error());								
											
						//echo "<br>$company_code";
						//do stuffs with company
						
						//we cant use replace here as it'll update our PK!
						
						//echo "select CID from company where CompanyCode = '$this_company_code' and BranchCode = '$this_branch_code'";
						$company_id  = getFirstItem("select CID from company where CompanyCode = '$this_company_code' and BranchCode = '$this_branch_code'");
						
						//echo $company_id;
						
						//replace all se-mi colons in to comma
						$values_to_use = str_replace(";",",",$values_to_use);
						$update_sql = str_replace(";",",",$update_sql);
						
						if(!$company_id){
						
							//dint have compnay => do insert
							$sql = "
							
							
								insert into company(
								
									LastModifiedDateTime
									, LastModifiedBy
									, CreatedDateTime
									, CreatedBy
									
									
									$fields_to_use
								)values(
								
									now()
									, '$sess_userid'
									, now()
									, '$sess_userid'
									
									
									$values_to_use
								
								
								)
							
							";
						}else{
						
						
							//have company -> do update
							
							$sql = "
							
							
								update company set
								
									LastModifiedDateTime = now()
									
									, LastModifiedBy = '$sess_userid'
									
									
									$update_sql
								
								where
								CID = '$company_id'
							
							";
							
						
						}		
						//echo "<br>".$sql;
						
						mysql_query($sql) or die(mysql_error());
						
						//get last inserted id if not have already
						if(!$company_id){
							$company_id = mysql_insert_id();
						}
						
						//try create lawfulness records....
						$lawful_count = getFirstItem("select count(*) from lawfulness where CID = '$company_id' and Year = '$selected_year'");
						
						if($lawful_count){
							//do nothing
							//echo "<br>found lawful record for year";
						}else{
							
							
							//add new lawfulness
							//echo "<br>not found lawful record for year";
							
							$sql = "insert into lawfulness(CID, Year) values('$company_id','$selected_year')";
							mysql_query($sql) or die(mysql_error());
						}
						
						$row_completed++;
					
					}
				
				
				}
			
			}				
			
			
		}
		
	}
	
}	
	
?>	



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม และ กรมการจัดหางาน) </h2>
                   
                    
                   

                    <strong>1.1. ข้อมูลจากประกันสังคม</strong>
                    
                  
                    <form method="post" enctype="multipart/form-data">
                    <table>
                    	<tr>
                        	<td>
                            นำเข้าข้อมูลประจำปี                            </td>
                            <td>
                            <?php include "ddl_year_plus_ten.php";?>                            </td>
                            <td>
                            <input name="input_file" type="file" /> <input name="upload_file" type="submit" value="Upload File" />                            </td>
                        </tr>
                    	<tr>
                    	  <td>&nbsp;</td>
                    	  <td>&nbsp;</td>
                    	  <td>
                          
                          <a href="to_import/to_import_2013.csv">ตัวอย่างไฟล์นำเข้า</a>
                          
                          <br />
                          - ไฟล์ csv ที่นำเข้า ห้ามมีเครื่องหมาย comma(,)
                          <br />
                          - ให้ replace เครื่องหมาย comma ทั้งหมดเป็น semi-colon(;)
                          <br />
                          - หลังจากนำเข้าไฟล์แล้ว ระบบจะเปลี่ยนเครื่องหมาย  semi-colon เป็น comma ให้อัตโนมัติ
                          
                          </td>
                  	  </tr>
                   </table>
                  
                   <a href=""></a>
                   
                    </form>
                   
                   <?php if($do_upload_files){?>
                   
                       <div style="color:#003300">นำเข้าข้อมูลสำเร็จ <strong><?php echo $row_completed;?></strong> สถานประกอบการ</div>
                       <div style="color:#CC3300">นำเข้าข้อมูลไม่สำเร็จ <strong><?php echo $row_failed;?></strong> สถานประกอบการ</div>
                       <?php echo $row_failed_to_show;?>
                   
                   <?php }?>
                   
                   
                   <br />
					<br />
                   <strong>1.2. ข้อมูลจากสำนักงานจัดหางาน</strong>
                   
                   <form method="post" enctype="multipart/form-data">
                    <table>
                    	<tr>
                        	<td>
                            นำเข้าข้อมูลประจำปี                            </td>
                            <td>
                            <?php include "ddl_year_plus_ten.php";?>                            </td>
                            <td>
                            <input name="input_file" type="file" /> <input name="upload_file" type="submit" value="Upload File" />                            </td>
                        </tr>
                    	<tr>
                    	  <td>&nbsp;</td>
                    	  <td>&nbsp;</td>
                    	  <td>
                          
                          <a href="to_import/to_import_2013.csv">ตัวอย่างไฟล์นำเข้า</a>
                          
                          <br />
                          - ไฟล์ csv ที่นำเข้า ห้ามมีเครื่องหมาย comma(,)
                          <br />
                          - ให้ replace เครื่องหมาย comma ทั้งหมดเป็น semi-colon(;)
                          <br />
                          - หลังจากนำเข้าไฟล์แล้ว ระบบจะเปลี่ยนเครื่องหมาย  semi-colon เป็น comma ให้อัตโนมัติ
                          
                          </td>
                  	  </tr>
                   </table>
                  
                   <a href=""></a>
                   
                    </form>
                   
                   
                    
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