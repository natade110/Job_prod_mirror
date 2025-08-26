<?php
$client = new http\Client;
$request = new http\Client\Request;
$request->setRequestUrl('http://103.13.229.228:5012/reporthandler/projectsummary');
$request->setRequestMethod('POST');
$body = new http\Message\Body;
$body->append('{
    Month : null,
    Year : 2020,
    ProvinceId : 114  
}');
$request->setBody($body);
$request->setOptions(array());
$request->setHeaders(array(
  'sso_tid' => '9a4c4b4b17a347278ceb630c0a89b7f1',
  'Content-Type' => 'application/json'
));
$client->enqueue($request)->send();
$response = $client->getResponse();
echo $response->getBody();