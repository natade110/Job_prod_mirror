<?php

	include "db_connect.php";
	include "session_handler.php";
	
	error_reporting(1);
	
	ini_set('max_execution_time', 300);
	ini_set("memory_limit","256M");
	
	
	
	//$file = "nay301-mini-to-test.txt";
	$file = "nay301-large.txt";
	
	$handle = fopen($file, "r");
	
	
	//file is ok -> delete current temp table to prepare file import
	 
	
	
	
	//while(!feof($handle) && $lineall <= 500000){
	//while(!feof($handle) && $lineall <= 50){
	while(!feof($handle)){
	 // $line = utf8_encode(fgets($handle));
	 // $line = to_utf(fgets($handle));
	  $line = fgets($handle);
	  $linecount++;
	  $lineall++;
	  
	  
	  //for each line -> try echo it out
	  //echo "<br>$line";
	  
		//for_each_line_postion -> explode it by len
		/*
		$company_code = doCleanInput(to_utf(trim(substr($line, 0, 10))));
		$branch_code = doCleanInput(to_utf(trim(substr($line, 10, 6))));
		$company_type_code = doCleanInput(to_utf(trim(substr($line, 16, 2))));
		$company_name_thai = doCleanInput(to_utf(trim(substr($line, 18, 50))));
		$address_1 = doCleanInput(to_utf(trim(substr($line, 68, 30))));
		
		$sub_district = doCleanInput(to_utf(trim(substr($line, 98, 30))));
		$district = doCleanInput(to_utf(trim(substr($line, 128, 20))));
		$province = doCleanInput(to_utf(trim(substr($line, 148, 20))));
		$zip = doCleanInput(to_utf(trim(substr($line, 168, 5))));
		$telephone = doCleanInput(to_utf(trim(substr($line, 173, 10))));
		
		$business_type_code = doCleanInput(to_utf(trim(substr($line, 183, 4))));		
		$employees = doCleanInput(to_utf(trim(substr($line, 187, 5))));
		*/
		
		
		$data .= '"'. doCleanInput(to_utf(trim(substr($line, 0, 10)))) . '"';
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 10, 6)))) . '"';
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 16, 2)))) . '"';
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 18, 50)))) . '"';
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 68, 30)))) . '"';
		
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 98, 30)))) . '"';
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 128, 20)))) . '"';
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 148, 20)))) . '"';
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 168, 5)))) . '"';
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 173, 10)))) . '"';
		
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 183, 4)))) . '"';		
		$data .= ',"' . doCleanInput(to_utf(trim(substr($line, 187, 5)))) . '"';
		
		$data .= "\r\n";
		  
	  
	  
	}
	
	
	echo  $linecount;
	
	
	//echo $data;
	file_put_contents("nay301-mini-2.txt", $data);
	
?>