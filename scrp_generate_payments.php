<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	//FIRST, see how many checkboxes are checked
	$post_records = $_POST["total_records"]*1;
	
	
	
	//then generate "main" letter
	$table_name = "payments";
			
	$input_fields = array(
						
						'cash_amount'
						
						,'check_bank'
						,'check_number'
						
						,'check_amount'
						
						,'note_number'
						,'note_amount'

						);
						
	$cash_date = $_POST["cash_date_year"]."-".$_POST["cash_date_month"]."-".$_POST["cash_date_day"];
	$check_date = $_POST["check_date_year"]."-".$_POST["check_date_month"]."-".$_POST["check_date_day"];
	$note_date = $_POST["note_date_year"]."-".$_POST["note_date_month"]."-".$_POST["note_date_day"];					
	
	$special_fields = array("pay_modified_date"
							,"cash_date"
							,"check_date"
							,"note_date"							
							, "pay_modified_by");
	$special_values = array("NOW()"
						,"'$cash_date'"
						,"'$check_date'"
						,"'$note_date'"
						, "'$sess_userid'");	
	
							
	
	
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	
	//insert each "main" letters to db
	//echo $the_sql;exit();
	mysql_query($the_sql);
	//exit();
	$new_payment_id = mysql_insert_id();
	
	
	//-----> what doc to insert to child table (DocRequestCompany)
	
	for($i=1 ; $i<=$post_records ; $i++){
		if($_POST["chk_$i"]){
			
			$org_id = $_POST["chk_$i"];
			
			//try to generate insert statement for each org...
			$table_name = "payments_company";
	
								
			$special_fields = array(
									
									'pc_payment_id'
									,'pc_company_id'
			
									);
			$special_values = array(
									"'".$new_payment_id."'"
									, "'".$org_id."'"
									);	
								
			$the_sql = generateInsertSQL($_POST,$table_name,$blank_fields,$special_fields,$special_values);
			
			//insert each "checked" letters to db
			//echo "<br>".$the_sql;exit();
			mysql_query($the_sql);
			//$new_id = mysql_insert_id();
			
			//---> finished main "letters", insert child letters
			
		}
	}
	
	
	
	//---> handle attached files
	$file_fields = array(
						"cash_docfile"
						,"check_docfile"
						,"note_docfile"
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
						,'$new_payment_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
			
			
			//no new file uploaded, retain old file name in db
			//array_push($special_fields,$file_fields[$i]);
			//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where LID = '".doCleanInput($_POST["LID"])."'")."'");
		
		}
	
	}
	
	//then redirect to whatever page
	//header("location: org_list.php?mode=lettersl&letter_added=letter_added");
	header("location: view_payment.php?id=$new_payment_id&payment_added=payment_added");

?>