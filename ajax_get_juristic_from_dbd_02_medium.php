<?php

	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";

    //$publicIP = file_get_contents('https://ipinfo.io/ip');
    //echo "Public IPx: " . trim($publicIP) . "\n";

	$the_id = "";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"];
	}
	if($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"];
	}
	
	
	if($_GET[mode]){
		$mode = $_GET[mode];
		$the_cid = $_GET[the_cid];
		doCompanyFullLog($sess_userid, $the_cid, "ajax_get_juristic_from_dbd_02_medium.php");
	}
	
	
	
	//$the_id = $the_id*1;
	
	//echo $the_id; exit();
	

	//$the_response = getAjaxDataDBD("", "http://203.154.94.100/dbd/ajax_get_juristic_from_dbd_02.php?the_id=0105544121728"); // <---- WANT TO GET DATA FROM THIS URL
	//$the_response = getAjaxDataDBD("", "http://203.154.94.100/dbd/new 1.txt");	
	//$the_response = getAjaxDataDBD("", "https://job.dep.go.th");

	//echo $the_response;
	
	//echo "http://203.154.94.100/dbd/ajax_get_juristic_from_dbd_02.php?the_id=0105544121728"; exit();
	
	//$data = file_get_contents("http://203.154.94.100/dbd/ajax_get_juristic_from_dbd_02.php?the_id=0105544121728");
	$opts = stream_context_create(array(
		'http'=> array(
			'method' => 'GET',
			'header'=> 'Host: job.dep.go.th',
		)
	));
	
	//$data = file_get_contents("http://10.0.116.6/dbd/new1.txt",false, $opts);
	$target_url = "http://10.0.116.6/dbd/ajax_get_juristic_from_dbd_02.php?the_id=$the_id"."&mode=$mode"."&the_cid=$the_cid"."&the_user=".$sess_userid;
	$data = file_get_contents($target_url, false, $opts);

    if($the_id == "0115556013607"){
        //echo $target_url;
    }


	
	//$data = file_get_contents("https://job.dep.go.th/dbd/new1.txt");
	
	echo $data;
	
	
	
	
	function getAjaxDataDBD($post_string, $target){
				
		$post = $post_string;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($ch);
		

		/*$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_errno= curl_errno($ch);
	  if ($http_status==503){
		echo "HTTP Status == 503 <br/>";
		echo "Curl Errno returned $curl_errno <br/>";		
	  }*/
  
		curl_close($ch);		
		
		
		return $response;
		//echo $response;		
		//return table2array($response);
		
	}