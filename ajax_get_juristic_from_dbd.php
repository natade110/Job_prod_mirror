<?php
header('Content-Type: application/json');
/*
	ดึงข้องมูลจาก กรมการค้าภายใน
	- POST DATA REQUIRE
		func			: juristicInfoListByJuristicName | juristicCertificateDetailByJuristicID
		juristicName	: (for juristicInfoListByJuristicName)
		juristicID: 	: (for 	juristicCertificateDetailByJuristicID)
			
		

*/
$func 			= $_POST["func"];
$juristicName	= $_POST["juristicName"];
$juristicID		= $_POST["juristicID"];

// WSDL Configuration
$func_allow = array('juristicInfoListByJuristicName','juristicCertificateDetailByJuristicID');
$func_wsdl = array(
	"juristicInfoListByJuristicName" => array(
		"wsdl" => "DBD/juristicInfoListByJuristicName.wsdl", 
		"params" => "juristicName"
		
	),
	
	"juristicCertificateDetailByJuristicID" => array(
		"wsdl" => "DBD/juristicCertificateDetailByJuristicID.wsdl", 
		"params" => "juristicID"
	)		
	
);

if(!in_array($func, $func_allow)){
	sendReturnData(1,"Invalid functions");
	exit;
}

getData($func);


function getData($func,$k){
	global $func_wsdl;
	$options = array(
		'exceptions'=>1,
		'trace'=>1		
	);
	
	$wsdl = $func_wsdl[$func]['wsdl'];
	$params = new StdClass();
	$params->$func_wsdl[$func]['params'] = $k;	
	$client  = new SoapClient($wsdl,$options);
	
	try {
		$result = $client->$func($params);
		sendReturnData(0,"Success",$result);
		
		
	}catch (SOAPFault $f) {
		sendReturnData(2,$f->getMessage());        
	}	
}

function sendReturnData($code,$msg,$xml=""){
	$data = array();
	$data["return_code"] = $code;
	$data["return_mesg"] = $msg;
	if($xml){
		$xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soap_result);
		$xml = simplexml_load_string($xml);
		$json = json_encode($xml);
		$data["data"] = json_decode($json,true);		
	}
	echo json_decode($data);	
	exit;
}



?>
