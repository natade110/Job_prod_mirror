<?php

	include "db_connect.php";
	
	//parameters
	$the_lid = doCleanInput($_POST["the_lid"]);
	$the_cid = doCleanInput($_POST["the_cid"]);
	$the_year = doCleanInput($_POST["the_year"]);
	
	
	
	if(!$the_lid || !$the_cid || !$the_year){
		
		echo "error - no input"; exit();
	}

	//echo "<br>".$the_lid;
	//echo "<br>".$the_cid;
	//echo "<br>".$the_year;
	
	//exit();

	//yoes 20211014 -- see submitted status
 	$submitted_company_lawful = getFirstItem("
											select 
												lawful_submitted
											from
												lawfulness_company
											where
												CID = '" . $the_cid . "'
												and
												Year = '".$the_year."'
											");


	  $resubmit_status = getLawfulnessMeta($the_lid, "es-resubmit");
	

	//yoes 20151021
	//also transfer branch data from temp "company table" to real table
	//but only do so if this is "current" fiscal year
	if(date("m") >= 9){
		$the_end_year = date("Y")+1; //new year at month 9
	}else{
		$the_end_year = date("Y");
	}
	
	
	//yoes 20211212
	$lawful_row = getFirstRow("select * from lawfulness_company WHERE cid='".$the_cid."' and year='".$the_year."'");
	
	  $sql = "
		
		insert into
			ejob_remarks(
			
				ejr_datetime
				, ejr_remarks
				, ejr_from
				, ejr_to
				, ejr_ejob_lid
				
				, ejr_lid
				, ejr_created_date
			)
			values(
			
				'".$lawful_row[lawful_submitted_on]."'			
				, (
				
					select
						lawful_remarks
					from
						lawfulness_company
					where
						cid='".$the_cid."' 
						and year='".$the_year."'			
				)
				, '".$the_cid."'
				, '".$sess_userid."'			 
				, '".$the_lid."'
				
				, 0
				, now()
				
			)
	  
	  ";
	  
	  mysql_query($sql);
	
	//yoes 20211212
	$reject_remark = doCleanInput($_POST["reject_remark"]);
	$sql = "
	
		insert into
			ejob_remarks(
			
				ejr_datetime
				, ejr_remarks
				, ejr_from
				, ejr_to
				, ejr_ejob_lid
				
				, ejr_lid
				, ejr_created_date
			)
			values(
			
				now()		
				, '".$reject_remark."'
				, '".$sess_userid."'
				, '".$the_cid."' 
				, '".$the_lid."'
				
				, 0
				, now()
				
			)
	  
	  ";
	  
	  mysql_query($sql);
	  
	  
	  
	  doLawfulnessCompanyFullLog($sess_userid, $the_lid, "scrp_transfer_data.php");
	
	//Transfer data from _company to real table

	////
	//1. start with number of employees 
	////

	if($submitted_company_lawful == 3){
		
		//do nothing for FIX
		
	}else{
	
		$sql = "


					UPDATE 
						lawfulness a 
					JOIN 				
						lawfulness_company b 

					ON 
						a.LID = b.LID 

					SET 
						a.Employees = b.Employees
						, a.Hire_NumOfEmp = b.Hire_NumOfEmp


					where	
						a.LID = '$the_lid'

				";

		mysql_query($sql);
		
		//yoes - NEW as of 20151021
		//also update lawfulness.Employees equals to value got from "All branches" instead

		//the_sum_employees
		$sql = "


					UPDATE 
						lawfulness a 

					SET 
						a.Employees = '".$_POST["the_sum_employees"]."'				

					where	
						a.LID = '$the_lid'

				";

		//echo $sql; exit();

		mysql_query($sql);
		
		if($the_year == $the_end_year){
	
			//try get branch info

			$result_set_sql = "
						select 
							* 
						from 
							company_employees_company 
						where 						
							lawful_year = '$the_year'

							and

							cid in (

								select 
									cid
								from							
									company 
								where 
									CompanyCode = '". getFirstItem("select CompanyCode from company where cid = '$the_cid'")."' 


							)

						";	

			//echo $result_set_sql; exit();

			$result_set = mysql_query($result_set_sql);

			while ($result = mysql_fetch_array($result_set)) {


				$sql = "
						update 
							company 
						set 
							employees = '".$result["employees"]."' 
						where 
							cid = '".$result["cid"]."' 

						";			
				mysql_query($sql);

				$sql = "
						update ignore
							company_employees_company 
						set 
							lawful_year = lawful_year +1000 
						where 
							cid = '".$result["cid"]."' 
							and
							lawful_year = '$the_year'
						";			
				mysql_query($sql);

			}	





			//also transfer company_company to real company

			$result_set_sql = "

				select 
					*
				from							
					company_company
				where 
					CompanyCode = '". getFirstItem("select CompanyCode from company where cid = '$the_cid'")."' 


			";


			$result_set = mysql_query($result_set_sql);

			while ($result = mysql_fetch_array($result_set)) {


				$_POST['CompanyCode'] = $result['CompanyCode'];

				//$_POST['CompanyCode'] = $result[];

				$_POST['BranchCode'] = $result[BranchCode];


				$_POST['CompanyNameThai'] = $result[CompanyNameThai];
				$_POST['CompanyNameEng'] = $result[CompanyNameEng];
				$_POST['Address1'] = $result[Address1];
				$_POST['Moo'] = $result[Moo];
				$_POST['Soi'] = $result[Soi];


				$_POST['Road'] = $result[Road];
				$_POST['Subdistrict'] = $result[Subdistrict];
				$_POST['District'] = $result[District];
				$_POST['Province'] = $result[Province];
				$_POST['Zip'] = $result[Zip];

				$_POST['CompanyTypeCode'] = getFirstItem("select CompanyTypeCode from company where cid = '$the_cid'");
				$_POST['BusinessTypeCode'] = getFirstItem("select BusinessTypeCode from company where cid = '$the_cid'");
				$_POST['Status'] = 1;

				$_POST['Employees'] = $result['Employees'];

				$table_name = "company";

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


									,'CompanyTypeCode'
									,'BranchCode'

									,'BusinessTypeCode'

									,'Status'

									);

				//fields not from $_post	
				$special_fields = array("LastModifiedDateTime","LastModifiedBy","Employees", "CreatedDateTime","CreatedBy", "is_active_branch");
				$special_values = array("NOW()","'$sess_userid'","'".deleteCommas($_POST['Employees'])."'","NOW()","'$sess_userid'","1");

				//add vars to db
				$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, "insert ignore");

				//echo "<br>".$the_sql;


				mysql_query($the_sql);

			}	//end while


			//yoes 20151122 --> delete data from company after the fact
			$delete_the_sql = "

				delete
				from							
					company_company
				where 
					CompanyCode = '". getFirstItem("select CompanyCode from company where cid = '$the_cid'")."' 


			";


			mysql_query($delete_the_sql);


		}//end if for if($the_year == $the_end_year){
	

	}
	
	
	
	
	
	
	
	
	
	
	//
	//echo $the_year . $the_end_year; exit();
	
	
	//only do this on the latest year
	
	//exit();
	
	
	/////////
	/////// number of employees moved - delete it
	/////////	
	//mysql_query("delete from lawfulness_company where LID = '$the_lid'");
	
	
	
	////
	//
	//	2# then do lawful employees
	//
	////
	
	
	//yoes 20210629 -- do insert first for le_id
	
	$sql = "
	
			insert into
				lawful_employees(
				
				 	le_name
					,le_gender
					,le_age
					,le_code
					,le_disable_desc
					,le_start_date
					,le_end_date
					,le_wage
					,le_position
					,le_year
					,le_cid
					,le_wage_unit	
					
					,le_from_oracle			
					
					,le_education
					
					
					, le_created_by
				
				)
			select
				le_name
				,le_gender
				,le_age
				,le_code
				,le_disable_desc
				,le_start_date
				,le_end_date
				,le_wage
				,le_position
				,le_year
				,le_cid
				,le_wage_unit	
				
				,le_from_oracle
				
				,le_education
				
				, le_id
			from 				
				lawful_employees_company
			where	
				le_cid = '$the_cid'
				and
				le_year = '$the_year'
				and
				job_leid = 0
	
			";
	
	//echo $sql; exit();
	mysql_query($sql);
	
	
	
	//yoes 20210629 -- then do update for existing le_id
	$sql = "
	
		update
			lawful_employees le
			
			
				join
					lawful_employees_company lec
					on
					le.le_id = lec.job_leid
					
		set
			
			le.le_end_date = lec.le_end_date		
			, le.le_created_by = lec.le_id
			
		where
			le.le_cid = '$the_cid'
			and
			le.le_year = '$the_year' 
						
	
	";

	//echo $sql; exit();
	
	mysql_query($sql);
	
	//yoes 20210629
	//also do a link between job and ejob info
	//yoes 20210629
	//clean the meta
	// --- no longer need this
	
	
	
	//yoes 20190212 --> for year 2018++ also add lawful_employees_meta
	//
	if($the_year >= 2018){
		
		
		//yoes 20210629
		//delete old relation first
		
		$sql = "
			
			delete from 
				lawful_employees_meta
			where
				meta_for = 'child_of'
				and
				meta_leid in (
					
					select
						le_id
					from
						lawful_employees
					where
						le_cid = '$the_cid'
						AND 
						le_year = '$the_year'
				)
		
		";
		
		mysql_query($sql);
		
		
		$sql = "
		
			insert into
			
				lawful_employees_meta(
				
					meta_leid
					, meta_for
					, meta_value
				
				)
			
			select
				
				c.le_id
				, 'child_of'
				, cc.le_id
				
			from
				lawful_employees_meta a
					
					
					JOIN lawful_employees_company b
						 ON a.meta_leid = b.le_id
					JOIN lawful_employees_company bb
						 ON a.meta_value = bb.le_id

					JOIN lawful_employees c
						 ON c.le_created_by = b.le_id
					JOIN lawful_employees cc
						 ON cc.le_created_by = bb.le_id
					
								
			where
				a.meta_for = 'child_of-es'
				
				and
				b.le_cid = '$the_cid'
				AND 
				b.le_year = '$the_year'
		
		
		";
		
		//echo $sql; exit();
		
		mysql_query($sql);
		
		
		
	}
	
	
	
	
	
	//yoes 20160501 --- also send files
	
	//yoes 20210629 
	//but first -> delete all 'ejob' related files
	
	$sql = "
	
		delete from
				files
			where
				file_name like 'ejob/%'				
				and
				file_type in (
							'docfile_33_1'
							, 'docfile_33_2'
							, 'docfile_33_71'
							)
							
				and
				file_for in (
				
					select
						le_id
					from
						lawful_employees
					where
						le_cid = '$the_cid'
						and
						le_year = '$the_year'
						and
						job_leid = 0
				
				)
			
		
	";
	
	//yoes 20211014 --> no longer need to delete the files - as we'll only do "Merge"
	//mysql_query($sql);
	
	
	//echo $sql; exit();
	
	
	$sql = "
	
	
			insert into files(
			
				file_name
				, file_for
				, file_type
			
			)
			select 
				
				concat('ejob/',a.file_name)				
				, c.le_id
				, a.file_type
			from
				files a
					join
						lawful_employees_company b 
					on
						a.file_for = b.le_id
						and
						(
							a.file_type = 'docfile_33_1'
							or 
							a.file_type = 'docfile_33_2'
							or 
							a.file_type = 'docfile_33_71'
						)
						and
						b.le_cid = '$the_cid'
						and
						b.le_year = '$the_year'						
					
					join
						lawful_employees c
					on
						b.le_code = c.le_code
						and
						b.le_cid = c.le_cid
						and
						b.le_year = c.le_year
						
				where
					b.job_leid = 0
						
			
			
	
	";
	
	//echo $sql; exit();
	mysql_query($sql);
	
	//exit();
	
	//yoes 20160501 then transfer the rest of 33 files
	
	
	$sql = "
	
		delete from
				files
			where
				file_name like 'ejob/%'				
				and
				file_type in (
							'company_34_docfile_1_adm'
							, 'company_33_docfile_3_adm'
							, 'company_33_docfile_4_adm'
							, 'company_33_docfile_5_adm'
							, 'company_33_docfile_6_adm'
							, 'company_33_docfile_7_adm'
							, 'company_docfile_adm'
							)
							
				and
				file_for in (
				
					select
						lid
					from
						lawfulness_company
					where
						cid = '$the_cid'
						and
						year = '$the_year'
				
				)
			
		
	";
	
	mysql_query($sql);
	
	$sql = "
		
			insert into files(
			
				file_name
				, file_for
				, file_type
			
			)
			select 
				
				concat('ejob/',a.file_name)				
				, c.lid
				, concat(a.file_type, '_adm')
			from
				files a
					join
						lawfulness_company b 
					on
						a.file_for = b.lid
						and
						(
							a.file_type = 'company_33_docfile_3'
							or 
							a.file_type = 'company_33_docfile_4'
							or 
							a.file_type = 'company_33_docfile_5'
							or 
							a.file_type = 'company_33_docfile_6'
							or 
							a.file_type = 'company_33_docfile_7'
							or
							a.file_type = 'company_docfile'
						)
						and
						b.cid = '$the_cid'
						and
						b.year = '$the_year'
					
					join
						lawfulness_company c
					on
						b.lid = c.lid
						and
						b.cid = c.cid
						and
						b.year = c.year
	
	
	";
	
	
	//echo $sql; exit();
	mysql_query($sql);
	
	/// delete the transferred info
	//mysql_query("delete from lawful_employees_company where le_cid = '$the_cid' and le_year = '$the_year'");
	
	
	
	
	//yoes 20160501 -- then attach file for m34
	$sql = "
		
			insert into files(
			
				file_name
				, file_for
				, file_type
			
			)
			select 
				
				concat('ejob/',a.file_name)				
				, c.lid
				, concat(a.file_type, '_adm')
			from
				files a
					join
						lawfulness_company b 
					on
						a.file_for = b.lid
						and
						(
							a.file_type = 'company_34_docfile_1'							
						)
						and
						b.cid = '$the_cid'
						and
						b.year = '$the_year'
					
					join
						lawfulness_company c
					on
						b.lid = c.lid
						and
						b.cid = c.cid
						and
						b.year = c.year
	
	
	";
	
	
	//echo $sql; exit();
	mysql_query($sql);
	//exit();
	
	////
	//
	//	3# then do CURATOR
	//
	////
	
	
	
	
	//echo $sql; exit();
	//mysql_query($sql);
	
	//First -> do parent curator...
	//yoes 20211116 -- only do insert for new es row
	$sql = "select * from curator_company where curator_lid = '$the_lid' and curator_parent = '0' and job_curator_id = 0";
	
	//echo $sql; exit();
	
	$sub_result = mysql_query($sql);
	
	while ($sub_row = mysql_fetch_array($sub_result)) {			

		
		//yoes 20210629 -- do update curator from existing link
		
		$sql = "
			
			update
				curator cu
					join
						curator_meta cum
							on
							cu.curator_id = cum.meta_curator_id
							and
							cum.meta_for = 'ejob_curator_id'
				
					join
						curator_company cuc
						on
						cuc.curator_id = cum.meta_value
						
					left join
						curator_meta cum2
						on
						cuc.curator_parent = cum2.meta_value
						and
						cum2.meta_for = 'ejob_curator_id'
			set
				cu.curator_name = cuc.curator_name
				, cu.curator_idcard = cuc.curator_idcard
				, cu.curator_gender = cuc.curator_gender
				, cu.curator_age = cuc.curator_age				
				, cu.curator_parent = coalesce(cum2.meta_curator_id,0)
				, cu.curator_contract_number = cuc.curator_contract_number
				, cu.curator_event = cuc.curator_event
				, cu.curator_event_desc = cuc.curator_event_desc
				, cu.curator_disable_desc = cuc.curator_disable_desc
				, cu.curator_is_disable = cuc.curator_is_disable
				, cu.curator_start_date = cuc.curator_start_date
				, cu.curator_end_date = cuc.curator_end_date
				, cu.curator_value = cuc.curator_value								

			where
				cu.curator_lid in (
					
					select
						lid
					from
						lawfulness_company
					where
						cid = '$the_cid'
						AND 
						year = '$the_year'
						
				
				)
		
		";
		
		
		//yoes 2021116
		//no longer do full update
		//mysql_query($sql);
		
		
		//special for curator -> update the parent
		
		

		//$total_sub++;
		//add parent
		$sql = "
	
				insert into
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
						
						, curator_created_by
					
					
					)
					select
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
						
						
						, curator_id
					from 
						curator_company
					where
						curator_id = '".$sub_row["curator_id"]."'
						
						and
						
						
						curator_id not in (
						
							select
								curator_created_by
							from
								curator
							where
								curator_lid in (
									
									select
										lid
									from
										lawfulness_company
									where
										cid = '$the_cid'
										AND 
										year = '$the_year'
										
								
								)
						)
						
						
						
	
				";
		
		//echo "<br>". $sql; exit();
		
		mysql_query($sql);
		
		//last inserted ID to "real" data
		$last_id = mysql_insert_id();		
		
		
	
	
	
		//also send file for this curator
		
		//after add parent, see if have child
		$child_sql = "select * from curator_company where curator_parent = '".$sub_row["curator_id"]."'";
		
		$child_result = mysql_query($child_sql);
		
		while ($child_row = mysql_fetch_array($child_result)) {		
			
			//if have any child....
			$sql = "
	
				insert into
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,curator_parent
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
						
						, curator_created_by
					
					
					)
					select
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						,'$last_id'
						,curator_event
						,curator_event_desc
						,curator_disable_desc
						,curator_is_disable
						,curator_start_date
						,curator_end_date
						,curator_value
						,curator_from_oracle 
						
						, curator_id
					from 
						curator_company
					where
						curator_id = '".$child_row["curator_id"]."'
						
						and
						
						
						curator_id not in (
						
							select
								curator_created_by
							from
								curator
							where
								curator_lid in (
									
									select
										lid
									from
										lawfulness_company
									where
										cid = '$the_cid'
										AND 
										year = '$the_year'
										
								
								)
						)
						
	
				";
			
			//add child..	
			mysql_query($sql);
			
		}
		
	}
	
	
	//yoes 20211116
	//update old 35 links
	$sql = "
	
		update
			curator cu
			
			
				join
					curator_company cuc
					on
					cu.curator_id = cuc.job_curator_id
					
		set
			
			cu.curator_end_date = cuc.curator_end_date		
			, cu.curator_created_by = cuc.curator_id
			
		where
			cuc.curator_lid = (
			
				select
					lid
				from
					lawfulness_company
				where
					cid = '$the_cid'
					AND 
					year = '$the_year'
				limit
					0,1
			
			)
						
	
	";

	//echo $sql; exit();
	
	mysql_query($sql);
	
	
	//yoes 20210626 -- add link for curator	
	$sql = "
		
		replace into
			curator_meta(
				
					meta_curator_id
					, meta_for
					, meta_value
				
				)
			select
				
				c.curator_id
				, 'ejob_curator_id'
				, b.curator_id
				
			from
				
					curator_company b	
						
						
						join
							curator c
							
							on
							c.curator_created_by = b.curator_id
					
								
			where
				
				b.curator_lid = (
				
				
					select
						lid
					from
						lawfulness_company
					where
						cid = '$the_cid'
						AND 
						year = '$the_year'
				
				)
	
	";
	
	//mysql_query($sql);
	
	//yoes 20190212 --> for year 2018++ also add curator_meta
	//
	if($the_year >= 2018){
		
		
		//yoes 20210629 -- clear old meta relation first
		
		$sql = "
			
			delete from 
				curator_meta
			where
				meta_for = 'child_of'
				and
				meta_curator_id in (
					
					select
						curator_id
					from
						curator
					where
						curator_lid in (
						
							select
								lid
							from
								lawfulness_company
							where
								cid = '$the_cid'
								AND 
								year = '$the_year'
						
						
						)
				)
		
		";
		
		mysql_query($sql);
		
		
		
		
		$sql = "
		
			insert into
			
				curator_meta(
				
					meta_curator_id
					, meta_for
					, meta_value
				
				)
			
			select
				
				c.curator_id
				, 'child_of'
				, cc.curator_id
				
			from
				curator_meta a
					join
						curator_company b						
						on
						a.meta_curator_id = b.curator_id
						
					join
						curator c						
						on
						c.curator_created_by = b.curator_id
						
					join
						curator_company bb						
						on
						a.meta_value = bb.curator_id
						
					join
						curator cc						
						on
						cc.curator_created_by = bb.curator_id
								
			where
				a.meta_for = 'child_of-es'
				
				and
				b.curator_lid = (
				
				
					select
						lid
					from
						lawfulness_company
					where
						cid = '$the_cid'
						AND 
						year = '$the_year'
				
				)
				
		
		
		";
		
		mysql_query($sql);
		
		
		
	}
	
	
	/// delete the transferred info
	//mysql_query("delete from curator_company where curator_lid = '$the_lid'");
	
	
	//yoes 20210629 -- do delete curator doc file
	$sql = "
	
		delete from
				files
			where
				file_name like 'ejob/%'				
				and
				file_type in (
							'curator_docfile'
							, 'curator_docfile_2'
							, 'curator_docfile_3'
							)
							
				and
				file_for in (
				
					select
						curator_id
					from
						curator
					where
						curator_lid in (
						
							select
								lid
							from
								lawfulness
							where
								cid = '$the_cid'
								AND 
								year = '$the_year'
						
						)
				
				)
			
		
	";
	
	mysql_query($sql);
	
	//yoes 20160501
	$sql = "
	
			insert into files(
			
				file_name
				, file_for
				, file_type
			
			)
			select 
				
				concat('ejob/',a.file_name)				
				, c.curator_id
				, a.file_type
			from
				files a
					join
						curator_company b 
					on
						a.file_for = b.curator_id
						and
						(
							a.file_type in ('curator_docfile', 'curator_docfile_2','curator_docfile_3')
						)
						and
						b.curator_lid = '$the_lid'
					
					join
						curator c
					on
						b.curator_lid = c.curator_lid
						and
						b.curator_idcard = c.curator_idcard
						
		
	
	";
	
	
	mysql_query($sql);
	
	
	//yoes 20231008
	//--- ขา แก้ ejob ครั้งที่ 2+
	/*
	D:\Dropbox\www\hire_projects\ref\fix_ejob_m35.txt
	
	ยื่นครั้งแรก  -> 35 ยังอยู่ ejob

	รับครั้งแรก -> 35 ยังอยู่ ejob

	ยื่นแก้ไขข้อมูล ejob -> ไฟล์ ejob ยังอยู่

	ส่งเรื่องแก้ไข ejob -> ไฟล์ ejob ยังอยู่

	รับเรื่องแก้ไข ejob -> ไฟล์หายไปแล้ว*** // state is lawful_submitted = 3 / scrp_transfer_data.php
	
	เกิดจากว่า การขอ fix ครั้งที่ 2+ จะใช้ link เก่ากับ link ใหม่ผสมกัน...
	quickfix -> กรณีไม่มีไฟล์ ให้ไปเอาไฟล์จาก link เก่ามาใช้...
	
	*/
	
	$file_35_existed_count = getFirstItem("	
		select 
			
			count(*)
		from
			files a
				join
					curator b 
				on
					a.file_for = b.curator_id
					and
					(
						a.file_type in ('curator_docfile', 'curator_docfile_2','curator_docfile_3')
					)
					and
					b.curator_lid = '$the_lid'
				
				join
					curator c
				on
					b.curator_lid = c.curator_lid
					and
					b.curator_idcard = c.curator_idcard
	
	");
	
	if(!$file_35_existed_count || $submitted_company_lawful == 3){
		
		
		//yoes 20231008 --- try get data from job_curator_id instead
		$sql = "
		
				
			insert into files(
						
				file_name
				, file_for
				, file_type

			)
			select 
				
				concat('ejob/',f.file_name)				
				, cur.curator_id
				, f.file_type
			from 
				files  f
					join
						curator_company_full_log cur_fl
						on
						f.file_for = cur_fl.curator_id
						and
						cur_fl.`curator_lid` = '$the_lid'
						and
						file_type in ('curator_docfile_2','curator_docfile_3')
						
					join
						curator cur
						on
						cur.curator_idcard = cur_fl.curator_idcard
						and
						cur.`curator_lid` = '$the_lid'
										
			
		
		";
		
		
		mysql_query($sql);
		
		
		
	}
	
	//echo $sql; exit();
	
	//exit();
	
	
	//update flag so we know we've moved this company's data
	$sql = "update 
			lawfulness_company 
			set 
				lawful_submitted = 2
				, lawful_approved_on = now() 
				, lawful_approved_by = '$sess_userid'
				
				, lawful_remarks = ''
				
				where Year = '$the_year' and CID = '$the_cid'";
	mysql_query($sql);
	
	
	//yoes 20220606 -- also add generic log
	// *** see "Approved time" from log_date - ดูวันที่กดอนุมัติจาก field log_date
	$generic_sql = "
	
		insert into generic_log(
		
			log_type
			, log_date
			, log_meta
		
		)values(
		
			'lawful_submitted_user-submitted_on'
			, now()
			, '".$the_year.";".$the_cid.";".$sess_userid.";".$lawful_row[lawful_submitted_on]."'			
		
		)
	
	";
	
	mysql_query($generic_sql);
	
	

	//yoes 20211014 - also update the user meta
	//backup old company 33
		$sql = "

			replace into lawfulness_meta(

				meta_lid
				, meta_for
				, meta_value



			) values ( 

				'$the_lid'
				, 'es-resubmit'
				, '0'

			)


		";

		//echo $sql; exit();

		mysql_query($sql) or die(mysql_error());
	
	
	//yoes 20151123
	//also send mail to company's user
	
	$user_row = getFirstRow("
						
						select 
							user_email 
						from 
							users 
						where 
							user_meta = '$the_cid' 
							and 
							AccessLevel = 4 
							and user_enabled = 1
						limit
							0,1
						
						");
						
	$mail_address = $user_row[user_email];
	//
				
	$the_header = "ระบบรายงานผลการจ้างงานคนพิการ: ผู้ดูแลระบบได้รับข้อมูลการปฏิบัติตามกฏหมายแล้ว";

	$the_body = "<table><tr><td>เรียนคุณ ".doCleanInput($mail_address["FirstName"])." ".doCleanInput($mail_address["LastName"])."<br><br>";

	$the_body .= "ผู้ดูแลระบบได้รับข้อมูลการปฏิบัติตามกฏหมายของคุณแล้ว <br>";
	$the_body .= "หลังจากมีการตรวจสอบข้อมูลที่เกี่ยวข้องแล้ว จะมีการแจ้งสถานะการปฏิบัติตามกฏหมายไปทาง email นี้อีกครั้ง<br><br>";


	$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
	
	
	if ($server_ip == "203.146.215.187"){
		//ictmerlin.com use default mail
		mail($mail_address, $the_header, $the_body);
	}elseif ($server_ip == "127.0.0.1"){
	
		//donothin	
	
	}else{
		//use smtp
		doSendMail($mail_address, $the_header, $the_body);	
		//echo "mail sent $mail_address, $the_header, $the_body"; exit();
	}
	
	
	//that's that..........
	//do a redirect backkkkkkkk...........
	
	//yoes 20160208
	resetLawfulnessByLID($the_lid);
	
	
	
	//yoes 20180319
	//also mark document_requests as "done"
		
	$sql = "
	
		select
			docr_id
		from
			document_requests
		where
			docr_org_id = '$the_cid'
			and
			docr_year = '$the_year'
		order by
			docr_id desc
		limit 0,1
		
	
	";
	
	
	$the_docr_id = getFirstItem($sql);
	
	if($the_docr_id){
		
		$sql = "update document_requests set docr_status = 1 where docr_id = '$the_docr_id'";
		mysql_query($sql);
		
	}else{
		
		$sql = "
		
			insert into 
				document_requests(
					docr_org_id
					,docr_status					
					,docr_year
					,docr_last_updated
					,docr_date
				)values( 
					'$the_cid'
					,'1'
					,'$the_year'
					,NOW()					
					,NOW()
					
					)
		
		";
		mysql_query($sql);
		
	}
	
	
	header("location: organization.php?id=$the_cid&focus=lawful&year=".$the_year."&auto_post=1");

	

?>