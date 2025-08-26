<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
	}else{
		exit();
	}
	
	
	//first thing first, get related receipt info
	$get_org_sql = "
				select 
					* 
				from 
					receipt, payment
				where 
					receipt.RID = '$this_id'
					and
					receipt.RID = payment.RID
				";
	
	
	//
	$org_array = array();
	//
	$the_year = "";
	
	
	$org_result = mysql_query($get_org_sql);
	$org_count = 0;
	
	while ($post_row = mysql_fetch_array($org_result)) {				
	
		
		//for each company in the receipt....
		
		$the_cid = getFirstItem("select CID from lawfulness where LID = '".$post_row["LID"]."'");		
		array_push($org_array, $the_cid);
		
		$the_year = $post_row["ReceiptYear"];
		
		//build extra query
		$org_count++;
		$extra_query .= "&org".$org_count."=$the_cid";
		
	
	}
	
	//print_r($org_array);	echo $the_year;	exit();
	
	$the_sql = "
				delete from payment
				where 
					RID = '$this_id'
				";
				
	mysql_query($the_sql);
	
	$the_sql = "
				delete from receipt
				where 
					RID = '$this_id'
				";
	
	mysql_query($the_sql);
			
	///
	
	header("location: payment_list.php?del_count=".count($org_array)."&year=$the_year".$extra_query);
	
	

?>