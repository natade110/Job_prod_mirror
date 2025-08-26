<?php
include("functions.php");
$wsdl_url = "http://122.155.197.65/ws/ktb/services.wsdl";
$arr_input = array(
	"user"		=>	'ktbserviceuat',
	"password"	=>  'So6s_yvh',
	"comcode"	=>	'1010',
	"prodcode"	=>	'1010',
	"command"	=>	'Inquiry',
	"bankcode"	=>	6,
	"bankref"	=>	'K00002 00004391',
	"datetime"	=>	'datetime',
	"effdate"	=>	'effdate',
	"channel"	=>	'channel',
	"ref1"		=>	'591000018741000000',
	"ref2"		=>	'00000000486',
	"ref3"		=>	'',
	"ref4"		=>	''
);
$method_name = "Inquiry"; 
$options = array(
	"trace" => 1,
	"exception" => 0,
	"location"	=> "http://122.155.197.65/ws/ktb/services.php"
);

$ret = callWebservice($wsdl_url,$arr_input,$method_name,$options);
var_dump($ret);

?>
