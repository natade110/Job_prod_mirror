<?php
/**
 * This example shows sending a message using PHP's mail() function.
 */

require 'PHPMailerAutoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;
//Set who the message is to be sent from


	$mail->Username = "noreply.nep.go.th@gmail.com";
	$mail->Password = "n0r3p1y@nep";
	
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = "tls";
	$mail->Host = "smtp.gmail.com";
	$mail->Mailer = "smtp";
	$mail->Port       = 587; 

$mail->setFrom('noreply.nep.go.th@gmail.com', 'NEP Admin');
//Set an alternative reply-to address
//$mail->addReplyTo('replyto@example.com', 'First Last');
//Set who the message is to be sent to
$mail->addAddress('p.daruthep@gmail.com');
//Set the subject line
$mail->Subject = 'PHPMailer mail() test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$the_body = "<table><tr><td>เรียนคุณ <aaaa><br><br>";
	
$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว <br>";
$the_body .= "หลังจากผู้ดูแลระบบได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว <br>";
$the_body .= "คุณจะสามารถเข้าใช้ระบบได้โดยใช้ username/password ด้านล่าง <br>";
$the_body .= "โดยการคุณจะได้รับ email ยืนยันการใช้งานระบบภายใน 24 ชม.<br><br>";
$the_body .= "username: ".($_POST["register_name"])." <br>";
$the_body .= "password: ".($_POST["register_password"])." <br><br>";
$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";


$mail->msgHTML($the_body);
//Attach an image file

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
?> ...