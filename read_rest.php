<?php

	$url = "http://61.19.50.29/ws/wsjson?user=test&password=test123&queryCode=HIRE01&CARD_ID=5200501031625";
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 4);
	$json = curl_exec($ch);
	if(!$json) {
		echo curl_error($ch);
	}
	curl_close($ch);
	
	//print_r(json_decode($json));
	
	//exit();
	
	$moomin_array = json_decode($json,true);
	
	//print_r($moomin_array["rows"]);
	
	//print_r($moomin_array["rows"][0]);
	
	$output_array = $moomin_array["rows"][0];
	
	print_r($output_array);
	
	echo "<br>".$output_array["FIRST_NAME_THAI"];
	
	exit();
	
	for($i=1;$i<count($output_array);$i++){
	
		echo "<br>-".$output_array[$i];
		
	}
	
	

?>