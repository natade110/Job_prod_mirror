<?php

	include "db_connect.php";

	include "session_handler.php";
	
	error_reporting(1);
	ini_set('max_execution_time', 600);
	ini_set("memory_limit","256M");
	header("Content-type:text/html; charset=UTF-8");
	if($_GET[test]) $testing_mode = true; else $testing_mode = false;	

	$errorMsg = "";
	
	if($_POST[clearing_cancel]) {
		header('Location: ktb_sync.php');
		exit;		
	}

	if($_POST[clearing_confirm]) {
		$cheque_clear= explode(',',$_POST[cheque_clear]);
		$cheque_clear_date= explode(',',$_POST[cheque_clear_date]);
		$cheque_cancel= explode(',',$_POST[cheque_cancel]);
		$cheque_cancel_date= explode(',',$_POST[cheque_cancel_date]);
		// Process Cearing Cheque
		for($i=0;$i<count($cheque_clear);$i++) {
			$id = $cheque_clear[$i];
			$dd = $cheque_clear_date[$i];
			$sql = "update bill_payment set PaymentStatus=10,KTB_cheque_clear_date='$dd' WHERE ID=$id";	
			mysql_query($sql);
			
		}
		
		
		// Process Cancel Cheque
		for($i=0;$i<count($cheque_cancel);$i++) {
			$id = $cheque_cancel[$i];
			$dd = $cheque_cancel_date[$i];
			$sql = "update bill_payment set PaymentStatus=13,KTB_cheque_clear_date='$dd' WHERE ID=$id";	
			mysql_query($sql);
			// Delete receipt, payment temp.
			$sql = "delete r,p from receipt r inner join payment p on r.RID=p.RID 
				where r.NEPFundPaymentID=(select NEPFundPaymentID from bill_payment where id=$id)";
			mysql_query($sql);							
			
		}		
		
		header('Location: ktb_sync.php');
		exit;		
	}	


	
	//print_r($_POST);
	
	//echo  $_FILES["upload_file"]['size'];

	if($_FILES["upload_file"] || $testing_mode){		
		$upload_folder = "ktb_sync/";
		if($testing_mode){
			$file_new_path = $upload_folder."test.xls";
		
		} else {
			//echo "whag";	
			$file_size = $_FILES["upload_file"]['size'];
			$file_type = $_FILES["upload_file"]['type'];		
			$file_name = $_FILES["upload_file"]['name'];
			$file_name_tmp = $_FILES["upload_file"]['tmp_name'];
			$new_file_name = date("ymdhis").rand(00,99)."_".$file_name;
			$file_new_path = $upload_folder.$new_file_name;
			
			//echo $file_new_path; exit();
			
			/*
			echo $file_size;
			echo $file_type;
			echo $file_name;
			exit();
			*/
			
			
			//validation
			
			//echo "file_type: ". $file_type; //exit();
			
			//echo $file_name;
			
			
			if($file_type != "application/vnd.ms-excel"){		
				//none zip = error
				$errorMsg = "ไฟล์ที่อัพโหลด ไม่ใช่ .xlsx";
			}
			if($file_size > 25000000){		
				//none zip = error
				$errorMsg = "ไฟล์ที่อัพโหลด มีขนาดเกิน 25mb"; 
			}
			
			if(!$errorMsg)
				move_uploaded_file($file_name_tmp,$file_new_path);
		
		}
		
		if(!$errorMsg) {
		
			//--- Process Excel file
			/** PHPExcel_IOFactory */
			//error_reporting(E_ALL);
			define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
			require_once './PHPExcel/Classes/PHPExcel/IOFactory.php';
			//echo "included PHPExcel";
			//echo $import_filename; exit();
			
			//echo date('H:i:s') , " Load from Excel2007 file" , EOL; //exit();
			//$objReader = PHPExcel_IOFactory::createReader('Excel2007');
			//$objPHPExcel = $objReader->load("ktb_sync/test.xls");
			
			//$callStartTime = microtime(true);
			//$objPHPExcel = PHPExcel_IOFactory::load("ktb_sync/check_bounced_and_not_bounced.xls");
			$objPHPExcel = PHPExcel_IOFactory::load($file_new_path);
			//$callEndTime = microtime(true);
			//$callTime = $callEndTime - $callStartTime;
			//echo 'Call time to read Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
			
			$data = array();
			$cheque_clear = array();
			$cheque_clear_date = array();
			$cheque_cancel = array();			
			$cheque_cancel_date = array();
			
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			
				$sheet_count++;
				//echo "$sheet_count<br>";

				$row_count = 0;
			
				foreach ($worksheet->getRowIterator() as $row) {
				
					$row_count++;
					$cell_date = "A$row_count";
					$cell_cheque = "E$row_count";
					$cell_amount = "F$row_count";
					
					$cheque_no = trim($worksheet->getCell($cell_cheque)->getValue());
					$amount = floatval(str_replace(',','',$worksheet->getCell($cell_amount)->getValue()));
					$clearing_date = DateTime::createFromFormat('d-m-Y H:i:s',$worksheet->getCell($cell_date)->getValue());
					
					
					if($cheque_no && $clearing_date ) {						
						if(array_key_exists($cheque_no,$data)) {
							if($amount < 0){
								
								$data[$cheque_no][status] = 0;							
								$data[$cheque_no][clearing_date] = $clearing_date;
							}
						} else {
								$data[$cheque_no][clearing_date] = $clearing_date;
								$data[$cheque_no][amount] = $amount;
								$data[$cheque_no][status] = 1;
								
						}
					}
				}
				
			}
		
		// Process bill_payment data
		$cheque_all = array_keys($data);
		$cheque_all_con = implode("','",$cheque_all);
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
				b.PaymentStatus = 12
				
				and
				
				b.ChequeNo IN ('$cheque_all_con') 
				
				and 
				concat(b.ServiceRef1, b.ServiceRef2) in (
					
					select
						concat(Ref1, Ref2)
					from
						webservice_log_ktb
				
				)
				
				
				ORDER BY b.PaymentDate DESC
				";		
		
			//echo $querySQL;
			$queryResult = mysql_query($querySQL);
			$num_rows = mysql_num_rows($queryResult);
			if($num_rows) {
				
				
				
				
			} else {
				$errorMsg = "ไม่พบข้อมูลในระบบ เพื่อทำการปรับปรุงตาม file แนบ";
					
			}
				
				
			
		}	
		
		
		//echo "ok"; exit();
		
	}
	
	//echo "esjat"; exit();
?>
<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >สรุปยอดการชำระเงินรายวัน KTB Online</h2>
<?php if($errorMsg) {
	//----------- Error Report ------------------------
	echo "<h3>มีความผืดพลาดเกิดขึ้น : ".$errorMsg."</h3><br>";
	echo '<a class="ui-button ui-widget ui-corner-all" href="ktb_sync.php">กลับสู่หน้า สรุปยอดการชำระเงินรายวัน KTB Online</a>';
} else {
	//------------ No Error ---------------------------

?>
<b>รายการข้อมูลที่มีการปรับปรุง</b>
                  <table border="1" align="center" cellpadding="5" cellspacing="0" style=" font-size: 11px;border-collapse: collapse;">
                    <thead>
                        <tr bgcolor="#efefef" align="center">
                            <td align="center">ลำดับที่</td>
                            <td align="center">Ref 1</td>
                            <td align="center">Ref 2</td>
                            <td align="center">เลขที่บัญชีนายจ้าง</td>
                            <td align="center">รหัสสาขา</td>
                            <td align="center">ชื่อบริษัท</td>
                            <td align="center">สำหรับปี</td>
                            <td align="center">วันที่ชำระเงิน</td>
                            <td align="center">ยอดเงินที่ขอจ่าย</td>
                            <td align="center">ยอดเงินที่จ่ายจริง</td>
                            <td align="center">จ่ายโดย</td>
                            <td align="center">ธนาคาร</td>
                            <td align="center">เลขที่เช็ค</td>
                            <td align="center">เล่มที่ใบเสร็จ</td>
                            <td align="center">เลขที่ใบเสร็จ</td>
                            <td align="center">สถานะ</td>                           
                        </tr>
					</thead>	
<?php 
				
				require_once 'c2x_include.php';
				$seq=0;
				$paymentStatusMapping = getPaymentStatusMapping();
				unset($paymentStatusMapping[0]);
				
				$paymentMethodMapping = getPaymentMethodMapping();				
				//----- Start Fetch Rows
				while ($row = mysql_fetch_array($queryResult)) {
					$seq ++;
					if($data[$row["ChequeNo"]][status] == 1){
						$status = 10;
						$cheque_clear[] = $row["ID"];
						$cheque_clear_date[] = $data[$row["ChequeNo"]][clearing_date]->format('Y-m-d H:i:s');
					} else {
						$status = 13;
						$cheque_cancel[] = $row["ID"];
						$cheque_cancel_date[] = $data[$row["ChequeNo"]][clearing_date]->format('Y-m-d H:i:s');
					}
					
?>
				<tr bgcolor="#ffffff" align="center">
					<td align="center"><?php echo $seq; ?></td>
					<td align="center"><?php echo $row["ServiceRef1"];?></td>
					<td align="center"><?php echo $row["ServiceRef2"];?></td>
					<td align="center"><?php echo $row["CompanyCode"];?></td>
					<td align="center"><?php echo $row["BranchCode"];?></td>
					<td align="center"><?php echo $row["CompanyNameThai"];?></td>
					<td align="center"><?php echo formatYear($row["Year"]);?></td>
					<td align="center"><?php writeDate($row["RequestDate"]);?></td>
					<td align="center"><?php formatNumber($row["TotalAmount"]);?></td>
					<td align="center"><?php formatNumber($row["TotalAmount"]);?></td>
					<td align="center"><?php echo $paymentMethodMapping[$row["PaymentMethod"]];?></td>
					<td align="center"><?php echo $row["BankName"];?></td>
					<td align="center"><?php echo $row["ChequeNo"];?></td>
					<td align="center"><?php echo $row["BookReceiptNo"];?></td>
					<td align="center"><?php echo $row["ReceiptNo"];?></td>
					<td align="center"><?php echo $paymentStatusMapping[$status]; ?></td>				
				</tr>
					

<?php
				
				
				
				}
				// ------- End Fetch Rows


?>


					</table>
				<br>
				<center>
					<form method="post">
						<input type="submit" name="clearing_confirm" value="ยืนยันการปรับปรุงข้อมูล">
						<input type="submit" name="clearing_cancel" value="ยกเลิกการปรับปรุงข้อมูล">
						<input type="hidden" name="cheque_clear" value="<?php echo implode(',',$cheque_clear);?>">
						<input type="hidden" name="cheque_clear_date" value="<?php echo  implode(',',$cheque_clear_date);?>">
						<input type="hidden" name="cheque_cancel" value="<?php echo  implode(',',$cheque_cancel);?>">
						<input type="hidden" name="cheque_cancel_date" value="<?php echo  implode(',',$cheque_cancel_date);?>">
					
					</form>
				</center>
	
	
<?php } ?>	
</td>
</tr>
<tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
                </td>
				</tr>
</table>
</body>
</html>
<?php

						function writeDate($dateValue){
							if (is_null($dateValue)){
								echo "-";
							}else{
								echo formatDateThai($dateValue);
							}
						}
						
?>					