<?php 
	
	
	//needs variable $this_id and $this_register_name from whatever page that call this
	
	$the_back_link = "http://ejob.dep.go.th/ejob/view_register.php?p=".htmlentities(base64_encode($this_id))."&n=".htmlentities(base64_encode(doCleanInput($this_register_name)));
	
	$the_back_link .= "&s=".htmlentities(base64_encode($this_seed));
	
	$company_row = getFirstRow("select * from company where cid = '$this_cid'");
	
	$the_company_name = formatCompanyName($company_row[CompanyNameThai], $company_row[CompanyTypeCode]);
		
	$the_header = "สมัครสมาชิก ระบบรายงานผลการจ้างงานคนพิการ เสร็จสิ้น";
	
	/*		
	$the_body = "<table><tr><td>เรียนผู้ใช้งาน<br><br>";
	
	$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบรายงานผลการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว <br><br>";
	
	$the_body .= "<a href='$the_back_link'>click ที่นี่</a> หรือไปที่ url $the_back_link เพื่อใส่รหัสผ่านที่ต้องการใช้งาน และใส่รายละเอียด ส่งเอกสารยืนยันตน เพื่อการใช้งานต่อไป<br><br>";
	
	$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
	*/
	
	
	$the_body = "<table><tr><td>เรียน $the_company_name <br>";	
	$the_body .= "เลขที่บัญชีนายจ้าง " .$company_row[CompanyCode] . "<br><br>";
	
	$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบรายงานผลการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว <br><br>";
	
	$the_body .= "<a href='$the_back_link'>click ที่นี่</a> หรือไปที่ url $the_back_link เพื่อใส่รหัสผ่านที่ต้องการใช้งาน และใส่รายละเอียด ส่งเอกสารยืนยันตน เพื่อการใช้งานต่อไป<br><br>";
	
	$the_body .= "<b>** หลังจากกรอกข้อมูลการใช้งานแล้ว กรุณาส่ง<u>เอกสารหลักฐานยืนยันตนตัวจริง</u>ให้เจ้าหน้าที่ เพื่อทำการเปิดสิทธิในการใช้งานระบบต่อไป</b> <br><br>";
	
	$the_body .= "ขอแสดงความนับถือ<br>";
	

	$the_body .= "กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br>";
	$the_body .= "โทรศัพท์ 02-106-9300, 02-106-9327-31<br>";
	
	
	//$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
	$the_body .= "</td></tr></table>";
	
	
	
?>