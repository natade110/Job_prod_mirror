<?php

	include "db_connect.php";
	
	header('Content-Type: text/html; charset=utf-8');
	
	$the_invoice_id = $_GET[invoice_id]*1;
	
	//yoes 20220108
	$table_name = "invoices";
	$item_table_name = "invoice_items";
	
		
	if($_GET[is_demo]){
		$is_demo = 1;		
		$table_name = "invoices_demo";
		$item_table_name = "invoice_items_demo";
	}
	
	$invoice_row = getFirstRow("
	
		select
			*
		from
			$table_name
		where
			invoice_id = '$the_invoice_id'
				
	");
	

	$the_cid = $invoice_row[invoice_cid];
	$the_year = $invoice_row[invoice_lawful_year];
	
	
	
	if($the_year >= 2018 && $the_year <= 2500 ){ 
							  
		//yoes 20190206
		//skip this if new law for now
		
		
		$do_hide_invoice_details = 1;
	
	}
	

?>

<div align="center"> <strong>ใบชำระเงิน<?php if($is_demo){echo " ---(ตัวอย่าง)--- ";}?></strong> <br />
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
                                <td>วันที่ออกใบชำระเงิน</td>
                                <td>
								
								<strong><?php 
								
								
									$this_date_time = date("Y-m-d");
								
									echo formatDateThai($this_date_time);?></strong>
                                
                                </td>
                              </tr>


                            <tr>
                                <td>จำนวนลูกจ้าง</td>
                                <td>

                                   <?php echo number_format($invoice_row[invoice_employees],0);?> คน

                                </td>
                            </tr>
                            <tr>
                                <td>อัตราส่วนลูกจ้างต่อคนพิการ</td>
                                <td>

                                  100:1 = <?php echo getEmployeeRatio($invoice_row[invoice_employees],100);?> คน

                                </td>
                            </tr>
                            <tr>
                                <td>รับคนพิการเข้าทำงานตาม ม.33</td>
                                <td>

                                   <?php echo number_format($invoice_row[invoice_33],0);?> คน

                                </td>
                            </tr>
                            <tr>
                                <td>ให้สัมปทานฯ ตาม ม.35</td>
                                <td>

                                   <?php echo number_format($invoice_row[invoice_35],0);?> คน

                                </td>
                            </tr>
                            <tr>
                                <td>ต้องจ่ายเงินแทนการรับคนพิการ</td>
                                <td>

                                    <?php echo number_format(getEmployeeRatio($invoice_row[invoice_employees],100) - $invoice_row[invoice_33]-$invoice_row[invoice_35],0);?> คน

                                </td>
                            </tr>

                              
  </table>
<br />
<table border="1" cellpadding="5" style="border-collapse:collapse;" >
  
  <tr>
    <td>วันที่ชำระเงิน</td>
    <td colspan="3"><strong>
      <?php 
								
								
									
								
									echo formatDateThai($invoice_row[invoice_payment_date]);?>
    </strong></td>
    </tr>
  <tr <?php if($do_hide_invoice_details){?>style="display:none;"<?php }?>>
    <td bgcolor="#FFF9F9">เงินต้นคงเหลือ</td>
    <td bgcolor="#FFF9F9"><?php 
	
		//yoes 20181125
		
		/*echo number_format(		
			$invoice_row[invoice_owned_principal]			
			+$invoice_row[m33_total_missing]
			+$invoice_row[m35_total_missing]			
			,2);*/
			
		echo number_format(		
			$invoice_row[invoice_owned_principal]			
			,2);
		
	?>
      บาท</td>
    <td bgcolor="#FFF9F9">ดอกเบี้ยค้างชำระ</td>
    <td bgcolor="#FFF9F9"><?php 
	
		echo number_format(
		
			$invoice_row[invoice_owned_interest]
			+$invoice_row[m33_total_interests]
			+$invoice_row[m35_total_interests]
			
			,2);
		
	?>
      บาท</td>
    </tr>
	
  <tr>
    <td bgcolor="#E6F2FF">จำนวนเงินที่ต้องการชำระ</td>
    <td bgcolor="#E6F2FF"> 
      <?php 
	
		echo number_format($invoice_row[invoice_amount],2);
		
	?> บาท</td>
    <td bgcolor="#E6F2FF">&nbsp;</td>
    <td bgcolor="#E6F2FF">&nbsp;</td>
    </tr>
	
  <tr <?php if($do_hide_invoice_details){?>style="display:none;"<?php }?>>
    <td bgcolor="#E6F2FF">ชำระเป็นเงินต้น</td>
    <td bgcolor="#E6F2FF"><?php 
	
		
		/*
		$owned_money = $invoice_row[invoice_owned_principal]
			+$invoice_row[m33_total_missing]
			+$invoice_row[m35_total_missing]
		;*/
		
		//yoes 20190130 -> owned money no need to include m33+35 because it already included in invoice.php
		$owned_money = $invoice_row[invoice_owned_principal];
		
		$pay_for_start = $invoice_row[invoice_principal_amount];
		
		//จ่ายเกิน vs จ่ายขาด
		
		/*
		echo "<br>owned_money: $owned_money";
		echo "<br>pay_for_start: $pay_for_start";
		*/
									
		if($owned_money < $pay_for_start){
			
			echo number_format($owned_money,2);
			$extra_paid = $pay_for_start- $owned_money;
			
		}elseif($owned_money > $pay_for_start){
			
			echo number_format($pay_for_start,2);
			$missing_paid = $owned_money - $pay_for_start;
			
		}else{
		
			echo number_format($pay_for_start,2);
		
		}
		
		
		//echo number_format($invoice_row[invoice_principal_amount],2);
		
	?>
      บาท</td>
    <td bgcolor="#E6F2FF">ชำระเป็นดอกเบี้ย</td>
    <td bgcolor="#E6F2FF"><?php 
	
		echo number_format($invoice_row[invoice_interest_amount],2);
		
	?>
      บาท</td>
    </tr>
	
	
	<tr <?php if($do_hide_invoice_details){?>style="display:none;"<?php }?>>
    <td bgcolor="#E6F2FF">เงินต้นค้างชำระ</td>
    <td bgcolor="#E6F2FF"><?php 
	
		
		
		//echo number_format($invoice_row[invoice_principal_amount],2);
						
		
		if($extra_paid){
			
			echo "<font color='green'>ชำระเกิน ".number_format($extra_paid,2)." บาท</font>";	
			
		}elseif($missing_paid){
			
			echo "<font color='red'>".number_format($missing_paid,2)." บาท</font>";	
			
		}else{
			echo "0.00 บาท";
		}
		
	?></td>
    <td bgcolor="#E6F2FF">ดอกเบี้ยค้างชำระ</td>
    <td bgcolor="#E6F2FF"><?php 
	
		$total_interests = 
		
				(
					$invoice_row[invoice_owned_interest] 
					+$invoice_row[m33_total_interests]
					+$invoice_row[m35_total_interests]
				)
		
				- $invoice_row[invoice_interest_amount]
				;
	
		if($total_interests){
	
			echo "<font color='red'>";
			
		}else{
			echo "<font>";
		}
		
		echo number_format($total_interests,2);
		
		echo " บาท</font>";
		
	?></td>
    </tr>
	
	
  <tr>
    <td height="34">หมายเหตุ</td>
    <td colspan="3">
    <?php if($is_demo){echo " ---(ตัวอย่าง)--- ";}?>
    <?php 
	
		echo $invoice_row[invoice_remarks];
	?>
    
    </td>
    </tr>
  <tr>
    <td>เจ้าหน้าที่ผู้ออกใบชำระเงิน</td>
    <td colspan="3"> 
    <?php 
	
		echo $invoice_row[invoice_userid_text];
	?></td>
    </tr>
</table>
<br />
<table border="1"  cellpadding="5" style="border-collapse:collapse;"  >
  
  <tr>
    <td>เลขที่ใบชำระเงิน</td>
    <td><strong>
	
		<?php 
		
			if($is_demo){
				echo " ---(ตัวอย่าง)--- ";				
			}else{								
				echo $invoice_row[invoice_id].$invoice_row[invoice_cid];								
			}
			
		?>
    </strong></td>
  </tr>
  
  
  <?php if(1==1 && !$is_demo){?>
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
		$barcodeObj = new CustomTCPDFBarcode($invoice_row[invoice_id].$invoice_row[invoice_cid], "C128");
		
		
	
	?>
    <div align="center">
    
    
    	<?php 
		
		echo $barcodeObj->getBarcodeCustomHTML();
		
		?>
        
        
    	<?php if(1==0){?>
    	<img src="decors/dummy_bar.jpg" />
        <?php }?>
    
    </div>    
    </td>
    </tr>
  <?php }?>
  
  
</table>

<br />


<?php if($_POST[do_print]){ ?>

	<script>
		window.print();
	</script>

<?php }else{ ?>
<form method="post" target="_blank">
	<input name="do_print" type="submit"  value="พิมพ์ใบชำระเงิน" />                                
</form>  
<?php }?>  
 
</div>
