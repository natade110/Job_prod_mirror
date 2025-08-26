<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	//current mode
	if(is_numeric($_GET["id"])){
		
		$this_id = $_GET["id"];
		
		$post_row = getFirstRow("select * 
								from 
									receipt
								where 
									RID  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						
						'BookReceiptNo'
						,'ReceiptNo'
						,'PaymentMethod'
						,'Amount'
						,'ReceiptNote'
						,'ReceiptDate'
						,'ReceiptYear'
						
						,'is_payback'
						
						);
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			$output_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
		}
		
		//
		if($output_values["is_payback"]){
			
			$mode = "is_payback";
		
		}
		
		//print_r($output_values);
		$payment_row = getFirstRow("select * from payment where RID = '$this_id' limit 0,1");
		$this_ref_number = $payment_row["RefNo"];
		$this_bank_id = $payment_row["bank_id"];
		
		
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
                    
                <?php if($mode == "is_payback"){?>
                 เลขที่หนังสือ
                <?php }else{?>
               	 ใบเสร็จเล่มที่ 
                <?php }?>
                
                <font color="#006699"><?php echo $output_values["BookReceiptNo"];?></font>
              
              
              <?php if($mode == "is_payback"){?>
                 ลงวันที่
                <?php }else{?>
               	  ใบเสร็จเลขที่ 
                <?php }?>
                
                <font color="#006699"><?php echo $output_values["ReceiptNo"];?></font>
                </h2>
                    
                    <div style="padding:5px 0 10px 2px"><a href="payment_list.php">ใบเสร็จรับเิงินทั้งหมด</a> >  ใบเสร็จเล่มที่ <?php echo $output_values["BookReceiptNo"];?> ใบเสร็จเลขที่ <?php echo $output_values["ReceiptNo"];?></div>
                    
                    <div style="padding-bottom:10px">
                   <strong>
                    
                    
                  
                สถานประกอบการในใบเสร็จรับเงิน</strong><br />
                 <?php if($sess_accesslevel!=5){?> 
                <a href="org_list.php?mode=add_company_payment&id=<?php echo $this_id;?>">+ เพิ่มข้อมูลสถานประกอบการเข้าไปในใบเสร็จรับเงินนี้</a>
                <?php }?>
                </div>
                <?php 
						if($_GET["delletter"]=="delletter"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* จดหมายแจ้งได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    
                    <form method="post" action="scrp_update_receipt.php" enctype="multipart/form-data" <?php if($sess_accesslevel !=4){?>onsubmit="return validatePaymentForm(this);"<?php }?>>
                    
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
                          
                          <td width="50"><div align="center"><span class="column_header">สถานประกอบการหลัก</span>                       	        </div></td>
                   	  </tr>
                        <?php
					
						
						$cur_year = $output_values["ReceiptYear"];
						
						$get_org_sql = "SELECT *, b.CID as companyid, c.LawfulStatus as lawfulness_status
										FROM payment a, company b, lawfulness c
										where 
										a.LID = c.LID
										and
										c.CID = b.CID
										and 
										a.RID ='$this_id'
										
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
							
							if(($_SESSION['sess_accesslevel'] == 3 && $_SESSION['sess_meta'] == $post_row["Province"]) || $_SESSION['sess_accesslevel'] == 1 || $_SESSION['sess_accesslevel'] == 2  ){
							
							?>
                           		<a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>&focus=lawful&year=<?php echo $output_values["ReceiptYear"];?>&auto_post=1"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>                          
                            <?php }else{ ?>
                            	<?php echo doCleanOutput($post_row["CompanyCode"]);?>
                            
                            <?php } ?>
                            
                            
                            </td>
                          
                            <td>
                            	
								
								<?php echo doCleanOutput($post_row["CompanyNameThai"]);?>
                                
                                
                                
                                </td>
                          
                           <td>
                            	<div align="center"><?php echo getLawfulImage(($post_row["lawfulness_status"]));?></div>                         </td>
                                
                                
                              <?php if($sess_accesslevel!=5){?> 
                            <td>
                            	<div align="center"><a href="scrp_delete_paycom.php?id=<?php echo doCleanOutput($post_row["PID"]);?>&rid=<?php echo $this_id;?>&cid=<?php echo doCleanOutput($post_row["CID"]);?>&year=<?php echo $output_values["ReceiptYear"];?>&is_main=<?php echo doCleanOutput($post_row["main_flag"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div></td>
                             <?php }?>
                                
                                
                                
                            <td><div align="center">
                              <input name="main_flag_pid" type="radio" value="<?php echo doCleanOutput($post_row["PID"]);?>" <?php if($post_row["main_flag"]){?>checked="checked"<?php }?>  />
                              
                          </div></td>
                        </tr>
                        <?php } //end loop to generate rows?>
				  </table>            
                   
                   
<?php 
						if($_GET["payment_added"]=="payment_added"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลการจ่ายเงินเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["duped_key"]=="duped_key"){
					?>							
                         <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <a href="view_payment.php?id=<?php echo $_GET["pay_id"]?>">ใบเสร็จเล่มที่ <?php echo $_GET["book_num"]?> ใบเสร็จเลขที่ <?php echo $_GET["pay_num"]?></a> มีอยู่ในระบบแล้ว กรุณาใส่เล่มที่/ใบเสร็จเลขที่ใหม่</div>
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
						if($_GET["delpayment"]=="delpayment"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ลบข้อมูลการจ่ายเงินเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    
                    <?php if($sess_accesslevel !=4){?>
                        <script>
							<!--
							function validatePaymentForm(frm) {
								
								
								if(frm.BookReceiptNo.value.length ==0)
								{
									alert("กรุณาใส่ข้อมูล: เล่มที่");
									frm.BookReceiptNo.focus();
									return (false);
								}
								if(frm.ReceiptNo.value.length == 0)
								{
									alert("กรุณาใส่ข้อมูล: ใบเสร็จเลขที่");
									frm.ReceiptNo.focus();
									return (false);
								}
								
								return(true);									
							
							}
						</script>
                        <?php } ?>
                    
                    <input name="receipt_id" type="hidden" value="<?php echo $this_id;?>" />
                      <table border="0" cellpadding="0">
                        <tr>
                          <td><table border="0" style="padding:10px 0 0 50px;" >
                              <tr>
                                <td><span style="font-weight: bold">ข้อมูลใบเสร็จ</span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>สำหรับปี</td>
                                <td><?php include "ddl_year.php";?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                
                                <?php if($mode == "is_payback"){?>
                                เลขที่หนังสือ
                                <?php }else{?>
                                  ใบเสร็จเล่มที่ 
                                <?php }?>
                                
                                </span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="BookReceiptNo" type="text" id="BookReceiptNo" value="<?php echo $output_values["BookReceiptNo"];?>"  />
                                  <input name="oldBookReceiptNo" type="hidden" value="<?php echo $output_values["BookReceiptNo"];?>"  />
                                </span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                
                                <?php if($mode == "is_payback"){?>
                               ลงวันที่
                                <?php }else{?>
                                  ใบเสร็จเลขที่ 
                                <?php }?>
                                
                                </span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="ReceiptNo" type="text" id="ReceiptNo" value="<?php echo $output_values["ReceiptNo"];?>"  />
                                  <input name="oldReceiptNo" type="hidden"  value="<?php echo $output_values["ReceiptNo"];?>"  />
                                </span></td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">วันที่จ่าย</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php
											   
											   $selector_name = "the_date";
											   $this_date_time = $output_values["ReceiptDate"];
											   
											   include ("date_selector.php");
											   
											   ?>
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จำนวนเงิน</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="Amount" type="text" id="Amount" style="text-align:right;" value="<?php echo formatNumber(default_value($output_values["Amount"],0));?>" onchange="addCommas('Amount');" />
                                  <?php
								  	
									include "js_format_currency.php";
								  
								  ?>
                                  <span class="style86" style="padding: 10px 0 10px 0;">บาท</span></span></span></td>
                                <td>จ่ายโดย</td>
                                <td><label>
                                  <select name="PaymentMethod" id="PaymentMethod" onchange="doToggleMethod();">
                                    <option value="Cash" >เงินสด</option>
                                    <option value="Cheque" <?php if($output_values["PaymentMethod"]=="Cheque"){echo "selected='selected'";}?>>เช็ค</option>
                                    <option value="Note" <?php if($output_values["PaymentMethod"]=="Note"){echo "selected='selected'";}?>>ธนาณัติ</option>
                                  </select>
                                  </label>
                                </td>
                              </tr>
                              <tr>
                                <td colspan="4"><table id="cash_table" border="0" style="padding:0px 0 0 50px;" >
                                    <tr>
                                      <td><span style="font-weight: bold">ข้อมูลการจ่ายเงินสด</span></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>
                                    <table id="cheque_table" border="0" style="padding:0px 0 0 50px;"  >
                                      <tr>
                                        <td><span style="font-weight: bold">ข้อมูลการจ่ายเช็ค</span></td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">ธนาคาร</span></td>
                                        <td><?php
											  include "ddl_bank.php";
											  ?>                                        </td>
                                      </tr>
                                      <tr>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่เช็ค</span></td>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">
                                          <input name="Cheque_ref_no" type="text" id="Cheque_ref_no" value="<?php echo $this_ref_number;?>"  />
                                        </span></td>
                                      </tr>
                                    </table>
    <table id="note_table" border="0" style="padding:0px 0 0 50px; " >
                                      <tr>
                                        <td><span style="font-weight: bold">ข้อมูลการจ่ายธนาณัติ</span></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td>เลขที่ธนาณัติ</td>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">
                                          <input name="Note_ref_no" type="text" id="Note_ref_no" value="<?php echo $this_ref_number;?>"  />
                                        </span></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                      </tr>
                                  </table></td>
                              </tr>
                              <tr>
                                <td valign="top">หมายเหตุ</td>
                                <td colspan="3"><label>
                                  <textarea name="ReceiptNote" cols="50" rows="4" id="ReceiptNote"><?php echo $output_values["ReceiptNote"];?></textarea>
                                </label></td>
                              </tr>
                              <tr>
                                <td>เอกสารประกอบ</td>
                                <td colspan="3"><?php 
                                                    
                                                    $file_type = "receipt_docfile";
                                                
                                                    include "doc_file_links.php";
                                                ?> 
                                    <input type="file" name="receipt_docfile" id="receipt_docfile" /></td>
                              </tr>
                          </table></td>
                        </tr>
                        <tr>
                          <td><hr />
                              <div align="center">
                              	<?php if($sess_accesslevel!=5){?>
                                <input type="submit" value="เพิ่มข้อมูล" />
                                <?php }?>
                            </div></td>
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
							
							
						</script>
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

</body>
</html>
