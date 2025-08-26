<?php

	//header("Access-Control-Allow-Origin: http://job.dep.go.th");

	include "db_connect.php";
	include "rest_allowed_ip.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	$status = 0;
	$status_message = "--unknown status--";
	
	//
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	
	//$data = file_get_contents('php://input');
	//print_r($data); exit();
	//echo $data["operacion"];
	
	$input_array = array(		
		
		
		'ref_code'
		,'user_name'
		,'password'
		,'hire_year'


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
		!$ref_code
		|| !$user_name
		|| !$password
		|| !$hire_year
	
		){
		
		$status = -2;
		$status_message = "Mandaroty Field missing";
		
	}
	
	//check username -> password
	$user_id = getFirstItem("
	
		select
			user_id
		from
			users
		where
			user_name = '$user_name'
			and
			user_password = md5('$password')
			and
			AccessLevel = 1
	
	");

	// echo $user_id . " - " . $ref_code . " - " . $user_name  . " - " .  $password . " - " .  $hire_year; exit();

	
	
	if($status == 0 && $user_id){
	
		$the_cid = getFirstItem("
			
				select
					cid
				from
					company
				where
					companyCode = '$ref_code'
					and
					branchCode < 1
			
			"); 
	
		//
		//reopening the case
		
		$timestamp = date("YmdHis");
		
		$sql = "
		
				update
					lawfulness_meta
				set
					meta_for = concat(meta_for,'-reject-', '$timestamp')
				where
					meta_lid in (
					
						select
							lid
						from
							lawfulness
						where
							cid = '$the_cid'
						and
							Year in ($hire_year)
					
					)
					and
					meta_for in (
					
						'courted_flag'
						, 'courted_by'
						, 'courted_ip'
						, 'courted_date'
					
					)
					
				";
		
		//echo $sql; exit();
		
		mysql_query($sql);
		
		
		
	}
	
	//exit();
	
	
	
	
	if($status == 0){
		$status = 1;
		$status_message = "";
		//$json_result[case_id] = $case_id;
	}
	
	
	$json_result[status] = $status;
	$json_result[status_message] = $status_message;
	
	if($status != 1){
		$json_result[request] = print_r($data, true);
	}
	
	print json_encode($json_result);