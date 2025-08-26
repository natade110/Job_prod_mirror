<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';

$logId = NULL;

$year = NULL;
$selectedExportMode = NULL;

if (is_numeric($_GET['id'])){
	$logId = intval($_GET['id']);
}

if (is_null($logId)){
	die();
}

$conditionSQL = "WHERE l.NEPFundExportLogID=$logId";

if (is_numeric($_POST['ddl_year'])){
	$year = intval($_POST['ddl_year']);
}

if (is_numeric($_POST['ddl_exportmode'])){
	$selectedExportMode = intval($_POST['ddl_exportmode']);
}

if (!is_null($year)){
	$conditionSQL .= " AND law.Year=$year";
}

if (!is_null($selectedExportMode)){
	$conditionSQL .= " AND l.ExportMode=$selectedExportMode";
}

$countSQL = "
		SELECT COUNT(*)
		FROM nepfund_export_log_detail l
		LEFT JOIN bill_payment b ON l.BillPaymentID = b.ID
		LEFT JOIN lawfulness law ON law.LID = b.LID
		LEFT JOIN company c ON law.CID = c.CID
		$conditionSQL";

$record_count_all = getFirstItem ($countSQL);

$per_page = 20;
$num_page = ceil ( $record_count_all / $per_page );

$cur_page = 1;
if (is_numeric ( $_POST ["start_page"] ) && $_POST ["start_page"] <= $num_page && $_POST ["start_page"] > 0) {
	$cur_page = $_POST ["start_page"];
}

$starting_index = 0;
if ($cur_page > 1) {
	$starting_index = ($cur_page - 1) * $per_page;
}

$the_limit = "limit $starting_index, $per_page";

// ///////////////


$querySQL = "
		SELECT
			l.ID,
			b.ServiceRef1,
			b.ServiceRef2,
			law.Year,
			c.CompanyNameThai,
			b.TransactionPrincipalAmount,
			b.TransactionInterestAmount,
			b.TransactionTotalAmount,
			b.PaidTotalAmount,
			b.PaymentMethod,
			ba.bank_name BankName,
			b.ChequeNo,
			l.ExportMode
		FROM nepfund_export_log_detail l
		LEFT JOIN bill_payment b ON l.BillPaymentID = b.ID
		LEFT JOIN lawfulness law ON law.LID = b.LID
		LEFT JOIN company c ON law.CID = c.CID
		LEFT JOIN bank ba ON ba.BankCode = b.ChequeBankCode
		$conditionSQL
		ORDER BY l.Seq DESC
		$the_limit";

$queryResult = mysql_query($querySQL);
$exportModeMapping = getExportModeMapping();
$paymentMethodMapping = getPaymentMethodMapping();
// total records
$seq = 0;

include "header_html.php";?>
<td valign="top" style="padding-left: 5px;">
	<h2 class="default_h1" style="margin: 0; padding: 0;">รายละเอียดของการส่งออกข้อมูลการชำระเงินไประบบกองทุน</h2>
	<br />

	<form method="post">
		<table style="padding: 10px 0 0px 0;">
			<tr>
				<td bgcolor="#efefef">ประเภทของการส่งออกข้อมูล:</td>
				<td>
					<?php echo createDropDownListFromMapping('ddl_exportmode', $exportModeMapping, $selectedExportMode, '--- ประเภทของการส่งออกข้อมูล ---'); ?>
				</td>
				<td bgcolor="#efefef">สำหรับปี:</td>
				<td><?php include "ddl_year_with_blank.php";?></td>
				<td bgcolor="#efefef"><input type="submit" value="แสดง"
					name="mini_search" /></td>
			</tr>

			<tr>
				<td colspan="5">
					<div align="left">
						<select name="start_page" onchange="this.form.submit()"><?php
							for($i = 1; $i <= $num_page; $i ++) {?>
							<option value="<?php echo $i;?>"
								<?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
							<?php }?>
						</select>
					</div>
				</td>
			</tr>
		</table>
	</form>

	<table border="1" cellspacing="0" cellpadding="5"
		style="border-collapse: collapse;" width="100%">
		<tr bgcolor="#9C9A9C" align="center">
			<td>
				<div align="center">
					<span class="column_header">ลำดับที่</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">Ref 1</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">Ref 2</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">ปีที่นำส่ง</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">ชื่อบริษัท</span>
				</div>
			</td>
<!-- 			<td> -->
<!-- 				<div align="center"> -->
<!-- 					<span class="column_header">ยอดเงินต้น</span> -->
<!-- 				</div> -->
<!-- 			</td> -->
<!-- 			<td> -->
<!-- 				<div align="center"> -->
<!-- 					<span class="column_header">ดอกเบี้ย</span> -->
<!-- 				</div> -->
<!-- 			</td> -->
<!-- 			<td> -->
<!-- 				<div align="center"> -->
<!-- 					<span class="column_header">รวม</span> -->
<!-- 				</div> -->
<!-- 			</td> -->
			<td>
				<div align="center">
					<span class="column_header">ยอดเงินที่ชำระ</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">วิธีการชำระเงิน</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">ธนาคารของเช็ค</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">หมายเลขเช็ค</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">ประเภทของการส่งออกข้อมูล</span>
				</div>
			</td>
		</tr>
		<?php
		$seq = $starting_index;
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
				<div align="right"><?php echo formatYear($row["Year"]);?></div>
			</td>
			<td>
				<?php echo $row["CompanyNameThai"];?>
			</td>
<?php if (false){?>			
			<td>
				<div align="right"><?php echo formatNumber($row["TransactionPrincipalAmount"]) ;?></div>
			</td>
			<td>
				<div align="right"><?php echo formatNumber($row["TransactionInterestAmount"]);?></div>
			</td>
			<td>
				<div align="right"><?php echo formatNumber($row["TransactionTotalAmount"]);?></div>
			</td>
<?php }?>
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
				<?php echo $exportModeMapping[$row["ExportMode"]];?>
			</td>
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

</div>
<!--end page cell-->
</td>
</tr>
</table>
</body>
</html>