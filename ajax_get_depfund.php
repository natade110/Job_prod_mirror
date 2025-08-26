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
	
	//$url = "https://fund.dep.go.th/jsonService?cmd=listContractByPsnId&user=mis&password=ydakd2&psnId=3710500301946";			
	$url = "http://fund.dep.go.th:8780/jsonService?cmd=listContractByPsnId&user=mis&password=ydakd2&psnId=$the_id";

								
	$homepage = file_get_contents($url);
	
	if(!$homepage){
		$the_remarks .= "file_get_contents failed";
		$status = 0;
	}else{
		$the_remarks .= "".	$homepage;
	}	
	
	//
	$ip_server = $_SERVER['SERVER_ADDR']; 
	
	/*	
	echo "Server IP Address is: $ip_server"; 
	echo "<br>";

	$externalContent = file_get_contents('http://checkip.dyndns.com/');
	preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
	$externalIp = $m[1];
	echo "<br>requestor external IP -> ".$externalContent . "<br>";

	echo "<br> request to: $url";
	
	echo "<br><br>status:";*/
	echo $the_remarks;