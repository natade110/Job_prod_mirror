<?php
require_once "PHPTelnet.php";

$telnet = new PHPTelnet();

// if the first argument to Connect is blank,
// PHPTelnet will connect to the local host via 127.0.0.1
$result = $telnet->Connect('wsg.sso.go.th','','');

if ($result == 0) {
$telnet->DoCommand('enter command here', $result);
// NOTE: $result may contain newlines
echo $result;
$telnet->DoCommand('another command', $result);
echo $result;
// say Disconnect(0); to break the connection without explicitly logging out
$telnet->Disconnect();
}
?>