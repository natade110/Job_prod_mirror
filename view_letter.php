<?php

	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";
	
	//current mode
	if(is_numeric($_GET["id"])){
		
		$this_id = $_GET["id"];
		
		$post_row = getFirstRow("select * 
								from 
									documentrequest
								where 
									RID  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'RID' 	
						,'Year' 	
						,'RequestDate' 	
						,'GovDocumentNo' 	
						,'RequestNum' 	
						,'ModifiedDate' 	
						,'ModifiedBy' 	
						,'Cancelled'
						
						,'hold_details',
						
						);
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			$output_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
		}
		
		//print_r($output_values);
		
	}else{
		header("location: index.php");
	}	


	if($_GET["type"] == "hold"){
	
		$type = "hold";
	
	}


?>
<?php include "header_html.php";?>
              <td valign="top">
                	
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                    
                <?php if($type == "hold"){?>การแจ้งอายัด<?php }else{?>จดหมายแจ้ง<?php echo $the_company_word;?><?php }?>ครั้งที่: <font color="#006699"><?php echo $output_values["RequestNum"];?></font> หนังสือเลขที่: <font color="#006699"><?php echo $output_values["GovDocumentNo"];?></font></h2>
                    
                    <div style="padding:5px 0 10px 2px"><a href="letter_list.php"><?php if($type == "hold"){?>การแจ้งอายัด<?php }else{?>จดหมายแจ้ง<?php echo $the_company_word;?><?php }?>ทั้งหมด</a> > หนังสือเลขที่: <?php echo $output_values["GovDocumentNo"];?></div>
                    
                   <form action="scrp_update_letter.php" method="post" onsubmit="return validateLetterForm(this); ">
                    <?php 
						if($_GET["updated"]=="updated"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลจดหมายแจ้งเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["existed"]=="existed"){
					?>							
                         <div style="color:#990000; padding:5px 0 0 0; font-weight: bold;">* <a href="view_letter.php?id=<?php echo $_GET["doc_id"];?>">หนังสือเลขที่ <?php echo $_GET["doc_no"]?> ครั้งที่ <?php echo $_GET["doc_seq"]?></a> มีอยู่ในระบบแล้ว</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["letter_added"]=="letter_added"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่่มข้อมูลจดหมายแจ้งเสร็จสิ้น</div>
                    <?php
						}					
					?>
                   <table style=" padding:10px 0 0px 0;">
                     <tr>
                    	  <td bgcolor="#efefef">ประจำปี: </td>
                    	  <td colspan="5"><?php include "ddl_year.php";?></td>
                   	  </tr>
                     <tr>
                       <td bgcolor="#efefef">วันที่: </td>
                       <td>
                       <?php
											   
					   $selector_name = "RequestDate";
					   
					   $this_date_time = $output_values["RequestDate"];
					 
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
					   
					   ?>                                              </td>
                       <td bgcolor="#efefef"> ครั้งที่: </td>
                       <td>
                       <input name="RequestNum" type="text" id="RequestNum" value="<?php echo ($output_values["RequestNum"]);?>" />
                       <input name="RequestNum_old" type="hidden"  value="<?php echo ($output_values["RequestNum"]);?>" />                       </td>
                       <td bgcolor="#efefef">หนังสือเลขที่: </td>
                       <td>
                       <input name="GovDocumentNo" type="text" id="GovDocumentNo" value="<?php echo ($output_values["GovDocumentNo"]);?>" />                       <input name="GovDocumentNo_old" type="hidden"  value="<?php echo ($output_values["GovDocumentNo"]);?>" />                       </td>
                     </tr>
                     
                     
                     <?php if($type == "hold"){?>
                     <tr>
                       <td bgcolor="#efefef">รายละเอียด:</td>
                       <td colspan="5"><textarea name="hold_details" cols="50" rows="5" id="hold_details"><?php echo $output_values["hold_details"];?></textarea> <input name="is_hold_letter" type="hidden" value="1" /></td>
                     </tr>
                     
                     <?php }?>
                     
                     <tr>
                       <td colspan="6" ><div align="right">
                         <input name="RID" type="hidden" value="<?php echo $output_values["RID"];?>" />
                         
                         <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){?>
                         <input type="submit" name="button" id="button" value="อัพเดทข้อมูล" />
                         <?php }?>
                         
                       </div></td>
                     </tr>
                   </table>
                </form>
                <script language='javascript'>
					<!--
					function validateLetterForm(frm) {
						
						
						if(frm.RequestNum.value.length ==0)
						{
							alert("กรุณาใส่ข้อมูล: ครั้งที่");
							frm.RequestNum.focus();
							return (false);
						}
						if(frm.GovDocumentNo.value.length == 0)
						{
							alert("กรุณาใส่ข้อมูล: หนังสือเลขที่");
							frm.GovDocumentNo.focus();
							return (false);
						}
						
						return confirm('คุณแน่ใจหรือว่าต้้องการอัพเดทข้อมูลจดหมายแจ้งฉบับนี้?');									
					
					}
					-->
				
				</script>
                   
                   <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >
                    <?php if($type == "hold"){?>
                    	<?php echo $the_company_word;?>ในการแจ้งอายัด
					<?php }else{?>
                    	<?php echo $the_company_word;?>ในจดหมายแจ้ง
					<?php }?>
                </h2>
                <?php 
						if($_GET["delletter"]=="delletter"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* จดหมายแจ้งได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    <div style="padding:10px 0 10px 0" ><a href="export_letter.php?id=<?php echo $this_id;?>">+ export ข้อมูลเป็น excel</a></div>
                    <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                        	 <td >
                           	<div align="center"><span class="column_header">ลำดับ</span>                       	        </div></td>
   	 				  <td >
                           	<div align="center"><span class="column_header">รหัส</span>                       	        </div></td>
                            
                      <td>
                           	<div align="center"><span class="column_header">ชื่อ<?php echo $the_company_word;?></span>                       	        </div></td>
                           
                      <td>
                           	<div align="center"><span class="column_header">สถานะ</span>                       	        </div></td>
                      <td>
                           	<div align="center"><span class="column_header">เลขที่ลงทะเบียน</span>                       	        </div></td>
                            
                       <?php if($type != "hold"){?>
                      <td>
                           	<div align="center"><span class="column_header">จพ 0-1</span>                       	        </div></td>
                      <td>
                           	<div align="center"><span class="column_header">จพ 0-2</span>                       	        </div></td>
                      <td>
                           	<div align="center"><span class="column_header">จพ 0-3</span>                       	        </div></td>
                           
                      <td>
                           	<div align="center"><span class="column_header">จพ 1-1</span>                       	        </div></td>
                      <td>
                           	<div align="center"><span class="column_header">จพ 1-2</span>                       	        </div></td>
                      <td>
                           	<div align="center"><span class="column_header">จพ 1-3</span>                       	        </div></td>
                         <?php }?>
                            
							<?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){?>
                          <td>
                       	  <div align="center"><span class="column_header">ลบข้อมูล</span>                       	        </div></td>
                          <?php }?>
                          
                      </tr>
                        <?php
					
						
						
						$cur_year = $output_values["Year"];
						
						
						$get_org_sql = "SELECT *, b.CID as companyid, y.LawfulStatus as lawfulness_status
										FROM 
											docrequestcompany a
											, company b LEFT outer JOIN lawfulness y ON b.CID = y.CID and y.Year = '$cur_year' 
										where 
										a.CID = b.CID
										and 
										a.RID ='$this_id'
										order by CompanyNameThai asc
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
                           	<?php echo $total_records;?>                          </td>
                            
                       	  <td >
                           	
                            
                            <?php
							
							if($_SESSION['sess_accesslevel'] == 3 && $_SESSION['sess_meta'] == $post_row["Province"]){
							
							?>
                           		<a href="organization.php?id=<?php echo doCleanOutput($post_row["companyid"]);?>&focus=official&year=<?php echo doCleanOutput($output_values["Year"]);?>"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>                        
                            <?php }else{ ?>
                            	<?php echo doCleanOutput($post_row["CompanyCode"]);?>
                            
                            <?php } ?>
                            
                             </td>
                          
                            <td>
                            	<?php echo doCleanOutput(formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]));?></td>
                          
                           <td>
                            	<div align="center"><?php echo getLawfulImage(($post_row["lawfulness_status"]));?></div>                         </td>
                                
                     
                         <td>
                                <?php echo doCleanOutput($post_row["PostRegNum"]);?></td>
                                
                         
                         <?php if($type != "hold"){?>
                         
                             <td>
                                    <?php if($post_row["DocBKK1"]=="1"){ ?><div align="center"><img src="decors/checked.gif" /></div><?php } ?>                            </td>
                                <td>
                                    <?php if($post_row["DocBKK2"]=="1"){ ?><div align="center"><img src="decors/checked.gif" /></div><?php } ?>                            </td>
                                <td>
                                    <?php if($post_row["DocBKK3"]=="1"){ ?><div align="center"><img src="decors/checked.gif" /></div><?php } ?>                            </td>
                                
                                <td>
                                    <?php if($post_row["DocPro1"]=="1"){ ?><div align="center"><img src="decors/checked.gif" /></div><?php } ?>                            </td>
                                <td>
                                    <?php if($post_row["DocPro2"]=="1"){ ?><div align="center"><img src="decors/checked.gif" /></div><?php } ?>                            </td>
                                <td>
                                    <?php if($post_row["DocPro3"]=="1"){ ?><div align="center"><img src="decors/checked.gif" /></div><?php } ?>                            </td>
                            
                            
                            <?php }?>
                            
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){?>
                            <td>
                            	<div align="center"><a href="scrp_delete_doccom.php?id=<?php echo doCleanOutput($post_row["DID"]);?>&rid=<?php echo doCleanOutput($post_row["RID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div></td>
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
