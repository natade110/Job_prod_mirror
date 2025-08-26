<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	//table name
	$table_name = "payments";
	$this_id = doCleanInput($_POST["pay_id"]);
		
	//specify all posts fields
	$input_fields = array(
	
						'check_bank'
						,'check_number'
						,'note_number'
						
						);
	
	$special_fields = array("pay_id"
								,"cash_date"
								,"check_date"
								,"note_date"
								,"cash_amount"
								,"check_amount"
								,"note_amount"
								);
	
	$cash_date = $_POST["cash_date_year"]."-".$_POST["cash_date_month"]."-".$_POST["cash_date_day"];
	$check_date = $_POST["check_date_year"]."-".$_POST["check_date_month"]."-".$_POST["check_date_day"];
	$note_date = $_POST["note_date_year"]."-".$_POST["note_date_month"]."-".$_POST["note_date_day"];
	
	$cash_amount = doCleanInput(deleteCommas($_POST["cash_amount"]));
	$check_amount = doCleanInput(deleteCommas($_POST["check_amount"]));
	$note_amount = doCleanInput(deleteCommas($_POST["note_amount"]));
	
	//echo $cash_date; exit();
	$special_values = array("'".doCleanInput($_POST["pay_id"])."'"
								,"'$cash_date'"
								,"'$check_date'"
								,"'$note_date'"
								,"'$cash_amount'"
								,"'$check_amount'"
								,"'$note_amount'"
								);
	
	$file_fields = array(
						"cash_docfile"
						,"check_docfile"
						,"note_docfile"
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
			//echo $hire_docfile_path;
			//
			if(move_uploaded_file($_FILES[$file_fields[$i]]['tmp_name'], $hire_docfile_path)){	
				//move upload file finished
				//array_push($special_fields,$file_fields[$i]);
				//array_push($special_values,"'".$new_hire_docfile_name.".".$hire_docfile_extension."'");
				
				//file uploaded, add record to files table
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
			
			//no new file uploaded, retain old file name in db
			//array_push($special_fields,$file_fields[$i]);
			//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where pay_id = '".doCleanInput($_POST["LID"])."'")."'");
			
			//do nothing
		
		}
	
	}
	
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, "replace");
	
	//echo $the_sql;exit();
	mysql_query($the_sql);
	
	header("location: view_payment.php?id=$this_id&updated=updated");

?>