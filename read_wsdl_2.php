<?php 

	error_reporting(1);

	$data = array(
	
		'paramMap' =>
		  array(
			'CARD_ID' => '1234567890123'
		  )
		  ,'user' => '1'
	  
	  );
	$method = 'executeQuery';
	$client = new SoapClient("http://61.19.50.29/ws/services/QueryWebService.wsdl",array('trace' => 1,"exceptions"=>0));
	$result = $client->$method($data);
	
	//print_r($result);
	
	echo "REQUEST:\n" . htmlentities($client->__getLastRequest()) . "\n";
	/* Print webservice response */
	var_dump($result);

?>