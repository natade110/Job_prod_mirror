<?php

	error_reporting(1);

	include "db_connect.php";
	
	$lawful_id = $_GET[lid];
	
	if(!$lawful_id){
		echo "nay";	 exit();
	}
	
	resetLawfulnessByLID($lawful_id);
	
	echo "yes...";
	
	
?>