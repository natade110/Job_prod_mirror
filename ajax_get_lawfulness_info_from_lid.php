<?php
	
	$origin = $_SERVER['HTTP_ORIGIN'];
	$allowed_domains = array(
		"http://203.154.94.105"
		, "http://law.dep.go.th"
	);

	if (in_array($origin, $allowed_domains)) {
		header('Access-Control-Allow-Origin: ' . $origin);
	}
	
	
	
	//header('Access-Control-Allow-Origin: http://law.dep.go.th');
	//header('Access-Control-Allow-Origin: *');
	
	//echo $origin;
	
	include_once "db_connect.php";
	
	
	$status = 0; //set status fail
	$status_message = "--unknown status--";
	
	//yoes 20220304 - add ejob mode
	$the_mode = $_GET["the_mode"];
	if($the_mode == "ejob"){
		$the_mode = "ejob";
	}else{
		$the_mode = "";
	}
	//bank 20220826 add string input
	$the_format = $_GET["the_format"];

	//$the_id = "2000000002"; // 2000000002 = mg solution
	//$the_year = 2021; // 
	
	if(!$the_id){
		$the_id = $_GET["the_id"]*1;
	}
	if(!$the_year){
		$the_year = $_GET["the_year"]*1;
	}
	
	
	
	if($_GET["current_date"]){
		$current_date = substr($_GET["current_date"],0,10);
	}else{
		$current_date = date("Y-m-d");
	}
	
	
	//yoes 20220221
	//allow to get values from LID
	if($_GET["the_lid"] || $_POST["the_lid"]){
		$the_lid = $_GET["the_lid"]*1;
		if(!$the_lid){
			$the_lid = $_POST["the_lid"]*1;
		}
	}
	
	
	//$current_date = '2022-02-18';
	
	if(!$the_id && !$the_year && !$the_lid){		
		//no vars specify
		exit();		
	}
	
	
	/*if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}*/
	
	
	///
	$company_row = getFirstRow("select cid from company where CompanyCode = '$the_id' and BranchCode < 1");
	//echo "select cid from company where CompanyCode = '$the_id' and BranchCode < 1";
	
	if($the_lid){
		
		$lawfulness_row = getFirstRow("select lid, Hire_NumofEmp, Year, Employees,LawfulStatus  from lawfulness where lid = '$the_lid'");
		
	}else{
	
		$lawfulness_row = getFirstRow("select lid, Hire_NumofEmp, Year, Employees,LawfulStatus  from lawfulness where cid = '".$company_row[cid]."' and year = ".($the_year*1)."");
		
	}
	
	//print_r($lawfulness_row);
	
	if(!$lawfulness_row)
	{
		$status = 0;
		$status_message = "Fail to get lid from lawfulness";
	}
	//echo $lawfulness_row[lid];

    if($the_lid == 2050540885){
        //resetLawfulnessByLID(($the_lid), 0);
        //print_r($lawful_status_array); exit();
    }
	
	//resetLawfulnessByLID(($the_lid), 0);
	
	if(!$the_lid){
		$the_lid = $lawfulness_row[lid]*1;
	}
		


    if($the_lid == 2050619067){
        //echo $the_lid; exit();
    }
	
	generateM34PrincipalsByDate(($the_lid), $current_date, $the_mode);
	
	generate33PrincipalFromLID(($the_lid), "_temp", $the_mode);
	generate33InterestsFromLID(($the_lid), "_temp", $current_date, $the_mode);

    if($the_lid == 2050649058){
        //echo "generate33InterestsFromLID(($the_lid), '_temp', $current_date, $the_mode)";
        //exit();
        //echo "<pre>"; print_r($total_money_to_pay_array); echo "</pre>"; exit();
    }
	
	generate35PrincipalFromLID(($the_lid), "_temp", $the_mode);
	generate35InterestsFromLID(($the_lid), "_temp", $current_date, $the_mode);
	
	$lawful_34_row = getFirstRow("select * from lawful_34_principals_temp where p_lid = '".($the_lid)."'");
	
	$total_money_to_pay_array = getGetTotalToPayByLid($the_lid, "_temp");


    if($the_lid == 2050649058){
        //echo "<pre>"; print_r($total_money_to_pay_array); echo "</pre>"; exit();
    }

	//yoes 20220211
	$total_money_to_pay = $total_money_to_pay_array[total_money_to_pay]*1;
	$total_principals = $total_money_to_pay_array[total_principals]*1;
	$total_interests = $total_money_to_pay_array[total_interests]*1;
	$paid_amount = getTotalPaidByLid($the_lid)*1;
	
	
	$refund_amount = getRefundAmount($paid_amount ,$the_lid)*1 ;
	//$refund_amount = 0;
	//echo $paid_amount . " vs " .$refund_amount   ;
	//echo "<br>to-pay: " . getGetTotalToPayByLid($the_lid, "_temp");
	//echo "<br>paid: " . getTotalPaidByLid($the_lid);
	
	//echo "select lid from lawfulness where cid = '".$company_row[cid]."' and year = ".($the_year*1)."";
	//echo "<br>";print_r($lawful_34_row);
	
	
	
	//yoes 20220222 --> values to fill....
	/*
	ต้น34/ดอก34 – all/pending
	ต้น33/ดอก33  – all/pending
	ต้น35/ดอก35 – all/pending
	ต้นรวม ดอกรวม  – all/pending	
	*/
			
	//yoes 20220325 --> to test with app
	//$the_output = "someVar = {";
	
	//bank 20220826 add check mode
	if ($the_format && $the_format == 'jsend')
	{
		$log_request = "GET:".print_r($_GET, true)."POST:".print_r($_POST, true);	
		$log_insert_id = insertWsLog(basename(__FILE__, '.php') , $log_request);
		
		$status = 1;
		$status_message = "Success";

		$the_output = "{";
		$the_output .= "
				\"total_money_to_pay\" : $total_money_to_pay
				,\"total_principals\" : $total_principals
				,\"total_interests\" : $total_interests
				, \"paid_amount\" : $paid_amount 	
				, \"refund_amount\" : $refund_amount 				
				
				";

			foreach($total_money_to_pay_array as $x => $val) {
				$the_output .= ", \"$x\" : " . ($val*1);

			}
		
		//other vars for debug purpose
		$the_output .= ", \"the_mode\" : \"$the_mode\"";
		$the_output .= ", \"the_lid\" : \"$the_lid\"";
		$the_output .= ", \"current_date\" : \"$current_date\"";
		
		$the_output .= "}";
		//echo $the_output;
		
		$response = array();
		$response["status"] = formatStatusMessage($status);
		$response["data"] = json_decode($the_output); 
		$response["message"] = $status_message; 
		echo json_encode($response);
		
		updateWsLog($log_insert_id, print_r($response, true));
	
	}else{
		//echo "else here";			
			$the_output = "{";
			
			$the_output .= "
			
					\"total_money_to_pay\" : $total_money_to_pay
					,\"total_principals\" : $total_principals
					,\"total_interests\" : $total_interests
					, \"paid_amount\" : $paid_amount 	
					, \"refund_amount\" : $refund_amount 
					
					";
			
				foreach($total_money_to_pay_array as $x => $val) {
					$the_output .= ", \"$x\" : " . ($val*1);
				}
			
			//other vars for debug purpose
			$the_output .= ", \"the_mode\" : \"$the_mode\"";
			$the_output .= ", \"the_lid\" : \"$the_lid\"";
			$the_output .= ", \"current_date\" : \"$current_date\"";
			
			//new values
			$lawfulness_ratio = getThisYearRatio($lawfulness_row["Year"]);
			$need_for_lawful = getEmployeeRatio($lawfulness_row["Employees"],$lawfulness_ratio);
			
			$the_output .= ", \"lawfulnessEmployees\" : \"".($lawfulness_row[Employees])."\"";
			
			$the_output .= ", \"need_for_lawful\" : \"".($need_for_lawful)."\"";
			
			$the_output .= ", \"LawfulStatus\" : \"".($lawfulness_row[LawfulStatus])."\"";
			$the_output .= ", \"Hire_NumofEmp\" : \"".($lawfulness_row[Hire_NumofEmp])."\"";
			
			$int_curator_user = getNumCuratorFromLid($the_lid, 0, $the_mode);
			
			$the_output .= ", \"curator_user\" : \"".($int_curator_user)."\"";
			$the_output .= ", \"curator_usee\" : \"".(getNumCuratorUseeFromLid($the_lid, 0, $the_mode))."\"";
			
			$the_output .= ", \"need_to_pay\" : \"".($need_for_lawful-$lawfulness_row[Hire_NumofEmp]-$int_curator_user)."\"";
			
			
			
			
			
			$the_output .= "}";
			echo $the_output;
			
	}
	
?>