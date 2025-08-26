<?php

	/* comments below are for off-line testing
	$the_output = "someVar = { 
					'FIRST_NAME_THAI' : 'Prachaya' 					
					,'LAST_NAME_THAI' : 'Daruthep'
					,'SEX_CODE' : 'M'
					,'BIRTH_DATE' : '28'
					,'DEFORM_ID' : '16'
					,'PREFIX_NAME_ABBR' : 'Mr.'
					}";
	
					
	//echo $the_output; exit();	
	*/
	
	//echo "try connecting oracle server for HIRE PROJECT...";
	
	$user = 'opp$_dba'; //nep_card
	$password = "password";	//password
	$db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 203.154.91.67)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = orainst1)
				)
			)";
			
	$connect = oci_connect($user, $password, $db, "TH8TISASCII");
	
	if($connect){
		//echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		//echo "<br><font color='red'>connection failed!</font>";
		exit;
	}
	
	//exit();

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	
	//$the_id = "5521200018059";
	$the_id = "";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	$s = oci_parse($connect, "select * from MN_DES_PERSON where PERSON_CODE = '$the_id'");
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
		
		
		$SEX_CODE = oci_result($s, "SEX_CODE");
		$BIRTH_DATE = oci_result($s, "BIRTH_DATE");
		
		$the_prefix = oci_result($s, "PREFIX_CODE_THAI");
		
	}
	
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


	$BIRTH_DATE = birthday($BIRTH_DATE);
	
	$s = oci_parse($connect, "select * from MN_DES_DEFORMED where MAIMAD_ID = '$the_maimad'");
	oci_execute($s, OCI_DEFAULT);
	while (oci_fetch($s)) {
		//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
		
		$DEFORM_ID = oci_result($s, "DEFORM_ID");
	}


	//get prefix
	$s = oci_parse($connect, "select * from BS_PREFIX where PREFIX_CODE = '$the_prefix'");
	oci_execute($s, OCI_DEFAULT);
	while (oci_fetch($s)) {
		//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
		
		$PREFIX_NAME_ABBR = oci_result($s, "PREFIX_NAME_ABBR");
		$PREFIX_NAME_ABBR = iconv("WINDOWS-874", "UTF-8", ($PREFIX_NAME_ABBR));
	}


	$the_output = "someVar = { 
					'FIRST_NAME_THAI' : '$FIRST_NAME_THAI' 					
					,'LAST_NAME_THAI' : '$LAST_NAME_THAI'
					,'SEX_CODE' : '$SEX_CODE'
					,'BIRTH_DATE' : '$BIRTH_DATE'
					,'DEFORM_ID' : '$DEFORM_ID'
					,'PREFIX_NAME_ABBR' : '$PREFIX_NAME_ABBR'
					}";
			
	if($the_count > 0){		
		echo $the_output; 	
	}else{
		echo "no_result";
	}

?>