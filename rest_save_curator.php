<?php

	//header("Access-Control-Allow-Origin: http://job.dep.go.th");

	include "db_connect.php";
	include "rest_allowed_ip.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	$status = 0;
	$status_message = "--unknown status--";
	
	
	$data = json_decode(file_get_contents('php://input'), true);
	//$data = file_get_contents('php://input');
	//print_r($data); exit();
	//echo $data["operacion"];
	
	$input_array = array(		
		
		
		'curator_name'
		,'curator_idcard'
		,'curator_gender'
		,'curator_age'
		,'curator_company'
		,'curator_child'
		,'curator_contract_number'
		,'curator_event'
		,'curator_event_desc'
		,'curator_disable_desc'
		,'curator_is_disable'
		,'curator_start_date'
		,'curator_end_date'
		,'curator_value'
		,'curator_dob'


	);
	
	for($i = 0; $i < count($input_array) ; $i++){		
		${$input_array[$i]} = $data[$input_array[$i]];
		
		if(!is_array(${$input_array[$i]})){
			${$input_array[$i]} = doCleanInput(${$input_array[$i]});
		}
	}
	
	/*
	print_r($case_contact_address);
	print_r($data[case_contact_address]); 
	exit();*/
	
	/*echo "<br>".$case_type;
	echo "<br>".$case_code;
	echo "<br>".$case_name;
	echo "<br>".$case_submitted_by_name;*/
	if(
		!$curator_name
		|| !$curator_idcard
		|| !$curator_company
		|| !$curator_start_date
		|| !$curator_end_date
		|| !$curator_value
	
		){
		
		$status = -2;
		$status_message = "Mandaroty Field missing - case_type - case_code - case_name - case_submitted_by_name";
		
	}
	
	
	if($status == 0){
	
		//do save case
		$the_sql = "
			
			insert into curator
				(
				
					curator_name
					,curator_idcard
					,curator_gender
					,curator_age					
					,curator_contract_number
					,curator_event
					,curator_event_desc
					,curator_disable_desc
					,curator_is_disable
					,curator_start_date
					,curator_end_date
					,curator_value
					,curator_dob
					,curator_created_date
					,curator_created_by

					
				)
			values(
				
					'$curator_name'
					,'$curator_idcard'
					,'$curator_gender'
					,'$curator_age'					
					,'$curator_contract_number'
					,'$curator_event'
					,'$curator_event_desc'
					,'$curator_disable_desc'
					,'$curator_is_disable'
					,'$curator_start_date'
					,'$curator_end_date'
					,'$curator_value'
					,'$curator_dob'
					, now()
					, '999888'
			
			)
		
		";
		
		//echo $the_sql; 
		
		mysql_query($the_sql);	
		
		$curator_id = mysql_insert_id();
		
		if(!$curator_id){
			
			$status = -2;
			$status_message = "error: failed create curator";
			
		}
		
	}
	
	//exit();
	
	
	if($status == 0){
	
		$comp = $curator_company;
	
		//do save case_address
		$the_sql = "
			
				update 
					curator
				set
					curator_lid = (
					
						select
							lid
						from
							lawfulness
						where
							cid = (
							
								select
									cid
								from
									company
								where
									companyCode = '".$comp[company_code]."'
									and
									branchCode < 1
							
							)
							and
							year = '".$comp[year]."'
					
					)
				where
					curator_id = '".$curator_id."'
				";
				
		//echo $the_sql;
				
		mysql_query( $the_sql);	
		
	}
	
	if($status == 0 && $curator_child){
	
		$child = $curator_child;
	
		//do save case_address
		$the_sql = "
			
				insert into curator
				(
				
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age					
						
						,curator_disable_desc
						,curator_is_disable						
						
						,curator_dob
						,curator_created_date
						,curator_created_by
						
						, curator_parent

						
					)
				values(
					
						'".$child[curator_name]."'
						,'".$child[curator_idcard]."'
						,'".$child[curator_gender]."'
						,'".$child[curator_age]."'					
						
						,'".$child[curator_disable_desc]."'
						,'".$child[curator_is_disable]."'
						
						,'".$child[curator_dob]."'
						, now()
						, '999888'
						
						, '$curator_id'
				
				)
				";
				
		//echo $the_sql;
				
		mysql_query( $the_sql);	
		
	}
	
	
	
	
	
	
	
	if($status == 0){
		$status = 1;
		$status_message = "";
		$json_result[case_id] = $case_id;
	}
	
	
	$json_result[status] = $status;
	$json_result[status_message] = $status_message;
	
	if($status != 1){
		$json_result[request] = print_r($data, true);
	}
	
	print json_encode($json_result);