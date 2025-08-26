<?php


	//transfer data between job and 109

	//first, connect own db
	$host = "localhost";
	$db = "hire_project";
	$username = "root";
	$password = "password";
	$password = "N9Af0a596f7Cb";
	
	
	$connect = mysql_connect($host,$username,$password) ;
	mysql_select_db($db, $connect) or die(mysql_error($connect)) ;
	mysql_query("SET NAMES 'utf8'", $connect);

	echo "connect to self on job.nep.go.th success!";
	
	
	
	///connect to 110
	
	$host = "192.168.3.110";
	$db = "hire_project";
	$username = "root";
	$password = "Next@now@nep";
	
	$connect2 = mysql_connect($host,$username,$password) ;
	mysql_select_db($db, $connect2) or die(mysql_error(connect2)) ;
	mysql_query("SET NAMES 'utf8'",$connect2);
	
	echo "connect to 110 success!";
	
	
	
	
	$sql = "select * from modify_history  ";
	
	$this_result = mysql_query($sql, $connect) or die(mysql_error($connect));
	
	
	
	
	while ($result_row = mysql_fetch_array($this_result)) {
		
		
		$sql2 = "
				INSERT INTO 
					`modify_history` (`mod_user_id`, `mod_cid`, `mod_date`, `mod_type`) 
				VALUES
					(
					".$result_row["mod_user_id"]."
					, ".$result_row["mod_cid"]."
					, '".$result_row["mod_date"]."'
					, ".$result_row["mod_type"]."
					)";
					
				mysql_query($sql2, $connect2);
				
			$count++;
			echo $count;
		
	}
	
	echo "DATA IMPORTED DONE!";
	



?>