<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	if($_POST[do_add_docfile]){
		
		//yoes 20210208
		//do add doc file only
		//echo "do add docfile";
		//exit();
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
							,'".($_POST[receipt_id]*1)."'
							,'".$file_fields[$i]."'
						)";
				
					mysql_query($sql);
					
				}
			}else{
			
			}
		
		}
		
		header("location: view_payment.php?id=".($_POST[receipt_id]*1) );
		exit();
		
	}
	
	
	
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	
	
	$table_name = "receipt_cancel_requests";
	
	
	//
	
	$cancel_date = date("Y-m-d");;
	
	
	//amounts and remarks
	
	$cancel_rid = $_POST['cancel_rid']*1;
	//$cancel_pid = $_POST['cancel_pid']*1;
	$cancel_tid = $_POST['cancel_tid']*1;
	
	$cancel_reason = $_POST['cancel_reason'];
	$cancel_userid = $_POST['cancel_userid'];
	
	$special_fields = array(
						
						"cancel_date"
						, "cancel_rid"
						//, "cancel_pid"
						, "cancel_tid"
						, 'cancel_reason'
						
						, 'cancel_userid'
						, 'cancel_userid_text'
						
						);
	
	
	$special_values = array(
	
	
						"'$cancel_date'"
						, "'$cancel_rid'"
						//, "'$cancel_pid'"
						, "'$cancel_tid'"
						, "'$cancel_reason'"
						
						, "'$cancel_userid'"						
						, "'". getFirstItem("select CONCAT(FirstName, ' ', LastName) from users where user_id = '$cancel_userid'") ."'");
	
						
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	//echo $the_sql; exit();	
	mysql_query($the_sql);
	$this_cancel_id = mysql_insert_id();					
	
	
	//---> handle attached files
	$file_fields = array(
						"cancel_docfile"
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
						,'$this_cancel_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
		
		}
	
	}
	
	
	
		
	//header("location: view_payment.php?id=".$cancel_rid );
	//exit();
	//header("location: add_invoice.php?search_id=".$invoice_cid."&mode=payment&for_year=".$invoice_lawful_year );
	header("location: cancel_receipt.php?cancel_id=".$this_cancel_id );
	exit();
		
	

?>