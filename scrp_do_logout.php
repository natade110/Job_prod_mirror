<?php

	
	session_start();
	// Unset all of the session variables.
	session_unset();
	// Finally, destroy the session.
	session_destroy();
	
	//yoes 20160205 --> logout to accesibility page
	if($_GET[a]){
		header("location: alogin.php");
		exit();
	}else{	
		header("location: index.php");
		exit();
	}
	

?>