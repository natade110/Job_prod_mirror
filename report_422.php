<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';

$date = new DateTime();
$date->add(new DateInterval('P3M'));

$currentYear = intval($date->format("Y"));

$province = NULL;
$section = NULL;

$conditionSQL = '';
$warningYear = $currentYear - WARNING_YEAR_RANGE;
$subfixTitle = 'ทั่วประเทศ';

if ($_POST['rad_area'] == 'province'){
	if (is_numeric($_POST['Province'])){
		$province = intval($_POST['Province']);
	}
}

if ($_POST['rad_area'] == 'section'){
	if (is_numeric($_POST['Section'])){
		$section = intval($_POST['Section']);
	}
}

//add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	$typecode_filter = "c.CompanyTypeCode >= 200  and c.CompanyTypeCode < 300";
}else{
	$typecode_filter = "c.CompanyTypeCode < 200";
}

$typecode_filter .= " AND c.CompanyTypeCode != '14'";

if (!is_null($province)){
	$conditionSQL = "c.Province=$province AND $typecode_filter";
	
	$provinceName = getFirstItem("SELECT province_name from provinces where province_id=$province");
	if($province == 1){
		$subfixTitle = $provinceName;
	}else{
		$subfixTitle = "จังหวัด".$provinceName;
	}
}

if (!is_null($section)){
	$conditionSQL = "c.section_id=$section AND $typecode_filter";
	
	$sectionName = getFirstItem("SELECT section_name from province_section where section_id=$section");
	$subfixTitle = $sectionName;
}

if (strlen($conditionSQL) == 0){
	$conditionSQL = "$typecode_filter";
}



$conditionSQL = "WHERE $conditionSQL";

$yearQuery = "SELECT 2011 Year";
for ($year = 2012;$year <= $currentYear;$year++)
{
	$yearQuery .= "
	UNION
	SELECT $year Year";
}
// ///////////////


$querySQL = "
SELECT 
		Y.Year
		, COALESCE(L.CountStatus0, 0) CountStatus0
		, COALESCE(L.CountStatus1, 0) CountStatus1
		, COALESCE(L.CountStatus2, 0) CountStatus2
		, COALESCE(L.CountStatus3, 0) CountStatus3
		, COALESCE(L.CountTotal, 0) CountTotal
		, COALESCE(D.CountNearDeadline, 0) CountNearDeadline
		, COALESCE(N.CountNotice, 0) CountNotice
		, COALESCE(S.CountSequestration, 0) CountSequestration
		, COALESCE(S.CountCancelSequestration, 0) CountCancelSequestration
		, COALESCE(P.CountProceeding, 0) CountProceeding
		, COALESCE(P.CountCourtAccept, 0) CountCourtAccept
		, COALESCE(P.CountLiquidation, 0) CountLiquidation
FROM (
	$yearQuery
) Y
LEFT JOIN (
	SELECT 
		l.Year,
		COUNT(CASE WHEN l.LawfulStatus=0 THEN 1 ELSE NULL END) CountStatus0,
		COUNT(CASE WHEN l.LawfulStatus=1 THEN 1 ELSE NULL END) CountStatus1,
		COUNT(CASE WHEN l.LawfulStatus=2 THEN 1 ELSE NULL END) CountStatus2,
		COUNT(CASE WHEN l.LawfulStatus=3 THEN 1 ELSE NULL END) CountStatus3,
		COUNT(CASE WHEN l.LawfulStatus=0 OR l.LawfulStatus=2 THEN 1 ELSE NULL END) CountNearDeadline,
        COUNT(1) CountTotal
	FROM lawfulness l
	INNER JOIN company c ON l.CID = c.CID AND (c.branchcode < 1 or l.Year <=2012)
	LEFT JOIN provinces p ON p.province_id = c.Province
    $conditionSQL
    GROUP BY Year
) L ON Y.Year = L.Year
LEFT JOIN (
	SELECT 
		l.Year + 4 Year,
		COUNT(CASE WHEN l.LawfulStatus=0 OR l.LawfulStatus=2 THEN 1 ELSE NULL END) CountNearDeadline
	FROM lawfulness l
	INNER JOIN company c ON l.CID = c.CID AND (c.branchcode < 1 or l.Year <=2012)
	LEFT JOIN provinces p ON p.province_id = c.Province
    $conditionSQL
    	AND l.Year >= 2011
    GROUP BY l.Year + 4
) D ON Y.Year = D.Year
LEFT JOIN (
	SELECT
		YEAR(DATE_ADD(DocumentDate, INTERVAL 3 MONTH)) Year
        , COUNT(1) CountNotice
    FROM noticedocument n
	INNER JOIN company c ON n.CID = c.CID
	LEFT JOIN provinces p ON p.province_id = c.Province
    $conditionSQL
    GROUP BY YEAR(DATE_ADD(DocumentDate, INTERVAL 3 MONTH))
) N ON Y.Year = N.Year
LEFT JOIN (
	SELECT
		YEAR(DATE_ADD(seq.DocumentDate, INTERVAL 3 MONTH)) Year
        , COUNT(1) CountSequestration
        , COUNT(cs.CSID) CountCancelSequestration
    FROM sequestration seq
	INNER JOIN company c ON seq.CID = c.CID
	LEFT JOIN provinces p ON p.province_id = c.Province
    LEFT JOIN cancelledsequestration cs ON cs.SID = seq.SID 
    $conditionSQL
    GROUP BY YEAR(DATE_ADD(seq.DocumentDate, INTERVAL 3 MONTH))
) S ON Y.Year = S.Year
LEFT JOIN (
	SELECT
		YEAR(DATE_ADD(pr.RequestDate, INTERVAL 3 MONTH)) Year
        , COUNT(CASE WHEN pr.PType=1 OR pr.PType=3 THEN 1 ELSE NULL END) CountProceeding
        , COUNT(CASE WHEN pr.PType=2 THEN 1 ELSE NULL END) CountLiquidation
        , COUNT(CASE WHEN pr.PType=3 THEN 1 ELSE NULL END) CountCourtAccept
    FROM proceedings pr
	INNER JOIN company c ON pr.CID = c.CID
	LEFT JOIN provinces p ON p.province_id = c.Province
    $conditionSQL
    GROUP BY YEAR(DATE_ADD(RequestDate, INTERVAL 3 MONTH))
) P ON Y.Year = P.Year
ORDER BY Y.Year";

//echo $querySQL;
$queryResult = mysql_query($querySQL);

	if($_POST["report_format"] == "excel"){
		header("Content-type: application/ms-excel");
		header("Content-Disposition: attachment; filename=report_422.xls");
	
		$is_excel = 1;
	}elseif($_POST["report_format"] == "words"){
		header("Content-type: application/vnd.ms-word");
		header("Content-Disposition: attachment;Filename=report_422.doc");
	}elseif($_POST["report_format"] == "pdf"){
		$is_pdf = 1;
	}else{
		header ('Content-type: text/html; charset=utf-8');
	}
 ?>
	<div align="center">
		<strong>รายงานข้อมูลสรุปการดำเนินการตามกฎหมาย <?php writeHTML($subfixTitle); ?></strong>
		<br />
	</div>
	<table border="1" align="center" cellpadding="5" cellspacing="0" style="border-collapse: collapse;font-size:<?php echo !$is_pdf ? 14:28; ?>px">
		<thead>
			<tr align="center">
				<td align="center">
					<strong>ปี</strong>
				</td>
				<td align="center">
					<strong>ปฎิบัติ</strong>
				</td>
				<td align="center">
					<strong>ไม่ปฎิบัติ</strong>
				</td>
				<td align="center">
					<strong>ปฎิบัติแต่ไม่ครบอัตราส่วน</strong>
				</td>
				<td align="center">
					<strong>ไม่เข้าข่าย</strong>
				</td>
				<td align="center">
					<strong>ทั้งหมด</strong>
				</td>
				<td align="center">
					<strong>ใกล้หมดอายุความ</strong>
				</td>
				<td align="center">
					<strong>แจ้งโนติส</strong>
				</td>
				<td align="center">
					<strong>อายัด</strong>
				</td>
				<td align="center">
					<strong>ถอนอายัด</strong>
				</td>
				<td align="center">
					<strong>ส่งพนักงานอัยการ</strong>
				</td>
				<td align="center">
					<strong>ศาลสั่งฟ้อง</strong>
				</td>
				<td align="center">
					<strong>ยื่นขอรับชำระหนี้ล้มละลาย</strong>
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
					<?php echo formatYear($row["Year"]);?>
					</div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountStatus1"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountStatus0"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountStatus2"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountStatus3"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountTotal"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountNearDeadline"]));?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountNotice"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountSequestration"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountCancelSequestration"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountProceeding"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountCourtAccept"]))?></div>
				</td>
				<td>
					<div align="right"><?php writeHtml(formatEmployeeReport($row["CountLiquidation"]))?></div>
				</td>
			</tr>
		<?php } //end loop to generate rows?>
		</tbody>
	</table>
	<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>