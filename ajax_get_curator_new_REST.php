<?php

	include "db_connect.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	
	$the_id = "1111111111111"; // main only
	
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	//overrid value below
	//$the_id = "3249800060424"; // main only
	//$the_id = "3249800005059"; // main and child
	//$the_id = "3249800004444";
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	
	
	
	//echo "hello: ".$the_id; exit();
	
	$curator_parent = doCleanInput($_POST["curator_parent"]);
	
	
	
	
	
	
	
	//-----------------------------------------
	//--------------- try get mn_desperson -> start below
	//-----------------------------------------
	
	
	
	/*$mm = oci_parse($oracle_connect, "select * from MN_DES_PERSON where PERSON_CODE = '$the_id'");
	oci_execute($mm, OCI_DEFAULT);*/
	
	//echo "select * from MN_DES_PERSON where PERSON_CODE = '$the_id'";
	
	//while (oci_fetch($mm)) {
	
	//found this person code
	$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE01&CARD_ID=$the_id";	
	//echo $url;exit();
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
	$output_array = $moomin_array["rows"][0];
		
	if($output_array[MAIMAD_ID]){
		$has_maimad = 1;
	}

	$the_maimad = $output_array[MAIMAD_ID];
	//
	$PERSON_CODE = $output_array["PERSON_CODE"];
	//
	//echo $PERSON_CODE;
	
	$FIRST_NAME_THAI = $output_array["FIRST_NAME_THAI"];
	//$FIRST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($FIRST_NAME_THAI));
	
	$LAST_NAME_THAI =  $output_array["LAST_NAME_THAI"];
	//$LAST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($LAST_NAME_THAI));
	
	$SEX_CODE = $output_array["SEX_CODE"];
	$BIRTH_DATE = $output_array["BIRTH_DATE"];
	$BIRTH_DATE = birthday($BIRTH_DATE);
	
	$the_prefix =  $output_array["PREFIX_CODE_THAI"];
	
	
	$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE02&THE_MAIMAD=$the_maimad";	
	//echo $url;exit();
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
	$output_array = $moomin_array["rows"][0];
	
	
	
	//echo $output_array[DEFORM_ID];exit();
	$DEFORM_ID = $output_array[DEFORM_ID];
	//echo $DEFORM_ID; exit();
	//echo $the_prefix; exit();
	
	if($has_maimad){
		
		$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE03&THE_PREFIX=$the_prefix";	
		//echo $url;exit();
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
		$output_array = $moomin_array["rows"][0];
		
		
		$PREFIX_NAME_ABBR = $output_array["PREFIX_NAME_ABBR"];
		
		///echo $PREFIX_NAME_ABBR; exit();
		
		
		//exit();
		
		if($DEFORM_ID == 1 || $DEFORM_ID == 6 || $DEFORM_ID == 12){
			$DEFORM_ID = "ความพิการทางการเห็น";
		}
		if($DEFORM_ID == 2 || $DEFORM_ID == 7 || $DEFORM_ID == 13){
			$DEFORM_ID = "ความพิการทางการได้ยินหรือสื่อความหมาย";
		}
		if($DEFORM_ID == 3 || $DEFORM_ID == 8 || $DEFORM_ID == 14){
			$DEFORM_ID = "ความพิการทางการเคลื่อนไหวหรือร่างกาย";
		}
		if($DEFORM_ID == 4 || $DEFORM_ID == 9 || $DEFORM_ID == 15){
			$DEFORM_ID = "ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก";
		}
		if($DEFORM_ID == 5 || $DEFORM_ID == 10 || $DEFORM_ID == 16){
			$DEFORM_ID = "ความพิการทางสติปัญญา";
		}
		if($DEFORM_ID == 6 || $DEFORM_ID == 11 || $DEFORM_ID == 17){
			$DEFORM_ID = "ความพิการทางการเีรียนรู้";
		}
		if($DEFORM_ID == 18){
			$DEFORM_ID = "ทางออทิสติก";
		}
	
		//echo $DEFORM_ID; exit();	
	
		$curator_lid = doCleanInput($_POST["curator_lid"]);
		
		//echo $curator_lid; exit();	
		//$curator_last_id = mysql_insert_id();	
		
		$the_output =
		
		'
		
			"curator_name" : "'.$PREFIX_NAME_ABBR . ' '. $FIRST_NAME_THAI . ' '. $LAST_NAME_THAI .'"
					, "curator_idcard" : "'.$PERSON_CODE .'"
					, "curator_gender" : "'. strtolower($SEX_CODE).'"
					, "curator_age" : "'.$BIRTH_DATE .'"
					, "curator_lid" : "'.$curator_lid .'"
					
					, "curator_parent" : "'.$curator_parent .'"					
					, "curator_disable_desc" : "'.$DEFORM_ID .'"					
					, "curator_is_disable" : "1"	
		';
		
		echo "{".preg_replace( "/\r|\n/", "", $the_output )."}";
		
		//echo "someVar = { ".$the_output."}";
		
		
	}		
	
	
	
	//-----------------------------------------
	//--------------- try get mn_desperson -> ended above
	//-----------------------------------------
	
	
	
	//-----------------------------------------
	//--------------- try get curator -> start below -> only start this if not have maimad from above
	//-----------------------------------------
	
	
	if(!$has_maimad && !$curator_parent){ //only do this if didn't have maimad, and have no parent
		
		
		/*$oci_sql = "select * from MN_DES_CURATOR where PERSON_CODE = '$the_id'";
		//echo $oci_sql;
		$s = oci_parse($oracle_connect, $oci_sql);
		oci_execute($s, OCI_DEFAULT);
		*/
		
		
		//found this person code
		$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE04&PERSON_CODE=$the_id";	
		//echo $url;exit();
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
		$output_array = $moomin_array["rows"][0];
		
		
		
		//while (oci_fetch($s)) {
		
		
		$FIRST_NAME_THAI = $output_array["FIRST_NAME_THAI"];
		//$FIRST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($FIRST_NAME_THAI));
		
		$LAST_NAME_THAI = $output_array["LAST_NAME_THAI"];
		//$LAST_NAME_THAI = iconv("WINDOWS-874", "UTF-8", ($LAST_NAME_THAI));
		
		$RELATION_OTHER = $output_array["RELATION_OTHER"];
		//$RELATION_OTHER = iconv("WINDOWS-874", "UTF-8", ($RELATION_OTHER));
		
		$SALARY = $output_array["SALARY"];
		
		$ISSUE_DATE = $output_array["ISSUE_DATE"];
		
		$the_prefix = $output_array["PREFIX_CODE_THAI"];
				
		$the_maimad = $output_array["MAIMAD_ID"];
		
		
		$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE03&THE_PREFIX=$the_prefix";	
		//echo $url;exit();
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
		$output_array = $moomin_array["rows"][0];
		
		
		$PREFIX_NAME_ABBR = $output_array["PREFIX_NAME_ABBR"];
	
		
		if($FIRST_NAME_THAI){
		
			//got this curator info
			//-> insert it into curator
			
			$the_curator_gender = "m";
			
			if($PREFIX_NAME_ABBR == "นาง" || $PREFIX_NAME_ABBR == "นางสาว" || $PREFIX_NAME_ABBR == "น.ส."){
				$the_curator_gender = "f";
			}
			
			$curator_lid = doCleanInput($_POST["curator_lid"]);
			
			$sql = "
				insert into 
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender						
						,curator_lid
						,curator_parent
						
						,curator_event_desc						
						, curator_value
						
					)values(
					
					
						'$PREFIX_NAME_ABBR $FIRST_NAME_THAI $LAST_NAME_THAI'
						,'$the_id'
						,'$the_curator_gender'						
						,'$curator_lid'
						,'0'
						
						,'$RELATION_OTHER'						
						, '$SALARY'
					
					)
					
				";
				
			//echo $sql;
			
			$the_output =
		
			"
			
						'curator_name' : '$PREFIX_NAME_ABBR $FIRST_NAME_THAI $LAST_NAME_THAI'
						, 'curator_idcard' : '$the_id'
						, 'curator_gender' : '$the_curator_gender'
						, 'curator_lid' : '$curator_lid'
						, 'curator_parent' : '0'
						
						, 'curator_event_desc' : '$RELATION_OTHER'					
						, 'curator_value' : '$curator_value'					
						
			";
			
			//next try MAIMAD DATA
			/*
			$mm = oci_parse($oracle_connect, "select * from MN_DES_PERSON where MAIMAD_ID = '$the_maimad'");
			oci_execute($mm, OCI_DEFAULT);*/
			
			$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE05&THE_MAIMAD=$the_maimad";	
			//echo $url;exit();
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
			$output_array = $moomin_array["rows"][0];
			
			
			
			//while (oci_fetch($mm)) {
		
			//
			$PERSON_CODE = $output_array["PERSON_CODE"];
			//
			
			$FIRST_NAME_THAI = $output_array["FIRST_NAME_THAI"];
			
			$LAST_NAME_THAI =  $output_array["LAST_NAME_THAI"];
			
			$SEX_CODE = $output_array["SEX_CODE"];
			$BIRTH_DATE = $output_array["BIRTH_DATE"];
			$BIRTH_DATE = birthday($BIRTH_DATE);
			
			$the_prefix =  $output_array["PREFIX_CODE_THAI"];
			
			////////
			/*$mmm = oci_parse($oracle_connect, "select * from MN_DES_DEFORMED where MAIMAD_ID = '$the_maimad'");
			oci_execute($mmm, OCI_DEFAULT);
			while (oci_fetch($mmm)) {
				//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
				
				$DEFORM_ID = oci_result($mmm, "DEFORM_ID");
			}*/
			$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE02&THE_MAIMAD=$the_maimad";	
			//echo $url;exit();
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
			$output_array = $moomin_array["rows"][0];			
			
			
			//echo $output_array[DEFORM_ID];exit();
			$DEFORM_ID = $output_array[DEFORM_ID];
			
			//}
			
			//get prefix
			/*$s = oci_parse($oracle_connect, "select * from BS_PREFIX where PREFIX_CODE = '$the_prefix'");
			oci_execute($s, OCI_DEFAULT);
			while (oci_fetch($s)) {
				//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
				
				$PREFIX_NAME_ABBR = oci_result($s, "PREFIX_NAME_ABBR");
				$PREFIX_NAME_ABBR = iconv("WINDOWS-874", "UTF-8", ($PREFIX_NAME_ABBR));
			}*/
			
			$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE03&THE_PREFIX=$the_prefix";	
			//echo $url;exit();
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
			$output_array = $moomin_array["rows"][0];
			
			
			$PREFIX_NAME_ABBR = $output_array["PREFIX_NAME_ABBR"];
			
			
			//exit();
			
			if($DEFORM_ID == 1 || $DEFORM_ID == 6 || $DEFORM_ID == 12){
				$DEFORM_ID = "ความพิการทางการเห็น";
			}
			if($DEFORM_ID == 2 || $DEFORM_ID == 7 || $DEFORM_ID == 13){
				$DEFORM_ID = "ความพิการทางการได้ยินหรือสื่อความหมาย";
			}
			if($DEFORM_ID == 3 || $DEFORM_ID == 8 || $DEFORM_ID == 14){
				$DEFORM_ID = "ความพิการทางการเคลื่อนไหวหรือร่างกาย";
			}
			if($DEFORM_ID == 4 || $DEFORM_ID == 9 || $DEFORM_ID == 15){
				$DEFORM_ID = "ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก";
			}
			if($DEFORM_ID == 5 || $DEFORM_ID == 10 || $DEFORM_ID == 16){
				$DEFORM_ID = "ความพิการทางสติปัญญา";
			}
			if($DEFORM_ID == 6 || $DEFORM_ID == 11 || $DEFORM_ID == 17){
				$DEFORM_ID = "ความพิการทางการเีรียนรู้";
			}
			if($DEFORM_ID == 18){
				$DEFORM_ID = "ทางออทิสติก";
			}
		
			
			
			$sql = "
				insert into 
					curator(
					
						curator_name
						,curator_idcard
						,curator_gender
						,curator_age
						,curator_lid
						
						,curator_parent											
						,curator_disable_desc						
						, curator_is_disable
						
					)values(
					
					
						'$PREFIX_NAME_ABBR $FIRST_NAME_THAI $LAST_NAME_THAI'
						,'$PERSON_CODE'
						,'". strtolower($SEX_CODE)."'
						,'$BIRTH_DATE'
						,'$curator_lid'
						
						,'$curator_last_id'						
						,'$DEFORM_ID'						
						, '1'					
					
					)
					
				";
				
				//echo $sql;
			$the_output .=
		
			"
			
						,'child_curator_name' : '$PREFIX_NAME_ABBR $FIRST_NAME_THAI $LAST_NAME_THAI'
						, 'child_curator_idcard' : '$PERSON_CODE'
						, 'child_curator_gender' : '". strtolower($SEX_CODE)."'
						, 'child_curator_age' : '$BIRTH_DATE'
						, 'child_curator_lid' : '$curator_lid'
						
						, 'child_curator_parent' : '$curator_last_id'					
						, 'child_curator_disable_desc' : '$DEFORM_ID'					
						, 'child_curator_is_disable' : '1'					
						
			";
			
			//echo "{ ".$the_output."}";
			echo "{".preg_replace( "/\r|\n/", "", str_replace("'", '"',$the_output))."}";
		
		}else{
		
			echo "no_result";
		
		}
	
	
	}

?>