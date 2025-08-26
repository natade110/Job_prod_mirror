<?php

	
	
	//yoes 20150923 -> use webservice instead
	$url = "http://fund.dep.go.th/jsonService?user=nep&password=rviz3k&cmd=getContractByPsnId&psnId=5710600017146";
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 4);
	$json = curl_exec($ch);
	if(!$json) {
		echo curl_error($ch);
	}
	curl_close($ch);
	
	
	//
	$moomin_array = json_decode($json,true);
	
	//echo "...";
	print_r($moomin_array); exit();
	
	/*
	$output_array = $moomin_array["rows"][0];	
	print_r($output_array);	
	//echo "<br>".$output_array["FIRST_NAME_THAI"];
	
	
	$the_output = "someVar = { 
					'FIRST_NAME_THAI' : '".$output_array[FIRST_NAME_THAI]."' 					
					,'LAST_NAME_THAI' : '".$output_array[LAST_NAME_THAI]."'
					,'SEX_CODE' : '".$output_array[SEX_CODE]."'
					,'BIRTH_DATE' : '".birthday($output_array[BIRTH_DATE])."'
					,'DEFORM_ID' : '".$output_array[DEFORM_ID]."'
					,'PREFIX_NAME_ABBR' : '".$output_array[PREFIX_NAME_ABBR]."'
					
					}";
					
	echo $the_output; 	
	*/
	

?>