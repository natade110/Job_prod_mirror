<?php

	include "db_connect.php";
	
	
	
	if(strlen(trim($_POST["user_name"]))==0 || strlen(trim($_POST["password"]))==0){
	
		//nologin
		header("Location: register_login.php?mode=error_pass");
	
	}else{
	
		//do login
		$user_name = doCleanInput($_POST["user_name"]);
		$password = doCleanInput($_POST["password"]);
		
		$query="SELECT * 
				FROM register
				WHERE register_name = '$user_name' 
				and register_password = '$password'";
		
		//echo $query; exit();
		$post_row = getFirstRow($query);
		
		
		if ($post_row["register_id"]=="") {
			//no login
			header("Location: register_login.php?mode=error_pass");
			exit();
		}else{
			//have login
			session_start();
			$_SESSION['sess_registerid'] = $post_row["register_id"];
						
			
			//update last login datetime
			//also update register stat
			$history_sql = "insert into modify_history_register(mod_register_id, mod_date, mod_type) values('".$post_row["register_id"]."',now(),2)";
			mysql_query($history_sql);
			
					
			header("Location: submit_forms.php");
			
		}
	
	}
	

?>