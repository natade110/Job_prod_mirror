<?php

	include "db_connect.php";
	
	
	if($_GET["del"]){
		$sql = "delete from users where user_meta = '71462' limit 1";	
		mysql_query($sql);
		echo "mg user deleted";
	}else{	
		$sql = "select * from users where user_meta = '71462'";	
		print_r(getFirstRow($sql));
		echo "...";
	}
	