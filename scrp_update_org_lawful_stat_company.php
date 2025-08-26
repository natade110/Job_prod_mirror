<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST); exit();
	include "session_handler.php";
	
	
	//include "scrp_config.php";
	
	//table name
	$table_name = "lawfulness_company";
	$this_id = doCleanInput($_POST["CID"]);
	$this_year = doCleanInput($_POST["Year"]);
	
	$lawful_id = getFirstItem("select LID from lawfulness_company where Year = '$this_year' and CID = '$this_id'");
	
	//echo "select LID from lawfulness_company where Year = '$this_year' and CID = '$this_id'";	
	//echo $this_year . "-" . $this_id . "-" . $lawful_id; exit();
	
	//20140306 -- also add this
	$current_is_submitted = getFirstItem("select lawful_submitted from lawfulness_company where Year = '$this_year' and CID = '$this_id'");
	
	
	if($current_is_submitted < 1){
		$is_submitted = $_POST["is_submitted"];
	}else{
		$is_submitted = $current_is_submitted;
	}
	
	
	//for COMPANY
	//JUST UPDATE flag to notify admin
	
	$sql = "update 
				lawfulness_company 
			set 
				lawful_submitted = '$is_submitted'
				, lawful_remarks = '".doCleaninput($_POST["lawful_remarks"])."' 
				, lawful_submitted_on = now()
			where 
				Year = '$this_year' and CID = '$this_id'";
	
	//echo $sql; exit();
	
	
	
	//yoes 20151123 --
	//also sending out email when company ยื่นแบบฟอร์มออนไลน์
	
	if($is_submitted == 1){
		
		
		$mail_address = "witaya8989@gmail.com";
				
		$the_header = "ระบบรายงานผลการจ้างงานคนพิการ: มีการยื่นเอกสารออนไลน์มาจากสถานประกอบการ";
	
		$the_body = "<table><tr><td>เรียน ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ<br><br>";

		$the_body .= "มีการส่งข้อมูลเข้ามาจากสถานประกอบการ ".getFirstItem("select CompanyNameThai from company where cid = '$this_id'")." <br>";
		$the_body .= "กรุณา login เข้าระบบเพื่อตรวจสอบข้อมูลที่สถานประกอบการได้ส่งเข้ามา<br><br>";

		$the_body .= ", ระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
		
		
		if ($server_ip == "203.146.215.187"){
			//ictmerlin.com use default mail
			mail($mail_address, $the_header, $the_body);
		}elseif ($server_ip == "127.0.0.1"){
		
			//donothin	
		
		}else{
			//use smtp
			doSendMail($mail_address, $the_header, $the_body);	
		}
		
		
	}
	
	
	mysql_query($sql);
	
	
	//also update payment info (if any)
	if($_POST["PaymentMethod"]){
		
		$payment_method = $_POST["PaymentMethod"];
		$ref_no = $_POST[$payment_method."_ref_no"];
		$amount = deleteCommas($_POST["Amount"]);
		
		//$the_pay_date = $_POST["the_pay_date_year"]."-".$_POST["the_pay_date_month"]."-".$_POST["the_pay_date_day"];	//this one is "payment" date
		//$the_note_date = $_POST["the_note_date_year"]."-".$_POST["the_note_date_month"]."-".$_POST["the_note_date_day"];	//this one is "note" date
		//$the_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];	//this one is cheque date
		
		
		//20140303
		//note date/pay date is the same as payment date		
		$the_pay_date = $_POST["the_pay_date_year"]."-".$_POST["the_pay_date_month"]."-".$_POST["the_pay_date_day"];
		
		
		//yoes 20151122 -- add default value if not select anythin
		if($the_pay_date == "0000-00-00"){
			
			$the_note_date = date("Y")."-".date("m")."-".date("d");
			$the_pay_date = $the_note_date;	
			$the_date = $the_note_date;	
			
		}else{
			$the_note_date = $the_pay_date;
			$the_date = $the_pay_date;	
		}
		
				
		$bank_id = $_POST["check_bank"];
	
		$sql = "
				replace into payment_company(
		
					CID
					, Year
					, PaymentMethod
					, PaymentDate
					, RefNo
					, bank_id
					, Amount
					, PayDate
					, NoteDate
		
				)values(
				
					'$this_id'
					, '$this_year'
					, '$payment_method'
					, '$the_date'
					, '$ref_no'
					, '$bank_id'
					, '$amount'
					, '$the_pay_date'
					, '$the_note_date'
				
				)
				";
				
		//echo $sql;exit();
		mysql_query($sql);
		
	}
	
	
	
	
	//---> handle attached files
	$file_fields = array(
						"company_docfile"					
						
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		
	
		$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
		
		if($hire_docfile_size > 0){
			
			
			
			$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
			$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
			$hire_docfile_exploded = explode(".", $hire_docfile_name);
			$hire_docfile_file_name = $hire_docfile_exploded[0]; 
			$hire_docfile_extension = $hire_docfile_exploded[1]; 
			
			
			//yoes 20151124 --> only allow some file type only
			//disallow upload for certain file type
			$allowed = array("image/jpeg", "image/jpg", "image/gif", "application/pdf");
			$allow_file_upload = 1;
			if(!in_array($hire_docfile_type, $allowed)) {
			  //$error_message = 'Only jpg, gif, and pdf files are allowed.';
			  //$error = 'yes';
			  $allow_file_upload = 0;
			}
			
			//echo  $allow_file_upload; exit();
				
			
			if($allow_file_upload){
			
			
				//new file name
				$new_hire_docfile_name = date("dmyhis").rand(00,99)."_".$hire_docfile_file_name; //extension
				$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
				
				//echo $hire_docfile_path; exit();
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
							,'$lawful_id'
							,'company_docfile'
						)";
				
					mysql_query($sql);
					
				}
			
			}
			
		}else{
			
			//no new file uploaded, retain old file name in db
			//array_push($special_fields,$file_fields[$i]);
			//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where LID = '".doCleanInput($_POST["LID"])."'")."'");
		
		}
	
	}
	
	
		
	if($_POST["auto_post"]){
		header("location: organization.php?id=$this_id&focus=lawful&updated=updated&year=".$_POST["Year"].""); 		
	}else{
		header("location: organization.php?id=$this_id&focus=lawful&updated=updated&year=".$_POST["Year"].""); 	
	}
	
	exit();

?>