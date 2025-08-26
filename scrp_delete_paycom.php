<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$this_cid = doCleanInput($_GET["cid"]);
		$this_rid = doCleanInput($_GET["rid"]);
		$this_pid = doCleanInput($_GET["pid"]);
		$this_year = doCleanInput($_GET["year"]);
		$is_main = doCleanInput($_GET["is_main"]);
	}else{
		exit();
	}
	
		
	
	$the_sql = "
	
				delete from payment
				where 
					PID = '$this_id'
				
				";
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	//if is main then set the other record as main
	if($is_main){
		$sql = "
				
				update payment
				set main_flag = 1
				where RID = '$this_rid'
				order by PID asc
				limit 1
				";
		//echo $sql; exit();
		mysql_query($sql);
	}
	
	//unset pay_status of this id
	$lawful_sql = "update lawfulness set
								pay_status = '0'
								where
								Year = '$this_year'
								and
								CID = '$this_cid'
								limit 1";
	//echo $lawful_sql; exit();
	mysql_query($lawful_sql);
	
	//check if this is a last company in this receipt
	$count_payments_in_receipt = getFirstItem("select count(PID) 
												 from payment
												where RID = '$this_rid'");
	if($count_payments_in_receipt == 0){
		//delete the receipt
		$the_sql = "
	
				delete from receipt
				where 
					RID = '$this_rid'				
				";
		mysql_query($the_sql);
		$back_to_org_page = 1;		
	}												
	
	
	
	
	//then add this to history
	//$history_sql = "insert into modify_history values('$sess_userid','$this_cid',now(),6)";
	//mysql_query($history_sql);
	$lawful_id = getFirstItem("select lid from lawfulness where Year = '$this_year' and CID = '$this_cid'");
	doAddModifyHistory($sess_userid,$this_cid,6,$lawful_id);
	
	//yoes 20160208
	resetLawfulnessByLID($lawful_id);
	
	if(is_numeric($this_rid) && !$back_to_org_page){
		header("location: view_payment.php?id=$this_rid&delpayment=delpayment&del_id=$this_cid");
	}elseif(is_numeric($this_cid) ){
		header("location: organization.php?id=$this_cid&delpayment=delpayment&focus=lawful&auto_post=1");
	}else{
		header("location: index.php");
	}

?>