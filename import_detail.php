<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';

$mode = NULL;
$logTable = NULL;
if ($_GET ["mode"] == "ktb") {
	$mode = "ktb";
	$logTable = 'ktb_import_log_detail';
} elseif ($_GET ["mode"] == "nepfund") {
	$mode = "nepfund";
	$logTable = 'nepfund_import_log_detail';
} else {
	die ();
}


$logId = NULL;
$year = NULL;
$selectedStatus = NULL;

if (is_numeric($_GET['id'])){
	$logId = intval($_GET['id']);
}

if ($mode=='nepfund'){
	$conditionSQL = "WHERE l.NEPFundImportLogID=$logId";
}elseif ($mode=='ktb'){
	$conditionSQL = "WHERE l.KTBImportLogID=$logId";
}

if (is_numeric($_POST['ddl_year'])){
	$year = intval($_POST['ddl_year']);
}

if (is_numeric($_POST['ddl_status'])){
	$selectedStatus = intval($_POST['ddl_status']);
}

if (!is_null($year)){
	$conditionSQL .= " AND law.Year=$year";
}

if (!is_null($selectedStatus)){
	$conditionSQL .= " AND l.ImportStatus=$selectedStatus";
}

$countSQL = "
		SELECT COUNT(*)
		FROM $logTable l
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
			b.TotalAmount,
			l.ImportStatus,
			l.ErrorMessage
		FROM $logTable l
		LEFT JOIN bill_payment b ON l.BillPaymentID = b.ID
		LEFT JOIN lawfulness law ON law.LID = b.LID
		LEFT JOIN company c ON law.CID = c.CID
		$conditionSQL
		ORDER BY l.Seq ASC
		$the_limit";

$queryResult = mysql_query($querySQL);
$statusMapping = getImportStatusMapping();
// total records
$seq = 0;

include "header_html.php";?>
<td valign="top" style="padding-left: 5px;">
	<h2 class="default_h1" style="margin: 0; padding: 0;">
	<?php 
		if ($mode == 'ktb'){
			echo 'รายละเอียดของการนำเข้าข้อมูลการชำระเงินจากธนาคารกรุงไทย';
		}elseif ($mode == 'nepfund') {
			echo 'รายละเอียดของการนำเข้าข้อมูลใบเสร็จจากระบบกองทุน';
		}
	?>
	</h2>
	<br />

	<form method="post">
		<table style="padding: 10px 0 0px 0;">
			<tr>
				<td bgcolor="#efefef">สถานะ:</td>
				<td>
					<?php echo createDropDownListFromMapping('ddl_status', $statusMapping, $selectedStatus, '--- สถานะ ---'); ?>
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
			<td>
				<div align="center">
					<span class="column_header">ยอดเงิน</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">สถานะ</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">ข้อผิดพลาด</span>
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
			<td>
				<div align="right"><?php echo formatNumber($row["TotalAmount"]);?></div>
			</td>
			<td>
				<?php echo $statusMapping[$row["ImportStatus"]];?>
			</td>
			<td>
				<?php echo $row["ErrorMessage"];?>
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