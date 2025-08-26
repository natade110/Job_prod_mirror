<?php

	include "db_connect.php";
	include "session_handler.php";
	
	ini_set('max_execution_time', 300);
	ini_set("memory_limit","256M");

	//....
	
	//first check if current batach is running....
	$have_current_batch = getFirstItem("select var_value from vars where var_name = 'upload_school_file' and var_value > now()");
	//echo $have_current_batch;
	if($have_current_batch){
		header("location: import_org_school.php");
		exit();	
	}
	
	$the_lawful_year = "2024";
	
	//(1) -> delete org that "ไม่เข้าข่าย"	
	//this one should not need in LIVE environment as admin may just want to import everything
	// 60 seconds here
	//start trans
	
	doUploadOrgLog($sess_userid, "START TRANSACTION นำข้อมูลโรงเรียนเข้าระบบ", '');	
	
	
	//start doing import file -> mark flag
	$sql = "
		replace into vars(
			
			var_name
			, var_value
		
		)values(
		
			'upload_school_file'
			, DATE_ADD(NOW(), INTERVAL 5 second)
		)
		
	";
	mysql_query($sql);
	
	
	mysql_query("START TRANSACTION");
	
	
	
	$sql = "
	
		update
			company_temp_school b
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
			company_temp_school
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
			,a.BusinessTypeCode
			,a.Employees 
			
			, '1'
		
		 from 
			company_temp_school a
				left outer join 
					company b on a.companyCode = b.companyCode 
					and a.branchCode = b.branchCode
					
		where
			b.branchCode is null
			and
			is_in_case = 1
			and
			has_lawfulness = 0
			and
			is_old_org = 0
			
	
	
	
	";
	
	
	$query_03 = mysql_query($sql);
	
	
	
	//next manage old org that duped on "school_code"
	
	/*$sql = "
		
		select
			*
		from
			company_temp_school
		where
			old_org_type = 'school'
	
	";
	
	$old_org_result = mysql_query($sql);
	
	while($old_org_row = mysql_fetch_array($old_org_result)){
		
		//see "current" row is it is school
		$current_is_school = getFirstItem("select meta_value from company_meta where meta_for = 'is_school' and meta_cid = '".$old_org_row[is_old_org]."'");
		
		if($current_is_school){
			
			//replace everthing?
			
		}
		
	}*/
	
	
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
					,company_temp_school b
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
			,company_temp_school b
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
	
	
	$sql = "
	
		update
			company a
			,company_temp_school b
			
		set
			a.province = b.province
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			a.province != b.province
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	";
	$query_06 = mysql_query($sql);
	
	
	//update other columns
	
	$sql = "
	
		update
			company a
			,company_temp_school b
		set
		
			a.CompanyCode	= b.CompanyCode
			,a.BranchCode = b.BranchCode
			,a.CompanyTypeCode = b.CompanyTypeCode			
			,a.Address1	= IF(b.Address1 IS NULL or b.Address1 = '', a.Address1, b.Address1)
			,a.Subdistrict = IF(b.Subdistrict IS NULL or b.Subdistrict = '', a.Subdistrict, b.Subdistrict)
			,a.District	= IF(b.District IS NULL or b.District = '', a.District, b.District)
			,a.Zip	= IF(b.Zip IS NULL or b.Zip = '', a.Zip, b.Zip)
			,a.Telephone	= IF(b.Telephone IS NULL or b.Telephone = '', a.Telephone, b.Telephone)
			,a.BusinessTypeCode	= b.BusinessTypeCode
			, a.last_modified_lid_year = '$the_lawful_year'
			, a.is_active_branch = 1
			
		where
			a.CompanyCode = b.CompanyCode
			and
			a.BranchCode = b.BranchCode
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	";
	$query_07 = mysql_query($sql);
	
	//province id (incase province name from file mismatch with province id on system)
	$sql = "
	
		update
			company a
			,company_temp_school b
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
			a.province != c.province_id
			and
			is_in_case = 1
			and
			has_lawfulness = 0
	
	
	";
	$query_08 = mysql_query($sql);
	
	
	//echo "all-done for import org";
	
	
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
						FROM company_temp_school
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
			lawfulness(CID, year, lawful_created_date, lawful_created_by, LawfulStatus)	
		select
			cid, '$the_lawful_year', now(), '$sess_userid', IF(b.Employees >= 100, 0, 3)
		from
			company a
			, company_temp_school b
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
			FROM company_temp_school
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
		
		
		//yoes 20160524 --> special for school....
		//add metas
		
		//what CID should be included?
		$sql = "
			
			select
				*		
			from 
				company_temp_school a
					left join 
						company b on a.companyCode = b.companyCode 
						and a.branchCode = b.branchCode					
			
		
		";
		
		//echo $sql; exit();
		
		$cid_result = mysql_query($sql);
		
		while($cid_row = mysql_fetch_array($cid_result)){
			
			$this_cid = $cid_row[CID];		
			$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','school_charity','".$cid_row[school_charity]."')";
			mysql_query($sql);
			
			
			//yoes 20160801 -- check if old school_code existed			
			$old_school_code = getFirstItem("select meta_value from company_meta where meta_for = 'school_code' and meta_cid = '$this_cid'");
			
			if(!$old_school_code){
				$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','school_code','".$cid_row[school_code]."')";
				mysql_query($sql);
			}
			
			
			$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','school_contract_teachers','".$cid_row[school_contract_teachers]."')";
			mysql_query($sql);
			$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','school_employees','".$cid_row[school_employees]."')";
			mysql_query($sql);
			$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','school_locate','".$cid_row[school_locate]."')";	
			mysql_query($sql);
			$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','school_teachers','".$cid_row[school_teachers]."')";
			mysql_query($sql);
			$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','school_type','".$cid_row[school_type]."')";
			mysql_query($sql);
			
			$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','is_school','1')";
			mysql_query($sql);
			
			//yoes 20160801 -- also add school_name
			$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$this_cid','school_name','".$cid_row[CompanyNameThai]."')";
			mysql_query($sql);
			
			//also add lawfulness meta
			$this_lid = getFirstItem("select LID from lawfulness where cid = '$this_cid' and year = $the_lawful_year");
			$sql = "replace into lawfulness_meta(meta_lid, meta_for, meta_value) values('$this_lid','school_contract_teachers','".$cid_row[school_contract_teachers]."')";
			mysql_query($sql);
			$sql = "replace into lawfulness_meta(meta_lid, meta_for, meta_value) values('$this_lid','school_employees','".$cid_row[school_employees]."')";
			mysql_query($sql);
			$sql = "replace into lawfulness_meta(meta_lid, meta_for, meta_value) values('$this_lid','school_teachers','".$cid_row[school_teachers]."')";
			mysql_query($sql);
			
			
			
			
		}
		
		
		
		
	} else {        
		mysql_query("ROLLBACK"); 		
		
		if(!$query_01){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 1 ผิดพลาด", '');	
		}
		if(!$query_02){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 2 ผิดพลาด", '');	
		}
		if(!$query_03){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 3 ผิดพลาด", '');	
		}
		if(!$query_04){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 4 ผิดพลาด", '');	
		}
		if(!$query_05){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 5 ผิดพลาด", '');	
		}
		if(!$query_06){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 6 ผิดพลาด", '');	
		}
		if(!$query_07){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 7 ผิดพลาด", '');	
		}
		if(!$query_08){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 8 ผิดพลาด", '');	
		}
		if(!$query_09){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 9 ผิดพลาด", '');	
		}
		if(!$query_10){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 10 ผิดพลาด", '');	
		}
		if(!$query_11){
			doUploadOrgLog($sess_userid, "โรงเรียน Query 11 ผิดพลาด", '');	
		}
		
		
		doUploadOrgLog($sess_userid, "ROLLBACK ข้อมูลโรงเรียนเข้าระบบไม่ถูกต้อง", '');	
		
				
		header("location: import_org_school.php");
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
			company_temp_school
	
	
	";
	
	
	mysql_query($sql);
	
	doUploadOrgLog($sess_userid, "เก็บโรงเรียนข้อมูลลง table SSO แล้ว", '');	
	
	
	//start doing import file -> mark flag
	$sql = "
		replace into vars(
			
			var_name
			, var_value
		
		)values(
		
			'upload_school_file'
			, now()
		)
		
	";
	mysql_query($sql);
	
	//add successlog
	
		
	//do a full success log right here
	$upload_folder = "./to_import_school/";
	$files = glob($upload_folder . '*.xlsx');
					
	foreach ($files as $filename) {
		$old_zip_file = $filename;	
		
		//echo $old_zip_file;
	}
	
	//exit();
	
	doUploadOrgLog($sess_userid, "upload ข้อมูลโรงเรียนเสร็จสิ้น", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "จำนวนโรงเรียน ".getFirstItem("select count(*) from company_temp_school")." แห่ง", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "จำนวนโรงเรียนที่ข้อมูลถูกต้อง ".getFirstItem("select count(*) from company_temp_school where is_error = 0")." แห่ง", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "จำนวนโรงเรียนที่ข้อมูลไม่ถูกต้อง ".getFirstItem("select count(*) from company_temp_school where is_error = 1")." แห่ง", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "ที่เข้าข่ายต้องปฏิบัติตามกฏหมาย และถูกนำเข้าระบบ ".getFirstItem("select count(*) from company_temp_school where is_in_case = 1")." แห่ง", str_replace($upload_folder,"",$old_zip_file));	
	doUploadOrgLog($sess_userid, "ปีงบประมาณที่นำเข้า ".($the_lawful_year+543), str_replace($upload_folder,"",$old_zip_file));	
	
	
	//alll data done --> clear everything
	$sql = "truncate table company_temp_school";
	mysql_query($sql);
	doUploadOrgLog($sess_userid, "ลบข้อมูล temp โรงเรียนออกจากระบบ", "");	
 
	
	include "scrp_do_delete_import_school_file.php";
	
	
	header("location: import_org_school.php?org_created=1");
	
?>