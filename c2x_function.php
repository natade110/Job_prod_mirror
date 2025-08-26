<?php
require_once 'db_connect.php';
//ini_set('display_errors', 'on');

function getImportStatusMapping(){
	return array(0 => 'เริ่มต้น', 1=>'สำเร็จ', 2=>'มีปัญหา');
}

function getExportStatusMapping(){
	return array(0 => 'เริ่มต้น', 1=>'สำเร็จ', 2=>'มีปัญหา');
}

function getExportModeMapping(){
	return array(1=>'สร้างใบเสร็จ', 9=>'ยกเลิกใบเสร็จ');
}

function getPaymentMethodMapping(){
	return array('Cash'=>'เงินสด', 'Cheque'=>'เช็ค', 'NET'=>'KTB Netbank');
}

function getPaymentStatusMapping(){
	return array(
			0 => 'ยังไม่ได้ชำระเงิน',
			1 => 'ชำระเงินแล้ว',
			9 => 'ยกเลิก',
			10 => 'มีการชำระเงิน KTB Online (รอการตรวจสอบ)',
			11 => 'มียกเลิก KTB Online (รอการตรวจสอบ)',
			12 => 'เช็คยังไม่ยืนยันการเคลียร์ริ่ง',
			13 => 'เช็คไม่เคลียร์/เช็ดเด้ง'
	);
}

function getLawStatusMapping(){
	return array(
			0 => 'ไม่เข้าข่าย',
			1 => 'ยังไม่ดำเนินการ',
			2 => 'แจ้งโนติส',
			3 => 'อายัดทรัพย์สิน',
			31 => 'ถอนอายัด',
			4 => 'ส่งพนักงานอัยการ',
			5 => 'ศาลสั่งฟ้อง',
			6 => 'ยื่นขอรับชำระหนี้ล้มละลาย',
			9 => 'ชำระหนี้ครบแล้ว'
	);
}

function getLawStatusInProcessMapping(){
	$arr = getLawStatusMapping();
	unset($arr[0]);
	unset($arr[1]);
	unset($arr[9]);
	
	return $arr;
}

function getLawStatusIconMapping(){
	return array(
			0 => 'fa fa-hourglass-start',
			1 => 'fa fa-hourglass-start',
			2 => 'fa fa-file-text-o',
			3 => 'fa fa-lock',
			31 => 'fa fa-lock',
			4 => 'fa fa-bank',
			5 => 'fa fa-gavel',
			6 => 'fa fa-money',
			9 => 'fa fa-money'
	);
}

function getLawStatusInProcessIconMapping(){
	$arr = getLawStatusIconMapping();
	unset($arr[0]);
	unset($arr[9]);
	
	return $arr;
}

function getAcountTypeMapping(){
	return array(			
			1 => 'บัญชีออมทรัพย์',
			2 => 'บัญชีเงินฝากประจำ',
			3 => 'บัญชีเดินสะพัด ',
	);
}

function getProceedingCreateTypeMapping(){
	return array(			
			1 => 'ส่งพนักงานอัยการ',
			2 => 'ยื่นขอชำระหนี้ล้มละลาย',			
	);
}

function getProceedingEditTypeMapping(){
	return array(			
			1 => 'ส่งพนักงานอัยการ',
			2 => 'ยื่นขอชำระหนี้ล้มละลาย',
			3 => 'ศาลสั่งฟ้อง'
	);	
}

function getDocumentTypeMapping(){
	return array(			
			4 => 'แจ้งโนติส',
			5 => 'อายัดทรัพย์สิน',
			6 => 'ถอนอายัด',
			1 => 'ส่งพนักงานอัยการ',
			3 => 'ศาลสั่งฟ้อง',
			2 => 'ยื่นขอรับชำระหนี้ล้มละลาย'
	);
}

function createDropDownListFromMapping($ddlName, $array, $selectedValue = NULL, $defaultLabel = NULL){
	$html = '<select name="'. htmlspecialchars($ddlName) .'" id="'. htmlspecialchars($ddlName) .'" >';
	
	if (!is_null($defaultLabel)){
		$html .= '<option value="">'. htmlspecialchars(strval($defaultLabel)) .'</option>';
	}
	foreach ($array as $key => $value){
		$selected = '';
		
		if (!is_null($selectedValue) && $key == $selectedValue){
			$selected = ' selected="selected"';
		}
		
		$html .= '<option value="'.htmlspecialchars(strval($key)).'"'.$selected.'>'. htmlspecialchars(strval($value)) .'</option>';
	}
	$html .= '</select>';
	
	return $html;
}

function createLikeSearchQuery($field, $value){
	$fieldValue = mysql_real_escape_string(str_replace(array('_', '%', '|'), array('|_', '|%', '||'), $value)); 
	return "$field LIKE '%$fieldValue%' ESCAPE '|'";
}

function writeHtml($text){
	echo htmlspecialchars($text);
}

function handleUnexpectedException(Exception $ex){
	error_log($ex->getMessage());
	error_log($ex->getTraceAsString());
}


/**
 * @param $_FILES $files
 * @return $files or arrary of $files : [{name = "", type = "", tmp_name = "", error = "", size = ""}]
 */
function fixFilesArray($files)
{	
	if( empty( $files ) ) {
		return $files;
	}
	 
	if( 'array'!==gettype($files['name'])) {
		return $files;
	}
	 
	$count = count($files['name']);
	$fileArray = array();
	for($i = 0; $i < $count; $i++){
		$file = array();
		if(empty($files["name"][$i])){
			$fileArray = null;
			break;
		}
		$file["name"] = $files["name"][$i];
		$file["type"] = $files["type"][$i];
		$file["tmp_name"] = $files["tmp_name"][$i];
		$file["error"] = $files["error"][$i];
		$file["size"] =  $files["size"][$i];
	
		array_push($fileArray, (object)$file);
	}
	 
	return $fileArray;
}

/**
 * @param object $file : {name = "", type = "", tmp_name = "", error = "", size = ""}
 * @param array $minmeTypes
 * @return boolean isValid : return true if found in  $minmeTypes
 */
function checkAllowFileUpload($file, array $minmeTypes = array("image/jpeg", "image/jpg", "image/gif", "application/pdf")){
	$isValid = true;
	$type = "";
	
	if(!is_null($file)){
		if(is_array($file)){
			
			for($i = 0; $i < count($file); $i++){				
				$type = trim($file[$i]->type);
				if(!in_array($type, $minmeTypes)){
					$isValid = false;
					break;
				}
			}
		}else{
			$type =trim($file->type);			
			$isValid = in_array($type, $minmeTypes);
		}
	}

	
	return $isValid;
}

/**
 * @param string $table
 * @param array $fields: [{ColumnName1: Value1, ColumnName2: Value2,...}]
 * @param array $specialFields:  [{ColumnName1: Value1, ColumnName2: Value2,...}]
 * @return boolean|resource
 */
function executeInsert($table, $fields, $specialFields = NULL){
	if (!(is_null($fields) || is_array($fields))
			|| (!(is_null($specialFields) || is_array($specialFields)))){
		return false;
	}

	$fieldNames = array();
	if ($fields != NULL){
		$fieldNames = array_keys($fields);
	}
	$fieldCount = count($fieldNames);

	$specialFieldNames = array();
	if ($specialFields != NULL){
		$specialFieldNames = array_keys($specialFields);
	}
	$specialFieldCount = count($specialFieldNames);

	if ($fieldCount == 0 && $specialFieldCount==0){
		return false;
	}

	$fieldString = "";
	$valueString = "";

	for ($i = 0;$i < $fieldCount;$i++){
		$fieldName = $fieldNames[$i];
		$fieldValue = $fields[$fieldName];

		$fieldString .= ", $fieldName";
		$valueString .= ', ';

		if (is_null($fieldValue)){
			$valueString .= 'NULL';
		}elseif (is_int($fieldValue) || is_float($fieldValue)){
			$valueString .= $fieldValue;
		} else if (is_bool($fieldValue)){
			if ($fieldValue){
				$valueString .= '1';
			}else{
				$valueString .= '0';
			}
		} else if (is_string($fieldValue)){
			$valueString .= '\''.mysql_real_escape_string($fieldValue).'\'';
		} else {
			return false;
		}
	}

	for ($i = 0;$i < $specialFieldCount;$i++){
		$specialFieldName = $specialFieldNames[$i];
		$specialFieldValue = $specialFields[$specialFieldName];

		$fieldString .= ", $specialFieldName";
		$valueString .= ", $specialFieldValue";
	}

	$fieldString = substr($fieldString, 2);
	$valueString = substr($valueString, 2);

	$sql = "INSERT INTO $table ($fieldString) VALUES ($valueString)";

	$result = mysql_query($sql);
	if ($result === false){
		error_log($sql);
		error_log('error no: '.mysql_error().' message: '.mysql_error());
	}

	return $result;
}

/**
 * 
 * @param string $table
 * @param string $condition
 * @param array $fields: [{ColumnName1: Value1, ColumnName2: Value2,...}]
 * @param array $specialFields: [{ColumnName1: Value1, ColumnName2: Value2,...}]
 * @return boolean|resource
 */
function executeUpdate($table, $condition, $fields, $specialFields = NULL){
	if (!(is_null($fields) || is_array($fields))
			|| (!(is_null($specialFields) || is_array($specialFields)))){
		return false;
	}

	$fieldNames = array();
	if ($fields != NULL){
		$fieldNames = array_keys($fields);
	}
	$fieldCount = count($fieldNames);

	$specialFieldNames = array();
	if ($specialFields != NULL){
		$specialFieldNames = array_keys($specialFields);
	}
	$specialFieldCount = count($specialFieldNames);

	if ($fieldCount == 0 && $specialFieldCount==0){
		return false;
	}

	$sql = "UPDATE $table SET ";

	for ($i = 0;$i < $fieldCount;$i++){
		$fieldName = $fieldNames[$i];
		$fieldValue = $fields[$fieldName];
		if ($i >= 1){
			$sql .= ', ';
		}

		$sql .= "$fieldName = ";

		if (is_null($fieldValue)){
			$sql .= 'NULL';
		}elseif (is_int($fieldValue) || is_float($fieldValue)){
			$sql .= $fieldValue;
		} else if (is_bool($fieldValue)){
			if ($fieldValue){
				$sql .= '1';
			}else{
				$sql .= '0';
			}
		} else if (is_string($fieldValue)){
			$sql .= '\''.mysql_real_escape_string($fieldValue).'\'';
		} else {
			return false;
		}
	}

	for ($i = 0;$i < $specialFieldCount;$i++){
		$specialFieldName = $specialFieldNames[$i];
		$specialFieldValue = $specialFields[$specialFieldName];

		if ($fieldCount == 0 && $i == 0){
			$sql .= "$specialFieldName = $specialFieldValue";
		}else{
			$sql .= ", $specialFieldName = $specialFieldValue";
		}
	}

	$sql .= " WHERE $condition";

	$result = mysql_query($sql);
	if ($result === false){
		error_log($sql);
		error_log('error no: '.mysql_error().' message: '.mysql_error());
	}

	return $result;
}

function getScheduleUserId(){
	$data = NULL;
	$result = mysql_query('select user_id from users where user_name=\'schedule_user\' limit 1');
	
	if ($result !== false){
		$row = mysql_fetch_row($result);
		if ($row !== false){
			$data = $row[0];
		}
		
		mysql_free_result($result);
	}
	return $data;
}

// function hasCreateRoleSequestrationByCollectionID($collectionID){
// 	$hasRole = false;
// 	global $sess_accesslevel;
// 	global $sess_meta;
// 	global $sess_islawyer;
// 	global $USER_ACCESS_LEVEL;
	
// 	if($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ){
// 		$hasRole = true;
// 	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี){
// 		$hasRole = true;
// 		$filter = " AND prov.province_code = '".BANGKOK_PROVINCE_CODE."'";
	
// 	}else if(($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ) && $sess_islawyer){
// 		$hasRole = true;
// 		$filter = " AND c.Province = ".$sess_meta;
// 	}
	
// 	$provIDSql = "SELECT c.Province
// 			FROM collectiondocument cd 
// 			INNER JOIN collectioncompany cc ON cd.CollectionID = cc.CollectionID
// 			INNER JOIN company c ON cc.CID = c.CID 
// 			INNER JOIN provinces p ON c.Province = p.province_id
// 			WHERE cd.CollectionID = $collectionID
// 			GROUP BY c.Province
// 	";
	
// 	$provCodeSql = "SELECT p.province_code
// 					FROM collectiondocument cd
// 					INNER JOIN collectioncompany cc ON cd.CollectionID = cc.CollectionID
// 					INNER JOIN company c ON cc.CID = c.CID
// 					INNER JOIN provinces p ON c.Province = p.province_id
// 					WHERE cd.CollectionID = $collectionID
// 					GROUP BY p.province_code
// 	";
// }

function hasCreateRoleCollection($collectionID = null){
	$hasRole = false;
	global $sess_accesslevel;
	global $sess_meta;
	global $sess_islawyer;
	global $sess_userid;
	global $USER_ACCESS_LEVEL;
	
	$filter = "";
	$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
	
	if((($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก) || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ)))
		//&& ($zone_user != null))
	 {
	 	$hasRole = true;
	 	if  ($zone_user != null){
			
			$filter .= " AND (collectcom.district_cleaned in (
				select district_name
				from districts
				where district_area_code in (
					select district_area_code
					from zone_district
					where zone_id = '$zone_user'
					)
				)
			OR collectcom.District in (
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
	}else if ($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ) {
		$hasRole = true;
	}
		

	if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
		$hasRole = true;
		$filter .= " and collectcom.Province = $sess_meta";
	}

	if($hasRole && ($collectionID != null)){
		$theSql = "select count(*) as CollectionCount
			from(
				select c.CollectionID
				from collectiondocument c
				left join (SELECT distinct ccom.CollectionID,comp.Province,comp.District,comp.district_cleaned 
                	FROM collectioncompany  ccom 
                	left join company comp on ccom.CID = comp.CID) collectcom on
                	c.CollectionID = collectcom.CollectionID 
				where c.CollectionID = $collectionID
				$filter
			)tmp";
		
		$theSqlResult = mysql_query($theSql);
		$row = mysql_fetch_array($theSqlResult);
		$count = $row["CollectionCount"];
		$hasRole = ($count > 0);
	}
	
	return $hasRole;
}

function hasUpdateRoleCollection($collectionID){
	$hasRole = false;
	global $sess_accesslevel;
	global $sess_meta;
	global $sess_islawyer;
	global $sess_userid;
	global $USER_ACCESS_LEVEL;

	$filter = "";
	$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
	$zone_collection = getFirstItem("select uz.zone_id from collectiondocument coll
			left join users u on coll.CreatedByID = u.user_id
			left join zone_user uz on u.user_id= uz.user_id
			where CollectionID ='$collectionID'");	

	if((($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก) || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ)))
	//&& ($zone_user != null))
	{
		if  ($zone_user != null){
			if  ($zone_collection != null){
				error_log("zone_collection == null");
				error_log($collectionID);
	
				$filter .= " AND (collectcom.district_cleaned  in (
				select district_name
				from districts
				where district_area_code in (
				select district_area_code
				from zone_district
				where zone_id  = $zone_user
				)
				)
				or collectcom.District  in (
				select district_name
				from districts
				where district_area_code in (
				select district_area_code
				from zone_district
				where zone_id = $zone_user
				)
				)
				)";
				
				$hasRole = true;
			}
		}else{
			$hasRole = true;
		}
	}else if ($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ) {
		$hasRole = true;
	}


	if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
		$hasRole = true;
		$filter .= " and collectcom.Province = $sess_meta";
	}

	if($hasRole && ($collectionID != null)){
		$theSql = "select count(*) as CollectionCount
		from(
		select c.CollectionID
		from collectiondocument c
		left join (SELECT distinct ccom.CollectionID,comp.Province,comp.District,comp.district_cleaned
		FROM collectioncompany  ccom
		left join company comp on ccom.CID = comp.CID) collectcom on
		c.CollectionID = collectcom.CollectionID
		where c.CollectionID = $collectionID
		$filter
		)tmp";
		error_log($theSql);
		$theSqlResult = mysql_query($theSql);
		$row = mysql_fetch_array($theSqlResult);
		$count = $row["CollectionCount"];
		$hasRole = ($count > 0);
	}

	return $hasRole;
}

function hasCreateRoleCollectionByCID($cid){
	$hasRole = false;
	global $sess_accesslevel;
	global $sess_meta;
	global $sess_islawyer;
	global $sess_userid;
	global $USER_ACCESS_LEVEL;

	$filter = "";
	if($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ){
		$hasRole = true;
	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก){
		$hasRole = true;
		$zoneSql = "select zone_id from zone_user where user_id = $sess_userid";
		$zoneSqlResult = mysql_query($zoneSql);

		$row_zone_id = mysql_fetch_array($zoneSqlResult);
		$zone_id = null;
		if($row_zone_id != null){
			$filter = " AND
				(
				
					c.District in (			
						select
						district_name
						from
						districts
						where
						district_area_code
						in (			
							select
							district_area_code
							from
							zone_district
							where
							zone_id = '$zone'		
						)			
					)
					or
					c.district_cleaned in (
						
						select
						district_name
						from
						districts
						where
						district_area_code
						in (
					
							select
							district_area_code
							from
							zone_district
							where
							zone_id = '$zone'
							
						)			
					)
				)";
		}

	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
		$hasRole = true;
		$filter = " AND c.Province = ".$sess_meta;
	}

	if($hasRole && ($cid != null)){
		$theSql = "SELECT l.Year 
		FROM lawfulness l
		INNER JOIN company c on l.CID = c.CID
		INNER JOIN provinces prov ON c.Province = prov.province_id
		WHERE (l.CID = $cid) AND ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2'))
		$filter
		ORDER BY l.Year limit 0, 1";
				
		error_log($theSql);

		$theSqlResult = mysql_query($theSql);
		if($theSqlResult){
			$hasRole = true;
		}		
	}

	return $hasRole;
}

function hasViewRoleCollection($collectionID = null){
	$hasRole = hasCreateRoleCollection($collectionID);
	
	return $hasRole;
}

function hasCreateRoleSequestrationByCID($cid){
	$hasRole = false;
	global $sess_userid;
	global $sess_accesslevel;
	global $sess_meta;
	global $sess_islawyer;
	global $USER_ACCESS_LEVEL;
	
	$filter = "";
	if($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ){
		$hasRole = true;
	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี){
		$hasRole = true;
		$filter = " AND prov.province_code = '".BANGKOK_PROVINCE_CODE."'";
	
	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
		$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
		if($zone_user){
			
			$filter = " AND
				(
				
					c.District in (
						
						select
						district_name
						from
						districts
						where
						district_area_code
						in (
						
							select
							district_area_code
							from
							zone_district
							where
							zone_id = '$zone_user'
							
						)
					
					)
					or
					c.district_cleaned in (
						
						select
						district_name
						from
						districts
						where
						district_area_code
						in (
						
							select
							district_area_code
							from
							zone_district
							where
							zone_id = '$zone_user'
							
							)
						
					)
				)";
		}else{
			$filter = " AND c.Province = ".$sess_meta;
		}
		
		$hasRole = true;
		
	}
	
	if($hasRole && ($cid != null)){
		if($filter != ""){			
				
			$sql = "SELECT c.CID FROM company c
					INNER JOIN provinces prov ON c.Province = prov.province_id 
					WHERE c.CID = $cid
					$filter";
				
			
			$sqlResult = mysql_query($sql);
			$error = mysql_error();
			if($error != ""){
				error_log($error);
				throw new Exception($error);
			}
				
			$row = mysql_fetch_array($sqlResult);
			if(($row != null) && ($row["CID"] != null)){
				$hasRole = true;
			}else{
				$hasRole = false;
			}
		}	
	}
	
	return $hasRole;
}

/***
 * ใช้เช็คว่ามีสิทธิสร้างจดหมายทวงถาม โนติส อายัด ส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลาย
 * @param int $documentID
 * @param $SEQUESTRATION_DOCUMENT_TYPE $documentType
 * @return boolean 
 */
function hasCreateRoleSequestration($documentID = null, $documentType = null){
	$hasRole = false;
	global $sess_userid;
	global $sess_accesslevel;
	global $sess_meta;
	global $sess_islawyer;
	global $USER_ACCESS_LEVEL;
	global $SEQUESTRATION_DOCUMENT_TYPE;
	
	$filter = "";	
	if($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ){
		$hasRole = true;
	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี){
		$hasRole = true;
		
		$filter = " AND (p.province_code = '".BANGKOK_PROVINCE_CODE."' )";
		
	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
		$hasRole = true;
		$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
		if($zone_user){
		
				
			$filter = " AND
			(
		
			c.District in (
		
			select
			district_name
			from
			districts
			where
			district_area_code
			in (
		
			select
			district_area_code
			from
			zone_district
			where
			zone_id = '$zone_user'
				
			)
				
			)
			or
			c.district_cleaned in (
		
			select
			district_name
			from
			districts
			where
			district_area_code
			in (
		
			select
			district_area_code
			from
			zone_district
			where
			zone_id = '$zone_user'
				
			)
		
			)
			)";
		}else{
			$filter = " AND c.Province = ".$sess_meta;
		}
	}
	
	
	if($hasRole && ($documentID != null)){
		
		
		$join = "";
		$sql = "SELECT * FROM(";
		
		if($documentType == null){
			$sql .= "SELECT q.PID AS ID FROM proceedings q
					  JOIN company c on q.CID = c.CID";
		}else if($documentType == $SEQUESTRATION_DOCUMENT_TYPE->แจ้งโนติส){
			$sql .= "SELECT q.NoticeID AS ID  FROM noticedocument q
					  JOIN company c on q.CID = c.CID";
			
		}else if($documentType == $SEQUESTRATION_DOCUMENT_TYPE->อายัดทรัพย์สิน){
			$sql .= "SELECT q.SID AS ID FROM sequestration q
					 JOIN company c on q.CID = c.CID";
			
		}else if($documentType == $SEQUESTRATION_DOCUMENT_TYPE->ถอนอายัด){
			$sql .= "SELECT q.CSID AS ID FROM cancelledsequestration q
					 JOIN sequestration seq on q.SID = seq.SID
					 JOIN company c on seq.CID = c.CID";
			
		}		
		
		if($filter != ""){
			$filter = " WHERE ".substr($filter, 4);
			
			$sql .= " JOIN provinces p on c.Province = p.province_id
					  $filter
					"; 
			$sql .= ")tmp WHERE ID = $documentID";
			
			$sqlResult = mysql_query($sql);
			$error = mysql_error();
			if($error != ""){
				error_log($error);
				throw new Exception($error);
			}
			
			$row = mysql_fetch_array($sqlResult);
			if(($row != null) && ($row["ID"] != null)){
				$hasRole = true;
			}else{
				$hasRole = false;
			}
		}
		
		
	}	
	
	return $hasRole;
}

/***
 * ใช้เช็คว่ามีสิทธิดูข้อมูลหรือไม่ : โนติส อายัด ส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลาย
 * @param int $documentID
 * @param $SEQUESTRATION_DOCUMENT_TYPE $documentType
 * @return boolean
 * 
 */
function hasViewRoleSequestration($documentID = null, $documentType = null){
	$hasRole = false;
	global $sess_userid;
	global $sess_accesslevel;
	global $sess_meta;
	global $sess_islawyer;
	global $USER_ACCESS_LEVEL;
	global $SEQUESTRATION_DOCUMENT_TYPE;
	
	$filter = "";	
	if($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ){
		$hasRole = true;
	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี){
		$hasRole = true;
		
		
	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
		$hasRole = true;
		$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
		
		
		if($zone_user){
			
				
			$filter = " AND
			(
		
			c.District in (
		
			select
			district_name
			from
			districts
			where
			district_area_code
			in (
		
			select
			district_area_code
			from
			zone_district
			where
			zone_id = '$zone_user'
				
			)
				
			)
			or
			c.district_cleaned in (
		
			select
			district_name
			from
			districts
			where
			district_area_code
			in (
		
			select
			district_area_code
			from
			zone_district
			where
			zone_id = '$zone_user'
				
			)
		
			)
			)";
		}else{
			$filter = " AND c.Province = ".$sess_meta;
		}
	}
	
	
	if($hasRole && ($documentID != null)){
		
		
		$join = "";
		$sql = "SELECT * FROM(";
		
		if($documentType == null){
			$sql .= "SELECT q.PID AS ID FROM proceedings q
					 JOIN company c on q.CID = c.CID";
		}else if($documentType == $SEQUESTRATION_DOCUMENT_TYPE->แจ้งโนติส){
			$sql .= "SELECT q.NoticeID AS ID  FROM noticedocument q
					 JOIN company c on q.CID = c.CID";
			
		}else if($documentType == $SEQUESTRATION_DOCUMENT_TYPE->อายัดทรัพย์สิน){
			$sql .= "SELECT q.SID AS ID FROM sequestration q
					 JOIN company c on q.CID = c.CID";
			
		}else if($documentType == $SEQUESTRATION_DOCUMENT_TYPE->ถอนอายัด){
			$sql .= "SELECT q.CSID AS ID FROM cancelledsequestration q
					 JOIN sequestration seq on q.SID = seq.SID
					 JOIN company c on seq.CID = c.CID";
			
		}		
		
		if($filter != ""){
			$filter = " WHERE ".substr($filter, 4);
			
			$sql .= " JOIN provinces p on c.Province = p.province_id 
					  $filter
					"; 
			$sql .= ")tmp WHERE ID = $documentID";
			
			
			$sqlResult = mysql_query($sql);
			$error = mysql_error();
			if($error != ""){
				error_log($error);
				throw new Exception($error);
			}
			
			$row = mysql_fetch_array($sqlResult);
			if(($row != null) && ($row["ID"] != null)){
				$hasRole = true;
			}else{
				$hasRole = false;
			}
		}	
		
	}		
	
	
	return $hasRole;
}

function getBangkokProvinceIDByCode(){
	$id = 0;
	$sql = "SELECT province_id FROM provinces WHERE province_code = ".BANGKOK_PROVINCE_CODE;
	$sqlResult = mysql_query($sql);
	$error = mysql_error();
	if($error != ""){
		error_log($error);
		throw new Exception($error);
	}
	
	$row = mysql_fetch_array($sqlResult);
	if(($row != null) && ($row["province_id"]))
	{
		$id = $row["province_id"];
	}
	return $id;
}

/**
 *@return array(): [province_id] = province_name
 */
function getProvinceMapping(){
	global $sess_accesslevel;
	global $USER_ACCESS_LEVEL;
	global $sess_meta;
	global $sess_userid;

	$sql = "";
	
	$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
	
	if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
		$sql = "select province_id,  province_name
		from provinces
		where province_id = '$sess_meta'";

	}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี){
		$sql = "select province_id, province_name
			from provinces
			where province_code = ".BANGKOK_PROVINCE_CODE;

	}else if(($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก) && ($zone_user != null)){
		
		$sql = "select prov.province_id,  prov.province_name
				from provinces prov
				where province_code in(
					    select province_code
					    from districts dist
					    inner join zone_district zdist on dist.district_area_code = zdist.district_area_code
				    )";
	}else{
		$sql = "select province_id,  province_name
            from provinces
            order by province_name asc
            ";
	}
	
	$sqlResult = mysql_query($sql);
	$list = array();
	
	while ($row = mysql_fetch_array($sqlResult)){
		$list[$row["province_id"]] = $row["province_name"];		
	}
	
	return (count($list) > 0)? $list : null;

}

function dbBegin(){
	mysql_query("BEGIN");
}

function dbCommit(){
	mysql_query("COMMIT");
}

function dbRollback(){
	mysql_query("ROLLBACK");
}