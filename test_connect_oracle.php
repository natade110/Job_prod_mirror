<?php

	//echo "try connecting oracle server for HIRE PROJECT...";
	
	$user = 'nep_app02'; //nep_card
	$password = "nepapp02admin";	//password
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.124.162)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = dfdb)
				)
			)";
			
			
	
			
	$connect = oci_connect($user, $password, $db, "TH8TISASCII");
	
	if($connect){
		echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		echo "<br><font color='red'>connection failed!</font>";
		$m = oci_error();
	    echo $m['message'], "\n";
		exit;
	}

?>...