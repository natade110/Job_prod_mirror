<?php

	include "db_connect.php";
	
	header('Content-Type: text/html; charset=utf-8');
	
	$cancel_id = $_GET[cancel_id]*1;
	
	$cencel_row = getFirstRow("
	
		select
			*
		from
			receipt_cancel_requests
		where
			cancel_id = '$cancel_id'
				
	");
	


	//
	$receipt_row = getFirstRow("
		
		
		select
			*
			, d.cid as the_cid
		from
			receipt a
				join
				 payment b
				 	on a.RID = b.RID
				join
					lawfulness c
					on
					b.lid = c.lid
				join
					company d
					on
					c.cid = d.cid
		where
			a.RID = '".$cencel_row[cancel_rid]."'
	
	");
	
	
	
	
	
	$the_cid = $receipt_row[the_cid];
	$the_year = $receipt_row[ReceiptYear];
	

?>

<div align="center"> <strong>ใบขอยกเลิกใบเสร็จ</strong> <br />
  <br />


<table border="1"  cellpadding="5" style="border-collapse:collapse;" >
                            
                              <?php if($sess_accesslevel !=4){?>
                              <?php } ?>
                              
                              <tr>
                                <td>สถานประกอบการ</td>
                                <td>
                                
                                
                                <strong><?php 
								
								
								
								$company_row = getFirstRow("select * from company where cid = '$the_cid'");
								
								echo formatCompanyName($company_row[CompanyNameThai],$company_row[CompanyTypeCode]);
								
								
								?></strong></td>
                              </tr>
                              <tr>
                                <td>เลขทะเบียนนายจ้าง</td>
                                <td><strong><?php 
								
								echo $company_row[CompanyCode];
								
								?></strong></td>
                              </tr>
                              <tr>
                                    <td>สำหรับปี</td>
                                    <td><strong><?php 
										//**toggle payment
										
										
										echo $the_year+543;
										
										
										// ddl_year_payments will only allow to add payment year 2015?></strong></td>
    </tr>
                              <tr>
                                <td>วันที่ออกใบยกเลิกใบเสร็จ</td>
                                <td>
								
								<strong><?php 
								
								
									$this_date_time = date("Y-m-d");
								
									echo formatDateThai($this_date_time);?></strong>
                                
                                </td>
                              </tr>
                              
  </table>
    
    
    <br />
    
    
    
    <div align="center">
    
    	ใบเสร็จที่จะทำการยกเลิก
    
    </div>
    <table border="1"  cellpadding="5" style="border-collapse: collapse; " >
                              
                              
                              <tr>
                                <td>#</td>
                                <td>ใบเสร็จเล่มที่</td>
                                <td>เลขที่ใบเสร็จ</td>
                              </tr>
                              <tr>
                                <td>
                                
                                <div align="right">
                                1
                                </div>
                                
                                </td>
                                <td><?php 
									
									echo $receipt_row[BookReceiptNo];
								
								?></td>
                                <td><?php 
									
									echo $receipt_row[ReceiptNo];
								
								?></td>
                              </tr>
                             
                             
                             
                              <?php 
											
												
												$related_reciept_sql = "select
														*
													from
														receipt
													where
														NEPFundPaymentId = '".$receipt_row["NEPFundPaymentID"]."'
														and
														RID != '".$receipt_row[RID]."'";
											
											
												$related_reciept = mysql_query($related_reciept_sql);
												
												
												$count_receipt = 1;
											
											while($related_array = mysql_fetch_array($related_reciept)){
												
												
												$count_receipt++;
											?>
                                            
                                            
                                            	<tr>
                                                <td>
                                                
                                                <div align="right">
                                                <?php echo $count_receipt;?>
                                                </div>
                                                
                                                </td>
                                                <td><?php 
                                                    
                                                    echo $related_array[BookReceiptNo];
                                                
                                                ?></td>
                                                <td><?php 
                                                    
                                                    echo $related_array[ReceiptNo];
                                                
                                                ?></td>
                                              </tr>
                                            
                                            <?php											
												
											}
											
											?>
                              
  </table>
<br />
<table border="1" cellpadding="5" style="border-collapse:collapse;" >
  <tr>
    <td height="34">หมายเหตุ</td>
    <td>
      
      <?php 
	
		echo $cencel_row[cancel_reason];
	?>
      
      </td>
  </tr>
  <tr>
    <td>เจ้าหน้าที่ผู้ขอยกเลิกใบเสร็จ</td>
    <td> 
    <?php 
	
		echo $cencel_row[cancel_userid_text];
	?></td>
    </tr>
</table>
<br />
<table border="1"  cellpadding="5" style="border-collapse:collapse;"  >
  <?php if($sess_accesslevel !=4){?>
  <?php } ?>
  <tr>
    <td>เลขที่ใบขอยกเลิกใบเสร็จ</td>
    <td><strong>
      <?php 
								
								
								echo $cencel_row[cancel_id].$cencel_row[cancel_tid];
								
								
								?>
    </strong></td>
  </tr>
  <tr>
  	<td>
    </td>
    <td>
    
    
    <style>
	
		.barcode1px {
            border-left: 1px solid black;
            position: absolute;
            height: 1cm;
        }

        .barcode2px {
            border-left: 2px solid black;
            position: absolute;
            height: 1cm;
        }

        .barcode3px {
            border-left: 3px solid black;
            position: absolute;
            height: 1cm;
        }

        .barcode4px {
            border-left: 4px solid black;
            position: absolute;
            height: 1cm;
        }
	
	</style>
    <?php 
	
		require_once 'custom_tcpdf_barcodes_1d_02.php';
		
		
		//$barcode = "|$taxId$serviceCode\r$ref1\r$ref2\r$amountInBarcode";
		//$barcode = "|99900\r591002737389\r3504\r30162300\r";
		$barcodeObj = new CustomTCPDFBarcode($cencel_row[cancel_id].$cencel_row[cancel_tid], "C128");
		
		
	
	?>
    <div align="center">
    
    	<?php 
		
		echo $barcodeObj->getBarcodeCustomHTML();
		
		?>
        
    
    </div>    
    </td>
    </tr>
</table>

<br />


<?php if($_POST[do_print]){ ?>

	<script>
		window.print();
	</script>

<?php }else{ ?>
<form method="post" target="_blank">
	<input name="do_print" type="submit"  value="พิมพ์ใบขอยกเลิกใบเสร็จ" />                                
</form>  
<?php }?>  
 
</div>
