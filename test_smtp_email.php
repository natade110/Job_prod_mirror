<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>UKLA House: Test mail script</title>
</head>

<body>

<?php

include "./functions.php";
	
	

error_reporting(E_ALL ^ E_NOTICE);


function doSendMailcc($to_who, $the_subject, $the_body){
	
	$mail = new PHPMailer;
	
	//$mail->Username = "admin@ejob.dep.go.th";
	//$mail->Password = "yp#SvPM2";
		
	$mail->isSMTP(); 
	//$mail->SMTPAuth   = true;	
	$mail->Host = "203.150.85.227";
	//$mail->SMTPSecure = "ssl";	
	$mail->Mailer = "smtp";
	//$mail->Port = 465;
	$mail->Port = 25;
	
	$mail->setFrom('admin@ejob.dep.go.th', 'กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ');
	
	$mail->addAddress($to_who);
	             
	$mail->CharSet  = 'UTF-8';
	$mail->Subject = $the_subject;	
	$mail->msgHTML($the_body);
	
	
	//
	$log_type = "mail_sent";
	$log_meta = $to_who . " - " . doCleanInput($the_subject);
	
	
	if(!$mail->Send())
	{
	   //echo "Message could not be sent. <p>";
	   //echo "Mailer Error: " . $mail->ErrorInfo;
	   //exit;
	   $log_type = "mail_error";
	}
	
	//yoes 20191223
	//add email log here....
	echo "Mailer status -> ";
	print_r($mail->ErrorInfo);
	
}



if($_POST["send_mail"] == "1"){
	
	
	$externalContent = file_get_contents('http://checkip.dyndns.com/');
	preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
	$externalIp = $m[1];
	echo "<br>requestor IP: ".$externalContent;
	
	$mail_address = $_POST["mail_address"];
	
	$the_header = "สมัครสมาชิก ระบบรายงานผลการจ้างงานคนพิการ เสร็จสิิ้น - $externalIp";
	
	$the_body = "เรียนคุณ <aaaa>\n\n";
	
	$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว \n\n";
	$the_body .= "หลังจากผู้ดูแลระบบได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว \n";
	$the_body .= "คุณจะสามารถเข้าใช้ระบบได้โดยใช้ username/password ด้านล่าง \n";
	$the_body .= "โดยการคุณจะได้รับ email ยืนยันการใช้งานระบบภายใน 24 ชม.\n\n";
	$the_body .= "username: ".($_POST["register_name"])." \n";
	$the_body .= "password: ".($_POST["register_password"])." \n\n";
	$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ";
	
	
	doSendMail($mail_address, $the_header, $the_body);
	
	//doSendMail("p.daruthep@gmail.com", "how are things", $the_body);
	
}

function pingAddress($ip) {
    $pingresult = exec("/bin/ping -n 3 $ip", $outcome, $status);
    if (0 == $status) {
        $status = "alive";
    } else {
        $status = "dead";
    }
    echo "The IP address, $ip, is  ".$status;
}

$ip_server = $_SERVER['SERVER_ADDR']; 
  
// Printing the stored address 
echo "Server IP Address is: $ip_server"; 
echo "<br>";

$externalContent = file_get_contents('http://checkip.dyndns.com/');
preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
$externalIp = $m[1];
echo "<br>requestor external IP: ".$externalContent . "<br>";

//pingAddress("203.150.85.227");

/*
$f = fsockopen('203.150.85.227', 465) ;
if ($f !== false) {
    $res = fread($f, 1024) ;
    if (strlen($res) > 0 && strpos($res, '220') === 0) {
        echo "Success!" ;
    }
    else {
        echo "Error: " . $res ;
    }
}
fclose($f) ;*/

?>

<form method="post">
<label>
Test sending test email to this address doSendMailcc: <input type="text" name="mail_address" value="<?php echo $_POST["mail_address"];?>" />
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
		//alert("The message has been sent to: <?php echo $mail_address;?>.");	
	</script>
<?php
	}	
?>
</body>
</html>
