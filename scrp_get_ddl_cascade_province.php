<?php
require_once 'db_connect.php';


$result = new stdClass();

if(isset($_POST["cate"]) && isset($_POST["parentcode"])){
	$cateName = $_POST["cate"];
	$parentCode = $_POST["parentcode"];
	$provinceCode = $_POST["provincecode"];
	
	$sql = "";
	if($cateName == 'district'){
		$sql = "SELECT district_code as Value, district_name as Text FROM `districts`d WHERE province_code = $provinceCode";
	}else{
		$sql = "SELECT subdistrict_code as Value, subdistrict_name as Text FROM `subdistrict` WHERE (province_code = $provinceCode) and (district_code = '$parentCode')";
	}
	
	if($sql != ""){
		$data = array();
		$dataResult = mysql_query($sql);
		$item = new stdClass();
		while ($dataRow = mysql_fetch_array($dataResult)) {
			$item = new stdClass();
			$item->Value = doCleanOutput($dataRow["Value"]);
			$item->Text = doCleanOutput($dataRow["Text"]);
			array_push($data, $item);
		}
		
		$result->Status = 1;
		$result->Data = $data;
	}else{
		$result->Status = 0;
		$result->Message = "ไม่พบรหัสจังหวัด";
	}
	
}else{
	$result->Status = 0;
	$result->Message = "ไม่พบข้อมูล";
}

echo  json_encode($result);
?>