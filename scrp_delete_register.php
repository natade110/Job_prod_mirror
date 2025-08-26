<?php

	include "db_connect.php";
	
	
	if(is_numeric($_GET["id"])){
		$this_id = doCleanInput($_GET["id"]);
		$mod_register_id = doCleanInput($_GET["register_id"]);
	}else{
		exit();
	}
	
	$the_sql = "
				update modify_history_register
				set
					mod_type = 4
				where 
					mod_id = '$this_id'
				limit 1
				";
				
	//echo $the_sql; exit();
	mysql_query($the_sql);
	
	
	$history_sql = "insert into modify_history_register(
							
							mod_register_id
							, mod_date
							, mod_type
							
							, mod_desc
							
							
					) values(
						'$mod_register_id'
						,now()
						,5
						
						, '$this_id'
						
						
					)";
					
	mysql_query($history_sql);
	
	header("location: submit_forms.php");
	exit();
	
	

?>