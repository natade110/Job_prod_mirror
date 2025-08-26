<?php

	include "db_connect.php";
	$sql = "ALTER TABLE `lawful_employees` ADD `le_wage_unit` INT NOT NULL DEFAULT '0'";
	mysql_query($sql) or die(mysql_error()) ;

?>