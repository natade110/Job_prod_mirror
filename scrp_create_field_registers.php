<?php

	include "db_connect.php";
	
	$sql = "
	

CREATE TABLE IF NOT EXISTS `modify_history_register` (
  `mod_id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_register_id` int(11) NOT NULL,
  `mod_date` datetime NOT NULL,
  `mod_type` int(11) NOT NULL,
  `mod_user_id` int(11) NOT NULL,
  `mod_desc` text NOT NULL,
  `mod_year` int(11) NOT NULL,
  `mod_file` text NOT NULL,
  PRIMARY KEY (`mod_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;
	
	";
	
	mysql_query($sql) or die(mysql_error()) ;

	echo "$sql <br> - success" ;

?>