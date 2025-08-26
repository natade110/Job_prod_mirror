<?php
//bank 20221223
//-- เพิ่ม filter จาก dropdown list แถวๆนี้
// fyi
// ภาครัฐ = type code 14 หรือ type code ที่มากกว่า 200 (ลองดูใน table businesstype ได้)
if($_POST[CompanyTypeCode]){
	
	$CompanyType_filter .= " and CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'";
	
	// --- filter
	
}
