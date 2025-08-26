<?php

	include "db_connect.php";
	
	//print_r($_POST);
	
	//table name
	$table_name = "document_requests";
	$this_id = doCleanInput($_POST["docr_org_id"]);
	
	//specify all posts fields
	$input_fields = array(
						'docr_org_id'
						
						,'docr_status'
						
						,'docr_status_remark'
						
						,'docr_year'
						
						
						
						);
	
	//if status is == 1 then we do not need docr desc
	//else, record docr desc
	if($_POST["docr_status"] != 1){
		$desc_to_use = $_POST["docr_desc"];
	}
	
	$this_docr_date = $_POST["docr_date_year"]."-".$_POST["docr_date_month"]."-".$_POST["docr_date_day"];
	
	
	//fields not from $_post	
	$special_fields = array("docr_last_updated","docr_desc","docr_date");
	$special_values = array("NOW()","'$desc_to_use'","'$this_docr_date'");
	
	
	//add vars to db
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	
	//echo $the_sql;
	mysql_query($the_sql);
	
	header("location: organization.php?id=$this_id&focus=official&updated=updated&year=".$_POST["docr_year"]);

?>