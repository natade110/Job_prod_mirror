<?php
	require "db_connect.php";
	include "scrp_config.php";
	
	// variables
	$table_name = "collectiondocument";
	$collection_id = $_POST["CollectionID"];
	$year = $_POST["ddl_year"];
	$request_date = $_POST["RequestDate"];
	$receiver_date = $_POST["RecievedDate"];
	$receiver = $_POST["Reciever"];
	$createdBy = $_POST["CreatedBy"];
	$sql_request_date = convertThaiDateToSqlFormat($request_date);
	$sql_receiver_date = convertThaiDateToSqlFormat($receiver_date);
	
	$input_fields = array(
			'GovDocumentNo'
			,'DocumentDetail'
	);
	
	if (is_numeric($collection_id)){
		$get_collection_doc = "select * from $table_name where CollectionID = $collection_id";
		$collection_result = getFirstRow($get_collection_doc);
		
		$condition_sql = "RequestDate = '".$collection_result["RequestDate"]."' and RequestNo = ".$collection_result["RequestNo"].
						" and GovDocumentNo = '".$collection_result["GovDocumentNo"]."'";
		
		$get_collection_list = "select * from $table_name where $condition_sql";
		$list_result = mysql_query($get_collection_list);
		
		while ($post_row = mysql_fetch_array($list_result)) {
			$condition_sql = "where CollectionID = ".$post_row["CollectionID"].";";
			
			if(is_null($sql_receiver_date)){
				$special_fields = array("RequestNo","RequestDate","ModifiedDate", "ModifiedBy","CreatedBy");
				$special_values = array($_POST["RequestNo"],"'$sql_request_date'","NOW()", "'$sess_userid'","'$createdBy'");
			}else{
				$special_fields = array("RequestNo","RequestDate","Reciever","RecievedDate","ModifiedDate", "ModifiedBy","CreatedBy"); 
				$special_values = array($_POST["RequestNo"],"'$sql_request_date'","'$receiver'","'$sql_receiver_date'","NOW()", "'$sess_userid'","'$createdBy'");
			}
			
			$the_sql = generateUpdateSQL($_POST, $table_name, $input_fields, $special_fields, $special_values,$condition_sql);
			mysql_query($the_sql) or die (mysql_error());
			$this_collectorID = $post_row["CollectionID"];
			add_attachment();
		}
	}
	
	//---> handle attached files
	function add_attachment(){
		$file_fields = array(
				"collector_doc"
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
	
				if(move_uploaded_file($_FILES[$file_fields[$i]]['tmp_name'], $hire_docfile_path)){
					//insert data to table files
					$sql = "insert into files(
							file_name
							, file_for
							, file_type)
						values(
							'".$new_hire_docfile_name.".".$hire_docfile_extension."'
							,".$this_collectorID."
							,'".$file_fields[$i]."'
						)";
	
					mysql_query($sql);
				}
			}
		}
	}
	
	header("location: collection_doc_list.php?issuccess=true");
?>