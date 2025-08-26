<?php

	//header("Access-Control-Allow-Origin: http://job.dep.go.th");

	include "db_connect.php";
	include "ajax_allowed_ip.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	
	$the_id = "1";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	$sql = "
		select
			*
		from
			curator a
				join
					lawfulness b
					on
					a.curator_lid = b.lid
				join
					company c
					on
					b.cid = c.cid
				join
					provinces d
					on
					d.province_id = c.province
					
		where
			a.curator_idcard = '$the_id'
	";
	
	//echo $sql . "<br>";
	$result = mysql_query($sql);
	$json_result = array();
	while($r = mysql_fetch_assoc($result)) {
		$json_result[] = $r;
	}
	print json_encode($json_result);