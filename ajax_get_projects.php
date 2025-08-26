<?php
//3829900066275
//$the_id = $_POST["the_id"];
//$the_id?$the_id:$_GET["the_id"];
$the_id = $_GET["the_id"];

//echo $the_id;
if($the_id){

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "http://203.150.53.105//ServicesHandler/getprojects",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS =>"{\r\n    IdCardNo : '{$the_id}'\r\n}",
	  CURLOPT_HTTPHEADER => array(
		"Content-Type: application/javascript"
	  ),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	echo $response;
}
