<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST); exit();
	include "session_handler.php";
	
	
	
	//yoes 20160104  --- do a close case thing
	$the_lid = doCleanInput($_POST["the_lid"]); 
	$the_cid = doCleanInput($_POST["the_cid"]); 
	$the_year = doCleanInput($_POST["the_year"]);
	$the_remark= doCleanInput($_POST["the_remarks"]); //20240820 Tor คดี ข้อ 4 2567

	//bank 20221223 add new urgent_flag
	$courted_date_is_urgent = doCleanInput($_POST["urgent_check"]);
	
	if($courted_date_is_urgent == "1"){
		
		
	}else{
		
	   $courted_date_is_urgent = "0";
	   
	}
	
	//get
	$hire_data_row = getFirstRow("
		
		select
			*
		from
			company a
				join
					lawfulness b
					on
					a.cid = b.cid
				left join
					provinces c
					on
					a.province = c.province_id
		where
			b.cid = '$the_cid'
			and
			b.year = '$the_year'
	
	");
	
	//print_r($hire_data_row); exit();
	
	
	//bank 20221223 add new urgent_flag courted_date_is_urgent
	
	$data = array(
	  'case_type'      => 'hire',
	  'case_code'    => $hire_data_row['CompanyCode'],
	  'case_name'       => formatCompanyName($hire_data_row['CompanyNameThai'], $hire_data_row['CompanyTypeCode']),
	  'case_submitted_by_name'      => getFirstItem("select concat(FirstName, ' ', LastName) from users where user_id = '$sess_userid'"),
	  'courted_date_is_urgent'	=> $courted_date_is_urgent,
	  'case_parent_remarks' => $the_remark, //20240820 Tor คดี ข้อ 4 2567
	  'case_remarks'	=> ""
	  ,'case_ref_pid'	=> "$the_cid"
	  , 'case_details' => array(
	  
			array(
				"case_year" => $hire_data_row['Year']
				,"contract_start_date" => "$the_year-01-01"
				,"contract_end_date" => "$the_year-12-31"
				,"contract_amount" => $_POST[the_principal]*1+$_POST[the_interests]*1
				,"contract_paid" => ''
				,"contract_outstanding" => $_POST[the_principal]*1+$_POST[the_interests]*1
				,"contract_last_payment_date" => '0000-00-00'
				,"case_principal" => $_POST[the_principal]*1
				,"case_interests" => $_POST[the_interests]*1
				,"case_owned_principal" => $_POST[the_principal]*1
				,"case_owned_interests" => $_POST[the_interests]*1
			)
		)
		
		, 'case_contact_address' => array(
		
			"address_no" => $hire_data_row['Address1']
			,"soi" => $hire_data_row['Moo']
			,"moo" => $hire_data_row['Soi']
			,"road" => $hire_data_row['Road']
			,"province_code" => $hire_data_row['province_code']
			,"province_name" => $hire_data_row['province_name']
			,"district" => ""
			,"district_name" => $hire_data_row['District']
			,"sub_district" => ""
			,"sub_district_name" => $hire_data_row['Subdistrict']
			,"postcode" => $hire_data_row['Zip']
			,"telephone" => $hire_data_row['Telephone']
			,"email" => $hire_data_row['email']

		)
		
		, "documents" => array(
		
			/*array(
				"label" => "ข้อมูลนิติบุคคล" . "ปี " . ($the_year+543)
				,"url" => "#"
			)
			, array(
				"label" => "ข้อมูลประกันสังคม" . "ปี " . ($the_year+543)
				,"url" => "#"
			)
			, array(
				"label" => "สำเนาแจ้งให้ปฏิบัติตามกฎหมาย" . "ปี " . ($the_year+543)
				,"url" => "#"
			)
			, array(
				"label" => "ไปรษณีย์ตอบรับสำเนาแจ้งให้ปฏิบัติตามกฎหมาย" . "ปี " . ($the_year+543)
				,"url" => "#"
			)
			, array(
				"label" => "หนังสือติดตามทวงถาม" . "ปี " . ($the_year+543)
				,"url" => "#"
			)*/
		)
		
		, "metas" => array(
		
			array(
				"label" => "lead_book_no"
				,"value" => $_POST[lead_book_no]
			)
			, array(
				"label" => "lead_book_date"
				,"value" => $_POST[lead_book_date]
			)
			
		)
	  
	);
	
	
	
	
	//yoes 20201020 -> send data to LAW SYSTEM
	if ($server_ip == "127.0.0.1" || $server_ip == "::1"){	
		$url = "http://localhost/law_system/rest_save_case.php"; 		
	}else{
		$url = "http://203.154.94.105/law_system/rest_save_case.php";  		
	}
	
	
	//print_r($data); exit();
	$content = json_encode($data);
	
	
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

	$json_response = curl_exec($curl);

	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	curl_close($curl);
	
	
	//end sending data
	
	//flag court case
	$sql = "
			replace into lawfulness_meta(
			
				meta_lid
				, meta_for
				, meta_value
			
			)values(
			
				'$the_lid'
				, 'courted_flag'
				, '1'
			
			)
			";
	
	
	mysql_query($sql);
	
	
	
	//flag court by
	$sql = "
			replace into lawfulness_meta(
			
				meta_lid
				, meta_for
				, meta_value
			
			)values(
			
				'$the_lid'
				, 'courted_by'
				, '$sess_userid'
			
			)
			";
	
	
	mysql_query($sql);
	
	
	
	//flag court ip
	$sql = "
			replace into lawfulness_meta(
			
				meta_lid
				, meta_for
				, meta_value
			
			)values(
			
				'$the_lid'
				, 'courted_ip'
				, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
			
			)
			";
	
	
	mysql_query($sql);
	
	
	//flag court by
	$sql = "
			replace into lawfulness_meta(
			
				meta_lid
				, meta_for
				, meta_value
			
			)values(
			
				'$the_lid'
				, 'courted_date'
				, now()
			
			)
			";
	
	
	mysql_query($sql);
	
	//flag lead_book_no
	$sql = "
			replace into lawfulness_meta(
			
				meta_lid
				, meta_for
				, meta_value
			
			)values(
			
				'$the_lid'
				, 'lead_book_no'
				, '".$_POST[lead_book_no]."'
			
			)
			";
	
	mysql_query($sql);
	
	
	//flag lead_book_no
	$sql = "
			replace into lawfulness_meta(
			
				meta_lid
				, meta_for
				, meta_value
			
			)values(
			
				'$the_lid'
				, 'lead_book_date'
				, '".$_POST[lead_book_date]."'
			
			)
			";
	
	mysql_query($sql);
	


	//bank add 20221223 urgent flag ( 1 = urgent , 0 = normal )
	if($courted_date_is_urgent == "1"){
		$sql = "
				replace into lawfulness_meta(
				
					meta_lid
					, meta_for
					, meta_value
				
				)values(
				
					'$the_lid'
					, 'courted_date_is_urgent'
					, '$courted_date_is_urgent'
				
				)
				";
		
		
		mysql_query($sql);

		//20240820 Tor คดี ข้อ 4 2567
		$sql = "
				replace into lawfulness_meta(
				
					meta_lid
					, meta_for
					, meta_value
				
				)values(
				
					'$the_lid'
					, 'courted_urgent_remarks'
					, '$the_remark'
				
				)
				";


		mysql_query($sql);
	}
	
	
	header("location: organization.php?id=$the_cid&focus=lawful&closed=closed&year=$the_year"); 
	exit();
	
?>