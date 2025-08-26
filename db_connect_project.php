<?php

	echo "try connecting oracle server for HIRE PROJECT...";
	
	$user = 'NEP_APP02'; //nep_card
	$password = "Test@1234";	//password
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 203.150.53.105)(PORT = 5050))
				)
				(CONNECT_DATA =
					(SERVICE_NAME  = xe)
				)
			)";
			
			
	//echo $db;
	//$conn = oci_connect("username","pwd", "123.123.123.123:1521/foo");

	//$connect = oci_connect($user, $password, "203.150.53.105:5050", "TH8TISASCII");
	$connect = oci_connect($user, $password, $db, "TH8TISASCII");
	
	if($connect){
		echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		//echo "test";
		echo "<br><font color='red'>connection failed!</font>";
		$m = oci_error();
	    echo $m['message'], "\n";
		exit;
	}
	
	
	
?>...