<?php
	require_once "db_connect.php";
	include_once "scrp_config.php";

	// variables
	$post_records = $_POST["total_records"]*1;
	$yearValue = $_POST["ddl_year"];
	$request_date = $_POST["RequestDate"];
	$sql_request_date = convertThaiDateToSqlFormat($request_date);
	$createdBy = $_POST["CreatedBy"];
	$table_name = "collectiondocument";
	$input_fields = array(
			'GovDocumentNo'
			,'DocumentDetail'
	);
	$this_collectorID = 0;
	
	if($_POST["send_to_all"]){
		$condition = ($_POST["send_to_all"]);
		$get_org_sql = "SELECT z.CID as the_cid FROM company z
						LEFT JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
						LEFT JOIN provinces c ON z.province = c.province_id
						JOIN lawfulness y ON z.CID = y.CID and y.Year = '$yearValue'
						where $condition";
		
		$org_result = mysql_query($get_org_sql);

		while ($post_row = mysql_fetch_array($org_result)) {
			$org_id = $post_row["the_cid"];
			$l_id = getFirstItem("select LID from lawfulness where Year = '".$yearValue."' and CID = ".$org_id." limit 0,1");
			
			$special_fields = array("RequestDate","RequestNo","LID","CreatedDate", "CreatedBy");
			$special_values = array("'$sql_request_date'",$_POST["RequestNum"],$l_id,"NOW()", "'$createdBy'");
			$the_sql = generateInsertSQL($_POST, $table_name, $input_fields, $special_fields, $special_values);
			
			mysql_query($the_sql) or die (mysql_error());
			$this_collectorID = mysql_insert_id();
			add_attachment();
		}
	}else{
		// keep value LID
		for($i=1 ; $i<=$post_records ; $i++){
			if($_POST["chk_$i"]){
				$org_id = $_POST["chk_$i"];
				$l_id = getFirstItem("select LID from lawfulness where Year = '".$yearValue."' and CID = ".$org_id." limit 0,1");
				
				$special_fields = array("RequestDate","RequestNo","LID","CreatedDate", "CreatedBy");
				$special_values = array("'$sql_request_date'",$_POST["RequestNum"],$l_id,"NOW()", "'$createdBy'");
				$the_sql = generateInsertSQL($_POST, $table_name, $input_fields, $special_fields, $special_values);
				
				mysql_query($the_sql);
				$this_collectorID = mysql_insert_id();
				add_attachment();
			}
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