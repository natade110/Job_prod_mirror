<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';


$conditionSQL = '';
$exportDateTo = NULL;// new DateTime();
$exportDateFrom = NULL;// DateTime::createFromFormat('Y-m-d', $exportDateTo->format('Y-m').'-1');
$selectedStatus = NULL;

// "date from" filter
if ($_POST ['exportDateFrom_year'] > 0 && $_POST['exportDateFrom_month'] > 0 && $_POST['exportDateFrom_day'] > 0) {
	$exportDateFrom = DateTime::createFromFormat('Y-m-d', $_POST["exportDateFrom_year"] . "-" . $_POST ["exportDateFrom_month"] . "-" . $_POST ["exportDateFrom_day"]);
}

// "date to" filter
if ($_POST ['exportDateTo_year'] > 0 && $_POST['exportDateTo_month'] > 0 && $_POST['exportDateTo_day'] > 0) {
	$exportDateTo = DateTime::createFromFormat('Y-m-d', $_POST["exportDateTo_year"] . "-" . $_POST ["exportDateTo_month"] . "-" . $_POST ["exportDateTo_day"]);
}

if (is_numeric($_POST['ddl_status'])){
	$selectedStatus = intval($_POST['ddl_status']);
}

if (!is_null($exportDateFrom)){
	if (!empty($conditionSQL)){
		$conditionSQL .= ' AND ';
	}
	$conditionSQL .= "l.ExportDate >= '" . $exportDateFrom->format('Y-m-d') . "'";
}

if (!is_null($exportDateTo)){
	if (!empty($conditionSQL)){
		$conditionSQL .= ' AND ';
	}
	$conditionSQL .= "l.ExportDate <= '" . $exportDateTo->format('Y-m-d') . " 23:59:59.999'";
}

if (!is_null($selectedStatus)){
	if (!empty($conditionSQL)){
		$conditionSQL .= ' AND ';
	}
	$conditionSQL .= "l.ExportStatus = $selectedStatus";
}

if (!empty($conditionSQL)){
	$conditionSQL = "WHERE ".$conditionSQL;
}

$countSQL = "
		SELECT COUNT(*)
		FROM nepfund_export_log l
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
			l.ExportDate,
			l.NumberOfRow,
			l.ExportStatus
		FROM nepfund_export_log l
		$conditionSQL
		ORDER BY l.ID DESC
		$the_limit";

$queryResult = mysql_query($querySQL);
$statusMapping = getExportStatusMapping();

// total records
$seq = 0;

function createDetailLink($row, $text)
{?>
	<a href="<?php echo "export_detail.php?id=${row['ID']}"; ?>"><?php echo $text; ?></a>
<?php 
}
include "header_html.php";?>
<td valign="top" style="padding-left: 5px;">
	<h2 class="default_h1" style="margin: 0; padding: 0;">รายงานการส่งออกข้อมูลใบเสร็จไประบบกองทุน</h2>
	<br />

	<form method="post">
		<table style="padding: 10px 0 0px 0;">
			<tr>
				<td bgcolor="#efefef">ตั้งแต่วันที่:</td>
				<td><?php
					$selector_name = "exportDateFrom";
					$this_date_time = is_null($exportDateFrom) ? '0000-00-00' : $exportDateFrom->format('Y-m-d');
					include ("date_selector.php");
				?>
				</td>
				<td bgcolor="#efefef">ถึงวันที่:</td>
				<td><?php
					$selector_name = "exportDateTo";
					$this_date_time = is_null($exportDateTo) ? '0000-00-00' : $exportDateTo->format('Y-m-d');
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
					<span class="column_header">วันที่ส่งออก</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">จำนวนรายการ</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">สถานะ</span>
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
					<?php echo createDetailLink($row, $seq); ?>
				</div>
			</td>
			<td>
				<?php echo createDetailLink($row, formatDateThai($row["ExportDate"])); ?>
			</td>
			<td>
				<div align="right"><?php echo $row["NumberOfRow"];?></div>
			</td>
			<td>
				<div align="right"><?php echo $statusMapping[$row["ExportStatus"]];?></div>
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