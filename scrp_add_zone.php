<?php

	include "db_connect.php";
	
	//table name
	$table_name = "zones";
	
	//specify all posts fields
	$input_fields = array(
						'zone_name'						
						);
					
	//fields not from $_post	
	$special_fields = array('zone_province_code');
	$special_values = array(getFirstItem("select province_code from provinces where province_id = '".($_POST[Province]*1)."'"));
	
	
	//add vars to db
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	
	//echo $the_sql;exit();
	mysql_query($the_sql);
	
	header("location: manage_zone_list.php");

?>