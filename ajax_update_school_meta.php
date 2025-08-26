<?php

	include "db_connect.php";
		
	$sql = "
			
			select
				*		
			from 
				company_temp_school a
					join 
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
		
		
		
		echo "all meta inserted";