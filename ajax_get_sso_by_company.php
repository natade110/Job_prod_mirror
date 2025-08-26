<?php

	//include "db_connect.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	if($_POST["name"] == "John" && $_POST["location"] == "Boston"){
		header("Access-Control-Allow-Origin: *");
	}
	
	$the_id = "5200501031625";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"]*1;
		$CompanyCode = $_POST["CompanyCode"]*1;
	}elseif($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"]*1;
		$CompanyCode = $_GET["CompanyCode"]*1;
	}else{
	
		echo "..";
		exit();	
		
	}
	
	
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	function formatEmployStatusDesc($what){
	
		$to_show = "-";
		
		switch ($what){
			case "1" : $to_show = "จ้างงาน"; break;
			case "0" : $to_show = "ไม่ได้จ้างงาน"; break;
			
		}
		
		return $to_show;
		
	}
	
	
	function formatDate($date_time, $have_space = 1, $show_time = 0){

		if(!$date_time){
			return "0000-00-00";	
		}
		
		$date_time = str_replace('/', '-', $date_time);
	
		if($date_time != "0000-00-00"){
		   $this_selected_year = date("Y", strtotime($date_time));
		   $this_selected_month = date("m", strtotime($date_time));
		   $this_selected_day = date("d", strtotime($date_time));
	   }else{
		   $this_selected_year = 0;
		   $this_selected_month = 0;
		   $this_selected_day = 0;
	   }
		
		
		$date_thai = ($this_selected_year-543) . "-" . $this_selected_month . "-" . $this_selected_day;
	
		return $date_thai;
	
	}
	
	
	function show($obj,$k) {
		//return "$k: ".$obj->$k."<br>";
		return $obj->$k;
	}
	
	function print_xml($title,$xml){
		echo "<b>$title</b></br><hr>";
		echo xml_highlight($xml);
		echo "<br><br>";
	
	}
	
	
	
	//yoes 20170909 -- instead of showing output -> show seleting table insteae
					
	//echo $the_output; exit();					
			
	//if($the_count > 0){			
	
	header('Content-Type: text/html; charset=utf-8');
	$wsdl = "sso/EmployeeEmployments.wsdl";
	$options = array(
		"trace"         => 1, 
		"encoding"	=> "utf-8",
		'location' => 'https://wsg.sso.go.th/DBforService/services/EmployeeEmployments'	
	);
	
	$username = "deptest";
	$password = "vLNfg0cS";	
	$ssoNum = $the_id;
	
	
	$client = new SoapClient($wsdl,$options);
	try {
		$result = $client->getServ38($username,$password,$ssoNum);
		//print_xml("REQUEST",$client->__getLastRequest());
		//print_xml("RESPONSE",$client->__getLastResponse());
	}
	catch(SoapFault  $e){
		echo $e->getMessage;
		//print_xml("REQUEST",$client->__getLastRequest());
		//print_xml("RESPONSE",$client->__getLastResponse());
	}

	
	foreach($result->result->employments as $employment){
		
		$seq++;
		
		echo "<br>";
		
		echo show($employment,"accNo");
		
		echo " - ";
		
		$empResignDate = show($employment,"empResignDate");
		//echo  formatDateThai($empResignDate);
		echo $empResignDate . " : " . formatDate($empResignDate);		
		/**/
		
		if(show($employment,"accNo") == $CompanyCode){
			echo "<br>";
			echo "end date is: " . formatDate($empResignDate);
		}
		
	}
	
?>


	