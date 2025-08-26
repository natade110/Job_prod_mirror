<?php

	include "db_connect.php";
	include "session_handler.php";
	
	error_reporting(1);
	ini_set('max_execution_time', 1800);
	ini_set("memory_limit","256M");
	
	//variables
	$upload_folder = "./to_import_school/";
	$this_lawful_year = 2024;
	
	//first check if current batach is running....
	$have_current_batch = getFirstItem("select var_value from vars where var_name = 'upload_school_file' and var_value > now()");
	//echo $have_current_batch;
	if($have_current_batch){
		header("location: import_org_school.php");
		exit();	
	}

	$files = glob($upload_folder . '*.xlsx');

	foreach ($files as $filename) {
		$import_filename = $filename;	
	}
	
	
	if(!$import_filename){
		doUploadOrgLog($sess_userid, "ไม่พบไฟล์ที่จะทำการ import เข้า temp table", $import_filename);	
		header("location: import_org_school.php?nofile=1");
		exit();
	}
	
	/*echo $import_filename;
	exit();*/
	
	//file is ok -> delete current temp table to prepare file import
	  $sql = "truncate table company_temp_school";
	  mysql_query($sql) or die(mysql_error());
	   doUploadOrgLog($sess_userid, "เตรียมการนำเข้าข้อมูล โรงเรียน", str_replace($upload_folder,"",$old_zip_file));	
	
	
	
	//start doing import file -> mark flag
	$sql = "
		replace into vars(
			
			var_name
			, var_value
		
		)values(
		
			'upload_school_file'
			, DATE_ADD(NOW(), INTERVAL 10 second)
		)
		
	";
	
	mysql_query($sql) or die(mysql_error());
	
	//star reading excel
	//echo "reading excel";  exit();
	
	
	/** PHPExcel_IOFactory */
	//error_reporting(E_ALL);
	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
	require_once './PHPExcel/Classes/PHPExcel/IOFactory.php';
	//echo "included PHPExcel";
	//echo $import_filename; exit();
	
	//echo date('H:i:s') , " Load from Excel2007 file" , EOL; //exit();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load($import_filename);
	
	
	//echo date('H:i:s') , " Iterate worksheets by Row" , EOL;
	
	
	
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
		
				
		//echo 'Worksheet - ' , $worksheet->getTitle() , EOL;	
	
		$rowcount = 0;
		
		foreach ($worksheet->getRowIterator() as $row) {
			
			$rowcount++;
		
			if($rowcount == 1){
				continue;	
			}
			
			if($rowcount >= 3){
				
				$data .= "\r\n";	
				
			}
			
			//echo '    Row number - ' , $row->getRowIndex() , EOL;
	
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
			
			$column_count = 0;
			$row_value = array();
			
			foreach ($cellIterator as $cell) {
				
				
				$column_count++;					
								
				//echo '        Cell - ' , $cell->getCoordinate() , ' - ' , $cell->getCalculatedValue() , EOL;
				
				
				$row_value[$column_count] = doCleanInput((trim($cell->getCalculatedValue())));
				
				
			}
			
			//ends the row
			//write data string
			//$data .= ',"'. doCleanInput((trim($cell->getCalculatedValue()))) . '"';
			$data .= '"'. doCleanInput($row_value[2]) . '"';
			$data .= ',"'. doCleanInput($row_value[3]) . '"';
			$data .= ',"07"';
			$data .= ',"'. doCleanInput($row_value[5]) . '"';
			$data .= ',"'. doCleanInput($row_value[9]) . '"';
			
			$data .= ',"'. doCleanInput($row_value[10]) . '"';
			$data .= ',"'. doCleanInput($row_value[11]) . '"';
			$data .= ',"'. doCleanInput($row_value[12]) . '"';
			$data .= ',"'. doCleanInput($row_value[13]) . '"';
			$data .= ',"'. doCleanInput($row_value[14]) . '"';
			
			$data .= ',"1601"';
			$data .= ',"'. (doCleanInput($row_value[6]) + doCleanInput($row_value[7]) +doCleanInput($row_value[8])) .'"'; //-- SUMMED employees
			$data .= ',""'; //calculated value for later
			$data .= ',""'; //calculated value for later
			$data .= ',""'; //calculated value for later
			
			$data .= ',""'; //calculated value for later
			$data .= ',""'; //calculated value for later			
			$data .= ',"'. doCleanInput($row_value[4]) . '"'; //school-code
			$data .= ',"'. doCleanInput($row_value[6]) . '"'; //school-trachers
			$data .= ',"'. doCleanInput($row_value[7]) . '"';
			
			$data .= ',"'. doCleanInput($row_value[8]) . '"';
			$data .= ',"'. doCleanInput($row_value[15]) . '"'; //school locate
			$data .= ',"'. doCleanInput($row_value[16]) . '"';
			$data .= ',"'. doCleanInput($row_value[17]) . '"';
			
			
			
			//echo $data;
			
		}
		
		
	}
	
	
	
	/*echo "end loading..", EOL;
	echo "<br>".$data;
	exit();
	echo "<br>".$data;
	exit();*/
		
	
	
	file_put_contents($upload_folder."temp/nay301-mini-2.txt", $data);
	
	//echo "created upload file;";
	
	
	//
	$mm = mysql_query("
	
		LOAD DATA LOCAL INFILE '".$upload_folder."temp/nay301-mini-2.txt' 
		INTO TABLE company_temp_school
			CHARACTER SET UTF8
			FIELDS TERMINATED BY ',' 
			OPTIONALLY ENCLOSED BY '".'"'."'
			LINES TERMINATED BY '\r\n'
			
		
		");
		
		//echo "woot";
		//exit();
	if($mm){
		 doUploadOrgLog($sess_userid, "นำไฟล์เข้าโรงเรียน เพื่อทำการตรวจสอบสำเร็จ", str_replace($upload_folder,"",$old_zip_file));	
	}else{
	   doUploadOrgLog($sess_userid, "นำไฟล์โรงเรียนเข้าทำการตรวจสอบไม่สำเร็จ", str_replace($upload_folder,"",$old_zip_file));	
	   header("location: import_org_school.php?failedsql=1");
		exit();
	}
		
	
	
	//yoes 20160205 --- first thing first....
	//update สุราษฏร์ธานี to สุราษฎร์ธานี
	$sql = "
	
			update									
				company_temp_school
			set
				province = 'สุราษฎร์ธานี'
			where
				
				province = 'สุราษฏร์ธานี'
				
	
	
	";
	
	//echo $sql; exit();
	
	mysql_query($sql);
	
	
		
	//yoes 20160203 --- add error rows
	
	//yoes 20160624 -- school has exception on CompanyCode and BranchCode
	$sql = "
	
			update									
				company_temp_school
			set
				is_error = 1
			where
				
				
				(
					(
						char_length(CompanyCode) != 10
						or
						CompanyCode REGEXP '[^0-9]+$'					
						or
						char_length(BranchCode) != 6
						or
						BranchCode REGEXP '[^0-9]+$'
					)
					and
					(
						char_length(BranchCode) > 0
						and
						char_length(CompanyCode) > 0						
					
					)
				)
				
				
													
				or
				
				CompanyTypeCode not in (									
					select
						CompanyTypeCode
					from
						companytype									
				)
				
				or 
				trim(CompanyNameThai) = ''
				or 
				CompanyNameThai is null
				
				
				or
				
				Province not in (									
					select
						province_name
					from
						provinces									
				)
				
				
				
				or
				
				BusinessTypeCode not in (									
					select
						BusinessTypeCode
					from
						businesstype									
				)
				
				
				
				or 
				trim(Employees) = ''
				or 
				Employees is null
				
	
	
	";
	
	mysql_query($sql);
	
	
	//yoes 20160624 -- also create company code to "blank" companycode
	$sql = "
		
		select
			*
		from
			company_temp_school
		where
			char_length(BranchCode) = 0
			and
			char_length(CompanyCode) = 0	
			and 
			is_error = 0
	
		";
	
	$temp_school_result = mysql_query($sql);
	
	
	while($temp_school_row = mysql_fetch_array($temp_school_result)){
		
		//generate new school_code
		$company_code_existed = 1;
		
		while($company_code_existed){
			
			$new_code = "77775".rand(10000,99999);
			
			$company_code_existed = getFirstItem("select 1 from company_temp_school where CompanyCode = '$new_code'");
			
		}
		
		//code not existed
		
		$sql = "
			
			update
				company_temp_school
			set
				companyCode = '$new_code'
				, BranchCode = '000000'
			where
				school_code = '".$temp_school_row[school_code]."'
		
		";
		
		
		mysql_query($sql);
		
	}
	
	
	//yoes 20160801 -- also create "school_code"
	$sql = "
		
		select
			*
		from
			company_temp_school
		where
			char_length(school_code) = 0
			and
			is_error = 0
	
		";
	
	$temp_school_result = mysql_query($sql);
	
	
	while($temp_school_row = mysql_fetch_array($temp_school_result)){
		
		//generate new school_code
		$company_code_existed = 1;
		
		while($company_code_existed){
			
			$new_code = "7775".rand(1000,9999);
			
			$company_code_existed = getFirstItem("select 1 from company_temp_school where school_code = '$new_code'");
			
		}
		
		//code not existed
		
		$sql = "
			
			update
				company_temp_school
			set
				school_code = '$new_code'				
			where
				CompanyCode = '".$temp_school_row[CompanyCode]."'
		
		";
		
		
		mysql_query($sql);
		
	}
	
	
	//yoes 20160624 -- mark whether this is new or old company
	
	//first see from compnaycode & branchcode & school_code
	$sql = "
	
		update
			company_temp_school a
				join
					company_meta b
					on a.school_code = b.meta_value and b.meta_for ='school_code'
				join company c
					on b.meta_cid = c.cid
					and
					a.CompanyCode = c.CompanyCode and a.BranchCode = c.BranchCode
		set
			a.is_old_org = c.cid
			, a.old_org_type = 'companybranchschool'
			where
			a.is_old_org = 0		
	
	";
	
	mysql_query($sql);
	
	//next see from compnaycode & branchcode
	$sql = "
	
		update
			company_temp_school a
				join company b
					on a.CompanyCode = b.CompanyCode and a.BranchCode = b.BranchCode
		set
			a.is_old_org = b.cid
			, a.old_org_type = 'companybranch'
		where
			a.is_old_org = 0 			
	
	";
	
	mysql_query($sql);
	
	
	//next just see from school_code
	//
	$sql = "
	
		update
			company_temp_school a
				join
					company_meta b
					on right(a.school_code,8) = b.meta_value and b.meta_for ='school_code'
				join company c
					on b.meta_cid = c.cid
		set
			a.is_old_org = c.cid
			, a.CompanyCode = c.CompanyCode
			, a.BranchCode = c.BranchCode
			, a.old_org_type = 'school'
			where
			a.is_old_org = 0
			
	
	";
	
	mysql_query($sql);
	
	
	//NEXT -> also check if these contains "MERGED" company
	$sql = "
		
		select
			*
		from
			company_temp_school a
				join 
					company_meta b
					on
					a.is_old_org = b.meta_cid
					and
					b.meta_for = 'merged_to'
		
		where
			a.is_old_org > 0
	
	
	";
	
	$merged_result = mysql_query($sql);
	
	while($merged_row = mysql_fetch_array($merged_result)){
		//print_r($merged_row);
		
		//try get CompanyCode and Branch code for current final company....
		$merged_from = $merged_row[meta_cid];
		$merged_to = $merged_row[meta_value];
		
		$still_have_parent_cid = 1;
		
		
		while($still_have_parent_cid){
		
			$sql = "
			
				select
					meta_value
				from				
					company_meta b					
				where
					meta_for = 'merged_to'
					and
					meta_cid = '$merged_to'
			
			";
			
			$this_merged_to = getFirstItem($sql);
			
			if(!$this_merged_to){
				
				$still_have_parent_cid = 0;
				
			}else{				
				$merged_to = $this_merged_to;	
				$still_have_parent_cid = 1;
			}
		
		}
		
		
		//got FINAL $merged_to
		//update this to this school_temp
		
		$sql = "
			
			update
				company_temp_school
			set
				is_old_org = '$merged_to'
			where
				is_old_org = '$merged_from'
		
		";
				
		//echo $sql; exit();
		mysql_query($sql);
		
		$sql = "
		
			update
				company_temp_school a					
					join company b
						on a.is_old_org = b.cid						
			set
				a.is_old_org = b.cid
				, a.CompanyCode = b.CompanyCode
				, a.BranchCode = b.BranchCode
				, a.old_org_type = 'merged'
			where
				a.is_old_org = '$merged_to'
				
		
		";
		
		//echo $sql; exit();
		
		mysql_query($sql);
		
	}	
	
	
	
	//echo $merged_to; exit();
	
	//exit();
	
	//delete not-in-case company
	// 148 seconds to run this
	$sql = "
	
		update
			company_temp_school
		set
			is_in_case = 1
		where
			companyCode in (
				
				
				select companyCode from (
					select
						companyCode
						, sum(employees)
					from
						company_temp_school
					group by
						companyCode
					having 
						sum(employees) >= 100
				) mnm
					
			)
	
	";
	
	mysql_query($sql);
	
	//echo "done delete not in case";
	
	
	//yoes 20150205 --- set government org into "not in case"
	$sql = "
	
		update
			company_temp_school
		set
			is_in_case = 0
			, is_government = 1
		where
			CompanyTypeCode = 14
			or
			companyCode in (
				
				
				select companyCode from (
					select
						companyCode
					from
						company
					where
						companyCode = 299
				) mnm
					
			)
	
	";
	
	mysql_query($sql);
	
	//echo "done delete not in case";
	
	
	//yoes 20160801 --> all SCHOOL in IN_CASE
	//yoes 20221212 --> not all school is in case? -- still - import all
	$sql = "
	
		update
			company_temp_school
		set
			is_in_case = 1
	";
	
	mysql_query($sql);
	
	
	
	
	//yoes 20160203 ---> also mark company that already have lawfulness records
	$sql = "
	
		update
			company_temp_school a
				join 
				
				(
					
					
					select 
						companyCode 
					from 
						company a
							join
							lawfulness b
								on a.cid = b.cid
								and b.year = $this_lawful_year
								and
								( b.LawfulStatus != 0 and b.LawfulStatus != 3)
						
				) bbbb
				on a.companyCode = bbbb.companyCode
				and
				a.is_in_case = 1
		set
			a.has_lawfulness = 1
		
	
	";
	
	mysql_query($sql) or die(mysql_error());
	
	
	//also mark schhol
	//yoes 20160203 ---> also mark company that already have lawfulness records
	$sql = "
	
		update
			company_temp_school a
				join 
				
				(
					
					
					select 
						a.cid 
					from 
						company a
							join
							lawfulness b
								on a.cid = b.cid
								and b.year = $this_lawful_year
								and
								( b.LawfulStatus != 0 and b.LawfulStatus != 3)
						
				) bbbb
				on a.is_old_org = bbbb.cid
				and
				a.is_in_case = 1
				and
				a.is_old_org > 0
		set
			a.has_lawfulness = 1
		
	
	";
	
	mysql_query($sql) or die(mysql_error());
	
	
	//yoes 20160203 ---> also mark company that have no main branch
	$sql = "
	
		update
			company_temp_school	
		set
			no_main_branch = 1
		where
			is_in_case = 1			
								
			and
			branchcode > 1
			and
			companycode not in (
				
				select companycode from (
					select companycode
					FROM `company_temp_school` 
					where 
					branchcode < 1
					and
					is_in_case = 1				
				) bbb
			)			
	
	";
	
	mysql_query($sql) or die(mysql_error());
	
	
	
	
	
	//start doing import file -> unmark flag
	$sql = "
		replace into vars(
			
			var_name
			, var_value
		
		)values(
		
			'upload_org_file'
			, NOW()
		)
		
	";
	
	mysql_query($sql) or die(mysql_error());
	
	header("location: import_org_school.php");
?>