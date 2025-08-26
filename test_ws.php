<?php

	$host = 'wsg.sso.go.th'; 
	$port = 443; 
	$waitTimeoutInSeconds = 1; 
	if($fp = fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){   
	   echo "It worked";	   
	} else {
	   echo "It didn't work";
	} 
	fclose($fp);
	
	echo "...";	
	
	$WSDL = "wsg.sso.go.th:443/services/EmployeeEmployments.wsdl";
	//$WSDL = "wsg.sso.go.th:80/services/EmployeeEmployments.wsdl";
	//$WSDL = "wsg.sso.go.th:443";
	
	/*$SOAP = new SoapClient($WSDL, array('trace' => true));

	$Response = $SOAP->DoRemoteFunction($Data);
	
	echo "REQUEST:\n" . htmlentities($SOAP->__getLastRequest()) . "\n";
	*/
	
	
	$ch = curl_init();
	$timeout = 0; // set to zero for no timeout
	
	curl_setopt ($ch, CURLOPT_URL, $WSDL); //--> CURL works on taobao.com
	
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	
	$inside_page = curl_exec($ch);
	curl_close($ch);	
	
	
	echo $inside_page;

?>