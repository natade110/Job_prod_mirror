<?php

	include "db_connect.php";
	
	$number_of_mails = getFirstItem("select count(*) from email_log where email_status = 0");
	
	
	echo "number of mails to send: ". $number_of_mails;
	
	//email types are:
	/*
	<option value="2" <?php if($_POST["alert_type"] == "2"){echo "selected='selected'";}?>>ปฏิบัติตามกฏหมายแล้ว</option>
	<option value="0" <?php if($_POST["alert_type"] == "0"){echo "selected='selected'";}?>>ไม่ทำตามกฏหมาย</option>
	<option value="3" <?php if($_POST["alert_type"] == "3"){echo "selected='selected'";}?>>ปฏิบัติตามกฏหมายแต่ไม่ครบอัตราส่วน</option>
	<option value="1" <?php if($_POST["alert_type"] == "1"){echo "selected='selected'";}?>>พบข้อมูลการใช้สิทธิซ้ำซ้อน</option>
	*/
	
	//define templates here
	//-------------------------------------------
	//lawful
	$the_header_2 = "Email การแจ้งรายงานผลการจ้างงานคนพิการในสถานประกอบการ ประจำปี 2559";
			
	$the_body_2 = "<table><tr><td>เรียนคุณ {contact_name}<br><br>";	
	
	$the_body_2 .= "Email เนื่องจากที่ท่านได้ดำเนินการตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการประจำปี 2559  ทางกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ขอแจ้งผลการปฏิบัติตามกฎหมายของท่านได้ดำเนินการ “ปฏิบัติตามกฎหมายครบถ้วนสมบูรณ์แล้ว” ถ้าการแจ้งมีข้อผิดพลาดประการใด กรุณาติดต่อเจ้าหน้าที่เพื่อทำการตรวจสอบได้ที่เบอร์โทรศัพท์ 0 2106 9327-31 ในเวลาราชการ หรือ สำนักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัดที่ท่านรายงาน ";
	$the_body_2 .= "<br><br>";
	
	$the_body_2 .= ", กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br>กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</td></tr></table>";
	
	
	
	//unlawful
	$the_header_0 = "Email การแจ้งรายงานผลการจ้างงานคนพิการในสถานประกอบการ ประจำปี 2559";
			
	$the_body_0 = "<table><tr><td>เรียนคุณ {contact_name}<br><br>";	
	
	$the_body_0 .= "Email เนื่องจากที่ท่านได้ดำเนินการตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการประจำปี 2559  ทางกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ขอแจ้งผลการปฏิบัติตามกฎหมายของท่านยัง “ไม่ทำตามกฎหมาย” ให้ท่านรีบดำเนินการตามกฎหมายโดยด่วน กรุณาติดต่อเจ้าหน้าที่เพื่อทำการตรวจสอบได้ที่เบอร์โทรศัพท์ 0 2106 9327-31 ในเวลาราชการ หรือ สำนักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัดที่ท่านรายงาน ถ้าการแจ้งมีข้อผิดพลาดประการใดขออภัยมา ณ โอกาสนี้";	
	$the_body_0 .= "<br><br>";
	
	$the_body_0 .= ", กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br>กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</td></tr></table>";
	
	
	//partial lawful
	$the_header_3 = "Email การแจ้งรายงานผลการจ้างงานคนพิการในสถานประกอบการ ประจำปี 2559";
			
	$the_body_3 = "<table><tr><td>เรียนคุณ {contact_name}<br><br>";	
	
	$the_body_3 .= "Email เนื่องจากที่ท่านได้ดำเนินการตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการประจำปี 2559  ทางกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ขอแจ้งผลการปฏิบัติตามกฎหมายของท่านยัง “ปฏิบัติตามกฎหมายแต่ไม่ครบอัตราส่วน” ให้ท่านรีบดำเนินการตามกฎหมายโดยด่วน  กรุณาติดต่อเจ้าหน้าที่เพื่อทำการตรวจสอบได้ที่เบอร์โทรศัพท์ 0 2106 9327-31 ในเวลาราชการ หรือ สำนักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัดที่ท่านรายงาน ถ้าการแจ้งมีข้อผิดพลาดประการใดขออภัยมา ณ โอกาสนี้";	
	$the_body_3 .= "<br><br>";
	
	$the_body_3 .= ", กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br>กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</td></tr></table>";
	
	
	//duped employees
	$the_header_1 = "Email การแจ้งรายงานผลการจ้างงานคนพิการในสถานประกอบการ ประจำปี 2559";
			
	$the_body_1 = "<table><tr><td>เรียนคุณ {contact_name}<br><br>";	
	
	$the_body_1 .= "Email เนื่องจากที่ท่านได้ดำเนินการตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการประจำปี 2559  ทางกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ขอแจ้งผลการปฏิบัติตามกฎหมายของท่านว่า  “มีการใช้สิทธิคนพิการซ้ำซ้อน” ให้ท่านรีบติดต่อเจ้าหน้าที่โดยด่วนเพื่อประโยชน์ในการปฏิบัติตามกฎหมายของท่าน  กรุณาติดต่อเจ้าหน้าที่เพื่อทำการตรวจสอบได้ที่เบอร์โทรศัพท์ 0 2106 9327-31 ในเวลาราชการ หรือ สำนักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัดที่ท่านรายงาน ถ้าการแจ้งมีข้อผิดพลาดประการใดขออภัยมา ณ โอกาสนี้";	
	$the_body_1 .= "<br><br>";
	
	$the_body_1 .= ", กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br>กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</td></tr></table>";
	
	
	//---------------------------------
	
	
	//get mails to send
	
	$mail_sql = "select * from email_log where email_status = 0 limit 0, 5";
	
	$mail_result = mysql_query($mail_sql);
	
	while($mail_row = mysql_fetch_array($mail_result)){
		
		//try sending out emails	
		$mail_user_id = $mail_row[user_id];
		$mail_type = $mail_row[email_type];				
		$mail_year = $mail_row[email_year];
		
		$user_row = getFirstRow("select * from users where user_id = '$mail_user_id'");		
		
		$mail_address = $user_row[user_email];
		
		$contact_name = $user_row[FirstName] . " " . $user_row[LastName];
		
		
		
		echo "<br>sending out mails for user: " . $mail_user_id . " contact name " . $contact_name . " mail " . $mail_address . " type " . $mail_type . " year " . $mail_year;
		
		//yoes 20151110 -> default all mails to yoes for now
		//$mail_address = "p.daruthep@gmail.com, witaya8989@gmail.com";
		//$mail_address = "p.daruthep@gmail.com";
		$mail_address = "witaya8989@gmail.com";
		
		//echo $mail_address; exit();
		
		//sending out emails
		if($mail_type == "0"){
			
			$the_header = $the_header_0;
			$the_body = $the_body_0;			
			//replace variable
			$the_body = str_replace("{contact_name}", $contact_name, $the_body);
				
		}elseif($mail_type == "1"){
			
			$the_header = $the_header_1;
			$the_body = $the_body_1;			
			//replace variable
			$the_body = str_replace("{contact_name}", $contact_name, $the_body);
				
		}elseif($mail_type == "2"){
			
			$the_header = $the_header_2;
			$the_body = $the_body_2;			
			//replace variable
			$the_body = str_replace("{contact_name}", $contact_name, $the_body);
				
		}elseif($mail_type == "3"){
			
			$the_header = $the_header_3;
			$the_body = $the_body_3;			
			//replace variable
			$the_body = str_replace("{contact_name}", $contact_name, $the_body);
				
		}
		
		
		if ($server_ip == "127.0.0.1"){
				
				//donothin	
				//echo $the_body;
				
				
		}else{
			//try send mails
			
			doSendMail($mail_address, $the_header, $the_body);	
		}
		
		
		//after mail sent, update mailing list status
		$update_mail_flag_sql = "
							update 
								email_log 
							set 
								email_status = 1 
								, email_date = now()
							where
								user_id = '$mail_user_id' 
								and email_type = '$mail_type' 
								and email_year = '$mail_year'
								
								
							";
							
		mysql_query($update_mail_flag_sql); // or die(mysql_error());
		
	}

?>