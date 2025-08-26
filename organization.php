<?php

	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";
	
	
	
		
	////manage year
	//try to show last "document_requests" entry
	if($_POST["ddl_year"]){
		//echo "in the loop";
		$this_year = doCleanInput($_POST["ddl_year"]);
		$conditions = " and Year = '$this_year'";
		
	}elseif($_GET["ddl_year"]){
		$this_year = doCleanInput($_GET["ddl_year"]);
		$conditions = " and Year = '$this_year'";
	}elseif($_GET["year"]){
		$this_year = doCleanInput($_GET["year"]);
		$conditions = " and Year = '$this_year'";
	}elseif($_POST["year_curator"]){
		//for curator's auto submit
		$this_year = doCleanInput($_POST["year_curator"]);
		$conditions = " and Year = '$this_year'";
	}else{
	
		//$this_year = strtotime(date('Y'),date('Y')."+1 year");
		//$this_year = date ( 'Y', strtotime ( '+123 day' . date('Y') ) );
		
		if(date("m") >= 9){
			$this_year = date("Y")+1; //new year at month 9
		}else{
			$this_year = date("Y");
		}
		
		$conditions = " and Year = '$this_year'";
	}
	
	
	//echo $this_year;
	
	$this_lawful_year = $this_year;
	
	//yoes 20160615
	if($this_lawful_year > 3000){
		$this_lawful_year -= 1000;
		$is_merged = 1;	
	}
	
	
	
	
		
	//current mode
	if($_GET["mode"] == "new"){
		$mode = "new";	
		
		//yoes 20151116 -- disable this for company user
		if($sess_accesslevel == 4){			
			header("location: organization.php");			
		}
		
		
		//yoes 20160203 -- default some vars?
		if($_GET[companycode]){
			$output_values[CompanyCode] = $_GET[companycode]*1;
			$output_values[BranchCode] = "000000";
		}
		
	}elseif(is_numeric($_GET["id"])){
		
		
		//yoes 20141103
		//also check if this is owner
		if($sess_accesslevel == 4 && $sess_meta != $_GET["id"]){
			//cant edit this	
			header("location: index.php");
			exit();
		}
		
		
		
		$mode = "edit";
		$this_id = $_GET["id"];
		$this_cid = $this_id;
		$this_focus = $_GET["focus"];
		
		
		$post_row = getFirstRow("select * 
								from 
									company
								where 
									cid  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'CID'
						,'Employees'
						,'CompanyCode'
						,'CompanyNameThai'
						,'CompanyNameEng'
						,'Address1'
						
						,'Moo'
						,'Soi'
						,'Road'
						,'Subdistrict'
						,'District'
						
						,'Province'
						,'Zip'
						,'Telephone'
						,'email'
						,'TaxID'
						
						,'CompanyTypeCode'
						,'BusinessTypeCode'
						,'BranchCode'
						,'org_website'
						
						,'LawfulFlag'
						,'Status'
						
						,'NoRecipient'
						,'NoRecipient_remark'
						
						,'ContactPerson1'
						,'ContactPhone1'
						,'ContactEmail1'
						,'ContactPosition1'
						,'ContactPerson2'
						,'ContactPhone2'
						,'ContactEmail2'
						,'ContactPosition2'
						
						
						);
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			$output_values[$output_fields[$i]] .= (doCleanOutput($post_row[$output_fields[$i]]));
		}
		
		//yoes 20200429
		//if CID == not exists then -> redirect back to index page--
		if(!$output_values[CID]){
			header("location: index.php");
		}
		
		//yoes 20160511
		//handles special type of company
		//also change company words there		
		
		//yoes 20160610 -- check if this company is a school?
		
		$is_school = getFirstItem("select meta_value from company_meta where meta_cid  = '$this_id' and meta_for = 'is_school'");
		
		if($sess_is_gov){
			$is_school = 0;	
		}
		
		//if($output_values[CompanyTypeCode] == "07"){
		if($is_school){
			
			//yoes 20160622 -> removed this
			//$the_company_word = "โรงเรียน";
			//$the_employees_word = "ครูและลูกจ้าง";
			
			
			//school have meta data
			$meta_result = mysql_query("
								select * 
								from 
									company_meta
								where 
									meta_cid  = '$this_id'
								");
			
			while($meta_row = mysql_fetch_array($meta_result)){			
				//
				$output_values[$meta_row[meta_for]] = (doCleanOutput($meta_row[meta_value]));				
			}
			
		}
				
		
		
	}else{
		header("location: index.php");
	}	
	
	
	
	//yoes 20160118 -- also check if case's closed
	$this_lawful_row = getFirstRow("select lid,close_case_date, reopen_case_date from lawfulness where Year = '$this_lawful_year' and CID = '$this_id'");
	if($this_lawful_row[close_case_date] > $this_lawful_row[reopen_case_date]){
		$case_closed = 1;					
		//echo "--> $case_closed <--";			
	}
	
	
	
	//yoes 20160114'
	//also check if you really should see this? for พมจ.
	//yoes 20160809 --> change this so พมจ can see but can't edit
	if($sess_accesslevel == 3 && $output_values[Province] != $sess_meta && $mode != "new"){			
		//header("location: index.php");		exit();
		//read only
		$is_read_only = 1;
		// yoes 20160908 -- indicates that this can't edit company table
		$is_company_table_read_only = 1; 
	}




	//yoes 20151208
	//also check if lawfulness exists for this year
	$have_lawful_record = getFirstItem("select count(*) from lawfulness where cid = '".$output_values["CID"]."' and year = '$this_year'");	

	//echo $have_lawful_record ;
	
	//yoes 20160125 --> add "read-only" mode
	//yoes 20160215 --> more "read-only" conditions
	if($sess_accesslevel == 2 && $this_year == 2011){
		$is_read_only = 1;
	}
	
	//yoes 20160629 -- open this for year 2012 and up as per mantis no 153
	if($sess_accesslevel == 8 && $this_year > 2012){
		$is_read_only = 1;
	}
	
	//yoes 20160613 --> more "read-only" mode
	if($this_year > 3000){
		$is_read_only = 1;
	}
	
	
	
	//yoes 200181024 - even more radonly options
	//incase of "ส่งฟ้องแล้ว"
	$this_lid = $this_lawful_row[lid];
	
	//resetLawfulnessByLID($this_lid);
	
	//yoes 20200624 - set beta status
	if($_GET[beta_on]){
		
		$beta_sql = "
			replace into
				lawfulness_meta(
					meta_lid
					, meta_for
					, meta_value
				)values(
					'$this_lid'
					, 'is_beta_2020'
					, '1'
				
				)
		
		";
		
		mysql_query($beta_sql);
		
		//yoes 20200817 -- only sync payment on FIRST TIME
		//syncPaymentMeta($this_lid);
		
	}
	
	//yoes 20200624
	if($_GET[beta_off]){
		
		$beta_sql = "
			replace into
				lawfulness_meta(
					meta_lid
					, meta_for
					, meta_value
				)values(
					'$this_lid'
					, 'is_beta_2020'
					, '0'
				
				)
		
		";
		
		//echo $beta_sql; exit();
		
		mysql_query($beta_sql);
		
	}
	
	
	//yoes 20211108
	//ma 64 opt- status
	if($_GET[optoutma]){
		
		$beta_sql = "
			replace into
				generic_meta(
					meta_id
					, meta_for
					, meta_value
				)values(
					'$sess_userid'
					, 'optoutma'
					, '1'
				
				)
		
		";
		
		//echo $beta_sql; exit();
		mysql_query($beta_sql);
		
	}
	
	if($_GET[optinma]){
		
		$beta_sql = "
			replace into
				generic_meta(
					meta_id
					, meta_for
					, meta_value
				)values(
					'$sess_userid'
					, 'optoutma'
					, '0'
				
				)
		
		";
		
		//echo $beta_sql; exit();
		mysql_query($beta_sql);
		
	}
	
	$optoutma = getFirstItem("select meta_value from generic_meta where meta_id = '$sess_userid' and meta_for = 'optoutma'");
	
	//yoes 20200624 -- init "new" lawfulness 33 here
	//if($_GET[beta_on]){
	//yoes 20201207 -- semi-putting this live
	//any company that didn't have beta-on is will now be beta on
	if(!getLidBetaStatus($this_lid)){
		
		generate33PrincipalFromLID($this_lid);
		//yoes 20200817 -- only sync payment on FIRST TIME
		syncPaymentMeta($this_lid);
		generate33InterestsFromLID($this_lid);
		generate35PrincipalFromLID($this_lid);
		syncPaymentMeta($this_lid, 0, "m35");
		generate35InterestsFromLID($this_lid);
		
		$_GET[beta_on] = 1;
		
		//update the flag
		$beta_sql = "
			replace into
				lawfulness_meta(
					meta_lid
					, meta_for
					, meta_value
				)values(
					'$this_lid'
					, 'is_beta_2020'
					, '1'
				
				)
		
		";
		
		mysql_query($beta_sql);
		
		$is_beta_mode = 1;
		
	}elseif(getLidBetaStatus($this_lid)){
		
		$is_beta_mode = 1;
		
		//init new lawfulness data according to new code...
		//generate new principal...
		generate33PrincipalFromLID($this_lid);
		//sync payment meta from old to new (if applicable)
		//yoes 20200817 -- only sync payment on FIRST TIME
		//syncPaymentMeta($this_lid);
		//run interests
		generate33InterestsFromLID($this_lid);
		//exit();
		
		//yoes 20200626 -- also do for m35
		generate35PrincipalFromLID($this_lid);
		//syncPaymentMeta($this_lid, 0, "m35");
		generate35InterestsFromLID($this_lid);
	}
	
	
	
	
	$courted_flag = getLawfulnessMeta($this_lid,"courted_flag");
	
	if(!in_array($sess_accesslevel, array(1,8)) && $this_year <= 2012 && $courted_flag){
		$is_read_only = 1;
	}
	
	
	
///

	//yoes 20181024 - ... check for non-read-only here
	//"ส่งฟ้องแล้ว" editable by admin and งานคดี
	if($courted_flag && in_array($sess_accesslevel, array(1,8))){
		
		$is_read_only = 0;
		//echo "is_read_only = " . $is_read_only;
		
	}else{
		//echo "is_read_only = " . $is_read_only;
	}
	

///curator thingie


///curator thingie
$is_on_ictmerlin = 0;
//echo "ictmerlin: ".$is_on_ictmerlin; exit();

if($_POST["btn_get_curator_data"] && $is_on_ictmerlin){

	$curator_lid = doCleanInput($_POST["curator_lid"]);

	//use dummy data instead
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
				
				
					'นาย ผู้ใช้สิทธิ " . rand(100,999) . "'
					,'1234567890" . rand(100,999) . "'
					,'m'
					,'35'
					,'$curator_lid'
					
					,'0'					
					,'ความพิการทางสติปัญญา'					
					, '0'					
				
				)
				
			";
			
			//echo "what: ".$sql; exit();
			
		mysql_query($sql);
		
		
		//
		$curator_last_id = mysql_insert_id();		
		
		//try simulate add 2 curators
		for($i = 1;$i <= 1; $i++){
			
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
					
					
						'นาย ถูกใช้สิทธิ_$i " . rand(100,999) . "'
						,'1234567890" . rand(100,999) . "'
						,'m'
						,'20'
						,'$curator_lid'
						,'$curator_last_id'
						
						
						,'ความพิการทางสติปัญญา'
						
						, '1'					
					
					)
					
				";
				
				//echo $sql;
				
				mysql_query($sql);
		
		}
		
		
		
		//if($this_year >= 2013){
			
		//yoes 2015122 --- do this regardless of year
		if(1 == 1){
		
			//only do auto post if >= year 2013
			$_GET["auto_post"] = 1;
			//also add curator flag
			$_GET["curate"] = "curate";
			
			//also pre-populate the "just inserted curator"
			$_GET["curator_id"] = $curator_last_id;
			
		}



}elseif($_POST["btn_get_curator_data"]){

	//echo "try get curator data, instead of manual input!";
	
	//yoes 20150928 --- change this to webservice instead
	
	
	/*$oracle_user = 'opp$_dba'; //nep_card
	$oracle_password = "password";	//password
	$oracle_db = "(DESCRIPTION =
				(ADDRESS_LIST = 
					(ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.3.13)(PORT = 1521))
				)
				(CONNECT_DATA =
					(SERVICE_NAME = ORCL)
				)
			)";
			
	$oracle_connect = oci_connect($oracle_user, $oracle_password, $oracle_db, "TH8TISASCII");
	
	if($oracle_connect){
		//echo "<br><font color='green'>connection estrabished!</font>";
	}else{
		//echo "<br><font color='red'>connection failed!</font>";
		//exit;
	}*/
	
	$the_id = "3520100391761";
	if($_POST["curator_idcard"] && is_numeric($_POST["curator_idcard"])){
		$the_id = $_POST["curator_idcard"];
	}else{
	
		$the_id = "";
		for($i=1;$i<=13;$i++){
			$the_id .= $_POST["id_".$i]; 
		}
		
	}
	
	$the_id = addslashes(substr($the_id,0,13));
	
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
	
	/*
	echo "<br>".$the_maimad;
	echo "<br>".$PERSON_CODE;
	echo "<br>".$FIRST_NAME_THAI;
	echo "<br>".$LAST_NAME_THAI;
	echo "<br>".$SEX_CODE;
	echo "<br>".$BIRTH_DATE;
	echo "<br>".$the_prefix;
	*/
	
	////////
	/*$mmm = oci_parse($oracle_connect, "select * from MN_DES_DEFORMED where MAIMAD_ID = '$the_maimad'");
	oci_execute($mmm, OCI_DEFAULT);
	while (oci_fetch($mmm)) {
		//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
		
		$DEFORM_ID = oci_result($mmm, "DEFORM_ID");
	}*/
	
	//}
	
	
	
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
					
					,'$curator_parent'					
					,'$DEFORM_ID'					
					, '1'					
				
				)
				
			";
			
			//echo $sql; exit();
			
		mysql_query($sql);
		
		$curator_last_id = mysql_insert_id();	
		
		
		//end add curator
		//if($this_year >= 2013){
			
		//yoes 20151222 -- doing this regardless of year
		//if($this_year >= 2013){
		if(1 == 1){
		
			//only do auto post if >= year 2013
			$_GET["auto_post"] = 1;
			//also add curator flag
			$_GET["curate"] = "curate";
			
			$_GET["curator_id"] = $curator_last_id;
			
		}
		
		
		
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
			
			//echo $the_prefix." - ".$FIRST_NAME_THAI . " - ". $LAST_NAME_THAI;
		
		//}
		
		//get prefix
		/*$s = oci_parse($oracle_connect, "select * from BS_PREFIX where PREFIX_CODE = '$the_prefix'");
		oci_execute($s, OCI_DEFAULT);
		while (oci_fetch($s)) {
			//echo "<br>deform id: " . oci_result($s, "DEFORM_ID");
			
			$PREFIX_NAME_ABBR = oci_result($s, "PREFIX_NAME_ABBR");
			//$PREFIX_NAME_ABBR = iconv("WINDOWS-874", "UTF-8", ($PREFIX_NAME_ABBR));
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
	
		
		//echo "<br>- curator is -> ". $PREFIX_NAME_ABBR . " ".$FIRST_NAME_THAI . " " .$LAST_NAME_THAI. " " .$RELATION_OTHER . " " . $SALARY . " " . $ISSUE_DATE;
		
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
			
			mysql_query($sql);
			
			$curator_last_id = mysql_insert_id();
			
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
				
				mysql_query($sql);
				
				
				//end add curator
				//if($this_year >= 2013){
					
				//yoes 20151222 -- do this on all year
				if(1 == 1){
				
				
				
					//only do auto post if >= year 2013
					$_GET["auto_post"] = 1;
					
					//also add curator flag
					$_GET["curate"] = "curate";
					
					$_GET["curator_id"] = $curator_last_id;
					
				}
		
		}else{
		
			//no first name thai?>        
			<script>
			alert("ไม่พบข้อมูลผู้ใช้สิทธิ");
			</script>        
			<?php
		
		}
	
	
	}
	
	
	//-----------------------------------------
	//--------------- try get curator -> end above
	//-----------------------------------------
	
	
	
	

}



include "scrp_add_curator.php";



if($this_lawful_year >= 2013 ){
					
	//show main branch only
	$is_2013 = 1;

}


//yoes test 20151222 -- allow auto calculate from 2011 and 2012
//this one is safe and can be allow here
$is_2013 = 1;



//yoes 20210203
//get dbd merged flag
$dbd_merged_to = getFirstItem("
		select
			meta_value 
		from
			company_meta
		where
			meta_cid = '$this_cid'
			and
			meta_for = 'dbd_merged_to'
		");


//echo $dbd_merged_to;




?>
<?php include "header_html.php";?>
<?php include "global.js.php";?>
                            
							
							
                               
                          
                        
              <td valign="top" bgcolor="#ffffff">
              
              	<?php //echo $this_lawful_year; ?>
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                    <?php if($mode == "new"){ ?>
					เพิ่มข้อมูล<?php echo $the_company_word;?>
                    <?php }elseif($mode == "edit"){ ?>
                    การจ้างงานคนพิการของ<?php echo $the_company_word;?>: <font color="#006699"><?php $company_name_to_use = formatCompanyName($output_values["CompanyNameThai"],$output_values["CompanyTypeCode"]); echo $company_name_to_use;?>
                    <?php } ?>
                    </font></h2>
                    
                    <?php if(strpos($this_page, "mode=new")){ ?>
                    
                    <?php }else{ ?>
                     <div style="padding:5px 0 10px 2px"><a href="org_list.php">รายชื่อ<?php echo $the_company_word;?></a> > <?php echo $company_name_to_use;?></div>
                     <?php }?>
					 
					 
					  <?php 
					 //yoes 20160613				 	
					if($dbd_merged_to){
						?>
					
									<div style="padding: 10px ;">
										<strong style=" color:#F30">*** สถานประกอบการถูกควบไปอยู่กับ
										
										
										<?php 
											
											$dbd_merge_to_cid = $dbd_merged_to;
														
											$dbd_merged_to_company_row = getFirstRow("
														
														select
															*
														from
															company
														where
															cid = '$dbd_merged_to'										
														
														");
														
											echo "<a href='organization.php?id=".$dbd_merged_to_company_row[CID]."'>".formatCompanyName($dbd_merged_to_company_row[CompanyNameThai],$dbd_merged_to_company_row[CompanyTypeCode])."</a>";						
											
										?>
										
										แล้ว 
														
										  </strong>
									</div>
									
					<?php		
						}				 
					 ?>
                     
                     
                     
                     <?php 
					 //yoes 20160613				 	
					if($this_year > 3000){
						?>
					
                    <div style="padding: 10px ;">
						<strong style=" color:#F30">*** ข้อมูลของสถานประกอบการนี้ ถูกรวมไปอยู่กับสถานประกอบการ
                        
                        
                        <?php 
							
							$merge_to_cid = getFirstItem("
										select 
											meta_value 
										from 
											company_meta
										where
											meta_cid = '$this_cid'	
											and
											meta_for = 'merged_to'
										");
										
							$merged_to_company_row = getFirstRow("
										
										select
											*
										from
											company
										where
											cid = '$merge_to_cid'										
										
										");
										
							echo "<a href='organization.php?id=".$merged_to_company_row[CID]."'>".formatCompanyName($merged_to_company_row[CompanyNameThai],$merged_to_company_row[CompanyTypeCode])."</a>";						
							
						?>
                        
                        แล้ว 
                        เมื่อวันที่ <?php echo formatDateThai(getFirstItem("
										select 
											meta_value 
										from 
											company_meta
										where
											meta_cid = '$this_cid' 
											and
											meta_for = 'merged_date'	
										"),1,1);?>
                            
                            
                            โดย <?php echo getFirstItem("
										select 
											user_name 
										from 
											company_meta a
												join users b
													on a.meta_value = b.user_id
										where
											meta_cid = '$this_cid' 
											and
											meta_for = 'merged_by'	
										");?>
                                        
                          </strong>
                    </div>
                    
                    <?php		
						}					 
					 ?>
                    
                    
                    
                    <?php 
					
					//yoes 20160615 --- also check if merged from somewhere?
					
						$merged_from_sql = "
						
							select
								meta_cid
							from
								company_meta
							where
								meta_value = '$this_cid'
								and
								meta_for = 'merged_to'
						
						";
						
						$merged_from_result = mysql_query($merged_from_sql);
						
						while($merged_from_row = mysql_fetch_array($merged_from_result)){
							
							?>
                            
                            <div style="padding: 10px ;">
                                <strong style=" color:#F30">
                                
                              *** มีการรวมข้อมูลมาจากสถานประกอบการ 
                        <?php 
							
							$merge_from_cid = $merged_from_row[meta_cid];
										
							$merged_from_company_row = getFirstRow("
										
										select
											*
										from
											company
										where
											cid = '$merge_from_cid'										
										
										");
										
							echo "".formatCompanyName($merged_from_company_row[CompanyNameThai],$merged_from_company_row[CompanyTypeCode])."";						
							
						?>
                        
                        
                        เมื่อวันที่ <?php echo formatDateThai(getFirstItem("
										select 
											meta_value 
										from 
											company_meta
										where
											meta_cid = '$merge_from_cid' 
											and
											meta_for = 'merged_date'	
										"),1,1);?>
                            
                            
                            โดย <?php echo getFirstItem("
										select 
											user_name 
										from 
											company_meta a
												join users b
													on a.meta_value = b.user_id
										where
											meta_cid = '$merge_from_cid' 
											and
											meta_for = 'merged_by'	
										");?>
                                
                                </strong>
                                
                            </div>
                            
                            
                            <?php
							
							
						}
					
					?>
					
					
					<?php 
					
								//yoes 20210203 --- also check if dbd merged from somewhere?
								
									$dbd_merged_from_sql = "
									
										select
											meta_cid
										from
											company_meta
										where
											meta_value = '$this_cid'
											and
											meta_for = 'dbd_merged_to'
									
									";
									
									$dbd_merged_from_result = mysql_query($dbd_merged_from_sql);
									
									while($dbd_merged_from_row = mysql_fetch_array($dbd_merged_from_result)){
										
											$is_dbd_merged_from = 1;
										?>
										
										<div style="">
											<strong style=" color:#F30">
											
										  *** เป็นการควบกิจการมาจากสถานประกอบการ 
									<?php 
										
										$dbd_merge_from_cid = $dbd_merged_from_row[meta_cid];
													
										$dbd_merged_from_row = getFirstRow("
													
													select
														*
													from
														company
													where
														cid = '$dbd_merge_from_cid'										
													
													");
													
										echo "<a href='organization.php?id=".$dbd_merged_from_row[CID]."'>".formatCompanyName($dbd_merged_from_row[CompanyNameThai],$dbd_merged_from_row[CompanyTypeCode])."</a>";						
										
									?>
											
											</strong>
											
										</div>
										
										
										<?php
										
										
									}
								
								?>
                    
                    
                    
                    
                    <?php
					
					
						//is this main branch or not?
						if($output_values["BranchCode"] > 0){
						
							//have branch code that is not 0
							//this is not a main branch
							$is_main_branch = 0;
						
						}else{
						
							$is_main_branch = 1;
						
						}
						
						if($_GET["all_tabs"] == 1 || $_GET["focus"]){
						
							$show_all_tabs = 1;
						
						}
					  
					  
					  
						//if this is not main branch, see if have lawfulness records
						if(!$is_main_branch){
						
							//see if has lawful
							$lawful_record = getFirstItem("select count(*) from lawfulness where CID = '".$output_values["CID"]."'");
							
							//
						
						}
						
						//echo $lawful_record;
					
					?>
                    
                    
                    
                    
                    <?php 
					
					//have info inputted from company?
					
					if($sess_accesslevel != 4){

						//only do this for non-company					
													
						//$count_info = countCompanyInfo($output_values["CID"], $this_lawful_year);
						
						$count_info = getFirstItem("select 
							count(*)
						from 
							lawfulness_company 
						where 
							CID = '".$output_values["CID"]."' 
							and 
							Year = '$this_lawful_year' 
							
							");
													
					}
							
					if(countCompanyInfo($output_values["CID"], $this_lawful_year) && $sess_accesslevel != 4 && !$is_merged){
					
					
					?>
                    
                     <div align="right" style="padding:10px 30px 10px 0;">
	                    <strong style="font-size: 20px; color:#C30; ">*** มีการส่งข้อมูลเข้ามาใหม่จากสถานประกอบการ</strong>
                    </div>
                    
                    
                    <?php
							
						
					}
					
					
					?>
				  
				  
				  
				   <?php 
				  
				  //20151102
				  //yoes 20211014
				//see if this company got submmited or not
				 $submitted_company_lawful = getFirstItem("
														select 
															lawful_submitted
														from
															lawfulness_company
														where
															CID = '" . $this_cid . "'
															and
															Year = '".$this_year."'
														");
				  
				  
				  $resubmit_status = getLawfulnessMeta($this_lid, "es-resubmit");
				  
				  
				  if( $submitted_company_lawful == 3 &&  $resubmit_status == 1){ ?>
					   <div align="right" style="padding:10px 30px 10px 0;">
							<strong style="font-size: 20px; color: blue; ">*** มีการส่งข้อมูล แก้ไข เข้ามาจากสถานประกอบการ</strong>
</div>
				  <?php }?>
                    
                    
                   
                    
                <table width="100%" >
                        <tr>
                        <td class="td_bordered">
              <table cellspacing="0">
                                <tr>
                                
                                  
                                  <?php if($mode != "new"){ ?>
                                  
                                  
                                 
                                  
                                  <td <?php echo $hide_style;?>>
                                      <a href="#history" onClick="showTab('history'); return false;">
                                      <div id="tab_history_black" class="white_on_black" style="width:160px;" align="center">ประวัติการปฏิบัติตามกฎหมาย</div>
                                      <div id="tab_history_grey" class="white_on_grey" style="width:160px; display:none; " align="center">ประวัติการปฏิบัติตามกฎหมาย</div>
                                      </a>
                                  </td>
                                  
                                 
                                  
                                 
                                  
                                  
                                  
                                  <td><a href="#general" onClick="showTab('general'); return false;">
                                  <div id="tab_general_black" class="white_on_black" style="width:120px; display:none;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  <div id="tab_general_grey" class="white_on_grey" style="width:120px;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  </a></td>
                                  
                                  
                                  
                                  <?php
								  
								  	
								  
								  
								  
								  
								  	//hide other tabs for non-main brancg
									//that have no lawful records
									if(
										(!$is_main_branch && $lawful_record < 1 && !$show_all_tabs)
										
										||
										(!$is_main_branch && $this_year >= 2012 && !$show_all_tabs)
										
										){
									
										$hide_style = 'style="display:none"';
									
									}
								  
								  ?>
                                  
                                  
                                  <td <?php echo $hide_style;?>>
									  <?php if($sess_accesslevel != 4){?>
                                          <a href="#official" onClick="showTab('official'); return false;">
                                          <div id="tab_official_black" class="white_on_black" style=" display:none;" align="center">จดหมายแจ้ง</div>
                                          <div id="tab_official_grey" class="white_on_grey" style=" " align="center">จดหมายแจ้ง</div>
                                          </a>
                                      <?php }?>
                                  </td>
                                  
                                  
                                  
                                  
                                  <td <?php echo $hide_style;?>>
                                      <a href="#lawful" onClick="showTab('lawful'); return false;">
                                      <div id="tab_lawful_black" class="white_on_black" style="width:150px; display:none;" align="center">การปฏิบัติตามกฎหมาย</div>
                                      <div id="tab_lawful_grey" class="white_on_grey" style="width:150px; " align="center">การปฏิบัติตามกฎหมาย</div>
                                      </a>
                                  </td>
                                  
                                  
                                  
                                  
                                   <?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3)  && $have_lawful_record && !$case_closed){ //yoes 20151122 -- allow admin to add dummy lawful employees?>
                                   <td <?php echo $hide_style;?>>
                                      <a href="#input" onClick="showTab('dummy'); return false;">
                                      <div id="tab_dummy_black" class="white_on_deepgreen" style="width:100px;  display:none;" align="center">ส่งข้อมูลชำระเงิน</div>
                                      <div id="tab_dummy_grey" class="white_on_lightgreen" style="width:100px; " align="center">ส่งข้อมูลชำระเงิน</div>
                                      </a>
                                  </td>
                                  <?php }?>
                                  
                                  
                                  
                                  
                                  <?php if($count_info){ //only show this if info sumbitted?>
                                   <td <?php echo $hide_style;?>>
                                      <a href="#input" onClick="showTab('input'); return false;">
                                      <div id="tab_input_black" class="white_on_black" style="width:160px; display:none;" align="center">ข้อมูลจากสถานประกอบการ</div>
                                      <div id="tab_input_grey" class="white_on_grey" style="width:160px;  " align="center">ข้อมูลจากสถานประกอบการ</div>
                                      </a>
                                  </td>
                                  <?php }?>
                                  
                                  
                                  <?php if(($sess_accesslevel == 1) || ($sess_accesslevel == 2)|| ($sess_accesslevel == 3) || ($sess_accesslevel == 8) ){?>
                                   <td <?php echo $hide_style;?>>
                                      <a href="#sequestration" onClick="showTab('sequestration'); return false;">
                                      <div id="tab_sequestration_black" class="white_on_black" style="width:180px; display:none;" align="center">ประวัติการดำเนินคดีตามกฎหมาย</div>
                                      <div id="tab_sequestration_grey" class="white_on_grey" style="width:180px;" align="center">ประวัติการดำเนินคดีตามกฎหมาย</div>
                                      </a>
                                   </td>
                                  <?php }?>
                                  
                                  
                                  
                                  
                                  
                                  
                                  
                                  <?php }else{?>
                                  <td><a href="#" >
                                  <div id="tab_general_black" class="white_on_black" style="width:120px;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  </a></td>
                                  <?php }?>
                                  
                                  
                                  
                                </tr>
                          </table>
                         </td>
                    </tr>
                </table>
                  <script>	
				  	function showTab(what){
						//toggle table on/off
						document.getElementById('general').style.display = 'none';
						
						
						
						document.getElementById('lawful').style.display = 'none';
						
						document.getElementById('history').style.display = 'none';
						
						
						
						
						document.getElementById('tab_general_black').style.display = 'none';
						document.getElementById('tab_general_grey').style.display = '';
						
						
						document.getElementById('tab_history_black').style.display = 'none';
						document.getElementById('tab_history_grey').style.display = '';


						var sequestrationTab = document.getElementById('sequestration');
						if(sequestrationTab != null){
							sequestrationTab.style.display = 'none';
							document.getElementById('tab_sequestration_black').style.display = 'none';
							document.getElementById('tab_sequestration_grey').style.display = '';
						}
						
						
						

						<?php if($sess_accesslevel != 4){?>
						document.getElementById('official').style.display = 'none';
						document.getElementById('tab_official_black').style.display = 'none';
						document.getElementById('tab_official_grey').style.display = '';					
						<?php } ?>
						
						
						<?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $have_lawful_record && !$case_closed){ //yoes 20151211 -- executive cant see this?>
							document.getElementById('dummy').style.display = 'none';
							document.getElementById('tab_dummy_black').style.display = 'none';
							document.getElementById('tab_dummy_grey').style.display = '';
						<?php }?>
						
						
						<?php if($count_info){ //only show this if info sumbitted?>
						document.getElementById('input').style.display = 'none';
						document.getElementById('tab_input_black').style.display = 'none';
						document.getElementById('tab_input_grey').style.display = '';
						<?php }?>
						
						document.getElementById(what).style.display = '';
						
						document.getElementById('tab_lawful_black').style.display = 'none';
						document.getElementById('tab_lawful_grey').style.display = '';
						
						document.getElementById('tab_'+what+'_black').style.display = '';
						document.getElementById('tab_'+what+'_grey').style.display = 'none';
						
						
						
						<?php if($this_year > 3000){?>
						
						document.getElementById('tab_dummy_black').style.display = 'none';
						document.getElementById('tab_dummy_grey').style.display = 'none';                               	
						/*document.getElementById('tab_history_black').style.display = 'none';
						document.getElementById('tab_history_grey').style.display = 'none';*/
						document.getElementById('tab_sequestration_black').style.display = 'none';
						document.getElementById('tab_sequestration_grey').style.display = 'none';	
						<?php }?>
						
						
						
					}
					
				  </script>
                  <script language='javascript'>
						<!--
						function validateForm(frm) {
							
							if(frm.CompanyCode.value.length < 10)
							{
								alert("กรุณาใส่ข้อมูล: <?php echo $the_code_word;?> เป็นเลข 10 หลักเท่านั้น");
								frm.CompanyCode.focus();
								return (false);
							}
							//CompanyCode - number only
							var checkOK = "1234567890";
							
							var checkStr = frm.CompanyCode.value;
							var allValid = true;
							for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("<?php echo $the_code_word;?> ต้องเป็นตัวเลขเท่านั้น");
							 frm.CompanyCode.focus();
							 return (false);
						   }
						   //CompanyCode - number only
							
							if(frm.BranchCode.value.length < 6)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่สาขา เป็นเลข 6 หลักเท่านั้น");
								frm.BranchCode.focus();
								return (false);
							}
							
							
							//BranchCode - number only
							var checkOK = "1234567890";
							
							var checkStr = frm.BranchCode.value;
							var allValid = true;
							for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("เลขที่สาขา ต้องเป็นตัวเลขเท่านั้น");
							 frm.BranchCode.focus();
							 return (false);
						   }
						   //BranchCode - number only
							if(frm.CompanyNameThai.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อบริษัท(ภาษาไทย)");
								frm.CompanyNameThai.focus();
								return (false);
							}
							if(frm.Employees.value.length == 0)
							{
								alert("กรุณาใส่ข้อมูล: จำนวน<?php echo $the_employees_word;?>");
								frm.Employees.focus();
								return (false);
							}
							//----
							if(frm.CompanyTypeCode.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: ประเภทธุรกิจ");
								frm.CompanyTypeCode.focus();
								return (false);
							}
							
							<?php if($output_values["CompanyTypeCode"] >= 200 && $output_values["CompanyTypeCode"] <= 300 || $sess_accesslevel == 6 ||  $sess_accesslevel == 7){ //don't validate this if this is an GOV company?> 
							
							<?php }else{?>
							
								if(frm.BusinessTypeCode.selectedIndex == 0)
								{
									alert("กรุณาใส่ข้อมูล: ประเภทกิจการ");
									frm.BusinessTypeCode.focus();
									return (false);
								}
							
							<?php }?>
							
							<?php if($sess_accesslevel != 3){ //only admin can select a province?>
							if(frm.Province.selectedIndex == 0 || frm.Province.value == 99)
							{
								alert("กรุณาใส่ข้อมูล: จังหวัด"); 
								frm.Province.focus();
								return (false);
							}
							<?php }?>
							
							
							//----
							return(true);									
						
						}
						-->
					
					</script>
                    <?php if($mode == "new"){ ?>
                   
                    
                    <form method="post" action="scrp_add_org.php" enctype="multipart/form-data" onsubmit="return validateForm(this);" id="organization_form">
                    <?php }elseif($mode == "edit"){ ?>
					<form method="post" action="scrp_update_org.php" enctype="multipart/form-data" onSubmit="return validateForm(this);" id="organization_form">
                    <input name="CID" type="hidden" value="<?php echo $output_values["CID"];?>" />
                    <input name="the_year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                    <?php } ?>
                    
                    <?php 
						if($_GET["new_id"]){
					?>							
                         <div style="color:#990000; padding:5px 0 0 0; font-weight: bold;">* ข้อมูล<?php echo $the_code_word;?> <a href="organization.php?id=<?php echo $_GET["new_id_link"];?>"><?php echo $_GET["new_id"];?> สาขา <?php echo $_GET["branch"];?></a> มีอยู่ในระบบแล้ว</div>
                    <?php
						}					
					?>
                    
                    <?php 
						if($_GET["added"]=="added"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูล<?php echo $the_company_word;?>ใหม่ได้ถูกบันทึกลงฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["updated"]=="updated" && !isset($_GET["year"])){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูล<?php echo $the_company_word;?>เสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["updated"]=="updated" && isset($_GET["year"])){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูล<?php echo $the_company_word;?>ปี <?php echo formatYear($_GET["year"]);?> เสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["reg"]=="reg"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มรหัสไปรษณีย์ลงทะเบียนเสร็จสิ้น</div>
                    <?php
						}					
					?>
					<?php 
						if($_GET["delletter"]=="delletter"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* จดหมายแจ้งได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["delpayment"]=="delpayment"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลการส่งเงินเข้ากองทุนได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    
                    <?php //echo $this_year;?>
                  <table style=" padding:10px 0 0px 0; " id="general">
                  
                  
                  
                  
                  
                  	<?php // populate branch info 
					
					
						
						
						
					
						$branch_count = getFirstItem("select count(*) from company where CompanyCode = '".$output_values["CompanyCode"]."'");
					
						//if($branch_count > 1 && $this_year >= 2013){
							
						//yoes 20151021 --> show all branches here regardless
						if($this_year >= 2013){
					
					?>
                    
                    
                    
                    
                    
                    
                    
                    <?php 
						//if($this_year >= 2013){ //only show this for years more than 2013
						//if(1==1){	
					?>
                  
                 	  <tr>
                        <td colspan="2">
                        
                        
                        	<?php if($mode != "new"){ ?>
                        	<div style="font-weight: bold; padding:5px 0 5px 0;">
                            
                            
                            	<?php if($this_year > 3000){?>
                                	ข้อมูลสาขา ประจำปี <?php echo $this_year+543-1000;?>
                                <?php }else{?>
                            		ข้อมูลสาขา ประจำปี <?php echo $this_year+543;?>
                                <?php }?>
                              
                            
                            </div>
                            <?php }?>
                            
                            
                             <?php if($sess_accesslevel == 4 && $mode != "new"){ ?>
                            <div style="padding:5px 0 5px 0; ">
                                
                                กรุณาใส่จำนวนลูกจ้างทั้งหมด ที่ทำงานในแต่ละสาขา <?php //echo "ปี ".$this_year;?>
                                <br />
                                จำนวนลูกจ้างรวมทั้งหมดทุกสาขา จะถูกนำไปใช้ในการคำนวณการปฏิบัติตามกฏหมาย
                                <br />
                                กรณีที่สาขาปิดไปแล้ว ให้ใส่จำนวนลูกจ้างเป็น 0 คน
                                
                                <br />
                                ต้องการเพิ่มข้อมูลสาขาใหม่ที่ไม่มีอยู่ในรายการ <a href="#" onclick="fireMyPopup('company_branch_popup',600,250); return false;">ให้คลิกที่นี่</a>
                                
                                
                                
                            
                            </div>
							<?php }?>
                            
                            </td>
							
							
							
							<?php
															
								
								$dbd_branch_count_all = getFirstItem("
								
														select
															count(*)
														from
															cs_branch_dbd
														where
															CompanyCode = '".$output_values["CompanyCode"]."'
								
														");
							
							?>
							
							
							<?php
								
																
								if($dbd_branch_count_all){
									
									
									//yoes 20190514
									$dbd_branch_sql = "
												
										select
											*
										from
											cs_branch_dbd
										where
											CompanyCode = '".$output_values["CompanyCode"]."'
										order by
											BranchCodeDBD*1 asc
									
									";
								
								
									$dbd_branch_result = mysql_query($dbd_branch_sql);
									
									
									
							?>
							
							<td colspan="2">
							
								<div style="font-weight: bold; padding:5px 0 5px 0;">
								ข้อมูลสาขาตาม <font color=blue>กรมพัฒนาธุรกิจการค้า</font>
								</div>
							
							</td>
							<?php }?>
							
							
							
                      </tr>
                      <tr>
                        <td colspan="2" valign=top>
                        
                        
                        		
                        
                        		<div id="branch_info_general" >
                                    <?php 
									
									
									if($mode != "new"){
									
										//20151102
										//see if this company got submmited or not
										 $submitted_company_lawful = getFirstItem("
																				select 
																					lawful_submitted
																				from
																					lawfulness_company
																				where
																					CID = '" . $this_cid . "'
																					and
																					Year = '".$this_year."'
																				");
										
										include "organization_branch_table.php";
										
									}
									
									?>
                               </div>
                                
                               
                                
                                
                        
                        </td>
						
						
						<?php
								
							if($dbd_branch_count_all){
						?>
												
						<td colspan="2" valign=top>
						
							<table cellpadding="3" style="border-collapse:collapse;" border="1">
								<tr bgcolor="#9C9A9C" align="center">
									 <td>
										<div align="center">
											<span class="column_header">
												ลำดับสาขา
											</span>
										</div>
									 </td> 
									<td>
										<div align="center">
											<span class="column_header">
												ที่อยู่
											</span>
										</div>
									</td>
									<td>
										<div align="center">
											<span class="column_header">
												ตำบล/แขวง
											</span>
										</div>
									</td>
									<td>
										<div align="center">
											<span class="column_header">
												อำเภอ/เขต
											</span>
										</div>
									</td>
									<td>
										<div align="center">
											<span class="column_header">
												จังหวัด
											</span>
										</div>
									</td>
									<td>
										<div align="center">
											<span class="column_header">
												รหัสไปรษณีย์
											</span>
										</div>
									</td>
									
									<?php 
									
										
										
										while($dbd_branch_row = mysql_fetch_array($dbd_branch_result)){
											
											$dbd_branch_count++;
											
									?>
										
										
										
										
										<tr <?php if($dbd_branch_count >= 11){ ?>style='display:none;' class='dbd_toggle_rows'<?php }?>>
                                                 <td  >
													<div align=center>
                                                   <?php 
														echo $dbd_branch_row[BranchCodeDBD];
													?>
													</div>
                                                 </td> 
												 <td  >
                                                   <?php 
														echo $dbd_branch_row[Address1];
													?>
                                                 </td> 
												 <td  >
                                                   <?php 
														echo $dbd_branch_row[Subdistrict];
													?>
                                                 </td>
												<td  >
                                                   <?php 
														echo $dbd_branch_row[District];
													?>
                                                 </td>
												<td  >
                                                   <?php 
														echo $dbd_branch_row[Province];
													?>
                                                 </td>	
												<td  >
                                                   <?php 
														echo $dbd_branch_row[Zip];
													?>
                                                 </td>													 
										</tr>
										
										<?php if($dbd_branch_count == 11){ ?>
										
											<tr>
                                                 <td colspan="6" class='dbd_toggle_linkxx' >
													
													<div align=center>
														<a href="#" onClick="$('.dbd_toggle_rows').toggle(); $('.dbd_toggle_link').toggle(); return false;">...แสดง/ซ่อนรายการสาขาทั้งหมด...</a>
													</div>
												  
                                                 </td> 
											</tr>
										
										<?php } ?>
									
									
										
									
									<?php							
									
										}
									
									?>
									
								</tr>
							</table>
						
						</td>
                      </tr>
					  
                      <?php  }?>
                      
                      
                      
                       <?php }//show all tabs?>
                      
                      
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:0 0 5px 0;">
						
							ข้อมูลทั่วไป
						
						</div></td>
                      </tr>
					  
					  
					  <?php 
						
						//yoes 20190514 - see if have DBD data from cleansing
					  
						$dbd_data = getFirstRow("
								
								select
									*
								from
									cs_company_dbd
								where
									companyCode = '".$output_values["CompanyCode"]."'
						
								");
					  
					  ?>
					  
					  <?php 
					  
					  if($dbd_data || 1==1){ 

						//print_r($dbd_data);
					  
					  ?>
					  
					  <tr>
                        <td id="tbl_save_company_info" colspan="4" style="border: 1px solid blue; padding: 10px; display: none; ">
							
							<table  >
								<tr>
									
									<td colspan=4>
										<b>ข้อมูลสถานประกอบการตาม <font color=blue>กรมพัฒนาธุรกิจการค้า</font></b>
									</td>
								</tr>
							</table>
							
							<span id="dbd_result">								
								<img src="decors/bigrotation2.gif" />
							</span>							
						
						
						
							<table  >
								<tr>
		
									<td colspan=4>
										
										
										<?php //yoes 20221213 --- remove this for now
										
										if(1==0){
										
										?><input id="btn_save_company_info" type="button" value="บันทึกข้อมูลสถานประกอบการตาม กรมพัฒนาธุรกิจการค้า เข้าสู่ระบบ" 
																				
											onClick="importDBDinfo(); return false;"										
										/><?php }?>
										
										<script>
										 
											function importDBDinfo(){
												
												/*
												console.log($("#dbd_CompanyNameThai").html());
												console.log($("#dbd_companyTypeText").html());
												console.log($("#dbd_TaxID").html());
												console.log($("#dbd_Address1").html());
												console.log($("#dbd_District").html());												
												console.log($("#dbd_subDistrict").html());
												console.log($("#dbd_province_name").html());
												console.log($("#dbd_zipCode").html());
												console.log($("#dbd_status").html());
												*/
												
												the_id = $('#dbd_TaxID').html();
												
												console.log(the_id);
												
												var cf = confirm("ต้องการบันทึกข้อมูลเข้าสู่ระบบ? ข้อมูลทั่วไป/ที่อยู่ในระบบจ้างงาน จะถูกแทนที่ด้วยข้อมูลจากกรมพัฒนาธุรกิจการค้า");												
												
												if(cf == true){													
													//alert("do stuff");
													$.ajax({ 
													
														//url: 'https://203.154.94.100/dbd/ajax_get_juristic_from_dbd_02.php',
														url: "https://job.dep.go.th/ajax_get_juristic_from_dbd_02_medium.php?the_id="+the_id+"&mode=import&the_cid=<?php echo $this_cid;?>",
														 data: { name: "John", location: "Boston", the_id: the_id, the_user: <?php echo $sess_userid;?>, CompanyCode: <?php echo $output_values["CompanyCode"];?>, the_name: "", mode: "import", the_cid: "<?php echo $this_cid;?>"},
														type: 'post',
														success: function(output) {
															//alert(output);
															//$('#cid_'+what+'_saving').css("display","none");
															window.location.href = "organization.php?id=<?php echo $this_cid;?>&focus=general";
															//console.log("organization.php?id=<?php echo $this_cid;?>&focus=general");
														}
													});
												}else{			
													//alert("dont do stuff");													
												}
												
											}
										
										</script>
										
									</td>
									
								</tr>
							</table>
						
						</td>
						
						
						
					  </tr>
					  
					  <?php } //end if $dbd_data?>
					  
					  
					   <?php 
						
						//yoes 20190514 - see if have DBD data from cleansing
					  
						$court_data = getFirstRow("
								
								select
									*
								from
									cs_company_court
								where
									companyCode = '".$output_values["CompanyCode"]."'
						
								");
					  
					  ?>
					  
					  <?php 
					  
					  if($court_data){ 

						//print_r($court_data);
					  
					  ?>
					  					  
					  
					  <tr>
                        <td colspan="4" style="border: 1px solid orangered; padding: 10px; ">
						
							<table  >
								<tr>
									
									<td colspan=4>
										<b><font color=orangered>ข้อมูลการดำเนินคดี</font></b>
									</td>
								</tr>	
								<tr>
									<td>
										จำเลย (ชื่อบริษัท/บุคคล):
									</td>
									<td colspan=3 style="color: blue;">
										<?php echo $court_data[companyNameThai_law];?>
									</td>
								</tr>	
								<tr>
									<td>
										เลขประจำตัวประชาชน/ทะเบียนนิติบุคคล:
									</td>
									<td colspan=3 style="color: blue;">
										<?php echo $court_data[taxId_law];?>
									</td>
								</tr>									
								<tr>
									<td>
										คดีล้มละลายหมายเลขดำ:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[black_code];?> 
									</td>
									<td>
										คดีล้มละลายหมายเลขแดง:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[red_code];?> 
									</td>
								</tr>
								<tr>
									<td>
										วันที่พิทักษ์ทรัพย์เด็ดขาด:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_1];?> 
									</td>
									<td>
										ราชกิจจาลงวันที่:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_2];?> 
									</td>
								</tr>
								<tr>
									<td>
										วันที่ครบกำหนดยื่นคำขอรับชำระหนี้:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_3];?> 
									</td>
									<td>
										นัดตรวจคำขอรอรับชำระหนี้:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_4];?> 
									</td>
								</tr>
								<tr>
									<td>
										วันพิพากษาให้ล้มละลาย:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_5];?> 
									</td>
									<td>
										วันจำหน่ายคดี:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_6];?> 
									</td>
								</tr>
								<tr>
									<td>
										วันปิดคดี :
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_7];?> 
									</td>
									
								</tr>
								<tr>
									<td>
										วันที่ศาลสั่งฟื้นฟูกิจการ:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_8];?> 
									</td>
									<td>
										ผู้ทำแผน (ชื่อผู้ทำแผน):
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_name_1];?> 
									</td>
								</tr>
								<tr>
									<td>
										วันที่ศาลเห็นชอบด้วยแผน:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_9];?> 
									</td>
									<td>
										ราชกิจจาลงวันที่:
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_10];?> 
									</td>
								</tr>
								<tr>
									<td>
										วันที่ยกเลิกคำสั่งฟื้นฟูกิจการ :
									</td>
									<td style="color: blue;">
										<?php echo $court_data[court_date_11];?> 
									</td>
									
								</tr>
								
							</table>
						
						
						</td>
						
						<tr>
							<td>
							</td>
						</tr>
						
					  </tr>
					  
					  
					  <?php } //end if court_data?>
					  
                      <tr>
                        <td><?php echo $the_code_word;?>: </td>
                        <td><label>
                          <?php 
						  		if($sess_accesslevel == 4){ 
									//company didnt see this textbox
									
									// yoes 20151116
									if(!$output_values["CompanyCode"]){
										
										
										//
										$company_company_row = getFirstRow("select * from company where cid = '$sess_meta'");
										
										$output_values["CompanyCode"] = $company_company_row["CompanyCode"];
										//$output_values["CompanyCode"] = getFirstItem("");
										
									}
									
									echo $output_values["CompanyCode"];
									 
									 
                          		}else{ 
						  ?>
	                          <input type="text" name="CompanyCode" id="CompanyCode" value="<?php echo $output_values["CompanyCode"];?>" maxlength="10"/>*
                          <?php }?>    
                            </label></td>
							
							
                       
						
								
								 <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
							   
								<?php }else{?>
									  <td class="td_left_pad"> เลขที่ประจำตัวผู้เสียภาษีอากร: </td>
										<td>
										
											<input type="text" id="TaxID" name="TaxID" value="<?php echo $output_values["TaxID"];?>" maxlength="13" />
											
											
											
											
											<script>
											
												function doGetCompanyInfo(){
													
													var the_id = "";
													
													the_id = the_id + document.getElementById('TaxID').value;
													
													//console.log(the_id);
													
													var checkOK = "1234567890";
												   var checkStr = the_id;
												   var allValid = true;
												   for (i = 0;  i < checkStr.length;  i++)
												   {
													 ch = checkStr.charAt(i);
													 for (j = 0;  j < checkOK.length;  j++)
													   if (ch == checkOK.charAt(j))
														 break;
													 if (j == checkOK.length)
													 {
													   allValid = false;
													   break;
													 }
												   }
												   if (!allValid)
												   {
													 alert("เลขที่ประจำตัวผู้เสียภาษีอากรต้องเป็นเลข 13 หลักเท่านั้น");
													 document.getElementById('TaxID').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 13)
													{
														alert("เลขที่ประจำตัวผู้เสียภาษีอากรต้องเป็นเลข 13 หลักเท่านั้น");
														document.getElementById('TaxID').focus();
														return (false);
													}
												
												
													getDBD(the_id,"");
													
												}
												
												
												function getDBD(the_id, the_name){
													
													$.ajax({
													  method: "POST",
													  //url: "https://203.154.94.100/dbd/ajax_get_juristic_from_dbd_02.php",
													  url: "https://job.dep.go.th/ajax_get_juristic_from_dbd_02_medium.php?the_id="+the_id,
													 
													  data: { name: "John", location: "Boston", the_id: the_id, the_user: <?php echo $sess_userid;?>, CompanyCode: <?php echo $output_values["CompanyCode"];?>, the_name: ""+the_name+"", the_cid: "<?php echo $this_cid;?>"}
													 
													})
													  .done(function( html ) {														
														$( "#dbd_result" ).html( html);	
														var span_result =  $( "#span_dbd_table_result" ).html();														
														if(span_result == 1){
															$( "#tbl_save_company_info" ).show();
														}else{
															$( "#tbl_save_company_info" ).hide();
														}
														
													  });
													
												}
												
												//try get id when page loaded
												$( document ).ready(function() {
													console.log($( "#CompanyNameThai" ).val());
													getDBD($( "#TaxID" ).val(), $( "#CompanyNameThai" ).val());
												});
												
											</script>
											
											
											<input id="btn_get_company_info" type="button" value="ดึงข้อมูลสถานประกอบการ" onClick="return doGetCompanyInfo();" />
											
											
											</td>
								<?php }?>
						
						
						
                      </tr>
                      
                      
                       <?php 
					  
					  	//yoes 20160816 -> disregard this... use below code instrad
						/*
					  	//yoes 20151021 -- add contact from "users" table (if any)
						$contact_sql = "
									select
										*
									from
										users 
									where
										AccessLevel = 4
										and
										user_meta = '$this_cid'
										and
										user_enabled = 1
									
									";
									
						$contact_result = mysql_query($contact_sql);
						
						$count_contact = 0;
						
						while($contact_row = mysql_fetch_array($contact_result)){
						
							$count_contact++;
						
						?>
                        
                        <tr>
                            <td>เลขทะเบียนนิติบุคคลของกระทรวงพาณิชย์: </td>
                            <td>
                            
                          
                            <?php if($count_contact > 1){echo "<br>";}?>
                            
                            <?php echo $contact_row[user_commercial_code] ;?>
                            
                            
                            </td>
                            
                          </tr>
                         
                          
                          
                        
                        
                        <?php
							
						}
					  	*/
					  ?>
                      
                      
                      
                      <?php

                            //yoes - merge $commercial_code to TaxId
                            if(!$sess_is_gov && 1==0){

                          ?>
                      <tr>
                            <td>เลขทะเบียนนิติบุคคลของกระทรวงพาณิชย์: </td>
                            <td>
                            
                          
                            
                            <?php 
								
								//get commercial code (if any)
								$commercial_code = getFirstItem("select meta_value from company_meta where meta_cid  = '$this_id' and meta_for = 'commercial_code'");
							
								?>
                                
                                <input type="text" name="commercial_code" id="commercial_code" maxlength="13" value="<?php echo $commercial_code ;?>" />
                            
                            
                            </td>
                            
                          </tr>
                          
                        <?php }?>
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      <tr>
                        <td>เลขที่สาขา:</td>
                        <td>
                         <?php 
						  		if($sess_accesslevel == 4){ 
									//company didnt see this textbox
									 echo $output_values["BranchCode"];
                          		}else{ 
						  ?>
                        <input type="text" name="BranchCode" id="BranchCode" value="<?php echo $output_values["BranchCode"];?>" maxlength="6"/> *
                        <?php } ?></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td > 
                        <?php
						
						if($sess_accesslevel == "6" || $sess_accesslevel == "7"){
							
							echo "ประเภทหน่วยงาน";
							
							
						}else{
							
							echo "ประเภทธุรกิจ";
							
						}
						
						?>
                        :</td>
                        <td><?php include "ddl_org_type.php";?>
                          * </td>
						  
					  <?php if($output_values["CompanyTypeCode"] >= 200 && $output_values["CompanyTypeCode"] <= 300 || $sess_accesslevel == 6 ||  $sess_accesslevel == 7){ //don't validate this if this is an GOV company?> 
						
						
						<?php }else{?>
									<td class="td_left_pad"> ประเภทกิจการ:</td>
									<td><?php include "ddl_bus_type.php";?> *</td>
						<?php }?>
						
						
                      </tr>
                      
                      
                      
					  
					  <tr id="school_row_1xx">
                        <td>
						
						
						  <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
								ชื่อหน่วยงาน (ภาษาไทย): 
							<?php }else{?>
								 ชื่อ<?php echo $the_company_word; ?> (ภาษาไทย): 
							<?php }?>
						</td>
                        <td><input type="text"  name="CompanyNameThai" id="CompanyNameThai" value="<?php echo ($output_values["CompanyNameThai"]);?>" />
                        *</td>
                        <td class="td_left_pad"> 
						
						
						 <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
								ชื่อหน่วยงาน (ภาษาอังกฤษ): 
							<?php }else{?>
								 ชื่อ<?php echo $the_company_word; ?> (ภาษาอังกฤษ): 
							<?php }?>
						
						</td>
                        <td><input type="text" name="CompanyNameEng" value="<?php echo $output_values["CompanyNameEng"];?>" /></td>
                      </tr>
                      
                      
                     
                       <tr>
                            <td>
                            
                            </td>
                            
                            
                             <?php if(!$sess_is_gov){ ?>
                      
                            <td colspan="3">
                             <input name="is_school" id="is_school"
                             	type="checkbox" value="1" <?php if($is_school){?>checked="checked"<?php }?>
                                
                                onclick="check_school();"
                                
                                /> เป็นโรงเรียนเอกชน
                            </td>
                            
                              <?php }else{?>
                              
                              <input type="hidden" name="is_school" id="is_school"  value="0" checked="checked" />
                              
                              <?php }?>
                            
                            
                            <script>
							
								function check_school(){
								
									if($('#is_school').is(':checked')){
										//alert(1);	
										$('#school_row_1').show();
										$('#school_row_2').show();
										$('#school_row_3').show();
										$('#school_row_4').show();
										$('#school_row_5').show();
										
										$('#Employees2').hide();
										$('#Employees2_text').show();
																				
										//yoes 20160713 --> default CompanyCode and BranchCode in case it is "blank"
										if($.trim($('#CompanyCode').val()) == ""){
											
											<?php 
												
												//yoes 20160713 --> what company code to show here?
												$latest_77777_code = getFirstItem("select max(substring(CompanyCode,6,5)) from company where CompanyCode like '77777%'");
												
												
												if(!$latest_77777_code){
													$latest_77777_code = 0;	
												}elseif($latest_77777_code == "99999"){
													$latest_77777_code = rand(20000,80000);													
												}
												
												$this_77777_code = sprintf('%05d', $latest_77777_code+1);
											
											?>
											
											$('#CompanyCode').val("77777<?php echo $this_77777_code;?>");
											
										}	
										
										if($.trim($('#BranchCode').val()) == ""){
											$('#BranchCode').val("000000");
										}									
										
										
									}else{
										//alert(0);	
										$('#school_row_1').hide();
										$('#school_row_2').hide();
										$('#school_row_3').hide();
										$('#school_row_4').hide();
										$('#school_row_5').hide();
										
										$('#Employees2').show();
										$('#Employees2_text').hide();
									}	
									
								}
								
								function do_sum_school(){
									//alert('sum school');	
									
									//sum_school = parseInt($('#school_teachers').val(,10))+parseInt($('#school_contract_teachers').val(),10)+parseInt($('#school_employees').val(),10);
									sum_school = ($('#school_teachers').val()*1)+($('#school_contract_teachers').val()*1)+($('#school_employees').val()*1);//+$('#school_contract_teachers').val()+$('#school_employees').val();
									
									$('#Employees2').val(sum_school);
									$('#Employees2_text').html(sum_school);
								}
							
														
								
								$( document ).ready(function() {
									
									
									check_school();
									
									
									
									
								});
							
							</script>
                          
                        
                        </tr>
                        
					  
                      
                      <?php 
					  
					  //yoes 20160511
					  
					  //if($is_school || 1==1){
					  ?>
                      
                       <tr id="school_row_1">
                        <td>
						
						
						 
							ชื่อโรงเรียน: 
							
						</td>
                        <td><input type="text"  name="school_name" value="<?php echo ($output_values["school_name"]);?>" /> *</td>
                        <td class="td_left_pad"> 
						
						
						
						</td>
                        <td></td>
                      </tr>
                      
                      
                       <tr id="school_row_2">
                        <td>รหัสโรงเรียน: </td>
                        <td>
                          <input type="text" name="school_code" id="school_code" maxlength="8" value="<?php echo $output_values["school_code"];?>" /> *
                          
                          
                          <?php 
						  
						  //yoes 20160519 --> check school duplication...
						  
						  $count_duped_school = getFirstItem("
						  				select 
											count(*) 
										from 
											company_meta 
										where 
											meta_value = '".$output_values["school_code"]."'
											and
											meta_for = 'school_code'
											and 
											meta_value != ''
											");
											

						  
						  
							if($count_duped_school > 1 && 1==0){						  
						  
						  ?>
                              <span style="color: #F60">
                                  <br />                          
                                  มีการซ้ำซ้อนของรหัสโรงเรียน
                                  <br />
                                  <a href="merge_company.php?school_code=<?php echo $output_values["school_code"];?>">คลิกที่นี่</a> เพื่อทำการรวมข้อมูลที่ซ้ำซ้อน
                              </span>
                          <?php }//ends $count_duped_school?>
                          
                        </td>
                        <td class="td_left_pad">ประเภทโรงเรียน: </td>
                        <td>
                        	
                            
                            <?php include "ddl_school_type.php";?>
                            
                          </td>
                      </tr>
                      <tr id="school_row_3">
                        <td>school_locate: </td>
                        <td>
                          <input type="text" name="school_locate" id="school_locate" maxlength="4"  value="<?php echo $output_values["school_locate"];?>" />
                        </td>
                        <td class="td_left_pad">school_charity: </td>
                        <td>
                        	 <?php include "ddl_school_charity.php";?>
                            
                          </td>
                      </tr>
                     
                           <tr id="school_row_4">
                            <td>จำนวนผู้บริหาร ครู <br />
                             ครูพิเศษ ครูพี่เลี้ยง: </td>
                            <td>
                              <input type="text" name="school_teachers" id="school_teachers"  onchange="do_sum_school();" value="<?php echo $output_values["school_teachers"];?>" /> คน
                            </td>
                            <td class="td_left_pad">จำนวนครูสัญญาจ้าง: </td>
                            <td>
                                <input type="text" name="school_contract_teachers" id="school_contract_teachers" onchange="do_sum_school();" value="<?php echo $output_values["school_contract_teachers"];?>" /> คน
                                
                              </td>
                          </tr>
                          
                           <tr id="school_row_5">
                            <td>จำนวนบุคลากรทั่วไป <br />
                             บุคลากรทางการศึกษา: </td>
                            <td>
                              <input type="text" id="school_employees" name="school_employees" onchange="do_sum_school();"  value="<?php echo $output_values["school_employees"];?>" /> คน*
                            </td>
                            <td class="td_left_pad"></td>
                            <td></td>
                          </tr>
                          
                          <script>
						  
						  
						   $().ready(function() {
								
								$("#organization_form").validate({
									
									
									rules: {
										
										school_name: {	
											required: true					
										},
										school_code: {	
											required: true,																				
											number: true,
											minlength: 8
																				
										},
										school_locate: {											
											number: true,
											minlength: 4
										},
										school_teachers: {
											required: true,
											number: true
										},
										school_contract_teachers: {
											required: true,
											number: true
										},
										school_employees: {
											required: true,
											number: true
										}
									},
									messages: {
										school_name: "กรุณาใส่ชื่อโรงเรียน",
										school_code: "ใส่เป็นรหัสตัวเลข 8 หลักเท่านั้น",
										school_locate: "ใส่เป็นรหัสตัวเลข 4 หลักเท่านั้น",
										school_teachers: "กรุณาใส่ จำนวนครู",
										school_contract_teachers: "กรุณาใส่ จำนวนครูสัญญาจ้าง",
										school_employees: "กรุณาใส่ จำนวนลูกจ้าง"
									}
								});
								 
								 
								 
							 }); /**/
						  
						  </script>
                          
                      <?php //}?>
                      
                      
                      
                       <tr>
                        <td > 
                        
                        <?php if(!$sess_is_gov){ ?>
                        	รวมทั้งสิ้น:
                        <?php }else{ ?>
                        	<?php echo $the_employees_word; ?>
                        <?php }?>
                        
                        
                        </td>
                        <td>
                        
                        
                        
                        <?php 
						
						//yoes 20151021 -- company see nothing here
						
						if($sess_accesslevel == 4){ ?>
                        
                        	<?php echo formatEmployee($sum_employees);?> คน
                         
                        <?php 
						
						//}elseif($output_values[CompanyTypeCode] == "07"){ //yoes 20160511 --> for School - just display summed amount
						
						}else{
						
						?>
                        
							
                            <span id="Employees2_text">
							<?php echo formatEmployee($output_values["Employees"]);?>
                            </span>
                        
                       
                        <input type="text" name="Employees" id="Employees2" value="<?php echo formatEmployee($output_values["Employees"]);?>" onChange="addEmployeeCommas('Employees2');"/>
                        
                          คน*<?php include "js_format_employee.php";?>
                          
                          <?php }?>
                          
                          
                          </td>
                          
                          
                           <td class="td_left_pad">
                            
                            </td>
                            <td>                            
                            
                            </td>
                        
                      </tr>
                      
                      
                      
                      <tr>
                       <?php
						
						if($sess_accesslevel == "6" || $sess_accesslevel == "7"){
							
							//see nothing 
							
						}else{
							
						?>
                        
                             <td >
                            สถานะของกิจการ
                            </td>
                            <td><?php include "ddl_company_status.php";?></td>
							<?php //bank add 20230104 ข้อมูลล้มละลาย ?>
						<!-- Output here -->
						<div id='selected_bankrupt' style="display: none;"></div>

					<?php //for auto update = 3 ล้มละลาย  ?>
						<?php if($output_values["Status"] == "3"){ ?>
						 <tr id="show_block_rput" style="display: block;">
						 <td>
							รายละเอียดการล้มละลาย: 
						 </td>
						 <td class="">
						 
						
						
						
						<a href="" data-toggle="modal" data-target=".prepaid_form">
							
							คลิ๊กเพื่อดูรายละเอียด														
							
						</a>
						
						<div class="modal prepaid_form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
							<?php include "modal_bankrupt.php";?>
						</div>
						
						
						</td>	
						 </tr>
						<?php } ?>
						
						<?php //for select to check?>
						
						 <tr id="show_block_rput" style="display: none;">
						 <td>
							รายละเอียดการล้มละลาย: 
						 </td>
						 <td class="">
						 
						
						
						
						<a href="" data-toggle="modal" data-target=".prepaid_form">
							
							คลิ๊กเพื่อดูรายละเอียด														
							
						</a>
						
						<div class="modal prepaid_form" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
							<?php include "modal_bankrupt.php";?>
						</div>
						
						
						</td>	
						 </tr>
						
						
		 
					<script>
					    const select = document.getElementById('bankruptSelect');
						  const selectedColor = document.getElementById('selected_bankrupt');
						  
						  select.addEventListener('change', (event) => {
							const selectedValue = event.target.value;
							selectedColor.innerHTML = selectedValue;
							
							
							
							  if(selectedValue == '3') {
								document.getElementById('show_block_rput').style.display = 'block';
							  } else {
								document.getElementById('show_block_rput').style.display = 'none';
							  }
							
					  });
					</script>
					 
					 <?php //End bank add 20230104 ข้อมูลล้มละลาย ?>	
						
						
						

						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
                        <?php
							
						}
						
						?>
                        <td class="td_left_pad"></td>
                        <td></td>
                      </tr>
                     
					 
					 
					 <?php 
					  $count_edit_request =	getFirstRow("SELECT count(*) as count_total
											
										FROM 
										
											company_edit_request a 
											
											join provinces p 
												on a.Province = p.province_id
												
											join  company c
												on a.cid = c.cid
											where a.cid = $this_id
											and a.Province_accepted = 0
											and a.Address1 != c.Address1
											and a.Moo != c.Moo
											and a.Soi != c.Soi
											and a.Road != c.Road
											and a.Subdistrict != c.Subdistrict
											and a.District != c.District
											and a.Province != c.Province
											and a.Zip != c.Zip
											");
											
											
						//yoes 20230119
						if($mode == "new"){
							$count_edit_request["count_total"] = 0;
						}
					 
					 ?>
					 
					 
					 
					 
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ที่อยู่</div></td>
                      </tr>
                      <tr <?php if($count_edit_request["count_total"] != '0'){?>style="display:none"<?php }?>>
                        <td>สถานที่ตั้งเลขที: </td>
                        <td><label>
                          <input type="text" name="Address1"  value="<?php echo $output_values["Address1"];?>" />
                        </label></td>
                        <td class="td_left_pad">ซอย: </td>
                        <td><input type="text" name="Soi" value="<?php echo $output_values["Soi"];?>" /></td>
                      </tr>
					  
                      <tr <?php if($count_edit_request["count_total"] != '0'){?>style="display:none"<?php }?>>
                        <td>หมู่:</td>
                        <td><input type="text" name="Moo" value="<?php echo $output_values["Moo"];?>" /></td>
                        <td class="td_left_pad"> ถนน:</td>
                        <td><input type="text" name="Road" value="<?php echo $output_values["Road"];?>" /></td>
                      </tr>
                      <tr <?php if($count_edit_request["count_total"] != '0'){?>style="display:none"<?php }?>>
                        <td>จังหวัด: </td>
                        <td><select name="Province" id="province_code" onchange="doProvinceChange(); doDistrictChange();">
                          <?php
                            
                            //yoes 20160908 - now province see all
                            
                            $get_province_sql = "select *
                                from provinces
                                order by province_name asc
                                ";
                                
                            echo '<option value="">-- เลือก --</option>';
                            
                            
                            //all photos of this profile   
                          
                            $province_result = mysql_query($get_province_sql);
                            
                            
                            
                            while ($province_row = mysql_fetch_array($province_result)) {
                            
                            
                            ?>
                          <option <?php if($_POST["Province"] == $province_row["province_id"] || $output_values["Province"] == $province_row["province_id"] || $output_values["user_meta"] == $province_row["province_id"]){echo "selected='selected'";}?> value="<?php echo $province_row["province_id"];?>"><?php echo $province_row["province_name"];?></option>
                          <?php
                            }
                            ?>
                        </select>
                          
                          <script>
						  
								function doProvinceChange(){
									
									//alert($('#province_code').val());
									$.ajax({
										type: "POST",
										url: "ajax_get_district.php",
										data: "prov=" + $('#province_code').val(),  
										cache: false,
										success: function(html){
											$('#div_district_code').html(""); 
											$('#div_district_code').append(html);
											
											doDistrictChange($('#province_code').val());
											
											}
										});
									
									
										$('#District_init').remove();
									
									
								}
								
								///
							  
								function doDistrictChange(the_province){
									
									//alert(the_province);
									//alert($('#district_code').val());
									
									if($('#District_init').val()){
										
										district_val = $('#District_init').val();	
										
									}else{
									
										district_val = $('#District').val();	
										
									}
									
									//alert(the_province);
									//alert(district_val);
									
									$.ajax({
										type: "POST",
										url: "ajax_get_subdistrict.php",
										data: "prov="+ the_province + "&dist=" + district_val,  
										cache: false,
										success: function(html){
											$('#div_subdistrict_code').html(""); 
											$('#div_subdistrict_code').append(html);
											}
										});
										
										$('#Subdistrict_init').remove();
									
								}
								
								//doProvinceChange();
								
							
						  
						  </script>
                          *</td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
						<?php //break bank ?>
						 <td style = "padding-top: 5px">เอกสารแนบข้อมูลที่อยู่: </td>
                                                          <td style = "padding-top: 5px">
															
															<?php                                              
																
																
																$this_id_temp = $this_id;
																$disable_delete  = 1; 
																$file_type = "company_address_info_file";  					
																include "doc_file_links.php";    
																$this_id = $this_id_temp;
																$disable_delete = 0;
																													
															?><br>
														   
                                                           <input type="file" name="company_address_info_file" id="company_address_info_file">
                                                           
                             
                                                          
                                                          </td>
						
                      </tr>
					  
					  
					   
                                                         

					  
					  
					  
					  
					  
					  
					  
					  
					  
					  
					  
					  
					  
					  
                      <tr <?php if($count_edit_request["count_total"] != '0'){?>style="display:none"<?php }?>>
                       
                        <td style = "padding-top: 5px"> อำเภอ/เขต:</td>
                        <td style = "padding-top: 5px">
                        
                        <?php if(1==0){ ?>
                        <input type="text" name="District" value="<?php echo $output_values["District"];?>" />
                        <?php }?>
                        
                        <?php 
						
							$province_code_sql = "
										
										select
											province_name
											,province_code
										from
											provinces
										where
											province_id = '".$output_values["Province"]."'
											
											
							
									";
									
							//echo $province_code_sql;
									
							$province_dropdown_row = getFirstRow($province_code_sql);
							
							$district_province_name = $province_dropdown_row[province_name];
							$district_province_code = $province_dropdown_row[province_code];
							
							include "ddl_district.php";
						
						?>
                       
                        <span id="div_district_code"></span> 
                        </td>
                       
                        <td style = "padding-top: 5px" class="td_left_pad">ตำบล/แขวง: </td>
                        <td style = "padding-top: 5px">
                        
                        <?php if(1==0){ ?>
                        <input type="text" name="Subdistrict" value="<?php echo $output_values["Subdistrict"];?>" />
                        <?php }?>
                        
                        
                        <?php
						
							$district_code_sql = "
										
										select
											district_code
										from
											districts
										where
											district_name = '".$output_values["District"]."'
											
											
							
									";
									
							//echo $province_code_sql;
									
							
						
							$subdistrict_province_code = $district_province_code;
							$subdistrict_district_code = getFirstItem($district_code_sql);
							
							
							include "ddl_subdistrict.php";
						
						?>
                        
                         <span id="div_subdistrict_code"></span>
                        
                        </td>
                       
                      </tr>
                      <tr <?php if($count_edit_request["count_total"] != '0'){?>style="display:none"<?php }?>>
                        <td class="" style = "padding-top: 5px" > รหัสไปรษณีย์:</td>
                        <td style = "padding-top: 5px"><input type="text" name="Zip" id="org_zip" value="<?php echo $output_values["Zip"];?>" /></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>โทรศัพท์:</td>
                        <td><input type="text" name="Telephone" value="<?php echo $output_values["Telephone"];?>" /></td>
                        <td class="td_left_pad">email:</td>
                        <td><input type="text" name="email" value="<?php echo $output_values["email"];?>" /></td>
                      </tr>
                      <tr>
                        <td>เวปไซต์:</td>
                        <td><input type="text" name="org_website" value="<?php echo $output_values["org_website"];?>" /></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      
                      
                     
					 <?php //if count address match = have approve
					 $count_row =	getFirstRow("SELECT count(*) as count_total
											
										FROM 
										
											company_edit_request a 
											
											join provinces p 
												on a.Province = p.province_id
												
											join  company c
												on a.cid = c.cid
											where a.cid = '$this_id'
											and a.Province_accepted = 0
											and a.Address1 = c.Address1
											and a.Moo = c.Moo
											and a.Soi = c.Soi
											and a.Road = c.Road
											and a.Subdistrict = c.Subdistrict
											and a.District = c.District
											and a.Province = c.Province
											and a.Zip = c.Zip
											");
					
					
					
					$get_row= getFirstRow("
										
										
										SELECT 
											a.cid
											, a.CompanyNameThai
											, a.CompanyTypeCode
											, a.Address1
											, a.Moo
											, a.Soi		
											, a.Road
											, a.Subdistrict
											, a.District
											, a.Province
											, p.province_name
											, a.Zip
											, a.edit_date
											, a.Province_accepted
											, a.file_id
											
										FROM 
										
											company_edit_request a 
											
											join provinces p 
												on a.Province = p.province_id
												
											join  company c
												on a.cid = c.cid
											where a.cid = '$this_id'");
											
					?>				
					<!-- <tr>
						<td>
						 <?php //echo $sess_accesslevel; ?>
						</td>
						<td>
						 <?php //echo $get_row["Province_accepted"]; ?>
						</td>
						<td>
						 <?php //echo $count_row["count_total"]; ?>
						</td>
					</tr> -->
					
					
					
					

					
						<?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3){?>			
						<?php if($get_row["Province_accepted"] == '0') { //have submit done ?>
							<?php if($count_row["count_total"] == '0'){ // no record in temp table ?>
				 
							<?php //yoes 20230108 -- move this under if ?>
					<!--	<form method="post" action="scrp_approve_company_edit_request.php" onSubmit="return validateForm(this);" id="organization_form"> -->
					<!--		<input name="CID" type="hidden" value="<?php // echo $output_values["CID"];?>" /> -->
					<!--		<input name="the_year" type="hidden" value="<?php // echo $this_lawful_year;?>" /> -->

						
						<tr>
								
									
										<tr>
											
											<td colspan=4>
												<b><font color=blue>แจ้งเตือนการเปลี่ยนที่อยู่</font></b>
											</td>
										</tr>
										
										
										
						</tr>		
						 <tr>
							<td>
							<div style="font-weight: bold; padding:5px 0 5px 0;">
								สถานที่ตั้งเลขที:
							</div>
							</td>
							<td  style="color: blue;">
								<?php echo $get_row["Address1"]; ?>
							</td>
							<td>
							<div style="font-weight: bold; padding:5px 0 5px 0;">
								ซอย:
							</div>
							</td>
							<td  style="color: blue;">
								<?php echo $get_row["Soi"]; ?>
							</td>
						</tr>
						<tr>
							<td>
							<div style="font-weight: bold; padding:5px 0 5px 0;">
								หมู่:
							</div>
							</td>
							<td  style="color: blue;">
								<?php echo $get_row["Moo"]; ?>
							</td>
							<td>
							<div style="font-weight: bold; padding:5px 0 5px 0;">
								ถนน:
							</div>
							</td>
							<td  style="color: blue;">
								<?php echo $get_row["Road"]; ?>
							</td>
						</tr>
						<tr>
							<td>
							<div style="font-weight: bold; padding:5px 0 5px 0;">
								อำเภอ/เขต:
							</div>
							</td>
							<td  style="color: blue;">
								<?php echo $get_row["Subdistrict"]; ?>
							</td>
							<td>
							<div style="font-weight: bold; padding:5px 0 5px 0;">
							            ตำบล/แขวง:	
							</div>
							</td>
							<td  style="color: blue;">
								<?php echo $get_row["District"]; ?>
							</td>
						</tr>
						<tr>
							<td>
							<div style="font-weight: bold; padding:5px 0 5px 0;">
								จังหวัด:
							</div>
							</td>
							<td  style="color: blue;">
								<?php echo $get_row["province_name"]; ?>
							</td>
							<td>
							<div style="font-weight: bold; padding:5px 0 5px 0;">
								รหัสไปรษณีย์:
							</div>
							</td>
							<td  style="color: blue;">
								<?php echo $get_row["Zip"]; ?>
							</td>
						</tr>
						
					
						
						<tr>
							<td>
								<b>เอกสารแนบข้อมูลที่อยู่:	</b>
							</td>
							<td>
								<?php                                              
																
																
																$this_id_temp = $this_id;
																$disable_delete  = 1; 
																$file_type = "company_address_info_file";  
																$file_id = $get_row["file_id"];																
																include "doc_file_links.php";                                                    
																$file_id = "";
																
																$this_id = $this_id_temp;
																$disable_delete = 0;
																													
															?>
                                                          
                                                          
                                              

							</td>
						</tr>
						
							<?php if ($sess_accesslevel == 3) { ?>
							
										 <tr>
										 <td colspan="4">
										  <div align="center">	
												<?php // bank 20221109 add new button reject ?>
															<hr />
															<?php if($mode == "edit" && $sess_accesslevel == "3" && !$is_company_table_read_only){?>
															<input type="submit" name="address_do_reject" id="address_do_reject" value="ยกเลิกการขอเปลี่ยนที่อยู่"
															onclick = "return confirm('ต้องการยกเลิกการขอเปลี่ยนที่อยู่นี้ ?');"
															/>
															
															
												 <?php  }?>
											</div>	 
										</td>
										</tr>
							
							<?php } ?>
					  				     <tr>
										 <td colspan="4">
										  <div align="center">	
												<?php // bank 20221107 add new button ?>
															<hr/>
															<?php 
															
															if($sess_accesslevel != "4" && $sess_accesslevel != "5" && $sess_accesslevel != "8" ){
																
																if(($sess_accesslevel == "3" && $sess_meta == $get_row["Province"]) || ($sess_accesslevel == "2" || $sess_accesslevel == "1")){  ?>
															<input type="submit" name="btn_approve_edit" id="btn_approve_edit" value="ยืนยันและปรับปรุงข้อมูล<?php echo $the_company_word;?>" 
															onclick = "toggleAction(); return confirm('ต้องการยืนยันปรับปรุงที่อยู่<?php echo $the_company_word;?>นี้?');" 
															/>
															
															<input type="submit" name="btn_reject_edit" id="btn_reject_edit" value="ยกเลิกการแก้ไขที่อยู่<?php echo $the_company_word;?>" 
															onclick = "toggleAction(); return confirm('ต้องยกเลิกการแก้ไขที่อยู่<?php echo $the_company_word;?>นี้?');"
															/>
															
															
												<?php }  }?>
											</div>	 
										</td>
										</tr>
										
										
										
						<!-- </form> -->
						<?php } ?>
					<?php } ?>
				<?php } ?>
					 
					 
<script>

function toggleAction(){
	

		document.getElementById("organization_form").action = 'scrp_approve_company_edit_request.php';

}


</script>
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
                      
                      <?php 
					  
					  	//yoes 20151021 -- add contact from "users" table (if any)
						$contact_sql = "
									select
										*
									from
										users 
									where
										AccessLevel = 4
										and
										user_meta = '$this_cid'
										and
										user_enabled = 1
									
									";
									
						$contact_result = mysql_query($contact_sql);
						
						$count_contact = 0;
						
						while($contact_row = mysql_fetch_array($contact_result)){
						
							$count_contact++;
						
						?>
                        
                        <?php if($count_contact == 1){?>
                        
                         <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ข้อมูลผู้ใช้งาน (ผู้รับมอบอำนาจ)</div></td>
                      </tr>
                        
                        <?php }?>
                        
                        <tr>
                            <td>ชื่อผู้ติดต่อ <?php echo $count_contact;?>: </td>
                            <td>
                            
                            <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 
								|| ($sess_can_manage_user && $sess_meta == $output_values["Province"])
								|| ($sess_accesslevel == 3 && $sess_meta == $output_values["Province"])
								
								){
                            	echo "<a href='view_user.php?id=$contact_row[user_id]'>";
                           }?>
                            
                            
                            <?php echo $contact_row[FirstName] . " " . $contact_row[LastName];?>
                            
                            
                             <?php if($sess_accesslevel == 1){
                            	echo "</a>";
                           }?>
                            
                            </td>
                            <td class="td_left_pad">เบอร์โทรศัพท์: </td>
                            <td><?php echo $contact_row[user_telephone];?></td>
                          </tr>
                          <tr>
                            <td>ตำแหน่ง:</td>
                            <td>
                            
                            <?php echo $contact_row[user_position];?>
                            </td>
                            <td class="td_left_pad"> อีเมล์:</td>
                            <td>
                            
                            <?php echo $contact_row[user_email];?>
                            
                            </td>
                          </tr>
                          
                          
                          <tr>
                            <td>วันที่สมัคร:</td>
                            <td>
                            
                            <?php echo formatDateThai($contact_row[user_created_date], 0,1);?>
                            </td>
                            <td class="td_left_pad"> วันที่อนุมัติ:</td>
                            <td>
                            
                             <?php echo formatDateThai($contact_row[user_approved_date], 0,1);?>
                             
                             <?php 
								
									if($contact_row[user_approved_by] && $sess_accesslevel == 1){
										echo " โดย <a href='view_user.php?id=$contact_row[user_approved_by]'>".getFirstItem("select user_name from users where user_id = '$contact_row[user_approved_by]'")."</a>";
									}
								
								?>
                            
                            </td>
                          </tr>
                          
                          <tr>
                            <td colspan="4"></td>
                            
                          </tr>
                          
                          
                          
                          <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ข้อมูลกรรมการบริษัท (ผู้มีอำนาจ)</div></td>
                      </tr>
                          
                           <tr>
                            <td>ชื่อ-สกุลกรรมการบริษัท: </td>
                            <td>
                            
                            <?php echo $contact_row[FirstName_2] . " " . $contact_row[LastName_2];?>
                            
                            </td>
                             <td>โทรศัพท์: </td>
                            <td>
                            
                            <?php echo $contact_row[user_telephone_2];?>
                            
                            </td>
                          </tr>
                          
                          
                          <tr>
                           
                            <td>ตำแหน่งกรรมการบริษัท: </td>
                            <td>
                            
                             <?php echo $contact_row[user_position_2];?>
                            
                            </td>
                            <td class="td_left_pad"></td>
                            <td></td>
                          </tr>
                          
                        
                        
                        <?php
							
							//ends count contact
							
						}
					  
					  ?>
                      
                      
                      
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ข้อมูลติดต่อ</div></td>
                      </tr>
                      
                      <tr>
                        <td>ชื่อผู้ติดต่อ 1: </td>
                        <td><label>
                          <input type="text" name="ContactPerson1" value="<?php echo $output_values["ContactPerson1"];?>" />
                        </label></td>
                        <td class="td_left_pad">เบอร์โทรศัพท์: </td>
                        <td><input type="text" name="ContactPhone1" value="<?php echo $output_values["ContactPhone1"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำแหน่ง:</td>
                        <td><input type="text" name="ContactEmail1" value="<?php echo $output_values["ContactEmail1"];?>" /></td>
                        <td class="td_left_pad"> อีเมล์:</td>
                        <td><input type="text" name="ContactPosition1" value="<?php echo $output_values["ContactPosition1"];?>" /></td>
                      </tr>
                       <tr>
                        <td>ชื่อผู้ติดต่อ 2: </td>
                        <td><label>
                          <input type="text" name="ContactPerson2" value="<?php echo $output_values["ContactPerson2"];?>" />
                        </label></td>
                        <td class="td_left_pad">เบอร์โทรศัพท์: </td>
                        <td><input type="text" name="ContactPhone2" value="<?php echo $output_values["ContactPhone2"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำแหน่ง:</td>
                        <td><input type="text" name="ContactEmail2" value="<?php echo $output_values["ContactEmail2"];?>" /></td>
                        <td class="td_left_pad"> อีเมล์:</td>
                        <td><input type="text" name="ContactPosition2" value="<?php echo $output_values["ContactPosition2"];?>" /></td>
                      </tr>
                      <tr>
                        <td colspan="4">
                          <div align="center">
                          	<hr />
                            
                            <?php if($sess_accesslevel !=5){ //exec can't do these?>
                                
                                <?php if($mode == "new"){?>                            
                             
                                เพิ่มข้อมูลปฎิบัติตามกฏหมายสำหรับปี:
                             	<?php include "ddl_year.php";?>
                                
                                <input type="submit" name="button" id="button" value="เพิ่มรายชื่อ<?php echo $the_company_word;?>" 
                                onclick = "return confirm('ต้องการเพิ่มรายชื่อ<?php echo $the_company_word;?>นี้?');"
                                 />
                                 
                                 
                                
                                <?php }?>
                                <?php if($mode == "edit" && $sess_accesslevel != "4" && $sess_accesslevel != "5" && $sess_accesslevel != "8" && !$is_company_table_read_only){?>
                                <input type="submit" name="button" id="button" value="ปรับปรุงข้อมูล<?php echo $the_company_word;?>" 
                                
                                onclick = "return confirm('ต้องการปรับปรุงข้อมูล<?php echo $the_company_word;?>นี้?');"
                                />
                                <?php }?>
                                
                             <?php }?>
                          </div>                        
						  </td>
                      </tr>
					  
					  <?php //yoes 20230108 -- add </form> here เพื่อจะได้กด save ข้อมูล สปก ได้ ?>
					  </form>
					 <?php //yoes 20230108 -- add </form> here เพื่อจะได้กด save ข้อมูล สปก ได้ ?>
						
							<tr>
						
                                    	<td colspan="10">
                                     	<div align="center" style=" color:#060">
											<b><font color=blue>ประวัติการเปลี่ยนที่อยู่ </font></b>
                                        </div>
                                        </td>
                            </tr>
									
									
                                    
                                     <tr>
                                    	       
										<td align="center"> 
                                        
                                        <div align="center">
                                     	<b>  ชื่อสถานประกอบการ </b> 
                                          </div>
                                          
                                          </td>											   
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	<b>  จังหวัดที่ส่ง </b> 
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center"> 
                                        
                                        <div align="center">
                                     	<b>  จังหวัดที่รับ </b> 
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center">
                                        
                                        <div align="center">
                                        
										<b> วันที่ส่ง </b> 
                                        
										
                                        </div>
                                        </td>
                                        
										<?php //bank 20230113 add file attach ?>
                                        <td align="center">
                                        
                                        <div align="center">
                                        
										<b> เอกสารแนบ </b> 
                                        
										
                                        </div>
                                        </td>
                                     

                                        </td>
										
										
										
										
										
                                    </tr>
									
									
									<?php 
									//yoes 20231124
									if($mode != "new"){?>

					  <?php   				
						$log_address_sql = "
											
											SELECT *
												
											FROM 
												company_edit_request
	
												
											Where cid = '$this_id'
											and Province_accepted != 0
											
										
										";
									
									//$request_filter
									
									//echo $request_sql;
									
									
									$log_address_result = mysql_query($log_address_sql);
									
									
									while($log_row = mysql_fetch_array($log_address_result)){?>
									
						 
						 

                                     <tr>
                                       <td>
                                       	<?php echo formatCompanyName($log_row["CompanyNameThai"],$log_row["CompanyTypeCode"]);?>
									   

									   
									   </td>
                                       <td align="center" style="text-align: center"> <?php 
									   	
										echo getFirstItem("select province_name from provinces where province_id = '".$log_row["Province_sent"]."'");
									   
									   ?></a></td>
                                       <td align="center" style="text-align: center"><?php 
									   
										$province_accetp = getFirstItem("select province_name from provinces where province_id = '".$log_row["Province_accepted"]."'");
									   	if($province_accetp == '0'){
										
										echo ""; }
										else{
										echo getFirstItem("select province_name from provinces where province_id = '".$log_row["Province_accepted"]."'");	
										
										}
	
									   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   //bank 20230113 change format date to thai
										echo formatDateThai($log_row[edit_date],1,5);
										   
									   ?></td>
										
										<?php //bank 20230113 add file attach ?>
										  
									    <td align="center" style="text-align: center">
                                       	<?php 
																//$this_id_temp = $this_id;
																//$disable_delete  = 1; 
																$file_type = "company_address_info_file";  
																$file_id = $log_row["file_id"];																
																include "doc_file_links.php";                                                    
																
																//echo $file_id;
																//$this_id = $this_id_temp;
																//$disable_delete = 0;
										?>
									   

									   
									   </td>
							
                                     </tr>
					  
						<?php } ?>		

										
					  
					  <?php  //bank add 20221221 Org Change Name Hsitroy table  ?>
						
									<tr>
									
                                    	<td colspan="10">
                                     	<div align="center" style=" color:#060">
										<hr />
											<b><font color=blue>ประวัติการเปลี่ยนชื่อของสถานประกอบการ </font></b>
                                        </div>
                                        </td>
                                    </tr>
									
									
                                    
                                     <tr>
                                    	       
										<td align="center"> 
                                        
                                        <div align="center">
                                     	 <b>ชื่อสถานประกอบการ</b>
                                          </div>
                                          
                                          </td>		

										<td align="center"> 
                                        
                                        <div align="center">
                                     	 <b>ประเภทสถานประกอบการ</b>
                                          </div>
                                          
                                          </td>											  
                                        
                                          
                                          
                                          <td align="center"> 
                                        
                                          <div align="center">
                                     	   <b>  วันที่เปลี่ยน </b>
											
                                          </div>
                                          
                                          </td>
										  
                                    </tr>
					  
					  
					  <?php   				
						$log_name_sql = "
											
											SELECT *
											FROM company_full_log
											WHERE cid = '$this_id'
											GROUP BY CompanyNameThai , CompanyTypeCode
											ORDER BY LastModifiedDateTime DESC
											
										
										";
									
									//$request_filter
									
									//echo $request_sql;
									
									
									$log_history_name = mysql_query($log_name_sql);
									
									
									while($log_row = mysql_fetch_array($log_history_name)){?>
									
						 
						 

                                     <tr>
                                       <td>
                                       	<?php echo formatCompanyName($log_row["CompanyNameThai"],$log_row["CompanyTypeCode"]);?>
									   

									   
									   </td>
									   
									   <td align="center" style="text-align: center">
                                       	<?php $company_type_name = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '$log_row[CompanyTypeCode]'");
											   echo $company_type_name; ?>

									   
									   </td>

									   
                                       <td align="center" style="text-align: center">
									   
									    
                                       	<?php //bank 20230113 change format date to thai
											echo formatDateThai($log_row["LastModifiedDateTime"],1,5);
										?>
									   

									   
									   </td>
									 
									   
									   
	
                                     </tr>
					  
						<?php } ?>
						
						
						<?php 
									//yoes 20231124
									} //end if($mode != "new"){?>
					  
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					 
					  
					  
					  
                      <tr>
                        <td colspan="4">
                          <div align="left">
                          	<hr />
                            <?php if($mode == "edit" && ($sess_accesslevel == 1 || $sess_accesslevel == 2)){?>
                            <input type="submit" name="btn_delete" id="btn_delete" value="'ลบ'<?php echo $the_company_word;?>" 
							
                             onclick = "return doConfirmDelete();"
                            />
                            <script>
								function doConfirmDelete(){
									confirm_1 = confirm('ต้องการลบข้อมูล<?php echo $the_company_word;?>นี้? ข้อมูล<?php echo $the_company_word;?>ที่ถูกลบไปแล้วจะไม่สามารถนำกลับคืนมาได้');
									
									if(confirm_1){
										return confirm('กดยืนยันอีกครั้งเพื่อลบ<?php echo $the_company_word;?>นี้');
									}else{
										return false;
									}
								}
							</script>
                            <?php }?>
                          </div>              
						  <hr />
						  </td>
                      </tr>
				 </form>	  
				 

              </table>
			  




              
              
			  <?php //break bank ?>
				
			
							
							
									
			
			
              
              
              
              
              
              
              
             <table style=" padding:10px 0 0px 0; " id="history" width="100%">
             
             	<tr>
                    <td >
                    
                    <div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการปฏิบัติตามกฎหมาย</div>
                    
                    <table border="0" style="color: #006699">
                            	<tr>
                                	<td >
                                    	<img src="decors/green.gif" alt="ทำตามกฎหมาย" title="ทำตามกฎหมาย">
                                    </td>
                                    <td valign="middle">
                                    	= ทำตามกฎหมาย 
                                    </td>
                                    <td >
                                    	<img src="decors/red.gif" alt="ไม่ทำตามกฎหมาย" title="ไม่ทำตามกฎหมาย">
                                    </td>
                                    <td valign="middle">
                                    	= ไม่ทำตามกฎหมาย 
                                    </td>
                                    <td >
                                    	<img src="decors/yellow.gif" alt="ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน" title="ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน">
                                    </td>
                                    <td valign="middle">
                                    	= ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน 
                                    </td>
                                    <td >
                                    	<img src="decors/blue.gif" alt="ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?>" title="ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?>">
                                    </td>
                                    <td valign="middle">
                                    	= ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?> 
                                    </td>
									<td >
                                    	<img src="decors/orange.gif" alt="นับว่าปฏิบัติฯเนื่องจากเข้าข่ายของคำวินิจฉัยกฤษฎีกา" title="นับว่าปฏิบัติฯเนื่องจากเข้าข่ายของคำวินิจฉัยกฤษฎีกา">
                                    </td>
                                    <td valign="middle">
                                    	= นับว่าปฏิบัติฯเนื่องจากเข้าข่ายของคำวินิจฉัยกฤษฎีกา
                                    </td>			

									<?php //bank add new status 20230103 ?>
                                	<td >
                                    	<img src="decors/purple.jpg" alt="นับว่าปฏิบัติตามกฎหมายเนื่องจากการยุติการดำเนินคดีทางกฎหมาย" title="นับว่าปฏิบัติตามกฎหมายเนื่องจากการยุติการดำเนินคดีทางกฎหมาย">
                                    </td>
                                    <td valign="middle">
                                    	= นับว่าปฏิบัติตามกฎหมายเนื่องจากการยุติการดำเนินคดีทางกฎหมาย
                                    </td>
                                    
									
									
                                </tr>
                                <tr>
                                	<td >
                                    	<img src="decors/grey.gif" alt="ไม่มีรายละเอียด ม.33 หรือ ม.35" title="ไม่มีรายละเอียด ม.33 หรือ ม.35">
                                    </td>
                                    <td valign="middle" colspan="7">
                                    	= ไม่มีรายละเอียด ม.33 หรือ ม.35
                                    </td>
                                    
                                </tr>
                            </table>
                    
                    </td>
                  </tr>
                  
                  
                   <?php
						   
					 //yoes 20160111
					 //allow to see full history log here
					 if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 6 ){
					 
					 ?>
					 
						 <tr>
							  <td >
							  <div align="left">
							  
									<a href="view_full_log.php?id=<?php echo $this_cid;?>" target="_blank" style="font-weight: bold;">>> ดู log การแก้ไขข้อมูลโดยเจ้าหน้า คลิกที่นี่ <<</a>
							  </div>
							 </td>
						</tr>
						
					 
					 <?php }?>
                  
                  
                  
                  
                  <tr>
                  
                  	<td>
                    
                    <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; 
					
					<?php if($is_dbd_merged_from){
						echo "display: none;";
					}
					?>
					
					
					">
                    	<tr bgcolor="#9C9A9C" align="center" >
                    	  <td><div align="center"><span class="column_header">ปี</span> </div></td>
           	           	   
                            
                            <td>
                            	<div align="center"><span class="column_header">จำนวน<?php echo $the_employees_word;?> (ราย)</span> </div>                            </td>
                            
                             
                              <?php if(1==0){//yoes 20160614 -- remove this as per witza's request?>
                             <td><div align="center"><span class="column_header">จดหมายแจ้ง</span> </div></td>
                             <?php }?>
                             
                             <td>
                            	<div align="center"><span class="column_header">อัตราส่วน</span> </div>                            </td>
                            
                            <td ><div align="center"><span class="column_header">รับคนพิการเข้าทำงาน<br />
                              ตามมาตรา 33 (ราย)</span></div></td>
                            
                            <!--
                            <td ><div align="center"><span class="column_header">เงินที่ต้องส่งเข้ากองทุน<br />
                            ตามมาตรา 34</span> </div></td>-->
                            
							
							<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
							
							<?php }else{?>
							
								<td ><div align="center"><span class="column_header">จ่ายเงินแทนการรับคนพิการ<br />
								ตามมาตรา 34</span> </div></td>
								
							<?php }?>
                           
                            <td ><div align="center"><span class="column_header">การให้สัมปทาน<br />
                            ตามมาตรา 35 (ราย)</span></div></td>
                            <td ><div align="center"><span class="column_header">สถานะ</span> </div></td>
                    	</tr>
                        
                        <?php
						
						
						
						//echo ".... $is_merged";
						//generate letter history
							$get_history_sql = "
										select
											 *
										from 
											lawfulness
										where
											CID = '$this_id'
											and
											Year <= '".(date("Y")+1)."'
											and
											Year >= '$dll_year_start'	
										
										order by Year desc
										
										";
										
							//yoes 20160623 -- in case of merged...
							if($is_merged){
								
								$get_history_sql = "
										select
											 *
										from 
											lawfulness
										where
											CID = '$this_id'
											and
											Year <= '".(date("Y")+1+1000)."'
											and
											Year >= '".($dll_year_start+1000)."'	
										
										order by Year desc
										
										";
								
							}
							
							//echo $get_letter_sql;
							
							$history_result = mysql_query($get_history_sql);
							$cnt = 0;
							while ($lawful_row = mysql_fetch_array($history_result)) {
								
								
								$cnt = $cnt + 1;
								$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_".$lawful_row["Year"]."'"),100);
								
								//yoes 20151201 -- just show real value
								$employees_to_use = $lawful_row["Employees"];
								/*
								if($lawful_row["Employees"] > 0){
									$employees_to_use = $lawful_row["Employees"];
								}else{
									$employees_to_use = $output_values["Employees"];
									
									if($lawful_row["LawfulStatus"] == 3 && $lawful_row["Year"] == 2011){
									
										$employees_to_use = 0;
									
									}
									
									
								}*/
								
								$final_employee = getEmployeeRatio( $employees_to_use,$ratio_to_use);
			
							
								//yoes 20181108
								//change this to standard function
								$curator_usee = getNumCuratorFromLid($this_lid);
								/*
								$curator_usee = getFirstItem("select 
									count(*) 
								from 
									curator 
									, lawfulness
								where 
								
									curator_lid = LID
									and
									Year = '".$lawful_row["Year"]."' 
									and
									CID = '$this_id'							
									
										
									and 
									
									(
									
										curator_parent in(
										
											select curator_id 
											from 
												curator 
												, lawfulness
											where 
											
												curator_lid = LID
												and
												Year = '".$lawful_row["Year"]."' 
												and
												CID = '$this_id'	
											
												and curator_parent = 0
										
										)
										
										
										
										OR (
									
											curator_is_disable = 1
											and 
											curator_parent = 0
										
										)
									
									)
									
									
									");*/
									
									
									$the_sql = "select sum(receipt.Amount) 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_id'
														and
														is_payback != 1
														";
														
									$paid_money_history = getFirstItem("$the_sql");
									
									$the_sql = "select sum(receipt.Amount) 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_id'
														and
														is_payback = 1
														";
														
									$back_money_history = getFirstItem("$the_sql");
									
									$paid_money_history = $paid_money_history - $back_money_history;
									
									
									//-----------
									
									//echo $output_values["Province"];
									//echo "-- $this_lawful_year";
									
									
									if($this_lawful_year == 2011){
										
										//use wage-rate by province instead
										$wage_rate = default_value(getFirstItem("select province_54_wage from provinces where province_id = '".$output_values["Province"]."'"),0);										
										$wage_rate = $wage_rate/2;
										
									}else{
									
										$wage_rate = default_value(getFirstItem("select var_value from vars where var_name = 'wage_".$this_lawful_year."'"),159);
									
									}
									
									
									//$year_date = date("z", mktime(0,0,0,12,31,$this_lawful_year));
									$year_date = 365;
									
									$start_money = $extra_employee*$wage_rate*$year_date;
									
									
							
							?>
                            
                                <tr >
                                  <td valign="top">
                                  
                                 
                                  	<div align="center">
                                    
                                    
                                  	<?php if($sess_accesslevel != 4 ){ ?>
                                        <a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>">
 	                               <?php }?>
                                   
                                            <?php 
											
												
												if($is_merged){
													echo formatYear($lawful_row["Year"]-1000);
												}else{
													echo formatYear($lawful_row["Year"]);
												}
												
												?>
                                            
                                   <?php if($sess_accesslevel != 4 ){ ?>
                                        </a>
                                    <?php }?>
                                    
                                    
                                    <?php 
									   	
											//yoes -> show submmited info
											$submitted_row = getFirstRow("
												select 
													lawful_submitted
													, lawful_submitted_on
													, lawful_approved_on
													, lawful_approved_by
												from
													lawfulness_company
												where
													CID = '" . $this_cid . "'
													and
													Year = '".$lawful_row["Year"]."'
												");
												
											//echo $this_cid;
											//print_r($submitted_row);
											
											if($submitted_row[lawful_submitted] == 1 || $submitted_row[lawful_submitted] == 2){
												
												
												echo "<br><font color='#003300'>มีการยื่นแบบฟอร์มออนไลน์มาเมื่อวันที่ ".formatDateThai($submitted_row[lawful_submitted_on],1, 1) ."</font>";
												
											}
											
											if($submitted_row[lawful_submitted] == 2){
												
												$approved_by_name = getFirstItem("select user_name from users where user_id = ".$submitted_row[lawful_approved_by]);
												
												echo "<br><font color='#003300'>เจ้าหน้าที่ทำการบันทึกข้อมูลเข้าระบบแล้วเมื่อวันที่ ".formatDateThai($submitted_row[lawful_approved_on],1, 1) ." โดย $approved_by_name</font>";
												
											}
									   
									   ?>
									   
									   <?php //yoes 20220606 
								 
										//import org history
										$get_log_sql = "
										
											select
												lid
												, cid
												, year
												, lawful_submitted_on
												, lawful_approved_on
												, lawful_approved_by
												
											from
												lawfulness_company_full_log
											where
												lawful_approved_on != '0000-00-00 00:00:00'
												and
												log_source = 'scrp_transfer_data.php'
												and
												CID = '" . $this_cid . "'
												and
												Year = '".$lawful_row["Year"]."'
											group by
												lid
												, cid
												, year
												, lawful_submitted_on
												, lawful_approved_on
												, lawful_approved_by
											ORDER BY 
												
												lawful_approved_on desc
												, lawful_submitted_on asc
										
										";
										
										$get_log_result = mysql_query($get_log_sql);
										
										if(mysql_num_rows($get_log_result)){
											echo "<br><font color=green>ประวัติการนำเข้าข้อมูล:";
										}
										
										while($get_log_row = mysql_fetch_array($get_log_result)){			
											//
											echo "<br>นำเข้าข้อมูลวันที่ :" . formatDateThai($get_log_row[lawful_approved_on],1,1);
											echo " โดย " . getFirstItem("select user_name from users where user_id = ".$get_log_row[lawful_approved_by]);
										}
										
										if(mysql_num_rows($get_log_result)){
											echo "</font>";
										}
									 
									 ?>
                                    
                                    
                                    
                                    
                                        
                                   </div>                                  </td>
                                  
                                   
                                    
                                    <td valign="top">
                                   	 <div align="right">
									 
									 <?php echo number_format($employees_to_use,0);?>
                                     
                                     </div>                                    </td>    
                                    
                                    
                                     
                                     <td valign="top">
                                    
                                    <div align="center">
                                        <?php echo $ratio_to_use;?> ต่อ 1 = <?php echo $final_employee;?> ราย                                    </div>                                    </td>
                                    
                                    
                                        
                                    <td valign="top">
                                    
                                    
                                    <div align="right">
                                        
                                        <?php 
                                        
                                        //yoes 20150803 -> company shouldn't be able to see this
                                        //if($sess_accesslevel != 4 || ($sess_accesslevel == 4 && $lawful_row["Year"] >= 2015)){
                                        if($sess_accesslevel != 4){ ?>
                                        <a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&le=le&year=<?php echo $lawful_row["Year"];?>">
                                        <?php }?>
                                        
											<?php if($lawful_row["Year"] == $this_year){?>
												<span id="txt_hire_numofemp_history">
											<?php }?>
											
												<?php echo $lawful_row["Hire_NumofEmp"]?>
												
											<?php if($lawful_row["Year"] == $this_year){?>
												</span>
											<?php }?>
											
                                        <?php //yoes 20150803 -> company shouldn't be able to see this
                                        if($sess_accesslevel != 4){ ?>
                                        </a>
                                        <?php }?>
                                    
                                    	
                                        
                                        <?php //yoes 20151118 -- also check duped มาตรา 34 here
										
											//echo "yes?";
										
											$check_dupe_sql = "
											
												select
													count(*)
												from
													lawful_employees
												where
													le_code in (
											
														select 
															le_code
														from
															lawful_employees
														where
															le_cid = '$this_id'
															and
															le_year = '$lawful_row[Year]'
															
													)													
													and
													le_year = '$lawful_row[Year]'
											
											
											";
											
											//echo $check_dupe_sql;
											/*
											if(getFirstItem($check_dupe_sql)){
												
											}
											*/
										?>
                                    
                                    
                                    </div>                                    
                                    
                                    
                                    
                                    </td>
                                    
                                    
                                    
                                    <td valign="top" 
									
									<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
									style="display:none;"
									<?php }?>
									
									>
                                    	<div align="right">
                                        
                                        <?php
										
										$the_sql = "select * 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '".$lawful_row["Year"]."'
														and
														lawfulness.CID = '$this_id'
														
														order by
														
														PaymentDate asc
														
														";
														
										$paid_money_history_result = mysql_query($the_sql);
										
										
										if(mysql_num_rows($paid_money_history_result)){
										
										?>
                                        
                                        
                                        <table border="1" style="border-collapse:collapse;" cellpadding="3" cellspacing="0">
                                        	<tr style="background-color:#CCCCCC;">
                                            	<td>
                                                 <div align="center">เล่มที่                                                </div></td>
                                                <td>
                                                  <div align="center">เลขที่                                                </div></td>
                                                <td>
                                                  <div align="center">จำนวนเงิน (บาท)                                                </div></td>
                                            </tr>
                                            
                                        
                                        
                                        <?php
										
										
										}
										
										while ($pmh_row = mysql_fetch_array($paid_money_history_result)) {
										
										?>
										
                                        <tr>
                                        	<td>
                                              <div align="left">
											  
                                              <?php if($sess_accesslevel != 4){ ?>
											  <a href="view_payment.php?id=<?php echo $pmh_row["RID"];?>">
											  <?php }?>
                                              
											  <?php echo $pmh_row["BookReceiptNo"];?>
                                              
                                              <?php if($sess_accesslevel != 4){ ?>
                                              </a>
                                              <?php }?>
                                              
                                              </div></td>
                                             <td>
                                               <div align="left"><?php echo $pmh_row["ReceiptNo"];?>                                            </div></td>
                                            	<td>
                                                  <div align="right">
                                                  
                                                  
                                                  
												  <?php if($pmh_row["is_payback"]){echo "<font >-";}?>
                                                  
												  <?php echo formatNumber($pmh_row["Amount"]);?>                                            
                                                  <?php if($pmh_row["is_payback"]){echo "</font>";}?>
                                                  
                                                  
                                                  </div></td>
                                        </tr>
                                        
                                        
                                        <?php	
										
										}
										
										?>
                                        
                                        
                                        
                                        
                                        <?php if(mysql_num_rows($paid_money_history_result)){?>
                                        
                                         <tr>
                                        	<td>
                                           </td>
                                             <td>
                                               รวม</td>
                                            	<td>
                                                  <div align="right">
                                                  
                                                  <?php if($sess_accesslevel != 4){ ?>
                                                  <a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>#the_payment_details">
                                                  <?php }?>
												  
												  <?php echo formatMoney($paid_money_history) ;?>                                       
                                                   
                                                   <?php if($sess_accesslevel != 4 ){ ?>
                                                   </a>
                                                   <?php }?>
                                                    
                                                    
                                                    </div></td>
                                        </tr>
                                        
                                        
                                        	</table>
                                        
                                        <?php }else{ //end if if(mysql_num_rows($paid_money_history_result)){?>
                                        
                                        
												<?php if($lawful_row["Year"] == $this_year){?>
													<span id="txt_total_paid_history">
												<?php }?>
														0.00       
												<?php if($lawful_row["Year"] == $this_year){?>
													</span>
												<?php }?>												 
                                                 
                                            
                                        
                                        <?php }?>
                                        
                                        
                                        
                                      </div>                                    </td>
                                   
                                    <td valign="top">
                                    
                                    	
                                   	 	<a href="organization.php?id=<?php echo $this_id;?>&focus=lawful&year=<?php echo $lawful_row["Year"];?>&curate=curate">
                                       
									   
									   
                                        
											<div align="right"><?php if($lawful_row["Year"] == $this_year){?>
												<span id="txt_curator_user_history">
											<?php }?><?php 
											
												
												$this_year_lid = getFirstItem("select lid from lawfulness where Year = '".$lawful_row["Year"]."' and CID = '$this_id'");
												$this_curator_usee = getNumCuratorFromLid($this_year_lid);
												
												echo $this_curator_usee;
											
											
											?><?php if($lawful_row["Year"] == $this_year){?>
												</span>
											<?php }?></div>       
                                        
                                        
                                         </a>                            
                                        
                                         
                                         </td>
                                    
                                    <td valign="top">
                                    
                                    	<div align="center"><?php if($lawful_row["Year"] == $this_year){?>
													
													<img id="img_lawfulStatus_00" src='decors/red.gif' border='0' alt='ไม่ทำตามกฏหมาย' title='ไม่ทำตามกฏหมาย' style="display:none;" />
													<img id="img_lawfulStatus_01" src='decors/green.gif' border='0' alt='ทำตามกฏหมาย' title='ทำตามกฏหมาย' style="display:none;"/>
													<img id="img_lawfulStatus_02" src='decors/yellow.gif' border='0' alt='กำลังดำเนินงาน' title='กำลังดำเนินงาน' style="display:none;"/>
													<img id="img_lawfulStatus_03" src='decors/blue.gif' border='0' alt='ไม่เข้าข่ายจำนวนลูกจ้าง' title='ไม่เข้าข่ายจำนวนลูกจ้าง' style="display:none;"/>
													<img id="img_lawfulStatus_05" src='decors/orange.gif' border='0' alt='นับว่าปฏิบัติตามกฎหมายเนื่องจากเข้าข่ายของคำวินิจฉัยกฤษฎีกา' title='นับว่าปฏิบัติตามกฎหมายเนื่องจากเข้าข่ายของคำวินิจฉัยกฤษฎีกา' style="display:none;"/>
													<img id="img_lawfulStatus_06" src='decors/purple.jpg' border='0' alt='นับว่าปฏิบัติตามกฎหมายเนื่องจากการยุติการดำเนินคดีทางกฎหมาย' title='นับว่าปฏิบัติตามกฎหมายเนื่องจากการยุติการดำเนินคดีทางกฎหมาย' style="display:none;"/>
													
											<?php }else{
													
												 
												
													echo getLawfulImageFromLID($lawful_row["LID"]);
													//echo $lawful_row["Year"];
													//echo $this_year;
											
												
											}?>
											
										</div>    
										
										
								<?php // bank add JQuery to general tab 20222712 ?>		
										<br>
										<?php $chk_sent_court = "select 
														  distinct a.year 
														from 
														  lawfulness a 
														  left join lawfulness_meta b on b.meta_lid = a.lid 
														where 
														  a.cid = '".doCleanOutput($lawful_row["CID"])."'
														  and a.year = '".doCleanOutput($lawful_row["Year"])."'
														  and b.meta_for like '%courted_flag%'
														";
											$chk_lid = getFirstItem($chk_sent_court);
											if($chk_lid == $lawful_row["Year"]){?>	
									
									
				
										<div align="center" id="law_status_output_<?php echo $cnt ?>">
										

											
											
											วันที่รับรื่อง :  <span id="lawful_case_accepted_date_<?php echo $cnt ?>">  </span>
											<br>
											สถานะปัจจุบัน : <span id="lawful_lov_label_<?php echo $cnt ?>">  </span>
														
										
										</div>
										
										<?php }else{ echo "";} ?>
										
									</td>
											



										<script>
										function getLawStatus(cid,year,cnt)	{
										  $.ajax({
											el: '#law_status_output_' + cnt,
											type: 'GET',
											url: "https://law.dep.go.th/law_ws/getCaseDetails.php",
											dataType: 'json',
											data: { case_type: 'hire', cid: cid , year: year },
											success: function(response) {
											  // The request was successful, the response is a JSON array
											  //$('#law_status_jquery').html(response);
											  //responseData = response;
											  var mm;	
											  var nn;
											  nn = response.data.case;
											  mm = response.data.case_detail;
											  
											
											  
											  $("#lawful_case_accepted_date_" + cnt).html(nn.case_accepted_date);
											  $("#lawful_lov_label_" + cnt).html(mm[0].lov_label);
											   //$('#law_status_output_'+ LID_CNT).html(response);
											   //document.getElementById('law_status_output_<?php echo $LID_CNT ?>').innerHTML = response;

											   console.log(nn);
											   
											   
											   
											  // var data = JSON.parse(response.data.case.case_type);
											   
											   //document.getElementById('law_status_output_<?php echo $LID_CNT ?>').innerHTML = response.data.case;
											}

										  });
										}
										//document.getElementById('law_status_output_<?php echo $LID_CNT ?>').innerHTML = getLawStatus(<?php echo $post_row["CID"]?>,<?php echo $post_row["Year"]?>,<?php echo $LID_CNT?>);
										getLawStatus(<?php echo $lawful_row["CID"]?>,<?php echo $lawful_row["Year"]?>,<?php echo $cnt?>);

										<?php  //echo $js_commands; ?>
										

										</script> 	
											
									<?php // End bank add JQuery 20222712 ?>	
                                </tr>
                            
                            
                            <?php 
									
									
									//yoes 20160613
									$lawful_history_count++;
							
								} //end while -> history table?>
                      </table>
                    
                    
                    	<?php 
						
						//yoes 20160613
						//no history count? --> see if have lawfulness 3000+
						if(!$lawful_history_count){
							
							$is_merged = getFirstItem("
								
								select
									count(*)
								from
									lawfulness
								where
									cid = '$this_cid'
									and
									year > 3000
							
							");	
							
							if($is_merged){
							
							?>
                            
                            	<div align="center" style="margin: 20px; color: #F30">
                                
                                	
                                	<strong>*** ข้อมูลของสถานประกอบการนี้ ถูกรวมไปอยู่กับสถานประกอบการอื่นแล้ว</strong>
                                </div>
                            
                            <?php	
								
							}
							
						}
						
						?>
						
						<?php if($is_dbd_merged_from){?>
						
							<div id="ajax_organization_history_table">						  
								<p v-html="inside_html"></p>						  
							</div>
							<script>
								new Vue({
									el: '#ajax_organization_history_table',
									data: { inside_html: '<img src="bigrotation2.gif" width=20 height=20/> .. กำลังโหลดประวัติการปฏิบัติตามกฎหมาย ..' },
									  mounted () {
										axios
										  .post('ajax_organization_history_table.php', "the_id=" + <?php echo $this_id;?>)
										  .then(response => (this.inside_html = response.data))
									  }
								})
							</script>
						
						<?php }	?>
                    
                    
                    </td>
                    
                 </tr>
                  
                  
            </table>
            
            
            
            
            
              
              	<?php
				
					
					
					
				
					//only show this to non-company
					if($sess_accesslevel !=4){
				?>
                
                	
                	<?php
					
					
						
						$whitelist = array(
							'127.0.0.1',
							'::1'
						);
						
						if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
							$is_local_host = 1;
							
							//echo $is_local_host;
						}
					
					 if((($sess_accesslevel == 1) || ($sess_accesslevel == 2)|| ($sess_accesslevel == 3) || ($sess_accesslevel == 8)) && !$is_local_host){
						 
						 
						 ?>
                	 <div style=" padding:10px 0 0px 0; " id="sequestration" >
            			 <?php include  'organization_sequestration.php';?>
            		 </div>
            		 <?php }?>
              		
                    
                    <table style=" padding:10px 0 0px 0; " id="official">
                      
                      <tr>
                        <td><div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการส่งจดหมายแจ้ง</div> 
                        
                        <?php
						
						
						//echo $this_year;
							//echo $this_lawful_year;
						
						
						 if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only){?>
                        <a href="org_list.php?mode=letters&search_id=<?php echo $this_id;?>&for_year=<?php echo $this_lawful_year;?>">+ ส่งจดหมายแจ้ง</a>                         
                        <?php }?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                        
                        <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                    	  <td><div align="center"><span class="column_header">วันที่</span> </div></td>
           	           	    <td>
                            	<div align="center"><span class="column_header">ครั้งที่</span> </div></td>
                            <td ><div align="center"><span class="column_header">หนังสือเลขที่</span> </div></td>
                            <td ><div align="center"><span class="column_header">เลขที่ลงทะเบียน</span> </div></td>
                            <td ><div align="center"><span class="column_header">ชื่อผู้รับจดหมาย</span> </div></td>
							 <td ><div align="center"><span class="column_header">วันที่/เวลาที่รับจดหมาย</span> </div></td>
                              <td ><div align="center"><span class="column_header">ไฟล์แนบ</span> </div></td>
                           
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only){?>
                            <td >  <div align="center"><span class="column_header">ปรับปรุงข้อมูล</span> </div></td>
                            <?php }?>
                           
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only){?>
                            <td >  <div align="center"><span class="column_header">ลบข้อมูล</span> </div></td>
                            <?php }?>
                          </tr>
                             
                         <?php 
							
							
							
							//generate letter history
							$get_letter_sql = "select *
										from documentrequest a, docrequestcompany b
										where
											a.rid = b.rid
										and 
											CID = '$this_id'
										
										$conditions	
										
										and is_hold_letter = '0'
										
										order by RequestDate desc
										
										";
							
							//echo $get_letter_sql;
							
							$letter_result = mysql_query($get_letter_sql);
							
							while ($post_row = mysql_fetch_array($letter_result)) {
								
								$letter_count++;
							?>
                        <tr bgcolor="#ffffff" align="center" >
                          <td> <?php echo formatDateThai($post_row["RequestDate"]);?></td>
                       	    <td>

                            	<?php echo doCleanOutput($post_row["RequestNum"]);?>                          </td>
                            <td ><a href="view_letter.php?id=<?php echo doCleanOutput($post_row["RID"]);?>"><?php echo doCleanOutput($post_row["GovDocumentNo"]);?></a> </td>
                            <td >
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only){?>
                              <form action="scrp_add_register.php"  method="post" enctype="multipart/form-data">
                            <?Php } ?>
                            <input name="PostRegNum" type="text" value="<?php echo doCleanOutput($post_row["PostRegNum"]);?>" />
                            <input name="DID" type="hidden" value="<?php echo doCleanOutput($post_row["DID"]);?>" />
                            <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                            <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                            
                          
                                                  </td>
                            <td ><input name="PostReceiverName" type="text" value="<?php echo doCleanOutput($post_row["PostReceiverName"]);?>" /></td>
                             <td >
                             <input id="PostReceivedTime<?php echo $letter_count;?>" name="PostReceivedTime" type="text" value="<?php 
							 
							 	if($post_row["PostReceivedTime"] != "0000-00-00 00:00:00"){
							 		echo doCleanOutput($post_row["PostReceivedTime"]);
								}
								
								?>" />
                             
                            
                            	<script>
							 
							 $.datetimepicker.setLocale('th');
							 
							 jQuery('#PostReceivedTime<?php echo $letter_count;?>').datetimepicker({
								  datepicker:true,
								  yearOffset:543
								  
								});
							 </script>

                            
                             </td>
                            <td >
                            
                            
                            <input type="file" name="docrequestcompany_docfile" id="docrequestcompany_docfile" />
                            
                            
                            <?php 
                                
									
									//also see if there are any attached files
									$docrequestcompany_file_path = mysql_query("select 
																			* 
																	   from 
																			 files 
																		where 
																			file_for = '".$post_row["DID"]."'
																			and
																			file_type = 'docrequestcompany_docfile'
								
																	");
																	
									//echo $docrequestcompany_file_path;
																			
									while ($file_row = mysql_fetch_array($docrequestcompany_file_path)) {
									
									?>
										<a href="hire_docfile/<?php echo $file_row["file_name"];?>" target="_blank">ไฟล์แนบ</a>
										
										<?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3){?>
										<a href="scrp_delete_docrequestcompany_file.php?id=<?php echo $file_row["file_id"];?>&this_cid=<?php echo $this_cid;?>&this_year=<?php echo $this_year;?>" title="ลบไฟล์แนบ" onClick="return confirm('คุณแน่ใจหรือว่าจะลบไฟล์แนบ? การลบไฟล์ถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบไฟล์ได้');"><img src="decors/cross_icon.gif" alt="" height="10"  border="0" /></a>
										<?php }?>
	
										
									<?php
									
									
									}
									
									
									
									
									?>
                            
                            </td>
                            
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only){?>
                            <td>
                              
                           		<input name="add_code" type="submit" value="ปรับปรุงข้อมูล" />							  
                            </form>                              
                            </td>
                            <?php }?>  
                            
							<?php if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only){?>
                                <td>
                                        <div align="center"><a href="scrp_delete_doccom.php?id=<?php echo doCleanOutput($post_row["DID"]);?>&cid=<?php echo doCleanOutput($output_values["CID"]);?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div>
                                    
                                </td>
                            <?php }?>
                            
                            
                            
                          </tr>
                          <?php
						  	} //generate letter history?>
                      </table>                       
                      
                                                   
                      </td>
                      </tr>
                      
                      
                      
                      
                      
                      <!---           แจ้งอายัด -                  -              -->
                                          
                      
                      <tr>
                        <td><hr /><div style="font-weight: bold; padding:0 0 5px 0;">เอกสารจาก<?php echo $the_company_word;?></div></td>
                      </tr>
                      <tr>
                        <td><?php
                        
							//echo $this_year;
							//echo $this_lawful_year;
						
							//try to show last "document_requests" entry
							//print_r($_POST);
							
							if($_POST["ddl_year"]){
								$this_docr_year = doCleanInput($_POST["ddl_year"]);
								$doc_conditions = " and docr_year = '$this_docr_year'";
							}elseif($_GET["ddl_year"]){
								$this_docr_year = doCleanInput($_GET["ddl_year"]);
								$doc_conditions = " and docr_year = '$this_docr_year'";
							}elseif($_GET["year"]){
								$this_docr_year = doCleanInput($_GET["year"]);
								$doc_conditions = " and docr_year = '$this_docr_year'";
							}else{
								$this_docr_year = date('Y');
							}
							
							$sql = "select * 
								from 
									document_requests
								where 
									docr_org_id  = '$this_id'
									
									$doc_conditions
								
								order by docr_id desc
								limit 0,1";
							//echo $sql;
							$docr_row = getFirstRow($sql);
						
							if($docr_row["docr_status"] == 1){
								$stat_1_checked = 'checked="checked"';
								$stat_2_checked = '';
								$date_to_show = ($docr_row["docr_date"]);
							}else{
								$stat_1_checked = '';
								$stat_2_checked = 'checked="checked"';
								$date_to_show = date("Y-m-d");
							}
						
						?>
                        <table border="0">
                        <form action="?id=<?php echo $this_id;?>&focus=official" method="post">
                         <tr>
                            <td colspan="2">ข้อมูลประจำปี 
                              <?php
                              
							  	$dll_year_name = "docr_year";
							  	include "ddl_year_auto_submit_lawful.php";
							  
							  
							  //	echo $this_year;
								//echo $this_lawful_year;
							  
							  
							  ?></td>
                          </tr>
                        </form>
                        
                        <form method="post" action="scrp_update_org_doc_stat.php">
                          
                         
                          <tr>
                            <td><label>
                            <input type="radio" name="docr_status" id="docr_stat" value="1" <?php echo $stat_1_checked;?> />
                            </label>
                            ได้รับเอกสารครบแล้ว ณ วันที </td>
                            <td>
                            
                            
                             <?php 
							
							//echo $this_year;
							//echo $this_lawful_year;
							
							?>
                            
                            <?php
											   
							   $selector_name = "docr_date";
							   
							   $this_date_time = $docr_row["docr_date"];
							 
							   if($this_date_time != "0000-00-00"){
								   $this_selected_year = date("Y", strtotime($this_date_time));
								   $this_selected_month = date("m", strtotime($this_date_time));
								   $this_selected_day = date("d", strtotime($this_date_time));
							   }else{
								   $this_selected_year = 0;
								   $this_selected_month = 0;
								   $this_selected_day = 0;
							   }
							   
							   include ("date_selector.php");
							   
							   ?>            <?php 
							
							//echo $this_year;
							//echo $this_lawful_year;
							
							?>                 </td>
                          </tr>
                          <tr>
                            <td colspan="2"><div style="padding-left:25px;"><textarea name="docr_status_remark" cols="35" rows="3"><?php echo doCleanOutput($docr_row["docr_status_remark"]);?></textarea></div>
                            
                            
                            <?php 
							
							//echo $this_year;
							//echo $this_lawful_year;
							
							?>
                            
                            </td>
                          </tr>
                          <tr>
                            <td><input type="radio" name="docr_status" id="docr_stat2" value="0"  <?php echo $stat_2_checked;?>/> 
                              ได้รับเอกสารยังไม่ครบ เอกสารที่ยังขาดคือ </td>
                            <td><label></label></td>
                          </tr>
                          <tr>
                            <td colspan="2"><div style="padding-left:25px;"><textarea name="docr_desc" cols="35" rows="3"><?php echo doCleanOutput($docr_row["docr_desc"]);?></textarea></div></td>
                          </tr>
                          <tr>
                            <td colspan="2"><div align="center">
                              <input name="docr_year" type="hidden" value="<?php echo $this_docr_year;?>" />
                              
                              <?php if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only){?>
                                  <input type="submit" name="button2" id="button2" value="ปรับปรุงข้อมูล" 
                                  onclick = "return confirm('ต้องการปรับปรุงข้อมูลเอกสารจาก<?php echo $the_company_word;?>นี้?');"
                                  />
                              <?php }?>
                              <input name="docr_org_id" type="hidden" value="<?php echo $this_id; ?>" />
                            </div>
                            
                            
                            <?php 
							
							//echo $this_year;
							//echo $this_lawful_year;
							//
							?>
                            
                            
                            </td>
                          </tr>
                        </table></form></td>
                      </tr>
                      
                    </table>
                  
              		<?php } //end if to show this tab for non-company only?>
              
              
              
              	                       
                 
              
              
              
              
              
              
                  <table style=" padding:10px 0 0px 0; " id="lawful">
                    
                      <tr>
                        <td><div style="font-weight: bold; padding:0 0 5px 0;">การปฏิบัติตามกฎหมายของ<?php echo $the_company_word;?></div></td>
                      </tr>
                      <tr>
                        <td><?php
                        
							
							
							//echo $this_year;
							//echo $this_lawful_year;
							
							
							
							
							//yoes 20160314 ---> special for COMPANY
							
							if($sess_accesslevel == 4){
								
								
								$lawful_row = getFirstRow("select * 
								from 
									lawfulness_company
								where 
									CID  = '$this_id'
									
									$conditions
									
								order by LID desc
								
								limit 0,1");
								
							}else{
								
								$lawful_row = getFirstRow("select * 
								from 
									lawfulness
								where 
									CID  = '$this_id'
									
									$conditions
									
								order by LID desc
								
								limit 0,1");
									
							}
								
							
							
							
							
						
							$stat_1_checked = '';
							$stat_2_checked = '';
							$stat_3_checked = '';
							$stat_4_checked = '';
							
							$no_recipient_checked = '';
						
							if($lawful_row["LawfulStatus"] == "0"){
								$stat_2_checked = 'checked="checked"';//unlawful
							}elseif($lawful_row["LawfulStatus"] == "1" || $lawful_row["LawfulStatus"] == "5"){ // yoes 20211025
								$stat_1_checked = 'checked="checked"'; //lawful
							}elseif($lawful_row["LawfulStatus"] == "2"){
								$stat_4_checked = 'checked="checked"';//in progress
							}elseif($lawful_row["LawfulStatus"] == "3"){
								$stat_3_checked = 'checked="checked"';//no employee
							}else{
								//blank row

								//yoes 2012 nov 5 -> if lawful row is blank then -> default check on nothing
								//$stat_2_checked = 'checked="checked"';//unlawful
								
								$is_blank_lawful = 1;
							}
							
							if($lawful_row["NoRecipient"] == "1"){
								$no_recipient_checked = 'checked="checked"';
							}
							
							$lawful_fields = array(
								'LID'
								,'CID'
								,'lawfulStatus'
								,'Employees'
								
								,'Hire_status'
								,'Hire_NumofEmp'
								,'Hire_docfile'
								
								,'Conc_status'
								
								,'Conc1_status'
								,'Conc1_docfile'
								,'Conc2_status'
								,'Conc2_docfile'
								,'Conc3_status'
								,'Conc3_docfile'
								,'Conc4_status'
								,'Conc4_docfile'
								,'Conc5_status'
								,'Conc5_docfile'
								
								,'pay_status'
						
								,'cash_date'
								,'cash_amount'
								,'cash_docfile'
								
								,'check_bank'
								,'check_number'
								,'check_date'
								,'check_amount'
								,'check_docfile'
								
								,'note_number'
								,'note_date'
								,'note_amount'
								,'note_docfile'
								
								,'NoRecipient'
								,'NoRecipient_remark'
								,'Hire_NewEmp'
								
								,'lawful_order'
								
								
								);
								
							for($i = 0; $i < count($lawful_fields); $i++){
								//clean all inputs
								$lawful_values[$lawful_fields[$i]] .= doCleanOutput($lawful_row[$lawful_fields[$i]]);
							}
						
						?>
                       	  <form action="?id=<?php echo $this_id;?>&focus=lawful" method="post">
                            <table>
                            	<tr>
                                  <td colspan="2">ข้อมูลประจำปี
                                  <?php
                              		
									//print_r($_POST);
							  		include "ddl_year_auto_submit_lawful.php";
							  
							  	?>
								
								
								<?php 
									//yoes 20200624
									//add option to select BETA version
									if($this_lawful_year >= 2018 && $this_lawful_year < 2500 && ($sess_accesslevel == 1 || $sess_accesslevel == 2)){
								?>
								
										<font color=blue>ข้อความสำหรับเจ้าหน้าที่ส่วนกลาง:</font>
										
										
										<?php if($is_beta_mode){?>										 
											<font color=green>
												หน้าจออยู่ในโหมดแสดงข้อมูลการปฏิบัติตามกฎหมายตามการคำนวณ 2563
											</font>
											<!--<a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $this_lawful_year;?>&beta_off=1" style="font-weight: normal;">
												กดที่นี่เพื่อปิดใช้งานการคำณวนเงินปี 2563 Beta
											</a>-->										
										<?php }else{?>
											
											<a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $this_lawful_year;?>&beta_on=1" style="font-weight: normal;">
												เปิดใช้งานการคำณวนเงินปี 2563 Beta
											</a> 
										
										<?php 
												}
										?>
								
								<?php 
										
									}?>
                                
                                <?php 
									
									if(!$lawful_values["LID"]){
									
										
								?>
                                
                                
                                <font color=orange>
                                    <br /> ** สถานประกอบการนี้ ไม่มีข้อมูลการปฏิบัติตามกฏหมายในปี <?php echo $this_year+543?> 
                                   
									<?php //yoes 20210203 
									
										if($dbd_merged_to){
											
											$is_read_only = 1;
										?>
										<br /> *** สถานประกอบการถูกควบไปอยู่กับ
										
										
										<?php 
														
											echo "<a href='organization.php?id=".$dbd_merged_to_company_row[CID]."'>".formatCompanyName($dbd_merged_to_company_row[CompanyNameThai],$dbd_merged_to_company_row[CompanyTypeCode])."</a>";						
											
										?>
										
										แล้ว 
										<br>กรณีที่ต้องการสร้างข้อมูลการปฏิบัติตามกฎหมาย ให้ทำการสร้างข้อมูลการปฏิบัติตามกฎหมายใหม่ที่ <?php echo formatCompanyName($dbd_merged_to_company_row[CompanyNameThai],$dbd_merged_to_company_row[CompanyTypeCode]); ?>
									<?php
											
										}else{
										
									?>
										
										
										
										<br />- ในกรณีที่ต้องการสร้างข้อมูลการปฏิบัติตามกฎหมาย ให้กด "ยืนยันปรับปรุงข้อมูล" ก่อนเพื่อให้ทำการใส่ข้อมูล ม.33, ม.34 และ ม.35 ได้
										
										
									<?php }?>
									
                                </font>
                                
                                <?php } ?>
                                
                                
                                
                                <?php if($this_year > 3000){?>
                                <font color=red>
                                    <br /> *** ข้อมูลของสถานประกอบการนี้ ถูกรวมไปอยู่กับ <?php echo "<a href='organization.php?id=".$merged_to_company_row[CID]."&year=".($this_year-1000)."'>".formatCompanyName($merged_to_company_row[CompanyNameThai],$merged_to_company_row[CompanyTypeCode])."</a>";?> แล้ว
                                    <br />- กรุณากรอกข้อมูลการปฏิบัติตามกฎหมายไว้ที่สถานประกอบการ <?php echo "<a href='organization.php?id=".$merged_to_company_row[CID]."&year=".($this_year-1000)."'>".formatCompanyName($merged_to_company_row[CompanyNameThai],$merged_to_company_row[CompanyTypeCode])."</a>";?>
                                </font> 
                                <?php }?>
                                
                              	</td>
                                </tr>
																<tr>
																	<td>
																		<div id="tb_contact_address2"></div>
																		<?php //DANG
																			$lawful_submitted = getFirstItem("select lawful_submitted from lawfulness_company where CID = '$this_id' and Year = '$this_lawful_year'");
																			if($lawful_submitted == 2){
																			?>
																				<script>
																				$( document ).ready(function() {
																						$("#tb_contact_address2").html($("#tb_contact_address").html());
																				});
																				</script>
																			<?php
																			}

																		?>
																	</td>
																</tr>
                            </table>
                          </form>
                            <script language='javascript'>
							
								
								<!--
								function validateLawfulStat(frm) {
									
									lawful_value = getCheckedValue(frm.lawfulStatus);
									//alert(lawful_value);
									
									<?php if($sess_accesslevel !=4){?>
									if(lawful_value == 0){
										if(frm.Hire_status.checked || frm.pay_status.checked || frm.Conc_status.checked){
											alert("ข้อมูลผิดพลาด -> ไม่ปฏิบัติตามกฎหมาย แต่มีการ จ้างคนพิการเข้าทำงาน, ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ หรือ ให้สัมปทานฯ ");
											
											return false;
										}
									}
									
									if(lawful_value == 1){
										
										if(!frm.Hire_status.checked && !frm.pay_status.checked  && !frm.Conc_status.checked){
											alert("* ข้อมูลผิดพลาด -> ปฏิบัติตามกฎหมาย แต่ไม่มีการ  จ้างคนพิการเข้าทำงาน,  ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ หรือ ให้สัมปทานฯ ");
											return false;
										}
									}
									<?php } ?>
									
									
									if(frm.Hire_status.checked && frm.Hire_NumofEmp.value < 1){
											alert("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n ข้อมูลผิดพลาด -> มีการจ้างงานคนพิการ แต่ไม่มีจำนวนคนพิการ\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
											return false;
									}
									
									//hire
									if(!frm.Hire_status.checked && frm.Hire_NumofEmp.value != 0){
											alert("!!!! มีจำนวนคนพิการ แต่ไม่ได้ check 'จ้างคนพิการเข้าทำงาน '!!!!");
											return false;
									}
									
									
									//hire
									if(frm.pay_status.checked && frm.have_receipt.value == 0){
											alert("!!!! มีการ check 'ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ' แต่ไม่มีข้อมูลใบเสร็จ!!!!");
											return false;
									}
									
									if(!frm.pay_status.checked && frm.have_receipt.value == 1){
											alert("!!!! มีข้อมูลใบเสร็จ แต่ไม่มีการ check 'ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ' !!!!");
											return false;
									}
									
									//conc
									if((frm.Conc1_status.checked || frm.Conc2_status.checked || frm.Conc3_status.checked || frm.Conc4_status.checked || frm.Conc5_status.checked) && !frm.Conc_status.checked){
											alert("!!!! มีรายละเอียดการให้สัมปทาน แต่ไม่ check ช่อง 'ให้สัมปทานฯ'!!!!");
											return false;
									}
									if((!frm.Conc1_status.checked && !frm.Conc2_status.checked && !frm.Conc3_status.checked && !frm.Conc4_status.checked && !frm.Conc5_status.checked) && frm.Conc_status.checked){
											alert("!!!! มีการ check ช่อง 'ให้สัมปทานฯ' แต่ไม่มีรายละเอียดการให้สัมปทาน!!!!");
											return false;
									}
									
									
									return true;
																								
								
								}
								
								function getCheckedValue(radioObj) {
									if(!radioObj)
										return "";
									var radioLength = radioObj.length;
									if(radioLength == undefined)
										if(radioObj.checked)
											return radioObj.value;
										else
											return "";
									for(var i = 0; i < radioLength; i++) {
										if(radioObj[i].checked) {
											return radioObj[i].value;
										}
									}
									return "";
								}

								-->
							
							</script>
							
							<?php  //bank add 20230106 MA 2022 ข้อ 4  ?>
												   <?php 
																	$url = "http://203.154.94.105/law_system/law_ws/getLawfulChangeRequest.php";


																	$ch = curl_init();
																	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																	curl_setopt($ch, CURLOPT_URL,$url);
																	$result=curl_exec($ch);
																	curl_close($ch);

																	
																	$array = json_decode($result, true);	
																	//$data = json_encode($array);
																	$data = $array["data"]["LawEditList"];
																	$total_law_edit = count($data);
																	
																//$request_law_done = 0;
																
																$law_path_file = "https://law.dep.go.th/law_docfile/"; //when merge need to fix
													
												for ($i = 0; $i <= $total_law_edit; $i++)  {
													
													//check cid , year and Approve_status from Law = 1 (ยังไม่ได้กดรับ)
													if($data[$i]["CID"] == $this_cid && $data[$i]["Year"] == $this_year && $data[$i]["Approve_status"] == "1"){?>
													
													<form method="post" id="law_request_form" action="scrp_approve_law_edit_request.php"  >
														<input type="hidden" name="CID_LAW" value="<?php echo $data[$i]["CID"];?>">
														<input type="hidden" name="Year_LAW" value="<?php echo $data[$i]["Year"];?>">
														<input type="hidden" name="case_id" value="<?php echo $data[$i]["case_id"];?>">
														<input type="hidden" name="law_eid" value="<?php echo $data[$i]["law_eid"];?>">
														<input type="hidden" name="Employees_LAW" value="<?php echo $data[$i]["Employees"];?>">
													<div>
													<table border= "1" id="lawful" style="border-color: blue;" > <?php //Note : id="lawful" for stay in this tab ?>
															<tr>
															
																<td colspan="10" style="padding:0 0 5px 0;">
																<div align="center" style=" color:#060">
																	<b><font color=blue>มีการขอเปลี่ยนจำนวนลูกจ้างประจำปี  <?php echo formatYear($this_year); ?> จากระบบติดตามและดำเนินคดี </font></b>
																</div>
																</td>
															</tr>
															
															
															
															 <tr>
																	   
																<td align="center"> 
																
																<div align="center">
																 <b>จำนวนลูกจ้างที่ต้องการเปลี่ยน</b>
																  </div>
																  
																  </td>		

																<td align="center"> 
																
																<div align="center">
																 <b>เอกสารแนบ</b>
																  </div>
																  
																  </td>											  

																
																  
															</tr>
											  
											  

															 <tr>
															   <td align="center" style="text-align: center">
																<?php echo $data[$i]["Employees"];?>
															   

															   
															   </td>
															   
															   <td align="center" style="text-align: center">
																   <a href="<?php echo "$law_path_file".$data[$i]["file_name"];?>" target="_blank">
																	<?php 

																		echo $data[$i]["file_name"];
																		
																		//echo $total_law_edit;
																	?>

																   </a>
															   </td>

															 </tr>
															 
											  
														
																						<hr />
												<tr>
												<td colspan="2">
													
													<div align="center">
															<input type="submit" name="btn_law_accepts" id="btn_law_accepts"  value="ปรับปรุงข้อมูล" 
															onclick = "return confirm('ต้องการปรับปรุงจำนวนลูกจ้างนี้?'); toggleAction_law();"/>
													</div>
													
													<div align="center">		
															<input type="submit" name="btn_law_reject" id="btn_law_reject"  value="ยกเลิกการปรับปรุงข้อมูล" 
															onclick = "return confirm('ต้องการปรับปรุงจำนวนลูกจ้างนี้?'); toggleAction_law();"/>
													</div>		
														   
															
												</td>
												</tr>
														
														

												
												</table>
												</div>
												</form>
				
												
											<script>

														function toggleAction_law(){
															

																document.getElementById("law_request_form").action = 'scrp_approve_law_edit_request.php';
																
																
																
																
																
																<?php //$_POST["law_eid"] = $data[0]["law_eid"]; ?>
																<?php //include "scrp_approve_law_edit_request.php";?>

														}

													



											</script>
												
												
												<?php } ?>		
											<?php } ?>				
							<?php  //End bank add 20230106 MA ข้อ 4  ?>
							
                            <form id="lawful_form" 
                            
                            <?php if($sess_accesslevel == 4){
									//company do what company do
									?>
                            	action="scrp_update_org_lawful_stat_company.php"
                            <?php }else{
								//else, normal case
								?>
                            	action="scrp_update_org_lawful_stat.php"
                            <?php }?>
                            
                            
                            method="post" enctype="multipart/form-data" 
                            
                            <?php if($is_2013 != 1){?>
                            onSubmit="return validateLawfulStat(this);"
                            <?php }?>
                            
                            >
							
							
            
									
									
									
                   <table border="0">
								
								

								
								
								
								
								
								
								 <tr>
									  <td colspan="2">
									  
									  
											<table border="0" width="100%">
                                
													<?php if($sess_accesslevel !=4){?>
													
													<tr>
													  <td>
													  
													  
													   <?php if($is_2013){?>

															
															
															<?php if($lawful_row["LawfulStatus"] == 3){?>
														
																<img id="chk_lawfulStatus_03" src="decors/checked.gif" />
																<input id="rad_lawfulStatus_03" name="" type="radio" value="" disabled="disabled" style="display:none;" />
														
															<?php }else{?>
															
																<img id="chk_lawfulStatus_03" src="decors/checked.gif" style="display:none;" />
																<input id="rad_lawfulStatus_03" name="" type="radio" value="" disabled="disabled" />
															
															<?php }?>
															
															
														
														
													   <?php }else{?>
														  <input type="radio" name="lawfulStatus" id="" value="3"  <?php echo $stat_3_checked;?>/>                      
														
														<?php }?>
													  
														ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?>
                                                       </td>
                                                       
                                                       <td colspan="2">
                                                       
                                                       	
                                                        	<table align="right">
                                                            	<tr>
                                                                	<td   bgcolor="#fcfcfc" >ลำดับ:</td>
													  <td  bgcolor="#fcfcfc">
                                                      
                                                      	<input name="lawful_order" type="text" size="5" style="text-align: right;" value="<?php echo doCleanOutput($lawful_values["lawful_order"])?>" />
                                                      
													  </td>
                                                                </tr>
                                                            </table>
                                                       
                                                       </td>
                                                       
												  </tr>
                                                    
                                                    
													<tr>
													  <td>
													  
													  <?php //echo "is_2013: ". $is_2013;?>
													  
													  <?php if($is_2013){?>
													  
														<input name="lawfulStatus" type="hidden" value="<?php echo $lawful_row["LawfulStatus"];?>" />
													  
														
														
														<?php if($lawful_row["LawfulStatus"] == 0){?>
														
															<img id="chk_lawfulStatus_00" src="decors/checked.gif" />
															<input id="rad_lawfulStatus_00" name="" type="radio" value="" disabled="disabled" style="display:none;" />
													
														<?php }else{?>
														
															<img id="chk_lawfulStatus_00" src="decors/checked.gif" style="display:none;" />
															<input id="rad_lawfulStatus_00" name="" type="radio" value="" disabled="disabled" />
														
														<?php }?>
														
														
													  <?php }else{?>
														  <input type="radio" name="lawfulStatus" id="" value="0"  <?php echo $stat_2_checked;?> />                                  
													  
													  <?php }?>
													  
													  
													  &nbsp;
													  
													  
														ไม่ปฏิบัติตามกฎหมาย
                                                       </td>
                                                        
                                                        
													  <td rowspan="4" valign="top" bgcolor="#fcfcfc" >หมายเหตุ:</td>
													  <td rowspan="4" bgcolor="#fcfcfc"><textarea name="NoRecipient_remark" rows="5" style="width:100%;"><?php echo doCleanOutput($lawful_values["NoRecipient_remark"])?></textarea>
													  </td>
                                                      
                                                      
													</tr>
													
													
													<tr>
													  <td style="padding-left: 20px;">
														<input name="NoRecipient" type="checkbox" value="1" <?php echo $no_recipient_checked;?>/> ไม่มีคนรับเอกสาร
														
													  </td>
													</tr>
												   
												   
													<tr>
													  <td>
													  
													  
													  
													   <?php if($is_2013){?>

															<?php if($lawful_row["LawfulStatus"] == 2){?>
														
																<img id="chk_lawfulStatus_02" src="decors/checked.gif" />
																<input id="rad_lawfulStatus_02" name="" type="radio" value="" disabled="disabled" style="display:none;" />
														
															<?php }else{?>
															
																<img id="chk_lawfulStatus_02" src="decors/checked.gif" style="display:none;" />
																<input id="rad_lawfulStatus_02" name="" type="radio" value="" disabled="disabled" />
															
															<?php }?>
														
													   <?php }else{?>
														   <input type="radio" name="lawfulStatus" id="" value="2"  <?php echo $stat_4_checked;?>/>           
														
														<?php }?>
													 
													  
													  
					ปฏิบัติตามกฏหมายแต่ไม่ครบตามอัตราส่วน</td>
													</tr>
													
													
													
													 <?php } ?>
													
									</table>
									  
									  
									  
									  </td>
								  
								  </tr>
								
                                <tr>
                                  <td>
                                   <?php if($sess_accesslevel !=4){?>
                                  	<label>
                                   
                                   
                                   <?php if($is_2013){?>

										<?php if($lawful_row["LawfulStatus"] == 1 || $lawful_row["LawfulStatus"] == 5 || $lawful_row["LawfulStatus"] == 6){ //yoes 20211001   //bank add status 6 20221227?>
                                    
											<input id="rad_lawfulStatus_01" name="" type="radio" value="0" checked />
											
											<!--<img src="decors/checked.gif" /> -->
                                    
                                        <?php }else{?>
                                        
                                            <input id="rad_lawfulStatus_01" name="" type="radio" value="" disabled="disabled" />
                                        
                                        <?php }?>
                                    
                                    
                                   <?php }else{?>
                                       <input id="rad_lawfulStatus_01" type="radio" name="lawfulStatus" id="" value="1" <?php echo $stat_1_checked;?> />                             
                                    
                                    <?php }?>
                                   
                                   
                                    
                                    
					
                                    </label>
                                    ปฏิบัติตามกฎหมาย<br />
                                    <?php } ?>
                                    <div class="style86" style="padding: 10px 0 10px 0;">
									
									
                                    
                                    <table >
									
										<tr>
											<td style="padding-left: 20px;" colspan=3>
												
												
												
											<?php //bank add change 20221227 checkbox to radio ?>
												<input name="is_lawful_exempt" type="radio" value="1" 
												
													<?php
														
														$sql_lawful_exempt = "	select
																count(*)
															from 
																lawfulness_meta 
															where
															
																meta_for = 'is_lawful_exempt'
																and
																meta_lid = '$this_lid'
																and
																meta_value = 1
															
															";
															
															
															
														$is_lawful_exempt = getFirstItem($sql_lawful_exempt);
													
														
														if($is_lawful_exempt && $lawful_row["LawfulStatus"] == 5){
													
													?>													
													checked
														<?php }?>
														
														<?php if(($sess_accesslevel != 1 && $sess_accesslevel != 2 && $sess_accesslevel != 3)){ ?>
														readonly disabled
														<?php } ?>

													/>
													
												
													<?php //echo $sql_lawful_exempt;?>
												นับว่าปฏิบัติตามกฎหมายเนื่องจากเข้าข่ายของคำวินิจฉัยกฤษฎีกา
												
												<img src="decors/orange.gif" >			
											
											</td>
										</tr>
                                    	
										<tr>
											<td style="padding-left: 20px;" colspan=3>
												
												
												
												<?php //bank add change 20221227 checkbox to radio ?>
												<input name="is_lawful_exempt" type="radio" value="2" 
												
													<?php
														
														$sql_lawful_exempt = "	select
																count(*)
															from 
																lawfulness_meta 
															where
															
																meta_for = 'is_court_case_closed'
																and
																meta_lid = '$this_lid'
																and
																meta_value = 1
															
															";
															
															
															
														$is_lawful_exempt = getFirstItem($sql_lawful_exempt);
													
														
														if($is_lawful_exempt && $lawful_row["LawfulStatus"] == 6){
													
													?>													
													checked
														<?php }?>
														
														<?php if(($sess_accesslevel != 1 && $sess_accesslevel != 2 && $sess_accesslevel != 3)){ ?>
														readonly disabled
														<?php } ?>

													/>
													
												
													<?php //echo $istest;?>
												นับว่าปฏิบัติตามกฎหมายเนื่องจากการยุติการดำเนินคดีทางกฎหมาย
												
												<img src="decors/purple.jpg" >			
											
											</td>
										</tr>
										
										
                                        
                                    	<tr>
                                    	  <td colspan="3">
                                          
                                          	<table border="0" style="margin: 5px 0 15px 30px;">
                                            
                                                             <!----- starts lawful employees and Ratio --->
                                                        <!----- starts lawful employees and Ratio --->
                                                        <!----- starts lawful employees and Ratio --->
                                                        <!----- starts lawful employees and Ratio --->
                                                        
                                                        
                                                        
                                                        
                                                      
                                                        <tr>
                                                          <td bgcolor="#fcfcfc">จำนวน<?php echo $the_employees_word;?>: </td>
                                                          <td>
                                                          
                                                          <?php 
														  
														  	//what employees to show
															
															//if this is a company, try get the inputted employees
															if($sess_accesslevel == 4){
																
																//new for company only -> create LID record if not existed
																$sqll = "select 
																								count(*) 
																							from 
																								lawfulness_company 
																							where 
																								LID = '" . $lawful_values["LID"] . "'";
																
																$lid_existed = getFirstItem($sqll);
																
																
																//echo $sqll;								
																
																
																//echo "existed: ".$lid_existed;
																
																//no compnay -> create it		
																if(!$lid_existed){
																
																	//yoes 2014093 -> create this lawfulness on "admin" side
																	
																	if(strlen($lawful_values["LID"]) < 1){
																	
																		$sql = "insert into
																						lawfulness(
																							CID
																							,Year
																							, Employees
																																										
																						)values(
																							'".  $output_values["CID"] ."'
																							
																							,'$this_lawful_year'
																							,'".$output_values["Employees"]."'
																						
																						)";
																						
																		
																		mysql_query($sql)or die(mysql_error());
																		$lawful_values["LID"] = mysql_insert_id();
																	
																		//echo $sql."<br>";
																	}
																	
																	
																	
																	
																	
																	$sql = "insert into
																					lawfulness_company(
																						LID
																						,CID
																						,Year
																						, Employees
																																									
																					)
																					
																					select 
																						LID
																						,CID
																						,Year	
																						, Employees
																						
																					from
																						lawfulness
																					where
																						LID = '" . $lawful_values["LID"] . "'";
																						
																	//echo $sql;
																	
																	mysql_query($sql)or die(mysql_error());
																	
																}
																
																
																//override it lawful value
																$company_employees = getFirstItem("select 
																						Employees 
																					from 
																						lawfulness_company 
																					where 
																						LID = '" . $lawful_values["LID"] . "'");
																				
																//if have value then override it		
																if($company_employees){
																	$lawful_values["Employees"] = $company_employees;
																}
																
																
																
																//20140220
																//see if this company got submmited or not
																 $submitted_company_lawful = getFirstItem("
																 										select 
																											lawful_submitted
																										from
																											lawfulness_company
																										where
																											LID = '" . $lawful_values["LID"] . "'
																										");
																
																
															}
                                                          
                                                          
															
															
															//yoes 20151201 -- stop this "place holder" thing
														  
														  	//echo "??????". $lawful_values["Employees"];
														  
														  	$employee_to_use = $lawful_values["Employees"];
															
															/*
                                                            if($lawful_values["Employees"]){
                                                                //have lawful value, use lawful value
                                                                $employee_to_use = $lawful_values["Employees"];
                                                                
                                                            }else{
                                                            
                                                                //didn't have lawful value, use ORG's value
                                                                if($sum_employees){
                                                                    //if have branch, use employees from all branch
                                                                    $employee_to_use = $sum_employees;
                                                                    
                                                                }else{
                                                                
                                                                    //else, just use ORG's employees
                                                                    $employee_to_use = $output_values["Employees"];
                                                                
                                                                }
                                                            }*/
															
															
															
															//yoes 20151021 -- company only see RAW data from all branche
															//yoes 20151122 -- but only do so for latest year
															//if($sess_accesslevel == 4 && $this_year == $the_end_year){
															//yoes 20160205 ---> also do this for blank lawfulness
															if($sess_accesslevel == 4 && $is_blank_lawful){
																
																$employee_to_use = $sum_employees;
																
															}elseif($is_blank_lawful){
															
																//yoes 20160712 --> if just "blank" lawfulness ...
																//show "1" as per mantis ticket number 162	
																$employee_to_use = 1;
																
															}
															
															
                                                            
                                                            ?>
                                                          
                                                          
                                                          
                                                          
                                                          <?php 
														  
														  	//yoes 20160712
															//special display for school org
															if($is_school){
																
														  ?>
                                                          
                                                          	<?php 
														
															//yoes 20160712
															//also move this here (moved from employees_popup)
																
															if($is_school){
																																
																//add metadata
																$meta_result = mysql_query("
																		select * 
																		from 
																			lawfulness_meta
																		where 
																			meta_lid  = '".$lawful_values["LID"]."'
																		");
																
																while($meta_row = mysql_fetch_array($meta_result)){			
																	//
																	$lawful_meta[$meta_row[meta_for]] = (doCleanOutput($meta_row[meta_value]));				
																}
																
															}
															
															?>
                                                          
                                                            <table>
                                                            	<tr>
                                                                	<td bgcolor="#fcfcfc">
                                                                    ครู
                                                                    </td>
                                                                    <td>
                                                                    <?php echo $lawful_meta[school_teachers]*1;?> คน
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                	<td bgcolor="#fcfcfc">
                                                                    ครูสัญญาจ้าง
                                                                    </td>
                                                                    <td>
                                                                    <?php echo $lawful_meta[school_contract_teachers]*1;?> คน
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                	<td bgcolor="#fcfcfc">
                                                                    ลูกจ้าง
                                                                    </td>
                                                                    <td>
                                                                    <?php 
																		//yoes 20160614 -- if no employees then...
																		if($lawful_meta[school_teachers] +$lawful_meta[school_contract_teachers] + $lawful_meta[school_employees] == 0){
																			$lawful_meta[school_employees] = $employee_to_use;
																		}
																	
																	?>
                                                                    <?php echo $lawful_meta[school_employees]*1;?> คน
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                	<td bgcolor="#fcfcfc">
                                                                    รวม
                                                                    </td>
                                                                    <td>
                                                                    <strong>
                                                                    <?php echo formatEmployee($lawful_meta[school_teachers]+$lawful_meta[school_contract_teachers]+$lawful_meta[school_employees])?>
                                                                    </strong>	
                                                                     คน
                                                                    </td>
                                                                </tr>
                                                            </table>
														  
													      <?php	
															}else{
														  
														  ?>
                                                          
                                                                  <strong>
                                                                  
                                                                      <?php 
                                                                      
                                                                        echo formatEmployee($employee_to_use);
                                                                        
                                                                      ?>
                                                                  
                                                                  </strong> คน
                                                          
                                                          <?php }?>
                                                          
                                                          
                                                          
                                                          
															
															
                                                         <?php
															//yoes 20151123
															//if this is from company page, also update it to real database
															if($sess_accesslevel == 4){
															
																mysql_query("
																	update 
																		lawfulness_company 
																	set 
																		Employees = '$employee_to_use' 
																	where
																		lid = '".$lawful_values["LID"]."'
																	
																	");
																
																
															}
															
															
															//echo $lawful_values["LID"];
															
															//yoes 20151118
															//mark an original $employee_to_use here because it will get changed later if there are some data came from company
															$employee_to_use_from_lawful = $employee_to_use;
															
															?>
                                                          
                                                            |
                                                           
                                                           
                                                           
                                                           <input name="Employees" id="Employees" style="width:50px" type="hidden" value="<?php echo ($employee_to_use);?>"  />
                                                           
                                                           
                                                           <?php if($sess_accesslevel == 5 || $sess_accesslevel == 18 || $is_read_only || $case_closed){ //exec can do nothing -- yoes 20160118 -- also closed case can do nothing?>
                                                  
                                                          <?php }elseif($sess_accesslevel != 4 && !$is_blank_lawful){ //can only do this if already have lawful row?>
                                                                 
                                                                 
                                                                 <a href="#" onclick="fireMyPopup('employees_popup',500,250); return false;">ปรับปรุงข้อมูล</a>
                                                           
                                                            <?php }elseif($sess_accesslevel == 4 && ($submitted_company_lawful == 1 || $submitted_company_lawful == 2)){ //can only do this if already have lawful row?>
                                                                 
                                                                                                                                  
                                                                 <!--<a href="#" onclick="fireMyPopup('employees_popup',500,250); return false;">ปรับปรุงข้อมูล</a>-->
                                                          
                                                          <?php }elseif($sess_accesslevel != 4 && !$is_blank_lawful ){   //yoes 20151118 -- company no longer allow to edit his?>
                                                           
                                                           
                                                           		<a href="organization.php?id=<?php echo $this_id;?>&focus=general">ปรับปรุงข้อมูลลูกจ้าง</a>
                                                           
                                                          <?php }elseif(!$is_blank_lawful && $sess_accesslevel != 4){ //yoes 20151118 -- company no longer allow to edit his?>
                                                                   
                                                                   <a href="#" onclick="fireMyPopup('employees_popup',500,250); return false;">ปรับปรุงข้อมูล</a>
                                                                   
                                                           
                                                           <?php }elseif(!$is_blank_lawful && $sess_accesslevel == 4 && $this_year != $the_end_year && 1==0){ //yoes 20151122  --> company can edit this for non-latest year?>
                                                                   
                                                                   <a href="#" onclick="fireMyPopup('employees_popup',500,250); return false;">ปรับปรุงข้อมูล</a>
                                                                   
                                                           
                                                           <?Php }?>
                                                           
                                                           
                                                           
                                                           
                                                          </td>
                                                        </tr>
                                                        
                                                      
                                                        
                                                        <tr>
                                                          <td bgcolor="#fcfcfc" style="padding-right:20px;">อัตราส่วน<?php echo $the_employees_word;?>ต่อคนพิการ: </td>
                                                          <td><?php 
                                                          
                                                            //what ratio to use?
                                                            //$ratio_to_use = default_value(getFirstItem("select var_value 
                                                            //                    from vars where var_name = 'ratio_$this_lawful_year'"),100);
															$ratio_to_use = getThisYearRatio($this_lawful_year);
                                                            
                                                            //$half_ratio_to_use = $ratio_to_use/2;
                                                            
                                                            echo ($ratio_to_use);
                                                            
                                                          
                                                          ?>:1 = <strong id="employee_ratio"><?php 
                                                            //if employee > 200
                                                            
															                                                            
                                                            $final_employee = getEmployeeRatio($employee_to_use,$ratio_to_use);
                                                            
                                                            echo formatEmployee($final_employee);
                                                            
                                                            ?></strong> คน</td>
                                                        </tr>
                                                        
                                                        
                                                         <tr>
                                                          <td bgcolor="#fcfcfc" style="padding-right:20px;">เอกสารประกอบ</td>
                                                          <td>
                                                          
                                                          <?php                                              
																
																
																$this_id_temp = $this_id;
									                            $this_id = $lawful_values["LID"]; 
																
																$file_id = "";
																$file_type = "lawful_employees_docfile";                                                
																include "doc_file_links.php";                                                    
																
																
																$this_id = $this_id_temp;
																													
															?>
                                                          
                                                          
                                                          <input type="file" name="lawful_employees_docfile" id="lawful_employees_docfile" /> 
                                                           
                                                          <?php if(!$is_read_only){?>
                                                           
	                                                          <br /><input type="submit" value="เพิ่มเอกสารประกอบ"/>
                                                          
                                                          <?php }?>
                                                          
                                                          </td>
                                                        </tr>
                                                  
                                                        
                                                        
                                                        
                                                        <script>
                                                            function reCalculateRatio(){
                                                            
                                                                
                                                                employee_to_use = document.getElementById("Employees").value; 
                                                                
                                                                employee_to_use = employee_to_use.replace(/,/g,"");
                                                                //
                                                                //alert(employee_to_use);
                                                                
                                                                if(employee_to_use > 0){
                                                                    if(employee_to_use > <?php echo $ratio_to_use;?> || employee_to_use == <?php echo $ratio_to_use;?>){
                                                                        left_over = employee_to_use%<?php echo $ratio_to_use;?>;
                                                                        if(left_over <= <?php echo json_encode($half_ratio_to_use);?>){
                                                                            ratio_to_use = Math.floor(employee_to_use/<?php echo $ratio_to_use;?>);
                                                                        }else{
                                                                            ratio_to_use = Math.ceil(employee_to_use/<?php echo $ratio_to_use;?>);
                                                                        }
                                                                    }else{
                                                                        ratio_to_use = 0;
                                                                    }
                                                                }
                                                                
                                                                document.getElementById("employee_ratio").innerHTML = ratio_to_use;
                                                            }
                                                        </script>
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        <!----- Ends lawful employees and Ratio --->
                                                        <!----- Ends lawful employees and Ratio --->
                                                        <!----- Ends lawful employees and Ratio --->
                                                        <!----- Ends lawful employees and Ratio --->
                                            
                                            
                                            
                                            </table>
                                            
                                            
                                            
                                            <table border="0" style="margin: 5px 0 15px 30px;">
                             
                                            
                                                <tr>
                                                  <td bgcolor="#fcfcfc" style="padding-right:20px;" colspan="2"><strong style="color:#006600;">สรุปการดำเนินการตามกฎหมาย</strong></td>
                                                  
                                                </tr>
                                                
                                                <tr>
                                                  <td bgcolor="#fcfcfc" style="padding-right:20px;">รับคนพิการเข้าทำงานตาม ม.33</td>
                                                  <td><span id="summary_33"></span> คน</td>
                                                </tr>
                                                
                                                <tr>
                                                  <td bgcolor="#fcfcfc" style="padding-right:20px;">ให้สัมปทานฯ ตาม ม.35</td>
                                                  <td><span id="summary_35"></span> คน</td>
                                                </tr>
                                                
                                                
                                                <?php if(!$sess_is_gov){ ?>
                                                <tr>
                                                  <td bgcolor="#fcfcfc" style="padding-right:20px;">ต้องจ่ายเงินแทนการรับคนพิการ</td>
                                                  <td><span id="summary_34"></span> คน</td>
                                                </tr>
                                                <?php }?>
                                            
                                                                                        
                                                                            
                                          </table>
										  
										  
										<table border="0" style="margin-left: 30px;<?php if($submitted_company_lawful != 2){?>display: none;<?php }?>">			
																<?php
																//yoes 20220225
																//---> contact address สำหรับส่งใบเสร็จ
																
																$contact_address_row = getFirstRow("
																														
																						select
																							*
																						from
																							company_by_year_company
																							left join provinces p on Province = p.province_id
																						where
																							cid = '$this_id'
																							and
																							year = '$this_lawful_year'
																							and
																							row_type = 1
																					
																					");
																
																

																 ?>
											  <tr>
												<td colspan="4">
													<div style="font-weight: bold; padding:5px 0 5px 0; color:#006600">ข้อมูลติดต่อ</div>
												</td>
											  </tr>

											  <tr>
												<td>ชื่อผู้ติดต่อ 1: </td>
												<td>
												  <?php echo $contact_address_row["ContactPerson1"];?>
												</td>
												<td class="td_left_pad">เบอร์โทรศัพท์: </td>
												<td><?php echo $contact_address_row["ContactPhone1"];?></td>
											  </tr>
											  <tr>
												<td>ตำแหน่ง:</td>
												<td><?php echo $contact_address_row["ContactEmail1"];?></td>
												<td class="td_left_pad"> อีเมล์:</td>
												<td><?php echo $contact_address_row["ContactPosition1"];?></td>
											  </tr>
											   <tr>
												<td>ชื่อผู้ติดต่อ 2: </td>
												<td>
												  <?php echo $contact_address_row["ContactPerson2"];?>
												</td>
												<td class="td_left_pad">เบอร์โทรศัพท์: </td>
												<td><?php echo $contact_address_row["ContactPhone2"];?></td>
											  </tr>
											  <tr>
												<td>ตำแหน่ง:</td>
												<td><?php echo $contact_address_row["ContactEmail2"];?></td>
												<td class="td_left_pad"> อีเมล์:</td>
												<td><?php echo $contact_address_row["ContactPosition2"];?></td>
											  </tr>
											  
											</table>
                                          
                                          
                                          
                                          <?php 
											
											//yoes 20151122
											//show extra information
											
											
											
											if($sess_accesslevel != 4 && $lawful_row["verified_by"]){
											
											?>
                                            
                                            <table border="0" style="margin: 5px 0 15px 30px;">
                             
                                            
                                                <tr>
                                                  <td bgcolor="#fcfcfc" style="padding-right:20px;" colspan="2"><strong style="color:#006600;">สรุปการตรวจสอบข้อมูล</strong></td>
                                                  
                                                </tr>
                                                
                                                
                                                 <tr>
                                                  <td bgcolor="#fcfcfc" style="padding-right:20px;">ตรวจสอบข้อมูลโดย user: </td>
                                                  <td><?php echo getFirstItem("select user_name from users where user_id = '".$lawful_row["verified_by"]."'")?></td>
                                                </tr>
                                                
                                                <tr>
                                                  <td bgcolor="#fcfcfc" style="padding-right:20px;">วันที่ </td>
                                                  <td><?php echo formatDateThai($lawful_row["verified_date"],1,1)?></td>
                                                </tr>
                                            
                                            
                                            </table>
                                            
                                            
                                            <?php } ?>
                                          
                                         
                                          
                                           
                                          
                                          
                                          </td>
                                   	  </tr>
                                      
                                      
                                    	<tr>
                                        	<td>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            
                                            <?php if($sess_accesslevel == 4){?>
                                            
                                            
                                            <?php }elseif($is_2013){?>

												<input name="Hire_status" type="hidden" id="Hire_status" value="<?php echo $lawful_row["Hire_status"];?>" />
												
												<?php if($lawful_row["Hire_status"] == 1){?>
                                            
                                                <img src="decors/checked.gif" />
                                                
                                            
                                                <?php }else{?>
                                                
                                                    <input name="" type="checkbox" value="" disabled="disabled" />
                                                
                                                <?php }?>
                                            
                                            
                                            <?php }else{?>
                                               <input name="Hire_status" type="checkbox" id="Hire_status" value="1" <?php echoChecked($lawful_values["Hire_status"])?> />                                                                      
                                            
                                            <?php }?>
                                            
                                    		
                                            
                                            </td>
                                            <td><span style="font-weight: bold;color:#006600;">
                                             <?php //if($sess_accesslevel ==4){
											 if(1==1){?>
                                             	<hr />
                                             	มาตรา 33 จ้างคนพิการเข้าทำงาน
                                             <?php }else{ ?>
                                            	ปฏิบัติตามมาตรา 33
                                             <?php } ?>
                                            </span></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    	<tr>
                                    	  <td>&nbsp;</td>
                                    	  <td colspan="2" style="padding-left: 30px;">
                                          
                                          <table border="0">
                                          
                                          
                                          
                                          
                                          	
                                            
                                            
                                            <tr>
                                              <td>จำนวนคนพิการที่ทำงานในปัจจุบัน</td>
                                              <td>
                                              
                                              <?php 
											  
											  	//now we count number of hired employeed directly from LE list
											  											  
												//for company -> get this from company table
												if($sess_accesslevel == 4){
													
													$hire_numofemp = getFirstItem("
														SELECT 
															count(*)
														FROM 
															lawful_employees_company
														where
															le_cid = '$this_id'
															and le_year = '$this_lawful_year'");/* */
													
												}else{
													
													/*$hire_numofemp = getFirstItem("
														SELECT 
															count(*)
														FROM 
															lawful_employees
														where
															le_cid = '$this_id'
															and le_year = '$this_lawful_year'");/* */
															
															
														$hire_numofemp =  getHireNumOfEmpFromLid($lawful_row["LID"]);
													
													
												}
												
												
												//echo getHireNumOfEmpFromLid($lawful_row["LID"]);
												
												
												//yoes 20151122 -- whatever here
												$hire_numofemp_origin = $hire_numofemp;
												
												
												if($hire_numofemp == 0 && $sess_accesslevel != 4){
												
													//no "real" value, use thos that is in lawfulness instead
													$hire_numofemp = $lawful_values["Hire_NumofEmp"];
													
												}
												
											   ?>
                                              
                                               <strong><span id="txt_hire_numofemp"><?php echo default_value($hire_numofemp,0);?></span></strong>
                                              
                                              
                                              <input name="Hire_NumofEmp" type="hidden" id="Hire_NumofEmp" size="10" value="<?php echo formatEmployee(default_value($hire_numofemp,"0"));?>" onChange=" addEmployeeCommas('Hire_NumofEmp');"/> 
                                              
                                              
                                              คน 
                                                                      
											 <?php //echo "is_blank_lawful: " . $is_blank_lawful;?>
                                              
                                              <?php if(!$is_blank_lawful && $sess_accesslevel != 4 ){?>
                                              || 
                                             	 
												 
												<a href="" data-toggle="modal" data-target=".bs-example-modal-lg-m33" onClick="resetForm(); return false;">ข้อมูลคนพิการที่ได้รับเข้าทำงาน</a>
												 
												
                                              
                                              <?php }?>
                                              
                                              
                                              <?php if($sess_accesslevel == 4 && !$submitted_company_lawful){?>
                                              || 
                                              	
												
											
													<a href="" data-toggle="modal" data-target=".bs-example-modal-lg-m33" onClick="resetForm(); return false;">ข้อมูลคนพิการที่ได้รับเข้าทำงาน</a>
												
												
                                              
                                              <?php }?>
                                              
                                             
                                              
                                              </td>
                                            </tr>
                                            
                                            
                                            <?php 
											
											 //yoes 20160118 -- also count for "Extra" 33
											  $extra_33 = getFirstItem("SELECT 
																count(*)
															FROM 															
																lawful_employees_extra
															where
																le_cid = '$this_id'
																and le_year = '$this_lawful_year'");
											
											if($extra_33){?>
                                             <tr >
                                              <td>เป็นข้อมูลที่เพิ่มเข้ามาหลังจากปิดงาน</td>
                                              <td><strong style="color: #900"><?php echo "$extra_33";?></strong> คน</td>
                                             </tr>
                                             <?php }?>
                                              
                                              
                                              
                                              
                                              
                                              
                                               <table border="0">
                                             
                                            <?php if($hire_numofemp){ //shows attachment part if have 33?>
                                          
                                           
                                            
                                            <tr >
                                              <td colspan="3">
                                              	
                                                <br />
                                                <font color="#006600">
                                                	<strong>เอกสารประกอบการรายงาน มาตรา 33</strong>
                                                </font>
                                                
                                                <div id="alert_33_files" style="display:none;">
                                                   
                                                    <font color="red">กรุณาแนบ ไฟล์สำเนาสัญญาจ้าง และ สำเนาบัตรประจำตัวคนพิการ/ผู้ดูแลคนพิการ ให้ครบสำหรับคนพิการทุกคนที่ได้เข้าทำงาน</font>
	                                            </div>
                                              
                                              </td>
                                            </tr>
                                            
                                            
                                            <tr bgcolor="#fcfcfc" >
                                            <td>
                                                <img id="ex333" src="exclaim_small.jpg" title="กรุณาแนบไฟล์" style="padding: 5px;" />
                                              </td>
                                              <td>
                                              	สำเนา สปส 1-10 ส่วนที่ 1 ประจำเดือน ต.ค. <?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>
                                                <br />(พร้อมสำเนาใบเสร็จการชำระเงินของประกันสังคมเดือน ต.ค.<?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>)
                                              </td>
                                              <td>
                                              	<?php 
                                                  
												  	
												  
												  	$required_doc++;
													
													$this_id_temp = $this_id;
						                            $this_id = $lawful_values["LID"]; 
													
												  	$file_type = "company_33_docfile_3_adm";
                                                    include "doc_file_links.php";
                                                 
                                                ?>
                                                <?php if($have_doc_file){$required_doc--;?><br /><script>$('#ex333').hide();</script><?php }?>
                                               <input type="file" name="company_33_docfile_3_adm" id="company_33_docfile_3_adm" />
                                              </td>
                                            </tr>
                                             <tr >
                                             <tr bgcolor="#fcfcfc" >
                                            <td>
                                                <img id="ex334" src="exclaim_small.jpg" title="กรุณาแนบไฟล์" style="padding: 5px;" />
                                              </td>
                                              <td>
                                              	สำเนา สปส 1-10 ส่วนที่ 2 ที่มีชื่อคนพิการประจำเดือน ม.ค.<?php echo ($this_year > 3000 ? $this_year+543-1000 : $this_year+543) ;?>
                                                <br />(พร้อมใบเสร็จการชำระเงินของประกันสังคม ม.ค.<?php echo ($this_year > 3000 ? $this_year+543-1000 : $this_year+543) ;?>) ถึงเดือนปัจจุบัน
                                              </td>
                                              <td>
                                              	<?php 
												
													$required_doc++;                                                  
												  	$file_type = "company_33_docfile_4_adm";
                                                    include "doc_file_links.php";
                                                 
                                                ?>
                                                <?php if($have_doc_file){$required_doc--;?><br /><script>$('#ex334').hide();</script><?php }?>
                                               <input type="file" name="company_33_docfile_4_adm" id="company_33_docfile_4_adm" />
                                              </td>
                                            </tr>
                                             <tr bgcolor="#fcfcfc">
                                             <tr bgcolor="#fcfcfc" >
                                            <td>
                                                <img id="ex335" src="exclaim_small.jpg" title="กรุณาแนบไฟล์" style="padding: 5px;" />
                                              </td>
                                              <td>
											  <?php $year_to_show = $this_lawful_year+543; ?>
                                               หนังสือรับรองนิติบุคคล
											  <?php if($year_to_show >= 2561){ ?>
												(อายุไม่เกิน 6 เดือน)
											  <?php }else{ ?>

											  <?php } ?>
                                              </td>
                                              <td>
                                              	<?php 
                                                  
												  	$required_doc++;
												  	$file_type = "company_33_docfile_5_adm";
                                                    include "doc_file_links.php";
                                                 
                                                ?>
                                                <?php if($have_doc_file){$required_doc--;?><br /><script>$('#ex335').hide();</script><?php }?>
                                               <input type="file" name="company_33_docfile_5_adm" id="company_33_docfile_5_adm" />
                                              </td>
                                            </tr>
                                             <tr >
                                             <tr bgcolor="#fcfcfc" >
                                            <td>
                                               
                                              </td>
                                              <td>
                                              	หนังสือมอบอำนาจ (กรณีมีการมอบอำนาจ)
                                              </td>
                                              <td>
                                              	<?php 
                                                  
												  	$file_type = "company_33_docfile_6_adm";
                                                    include "doc_file_links.php";
                                                 
                                                ?>
                                                
                                               <input type="file" name="company_33_docfile_6_adm" id="company_33_docfile_6_adm" />
                                              </td>
                                            </tr>
                                             <tr bgcolor="#fcfcfc">
                                             <tr bgcolor="#fcfcfc" >
                                            <td>
                                                
                                              </td>
                                              <td>
                                              	อื่นๆ
                                              </td>
                                              <td>
                                              	<div style="width: 250px">
                                              	<?php                                              
													
                                                    $file_type = "company_33_docfile_7_adm";                                                
                                                    include "doc_file_links.php";                                                    
                                                    $this_id = $this_id_temp;
													                                                    
                                                ?>
                                                
                                               <input type="file" name="company_33_docfile_7_adm" id="company_33_docfile_7_adm" /> 
                                               
                                               </div>
                                               
                                              </td>
                                            </tr>
                                            
                                            
                                            <?php if(!$is_read_only && !$case_closed){?>
                                            <tr>
                                            	<td colspan="3">
                                                
                                                 <hr />
            									<div align="center">
										            <input type="submit" value="เพิ่มเอกสารประกอบ"/>
                                                </div>
                                                
                                                </td>
                                            </tr>
                                            <?php }?>
                                            
                                              <?php }//ends if(!$hire_numofemp){ for file attachment?>
                                              
                                              
                                              
                                              
                                              
                                              
                                              
                                            
                                            
                                           
                                            
                                            
                                             <?php //yoes 20151118 -- user no longer wants to see this?>
                                            <tr style="display: none;">
                                              <td>ผู้พิการใช้สิทธิมาตรา 35</td>
                                              <td>
                                              
                                              
                                              <?php
											  	
													
													//curator user are person OR disabled person who is the "top" level
													
													//$curator_table_name is from file "scrp_add_curator"
													
													
													//yoes 20181108 -> change this new function
													$curator_user = getNumCuratorFromLid($lawful_values["LID"]);
													/*$curator_user = getFirstItem("select 
															count(*) 
														from 
															$curator_table_name 
															
														where 
														
															curator_lid = '".$lawful_values["LID"]."'
																
																
															and 
															
															curator_parent = 0
															
															
															");	*/
													
													
													
											  ?>
                                              <strong><?php echo $curator_user;?></strong>
                                              
                                               
                                              คน
                                            </tr>
                                            
                                           
                                            
                                            <?php //yoes 20151118 -- user no longer wants to see this?>
                                             <tr style="display:none;">
                                              <td>ต้องจ่ายเงินแทนการรับคนพิการ</td>
                                              <td>
                                              
                                              <b>
                                              <?php 
											  
											  	$extra_emp = $final_employee - $hire_numofemp - $curator_user;
												
												if($extra_emp < 0){
													$extra_emp = 0;
												}
												
											    echo formatEmployee(default_value($extra_emp,"0"));
											  ?>
                                              </b>
                                              
                                              
                                              <input name="Hire_NewEmp" type="hidden" id="Hire_NewEmp" size="10" value="<?php echo $extra_emp;?>" onChange=" addEmployeeCommas('Hire_NewEmp');" /> 
                                              คน
                                            </tr>
                                            
                                            
                                              <script>
											//yoes 20151118 -- retroactively change span value
											$("#summary_33").html("<?php echo formatEmployee(default_value($hire_numofemp,"0"));?>");
											$("#summary_35").html("<?php echo $curator_user;?>");
											$("#summary_34").html("<?php echo formatEmployee(default_value($extra_emp,"0"));?>");
											</script>
                                            
											
											<?php 
											
												//echo "$final_employee - $hire_numofemp - $curator_user";
											
											?>
                                            
                                            
                                            <?php if($sess_accesslevel != 4){ //company won't see this?>
                                                
                                                <tr>
                                                  <td>เอกสารประกอบ</td>
                                                  <td><span class="style86" style="padding: 10px 0 10px 0;">
                                                   
                                                    <div style="width:400px; padding-bottom:5px;">
                                                    <?php 
                                                        
                                                        //do $this_id swap thing because doc link use LID, but consume $this_id
                                                        //but $this_id on this page is CID and not LID...
                                                        $this_id_temp = $this_id;
                                                        $this_id = $lawful_values["LID"];
                                                        
                                                        $file_type = "Hire_docfile";
                                                    
                                                        include "doc_file_links.php";
                                                        
                                                        $this_id = $this_id_temp;
                                                        
                                                    ?>
                                                    </div>
                                                    <input type="file" name="Hire_docfile" id="Hire_docfile" />
                                                   </span></td>
                                                </tr>
                                            
                                            <?php }?>
                                            
                                            

                                          </table>
                                          
                                          
                                          </td>
                                   	  </tr>
                                    	<tr>
                                    	  <td colspan="3">&nbsp;</td>
                                   	  </tr>
                                      
                                      
                                      
                                      
                                      
                                    </table>

                                    <table 
									id="rule_34_table" border="0"
                                    
                                    <?php if($sess_accesslevel == 4){ //wont show this for company?>
                                    	style="display:none;"
                                    <?php }?>
                                    
                                    >
									
									
                                    	
                                    
                                      <tr>
                                        <td>
                                        
                                        <div align="right" style="margin-left: 30px;">
                                        
                                        <?php if($is_2013){?>

                                            <input name="pay_status" type="hidden" id="pay_status" value="<?php echo $lawful_row["pay_status"];?>" />
                                            
                                            <?php if($lawful_row["pay_status"] == 1){?>
                                        
                                            <img src="decors/checked.gif" />
                                            
                                        
                                            <?php }else{?>
                                            
                                                <input name="" type="checkbox" value="" disabled="disabled" />
                                            
                                            <?php }?>
                                        
                                        
                                        <?php }else{?>
                                           <input name="pay_status" type="checkbox" id="pay_status" value="1" <?php echoChecked($lawful_values["pay_status"])?>/>                               
                                        
                                        <?php }?>
                                        
                                        </div>
                                        
                                        </td>
                                        <td><span style="font-weight: bold; color:#006600;">
                                       <?php //if($sess_accesslevel ==4){
											 if(1==1){?>
                                             	<hr />
                                             	มาตรา 34 ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ
                                             <?php }else{ ?>
                                            	ปฏิบัติตามมาตรา 34
                                             <?php } ?>
                                        </span></td>
                                        <td>&nbsp;</td>
                                      </tr>

                                      <tr >
                                        <td>&nbsp;</td>
                                        <td colspan="2" style="padding-left: 30px;" >
                                        
                                        
                                        
                                        <table border="0">
                                            <tr>
                                              <td valign="top">
                                                
                                                
                                                    <!-- start payment table -->
                                                    <!-- start payment table --> 
                                                    <!-- start payment table -->
                                                    <!-- start payment table -->
                                                    <!-- start payment table -->
                                                    <!-- start payment table -->
                                                    <!-- start payment table -->
                                                    
                                                    
                                                    
                                                    <table border="0" >
                                                        
                                                        
                                                         <?php
														 
														 
														 		//yoes 20170123
																//check if 2011 and have interestes...
																if($this_lawful_year == 2011){
																	
																	$do_54_budget = getFirstItem("
								
																		select
																			meta_value
																		from
																			lawfulness_meta
																		where
																			meta_for = 'do_54_budget'
																			and
																			meta_lid = '". $lawful_values["LID"]."'
																		
																		
																		");
																		
																		
																		$the_54_budget_date = getFirstItem("
								
																			select
																				meta_value
																			from
																				lawfulness_meta
																			where
																				meta_for = 'do_54_budget_start_date'
																				and
																				meta_lid = '". $lawful_values["LID"]."'
																			
																			
																			");
																	
																}
																
																
																
                                                                    
                                                                //$extra_employee = $lawful_values["Hire_NewEmp"];
                                                                $extra_employee = $extra_emp;													
                                                                //if($extra_employee == 0){
                                                                //	$extra_employee = $final_employee - $lawful_values["Hire_NumofEmp"];
                                                                    
                                                                //}
                                                                
                                                                
																if($this_lawful_year == 2011){
																	
																	//use wage-rate by province instead
																	$wage_rate = default_value(getFirstItem("select province_54_wage from provinces where province_id = '".$output_values["Province"]."'"),0);										
																	$wage_rate = $wage_rate/2;
																	
																}else{
																
																	$wage_rate = default_value(getFirstItem("select var_value from vars where var_name = 'wage_".$this_lawful_year."'"),159);
																
																}
																
																
																
                                                                //$year_date = date("z", mktime(0,0,0,12,31,$this_lawful_year))+1;
                                                                $year_date = 365;
                                                                
                                                                
            
                                                                //20140224 - not used this
                                                                
                                                                
                                                                $start_money = $extra_employee*$wage_rate*$year_date;
																
																//echo $start_money;
                                                                
                                                            	//yoes 20180208 -> for year 2018 -> money is calculate separately for each 34 records
																//yoes 20181108 -> adjest these
																
																//$this_lawful_status = $lawful_row["LawfulStatus"];
																
																//yoes 20190803
																if(
																		$this_cid == 52427
																		&& $this_lawful_year == 2017
																	){
																		
																		$force_new_law = 1;
																		
																	}
																
																if($this_lawful_year >= 2018 && $this_lawful_year < 2050 && $this_lawful_status != 1
																
																	|| $force_new_law
																	
																){
																	
																	
																	
																	$m33_total_reduction_array = get33DeductionByCIDYearArray($this_cid, $this_lawful_year);
																	//print_r($m33_total_reduction);
																	$m33_total_reduction = $m33_total_reduction_array[m33_total_reduction];
																	$m33_total_missing = $m33_total_reduction_array[m33_total_missing];
																	$m33_total_interests = $m33_total_reduction_array[m33_total_interests];
																	
																	//yoes 20181108
																	$m35_total_reduction_array = get35DeductionByCIDYearArray($this_cid, $this_lawful_year);
																	$m35_total_reduction = $m35_total_reduction_array[m35_total_reduction];
																	$m35_total_missing = $m35_total_reduction_array[m35_total_missing];
																	$m35_total_interests = $m35_total_reduction_array[m35_total_interests];
																	
																	
																}
                                                                
                                                                
                                                                $the_sql = "select sum(receipt.Amount) 
                                                                    from payment, receipt , lawfulness
                                                                    where 
                                                                    receipt.RID = payment.RID
                                                                    and
                                                                    lawfulness.LID = payment.LID
                                                                    and
                                                                    ReceiptYear = '$this_lawful_year'
                                                                    and
                                                                    lawfulness.CID = '".$this_id."'
                                                                    and
                                                                    is_payback = 1
                                                                    ";
                                                                    
                                                                $payback_money = getFirstItem("$the_sql");
                                                                
                                                                //------
                                                                
                                                                //echo "start money: $start_money<br>" ;
                                                                //echo "paid_money: $paid_money<br>" ;
                                                                
                                                                $owned_money = $start_money - $paid_money ;//+$payback_money
                                                                
                                                                
                                                                
                                                                if($owned_money < 0){
                                                                    $owned_money = 0;
                                                                }
                                                            
                                                                
                                                            
                                                            ?>
                                                        
                                                     
                                                      <tr>
                                                        <td>
                                                        
                                                          <a name="the_payment_details" id="the_payment_details"></a>
                                                          
                                                          
                                                        <hr />
                                                       <strong> ข้อมูลการส่งเงินเข้ากองทุน</strong>
                                                       
                                                       <?php 
													   
													   
													   //if($sess_accesslevel!=4 && $sess_accesslevel!=5 && !$is_blank_lawful && $this_lawful_year <= 2015 && 1==0){
														   
													//yoes 20160111 -- reenable this for now
													//*toggles payment
													if(
													$sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && !$case_closed
													 && !$is_blank_lawful // yoes 20200108 - allow พมจ and ส่วนกลาง to use this again && 1==1 //&& $this_lawful_year <= 2015 
													 ){
														   
														   
														?>
                                                        
                                                        <?php 
														
															//yoes 20210326 -- allow pairoj.s and saowaluk.t to manual add items
															if($this_lawful_year < 2016 || $sess_userid == 1 || $sess_userid == 7999 || $sess_userid == 3242
															
															|| $sess_accesslevel == 1){ // // yoes 20200108 - allow พมจ and ส่วนกลาง to use this again // && 1==0 || $sess_userid == 1?>
                                                        
                                                        <br />
                                                       <a href="org_list.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;">+ เพิ่มข้อมูลการส่งเงินเข้ากองทุน</a>
                                                       <?php }//else{?>
                                                       
													   <?php //yoes 20221601 
													   
													   if(
															
															($this_lawful_year < 2022
															&& 1==0) //yoes 20220223 --> stop using the old one
															||
															($sess_accesslevel == 1
															&& 1==0)
														){
													   ?>
                                                        <br />
                                                       <a href="add_invoice.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;">+ พิมพ์ใบชำระเงินสำหรับระบบใบเสร็จออนไลน์</a>
													   <?php } ?>
													   
													   <?php if(
																
																($sess_accesslevel == 1 || $sess_accesslevel == 2) 
																|| ($sess_accesslevel == 3 && $this_lawful_year >= 2022 && $this_lawful_year < 2100) 
																|| $sess_accesslevel == 3 //yoes 20220223 --> open for ตจว
															
															){//yoes 20211108 64 opt-in ?>
													    <br />
                                                       <a href="add_invoice_pro.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;">+ พิมพ์ใบชำระเงินสำหรับระบบใบเสร็จออนไลน์ (ระบุแบ่งชำระ 33/34/35)</a>
													   <?php }?>
													   
                                                       <?php //}?>
                                                       
                                                       <?php if($this_lawful_year == 2011){?>
                                                         <br />
                                                       <a href="#" style="font-weight: bold; color:#600;" onclick="fireMyPopup('54_interest_popup',500,250); return false;">+ คิดดอกเบี้ยสำหรับสถานประกอบการปี 2554</a>
                                                       <?php }?>
                                                       
                                                       <?php }?>
                                                       
                                                       <br />
                                                        <a href="#" onClick="togglePaymentDetails(); return false;">- แสดงข้อมูลการจ่ายเงิน</a>
                                                        
                                                        <script>
                                                        
                                                            function togglePaymentDetails(){
                                                            
                                                            
                                                                if(document.getElementById('payment_details').style.display == 'none'){
                                                                    document.getElementById('payment_details').style.display = '';																
                                                                }else{
                                                                    document.getElementById('payment_details').style.display = 'none';
                                                                }
                                                            
                                                            }
                                                        
                                                            
                                                        
                                                        </script>
                                                        
                                                        
                                                        
                                                        <?php 
														
														
														//yoes 200170415 -- see if there are "add receipt" request
														if(
														
															$this_lawful_year < 2016
															&&
															$sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && !$case_closed
															 && !$is_blank_lawful
															
															
															){
                                                        
                                                        
															$request_sql = "
											
																
																SELECT 
																	*
																FROM 
																	payment_request a
																		join
																			receipt_request b												
																		on
																			a.RID = b.RID											
																where
																	request_status = 0
																	and
																	lid = '".$lawful_values["LID"]."'
															
															";
															
															
															//echo $request_sql;
                                                                
                                                                
                                                                $request_result = mysql_query($request_sql);
																
																
																
																
																if(mysql_num_rows( $request_result)){
																	
																	
																	
																	?>
                                                                    
                                                                    <div style="padding: 10px 0;">
                                                                    <table border="1" style="border:1px solid #CCC; border-collapse: collapse;">
                                                                    <tr>
                                                                        <td colspan="5">
                                                                        <div align="center" style=" color:#060">
                                                                       คำขอต้องการเพิ่มใบเสร็จตามมาตรา 34
                                                                        </div>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                     <tr>
                                                                                               
                                                                        
                                                                        <td align="center"> 
                                                                        
                                                                        <div align="center">
                                                                         เล่มที่ใบเสร็จ
                                                                          </div>
                                                                          
                                                                          </td>
                                                                          
                                                                          
                                                                          <td align="center"> 
                                                                        
                                                                        <div align="center">
                                                                         เลขที่ใบเสร็จ
                                                                          </div>
                                                                          
                                                                          </td>
                                                                          
                                                                          
                                                                          <td align="center">
                                                                        
                                                                        <div align="center">
                                                                        
                                                                        ข้อมูลปี
                                                                        
                                                                        </div>
                                                                        </td>
                                                                        
                                                                        
                                                                        <td align="center">
                                                                        <div align="center">
                                                                         ประเภทการขอ
                                                                          </div>
                                                                        </td>
                                                                        
                                                                        <td align="center">
                                                                        <div align="center">
                                                                         ผู้ขอ
                                                                          </div>
                                                                        </td>
                                                                    </tr>
                                                                    
                                                                    
                                                                    <?php                                                                
                                                                
																	while($request_row = mysql_fetch_array($request_result)){
																		
																		$receipt_row = $request_row;
																	
																	?>
																	
																	 <tr>
																	   <td align="center" style="text-align: center"><a href="view_payment.php?id=<?php echo $receipt_row[RID]?>&view=request" target="_blank">
																	   
																	   <?php 
																		
																		
																						
																		echo $receipt_row[BookReceiptNo];
																	   
																	   ?>
																	   
																	   </a></td>
																	   <td align="center" style="text-align: center"><a href="view_payment.php?id=<?php echo $receipt_row[RID]?>&view=request"> <?php 
																		
																		echo $receipt_row[ReceiptNo];
																	   
																	   ?></a></td>
																	   <td align="center" style="text-align: center"><?php 
																		
																		echo $receipt_row[ReceiptYear]+543;
																	   
																	   ?></td>
																	   <td align="center" style="text-align: center">
																	   
                                                                       เพิ่มใบเสร็จ
                                                                       
                                                                       </td>
																	   <td align="center" style="text-align: center"><?php 
																	   
																	   
																		echo getFirstItem("select concat(FirstName, ' ', LastName) from users where user_id = '".$request_row[request_userid]."'") ;
																	   
																	   ?></td>
																	 </tr>
																	 
																	 <?php 
																	 
																	}
																	 
																	 ?>
																	
																	
																 </table>
                                                           		 </div>
                                                            
                                                            
                                                            
                                                            <?php
                                                        
															}
                                                        
                                                        
                                                        }?>
                                                        
                                                        
                                                        
                                                       
                                                       
                                                       <?php if($the_54_budget_date){?>
                                                       <div style="color: #060; padding: 10px 0;" >
                                                        มีการคิดดอกเบี้ยสำหรับปี 2554 ตั้งแต่วันที่ <?php echo formatDateThai($the_54_budget_date);?>
                                                        </div>
                                                        <?php }?>
                                                       
                                                       
                                                       <?php echo "<div id='payment_details' >";?>
                                                       
                                                       
                                                        <?php 
                                                            //generate reciept info
                                                           /* $the_sql = "select * from payment, receipt , lawfulness
                                                                        where 
                                                                        receipt.RID = payment.RID
                                                                        and
                                                                        lawfulness.LID = payment.LID
                                                                        and
                                                                        ReceiptYear = '$this_lawful_year'
                                                                        and
                                                                        lawfulness.CID = '".$this_id."' 
                                                                        
                                                                        and
                                                                        is_payback != 1
                                                                        order by ReceiptDate, BookReceiptNo, ReceiptNo asc";*/
																		
															//yoes 20160615 --> rely on RID instead
															$the_sql = "select * from payment, receipt , lawfulness
                                                                        where 
                                                                        receipt.RID = payment.RID
                                                                        and
                                                                        lawfulness.LID = payment.LID
                                                                       
                                                                        and
																		payment.LID = '".$lawful_values["LID"]."'
                                                                        
                                                                        and
                                                                        is_payback != 1
                                                                        order by ReceiptDate, BookReceiptNo, ReceiptNo asc";
                                                            
                                                            //echo $the_sql;
                                                            $the_result = mysql_query($the_sql);
                                                            
                                                            $have_receipt = 0;
                                                            while($result_row = mysql_fetch_array($the_result)){
                                                            
                                                                $have_receipt = 1;
                                                                
                                                                //echo "select * from receipt where RID = '".$result_row["RID"]."'";										
                                                                $receipt_row = getFirstRow("select * from receipt where RID = '".$result_row["RID"]."'");
                                                            
                                                            ?>
                                                            
                                                                    
                                                                    
                                                                     <div style="padding:5px">
                                                                     <?php if($case_closed){ //yoes 20160118 case closed is read-only?>
                                                                     ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <?php echo $receipt_row["ReceiptNo"]?> 
                                                                     
                                                                     <?php }elseif($sess_accesslevel != 4){?>
                                                                        
                                                                        ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <a href="view_payment.php?id=<?php echo $result_row["RID"]?>"><?php echo $receipt_row["ReceiptNo"]?></a> 
                                                                        
                                                                        
                                                                        <?php }elseif($sess_accesslevel == 4 && strlen($receipt_row["BookReceiptNo"]) > 0 ){?>
                                                                        ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <?php echo $receipt_row["ReceiptNo"]?> 
                                                                        <?php //}elseif($sess_accesslevel == 4){
                                                                            }elseif(1 == 0){																	
                                                                        ?>
                                                                         <a href="scrp_delete_receipt.php?id=<?php echo doCleanOutput($result_row["RID"]);?>"  onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');" title="ลบข้อมูลการจ่ายเงินออกจากระบบ"><img src="decors/trashcan_icon.jpg" border="0" /></a>
                                                                        <?php }?>
                                                                        
                                                                        วันที่จ่าย <?php echo formatDateThai($receipt_row["ReceiptDate"])?> จำนวนเงิน <?php echo formatNumber($receipt_row["Amount"])?> บาท 
                                                                        
                                                                        
                                                                        
                                                                        
                                                                     <?php 
																	 //yoes 20160608 
																	 //see if this comes from somewhere
																	 
																	 $payment_meta = getFirstRow("
																	 	
																		select
																			*
																		from
																			payment_meta
																		where
																			meta_value = '".$result_row[RID]."'
																			and
																			meta_for = 'rid_from_to'
																	 
																	 ");
																	 
																	 //echo $meta_sql;
																	 
																	 if($payment_meta[meta_value]){
																		 
																		 
																		 $old_company_row = getFirstRow("
																		 	
																			select
																				*
																			from
																				company a
																					join lawfulness b																						
																						on
																						a.CID = b.CID
																					join payment c
																						on
																						b.LID = c.LID
																					join receipt d
																						on
																						c.RID = d.RID
																			where
																				d.RID =	'".$payment_meta[meta_pid]."'														
																		 
																		 	");
																		
																	 
																	 ?>
                                                                         <div style=" padding: 5px; color:#F30;">
                                                                            
                                                                            ** เป็นข้อมูลมาตรา 34 ที่ถูกย้ายมาจาก
                                                                            
                                                                                <?php echo formatCompanyName($old_company_row[CompanyNameThai], $old_company_row[CompanyTypeCode]);?>
                                                                            
                                                                         </div>
                                                                     
                                                                     <?php 
																	 }
																	 ?>
                                                                     
                                                                     
                                                                     <?php 
																	 //yoes 20160608 
																	 //see if this moved to somewhere
																	 
																	 $payment_meta = getFirstRow("
																	 	
																		select
																			*
																		from
																			payment_meta
																		where
																			meta_pid = '".$result_row[RID]."'
																			and
																			meta_for = 'rid_from_to'
																	 
																	 ");
																	 
																	 //echo $meta_sql;
																	 
																	 if($payment_meta[meta_value]){
																		 
																		 
																		 $old_company_row = getFirstRow("
																		 	
																			select
																				*
																			from
																				company a
																					join lawfulness b																						
																						on
																						a.CID = b.CID
																					join payment c
																						on
																						b.LID = c.LID
																					join receipt d
																						on
																						c.RID = d.RID
																			where
																				d.RID =	'".$payment_meta[meta_value]."'														
																		 
																		 	");
																		
																	 
																	 ?>
                                                                         <div style=" padding: 5px; color:#F30;">
                                                                            
                                                                            ** เป็นข้อมูลมาตรา 34 ที่ถูกย้ายไปยัง
                                                                            
                                                                                <?php echo formatCompanyName($old_company_row[CompanyNameThai], $old_company_row[CompanyTypeCode]);?>
                                                                            
                                                                         </div>
                                                                     
                                                                     <?php 
																	 }
																	 ?>
                                                                        
                                                                        
                                                                        
                                                                        
                                                                        จ่ายโดย <?php echo formatPaymentName($receipt_row["PaymentMethod"]);?> <?php
                                                                        
                                                                        $paid_for = getFirstItem("select count(*) from payment where payment.rid = '".$result_row["RID"]."'");
                                                                        if($paid_for > 1){
                                                                            echo "<span style='color:green'>จ่ายให้ $paid_for แห่ง</span>";
                                                                        }
                                                                        ?>
                                                                        
                                                                        
                                                                        
																	
																	
																	
															<div <?php if($this_year >= 2018 && $this_lawful_year <= 2500){ echo 'style="display: none;"';} ////yoes 20190417?>> 
																	
																	
																	
																		
																		
                                                                        <br />
																		เงินต้น ณ วันที่จ่าย
                                                                        
                                                                        <?php
                                                                        
                                                                        //echo "owned money = $owned_money<br>";
                                                                        //echo "paid_from_last_bill = $paid_from_last_bill<br>";
                                                                        
                                                                        $owned_money = $owned_money - $paid_from_last_bill;//+ $receipt_row["Amount"]
                                                                        echo formatNumber($owned_money);
                                                                        
                                                                        ?> บาท
                                                                        
                                                                        
                                                                        <br />
                                                                        
                                                                        <?php
                                                                        
                                                                            $this_paid_amount = $receipt_row["Amount"];											
                                                                                                            
                                                                                                            
                                                                            
                                                                            if(!$last_payment_date){
                                                                                
																				//yoes 20170123
																				if($the_54_budget_date){
																				
																					$last_payment_date = "$the_54_budget_date 00:00:00";
																				
																				}else{
																																										
																					$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);	
																				}
                                                                            }
																			
																			//echo $last_payment_date;
                                                                                                    
                                                                            //echo "$this_lawful_year-02-01 00:00:00";		
                                                                            //yoes 20151013 - fix this date so it show actual date...
																			//$last_payment_date_to_show didnt have real impact with actual calculation
																			$last_payment_date_to_show = $last_payment_date;
                                                                           
                                                                            //if last payment date is less than FEB 01 then detaulit it to FEB 01
                                                                            if(strtotime(date($last_payment_date)) 
                                                                                < 
                                                                                strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year)))){
                                                                            
                                                                               
																				
																				//yoes 20170123
																				if($the_54_budget_date){
																				
																					$last_payment_date = "$the_54_budget_date 00:00:00";
																				
																				}else{
																					
																					$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);	
																				}
                                                                            
                                                                            }                                                                                
                                                                            
                                                                            //echo "last_payment_date: $last_payment_date <br>";												
                                                                            $interest_date = getInterestDate($last_payment_date, $this_lawful_year, $receipt_row["ReceiptDate"],$the_54_budget_date);
                                                                            
                
																			
                                                                           
                                                                            
                                                                            
                                                                            //update last payment date to use it for next record
																			
																			//yoes 20170415
																			//alos account for year 2554 custom interest date
																			
																			if(
																			
																			$the_54_budget_date
																			
																			&&
																			
																			strtotime(date($receipt_row["ReceiptDate"])) 
																			<= 
																			strtotime(date("$the_54_budget_date 00:00:00"))
																			
																			){
																				
																				
																				$last_payment_date = "$the_54_budget_date 00:00:00";
																				
																				//echo "yipp";
																		
																			}else{
																				
																				$last_payment_date = $receipt_row["ReceiptDate"];
																			}
																			
																			
																			
                                                                            
																			
																			//echo $receipt_row["ReceiptDate"] ." < $the_54_budget_date 00:00:00";		
                                                                            
                                                                                                                                        
                                                                            //$interest_date = getInterestDate("2012-07-13 00:00:00", $this_lawful_year, $receipt_row["ReceiptDate"]);
                                                                            
                                                                            //echo "<br>2012-07-13 00:00:00" . " / ". $this_lawful_year . " / ". $receipt_row["ReceiptDate"]."<br>";
                                                                            
                                                                            //echo "interst date: $interest_date<br>";
                                                                            //echo "owned_money: $owned_money<br>";
                                                                            //echo "year_date: $year_date<br>";
                                                                            
																			//yoes 20170108
																			//interests for 2011
																			
                                                                            if($this_lawful_year >= 2012 || $do_54_budget){ //only show interests when 2012+
                                                                                $interest_money = doGetInterests($interest_date,$owned_money,$year_date);
                                                                            }else{
                                                                                $interest_money = 0;
                                                                            }
                                                                            
                                                                            
                                                                            
                                                                            
                                                                            //echo "<br>".$interest_date . " " . $owned_money . " " .$year_date."<br>";
                                                                            //$have_pending_interest = 0;
                                                                            if($total_pending_interest > 0){
                                                                            
                                                                                //if have pending interests, add it here
                                                                                $interest_money += $total_pending_interest;
                                                                            
                                                                            }
                                                                            
                                                                            
                                                                            
                                                                            if($this_paid_amount < $interest_money){
                                                                                $have_pending_interest = 1;
                                                                                $interest_money_to_show = $this_paid_amount;
                                                                            }else{
                                                                                $interest_money_to_show = $interest_money;
                                                                            }
                                                                            
                                                                            
                                                                            
                                                                            
                                                                            
                                                                        ?>
                                                                        
                                                                        
                                                                        <?php 
                                                                            
                                                                            if($is_pay_detail_first_row > 0){
                                                                            ?>
                                                                            
                                                                            
                                                                            
																				<?php 
                                                                                
                                                                                
                                                                                //yoes 20170415 ... 
                                                                                if(
																				
																				
																				$the_54_budget_date
																				
																				&&
																				
																				strtotime(date($receipt_row["ReceiptDate"])) 
                                                                                <= 
                                                                                strtotime(date("$the_54_budget_date 00:00:00"))){
                                                                                                                                                                    
                                                                                    //do nothing 
                                                                            
                                                                                }else{
																					
																					
																					
																					 if($is_54_pay_detail_first_row == 0 && $do_54_budget){		
																					
																					
																					?>
                                                                                    
                                                                                     <font color="#006600">วันที่เริ่มคิดดอกเบี้ย <?php echo formatDateThai($last_payment_date_to_show);?></font>
                                                                                     <br />
                                                                                    
                                                                                    <?php 
																					
																						$is_54_pay_detail_first_row++;
																					 
																					 }else{
                                                                                    
                                                                                    ?>
                                                                                    
                                                                                     วันที่จ่ายล่าสุด ของใบเสร็จนี้ <?php echo formatDateThai($last_payment_date_to_show);?><br />
                                                                                    
                                                                                    <?php
																					
																					}
                                                                                }
                                                                                
                                                                                ?>
                                                                               
                                                                        
                                                                        <?php
                                                                            }
                                                                            $is_pay_detail_first_row++;
                                                                        ?>
                                                                        
                                                                        
                                                                        
                                                                        <?php
																		
																		//yoes 20170108
																		//interests for 2011
																		 if($this_lawful_year >= 2012 || $do_54_budget){ //only show interests when 2012+?>
                                                                                        
                                                                                        
                                                                                        ดอกเบี้ย ณ วันที่จ่าย: <?php echo $interest_date;?> วัน x 7.5/100/<?php echo $year_date?> x <?php echo formatNumber($owned_money);?> = <?php  
                                                                                        
                                                                                        //echo $interest_money. "-" . $total_pending_interest ."-"; 
                                                                                        
                                                                                        echo formatNumber($interest_money - $total_pending_interest);?> บาท
                                                                                        
                                                                                        
                                                                                       
                                                                                        
                                                                                        
                                                                                        <?php 
                                                                                            if($total_pending_interest > 0){
                                                                                            
                                                                                            
                                                                                            ?>
                                                                                            
                                                                                            +ดอกเบี้ยค้างชำระ <?php echo formatNumber($total_pending_interest);?> บาท
                                                                                                
                                                                                        <?php	
                                                                                        
                                                                                        
                                                                                            }
                                                                                        ?>
                                                                                        
                                                                                        <br />
                                                                                        
                                                                        <?php }?>
                                                                                        
                                                                        
                                                                        <?php 
																		
																		
																		//yoes 20170108
																		//interests for 2011
																		
																		if($this_lawful_year >= 2012 || $do_54_budget){ //only show interests when 2012+?>
                                                                                     
																					จ่ายเป็นดอกเบี้ย
                                                                                    <?php
                                                                                        $total_interest_money += $interest_money_to_show;
                                                                                    
                                                                                        echo formatNumber($interest_money_to_show);
                                                                                        
                                                                                    ?>
																					บาท 
                                                                        
                                                                         <?php }?>
                                                                        
                                                                        
																		จ่ายเป็นเงินต้น 
                                                                        
                                                                        
                                                                        <?php 
                                                                        
                                                                            
																			
																			
                                                                            $this_paid_money = $this_paid_amount-$interest_money;
																			
																			
                                                                            
                                                                            if($this_paid_money < 0){
                                                                                $this_paid_money = 0;
                                                                            }
                                                                            
                                                                            $total_paid_money += $this_paid_money;
                                                                            
                                                                            echo formatNumber($this_paid_money);
                                                                            
                                                                            
                                                                            //echo "this paid money: ".$this_paid_money;
                                                                            
                                                                            $paid_money += $this_paid_money;
                                                                            
                                                                            $paid_from_last_bill = $this_paid_money;
                                                                            
                                                                            //if($paid_from_last_bill == 0){
                                                                            //	echo "wrong";
                                                                            //}else{
                                                                            //	echo "right ".$paid_from_last_bill;
                                                                            //}
                                                                        ?>
                                                                        
                                                                        
																		บาท 
                                                                        
                                                                        <?php 
                                                                        
                                                                        
                                                                        if($this_paid_amount < $interest_money){
                                                                            $pending_interest = (($interest_money - $this_paid_amount ));
                                                                            
                                                                            $total_pending_interest = $pending_interest;
                                                                        ?>
                                                                        ดอกเบี้ยค้างชำระ <?php echo formatNumber($pending_interest);?> บาท
                                                                        <?php }else{
                                                                        
                                                                            $total_pending_interest = 0;
                                                                        
                                                                        }?>
                                                                        
                                                                     </div>
																	 
																	 
															</div>
                                                                     
                                                                     <?php if(strlen($receipt_row["ReceiptNote"])>0){ ?>
                                                                         <div style="padding:5px">
																			ชำระเพื่อ: <?php echo $receipt_row["ReceiptNote"]?>                                                             
																		 </div>
                                                                     <?php } ?>
                                                                     
                                                                     
                                                                     
																	 
																
                                                                    
                                                                     
                                                            <?php
                                                                
                                                                }		//end while for looping to display payment details										
                                                            ?>
                                                            
                                                            <?php echo "</div> <!--- DIV closing payment details tag-->";?>
                                                            
                                                             <input name="have_receipt" type="hidden" value="<?php echo $have_receipt?>" />                                         	
                                                             
                                                            <hr />
                                                            
                                                        </td>
                                                        
                                                        
                                                       
                                                        
                                                     </tr>
                                                      
                                                      
                                                    <tr >
                                                        <td>
                                                                                                        
                                                        
                                                            <?php //if($start_money > 0){
                                                                //only show this for 2012++ year
                                                                
																
																//if($this_lawful_year > 2011){
																
																//yoes 20151208 -- turn it back so year 2011 also have m.34 details
																//is it thought? -> there is a good reason we not enabled m.34 for 2011
																//mainly because we start using this on 2012 - and there no telling what will happen if we enable it for 2011....
																
																//yoes 20151222 -- try this
																//if($this_lawful_year > 2011){
																
																//yoes 20151222
																//allow 34 details on all year
																if($this_lawful_year >= 2011){
																
																	
                                                                //only show this if has starting money
                                                            ?>
																
																<?php if(
																		$sess_accesslevel == 1
																		&& $this_lawful_year > 2012
																			){
																		include "organization_34_table.php";
																	}?>
															
																<?php if(
																		(
																			$sess_accesslevel == 1 || $sess_accesslevel == 2 
																			|| ($sess_accesslevel == 3 && $this_lawful_year >= 2022 && $this_lawful_year < 2100)
																			|| ($sess_accesslevel == 3) //yoes 20220223 -- open for all type of users
																			|| ($sess_accesslevel == 5) //yoes 20220317 -- open for ผู้บริหาร / งานคดี
																			|| ($sess_accesslevel == 8)
																			
																		)																		
																		){//yoes 20211108 64 opt-in ?>
																
																<font color=blue><strong>** ตารางการคำนวณ<u>เงินคงเหลือ</u>ที่ต้องจ่าย  **</strong></font><!---->
																<div id="calculated_34_table">
																	
																	<?php 
																		
																		/*$the_lid = $lawful_values["LID"];
																		$wage_rate = $wage_rate;
																		$year_date = $year_date;
																		$this_lawful_year = $this_lawful_year;
																	
																		include "organization_34_md_summary_table.php";*/?>
																
																</div>
																<?php } ?>
                                                            
                                                            
                                                            <?php }//starting_money > 0?>
                                                        
                                                        </td>
                                                      </tr>
                                                      
                                                       <script>
													  
														<?php
														
															$get34Table_row = array();
															$get34Table_row[the_lid] = $lawful_values["LID"];
															$get34Table_row[wage_rate] = $wage_rate;
															$get34Table_row[year_date] = $year_date;
															$get34Table_row[this_lawful_year] = $this_lawful_year;			
															$get34Table_row[extra_employee] = $extra_employee;	
															$get34Table_row[this_id] = $this_id;
														
														?>
													  
														function get34Table(json){
															
															<?php if((($sess_accesslevel == 1 ) || $sess_accesslevel == 2 || $sess_accesslevel == 3 || $sess_accesslevel == 5 || $sess_accesslevel == 8)){//|| $sess_accesslevel == 2 yoes 20211108 64 opt-in ?>
															$.ajax({
															  method: "POST",
															  url: "organization_34_md_summary_table.php?ws=1",
															  data: json
															 
															})
															  .done(function( html ) {				
																	//alert(html);
																	$("#calculated_34_table").html(html);

															  });
															<?php }?>
															
															////
														}
														
														function doGet34Table(json){															
															get34Table(<?php echo json_encode($get34Table_row); ?>);
														}
														
														doGet34Table();
													  
													  </script>
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                       <tr>
                                                        <td>
                                                        
                                                             <hr />
                                                           <strong> รายละเอียดขอเงินคืนจากกองทุนฯ </strong>
                                                           
                                                           <?php 
														   
														   
														   //yoes 20160331 --> allow ส่งเงินคืน ทุกปี ทุกกรณี
														   if($sess_accesslevel!=4 && $sess_accesslevel!=5 && !$is_blank_lawful && !$is_read_only){
														   //yoes 20160111 -- reenable this for now
															/*if(
															$sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && !$case_closed
															report_1333.php
															&& !$is_blank_lawful && $this_lawful_year <= 2015){*/
															   
															   
															   ?>
                                                       <a href="org_list.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>&payback=1" style="font-weight: bold;">+ เพิ่มรายละเอียดขอเงินคืนจากกองทุนฯ</a>
                                                       <?php }?>
                                                           
                                                           
                                                           <br />
                                                        <a href="#" onClick="togglePaybackDetails(); return false;">- แสดงข้อมูลการขอเงินคืน</a>
                                                        
                                                        <script>
                                                        
                                                            function togglePaybackDetails(){
                                                            
                                                            
                                                                if(document.getElementById('payback_details').style.display == 'none'){
                                                                    document.getElementById('payback_details').style.display = '';																
                                                                }else{
                                                                    document.getElementById('payback_details').style.display = 'none';
                                                                }
                                                            
                                                            }
                                                        
                                                            
                                                        
                                                        </script>
                                                           
                                                           
                                                       <?php echo "<div id='payback_details' >";?>
                                                           
                                                           
                                                             <?php 
                                                            //generate payback info
                                                            $the_sql = "select * from payment, receipt , lawfulness
                                                                        where 
                                                                        receipt.RID = payment.RID
                                                                        and
                                                                        lawfulness.LID = payment.LID
                                                                        and
                                                                        ReceiptYear = '$this_lawful_year'
                                                                        and
                                                                        lawfulness.CID = '".$this_id."' 
                                                                        
                                                                        and
                                                                        is_payback = 1
                                                                        order by receipt.RID desc";
                                                            
                                                            //echo $the_sql;
                                                            $the_result = mysql_query($the_sql);
                                                            
                                                            $have_receipt = 0;
                                                            while($result_row = mysql_fetch_array($the_result)){
                                                            
                                                                $have_receipt = 1;
                                                                
                                                                //echo "select * from receipt where RID = '".$result_row["RID"]."'";										
                                                                $receipt_row = getFirstRow("select * from receipt where RID = '".$result_row["RID"]."'");
                                                            
                                                            ?>
                                                                     <div style="padding:5px">
                                                                     
                                                                      <?php if($case_closed){ //yoes 20160118 case closed is read-only?>
                                                                       ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <?php echo $receipt_row["ReceiptNo"]?> 
                                                                     <?php }elseif($sess_accesslevel != 4){?>
                                                                        ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <a href="view_payment.php?id=<?php echo $result_row["RID"]?>"><?php echo $receipt_row["ReceiptNo"]?></a> 
                                                                        <?php }elseif($sess_accesslevel == 4 && strlen($receipt_row["BookReceiptNo"]) > 0){?>
                                                                        ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <?php echo $receipt_row["ReceiptNo"]?> 
                                                                        <?php //}elseif($sess_accesslevel == 4){
                                                                            }elseif(1 == 0){																	
                                                                        ?>
                                                                         <a href="scrp_delete_receipt.php?id=<?php echo doCleanOutput($result_row["RID"]);?>"  onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');" title="ลบข้อมูลการจ่ายเงินออกจากระบบ"><img src="decors/trashcan_icon.jpg" border="0" /></a>
                                                                        <?php }?>
                                                                        
                                                                        วันที่จ่าย <?php echo formatDateThai($receipt_row["ReceiptDate"])?> จำนวนเงิน <?php echo formatNumber($receipt_row["Amount"]); $total_payback_money += $receipt_row["Amount"];?> บาท จ่ายโดย <?php echo formatPaymentName($receipt_row["PaymentMethod"]);?> <?php
                                                                        
                                                                        $paid_for = getFirstItem("select count(*) from payment where payment.rid = '".$result_row["RID"]."'");
                                                                        if($paid_for > 1){
                                                                            echo "<span style='color:green'>จ่ายให้ $paid_for แห่ง</span>";
                                                                        }
                                                                        ?>
                                                                     </div>
                                                                     
                                                                     <?php if(strlen($receipt_row["ReceiptNote"])>0){ ?>
                                                                         <div style="padding:5px">
                                                                         ชำระเพื่อ: <?php echo $receipt_row["ReceiptNote"]?>                                                             </div>
                                                                     <?php } ?>
                                                                     
                                                                     
                                                                     
                                                            <?php
                                                                
                                                                }		//end loop for display "payback"										
                                                            ?>


                                                            <?php if($have_receipt == 1) { ?>
                                                                <div style="margin-top: 15px; padding: 10px; border-top: 1px solid #eee;">
                                                                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                                                                        <input type="checkbox"
                                                                               id="payback_verified"
                                                                               name="payback_verified"
                                                                               style="margin-top: 3px;"
                                                                               <?php if(getFirstItem("SELECT meta_value FROM `lawfulness_meta` WHERE `meta_lid` = '".$lawful_values["LID"]."' and meta_for = 'payback_verified'")){?>
                                                                               checked="checked"
                                                                               <?php }?>
                                                                        />
                                                                        <label for="payback_verified" style="font-size: 14px; color: #444;">
                                                                            ได้ทำการคืนเงินครบถ้วนตามที่ได้รับอนุมัติตามกฎหมายแล้ว
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                           
                                                           
                                                           <?php echo "</div>"; // end div for "payback"?>
                                                           
                                                           
                                                           
                                                           
                                                           
                                                           
                                                           
                                                         </td>
                                                         
                                                         
                                                         
                                                         
                                                          
                                                         
                                                         
                                                         
                                                       </tr>
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      <tr>
                                                        
                                                        <td colspan="4">
                                                        
                                                        <Div  align="">
															
															<table border="0" <?php if($submitted_company_lawful != 2){?>style="display: none;"<?php }?>>			
																<?php
																//yoes 20220225
																//---> contact address สำหรับส่งใบเสร็จ
																
																$contact_address_row = getFirstRow("
																														
																						select
																							*
																						from
																							company_by_year_company
																							left join provinces p on Province = p.province_id
																						where
																							cid = '$this_id'
																							and
																							year = '$this_lawful_year'
																							and
																							row_type = 1
																					
																					");
																
																

																 ?>
																<tr >
																	  <td colspan="3">
																		
																		<hr>
																		<font color="#006600">
																			<strong>ที่อยู่สำหรับส่งใบเสร็จตัวจริง: </strong>
																		</font>
																	 
																	  
																	  </td>
																	</tr>

																<tr>
																				<td>สถานที่ตั้งเลขที: </td>
																				<td>
																					<b><?php echo $contact_address_row["Address1"];?></b>
																				</td>
																				<td class="td_left_pad">ซอย: </td>
																				<td>
																					<b><?php echo $contact_address_row["Soi"];?></b>
																				</td>
																		  </tr>


																		  <tr>
																			<td>หมู่:</td>
																			<td>
																				<b><?php echo $contact_address_row["Moo"];?></b>
																			</td>
																			<td class="td_left_pad"> ถนน:</td>
																			<td>
																				<b><?php echo $contact_address_row["Road"];?></b>
																			</td>
																		  </tr>
																		  <tr>
																			<td>ตำบล/แขวง: </td>
																			<td>
																				<b><?php echo $contact_address_row["Subdistrict"];?></b>
																			</td>
																			<td class="td_left_pad"> อำเภอ/เขต:</td>
																			<td>
																				<b><?php echo $contact_address_row["District"];?></b>
																			</td>
																		  </tr>
																		  <tr>
																			<td>จังหวัด: </td>
																			<td> 
																				<b><?php echo 
																				
																				//yoes 20200401
																				//หน้า ejob ขาดจัวหวัดค่ะ
																				//https://app.asana.com/0/794303922168293/1169293249450220
																				$contact_address_row["province_name"];?></b>
																			</td>
																			<td class="td_left_pad"> รหัสไปรษณีย์:</td>
																			<td>
																				<b><?php echo $contact_address_row["Zip"];?></b>
																			</td>
																		  </tr>

																			 
																			  
																</table>
														
                                                                                <br />
                                                            <table border="0" >
                                                                                                        
                                                                    <tr >
                                                                      <td colspan="3">
                                                                        
                                                                        
                                                                        <font color="#006600">
                                                                            <strong>เอกสารประกอบการรายงาน มาตรา 34</strong>
                                                                        </font>
                                                                     
                                                                      
                                                                      </td>
                                                                    </tr>
                                                                    
                                                                    <tr bgcolor="#fcfcfc" >
                                                                     <td>
                                                                        <img id="ex341" src="exclaim_small.jpg" title="กรุณาแนบไฟล์" style="padding: 5px;" />
                                                                      </td>
                                                                      <td>
                                                                        สำเนา สปส 1-10 ส่วนที่ 1
                                                                        <br />
                                                                        
                                                                       
                                                                        ประจำเดือน ต.ค.<?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>
							<br />(พร้อมสำเนาใบเสร็จการชำระเงินของประกันสังคมเดือน ต.ค.<?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>)																		
                                                                      </td>
                                                                      <td>
                                                                        
                                                                        <?php 
                                                                            
                                                                            //do $this_id swap thing because doc link use LID, but consume $this_id
                                                                            //but $this_id on this page is CID and not LID...
                                                                            $required_doc++;
                                                                                                                                
                                                                            $this_id_temp = $this_id;
                                                                            $this_id = $lawful_values["LID"];       
                                                                                
                                                                            $file_type = "company_34_docfile_1_adm";                                                
                                                                            include "doc_file_links.php";                                                    
                                                                            $this_id = $this_id_temp;
                                                                                                                                    
                                                                            ?>
                                                                            <?php if($have_doc_file){$required_doc--;?><br /><script>$('#ex341').hide();</script><?php }?>
                                                                           <input type="file" name="company_34_docfile_1_adm" id="company_34_docfile_1_adm" /> 
                                                                           
                                                                           
                                                                          </td>
                                                                        </tr>
                                                                                                    
                                                                    
                                                                    
                                                                    <?php if(!$is_read_only && !$case_closed){?>
                                                                    <tr>
                                                                        <td colspan="3">
                                                                        
                                                                         <hr />
                                                                        <div align="center">
                                                                            <input type="submit" value="เพิ่มเอกสารประกอบ"/>
                                                                        </div>
                                                                        
                                                                        </td>
                                                                    </tr>  
                                                                    <?php }?>
                                                                    
                                                              
                                                            </table>
                                                        </Div>
                                                        
                                                        
                                                        
                                                        <hr />
                                                        
                                                        
                                                        
                                                        <hr />
                                                        
                                                        
                                                        </td>
                                                      </tr>
                                                    </table>
                                                    
                                                    
                                                    
                                                    <!-- end payment table -->
                                                    <!-- end payment table -->
                                                    <!-- end payment table -->
                                                    <!-- end payment table -->
                                                    <!-- end payment table -->
                                                     <!-- end payment table -->
                                                
                                                
                                                
                                                
                                              </td>
                                                <td valign="top">
                                               
                                            
                                            			
                                                        
                                                        <table style="border:1px solid #666; margin: 5px 0 0 5px;">
                                                        	<tr>
                                                            	<td colspan="3">
                                                               	 
                                                                <div align="center" style="font-weight: bold; padding: 5px; background-color:#efefef">สรุปการส่งเงิน</div>
                                                                </td>
                                                            	
                                                           </tr>
                                                           <tr>
                                                            	<td>
                                                               	เงินต้น 
                                                                </td>
                                                            	<td>
                                                                
                                                                    <div align="right">
                                                                    <?php echo formatNumber($total_paid_money); ?>
                                                                    </div>
                                                                
                                                                </td>
                                                            	<td>บาท</td>
                                                           </tr>
                                                            <tr>
                                                            	<td>
                                                               	ดอกเบี้ย 
                                                                </td>
                                                            	<td>
                                                                     <div align="right">
                                                                    <?php echo formatNumber($total_interest_money); ?>
                                                                    </div>
                                                                </td>
                                                            	<td>บาท</td>
                                                           </tr>
                                                            <tr>
                                                            	<td>รับเงินคืนจากกองทุน 
                                                                </td>
                                                            	<td>
                                                                <div align="right">
                                                                    <?php echo formatNumber($total_payback_money); ?>
                                                                  </div>
                                                                </td>
                                                            	<td>บาท</td>
                                                           </tr>
                                                           <tr>
                                                            	<td>
                                                               	รวม 
                                                                </td>
                                                            	<td>
																<div align="right">
																<strong><?php echo formatNumber($total_paid_money + $total_interest_money - $total_payback_money); ?></strong>
                                                                </div>
                                                                </td>
                                                            	<td>บาท</td>
                                                           </tr>
                                                       </table>
                                            
                                           
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        
                                        
                                        
                                        </td>
                                      </tr>
                                    </table>


<?php 
	if($output_values["CompanyTypeCode"] >= 200 && $output_values["CompanyTypeCode"] <= 300 || $sess_accesslevel == 6 ||  $sess_accesslevel == 7){
		//if(1==1){
?>							
<script>	
document.getElementById('rule_34_table').style.display = 'none';
</script>
<?php }?>








                                    
<table border="0">

	
  <tr>
    <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    
    	<?php if($sess_accesslevel == 4){?>
        
        
    
    	<?php }elseif($is_2013){?>

            <input name="Conc_status" type="hidden" id="Conc_status" value="<?php echo $lawful_row["Conc_status"];?>" />
            
            <?php if($lawful_row["Conc_status"] == 1){?>
        
            	<img src="decors/checked.gif" />
            
        
            <?php }else{?>
            
                <input name="" type="checkbox" value="" disabled="disabled" />
            
            <?php }?>
        
        
        <?php }else{?>
           <input name="Conc_status" type="checkbox" id="Conc_status" value="1"  <?php echoChecked($lawful_values["Conc_status"])?>/>                                                                          
        
        <?php }?>
        
        
        
        </td>
    <td><span style="font-weight: bold;color:#006600;">
    <?php //if($sess_accesslevel ==4){
	 if(1==1){?>
     	<hr />
        มาตรา 35 ให้สัมปทานฯ
     <?php }else{ ?>
        ปฏิบัติตามมาตรา 35
     <?php } ?></span></td>
    <td></td>
  </tr>
  <tr>
    <td ></td>
    <td>
    
    - มีผู้ใช้สิทธิ: <strong id="txt_curator_user"><?php 
					
										
			$curator_user = getNumCuratorFromLid($lawful_values["LID"]);
	
	
			echo $curator_user;
	
		?></strong> คน, ผู้พิการถูกใช้สิทธิ: <strong id="txt_curator_usee"><?php 
	
			
			//$curator_usee is disabled person who has parents (right is used by someone else)
			//disable whose rights is not used by someone else is not $curator_usee
			
			$curator_usee = getFirstItem("
						
								select count(*) 
								from 
								$curator_table_name 
								where 
								curator_lid = '".$lawful_values["LID"]."' and curator_parent > 0
			
								");
							
			echo $curator_usee;
	
		?></strong> คน
        
        
   
    
    <input name="curator_usee" type="hidden" value="<?php echo $curator_user;?>" />
    
    
    <?php if(
		(!$is_blank_lawful && $sess_accesslevel != 4 && $sess_accesslevel != 18 && !$is_read_only)
		||
		($sess_accesslevel == 4 && !$submitted_company_lawful)
		
		){?>
		
		
			<a href="" data-toggle="modal" data-target=".bs-example-modal-lg-m35" onClick="getCuratorForm(-1);">ข้อมูลผู้ใช้สิทธิมาตรา 35 คลิกที่นี่</a>
		
	
		
		
		
    <?php }?>
    
   
    
    
    
   
    </td>
    <td></td>
  </tr>
  
  
 
  
  <tr>
    <td ></td>
    <td>
   
   
   <?php 
   
   
   	//yoes 20151118 -- also show full details here
	if($submitted_company_lawful && $sess_accesslevel == 4){
		
	?>
   		
        <a href="#" onClick="toggleSubmittedCurator(); return false;">++ แสดงรายชื่อผู้ใช้สิทธิในปัจจุบัน - คลิกที่นี่</a>
        
        <?php include "company_curator_table.php"; ?>
        
        
         <script>
                                                        
				function toggleSubmittedCurator(){
				
				
					if(document.getElementById('submitted_curator_table').style.display == 'none'){
						document.getElementById('submitted_curator_table').style.display = '';																
					}else{
						document.getElementById('submitted_curator_table').style.display = 'none';
					}
				
				}
				
				toggleSubmittedCurator();
			
				
			
			</script>
        
    <?php
	}else{
		
		//else show the editable stufss	
		
	?>
   
    
   
   
  		
         <a href="#" onClick="$('#organization_35_details_table').toggle(); <?php /*//yoes 20220418*/if(1 == 1){?>$('#organization_35_details_table_64').toggle();<?php }?>  return false;">++ แสดงรายชื่อผู้ใช้สิทธิในปัจจุบัน - คลิกที่นี่</a>
         
         
		  <?php 
									//yoes 20231124
									if($mode != "new"){?>
          <?php 
			
			
			//yoes 20240215
			if(!$lawful_values["LID"]){
				
				//
				
			}elseif($sess_userid == 1){
				//yoes 20220418
				include "organization_35_details_table_64.php";			
			
			}elseif($sess_userid == -1 || $this_id == 67363){
				include "organization_35_details_table.php";			
			}elseif(1 == 1){
				include "organization_35_details_table_64.php";
			}else{
				include "organization_35_details_table.php";
			}
		   
		  
		  ?>
		  
		<?php }?>
          
          
          
          
          
          
           <?php 
			
				//yoes 20160120 case closed thing
				$curator_after_case_close = getFirstItem("
									select 
										count(*)
									from
										curator_extra
									where
										curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0
									");
									
				if($curator_after_case_close){
					
					
					//yoes 20160120 case closed thing
					$usee_after_case_close = getFirstItem("
									select 
										count(*)
									from
										curator_extra
									where
										curator_lid = '".$lawful_values["LID"]."' and curator_parent > 0
									");
					
				?>
			
			 <hr />- มีข้อมูลที่เพิ่มเข้ามาหลังจากปิดงาน ผู้ใช้สิทธิ: <strong style="color: #900;"><?php echo $curator_after_case_close;?></strong> คน, ผู้พิการถูกใช้สิทธิ: <strong style="color: #900;"><?php echo $usee_after_case_close;?></strong> คน
			
			<?php
					
				}
			
		  
		  
		 	if($curator_after_case_close){
				
				?>
                <br> <a href="#" onClick="$('#organization_35_details_tableextra').toggle();  return false;" >++ แสดงรายชื่อผู้ใช้สิทธิ ที่ถูกเพิ่มมาหลังจากการปิดงาน - คลิกที่นี่</a>
                <?php
				
				$is_extra_table = "extra";
				$origin_name = $curator_table_name;
				
				
				$curator_table_name = "curator_extra";
				include "organization_35_details_table.php";
		 		$is_extra_table = "";
				 
				$curator_table_name = $origin_name;
				
			}
		  
		  ?>
   
   
           
    
    
    <?php } //ends if($submitted_company_lawful){?>
    
    
    </td>
    <td></td>
  </tr>
  
  
   
  
  
  
</table>












<?php 


		////////////////// 
		//////////
		//////////		NEW AS OF 20140215
		//////////		Lawfulnewss 34 for ORG
		//////////
		////////////////// 

?>









<?php 


			
			
//PAYMENT FOR COMPANY ----> ONLY DO THIS IF NEEDED TO			
$extra_employee = $final_employee - $hire_numofemp - $curator_user;

		
if($sess_accesslevel == 4 && $extra_employee > 0){ // company do whatever company do  -------------> PAYMENT


?>
<table border="0" style="padding-top:20px;">

	
    
    
     
    
    
    
    
    
  <tr>
    <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    
           <!--<input name="sss" type="checkbox" id="sss" value="1" />-->                                                                      
        
       
        
        </td>
    <td><span style="font-weight: bold;color:#006600;">
    มาตรา 34 ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ </span>
    
    
    </td>
    <td></td>
    
  </tr>
  
 
   
    <tr>
        <td ></td>
        <td colspan="2">
        
        
    <div style="font-size:18px; padding:10px 0; color:#C00;">
	    ท่านต้องชำระเงินตามมาตรา 34 
    </div>
       
  <table cellpadding="3" style="margin:0px 0 0 20px;" >
   	  
        <tr>
            <td> ประจำปี</td>
            <td><strong><?php echo formatYear($this_year);?></strong>  </td>
            <td>&nbsp;</td>
            <td>อัตราค่าแรง</td>
            <td><strong id="province_wage"><?php 
			
			
			
			if($this_year == 2011){
										
				//use wage-rate by province instead
				$wage_rate = default_value(getFirstItem("select province_54_wage from provinces where province_id = '".$output_values["Province"]."'"),0);										
				$wage_rate = $wage_rate/2;
				
			}else{
			
				$this_year_wage = default_value(getFirstItem("select var_value from vars where var_name = 'wage_$this_year'"),300);
			
			}
			
			echo $this_year_wage;
			
			?></strong> บาท/วัน</td>
 	    </tr>
        
        
                	<tr>
                	  <td valign="top">จำนวนลูกจ้างทั่วประเทศ                      
                      
                      </td>
                	  <td valign="top"> 
                      		<strong><?php echo formatEmployee($employee_to_use);?></strong>
                                 
                                 </td>
                	  <td valign="top">คน</td>
                	  <td valign="top">อัตราส่วนที่ต้องรับคนพิการ</td>
                	  <td valign="top">
                      <?php echo $ratio_to_use;?> :1 = <strong id="employee_ratio"><?php  echo formatEmployee($final_employee);?></strong> คน
                      </td>
              	  </tr>
                	<tr>
                	  <td>รับคนพิการเข้าทำงานแล้ว</td>
                	  <td>
                      
                      <strong><?php echo $hire_numofemp;?></strong>
                      
                      </td>
                	  <td>คน</td>
                	  <td>ให้สัมปทานฯ ตาม ม.35</td>
                	  <td>
                      
                      <strong><?php echo $curator_user;?></strong>
                      
                	  คน/สัญญา</td>
              	  </tr>
                  
                 
                  
                  
                  <tr>
                	  <td>วันที่ต้องการชำระเงิน</td>
                	  <td>
                      
                      <?php 
					  
					  	//get inputted date
						$company_pay_data = getFirstRow("select * from payment_company where CID = '$this_id' and Year = '$this_lawful_year'");
                                                
					    //
					  	$selector_name = "the_pay_date";						
						$this_date_time = $company_pay_data["PayDate"];	
						//
						$this_ref_number = $company_pay_data["RefNo"];			
														   
						
					  	include "date_selector.php";
					  
					  ?>
                      
                      
                	    </td>
                	  <td>&nbsp;</td>
                	  <td>&nbsp;</td>
                	  <td></td>
              	  </tr>
                  
                  
                  
                  
                  <?php 
				  
				  	$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $this_date_time);
				  
				  ?>
                  
                  
                  
                  
                  
                	<tr <?php if(!$interest_date){?> style="display:none"<?php }?>>
                	  <td>เงินต้น
                      <!--<br>
               	      (จ่ายภายในวันที่ 31 ม.ค. 2557)--> </td>
                	  <td colspan="4">
                	    
                        
                        <span id="final_value_to_show" style="color:#060;">
               	        
							<?Php
                            
                            if($extra_employee < 0){
                                $extra_employee = 0;	 
                            }
                            
                            $extra_money = $extra_employee * $this_year_wage * 365;	
							
							
                            
                             echo formatNumber($extra_money);
                             
                             ?>
                         
                         
               	        
               	        </span> บาท
                	                   	        
                	     
                         
                         
                         
               	     </td>
               	    </tr>
                    
                    
                    <tr <?php if(!$interest_date){?> style="display:none"<?php }?>>
                	  <td>
                      ดอกเบี้ย     (<?Php
                            
							
                            
                             echo ($interest_date);
                             
                             ?> วัน นับตั้งแต่วันที่ 31 ม.ค.)                 
                      </td>
                	  <td colspan="4">
                	    
                        <font color="#FF0000"><?php 
						
						$interest_money = doGetInterests($interest_date,$extra_money,$year_date);
						
						 echo formatNumber($interest_money);
						 

						 
						
						?></font>
                       
               	        
							
                         
						บาท
                         
                         
               	     </td>
               	    </tr>
                    
                     <tr>
                	  <td>
                     จำนวนเงินที่ต้องจ่าย           
                      </td>
                	  <td colspan="4">
                	    
                        <?php 
						
							$final_money = $interest_money + $extra_money;
							
							echo formatNumber($final_money);
						
						?> บาท
                        
                        <input type="hidden" name="Amount" value="<?php echo $final_money;?>" />
                         <input type="hidden" name="auto_post" value="<?php echo $_GET["auto_post"];?>" />
                         
                         
               	     </td>
               	    </tr>
                    
                
                
                  
                  
                  <tr>
                	  <td>จ่ายโดย</td>
                	  <td colspan="3"><select name="PaymentMethod" id="PaymentMethod" onchange="doToggleMethod();">
                                    <option value="Cash" >เงินสด</option>
                                    <option value="Cheque" <?php if($company_pay_data["PaymentMethod"]=="Cheque"){echo "selected='selected'";}?>>เช็ค</option>
                                    <option value="Note" <?php if($company_pay_data["PaymentMethod"]=="Note"){echo "selected='selected'";}?>>ธนาณัติ</option>
                                  </select></td>
                	  
              	  </tr>
                   <tr>
                                <td colspan="4">
                                
                                
                                
                                <table id="cash_table" border="0" style="padding:0px 0 0 50px;" >
                                    <tr>
                                      <td><span style="font-weight: bold">ข้อมูลการจ่ายเงินสด</span></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>
                                  
                                  
                                    <table id="cheque_table" border="0" style="padding:0px 0 0 50px;"  >
                                      <tr>
                                        <td><span style="font-weight: bold">ข้อมูลการจ่ายเช็ค</span></td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">ธนาคาร</span></td>
                                        <td><?php
											
												$this_bank_id = $company_pay_data["bank_id"];
										
											  include "ddl_bank.php";
											  ?>                                        </td>
                                      </tr>
                                      <tr>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่เช็ค</span></td>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">
                                          <input name="Cheque_ref_no" type="text" id="Cheque_ref_no" value="<?php echo $this_ref_number;?>"  />
                                        </span></td>
                                      </tr>
                                      
                                      <?php if($this_date_time){?>
                                      
                                      <tr>
                                        <td>ลงวันที่</td>
                                        <td>
                                        <?php 
										
										$selector_name = "the_date";										
										$this_date_time = $company_pay_data["PayDate"];											
										//include "date_selector.php";
										
										echo formatDateThai($this_date_time);
										
										
										?>
                                        </td>
                                        
                                      </tr>
                                      
                                      <?php }?>
                                      
                                    </table>
                                    
                                    
                                    
                                    
   								 <table id="note_table" border="0" style="padding:0px 0 0 50px; " >
                                      <tr>
                                        <td><span style="font-weight: bold">ข้อมูลการจ่ายธนาณัติ</span></td>
                                        <td>&nbsp;</td>
                                       
                                      </tr>
                                      <tr>
                                        <td>เลขที่ธนาณัติ</td>
                                        <td><span class="style86" style="padding: 10px 0 10px 0;">
                                          <input name="Note_ref_no" type="text" id="Note_ref_no" value="<?php echo $this_ref_number;?>"  />
                                        </span></td>
                                        
                                      </tr>
                                      
                                      <?php if($this_date_time){?>
                                           <tr>
                                            <td>ลงวันที่</td>
                                            <td>
                                             <?php 
                                            
                                            $selector_name = "the_note_date";										
                                            $this_date_time = $company_pay_data["PayDate"];												
                                            //include "date_selector.php";
                                            echo formatDateThai($this_date_time);
                                            
                                            ?>
                                            </td>
                                            
                                          </tr>
                                      <?php }?>
                                      
                                  </table>
                                  
                                  
                                  
                                  </td>
                              </tr>
                              
                              <?php if(!$submitted_company_lawful){?>
                              <tr>
                              	<td colspan="6">
                                <hr />
                                    <div align="center">
                                    
                                      <input type="submit" value="คำนวณเงินมาตรา 34" />
                                                                      
                                    </div>
                                <hr />
                                </td>
                                
                             </tr>
                             <?php }?>
                             
                             
                            
                  
 				 </table>
                 
                 <script>
									
														
							function doToggleMethod(){
							
								the_method = document.getElementById("PaymentMethod").value;
							
								document.getElementById("cash_table").style.display = "none";
								document.getElementById("cheque_table").style.display = "none";
								document.getElementById("note_table").style.display = "none";
								
								if(the_method == "Cash"){
									//document.getElementById("cash_table").style.display = "";
								}else if(the_method == "Cheque"){
									document.getElementById("cheque_table").style.display = "";
								}else if(the_method == "Note"){
									document.getElementById("note_table").style.display = "";
								}
							}	
							
							doToggleMethod();							
							
							
						</script>
                 
       
       </td>
   
   </tr>
   

	
    
    
    

  
</table>  

<?php }//end if($sess_accesslevel == 4 ////---------> Payment?>






<?php 


		////////////////// 
		//////////
		//////////		NEW AS OF 20140215
		//////////		Company Attachment
		//////////
		////////////////// 

?>


<?php if($sess_accesslevel == 4){ // company do whatever company do?>

<table border="0" style="padding-top:20px;">

	
  <tr>
    <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
          <!-- <input name="ปปป" type="checkbox" id="ปปป" value="1" /> -->
          
          
        </td>
    <td><span style="font-weight: bold;color:#006600;">
    เอกสารเพิ่มเติม</span></td>
    <td></td>
    <td></td>
    
  </tr>
  
 
   
    <tr>
    	<td >
        </td>
        <td  >เอกสารประกอบ
		
		<br><font style="font-size: 11px;">(ไฟล์ jpg, gif หรือ pdf เท่านั้น)</font>
		</td>
        <td colspan="2" >
        
        
        
        <div style="width:400px; padding-bottom:5px;">
        <?php 
            
            //do $this_id swap thing because doc link use LID, but consume $this_id
            //but $this_id on this page is CID and not LID...
            $this_id_temp = $this_id;
            $this_id = $lawful_values["LID"];
			
			//echo $this_id . $this_year;
            
            $file_type = "company_docfile";
        
            include "doc_file_links.php";
            
            $this_id = $this_id_temp;
            
        ?>
        </div>
                           
        
        <?php if(!$submitted_company_lawful){?>
                                
            <input type="file" name="company_docfile" id="company_docfile" multiple="multiple"/>
            
            <hr />
            
            <input type="submit" value="เพิ่มเอกสารประกอบ"/>
        
        <?php }?>
        
        </td>
       
    </tr>
    
    
    
    <tr>
    	<td >
        </td>
    	<td colspan="3" >
        <hr />
        </td>
    </tr>
    
    
    <tr>
    	<td >
        </td>
        <td valign="top">
        <span style="font-weight: bold;color:#006600;">
        หมายเหตุเพิ่มเติม(ถ้ามี)
        </span>
        </td>
        <td colspan="2" >
       
       
       
       
        </td>
    </tr>
    
   
    
    <tr>
    	<td >
        </td>
        <td colspan="3">
       
       
       
       <textarea name="lawful_remarks" cols="50" rows="4" id="lawful_remarks"><?php echo getFirstItem("select lawful_remarks from lawfulness_company where CID = '$this_id' and Year = '$this_lawful_year'");?></textarea>
       
       
        </td>
    </tr>
    
    
    
    
</table>    


<?php }?>








<script>
	function doPopSubCurator(parent){				
						
		document.getElementById('curator_parent').value = parent;
		
		if(parent == 3){
		
			document.getElementById('curator_input_forms').style.display = 'none';
		
		}else if(parent == 0){
		
			//parent -> have events
			document.getElementById('tr_curator_event').style.display = '';
			document.getElementById('tr_curator_event_2').style.display = '';
			document.getElementById('tr_curator_docfile').style.display = '';
			document.getElementById('tr_curator_disable').style.display = 'none';
			
			document.getElementById('the_parent').style.display = '';
			document.getElementById('the_child').style.display = 'none';
			
			document.getElementById('curator_input_forms').style.display = '';
			
			//
			document.getElementById('curator_is_disable').style.display = '';
			document.getElementById('curator_is_disable_text').style.display = '';
			
			document.getElementById('curator_start_date_text').style.display = '';
			document.getElementById('curator_start_date_day').style.display = '';
			document.getElementById('curator_start_date_month').style.display = '';
			document.getElementById('curator_start_date_year').style.display = '';
			
			document.getElementById('curator_end_date_text').style.display = '';
			document.getElementById('curator_end_date_day').style.display = '';
			document.getElementById('curator_end_date_month').style.display = '';
			document.getElementById('curator_end_date_year').style.display = '';
			
		}else{
		
			document.getElementById('tr_curator_event').style.display = 'none';
			document.getElementById('tr_curator_event_2').style.display = 'none';
			document.getElementById('tr_curator_docfile').style.display = 'none';
			document.getElementById('tr_curator_disable').style.display = '';
			
			document.getElementById('the_parent').style.display = 'none';
			document.getElementById('the_child').style.display = '';
			
			document.getElementById('curator_input_forms').style.display = '';
			
			//
			document.getElementById('curator_is_disable').style.display = 'none';
			document.getElementById('curator_is_disable_text').style.display = 'none';
			
			document.getElementById('curator_start_date_text').style.display = 'none';
			document.getElementById('curator_start_date_day').style.display = 'none';
			document.getElementById('curator_start_date_month').style.display = 'none';
			document.getElementById('curator_start_date_year').style.display = 'none';
			
			document.getElementById('curator_end_date_text').style.display = 'none';
			document.getElementById('curator_end_date_day').style.display = 'none';
			document.getElementById('curator_end_date_month').style.display = 'none';
			document.getElementById('curator_end_date_year').style.display = 'none';
		
		}
	}
</script>

 


<div align="center">
    	            
    	</div>


<!----------------------->


                                  <td>&nbsp;</td>
                                </tr>
                                
                                
                                
                                <tr>
                                  <td colspan="2"><div align="center">
                                  
                                  	<script>
									
										function doShowConfirm(){
											$("#update_lawful").show();
											
											$("#update_lawful_yes").hide();
											$("#update_lawful_no").show();
										}
									
									
										function doHideConfirm(){
											
											$("#update_lawful").hide();
											
											$("#update_lawful_yes").show();
											$("#update_lawful_no").hide();
											
										}
								
									
									</script>
                                  	
                                  	<hr />
                                    
                                    	<?php
												
													//yoes 2019016
													//add this for new law
													//variable is frin widget_check_xxx
													
													//echo $have_duplicate_33 ."xxxx". $have_duplicate_35;
													
													if($this_lawful_year >= 2018 && $this_lawful_year <= 2050){
																												
													
													?>
													<span id="dupe_status_2018"></span>													
													<?php
														
													}else{
														
														
														
														//yoes 20160201 -- more conditions for close case
														$valid_33_35_sql = "
														
															select
																 count(the_id_card)
														
															from
															
															(
															
																select
																	a.le_code as the_id_card
																from
																	lawful_employees a
																		
																		join (																																	
															
																			select
																				le_code as the_id_card
																			from
																				lawful_employees
																			where
																				le_cid = '$this_cid'
																				and
																				le_year = '$this_lawful_year'
																				and
																				le_is_dummy_row = 0	
																				
																			union
																			
																			select
																				curator_idcard as the_id_card
																			from
																				curator
																			where
																				curator_lid = '".$lawful_values["LID"]."'
																				
																				and
																				curator_is_dummy_row = 0	
																				and
																				curator_is_disable = 1
																		
																		) bb
																			on a.le_code = bb.the_id_card
																		
																	
																where
																	
																	a.le_year = '$this_lawful_year'
																	and
																	a.le_is_dummy_row = 0
																									
																
																	
																	
																	
																union all
																
																
																select 
																	a.curator_idcard as the_id_card
																from 
																	curator a
																	join
																		 lawfulness b
																			on
																				a.curator_lid 	= b.LID
																				and
																				b.year = '$this_lawful_year'
																	
																		join (																																	
															
																			select
																				le_code as the_id_card
																			from
																				lawful_employees
																			where
																				le_cid = '$this_cid'
																				and
																				le_year = '$this_lawful_year'
																				and
																				le_is_dummy_row = 0	
																				
																			union
																			
																			select
																				curator_idcard as the_id_card
																			from
																				curator
																			where
																				curator_lid = '".$lawful_values["LID"]."'
																				
																				and
																				curator_is_dummy_row = 0	
																				and
																				curator_is_disable = 1
																		
																		) bb
																			on a.curator_idcard = bb.the_id_card
																
																where 
																	
																	
																	
																	curator_is_dummy_row = 0
																	and
																	curator_is_disable = 1
																
															) zzz
															
															
															group by
																the_id_card														
															having 
																count(the_id_card) > 1
															
															
														
														";
														
														//echo $valid_33_35_sql;
														
														
														
														$invalid_33_35 = getFirstItem($valid_33_35_sql);
													}
												
												?>
                                                
                                                
                                        <?php if($invalid_33_35){?>
                                        <div align="center">
                                        <font color="#FF6600">มีข้อมูลซ้ำซ้อนในมาตรา 33 หรือ มาตรา 35</font> - กรุณาทำการตรวจสอบข้อมูล
                                        </div>
                                        <?php }?>
                                    
                                    
                                      <input name="Year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                      
                                      <?php if($sess_accesslevel == 5 || $sess_accesslevel == 18 || $is_read_only || $case_closed){ //exec can do nothing yoes 20160118 -- also check if case's closed?>
                                      
                                      <?php }elseif($sess_accesslevel !=4 ){ ?>
                                      
                                              <input type="button" id="update_lawful_yes" value="ยืนยันปรับปรุงข้อมูล" onclick="doShowConfirm();"/>
                                              
                                              
                                              
                                              <input type="submit" name="update_lawful" id="update_lawful" value="** กดที่นี่อีกครั้งเพื่อปรับปรุงข้อมูล" 
                                              
                                                <?php if(!$_GET["auto_post"]){?>
                                                style="display: none;"
                                                <?php }?> 
                                                
                                                  <?php if(!$_GET["auto_post"] && 1==0){ //no longer used?>
                                                  onclick = "return confirm('ต้องการปรับปรุงข้อมูลการปฏิบัติตามกฎหมายนี้?');"
                                                  <?php }?>
                                                  
                                              />
                                              <input type="button" id="update_lawful_no" value="ยกเลิกการปรับปรุงข้อมูล" style="display: none;" onclick="doHideConfirm();"/>
                                              
                                              
                                              
                                                <?php if($_GET["auto_post"]){ //if this is an auto-post?>
                                                    
                                                    <input name="le" type="hidden" value="<?php echo $_GET["le"];?>" />
                                                    <input name="delle" type="hidden" value="<?php echo $_GET["delle"];?>" />
                                                    <input name="curate" type="hidden" value="<?php echo $_GET["curate"];?>" />
                                                    <input name="curator_id" type="hidden" value="<?php echo $_GET["curator_id"];?>" />
                                                    
                                                    
                                                    <input name="the_focus" type="hidden" value="<?php echo $_GET["focus"];?>" />
                                                   
                                                
                                                <?php }?>
                                                    
													
												
                                                 
                                        
                                        
                                      <?php }else{ //if sess_accesslevel == 4 ?>
                                      
                                             
                                              <?php if(!$submitted_company_lawful){?>
                                             
                                             
                                                  <input type="submit" name="submit_lawful" id="submit_lawful" value="บันทึกข้อมูล" 
                                                  onclick = "return confirm('บันทึกข้อมูลการปฏิบัติตามกฎหมายนี้?');"
                                                  />
                                                  
                                                  
                                                
                                                  <input type="button" name="submit_doc" id="submit_doc" value="ยื่นแบบฟอร์มออนไลน์"                                      
                                                  />
                                                  
                                                  <input type="hidden" name="is_submitted" id="is_submitted" value="0" />
                                              
                                              <?php }?>
                                              
                                              <hr />
                                              
                                              
                                              
                                              
                                            <script>
                                                function doConfirmCancel(){
                                                    if(confirm('ยกเลิกการแก้ไขข้อมูล และกลับไปหน้าแรก?')){
                                                        document.location = "organization.php?id=<?php echo $this_id;?>";
                                                    }else{
                                                        return false;
                                                    }
                                                }
                                              </script>
                                              
                                              
                                              
                                            <script>                                        
                                                
                                                var form = document.getElementById('lawful_form');
                                                
                                                document.getElementById('submit_doc').onclick = function() {
                                                    
                                                    if(confirm('การยื่นเอกสารออนไลน์  หลังจากยื่นแล้วจะไม่สามารถกลับมาแก้ไขข้อมูลผ่านระบบออนไลน์ได้อีก กรุณาตรวจสอบความถูกต้องอีกครั้ง และพิมพ์แบบรายงานเพื่อลงนามจากผู้ที่มีอำนาจพร้อมประทับตราบริษัท แล้วจัดส่งแบบรายงานมาพร้อมกับเอกสารอื่นๆที่เกี่ยวข้องมาที่กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ภายในวันที่ 31 มกราคม ')){
                                                            
                                                        //form.action = 'report_org_list.php';
                                                        //form.target = '_blank';
                                                        
                                                        document.getElementById('is_submitted').value = 1;
                                                        form.submit();
                                                    }else{                                            
                                                        return false;                                              
                                                        
                                                    }
                                                }
                                                
                                                
                                            </script>
                                      
                                      
                                      <?php } ?>
                                      
                                      <input name="CID" type="hidden" value="<?php echo $this_id; ?>" />
                                      <input name="LID" type="hidden" value="<?php echo $lawful_values["LID"]; ?>" />
                                      
                                      
                                      
                                        
                                        
                                  </div></td>
                                </tr>
                          </form>
                          
                          
                            <?php if($lawful_row[close_case_by] && !$case_closed){?>
                                  <tr>
                                    <td colspan="2" align="center">
                                    
                                            <div align="center">
                                                     
                                            <hr />   
                                            <table>
                                                <tr>
                                                    <td>
                                                        <font color="#336600">
                                                         การปฏิบัติตามกฏหมายนี้เคยมีการปิดงานไปแล้ว
                                                         </font>
                                                         <table border="0" style="margin: 5px 0 15px 30px;">
                                                                                 
                                                           <tr>
                                                              <td bgcolor="#fcfcfc" style="padding-right:20px;">ปิดงานโดย</td>
                                                              <td>
                                                              <?php echo getUserName($lawful_row[close_case_by]);?> 
                                                              (<?php echo str_replace("-----","",$lawful_row[close_case_ip])?>)</td>
                                                            </tr>
                                                            <tr>
                                                              <td bgcolor="#fcfcfc" style="padding-right:20px;">วันที่ปิดงาน</td>
                                                              <td>
                                                              <?php echo formatDateThai($lawful_row[close_case_date],1,1);?> 
                                                              </td>
                                                            </tr>
                                                                                                   
                                                                                        
                                                         </table>
                                                            
                                                            
                                                    </td>
                                                    
                                                   
                                                    <td>
                                                    <font color="#336600">
                                                          การปฏิบัติตามกฏหมายนี้ถูกเปิดงานอีกครั้งโดย
                                                          </font>
                                                         <table border="0" style="margin: 5px 0 15px 30px;">
                                                                                 
                                                           <tr>
                                                              <td bgcolor="#fcfcfc" style="padding-right:20px;">เปิดงานโดย</td>
                                                              <td>
                                                              <?php echo getUserName($lawful_row[reopen_case_by]);?> 
                                                              (<?php echo str_replace("-----","",$lawful_row[reopen_case_ip])?>)</td>
                                                            </tr>
                                                            <tr>
                                                              <td bgcolor="#fcfcfc" style="padding-right:20px;">วันที่เปิดงาน</td>
                                                              <td>
                                                              <?php echo formatDateThai($lawful_row[reopen_case_date],1,1);?> 
                                                              </td>
                                                            </tr>
                                                                                                   
                                                                                        
                                                         </table>
                                                            
                                                            
                                                    </td>
                                                </tr>
                                            </table>
                                            
                                            </div>
                                                 
                                    </td>
                                 </tr>
                          <?php }?>
                          
                          
                           <?php						   
						   
						   	 //yoes 20160104 -- do a close case function
							 //allow close case for non-company users and lawfulness = 1 and case in not closed
						   	 if($sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && $lawful_row["LawfulStatus"] == 1 && !$case_closed){ 
							 
							 ?>
                                    <tr>
                                          <td colspan="2">
                                          
                                          <hr />
                                          <div align="center">
                                          
                                          		สถานประกอบการได้ทำการ<font color="green">ปฏิบัติตามกฏหมายครบถ้วนแล้ว</font>
                                              
                                                
                                               
                                               
                                          		<?php if($invalid_33_35){?>
                                                
                                                	
                                                     <div align="center">
                                                    <font color="#FF6600">ไม่อนุญาตให้ปิดงาน เนื่องจากมีข้อมูลซ้ำซ้อนในมาตรา 33 หรือ มาตรา 35</font> - กรุณาทำการตรวจสอบข้อมูลก่อนปิดงาน
                                                    </div>
                                                    
                                                
                                                <?php } ?>
                                                
                                                <?php 
																										
													//yoes 20160402 - also check for dummy records
													
													$dummy_record_check = 0;
													
													
													$sql = "
														select 
															count(*) 
														from 
															lawful_employees 
														where
															le_cid = '".doCleanOutput($output_values["CID"])."'
															and
															le_year = '".$this_year."'
															and
															le_is_dummy_row = 1
														";
														
													$dummy_record_check += getFirstItem($sql);
													
													
													$sql = "
														select 
															count(*) 
														from 
															curator 
														where
															curator_lid = '".$lawful_values["LID"]."'
															and
															curator_is_dummy_row = 1
														";
														
													$dummy_record_check += getFirstItem($sql);	
													
													//echo "..." . $dummy_record_check;
												?>
                                                
                                                
                                                <?php if($dummy_record_check){?>
                                                
                                                	
                                                     <div align="center">
                                                    <font color="#FF6600">ไม่อนุญาตให้ปิดงาน เนื่องจากรายละเอียด ม.33 และ ม.35 ยังมีข้อมูลชั่วคราว จากเมนู ส่งข้อมูลชำระเงิน</font>
                                                    <br />- กรุณาทำการตรวจสอบข้อมูลก่อนปิดงาน
                                                    </div>
                                                    
                                                    
                                                
                                                <?php } ?>
												
												
												<?php if(!$invalid_33_35 && !$dummy_record_check

												     && 1==0 //yoes 20241127 -- disable this for good
												){ ?>
                                                
                                                  <br />คลิกที่ที่นี่เพื่อทำการปิดงาน เพื่อไม่ให้มีการเปลี่ยนแปลงข้อมูลการปฏิบัติตามกฏหมายอีก
													<form method="post" action="./scrp_do_close_case.php" >
											   
												
														<input name="" type="submit" value="ปิดงาน" onclick="return confirm('คุณต้องการปิดงานไม่ให้มีการเปลี่ยนแปลงข้อมูลการปฎิบัติตามกฏหมายได้?');" />
																						
														<input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
														<input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
														<input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
																										 
													
													</form>
												
												
                                                <?php }elseif( 1==0 /*yoes 20241127 -- disable this for good*/){?>
                                                
                                                	 <input name="" type="button" value="ปิดงาน" disabled="disabled" />
                                                
                                                <?php }?>
                                          </div>
                                         </td>
                                    </tr>
                          
                                  <?php }?>
                                  
                                  
                                  
                                
                                 <?php
						   
						   
						   	 //yoes 20160104 -- do a close case function
							 //allow close case for non-company users and lawfulness = 1 and case in not closed
						   	 if($sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && $case_closed ){ 
							 
							 ?>
                                    <tr>
                                          <td colspan="2">
                                          
                                          <hr />
                                          <div align="center">
                                          
                                          		สถานประกอบการได้ทำการ<font color="green">ปฏิบัติตามกฏหมายครบถ้วนแล้ว และมีการทำปิดงานไปแล้ว</font>
                                                <br />ถ้าต้องการแก้ไขข้อมูลปฏิบัติตามกฏหมาย กรุณาติดต่อเจ้าหน้าที่ที่มีสิทธิในการเปิดงาน เพื่อทำการเปิดงานอีกครั้ง
                                                
                                                
                                                <table border="0" style="margin: 5px 0 15px 30px;">
                                                                         
                                                   <tr>
                                                      <td bgcolor="#fcfcfc" style="padding-right:20px;">ปิดงานโดย</td>
                                                      <td>
													  <?php echo getUserName($lawful_row[close_case_by]);?> 
                                                      (<?php echo str_replace("-----","",$lawful_row[close_case_ip])?>)</td>
                                                    </tr>
                                                    <tr>
                                                      <td bgcolor="#fcfcfc" style="padding-right:20px;">วันที่ปิดงาน</td>
                                                      <td>
                                                      <?php echo formatDateThai($lawful_row[close_case_date],1,1);?> 
                                                      </td>
                                                    </tr>
                                                                                           
                                                                                
                                             	 </table>
                                          
                                          
                                            	<form method="post" action="./scrp_do_open_case.php" >
                                           
                                           			<?php if($sess_accesslevel != 1){ //non-admin need password to re-open case?>
                                            		รหัสในการเปิดงาน
                                                    <input type="password" name="reopen_case_password" style="width: 100px;" required="required"/>
                                                    <?php }?>
                                                    
                                                    
                                                    <input name="" type="submit" value="เปิดงาน" onclick="return confirm('การปฏิบัติตามกฏหมายนี้เคยถูกปิดงานไปไม่ให้มีการเปลี่ยนแปลงข้อมูลแล้ว คุณต้องการเปิดงานให้เจ้าหน้าที่เข้ามาเปลี่ยนแปลงข้อมูลการปฎิบัติตามกฏหมายได้?');" />
                                                                                    
                                                    <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                                    <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                                    <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                                   
                                                
                                                </form>
                                                
                                                <?php if($_GET[notreopen]){?>
                                                <script>
													alert("รหัสในการเปิดงานไม่ถูกต้อง โปรดลองอีกครั้ง");
												</script>
                                                <?php }?>
                                                
                                                <hr />
                                                
                                                <form method="post" action="org_report.php" target="_blank" >
                                           
                                           			
                                                    
                                                    
                                                    <input name="" type="submit" value="พิมพ์เอกสารรายงานผลการปฏิบัติตามกฎหมายของสถานประกอบการ"  />
                                                                                    
                                                    <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                                    <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                                    <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                                    
                                                    
                                                    <input type="hidden" name="the_ratio" value="<?php echo $ratio_to_use; ?>"/>
                                                    <input type="hidden" name="the_33" value="<?php echo $hire_numofemp; ?>"/>
                                                    <input type="hidden" name="the_35" value="<?php echo $curator_user; ?>"/>
                                                   
                                                
                                                </form>
                                                
                                                
                                                
                                          </div>
                                         </td>
                                    </tr>
                          
                                  <?php }?>
                                  
								  
								  
								  
								  <?php						   
						   
						   	 //yoes 20181024
							 //allow ส่งฟ้อง for un-lawfulness
																						
							//echo "courted_flag: ".$courted_flag;
							
							//yoes 20231019 -- ตจว ยังไม่ให้ส่งคดีฯ
							
						   	 if($sess_accesslevel != 4 && $sess_accesslevel != 3 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && $lawful_row["LawfulStatus"] != 1 && !$case_closed && !$courted_flag){ 
							 
							 ?>
                                    <tr>
                                          <td colspan="2">
                                          
                                          <hr />
                                          <div align="center">
										  <?php 
												
												//bank fix principal , interest 20230206
												
													$m33_principal_row = getFirstRow("

																	select
																		sum(p_amount) as the_principals
																		, sum(p_interests) as the_interests
																		, sum(p_pending_amount) as the_pending_principals
																		, sum(p_pending_interests) as the_pending_interests
																	from
																		lawful_33_principals
																	where
																		p_lid = '".$lawful_values["LID"]."'

																");
												$m35_principal_row = getFirstRow("
												
																	select
																		sum(p_amount) as the_principals
																		, sum(p_interests) as the_interests
																		, sum(p_pending_amount) as the_pending_principals
																		, sum(p_pending_interests) as the_pending_interests						
																	from
																		lawful_35_principals
																	where
																		p_lid = '".$lawful_values["LID"]."'

																");
											
												$m33_35_34_prinical = $max_pay_34_row[p_amount] + $m33_principal_row[the_principals] + $m35_principal_row[the_principals];
												$m33_35_34_interests = $pending_interest_pay_34 + $m33_principal_row[the_interests] + $m35_principal_row[the_interests];
												//$total = $m33_35_34_prinical + $m33_35_34_interests;
												
												
												?>
                                          			
												<font color="red">ส่งข้อมูลดำเนินคดีตามกฏหมาย</font>  สถานประกอบการที่ยังไม่ปฏิบัติตามกฏหมาย
												<br>ข้อมูลสถานประกอบการที่ได้ทำการส่งข้อมูลดำเนินคดีตามกฏหมายจะแสดงให้ผู้ใช้งานกลุ่มงานคดีแก้ไขได้เท่านั้น
												
                                                
                                                 
													<form method="post" action="./scrp_do_court_case.php" >
											   
												
												
														เลขที่หนังสือนำ <input type=text name="lead_book_no" required>
														ลงวันที่ <input type=date name="lead_book_date" required>
														<br>
																												
														<?php //bank 20221223 add check box urgent case ?>
														
														<input id="urgent_check" name="urgent_check" type="checkbox" value="1" onclick="validateUrgentCase(), toggleUrgentRemarks()"/>
														case ด่วนที่สุด
														<br>
														<!-- 20240820 Tor คดี ข้อ 4 2567-->
														<div id="remarks_container" style="display: none;">
															หมายเหตุ <textarea id="the_remarks" name="the_remarks" rows="4" cols="50"></textarea>

														</div>
														<br>
														
														<input 
															name="" type="submit" value="ส่งข้อมูลดำเนินคดีตามกฏหมาย" onclick="return confirm('คุณต้องการทำการส่งข้อมูลดำเนินคดีตามกฏหมาย? ข้อมูลสถานประกอบการที่ได้ทำการส่งข้อมูลดำเนินคดีตามกฏหมายจะแสดงให้ผู้ใช้งานกลุ่มงานคดีแก้ไขได้เท่านั้น');"
															style="color: red;"

														/>
																						
														<input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
														<input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
														<input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
														
														<?php //bank fix principal , interest 20230206 ?>

														<input type="hidden" name="the_principal" value="<?php echo $m33_35_34_prinical; ?>"/>
														<input type="hidden" name="the_interests" value="<?php echo $m33_35_34_interests; ?>"/>

														<!--input type="hidden" name="the_principal" value="<?php //echo $owned_money; ?>"/-->
														<!--input type="hidden" name="the_interests" value="<?php //echo $interest_money; ?>"/-->
																										 
													
													</form>


													<!-- 20240820 Tor คดี ข้อ 4 2567-->
                                                    <script>
                                                    function toggleUrgentRemarks() {
                                                        var checkbox = document.getElementById('urgent_check');
                                                        var remarksContainer = document.getElementById('remarks_container');
                                                        var remarksTextarea = document.getElementById('the_remarks');

                                                        if (checkbox.checked) {
                                                            remarksContainer.style.display = 'block';
                                                        } else {
                                                            remarksContainer.style.display = 'none';
                                                            remarksTextarea.value = '';


                                                        }
                                                    }
                                                    </script>
												
                                                
                                          </div>
                                         </td>
                                    </tr>
                          
                            <?php }?>
                                  
								  
							
							
							<?php 
							
							//yoes 20201125
							//also show details if rejected
							
							//echo $lawful_values["LID"];
							
							$is_rejected_court = getFirstItem("select 
																	count(*) 
																from 
																	lawfulness_meta 
																where 
																	meta_lid = '".$lawful_values["LID"]."'
																	and
																	`meta_for` LIKE '%courted_flag-reject-%'
																	");
																	
							//echo $is_rejected_court;
							
							
							if($courted_flag){?>
								
								
								<tr>
                                          <td colspan="2">
                                          
                                          <hr />
                                          <div align="center">
                                          
                                          		
												
												มีการ <font color="red">ทำการส่งข้อมูลดำเนินคดีตามกฏหมาย</font>  สถานประกอบการที่ยังไม่ปฏิบัติตามกฏหมายนี้แล้ว
												
												<table border="0" style="margin: 5px 0 15px 30px;">
                                                                         
                                                   <tr>
                                                      <td bgcolor="#fcfcfc" style="padding-right:20px;">ทำการส่งข้อมูลดำเนินคดีตามกฏหมายโดย</td>
                                                      <td>
													  <?php echo getUserName(getLawfulnessMeta($lawful_row["LID"],'courted_by'));?> 
                                                      (<?php echo str_replace("-----","",getLawfulnessMeta($lawful_row["LID"],'courted_ip'))?>)</td>
                                                    </tr>
                                                    <tr>
                                                      <td bgcolor="#fcfcfc" style="padding-right:20px;">วันที่ส่งข้อมูลดำเนินคดีตามกฏหมาย</td>
                                                      <td>
                                                      <?php echo formatDateThai(getLawfulnessMeta($lawful_row["LID"],'courted_date'),1,1);?> 
                                                      </td>
                                                    </tr>
                                                    <!-- 20240820 Tor คดี ข้อ 4 2567-->
													<tr>
                                                      <td bgcolor="#fcfcfc" style="padding-right:20px;">หมายเหตุ (กรณีด่วนที่สุด)</td>
                                                      <td>
                                                      <?php echo getFirstItem("select meta_value from lawfulness_meta where meta_lid = '".$lawful_row["LID"]."' and meta_for = 'courted_urgent_remarks'");?>
                                                      </td>
                                                    </tr>
                                                                                           
                                                                                
                                             	 </table>
                                                
                                          </div>
										  
										  <div align="center" >
										  
												ข้อมูลการดำเนินคดีถูกส่งไปยังระบบการติดตามและดำเนินคดีแล้ว
												<br>ในกรณีที่ต้องการ <font color=blue>ยกเลิกการส่งข้อมูลดำเนินคดีตามกฏหมาย</font> สามารถทำได้โดยแจ้งเจ้าหน้าที่ระบบการติดตามและดำเนินคดี
												<br>** สิทธิการยกเลิกการส่งข้อมูลดำเนินคดีตามกฏหมาย สามารถทำได้โดยกลุ่มงานคดีเท่านั้น 
												
											
												<?php if($sess_accesslevel == 1 && 1==0){?><form method="post" action="./scrp_do_open_court.php" >
											   
														
														<input name="" type="submit" value="ยกเลิกการส่งข้อมูลดำเนินคดีตามกฏหมาย" onclick="return confirm('ยืนยันยกเลิกการส่งข้อมูลดำเนินคดีตามกฏหมาย?');" 
														
														<?php if(!in_array($sess_accesslevel,array(1,8))){?>
														disabled=disabled
														<?php }?>
														
														/>
																						
														<input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
														<input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
														<input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
													   
													
													</form>
												<?php }?>
												
											</div>
										  
                                         </td>
                                    </tr>
								
								
							<?php
								}
                            ?> 
                          
						  
							<?php if($courted_flag || $is_rejected_court){ ?>
								<tr>
                                   <td colspan="2">
                                          
                                          
										  
										  <div align="center" id="court_info">
										  
												<?php if($courted_flag){?>
												
												<font color=purple>ได้มีการส่งข้อมูลการดำเนินคดีไปยังระบบการติดตามและดำเนินคดีแล้ว</font>
												<br>รายละเอียดข้อมูลการดำเนินคดีเป็นดังตามต่อไปนี้
												
												<?php }else{?>
												
												<font color=orangered>ระบบคดีทำการปฏิเสธข้อมูล</font> - เจ้าหน้าที่สามารถดูเหตุผลการปฏิเสธข้อมูลได้จากหมายเหตุ
												<br>รายละเอียดข้อมูลการดำเนินคดีที่ได้ส่งไปเป็นดังตามต่อไปนี้
												
												<?php }?>
												
												<table style="background-color: #f8f9f9 ;" >
													<tr>
														<td style="padding-right:20px;">
															<b>เลขที่หนังสือนำส่ง:</b> {{ lead_book_no }}
														</td>
														<td>
															<b>วันที่หนังสือนำส่ง:</b> {{ lead_book_date }}
														</td>
													</tr>
													<tr>
														<td style="padding-right:20px;">
															<b>ผู้กดรับเรื่องในระบบงานคดี:</b> {{ case_accepted_by }}
														</td>
														<td>
															<b>ผู้รับผิดชอบในระบบงานคดี:</b> {{ case_owner }}
														</td>
													</tr>
													<tr>
														<td colspan=2>
															<b>หมายเหตุจากระบบคดี:</b> {{ case_remarks }}
														</td>
														
													</tr>
												</table>
											   <?php 
							
												//bank add 20221228 new text when case done
												
												$is_done_court = getFirstItem("select 
																						count(*) 
																					from 
																						lawfulness_meta 
																					where 
																						meta_lid = '".$lawful_values["LID"]."'
																						and
																						`meta_for` LIKE '%court_%-finished-%'
																						");
																						
												
												
												if($is_done_court){?>
											  
											  
											  
													<table style="background-color: #f8f9f9 ;" >
													
													<br>
													<font color=purple>ระบบการติดตามและดำเนินคดี ได้ทำการยุติการดำเนินคดี</font>
													<br>รายละเอียดข้อมูลการยุติการดำเนินคดี เป็นดังตามต่อไปนี้ 
							
													<tr>
													<td style="padding-right:20px;">
															<b>ผู้ยุติการดำเนินคดี: </b> <?php echo getFirstItem("SELECT meta_value FROM `lawfulness_meta` WHERE meta_lid = '".$lawful_values["LID"]."' and meta_for like '%court_by-finished%'"); ?>
													</td>
													<td>
															<b>วันที่ยุติการดำเนินคดี:</b> <?php echo getFirstItem("SELECT meta_value FROM `lawfulness_meta` WHERE meta_lid = '".$lawful_values["LID"]."' and meta_for like '%court_date-finished%'"); ?>
													</td>
													</tr>
													
													
													</table>
												
												<?php } ?>
												
												
											</div>
											
											<script>
			
												var court_info = new Vue({
												  el: '#court_info',
												  data: {
														   lead_book_no: ''
														   , lead_book_date: ''
														   , case_accepted_by: ''
														   , case_owner: ''
														   , case_remarks: '---'
														 }
												   })
												   
												function doGetCaseInfo(id){
													
													//http://localhost/law_system
													//alert("ss");
													axios
														//.post("http://203.154.94.105/law_system/ajax_case_info.php", "the_id=" + id)//{ step: ""+what+"", the_id: ""+id+""})
														.post("https://law.dep.go.th/ajax_case_info.php", "the_id=" + id)//{ step: ""+what+"", the_id: ""+id+""})
														
														.then(response => {

															var mm;		
															mm = response.data;
															console.log(mm);
															
															//alert(mm.lead_book_no);															
															//run_status[what+"_status"] = mm.status;
															if(mm.case_accepted_by_name == null){
																mm.case_accepted_by_name = "ยังไม่มีคนรับเรื่อง";
															}
															if(mm.case_owner_name == null){
																mm.case_owner_name = "ยังไม่ระบุผู้รับผิดชอบ";
															}
															if(mm.case_remarks == null || mm.case_remarks == ""){
																mm.case_remarks = "---";
															}
															
															court_info["lead_book_no"] = mm.lead_book_no;
															court_info["lead_book_date"] = mm.lead_book_date;
															court_info["case_accepted_by"] = mm.case_accepted_by_name;
															court_info["case_owner"] = mm.case_owner_name;
															court_info["case_remarks"] = mm.case_remarks;
															
															
														})
													
													
												}
												
												doGetCaseInfo(<?php echo $this_id;?>);
												   
											
											</script>
											
										  
										  
                                         </td>
                                    </tr>
							<?php }?>
                          
                          
                          
                             <?php if(($this_lawful_year == 2014) && $sess_accesslevel == 4){?>
                                    <tr>
                                          <td colspan="2">
                                          <div align="center">
                                          
                                          <?php if($output_values["Province"] == 1){?>
                                             	<form method="post" action="./tcpdf/bangkok_57_pdf.php" target="_blank">
                                            <?php }else{?>
                                            	<form method="post" action="./tcpdf/province_57_pdf.php" target="_blank">
                                            <?php }?>
                                            
                                                    <input name="" type="submit" value="พิมพ์แบบฟอร์มออนไลน์" />
                                                                                    
                                                    <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                                    <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                                    <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                                    
                                                    
                                                    <input type="hidden" name="employee_to_use" value="<?php echo $employee_to_use; ?>"/>
                                                    <input type="hidden" name="final_employee" value="<?php echo $final_employee; ?>"/>
                                                    <input type="hidden" name="curator_user" value="<?php echo $curator_user; ?>"/>
                                                    
                                                    <input type="hidden" name="hire_numofemp" value="<?php echo $hire_numofemp; ?>"/>
                                                    <input type="hidden" name="extra_money" value="<?php echo $final_money; ?>"/>
                                                    
                                                    <input type="hidden" name="extra_employee" value="<?php echo $extra_employee; ?>"/>
                                                   
                                                
                                                </form>
                                          </div>
                                         </td>
                                    </tr>
                          
                                  <?php }?>
                          
                          
                          <?php if($sess_accesslevel != 4){ ?>
                      <tr>
                        <td><hr />
                        <div style="font-weight: bold; padding: 5px; background-color:#efefef">การประกาศผ่านสื่อ</div>
                        
                      				  <table border="0" >
                                        	
                                          <tr>
                                          	<td>
                                            <?php 
												//generate reciept info
												$the_sql = "select * from announcecomp where CID = '".$this_id."' order by AID desc";
												
												$the_result = mysql_query($the_sql);
												
												while($result_row = mysql_fetch_array($the_result)){
													//echo "select * from receipt where RID = '".$result_row["RID"]."'";										
													$announcement_row = getFirstRow("select * from announcement where AID = '".$result_row["AID"]."'");
												
												?>
                                               			 <div style="padding:5px">ประกาศผ่านสื่อเลขที่ <a href="view_announce.php?id=<?php echo $announcement_row["AID"]?>"><?php echo $announcement_row["GovDocNo"]?></a> <!--ครั้งที่ <?php echo $announcement_row["ANum"]?> วันที่ <?php echo formatDateThai($announcement_row["ADate"])?> ประกาศทาง <?php echo getFirstItem("select newspaper_name from newspaper where newspaper_id = '".$announcement_row["newspaper_id"]."'");?>--></div>
                                                <?php
													
													}												
												?>
		                                         
                                         	</td>
                                         </tr>
                                          
                                          <tr>
                                            
                                            <td colspan="4"><hr />
                                            
                                             <?php if($sess_accesslevel!=5){?> 
                                            <a href="org_list.php?search_id=<?php echo $this_id?>&mode=announce&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;">+ เพิ่มข้อมูลการแจ้งผ่านสื่อ</a>
                                            <?php }?>
                                            
                                            
                                            </td>
                                          </tr>
                                          
                                          <tr>
                                            
                                            <td colspan="4"><hr /></td>
                                          </tr>
                          </table>
                        
                        </td>
                      </tr>
                            <?php }// <?php if($sess_accesslevel != 4){ ?>   
                               
                  </table>
                  
                  
                     
					  <?php if(($this_lawful_year == 2016 || $this_lawful_year == 2017) && ($sess_accesslevel == 4)){ // yoes20151025 - add form จพ-xx?>
                            
                            
                             
                             <?php 
							 
							 	$print_bill_date =  $company_pay_data["PayDate"];	
								
								
								if($print_bill_date == "0000-00-00"){
									$print_bill_date = date("Y-m-d");
								}
								
								?>
                             
                             
                             <?php 
							 if($sess_userid == 1750 || $sess_userid == 1766 || $sess_userid == 1770 ){
							 //if(1==0){ //yoes 20160106 --- do not allow this for now?>
                            <div align="center">
                             <form method="post" action="./bill_payment/print_bill.php?year=<?php echo $this_lawful_year;?>&date=<?php echo $print_bill_date; //echo $company_pay_data["PayDate"]; ?>" target="_blank">
                        
                                <input name="" type="submit" value="พิมพ์ใบชำระเงิน ตามมาตรา 34" />
                                
                            
                            </form>
                            </div>
                            <?php }?>
                            
                            <hr />
                            
                            
                            
                            
                            
                            
                            <div align="center">
                            <form method="post" action="./create_pdf_4.php" target="_blank">
                        
                        
                        		<?php if($submitted_company_lawful == 0){?>
                                	<input name="" type="submit" value="Preview แบบฟอร์ม จพ. สำหรับรายงานผลการปฏิบัติตามกฎหมาย" />
                                <?php }else{?>
                                	<input name="" type="submit" value="พิมพ์แบบฟอร์ม จพ. สำหรับรายงานผลการปฏิบัติตามกฎหมาย" />
                                <?php }?>
                                                                
                                <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                
                                <input type="hidden" name="employee_to_use" value="<?php echo $employee_to_use; ?>"/>
                                <input type="hidden" name="final_employee" value="<?php echo $final_employee; ?>"/>
                                <input type="hidden" name="hire_numofemp" value="<?php echo $hire_numofemp; ?>"/>
                                <input type="hidden" name="extra_emp" value="<?php echo $extra_emp; ?>"/>
                                <input type="hidden" name="final_money" value="<?php echo $final_money; ?>"/>
                                <input type="hidden" name="curator_user" value="<?php echo $curator_user; ?>"/>
                            
                            </form>
                            </div>
                            <hr />
                            
                           
                            
                       <?php }?>
                  
                                    
                  
                  
                  
                  
                </td>
              </tr>
            </table>
            
            
            
            
            
            
            
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            <!---------------- END LAWFUL TABLE--->
            
            
            
            
            
            
            
            
            
            <!------ STARTING DUMMY TABLE ---->
            <!------ STARTING DUMMY TABLE ---->
            <!------ STARTING DUMMY TABLE ---->
            <!------ STARTING DUMMY TABLE ---->
            <!------ STARTING DUMMY TABLE ---->
            <!------ STARTING DUMMY TABLE ---->
            <!------ STARTING DUMMY TABLE ---->
            <!------ STARTING DUMMY TABLE ---->
            
            
            <?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $have_lawful_record && !$case_closed ){ //yoes 20151122 -- allow admin to add dummy lawful employees?>
            
            
	            <?php include "organization_dummy_input_table.php";?>
            
            <?php } //ends if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3){?>
            
            
            <!------ END DUMMY TABLE ---->
            <!------ END DUMMY TABLE ---->
            <!------ END DUMMY TABLE ---->
            <!------ END DUMMY TABLE ---->
            <!------ END DUMMY TABLE ---->
            <!------ END DUMMY TABLE ---->
            <!------ END DUMMY TABLE ---->
            <!------ END DUMMY TABLE ---->
            <!------ END DUMMY TABLE ---->
            
            
            
            
            
            
            
            
            
            
            
            <?php 
			
			if($count_info && $mode != "new"){ //only show this if info sumbitted			
			//if(1==1){
			
			?>
            
            <table style=" padding:10px 0 0px 0; " id="input">
                 	 <tr>
                        <td>
                        	<div style="font-weight: bold; padding:0 0 5px 0;">
                            
                            	
                                ข้อมูลจากสถานประกอบการ
                            
                            
                            </div>
                            
                            </td>
                      </tr>
                      
                      
                      <tr>
                            <td>
                            
                            <form action="?id=<?php echo $this_id;?>&focus=input" method="post">
                            <table>
                            	<tr>
                                  <td colspan="2">ข้อมูลประจำปี
                                  <?php
                              		
									//print_r($_POST);
							  		include "ddl_year_auto_submit_lawful.php";
							  
							  	?>
                              	</td>
                                </tr>
                            </table>
                          </form>
                            
                            
                            </td>
                      </tr>
                      
                      
                      
                      
                      <tr>
                      	<td>
                        
                        	<div id="branch_info_company" align="center" >
									<?php 
										
										//yoes 20151021
										//branch info on Lawfulness page
										//mark this value so it shows data from company input
										$show_company_employees_company = 1;
										$show_comparison = 1;
										include "organization_branch_table.php";
										
										?>
                                </div>
                               
                        
                        
                        	<div align="center">
                        	 <table border="0" style="margin: 5px 0 15px 30px;">
                             
                             
                             
                               <tr>                               
                               
                               
                                
                                  <td bgcolor="#fcfcfc" style="padding-right: 10px;">จำนวน<?php echo $the_employees_word;?> ณ วันที่ 1 ตค. <?php echo $this_lawful_year+543-1;?>: </td>
                                  <td>
                                  
                                  <?php 
                                  
								  
									//yoes 20151021
									//change this to company from all branches
									/*$company_employees = getFirstItem("select 
															Employees 
														from 
															lawfulness_company 
														where 
															LID = '" . $lawful_values["LID"] . "'");*/
															
									
									$company_employees = $sum_employees;
															
															
													
									//if have value then override it		
									if($company_employees){
										$lawful_values["Employees"] = $company_employees;
									}
														
                                  
								  
								  //yoes 20151201 -- stop this place holder thing
								  $employee_to_use = $lawful_values["Employees"];
								  
								  
                                   /* if($lawful_values["Employees"]){
                                        //have lawful value, use lawful value
                                        $employee_to_use = $lawful_values["Employees"];
                                        
                                    }else{
                                    
                                        //didn't have lawful value, use ORG's value
                                        if($sum_employees){
                                            //if have branch, use employees from all branch
                                            $employee_to_use = $sum_employees;
                                            
                                        }else{
                                        
                                            //else, just use ORG's employees
                                            $employee_to_use = $output_values["Employees"];
                                        
                                        }
                                    }*/
                                    
                                    ?>
                                  
                                 <strong><?php echo formatEmployee($employee_to_use);?></strong>  คน
                                   
                                  </td>
                                </tr>
                                
                                <tr>
                                  <td bgcolor="#fcfcfc" style="padding-right:20px;">อัตราส่วน<?php echo $the_employees_word;?>ต่อคนพิการ: </td>
                                  <td><?php 
                                  
                                    //what ratio to use?
                                    $ratio_to_use = default_value(getFirstItem("select var_value 
                                                        from vars where var_name = 'ratio_$this_lawful_year'"),100);
                                    
                                    //$half_ratio_to_use = $ratio_to_use/2;
                                    
                                    echo ($ratio_to_use);
                                    
                                  
                                  ?>:1 = <strong id="employee_ratio"><?php 
                                    //if employee > 200
                                    
                                    
                                    
                                    $final_employee = getEmployeeRatio($employee_to_use,$ratio_to_use);
                                    
                                    echo formatEmployee($final_employee);
                                    
                                    ?></strong> คน</td>
                                </tr>
                                
                                
                                                                
                              </table>
                              
                        	</div>
                        
                        
                        </td>
                     </tr>
					 
					 
					 
					 
					 <tr>
                    	<td>
                             <hr />
                             <span style="font-weight: bold;color:#006600;">
                                             
                                             	ที่อยู่ในการติดต่อ
                                           
                                            </span>
                       </td>                
					</tr>   
					
					
					
					<tr>
                    	<td>

							<?php
							// DANG
							$sql = "SELECT *
									FROM company_company c 
									left join provinces p on c.Province = p.province_id
									WHERE CID='$this_id' AND year='$this_lawful_year'";
							$output_values = getFirstRow($sql);

							 ?>
							 <div id="tb_contact_address">
							 <table border="0">
					
								<tr>
												<td>สถานที่ตั้งเลขที: </td>
												<td>
													<b><?php echo $output_values["Address1"];?></b>
												</td>
												<td class="td_left_pad">ซอย: </td>
												<td>
													<b><?php echo $output_values["Soi"];?></b>
												</td>
										  </tr>


										  <tr>
											<td>หมู่:</td>
											<td>
												<b><?php echo $output_values["Moo"];?></b>
											</td>
											<td class="td_left_pad"> ถนน:</td>
											<td>
												<b><?php echo $output_values["Road"];?></b>
											</td>
										  </tr>
										  <tr>
											<td>ตำบล/แขวง: </td>
											<td>
												<b><?php echo $output_values["Subdistrict"];?></b>
											</td>
											<td class="td_left_pad"> อำเภอ/เขต:</td>
											<td>
												<b><?php echo $output_values["District"];?></b>
											</td>
										  </tr>
										  <tr>
											<td>จังหวัด: </td>
											<td> 
												<b><?php echo 
												
												//yoes 20200401
												//หน้า ejob ขาดจัวหวัดค่ะ
												//https://app.asana.com/0/794303922168293/1169293249450220
												$output_values["province_name"];?></b>
											</td>
											<td class="td_left_pad"> รหัสไปรษณีย์:</td>
											<td>
												<b><?php echo $output_values["Zip"];?></b>
											</td>
										  </tr>

											 
											  
								</table>
                       </td>                
					</tr>   
					
					
					
					
					
					
                     
                     <tr>
                    	<td>
                             <hr />
                             <span style="font-weight: bold;color:#006600;">
                                             
                                             	มาตรา 33 จ้างคนพิการเข้าทำงาน
                                           
                                            </span>
                       </td>                
					</tr>                       
                    
                     <tr>
                    	<td>
                        	
                            
                            	 <table border="0">
                                          
                                            
                                           
                                              <tr>
                                              <td>จำนวนคนพิการที่ทำงานในปัจจุบัน</td>
                                              <td>
                                              
                                              <?php 
											  
											  
													
												$hire_numofemp = getFirstItem("
													SELECT 
														count(*)
													FROM 
														lawful_employees_company
													where
														le_cid = '$this_id'
														and le_year = '$this_lawful_year'");/* */
													
												
												
												
												if($hire_numofemp == 0 && $sess_accesslevel != 4){
												
													//no "real" value, use thos that is in lawfulness instead
													$hire_numofemp = $lawful_values["Hire_NumofEmp"];
													
												}
												
											   ?>
                                              
                                               <strong><?php echo default_value($hire_numofemp,0);?></strong>
                                              
                                              
                                             
                                              
                                              
                                              คน 
                                              
                                              
                                            
                                              
                                              </td>
                                            </tr>
                                            
                                            
                                            <tr>
                                              <td>ผู้พิการใช้สิทธิมาตรา 35</td>
                                              <td>
                                              
                                              
                                              <?php
											  	
													
													//curator user are person OR disabled person who is the "top" level
													
													//$curator_table_name is from file "scrp_add_curator"
													
													
													$curator_user = getFirstItem("select 
															count(*) 
														from 
															curator_company 
															
														where 
														
															curator_lid = '".$lawful_values["LID"]."'
																
																
															and 
															
															curator_parent = 0
															
															
															");	
													
													
													
											  ?>
                                              <strong><?php echo $curator_user;?></strong>
                                              
                                               
                                              คน
                                            </tr>
                                            
                                            
                                             <tr>
                                              <td>ต้องจ่ายเงินแทนการรับคนพิการ</td>
                                              <td>
                                              
                                              <b>
                                              <?php 
											  
											  	$extra_emp = $final_employee - $hire_numofemp - $curator_user;
												
												
												
												if($extra_emp < 0){
													$extra_emp = 0;
												}
												
											    echo formatEmployee(default_value($extra_emp,"0"));
											  ?>
                                              </b>
                                                                                          
                                             
                                              คน
                                            </tr>
                                            
                                            
                                            
                                            
                                            
                                            
                                            </table>
                                            
                        
                        
                        
                        
                        			<!------------ DETAILS TABLE -------------->
									
									<!-- table for "new 33" -->
									<?php //yoes 20220628 ?>
									<?php if(
									
											($sess_accesslevel == 1 || $sess_accesslevel == 2 || 1==1)
											
											//&& ($this_cid != 1289 && $this_lawful_year != 2022)
											
											){ // yoes 20220628 - yoes 20220701 -- open this to all ?>
									<div align=center id="myTable_ejob_div">
											<!--<font color=blue><-- ตาราง ejob ใหม่ 29 มิย 65 - สำหรับเจ้าหน้าที่ admin และส่วนกลางเห็นเท่านั้น-></font>-->
								
											<?php
												
												$get_org_sql = "
										
														SELECT 
															a.*
															
															, b.meta_leid as child_meta_leid
															, b.meta_for as child_meta_for
															, b.meta_value as child_meta_value
															
															, c.meta_leid as parent_meta_leid
															, c.meta_for as parent_meta_for
															, c.meta_value as parent_meta_value
															
															, d.meta_for as sso_failed
														FROM 
														
															lawful_employees_company a
																left join
																	lawful_employees_meta b
																		on a.le_id = b.meta_leid and b.meta_for = 'child_of-es'
																left join
																	lawful_employees_meta c
																		on a.le_id = c.meta_value and c.meta_for = 'child_of-es'
																		
																left join
																	lawful_employees_meta d
																		on a.le_id = d.meta_leid and d.meta_for = 'sso_failed'
														
														
														where
															
															(
																le_cid = '$this_id'
																and 
																le_year = '$this_lawful_year'
															)
																															
															$search_filter
															
														order by 
															le_id asc
															
														";
														
													$org_result = array();
													array_push($org_result,mysql_query($get_org_sql));
													
													
													$post_row_parent_array = array();										
													$post_row_child_array = array();
													$post_row_array = array();	
													
													
													
													for($result_count = 0; $result_count < count($org_result); $result_count++){
													
														while ($post_row = mysql_fetch_array($org_result[$result_count])) {
																									
															//for parent -> push to parent
															if(!$post_row['child_meta_value']){
																array_push($post_row_parent_array,$post_row);
															}else{
																
																//for child -> push to child
																$post_row_child_array[$post_row['child_meta_leid']] = $post_row;
															
															}
															
															
																									
														
														} //end while $post row
													}//end for result count 
													
													
													for($result_count = 0; $result_count < count($post_row_parent_array); $result_count++){
										
														$group_count++; //group count for painting colors
														$post_row_parent_array[$result_count]['group_count'] = $group_count;
														array_push($post_row_array,$post_row_parent_array[$result_count]);
														
														$this_child = $post_row_parent_array[$result_count]['parent_meta_leid'];
														while($this_child){
															
															$post_row_child_array[$this_child]['group_count'] = $group_count;
															array_push($post_row_array,$post_row_child_array[$this_child]);
															$this_child = $post_row_child_array[$this_child]['parent_meta_leid'];
															
														}
														
													}
													
													$total_records = 1;
												?>
												
												<table bgcolor="#FFFFFF" width="1000" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;   ">    
													<thead>
														<tr bgcolor="#efefef">
														  <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
														  <td><div align="center">ชื่อ</div></td>
														  <td><div align="center">เพศ</div></td>

														  <td><div align="center">อายุ</div></td>
														  <td><div align="center">เลขที่บัตรประชาชน</div></td>
														  <td width="140px"><div align="center">ลักษณะความพิการ</div></td>

														  <td><div align="center">เริ่มบรรจุงาน </div></td>
														  <td><div align="center">ค่าจ้าง </div></td>
														  <td ><div align="center">ตำแหน่งงาน</div></td>
														  <td ><div align="center">การศึกษา</div></td>
														  <td ><div align="center">ไฟล์แนบ</div></td>
														  
														</tr>
													</thead>
													<tbody>

												<?php
												
												for($result_count = 0; $result_count < count($post_row_array); $result_count++){
													
														$post_row = $post_row_array[$result_count];
														
														?>
															<tr>
																
																<td colspan="13" id="td_ejob33_<?php echo $post_row[le_id]?>">
																	<div align="center">...* กำลังดึงข้อมูล <?php echo $post_row[le_id]?> *...</div>
																	
																	<?php // echo print_r($post_row);?>
																	
																</td>
																
															</tr>
															
														<?php
														
														$post_row[this_id] = $this_id;
														$post_row[this_lawful_year] = $this_lawful_year;
														$post_row[this_lid] = $this_lid;
														$post_row[is_ejob] = 1;
														
														$ejob_33_vue_command .= " getLeTds_ejob($post_row[le_id],".json_encode($post_row).");";
														
														echo "<script>";
														//echo " getLeTds_ejob($post_row[le_id],".json_encode($post_row).");";
														
														echo "</script>";
														
														$total_records++;
													
													}
												
												?>
												
												</tbody>
											</table><!-- ends myTable-->
												
								
									<!--<font color=blue><-- ตาราง ejob ใหม่ 29 มิย 65 - สำหรับเจ้าหน้าที่ admin และส่วนกลางเห็นเท่านั้น-></font>-->
									</div><!-- ends myTable_div -->
									
									
									<script>
									
										<?php echo $ejob_33_vue_command;?>
										
										function getLeTds_ejob(id, json){
								
											/**/$.ajax({
											  method: "POST",
											  url: "https://job.dep.go.th/organization_33_detailed_rows_modal.php",
											  data: json
											})
											  .done(function( html ) {																
												
												$("#td_ejob33_"+id).parent().replaceWith(html);

											  });
											  
											  //alert("1");
											
											//my_popup["content_"+id] = "<tr><td>"+id+"</td></tr>";

										}
									
										
									
									</script>
									
									<?php }?>

									
									<?php if(1==0){ // yoes 20220701 ends if 1==0?>
                        			 <table>
                                         <tr>
                                            <td bgcolor="#efefef" colspan="9">
                                            <strong>ข้อมูลคนพิการที่ได้รับเข้าทำงาน</strong>
                                            </td>
                                        </tr>
                                            
                                             <?php
                        
                            
                                            
                                                
                                                $get_org_sql = "SELECT *
                                                                FROM 
                                                                
                                                                lawful_employees_company
                                                                
                                                                where
                                                                    le_cid = '$this_id'
                                                                    and le_year = '$this_lawful_year'
                                                                order by le_id asc
                                                                ";
                                                
                                            
                                            
                                            //echo $get_org_sql;
                                            $org_result = mysql_query($get_org_sql);
                                            $total_records = 1;
                                            while ($post_row = mysql_fetch_array($org_result)) {
                                        
                                                if($total_records == 1){
                                                ?>
                                                
                                                <tr bgcolor="#efefef">
                                                  <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                                                  <td><div align="center">ชื่อ</div></td>
                                                  <td><div align="center">เพศ</div></td>
                                                  <td><div align="center">อายุ</div></td>
                                                  <td><div align="center">เลขที่บัตรประชาชน</div></td>
                                                  <td width="140px"><div align="center">ลักษณะความพิการ</div></td>
                                                  <td><div align="center">เริ่มบรรจุงาน </div></td>
                                                  <td><div align="center">ค่าจ้าง </div></td>
                                                  <td ><div align="center">ตำแหน่งงาน</div></td>
                                                  <td ><div align="center">การศึกษา</div></td>
                                                  <td ><div align="center">ไฟล์แนบ</div></td>
												   <td ><div align="center">หมายเหตุ</div></td>
                                                 
                                                </tr>
                                                
                                                <?php
                                                
                                                }											
                                            
                                            ?>     
                                        <tr>
                                          <td valign="top"><div align="center"><?php echo $total_records;?></div></td>
                                          <td valign="top"><?php 
										  
										  
											echo doCleanOutput($post_row["le_name"]);
											
											
											//yoes 20190212 -- ทำงานแทนโดยใครอะไรยังไงหรือไม่
											//
											$get_parent_sql = "
												
												select
													le_name
												from
													lawful_employees_company
												where
													le_id in (
												
														select
															meta_value
														from
															lawful_employees_meta
														where
															meta_for = 'child_of-es'
															and
															meta_leid = '".$post_row["le_id"]."'
												
														)
											
											
											";
											//echo $get_child_sql;
											
											$parent_name = getFirstItem($get_parent_sql);
											
											if($parent_name){											
												echo "<br><font color='blue'>(ทำงานแทน ".$parent_name." )</font>";											
											}
											
											
											//
											$get_child_sql = "
												
												select
													le_name
												from
													lawful_employees_company
												where
													le_id in (
												
														select
															meta_leid
														from
															lawful_employees_meta
														where
															meta_for = 'child_of-es'
															and
															meta_value = '".$post_row["le_id"]."'
												
														)
											
											
											";
											//echo $get_child_sql;
											
											$child_name = getFirstItem($get_child_sql);
											
											if($child_name){											
												echo "<br><font color='#ff00ff'>(ทำงานแทนโดย ".$child_name." )</font>";											
											}
											
											?></td>
                                          <td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
                                          <td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
                                          <td valign="top">
                                          <?php echo doCleanOutput($post_row["le_code"]);?>
                                          
                                             
                                          
                                          </td>
                                          <td valign="top"><?php echo doCleanOutput($post_row["le_disable_desc"]);?></td>
                                          <td valign="top">	<?php 
														
														
														echo formatDateThai($post_row["le_start_date"],0);
														
														
														
														if($post_row["le_end_date"] && $post_row["le_end_date"] != '0000-00-00'){
															echo "-".formatDateThai($post_row["le_end_date"],0);
														}
														
														
														?>
														
												<?php //yoes 20210222 
												
													//yoes 20211129 -- only show this if is a "submitted" for approval)
													if(
													
														($submitted_company_lawful == 1 || 1==1)
														
													
													){
												
												?>
												<br><span id="submitted_employees_table_<?php echo $post_row["le_id"];?>" v-html="the_text"></span>
												<script>
											 
													var submitted_employees_table_<?php echo $post_row["le_id"];?> = new Vue({
													  el: '#submitted_employees_table_<?php echo $post_row["le_id"];?>',
													  data: {
															   the_text: ''
															 }
													   });													   
													   //submitted_employees_table_<?php echo $post_row["le_id"];?>["the_text"] = "ssss";
													   axios
														.post("ajax_get_le_end_date_sso.php?le_id=<?php echo $post_row["le_id"];?>&the_cid=<?php echo $post_row["le_cid"];?>")
														.then(response => {
															submitted_employees_table_<?php echo $post_row["le_id"];?>["the_text"] = response.data;
														})
												</script>	
												<?php 
												
													//yoes 20211129 -- only show this if is a "submitted" for approval)
													}//ends if($submitted_company_lawful == 1){
												
												?>												
														
														
														
														
										  </td>
                                          
                                          <td valign="top"><div align="right">
                                          
                                          <?php echo formatNumber($post_row["le_wage"]);?>
                                          
                                          
                                          <?php echo getWageUnit($post_row["le_wage_unit"]);?>
                                          
                                          </div></td>
                                          
                                          <td valign="top"><?php echo doCleanOutput($post_row["le_position"]);?></td>
                                          <td valign="top"><?php echo formatEducationLevel(doCleanOutput($post_row["le_education"]));?></td>
                                         
                                         
                                         	 <td valign="top">
									  
														  <?php 
												
															if($post_row["job_leid"]){
																$leid_to_get_file = $post_row["job_leid"];
																$le_file_url = "https://job.dep.go.th";
															}else{												
																$leid_to_get_file = $post_row["le_id"];
																$le_file_url = "https://ejob.dep.go.th/ejob";
															}
                                                         
                                                            //yoes 20160427 -->
                                                            //also see if there are any attached files											 
                                                            $curator_file_path = mysql_query("select 
                                                                                                    * 
                                                                                               from 
                                                                                                     files 
                                                                                                where 
                                                                                                    file_for = '".$leid_to_get_file."'
                                                                                                    and
                                                                                                    (
                                                                                                    
                                                                                                        file_type = 'docfile_33_1'																						
                                                                                                        or
                                                                                                        file_type = 'docfile_33_2'
																										or
                                                                                                        file_type = 'docfile_33_71'
                                                                                                    )
                                                                                                    ");
                                                            
                                                            $file_count_33 = 0;
                                                                                        
                                                            while ($file_row = mysql_fetch_array($curator_file_path)) {
                                                            
                                                                $file_count_33++;
                                                                
                                                                if($file_count_33 > 1){echo "<br>";}
                                                            ?>
                                                                
                                                                
                                                            
                                                                <a href="<?php echo $le_file_url; ?>/hire_docfile/<?php 
																
																
																			if($post_row["job_leid"]){
																				echo str_replace("ejob","",$file_row["file_name"]);
																			}else{
																				echo $file_row["file_name"];
																			}
																		 
																		 
																		 ?>" target="_blank">
                                                                
                                                                <?php 
                                                                    if($file_row["file_type"] == "docfile_33_1"){
                                                                        echo "สำเนาสัญญาจ้าง";
                                                                        $required_doc_33_1--;
                                                                    }elseif($file_row["file_type"] == "docfile_33_2"){
                                                                        echo "สำเนาบัตรประจำตัวคนพิการ/ผู้ดูแลคนพิการ";																												
                                                                        $required_doc_33_2--;
                                                                        
                                                                    }elseif($file_row["file_type"] == "docfile_33_71"){
                                                                        echo "เอกสาร จพ7/1";																												
                                                                        $required_doc_33_2--;
                                                                        
                                                                    }else{
                                                                        echo "ไฟล์แนบ";	
                                                                    }
                                                                    
                                                                ?>
                                                                
                                                                </a>
                                                                
                                                            <?php
                                                            
                                                            
                                                            }
                                                            
                                                            
                                                          ?>
                                                      
                                                      </td>
													  
													  
													  
													  <td>
													  
														<?php
															
															//yoes 20210629
															//remarks
															//see if มันคือ record เดิม
															
															$ejob_leid_sql = "
																
																select
																	meta_leid
																from
																	lawful_employees_meta
																where
																	meta_for = 'ejob_leid'
																	and
																	meta_value = '".$post_row["le_id"]."'
															
															";
															
															
															$the_job_leid = getFirstItem($ejob_leid_sql);
															
															//echo $the_job_leid ;
															if($submitted_company_lawful == 3){
																if($the_job_leid || $post_row["job_leid"]){
																	echo "<font color=green>* เป็นการปรับปรุงข้อมูลเดิม</font>";
																}else{
																	echo "<font color=orange>* เป็นการกรอกข้อมูลใหม่</font>";

																}
															}
															
														?>													  
													  
													  </td>
													  
													  
													  
                                        
                                          
                                          
                                        </tr>
                                        <?php 
                                            $total_records++;
                                            
                                            //END LOOP TO CREATE LAWFUL EMPLOYEES
                                             
                                            }?>
                                        
                                        
                                  	 </table>
									 
									<?php } // yoes 20220701 ends if 1==0?>
                                     
                                     <table border="0">
                                             
                                            <?php 
												//yoes 20220731 --> showing this always
												if($hire_numofemp || 1==1){ //shows attachment part if have 33?>
                                          
                                           
                                            
                                            <tr >
                                              <td colspan="3">
                                              	
                                                <br />
                                                <font color="#006600">
                                                	<strong>เอกสารประกอบการรายงานฯ</strong>
                                                </font>
                                                
                                                <div id="alert_33_files" style="display:none;">
                                                   
                                                    <font color="red">กรุณาแนบ ไฟล์สำเนาสัญญาจ้าง และ สำเนาบัตรประจำตัวคนพิการ/ผู้ดูแลคนพิการ ให้ครบสำหรับคนพิการทุกคนที่ได้เข้าทำงาน</font>
	                                            </div>
                                              
                                              </td>
                                            </tr>
                                            
                                            
                                            <tr bgcolor="#fcfcfc" >
                                            <td>
                                                <img id="ex333" src="exclaim_small.jpg" title="กรุณาแนบไฟล์" style="padding: 5px;" />
                                              </td>
                                              <td>
                                              	สำเนา สปส 1-10 ส่วนที่ 1 ประจำเดือน ต.ค. <?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>
                                                <br />(พร้อมสำเนาใบเสร็จการชำระเงินของประกันสังคมเดือน ต.ค.<?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>)
                                              </td>
                                              <td>
                                              	<?php 
												
												
													//yoes 20160501 --- enabled deletion or not
													//yoes 20250103 -- ห้ามลบไฟล์แนบจาก สปก
													if($submitted_company_lawful || 1==1){
														$disable_delete = 1;
													}
                                                  
												  	
												  
												  	$required_doc++;
													
													$this_id_temp = $this_id;
						                            $this_id = $lawful_values["LID"]; 
													
												  	$file_type = "company_33_docfile_3";
                                                    include "doc_file_links_ejob.php";
                                                 
                                                ?>
                                                <?php if($have_doc_file){$required_doc--;?><br /><script>$('#ex333').hide();</script><?php }?>
                                                
												<?php if(! $submitted_company_lawful){?>
                                               		<input type="file" name="company_33_docfile_3" id="company_33_docfile_3" />
                                               <?php }?>
                                              </td>
                                            </tr>
                                             <tr >
                                             <tr bgcolor="#fcfcfc" >
                                            <td>
                                                <img id="ex334" src="exclaim_small.jpg" title="กรุณาแนบไฟล์" style="padding: 5px;" />
                                              </td>
                                              <td>
                                              	
												สำเนา สปส 1-10 ส่วนที่ 2 ที่มีชื่อคนพิการ
												
												<br>
												ประจำเดือน ม.ค.<?php echo ($this_year > 3000 ? $this_year+543-1000 : $this_year+543) ;?>
												
												<?php if($this_year == date("Y") || $this_year == date("Y")+1){?>
												ถึงเดือนปัจจุบัน
												<?php }else{?>
												ถึงเดือน ธ.ค. <?php echo $this_year+543;?>
												<?php }?>	
                                                <!--<br />(พร้อมใบเสร็จการชำระเงินของประกันสังคม ม.ค.<?php echo ($this_year > 3000 ? $this_year+543-1000 : $this_year+543) ;?>) -->
												<br>(พร้อมใบเสร็จการชำระเงินของประกันสังคม)
												
                                              </td>
                                              <td>
                                              	<?php 
												
													$required_doc++;                                                  
												  	$file_type = "company_33_docfile_4";
                                                    include "doc_file_links_ejob.php";
                                                 
                                                ?>
                                                <?php if($have_doc_file){$required_doc--;?><br /><script>$('#ex334').hide();</script><?php }?>
                                                <?php if(! $submitted_company_lawful){?>
                                               <input type="file" name="company_33_docfile_4" id="company_33_docfile_4" />
                                               <?php }?>
                                              </td>
                                            </tr>
                                             <tr bgcolor="#fcfcfc">
                                             <tr bgcolor="#fcfcfc" >
                                            <td>
                                                <img id="ex335" src="exclaim_small.jpg" title="กรุณาแนบไฟล์" style="padding: 5px;" />
                                              </td>
                                              <td>
											  <?php $year_to_show = $this_lawful_year+543; ?>
                                              หนังสือรับรองนิติบุคคล
											  <?php if($year_to_show >= 2561){ ?>
												(อายุไม่เกิน 6 เดือน)
											  <?php }else{ ?>

											  <?php } ?>
                                              </td>
                                              <td>
                                              	<?php 
                                                  
												  	$required_doc++;
												  	$file_type = "company_33_docfile_5";
                                                    include "doc_file_links_ejob.php";
                                                 
                                                ?>
                                                <?php if($have_doc_file){$required_doc--;?><br /><script>$('#ex335').hide();</script><?php }?>
                                                <?php if(! $submitted_company_lawful){?>
                                               <input type="file" name="company_33_docfile_5" id="company_33_docfile_5" />
                                               <?php }?>
                                              </td>
                                            </tr>



                                            <!-- New additional docfile_55 row for schools in 2025+ -->
                                                <?php
                                                // Check if organization is a school AND year is 2025 or later
                                                if(($output_values["CompanyTypeCode"] == "07" ||
                                                    getFirstItem("select CompanyTypeCode from company where cid = '$this_cid'") == "07" ||
                                                    getFirstItem("select meta_value from company_meta where meta_cid = '$this_cid' and meta_for = 'is_school'"))
                                                    && $this_year >= 2025){
                                                ?>
                                                    <tr bgcolor="#fcfcfc">
                                                        <td>

                                                            <img id="ex3355" src="exclaim_small.jpg" title="กรุณาแนบไฟล์" style="padding: 5px;" />
                                                        </td>
                                                        <td>
                                                            หนังสือกองทุนสงเคราะห์ครู เดือน ต.ค.
                                                            <?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1); ?>
                                                            พร้อมใบเสร็จรับเงิน
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $required_doc++;
                                                            $file_type = "company_33_docfile_55";
                                                            include "doc_file_links_ejob.php";

                                                            if($have_doc_file){$required_doc--;?>
                                                                <br />
                                                                <script>$('#ex3355').hide();</script>
                                                            <?php }?>
                                                            <?php if(!$submitted_company_lawful || $submitted_company_lawful == 3){?>
                                                                <input type="file" name="company_33_docfile_55" id="company_33_docfile_55" />
                                                            <?php }?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>





                                             <tr >
                                             <tr bgcolor="#fcfcfc" >
                                            <td>
                                               
                                              </td>
                                              <td>
                                              	หนังสือมอบอำนาจ (กรณีมีการมอบอำนาจ)
                                              </td>
                                              <td>
                                              	<?php 
                                                  
												  	$file_type = "company_33_docfile_6";
                                                    include "doc_file_links_ejob.php";
                                                 
                                                ?>
                                                <?php if(! $submitted_company_lawful){?>
                                               <input type="file" name="company_33_docfile_6" id="company_33_docfile_6" />
                                               <?php }?>
                                              </td>
                                            </tr>
                                             <tr bgcolor="#fcfcfc">
                                             <tr bgcolor="#fcfcfc" >
                                            <td>
                                                
                                              </td>
                                              <td>
                                              	อื่นๆ
                                              </td>
                                              <td>
                                              	<div style="width: 250px">
                                              	<?php                                              
													
                                                    $file_type = "company_33_docfile_7";                                                
                                                    include "doc_file_links_ejob.php";                                                    
                                                    $this_id = $this_id_temp;
													                                                    
                                                ?>
                                                <?php if(! $submitted_company_lawful){?>
                                               <input type="file" name="company_33_docfile_7" id="company_33_docfile_7" /> 
                                               <?php }?>
                                               </div>
                                               
                                              </td>
                                            </tr>
                                            
                                            <?php if(! $submitted_company_lawful){?>
                                            <tr>
                                            	<td colspan="3">
                                                
                                                 <hr />
            									<div align="center">
										            <input type="submit" value="เพิ่มเอกสารประกอบ"/>
                                                </div>
                                                
                                                </td>
                                            </tr>
                                            <?php }?>
                                            
                                       <?php }//ends if(!$hire_numofemp){ for file attachment?>
                                        
										</table>
										
										
										<table border="0">										
                                            
                                            
                                             <?php //yoes 20151118 -- user no longer wants to see this?>
                                            <tr style="display: none;">
                                              <td>ผู้พิการใช้สิทธิมาตรา 35</td>
                                              <td>
                                              
                                              
                                              <?php
											  	
													
													//curator user are person OR disabled person who is the "top" level
													
													//$curator_table_name is from file "scrp_add_curator"
													
													
													$curator_user = getFirstItem("select 
															count(*) 
														from 
															$curator_table_name 
															
														where 
														
															curator_lid = '".$lawful_values["LID"]."'
																
																
															and 
															
															curator_parent = 0
															
															
															");	
													
													
													
											  ?>
                                              <strong><?php echo $curator_user;?></strong>
                                              
                                               
                                              คน
                                            </tr>
                                            
                                           
                                            
                                            <?php //yoes 20151118 -- user no longer wants to see this?>
                                             <tr style="display:none;">
                                              <td>ต้องจ่ายเงินแทนการรับคนพิการ</td>
                                              <td>
                                              
                                              <b>
                                              <?php 
											  
											  	$extra_emp = $final_employee - $hire_numofemp - $curator_user;
												
												if($extra_emp < 0){
													$extra_emp = 0;
												}
												
											    echo formatEmployee(default_value($extra_emp,"0"));
											  ?>
                                              </b>
                                              
                                              
                                              <input name="Hire_NewEmp" type="hidden" id="Hire_NewEmp" size="10" value="<?php echo $extra_emp;?>" onChange=" addEmployeeCommas('Hire_NewEmp');" /> 
                                              คน
                                            </tr>
                                            
                                            
                                         
                                            
                                            
                                            
                                          
                                            
                                            

                                          </table>
                        
                        
                        
                        
                        </td>
                    </tr>    
                    
                    
                    
                    <!----- END ROW FOR lawful_employees----->
                    
                    
                    
                    
                    <!----- START ROW for CURATOR ----->
                    
                    <tr>
                      
                     		 <td>
                             
                             <hr />
                             
                             
                             <span style="font-weight: bold;color:#006600;">
                                             
                                             	มาตรา 35 จ้างคนพิการเข้าทำงาน
                                           
                                            </span></td>
                                            
                                            
						</tr>        
                        
                        
                        
                        
                        
                        <tr>
                        	<td>
                            
                            
                             - มีผู้ใช้สิทธิ: <strong><?php 
							
									
									//$curator_table_name is from scrp_add_curator.php
							
									$curator_user = getFirstItem("
																			
																	select count(*) 
																	from 
																	
																	curator_company
																	
																	where 
																	curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0
																	
																
																");
							
							
									echo $curator_user;
							
								?></strong> คน, ผู้พิการถูกใช้สิทธิ: <strong><?php 
							
									
									//$curator_usee is disabled person who has parents (right is used by someone else)
									//disable whose rights is not used by someone else is not $curator_usee
									
									$curator_usee = getFirstItem("
												
														select count(*) 
														from 
														curator_company
														where 
														curator_lid = '".$lawful_values["LID"]."' and curator_parent > 0
									
														");
													
									echo $curator_usee;
							
								?></strong> คน
                            
                            
                            	 <hr />
  
  
  								<?php 
								
								
								include "company_curator_table.php";
								
								
								?>
                                                       
                            
                            </td>
                       </tr>
                            
                            
                         <!--------- END ROW FOR CURATOR ---------->   
                         
                         
                         
                         
                         
                         
                         
                         
                         
                         
                         
                         
                         <?php 
				 
				 
				 //////////
				 //
				 //
				 //		20140220 - get payment detail
				 //
				 //
				 //////////
				 
				 
				 $company_payment_row = getFirstRow("
				 
											select
												*
											from
												payment_company
											where
												 CID = '$this_id'
												 and Year = '$this_lawful_year'
				 
											");
				 
				 
				 ?>
                         
                         
                         
                         
                 <!----- START ROW for PAYMENT ----->
                    
                   	 <tr <?php if(!$company_payment_row){?>style="display: none;"<?php }?> >
                      
                     		 <td>
                             
                             <hr />
                             
                             
                             <span style="font-weight: bold;color:#006600;">
                                             
                                             	มาตรา 34 ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ
                                           
                                            </span>
                                            
                                            
                             
                             
                             
                             
                             
                             
                             
                             
                             
                             
                             <!----- START PAYMENT DETAIL ----->
                             
                             
                             
                             
                             <table cellpadding="3" style="margin:0px 0 0 20px;" >
                                      
                                                <tr>
                                                  <td>วันที่ชำระเงิน</td>
                                                  <td>
                                                  
                                                  <?php echo formatDateThai($company_payment_row["PayDate"]);?>
                                                  
                                                  
                                                    </td>
                                                  <td>&nbsp;</td>
                                                  <td>&nbsp;</td>
                                                  <td></td>
                                              </tr>
                                                <tr>
                                                  <td>จำนวนเงินที่ชำระ</td>
                                                  <td colspan="3"><?Php echo formatNumber($company_payment_row["Amount"]);?>
                                                     บาท </td>
                                                  
                                              </tr>
                                              <!--<tr>
                                                  <td>ดอกเบี้ย</td>
                                                  <td colspan="3"><?Php echo formatNumber(789);?>
                                                     บาท </td>
                                                  
                                              </tr>-->
                                              
                                              <tr>
                                                  <td>จ่ายโดย</td>
                                                  <td colspan="3">
                                                  
                                                  <strong><?php 
												  
												  echo formatPaymentName($company_payment_row["PaymentMethod"]);
												  
												  ?></strong>
                                                  
                                                  </td>
                                                  
                                              </tr>
                                              
                                              
                                              
                                              
                                              <?php if($company_payment_row["PaymentMethod"] == "Cheque"){?>
                                              
                                              
                                                    <tr>
                                                                <td colspan="4">
                                                              
                                                                <table id="cheque_table" border="0" style="padding:0px 0 0 50px;"  >
                                                                  <tr>
                                                                    <td><span style="font-weight: bold">ข้อมูลการจ่ายเช็ค</span></td>
                                                                    <td>&nbsp;</td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">ธนาคาร</span></td>
                                                                    <td><?php echo getFirstItem("select bank_name from bank where bank_id = '".$company_payment_row["bank_id"]."'");?>                                       </td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่เช็ค</span></td>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                                                      <?php echo $company_payment_row["RefNo"]?>
                                                                    </span></td>
                                                                  </tr>
                                                                  
																   <?php //yoes 20200311
																  //as per https://app.asana.com/0/794303922168293/1165739389391433 -> hide this for now
																  if(1==0){
																  ?>
                                                                  <tr>
                                                                    <td>ลงวันที่</td>
                                                                    <td>
                                                                    <?php echo formatDateThai($company_payment_row["PaymentDate"]);?>
                                                                    </td>
                                                                    
                                                                  </tr>
																  <?php }?>
                                                                </table>
                                                                
                                                                
                                                              
                                                              
                                                              </td>
                                                          </tr>
                                              
                                              
                                              
                                              
                                              
                                              <?php }?>
                                              
                                              
                                              <?php if($company_payment_row["PaymentMethod"] == "Note"){?>
                                              		 <tr>
                                                            <td colspan="4">
                                                            
                                                            
                                                            
                                                           
                                                              
                                                              
                                                                <table id="cheque_table" border="0" style="padding:0px 0 0 50px;"  >
                                                                  <tr>
                                                                    <td><span style="font-weight: bold">ข้อมูลการจ่ายธนาณัติ</span></td>
                                                                    <td>&nbsp;</td>
                                                                  </tr>
                                                                 
                                                                  <tr>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่ธนาณัติ</span></td>
                                                                    <td><span class="style86" style="padding: 10px 0 10px 0;">
                                                                      <?php echo $company_payment_row["RefNo"]?>
                                                                    </span></td>
                                                                  </tr>
                                                                  
                                                                  <?php //yoes 20200311
																  //as per https://app.asana.com/0/794303922168293/1165739389391433 -> hide this for now
																  if(1==0){
																  ?>
																  <tr>
                                                                    <td>ลงวันที่</td>
                                                                    <td>
                                                                    <?php echo formatDateThai($company_payment_row["NoteDate"]);?>
                                                                    </td>
                                                                  </tr>
																  <?php }?>
                                                                </table>
                                                                
                                                                
                                                              
                                                              
                                                              </td>
                                                          </tr>
                                                          
                                                 <?php }?>
                                              
                                             </table>
                             
                             
                             
                             
                             
                             <!------ END PAYMENT DETAIL ------>
							 
							 
							 <table border="0">
								
								<?php
								//yoes 20220225
								//---> contact address สำหรับส่งใบเสร็จ
								
								$contact_address_row = getFirstRow("
																						
														select
															*
														from
															company_by_year_company
															left join provinces p on Province = p.province_id
														where
															cid = '$this_id'
															and
															year = '$this_lawful_year'
															and
															row_type = 1
													
													");
								
								

								 ?>
								<tr >
                                      <td colspan="3">
                                        
                                        <hr>
                                        <font color="#006600">
                                            <strong>ที่อยู่สำหรับส่งใบเสร็จตัวจริง: </strong>
                                        </font>
                                     
                                      
                                      </td>
                                    </tr>
					
								<tr>
												<td>สถานที่ตั้งเลขที: </td>
												<td>
													<b><?php echo $contact_address_row["Address1"];?></b>
												</td>
												<td class="td_left_pad">ซอย: </td>
												<td>
													<b><?php echo $contact_address_row["Soi"];?></b>
												</td>
										  </tr>


										  <tr>
											<td>หมู่:</td>
											<td>
												<b><?php echo $contact_address_row["Moo"];?></b>
											</td>
											<td class="td_left_pad"> ถนน:</td>
											<td>
												<b><?php echo $contact_address_row["Road"];?></b>
											</td>
										  </tr>
										  <tr>
											<td>ตำบล/แขวง: </td>
											<td>
												<b><?php echo $contact_address_row["Subdistrict"];?></b>
											</td>
											<td class="td_left_pad"> อำเภอ/เขต:</td>
											<td>
												<b><?php echo $contact_address_row["District"];?></b>
											</td>
										  </tr>
										  <tr>
											<td>จังหวัด: </td>
											<td> 
												<b><?php echo 
												
												//yoes 20200401
												//หน้า ejob ขาดจัวหวัดค่ะ
												//https://app.asana.com/0/794303922168293/1169293249450220
												$contact_address_row["province_name"];?></b>
											</td>
											<td class="td_left_pad"> รหัสไปรษณีย์:</td>
											<td>
												<b><?php echo $contact_address_row["Zip"];?></b>
											</td>
										  </tr>

											 
											  
								</table>
							 
                             
                             <table border="0" >
									
									
									<?php
									
										//yoes 20220731
										//see if have 34 docfile ...
										$have_ejob_34_files = getFirstItem("
			
											select
												count(*)
											from
												lawfulness_company a
													join
														files f1
														on
														a.lid = f1.file_for
														and
														f1.file_type = 'company_34_docfile_1'
													
											where
												a.lid = '".($lawful_values["LID"])."'				
												
										
										");
									
									?>
									
									<tr >
                                      <td colspan="3">
                                        
                                        
                                        <font color="#006600">
                                            <strong>เอกสารประกอบการรายงาน มาตรา 34</strong>
                                        </font>
                                     
                                      
                                      </td>
                                    </tr>
									
									<?php if($have_ejob_34_files){?>
                                                                        
                                    
                                    
                                    <tr bgcolor="#fcfcfc" >
                                     <td>
                                       
                                      </td>
                                      <td>
                                        สำเนา สปส 1-10 ส่วนที่ 1
                                        <br />
                                       ประจำเดือน ต.ค.<?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>
									   <br />(พร้อมสำเนาใบเสร็จการชำระเงินของประกันสังคมเดือน ต.ค.<?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>)	
                                      </td>
                                      <td>
                                        
                                        <?php 
                                            
                                            //do $this_id swap thing because doc link use LID, but consume $this_id
                                            //but $this_id on this page is CID and not LID...
                                            $required_doc++;
                                                                                                
                                            $this_id_temp = $this_id;
                                            $this_id = $lawful_values["LID"];       
                                                
                                            $file_type = "company_34_docfile_1";                                                
                                            include "doc_file_links_ejob.php";                                                    
                                            $this_id = $this_id_temp;
                                                                                                    
                                            ?>
                                            <?php if($have_doc_file){$required_doc--;?><br /><script>$('#ex341').hide();</script><?php }?>
                                            <?php if(! $submitted_company_lawful){?>
                                           <input type="file" name="company_34_docfile_1" id="company_34_docfile_1" /> 
                                           <?php }?>
                                           
                                           
                                          </td>
                                        </tr>
										
										
									<?php }else{ ?>
									
										<tr bgcolor="#fcfcfc" >
										 <td>
										   
										  </td>
										  <td>
											สำเนา สปส 1-10 ส่วนที่ 1
											<br />
										   ประจำเดือน ต.ค.<?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>
										   <br />(พร้อมสำเนาใบเสร็จการชำระเงินของประกันสังคมเดือน ต.ค.<?php echo ($this_year > 3000 ? $this_year+543-1-1000 : $this_year+543-1) ;?>)	
										  </td>
										  <td>
											
											<?php 
												
												//do $this_id swap thing because doc link use LID, but consume $this_id
												//but $this_id on this page is CID and not LID...
												$this_id_temp = $this_id;
												$disable_delete_temp = $disable_delete;
												$disable_delete = 1;
												$this_id = $lawful_values["LID"]; 
												
												$file_type = "company_33_docfile_3";
												include "doc_file_links_ejob.php";
												$this_id = $this_id_temp;	
												$disable_delete = $disable_delete_temp;
												?>
												
											   
											   
											  </td>
											</tr>
									
									<?php }?>
                                                                    
                                    
                              
                            </table>
                             
							 
							 
							 
                             
                                            
                                            
                                            
                                            
                                            
                             </td>
                                            
                                            
						</tr>        
                         
                         
                         
                         
                         
                         <!--------- END ROW FOR PAYMENT ---------->  
                         
                         
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                    <!----- START ROW for FILES ----->
                    
                   	 <tr>
                      
                     		 <td>
                     		 
                     		 
                     		 <table>
								  <tr>
									<td colspan="4">
										<hr>
										<div style="font-weight: bold; padding:5px 0 5px 0; color:#006600">ข้อมูลติดต่อ</div>
									</td>
								  </tr>

								  <tr>
									<td>ชื่อผู้ติดต่อ 1: </td>
									<td>
									  <?php echo $contact_address_row["ContactPerson1"];?>
									</td>
									<td class="td_left_pad">เบอร์โทรศัพท์: </td>
									<td><?php echo $contact_address_row["ContactPhone1"];?></td>
								  </tr>
								  <tr>
									<td>ตำแหน่ง:</td>
									<td><?php echo $contact_address_row["ContactEmail1"];?></td>
									<td class="td_left_pad"> อีเมล์:</td>
									<td><?php echo $contact_address_row["ContactPosition1"];?></td>
								  </tr>
								   <tr>
									<td>ชื่อผู้ติดต่อ 2: </td>
									<td>
									  <?php echo $contact_address_row["ContactPerson2"];?>
									</td>
									<td class="td_left_pad">เบอร์โทรศัพท์: </td>
									<td><?php echo $contact_address_row["ContactPhone2"];?></td>
								  </tr>
								  <tr>
									<td>ตำแหน่ง:</td>
									<td><?php echo $contact_address_row["ContactEmail2"];?></td>
									<td class="td_left_pad"> อีเมล์:</td>
									<td><?php echo $contact_address_row["ContactPosition2"];?></td>
								  </tr>
								  
								</table>
                             
                             <hr />
                             
                             
                             <div style="font-weight: bold;color:#006600; padding-bottom:10px;">
                                             
                                              	เอกสารเพิ่มเติม	
												
                                           
                           </div>
                                            
                          
                            
                            
                           <div style="width:400px; padding-bottom:5px;">
							<?php 
                                
                                //do $this_id swap thing because doc link use LID, but consume $this_id
                                //but $this_id on this page is CID and not LID...
                                $this_id_temp = $this_id;
                                $this_id = $lawful_values["LID"];
                                
                                $file_type = "company_docfile";
                            
                                include "doc_file_links_ejob.php";
                                
                                $this_id = $this_id_temp;
                                
                            ?>
                            </div>
                             
                             
                             </td>
                             
                             
                    </tr>
                    
                   
                   
                    <!----- END ROW for FILES ----->
                   
                   
                   
                   
                   
                   
                   
                   
                   <!----- START ROW for REMARKS ----->
                    
                   	 <tr>
                      
                     		 <td>
                             
                             <hr />
                             
                             
                             <div style="font-weight: bold;color:#006600; padding-bottom:10px;">
                                             
                                              	หมายเหตุ
                                           
                           </div>
                                            
                          
                            
                            
                            <?php 
							
									$the_ejob_submitted_row = getFirstRow("
									
												select
													*
												from
													lawfulness_company
												where
													 CID = '$this_id'
													 and Year = '$this_lawful_year'
									
											");
							
							
									$the_ejob_remarks = nl2br($the_ejob_submitted_row[lawful_remarks]);
									
									if($the_ejob_submitted_row[lawful_submitted] == 0){
										
										$the_ejob_submitted_on = 0; 
									}else{									
										$the_ejob_submitted_on = $the_ejob_submitted_row[lawful_submitted_on]; 
									}
									
									?>
									
									
									<?php if($the_ejob_remarks && $the_ejob_submitted_row[lawful_submitted_on] < '2021-12-12 00:00:01'){ ?>
									
										<?php echo $the_ejob_remarks;?>
										
									<?php }?>
                             
								<div style="width: 700px;" class="chat-box scrollable ps-container ps-theme-default ps-active-y"  data-ps-id="cfd00d42-28f8-0b6b-8b32-233bdc335ad1">
                                    <!--chat Row -->
                                    <ul class="chat-list">
									
									
									<?php if($the_ejob_remarks && $the_ejob_submitted_row[lawful_submitted_on] > '2021-12-12 00:00:01'){ ?>
									
									<li class="chat-item">
                                            <div class='chat-img'><img src='./decors/chat_company.jpg' alt='user'></div>
											<div class="chat-content">
                                                <div class="box bg-light-success">
                                                    <h5 class="font-medium"><b>ผู้ใช้งานสถานประกอบการ</b></h5>
													<?php if($the_ejob_submitted_on){ ?>
														<div class="chat-time"><?php echo formatDateThai($the_ejob_submitted_on,0,1)?></div>
													<?php }?>
                                                    <p class="font-light mb-0"><?php echo nl2br($the_ejob_remarks);?> </p>
                                                    
                                                </div>
                                            </div>
                                            
                                        </li>
										
									<?php }?>
							 
								<?php
								
									$sql = "select
												*
											from
												ejob_remarks
											where
												ejr_ejob_lid = '".$lawful_values["LID"]."'
											order by
												ejr_id desc
											";
											
									$ejob_remarks_result = mysql_query($sql);
									
									
									while($remarks_row = mysql_fetch_array($ejob_remarks_result)){	
									
										if($remarks_row[ejr_from] != $the_ejob_submitted_row[CID]){
											$remarks_logo = "./dep_logo.jpg";
											$remarks_name = "เจ้าหน้าที่กองทุนฯ";
											$the_odd = "odd";
											
											$remarks_logo_left = "";
											$remarks_logo_right = "<div class='chat-img'><img src='$remarks_logo' alt='user'></div>";
										}else{
											$remarks_logo = "./decors/chat_company.jpg";
											$remarks_name = "ผู้ใช้งานสถานประกอบการ";
											$the_odd = "";
											$remarks_logo_left = "<div class='chat-img'><img src='$remarks_logo' alt='user'></div>";
											$remarks_logo_right = "";
											
										}
										//$remarks_logo = "./decors/chat_company.jpg";
										
									
									?>
									
										<li class="<?php echo $the_odd;?> chat-item">
                                            <?php echo $remarks_logo_left;?>
											<div class="chat-content">
                                                <div class="box bg-light-success">
                                                    <h5 class="font-medium"><b><?php echo $remarks_name;?></b></h5>
													<div class="chat-time"><?php echo formatDateThai($remarks_row[ejr_datetime],0,1)?></div>
                                                    <p class="font-light mb-0"><?php echo nl2br($remarks_row[ejr_remarks]);?> </p>
                                                    
                                                </div>
                                            </div>
                                            <?php echo $remarks_logo_right;?>
                                        </li>
									
									
									<?php
									
									}
								
								?>
								
									</ul>
								  </div>
							 
									<?php if(1==0){?><div class="chat-box scrollable ps-container ps-theme-default ps-active-y"  data-ps-id="cfd00d42-28f8-0b6b-8b32-233bdc335ad1">
                                    <!--chat Row -->
                                    <ul class="chat-list">
                                
                                        
										<!--chat Row -->
                                        <li class="chat-item">
                                            <div class="chat-img"><img src="./decors/chat_company.jpg" alt="user"></div>
											<div class="chat-content">
                                                <div class="box bg-light-success">
                                                    <h5 class="font-medium"><b>ผู้ใช้งานสถานประกอบการ</b></h5>
													<div class="chat-time"><?php echo formatDateThai('2019-09-01 13:14:32',0,1)?></div>
                                                    <p class="font-light mb-0"><?php echo $the_ejob_remarks;?> </p>
                                                    
                                                </div>
                                            </div>
                                            
                                        </li>
										
										<!--chat Row -->
                                        <li class="chat-item">
											<div class="chat-img"><img src="./dep_logo.jpg" alt="user"></div>
                                            <div class="chat-content">
                                                <div class="box bg-light-success">
                                                    <h5 class="font-medium"><b>เจ้าหน้าที่กองทุนฯ</b></h5>
													<div class="chat-time"><?php echo formatDateThai('2019-07-01 12:23:32',0,1)?></div>
                                                    <p class="font-light mb-0">เจ้าหน้าที่ได้ตรวจสอบเอกสารแล้ว พบว่า
<br>1. ขาดสำเนาบัตรประชาชนของผู้มอบอำนาจและผู้รับมอบอำนาจ
<br>- ตามไฟล์แนบนะคะ
<br>2. ขาดสำเนา สปส.1-10 ส่วนที่ 2 ของเดือนมกราคม 2563 ที่มีรายชื่อคนพิการครบทุกคน
<br>- ของสังแวว ไม่ได้ส่งประกันสังคมเนื่องจากสูงอายุแล้วค่ะ แนบหนังสือรับรองรายได้ที่ส่งให้กรมสรรพากรมาให้แทน
<br>หลังจากทำการแก้ไขเอกสารถูกต้องแล้ว โปรดนำส่งมาใหม่อีกครั้งค่ะ
<br>หากมีข้อสงสัยเพิ่มเติม กรุณาติดต่อ 038-277877 ต่อ 24 (กิ๊ฟ)</p>
                                                    
                                                </div>
                                            </div>
											
                                        </li>
                                        
                                    </ul>
								  </div>
									<?php }?>
                                    
                           
                             
                             </td>
                             
                             
                    </tr>
                    
                   
                   
                    <!----- END ROW for REMARKS ----->
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                    
                    
                    
                    <tr>
                    	<td>
                        
                        <hr />
                        
                        <div align="center">
                        
                        
                        
                        <table>
                        	<tr>
                            	<td>
                                
                                <?php 
								
								$lawful_submitted = getFirstItem("
							
							
										select
															lawful_submitted
														from
															lawfulness_company
														where
															 CID = '$this_id'
                                                             and Year = '$this_lawful_year'");
								
								
								?>
                                
                                
                                <?php 
									if( $lawful_submitted){
								
								?>
                                
                                
                                 <div align="center" style="color: #060">
                                        มีการยื่นแบบฟอร์มออนไลน์มาวันที่ <?php 
                                        
                                        
                                        $submmited_date = getFirstItem("
                                                            select 
                                                                lawful_submitted_on
                                                            from
                                                                lawfulness_company
                                                            where
                                                                CID = '" . $this_id . "'
                                                                and
                                                                Year = '".$this_lawful_year."'
                                                            ");
                                        
                                            
                                        echo formatDateThai($submmited_date,1, 1);
                                        
                                        ?>                        
                                    </div>
                                    
                                    
                                 <?php }?>
                                 
                                 
                                  <?php 
									if( $lawful_submitted == 2){
								
								?>
                                
                                
                                  <div align="center" style="color: #060">
                                            เจ้าหน้าที่ทำการบันทึกข้อมูลเข้าระบบแล้วเมื่อวันที่ <?php 
                                            
                                            
                                            $approved_date = getFirstItem("
                                                                select 
                                                                    lawful_approved_on
                                                                from
                                                                    lawfulness_company
                                                                where
                                                                    CID = '" . $this_id . "'
                                                                    and
                                                                    Year = '".$this_lawful_year."'
                                                                ");
																
											 $approved_by = getFirstItem("
                                                                select 
                                                                    lawful_approved_by
                                                                from
                                                                    lawfulness_company
                                                                where
                                                                    CID = '" . $this_id . "'
                                                                    and
                                                                    Year = '".$this_lawful_year."'
                                                                ");
                                            
                                                
                                            echo formatDateThai($approved_date,1, 1);
                                            
                                            ?>                        
                                            
                                            โดย
                                            
                                            
                                            <?php 
											
											echo getFirstItem("select user_name from users where user_id = ".$approved_by);
											?>
                                            
                                        </div>
                                    
                                    
                                 <?php }?>
								 
								 
								 <?php //yoes 20220606 
								 
									//import org history
									$get_log_sql = "
									
										select
											lid
											, cid
											, year
											, lawful_submitted_on
											, lawful_approved_on
											, lawful_approved_by
											
										from
											lawfulness_company_full_log
										where
											lawful_approved_on != '0000-00-00 00:00:00'
											and
											log_source = 'scrp_transfer_data.php'
											and
											CID = '" . $this_id . "'
											and
											Year = '".$this_lawful_year."'
										group by
											lid
											, cid
											, year
											, lawful_submitted_on
											, lawful_approved_on
											, lawful_approved_by
										ORDER BY 
											
											lawful_approved_on desc
											, lawful_submitted_on asc
									
									";
									
									$get_log_result = mysql_query($get_log_sql);
									
									if(mysql_num_rows($get_log_result)){
										echo "<font color=green>ประวัติการนำเข้าข้อมูล:";
									}
									
									while($get_log_row = mysql_fetch_array($get_log_result)){			
										//
										echo "<br>นำเข้าข้อมูลวันที่ :" . formatDateThai($get_log_row[lawful_approved_on],1,1);
										echo " โดย " . getFirstItem("select user_name from users where user_id = ".$get_log_row[lawful_approved_by]);
									}
									
									if(mysql_num_rows($get_log_result)){
										echo "</font>";
									}
								 
								 ?>
								
								
							<?php
														
														
							 
							//yoes 20211102
							if(
									($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3)

									&&
									(isset($this_cid) || $output_values["AccessLevel"]=="4")
							){ 
							
								//bank 20221220 move btn login ejob to here
								include "scrp_btn_login_ejob.php"; 
								
							}
							?>
								
								
								
								
								<?php
								
								//yoes 20220309
								//add this
								$resubmit_status = getLawfulnessMeta($this_lid, "es-resubmit");
								
								if( $lawful_submitted == 1 || ($lawful_submitted == 3 && $resubmit_status == 1)){							
							
									?>
                                    
                                
                                
                                    <div align="center">
                                
                                        <form method="post" action="scrp_transfer_data.php"

                                        <?php

                                            //yoes 20151211 --- disallow executive to save data on this page
                                            if($sess_accesslevel == 5 || $sess_accesslevel == 18 || $is_read_only || $case_closed){

                                                echo "style='display:none;'";

                                            }


                                        ?>

                                        >

                                                <input name="" type="submit" value="ยืนยันและบันทึกข้อมูลจากสถานประกอบการ"

                                                 onclick = "return confirm('ต้องการยืนยันและบันทึกข้อมูลจากสถานประกอบการนี้?');"

                                                />

												
												<input name="" type="button" value="ปฏิเสธข้อมูลจากสถานประกอบการ"
													style="color: red;"                                                 
													id="btnSendRejectToCompany"
													onClick="SendRejectToCompany()"
                                                />
												<br>
												<br>
												<font color="#ff4000">
                                        ** กรณีที่ตรวจสอบแล้วข้อมูลจากสถานประกอบการไม่ถูกต้อง <br>ท่านสามารถทำการปฏิเสธข้อมูลเพื่อให้สถานประกอบการทำการปรับปรุงข้อมูลและกรอกข้อมูลเข้ามาใหม่ได้
												</font>                                  

                                        


                                                <textarea name="reject_remark" rows="5" style="width:80%;" placeholder="กรอก เหตุผล คำแนะนำ ในการกรอกข้อมูลของสถานประกอบการ" id="reject_remark" required></textarea>												
                                                <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                                <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                                <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>

                                                <input type="hidden" name="the_sum_employees" value="<?php echo $sum_employees; ?>"/>



                                        </form>

                                        <hr>

                                        <font color="#00008B">
                                        ส่งข้อความถึงสถานประกอบการ <br> ท่านสามารถส่งข้อความถึงสถานประกอบการได้โดยใส่ข้อความลงในกล่องข้อความด้านล่าง และกด "ส่งข้อความ"
                                        <br> ข้อความจะถูกส่งไปหาเจ้าหน้าที่สถานประกอบการ <b>คุณ xxx yyy</b> email: <b>zzz</b>
                                        </font>


                                        <form method="post" action="scrp_send_message.php"

                                        <?php

                                            //yoes 20181016 --- disallow executive to save data on this page
                                            if($sess_accesslevel == 5 || $sess_accesslevel == 18 || $is_read_only || $case_closed){

                                                echo "style='display:none;'";

                                            }


                                        ?>

                                        >


                                                <textarea name="comments_remark" rows="5" style="width:80%;" placeholder="กรอกข้อความที่ต้องการส่งให้สถานประกอบการ" id="comments_remark" required></textarea>

                                                <input name="" type="button" value="ส่งข้อความให้สถานประกอบการ"                                                
													style="color: #00008B;"                                               
													id="btnSendMessageToCompany"
													onClick="SendMessageToCompany()"
                                                />

                                                <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                                <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                                <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>

                                                <input type="hidden" name="the_sum_employees" value="<?php echo $sum_employees; ?>"/>



                                        </form>




                                        <hr>

                                        



                                                <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                                <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                                <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>

                                                <input type="hidden" name="the_sum_employees" value="<?php echo $sum_employees; ?>"/>



                                        </form>
                                
                                    </div>
                                
                                <?php }?>
                                <?php
																	include "ajax_send_message_to_company.php"; // 20181106:: DANG
																	sendMessagePrintJS();
																?>

                                </td>
                                <td>
                        
                        
                        		<?php if($this_lawful_year == 2013 && $output_values["Province"] == 1){?>
                                
                        		<form method="post" action="./tcpdf/bangkok_56_pdf.php" target="_blank">
                            
                                    <input name="" type="submit" value="พิมพ์แบบฟอร์มเป็น pdf" />
                                                                    
                                    <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                    <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                    <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                
	                            </form>
                                
                                <?php }?>
                                
                                
                               
                                
                                
                                
                            
                                
                               </td>
                           </tr>
                       </table>
                               
                                                       
                       
                        
                        
                        </div>
                        
                         <?php if($this_lawful_year == 2016 && ($sess_accesslevel == 1)){ // yoes20151025 - add form จพ-xx?>
                                
                                
                                <?php 
								
									//yoes 20151025
									//special for admin-page
									if(!$final_money){
										
										$final_money = $company_payment_row["Amount"];
											
									}
								
								?>
                                
                                <hr />
                                <div align="center">
                                <form method="post" action="./create_pdf_4.php" target="_blank">
                            
                            
                                    <?php if(!countCompanyInfo($output_values["CID"], $this_lawful_year)){?>
                                        <input name="" type="submit" value="Preview แบบฟอร์ม จพ. สำหรับรายงานผลการปฏิบัติตามกฎหมาย" />
                                    <?php }else{?>
                                        <input name="" type="submit" value="พิมพ์แบบฟอร์ม จพ. สำหรับรายงานผลการปฏิบัติตามกฎหมาย" />
                                    <?php }?>
                                                                    
                                    <input type="hidden" name="the_lid" value="<?php echo $lawful_values["LID"]; ?>" />
                                    <input type="hidden" name="the_cid" value="<?php echo $this_id;?>"/>
                                    <input type="hidden" name="the_year" value="<?php echo $this_lawful_year; ?>"/>
                                    
                                    <input type="hidden" name="employee_to_use" value="<?php echo $employee_to_use; ?>"/>
                                    <input type="hidden" name="final_employee" value="<?php echo $final_employee; ?>"/>
                                    <input type="hidden" name="hire_numofemp" value="<?php echo $hire_numofemp; ?>"/>
                                    <input type="hidden" name="extra_emp" value="<?php echo $extra_emp; ?>"/>
                                    <input type="hidden" name="final_money" value="<?php echo $final_money; ?>"/>
                                    <input type="hidden" name="curator_user" value="<?php echo $curator_user; ?>"/>
                                
                                </form>
                                </div>
                                <hr />
                                
                           <?php }?>
                        
                        
                        </td>
                    </tr>
                      
  
                    
</table><!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
            <!------------ END INPUT TABLE >>----->
<?php }//end if($count_info){ ?>            
            
            
                    
          </td>
        </tr>
            
             <tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
                </td>
            </tr>  
            
	  </table>                            
        
    </td>
  </tr>
    
</table> 







  


<div id="employees_popup" style=" position:absolute; padding:3px; background-color:#006699; width: 500px; display:none; " >

	<form  method="post" enctype="multipart/form-data" action="scrp_update_lawful_employees.php"><!--- curator information just get posted into this page-->
	<table  bgcolor="#FFFFFF" width="500" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
    
    	<tr>
            <td colspan="2">
                    <div style="font-weight: bold;color:#006600;  " >
                    ปรับปรุงจำนวน<?php echo $the_employees_word;?>สำหรับมาตรา 33
                    </div> 
					
					
				</td>
        </tr>
    
    
    
    	<?php 
		
			//yoes 20160511
			//if($output_values[CompanyTypeCode] == "07"){
				
			if($is_school){
				
				
				//add metadata
				$meta_result = mysql_query("
						select * 
						from 
							lawfulness_meta
						where 
							meta_lid  = '".$lawful_values["LID"]."'
						");
				
				while($meta_row = mysql_fetch_array($meta_result)){			
					//
					$lawful_meta[$meta_row[meta_for]] = (doCleanOutput($meta_row[meta_value]));				
				}
			
			?>
        
        	<tr>
                <td>
                จำนวนครู
                </td>
                <td>
                <input name="school_teachers" id="lawful_school_teachers" style="width:50px" type="text" 
                value="<?php echo $lawful_meta[school_teachers];?>"   onchange="do_sum_lawful_school();"/> คน
                </td>
            </tr>
            
            <tr>
                <td>
                จำนวนครูสัญญาจ้าง
                </td>
                <td>
                <input name="school_contract_teachers" id="lawful_school_contract_teachers" style="width:50px" type="text" 
                value="<?php echo $lawful_meta[school_contract_teachers];?>"  onchange="do_sum_lawful_school();"/> คน
                </td>
            </tr>
            
            
        	<tr>
                <td>
                จำนวนลูกจ้าง
                </td>
                <td>
                <?php 
					
					//yoes 20160614 -- if no employees then...
					if($lawful_meta[school_teachers] +$lawful_meta[school_contract_teachers] + $lawful_meta[school_employees] == 0){
						$lawful_meta[school_employees] = $employee_to_use_from_lawful;
					}
				
				?>
                
                <input name="school_employees" id="lawful_school_employees" style="width:50px" type="text" 
                value="<?php echo $lawful_meta[school_employees];?>" onchange="do_sum_lawful_school();"   /> คน
                </td>
            </tr>
            
            <tr>
                <td>
                รวมทั้งสิ้น
                </td>
                <td>
                <span id="lawful_school_sum">...</span> คน
                
                <script>
				
					function do_sum_lawful_school(){
									
						sum_school = ($('#lawful_school_teachers').val()*1)+($('#lawful_school_contract_teachers').val()*1)+($('#lawful_school_employees').val()*1);
						
						$('#lawful_school_sum').html(sum_school);
					}
							 
				
					$( document ).ready(function() {
						
						do_sum_lawful_school();
					
					});
					
				</script>
                
                </td>
            </tr>
            
            
            <input name="update_employees" id="update_employees" type="hidden" value="<?php echo formatEmployee($employee_to_use_from_lawful);?>"/>
        	
        
        <?php }else{?>
            <tr>
                <td>
                จำนวน<?php echo $the_employees_word;?>: 
                </td>
                <td>
                <input name="update_employees" id="update_employees_01" style="width:50px" type="text" value="<?php echo formatEmployee($employee_to_use_from_lawful); //yoes 20151118 -- always use original values ?>" onchange="addEmployeeCommas('update_employees_01');"  /> คน

                
                 <?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && !$is_read_only && !$case_closed && 1==0){ ?>
                                            <input id="btn_get_sso_employees" type="button" value="ดึงข้อมูลจำนวนลูกจ้าง" onClick="return doGetSSOEmployees();" />
                                            <script>
											
												function doGetSSOEmployees(){
													
													
													$.ajax({
													  method: "POST",
													  url: "ajax_get_sso_employees.php",
													  data: { name: "John", location: "Boston" }
													})
													  .done(function( html ) {
														//alert( "Data Saved: " + msg );
														$( "#sso_employee_result" ).html( html);
													  });
													
												}
												
											</script>
                                            <?php }?>
                
                
				
				
				
                </td>
            </tr>

            <?php if($sess_accesslevel == "6" || $sess_accesslevel == "7"){?>

            <tr>
				<td>
                จำนวนพนักงานราชการ:
				</td>
				<td>
				<input name="update_employees_gov_02" id="update_employees_gov_02" style="width:50px" type="text"
				value="<?php $update_employees_gov_02 = getFirstItem("select meta_value from lawfulness_meta where meta_for = 'employees_gov_02' and meta_lid = '".$this_lid."'"); echo $update_employees_gov_02;?>"
				 /> คน
				</td>
			</tr>
			<tr>
				<td>
                จำนวนลูกจ้างประจำ:
				</td>
				<td>
				<input name="update_employees_gov_03" id="update_employees_gov_03" style="width:50px" type="text"
				 value="<?php $update_employees_gov_03 = getFirstItem("select meta_value from lawfulness_meta where meta_for = 'employees_gov_03' and meta_lid = '".$this_lid."'"); echo $update_employees_gov_03;?>"/> คน
				</td>
			</tr>
			<tr>
				<td>
                จำนวนผู้ปฏิบัติการที่เรียกชื่ออย่างอื่น:
				</td>
				<td>
                <input name="update_employees_gov_04" id="update_employees_04" style="width:50px" type="text"
                 value="<?php $update_employees_gov_04 = getFirstItem("select meta_value from lawfulness_meta where meta_for = 'employees_gov_04' and meta_lid = '".$this_lid."'"); echo $update_employees_gov_04;?>"/> คน
				</td>
			</tr>

			<script>

			     $('#update_employees_01').val(<?php echo $employee_to_use_from_lawful - $update_employees_gov_02 - $update_employees_gov_03 - $update_employees_gov_04;?>);
            </script>

			<?php }?>
            
            <?php if(1==1){?>
            <tr>
            	<td colspan="2">
                
                	<span id="sso_employee_result">
                    
                    </span>
                    
                </td>
            </tr>
            <?php }?>
        <?php }?>
        
        
        
        
        <tr>
            <td colspan="2">
            	<div align="center">
                   <input name="" type="submit" value="ปรับปรุงข้อมูล"/>
                   <input name="" type="button" onClick="fadeOutMyPopup('employees_popup'); $('#employees_popup_focus').val(''); return false;" value="ปิดหน้าต่าง"/>
                   
                  	<input name="LID" type="hidden" value="<?php echo $lawful_values["LID"];?>" />
                    
                    <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                    <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                    
                     <input id="employees_popup_focus" name="employees_popup_focus" type="hidden" value="" />
                  
                </div>
			</td>
        </tr>
        
    	
    </table>

</form>
</div>      





<div id="54_interest_popup" style=" position:absolute; padding:3px; background-color:#006699; width: 500px; display:none; " >

	<form  method="post" enctype="multipart/form-data" action="scrp_update_interest_day.php"><!--- curator information just get posted into this page-->
	<table  bgcolor="#FFFFFF" width="500" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
    
    	<tr>
            <td colspan="2">
                    <div style="font-weight: bold;color:#006600;  " >
                   คิดดอกเบี้ยสำหรับปี 2554 
                    </div> 
				</td>
        </tr>
    
    
    
    		 <tr>
                <td>
               คิดดอกเบี้ยหรือไม่:
                </td>
                <td>
                
               
                <select name="do_54_budget">
                	<option value="0">ไม่คิดดอกเบี้ย</option>
                    <option value="1" <?php if($do_54_budget){?>selected="selected"<?php }?>>คิดดอกเบี้ย</option>
                </select>
                </td>
            </tr>
    	
            <tr>
                <td>
                วันที่เริ่มคิดดอกเบี้ย: 
                </td>
                <td>
                
                 <?php
											   
					   $selector_name = "the_date";
					  // $this_date_time = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];		
					  
					
					  
						if(!$the_54_budget_date){ 
						
							$this_date_time = date("Y-m-d");					   
						
						}else{
							
							$this_date_time = $the_54_budget_date;					   
							
						}
						//*toggles_payment*
					   //
					   include ("date_selector.php");
				   
				   ?>
                
                </td>
            </tr>
        
        
        <tr>
            <td colspan="2">
            	<div align="center">
                   <input name="" type="submit" value="ปรับปรุงข้อมูล"/>
                   <input name="" type="button" onClick="fadeOutMyPopup('54_interest_popup'); return false;" value="ปิดหน้าต่าง"/>
                   
                  	<input name="LID" type="hidden" value="<?php echo $lawful_values["LID"];?>" />
                    
                    <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                    <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />

                  
                </div>
			</td>
        </tr>
        
    	
    </table>

</form>
</div>      



<?php include "organization_35_popup.php";?>                





<?php //yoes 2015116 -- new popup for company branch

if($sess_accesslevel == 4 || ($count_info && $sess_accesslevel == 1)){ // only company can see this

	
	
	//if there are any branch data here then populate it also
	
	if(is_numeric($_GET[bid])){
		
		$the_bid = $_GET[bid];		
		$branch_row = getFirstRow("select * from company_company where CID = '$the_bid'");
		
	}

?>


 <div id="company_branch_popup" style="position: absolute;  padding:3px; background-color:#006699; width: 600px; display: none; " >
 		
		
			<script language='javascript'>
						<!--
						function validateFormBranch(frm) {
							
							
							var checkOK = "1234567890";
							
							var checkStr = frm.CompanyCode.value;
							var allValid = true;
							for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   
						   //CompanyCode - number only
							
							if(frm.BranchCode.value.length < 6)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่สาขา เป็นเลข 6 หลักเท่านั้น");
								frm.BranchCode.focus();
								return (false);
							}
							
							
							//BranchCode - number only
							var checkOK = "1234567890";
							
							var checkStr = frm.BranchCode.value;
							var allValid = true;
							for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("เลขที่สาขา ต้องเป็นตัวเลขเท่านั้น");
							 frm.BranchCode.focus();
							 return (false);
						   }
						   
						   
						   //BranchCode - number only
							if(frm.CompanyNameThai.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อบริษัท(ภาษาไทย)");
								frm.CompanyNameThai.focus();
								return (false);
							}
							if(frm.Employees.value.length == 0)
							{
								alert("กรุณาใส่ข้อมูล: จำนวน<?php echo $the_employees_word;?>");
								frm.Employees.focus();
								return (false);
							}
							//----
							
							
							var checkStr = frm.Employees.value;
							var allValid = true;
							for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("จำนวนลูกจ้าง ต้องเป็นตัวเลขเท่านั้น");
							 frm.Employees.focus();
							 return (false);
						   }
														
							
							
							if(frm.Province.selectedIndex == 0 || frm.Province.value == 99)
							{
								alert("กรุณาใส่ข้อมูล: จังหวัด"); 
								frm.Province.focus();
								return (false);
							}
							
							
							
							
							if(frm.Address1.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: สถานที่ตั้ง");
								frm.Address1.focus();
								return (false);
							}
							
							
							//----
							return(true);									
						
						}
						-->
					
					</script>
 
 	<form method="post" action="scrp_add_branch.php" onsubmit="return validateFormBranch(this);" style="">
    
        
 	<table  bgcolor="#FFFFFF" width="600" border="0" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse; ">
    	
                  <tr>
                    <td colspan="4"><div style="font-weight: bold; padding:0 0 5px 0;"><?php  
								
								if($the_bid){
									
									if($sess_accesslevel == 4){
										echo "แก้ไขข้อมูลสาขา";
									}else{
										echo "ข้อมูลสาขา";
									}
									
									
								}else{									
									echo "เพิ่มข้อมูลสาขา";									
								}
								
								?> - ข้อมูลทั่วไป</div></td>
                  </tr>
                  
                   <tr>
                        <td>เลขที่บัญชีนายจ้าง: </td>
                        <td>
                        <?php echo $output_values["CompanyCode"];?>
                        <input name="CompanyCode" type="hidden" value="<?php echo $output_values["CompanyCode"];?>" />
                        <input name="CID" type="hidden" value="<?php echo $this_cid;?>" />
                        </td>
						
								
                        <td > ชื่อบริษัท : </td>
                        <td>
                        
                        <?php echo $output_values["CompanyNameThai"];?>
                        
                        </td>
														
						
						
                      </tr>
                      
                      
                       <tr>
                        <td>ชื่อสาขา (ภาษาไทย): </td>
                        <td>
                        <input type="text" name="CompanyNameThai" value="<?php echo doCleanInput($branch_row[CompanyNameThai]);?>" /> *
                        </td>
						
								
                        <td > ชื่อบริษัท (ภาษาอังกฤษ): </td>
                        <td>
                        <input type="text" name="CompanyNameEng" value="<?php echo doCleanInput($branch_row[CompanyNameEng]);?>" /> 
                        
                        </td>
														
						
						
                      </tr>
                      
                      
                  <tr>
                        <td>เลขที่สาขา:</td>
                        <td>
                                                 <input type="text" name="BranchCode" value="<?php echo doCleanInput($branch_row[BranchCode]);?>" maxlength="6"/> *
                        </td>
                        <td >จำนวนลูกจ้าง:</td>
                        <td> <input type="text" name="Employees" id="" value="<?php echo doCleanInput($branch_row[Employees]);?>" /> *</td>
                      </tr>
                      
                  
                  <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ที่อยู่</div></td>
                      </tr>
                      <tr>
                        <td>สถานที่ตั้งเลขที: </td>
                        <td><label>
                          <input type="text" name="Address1" value="<?php echo doCleanInput($branch_row[Address1]);?>" /> *
                        </label></td>
                        <td class="td_left_pad">ซอย: </td>
                        <td><input type="text" name="Soi" value="<?php echo doCleanInput($branch_row[Soi]);?>" /></td>
                      </tr>
                      <tr>
                        <td>หมู่:</td>
                        <td><input type="text" name="Moo" value="<?php echo doCleanInput($branch_row[Moo]);?>" /></td>
                        <td class="td_left_pad"> ถนน:</td>
                        <td><input type="text" name="Road" value="<?php echo doCleanInput($branch_row[Road]);?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำบล/แขวง: </td>
                        <td><input type="text" name="Subdistrict" value="<?php echo doCleanInput($branch_row[Subdistrict]);?>" /></td>
                        <td class="td_left_pad"> อำเภอ/เขต:</td>
                        <td>
                        <input type="text" name="District" value="<?php echo doCleanInput($branch_row[District]);?>" />
                        </td>
                      </tr>
                      <tr>
                        <td>จังหวัด: </td>
                        <td><?php 
						
						if($branch_row[Province]){
							$_POST["Province"] = $branch_row[Province];
						}
						
						include "ddl_org_province_all.php"
						
						?> *</td>
                        <td class="td_left_pad"> รหัสไปรษณีย์:</td>
                        <td><input type="text" name="Zip" value="<?php echo doCleanInput($branch_row[Zip]);?>" /></td>
                      </tr>
                      
                      
                      <tr>
                        <td colspan="4">
                          <div align="center">
                          	<hr />
                            
                            	<?php if($sess_accesslevel == 4){?>
                            
                                <input type="submit" name="button" id="button" value="<?php  
								
								if($the_bid){
									echo "แก้ไขข้อมูลสาขา";
								}else{									
									echo "เพิ่มข้อมูลสาขา";									
								}
								
								?>" 
                                onclick = "return confirm('ต้องการเพิ่มข้อมูลสาขานี้?');"
                                 />
                                 
                                 <?php }?>
                                 
                                  <input name="" type="button" onClick="fadeOutMyPopup('company_branch_popup'); return false;" value="ปิดหน้าต่าง"/>
                                 
                                 
                                
                                                                                                
                                                       </div>                        </td>
                      </tr>
                      
              
        
     </table>
     
     </form>
 
 </div>
 
<?php 

	//also show this popup if this is a "edit" mode
	if($the_bid){?>
    
    <script>
		fireMyPopup('company_branch_popup',600,250); 
	</script>
    
<?php	
		
	}

?>


 <?php }?>


<?php

	//yoes 20211018 --> this thing have to go to a separate file
	include "organization_my_popup.php";

?>




<script>

<?php if(($_GET["le"] == "le" || $_GET["delle"] == "delle") &&  !$_GET["auto_post"]){ ?>
	
		$(".bs-example-modal-lg-m33").modal('toggle');
	
	
<?php }?>

<?php 
if($this_focus == "official" || $this_focus == "lawful" || $this_focus == "general" || $this_focus == "history" ||  $this_focus == "sequestration" || $this_focus == "input" || $this_focus == "dummy"){
?>
	
	showTab('<?php echo $this_focus;?>');
	
<?php }elseif($is_merged){?>

//alert('waht');
showTab('general');

<?php
}elseif($mode=="new"){
//if($mode=="new"){

?>

document.getElementById('lawful').style.display = 'none';

document.getElementById('history').style.display = 'none';


var sequestrationTab = document.getElementById('sequestration');
if(sequestrationTab != null){
	sequestrationTab.style.display = 'none';	
}


<?php if($sess_accesslevel !=4){ ?>
document.getElementById('official').style.display = 'none';
<?php } ?>

<?php

}else{
//if($mode=="new"){
?>


document.getElementById('lawful').style.display = 'none';

document.getElementById('general').style.display = 'none';


var sequestrationTab = document.getElementById('sequestration');
if(sequestrationTab != null){
	sequestrationTab.style.display = 'none';	
}


<?php if($sess_accesslevel !=4){ ?>
document.getElementById('official').style.display = 'none';
<?php } ?>


var elInput = document.getElementById('input');
if(elInput != null){
	elInput.style.display = 'none';
}



<?php
}
?>
</script>
<script>
										  	
											
	function alertContents() {
		if (http_request.readyState == 4) {
			if (http_request.status == 200) {
				//alert(http_request.responseText.trim()); 
				document.getElementById("loading_"+http_request.responseText.trim()).style.display = 'none';
			} else {
				//alert('There was a problem with the request.');
			}
		}
	}
  </script>
  
   <?php if(($_GET["curate"] == "curate" || $curate == "curate") &&  !$_GET["auto_post"]){?>
	<script>
    
		
        //doPopSubCurator('3');
		//fireMyPopup('35_popup',1020,160);
   		// alert("what");
		//window.location = "view_curator.php?curator_id=<?php echo $curator_last_id;?>";
		
    </script>
    <?php }?>
    
    
    <?php if($is_edit_curator && $is_curator_parent){?>
		
		<script>        
            doPopSubCurator('0');
            // alert("what");
        </script>
    
    <?php }?>
    
    <?php if($is_edit_curator && !$is_curator_parent){?>
		
		<script>        
            doPopSubCurator('<?php echo $curator_row_to_fill["curator_parent"];?>');
            // alert("what");
        </script>
    
    <?php }?>
    
    
    
    
      <?php if($curator_row_to_fill["curator_is_disable"] == "1"){ //pre-fill curator information -> show this dropdown?>
     
        <script>
            document.getElementById('tr_curator_disable').style.display = '';
        </script>
     
     <?php }?>
    
    
    <?php if($_GET["auto_post"] ){ //auto-post -> show loading screen?>
        <div id="overlay"> 
           <div id="img-load" style="color:#FFFFFF; text-align:center">
           	<img src="./decors/bigrotation2.gif"  />
            
            <?php if($_GET["le"]){?>
            <br />
            <strong>กำลังเพิ่มข้อมูลคนพิการที่ได้ีรับเข้าทำงาน...</strong>
            <?php }elseif($_GET["delle"]){?>
            <br />
            <strong>กำลังลบข้อมูลคนพิการที่ได้ีรับเข้าทำงาน...</strong>
            <?php }elseif($_GET["curate"]){?>
            <br />
            <strong>กำลังปรับปรุงข้อมูลมาตรา 35...</strong>
            <?php }else{?>
            <br />
            <strong>กำลังปรับปรุงข้อมูล...</strong>
            <?php }?>
            
            
            </div>
        </div>
        
        <script>
        $t = $("#main_body");
        
        $("#overlay").css({
          opacity: 0.5,
          top: 0,
          left: 0,
          width: $t.outerWidth(),
          height: $t.outerHeight()
        });
        
        $("#img-load").css({
          top:  (380),
          left: ($t.width() / 2 -110)
        });
        
        //$t.mouseover(function(){
           $("#overlay").fadeIn();
        //}
        //);
        </script>
        
    <?php }?>
    
    
    
    <?php 
	
		//yoes 20140612 - also mark status into this as "done reconcile"
		
		if($_GET["auto_post"] && $_GET["reconcile_by_bot"]){
			
			mysql_query("update company_to_reconcile_with_year set reconciled = 1 where cid = '".$this_id."' and year = '". $this_lawful_year ."'");
			
		}
	?>
    
    
    <?php if($_GET["auto_post"]){?>
    
    	<script>
		document.forms["lawful_form"].submit();
		</script>
    
    <?php }?>
	
	
	<?php 
	//yoes new 200181120
	//include "organization_top_warning_box.php";?>
	
	
	
	
	
	<?php 
		if($have_duplicate_33 || $have_duplicate_35){
	?>
		<script>
		
			$('#dupe_status_2018').html('<div align="center"><font color="#FF6600"><?php //echo $have_duplicate_33 . "-". $have_duplicate_35;?>มีข้อมูลซ้ำซ้อนในมาตรา 33 หรือ มาตรา 35</font> - กรุณาทำการตรวจสอบข้อมูล  </div>');
			
			
		</script>
	<?php					
		}
	?>
	

	
	<script type="text/javascript">
    function validateUrgentCase() {
        if (document.getElementById('urgent_check').checked) {
            alert("เป็นเคสด่วนที่สุด");
        } else {
            alert("ยกเลิกเคสด่วนที่สุด");
        }
    }
	
	
	//yoes 20230109
	
	function refreshLawfulness(){
		
		//alert('w');
		//console.log('w');
		axios
			.post('ajax_get_lawfulness_info_from_lid.php', "the_lid=" + <?php echo $this_lid; ?>)//{ step: ""+what+"", the_id: ""+id+""})
			.then(response => {
				
				var mm;		
				mm = response.data;
				//console.log(mm);
				
				//existing id
				$("#summary_33").html(mm.Hire_NumofEmp);
				$("#summary_35").html(mm.curator_user);				
				$("#summary_34").html(mm.need_to_pay);
				
				//new id
				$("#txt_hire_numofemp").html(mm.Hire_NumofEmp);
				$("#txt_curator_user").html(mm.curator_user);				
				$("#txt_curator_usee").html(mm.curator_usee);
				
				//history tab
				$("#txt_hire_numofemp_history").html(mm.Hire_NumofEmp);
				$("#txt_curator_user_history").html(mm.curator_user);
				$("#txt_total_paid_history").html(mm.all_333435_paid);
				
				
				//lawful sattus
				$("#rad_lawfulStatus_01").prop("checked", false);
				
				$("#chk_lawfulStatus_00").hide();
				$("#rad_lawfulStatus_00").show();
				
				$("#chk_lawfulStatus_02").hide();
				$("#rad_lawfulStatus_02").show();
				
				$("#chk_lawfulStatus_03").hide();
				$("#rad_lawfulStatus_03").show();
				
				//lawful history table image
				$("#img_lawfulStatus_00").hide();
				$("#img_lawfulStatus_01").hide();
				$("#img_lawfulStatus_02").hide();
				$("#img_lawfulStatus_03").hide();
				$("#img_lawfulStatus_05").hide();
				$("#img_lawfulStatus_06").hide();
				
				
				if(mm.LawfulStatus == 0){
					$("#chk_lawfulStatus_00").show();
					$("#rad_lawfulStatus_00").hide();
					
					$("#img_lawfulStatus_00").show();
				}
				
				if(mm.LawfulStatus == 1){
					$("#rad_lawfulStatus_01").prop("checked", true);
					
					$("#img_lawfulStatus_01").show();
				}
				
				if(mm.LawfulStatus == 2){
					$("#chk_lawfulStatus_02").show();
					$("#rad_lawfulStatus_02").hide();
					
					$("#img_lawfulStatus_02").show();
				}
				
				if(mm.LawfulStatus == 3){
					$("#chk_lawfulStatus_03").show();
					$("#rad_lawfulStatus_03").hide();
					
					$("#img_lawfulStatus_03").show();
				}
				
				if(mm.LawfulStatus == 6){
					$("#chk_lawfulStatus_06").show();
					$("#rad_lawfulStatus_06").hide();
					
					$("#img_lawfulStatus_06").show();
				}
				
			})
			
		
		
		
	}
	
	refreshLawfulness();
	setInterval(function() {refreshLawfulness()}, 15000);
	
	</script>

    <!-- 20240820 Tor คดี ข้อ 8 2567-->
	<script>
		function getLawStatusByCidYear(cid,year)	{
			$.ajax({
			el: '#chk_law_status',
			type: 'GET',
			//url: "https://law.dep.go.th/law_ws/getCaseDetails.php",
			url: "https://law.dep.go.th/law_ws/getCaseDetails.php",
			dataType: 'json',
			data: { case_type: 'hire', cid: cid , year: year },
			success: function(response) {

				var mm;
				var nn;
				nn = response.data.case;
				mm = response.data.case_detail;

				if (nn && (nn.case_status === 7 || nn.case_status === 6 || nn.case_status === 5 || nn.case_status === 4)) {
                	var $link = $('a[onclick*="fireMyPopup"]');
					$link.hide();

                    //alert(nn.case_status);

					// Replace the link with the alert message
					$link.after('<span id="alert_message">ไม่สามารถเปลี่ยนข้อมูลจำนวนลูกจ้างได้ เนื่องจากมีการส่งฟ้องแล้ว</span>');

            	}



			}

			});
		}

		$(document).ready(function() {
			getLawStatusByCidYear(<?php echo $this_cid*1; ?>, <?php echo $this_lawful_year*1; ?>);
		});

		</script>

</body>
</html>


