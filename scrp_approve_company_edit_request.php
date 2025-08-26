<?php

	include "db_connect.php";
	include "session_handler.php";
	
	//table name
	$table_name = "company";
	$this_id = doCleanInput($_POST["CID"]);
	
	
	//yoes 20160105
	//add log before doing anything
		doCompanyFullLog($sess_userid, $this_id, basename($_SERVER["SCRIPT_FILENAME"]));	
		
		doAddModifyHistory($sess_userid,$this_id,0);

		if($_POST["btn_approve_edit"]){
			
				$count_row =	getFirstRow("SELECT a.* 
													
												FROM 
												
													company_edit_request a 
													
													join provinces p 
														on a.Province = p.province_id
														
													join  company c
														on a.cid = c.cid
													where a.cid = '$this_id'");
				
				
				$update_sql = "update company 
								set Address1 = '".$count_row[Address1]."' ,
									Moo = '".$count_row[Moo]."' ,
									Soi = '".$count_row[Soi]."' ,
									Road = '".$count_row[Road]."' ,
									Subdistrict = '".$count_row[Subdistrict]."' ,
									District = '".$count_row[District]."' ,
									Province = '".$count_row[Province]."' ,
									Zip = '".$count_row[Zip]."'
								where CID = '".$this_id."'
								";
				
				mysql_query($update_sql);
			
				
				if($sess_accesslevel == 1 || $sess_accesslevel ==2){
					
					$sess_meta = 1;
					
				}elseif($sess_accesslevel == 3){
					
					$sess_meta = $sess_meta;
					
				}
				
						
				$current_time = date("Y-m-d H:i:s");
				
				/*$update_meta_sql = "update company_edit_request a
									set a.Province_accepted = '$sess_meta',
										a.approve_date = '$current_time'
								where a.CID = '".$this_id."'
								";
				
				mysql_query($update_meta_sql);*/
				
				$province_sent = getFirstItem("select Province_sent from company_edit_request where cid = '$this_id' and Province_accepted = 0");

				$approve_address_sql = "
											insert into
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
												, approve_date
												, year
												, Province_accepted
												, Province_sent
												, file_id
												, request_by_user_id
												, approve_by_user_id
											)values(
												'".$count_row[CID]."',
												'".$count_row[Employees]."',
												'".$count_row[CompanyCode]."',
												'".$count_row[BranchCode]."',
												'".$count_row[CompanyNameThai]."',
												'".$count_row[CompanyNameEng]."',
												'".$count_row[Address1]."',
												'".$count_row[Moo]."',
												'".$count_row[Soi]."',
												'".$count_row[Road]."',
												'".$count_row[Subdistrict]."',
												'".$count_row[District]."',
												'".$count_row[Province]."',
												'".$count_row[Zip]."',
												'".$count_row[CompanyTypeCode]."',
												'".$count_row[edit_date]."',
												'$current_time',
												'".$this_lawful_year."',
												'$sess_meta',
												'$province_sent',
												'".$count_row[file_id]."',
												'".$count_row[request_by_user_id]."',
												'$sess_userid'
											)
												
											";
								
				mysql_query($approve_address_sql);
				
			$delete_reject = "delete from company_edit_request
							  where cid = '$this_id'
							  and Province_accepted = 0";
			mysql_query($delete_reject);
		}
		
		elseif($_POST["address_do_reject"] || $_POST["btn_reject_edit"]){
			

			
			$delete_reject = "delete from company_edit_request
							  where cid = '$this_id'
							  and Province_accepted = 0";
			mysql_query($delete_reject);
		}
		
		header("location: organization.php?id=$this_id&updated=updated");
?>