<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	//table name
	$this_id = doCleanInput($_POST["register_id"]);
	
		
	
	//---> handle attached files
	$file_fields = array(
						"mod_file"
						
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
		
		if($hire_docfile_size > 0){
			
			$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
			$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
			$hire_docfile_exploded = explode(".", $hire_docfile_name);
			$hire_docfile_file_name = $hire_docfile_exploded[0]; 
			$hire_docfile_extension = $hire_docfile_exploded[1]; 
			
			if($hire_docfile_extension != "pdf" && $hire_docfile_extension != "docx" && $hire_docfile_extension != "doc"){
				
				header("location: submit_forms.php?format=format");
				exit();
				
			}
			
			//new file name
			$new_hire_docfile_name = date("dmyhis").rand(00,99)."_companydoc"; //extension
			$hire_docfile_path = "./register_doc/" . $new_hire_docfile_name . "." . $hire_docfile_extension; 
			//echo $hire_docfile_path;exit();
			//
			if(move_uploaded_file($_FILES[$file_fields[$i]]['tmp_name'], $hire_docfile_path)){	
				//move upload file finished
				//array_push($special_fields,$file_fields[$i]);
				//array_push($special_values,"'".$new_hire_docfile_name.".".$hire_docfile_extension."'");
				$file_name = $new_hire_docfile_name.".".$hire_docfile_extension;
				
			}
		}else{
			
			//no new file uploaded, retain old file name in db
			//array_push($special_fields,$file_fields[$i]);
			//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where LID = '".doCleanInput($_POST["LID"])."'")."'");
		
		}
	
	}	
	
	
	
	
	//also update register stat
	$history_sql = "insert into modify_history_register(
							
							mod_register_id
							, mod_date
							, mod_type
							
							, mod_desc
							, mod_year
							, mod_file
							
					) values(
						'$this_id'
						,now()
						,3
						
						, '".doCleanInput($_POST["mod_desc"])."'
						, '".doCleanInput($_POST["ddl_year"])."'
						, '$file_name'
						
					)";
					
	mysql_query($history_sql);
	
	//echo $the_sql; exit();
	mysql_query($the_sql);	
	header("location: submit_forms.php?added=added");
	exit();
		
	
	
	

?>