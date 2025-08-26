<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if(is_numeric($_POST["user_id"]) && is_numeric($_POST["email_id"]) && is_numeric($_POST["mail_enabled"])){

        $user_id = $_POST["user_id"]*1;
        $email_id = $_POST["email_id"]*1;
        $mail_enabled = $_POST["mail_enabled"]*1;
		
	}else{
		exit();
	}

    $the_sql = "
	
				replace into user_email(
					user_id
					 , email_id
					  , mail_enabled
				)
				values(
				
					'$user_id'
					, '$email_id'
					, '$mail_enabled'
				
				)
				
				";
	
	mysql_query($the_sql);
				
	//echo trim($zone.":".$user);

?>