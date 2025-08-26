<meta charset=tis-620>
<?php

	echo "try connecting oracle server for HIRE PROJECT...";
	
	$user = 'opp$_dba'; //nep_card
	$password = "password";	//password
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.3.13)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = orcl)
				)
			)";
	// print "user $user password $password db $db ";		
	$connect = ocilogon($user, $password, $db);
	
	//$connect = oci_connect($user, $password, '\\192.168.3.13:1521/orcl');
	
	if($connect){
		echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		echo "<br><font color='red'>connection failed!</font>";
		$e = oci_error();
		print htmlentities($e['message']);
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);	
		exit;
	}

	//then show all tables
	echo "<br>=================<br>all tables in this DB is:";
	
	$s = oci_parse($connect, "select * from MN_DES_PERSON where rownum <= 20");
	oci_execute($s, OCI_DEFAULT);
	while (oci_fetch($s)) {
		echo "<br>" . oci_result($s, "PERSON_CODE") ." ". oci_result($s, "FIRST_NAME_THAI") . " " . oci_result($s, "LAST_NAME_THAI"). " " . oci_result($s, "SEX_CODE"). " " . oci_result($s, "BIRTH_DATE"). " " . oci_result($s, "MAIMAD_ID");
	}


?>
