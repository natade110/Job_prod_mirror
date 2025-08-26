<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>UKLA House: Test mail script</title>
</head>

<body>

<?php
error_reporting(E_ALL ^ E_NOTICE);
if($_POST["send_mail"] == "1"){
	
	
	$mail_address = $_POST["mail_address"];
	
	$the_header = "สมัครสมาชิก ระบบรายงานผลการจ้างงานคนพิการ เสร็จสิิ้น";
	
	$the_body = "เรียนคุณ <aaaa>\n\n";
	
	$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว \n\n";
	$the_body .= "หลังจากผู้ดูแลระบบได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว \n";
	$the_body .= "คุณจะสามารถเข้าใช้ระบบได้โดยใช้ username/password ด้านล่าง \n";
	$the_body .= "โดยการคุณจะได้รับ email ยืนยันการใช้งานระบบภายใน 24 ชม.\n\n";
	$the_body .= "username: ".($_POST["register_name"])." \n";
	$the_body .= "password: ".($_POST["register_password"])." \n\n";
	$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ";
	
	
	mail($mail_address, $the_header, $the_body);
}
?>

<form method="post">
<label>
Test sending test email to this address: <input type="text" name="mail_address" />
</label>
<label>
<input type="submit" name="Submit" value="Submit" />
<input name="send_mail" type="hidden" value="1" />
</label>
</form>
<?php

	if($_POST["send_mail"] == "1"){
?>
	<script language="javascript">
		alert("The message has been sent to: <?php echo $mail_address;?>.");	
	</script>
<?php
	}	
?>
</body>
</html>
