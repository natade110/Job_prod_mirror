<?php

require_once 'PHPMailerAutoload.php';

function doSendMail($to_who, $the_subject, $the_body){
	
	$mail = new PHPMailer;
	
	$mail->Username = "noreply.nep.go.th@gmail.com";
	$mail->Password = "n0r3p1y@nep";
	
	/*
	$mail->Username = "mailadmin@chinapreorder.com";
	$mail->Password = "@aE6SZ9";*/
	
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = "tls";
	$mail->Host = "smtp.gmail.com";
	//$mail->Host = "mail.chinapreorder.com";
	$mail->Mailer = "smtp";
	$mail->Port = 587;
	//$mail->Port = 25;
	
	$mail->setFrom('noreply.nep.go.th@gmail.com', 'NEP Admin');
	//$mail->setFrom('mailadmin@chinapreorder.com', 'ChinaPreorder Admin');
	
	
	$mail->addAddress($to_who);
	             
	$mail->CharSet  = 'UTF-8';
	$mail->Subject = $the_subject;	
	$mail->msgHTML($the_body);
	
	if(!$mail->Send())
	{
	   echo "Message could not be sent. <p>";
	   echo "Mailer Error: " . $mail->ErrorInfo;
	   //exit;
	}
	
}

//common set up
//set years for ddl_years
$dll_year_start = 2011;

//user 6 and 7 is gov only
if($sess_accesslevel == "3"){
	//yoes 20151228 -- allow PMJ to see all years
	//$dll_year_start = 2013;
	$dll_year_start = 2011;
}

if($sess_accesslevel == "6" || $sess_accesslevel == "7"){
	$dll_year_start = 2012;
	$is_2013 = 1;
}


if($sess_accesslevel == "6" || $sess_accesslevel == "7"){
	
	$the_company_word = "หน่วยงานภาครัฐ";
	$the_code_word = "เลขที่บัญชีหน่วยงาน";
	
	$the_employees_word = "ผู้ปฏิบัติงาน";
	
	
}else{
	
	$the_company_word = "สถานประกอบการ";
	$the_code_word = "เลขที่บัญชีนายจ้าง";
	
	$the_employees_word = "ลูกจ้าง";
	
}


//simple function to get ofirst result from query
function getFirstItem($sql){
	if ($result = mysql_query($sql)) {
		$row    = mysql_fetch_row($result);
		return $row[0];
	}
}

function getFirstRow($sql){
	$result = mysql_query($sql);
	$row    = mysql_fetch_array($result);
	return $row;
}

function getResult($sql){
	$unit_result = mysql_query($sql);
	return $unit_result;
}

function default_value($input_value, $default_value){

	//function to see if input value is valid
	//if not then return defaul value
	if($input_value=="" || strlen($input_value)==0){
		return $default_value;
	}else{
		return $input_value;
	}

}

//function to escape html/sqls of input string and return that
function doCleanInput($input_string){
	return htmlspecialchars(mysql_real_escape_string($input_string));
}

function doCleanOutput($output_string){
	//return (htmlspecialchars_decode($output_string));
	return (stripslashes($output_string));
}

//yoes 31 aug
//html output to show outside text aread
function doCleanHtmlOutput($output_string){
	//return nl2br(htmlspecialchars_decode($output_string));
	return nl2br(stripslashes($output_string));
}

function addLeadingZeros($ref_to_show,$length){
	$new_ref_to_show = $ref_to_show;
	while(strlen($new_ref_to_show) < $length){
		$new_ref_to_show = "0".$new_ref_to_show ;
	}
	return $new_ref_to_show;
}

function validate_image($image_size, $max_size , $image_type){
	//get image size and type
	//return 1 if image is valid 
	//2 if image is too large
	//3 if invalid type
	if($image_size > $max_size ){
		//image is too large		
		return 2;		
	}elseif( $image_type != "image/pjpeg" && $image_type != "image/jpeg" &&  $image_type != "image/gif"  && $image_type != "application/x-shockwave-flash" && $image_type != "application/shockwave-flash") {
		//invalid type
		return 3;
	}else{
		return 1;
	}
	//&&  $image_type != "image/png" &&  $image_type != "image/x-png"
}


function pageLimitSQL($per_page, $curr_page){	
	$start_interval = $per_page * ($curr_page-1);	
	return " LIMIT $start_interval, $per_page";
}

function getNumberOfPost($this_ip, $this_date){
	$query = "select count(*) from history 
						where his_ip = '$this_ip' 
						and his_date between '".  $this_date ." 00:00:00' and '".  $this_date ." 23:59:59'";
	//echo $query;
	return getFirstItem($query);
}

function getGenderText($gender_id){

	//0 - unknow, 1=male, 2=female
	if($gender_id == "1"){
		return "Male";
	}else if($gender_id == "2"){
		return "Female";
	}else{
		return "Not Specified";
	}
	
}

function formatDate($date_time){

	return date("d M Y", strtotime($date_time));

}

function formatGender($what){

	if($what == "m"){
		return "ชาย";
	}elseif($what == "f"){
		return "หญิง";
	}else{
		return "---";
	}

}

function formatCuratorType($what){

	if($what == "1"){
		return "คนพิการ";
	}elseif($what == "0"){
		return "ผู้ดูแลคนพิการ";
	}else{
		return "---";
	}

}

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
		$date_thai = $this_selected_day . "" . $month_to_show . "" . ($this_selected_year+543);
	}else{
		$date_thai = $this_selected_day . " " . $month_to_show . " " . ($this_selected_year+543);
	}
	
	
	//yoes 20151021
	if($show_time){
		$date_thai .= " ".date("H:i:s", strtotime($date_time));
	}

	return $date_thai;

}



function formatDateThaiShort($date_time, $have_space = 1, $show_time = 0){

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
		$month_to_show = "ม.ค.";
	}elseif($this_selected_month == "02"){
		$month_to_show = "ก.พ.";
	}elseif($this_selected_month == "03"){
		$month_to_show = "มี.ค.";
	}elseif($this_selected_month == "04"){
		$month_to_show = "เม.ย.";
	}elseif($this_selected_month == "05"){
		$month_to_show = "พ.ค.";
	}elseif($this_selected_month == "06"){
		$month_to_show = "มิ.ย.";
	}elseif($this_selected_month == "07"){
		$month_to_show = "ก.ค.";
	}elseif($this_selected_month == "08"){
		$month_to_show = "ส.ค.";
	}elseif($this_selected_month == "09"){
		$month_to_show = "ก.ย.";
	}elseif($this_selected_month == "10"){
		$month_to_show = "ต.ค.";
	}elseif($this_selected_month == "11"){
		$month_to_show = "พ.ย.";
	}elseif($this_selected_month == "12"){
		$month_to_show = "ธ.ค.";
	}
	
	if($have_space == "0"){
		$date_thai = $this_selected_day . "" . $month_to_show . "" . ($this_selected_year+543);
	}else{
		$date_thai = $this_selected_day . " " . $month_to_show . " " . ($this_selected_year+543);
	}
	
	
	//yoes 20151021
	if($show_time){
		$date_thai .= " ".date("H:i:s", strtotime($date_time));
	}

	return $date_thai;

}

function formatYear($year){

	return $year+543;

}

function formatInputDate($date_time){

	return date("Y-m-d", strtotime($date_time));

}

function generateInsertSQL($post_array,$table_name,$input_fields,$special_fields,$special_values,$insert_word = "insert"){
	//build the sql based on input fields
	$the_sql = "$insert_word into $table_name(";	
	$first_row_done = 0;
	$first_value_done = 0;	
					
	for($i = 0; $i < count($input_fields); $i++){
		if($first_row_done ==1){$the_sql .= ",";}
		$the_sql .= "".$input_fields[$i]."";
		$first_row_done = 1;
		
	}		
	
	//any special fields goes here
	for($i = 0; $i < count($special_fields); $i++){
		if($first_row_done ==1){$the_sql .= ",";}
		$the_sql .= "".$special_fields[$i]."";
		$first_row_done = 1;
	}	
						
	$the_sql .=	")values(
					";
	
	for($i = 0; $i < count($input_fields); $i++){
		//clean all inputs
		if($first_value_done ==1){$the_sql .= ",";}
		$the_sql .= "'".doCleanInput($post_array["$input_fields[$i]"])."'";
		$first_value_done = 1;
	}
					
	//any special fields goes here
	for($i = 0; $i < count($special_values); $i++){
		if($first_value_done ==1){$the_sql .= ",";}
		$the_sql .= "".$special_values[$i].""; //noted that special values didn't have "'" so you can use sql NOW() functions and the likes
		$first_value_done = 1;
	}	
	
	$the_sql .=	")";
	
	return $the_sql;
}


//
function generateCheckRowExistedSQL($post_array,$table_name,$input_fields,$special_fields,$special_values, $condition_sql){
	//build the sql based on input fields
	$the_sql = "select count(*) from  $table_name where ";	
	
	$first_row_done = 0;
						
	for($i = 0; $i < count($input_fields); $i++){
		if($first_row_done ==1){$the_sql .= " and ";}
		$the_sql .= "".$input_fields[$i]."="."'".doCleanInput($post_array["$input_fields[$i]"])."'";
		$first_row_done = 1;
	}		
	
	//any special fields goes here
	for($i = 0; $i < count($special_fields); $i++){
		if($first_row_done ==1){$the_sql .= " and ";}
		$the_sql .= "".$special_fields[$i]."="."".$special_values[$i].""; //noted that special values didn't have "'" so you can use sql NOW() functions and the likes
		$first_row_done = 1;
	}	
						
	$the_sql .= $condition_sql;
	
	return $the_sql;
}



function generateUpdateSQL($post_array,$table_name,$input_fields,$special_fields,$special_values, $condition_sql){
	//build the sql based on input fields
	$the_sql = "update  $table_name set ";	
	
	$first_row_done = 0;
						
	for($i = 0; $i < count($input_fields); $i++){
		if($first_row_done ==1){$the_sql .= ",";}
		$the_sql .= "".$input_fields[$i]."="."'".doCleanInput($post_array["$input_fields[$i]"])."'";
		$first_row_done = 1;
	}		
	
	//any special fields goes here
	for($i = 0; $i < count($special_fields); $i++){
		if($first_row_done ==1){$the_sql .= ",";}
		$the_sql .= "".$special_fields[$i]."="."".$special_values[$i].""; //noted that special values didn't have "'" so you can use sql NOW() functions and the likes
		$first_row_done = 1;
	}	
						
	$the_sql .= $condition_sql;
	
	return $the_sql;
}

function getLawfulImage($what){
	//echo $what;
	if($what == 0){
		return "<img src='decors/red.gif' border='0' alt='ไม่ทำตามกฏหมาย' title='ไม่ทำตามกฏหมาย' />";
	}elseif($what == 1){
		return "<img src='decors/green.gif' border='0' alt='ทำตามกฏหมาย' title='ทำตามกฏหมาย'/>";
	}elseif($what == 2){
		return "<img src='decors/yellow.gif' border='0' alt='กำลังดำเนินงาน' title='กำลังดำเนินงาน'/>";
	}elseif($what == 3){
		return "<img src='decors/blue.gif' border='0' alt='ไม่เข้าข่ายจำนวนลูกจ้าง' title='ไม่เข้าข่ายจำนวนลูกจ้าง' />";
	}else{
		//default to unlawful
		return "<img src='decors/red.gif' border='0' alt='ไม่ทำตามกฏหมาย' title='ไม่ทำตามกฏหมาย' />";
	}

}


function getLawfulText($what){
	//echo $what;
	if($what == 0){
		return "ไม่ทำตามกฏหมาย";
	}elseif($what == 1){
		return "ทำตามกฏหมาย";
	}elseif($what == 2){
		return "กำลังดำเนินงาน";
	}elseif($what == 3){
		return "ไม่เข้าข่ายจำนวนลูกจ้าง";
	}else{
		//default to unlawful
		return "ไม่ทำตามกฏหมาย";
	}

}


function getMailAlertText($what){
	//echo $what;
	if($what == 0){
		return "ยังไม่ปฏิบัติตามกฏหมาย";
	}elseif($what == 1){
		return "พบข้อมูลลูกจ้างซ้ำซ้อน";
	}elseif($what == 2){
		return "ปฏิบัติตามกฏหมายแล้ว";
	}elseif($what == 3){
		return "ปฏิบัติตามกฏหมายแต่ไม่ครบอัตราส่วน";
	}else{
		//default to unlawful
		return "-- error --";
	}

}


function getMailStatusText($what){
	//echo $what;
	if($what == 0){
		return "<span style='color:#900000'>เตรียมส่ง email</span>";
	}elseif($what == 1){
		return "<span style='color:#009933'>ส่ง email แล้ว</span>";
	}else{
		//default to unlawful
		return "-- error --";
	}

}

function getCompanyStatusText($what){
	//echo $what;
	if($what == 0){
		return "ปิดกิจการ";
	}elseif($what == 1){
		return "เปิด";
	}elseif($what == 2){
		return "ย้าย";
	}else{
		//default to unlawful
		return "-- error --";
	}

}

//yoes 20151021
function getUserEnabledText($what){
	//echo $what;
	if($what == 0){
		return "<font color='#009900'>รอเปิดใช้งาน</font>";
	}elseif($what == 1){
		return "เปิดให้ใช้งาน";
	}elseif($what == 2){
		return "<font color='#CC0000'>ไม่อนุญาตให้ใช้งาน</font>";
	}else{
		return "<font color='#FF00FF'>!--unknown--!</font>";
	}

}


function formatAccessLevel($what){

	if($what == 1){
		return "ผู้ดูแลระบบ";
	}elseif($what == 2){
		return "เจ้าหน้าที่ พก.";
	}elseif($what == 3){
		return "เจ้าหน้าที่ พมจ.";
	}elseif($what == 4){
		return "เจ้าหน้าที่สถานประกอบการ";
	}elseif($what == 5){
		return "ผู้บริหาร";
	}elseif($what == 6){
		return "ผู้ดูแลระบบ สศส.";
	}elseif($what == 7){
		return "เจ้าหน้าที่ สศส.";
	}elseif($what == 8){
		return "เจ้าหน้าที่งานคดี";
	}else{
		return "!--unknown--!";
	}

}

function formatEducationLevel($what){

	if($what == 1){
		return "ไม่มีการศึกษา";
	}elseif($what == 2){
		return "ประถม";
	}elseif($what == 3){
		return "มัธยมต้น";
	}elseif($what == 4){
		return "มัธยมปลาย";
	}elseif($what == 5){
		return "ปวส ปวช";
	}elseif($what == 6){
		return "อนุปริญญา";
	}elseif($what == 7){
		return "ปริญญาตรี";
	}elseif($what == 8){
		return "ปริญญาโท";
	}elseif($what == 9){
		return "ปริญญาเอก";
	}elseif($what == 10){
		return "อื่นๆ";
	}else{
		return $what;
	}

}


function formatPositionGroup($what){
	
	$name = getFirstItem("select group_name from position_group where group_id = '$what'");
	//echo "select position_name from position_group where position_id = '$what'";

	if($name){
		return $name;
	}else{
		return $what;
	}

}



function getUserName($what){
	return getFirstItem("select user_name from users where user_id = '$what'");
}

function echoChecked($the_input){

	if($the_input == 1){
		echo 'checked="checked"';
	}

}

function formatCompanyName($company_name, $company_type){

	//also check for 'สาขา'
	$company_name_array = explode("สาขา", $company_name);
	
	$company_name = $company_name_array[0];
	$company_branch_name = $company_name_array[1];
	

	$company_type_name = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '$company_type'");
	
	if($company_type_name == "บริษัทจำกัด"){
		$formatted_name = "บริษัท ".$company_name . " จำกัด";
	}elseif($company_type_name == "อื่น ๆ"){
		$formatted_name = $company_name ;
	}elseif($company_type_name == "บริษัทจำกัด(มหาชน)"){
		$formatted_name = "บริษัท ".$company_name . " จำกัด(มหาชน)";
	}elseif($company_type_name == "หน่วยราชการ"){
		$formatted_name = $company_name;
	}elseif($company_type_name == "ห้างหุ้นส่วนจำกัด"){
		$formatted_name = "ห้างหุ้นส่วน ".$company_name . " จำกัด";
	}else{
		$formatted_name = $company_type_name . " " . $company_name;
	}
	
	if($company_branch_name){
		return $formatted_name . " สาขา" .$company_branch_name;
	}else{
		return $formatted_name;
	}

}

function formatPaymentName($payment_name){

	
	if($payment_name == "Cash"){
		return "เงินสด";
	}elseif($payment_name == "Cheque"){
		return "เช็ค";
	}elseif($payment_name == "Note"){
		return "ธนาณัติ";
	}else{
		return $payment_name ;
	}

}

function formatNumber($number){
	return number_format($number,2);
}

function formatNumberReport($number){
	if($number == "0"){
		return "-";
	}else{
		return number_format($number,2);
	}
}

function formatEmployee($number){
	return number_format($number);
}

function formatEmployeeReport($number){
	if($number == "0"){
		return "-";
	}else{
		return number_format($number);
	}
}


function formatMoney($number){
	return number_format($number,2,".",",");
	//return $number;
}

function formatMoneyReport($number){
	if($number == "0"){
		return "-";
	}else{
		return number_format($number,2,".",",");
	}
}

function deleteCommas($what){
	return str_replace(",","",$what);
}

function formatProvince($province_text){
	if($province_text == "กรุงเทพมหานคร"){
		return $province_text;
	}else{
		return "จ.".$province_text;
	}
}

function dateDiffTs($start_ts, $end_ts) {
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
}






//interest date is date since last payment, year_date is number of days within the year
function doGetInterests($interest_date,$owned_money,$year_date){

	//echo "$interest_date - $owned_money - $year_date";
	//echo "owned: ".$owned_money;	
	
	//yoes 20150326 - fix it so it will not calculate interest if owned money is NEGATIVE
	if($owned_money <= 0){
		return 0;
	}
		
		
	//$interest_money = round(($owned_money*7.5/100/$year_date*$interest_date), 2, PHP_ROUND_HALF_UP);
	$interest_money = round(($owned_money*7.5/100/$year_date*$interest_date), 2);
	//$interest_money = $owned_money*7.5/100/$year_date*$interest_date;
	
	return $interest_money;


}




//get interest date (x day from last payment date etc)
function getInterestDate($from_what_date, $this_lawful_year, $to_what_date){

	//every day that's less than 1 feb will have no interests	
	if(strtotime(date($to_what_date)) <= strtotime(date("$this_lawful_year-01-31"))){
		return 0;
	}
	
	

	//echo "actual_interest_date: ".$from_what_date; //strtotime(date("Y-m-d"))
	
	if($from_what_date && $from_what_date != '0000-00-00 00:00:00'){
	
		$interest_date = dateDiffTs(strtotime(date($from_what_date)), strtotime(date($to_what_date))) ;	//plus+1 because we also count ("last payment date")
	}else{
	
		$interest_date = dateDiffTs(strtotime(date("$this_lawful_year-01-31")), strtotime(date($to_what_date))) ;	 //plus+1 because we also count ("last payment date")
	}

	if($interest_date < 0){
		$interest_date = 0;
	}

	return $interest_date;

}



function getAddressText($lawful_row){

	$the_province_text = formatProvince(getFirstItem("select province_name from provinces where province_id = '".$lawful_row["Province"]."'"));		
	
	$address_to_use = $lawful_row["Address1"]." ".$lawful_row["Moo"]." ".$lawful_row["Soi"]." ".$lawful_row["Road"]." ".$lawful_row["Subdistrict"]." ".$lawful_row["District"]." ".$the_province_text." ".$lawful_row["Zip"];
	
	//yoes20140709 also remove ";"
	$address_to_use = str_replace(";",",",$address_to_use);
	
	return $address_to_use;

}


function getEmployeeRatio($employee_to_use,$ratio_to_use){

	$half_ratio_to_use = $half_ratio_to_use = $ratio_to_use/2;

	if(($employee_to_use/$ratio_to_use)>1 || $employee_to_use == $ratio_to_use){
	
		//see mod...
		$left_over = $employee_to_use%$ratio_to_use;
		
		if($left_over <= $half_ratio_to_use){
			$final_employee = (floor($employee_to_use/$ratio_to_use));
		}else{
			$final_employee = (ceil($employee_to_use/$ratio_to_use));
		}
	
		
	
	}else{
		$final_employee = "0";
	}
	
	return $final_employee;

}

function to_utf($what){

	return iconv("WINDOWS-874", "UTF-8",$what);

}


function getModType($what){

	if($what == 0){
		return "ข้อมูลสถานประกอบการ";
	}
	elseif($what == 1){
		return "ข้อมูลการปฏิบัติตามกฏหมาย";
	}
	elseif($what == 2){
		return "เพิ่มหรือแก้ไขข้อมูลคนพิการที่ได้รับเข้าทำงานมาตรา 33";
	}
	elseif($what == 3){
		return "ลบข้อมูลคนพิการที่ได้รับเข้าทำงานมาตรา 33";
	}
	elseif($what == 4){
		return "แก้ไขจำนวนลูกจ้างมาตรา 33";
	}
	elseif($what == 5){
		return "ลบข้อมูลผู้ใช้สิทธิมาตรา 35";
	}
	elseif($what == 6){
		return "ลบใบเสร็จรับเงินมาตรา 34";
	}
	elseif($what == 7){
		return "เพิ่มหรือแก้ไขใบเสร็จรับเงินมาตรา 34";
	}	
	elseif($what == 8){
		return "เพิ่มหรือแก้ไขข้อมูลผู้ใช้สิทธิมาตรา 35";
	}
	elseif($what == 20){
		return "ส่งข้อมูลชำระเงิน ม.33";
	}
	elseif($what == 21){
		return "ส่งข้อมูลชำระเงิน ม.35";
	}

	return "-- ไม่ระบุ --";

}

function getOrgModType($what){

	if($what == 1){
		return "สมัครใช้งานระบบ";
	}
	elseif($what == 2){
		return "Login เข้าใช้ระบบ";
	}
	elseif($what == 3 || $what == 4){
		return "Upload ไฟล์การปฏิบัติตามกฏหมาย";
	}
	elseif($what == 5){
		return "ลบไฟล์การปฏิบัติตามกฏหมาย";
	}
	elseif($what == 6){
		return "แก้ไขข้อมูลสถานประกอบการ";
	}

	return "-- ไม่ระบุ --";

}


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



function getWageUnit($what){

	if($what == 1){
	
		return "บาท/วัน";
		
	}elseif($what == 2){
	
		return "บาท/ชม.";
		
	}else{
	
		return "บาท/เดือน";
	}

}

function getThisYearRatio($the_year){
	$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_".$the_year."'"),100);	
	return $ratio_to_use;
}


function getThisYearWage($the_year, $the_province = 1){
	
	if($the_year == 2011){
		$wage_to_use = default_value(getFirstItem("select province_54_wage from provinces where province_id = '".$the_province."'"),300);	
	}else{
		$wage_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'wage_".$the_year."'"),300);	
	}
	
	return $wage_to_use;
}





///
//see if 1 or 2
function getCompanyInfo($the_cid, $cur_year){
	
	return getFirstItem("select 
							lawful_submitted
						from 
							lawfulness_company 
						where 
							CID = '$the_cid' 
							and 
							Year = '$cur_year' 
							and 
							
							(lawful_submitted = 1 or lawful_submitted = 2)
							
							");
	
}

//
// see if [1] - NEW only
function countCompanyInfo($the_cid, $cur_year){
	
	return getFirstItem("select 
							count(*)
						from 
							lawfulness_company 
						where 
							CID = '$the_cid' 
							and 
							Year = '$cur_year' 
							and 
							
							(lawful_submitted = 1)
							
							");
	
}

//yoes 20160125
//generic function
function doAddModifyHistory($the_userid,$the_cid,$the_type,$the_lid=""){
	
	$history_sql = "
				insert into 
					modify_history (
						mod_user_id
						, mod_cid
						, mod_date
						, mod_type
						, mod_lid
					)
				values
					(
						'$the_userid'
						,'$the_cid'
						,now()
						,'$the_type'
						,'$the_lid'	
					)
					
					";
	mysql_query($history_sql);
	
}



//yoes 20160113 ---> functions for doing full-logs
function doCompanyFullLog($the_sess_userid, $the_id, $the_source){
	
	$log_sql = "
				insert into 
					company_full_log
				select
					*
					, now()
					, '$the_sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, '$the_source'
					, ''
				from
					company
				where
					cid = '$the_id'
				";
	
	///echo $log_sql; exit();
	mysql_query($log_sql) or die(mysql_error());
	
}


//yoes 20160104 -- function to do lawfulness log
function doLawfulnessFullLog($the_sess_userid, $the_lawful_id, $the_source){
	
	$log_sql = "
				insert into 
					lawfulness_full_log
				select
					*
					, now()
					, '$the_sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, '$the_source'
					, ''
				from
					lawfulness
				where
					lid = '$the_lawful_id'
				";

	//echo $log_sql; exit();
	mysql_query($log_sql) or die(mysql_error());
	
}


function doLawfulEmployeesFullLog($the_sess_userid, $the_id, $the_source){
	
	$log_sql = "
				insert into 
					lawful_employees_full_log
				select
					*
					, now()
					, '$the_sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, '$the_source'
					, ''
				from
					lawful_employees
				where
					le_id = '$the_id'
				";
	
	///echo $log_sql; exit();
	mysql_query($log_sql);
	
}



function doCuratorFullLog($the_sess_userid, $the_id, $the_source, $is_child){
	
	$key_name = "curator_id";
	
	if($is_child){
		$key_name = "curator_parent";
	}
	
	$log_sql = "
				insert into 
					curator_full_log
				select
					*
					, now()
					, '$the_sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, '$the_source'
					, ''
				from
					curator
				where
					$key_name = '$the_id'
				";
	
	///echo $log_sql; exit();
	mysql_query($log_sql);
	
}


//

function doUploadOrgLog($the_sess_userid, $the_event, $the_file_name){
	
	
	
	$log_sql = "
				insert into 
					upload_org_log(
						upload_date
						, upload_event
						, upload_by
						, upload_file
					)
				values(
					
						now()
						, '$the_event'
						, '$the_sess_userid'
						, '$the_file_name'
					
					)
				";
	
	///echo $log_sql; exit();
	mysql_query($log_sql);
	
}

//error_reporting(1);

//yoes 20160208 -- new function here
function resetLawfulnessByLID($the_lid){
	
	//get lawfulness's details
	$lawfulness_row = getFirstRow("select * from lawfulness where lid = '$the_lid'");
	
	
	//basic values
	$lawfulness_year = $lawfulness_row[Year];	
	$lawfulness_employees = $lawfulness_row[Employees];
	
	//calc value
	$lawfulness_ratio = getThisYearRatio($lawfulness_year);
	$need_for_lawful = getEmployeeRatio($lawfulness_employees,$lawfulness_ratio);
	
	$cid_province = getFirstItem("select province from company where cid = '".$lawfulness_row[CID]."'");
	
	//to calculate lawfulness we need data for 33, 34, 35
	
	//also re-sync m33 here just in case
	$hire_numofemp = getFirstItem("
									SELECT 
										count(*)
									FROM 
										lawful_employees
									where
										le_cid = '".$lawfulness_row[CID]."'
										and le_year = '".$lawfulness_year."'");
	
	$the_sql = "update 
						lawfulness 
					set 
						Hire_NumofEmp = '$hire_numofemp' 
					where 
						lid = '$the_lid'";	
						
	mysql_query($the_sql);			
						
						
	//set the 33 numbers to whatever we got from last time			
	$the_33 = $hire_numofemp;
			
	$the_35_sql = "
		
			select
				count(*)
			from
				curator
			where
				curator_lid = '$the_lid'
				and
				curator_parent = 0
	
			";
	
	$the_35 = getFirstItem($the_35_sql);
	
	
	
	
	$lid_to_get_34 = $the_lid; //lid
	$employees_ratio = $need_for_lawful - $the_33 -$the_35; //ต้องรับคนกี่คน // yoes 20160227 -- fix this so it account for 33 35
	
	if($employees_ratio < 0){
		$employees_ratio = 0;	
	}
	
	$year_date = 365; //days in years
	$the_wage = getThisYearWage($lawfulness_year, $cid_province); //ค่าจ้างประขำปี
	$this_lawful_year = $this_lawful_year; //ปีนี้ปีอะไร (ปีของ lid)
	$the_province = $cid_province; //province อะไร
	
	
	//special script here (same with the one used in reports)
	include_once "scrp_get_34_from_lid.php";
	
	$the_34 = $maimad_paid;
	
	//echo $the_34; exit();
	
	
	$pending_maimad = $need_for_lawful -$the_33 -$the_34 -$the_35 ;
	
	//echo $pending_maimad; exit();
	
	//update this lawfulness accordingly
	//yoes 20160226 --> fix this to account for "less than" ratio case
	if($lawfulness_employees < $lawfulness_ratio){
		$lawful_status = 3;	
	}elseif($pending_maimad <= 0){
		$lawful_status = 1;	
	}elseif($pending_maimad == $need_for_lawful){
		$lawful_status = 0;
	}
	
	
	
	
	if($the_33){
		$hire_status = 1;
		if($lawful_status == 0){
			$lawful_status = 2;	//do something
		}
	}else{
		$hire_status = 0;
	}
	if($the_34 || $have_some_34){ //$have_some_34 is from scrp_get_34_from_lid.php
		$pay_status = 1;
		if($lawful_status == 0){
			$lawful_status = 2;	//do something
		}
	}else{
		$pay_status = 0;
	}
	if($the_35){
		$conc_status = 1;
		if($lawful_status == 0){
			$lawful_status = 2;	//do something
		}
	}else{
		$conc_status = 0;
	}
	
	
	//echo $lawful_status; exit();
	
	
	
	$sql = "
	
		update
			lawfulness
		set
			LawfulStatus = '$lawful_status'
			, Hire_status = '$hire_status'
			, pay_status = '$pay_status'
			, Conc_status = '$conc_status'
		where
			lid = '$the_lid'
	
	";
	
	//echo $sql;
	
	mysql_query($sql);
	
	/*
	//debug information here
	echo "<br>lawfulness_employees: ".$lawfulness_employees;
	echo "<br>lawfulness_ratio: ".$lawfulness_ratio;
	echo "<br>need_for_lawful: ".$need_for_lawful;
	echo "<br>the_33: ".$the_33;
	echo "<br>the_34: ".$the_34;
	echo "<br>the_35: ".$the_35;
	
	*/
}

function  convertThaiDateToSqlFormat($thai_date){
	$str_thai_date = $thai_date;
	$sql_date = null;

	if($str_thai_date != ""){
		$arr_date = explode(" ",$str_thai_date);
		
		if ($arr_date != null){
			// day
			$day = $arr_date[0];
			
			// map month
			$month = $arr_date[1];
			$mountReturn = "";
			switch ($month){
				case "มกราคม" : $mountReturn = "01"; break;
				case "กุมภาพันธ์" : $mountReturn = "02"; break;
				case "มีนาคม" : $mountReturn = "03"; break;
				case "เมษายน" : $mountReturn = "04"; break;
				case "พฤษภาคม" : $mountReturn = "05"; break;
				case "มิถุนายน" : $mountReturn = "06"; break;
				case "กรกฏาคม" : $mountReturn = "07"; break;
				case "สิงหาคม" : $mountReturn = "08"; break;
				case "กันยายน" : $mountReturn = "09"; break;
				case "ตุลาคม" : $mountReturn = "10"; break;
				case "พฤศจิกายน" : $mountReturn = "11"; break;
				case "ธันวาคม" : $mountReturn = "12"; break;
			}
			
			// year
			$year = is_numeric($arr_date[2]) ? (intval($arr_date[2]) - 543) : "";
			
			if($year != null && $mountReturn != null && $day != null){
				$sql_date = $year."-".$mountReturn."-".$day." 00:00:00";
			}
		}
	}
	
	return $sql_date;
}
//yoes 2016029
function doGetLogSourceName($what){

	//company
	if($what == "scrp_update_org.php"){
		return "ปรับปรุงข้อมูลสถานประกอบการ ";
	}
	if($what == "upload_import_org.php"){
		return "อัพโหลดไฟล์จากประกันสังคม ";
	}
	//lawfulness
	if($what == "scrp_update_lawful_employees.php"){
		return "ปรับปรุงข้อมูลลูกจ้างมาตรา 33 ";
	}
	if($what == "scrp_update_org_lawful_stat.php"){
		return "ปรับปรุงข้อมูลการปฏิบัติตามกฏหมาย ";
	}
	if($what == "scrp_delete_lawful_employee.php"){
		return "ลบข้อมูลมาตรา 33 ";
	}
	if($what == "scrp_update_org.php"){
		return "ปรับปรุงข้อมูลสถานประกอบการ ";
	}
	if($what == "upload_import_org.php"){
		return "อัพโหลดไฟล์จากประกันสังคม ";
	}
	//33
	if($what == "scrp_add_lawful_employee.php"){
		return "ปรับปรุงข้อมูลลูกจ้างมาตรา 33 ";
	}
	if($what == "scrp_delete_lawful_employee.php"){
		return "ลบข้อมูลลูกจ้างมาตรา 33 ";
	}
	//35
	if($what == "organization.php"){
		return "ปรับปรุงข้อมูลมาตรา 35 ";
	}
	if($what == "scrp_delete_curator_new.php"){
		return "ลบข้อมูลมาตรา 35 ";
	}
	
	return $what;	
}

?>