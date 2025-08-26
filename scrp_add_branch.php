<?php

	//function for Company user to create new branch

	include "db_connect.php";
	
	//table name
	$table_name = "company_company";
	
	//first, validate company code
	$the_code = doCleanInput($_POST["CompanyCode"]);
	$the_branch = doCleanInput($_POST["BranchCode"]);
	$the_cid = doCleanInput($_POST["CID"]);
	
	
	
	//also see if there are already this branch_code in system
	$count_company = getFirstItem("select CID from company
						where 
							CompanyCode = '".$the_code."'
							and BranchCode = '".$the_branch."'
							
							");
	if(strlen($count_company) > 0){
		//come to this page via lawful tab
		header("location: organization.php?error=duped" );
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
						
						,'BranchCode'
						
												
						);
					
	//fields not from $_post	
	$special_fields = array("LastModifiedDateTime","LastModifiedBy","Employees", "CreatedDateTime","CreatedBy", "is_active_branch");
	$special_values = array("NOW()","'$sess_userid'","'".deleteCommas($_POST['Employees'])."'","NOW()","'$sess_userid'","1");
	
	//add vars to db
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, "replace");
	
	//echo $the_sql;exit();
	mysql_query($the_sql);
	$this_id = mysql_insert_id();
	
	if($sess_accesslevel == 4){
		$target = "general";
	}else{
		$target = "input";
	}
	
	header("location: organization.php?id=$the_cid&added=added&focus=$target");

?>