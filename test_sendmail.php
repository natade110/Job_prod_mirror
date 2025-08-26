<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Yoes: Test mail script</title>
</head>

<body>

<?php

include "functions.php";


function doSendMailWS($to_who, $the_subject, $the_body){
	
	//echo "$to_who"; return;
	/*
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
	if ($result === FALSE) {   }
	
	
	var_dump($result);
	
	*/
	
	$mail = new PHPMailer;
	
	//yoes 20200422
	//https://app.asana.com/0/794303922168293/1172325879013145
	//$mail->Username = "noreply.nep.go.th@gmail.com";
	//$mail->Password = "n0r3p1y@nep";
	//$mail->Username = "dep_support@mgsolution.co.th";
	//$mail->Password = "Qwerty789!";
	
	$mail->Username = "itfund03@dep.go.th";
	$mail->Password = "Fund@it03";
	
	//$mail->Username = "e-regis@osep.mail.go.th";
	//$mail->Password = "Admin@2562";
	
	//$mail->SMTPAuth   = true;
	//$mail->SMTPSecure = "tls";
	//$mail->SMTPAutoTLS = false;
	//$mail->Host = "mail.mgsolution.co.th";	
	//$mail->Mailer = "smtp";	
	//$mail->Port = 25;
	
	
	
	$mail->SMTPAuth   = true;
	//$mail->SMTPSecure = "ssl";	
	$mail->SMTPSecure = false;
	//$mail->Host = "outgoing.mail.go.th";
	//$mail->Host = "outgoing.workd.go.th";
	$mail->Host = "mailrelay.workd.go.th";
	//$mail->Host = "webmail.workd.go.th";
	$mail->Mailer = "smtp";
	//$mail->Port = 465;
	//$mail->Port = 993;
	$mail->Port = 25;
	
	//$mail->setFrom('dep_support@mgsolution.co.th', 'กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ');
	$mail->setFrom('itfund03@dep.go.th', 'กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ');
	
	$mail->addAddress($to_who);
				 
	$mail->CharSet  = 'UTF-8';
	$mail->Subject = $the_subject;	
	$mail->msgHTML($the_body);
	
	$mail->Send();
	if(!$mail->Send())
	{
	   echo "Message could not be sent. <p>";
	   echo "Mailer Error: " . $mail->ErrorInfo;
		//echo $mail->Host;
	   exit;
	}
	
	
	
	
	
}

error_reporting(E_ALL ^ E_NOTICE);
if($_POST["send_mail"] == "1"){
	
	
	$mail_address = $_POST["mail_address"];
	
	$the_header = "itfund03 สมัครสมาชิก ระบบรายงานผลการจ้างงานคนพิการ เสร็จสิิ้น";
	
	$the_body = "เรียนคุณ <aaaa><br><br>";
	
	$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว <br><br>";
	$the_body .= "หลังจากผู้ดูแลระบบได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว <br>";
	$the_body .= "คุณจะสามารถเข้าใช้ระบบได้โดยใช้ username/password ด้านล่าง <br>";
	$the_body .= "โดยการคุณจะได้รับ email ยืนยันการใช้งานระบบภายใน 24 ชม.<br><br>";
	$the_body .= "username: ".($_POST["register_name"])." <br>";
	$the_body .= "password: ".($_POST["register_password"])." <br><br>";
	$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ";
	
	
	doSendMailWS($mail_address, $the_header, $the_body);
	
	//doSendMail("p.daruthep@gmail.com", "how are things", $the_body);
	
}
?>

<form method="post">
<label>
Test sending test email to this address : <input type="text" name="mail_address" />
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
