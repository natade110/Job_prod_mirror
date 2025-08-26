<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';

$mode = NULL;
$logTable = NULL;
if ($_GET ["mode"] == "ktb") {
	$mode = "ktb";
	$logTable = 'ktb_import_log';
} elseif ($_GET ["mode"] == "nepfund") {
	$mode = "nepfund";
	$logTable = 'nepfund_import_log';
} else {
	die ();
}

$conditionSQL = '';
$importDateTo = NULL;// new DateTime();
$importDateFrom = NULL;// DateTime::createFromFormat('Y-m-d', $importDateTo->format('Y-m').'-1');
$selectedStatus = NULL;

// "date from" filter
if ($_POST ['ImportDateFrom_year'] > 0 && $_POST['ImportDateFrom_month'] > 0 && $_POST['ImportDateFrom_day'] > 0) {
	$importDateFrom = DateTime::createFromFormat('Y-m-d', $_POST["ImportDateFrom_year"] . "-" . $_POST ["ImportDateFrom_month"] . "-" . $_POST ["ImportDateFrom_day"]);
}

// "date to" filter
if ($_POST ['ImportDateTo_year'] > 0 && $_POST['ImportDateTo_month'] > 0 && $_POST['ImportDateTo_day'] > 0) {
	$importDateTo = DateTime::createFromFormat('Y-m-d', $_POST["ImportDateTo_year"] . "-" . $_POST ["ImportDateTo_month"] . "-" . $_POST ["ImportDateTo_day"]);
}

if (is_numeric($_POST['ddl_status'])){
	$selectedStatus = intval($_POST['ddl_status']);
}

if (!is_null($importDateFrom)){
	if (!empty($conditionSQL)){
		$conditionSQL .= ' AND ';
	}
	$conditionSQL .= "l.ImportDate >= '" . $importDateFrom->format('Y-m-d') . "'";
}

if (!is_null($importDateTo)){
	if (!empty($conditionSQL)){
		$conditionSQL .= ' AND ';
	}
	$conditionSQL .= "l.ImportDate <= '" . $importDateTo->format('Y-m-d') . " 23:59:59.999'";
}

if (!is_null($selectedStatus)){
	if (!empty($conditionSQL)){
		$conditionSQL .= ' AND ';
	}
	$conditionSQL .= "l.ImportStatus = $selectedStatus";
}

if (!empty($conditionSQL)){
	$conditionSQL = "WHERE ".$conditionSQL;
}

$countSQL = "
		SELECT COUNT(*)
		FROM $logTable l
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
			l.ImportDate,
			l.NumberOfRow,
			l.ImportStatus,
			l.Filename
		FROM $logTable l
		$conditionSQL
		ORDER BY l.ID DESC
		$the_limit";

$queryResult = mysql_query($querySQL);
$statusMapping = getImportStatusMapping();

// total records
$seq = 0;

function createDetailLink($row, $mode, $text)
{?>
	<a href="<?php echo "import_detail.php?id=${row['ID']}&mode=$mode"; ?>"><?php echo $text; ?></a>
<?php 
}
include "header_html.php";?>
<td valign="top" style="padding-left: 5px;">
	<h2 class="default_h1" style="margin: 0; padding: 0;">
	<?php 
		if ($mode == 'ktb'){
			echo 'รายงานการนำเข้าข้อมูลการชำระเงินจากธนาคารกรุงไทย';
		}elseif ($mode == 'nepfund') {
			echo 'รายงานการนำเข้าข้อมูลใบเสร็จจากระบบกองทุน';
		}
	?>
	</h2>
	<br />

	<form method="post">
		<table style="padding: 10px 0 0px 0;">
			<tr>
				<td bgcolor="#efefef">ตั้งแต่วันที่:</td>
				<td><?php
					$selector_name = "ImportDateFrom";
					$this_date_time = is_null($importDateFrom) ? '0000-00-00' : $importDateFrom->format('Y-m-d');
					include ("date_selector.php");
				?>
				</td>
				<td bgcolor="#efefef">ถึงวันที่:</td>
				<td><?php
					$selector_name = "ImportDateTo";
					$this_date_time = is_null($importDateTo) ? '0000-00-00' : $importDateTo->format('Y-m-d');
					include ("date_selector.php");
				?>
				</td>
				<td bgcolor="#efefef">&nbsp;</td>
			</tr>
			<tr>
				<td bgcolor="#efefef">สถานะ:</td>
				<td>
					<?php echo createDropDownListFromMapping('ddl_status', $statusMapping, $selectedStatus, '--- สถานะ ---'); ?>
				</td>
				<td></td>
				<td></td>
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
					<span class="column_header">วันที่นำเข้า</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">จำนวนรายการ</span>
				</div>
			</td>
			<td><div align="center">
					<span class="column_header">สถานะ</span>
				</div></td>
			<td>
				<div align="center">
					<span class="column_header">ไฟล์ที่นำเข้า</span>
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
					<?php echo createDetailLink($row, $mode, $seq); ?>
				</div>
			</td>
			<td>
				<?php echo createDetailLink($row, $mode, formatDateThai($row["ImportDate"])); ?>
			</td>
			<td>
				<div align="right"><?php echo $row["NumberOfRow"];?></div>
			</td>
			<td>
				<div align="right"><?php echo $statusMapping[$row["ImportStatus"]];?></div>
			</td>
			<td>
				<?php echo $row["Filename"];?>
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