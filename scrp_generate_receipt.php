<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	
	
	$selected_year = $_POST["ddl_year"];
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	$is_payback = $_POST["is_payback"];
	
	
	$table_name = "receipt_request";

	$input_fields = array(
						
						'PaymentMethod'
						
						,'ReceiptNote'
						
						,'is_payback'
						
						);

	
	if($sess_accesslevel != 4){
				
		//also, check if BookReceiptNo and ReceiptNo already existed
		$count_receipt = getFirstItem("select RID from receipt 
						where BookReceiptNo = '".doCleanInput($_POST["BookReceiptNo"])."'
						and ReceiptNo = '".doCleanInput($_POST["ReceiptNo"])."'");
		
		//yoes 20220719 - also count from receipt_request -- พม 0702/3564 -- 6 กรกฎาคม 2565
		$count_receipt += getFirstItem("select RID from receipt_request 
						where BookReceiptNo = '".doCleanInput($_POST["BookReceiptNo"])."'
						and ReceiptNo = '".doCleanInput($_POST["ReceiptNo"])."'");
		
		//echo $count_receipt; exit();

		if(strlen($count_receipt) == 0 || $count_receipt == 0){
			//if not existed then push these fields, else ignore it
			array_push($input_fields,'BookReceiptNo','ReceiptNo');
			
			//print_r($input_fields); exit();
			
		}else{
			$extra_query = "&duped_key=duped_key";
			
			if($_POST["back_to"] == "lawfulness_tab"){
				//come to this page via lawful tab
				header("location: org_list.php?search_id=".$_POST["chk_1"]."&mode=payment&for_year=$selected_year".$extra_query."&pay_id=".$count_receipt."&book_num=".$_POST["BookReceiptNo"]."&pay_num=".$_POST["ReceiptNo"]."" );
				exit();
			}else{
				//come to this page via org list
				header("location: org_list.php?mode=payment".$extra_query."&pay_id=".$count_receipt."&book_num=".$_POST["BookReceiptNo"]."&pay_num=".$_POST["ReceiptNo"]."" );
				exit();
			}
		}
	}

	$the_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];			
	
	
	$special_fields = array("ReceiptYear","ReceiptDate",'Amount');
	$special_values = array("'$selected_year'","'$the_date'","'".deleteCommas($_POST['Amount'])."'");							
	
						
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	//echo $the_sql; exit();	
	mysql_query($the_sql);
	$this_RID = mysql_insert_id();					
	
	///----------------------------------------------
	//add payments to all selected company
	///----------------------------------------------
	//prepare "common vars" of every payment
	//FIRST, see how many checkboxes are checked
	$post_records = $_POST["total_records"]*1;
	
	
	$table_name = "payment_request";
	$payment_method = $_POST["PaymentMethod"];
	
	
	$_POST[Amount] = deleteCommas($_POST[Amount]);
					
	$input_fields = array(
						
						'PaymentMethod'
						,'Amount'

						);
						
	$ref_no = $_POST[$payment_method."_ref_no"];
	if($payment_method == "Cheque"){
		$bank_id = $_POST["check_bank"];
	}
	
	
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
			//echo "1";
			
			$selected_company = $post_row["CID"];
				
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
			
			$special_fields = array(
			
									"PaymentDate"
									,"RefNo"
									,"bank_id"
									,"LID"
									,"RID"	
									
									, 'request_reason'
									, 'request_userid'
									, 'request_date'
									, 'request_status'
									
												
								);									
									
									
			$special_values = array(
								
								"'$the_date'"
								,"'$ref_no'"
								,"'$bank_id'"
								,"'$lawful_id'"
								,"'$this_RID'"
								
								, "'".doCleanInput($_POST[request_reason])."'"
								, "'".$sess_userid."'"
								, 'now()'
								, "'0'"
								
								
							);		
									
			$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace") . ";";
			

			mysql_query($the_sql);									
			
			
		}
		//echo $the_sql;
		//exit();
	}else{
	
	
		//for each selected companies, do insert
		for($i=1 ; $i<=$post_records ; $i++){
			if($_POST["chk_$i"]){
				
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
				
				if(!$first_row_done){
					
					//make first row to "main" payment record
					$special_fields = array("PaymentDate"
										,"RefNo"
										,"bank_id"
										,"LID"
										,"RID"		
										,"main_flag"	
										
										, 'request_reason'
										, 'request_userid'
										, 'request_date'
										, 'request_status'
									
										
										);
										
					$special_values = array("'$the_date'"
									,"'$ref_no'"
									,"'$bank_id'"
									,"'$lawful_id'"
									,"'$this_RID'"
									,"'1'"
									
									, "'".doCleanInput($_POST[request_reason])."'"
									, "'".$sess_userid."'"
									, 'now()'
									, "'0'"
									
									);
					
					$first_row_done = 1;
				}else{
					
					
					
					$special_fields = array("PaymentDate"
										,"RefNo"
										,"bank_id"
										,"LID"
										,"RID"	
										
										, 'request_reason'
										, 'request_userid'
										, 'request_date'
										, 'request_status'
													
										);
										
					$special_values = array("'$the_date'"
									,"'$ref_no'"
									,"'$bank_id'"
									,"'$lawful_id'"
									,"'$this_RID'"
									
									, "'".doCleanInput($_POST[request_reason])."'"
									, "'".$sess_userid."'"
									, 'now()'
									, "'0'"
									
									);
				}									
				
				
				
				$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
				
				//insert each "main" letters to db
				//echo $the_sql;exit();
				mysql_query($the_sql);
				//exit();
				
			}
		}
	
	}
	
	//---> handle attached files
	$file_fields = array(
						"receipt_docfile_request"
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		//echo "filesize: ".$hire_docfile_size;
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
			//echo $hire_docfile_path;
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
						,'$this_RID'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
			
			
			
		
		}
	
	}
	
	
	//then add this to history
	//$history_sql = "insert into modify_history values('$sess_userid','$selected_company',now(),7)";
	//mysql_query($history_sql);
	doAddModifyHistory($sess_userid,$selected_company,7,$lawful_id);
	
	//yoes 20160208
	resetLawfulnessByLID($lawful_id);

	//yoes 20180112 --> also add send-email-out here

    //
    $company_row = getFirstRow("select * from company where CID = '".$selected_company."'");
    $lawful_row = getFirstRow("select * from lawfulness where CID = '".$selected_company."' and year = '".$selected_year."'");

    $vars = array(

        "{company_code}" =>  $company_row["CompanyCode"]
        ,"{company_name}" => $company_row["CompanyNameThai"]
        ,"{the_year}" => $selected_year+543
        ,"{lawfulness}" =>  getLawfulText($lawful_row["LawfulStatus"])
        ,"{book_no}" => doCleanInput($_POST["BookReceiptNo"])
        ,"{receipt_no}" => doCleanInput($_POST["ReceiptNo"])
        ,"{pay_date}" => $the_date
        ,"{pay_amount}" => number_format(deleteCommas($_POST['Amount']),2)
        ,"{pay_method}" => formatPaymentName($_POST["PaymentMethod"])
        ,"{now_date}" => date("Y-m-d")

    );

    //print_r($vars); exit();

    sendMailByEmailId(4, $vars);



	//then redirect to whatever page
	//header("location: org_list.php?mode=lettersl&letter_added=letter_added");
	if($_POST["back_to"] == "lawfulness_tab" && !$extra_query){
		//header("location: organization.php?id=$selected_company&focus=lawful&year=$selected_year".$extra_query."&auto_post=1" );
		header("location: view_payment.php?id=".$this_RID."&view=request" );
		exit();
	}else{
		
		header("location: view_payment.php?id=".$this_RID."&view=request" );
		exit();
		
	}

?>