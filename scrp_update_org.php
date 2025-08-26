<?php

	include "db_connect.php";
	include "session_handler.php";
	include "scrp_config.php";
	
	//table name
	$table_name = "company";
	$this_id = doCleanInput($_POST["CID"]);
	
	//yoes 20160105
	//add log before doing anything
	doCompanyFullLog($sess_userid, $this_id, basename($_SERVER["SCRIPT_FILENAME"]));	
	
	if($_POST["btn_delete"]){
	
	
		//yoes 20160126 --> add log before delete		
	
		//delete everything
		$the_sql = "delete from announcecomp where CID = '$this_id'";
		mysql_query($the_sql);
		
		$the_sql = "delete from docrequestcompany where CID = '$this_id'";
		mysql_query($the_sql);
		
		$the_sql = "delete from document_requests where docr_org_id = '$this_id'";
		mysql_query($the_sql);

		$the_sql = "delete from lawful_employees where le_cid = '$this_id'";
		mysql_query($the_sql);
		
		//delete payments of this LID
		$the_sql = "select LID from lawfulness where CID = '$this_id'";
		$the_result = mysql_query($the_sql);
		while($post_row = mysql_fetch_array($the_result)){
			
			//add full log for delete
			doLawfulnessFullLog($sess_userid, $post_row["LID"], "deleted-".basename($_SERVER["SCRIPT_FILENAME"]));
			
			$the_sql = "delete from payment where LID = '".$post_row["LID"]."'";
			mysql_query($the_sql);
		}
		
		$the_sql = "delete from lawfulness where CID = '$this_id'";
		mysql_query($the_sql);
		
		
		//yoes 20170112 move this here
		doCompanyFullLog($sess_userid, $this_id,  "deleted-".basename($_SERVER["SCRIPT_FILENAME"]));	
		
		
		$the_sql = "delete from company where CID = '$this_id'";
		mysql_query($the_sql);
		
		
		
		header("location: org_list.php");
	
	}else{
	
		//do validation
		$the_code = doCleanInput($_POST["CompanyCode"]);
		$the_branch = doCleanInput($_POST["BranchCode"]);
	
		$existed_company_id = getFirstItem("select (CID) from company
							where 
							CompanyCode = '".$the_code."'
							and BranchCode = '".$the_branch."'"
							);
							
		//echo $count_company;exit();							
		if(strlen($existed_company_id) > 0){
						
			if($existed_company_id != $this_id){
				header("location: organization.php?id=$this_id&new_id=$the_code&new_id_link=$existed_company_id&branch=$the_branch" );
				exit();
			}
		}	
	
	
		
		//yoes 20170119
		if($_POST[District_init]){			
			$_POST[District] = $_POST[District_init];
		}
		
		if($_POST[Subdistrict_init]){			
			$_POST[Subdistrict] = $_POST[Subdistrict_init];
		}
		
		
		//print_r($_POST); exit();
		
		//specify all posts fields
		
		$input_fields = array(
							
							'CompanyNameThai'
							,'CompanyNameEng'
							/*,'Address1'
							
							,'Moo'
							,'Soi'
							,'Road'
							,'Subdistrict'
							,'District'
							
							,'Province'
							,'Zip'*/
							
							,'Telephone'
							,'email'
							,'TaxID'
							
							,'CompanyTypeCode'
							,'BusinessTypeCode'
							
							,'org_website'
							
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
						
		if($sess_accesslevel != 4){
			//non-company user can push these
			array_push($input_fields,'CompanyCode','BranchCode');
		}
		
// bank 20221107 add temp edit address company
		$current_time = date("Y-m-d H:i:s");	
		if($sess_meta == "0"){
			
			$sess_meta = 1;
		}

		$edit_company_sql = "
									replace into
									company_edit_request
									(
										CID
										, Employees
										, CompanyCode
										, BranchCode
										, CompanyNameThai
										, CompanyNameEng
										, Address1
										, Moo
										, Soi
										, Road
										, Subdistrict
										, District
										, Province
										, Zip
										, CompanyTypeCode
										, edit_date
										, year
										, request_by_user_id
										, Province_sent
									)values(
										'".$_POST[CID]."',
										'".$_POST[Employees]."',
										'".$_POST[CompanyCode]."',
										'".$_POST[BranchCode]."',
										'".$_POST[CompanyNameThai]."',
										'".$_POST[CompanyNameEng]."',
										'".$_POST[Address1]."',
										'".$_POST[Moo]."',
										'".$_POST[Soi]."',
										'".$_POST[Road]."',
										'".$_POST[Subdistrict]."',
										'".$_POST[District]."',
										'".$_POST[Province]."',
										'".$_POST[Zip]."',
										'".$_POST[CompanyTypeCode]."',
										'$current_time',
										'".$_POST[the_year]."',
										'$sess_userid',
										'$sess_meta'
									)
										
									";
						
						mysql_query($edit_company_sql);
		
		//yoes 20160511
		//also add meta data (if any)
		// 07 -> school
		//if($_POST[CompanyTypeCode] == "07"){
		//if($_POST[is_school]){
			
			if(!$_POST[is_school]){
				$_POST[is_school] = 0;
				
				//yoes 20160907 -- if not school then delete all school related meta for this company
				$sql = "delete from company_meta where meta_for like 'school_%' and meta_cid = '$this_id'";
				mysql_query($sql) or die(mysql_error());
				
				$sql = "update company_meta set meta_value = 0 where meta_for = 'is_school' and meta_cid = '$this_id'";
				mysql_query($sql) or die(mysql_error());
				//echo $sql; exit();
			}
		
			if($_POST[is_school]){
			
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
		
			} //end if school
			//DANG Additional meta
			$meta_fields = array(					
				'commercial_code'				
			);				
			for($metai=0;$metai<count($meta_fields);$metai++){					
				$meta_value = doCleanInput($_POST[$meta_fields[$metai]]);					
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
				
			
		//}
		
		
		
		
		
		//fields not from $_post	
		$special_fields = array("LastModifiedDateTime","LastModifiedBy","Employees","last_modified_lid_year"); //yoes 20160125 ---> more details
		$special_values = array("NOW()","'$sess_userid'","'".deleteCommas($_POST['Employees'])."'", "'".($_POST[the_year]*1)."'");
		
		//conditions
		$condition_sql = "where CID = '".$this_id."' limit 1";
				
		
		//yoes 20150617 - also check if this is actually a "modified" data (or someone just press save without modifying anythin...)
		$condition_sql_check_existed = " and CID = '".$this_id."'";
		$special_fields_check_existed = array("Employees");
		$special_values_check_existed = array("'".deleteCommas($_POST['Employees'])."'");
		$the_sql = generateCheckRowExistedSQL($_POST,$table_name,$input_fields,$special_fields_check_existed,$special_values_check_existed, $condition_sql_check_existed);
		$row_existed = getFirstItem($the_sql);
		
		
		
		//add vars to db
		//yoes 20150617 -> only add history if this is an actual "edit"
		if(!$row_existed){
			
			$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, $condition_sql);
			
			//echo $the_sql; exit();
			mysql_query($the_sql);
			
			//yoes 20160201
			$district_to_clean_cid = $this_id;
			include "scrp_update_district_cleaned_to_cid.php";
			
			//then add this to history
			//$history_sql = "insert into modify_history values('$sess_userid','$this_id',now(),0)";
			//mysql_query($history_sql);
			
			doAddModifyHistory($sess_userid,$this_id,0);
			
		}
		
		
		if($sess_accesslevel == 4){			
			//alert email to admin if company user update his info
			
			$formatted_name = formatCompanyName($_POST["CompanyNameThai"],$_POST["CompanyTypeCode"]);
			$company_province = getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
			
			$headers .= "Content-type: text/plain;charset=utf-8" . "\r\n";		
			
			mail("yoes@uklahouse.com,jazzining@gmail.com"
				, "มีการปรับปรุงข้อมูลกิจการของ $formatted_name : $company_province"
				, "มีการปรับปรุงข้อมูลกิจการของ $formatted_name : $company_province \n\nกดที่นี่เพื่อดูรายละเอียด (ต้อง login ก่อน): http://thaidrivingspirit.com/organization.php?id=$this_id"
				, $headers);
		
		}


//---> handle attached files
	$file_fields = array(
						"company_address_info_file"
						);
						
	
						
	for($i = 0; $i < count($file_fields); $i++){
	
		$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
		
		if($hire_docfile_size > 0){
			
			$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
			$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
			$hire_docfile_exploded = explode(".", $hire_docfile_name);
			$hire_docfile_file_name = $hire_docfile_exploded[0]; 
			$hire_docfile_extension = $hire_docfile_exploded[1]; 
			
			//new file name
			$new_hire_docfile_name = date("dmyhis").rand(00,99)."_".$hire_docfile_file_name; //extension
			$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
			//echo $hire_docfile_path;exit();
			//
			if(move_uploaded_file($_FILES[$file_fields[$i]]['tmp_name'], $hire_docfile_path)){	
				//move upload file finished
				//array_push($special_fields,$file_fields[$i]);
				//array_push($special_values,"'".$new_hire_docfile_name.".".$hire_docfile_extension."'");
				$sql = "insert into files(
						file_name
						, file_for
						, file_type)
					values(
						'".$new_hire_docfile_name.".".$hire_docfile_extension."'
						,'".$this_id."'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
				$new_record_id = mysql_insert_id();
				
				
				$sql_update_file_id = "update company_edit_request
										set file_id = '".$new_record_id."'
										where CID = '".$this_id."'
										and file_id = 0
								
								";
			
				mysql_query($sql_update_file_id);
				
				
			}
			
			
			//yoes 20150617
			//--- if have new attach file -> just assume this is a "newly edited"
			$row_existed = 0;
						
		}else{
			
		
			
			//no new file uploaded, retain old file name in db
			//array_push($special_fields,$file_fields[$i]);
			//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where LID = '".doCleanInput($_POST["LID"])."'")."'");
		
		}
	
	}
	///
	//end handle attached file
    //////
		
		header("location: organization.php?id=$this_id&updated=updated");
		
	}

?>