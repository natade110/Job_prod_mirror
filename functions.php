<?php

require_once 'PHPMailerAutoload.php';

//require_once 'PHPMailer-master\PHPMailer.php';


require_once 'functions_new_law_33.php';
require_once 'functions_new_law_35.php';
require_once 'functions_new_law_3335.php';

//yoes 20200608
require_once "functions_3335_2020.php";


//yoes 20210521
function file_post_contents($url, $params) {		
	$content = http_build_query($params, '', '&');
	$header = array(
		"Content-Type: application/x-www-form-urlencoded",
		"Content-Length: ".strlen($content)
	);
	$options = array(
		'http' => array(
			'method' => 'POST',
			'content' => $content,
			'header' => implode("\r\n", $header)
		)
		
	);
	return file_get_contents($url, false, stream_context_create($options));
}

//yoes 20211029
//sync receipt meta with invoice meta
function syncInvoiceAndReceiptMeta($invoice_id, $the_rid){
	
	//get invoince metas
	$sql = "
		
		SELECT
			*
		from
			invoice_items
		where 
			invoice_id = '$invoice_id'
	
	";
	
	
	$ini_result = mysql_query($sql);

    while ($ini_row = mysql_fetch_array($ini_result)) {

       	//echo "<br>";
		//print_r($ini_row);
		
		if($ini_row[ini_type] == 33){
			
			
			//sync 33vspayment meta
			$sql = "
			
				replace into receipt_meta(
					
					meta_rid
					, meta_for
					, meta_value				
				
				)values(
				
					'$the_rid'
					, '".($ini_row[p_lid].$ini_row[p_from].$ini_row[p_to])."'
					, '".($ini_row[ini_amount])."'
				
				)
			
			";
			
			//echo "<br>$sql";			
			mysql_query($sql);			
			
		}elseif($ini_row[ini_type] == 35){
			
			
			//sync 33vspayment meta
			$sql = "
			
				replace into receipt_meta(
					
					meta_rid
					, meta_for
					, meta_value				
				
				)values(
				
					'$the_rid'
					, 'c".($ini_row[p_lid].$ini_row[p_from].$ini_row[p_to])."'
					, '".($ini_row[ini_amount])."'
				
				)
			
			";
			
			//echo "<br>$sql";			
			mysql_query($sql);			
			
		}

    }
	
	
}


//yoes 20210204
function curatorIs6MonthsTraining($curator_id){
	
	
	$le_row = getFirstRow("select * from 
								curator a 
									join
										lawfulness b
											on
											a.curator_lid = b.lid
								
								where 
									a.curator_id = '$curator_id'");
	
	if(
		trim($le_row[curator_event]) == "ฝึกงาน" 
		&&
		$le_row[Year] >= 2018 
		&& 
		$le_row[Year] <= 2500
	){
		
		//
		$curator_start_date = $le_row[curator_start_date];		
		//$curator_start_date = "2018-07-31";
		$curator_start_day = substr($curator_start_date, 8, 2) ;
		$curator_start_month = substr($curator_start_date, 5, 2) ;
		$curator_start_year = substr($curator_start_date, 0, 4) ;
		
		
		$curator_6_month_month_year = date('Y-m-01', strtotime("+6 months", strtotime($curator_start_year."-".$curator_start_month."-01")));
		$curator_6_month_month_year_last_day = date('t', strtotime($curator_6_month_month_year));
		$curator_6_month_day = $curator_start_day;
		if($curator_6_month_day > $curator_6_month_month_year_last_day){
			$curator_6_month_day = $curator_6_month_month_year_last_day;
		}
		
		
		$curator_6_month_for_compare = substr($curator_6_month_month_year,0,7)."-$curator_6_month_day";
		$curator_6_month_for_compare = date('Y-m-d', strtotime("-1 day", strtotime($curator_6_month_for_compare)));
		//$curator_6_month_for_compare .= " 00:00:00";
		
		$curator_end_date = $le_row[curator_end_date];
		$curator_end_day = substr($curator_end_date, 8, 2) ;
		$curator_end_month = substr($curator_end_date, 5, 2) ;
		$curator_end_year = substr($curator_end_date, 0, 4) ;
		
		
		
		if($le_row[curator_end_date] >= $curator_6_month_for_compare || $le_row[curator_end_date] == "0000-00-00"){
			$is_6_month_training = 1;
		}else{
			$is_6_month_training = 0;
		}
		
	}else{
	
		$is_6_month_training = 0;
	
	}
	
	return $is_6_month_training;
	
}


function getLidBetaStatus($the_lid){
	
	return getFirstItem("select 1 from lawfulness_meta where meta_lid = '$the_lid' and meta_for = 'is_beta_2020' and meta_value = 1 ");
	
}

function getCompany33ListSql($this_id, $this_lawful_year){
	
	
	return  "
																
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
			
				lawful_employees a
					left join
						lawful_employees_meta b
							on a.le_id = b.meta_leid and b.meta_for = 'child_of'
					left join
						lawful_employees_meta c
							on a.le_id = c.meta_value and c.meta_for = 'child_of'
							
					left join
						lawful_employees_meta d
							on a.le_id = d.meta_leid and d.meta_for = 'sso_failed'
			
			
			where
				le_cid = '$this_id'
				and le_year = '$this_lawful_year'
				
			order by 
				le_id asc ";
	
}

function getCompany33ListSqlEjob($this_id, $this_lawful_year){
	
	
	return  "
																
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
				le_cid = '$this_id'
				and le_year = '$this_lawful_year'
				
			order by 
				le_id asc ";
	
}


function getCompany35ListSql($this_lid){
	
	
	return  "
	
	
			SELECT 
				*
			FROM 
				curator
			where
				curator_lid = '$this_lid'
				and
				curator_parent = 0
				
				and
					curator_id not in (
					
						select
							meta_curator_id
						from
							curator_meta
						where
							meta_for = 'is_extra_35'
							and
							meta_value = 1
					
					)
				
			order by 
				curator_id asc ";
	
}


function getCompany35ListSqlEjob($this_lid){
	
	
	return  "
	
	
			SELECT 
				*
			FROM 
				curator_company
			where
				curator_lid = '$this_lid'
				and
				curator_parent = 0
				
				and
					curator_id not in (
					
						select
							meta_curator_id
						from
							curator_meta
						where
							meta_for = 'is_extra_35-es'
							and
							meta_value = 1
					
					)
				
			order by 
				curator_id asc ";
	
}

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
	$sort_col = array();
	foreach ($arr as $key=> $row) {
		$sort_col[$key] = $row[$col];
	}

	array_multisort($sort_col, $dir, $arr);
}/**/


function doSendMail($to_who, $the_subject, $the_body){
	
	
	//yoes 20230106
	//send mail via ejob
	//echo "$to_who"; return;
	
	$url = 'https://ejob.dep.go.th/ejob/hire_ws/postSendMail.php';
	$data = array(
	
				"mail_address" => $to_who
				
				//'mail_address' => "p.daruthep@gmail.com"
	
				,"the_header" => $the_subject
				,"the_body" => $the_body
					);

	// use key 'http' even if you send the request to https://...
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { /* Handle error */ }
	
	/*
	$mail = new PHPMailer;
	
	//yoes 20200422
	//https://app.asana.com/0/794303922168293/1172325879013145
	//$mail->Username = "noreply.nep.go.th@gmail.com";
	//$mail->Password = "n0r3p1y@nep";
	//$mail->Username = "dep_support@mgsolution.co.th";
	//$mail->Password = "Qwerty789!";
	$mail->Username = "itfund03@dep.go.th";
	$mail->Password = "Fund@it03";
	
	
	
	//$mail->SMTPAuth   = true;
	//$mail->SMTPSecure = "tls";
	//$mail->SMTPAutoTLS = false;
	//$mail->Host = "mail.mgsolution.co.th";	
	//$mail->Mailer = "smtp";	
	//$mail->Port = 25;
	
	
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = "ssl";	
	$mail->Host = "outgoing.mail.go.th";
	$mail->Mailer = "smtp";
	$mail->Port = 465;
	
	//$mail->setFrom('dep_support@mgsolution.co.th', 'กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ');
	$mail->setFrom('itfund03@dep.go.th', 'กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ');
	
	$mail->addAddress($to_who);
	             
	$mail->CharSet  = 'UTF-8';
	$mail->Subject = $the_subject;	
	$mail->msgHTML($the_body);
	
	if(!$mail->Send())
	{
	  // echo "Message could not be sent. <p>";
	   //echo "Mailer Error: " . $mail->ErrorInfo;
	   //exit;
	}
	*/
	
}



function sendMailByEmailId($email_id, $vars_array, $the_province = 0){



    $the_mail_row = getFirstRow("select * from email_templates where email_id = '$email_id'");

    $the_subject = $the_mail_row["email_subject"];

    $the_body = strtr($the_mail_row["email_body"], $vars_array);

    //echo  $the_body; exit();

    $to_who_sql = "
                   select
                        *
                   from
                      user_email a
                        JOIN 
                        users b
                          on a.user_id = b.AccessLevel
                    WHERE 
                      a.email_id = '$email_id'
                      AND 
                      a.mail_enabled = 1
                      AND 
                      b.user_email is not null and b.user_email != '' 
                      and
                      
                      (
                        b.AccessLevel in (1,5,4)
                                            
                    ";

    if($the_province){

        $to_who_sql .= "
                        
                        or
                        (
                          b.AccessLevel in (3)
                          and
                          user_meta in ($the_province)                        
                        )  
                        
                        ";

    }

    if($the_province == 10 || $email_id  == 3 || $email_id  == 4 || $email_id  == 5 ){

        $to_who_sql .= "                        
                        or
                        (
                          b.AccessLevel in (2)                                              
                        )
                        ";

    }

    $to_who_sql .= " )";

    $to_who_result = mysql_query($to_who_sql);

    while ($post_row = mysql_fetch_array($to_who_result)) {

        doSendMail($post_row["user_email"], $the_subject, $the_body);

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
	
	$sess_is_gov = 1;	
	
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

function doCleanInputC($connect, $input_string){
	return htmlspecialchars(mysqli_real_escape_string($connect, $input_string));
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


function formatInvoiceStatus($what){

	if($what == "1"){
		return "ยังไม่ได้จ่ายเงิน";
	}elseif($what == "2"){
		return "จ่ายเงินแล้ว";
	}elseif($what == "99"){
		return "การชำระเงินตามคำพิพากษา - ยังไม่ได้ชำระเงิน";
	}elseif($what == "98"){
		return "การชำระเงินตามคำพิพากษา - ชำระเงินแล้ว";
	}else{
		return "*ไม่ระบุ*";
	}

}

function formatMonthThai($this_selected_month){
	
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
	
	return $month_to_show;
	
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

function generateReplaceSQL($post_array,$table_name,$input_fields,$special_fields,$special_values, $condition_sql){
	//build the sql based on input fields
	$the_sql = "replace into $table_name";	
	
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
function getLawfulImage($what, $suffix = ""){
	
	//echo $what;
	if($what == 0){
		return "<img src='decors/red$suffix.gif' border='0' alt='ไม่ทำตามกฏหมาย' title='ไม่ทำตามกฏหมาย' />";
	}elseif($what == 1){
		return "<img src='decors/green$suffix.gif' border='0' alt='ทำตามกฏหมาย' title='ทำตามกฏหมาย'/>";
	}elseif($what == 2){
		return "<img src='decors/yellow$suffix.gif' border='0' alt='กำลังดำเนินงาน' title='กำลังดำเนินงาน'/>";
	}elseif($what == 3){
		return "<img src='decors/blue$suffix.gif' border='0' alt='ไม่เข้าข่ายจำนวนลูกจ้าง' title='ไม่เข้าข่ายจำนวนลูกจ้าง' />";
	}elseif($what == 5){
		return "<img src='decors/orange$suffix.gif' border='0' alt='นับว่าปฏิบัติตามกฎหมายเนื่องจากเข้าข่ายของคำวินิจฉัยกฤษฎีกา' title='นับว่าปฏิบัติตามกฎหมายเนื่องจากเข้าข่ายของคำวินิจฉัยกฤษฎีกา' />";
	//bank add status = 6 20230103
	}elseif($what == 6){
		return "<img src='decors/purple.jpg' border='0' alt='นับว่าปฏิบัติตามกฎหมายเนื่องจากการยุติการดำเนินคดีทางกฎหมาย' title='นับว่าปฏิบัติตามกฎหมายเนื่องจากการยุติการดำเนินคดีทางกฎหมาย' />";
	}else{
		//default to unlawful
		return "<img src='decors/red$suffix.gif' border='0' alt='ไม่ทำตามกฏหมาย' title='ไม่ทำตามกฏหมาย' />";
	}

}



function getLawfulImageFromLID($what){
	
	//yoes 20170103
	//also check "non-completed" items
	
	$sql = "select
				
				a.CID
				, LID
				, Year
				, LawfulStatus
				, count(le_id) as count_le_id
				, count(curator_id) as count_curator_id
			from
				company a
					join lawfulness b on
						a.CID = b.CID
					
					left join lawful_employees c on
						c.le_cid = a.cid
						and
						c.le_year = b.Year
						and
						c.le_is_dummy_row = 1
						
						
					left join curator d on
						d.curator_lid = b.lid
						and						
						d.curator_is_dummy_row = 1
				
			where
				LID = '$what'
				
			group by
				
				a.CID
				, LID
				, Year	
				
				";
				
	//echo $sql;
	
	$lawfulImageRow = getFirstRow($sql);
	
	if($lawfulImageRow[count_le_id] + $lawfulImageRow[count_curator_id]){
		
		echo getLawfulImage($lawfulImageRow[LawfulStatus], $suffix = "_grey");
	}else{
		
		echo getLawfulImage($lawfulImageRow[LawfulStatus], $suffix = "")	;
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
	}elseif($what == 6){
		return "การแจ้งแนบไฟล์ สปส 1-10 ส่วนที่ 2";
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
	}elseif($what == 9){
		return "<font color='#FF00FF'>รอยืนยัน email โดยผู้ใช้งาน</font>";
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
	
	if(is_numeric($what)){
	
		$name = getFirstItem("select group_name from position_group where group_id = '$what'");
		//echo "select position_name from position_group where position_id = '$what'";
				
	}

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

	//yoes 20210129
	if($company_name == "ไชน่าแอร์ไลน์ (สาขาประเทศไทย)"){
		return "บริษัท ". $company_name . " จำกัด";
	}

	//also check for 'สาขา'
	
	$company_name_array = explode("สาขา", $company_name);
	//print_r($company_name_array);
	
	
	$company_name = $company_name_array[0];
	$company_branch_name = $company_name_array[1];
	

	$company_type_name = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '$company_type'");
	
	if(!$company_type_name){
		
		return $company_name;
		
	}
	
	//return $company_type_name;
	
	if($company_type_name == "บริษัทจำกัด"){
		$formatted_name = "บริษัท ".$company_name . " จำกัด";
	}elseif($company_type_name == "อื่น ๆ"){
		$formatted_name = $company_name ;
	}elseif($company_type_name == "บริษัทจำกัด (มหาชน)"){
		$formatted_name = "บริษัท ".$company_name . " จำกัด (มหาชน)";
	}elseif($company_type_name == "หน่วยราชการ"){
		$formatted_name = $company_name;
	}elseif($company_type_name == "ห้างหุ้นส่วนจำกัด"){
		//$formatted_name = "ห้างหุ้นส่วน ".$company_name . " จำกัด";
		//yoes 20180629 -> change per champ's request
		$formatted_name = "ห้างหุ้นส่วนจำกัด ".$company_name;
	}elseif($company_type_name == "บริษัทธนาคารจำกัด (มหาชน)"){
		//yoes 20180629 -> change per champ's request
		$formatted_name = "บริษัทธนาคาร  ".$company_name. " จำกัด (มหาชน)";		
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
	}elseif($payment_name == "NET"){
		return "KTB Netbank";
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

function dateDiffTs($start_ts, $end_ts, $off_set = 0) {
	$diff = $end_ts - $start_ts;
	return round(($diff / 86400) + $off_set);
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



//yoes 20180131
function getDefaultLastPaymentDateByYear($the_year){

	if($the_year >= 2018 && $the_year < 2500){
		
		return "$the_year-03-31 00:00:00";
		
	}else{
		
		return "$the_year-01-31 00:00:00";
	}
	
}

function getThisYearInterestDate($this_lawful_year){
	
	if($this_lawful_year >= 2018){	
		$this_interest_date = "$this_lawful_year-03-31";	
	}else{	
		$this_interest_date = "$this_lawful_year-01-31";	
	}
	
	return $this_interest_date;
	
}


//get interest date (x day from last payment date etc)
function getInterestDate($from_what_date, $this_lawful_year, $to_what_date,$the_54_budget_date = 0){

	//yoes 20180130 --> for year 2018 ---> interest date starts at 1 April		
	$this_interest_date = getThisYearInterestDate($this_lawful_year);
	
	//every day that's less than 1 feb will have no interests	
	//yoes 20170415 -- account for custom budget date
	if(strtotime(date($to_what_date)) <= strtotime(date("$this_interest_date")) && !$the_54_budget_date){
		return 0;
	}
	
	

	//echo "actual_interest_date: ".$from_what_date; //strtotime(date("Y-m-d"))
	
	if($from_what_date && $from_what_date != '0000-00-00 00:00:00'){
	
		$interest_date = dateDiffTs(strtotime(date($from_what_date)), strtotime(date($to_what_date))) ;	//plus+1 because we also count ("last payment date")
	}else{
	
		$interest_date = dateDiffTs(strtotime(date("$this_interest_date")), strtotime(date($to_what_date))) ;	 //plus+1 because we also count ("last payment date")
	}
	
	//yoes 20170509
	//if it's 54 budget date then it should start at that day
	//so just add one more day to the date
	if($the_54_budget_date){
	
		$interest_date++;	
		
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



//yoes 20171221 --> add mssql BD
function sqlBirthday ($birthday){
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
							
							(
								lawful_submitted = 1
								
								or
								(
									lawful_submitted = 3
									and
									LID in (
										
										select
											meta_lid
										from
											lawfulness_meta
										where
											meta_for = 'es-resubmit'
											and
											meta_value = 1
									)
								
								)
								
							)
							
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

//yoes 20211213
function doUsersFullLog($the_sess_userid, $the_id, $the_source){
	
	$log_sql = "
				insert into 
					users_full_log
				select
					*
					, now()
					, '$the_sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, '$the_source'
					, ''
				from
					users
				where
					user_id = '$the_id'
				";
	
	///echo $log_sql; exit();
	mysql_query($log_sql) or die(mysql_error());
	
}


//20211212
function doLawfulnessCompanyFullLog($the_sess_userid, $the_lawful_id, $the_source){
	
	$log_sql = "
				insert into 
					lawfulness_company_full_log
				select
					*
					, now()
					, '$the_sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, '$the_source'
					, ''
				from
					lawfulness_company
				where
					lid = '$the_lawful_id'
				";

	//echo $log_sql; exit();
	mysql_query($log_sql) or die(mysql_error());
	
}

function doPaymentFullLog($the_sess_userid, $the_id, $the_source){
	
	$log_sql = "
				insert into 
					payment_full_log
				select
					*
					, now()
					, '$the_sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, '$the_source'
					, ''
				from
					payment
				where
					rid = '$the_id'
				";
	
	///echo $log_sql; exit();
	mysql_query($log_sql) or die(mysql_error());
	
}



function doReceiptFullLog($the_sess_userid, $the_id, $the_source){
	
	$log_sql = "
				insert into 
					receipt_full_log
				select
					*
					, now()
					, '$the_sess_userid'
					, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
					, '$the_source'
					, ''
				from
					receipt
				where
					rid = '$the_id'
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


//yoes 20220211
function generateM34PrincipalsByDate($the_lid, $current_date, $the_mode = ""){ 
	
	//yoes 20210902 -- no longer do the old law
	//$force_old_law = 0;
	if(!$current_date){
		$current_date = date("Y-m-d");
	}
	
	//get lawfulness's details
	//yoes 20221226
	//-ejob mode
	if($the_mode == "ejob"){
		
		$lawfulness_row = getFirstRow("select * from lawfulness_company where lid = '$the_lid'");
	}else{
		$lawfulness_row = getFirstRow("select * from lawfulness where lid = '$the_lid'");
	}
	
	
	//basic values
	$lawfulness_year = $lawfulness_row[Year];	
	$lawfulness_employees = $lawfulness_row[Employees];
	
	//calc value
	$lawfulness_ratio = getThisYearRatio($lawfulness_year);
	$need_for_lawful = getEmployeeRatio($lawfulness_employees,$lawfulness_ratio);
	
	$cid_province = getFirstItem("select province from company where cid = '".$lawfulness_row[CID]."'");
			
	$hire_numofemp = getHireNumOfEmpFromLid($the_lid, $force_old_law, $the_mode);
	
	//yoes 20220323 -- fix this..
	if($the_lid == 2050572947){
		//$hire_numofemp = $hire_numofemp+ getFirstItem("select * from lawful_employees_company where cid = '".$lawfulness_row[CID]."' and year = '2022'");
		
		$mmmmm = getFirstItem("select 
							lawful_submitted
						from 
							lawfulness_company 
						where 
							CID = '$lawfulness_row[CID]' 
							and 
							Year = '2022' 
							
							
							");
		
		if($mmmmm == 3){
			$hire_numofemp = $hire_numofemp+ 1;
		}
	}
						
	//set the 33 numbers to whatever we got from last time			
	$the_33 = $hire_numofemp;
	
	//yoes 20181108
	//change this
	
	//$the_35 = getNumCuratorFromLid($the_lid);
	
	//yoes 20230322 -- change this to have ejob mode
	$the_35 = getNumCuratorFromLid($the_lid, 0, $the_mode);
	
	
	$lid_to_get_34 = $the_lid; //lid
	$employees_ratio = $need_for_lawful - $the_33 -$the_35; //ต้องรับคนกี่คน // yoes 20160227 -- fix this so it account for 33 35
	
	if($employees_ratio < 0){
		$employees_ratio = 0;	
	}
	
	$year_date = 365; //days in years
	$the_wage = getThisYearWage($lawfulness_year, $cid_province); //ค่าจ้างประขำปี
	$this_lawful_year = $lawfulness_year; //ปีนี้ปีอะไร (ปีของ lid)
	$the_province = $cid_province; //province อะไร
	
	
	//echo " the_wag:e ".$the_wage;
	//echo " this_lawful_year: ".$this_lawful_year;
	//echo " the_province: ".$the_province;
	
	//special script here (same with the one used in reports)
	//*******
	
	//yoes 20220218 
	$current_date = $current_date;
	$lawful_34_principals_table_name = "lawful_34_principals_temp";
	include "scrp_get_34_from_lid.php";
	
}


//yoes 2018125
function getLawfulnessStatusArrayByLID($the_lid, $force_old_law = 0){ //$force_old_law = 0 -> คิด 33/35 ให้ตามสูตรปีของ lid / = 1 -> คิด 33/35 ให้เต็มปีตลอด
	
	//yoes 20210902 -- no longer do the old law
	//$force_old_law = 0;
	
	
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
	/*$hire_numofemp = getFirstItem("
									SELECT 
										count(*)
									FROM 
										lawful_employees
									where
										le_cid = '".$lawfulness_row[CID]."'
										and le_year = '".$lawfulness_year."'");*/
	
	
	//yoes 20181108 --> change on how to count hireNumOfEmp
	$hire_numofemp = getHireNumOfEmpFromLid($the_lid, $force_old_law);
	
		
						
						
	//set the 33 numbers to whatever we got from last time			
	$the_33 = $hire_numofemp;
	
	//yoes 20181108
	//change this
	$the_35 = getNumCuratorFromLid($the_lid);
	
	$lid_to_get_34 = $the_lid; //lid
	$employees_ratio = $need_for_lawful - $the_33 -$the_35; //ต้องรับคนกี่คน // yoes 20160227 -- fix this so it account for 33 35
	
	if($employees_ratio < 0){
		$employees_ratio = 0;	
	}
	
	$year_date = 365; //days in years
	$the_wage = getThisYearWage($lawfulness_year, $cid_province); //ค่าจ้างประขำปี
	$this_lawful_year = $lawfulness_year; //ปีนี้ปีอะไร (ปีของ lid)
	$the_province = $cid_province; //province อะไร
	
	
	//echo " the_wag:e ".$the_wage;
	//echo " this_lawful_year: ".$this_lawful_year;
	//echo " the_province: ".$the_province;
	
	//special script here (same with the one used in reports)
	//*******
	
	//yoes
	
	
	include "scrp_get_34_from_lid.php";
	
	if($the_lid == 2050540885){
		//echo "$paid_money == $start_money && $this_lawful_year >= 2018 && $this_lawful_year < 2050";exit();
	}
	
	
	if($the_lid == 2050540885){
		//
		//echo "$pending_maimad + $missing_33 + $missing_35"; exit();
		//print_r($lawful_status_array); exit();
		//echo $pending_maimad; exit();
		//echo "pending_maimad = $need_for_lawful -$the_33 -$the_34 -$the_35"; exit();	
		//echo $maimad_paid; exit();
	}
	
	$the_34 = $maimad_paid;
	
	//echo $the_34; exit();
	
	//echo " the_34: ".$the_34;
	
	$pending_maimad = $need_for_lawful -$the_33 -$the_34 -$the_35 ;

    if($the_lid == 2050540885) {
        //echo "pending_maimad = $need_for_lawful -$the_33 -$the_34 -$the_35";
        //exit();
    }
	
	
	
	//yoes 20181225
	//also add left-over non-deducted 33+35 to pending maimad ...
	
	//yoes 20190123 -> last receipt date here?
	
	//yoes 20190129 -> last_receipt_date is inclusive in the function itself
	//$missing_33_array = get33DeductionByCIDYearArray($lidcid, $this_lawful_year, $last_receipt_date);
	$missing_33_array = get33DeductionByCIDYearArray($lidcid, $this_lawful_year);	
	$missing_35_array = get35DeductionByCIDYearArray($lidcid, $this_lawful_year);

    if($the_lid == 2050540885){
        //print_r($missing_33_array); exit();
    }
	
	//yoes 20200624
	//status as per BETA
	//for BETA lawfulness -> get m33 from new code
	//$is_beta = getFirstItem("select 1 from lawfulness_meta where meta_lid = '$the_lid' and meta_for = 'is_beta_2020' and meta_value = 1 ");
	//echo "select 1 from lawfulness_meta where meta_lid = '$the_lid' and meta_for = 'is_beta_2020' and meta_value = 1"; exit();
	
	if(getLidBetaStatus($the_lid)){
		
		//needs m33_total_missing+m33_total_pending
		/*
		echo "<br>old m33_total_missing ".$missing_33_array[m33_total_missing]; 
		echo "<br>old m33_total_pending ".$missing_33_array[m33_total_pending];
		echo "<br>old this_lid_interests ".$this_lid_interests; //--> this comes from scrp_get_34_from_lid.php
		*/
		
		$m33_principal_row = getFirstRow("
		
			select
				sum(p_amount) as m33_total_missing
				, sum(p_pending_amount+p_pending_interests) as m33_total_pending
				, sum(p_interests) as this_lid_interests
				
			from
				lawful_33_principals
			where
				p_lid = '$the_lid'
		
		");
		
		//delete interest from OLD code
		$this_lid_interests -= $deducted_33_array[m33_total_interests];
		//add interets from new code
		$this_lid_interests += $m33_principal_row[this_lid_interests];
		
		//print_r($m33_principal_row);
		//echo "<br>";print_r($missing_33_array);
		$missing_33_array[m33_total_missing] = $m33_principal_row[m33_total_missing];
		$missing_33_array[m33_total_pending] = $m33_principal_row[m33_total_pending];
		//echo "<br>";print_r($missing_33_array);
		
		//yoes 20200626
		//add for new 35 code
		$m35_principal_row = getFirstRow("
		
			select
				sum(p_amount) as m35_total_missing
				, sum(p_pending_amount+p_pending_interests) as m35_total_pending
				, sum(p_interests) as this_lid_interests
				
			from
				lawful_35_principals
			where
				p_lid = '$the_lid'
		
		");
		
		//delete interest from OLD code
		$this_lid_interests -= $deducted_35_array[m35_total_interests];		
		//print_r($deducted_35_array); exit();
		//add interets from new code
		$this_lid_interests += $m35_principal_row[this_lid_interests];
		//echo "<br>";print_r($missing_35_array); 
		$missing_35_array[m35_total_missing] = $m35_principal_row[m35_total_missing];
		$missing_35_array[m35_total_pending] = $m35_principal_row[m35_total_pending];
		//echo "<br>";print_r($missing_35_array); exit();
		
	}
	
	//echo "<br>lidcid - ". $lidcid . " - " . $this_lawful_year;
	//print_r($missing_33_array); exit();
	
	$missing_33 = $missing_33_array[m33_total_pending];
	
	//$missing_35_array = get35DeductionByCIDYearArray($lidcid, $this_lawful_year, $last_receipt_date);
	
	$missing_35 = $missing_35_array[m35_total_pending];
	
	//echo $last_receipt_date; exit();
	
	//yoes 20200702
	//fix for https://app.asana.com/0/794303922168293/1182950211219809
	if($missing_33 < 0){
		$missing_33 = 0;
	}
	if($missing_35 < 0){
		$missing_35 = 0;
	}
	
	
	if($this_lawful_year >= 2018 && $this_lawful_year < 2050 && !$force_old_law){
		
		/*
		echo "paid_money: >> $paid_money << ";
		echo "start_money: >> $start_money << "; //exit();
		echo "the_34: >> $the_34 << ";		
		echo " = $pending_maimad => ";		*/
		//echo " pending_maimad = $pending_maimad + $missing_33 + $missing_35 ; ";	exit();
		
		$pending_maimad = $pending_maimad + $missing_33 + $missing_35 ;

        if($the_lid == 2050540885){
            //echo " => $pending_maimad "; exit();
        }
		//echo " => $pending_maimad "; exit();
		
	}
	
	
	
	//echo "$pending_maimad + $missing_33 + $missing_35"; exit();
	
	//yoes 20190123
	if($this_lawful_year >= 2018 && $this_lawful_year < 2050 && !$force_old_law){

        //49649
        if($the_lid == 2050540885999999999){

            echo "total_paid_amount: ". round($total_paid_amount,2);
            echo "total_missing_amount: ". round($missing_33_array[m33_total_missing]*1
                    //+	$missing_33_array[m33_total_interests]

                    +	$missing_35_array[m35_total_missing]*1
                    //+	$missing_35_array[m33_total_interests]

                    +	$start_money*1

                    + 	$this_lid_interests*1

                    ,2);

            //Array ( [m33_total_reduction] => 128128 [m33_total_missing] => 97328 [m33_total_interests] => 37614.9 [m33_total_pending] => 134942.9 )
            echo "total_missing_amount: ". ($missing_33_array[m33_total_missing]*1)


                    ."+".	($missing_35_array[m35_total_missing]*1)


                    ."+".	($start_money*1)

                    ."+". 	($this_lid_interests*1)

                    ;





        }

		if(
			round($total_paid_amount,2) >= round($missing_33_array[m33_total_missing]*1
									//+	$missing_33_array[m33_total_interests]
									
									+	$missing_35_array[m35_total_missing]*1
									//+	$missing_35_array[m33_total_interests]
									
									+	$start_money*1
									
									+ 	$this_lid_interests*1
									
									,2)){
		
			$pending_maimad = 0;	
			
			
		}else{
			
			$pending_maimad = 1;
			
		}
		
		if($lawfulness_row[CID] == 72406){
			
			
			if(round($total_paid_amount,2) >= round($missing_33_array[m33_total_missing]*1
									
									
									+	$missing_35_array[m35_total_missing]*1
									
									
									+	$start_money*1
									
									+ 	$this_lid_interests*1
									
									,2)){
										
				echo " total_paid_amount >= sum = $total_paid_amount";
				
				echo "<br>";
				
				echo round($total_paid_amount,2);
				
				echo " vs ";
				
				echo round($missing_33_array[m33_total_missing]*1
									
									
									+	$missing_35_array[m35_total_missing]*1
									
									
									+	$start_money*1
									
									+ 	$this_lid_interests*1
									
									,2);
			
			}
			
			if($total_paid_amount >= $missing_33_array[m33_total_missing]*1
									
									
									+	$missing_35_array[m35_total_missing]*1
									
									
									+	$start_money*1
									
									+ 	$this_lid_interests*1
									
									){
										
				echo "<br>NO ROUND total_paid_amount >= sum = $total_paid_amount";
				
				echo "<br>";
				
				echo round($total_paid_amount,2);
				
				echo " vs ";
				
				echo round($missing_33_array[m33_total_missing]*1
									
									
									+	$missing_35_array[m35_total_missing]*1
									
									
									+	$start_money*1
									
									+ 	$this_lid_interests*1
									
									,2);
			
			}else{
				
				echo "<br>NO ROUND IS INVALID?";
				
			}
		
			echo "<br>total_paid_amount -- ". $total_paid_amount . "<br>";
			echo "<br>start_money -- ". $start_money . "<br>";
			echo "<br>m33_total_missing -- ". ($missing_33_array[m33_total_missing]*0) . "<br>";
			echo "<br>m35_total_missing -- ". ($missing_35_array[m35_total_missing]*0) . "<br>";
			echo "<br>this_lid_interests -- ". $this_lid_interests . "<br>";
			echo "<br>pending_maimad -- ". $pending_maimad . "<br>"; 
			
			echo "<br>sum = ";
			
			echo $missing_33_array[m33_total_missing]*1
									
									
									+	$missing_35_array[m35_total_missing]*1
									
									
									+	$start_money*1
									
									+ 	$this_lid_interests*1;
									
			echo "<br>sum is ";
			
			echo $missing_33_array[m33_total_missing] ."*1+".
									
									
										($missing_35_array[m35_total_missing]*1)."*1+".
									
									
										$start_money."*1+".
									
									 	$this_lid_interests ."*1";
			
			//exit();
		}
		
	}
	
	/*	
	echo "start_money: $start_money";
	echo "<br>";
	echo "owned_money: $owned_money";
	echo "<br>";	
	echo "this_lid_interests: $this_lid_interests";
	echo "<br>";	
	print_r($missing_33_array); 
	echo "<br>";	
	print_r($missing_35_array);	
	echo "<br>";
	echo " m33_total_missing: " . $missing_33_array[m33_total_missing] 
		. " m35_total_missing: " . 	$missing_35_array[m35_total_missing] 
		. " start_money: " .	$start_money 
		. " this_lid_interests: " . 	$this_lid_interests;
	echo "<br>";
	echo " OWNED: " . ($missing_33_array[m33_total_missing] + 	$missing_35_array[m35_total_missing] +	$start_money +	$this_lid_interests);
	echo "<br>";
	echo " total_paid_amount: " . $total_paid_amount;
	echo "<br>";
	echo " pending_maimad = $pending_maimad + $missing_33 + $missing_35 ; ";
	exit();*/
	
	
	//echo $pending_maimad; exit();
	
	//echo $missing_33; exit();
	
	
	//echo " need_for_lawful; ".$need_for_lawful; //exit();
	//echo " pending_maimad; ".$pending_maimad . "<br>"; //exit();
	
	
	
	//update this lawfulness accordingly
	//yoes 20160226 --> fix this to account for "less than" ratio case
	//yoes 20190103 -> new law => if paid = start then .... -> is lawful
	
	//echo "$paid_money == $start_money && $this_lawful_year >= 2018 && $this_lawful_year < 2050";exit();
	
	//echo "lawful_status -> ". $lawful_status . "<br>";
	
	//echo $pending_maimad; exit();
	
	if($the_lid == 2050553291){
		//echo "$paid_money == $start_money && $this_lawful_year >= 2018 && $this_lawful_year < 2050";exit();
	}
	
	if($lawfulness_employees < $lawfulness_ratio){
		$lawful_status = 3;	
		
	}elseif(
	
			// yoes interim fix 20200123 ?
			// as per https://app.asana.com/0/794303922168293/1158468034248001
			$lawfulness_row[CID] == 71034 
			
			&& (
			
				(
					
					//only lawful if paid = start + toal missing 33+34+35
					$total_paid_amount >= $start_money 
									+ $missing_33_array[m33_total_missing]
									+ $missing_35_array[m35_total_missing]
									+ $this_lid_interests
				)
				&& $this_lawful_year >= 2018 && $this_lawful_year < 2050 && !$pending_maimad
			
			)
		
		){ 
	
		//echo "<br>2ssss2"; exit();
		$lawful_status = 1;	
		
		
	}elseif(
	
		$lawfulness_row[CID] != 71034 // yoes interim fix 20200123 as per https://app.asana.com/0/794303922168293/1158468034248001
		&& $paid_money == $start_money && $this_lawful_year >= 2018 && $this_lawful_year < 2050 && !$pending_maimad //this can be wrong if บังเอิญต้องจ้าง 5 -> ต้องจ่าย 2 และต้องจ่ายทดแทน 2 และจ่ายแทน 2 แล้ว - ทำให้เงินต้น 34 ที่ต้องจ่าย = เงินที่จ่ายทดแทน และ ไม่มีค้าง ม33
		
		){
		
		//echo "$paid_money == $start_money && $this_lawful_year >= 2018 && $this_lawful_year < 2050 && !$pending_maimad"; exit();
		$lawful_status = 1;	
		//echo "<br>22"; exit();
	}elseif(
	
		// $lawfulness_row[CID] != 71034 // yoes interim fix 20200123 as per https://app.asana.com/0/794303922168293/1158468034248001
		// &&
		$pending_maimad <= 0
		
		){
		$lawful_status = 1;	
		//echo "33"; exit();
	}elseif($pending_maimad == $need_for_lawful){
		$lawful_status = 0;
		//echo "44"; exit();
	}else{
		$lawful_status = 0;
		//echo "55"; exit();
		
	}
	
	
	//yoes 20130717 บริษัท ซาฟารีเวิลด์ จำกัด (มหาชน)  2564
	//ทำไมไม่เขียว?? -> ไม่เข้า if ข้างบน
	if($the_lid == 2050553291){
		//echo " lawful_status - $lawful_status ".$extra_money . " - "; exit();
	}
	
	
	
	if($lawfulness_row[CID] == 72406){
		
		//echo $lawful_status; exit();	
		//echo $pending_maimad . " == ". $need_for_lawful;exit();
	}
	
	if($lawfulness_row[CID] == 71034){
		//echo($lawful_status); exit();
	}
	
	
	
	
	if($the_33){
		$hire_status = 1;
		if($lawful_status == 0){
			$lawful_status = 2;	//do something
			
			if($the_lid == 2050553291){
				//echo " lawful_status - $lawful_status ".$extra_money . " - "; exit();
			}
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
	
	
	
	
	
	
	//yoes 20170320 --
	//if also check "pay_status" for one receipt multiple company ...
	if($pay_status == 0){
		
		$the_sql = "select *
					, receipt.amount as receipt_amount
					, lawfulness.year as lawfulness_year
					 from payment, receipt , lawfulness
						where 
						receipt.RID = payment.RID
						and
						lawfulness.LID = payment.LID
						
						and
						lawfulness.lid = '".$lid_to_get_34."' 
						
						and
						is_payback != 1
						and 
						main_flag = 0
						";
						
		$multiple_34 = getFirstItem($the_sql);
		
		//echo $multiple_34; exit();
		
		if($multiple_34){
			
			$pay_status = 1;
		}
	
	}
	
		
	
	//echo $pay_status; exit();
	
	
	if($the_35){
		$conc_status = 1;
		if($lawful_status == 0){
			
			
			$lawful_status = 2;	//do something
		}
	}else{
		$conc_status = 0;
	}
	
	
	
	
	
	
	//echo $lawful_status; exit();
	
	
	//echo " lawful_status old - ".$lawfulness_row[LawfulStatus];
	//echo " lawful_status new - ".$lawful_status;
	//echo " hire_status - ".$hire_status;
	//echo " pay_status - ".$pay_status;
	//echo " conc_status - ".$conc_status;
	
	
	//yoes 20170320
	//also 2011-2012 - if main branch is Lawful then Branch is Lawful
	if($pay_status && ($lawfulness_year == 2011 || $lawfulness_year == 2012) && $lawful_status != 3){
		
		
		
					
		//yoes 20170321
		//if someplace in that receipt is Lawful then all these are lawful ...
		//yoes 20170703 // that place must not be own's place
		$the_sql = "
				
					select
						bb.lawfulStatus
					from
						payment aa
							join
								lawfulness bb
								
								on aa.LID = bb.LID
					where
						RID in (
			
			
			
							select 
								
								receipt.RID
							
							 from payment, receipt , lawfulness
								where 
								receipt.RID = payment.RID
								and
								lawfulness.LID = payment.LID
								
								and
								lawfulness.lid = '".$lid_to_get_34."' 
								
								and
								is_payback != 1


						
						)
						and
						bb.lawfulStatus = 1
						
						and
						bb.lid != '".$lid_to_get_34."'
						
						
						limit 0,1
					
					
					
					";

		
		if($lawfulness_row[CID] == 4662){
			
			//echo $the_sql; exit();	
		}
		
		$main_lawfulness = getFirstItem($the_sql);
		
							
									
							
		if($main_lawfulness == 1){
				
			$lawful_status = 1;
		}
		
		
	}
	
	if($lawfulness_row[CID] == 72406){
		
		//echo $lawful_status; exit();	
	}
	
	
	
	
	
	//yoes 20190702
	//if courted_flag and pay all related court then IS LAWFUL
	$courted_flag = getLawfulnessMeta($the_lid,"courted_flag");
	//echo $courted_flag; exit();
	if($courted_flag){
		
		//have related court?
		$court_count = getFirstItem("select	count(*) from invoices_law 
										where invoice_cid = '".$lawfulness_row[CID]."' and invoice_lawful_year = '".$lawfulness_row[Year]."'");
										
		
		if($court_count){
			
			//get owned money
			$owned_court_money = getFirstItem("
				
				select
					sum(invoice_principal_amount)+sum(lawyer_fee)+sum(process_fee)+sum(other_fee)
				from
					invoices_law
				where
					invoice_cid = '".$lawfulness_row[CID]."'
					and
					invoice_lawful_year = '".$lawfulness_row[Year]."'
					
			
			
			");
			
			
			$court_paid = getFirstItem("
														
									select
										sum(invoice_amount)-sum(invoice_interest_amount)
									from
										invoices
									where
										invoice_cid = '".$lawfulness_row[CID]."'
										and
										invoice_lawful_year = '".$lawfulness_row[Year]."'
										and
										invoice_status = 98
								
							
									");
			
			//get paid money
			
			//echo $court_paid; exit();
			
			if($owned_court_money - $court_paid <= 0){
				
				$lawful_status = 1;
				
			}
			
		}
		
		
		
	}
	
	//yoes 20230717
	if($the_lid == 2050553291 && $extra_money <= 0){
		//echo " lawful_status - $lawful_status ".$extra_money . " - "; exit();
		$lawful_status = 1;
	}

    //yoes 20250630  เวย หง จำกัด 63 คำนวณแล้วเศษเหลือ 5.8207660913467E-11 (0.000000000058 )
    //อาจต้อง fix ตรงนี้ให้มันไม่ float
    if($the_lid == 2050540885 && $extra_money <= 0.0000001){
        //echo $extra_money;
        //echo round($extra_money,2); exit();
        $lawful_status = 1;

    }
	
	
	
	
	//echo $start_money;
	//echo $this_lid_interests;
	//yoes 20211112
	//if จ่ายเกิน = ยังไงก็ lawful ?
	if($extra_money > 0 && ($the_lid == 2050557665 || $the_lid == 2050556546 || $the_lid == 2050533091)){
		
		$lawful_status = 1;
	}
	
	//yoes 20231220 -- case แปลก?
	if($extra_money <= 0 && ($the_lid == 166900)){
		
		$lawful_status = 1;
	}
	
	
	if($the_lid == 166900){
		//echo $extra_money; exit();
		//echo $lawful_status;
		//print_r($lawful_status_array);exit();
	}
	
	
	
	
	//if($this_lawful_year >= 2018 && $this_lawful_year < 2050){ 	
	//yoes 20211112
	if(
		(
			$lawfulness_row[Year] >= 2018 && $lawfulness_row[Year] < 2050
		)
		&& 
		(
		
			//$the_lid == 2050556546
			//||
			//$the_lid == 2050549556
			1 == 1
		
		) 
		&& 
		//yoes 20220116
		//จ้างงานขอย้อนกลับ ให้สถานะไม่เปลี่ยนตามการคำนวนแบบใหม่
		(
			//$_SESSION['sess_accesslevel'] == 1 || $_SESSION['sess_accesslevel'] == 2 
			/*($_SESSION['sess_accesslevel'] == 1 && $lawfulness_row[Year] >= 2022 && $lawfulness_row[Year] < 2100)
			|| ($_SESSION['sess_accesslevel'] == 2 && $lawfulness_row[Year] >= 2022 && $lawfulness_row[Year] < 2100)
			|| ($_SESSION['sess_accesslevel'] == 3 && $lawfulness_row[Year] >= 2022 && $lawfulness_row[Year] < 2100))*/
			
			$lawfulness_row[Year] >= 2022 && $lawfulness_row[Year] < 2100 
			
			||
			(
			
				//yoes 20220322 -> special case for ปี 64 บาง บ ที่จะใช้สูตรใหม่
				$lid_to_get_34 == 2050554622
				||
				$lid_to_get_34 == 2050569011
				||
				$lid_to_get_34 == 2050563339
				||
				$lid_to_get_34 == 2050553291 //บริษัท ซาฟารีเวิลด์ จำกัด (มหาชน)  64
				
				||
				$lid_to_get_34 == 192501
				//||
				//$lid_to_get_34 == 2050561292
			)
			
		)
		
		){
			
			
		//yoes 20211112 --
		//calculate everything from db
		$sql = "
					select
						*
					 from 
							lawful_34_principals
						where 
							p_lid = '".$the_lid."'

						order by
							p_uid desc
						limit 0,1

						";

		
		$pay_34_row = getFirstRow($sql);

		$pay_34_row[p_pending_amount] = max($pay_34_row[p_pending_amount],0);		
		
		//yoes 20211020	--> use this instead
		$m33_principal_row = getFirstRow("

			select
				sum(p_amount) as the_principals
				, sum(p_interests) as the_interests
				, sum(p_pending_amount) as the_pending_principals
				, sum(p_pending_interests) as the_pending_interests
			from
				lawful_33_principals
			where
				p_lid = '$the_lid'

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
				p_lid = '$the_lid'

		");
		
		//echo " pay_34_row[p_pending_amount] " . $pay_34_row[p_pending_amount] . " pay_34_row[p_pending_amount] ";
		
		
		$total_all_333435_pending = default_value($pay_34_row[p_pending_amount],0) + default_value($m33_principal_row[the_pending_principals],0) +default_value($m35_principal_row[the_pending_principals],0);
		
		//echo $total_all_333435_pending;
		
		
		if($total_all_333435_pending <= 0){
			
			$lawful_status = 1;
		}
	
	}
	
	//yoes 20211115
	if($lawfulness_employees < $lawfulness_ratio){
		$lawful_status = 3;	
	}
	
	
	$lawful_status_array = array(
	
		"lawful_status" => $lawful_status
		, "hire_status" => $hire_status
		, "pay_status" => $pay_status
		, "conc_status" => $conc_status
		, "maimad_paid" => $maimad_paid
		, "start_money" => $start_money
		, "this_lid_interests" => $this_lid_interests
	
	);
	
	
	
	
	
	return $lawful_status_array;
	
}

//yoes 20220211
function getGetTotalToPayByLid($the_lid, $table_suffix = ""){
	
	
	//yoes 2022022
	//first principal 34 row (to get max_p_amount to pay 34)
	$sql = "
				select 
					*
					
				 from 
						lawful_34_principals$table_suffix
					where 
						p_lid = '".$the_lid."'

					order by
						p_uid asc
					limit 0,1

					";

	
	$max_pay_34_row = getFirstRow($sql);
	
	
	//yoes 2022022
	//last principal 34 row (to get pending 34)
	$sql = "
				select 
					*
					
				 from 
						lawful_34_principals$table_suffix
					where 
						p_lid = '".$the_lid."'

					order by
						p_uid desc
					limit 0,1

					";

	
	$last_pay_34_row = getFirstRow($sql);
	
	
	//
	$sql = "
				select 
					sum(p_interests)
					
				 from 
						lawful_34_principals$table_suffix
					where 
						p_lid = '".$the_lid."'

					";

	//yoes 20211110
	$pending_interest_pay_34 = getFirstItem($sql);
	
	$lawful_row = getFirstRow("select * from lawfulness where lid = '".$the_lid."'");
	$this_lawful_year = $lawful_row["Year"];
	
	//print_r($lawful_row);
	
	if($this_lawful_year >= 2018 && $this_lawful_year < 2050){ 											
				
					//yoes 20211020	--> use this instead
					$m33_principal_row = getFirstRow("

						select
							sum(p_amount) as the_principals
							, sum(p_interests) as the_interests
							, sum(p_pending_amount) as the_pending_principals
							, sum(p_pending_interests) as the_pending_interests
						from
							lawful_33_principals$table_suffix
						where
							p_lid = '$the_lid'

					");

					$m35_principal_row = getFirstRow("
												
						select
							sum(p_amount) as the_principals
							, sum(p_interests) as the_interests
							, sum(p_pending_amount) as the_pending_principals
							, sum(p_pending_interests) as the_pending_interests						
						from
							lawful_35_principals$table_suffix
						where
							p_lid = '$the_lid'

					");
					
	}
	
	//data...
	
	$total_principals = 
		0		
		+$max_pay_34_row[p_amount]		
		+$m33_principal_row[the_principals]		
		+$m35_principal_row[the_principals] ;
		
	$total_interests = 
		0				
		+$pending_interest_pay_34 
		+$m33_principal_row[the_interests]		
		+$m35_principal_row[the_interests];
		
	
	$total_money_to_pay = 
	
		0		
		+$total_principals
		+$total_interests
		;
		
	
	$result_array = array();
	
	//
	$result_array[total_principals] = $total_principals;
	$result_array[total_interests] = $total_interests;
	$result_array[total_money_to_pay] = $total_money_to_pay;
	
	
	//yoes 20220222 -> even more data to return..
	//34
	$result_array[all_34_principals] = $max_pay_34_row[p_amount];
	$result_array[all_34_interests] = $pending_interest_pay_34;
	$result_array[all_34_to_pay] = $max_pay_34_row[p_amount] +$pending_interest_pay_34;
	
	$result_array[pending_34_principals] = $last_pay_34_row[p_pending_amount];	
	//yoes 20220306
	if($result_array[pending_34_principals] < 0){
		$result_array[pending_34_principals] = 0;
	}
	
	$result_array[pending_34_interests] = $last_pay_34_row[p_pending_interests];		
	$result_array[pending_34_to_pay] = $last_pay_34_row[p_pending_amount]+$last_pay_34_row[p_pending_interests];
	
	
	//33
	$result_array[all_33_principals] = $m33_principal_row[the_principals];
	$result_array[all_33_interests] = $m33_principal_row[the_interests];
	$result_array[all_33_to_pay] = $m33_principal_row[the_principals] +$m33_principal_row[the_interests];
	
	$result_array[pending_33_principals] = $m33_principal_row[the_pending_principals];	
	$result_array[pending_33_interests] = $m33_principal_row[the_pending_interests];
	$result_array[pending_33_to_pay] = $m33_principal_row[the_pending_principals] +$m33_principal_row[the_pending_interests]; 
	
	//35
	$result_array[all_35_principals] = $m35_principal_row[the_principals];
	$result_array[all_35_interests] = $m35_principal_row[the_interests];
	$result_array[all_35_to_pay] = $m35_principal_row[the_principals] +$m35_principal_row[the_interests];
	
	$result_array[pending_35_principals] = $m35_principal_row[the_pending_principals];	
	$result_array[pending_35_interests] = $m35_principal_row[the_pending_interests];
	$result_array[pending_35_to_pay] = $m35_principal_row[the_pending_principals] +$m35_principal_row[the_pending_interests]; 
	
	//333435 (all)
	$result_array[all_333435_principals] = $total_principals;
	$result_array[all_333435_interests] = $total_interests;
	$result_array[all_333435_to_pay] = $total_principals+$total_interests;
	
	$result_array[pending_333435_principals] = $result_array[pending_33_principals] +$result_array[pending_34_principals] +$result_array[pending_35_principals];	
	$result_array[pending_333435_interests] = $result_array[pending_33_interests] +$result_array[pending_34_interests] +$result_array[pending_35_interests];	
	$result_array[pending_333435_to_pay] = $result_array[pending_333435_principals] +$result_array[pending_333435_interests];

    if($the_lid == 2050649058){

        //print_r($result_array);

    }

	//yoes 20240808?
	if($the_lid == 2050594846){		
		//จ่ายวันนี้แล้วดอกเหลือ 0
		$result_array[pending_333435_to_pay] = $result_array[pending_333435_principals] + ($result_array[all_333435_interests] - getTotalPaidByLid($the_lid));
		$result_array[pending_333435_interests] = ($result_array[all_333435_interests] - getTotalPaidByLid($the_lid));	
	}
	
	//yoes 20220222 -> also show this
	$result_array[all_333435_paid] = getTotalPaidByLid($the_lid);
	
	
	return $result_array;
	
}

//yoes 20220211
function getTotalPaidByLid($the_lid){
	
	$sql = "select 
			sum(receipt.Amount) as the_amount
		from 
			payment
			, receipt
			, lawfulness
		where 
			receipt.RID = payment.RID
			and
			lawfulness.LID = payment.LID
	
			and
			lawfulness.lid = '".$the_lid."' 
	
			and
			is_payback != 1
			and 
			main_flag = 1
		group by 
			lawfulness.LID";
			
	//echo $sql;

	$lid_receipt_row = getFirstRow($sql);
	
	$paid_amount = $lid_receipt_row[the_amount];
	
	return $paid_amount;
	
}

function getRefundAmount($amount , $the_lid){
	
	
	$sql = "select 
			sum(receipt.Amount) as the_amount
		from 
			payment
			, receipt
			, lawfulness
		where 
			receipt.RID = payment.RID
			and
			lawfulness.LID = payment.LID
	
			and
			lawfulness.lid = '".$the_lid."' 
	
			and
			is_payback = 1
			and 
			main_flag = 1
		group by 
			lawfulness.LID";

    if($the_lid == 2050540885) {

        //echo $sql; exit();
    }


	$lid_receipt_row = getFirstRow($sql);
	
	$paid_amount = $lid_receipt_row[the_amount];
	$paid_amount = $amount - $paid_amount;
	
	
	return $paid_amount;
	
}

//yoes 20160208 -- new function here
function resetLawfulnessByLID($the_lid){
	
	//doLawfulnessFullLog("0", $the_lid, "functions.php-resetLawfulnessByLID");
	
	if(getLidBetaStatus($the_lid)){
		
		$is_beta_mode = 1;
		
		//init new lawfulness data according to new code...
		//generate new principal...
		generate33PrincipalFromLID($the_lid);
		//sync payment meta from old to new (if applicable)
		//yoes 20200817 -- no longer sync this
		//syncPaymentMeta($the_lid);
		//run interests
		generate33InterestsFromLID($the_lid);
		//exit();
		
		generate35PrincipalFromLID($the_lid);
		//yoes 20200817 -- no longer sync this
		//syncPaymentMeta($the_lid, 0, "m35");
		generate35InterestsFromLID($the_lid);
	}
	
	$hire_numofemp = getHireNumOfEmpFromLid($the_lid);
	
	
	//-----
	
	$lawful_status_array = getLawfulnessStatusArrayByLID($the_lid);
	
	if($the_lid == 2050540885){
		//print_r($lawful_status_array); exit();
	}
	
	
	//print_r($lawful_status_array); exit();
	
	//yoes 20201021
	//special for ชำระบัญชี
	//always LAWFUL
	$the_account_status = getLawfulnessMeta($the_lid,"account_status");
	
	if($the_account_status == 3){
		
		$lawful_status_array[lawful_status] = 1;
	}
	
	//yoes special - 20211001 lawful_status_array[lawful_status] - status 5
	$sql_lawful_exempt = "	select
			count(*)
		from 
			lawfulness_meta 
		where
		
			meta_for = 'is_lawful_exempt'
			and
			meta_lid = '$the_lid'
			and
			meta_value = 1
		
		";
		
		
		
	$is_lawful_exempt = getFirstItem($sql_lawful_exempt);

	
	if($is_lawful_exempt){
		$lawful_status_array[lawful_status] = 5;
	
	}
	
	if($the_lid == 2050540885){
		//print_r($lawful_status_array); exit();
	}
		//bank add check status 6 court 20221227
		$sql_court_case_closed = "	select
		count(*)
	from 
		lawfulness_meta 
	where
	
		meta_for = 'is_court_case_closed'
		and
		meta_lid = '$the_lid'
		and
		meta_value = 1
	
	";
	
	
	
	$is_court_case_closed = getFirstItem($sql_court_case_closed);


	if($is_court_case_closed){
		$lawful_status_array[lawful_status] = 6;

	}
	//end bank add check status 6 court 20221227


	if($the_lid == 2050540885){
		//print_r($lawful_status_array); exit();
	}
	
	if($lawful_status_array[lawful_status] == 1){
			
		//yoes 20230302
		//if เป็นเขียว ให้ update ได้
		//else ห้าม update ในปีเก่าๆ
		
		$sql = "
		
			update
				lawfulness
			set
				LawfulStatus = '".$lawful_status_array[lawful_status]."'
				, Hire_status = '".$lawful_status_array[hire_status]."'
				, pay_status = '".$lawful_status_array[pay_status]."'
				, Conc_status = '".$lawful_status_array[conc_status]."'
				, Hire_NumofEmp = '$hire_numofemp' 
			where
				lid = '$the_lid'
		
		";
		
	}else{
		
		$sql = "
		
			update
				lawfulness
			set
				LawfulStatus = '".$lawful_status_array[lawful_status]."'
				, Hire_status = '".$lawful_status_array[hire_status]."'
				, pay_status = '".$lawful_status_array[pay_status]."'
				, Conc_status = '".$lawful_status_array[conc_status]."'
				, Hire_NumofEmp = '$hire_numofemp' 
			where
				lid = '$the_lid'
				and
				year not in (2011,2012)
		
		";
	
	}
	
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


function resetLawfulnessByLID_old_law($the_lid){
	
	$hire_numofemp = getHireNumOfEmpFromLid($the_lid, 1);
	
	$lawful_status_array = getLawfulnessStatusArrayByLID($the_lid, 1);
	
		
	//print_r($lawful_status_array); exit();
	
	/*$sql = "
	
		update
			lawfulness_old_law
		set
			LawfulStatus = '".$lawful_status_array[lawful_status]."'
			, Hire_status = '".$lawful_status_array[hire_status]."'
			, pay_status = '".$lawful_status_array[pay_status]."'
			, Conc_status = '".$lawful_status_array[conc_status]."'
			, Hire_NumofEmp = '$hire_numofemp'
		where
			lid = '$the_lid'
	
	";*/
	
	
	$sql = "
	
		replace into
			lawfulness_old_law(
		
				lid
				, LawfulStatus
				, Hire_status
				, pay_status
				, Conc_status
				, Hire_NumofEmp
				
			)
			values(
			
				'$the_lid'			
				, '".$lawful_status_array[lawful_status]."'
				, '".$lawful_status_array[hire_status]."'
				, '".$lawful_status_array[pay_status]."'
				, '".$lawful_status_array[conc_status]."'
				, '$hire_numofemp'
				
			) 
	
	";
	
	//echo $sql; exit();
	
	mysql_query($sql) or die(mysql_error());
	
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

//yes 20160301
function checkCaseClosed($this_lawful_year, $this_id){
	
	$this_lawful_row = getFirstRow("select close_case_date, reopen_case_date from lawfulness where Year = '$this_lawful_year' and CID = '$this_id'");
	if($this_lawful_row[close_case_date] > $this_lawful_row[reopen_case_date]){
		$case_closed = 1;					
		//echo "--> $case_closed <--";			
	}else{
		
		$case_closed = 0;	
	}
	
	return $case_closed;
		
	
}

function intToDoNotDo($what){
					
	if($what){
		return "ปฏิบัติ";	
	}else{					
		return "-";						
	}
	
}

/// -- Cal Web Service [2017-03-13]
function callWebservice($wsdl_url, $arr_input=NULL, $method_name=NULL, $options=NULL){
	if($options)
		$client = new SoapClient($wsdl_url,$options);
	else 
		$client = new SoapClient($wsdl_url);
	if($arr_input && $method_name){
		$params = array($arr_input);
		try {
			$response = $client->__soapCall($method_name, $params);		
		} catch (Exception $e) {
			echo($client->__getLastResponse());
			echo PHP_EOL;
			echo($client->__getLastRequest());
		}		
	} else {
		var_dump($client->__getFunctions()); 
		var_dump($client->__getTypes()); 		
	}

	//then return response as array 
	return (array) $response;	
}



function formatEmployStatusDesc($what){
	
	$to_show = "-";
	
	switch ($what){
		case "1" : $to_show = "จ้างงาน"; break;
		case "0" : $to_show = "ไม่ได้จ้างงาน"; break;
		
	}
	
	return $to_show;
	
}

function getLawfulnessMeta($the_lid, $what){
	
	
	
	return getFirstItem("
	
		select
			meta_value
		from
			lawfulness_meta
		where
			meta_lid = '$the_lid'
			and
			meta_for = '$what'
	
	
	");	
	
}

function getSSOdata($the_id,$companyCode){
	
	//$html = file_get_contents("https://wsfund.dep.go.th/ajax_get_sso.php?the_id=$the_id&CompanyCode=$CompanyCode");
	$the_url = "http://203.154.94.108/ajax_get_sso.php?the_id=$the_id&CompanyCode=$companyCode";
	$html = file_get_contents($the_url);
	//echo $the_url;
	
	//echo $html;
	
	//echo "http://203.154.94.108/ajax_get_sso.php?the_id=$the_id&CompanyCode=$CompanyCode";
	//yoes 20220915
	//wsfund got blocked by SSO?
	//use the one on JOB instead
	//$html = file_get_contents("https://job.dep.go.th/ajax_get_sso.php?the_id=$the_id&CompanyCode=$CompanyCode");
	
	$dom = new DOMDocument('1.0', 'utf-8');
	$dom->loadHTML($html);
	foreach($dom->getElementsByTagName('tr') as $node)
	{
		$row = $dom->saveHTML($node);
		if(preg_match('/so_end_date/',$row)){					
			if(preg_match_all('#<td>(.*?)</td>#s',$row,$m)){
				$d = array();
				$d[CompanyName] 	= trim($m[1][1]);
				$d[CompanyCode] 	= trim($m[1][2]);
				$d[BranchCode] 		= trim($m[1][3]);
				$d[Status] 			= trim($m[1][4]);
				
				$tmp = explode('<input',$m[1][5]);
				$d[StartDateTH]		= trim($tmp[0]);
				
				$tmp = explode('"',$m[1][5]);
				if($tmp[5])
					$d[StartDate]		= $tmp[17].'-'.$tmp[11].'-'.$tmp[5];	//YYYY-MM-DD
				else 							
					$d[StartDate]		= "";
					
				$tmp = explode('<input',$m[1][6]);
				$d[ResignDateTH]	= trim($tmp[0]);
				
				$tmp = explode('"',$m[1][6]);
				if($tmp[5])
					$d[ResignDate]	= $tmp[17].'-'.$tmp[11].'-'.$tmp[5];	//YYYY-MM-DD
				else							
					$d[ResignDate]			= "";
				
				$a[] = $d;
			}
			
		}								

	}			
	return $a;
}


function getAjaxData($post_string, $target){
				
	$post = $post_string;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $target);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$response = curl_exec($ch);
	curl_close($ch);						
	
	return $response;
	//echo $response;		
	//return table2array($response);
	
}

//for test


function formatStatusMessage($status){
	
	if($status == 1)
	{
		return "success";
	}
	elseif($status == 0)
	{
		return "fail";
	}
	elseif($status == -1)
	{
		return "error";
	}else{
		return "unknown";
	}
}

function insertWsLog($file_name, $log_request){				
					
	$file_name = "'$file_name'";
	$log_request = "'$log_request'";
	
	$sql = "insert into ws_logs(
		
		LogTime
		,Username
		,IPAddress
		,FunctionCall
		,Request		
		,Response
		
	)values(
		now()
		,'0'
		,'".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
		,$file_name
		,$log_request
		,' '
	
	)";
		
	mysql_query($sql);
	
	$sql2 = "	SELECT log_id FROM ws_logs
				WHERE FunctionCall= $file_name
				and Response = ' '
				ORDER BY log_id DESC LIMIT 1;";
				
	 $log_insert_id = getFirstItem($sql2);	
	 
	return $log_insert_id;
	
}

function updateWsLog($log_insert_id, $log_response){
	
	$sql = "update
				ws_logs
			set
				Response = '$log_response'
			where
				log_id = '$log_insert_id'
			";
			
	mysql_query($sql) or die(mysql_error());
	
	
function to_windows($what){
 
 $server_ip=$_SERVER[SERVER_ADDR];
 if($server_ip == "127.0.0.1" || $server_ip == "::1"){
  return iconv("UTF-8", "WINDOWS-874",$what);
 }else{
  return $what;
 }
}
	
}

?>