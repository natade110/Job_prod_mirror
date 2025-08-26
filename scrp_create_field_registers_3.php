<?php

	include "db_connect.php";
	
	$sql = "
	


CREATE TABLE IF NOT EXISTS `register` (
  `register_id` int(11) NOT NULL AUTO_INCREMENT,
  `register_name` text NOT NULL,
  `register_password` text NOT NULL,
  `register_org_name` text NOT NULL,
  `register_province` int(11) NOT NULL,
  `register_contact_name` text NOT NULL,
  `register_contact_phone` text NOT NULL,
  `register_position` text NOT NULL,
  `register_email` text NOT NULL,
  PRIMARY KEY (`register_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;



	
	
	";
	
	mysql_query($sql) or die(mysql_error()) ;

	echo "$sql <br> - success" ;

?>