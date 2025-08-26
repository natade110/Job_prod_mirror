<?php

	//header("Access-Control-Allow-Origin: http://job.dep.go.th");

	include "db_connect.php";
	include "rest_allowed_ip.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	$status = 0;
	$status_message = "--unknown status--";
	
	$data = array(
	
		
		'curator_name'	=> 'นาย อธิพันธ์ คงเขียว'
		,'curator_idcard'	=> '1100700135135'
		,'curator_gender'	=> "m"
		,'curator_age'	=> "50"
		,'curator_company' => array(
		
			'company_code' => '1002695627'
			, 'year' => '2020'
		
		)
		,'curator_child' => array(
			
			'curator_name'	=> 'นาย อธิพันธ์ อธิพันธ์'
			,'curator_idcard'	=> '1100700139999'
			,'curator_gender'	=> "m"
			,'curator_age'	=> "30"
			,'curator_disable_desc' => ""
			,'curator_is_disable'	=> '0'
			,'curator_dob'	=> '1984-10-24'
			
		
		) //someting
		,'curator_contract_number' => 'ม.35/002-2562'
		,'curator_event' => 'การให้สัมปทาน'
		,'curator_event_desc'	=> 'บิดา'
		,'curator_disable_desc' => ""
		,'curator_is_disable'	=> '0'
		,'curator_start_date'	=> '2020-01-01'
		,'curator_end_date'	=> '2020-12-31'
		,'curator_value'	=> '118625'
		,'curator_dob'	=> '1984-10-24'

	  
	);
	
	
	if ($server_ip == "127.0.0.1" || $server_ip == "::1"){	
		$url = "http://localhost/hire_dev/rest_save_curator.php"; 		
	}else{
		$url = "http://203.154.94.105/rest_save_curator.php";  		
	}
	
	$content = json_encode($data);
	
	echo "<br>".$content."<br>";

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

	$json_response = curl_exec($curl);

	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	echo "<br>status: ".$status ;
	echo "<br>json_response: ".$json_response ;
	
	if ( $status != 201 ) {
		//die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
	}


	curl_close($curl);

	$response = json_decode($json_response, true);
	
	//echo $response;