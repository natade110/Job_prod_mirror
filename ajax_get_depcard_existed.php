<?php

	include_once "db_connect.php";
	
	$min_leid = getFirstItem("
		
		select
			var_value
		from
			vars
		where
			var_name = 'min_depcard_leid'
	
	");
	
	$min_leid = $min_leid*1;
	
	$sql = "
	
		select
			*
		from
			lawful_employees
		where
			le_from_oracle = 0
			and
			le_id < $min_leid
			and
			le_year in (2023,2022)
			
		order by
			le_id desc
		limit
			0,25
	
	";
	
	echo $sql;
	
	//echo $sql; exit();
	$the_result = mysql_query($sql);
	
	class DesPersonRequest {
			public $username;
			public $password;
			public $person_code;
		};
	
	while ($the_row = mysql_fetch_array($the_result)) {    
		
		//print_r($the_row);
		
		echo "<br><br>checking - leid: " . $the_row[le_id];
		echo "<br>le_code: " . $the_row[le_code];
		
		$have_record_in_oracle = 0;
		
		
		$req = new DesPersonRequest();
		$req->username = 'jobdepgoth';
		$req->password = ']y[l6fpvf';
		$req->person_code = $the_row[le_code];
		
		$ws = new SoapClient("http://161.82.250.36/ws/services.php?func=01",array("trace" => 1, "exception" => 0));
		$result = $ws->getDesPerson($req);

		
		
		if($result->return_code != 0){
			//echo "Error: ".$result->return_message;
			//exit;
		}
		
		
		$dat = $result->maimad_details->maimad;
			
		if($dat->first_name_thai){
			$have_record_in_oracle = 1;	
		}
		
		
		
		echo "<br>have_record_in_oracle: " . $have_record_in_oracle;
		
		if($have_record_in_oracle){ // $have_record_in_oracle are from scrp_check_mn_des_person.php
	
			$sql = "
			
				update
					lawful_employees
				set
					le_from_oracle = 1
				where
					le_from_oracle = 0
					and
					le_id = '".($the_row[le_id]*1)."'
				limit
					1
			
			";
			
			mysql_query($sql);
			
		}
		
		
		//mark minimum leid
		$min_leid = $the_row[le_id];
	
	}
	
	
	mysql_query("
		
		update
			vars
		set
			var_value = '$min_leid'
		where
			var_name = 'min_depcard_leid'
	");
	
	echo "<br>----";