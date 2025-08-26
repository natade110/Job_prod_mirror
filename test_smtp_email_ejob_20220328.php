<?php
	
	include "db_connect.php";

	$sql = "
		
		select
			user_name
			, user_email
			, companyNameThai
			, companyCode
			, law.lawfulStatus
			, lawc.cid
			
		from			
			lawfulness law					
				join
					lawfulness_company lawc
					on
					law.lid = lawc.lid
					and
					law.year = 2022
				join
					users
						on
						user_meta = lawc.cid
				join
					company c
					on
					c.cid = lawc.cid
		where
			law.LawfulStatus = 2
			and
			lawful_submitted >= 2
			and
			user_enabled = 1
			and
			AccessLevel = 4
		order by user_id asc
		limit 
			1, 99
	
	";
	
	$the_result = mysql_query($sql);
	
	//$principal_row = getFirstRow($principal_sql);
	while($the_row = mysql_fetch_array($the_result)){
		
		echo "<br>";
		print_r($the_row);
		
		$subject = "[ระบบรายงานผลการจ้างงานคนพิการ] แจ้งเตือนการยื่นส่งเงินเข้ากองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ";
		  $msg_body = "
			  เรียน บริษัท ".$the_row[companyNameThai]." จำกัด
				 <br> เลขที่บัญชีนายจ้าง ".$the_row[companyCode]."

				  <br><br>คุณได้รับข้อความติดต่อจากผู้ดูแลระบบ ระบบรายงานผลการจ้างงานคนพิการ สำหรับสถานประกอบการ ดังต่อไปนี้:

				  <br><br>สถานประกอบการที่ยื่นรายงานผ่านระบบรายงานผลการจ้างคนพิการ (e-service)
				  <br>ที่ได้ยื่นส่งเงินเข้ากองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแต่ยังไม่ได้ส่งเข้ากองทุนฯ
				  <br>เพื่อเป็นการหลีกเลี่ยงดอกเบี้ยที่เกิดหลังเครียริ่งเช็ค กรุณาชำระเงินก่อนเที่ยงวันของวันที่ 31 มีนาคม 2565

				  <br><br>ขอแสดงความนับถือ
			  ";
			  
			$company_row = getFirstRow("select * from company join provinces on Province = province_id where cid = '".$the_row[cid]."'");
  
		  if($company_row["Province"] != 1){
			 

			//yoes 20220316 --> get contact number
			$province_contact_details = getFirstItem("select province_contact_details from provinces where province_id = '".$company_row["Province"]."'");
			  
			  
			$msg_body .= "<br>หากมีข้อสงสัยกรุณาติดต่อ สำนักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัด".$company_row["province_name"];
			$msg_body .= "<br>โทรศัพท์ $province_contact_details";
			
			
		  }else{
			  
			//yoes 20220316 --> get contact number
			$province_contact_details = getFirstItem("select province_contact_details from provinces where province_id = '".$company_row["Province"]."'");
			  
			$msg_body .= "<br>กองกองทุนและส่งเสริมความเสมอภาคคนพิการ
				<br>โทรศัพท์ $province_contact_details";
		  }


		  //logMessage($d,"message");
		  doSendMail("p.daruthep@gmail.com",$subject,$msg_body);
		  doSendMail($the_row[user_email],$subject,$msg_body);
		 echo "mail sent to p.daruthep@gmail.com";
		 echo "mail sent to ".$the_row[user_email];
		
	}