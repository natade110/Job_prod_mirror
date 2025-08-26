<?php 

	include "db_connect.php";
	include "session_handler.php";
	
	//first check if current batach is running....
	$have_current_batch = getFirstItem("select var_value from vars where var_name = 'upload_org_file' and var_value > now()");
	//echo $have_current_batch;
	if($have_current_batch){
		header("location: import_org_new.php");
		exit();	
	}
	
	
	
	include "scrp_do_delete_import_org_file.php";
	
	
	  
	  header("location: import_org_new.php");

?>