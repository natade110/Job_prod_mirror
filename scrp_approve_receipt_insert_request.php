<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	
	
	
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	
	
	
	
	
	//
	//print_r($_POST); exit();
	
	
	//amounts and remarks
	
	$edit_rid = $_POST['edit_rid']*1;
	//$cancel_pid = $_POST['cancel_pid']*1;
	
	//echo $edit_rid; exit();
	
	$edit_approve_by = $sess_userid;

	$edit_status = 0;
	
	if($_POST[approve_request]){
		
		$edit_status = 1;
	}elseif($_POST[reject_request]){
		
		$edit_status = 2;
	
	}elseif($_POST[cancel_request]){
		
		//yoes 20170516 -- cancel request
		$edit_status = 3;
	
	}
	
	
	
	if($edit_status == 1){
		
		
		
		//approved ...	
		$request_edit_row = getFirstRow("
													
										SELECT 
											*
											, request_reason as edit_reason
											, request_userid as edit_userid
											, request_date as edit_date
										FROM 
											payment_request a
												join
													receipt_request b												
												on
													a.RID = b.RID											
										where
											request_status = 0
											and
											a.rid = '$edit_rid'
						
										");
										
		//update back to "payment"..
		
		
		$addon_text = "-- อนุมัติการเพิ่มข้อมูลวันที่ " . date("d-m-Y") . " ขอปรับโดย "
		
								. getFirstItem("select user_name from users where user_id = '".$request_edit_row[edit_userid]."'")
								
								. " อนุมัติโดย "
								
								. getFirstItem("select user_name from users where user_id = '".$sess_userid."'")
								
								;
		
		
	
		
		//edit mode
	
		$sql = "
			
			
			insert into 
				receipt (
					
					BookReceiptNo
					, ReceiptNo
					, Amount
					, PaymentMethod
					, ReceiptNote
					, ReceiptYear
					, ReceiptDate
					, is_payback
				
				)
			select
				BookReceiptNo
				, ReceiptNo
				, Amount
				, PaymentMethod
				, '$addon_text'
				, ReceiptYear
				, ReceiptDate
				, is_payback
			from
				receipt_request
			where
				RID = '$edit_rid'

		
		";
		
		//echo $sql; exit();
		
		mysql_query($sql);
		
		//also update payment
		
		$insert_rid = mysql_insert_id();
		
		$sql = "
			
			insert into
				payment(
				
					PaymentMethod
					, PaymentDate
					, RefNo
					, bank_id
					, Amount
					, RID
					, LID
					, main_flag
										
				)
			
			select
			
			
					
					PaymentMethod
					, PaymentDate
					, RefNo
					, bank_id
					, Amount
					, '$insert_rid'
					, LID
					, main_flag
					
			from
				payment_request
			where
				RID = '$edit_rid'
		
		";
		
		//echo $sql; exit();
		
		mysql_query($sql);
		
		
		
		//yoes 20170509 
		//also update linked file ...
		$sql = "
			
			update
				files
			set
				file_for = '$insert_rid'
				, file_type = 'receipt_docfile'
			where
				file_for = '$edit_rid'
				and
				file_type = 'receipt_docfile_request'
		
		";		
		mysql_query($sql);
		
		
		$sql = "
				select
					lid
				from
					payment
				where
					RID = '$insert_rid'
				
				";
				
		$payment_result = mysql_query($sql);
		
		
		
		while($payment_row = mysql_fetch_array($payment_result)){
		
			//reset lawfulness...
			resetLawfulnessByLID($payment_row[lid]);
			
			$cid_row_to_return = getFirstRow("
				
				select
					cid
					, year
				from
					lawfulness 
				where
					lid = '".$payment_row[lid]."'
			
			");
		
		}
		
		
	}elseif($edit_status == 2 || $edit_status == 3){
		
		//not approved ...	
		//do nothing for "insert" case
		
	}
	
	//update request records..
	$sql = "
		
		update
			payment_request
		set
			request_approve_date = now()
			, request_approve_by = '$edit_approve_by'
			, request_status = '$edit_status'			
		where
			RID = '$edit_rid'
			
	
	
	";
	
	//print_r($_POST);
	
	//echo $sql; exit();
	
	mysql_query($sql);
	
	
	
	
	
	
	//
	//$cid_row_to_return
	if($request_edit_row[Amount] <= 0){
		//header("location: organization.php?id=".$cid_row_to_return[cid]."&focus=lawful&year=".$cid_row_to_return[year] );
		header("location: view_payment.php?id=".$insert_rid . "" );
		exit();
	}else{
		//header("location: view_payment.php?id=".$insert_rid );
		//header("location: organization.php?id=".$cid_row_to_return[cid]."&focus=lawful&year=".$cid_row_to_return[year] );
		header("location: view_payment.php?id=".$insert_rid . "" );
		exit();
	}
		
	

?>