<?php
	require "db_connect.php";
	
	$table_name = "schedulecollection";
	$str_field = "";
	$str_value = "";
	
	$get_schedule = mysql_query("select * from $table_name order by SID desc limit 1");
	if($get_schedule != null){
		$post_row = mysql_fetch_array($get_schedule);
	}
	
	for ($i = 1;$i < 5;$i++){
		$day = $_POST["day_$i"];
		$month = $_POST["month_$i"];
		$dateValue = null;
		
		if($day != "00" && $month != "00"){
			$dateValue = $month.$day;
			$field_sent = "SentNo".$i;
			$str_field = $str_field.", ".$field_sent;
			$str_value = $str_value.", '".$dateValue."'";
		}
	}
	
	$str_field = substr($str_field, 1);
	$str_value = substr($str_value, 1);
	$endYear = (is_numeric($_POST["endyear"]))? $_POST["endyear"] : "NULL";
	
	if($post_row != null){ 
		//case update
		$str_field = $str_field.",BeginYear,EndYear,ModifiedDate,ModifiedBy";
		$str_value = $str_value.",".$_POST["beginyear"].",".$endYear.",Now(),$sess_userid";
		
		$arr_field = explode(",",$str_field);
		$arr_value = explode(",",$str_value);

		$q = "";
		for ($r = 0;$r < count($arr_field); $r++){
			$q .= ",".$arr_field[$r]." = ".$arr_value[$r];
		}
		
		$q = substr($q, 1);
		$sql = "update ".$table_name." set ".$q." where SID=".$post_row['SID'];
		mysql_query($sql);
	}else { 
		//case create
		$str_field = $str_field.",BeginYear,EndYear,CreatedDate,CreatedBy";
		$str_value = $str_value.",".$_POST["beginyear"].",".$endYear.",Now(),$sess_userid";
		$sql = "insert into ".$table_name." (".$str_field.") values (".$str_value.")";
		mysql_query($sql);
	}
	
	header("location: config_sending_letter.php?issuccess=true");
?>