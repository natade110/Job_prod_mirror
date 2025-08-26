<?php



	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";


	$the_id = "5200501031625";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}

	$the_id = addslashes(substr($the_id,0,13));

	$the_count = 0;

	function formatDateThai($date_time, $have_space = 1, $show_time = 0){

		if(!$date_time){
			return "";	
		}

		if($date_time != "0000-00-00"){
		   $this_selected_year = date("Y", strtotime($date_time));
		   $this_selected_month = date("m", strtotime($date_time));
		   $this_selected_day = date("d", strtotime($date_time));
	   }else{
		   $this_selected_year = 0;
		   $this_selected_month = 0;
		   $this_selected_day = 0;
	   }
		
		//$month_to_show = $this_selected_month;
		
		if($this_selected_month == "01"){
			$month_to_show = "มกราคม";
		}elseif($this_selected_month == "02"){
			$month_to_show = "กุมภาพันธ์";
		}elseif($this_selected_month == "03"){
			$month_to_show = "มีนาคม";
		}elseif($this_selected_month == "04"){
			$month_to_show = "เมษายน";
		}elseif($this_selected_month == "05"){
			$month_to_show = "พฤษภาคม";
		}elseif($this_selected_month == "06"){
			$month_to_show = "มิถุนายน";
		}elseif($this_selected_month == "07"){
			$month_to_show = "กรกฎาคม";
		}elseif($this_selected_month == "08"){
			$month_to_show = "สิงหาคม";
		}elseif($this_selected_month == "09"){
			$month_to_show = "กันยายน";
		}elseif($this_selected_month == "10"){
			$month_to_show = "ตุลาคม";
		}elseif($this_selected_month == "11"){
			$month_to_show = "พฤศจิกายน";
		}elseif($this_selected_month == "12"){
			$month_to_show = "ธันวาคม";
		}
		
		if($have_space == "0"){
			$date_thai = $this_selected_day . "" . $month_to_show . "" . ($this_selected_year);
		}else{
			$date_thai = $this_selected_day . " " . $month_to_show . " " . ($this_selected_year);
		}
		
		
		//yoes 20151021
		if($show_time){
			$date_thai .= " ".date("H:i:s", strtotime($date_time));
		}

		return $date_thai;

	}

	//calculate years of age (input string: YYYY-MM-DD)
	function birthday ($birthday){
		//yoes 20171221 -> change this to comply with SQL's data
		
		//list($day,$month,$year) = explode("-",$birthday);

		$year = substr($birthday,0,4);
		//return $year;
		$month = substr($birthday,4,2);
		//return $month;
		$day = substr($birthday,6,2);
		
		
		
		//$year = $year  - 543;
		//echo $year;
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if ($day_diff < 0 || $month_diff < 0)
		  $year_diff--;
		return $year_diff;
	}


	//yoes 20150923 -> use webservice instead
	/*
	$url = "http://203.155.46.29/ws/wsjson?user=test&password=test123&queryCode=HIRE01&CARD_ID=$the_id";
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 4);
	$json = curl_exec($ch);
	if(!$json) {
		//echo curl_error($ch);
		echo "json_error";
		exit();
	}
	curl_close($ch);
	*/

	//dang 20171128 Swithc to SOAP Request
	//
	class DesPersonRequest {
		public $username;
		public $password;
		public $person_code;
	};

	$req = new DesPersonRequest();
	$req->username = 'jobdepgoth';
	$req->password = ']y[l6fpvf';
	$req->person_code = $the_id;

	//$ws = new SoapClient("http://203.107.181.36/ws/services.php?func=01",array("trace" => 1, "exception" => 0));
	$ws = new SoapClient("http://161.82.250.36/ws/services.php?func=01",array("trace" => 1, "exception" => 0));
	$result = $ws->getDesPerson($req);
	
	//print_r($result);

	if($result->return_code != 0){
		echo "Error: ".$result->return_message;
		exit;
	}

	//
	$moomin_array = json_decode($json,true);

	//print_r($moomin_array["rows"]); exit();

	$output_array = $moomin_array["rows"][0];
	// --
	$dat = $result->maimad_details->maimad;
	
	//print_r($dat);
	
	//20201126 - yoes quick fix - incase the thing return more than one row
	if(is_array($dat)){
		$dat = $dat[0];
	}
	//$dat = $dat[0];
	//print_r($dat);
	
	$output_array = array();
	$output_array[FIRST_NAME_THAI]	=  $dat->first_name_thai;
	$output_array[LAST_NAME_THAI] = $dat->last_name_thai;
	$output_array[SEX_CODE] = $dat->sex_code;
	//$output_array[BIRTH_DATE] = birthday($dat->birth_date);
	$output_array[BIRTH_DATE] = $dat->birth_date;
	$output_array[DEFORM_ID] = $dat->deform_id;
	$output_array[PREFIX_NAME_ABBR] = $dat->prefix_name_abbr;
	$output_array[DEFORM_YEAR] = $dat->home_fax;
	
	
	$output_array[ISSUE_DATE] = $dat->curator_id;
	$output_array[EXP_DATE] = $dat->home_region_code;
	$output_array[PERMIT_DATE] = $dat->home_region_name;
	
	//yoes 20230109
	if($output_array[ISSUE_DATE]){
		
		$ISSUE_DATE_DESC = "วันที่ออกบัตร: ".formatDateThai($output_array[ISSUE_DATE])."<br>วันที่บัตรหมดอายุ: ".formatDateThai($output_array[EXP_DATE])."";
		
		if($output_array[ISSUE_DATE] != $output_array[PERMIT_DATE]){	
			$ISSUE_DATE_DESC .= "<br>(ออกบัตรครั้งแรกวันที่: ".formatDateThai($output_array[PERMIT_DATE]).")";
		}
		
		//$ISSUE_DATE_DESC .= "<br><font style=\"font-size: 12px; color: orangered\">** เจ้าหน้าที่ส่วนกลางฯเห็นเท่านั้น (ยังไม่ได้เปิดใช้งานทั่วประเทศ)</font>";
		
	}else{
		
		$ISSUE_DATE_DESC = "";
	}
	

	//print_r($output_array); exit();
	//echo "<br>".$output_array["FIRST_NAME_THAI"];


	$the_output = "someVar = {
					'FIRST_NAME_THAI' : '".$output_array[FIRST_NAME_THAI]."'
					,'LAST_NAME_THAI' : '".$output_array[LAST_NAME_THAI]."'
					,'SEX_CODE' : '".$output_array[SEX_CODE]."'
					,'BIRTH_DATE' : '".birthday($output_array[BIRTH_DATE])."'
					,'DEFORM_ID' : '".$output_array[DEFORM_ID]."'
					,'PREFIX_NAME_ABBR' : '".$output_array[PREFIX_NAME_ABBR]."'
					,'DEFORM_YEAR' : '".$output_array[DEFORM_YEAR]."'
					
					,'ISSUE_DATE' : '".$output_array[ISSUE_DATE]."'
					,'EXP_DATE' : '".$output_array[EXP_DATE]."'
					,'PERMIT_DATE' : '".$output_array[PERMIT_DATE]."'
					
					, 'ISSUE_DATE_DESC' : '$ISSUE_DATE_DESC'

					}";


	//if($the_count > 0){
	if(strlen($output_array[FIRST_NAME_THAI]) > 0){
		echo $the_output;
	}else{
		echo "no_result";
	}

?>
