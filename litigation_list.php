<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';


$CANCEL_SEQUESTRATION_STATUS = $COMPANY_LAW_STATUS->ถอนอายัด;

function createDetailLink($row, $text)
{
	$requestType = $row['RequestType'];
	$pageName = "litigation_form.php";
	if(($requestType == 1) || ($requestType == 2)){
		$pageName = "holding_edit.php";	
	}else if($requestType == 3){
		$pageName = "proceedings_edit.php";
	}else if($requestType == 4){
		$pageName = "notice_edit.php";
	}
	
?>
<a href="<?php echo $pageName."?id=${row['RequestID']}&type=$requestType"; ?>"><?php writeHtml($text); ?></a>
<?php 
}

function createStatusIcon($status, $statusMapping, $statusIconMapping)
{
	global $COMPANY_LAW_STATUS;
	$iconRed = "";
	if($status == $COMPANY_LAW_STATUS->ถอนอายัด){
		$iconRed = "litigation-icon-red";
	}
	
	?>
	<span class="litigation-icon <?php echo $iconRed?>" title="<?php writeHtml("${statusMapping[$status]}"); ?>"><i class="<?php writeHtml("${statusIconMapping[$status]}"); ?>"/></i></span>
<?php 
}


$isDisplaySearchResult = $_SERVER['REQUEST_METHOD'] == 'POST';

$statusMapping = getLawStatusInProcessMapping();
$statusIconMapping = getLawStatusInProcessIconMapping();

$docTypeMapping = getDocumentTypeMapping();

// กรองตามสิทธิการมองเห็น
$innerCondition = "";
$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
if((($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก) || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ))
	&& ($zone_user != null)) {
	//กรองตามโซน
	//$innerCondition .= " AND zu.zone_id = $zone_user";
	$innerCondition .= " AND (c.district_cleaned in (
		select district_name
		from districts
		where district_area_code in (
			select district_area_code
			from zone_district
			where zone_id = '$zone_user'
			)
		)
	OR c.District in (
		select district_name
		from districts
		where district_area_code in (
			select district_area_code
			from zone_district
			where zone_id = '$zone_user'
			)
		)
	)";
}

if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
	$innerCondition .= " AND C.Province = $sess_meta";
}

$innerCondition = ($innerCondition != "")? (" WHERE ".substr($innerCondition, 4)) : "";

if ($isDisplaySearchResult){
	$innerQuery = "
	SELECT data.RequestDate, data.DocumentNo, data.DocumentType, data.CID, data.RequestType, data.RequestID, C.CompanyNameThai, P.province_name ProvinceName, C.LawStatus, C.Province as ProvinceID, C.BusinessTypeCode, C.CompanyTypeCode, C.CompanyCode
	FROM
	(
		SELECT DocumentDate RequestDate, GovDocumentNo DocumentNo, 5 DocumentType, CID, 1 RequestType, SID RequestID, CreatedBy
		FROM sequestration s
		UNION
		SELECT RequestDate, RequestNo DocumentNo, 6 DocumentType, CID, 2 RequestType, cs.SID RequestID, cs.CreatedBy
		FROM sequestration s
		INNER JOIN cancelledsequestration cs ON s.SID = cs.SID
		UNION
		SELECT RequestDate, GovDocumentNo, p.PType DocumentType, CID, 3 RequestType, PID RequestID, CreatedBy
		FROM proceedings p
		UNION
		SELECT DocumentDate, GovDocumentNo, 4 DocumentType, CID, 4 RequestType, NoticeID RequestID, CreatedBy
		FROM noticedocument n
	) data
	INNER JOIN company C ON data.CID = C.CID
	LEFT JOIN provinces P ON P.province_id = C.Province
	-- LEFT JOIN users u on data.CreatedBy =  CONCAT(u.FirstName,  ' ' ,u.LastName) 
	-- LEFT JOIN zone_user zu on u.user_id = zu.user_id
	$innerCondition";
	
	$conditionSQL = '';
	$selectedStatus = NULL;
	$selectedProvince = NULL;
	$filterCompanyName = NULL;
	$filterCompanyCode = NULL;
	$selectedBusinessTypeCode = NULL;
	$selectedCompanyTypeCode = NULL;
	$filterDocumentNo = NULL;
	$selectedDocumentType = NULL;

	//Get Form Value
	if (is_numeric($_POST['LawStatus'])){
		$selectedStatus = intval($_POST['LawStatus']);
	}
	
	if (is_numeric($_POST['Province'])){
		$selectedProvince = intval($_POST['Province']);
	}
	
	if (is_string($_POST['CompanyNameThai'])){
		$filterCompanyName = $_POST['CompanyNameThai'];
	}
	
	if (is_string($_POST['CompanyCode'])){
		$filterCompanyCode = trim($_POST['CompanyCode']);
	}
	
	if (is_string($_POST['CompanyTypeCode'])){
		$selectedCompanyTypeCode = $_POST['CompanyTypeCode'];
	}
	
	if (is_string($_POST['BusinessTypeCode'])){
		$selectedBusinessTypeCode = $_POST['BusinessTypeCode'];
	}
	
	if (is_string($_POST['DocumentNo'])){
		$filterDocumentNo = trim($_POST['DocumentNo']);
	}
	
	if (is_numeric($_POST['DocumentType'])){
		$selectedDocumentType = intval($_POST['DocumentType']);
	}
	
	
	
	//Build Where Cause
	if (!is_null($selectedStatus)&&!is_null($statusMapping[$selectedStatus])){
		$conditionSQL .= " AND LawStatus = $selectedStatus";
	}else{
		$conditionSQL .= " AND LawStatus NOT IN (0,1,9)";
	}
	
	if (!is_null($selectedProvince)){
		$conditionSQL .= " AND ProvinceID = $selectedProvince";
	}
	
	if (strlen($filterCompanyName) > 0){
		$conditionSQL .= ' AND '.createLikeSearchQuery('CompanyNameThai', $filterCompanyName);
	}
	
	if (strlen($filterCompanyCode) > 0){
		$conditionSQL .= ' AND '.createLikeSearchQuery('CompanyCode', $filterCompanyCode);
	}
	
	if (strlen($selectedBusinessTypeCode) > 0){
		$conditionSQL .= " AND BusinessTypeCode = '".mysql_real_escape_string($selectedBusinessTypeCode)."'";
	}
	
	if (strlen($selectedCompanyTypeCode) > 0){
		$conditionSQL .= " AND CompanyTypeCode = '".mysql_real_escape_string($selectedCompanyTypeCode)."'";
	}
	
	if (strlen($filterDocumentNo) > 0){
		$conditionSQL .= ' AND '.createLikeSearchQuery('DocumentNo', $filterDocumentNo);
	}
	
	if (!is_null($selectedDocumentType)&&!is_null($docTypeMapping[$selectedDocumentType])){
		$conditionSQL .= " AND DocumentType = $selectedDocumentType";
	}

	if (!empty($conditionSQL)){
		$conditionSQL = "WHERE ".substr($conditionSQL, 4);
	}

	$countSQL = "
			SELECT COUNT(*)
			FROM ($innerQuery) d
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
	
	if ($starting_index > $record_count_all){
		$starting_index = 0;
		$cur_page = 1;
	}
	
	$the_limit = "limit $starting_index, $per_page";
	
	// ///////////////
	$querySQL = "
			SELECT
				RequestDate,
				DocumentNo,
				CID,
				RequestType,
				RequestID,
				CompanyNameThai,
				ProvinceName,
				LawStatus,
				DocumentType
			FROM ($innerQuery) d
			$conditionSQL
			ORDER BY RequestDate DESC
			$the_limit";

	$queryResult = mysql_query($querySQL);
	if ($queryResult === false){
		$isDisplaySearchResult = false;
	}
}



include "header_html.php";?>
<td valign="top" style="padding-left: 5px;">
	<h2 class="default_h1" style="margin: 0; padding: 0;">
		การดำเนินคดีตามกฎหมายทั้งหมด
	</h2>
	
	<?php if($_GET["deleted"]=="deleted"){?>							
    <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* การลบข้อมูลสำเร็จ</div>
    <?php }?>
    
	<div style="padding-top:10px; font-weight: bold;">1. ค้นหา<?php writeHtml($the_company_word);?></div>
	<form method="post" action="litigation_list.php">
		<table style="padding: 10px 0 0px 0;">
			<tr>
				<td bgcolor="#efefef">สถานะ <!-- การดำเนินการตามกฎหมาย -->:</td>
				<td>
					<?php echo createDropDownListFromMapping('LawStatus', $statusMapping, $selectedStatus, '-- select --'); ?>
				</td>
				<td bgcolor="#efefef">จังหวัด:</td>
				<td><?php include "ddl_org_province.php";?></td>
			</tr>
			<tr>
				<td bgcolor="#efefef">ชื่อ:</td>
				<td><input type="text" name="CompanyNameThai"
					value="<?php writeHtml($filterCompanyName);?>" /></td>
				<td bgcolor="#efefef"><?php writeHtml($the_code_word); ?>:</td>
				<td><input type="text" name="CompanyCode"
					value="<?php writeHtml($filterCompanyCode);?>" /></td>
			</tr>
			<tr>
				<td bgcolor="#efefef">
					<?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
						ประเภทหน่วยงาน:
					<?php }else{?>
						ประเภทธุรกิจ:
					<?php }?>
                </td>
				<td><?php include "ddl_org_type.php";?></td>
                <?php if (!($sess_accesslevel == 6 ||  $sess_accesslevel == 7)){?>
	                <td bgcolor="#efefef">ประเภทกิจการ:</td>
					<td><?php include "ddl_bus_type.php";?></td>
                <?php } else {?>
                	<td colspan='2'>&nbsp;</td>
                <?php }?>                                  
			</tr>
			<tr>
				<td bgcolor="#efefef">เลขที่หนังสือ:</td>
				<td><input type="text" name="DocumentNo"
					value="<?php writeHtml($filterDocumentNo);?>" /></td>
				<td  bgcolor="#efefef">ประเภทหนังสือ</td>
				<td><?php echo createDropDownListFromMapping('DocumentType', $docTypeMapping, $selectedDocumentType, '-- select --'); ?></td>
			</tr>
			<tr>
				<td><input type="submit" value="แสดง"
					name="search" /></td>
				<td colspan='3'>&nbsp;</td>
			</tr>
		</table>
		<hr />
<?php 
	if ($isDisplaySearchResult) { ?>
		<table border="0" width="100%" >
			<tbody>
				<tr>
					<td align="left">
						<font color="#006699">แสดงข้อมูล <?php writeHtml($starting_index+1);?>-<?php writeHtml(($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page);?> จากทั้งหมด <?php echo writeHtml($record_count_all); ?> รายการ</font>
					</td>
					<td align="right" valign="bottom">
						<div style="padding:5px 0 0px 0;" align="right">
							<span>แสดงข้อมูล:</span>
							<select name="start_page" onchange="this.form.submit()"><?php
								for($i = 1; $i <= $num_page; $i ++) {?>
								<option value="<?php writeHtml($i);?>"
									<?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php writeHtml($i);?></option>
								<?php }?>
							</select>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div style='text-align: left;line-height: 2em'>
		<?php
		foreach ($statusMapping as $status => $statusText)
		{?>
			<span style='margin-right:15px;' class='text-nowrap'>
			<?php 
				createStatusIcon($status, $statusMapping, $statusIconMapping);
				writeHtml('= '.$statusText);
			?>
			</span>
<?php	} ?>
		</div>
<?php	
	} ?>
	</form>

<?php
	if ($isDisplaySearchResult) { ?>
	<table border="1" cellspacing="0" cellpadding="5"
		style="border-collapse: collapse;" width="100%">
		<tr bgcolor="#9C9A9C" align="center">
			<td>
				<div align="center">
					<span class="column_header">วันที่ยื่นเรื่อง</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">หนังสือเลขที่</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">ประเภทหนังสือ</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">ชื่อ นายจ้างหรือ สถานประกอบการ</span>
				</div>
			</td>
			<td>
				<div align="center">
					<span class="column_header">จังหวัด</span>
				</div>
			</td>
			<td style='width:80px'>
				<div align="center">
					<span class="column_header">สถานะการดำเนินการตามกฎหมาย</span>
				</div>
			</td>
		</tr>
		<?php
		// main loop
		while ($row = mysql_fetch_array($queryResult)) {
		?>
		<tr bgcolor="#ffffff" align="center">
			<td style='text-align:center'>
				<?php writeHtml(formatDateThai($row["RequestDate"])); ?>
			</td>
			<td>
				<div><?php createDetailLink($row, $row["DocumentNo"]);?></div>
			</td>
			<td>
				<div><?php writeHtml($docTypeMapping[$row["DocumentType"]]);?></div>
			</td>
			<td>
				<?php 
					writeHtml($row["CompanyNameThai"]);
				 ?>
			</td>
			<td>
				<div><?php writeHtml($row["ProvinceName"]);?></div>
			</td>
			<td style='text-align:center'>
				<?php createStatusIcon($row["LawStatus"], $statusMapping, $statusIconMapping); ?>
			</td>
		</tr>
		<?php } //end loop to generate rows?>
	</table>
<?php }?>
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