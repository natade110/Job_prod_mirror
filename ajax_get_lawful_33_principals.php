<?php

	//header("Access-Control-Allow-Origin: http://job.dep.go.th");

	include "db_connect.php";
	include "ajax_allowed_ip.php";
	include "session_handler.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	
	
	if(is_numeric($_POST["p_from"]) && is_numeric($_POST["p_to"])){
		$p_from = $_POST["p_from"];
		$p_to = $_POST["p_to"];
	}
	
	if(is_numeric($_GET["p_from"]) && is_numeric($_GET["p_to"])){
		$p_from = $_GET["p_from"];
		$p_to = $_GET["p_to"];
	}
	
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	$sql = "
		select
			*
		from
			lawful_33_principals
		where
			p_from = '$p_from'
			and
			p_to = '$p_to'
	";
	
	//echo $sql . "<br>";
	$result = mysql_query($sql);
	$json_result = array();
	while($r = mysql_fetch_assoc($result)) {
		$json_result[] = $r;
	}
	print json_encode($json_result);