<?php

	include "db_connect.php";
	include "session_handler.php";
	
	ini_set('max_execution_time', 300);
	ini_set("memory_limit","256M");

	//....
	
	//first check if current batach is running....
	$have_current_batch = getFirstItem("select var_value from vars where var_name = 'upload_org_file' and var_value > now()");
	//echo $have_current_batch;
	if($have_current_batch){
		header("location: import_org_new.php");
		exit();	
	}
	
	//$the_lawful_year = "2017";
	
	$the_lawful_year = getFirstItem("select var_value from vars where var_name = 'import_org_year'");
	
	//(1) -> delete org that "ไม่เข้าข่าย"	
	//this one should not need in LIVE environment as admin may just want to import everything
	// 60 seconds here
	//start trans
	
	doUploadOrgLog($sess_userid, "START TRANSACTION นำข้อมูลเข้าระบบ", '');	
	
	
	//start doing import file -> mark flag
	$sql = "
		replace into vars(
			
			var_name
			, var_value
		
		)values(
		
			'upload_org_file'
			, DATE_ADD(NOW(), INTERVAL 30 minute)
		)
		
	";
	mysql_query($sql);
	
	
	mysql_query("START TRANSACTION");
	
	
	
	$sql = "
	
		update
			company_temp_all b
			,provinces c
		set
			b.province = c.province_id
		where
			b.province = c.province_name
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	";
	
	$query_01 = mysql_query($sql);
	
	
	//(2)----------------- next. update branchcode from 0 to 000000
	$sql = "
	
		update	
			company_temp_all
		set
			BranchCode = '000000'
		where
			trim(BranchCode) = '0'
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	";
	
	$query_02 = mysql_query($sql);
	
	
	//(3)------- insert company into real table (do for "BRAND NEW" company) only
	
	
	
	//(4) ------ next do insert branch that is "BRAND NEW" for this time......
	
	
	
	$sql = "
	
			
		insert ignore into 
		company( 
		
			LastModifiedDateTime 
			, CreatedDateTime
			, CreatedBy
			,CompanyCode
			,BranchCode
			,CompanyTypeCode
			,CompanyNameThai
			
			,Address1
			,Subdistrict
			,District
			
			,Zip
			
			,Telephone
			,BusinessTypeCode
			,Employees 
			
			, is_active_branch
		)
		select
			
			now()
			, now()
			, '$sess_userid'
			,a.CompanyCode
			,a.BranchCode
			,a.CompanyTypeCode
			,a.CompanyNameThai
			
			,a.Address1
			,a.Subdistrict
			,a.District
			
			,a.Zip
			
			,a.Telephone
			, if(a.BusinessTypeCode = '', '0000', a.BusinessTypeCode)
			,a.Employees 
			
			, '1'
		
		 from 
			company_temp_all a
				left outer join 
					company b on a.companyCode = b.companyCode 
					and a.branchCode = b.branchCode
					
		where
			b.branchCode is null
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	
	
	";
	
	
	$query_03 = mysql_query($sql);
	
	
	//(4) manually, we will get status for "CompanyTypeCode" ที่เป็นหน่วยงานราชการ มาจากปีที่ผ่านมา
	//but automatically -> admin shouldn't try to import those in the file
	
	
	//(5) ---- next -> update new data into old data	
	//update employees
	
	
	//before doing this -> we have to do company_full_log
	$sql = "
		
		insert into 
					company_full_log
				select
					a.*
					, now()
					, '$sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, 'upload_import_org.php'
					, ''
				from
					company a
					,company_temp_all b
				where
					a.CompanyCode = b.CompanyCode
					and
					a.BranchCode = b.BranchCode
					and
					is_in_case = 1
					and
					has_lawfulness = 0
				
	
	";
	
	//mysql_query("ROLLBACK"); 
	
	//echo $sql; exit();	
	$query_04 = mysql_query($sql);
	
	
	//exit();
	
	$sql = "
	
		update
			company a
			,company_temp_all b
		set
			a.employees = b.employees
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			a.employees != b.employees
			and
			b.employees > 0	
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	";
	$query_05 = mysql_query($sql);
	
	
	//update province
	
	//yoes 20231103 -- only update ที่อยู่ของ company จังหวัดที่ province ตรงกัน
	$sql = "
	
		update
			company a
			,company_temp_all b
			
		set
			a.province = b.province
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			-- a.province != b.province
			a.province = b.province
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	";
	$query_06 = mysql_query($sql);
	
	
	//update other columns
	//yoes 20231103 -- only update ที่อยู่ของ company จังหวัดที่ province ตรงกัน
	$sql = "
	
		update
			company a
			,company_temp_all b
		set
		
			a.CompanyCode	= b.CompanyCode
			,a.BranchCode = b.BranchCode
			
			,a.CompanyNameThai = b.CompanyNameThai
			,a.Address1	= b.Address1
			,a.Subdistrict = b.Subdistrict
			,a.District	= b.District
			,a.Zip	= b.Zip
			
			
			, a.last_modified_lid_year = '$the_lawful_year'
			, a.is_active_branch = 1
			
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			a.province = b.province
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	";
	$query_07 = mysql_query($sql);
	
	
	//yoes 20191203
	//only update companyTypeCode that is not GOV
	$sql = "
	
		update
			company a
			,company_temp_all b
		set
		
			a.CompanyTypeCode = b.CompanyTypeCode
			
			
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			is_in_case = 1
			and
			has_lawfulness = 0
			and
			a.CompanyTypeCode != 14			
			and
			(
			a.CompanyTypeCode < 201
			or
			a.CompanyTypeCode > 299 
			)
			
	
	";
	$query_07_01 = mysql_query($sql);
	
	
	//yoes 20191203
	//skip blank business typecode 
	
	//,a.BusinessTypeCode	= b.BusinessTypeCode
	$sql = "
	
		update
			company a
			,company_temp_all b
		set
		
			a.BusinessTypeCode	= b.BusinessTypeCode
			
			
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			is_in_case = 1
			and
			has_lawfulness = 0
			and			
			(
			b.BusinessTypeCode != ''
			and
			b.BusinessTypeCode is not null
			)
			
	
	";
	$query_07_02 = mysql_query($sql);
	
	//yoes 20191203 -- update telephone
	//,a.Telephone	= b.Telephone
	$sql = "
	
		update
			company a
			,company_temp_all b
		set
		
			a.Telephone	= b.Telephone
			
			
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			is_in_case = 1
			and
			has_lawfulness = 0
			and			
			(
			b.Telephone != ''
			and
			b.Telephone is not null
			)
			
	
	";
	$query_07_03 = mysql_query($sql);
	
	//province id (incase province name from file mismatch with province id on system)
	//yoes 20231103 -- no need to do this เพราะจะเกิดกรณี จังหวัดเปลี่ยน แล้วข้อมูลจะย้ายจังหวัด = พมจ จะสงสัย
	$sql = "
	
		update
			company a
			,company_temp_all b
			,provinces c
		set
			a.province = c.province_id
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			b.province = c.province_name
			and
			-- a.province != c.province_id
			a.province == c.province_id
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	
	";
	$query_08 = mysql_query($sql);
	
	
	echo "all-done for import org";
	
	
	///now do something about lawfulness

	
	
	$sql = "
		
		insert into 
					lawfulness_full_log
				select
					b.*
					, now()
					, '$sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, 'upload_import_org.php'
					, ''
				from
					company a
					, lawfulness b
					, (
					
						SELECT companyCode as the_company_code, sum( employees ) as summed_employees
						FROM company_temp_all
						where
						is_in_case = 1
						and
						has_lawfulness = 0
						GROUP BY companyCode	
					
					)e
				where
					a.cid = b.cid
					and
					a.companyCode = e.the_company_code
					
					and
					b.year = $the_lawful_year
					and
					branchcode < 1
				
	
	";
	
	//echo $sql;	
	$query_09 = mysql_query($sql);
	
	//exit();
	
	
	$sql = "
	
		insert ignore into
			lawfulness(CID, year, lawful_created_date, lawful_created_by)	
		select
			cid, '$the_lawful_year', now(), '$sess_userid'
		from
			company a
			, company_temp_all b
		where
			a.companyCode = b.CompanyCode
			and
			a.branchCode = b.branchCode
			and
			a.BranchCode < 1
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	";
	$query_10 = mysql_query($sql);
	
	
	//(7) also insert lawfulness for company that have BRANCH but no MAIN BRANCH in the file - but already have MAIN BRANCH in the database 
	$sql = "
	
	
		update	
			company a
			, lawfulness b
			, (
			
			SELECT companyCode as the_company_code, sum( employees ) as summed_employees
			FROM company_temp_all
			where
			is_in_case = 1
			and
			has_lawfulness = 0
			GROUP BY companyCode	
			
			)e
		set
			b.employees = summed_employees
		where
			a.cid = b.cid
			and
			a.companyCode = e.the_company_code
			
			and
			b.year = $the_lawful_year
			and
			branchcode < 1
						
	
	";
	
	$query_11 = mysql_query($sql);
	
	
	//commit or rollback?
	if ($query_01 && $query_02 && $query_03 && $query_04 && $query_05 && $query_06 && $query_07
		 && $query_08 && $query_09 && $query_10 && $query_11 ) {
		mysql_query("COMMIT");
		//echo "yessss!"; exit();
		doUploadOrgLog($sess_userid, "COMMIT ข้อมูลเข้าระบบเรียบร้อย", '');	
	} else {        
		mysql_query("ROLLBACK"); 		
		
		if(!$query_01){
			doUploadOrgLog($sess_userid, "Query 1 ผิดพลาด", '');	
		}
		if(!$query_02){
			doUploadOrgLog($sess_userid, "Query 2 ผิดพลาด", '');	
		}
		if(!$query_03){
			doUploadOrgLog($sess_userid, "Query 3 ผิดพลาด", '');	
		}
		if(!$query_04){
			doUploadOrgLog($sess_userid, "Query 4 ผิดพลาด", '');	
		}
		if(!$query_05){
			doUploadOrgLog($sess_userid, "Query 5 ผิดพลาด", '');	
		}
		if(!$query_06){
			doUploadOrgLog($sess_userid, "Query 6 ผิดพลาด", '');	
		}
		if(!$query_07){
			doUploadOrgLog($sess_userid, "Query 7 ผิดพลาด", '');	
		}
		if(!$query_08){
			doUploadOrgLog($sess_userid, "Query 8 ผิดพลาด", '');	
		}
		if(!$query_09){
			doUploadOrgLog($sess_userid, "Query 9 ผิดพลาด", '');	
		}
		if(!$query_10){
			doUploadOrgLog($sess_userid, "Query 10 ผิดพลาด", '');	
		}
		if(!$query_11){
			doUploadOrgLog($sess_userid, "Query 11 ผิดพลาด", '');	
		}
		
		
		doUploadOrgLog($sess_userid, "ROLLBACK ข้อมูลเข้าระบบไม่ถูกต้อง", '');	
		
				
		header("location: import_org_new.php");
		exit();
	}
	
	
		
	//add this new information into company_sso
	$sql = "
		
		insert into
			companysso_all(
			
			
				CompanyCode
				,BranchCode
				,CompanyTypeCode
				,CompanyNameThai
				,Address1
				,Subdistrict
				,District
				,Province
				,Zip
				,Telephone
				,BusinessTypeCode
				,Employees
				,sso_year
				,sso_by
				,sso_date

			
			)
		select
			CompanyCode
			,BranchCode
			,CompanyTypeCode
			,CompanyNameThai
			,Address1
			,Subdistrict
			,District
			,Province
			,Zip
			,Telephone
			,BusinessTypeCode
			,Employees
			,'$the_lawful_year'
			,'$sess_userid'
			, now()
		from
			company_temp_all
	
	
	";
	
	
	mysql_query($sql);
	
	doUploadOrgLog($sess_userid, "เก็บข้อมูลลง table SSO แล้ว", '');	
	
	
	//start doing import file -> mark flag
	$sql = "
		replace into vars(
			
			var_name
			, var_value
		
		)values(
		
			'upload_org_file'
			, now()
		)
		
	";
	mysql_query($sql);
	
	//add successlog
	
		
	//do a full success log right here
	$upload_folder = "./to_import/";
	$files = glob($upload_folder . '*.zip');
					
	foreach ($files as $filename) {
		$old_zip_file = $filename;	
		
		//echo $old_zip_file;
	}
	
	//exit();
	
	doUploadOrgLog($sess_userid, "upload ข้อมูลสถานประกอบการเสร็จสิ้น", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "จำนวนสถานประกอบการ ".getFirstItem("select count(*) from company_temp_all")." แห่ง", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "จำนวนสถานประกอบการที่ข้อมูลถูกต้อง ".getFirstItem("select count(*) from company_temp_all where is_error = 0")." แห่ง", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "จำนวนสถานประกอบการที่ข้อมูลไม่ถูกต้อง ".getFirstItem("select count(*) from company_temp_all where is_error = 1")." แห่ง", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "ที่เข้าข่ายต้องปฏิบัติตามกฏหมาย และถูกนำเข้าระบบ ".getFirstItem("select count(*) from company_temp_all where is_in_case = 1")." แห่ง", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "ปีงบประมาณที่นำเข้า ".($the_lawful_year+543), str_replace($upload_folder,"",$old_zip_file));	
	
	
	//alll data done --> clear everything
	$sql = "truncate table company_temp_all";
	mysql_query($sql);
	doUploadOrgLog($sess_userid, "ลบข้อมูล temp ออกจากระบบ", "");	
 
	
	include "scrp_do_delete_import_org_file.php";
	
	
	header("location: import_org_new.php?org_created=1");
	
?>