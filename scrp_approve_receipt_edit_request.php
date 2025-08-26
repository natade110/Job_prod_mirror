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
													
										select
											*
										from
											receipt_edit_request
										where
											edit_status = 0
											and
											rid = '$edit_rid'
						
										");
										
		//update back to "payment"..
		
		
		$addon_text = "-- อนุมัติการปรับปรุงวันที่ " . date("d-m-Y") . " ขอปรับโดย "
		
								. getFirstItem("select user_name from users where user_id = '".$request_edit_row[edit_userid]."'")
								
								. " อนุมัติโดย "
								
								. getFirstItem("select user_name from users where user_id = '".$sess_userid."'")
								
								;
		
		
		if($request_edit_row[Amount] > 0){
			
			//edit mode
		
			$sql = "
				
				update
					receipt
				set
					Amount = '".$request_edit_row[Amount]."'
					, PaymentMethod = '".$request_edit_row[PaymentMethod]."'
					, ReceiptNote = '".$request_edit_row[ReceiptNote]."'
					, ReceiptDate = '".$request_edit_row[ReceiptDate]."'
					, ReceiptNote = concat(ReceiptNote,'$addon_text')
				where
					RID = '$edit_rid'
						
			
			
			";
			
			//echo $sql; exit();
			
			mysql_query($sql);
			
			//also update payment
			
			
			
			//do full-log first
			doPaymentFullLog($sess_userid, $edit_rid, "scrp_approve_receipt_edit_request.php");
			doReceiptFullLog($sess_userid, $edit_rid, "scrp_approve_receipt_edit_request.php");
			
			$sql = "
				
				update
					payment
				set
					PaymentMethod = '".$request_edit_row[PaymentMethod]."'
					, RefNo = '".$request_edit_row[RefNo]."'
					, bank_id = '".$request_edit_row[bank_id]."'
					, Amount = '".$request_edit_row[Amount]."'
					
				where
					RID = '$edit_rid'
			
			";
		
		}elseif($request_edit_row[Amount] <= 0){
			
			//cancel mode
			doPaymentFullLog($sess_userid, $edit_rid, "scrp_approve_receipt_edit_request.php(deleted)");
			doReceiptFullLog($sess_userid, $edit_rid, "scrp_approve_receipt_edit_request.php(deleted)");
			
			
		}
		
		
		//echo $sql; exit();
		
		mysql_query($sql);
		
		
		$sql = "
				select
					lid
				from
					payment
				where
					RID = '$edit_rid'
				
				";
				
		$payment_result = mysql_query($sql);
		
		
		//if cancel -> delete RID, PID before recalc status
		if($request_edit_row[Amount] <= 0){
			
			
			//cancel mode
			$sql = "
				
				delete from payment where RID = '$edit_rid'
			
			";
			
			mysql_query($sql);
			
			
			//cancel mode
			$sql = "
				
				delete from receipt where RID = '$edit_rid'
			
			";
			
			mysql_query($sql);
			
							
		}
		
		
		
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
		$request_edit_row = getFirstRow("
													
										select
											*
										from
											receipt_edit_request
										where
											edit_status = 0
											and
											rid = '$edit_rid'
						
										");
										
		//update back to "payment"..
		
		
		if($edit_status == 2){
			
			$addon_text = "-- ไม่อนุมัติการปรับปรุงวันที่ " . date("d-m-Y") . " ขอปรับโดย "
		
								. getFirstItem("select user_name from users where user_id = '".$request_edit_row[edit_userid]."'")
								
								. " ไม่อนุมัติโดย "
								
								. getFirstItem("select user_name from users where user_id = '".$sess_userid."'")
								
								;
		}elseif($edit_status == 3){
			
			//ยกเลิกการปรับปรุง - do nothing
			$addon_text = "-- ยกเลิกคำร้องขอปรับปรุงข้อมูลวันที่ " . date("d-m-Y") .  " โดย "
								
								. getFirstItem("select user_name from users where user_id = '".$sess_userid."'")
								
								;
		}
		
		
		$sql = "
			
			update
				receipt
			set
				ReceiptNote = concat(ReceiptNote,'$addon_text')
			where
				RID = '$edit_rid'
					
		
		
		";
		
		//echo $sql; exit();
		
		mysql_query($sql);
		
		//also update payment
		
		
	}
	
	//update request records..
	$sql = "
		
		update
			receipt_edit_request
		set
			edit_approve_date = now()
			, edit_approve_by = '$edit_approve_by'
			, edit_status = '$edit_status'
		where
			RID = '$edit_rid'
			and
			edit_status = 0
	
	
	";
	
	//print_r($_POST);
	
	//echo $sql; exit();
	
	mysql_query($sql);
	
	
	
	
	
	
	//
	//$cid_row_to_return
	if($request_edit_row[Amount] <= 0){
		header("location: organization.php?id=".$cid_row_to_return[cid]."&focus=lawful&year=".$cid_row_to_return[year] );
		exit();
	}else{
		header("location: view_payment.php?id=".$edit_rid );
		exit();
	}
		
	

?>