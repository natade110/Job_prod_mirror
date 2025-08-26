<?php

	include "db_connect.php";
	
	
	if(!$_POST[zone_id]){
	
		//this is an "unassign zones"
		$sql = "delete from zone_user where user_id = '".($_POST[user_id])."'";	
		
		mysql_query($sql);
		header("location: view_user.php?id=".$_POST[user_id]);
		exit();
		
	}
	
	
	
	//table name
	$table_name = "zone_user";
	
	//specify all posts fields
	$input_fields = array(
						'zone_id'						
						, 'user_id'
						);
					
	//fields not from $_post	
	$special_fields = array();
	$special_values = array();
	
	
	//add vars to db
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
	
	//echo $the_sql;exit();
	mysql_query($the_sql);
	
	header("location: view_user.php?id=".$_POST[user_id]);

?>