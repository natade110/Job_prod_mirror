<?php

	include "db_connect.php";
	
	$sql = "
	
INSERT INTO `register` (`register_id`, `register_name`, `register_password`, `register_org_name`, `register_province`, `register_contact_name`, `register_contact_phone`, `register_position`, `register_email`) VALUES
(6, 'yoes', '1234', 'ทดสอบสถานประกอบการ', 1, 'Yoes', '0870492252', '', 'p.daruthep@gmail.com'),
(7, 'yoes1234', '1234', 'ทดสอบสถานประกอบการกระบี่', 63, 'Yoes Chaya', '0870492252', 'ทดสอบ', 'p.daruthep@gmail.com');


	
	
	";
	
	mysql_query($sql) or die(mysql_error()) ;

	echo "$sql <br> - success" ;

?>