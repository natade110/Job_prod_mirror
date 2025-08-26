<?php
	
	$origin = $_SERVER['HTTP_ORIGIN'];
	$allowed_domains = array(
		"http://203.154.94.105"
		, "http://law.dep.go.th"
		, "https://law.dep.go.th"
	);

	if (in_array($origin, $allowed_domains)) {
		header('Access-Control-Allow-Origin: ' . $origin);
	}
	
	//header('Access-Control-Allow-Origin: *');
	
	
	//header('Access-Control-Allow-Origin: http://law.dep.go.th');
	//header('Access-Control-Allow-Origin: *');
	
	//echo $origin;
	
	include "db_connect.php";
	
	
	$the_id = $_GET["the_id"]*1;
	$the_year = $_GET["the_year"]*1;
	
	/*$the_id = "1001953231"; // 1001953231 - ทองเทพ แอนด์ ไทเทพ
	$the_year = 2013; // 1001953231 - ทองเทพ แอนด์ ไทเทพ*/
	
	if(!$the_id || !$the_year){		
		//no vars specify
		exit();		
	}
	
	
	/*if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}*/
	
	
	
	///
	$company_row = getFirstRow("select cid from company where CompanyCode = '$the_id' and BranchCode < 1");
	//echo "select cid from company where CompanyCode = '$the_id' and BranchCode < 1";
	$lawfulness_row = getFirstRow("select lid from lawfulness where cid = '".$company_row[cid]."' and year = ".($the_year*1)."");
	
	//echo "select lid from lawfulness where cid = '".$company_row[cid]."' and year = ".($the_year*1)."";
	//print_r($lawfulness_row);
	
	$get_file_sql = "
		
		select
			*
		from
			files
		where
			file_for = '".($lawfulness_row[lid]*1)."'
			and
			file_type in (
			
				'lawful_employees_docfile'
				,'Hire_docfile'
				,'company_34_docfile_1_adm'
				
			)
	
	";
	
	$file_result = mysql_query($get_file_sql);
		
	$the_count = 0;
	while ($file_row = mysql_fetch_array($file_result)) {
	
		$the_count++;
		
		if($the_count > 1){
			echo "<br>";
		}
		
		
		if($file_row[file_type] == "lawful_employees_docfile"){
			echo "ข้อมูลจำนวนลูกจ้าง: ";
		}
		if($file_row[file_type] == "Hire_docfile"){
			echo "ข้อมูล ม.33: ";
		}
		if($file_row[file_type] == "company_34_docfile_1_adm"){
			echo "ข้อมูล ม.34 - สำเนา สปส 1-10 ส่วนที่ 1 ประจำเดือน ต.ค. ". ($the_year-1-543) . ": ";
		}
		
		$the_file_name_array = explode("_",$file_row[file_name],2);
		
		//print_r($the_file_name_array);
		
		$the_file_name = $the_file_name_array[1];
		
		echo "<a href='https://job.dep.go.th/hire_docfile/".$file_row[file_name]."' target='_blank'>".$the_file_name."</a>";
	}
	
			
	/*if($company_row){	
	
		$the_output = "someVar = { 
					'company_name_thai' : '". formatCompanyName($company_row["CompanyNameThai"], $company_row["CompanyTypeCode"])."'
					, 'company_cid' : '".$company_row["CID"]."'
					}";
		
		echo $the_output; 	
	}else{
		echo "no_result";
	}*/

?>