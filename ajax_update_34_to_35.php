<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if(is_numeric($_POST["curator_id"]) && $_POST["rid"]){

        $curator_id = $_POST["curator_id"]*1;
        $rid = $_POST["rid"]*1;   
		$rid_amount = deleteCommas($_POST["rid_amount"])*1; 		
		
	}else{
		exit();
	}
	
	
	//yoes 20190503
	//also clean 33 that no longer exitsts...
	
	$the_sql = "
		
		delete
		
		from
			receipt_meta
		where
			meta_for = '35_for'
			and
			meta_value not in (
			
			
				select
					curator_id
				from
					curator
				
			
			)
	
	
	";
	
	mysql_query($the_sql);
	
	//yoes 20190425
	//clean amount that didn;t have parent...
	//do this everytime here -> inefficient but suffice
	
	$the_sql = "
	
				delete 
					
				from 
				
					receipt_meta
					
				where
					meta_for like '35_for-%-amount'
					
					and meta_rid not in (
					
						
						select
							meta_rid
						from
						(
						
							select
								meta_rid
							from
								receipt_meta
							where
								meta_for =  '35_for'					
								
						) a
					
					)
					
				";
				
	mysql_query($the_sql);

	
	if($rid == "xxx"){
		
		
		$the_sql = "
	
				delete 
					
				from 
				
					receipt_meta
				
				where
					
					meta_rid in (
						
						select
							meta_rid
						from
						
							(
								select
									meta_rid
								from 
							
									receipt_meta
								
								where
									meta_for =  '35_for'
									and meta_value = '$curator_id'
							) a
							
					)
					and 
					meta_for  = '35_for-$curator_id-amount'
				
				
				";
		
		mysql_query($the_sql);
		
		$msg['bedug_code'] = $the_sql;
		
		
		$the_sql = "
	
				delete from 
				
					receipt_meta
				
				where
					meta_for =  '35_for'
					and meta_value = '$curator_id'
				
				
				
				";
				
		mysql_query($the_sql);
		
		//
		
		$response = "200";
		
	}else{
	
		
		
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
				meta_for  like '35_for-%-amount'
				and
				meta_for  != '35_for-$curator_id-amount'
			
		
		");
				
				
		$excess_amount = $receipt_amount - ($receipt_used_amount + $rid_amount);
		$rid_balance = $receipt_amount - $receipt_used_amount;
		
		if($excess_amount < 0){
			
			$response = "500";
			$msg['resp_code'] = $response;
			$msg['receipt_amount'] =  number_format($receipt_amount,2);
			$msg['receipt_used_amount'] =  number_format($receipt_used_amount,2);
			$msg['rid_amount'] =  number_format($rid_amount,2);
			$msg['excess_amount'] =  number_format($excess_amount*-1,2);
			$msg['rid_balance'] =  number_format($rid_balance,2);
			
			
			echo json_encode($msg);			
			exit();
			
		}
		
		
	
		$the_sql = "
	
				replace into receipt_meta(
					meta_rid
					 , meta_for
					  , meta_value
				)
				values(
				
					'$rid'
					, '35_for'
					, '$curator_id'
				
				)
				
				";
				
		mysql_query($the_sql);
		
		
		$the_sql = "
	
				delete from 
					receipt_meta
				where
				
					meta_rid = '$rid'
					and 
					meta_for  = '35_for-$curator_id-amount'

				
				";
				
		mysql_query($the_sql);
		
		
		
		$the_sql = "
	
				replace into receipt_meta(
					meta_rid
					 , meta_for
					  , meta_value
				)
				values(
				
					'$rid'
					, '35_for-$curator_id-amount'
					, '$rid_amount'
				
				)
				
				";
				
		mysql_query($the_sql);
		
		$response = "200";
				
		
	}
	

				
	//echo trim($zone.":".$user);
	
	
	//yoes 20190425	
	$msg['resp_code'] = $response;
		
	echo json_encode($msg);

?>