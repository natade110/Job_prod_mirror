<?php

	include "db_connect.php";
	include "session_handler.php";
		
    $rid = $_POST["rid"];	
	$leid = $_POST["leid"]*1;
	$leid_to = $_POST["leid_to"]*1;
	$the_lid = $_POST["the_lid"]*1;
	$p_from = $leid;
	$p_to = $leid_to;
	
	if($_POST["rid"] == "xxx"){
		
		mysql_query("delete from receipt_meta where meta_for = '".$the_lid.$p_from.$p_to."'");
		//yoes 20190425	
		$msg['resp_code'] = $response;		
		echo json_encode($msg);
		exit();
		
	}elseif(is_numeric($_POST["leid"]) && $_POST["rid"] && is_numeric($_POST["leid_to"]) && $_POST["the_lid"]){

        $leid = $_POST["leid"]*1;
        $rid = $_POST["rid"]*1;
		$rid_amount = deleteCommas($_POST["rid_amount"])*1;   

		//yoes 20200624
		//needs lid and from-to
		$leid_to = $_POST["leid_to"]*1;
		$the_lid = $_POST["the_lid"]*1;
		
		
		$p_from = $leid;
		$p_to = $leid_to;
		
	}else{
		
		$response = "501";
		$msg['resp_code'] = $response;	
		$msg['remarks'] = "not all param presents";	
		echo json_encode($msg);
		exit();
	}
	
	
	//yoes 20200918 -- delete 33 that no longer exists
	$delete_sql = "
	
		delete a.*
			
			FROM receipt_meta a
				join
					payment b
						on
						a.meta_rid = b.rid
				join
					lawfulness c
					on
						b.lid = c.lid
					
			WHERE 
			
			(
				replace(a.meta_for, c.lid,'') not in (

					select
						concat(p_from, p_to)
					from
						lawful_33_principals
					

				)
				or
				replace(a.meta_for, c.lid,'')  = '00'
			)

			and
			a.meta_for != '33_for'
			and
			a.meta_for not like ('%amount')
			and
			a.meta_rid = '".$rid."'
	
	";
	
	//echo $delete_sql;
	
	mysql_query($delete_sql);
	
	
	//yoes 20190503
	//also clean 33 that no longer exitsts...
	
	mysql_query("delete from receipt_meta where meta_rid = '".$rid."' and meta_for = '".$the_lid.$p_from.$p_to."'");
	
	
	
	//exit();
		
	//yoes 20190425 --> check if sum amount exceeds rid amount
	
	//get receipt amount
	$receipt_amount = getFirstItem("
			
				select
					amount 
				from 
					receipt
				where
					rid = '$rid'
					
			");
	

	//get current total RID
	$receipt_used_amount = getFirstItem("
	
		select
			sum(meta_value)
		from
			receipt_meta
		where
			meta_rid = '$rid'
			and
			meta_for  like '".$the_lid."%'
			and
			meta_for  != '".$the_lid.$p_from.$p_to."'
		
	
	");
			
			
	$excess_amount = round($receipt_amount - ($receipt_used_amount + $rid_amount),2);
	$rid_balance = $receipt_amount - $receipt_used_amount;
	
	if($excess_amount < 0){
		
		$response = "500";
		$msg['resp_code'] = $response;
		$msg['receipt_amount'] =  number_format($receipt_amount,2);
		$msg['receipt_used_amount'] =  number_format($receipt_used_amount,2);
		$msg['rid_amount'] =  number_format($rid_amount,2);
		$msg['excess_amount'] =  number_format($excess_amount*-1,2);
		$msg['rid_balance'] =  number_format($rid_balance,2) ;
		
		
		echo json_encode($msg);			
		exit();
		
	}
	
	
	//yoes 20200624
	//insert new meta if have value else ignore it (else = is delete receipt meta)
	if($rid_amount > 0){
		
		$the_sql = "
				
				replace into receipt_meta(
					meta_rid
					, meta_for
					, meta_value
				)values(
					'".$rid."'
					, '".$the_lid.$p_from.$p_to."'
					, '".$rid_amount."'
				
				)
			";
				
		//echo $the_sql; exit();
			
		mysql_query($the_sql);
	}
	
	$response = "200";
	
	
	
	//yoes 20200624 - update lawfulstatus here?
	//resetLawfulnessByLID($the_lid);
	
				
	//echo trim($zone.":".$user);
	
	
	//yoes 20190425	
	$msg['resp_code'] = $response;
		
	echo json_encode($msg);

?>