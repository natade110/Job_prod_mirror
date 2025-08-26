<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	
	//yoes 20170415 -- add table names
	
	if($_GET[view] == "request"){
		$payment_table = "payment_request";
		$receipt_table = "receipt_request";
	}else{
		$payment_table = "payment";
		$receipt_table = "receipt";	
	}
	
	//current mode
	
	$mode  = "normal";
	
	if(is_numeric($_GET["id"])){
		
		$this_id = $_GET["id"];
		
		$post_row = getFirstRow("select * 
								from 
									$receipt_table
								where 
									RID  = '$this_id'
								limit 0,1");
				
		//print_r($post_row);		
						
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
						,'NEPFundPaymentID'
						
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
		$payment_row = getFirstRow("select * from $payment_table where RID = '$this_id' limit 0,1");
		$this_ref_number = $payment_row["RefNo"];
		$this_bank_id = $payment_row["bank_id"];
		
		
	}else{
		header("location: index.php");
	}	
		
	//yoes 20160125 --> add "read-only" mode
	//$is_read_only = 1;

	
	//yoes 20170115
	//print_r($_POST);
	
	//yoes 20170415
	//disable everything by default
	$the_disabled = 'disabled="disabled"';
	$the_disabled_booknum = 'disabled="disabled"';
	
	if($_POST[do_edit_receipt]){
		
		$mode = "edit_receipt";		
		$the_disabled = '';
		
	}
	
	
	if($_POST[do_cancel_receipt]){
		
		$mode = "cancel_receipt";
		$the_disabled = '';
		
	}
	
	//check if already have requests sent...
	$edit_requested = getFirstRow("
	
				
			select
				rid
				, Amount
			from
				receipt_edit_request
			where
				edit_status = 0
				and
				rid = '$this_id'
	
		
			");
			
	if($edit_requested[rid] && $edit_requested[Amount] > 0){
		
		$mode = "edit_requested";	
		
	}elseif($edit_requested[rid] && $edit_requested[Amount] <= 0){
		
		$mode = "cancel_requested";	
		
	}elseif($_GET[view] == "request"){
		
		$mode = "insert_requested";	
		
	}

	

?>
<?php 
	include "header_html.php";
	include "global.js.php";	
?><script>
										  	
											
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
                    
                    <div style="padding:5px 0 10px 2px"><a href="payment_list.php">ใบเสร็จรับเงินทั้งหมด</a> >  ใบเสร็จเล่มที่ <?php echo $output_values["BookReceiptNo"];?> ใบเสร็จเลขที่ <?php echo $output_values["ReceiptNo"];?></div>
                    
                    <div style="padding-bottom:10px">
                   <strong>
                    
                    
                  
                สถานประกอบการในใบเสร็จรับเงิน</strong><br />
                 <?php if($sess_accesslevel != 5 && 1==0){?> 
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
          			
                    <?php 
					//yoes 20150930 -- destroy this form					
					//yoes 20151002 -- revive this form
					if(1==1){
					
					?>                              
                    
          	          <form method="post"
                      
                       <?php if($mode == "edit_receipt" || $mode == "cancel_receipt"){?>
                      action="scrp_generate_receipt_edit_request.php"
                      <?php }?>
                      
                      <?php if($mode == "edit_requested" || $mode == "cancel_requested"){?>
                      action="scrp_approve_receipt_edit_request.php"
                      <?php }?>
                      
                      
                      <?php if($mode == "insert_requested"){?>
                      action="scrp_approve_receipt_insert_request.php"
                      <?php }?>
                      
                      
                      
                      <?php if($output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "normal"){?>
                      
                      action="scrp_generate_receipt_cancel_request.php"
                      <?php }?>
                      
                     
                      
                       enctype="multipart/form-data" <?php if($sess_accesslevel !=4){?>onsubmit="return validatePaymentForm(this);"<?php }?>>
                    
                    <?php }?>
                  
                    
                    <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                        	
           	 				 <td >
                           	<div align="center"><span class="column_header">เลขที่บัญชีนายจ้าง</span>                       	        </div></td>
                            
                      <td>
                           	<div align="center"><span class="column_header">ชื่อนายจ้างหรือสถานประกอบการ</span>                       	        </div></td>
                           
                      <td>
                           	<div align="center"><span class="column_header">สถานะ</span>                       	        </div></td>
                            
                          <?php if($sess_accesslevel!=5 && 1==0){?> 
                          
                          <?php }?>
                          
                          
                          <?php 
						  	//if($sess_accesslevel ==1){ // yoes 20151001 -- aloow admin to delete this
							//**toggles_payment
							if($mode == "is_payback"){ // yoes 20151002 -- bring this back - allow delete on those that not from webservice
							//if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $output_values["ReceiptYear"] < 2016){ // yoes 201660111 --> allow staff to do this for now
							?>
                          
                          	<td>
                       	  <div align="center"><span class="column_header">ลบข้อมูล</span>        </div></td>
                          
                          <?php }?>
                          
                          <td width="50"><div align="center"><span class="column_header">สถานประกอบการหลัก</span>                       	        </div></td>
                   	  </tr>
                        <?php
					
						
						$cur_year = $output_values["ReceiptYear"];
						
						$get_org_sql = "SELECT *, b.CID as companyid, c.LawfulStatus as lawfulness_status
										FROM $payment_table a, company b, lawfulness c
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
						
						//20140127
						//arrays for auto-update
						$cid_array = array();
						$year_array = array();
					
						while ($post_row = mysql_fetch_array($org_result)) {
					
							$total_records++;
							
							//push all cid of this payment into arrray
							array_push($cid_array, $post_row["CID"]);
							array_push($year_array, $output_values["ReceiptYear"]);
							
							
							
							
						?>     
                        <tr bgcolor="#ffffff" align="center" >
                        	
                       	  <td >
                          	<?php
							
							if(($_SESSION['sess_accesslevel'] == 3 && $_SESSION['sess_meta'] == $post_row["Province"]) || $_SESSION['sess_accesslevel'] == 1 || $_SESSION['sess_accesslevel'] == 2  ){
							
							?>
                           		<a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>&focus=lawful&year=<?php echo $output_values["ReceiptYear"];?>"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>                          
                            <?php 
							
							
							}else{ ?>
                            	<?php echo doCleanOutput($post_row["CompanyCode"]);?>
                            
                            <?php } ?>
                            
                            
                            </td>
                          
                            <td>
                            	
								
								<?php echo doCleanOutput($post_row["CompanyNameThai"]);?>
                                
                                
                                
                                </td>
                          
                           <td>
                            	<div align="center"><?php echo getLawfulImage(($post_row["lawfulness_status"]));?></div>                         </td>
                                
                                
                              <?php if($sess_accesslevel!=5 && 1==0){?> 
                            
                             <?php }?>
                             
                             <?php 
							 
							 	//if($sess_accesslevel ==1){ // yoes 20151001 -- aloow admin to delete this
								//**toggles_payment
							 	if($mode == "is_payback"){ // yoes 20151002 -- bring this back
								//if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $output_values["ReceiptYear"] < 2016){ // yoes 201660111 --> allow staff to do this for now
							 
							 ?>
                             
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
								
								
								if((frm.the_date_year.value >= 2015 && frm.the_date_month.value >= 10) || frm.ddl_year.value >= 2016 ){
									
									<?php if($mode != "is_payback"){?>
									//alert("ไม่อนุญาตให้ใส่ข้อมูลชำระเงินของปีงบประมาณ 2559");
									//frm.the_date_year.focus();
									//return (false);
									<?php }?>
									
								}
								
								
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
                                <td><span style="font-weight: bold">
                                
                                
                                <?php if($mode == "edit_receipt"){echo "แก้ไข";}?>ข้อมูลใบเสร็จ
                                
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                               <?php if($mode == "edit_receipt" || $mode == "cancel_receipt"){?>
                               
                                  	 <tr>
                                    
                                          <td colspan="4">
                                                <hr />
                                          </td>
                                 	 </tr>
                                		
                                        <tr>
                                            <td valign="top">
                                            
                                            <?php 
											
											if($mode == "edit_receipt"){
												
												echo "เหตุผลที่ขอปรับปรุงใบเสร็จรับเงิน";
												
											}elseif($mode == "cancel_receipt"){
												echo "เหตุผลที่ขอยกเลิกใบเสร็จรับเงิน";
												
											}
											
											?>
                                            
                                            
                                            
                                            </td>
                                            <td colspan="3"><label>
                                            <textarea name="edit_reason" cols="50" rows="4" id="edit_reason"></textarea>
                                            </label></td>
                                      </tr>
                                          
                                          <tr>
                                            <td valign="top">
                                            
                                             <?php 
											
											if($mode == "edit_receipt"){
												
												echo "ผู้ที่ขอปรับปรุงข้อมูลใบเสร็จ";
												
											}elseif($mode == "cancel_receipt"){
												echo "ผู้ที่ขอยกเลิกข้อมูลใบเสร็จ";
												
											}
											
											?>
                                            
                                          </td>
                                            <td colspan="3"><?php 
                                            
                                                echo $sess_userfullname;
                                            
                                            ?>
                                            
                                            <input name="edit_userid" type="hidden" value="<?php echo $sess_userid?>" />
                                            <input name="edit_rid" type="hidden" value="<?php echo $this_id?>" />
                                            
                                            </td>
                                          </tr>
                                          
                                          
                                          <tr>
                                
                                            <td colspan="4">
                                                    <hr />
                                              </td>
                                      	</tr>
                                   
                            
                              
                              
                            
                              
                              <tr>
                                     <td colspan="4"><span style="font-weight: bold">
                                     
                                     
                                      <?php 
											
											if($mode == "edit_receipt"){
												
												echo "แก้ไขข้อมูล";
												
											}elseif($mode == "cancel_receipt"){
												echo "ยกเลิกข้อมูล";
												
											}
											
											?>
                                     
                                     </span></td>
                            </tr>
                            
                                   <?php }?>
                            
                              <tr>
                                <td>สำหรับปี</td>
                                <td><?php 
								
								
										//yoes 20160111 --- turn this to static value
										if($output_values["ReceiptYear"] >= 2016){
											//include "ddl_year.php";	//normal
											echo $output_values["ReceiptYear"]+543;
										}else{
											//include "ddl_year_payments.php";
											echo $output_values["ReceiptYear"]+543;
										}
									
									
									?>
                                    <input name="ddl_year" type="hidden" value="<?php echo $output_values["ReceiptYear"]?>" />
                                    </td>
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
                                  <input name="BookReceiptNo" type="text" id="BookReceiptNo" <?php echo $the_disabled_booknum;?> value="<?php echo $output_values["BookReceiptNo"];?>"  />
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
                                  <input name="ReceiptNo" type="text" id="ReceiptNo" <?php echo $the_disabled_booknum;?>  value="<?php echo $output_values["ReceiptNo"];?>"  />
                                  <input name="oldReceiptNo" type="hidden"  value="<?php echo $output_values["ReceiptNo"];?>"  />
                                </span></td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">วันที่จ่าย</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php
											   
											   $selector_name = "the_date";
											   $this_date_time = $output_values["ReceiptDate"];
											   
											  
											 
											   //toggles_payment
											   //
											   if($output_values["ReceiptYear"] >= 2016 || $output_values["NEPFundPaymentID"] || $_GET[view] == "request" || $output_values["is_payback"]){
													 include ("date_selector.php");	//normal
												}else{
													 //include ("date_selector_payments.php");
													 //yoes 20170509 -- default everything to "all years" mode
													 include ("date_selector.php");
												}
												
												
											
											   
											   ?>
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จำนวนเงิน</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="Amount" type="text" id="Amount" <?php echo $the_disabled;?>  style="text-align:right;" value="<?php echo formatNumber(default_value($output_values["Amount"],0));?>" onchange="addCommas('Amount');" />
                                  <?php
								  	
									include "js_format_currency.php";
								  
								  ?>
                                  <span class="style86" style="padding: 10px 0 10px 0;">บาท</span></span></span></td>
                                <td>จ่ายโดย</td>
                                <td><label>
                                  <select name="PaymentMethod" id="PaymentMethod" onchange="doToggleMethod();" <?php echo $the_disabled;?> >
                                    <option value="Cash" >เงินสด</option>
                                    <option value="Cheque" <?php if($output_values["PaymentMethod"]=="Cheque"){echo "selected='selected'";}?>>เช็ค</option>
                                    <option value="Note" <?php if($output_values["PaymentMethod"]=="Note"){echo "selected='selected'";}?>>ธนาณัติ</option>
                                    <option value="NET" <?php if($output_values["PaymentMethod"]=="NET"){echo "selected='selected'";}?>>KTB Netbank</option>
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
										
												//$the_disabled = 'disabled="disabled"';
												include "ddl_bank.php";												
												//$the_disabled = '';
											  
											  ?>                                        </td>
                                      </tr>
                                      <tr>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่เช็ค</span></td>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">
                                          <input name="Cheque_ref_no" <?php echo $the_disabled;?>  type="text" style="width: 370px;" id="Cheque_ref_no" value="<?php echo $this_ref_number;?>"  />
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
                                          <input name="Note_ref_no" <?php echo $the_disabled;?>  type="text" id="Note_ref_no" value="<?php echo $this_ref_number;?>"  />
                                        </span></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                      </tr>
                                  </table></td>
                              </tr>
                              <tr>
                                <td valign="top">หมายเหตุ</td>
                                <td colspan="3"><label>
                                  <textarea name="ReceiptNote" <?php echo $the_disabled;?>   cols="50" rows="4" id="ReceiptNote"><?php echo $output_values["ReceiptNote"];?></textarea>
                                </label></td>
                              </tr>
                              
							  <tr>
                                <td>เอกสารประกอบ</td>
                                <td colspan="3"><?php 
                                                    
                                                    if($mode == "insert_requested"){
														$file_type = "receipt_docfile_request";
													}else{
														$file_type = "receipt_docfile";
													}
                                                    include "doc_file_links.php";
                                                ?> 
                                    <input type="file" name="receipt_docfile" id="receipt_docfile" />
									
									
									<br>
									
                              </tr>
							  
							  <?php if($output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "normal" ){?>
							  <tr>
                                <td></td>
                                <td colspan="3">
									<input name="do_add_docfile" type="submit" value="เพิ่มเอกสารประกอบ " />
									
								</td>
                              </tr>
							  <?php }?>
							  
							  
                               <tr>
                                
                                <td colspan="4">
                                		<hr />
	                              </td>
                              </tr>
                              <tr>
                                
                                <td colspan="4">
                                
                                <div align="center">
                                
                               
                                
                                
								<?php 
								
									//yoes 20170115									
									//ALLOW edit if this not from NEPFUND
									if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "normal"){
								?>
                                
                                <input name="do_edit_receipt" type="submit" value="ต้องการแก้ไขข้อมูลใบเสร็จ คลิกที่นี่" />
                                
                                
                                
                                <hr />
                                 <input name="do_cancel_receipt" type="submit" value="ต้องการ 'ยกเลิก' ข้อมูลใบเสร็จ คลิกที่นี่" />
                                
										
								<?php		
									}
								
								?>
                                
                                
                                
                                <?php 
								
									//yoes 20170115									
									//ALLOW edit if this not from NEPFUND
									if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "edit_requested"){
								?>
                                
                               		
                                    <font color="#006699">มีการส่งเรื่อง <font color="#FF0000">"แก้ไขข้อมูลใบเสร็จ"</font> โปรดตรวจสอบข้อมูลก่อนการอนุมัติ</font>
                                    
                                    
                                    
										
								<?php		
									}
								
								?>
                                
                                
                                 
                                <?php 
								
									//yoes 20170115									
									//ALLOW edit if this not from NEPFUND
									if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "cancel_requested"){
								?>
                                    
                                    <font color="#006699">มีการส่งเรื่อง <font color="#FF0000">"ขอยกเลิกใบเสร็จ"</font> โปรดตรวจสอบข้อมูลก่อนการอนุมัติ</font>
                                                                        	
								<?php		
									}
								
								?>
                                
                                
                                <?php 
								
									//yoes 20170115									
									//ALLOW edit if this not from NEPFUND
									if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "insert_requested"){
								?>
                                
                               		
                                    
                                    <?php if($output_values["is_payback"]){?>
                                    	
                                        <font color="#006699">มีการส่งเรื่อง <font color="#FF0000">"ขอเพิ่มใบเสร็จในกรณีคืนเงินให้สถานประกอบการ"</font> โปรดตรวจสอบข้อมูลก่อนการอนุมัติ</font>
                                    <?php }else{?>
                                    
                                    	<font color="#006699">มีการส่งเรื่อง <font color="#FF0000">"ขอเพิ่มใบเสร็จ"</font> โปรดตรวจสอบข้อมูลก่อนการอนุมัติ</font>
	                                 
                                    <?php }?>
                                    
										
								<?php		
									}
								
								?>
                                
                                
                                
                                
                                
                                
                                <?php 
								
									//yoes 20170115									
									//ALLOW edit if this not from NEPFUND
									if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "edit_receipt"){
								?>
                                
	                                <input name="do_request_edit_receipt" type="submit" value="ส่งเรื่องขอปรับปรุงข้อมูลใบเสร็จรับเงิน" />
                                
                                <?php		
									}
								
								?>
                                
                                
                                <?php 
								
									//yoes 20170115									
									//ALLOW edit if this not from NEPFUND
									if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "cancel_receipt"){
								?>
                                
	                                <input name="do_request_cancel_receipt" type="submit" value="ส่งเรื่องขอ 'ยกเลิก' ข้อมูลใบเสร็จรับเงิน" />
                                
                                <?php		
									}
								
								?>
                                
                                </div>
                                
                                <?php if($mode == "edit_requested" || $mode == "cancel_requested" || $mode == "insert_requested"){?>
                                
                                
                                
                                <?php 
								
									
									//
									
									if($mode == "insert_requested"){
										
											
										$request_edit_row = getFirstRow("
													
													SELECT 
															*
															, request_reason as edit_reason
															, request_userid as edit_userid
															, request_date as edit_date
														FROM 
															payment_request a
																join
																	receipt_request b												
																on
																	a.RID = b.RID											
														where
															request_status = 0
															and
															a.rid = '$this_id'
									
													");
													
											$modify_word = "เพิ่ม";
										
													
									}else{
										
										$request_edit_row = getFirstRow("
												
												select
													*
												from
													receipt_edit_request
												where
													edit_status = 0
													and
													rid = '$this_id'
								
												");			
												
										$modify_word = "ปรับปรุง";							
										
										
									}
								
								?>
                                 <table border="1" style="margin-top:15px;" width="100%" cellspacing="0" cellpadding="5"> 
                                    <tr>
                                      <td>
                                
                                		<strong>	
                                        
                                        <?php 
										
										 if($mode == "edit_requested"){
											 
											echo "ข้อมูลใบเสร็จที่มีการแก้ไขเข้ามา";
											 
										 }elseif($mode == "cancel_requested"){
											 
											 echo "มีการขอยกเลิกใบเสร็จ";
											 
										 }
										

										?>
                                        
                                        </strong>
                                        
                                   			<table border="1" style="margin-top:15px;" width="100%" cellspacing="0" cellpadding="5" > 
                                            
                                            
                                            
                                            <?php 
												
											 if($mode == "edit_requested"){
											?>
                                                <tr>
                                                  <td>
                                                  	วันที่จ่าย
                                                    
                                                   
                                                  </td>
                                                  <td colspan="3">
                                                   <?php 
														
														echo formatDateThai($request_edit_row[ReceiptDate]);
													
													?>
                                                  </td>
                                				</tr>
                                                <tr>
                                                  <td>
                                                  	จำนวนเงิน
                                                  </td>
                                                  <td>
                                                  
                                                   <?php 
														
														echo formatNumber($request_edit_row[Amount]);
													
													?>
                                                  
                                                  บาท
                                                  
                                                  </td>
                                                 
                                				</tr>
                                                
                                                 <tr>
                                                                                                    <td>
                                                  	จ่ายโดย
                                                  </td>
                                                  <td>
                                                  
                                                  <?php 
														
														echo formatPaymentName($request_edit_row[PaymentMethod]);
													
													?>
                                                  
                                                  </td>
                                				</tr>
                                                
                                                <?php 
												
												if($request_edit_row[PaymentMethod]=="Cheque" || $request_edit_row[PaymentMethod]=="Note"){
													
												?>
                                                
                                                    <tr>   
                                                     <td >
                                                      ข้อมูลการจ่ายเงิน
                                                      </td>                                               
                                                      <td colspan="3">
                                                      
                                                      <?php 
                                                        
                                                        if($request_edit_row[PaymentMethod]=="Cheque"){
                                                            
                                                            echo "ธนาคาร ". getFirstItem("select bank_name from bank where bank_id = '".$request_edit_row[bank_id]."'");	
                                                            echo "<br>เลขที่เช็ค " . $request_edit_row[RefNo];
                                                            
                                                        }
                                                        
                                                        if($request_edit_row[PaymentMethod]=="Note"){
                                                            
                                                            echo "เลขที่ธนาณัติ " . $request_edit_row[RefNo];
                                                            
                                                        }
                                                      
                                                      ?>
                                                      
                                                      
                                                      </td>
                                                    </tr>
                                                
                                                <?php }?>
                                                
                                                
                                             <?php }//ends  if($mode == "edit_requested"){?>
                                                
                                                 <tr>                                                  
                                                  <td>เหตุผลที่ขอ<?php echo $modify_word;?>ใบเสร็จรับเงิน</td>
                                                  <td colspan="3">
                                                  
                                                   <?php 
														
														echo $request_edit_row[edit_reason];
													
													?>
                                                  
                                                  
                                                  </td>
                                                </tr>
                                                 <tr>
                                                   <td>ผู้ที่ขอ<?php echo $modify_word;?>ข้อมูลใบเสร็จ</td>
                                                   <td colspan="3">
                                                   
                                                    <?php 
														
														echo getFirstItem("select concat(FirstName, ' ', LastName) from users where user_id = '". $request_edit_row[edit_userid]."'");
													
													?>
                                                   
                                                   </td>
                                                 </tr>
                                                 <tr>
                                                   <td>วันที่มีการขอ<?php echo $modify_word;?>ข้อมูล</td>
                                                   <td colspan="3">
                                                   
                                                   <?php 
														
														echo formatDateThai($request_edit_row[edit_date]);
													
													?>
                                                   
                                                   </td>
                                                 </tr>
                                                 
                                                 
                                                 <?php if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_can_approve_34 || 1==1)){?>
                                                 <tr>
                                                   <td colspan="4">
                                                   	
                                                    	<div align="center">
                                                        
                                                        	
                                                        <input name="edit_rid" type="hidden" value="<?php echo $this_id?>" />
                                                    	  <input name="approve_request" type="submit" value="อนุมัติ" />
                                                        
                                                          <input name="reject_request" type="submit" value="ไม่อนุมัติ" />
                                                    	</div>
                                                   </td>
                                                 </tr>
                                                 <?php }?>
                                                 
                                                 
                                                 
                                                  <?php if(!$output_values["NEPFundPaymentID"] && ($sess_accesslevel == 3 )){?>
                                                 <tr>
                                                   <td colspan="4">
                                                   	
                                                    	<div align="center">
                                                        
                                                        	
                                                        <input name="edit_rid" type="hidden" value="<?php echo $this_id?>" />
                                                    	  
                                                        
                                                          <input name="cancel_request" type="submit" value="ยกเลิกคำร้อง" />
                                                    	</div>
                                                   </td>
                                                 </tr>
                                                 <?php }?>
                                                
                                            </table>    
                                            
                                		</td>
                                        
                                   </tr>
                               </table>
                                <?php }?>
                                
                                
                                
                                
                                <?php 
								
									//yoes 20170115									
									//ALLOW edit if this not from NEPFUND
									if($output_values["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $mode == "normal" ){
								?>
                                
                              	
                                        <div align="center">
                                        
                                        
                                        เป็นข้อมูลใบเสร็จ จากระบบใบเสร็จออนไลน์
                                      
                                        <br />
                                        กรณีต้องการยกเลิกใบเสร็จ กรุณาใส่เหตุผลที่ต้องการยกเลิกใบเสร็จ
                                      
                                        <br />
                                        
                                        
                                        <textarea name="cancel_reason" cols="50" rows="4" id="cancel_reason"></textarea>
                                        
                                        
                                        <br />
                                        
                                       
                                       ผู้ขอยกเลิกใบเสร็จ: 
                                       <?php 
                                                
                                            echo $sess_userfullname;
                                        
                                        ?>
                                        
                                        
                                      
                                      
                                        <br />
                                        
                                        
                                        
                                        
                                         <?php 
								
								
										//yoes 20170307 -- check on how many receipts is in this TID
										
										//echo $output_values["NEPFundPaymentID"];
										
										
										$tid_count = getFirstItem("
										
													select
														count(*)
													from
														$receipt_table
													where
														NEPFundPaymentId = '".$output_values["NEPFundPaymentID"]."'
														and
														RID != '".$this_id."'
													
												
												");
												
										
										if($tid_count > 0){
											
											
										?>
													
                                                    
                                            <hr />
                                            
                                            <div align="center">
                                            
                                            เนื่องจากการจ่ายเงินของใบเสร็จนี้ มีการจ่ายเงินของใบเสร็จอื่นร่วมอยู่ด้วย
                                             (เช่น มีการจ่ายเช็คใบเดียว ให้สองใบเสร็จ)
                                            <br />ถ้าท่านทำการยกเลิกใบเสร็จนี้ ใบเสร็จอื่นๆที่เกี่ยวข้องจะถูกยกเลิกไปด้วย
                                            <br /><strong>ใบเสร็จที่เกี่ยวข้องกับใบเสร็จนี้ ได้แก่:      </strong>                                      
                                            
                                            
                                            <?php 
											
												
												$related_reciept_sql = "select
														*
													from
														$receipt_table
													where
														NEPFundPaymentId = '".$output_values["NEPFundPaymentID"]."'
														and
														RID != '".$this_id."'";
											
											
												$related_reciept = mysql_query($related_reciept_sql);
												
											
											while($related_array = mysql_fetch_array($related_reciept)){
											?>
                                            
                                            
                                            	<br />ใบเสร็จเล่มที่ 
												
												
                                                <a href="view_payment.php?id=<?php echo $related_array[RID]?>" target="_blank">
													<?php echo $related_array[BookReceiptNo]?>
                                                
                                                </a>
                                                
                                                 เลขที่ <?php echo $related_array[ReceiptNo]?> วันที่จ่าย <?php echo formatDateThai($related_array[ReceiptDate]) ?> จำนวนเงิน <?php echo number_format($related_array[Amount],2)?> บาท
                                            
                                            <?php											
												
											}
											
											?>
                                            
                                            
                                            
                                            </div>      
                                            
                                            <hr />  							
										
										<?php
											
										}
										
										?>
                                        
                                        <input name="cancel_rid" type="hidden" value="<?php echo $this_id;?>" />
                                        <input name="cancel_tid" type="hidden" value="<?php echo $output_values["NEPFundPaymentID"];?>" />
                                       
                                        <input name="cancel_userid" type="hidden" value="<?php echo $sess_userid?>" />
                                        
                                         <input name="do_cancel_receipt" type="submit" value="ออกใบขอยกเลิกใบเสร็จ คลิกที่นี่" />
                                        
                                        
                                        </div>
                                
                                
                                
                               
										
								<?php		
									}
								
								?>
                                
                                </td>
                              </tr>
                          </table></td>
                        </tr>
                        
                      
                              	
                              
                              		<input name="origin_year" type="hidden" value="<?php echo $year_array[0];?>" />
                                 </form>
                                   
                                   
                                   
                                   <?php 
									
									//yoes 20180104
									//allow printing form for "ขอเงินคืน"
									if($mode == "is_payback"){ ?>
                                   
                                   <tr>
                                
                                      <td colspan="4">
                                            <div align="center">
                                    			 <form action="payback_form.php" target="_blank" method="post">
	                                            	 <input name="do_print_payback" type="submit" value="ออกแบบฟอร์มขอเงินคืน คลิกที่นี่" />
                                                     <input name="the_rid" type="hidden" value="<?php echo $this_id; ?>" />
                                                 </form>
                                            </div>
                                      </td>
                                  </tr>
                                	
                                <?php
										
									}
								
								?>
                                                                  
                                                         
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
                        
                        
                        
                        
                        
                        
                        
               
                
                
                
                
                            
                  
                  <?php 
					
					///20140127
					//iframe incase we updated any years....	
					if($_GET["updated"] == "updated" || $_GET["payment_added"] == "payment_added" || ($_GET["delpayment"] == "delpayment" && is_numeric($_GET["del_id"]))){
				  
				  		//this page is updated via scrp_update_payment
						//update all org in thjis payment via iframe
						
						?>
                        
                        
                        	<div id="overlay"> 
                               <div id="img-load" style="color:#FFFFFF; text-align:center">
                                <img src="./decors/bigrotation2.gif"  />
                                
                                
                                <br />
                                <strong>กำลังปรับปรุงข้อมูล...</strong>
                                
                                
                                
                                </div>
                            </div>
                            
                            <script>
                            $t = $("#main_body");
                            
                            $("#overlay").css({
                              opacity: 0.5,
                              top: 0,
                              left: 0,
                              width: $t.outerWidth(),
                              height: $t.outerHeight()
                            });
                            
                            $("#img-load").css({
                              top:  (380),
                              left: ($t.width() / 2 -110)
                            });
                            
                            //$t.mouseover(function(){
                               $("#overlay").fadeIn();
                            //}
                            //);
                            </script>
                        
                        
                        <?php	
						
						for($i=0;$i<count($cid_array);$i++){
				  
				  ?>
                  
	                  <iframe width="1" height="1" src="./organization.php?id=<?php echo $cid_array[$i];?>&focus=lawful&year=<?php echo $year_array[$i];?>&auto_post=1"></iframe>
                      
                      
                  <?php 
				  
							if($_GET["origin_year"]){
								//origin year specified -> update that also
								$origin_year = $_GET["origin_year"]*1;
								?>
                                
                                <iframe width="1" height="1" src="./organization.php?id=<?php echo $cid_array[$i];?>&focus=lawful&year=<?php echo $origin_year;?>&auto_post=1"></iframe>
                                
                                <?php								
								
								
							}
				  
				  
						}//end for $i=
						
						
						 //20140127
					 //extra case for deletion
					 if($_GET["delpayment"] == "delpayment" && is_numeric($_GET["del_id"])){
						 
						 
						 
						 	?>
                           <iframe width="1" height="1"  src="./organization.php?id=<?php echo ($_GET["del_id"]);?>&focus=lawful&year=<?php echo $year_array[$i];?>&auto_post=1"></iframe>
                            
                      <?php
						 
					 }
						
						
							
						//dummy iframe to catch on-load events
						?>
                        
                        <iframe width="1" height="1" src="./organization.php?id=<?php echo $_GET["org".$i];?>&focus=lawful&year=<?php echo $the_year;?>" onload='$("#overlay").hide();'></iframe>
                        
                        <?php
						
						
						
				  
					}//end if updated
					  
					  
					
					  
				  ?> 
                  
                  
                  
                  <?php 
				  
				  
				  
				    $log_sql = "
                            
                            
                                SELECT 
                                    *
                                FROM 
                                    payment_full_log a
                                        join
                                            receipt_full_log b												
                                        on
                                            a.RID = b.RID											
                                            and
                                            a.log_date = b.log_date
                                where
                                    a.RID = '$this_id'
									
								order by
									a.log_id desc
                            
                            
                            ";
							
					 $log_result = mysql_query($log_sql);
				  
				  if(mysql_num_rows($log_result)){
					  
					  
					  ?>
                  <div align="center" style=" padding: 10px 0; ">
    
                  <a href="#" onclick="$( '#log_table' ).toggle();">
	                  >> ประวัติการแก้ไขใบเสร็จ <<
                  </a>
                 
                  
                      <table id="log_table" border="1"  cellspacing="0" cellpadding="5" style="border-collapse:collapse; display: none;" width="100%">
                                        
                        
                        <?php while($post_row = mysql_fetch_array($log_result)){ ?>
                         <tr>
                             <td colspan="7" >
                                  ข้อมูลวันที่
                                    <strong><?php echo $post_row[log_date];?> </strong>
                                    
                                    
                                    แก้ไขโดย <strong><?php 
                                        
                                        echo getFirstItem("select user_name from users where user_id = '".$post_row["log_by"]."'");
                                        
                                        echo "(".str_replace("-----","",$post_row["log_ip"]).")"; //--- IP
                                        
                                        ?></strong>
                                        
                                        <?php if(1==1){?>
                                        การกระทำ: 
                                        <strong><?php echo doGetLogSourceName($post_row["log_source"]);?></strong>
                                        <?php }?>
                             
                             </td>
                             
                                                          
                       </tr>
                      
                                        
                         <tr bgcolor="#efefef">
                             
                              <td><div align="center">สำหรับปี</div></td>
                              <td><div align="center">ใบเสร็จเล่มที่</div></td>
                              <td><div align="center">ใบเสร็จเลขที่</div></td>                                      
                              <td><div align="center">วันที่จ่าย</div></td>
                              <td><div align="center">จำนวนเงิน</div></td>
                              
                              <td><div align="center">จ่ายโดย</div></td>
                              <td><div align="center">หมายเหตุ</div></td>                                      
                                                               
                              
                          </tr>
                          
                          <tr>
                             
                              <td><?php
                              
                                //print_r($post_row);
                                echo $post_row["ReceiptYear"]+543;
                              
                              ?></td>
                              <td><?php
                                                                            
                                echo $post_row["BookReceiptNo"];
                              
                              ?></td>
                              <td><?php
                                                                            
                                echo $post_row["ReceiptNo"];
                              
                              ?></td>
                              <td><?php
                                                                            
                                echo formatDateThai($post_row["ReceiptDate"]);
                              
                              ?></td>
                              <td><?php
                                                                            
                                echo number_format($post_row["Amount"],2);
                              
                              ?></td>
                              
                              <td><?php
                              
                                echo formatPaymentName($post_row["PaymentMethod"]);
                                
                                if($post_row["RefNo"]){
                                    echo " " . $post_row["RefNo"];
                                }
                                
                                if($post_row["bank_id"]){
                                    echo " " . getFirstItem("select * from bank where bank_id = '".$post_row["bank_id"] . "'");
                                }
                              
                              ?></td>
                              <td><?php
                                                                            
                                echo $post_row["ReceiptNote"];
                              
                              ?></td>
                                                     
                              
                          </tr>
                                          
                                          
                                      
                            
                            
                           
                       
                        <?php }?>
                        
    
                      </table>
                  
                   </div>
                  
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

</body>
</html>
