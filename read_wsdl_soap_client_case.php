<?php 

error_reporting(1);


$client = new SoapClient("http://ledgdxp.led.go.th/gdxEJB/GdxWebServiceBankr?wsdl",array('trace' => 1,"exceptions"=>0));

/*, "username" => "nep", "password" => "6w58fe"*/

/*
echo "--";

class Person {
    function Person($card_id) 
    {
        $this->CARD_ID = $card_id;
    }
}
*/

//$person = new Person('5521200018059');

//print_r($person);
//$person2 = array();

//$hash = array();
//$values = array();

//unset $values[$hash[$key]];
/*
$key = "CARD_ID";
$value = "5521200018059";
$hash[$key] = $value;
$values[$value] = $key;
print_r($values[$hash[$key]]);
*/

/*
$s = new SplObjectStorage;
$o1 = new stdClass;
$o2 = new stdClass;
$o2->CARD_ID = '5521200018059';

$s[$o1] = 'baz';
$s[$o2] = 'bingo';
*/

/*
echo $s[$o1]; // 'baz'
echo $s[$o2]; // 'bingo'
*/

//$data = null;
//$data->CARD_ID = "5521200018059";

//print_r($data);

$data = array('CARD_ID' => '123');


$wut = new SoapVar('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:gdx="http://gdx.session.ejb.led.go.th/">
   <soapenv:Header>
      <gdx:username>nep</gdx:username>
      <gdx:password>6w58fe</gdx:password>
   </soapenv:Header>
   <soapenv:Body><gdx:wsBankr>
         <gdx:type>1</gdx:type>
         <gdx:firstName>จ๊อบ ซัพพลาย</gdx:firstName>
         <gdx:lastName></gdx:lastName>
         <gdx:idCard>2000027440</gdx:idCard>
         
      </gdx:wsBankr>
   </soapenv:Body>
</soapenv:Envelope>', XSD_ANYXML);

//$header = new SoapHeader('nep', '6w58fe');
//$auth = array('username' => 'nep', 'password' => '6w58fe');
//$header = new SoapHeader(array('username' => 'nep', 'password' => '6w58fe'));
//$header = new SoapHeader('header','RequestorCredentials',$auth,false);

//$client->__setSoapHeaders($header);

$auth = array(
	'UserName'=>'USERNAME',
	'Password'=>'PASSWORD',
	'SystemId'=> array('_'=>'DATA','Param'=>'PARAM'),
	);
$header = new SoapHeader('NAMESPACE','Auth',$auth,false);
$client->__setSoapHeaders($header);

$params = array(
  
  
  //'username' => 'nep'
  //,'password' => '6w58fe'
 // ,'queryCode' => 'FUND03'
  //'paramMap' => $wut
 // $wut
  //,'paramMap' => $data
  //,'paramMap' => $s[$o1]
  //,'paramMap' => ""
  //,'paramMap' => (object) array('CARD_ID'=>array('5521200018059'))
  //,'paramMap' => array('CARD_ID'=>array('5521200018059'))
  //,'paramMap' => $person
  //,'paramMap' => $person2
  //,'paramMap'=>'5521200018059'
  //,'paramMap'=>array(array('CARD_ID'=>'5521200018059'))
 // ,'paramMap'=>$values[$hash[$key]]
  'idCard'=>'5521200018059'
  
);




$response = $client->__soapCall("wsBankr", array($params)); //, NULL, $header
//$response = $client->executeQuery(array('user' => 'test','password' => 'test123','queryCode' => 'FUND03','paramMap' => (object) array('CARD_ID'=>'1234567890123')));
echo "REQUEST:\n" . htmlentities($client->__getLastRequest()) . "\n";
/* Print webservice response */
var_dump($response);

echo "--";
?>