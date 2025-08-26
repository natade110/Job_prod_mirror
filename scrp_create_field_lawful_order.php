<?php

	include "db_connect.php";
	
	mysql_query("ALTER TABLE `lawfulness` ADD `lawful_order` VARCHAR( 255 ) NOT NULL") ;

?>