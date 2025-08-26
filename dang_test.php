<?php 
	error_reporting(-1);
	echo "----------------------------------<br>";
	echo $_SERVER["SERVER_ADDR"]."<br>";
	echo "----------------------------------<br>";
	//http://203.154.94.100
	$opts = stream_context_create(array(
    'http'=> array(
        'method' => 'GET',
        'header'=> 'Host: job.dep.go.th',
    )
));
	echo file_get_contents("http://10.0.116.6/dbd/new1.txt",false, $opts); 
	//var_dump($http_response_header);
?>