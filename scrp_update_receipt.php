<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	//update the receipt
	$this_id = $_POST["receipt_id"];
	
	$selected_year = $_POST["ddl_year"];
	
	
	//20140127
	$origin_year = $_POST["origin_year"];
	
	
	//echo $selected_year; echo $origin_year; exit();
	
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	$table_name = "receipt";

	$input_fields = array(
						'PaymentMethod'
						
						,'ReceiptNote'
						
						);
	
	//only check this if new key != old key
	if($sess_accesslevel != 4 && ($_POST["oldBookReceiptNo"].$_POST["oldReceiptNo"] != $_POST["BookReceiptNo"].$_POST["ReceiptNo"] )){
		//also, check if BookReceiptNo and ReceiptNo already existed
		$count_receipt = getFirstItem("select RID from receipt 
						where BookReceiptNo = '".doCleanInput($_POST["BookReceiptNo"])."'
						and ReceiptNo = '".doCleanInput($_POST["ReceiptNo"])."'");

		if(strlen($count_receipt) == 0){
			//this is an "update" case, so there should be atleast 1 record in the DB
			array_push($input_fields,'BookReceiptNo','ReceiptNo');
			
		}else{
			$extra_query = "&duped_key=duped_key&pay_id=$count_receipt&book_num=".$_POST["BookReceiptNo"]."&pay_num=".$_POST["ReceiptNo"]."";
		}
	}
						
	
	$special_fields = array("pay_id"
								,"cash_date"
								,"check_date"
								,"note_date"
								,"cash_amount"
								,"check_amount"
								,"note_amount"
								);
	
	$the_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];			
							
	$special_fields = array("ReceiptYear","ReceiptDate",'Amount');
	$special_values = array("'$selected_year'" ,"'$the_date'" ,"'".deleteCommas($_POST["Amount"])."'");
						
	$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values," where RID = '$this_id'");
	//echo $the_sql; exit();	
	mysql_query($the_sql);
	
	
	//then update all payments inside this receipt
	$table_name = "payment";
	$payment_method = $_POST["PaymentMethod"];
	$input_fields = array(
						
						'PaymentMethod'
						

						);
						
	$ref_no = $_POST[$payment_method."_ref_no"];
	if($payment_method == "Cheque"){
		$bank_id = $_POST["check_bank"];
	}
	$special_fields = array("PaymentDate"
								,"RefNo"
								,"bank_id"
								,"Amount"
								,"main_flag"
								);
	$special_values = array("'$the_date'"
								,"'$ref_no'"
								,"'$bank_id'"
								,"'".deleteCommas($_POST["Amount"])."'"
								,"0"
								);	//also update main flag of eveything company in this receipt to 0 (we re-update this at next step)
								
	$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values," where RID = '$this_id'");
	//echo $the_sql; exit();	
	mysql_query($the_sql);
	
	//re-assign main company
	$the_sql = "update payment set main_flag = 1 where PID = '".$_POST["main_flag_pid"]."'";
	mysql_query($the_sql);
	
	//---> handle attached files
	$file_fields = array(
						"receipt_docfile"
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
						,'$this_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
			
			
			
		
		}
	
	}		
	
	//20140127
	//also check whether to refresh what years?
	if($selected_year != $origin_year){
		
		$extra_query .= "&origin_year=".$origin_year;
		
	}
	
	
	header("location: view_payment.php?id=$this_id&updated=updated".$extra_query);

?>