<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	//table name
	$table_name = "users";
	$this_id = doCleanInput($_POST["user_id"]);
	
	$user_row = getFirstRow("select * from users where user_id = '$this_id'"); 
	//echo "select * from users where user_id = '$this_id'";
	
	//print_r($user_row);
	
	$mail_address = doCleanInput($user_row["user_email"]);
	
	
	//
	$this_id = $this_id;
	$this_register_name = $user_row["user_name"];
	$this_seed = $this_id+doCleanInput($user_row["user_meta"])+7890;
	$this_cid = $user_row["user_meta"];
	
	/*echo "<br>this_id ".$this_id;
	echo "<br>this_register_name ".$this_register_name;
	echo "<br>this_seed ".$this_seed;
	echo "<br>this_cid ".$this_cid;*/
	//exit();
	
	include "template_register_email.php";
	
	//echo $mail_address;	
	/*echo "<br>".$the_header;
	echo "<br>".$the_body;
	
	
	
	exit();*/
	
	doSendMail($mail_address, $the_header, $the_body);	
	
	doSendMail("emotioniii.18@gmail.com", $the_header, $the_body);	
	
	doSendMail("p.daruthep@gmail.com", $the_header, $the_body);	
	
		
	header("location: view_user.php?id=$this_id&mailed=mailed");
	
	
	

?>