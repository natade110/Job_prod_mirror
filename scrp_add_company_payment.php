<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	//try to add more company to this receipt
	$RID = $_POST["RID"]*1;
	
	//first get RID information
	
	$this_RID = $RID;					
	
	///----------------------------------------------
	//add payments to all selected company
	///----------------------------------------------
	//prepare "common vars" of every payment
	//FIRST, see how many checkboxes are checked
	$post_records = $_POST["total_records"]*1;
	
	
	$table_name = "payment";
	//$payment_method = $_POST["PaymentMethod"];

	$payment_row = getFirstRow("select * from payment where RID = '$RID' limit 0,1");
	$PaymentMethod = $payment_row["PaymentMethod"];
	$Amount = $payment_row["Amount"];
						
	$ref_no = $payment_row["RefNo"];
	$bank_id = $payment_row["bank_id"];
	$selected_year = getFirstItem("select ReceiptYear from receipt where RID = '$this_RID'");
	//echo "select ReceiptYear from receipt where RID = '$this_id'"; exit;
	//for each selected companies, do insert
	
	if($_POST["send_to_all"]){
	
		//echo "do send to all";exit();
		$condition = ($_POST["send_to_all"]);
		
		$get_org_sql = "SELECT *
						FROM company z
						
						
						LEFT JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
						LEFT JOIN provinces c ON z.province = c.province_id
						
						where
							1=1														
							$condition
						order by CompanyNameThai asc
						";
		//echo $get_org_sql;
		$org_result = mysql_query(stripslashes($get_org_sql));
		
		while ($post_row = mysql_fetch_array($org_result)) {
		
			$selected_company = $post_row["CID"];
			
			$lawful_id = getFirstItem("select LID from lawfulness 
											where CID = '$selected_company'
											and Year = '$selected_year'
											limit 0,1
											");
											
			if(strlen($lawful_id) > 0){
				//have lawful, update payment status
				$lawful_sql = "update lawfulness set
								pay_status = '1'
								,LawfulStatus = '1'
								where
								Year = '$selected_year'
								and
								CID = '$selected_company'
								limit 1";
				//echo $lawful_sql; exit();
				mysql_query($lawful_sql);
				
				mysql_query("update company set LawfulFlag = '1' where cid = '$selected_company' limit 1");
			}else{
				//generate new lawfulness row for this company
				$lawful_sql = "insert into lawfulness(
									Year
									,CID
									,pay_status
									,LawfulStatus
									
								)
								values(
									'$selected_year'
									,'$selected_company'
									,'1'
									,'1'
									
								)";
				mysql_query($lawful_sql);
				$lawful_id = mysql_insert_id();
				mysql_query("update company set LawfulFlag = '1' where cid = '$selected_company' limit 1");
			}
			
			$special_fields = array("PaymentDate"
									,"RefNo"
									,"bank_id"
									,"LID"
									,"RID"	
									,'PaymentMethod'
									,'Amount'			
									);
			$special_values = array("'$the_date'"
								,"'$ref_no'"
								,"'$bank_id'"
								,"'$lawful_id'"
								,"'$this_RID'"
								,"'$PaymentMethod'"
								,"'$Amount'"
								);	
			
			
			
			$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
			
			//insert each "main" letters to db
			//echo $the_sql;exit();
			mysql_query($the_sql);
		
		}
		
	}else{
	
		for($i=1 ; $i<=$post_records ; $i++){
			
			if($_POST["chk_$i"]){
				
				//echo "...";
				
				$selected_company = $_POST["chk_$i"];
				
				//check if lawfulness existed for this company and year...
				$lawful_id = getFirstItem("select LID from lawfulness 
												where CID = '$selected_company'
												and Year = '$selected_year'
												limit 0,1
												");
												
				if(strlen($lawful_id) > 0){
					//have lawful, update payment status
					$lawful_sql = "update lawfulness set
									pay_status = '1'
									,LawfulStatus = '1'
									where
									Year = '$selected_year'
									and
									CID = '$selected_company'
									limit 1";
					//echo $lawful_sql; exit();
					mysql_query($lawful_sql);
					mysql_query("update company set LawfulFlag = '1' where cid = '$selected_company' limit 1");
				}else{
					//generate new lawfulness row for this company
					$lawful_sql = "insert into lawfulness(
										Year
										,CID
										,pay_status
										,LawfulStatus
										
									)
									values(
										'$selected_year'
										,'$selected_company'
										,'1'
										,'1'
										
									)";
					mysql_query($lawful_sql);
					$lawful_id = mysql_insert_id();
					mysql_query("update company set LawfulFlag = '1' where cid = '$selected_company' limit 1");
				}
				
				//see if this cid id already exists in this receipt...
				//
				$count_existed_sql = "select 
										count(*)
									from 
										receipt
										, payment
										, company
										, lawfulness
									where
										receipt.RID = payment.RID
										and payment.LID = lawfulness.LID
										and lawfulness.CID = company.CID
										and ReceiptYear = '$selected_year'
										and company.CID = '$selected_company'
										and receipt.RID = '$this_RID'
									";
				
				//echo $count_existed_sql; exit();
				$count_existed = getFirstItem($count_existed_sql);
				
				
				$special_fields = array("PaymentDate"
										,"RefNo"
										,"bank_id"
										,"LID"
										,"RID"	
										,'PaymentMethod'
										,'Amount'			
										);
				$special_values = array("'$the_date'"
									,"'$ref_no'"
									,"'$bank_id'"
									,"'$lawful_id'"
									,"'$this_RID'"
									,"'$PaymentMethod'"
									,"'$Amount'"
									);	
				
				
				if($count_existed == 0){
					$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
				}
				
				//insert each "main" letters to db
				//echo $the_sql;exit();
				mysql_query($the_sql);
				//exit();
				
			}
		}
		
	}
	
	//then redirect to whatever page
	//header("location: org_list.php?mode=lettersl&letter_added=letter_added");
	header("location: view_payment.php?id=$RID&payment_added=payment_added");

?>