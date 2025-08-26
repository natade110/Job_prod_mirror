<?php

	include "db_connect.php";
	
	$the_id = "9999999999";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	
	
	///
	$company_row = getFirstRow("select CompanyNameThai,CompanyTypeCode, CID from company where CompanyCode = '$the_id' and BranchCode < 1");
	


	
			
	if($company_row){	
	
		$the_output = "someVar = { 
					'company_name_thai' : '". formatCompanyName($company_row["CompanyNameThai"], $company_row["CompanyTypeCode"])."'
					, 'company_cid' : '".$company_row["CID"]."'
					}";
		
		echo $the_output; 	
	}else{
		echo "no_result";
	}

?>