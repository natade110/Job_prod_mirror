<?php
include("config.php");
include("common.php");
include("wsdl.php");
include("base.class.php");
//include("../../functions.php");

//-- disable wsdl cache  
ini_set("soap.wsdl_cache_enabled", "0");  
$url = baseUrl();

// WSDL Request
if($_GET[func]){	
	if ((strlen($_GET[func]) % 2) == 0) {
		$s = array();
		for($i=0;$i<strlen($_GET[func]);$i=$i+2){
			$func = intval(substr($_GET[func],$i,2));
			$s[$func] = $SOAP[$func];
		}
		echo genWSDL($s); 
	}
	exit;	
}


class ServiceClass{
	public function getWorkHistory($params){
		$getWorkHistory = new getWorkHistoryAction($params);
		$getWorkHistory->doAction();
		return $getWorkHistory;
	}

	public function getPerson3335History($params){
		$getPerson3335History = new getPerson3335HistoryAction($params);
		$getPerson3335History->doAction();
		return $getPerson3335History;
	}

	public function getCompany3335History($params){
		$getCompany3335History = new getCompany3335HistoryAction($params);
		$getCompany3335History->doAction();
		return $getCompany3335History;
	}
}



$data = genWSDL($SOAP);
$wsdl_file = 'data://text/plain;base64,'.base64_encode($data);
$server = new SoapServer($wsdl_file);
$server->setClass("ServiceClass");   
$data = file_get_contents('php://input');
BaseAction::setLog('Request',$data);

// -- Start SOAP Process
ob_start();
$server->handle($data);  
$response = ob_get_contents();
BaseAction::setLog('Response',$response);
ob_end_flush();
BaseAction::saveLog();


?>