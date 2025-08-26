<?php

	

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	
	$the_id = "5200501031625";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	
	//calculate years of age (input string: YYYY-MM-DD)
	function birthday ($birthday){
		list($day,$month,$year) = explode("-",$birthday);
		$year = $year  - 543;
		//echo $year;
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if ($day_diff < 0 || $month_diff < 0)
		  $year_diff--;
		return $year_diff;
	}

	
	//yoes 20150923 -> use webservice instead
	$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE01&CARD_ID=$the_id";
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 4);
	$json = curl_exec($ch);
	if(!$json) {
		//echo curl_error($ch);
		echo "json_error";
		exit();
	}
	curl_close($ch);
	
	
	//
	$moomin_array = json_decode($json,true);
	
	//print_r($moomin_array["rows"]); exit();
	
	$output_array = $moomin_array["rows"][0];	
	//print_r($output_array);	
	//echo "<br>".$output_array["FIRST_NAME_THAI"];
	
	
	$the_output = "someVar = { 
					'FIRST_NAME_THAI' : '".$output_array[FIRST_NAME_THAI]."' 					
					,'LAST_NAME_THAI' : '".$output_array[LAST_NAME_THAI]."'
					,'SEX_CODE' : '".$output_array[SEX_CODE]."'
					,'BIRTH_DATE' : '".birthday($output_array[BIRTH_DATE])."'
					,'DEFORM_ID' : '".$output_array[DEFORM_ID]."'
					,'PREFIX_NAME_ABBR' : '".$output_array[PREFIX_NAME_ABBR]."'
					
					}";
					
	//echo $the_output; exit();					
			
	//if($the_count > 0){		
	if(strlen($output_array[FIRST_NAME_THAI]) > 0){
		echo $the_output; 	
	}else{
		echo "no_result";
	}

?>