<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	//table name
	$table_name = "docrequestcompany";
	$this_id = doCleanInput($_POST["DID"]);
	$this_cid = doCleanInput($_POST["CID"]);
	
	$this_year = $_POST["this_year"];
	
	//specify all posts fields
	$input_fields = array(
						'PostRegNum'
						, 'PostReceiverName'
						, 'PostReceivedTime'
						);
					
	//fields not from $_post	
	$special_fields = array("ModifiedDate","ModifiedBy");
	$special_values = array("NOW()","'$sess_userid'");
	
	//add vars to db
	$condition_sql = "where DID = '".$this_id."' limit 1";
	
	//add vars to db
	$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, $condition_sql);
	
	//echo $the_sql;exit();
	mysql_query($the_sql);
	

	
	//---> handle attached files
	$file_fields = array(
						"docrequestcompany_docfile"
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		//echo "filesize: ".$hire_docfile_size;
		$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
		if($hire_docfile_size > 0){
			
			//echo "what";
		
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
	
	
	
	
	
	header("location: organization.php?id=$this_cid&reg=reg&focus=official&year=$this_year");

?>