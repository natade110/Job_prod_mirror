<?php

	include "db_connect.php";
	
	$sql = "
	

INSERT INTO `vars` (`var_name`, `var_value`) VALUES

('submit_date_from_month', '10'),
('submit_date_to_month', '1'),
('submit_date_from_day', '31'),
('submit_date_to_day', '31'),
('submit_date_to_year', '2014'),
('submit_date_from_year', '2012');

	
	
	";
	
	mysql_query($sql) or die(mysql_error()) ;

	echo "$sql <br> - success" ;

?>