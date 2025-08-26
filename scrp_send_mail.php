<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	
	//print_r($_POST);
	$post_records = $_POST["total_records"]*1;
	
	//is preview
	$is_preview = $_POST["is_preview"];
	
	//echo $is_preview;	
	//echo $post_records;
	
	$txt_subject = ($_POST["txt_subject"]);
	$txt_body = ($_POST["txt_body"]);
	
	
	
	
		

	header('Content-Type: text/html; charset=utf-8');

		?>
		
		
			<div align="center">
			
            	<strong>
                
                
                <?php if($is_preview){?>
                	Preview Email
                <?php }else{ ?>
                	ส่ง email ให้สถานประกอบการ
                <?php }?>
                 </strong>
                 
                 <br />
                 <br />
                
                
                <?php 
				
				
				if(!$is_preview){
						
						
						//really sending out emails
						for($i=1 ; $i<=$post_records ; $i++){
							if($_POST["chk_$i"]){
								
								$mail_sent = 1;
								
								//sending out mail for this company
								$selected_company = $_POST["chk_$i"];
								
								
								//get email
								$the_row = getFirstRow("select CompanyNameThai, CompanyTypeCode, email from company where CID = '$selected_company'");
								
								$the_email = $the_row["email"];
								
								//get company name in format
								$company_name = $the_row["CompanyNameThai"];
								$company_type = $the_row["CompanyTypeCode"];
								$the_name_to_use = formatCompanyName($company_name, $company_type);
								
								//echo "select email from company where CID = '$selected_company'";
								
								//echo $the_email; exit();
								
								//replace body
								//$txt_body = str_replace("**company**", $the_name_to_use, $txt_body);
								
								//
								$txt_body = "เรียน $the_name_to_use \r\n\r\n" . $txt_body; 
								
								mail($the_email, $txt_subject, $txt_body);
								echo "<br><span style='color:#060'>ส่งอีเมล์ถึง $the_name_to_use - $the_email แล้ว</span>";
								
								//echo $selected_company;
								
							}
						}
						
				}
				
				?>
                
                
                
                
                
                
               
            	
                <br />
                <br />
            
				<table border="1" style="border-collapse: collapse; border: 1px solid #666;" cellpadding="5" width="900">
					<tr>
						<td valign="top">
						หัวข้อ email:
						</td>
						<td>
						<?php echo $txt_subject; ?>
						</td>
					</tr>
					<tr>
						<td valign="top">
						เนื้อหา email:
						</td>
						<td>
						<?php 
						
						
						//$txt_body = $txt_body/"เรียน $the_name_to_use /r/n/r/n";
						
						if(!$mail_sent){
							$txt_body = "เรียน สถานประกอบการ \r\n\r\n" . $txt_body; 
						}
						
						echo nl2br(str_replace(" ","&nbsp;&nbsp;&nbsp;",$txt_body));
						
						 ?>
						</td>
					</tr>
				</table>
			
			</div>
		
		
		<?php	
	
		
	

?>