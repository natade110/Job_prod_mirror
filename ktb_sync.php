<?php

	include "db_connect.php";
	require_once 'c2x_include.php';	
	// Do Ajax Request
	if($_POST["ajax"]) {
			$status = $_POST[cstatus];
			$id = $_POST[id];
			$cdate = DateTime::createFromFormat('d-m-Y', $_POST[cdate]);
			$mdate = $cdate->format('Y-m-d');
			$paymentStatusMapping = getPaymentStatusMapping();
			unset($paymentStatusMapping[0]);
			$retunStatus = $paymentStatusMapping[$status];
			$returnKTBDate = formatDateThai($mdate);
			$returnBillDate = formatDateThai(date('Y-m-d'));
			$ret = "";
			if($status == 10) {
				$sql = "update bill_payment set PaymentStatus=10, KTB_cheque_clear_date='$mdate' where ID=$id";
				mysql_query($sql);
				$ret = "{\"success\": 1, \"status\":\"$retunStatus\"}";
			
			} elseif ($status == 13) {		
				// Update bill_payment
				$sql = "update bill_payment set PaymentStatus=13, KTB_cheque_clear_date='$mdate' where ID=$id";
				mysql_query($sql);		
				
				
				// Delete receipt, payment temp.
				$sql = "delete r,p from receipt r inner join payment p on r.RID=p.RID 
					where r.NEPFundPaymentID=(select NEPFundPaymentID from bill_payment where id=$id)";
				mysql_query($sql);				
				$ret = "{\"success\": 1, \"status\":\"$retunStatus\"}";
			} else {
				;;				
			}
			
			
			//echo "HELLO: ". writeDate($mdate);
			header("Content-type:text/html; charset=UTF-8");
			echo $ret;
			exit;
	}


	
	include "session_handler.php";	
	if($sess_accesslevel != 1 && $sess_accesslevel != 2 ){	
		header("location: index.php");	
		exit();		
	}
	
?>


<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >สรุปยอดการชำระเงินรายวัน KTB Online</h2>
                   
                   
                   <form method="post">
                   
					   <input name="chk_from" type="checkbox" value="1" <?php if($_POST['chk_from']){?>checked="checked"<?php }?> />ข้อมูลระหว่างวันที่
                           
							<?php 
								$selector_name = "txDateFrom";
								$this_date_time = date("Y-m-d");
								include "date_selector.php";?> 
                            
                            &nbsp;&nbsp;ถึง&nbsp;&nbsp;
							
							<?php 
								$selector_name = "txDateTo";
								include "date_selector.php";?>
                                
                                
                                <input type="submit" value="เรียกดู">
                   </form>
                   
                   <hr />
                   
                   <form method="post"  action="scrp_upload_ktb_sync.php"  enctype="multipart/form-data">
                   
                   <table>
                    	<tr>
                        	<td>
                            
                           Upload Bank Statement เพื่อตรวจสถานะของเช็ค
                            
                            </td>
                          
                            <td>
                            <input name="upload_file" type="file" /> <input name="upload_file_statement" type="submit" value="Upload File" />                            </td>
                        </tr>
                    	
                        
                        <tr>
                    	  <td>&nbsp;</td>
                    	 
                    	  <td>
                          
                          <a href="ktb_sync\bank-statement-example.xls">ตัวอย่างไฟล์นำเข้า</a>
                          
                         
                          </td>
                  	  </tr>
                     
                   </table>
                   
                   </form>
                   
                    <hr />
                   
                   
                   <?php 
				   
				   		if ($_POST['chk_from'] == '1'){
							// "date from" filter
							if ($_POST ['txDateFrom_year'] > 0 && $_POST['txDateFrom_month'] > 0 && $_POST['txDateFrom_day'] > 0) {
								$txDateFrom = DateTime::createFromFormat('Y-m-d', $_POST["txDateFrom_year"] . "-" . $_POST ["txDateFrom_month"] . "-" . $_POST ["txDateFrom_day"]);
							}
							
							// "date to" filter
							if ($_POST ['txDateTo_year'] > 0 && $_POST['txDateTo_month'] > 0 && $_POST['txDateTo_day'] > 0) {
								$txDateTo = DateTime::createFromFormat('Y-m-d', $_POST["txDateTo_year"] . "-" . $_POST ["txDateTo_month"] . "-" . $_POST ["txDateTo_day"]);
							}
						}
						
						
						if (!is_null($txDateFrom)){
							$conditionSQL .= " AND b.RequestDate >= '" . $txDateFrom->format('Y-m-d') . "'";
						}
						
						if (!is_null($txDateTo)){
							$conditionSQL .= " AND b.RequestDate <= '" . $txDateTo->format('Y-m-d') . " 23:59:59.999'";
						}

						$querySQL = "
								SELECT
									b.ID,
									b.ServiceRef1,
									b.ServiceRef2,
									c.CompanyCode,
									c.BranchCode,
									c.CompanyNameThai,
									law.Year,
									b.PaymentDate,
									b.RequestDate,
									b.TransactionPrincipalAmount,
									b.TransactionInterestAmount,
									b.TransactionTotalAmount,
									b.PrincipalAmount,
									b.InterestAmount,
									b.TotalAmount,
									b.PaidTotalAmount,
									b.PaymentMethod,
									ba.bank_name BankName,
									b.ChequeNo,
									COALESCE(r.BookReceiptNo,cp.BookReceiptNo) BookReceiptNo,
									COALESCE(r.ReceiptNo,cp.ReceiptNo) ReceiptNo,
									b.PaymentStatus,
									b.KTBImportDate, 
									b.KTBCancelDate, 
									b.NEPFundExportDate, 
									b.NEPFundImportDate,
									b.NEPFundCancelDate
								FROM bill_payment b
								LEFT JOIN lawfulness law ON law.LID = b.LID
								LEFT JOIN company c ON law.CID = c.CID
								LEFT JOIN bank ba ON ba.BankCode = b.ChequeBankCode
								LEFT JOIN receipt r ON r.RID = b.ReceiptID
								LEFT JOIN cancelled_payment cp ON cp.NEPFundPaymentID = b.NEPFundPaymentID AND b.ReceiptID IS NULL AND b.NEPFundPaymentID IS NOT NULL
								
										
								where 
								1 = 1
								
								$conditionSQL
								
								and
								b.PaymentStatus in (1,9,10,11,12)
								
								
								and 
								concat(b.ServiceRef1, b.ServiceRef2) in (
									
									select
										concat(Ref1, Ref2)
									from
										webservice_log_ktb
								
								)
								
								
								ORDER BY b.PaymentDate DESC
								";
								
						/*
						
						and
						b.PaymentStatus in (10)
						
						and
						b.PaymentMethod = 'Cheque'
						
						*/
						
						//echo $querySQL;
						
						$queryResult = mysql_query($querySQL);
						
						$paymentStatusMapping = getPaymentStatusMapping();
						unset($paymentStatusMapping[0]);
						
						$paymentMethodMapping = getPaymentMethodMapping();
						
						// total records
						$seq = 0;
						
						function writeDate($dateValue){
							if (is_null($dateValue)){
								echo "-";
							}else{
								echo formatDateThai($dateValue);
							}
						}
				   
				   
				   ?>
                   
                   
                   <table border="1" align="center" cellpadding="5" cellspacing="0" style=" font-size: 11px;border-collapse: collapse;">
                    <thead>
                        <tr bgcolor="#efefef" align="center">
                            <td align="center">
                                ลำดับที่
                            </td>
                            <td align="center">
                                Ref 1
                            </td>
                            <td align="center">
                                Ref 2
                            </td>
                            <td align="center">
                                เลขที่บัญชีนายจ้าง
                            </td>
                            <td align="center">
                                รหัสสาขา
                            </td>
                            <td align="center">
                                ชื่อบริษัท
                            </td>
                            <td align="center">
                                สำหรับปี
                            </td>
                            
                            <?php if(1==0){?>
                            <td align="center">
                                เงินต้น
                            </td>
                            <td align="center">
                                ดอกเบี้ย
                            </td>
                            <td align="center">
                                รวม
                            </td>
                            <?php }?>
                            <td align="center">
                                วันที่ชำระเงิน
                            </td>
                            <td align="center">
                                ยอดเงินที่ขอจ่าย
                            </td>
                            <td align="center">
                                ยอดเงินที่จ่ายจริง
                            </td>
                            <td align="center">
                                จ่ายโดย
                            </td>
                            <td align="center">
                                ธนาคาร
                            </td>
                            <td align="center">
                                เลขที่เช็ค
                            </td>
                            <td align="center">
                                เล่มที่ใบเสร็จ
                            </td>
                            <td align="center">
                                เลขที่ใบเสร็จ
                            </td>
                            <td align="center">
                                สถานะ
                            </td>
                            <td align="center">
                                วันที่ธนาคารยกเลิกรายการ
                            </td>
                            <td align="center">
                                วันที่ระบบใบเสร็จยกเลิกรายการ
                            </td>
                           
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // main loop
                    while ($row = mysql_fetch_array($queryResult)) {
                        $seq ++;
						$id=$row[ID];
                    ?>
                        <tr bgcolor="#ffffff" align="center" id="row<?php echo $id; ?>">
                            <td>
                                <div align="center">
                                    <?php echo $seq; ?>
                                </div>
                            </td>
                            <td>
                                <?php echo $row["ServiceRef1"];?>
                            </td>
                            <td>
                                <?php echo $row["ServiceRef2"];?>
                            </td>
                            <td>
                                <?php echo $row["CompanyCode"];?>
                            </td>	
                            <td>
                                <?php echo $row["BranchCode"];?>
                            </td>	
                            <td>
                                <?php echo $row["CompanyNameThai"];?>
                            </td>	
                            <td>
                                <div align="right"><?php echo formatYear($row["Year"]);?></div>
                            </td>
                            
                            <?php if(1==0){?>
                            <td>
                                <div align="right"><?php echo formatNumber($row["PrincipalAmount"]) ;?></div>
                            </td>
                            <td>
                                <div align="right"><?php echo formatNumber($row["InterestAmount"]);?></div>
                            </td>
                            <td>
                                <div align="right"><?php echo formatNumber($row["TotalAmount"]);?></div>
                            </td>
                            <?php }?>
                            <td>
                                <?php writeDate($row["PaymentDate"]); //yoes 20250422 - เปลี่ยนจากเดิม RequestDate เป็น PaymentDate?>
                            </td>
                            <td>
                                <div align="right"><?php 
								
											
											$sum_total_amount += $row["TotalAmount"];
											
											echo formatNumber($row["TotalAmount"]);
										
										
										?></div>
                            </td>
                            
                            <td>
                                <div align="right"><?php  
								
											
											$sum_actual_amount += $row["TotalAmount"];
								
											echo formatNumber($row["TotalAmount"]);
								
								
								?></div>
                            </td>
                            <td>
                                <?php echo $paymentMethodMapping[$row["PaymentMethod"]];?>
                            </td>
                            <td>
                                <?php echo $row["BankName"];?>
                            </td>
                            <td>
                                <?php echo $row["ChequeNo"];?>
                            </td>
                            
                            
                            <?php if(1==1){?>
                            
                                <td>
                                    <?php echo $row["BookReceiptNo"];?>
                                </td>
                                <td>
                                    <?php echo $row["ReceiptNo"];?>
                                </td>
                                <td>									
									<div id="PaymentStatusInfo_<?php echo $id; ?>">
                                    <?php echo $paymentStatusMapping[$row["PaymentStatus"]];
									
									// START : Cheque Clearing Status
									if($row["PaymentStatus"] == 12){
										
									?>	
									
									<div id="PaymentStatus_<?php echo $id; ?>">
									<select id="clearing_status_<?php echo $id; ?>">                                    
                                        <option value="">-- เลือกสถานะ --</option>
                                        <option value="10">เช็คเคลียร์แล้ว</option>
                                        <option value="13">เช็คไม่เคลียร์/เช็ดเด้ง</option>
                                    </select>
                                    
                                    <div id="clearing_date_select_<?php echo $id; ?>">
									วันที่เช็คเคลียร์
									<input type="text" id="clearing_date_<?php echo $id; ?>">                                    
                                    <button id="button_<?php echo $id; ?>">ปรับข้อมูล</button>
									</div>
                                    </div>
									</div>
									<script>
										$("#clearing_date_select_<?php echo $id; ?>").hide();
										$( function() {$( "#clearing_date_<?php echo $id; ?>" ).datepicker({ dateFormat: 'dd-mm-yy' });} );
										$("#clearing_status_<?php echo $id; ?>").change(function() {
											if($(this).val()){ $("#clearing_date_select_<?php echo $id; ?>").show(); } else { $("#clearing_date_select_<?php echo $id; ?>").hide(); }																																
										});
										$("#button_<?php echo $id; ?>").click(function() {
											var clearing_status = $("#clearing_status_<?php echo $id; ?>").val();
											var clearing_date = $("#clearing_date_<?php echo $id; ?>").val();											
											if(clearing_date) {												
												$.post( "ktb_sync.php", { ajax: "request", id: "<?php echo $id; ?>", cstatus: clearing_status, cdate: clearing_date })
													.done(function( data ) {														
														data = $.parseJSON( data );
														if(data.success == 1){
															if(clearing_status == 10){
																$("#PaymentStatusInfo_<?php echo $id; ?>").html(data.status);
															}	
															
															if(clearing_status == 13){
																$("#row<?php echo $id; ?>").hide();
															}
														}
												});
											} else {
													alert("กรุณาระบุวันที่เช็คเคลียร์");
											}
										});
									</script>
                                    <?php
									// END : Cheque Clearing Status
									}
									
									
									?>
                                </td>
                                <td>
                                    <?php writeDate($row["KTBCancelDate"]);?>
                                </td>
                                <td>
                                    <?php writeDate($row["NEPFundCancelDate"]);?>
                                </td>
                            
                            <?php }?>
                            
                        </tr>
                       
                    <?php } //end loop to generate rows?>
                    
                    
                    	 <tr bgcolor="#ffffff" align="center">
                          <td bgcolor="#efefef" colspan="8">
                          
                          <div align="right">
                          	รวม
                          </div>
                          </td>
                         
                          <td>
                           <div align="right"><?php 
								
									echo formatNumber($sum_total_amount);								
								
								?></div></td>
                          <td><div align="right"><?php 
								
									echo formatNumber($sum_actual_amount);								
								
								?></div></td>
                          <td bgcolor="#efefef" colspan="8"></td>
                        </tr>
                        
                    </tbody>
                </table></td>
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


</body>
</html>