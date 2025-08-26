<?php

	include "db_connect.php";
	//include "functions.php";
	
	
	//table name
	$table_name = "documentrequest";
	$this_id = doCleanInput($_POST["RID"]);
	$this_seq = doCleanInput($_POST["RequestNum"]);
	$this_docno = doCleanInput($_POST["GovDocumentNo"]);
	
	$this_seq_old = doCleanInput($_POST["RequestNum_old"]);
	$this_docno_old = doCleanInput($_POST["GovDocumentNo_old"]);
	
	//if change seq/name
	//first check if name/seq already existed...
	if($this_seq != $this_seq_old || $this_docno != $this_docno_old){
	
		$sql = "select count(*) from documentrequest where 
					(
						RequestNum = '$this_seq' 
						and 
						GovDocumentNo = '$this_docno'
					)
				 limit 0,1";
		$existed = getFirstItem($sql);
		
		//echo $sql; exit();
		if($existed){
			header("location: view_letter.php?id=$this_id&existed=existed&doc_no=".htmlspecialchars($this_docno)."&doc_seq=".htmlspecialchars($this_seq)."");
			exit();
		}
	}
	
	//specify all posts fields
	$input_fields = array(
						
						 	
						'GovDocumentNo' 	
						,'RequestNum' 
						,'hold_details' 
						
						);
	
	$request_date = $_POST["RequestDate_year"]."-".$_POST["RequestDate_month"]."-".$_POST["RequestDate_day"];
	
	//fields not from $_post	
	$special_fields = array("ModifiedDate","Year","ModifiedBy","RequestDate");
	$special_values = array("NOW()","'".$_POST["ddl_year"]."'","'$sess_userid'","'$request_date'");
	
	//conditions
	$condition_sql = "where RID = '".$this_id."' limit 1";
	
	//add vars to db
	$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, $condition_sql);
	
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	
	
	if($_POST["is_hold_letter"]){
		header("location: view_letter.php?id=$this_id&updated=updated&type=hold");
	}else{
		header("location: view_letter.php?id=$this_id&updated=updated");
	}

?>