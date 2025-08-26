<?php

	include "db_connect.php";
	
	//table name
	$table_name = "company";
	
	//first, validate company code
	$the_code = doCleanInput($_POST["CompanyCode"]);
	$the_branch = doCleanInput($_POST["BranchCode"]);
	
	$count_company = getFirstItem("select CID from company
						where 
							CompanyCode = '".$the_code."'
							and BranchCode = '".$the_branch."'
							
							");
	if(strlen($count_company) > 0){
		//come to this page via lawful tab
		header("location: organization.php?mode=new&new_id=$the_code&new_id_link=$count_company&branch=$the_branch" );
		exit();
	}						
	
	//specify all posts fields
	$input_fields = array(
						'CompanyCode'
						,'CompanyNameThai'
						,'CompanyNameEng'
						,'Address1'
						
						,'Moo'
						,'Soi'
						,'Road'
						,'Subdistrict'
						,'District'
						
						,'Province'
						,'Zip'
						,'Telephone'
						,'email'
						,'TaxID'
						
						,'CompanyTypeCode'
						,'BranchCode'
						,'org_website'
						,'BusinessTypeCode'
						
						,'Status'
						
						,'ContactPerson1'
						,'ContactPhone1'
						,'ContactEmail1'
						,'ContactPosition1'
						,'ContactPerson2'
						,'ContactPhone2'
						,'ContactEmail2'
						,'ContactPosition2'
												
						);
					
	//fields not from $_post	
	$special_fields = array("LastModifiedDateTime","LastModifiedBy","Employees", "CreatedDateTime","CreatedBy", "is_active_branch");
	$special_values = array("NOW()","'$sess_userid'","'".deleteCommas($_POST['Employees'])."'","NOW()","'$sess_userid'","1");
	
	//add vars to db
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	
	//echo $the_sql;exit();
	mysql_query($the_sql);
	$this_id = mysql_insert_id();
	
	//yoes 20160623 -- handle school information
	if(!$_POST[is_school]){
		$_POST[is_school] = 0;
	}
	
	$meta_fields = array(
	
		'school_code'
		,'school_type'
		,'school_locate'
		,'school_charity'
		
		,'school_teachers'
		,'school_contract_teachers'
		,'school_employees'
		
		,'is_school'
		
		,'school_name'
		,'commercial_code'
	
	);
	
	for($metai=0;$metai<count($meta_fields);$metai++){
					
		
		$meta_value = doCleanInput($_POST[$meta_fields[$metai]]);
		
		if($meta_fields[$metai] == "school_teachers" || $meta_fields[$metai] == "school_contract_teachers" || $meta_fields[$metai] == "school_employees"){
			$meta_value = deleteCommas($meta_value);
		}				
		
		
		if(strlen($meta_value)){
			
			$meta_sql = "
						replace into
						company_meta
						(
							meta_cid
							, meta_for
							, meta_value
						)values(
							
							'".$this_id."'
							,'".$meta_fields[$metai]."'
							,'".$meta_value."'
						)
							
						";
			
			mysql_query($meta_sql);
			
			//echo "<br>".$meta_sql;
		
		}
	
	}
	
	//exit();
	
	//also update employees accordingly
	
	if($_POST[school_teachers] || $_POST[school_contract_teachers] || $_POST[school_employees]){
		$_POST['Employees'] = deleteCommas($_POST[school_teachers])+deleteCommas($_POST[school_contract_teachers])+deleteCommas($_POST[school_employees]);
	}
	
	
	/*
	$the_end_year = date("Y")+10;
	for($i= $the_end_year;$i>=2007;$i--){
	
		//also generate defaul lawfulness
		$the_sql = "insert ignore into lawfulness(CID, Year, employees) values('$this_id', '$i','".deleteCommas($_POST['Employees'])."')";
		
		mysql_query($the_sql);
	
	}*/
	
	//yoes 20160201
	$district_to_clean_cid = $this_id;
	include "scrp_update_district_cleaned_to_cid.php";
	
	
	//what year to create a lawfulness for
	$lawful_year = $_POST["ddl_year"];
	
	$the_sql = "insert ignore into lawfulness(CID, Year, employees) values('$this_id', '$lawful_year','".deleteCommas($_POST['Employees'])."')";	
	mysql_query($the_sql);
	
	$new_lid = mysql_insert_id();
	
	//yoes 20160623 -- also add lawulness incase of "is_school"
	
	if($_POST[is_school]){
		
		$meta_fields = array(
		
			'school_teachers'
			,'school_contract_teachers'
			,'school_employees'
		
		);
		
		for($metai=0;$metai<count($meta_fields);$metai++){
						
			$meta_sql = "
						replace into
						lawfulness_meta
						(
							meta_lid
							, meta_for
							, meta_value
						)values(
							
							'".$new_lid."'
							,'".$meta_fields[$metai]."'
							,'".deleteCommas($_POST[$meta_fields[$metai]])."'
						)
							
						";
			
			mysql_query($meta_sql);
		
		}
	
	}
	
	
	header("location: organization.php?id=$this_id&added=added");

?>