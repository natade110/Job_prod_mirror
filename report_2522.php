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

if (!is_null($year)){
	$conditionSQL .= " AND Year=$year";
}

if (!is_null($txDateFrom)){
	$conditionSQL .= " AND LogTime >= '" . $txDateFrom->format('Y-m-d') . "'";
}

if (!is_null($txDateTo)){
	$conditionSQL .= " AND LogTime <= '" . $txDateTo->format('Y-m-d') . " 23:59:59.999'";
}


if (!is_null($province)){
	$conditionSQL .= " AND e.Province = $province";
}

// }

$the_limit = '';//"limit $starting_index, $per_page";

// ///////////////


$querySQL = "
		
		SELECT 
		
			* 
		FROM
			`webservice_log_ktb` a
				left join 
					bill_payment b
					on 
					a.Ref1 = b.ServiceRef1
					and
					a.Ref2 = b.ServiceRef2
				left join
					receipt c
					on
					b.ReceiptID = c.RID
				left join
					lawfulness d
						on b.LID = d.LID
				left join
					company e
						on d.CID = e.CID
				left join
					provinces f
						on e.Province = f.province_id
						
		where
			1=1
			$conditionSQL
		order by
			LogTime Desc
		
		";

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
		<strong>Transaction Log การจ่ายเงินผ่านระบบธนาคารกรุงไทย online</strong>
		<br />
	</div>
	<table border="1" align="center" cellpadding="5" cellspacing="0" style="border-collapse: collapse;font-size:<?php echo !$is_pdf ? 14:28; ?>px">
		<thead>
			<tr bgcolor="#9C9A9C" align="center">
				<td align="center">
					ลำดับที่
				</td>
				<td align="center">
					TransactionID
				</td>
				<td align="center">
					Method ที่เรียกใช้
				</td>
				<td align="center">
					Ref1
				</td>
				<td align="center">
					Ref2
				</td>
				<td align="center">
					Request XML
				</td>
				<td align="center">
					Response XML
				</td>
				<td align="center">
					วันที่ของ Transaction
				</td>
				<td>ปีการปฏิบัติตามกฎหมาย</td>
                <td>สถานประกอบการ</td>
                <td>จังหวัด</td>
                <td>สถานะ<br />การปฏิบัติตามกฎหมาย</td>
				
				
				<td><div align="center">ใบเสร็จเล่มที่</div></td>
				<td><div align="center">ใบเสร็จเลขที่</div></td>
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
					<?php echo $row["TransactionID"];?>
				</td>
				<td>
					<?php echo $row["Method"];?>
				</td>
				<td>
					<?php echo $row["Ref1"];?>
				</td>	
				<td>
					<?php echo $row["Ref2"];?>
				</td>	
				<td>
					<font color="#FF0000">
						<?php //echo htmlentities($row["Request"]);
                        
                        echo "*XML Request*";?>
                    </font>
				</td>	
				<td>
                	<font color="#FF0000">
						<?php //echo htmlentities($row["Response"]);
                        
                        
                        echo "*XML Response*";?>
                    </font>
				</td>
				<td>
					<?php echo formatDateThai($row["LogTime"],1,1);
					//echo $row["LogTime"];?>
				</td>
				<td><?php echo $row[Year]+543?></td>
                <td><?php echo formatCompanyName($row[CompanyNameThai], $row[CompanyTypeCode])?></td>
                <td><?php echo $row[province_name]?></td>
				
				<td><?php echo getLawfulText($row[LawfulStatus])?></td>
				<td><?php echo $row[BookReceiptNo]?></td>
				<td><?php echo $row[ReceiptNo]?></td>
			</tr>
		<?php } //end loop to generate rows?>
		</tbody>
	</table>
	<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?></div>
    <div align="right">กรณีที่ต้องการดูรายละเอียด XML Request และ XML Response กรุณาติดต่อเจ้าหน้าที่ผู้ดูแลระบบ</div>