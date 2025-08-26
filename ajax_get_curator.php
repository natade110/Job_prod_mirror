<?php

	//echo "try connecting oracle server for HIRE PROJECT...";
	
	$user = 'opp$_dba'; //nep_card
	$password = "password";	//password
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.1.10)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = oppddb)
				)
			)";
			
			
	//new connection as of 20131010
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.3.13)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = ORCL)
				)
			)";
			
	$connect = oci_connect($user, $password, $db, "TH8TISASCII");
	
	if($connect){
		echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		echo "<br><font color='red'>connection failed!</font>";
		exit;
	}

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
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
	
	
	
	$the_id = "3100905673020";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	$s = oci_parse($connect, "select * from MN_DES_CURATOR where PERSON_CODE = '$the_id'");
	//echo "select * from MN_DES_CURATOR where PERSON_CODE = '$the_id'";
	oci_execute($s, OCI_DEFAULT);
	while (oci_fetch($s)) {
	
		$the_count++;
		//echo "<br>" . oci_result($s, "PERSON_CODE") ." ". oci_result($s, "FIRST_NAME_THAI") . " " . oci_result($s, "LAST_NAME_THAI"). " " . oci_result($s, "SEX_CODE"). " " . oci_result($s, "BIRTH_DATE"). " " . oci_result($s, "MAIMAD_ID");
	
		$the_maimad = oci_result($s, "MAIMAD_ID");
		
		//reuslt we need
		//$FIRST_NAME_THAI = "what";
		$FIRST_NAME_THAI = oci_result($s, "FIRST_NAME_THAI");
		$FIRST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($FIRST_NAME_THAI));
		
		$LAST_NAME_THAI = oci_result($s, "LAST_NAME_THAI");
		$LAST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($LAST_NAME_THAI));
		
		$RELATION_OTHER = oci_result($s, "RELATION_OTHER");
		$RELATION_OTHER = iconv("WINDOWS-874", "UTF-8", ($RELATION_OTHER));
		
		$SALARY = oci_result($s, "SALARY");
		
		$ISSUE_DATE = oci_result($s, "ISSUE_DATE");
		//$RELATION_OTHER = iconv("WINDOWS-874", "UTF-8", ($RELATION_OTHER));
		
		
		//$SEX_CODE = oci_result($s, "SEX_CODE");
		//$BIRTH_DATE = oci_result($s, "BIRTH_DATE");
		
		$the_prefix = oci_result($s, "PREFIX_CODE_THAI");
		
		
		$the_maimad = oci_result($s, "MAIMAD_ID");
		
	}
	
	


	//get prefix
	$s = oci_parse($connect, "select * from BS_PREFIX where PREFIX_CODE = '$the_prefix'");
	oci_execute($s, OCI_DEFAULT);
	while (oci_fetch($s)) {
		//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
		
		$PREFIX_NAME_ABBR = oci_result($s, "PREFIX_NAME_ABBR");
		$PREFIX_NAME_ABBR = iconv("WINDOWS-874", "UTF-8", ($PREFIX_NAME_ABBR));
	}


	
	echo "<br>- curator is -> ". $PREFIX_NAME_ABBR . " ".$FIRST_NAME_THAI . " " .$LAST_NAME_THAI. " " .$RELATION_OTHER . " " . $SALARY . " " . $ISSUE_DATE;
	
	
	
	
	/////// now we got all details about curator
	//// now we get maimad
	$ss = oci_parse($connect, "select * from MN_DES_PERSON where MAIMAD_ID = '$the_maimad'");
	oci_execute($ss, OCI_DEFAULT);
	
	echo "<br> This person code have this maimad";
	
	while (oci_fetch($ss)) {
	
		$FIRST_NAME_THAI = oci_result($ss, "FIRST_NAME_THAI");
		$FIRST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($FIRST_NAME_THAI));
		
		$LAST_NAME_THAI = oci_result($ss, "LAST_NAME_THAI");
		$LAST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($LAST_NAME_THAI));
		
		$SEX_CODE = oci_result($ss, "SEX_CODE");
		$BIRTH_DATE = oci_result($ss, "BIRTH_DATE");
		$BIRTH_DATE = birthday($BIRTH_DATE);
		
		$the_prefix = oci_result($ss, "PREFIX_CODE_THAI");
		
		////////
		$sss = oci_parse($connect, "select * from MN_DES_DEFORMED where MAIMAD_ID = '$the_maimad'");
		oci_execute($sss, OCI_DEFAULT);
		while (oci_fetch($sss)) {
			//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
			
			$DEFORM_ID = oci_result($sss, "DEFORM_ID");
		}
	
	}
	
	//get prefix
	$s = oci_parse($connect, "select * from BS_PREFIX where PREFIX_CODE = '$the_prefix'");
	oci_execute($s, OCI_DEFAULT);
	while (oci_fetch($s)) {
		//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
		
		$PREFIX_NAME_ABBR = oci_result($s, "PREFIX_NAME_ABBR");
		$PREFIX_NAME_ABBR = iconv("WINDOWS-874", "UTF-8", ($PREFIX_NAME_ABBR));
	}
	
	echo "<br>- maimad is -> ". $PREFIX_NAME_ABBR . " ".$FIRST_NAME_THAI . " " .$LAST_NAME_THAI . " " . $BIRTH_DATE . " " . $DEFORM_ID ;
	

?>