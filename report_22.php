<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';


if($_POST["report_format"] == "excel"){
	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_22.xls");

	$is_excel = 1;
}elseif($_POST["report_format"] == "words"){
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=report_22.doc");
}elseif($_POST["report_format"] == "pdf"){
	$is_pdf = 1;
}else{
	header ('Content-type: text/html; charset=utf-8');
}

$year = NULL;
$province = NULL;
$txDateFrom  = NULL;
$txDateTo = NULL;
$ref1 = NULL;
$ref2 = NULL;
$companyName = NULL;
$companyCode = NULL;
$bookReceiptNo = NULL;
$receiptNo = NULL;
$selectedPaymentStatus = NULL;
$bank = NULL;
$chequeNo = NULL;


$conditionSQL = '';

if (is_numeric($_POST['ddl_paymentstatus'])){
	$selectedPaymentStatus = intval($_POST['ddl_paymentstatus']);
}

if (is_numeric($_POST['ddl_year'])){
	$year = intval($_POST['ddl_year']);
}

if (is_numeric($_POST['Province'])){
	$province = intval($_POST['Province']);
}

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

if ($_POST['Ref1'] != ""){
	$ref1 = $_POST['Ref1'];
}

if ($_POST['Ref2'] != ""){
	$ref2 = $_POST['Ref2']; 
}

if ($_POST['CompanyCode'] != ""){
	$companyCode = $_POST['CompanyCode'];
}

if ($_POST['CompanyName'] != ""){
	$companyName = $_POST['CompanyName']; 
}

if ($_POST['BookReceiptNo'] != ""){
	$bookReceiptNo = $_POST['BookReceiptNo'];
}

if ($_POST['ReceiptNo'] != ""){
	$receiptNo = $_POST['ReceiptNo']; 
}

if ($_POST['check_bank'] != ""){
	$bank = $_POST['check_bank'];
}

if ($_POST['ChequeNo'] != ""){
	$chequeNo = $_POST['ChequeNo']; 
}

if (!is_null($selectedPaymentStatus)){
	$conditionSQL = "WHERE b.PaymentStatus=$selectedPaymentStatus";
}else{
	$conditionSQL = 'WHERE b.PaymentStatus <> 0';
}

if (!is_null($year)){
	$conditionSQL .= " AND law.Year=$year";
}

if (!is_null($txDateFrom)){
	$conditionSQL .= " AND b.PaymentDate >= '" . $txDateFrom->format('Y-m-d') . "'";
}

if (!is_null($txDateTo)){
	$conditionSQL .= " AND b.PaymentDate <= '" . $txDateTo->format('Y-m-d') . " 23:59:59.999'";
}

if (!is_null($ref1)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('b.ServiceRef1', $ref1);
}

if (!is_null($ref2)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('b.ServiceRef2', $ref2);
}

if (!is_null($companyName)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('c.CompanyNameThai', $companyName);
}

if (!is_null($companyCode)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('c.CompanyCode', $companyCode);
}

if (!is_null($bookReceiptNo)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('r.BookReceiptNo', $bookReceiptNo);
}

if (!is_null($receiptNo)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('r.ReceiptNo', $receiptNo);
}

if (!is_null($bank)){
	$conditionSQL .= " AND ba.bank_id=$bank";
}

if (!is_null($chequeNo)){
	$conditionSQL .= ' AND ' . createLikeSearchQuery('b.ChequeNo', $chequeNo);
}

if (!is_null($province)){
	$conditionSQL .= " AND c.Province = $province";
}

// $countSQL = "
// 		SELECT COUNT(*)
// 		FROM bill_payment b
// 		LEFT JOIN lawfulness law ON law.LID = b.LID
// 		LEFT JOIN company c ON law.CID = c.CID
// 		LEFT JOIN bank ba ON ba.BankCode = b.ChequeBankCode
//         LEFT JOIN receipt r ON r.RID = b.ReceiptID
// 		$conditionSQL";

// $record_count_all = getFirstItem ($countSQL);

// $per_page = 20;
// $num_page = ceil ( $record_count_all / $per_page );

// $cur_page = 1;
// if (is_numeric ( $_POST ["start_page"] ) && $_POST ["start_page"] <= $num_page && $_POST ["start_page"] > 0) {
// 	$cur_page = $_POST ["start_page"];
// }

// $starting_index = 0;
// if ($cur_page > 1) {
// 	$starting_index = ($cur_page - 1) * $per_page;
// }

$the_limit = '';//"limit $starting_index, $per_page";

// ///////////////


$querySQL = "
		SELECT
			b.ServiceRef1,
			b.ServiceRef2,
			c.CompanyCode,
			c.BranchCode,
			c.CompanyNameThai,
			law.Year,
			b.PaymentDate,
			b.TransactionPrincipalAmount,
			b.TransactionInterestAmount,
			b.TransactionTotalAmount,
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
		$conditionSQL
		
		and 
		concat(b.ServiceRef1, b.ServiceRef2) not in (
			
			select
				concat(Ref1, Ref2)
			from
				webservice_log_ktb
		
		)
		
		ORDER BY b.PaymentDate DESC
		$the_limit";

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
	<div align="center">
		<strong>รายละเอียดของการชำระเงินผ่านธนาคาร<br />กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
		<br />
	</div>
	<table border="1" align="center" cellpadding="5" cellspacing="0" style="border-collapse: collapse;font-size:<?php echo !$is_pdf ? 14:28; ?>px">
		<thead>
			<tr bgcolor="#9C9A9C" align="center">
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
				<td align="center">
					เงินต้น
				</td>
				<td align="center">
					ดอกเบี้ย
				</td>
				<td align="center">
					รวม
				</td>
				<td align="center">
					วันที่ชำระเงิน
				</td>
				<td align="center">
					ยอดเงินที่จ่าย
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
		?>
			<tr bgcolor="#ffffff" align="center">
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
				<td>
					<div align="right"><?php echo formatNumber($row["TransactionPrincipalAmount"]) ;?></div>
				</td>
				<td>
					<div align="right"><?php echo formatNumber($row["TransactionInterestAmount"]);?></div>
				</td>
				<td>
					<div align="right"><?php echo formatNumber($row["TransactionTotalAmount"]);?></div>
				</td>
				<td>
					<?php writeDate($row["PaymentDate"]);?>
				</td>
				<td>
					<div align="right"><?php echo formatNumber($row["PaidTotalAmount"]);?></div>
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
				<td>
					<?php echo $row["BookReceiptNo"];?>
				</td>
				<td>
					<?php echo $row["ReceiptNo"];?>
				</td>
				<td>
					<?php echo $paymentStatusMapping[$row["PaymentStatus"]];?>
				</td>
				<td>
					<?php writeDate($row["KTBCancelDate"]);?>
				</td>
				<td>
					<?php writeDate($row["NEPFundCancelDate"]);?>
				</td>
			</tr>
		<?php } //end loop to generate rows?>
		</tbody>
	</table>
	<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?></div>