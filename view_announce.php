<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	//current mode
	if(is_numeric($_GET["id"])){
		
		$this_id = $_GET["id"];
		
		$post_row = getFirstRow("select * 
								from 
									announcement
								where 
									AID  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'AID'
						,'ADate'
						,'ANum'
						,'GovDocNo'
						,'newspaper_id'
						,'Topic'
						,'NewspaperDate'
						,'Cancelled'
						
						);
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			$output_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
		}
		
	}else{
		header("location: index.php");
	}	

?>
<?php 
	include "header_html.php";
	include "global.js.php";
?>
              <td valign="top">
                	
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                    
                ประกาศผ่านสื่อเลขที่ <font color="#006699"><?php echo $output_values["GovDocNo"];?></font> </h2>
                    
                    <div style="padding:5px 0 0px 2px"><a href="announce_list.php">การประกาศผ่านสื่อทั้งหมด</a> > หนังสือเลขที่: <?php echo $output_values["GovDocNo"];?></div>
                    
                    
                    <?php 
						if($_GET["company_added"]=="company_added"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มสถานประกอบการเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["updated"]=="updated"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลการจ่ายเงินเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["delannounce"]=="delannounce"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ลบข้อมูลการการประกาศผ่านสื่อเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <form method="post" action="scrp_update_announcement.php" enctype="multipart/form-data">
                    <input name="announcement_id" type="hidden" value="<?php echo $this_id;?>" />
                    <table border="0" cellpadding="0">
                      <tr>
                        <td><table border="0" style="padding:10px 0 0 50px;" >
                            <tr>
                              <td><span style="font-weight: bold">การประกาศผ่านสื่อ</span></td>
                              <td>&nbsp;</td>
                              <td>&nbsp;</td>
                              <td>&nbsp;</td>
                            </tr>
                            <tr>
                              <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่หนังสือประกาศ</span></td>
                              <td><span class="style86" style="padding: 10px 0 10px 0;">
                                <input name="GovDocNo" type="text" id="GovDocNo" value="<?php echo $output_values["GovDocNo"];?>" />
                              </span></td>
                              <td>&nbsp;</td>
                              <td>&nbsp;</td>
                            </tr>
                           <!--
                            <tr>
                              <td><span class="style86" style="padding: 10px 0 10px 0;">วันที่ประกาศ</span></td>
                              <td><span class="style86" style="padding: 10px 0 10px 0;">
                                <?php
											   
											   $selector_name = "announce_date";
											   $this_date_time = $output_values["ADate"];
											   
											   include ("date_selector.php");
											   
											   ?>
                              </span></td>
                              <td>ครั่งที่</td>
                              <td><span class="style86" style="padding: 10px 0 10px 0;">
                                <input name="ANum" type="text" id="ANum" value="<?php echo $output_values["ANum"];?>" size="10"  />
                              </span></td>
                            </tr>
                            <tr>
                              <td><span class="style86" style="padding: 10px 0 10px 0;">สื่อสิ่งพิมพ์</span></td>
                              <td><?php include "ddl_newspaper.php";?></td>
                              <td><span class="style86" style="padding: 10px 0 10px 0;">ฉบับวันที่</span></td>
                              <td><span class="style86" style="padding: 10px 0 10px 0;">
                                <?php
											   
											   $selector_name = "news_date";
											   $this_date_time = $output_values["NewspaperDate"];
											   
											   include ("date_selector.php");
											   
											   ?>
                              </span></td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              <td>&nbsp;</td>
                              <td>สถานะ</td>
                              <td><label>
                                <select name="Cancelled" id="Cancelled">
                                  <option value="0">ประกาศผ่านสื่อ</option>
                                  <option value="1" <?php if($output_values["Cancelled"] == "1"){echo "selected='selected'";} ?>>ยกเลิกการประกาศ</option>
                                </select>
                              </label></td>
                            </tr>
                            -->
                            
                            <tr>
                              <td valign="top">รายละเอียด</td>
                              <td colspan="3"><label>
                                <textarea name="Topic" cols="50" rows="4" id="Topic"><?php echo $output_values["Topic"];?></textarea>
                              </label></td>
                            </tr>
                            <tr>
                              <td>เอกสารประกอบ</td>
                              <td colspan="3"><?php 
                                                    
                                                    $file_type = "announce_docfile";
                                                	
                                                    include "doc_file_links.php";
                                                ?> 
                                  <input type="file" name="announce_docfile" id="announce_docfile" /></td>
                            </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td><hr />
                            <div align="center">
                            <?php if($sess_accesslevel!=5){?> 
                              <input type="submit" value="เพิ่มข้อมูล" />
                              <?php }?>
                          </div><hr /></td>
                      </tr>
                    </table>
                    <script>
									
														
							function doToggleMethod(){
							
								the_method = document.getElementById("PaymentMethod").value;
							
								document.getElementById("cash_table").style.display = "none";
								document.getElementById("cheque_table").style.display = "none";
								document.getElementById("note_table").style.display = "none";
								
								if(the_method == "Cash"){
									//document.getElementById("cash_table").style.display = "";
								}else if(the_method == "Cheque"){
									document.getElementById("cheque_table").style.display = "";
								}else if(the_method == "Note"){
									document.getElementById("note_table").style.display = "";
								}
							}	
							
							doToggleMethod();							
							
						
						function alertContents() {
							if (http_request.readyState == 4) {
								if (http_request.status == 200) {
									//alert(http_request.responseText.trim()); 
									document.getElementById("loading_"+http_request.responseText.trim()).style.display = 'none';
								} else {
									//alert('There was a problem with the request.');
								}
							}
						}
							
						</script>
                        
                </form>
                    
                    
                    <div style="padding-bottom:10px">
                   <strong>
                    
                สถานประกอบการประกาศผ่านสื่อ</strong><br />
                
                <?php if($sess_accesslevel!=5){?> 
                	<a href="org_list.php?mode=add_company_announce&id=<?php echo $this_id;?>">+ เพิ่มข้อมูลสถานประกอบการเข้าไปในการประกาศผ่านสื่อนี้</a>
                <?php }?>
                
                
                <br />
                <a href="export_announce.php?id=<?php echo $this_id;?>">+ export ข้อมูลเป็น excel</a>
                </div>
                <?php 
						if($_GET["delletter"]=="delletter"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    
                    <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                        	
           	 				 <td >
                           	<div align="center"><span class="column_header">เลขที่บัญชีนายจ้าง</span>                       	        </div></td>
                            
                             <td>
                           	<div align="center"><span class="column_header">ชื่อนายจ้างหรือสถานประกอบการ</span>                       	        </div></td>
                           
                            <td>
                           	<div align="center"><span class="column_header">สถานะ</span>                       	        </div></td>
                            
                            
                            <?php if($sess_accesslevel!=5){?> 
                          <td>
                       	  <div align="center"><span class="column_header">ลบข้อมูล</span>                       	        </div></td>
                          <?php }?>
                          
                          
                        </tr>
                        <?php
					
						
						
						
						$get_org_sql = "SELECT *, b.CID as companyid
										FROM announcecomp a, company b
										where 
										
										
										a.CID = b.CID
										and 
										a.AID ='$this_id'
										
										";
						//echo $get_org_sql;
						$org_result = mysql_query($get_org_sql);
					
						//total records 
						$total_records = 0;
					
						while ($post_row = mysql_fetch_array($org_result)) {
					
							$total_records++;
							
						?>     
                        <tr bgcolor="#ffffff" align="center" >
                        	
                       	  <td >
                           	                         
                            
                            <?php
							
							if($_SESSION['sess_accesslevel'] == 3 && $_SESSION['sess_meta'] == $post_row["Province"]){
							
							?>
                           		<a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>&focus=official"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>                         
                            <?php }else{ ?>
                            	<?php echo doCleanOutput($post_row["CompanyCode"]);?>
                            
                            <?php } ?>
                            
                            
                            </td>
                          
                            <td>
                            	<?php echo formatCompanyName(doCleanOutput($post_row["CompanyNameThai"]),doCleanOutput($post_row["CompanyTypeCode"]));?></td>
                          
                           <td>
                            	<div align="center"><?php echo getLawfulImage(($post_row["LawfulFlag"]));?></div>                         </td>
                                
                                
                            <?php if($sess_accesslevel!=5){?> 
                            <td>
                            	<div align="center"><a href="scrp_delete_announcecom.php?id=<?php echo doCleanOutput($post_row["ACID"]);?>&aid=<?php echo $this_id;?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div></td>
                                
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

</body>
</html>
