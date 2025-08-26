<?php

	include "db_connect.php";
	include "session_handler.php";
	
	
	if($sess_accesslevel != 1 && $sess_accesslevel != 2 && $sess_accesslevel != 3 && $sess_accesslevel != 8){
		
		echo "ไม่มีสิทธิในการลบใบจ่ายเงิน"; exit();
		
	}
	
	
	if(is_numeric($_GET["invoice_id"])){
		$invoice_id = doCleanInput($_GET["invoice_id"]);
		$invoice_row = getFirstRow("select * from invoices where invoice_id = '$invoice_id'");
		
		//echo "select * from invoices where invoice_id = '$invoice_id'";		
		//print_r($invoice_row); exit;
	}else{
		echo "ไม่พบเลขใบจ่ายเงิน"; exit();
	}
	
	
	
	$the_sql = "
	
				delete from invoices
				where 
					invoice_id = '$invoice_id'
					and
					invoice_status != '2'
				
				";
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	header("location: add_invoice_pro.php?search_id=".$invoice_row[invoice_cid]."&mode=payment&for_year=".$invoice_row[invoice_lawful_year]."");
	

?>