<?php

	include "db_connect.php";
	
	header('Content-Type: text/html; charset=utf-8');
	
	$the_rid = $_POST[the_rid]*1;
	
	$payment_row = getFirstRow("
	
		select
			*
			, d.CID as the_cid
			, c.year as the_year
		from
			receipt a
				join 
					payment b
						on a.rid = b.rid
				join
					lawfulness c
						on b.lid = c.lid
				join
					company d
						on 
						c.cid = d.cid
		where
			a.RID = '$the_rid'
				
	");
	

	$the_cid = $payment_row[the_cid];
	$the_year = $payment_row[the_year];
	

?>

<div align="center"><strong>แบบฟอร์มขอเงินคืน</strong><br />
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
                                <td><?php 
								
								echo $company_row[CompanyCode];
								
								?></td>
                              </tr>
                              <tr>
                                    <td>สำหรับปี</td>
                                    <td><strong><?php 
										//**toggle payment
										
										
										echo $the_year+543;
										
										
										// ddl_year_payments will only allow to add payment year 2015?></strong></td>
    </tr>
                              <tr>
                                <td>เลขที่หนังสือ</td>
                                <td><?php 
								
								echo $payment_row[BookReceiptNo];
								
								?></td>
                              </tr>
                              <tr>
                                <td>ลงวันที่</td>
                                <td><?php 
								
								echo $payment_row[ReceiptNo];
								
								?></td>
                              </tr>
                              <tr>
                                <td>วันที่จ่าย</td>
                                <td><?php 
								
								echo formatDateThai($payment_row[PaymentDate]);
								
								?></td>
                              </tr>
                              <tr>
                                <td>จำนวนเงิน</td>
                                <td><?php 
								
								echo number_format($payment_row[Amount],2);
								
								?> บาท</td>
                              </tr>
                              <tr>
                                <td>จ่ายโดย</td>
                                <td><?php 
								
								echo formatPaymentName($payment_row[PaymentMethod]);
								
								?></td>
                              </tr>
                              <tr>
                                <td>หมายเหตุ</td>
                                <td><?php
                                
								echo doCleanOutput($payment_row[ReceiptNote]);
								
								?></td>
                              </tr>
                              
                              
  </table>

<br />


<?php if($_POST[do_print]){ ?>

	<script>
		window.print();
	</script>

<?php }else{ ?>
<form method="post" target="_blank">
	<input name="do_print" type="submit"  value="พิมพ์แบบฟอร์มขอเงินคืน" />                    
    <input name="the_rid" type="hidden" value="<?php echo $the_rid;?>"/>            
</form>  
<?php }?>  
 
</div>
